<?php
/**
 * history.php – Per-item stock in/out transaction history
 * Folder-tab item picker (search + click) -> AJAX-loaded item detail
 * card + transaction timeline (ajax_handler.php: action=get_item_history)
 */
declare(strict_types=1);
header('Content-Type: text/html; charset=utf-8');

session_start();

require_once __DIR__ . '/db.php';
$pdo = getDB();

// ── Fetch all items for the tab bar / search ───────────────────
$allItems = $pdo->query(
    'SELECT id, group_name, name, image, unit, pack_qty, current_stock, min_stock
     FROM items
     ORDER BY group_name, name'
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ประวัติการเบิก – Office Supplies</title>
  <meta name="description" content="ประวัติการเบิกและเติมสต็อกวัสดุสำนักงานแยกตามรายการ">
  <link rel="stylesheet" href="style.css">
  <style>
    /* Item-detail header shown above the transaction list */
    .item-detail-flex {
      display: flex; align-items: center; gap: 16px; flex-wrap: wrap;
    }
    .item-detail-flex .item-thumb { width: 68px; height: 68px; flex-shrink: 0; }
    .item-detail-meta { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-top: 6px; }
    .item-detail-meta .stock-num { font-size: 1.3rem; }

    /* One transaction row */
    .txn-card { cursor: default; }
    .txn-card:hover { transform: none; border-color: var(--border); background: var(--surface); }
    .txn-card__top {
      display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;
    }
    .txn-card__date { font-weight: 700; color: var(--dark); font-size: .95rem; }
    .txn-card__meta { color: var(--text-muted); font-size: .85rem; margin-top: 4px; line-height: 1.7; }
    .txn-card__qty { font-weight: 800; font-size: 1.15rem; text-align: right; white-space: nowrap; }
    .txn-card__qty.in  { color: #1d8a3d; }
    .txn-card__qty.out { color: var(--out-stock-bg); }

    /* Item tab bar: no icon badge needed, plain text tabs */
    #historyTabBar .folder-tab { min-width: unset; padding: 10px 16px; }
    #historyTabBar .folder-tab__label { max-width: 220px; overflow: hidden; text-overflow: ellipsis; }
  </style>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
</head>
<body>

<!-- ── NAVBAR ────────────────────────────────────────────────── -->
<nav class="navbar" role="navigation" aria-label="เมนูหลัก">
  <div class="navbar__brand">
    Office Supplies
  </div>
  <div class="navbar__nav">
    <a href="dashboard.php">แดชบอร์ด</a>
    <a href="items.php">รายการอุปกรณ์</a>
    <a href="history.php" class="active" aria-current="page">ประวัติการเบิก</a>
  </div>
  <div class="navbar__right"></div>
</nav>

<!-- ── PAGE ──────────────────────────────────────────────────── -->
<main class="page-wrapper">
  <div class="page-header">
    <h1>📜 ประวัติการเบิก</h1>
    <p>เลือกรายการอุปกรณ์เพื่อดูประวัติการเบิก / เติมสต็อกทั้งหมด</p>
  </div>

  <?php if (!$allItems): ?>
    <div class="panel">
      <div class="empty-state">
        <div class="es-icon">📭</div>
        <p>ยังไม่มีรายการอุปกรณ์ในระบบ</p>
      </div>
    </div>
  <?php else: ?>

  <!-- Search box to filter the item tabs below -->
  <div class="toolbar">
    <div class="search-wrap">
      <span class="search-icon">🔍</span>
      <input type="text" id="historySearchInput" placeholder="ค้นหาชื่อหรือกลุ่มรายการ..."
             aria-label="ค้นหาอุปกรณ์" oninput="filterHistoryTabs()">
    </div>
  </div>

  <div class="folder-tabs-container" id="historyFolders" style="height:auto;">
    <!-- Item picker tab bar -->
    <div class="folder-tab-bar" id="historyTabBar" role="tablist" aria-label="เลือกอุปกรณ์">
      <?php foreach ($allItems as $idx => $item): ?>
        <div class="folder-tab<?= $idx === 0 ? ' active' : '' ?>"
             data-item-id="<?= (int)$item['id'] ?>"
             data-search="<?= htmlspecialchars(mb_strtolower($item['group_name'] . ' ' . $item['name'])) ?>"
             role="tab" tabindex="0"
             aria-selected="<?= $idx === 0 ? 'true' : 'false' ?>"
             onclick="selectHistoryTab(this)"
             onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();selectHistoryTab(this);}">
          <span class="folder-tab__icon">📦</span>
          <span class="folder-tab__label"><?= htmlspecialchars($item['name']) ?></span>
        </div>
      <?php endforeach; ?>
      <div id="historyTabsEmpty" class="folder-tab" style="display:none; cursor:default;">
        <span class="folder-tab__label">ไม่พบรายการ</span>
      </div>
    </div>

    <!-- AJAX-loaded content -->
    <div class="folder-content-area">
      <div id="historyContent">
        <div class="spinner"></div>
      </div>
    </div>
  </div>

  <?php endif; ?>
</main>

<!-- ── Toast container ───────────────────────────────────────── -->
<div id="toastContainer"></div>

<script>
const HISTORY_PAGE_SIZE = 10;
let historyPageData = { transactions: [], currentPage: 1 };

// ── Tab search filter ───────────────────────────────────────────
function filterHistoryTabs() {
  const q = document.getElementById('historySearchInput').value.trim().toLowerCase();
  const tabs = document.querySelectorAll('#historyTabBar .folder-tab[data-item-id]');
  let visibleCount = 0;
  tabs.forEach(tab => {
    const match = tab.dataset.search.includes(q);
    tab.style.display = match ? '' : 'none';
    if (match) visibleCount++;
  });
  document.getElementById('historyTabsEmpty').style.display = visibleCount === 0 ? 'flex' : 'none';
}

// ── Tab selection ────────────────────────────────────────────────
function selectHistoryTab(tabEl) {
  document.querySelectorAll('#historyTabBar .folder-tab[data-item-id]').forEach(t => {
    t.classList.remove('active');
    t.setAttribute('aria-selected', 'false');
  });
  tabEl.classList.add('active');
  tabEl.setAttribute('aria-selected', 'true');
  tabEl.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
  loadItemHistory(parseInt(tabEl.dataset.itemId, 10));
}

// ── Load item detail + transaction history via AJAX ─────────────
function loadItemHistory(itemId) {
  const contentEl = document.getElementById('historyContent');
  contentEl.innerHTML = '<div class="spinner"></div>';

  fetch('ajax_handler.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'action=get_item_history&item_id=' + encodeURIComponent(itemId)
  })
  .then(r => r.json())
  .then(data => {
    if (data.error) {
      contentEl.innerHTML = '<div class="empty-state"><p>เกิดข้อผิดพลาด: ' + escHtml(data.error) + '</p></div>';
      return;
    }
    renderItemHistory(data.item, data.transactions || []);
  })
  .catch(() => {
    contentEl.innerHTML = '<div class="empty-state"><p>ไม่สามารถโหลดข้อมูลได้</p></div>';
  });
}

function renderItemHistory(item, transactions) {
  const contentEl = document.getElementById('historyContent');
  const st = parseInt(item.current_stock);
  const min = parseInt(item.min_stock);
  let badgeClass, badgeText;
  if (st === 0) { badgeClass = 'badge-out-stock'; badgeText = 'หมดแล้ว'; }
  else if (st <= min) { badgeClass = 'badge-low-stock'; badgeText = 'ใกล้หมด'; }
  else { badgeClass = 'badge-in-stock'; badgeText = 'มีสต็อก'; }

  const imageSrc = 'assets/images/' + escHtml(item.image || 'default.png');

  let html = `
    <div class="folder-content__header">
      <div class="item-detail-flex">
        <img src="${imageSrc}" alt="${escHtml(item.name)}" class="item-thumb" onerror="this.src='assets/images/default.png'">
        <div>
          <h2 class="folder-content__title">${escHtml(item.name)}</h2>
          <p class="folder-content__subtitle">กลุ่ม: ${escHtml(item.group_name)}</p>
          <div class="item-detail-meta">
            <span class="stock-num ${st === 0 ? 'zero' : st <= min ? 'low' : ''}">${st}</span>
            <span style="color:var(--text-muted);font-size:.88rem;">${escHtml(item.unit)}</span>
            <span class="badge ${badgeClass}">${badgeText}</span>
            ${packNoteHtml(item.pack_qty, item.unit)}
          </div>
        </div>
      </div>
    </div>`;

  html += `<h3 style="font-size:1rem;font-weight:700;">📋 ประวัติการเบิก-เติม (${transactions.length.toLocaleString('th-TH')} รายการ)</h3>`;

  if (!transactions.length) {
    html += '<div class="empty-state"><div class="es-icon">📭</div><p>ยังไม่มีประวัติการเบิก-เติมสำหรับรายการนี้</p></div>';
    contentEl.innerHTML = html;
    return;
  }

  html += '<div class="layered-cards" id="txnCardsList"></div><div id="historyPagination" class="pagination"></div>';
  contentEl.innerHTML = html;

  historyPageData = { transactions, currentPage: 1, unit: item.unit };
  renderHistoryPage();
}

function renderHistoryPage() {
  const { transactions, currentPage, unit } = historyPageData;
  const totalPages = Math.ceil(transactions.length / HISTORY_PAGE_SIZE);
  const start = (currentPage - 1) * HISTORY_PAGE_SIZE;
  const pageItems = transactions.slice(start, start + HISTORY_PAGE_SIZE);

  let html = '';
  pageItems.forEach(t => {
    const isIn = t.type === 'STOCK_IN';
    const qty = parseInt(t.quantity);
    const dt = formatThaiDateTime(t.created_at);

    html += `<div class="layered-card txn-card">
      <div class="txn-card__top">
        <div>
          <div class="txn-card__date">📅 ${dt}</div>
          <div class="txn-card__meta">
            ผู้ทำรายการ: ${escHtml(t.requester || '-')}<br>
            ${t.department_name ? 'แผนก: ' + escHtml(t.department_name) + '<br>' : ''}
            ${t.note ? 'หมายเหตุ: ' + escHtml(t.note) : ''}
          </div>
        </div>
        <div>
          <span class="badge ${isIn ? 'badge-in-stock' : 'badge-out-stock'}">${isIn ? 'เติมเข้า' : 'เบิกออก'}</span>
          <div class="txn-card__qty ${isIn ? 'in' : 'out'}">${isIn ? '+' : '-'}${qty.toLocaleString('th-TH')} ${escHtml(unit)}</div>
        </div>
      </div>
    </div>`;
  });
  document.getElementById('txnCardsList').innerHTML = html;

  renderPagination(document.getElementById('historyPagination'), totalPages, currentPage, transactions.length, (p) => {
    historyPageData.currentPage = p;
    renderHistoryPage();
    document.getElementById('historyContent').scrollIntoView({ behavior: 'smooth', block: 'start' });
  });
}

// ── Generic pagination control renderer (same convention as
//    dashboard.php / items.php) ────────────────────────────────
function renderPagination(container, totalPages, current, totalCount, onPageClick) {
  if (totalPages <= 1) { container.innerHTML = ''; return; }

  const makeBtn = (label, page, opts = {}) => {
    const { disabled = false, active = false, ellipsis = false } = opts;
    const cls = ['page-btn', active ? 'active' : '', ellipsis ? 'ellipsis' : ''].filter(Boolean).join(' ');
    return `<button type="button" class="${cls}" data-page="${page}" ${disabled ? 'disabled' : ''}>${label}</button>`;
  };

  let html = `<div class="pagination__info">หน้า ${current} จาก ${totalPages} • ทั้งหมด ${totalCount.toLocaleString('th-TH')} รายการ</div>`;
  html += makeBtn('‹ ก่อนหน้า', current - 1, { disabled: current === 1 });

  const pageNums = new Set([1, totalPages, current, current - 1, current + 1]);
  let prevShown = 0;
  for (let p = 1; p <= totalPages; p++) {
    if (!pageNums.has(p)) continue;
    if (p - prevShown > 1) html += makeBtn('…', 0, { ellipsis: true });
    html += makeBtn(String(p), p, { active: p === current });
    prevShown = p;
  }

  html += makeBtn('ถัดไป ›', current + 1, { disabled: current === totalPages });
  container.innerHTML = html;

  container.querySelectorAll('.page-btn[data-page]:not(.ellipsis):not(:disabled)').forEach(btn => {
    btn.addEventListener('click', () => onPageClick(parseInt(btn.dataset.page, 10)));
  });
}

// ── Utilities ────────────────────────────────────────────────────
function escHtml(str) {
  return String(str ?? '')
    .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
    .replace(/"/g,'&quot;').replace(/'/g,'&#039;');
}

function packNoteHtml(packQty, unit) {
  const n = parseInt(packQty);
  if (!n || n < 1) return '';
  return `<small class="pack-note" style="display:inline-block;margin-top:0;">1 แพ็ค / ${n.toLocaleString('th-TH')} ${escHtml(unit)}</small>`;
}

// created_at comes as "YYYY-MM-DD HH:MM:SS" from MySQL
function formatThaiDateTime(mysqlDateTime) {
  if (!mysqlDateTime) return '-';
  const [datePart, timePart] = String(mysqlDateTime).split(' ');
  const [y, m, d] = datePart.split('-');
  const time = timePart ? timePart.slice(0, 5) : '';
  return `${d}/${m}/${y}${time ? ' - ' + time : ''}`;
}

function showToast(msg, type = 'success') {
  const el = document.createElement('div');
  el.className = 'toast ' + type;
  el.innerHTML = (type === 'success' ? '✅ ' : '❌ ') + escHtml(msg);
  document.getElementById('toastContainer').appendChild(el);
  setTimeout(() => {
    el.style.animation = 'toastOut .3s ease forwards';
    setTimeout(() => el.remove(), 300);
  }, 3000);
}

// ── Initial load: first item in the tab bar ─────────────────────
document.addEventListener('DOMContentLoaded', () => {
  const firstTab = document.querySelector('#historyTabBar .folder-tab[data-item-id]');
  if (firstTab) {
    loadItemHistory(parseInt(firstTab.dataset.itemId, 10));
  }
});
</script>
</body>
</html>