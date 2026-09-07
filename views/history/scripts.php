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
// เก็บสถานะหมวดหมู่ปัจจุบันที่เลือก
let currentSelectedGroup = null;

// ── 1. ควบคุมการสลับหมวดหมู่ ────────────────────────────────
function selectCategoryTab(catEl) {
  if (!catEl) return;

  document.querySelectorAll('.category-tab').forEach(el => el.classList.remove('active'));
  catEl.classList.add('active');

  currentSelectedGroup = catEl.dataset.group;
  applyTabFiltering();

  // เลือกแท็บอุปกรณ์รายการแรกที่แสดงอยู่ทันที
  const firstVisibleItem = document.querySelector(`.item-tab[data-group="${CSS.escape(currentSelectedGroup)}"]:not([style*="display: none"])`);
  if (firstVisibleItem) {
    selectHistoryTab(firstVisibleItem);
  } else {
    document.getElementById('historyContent').innerHTML = '<div class="empty-state"><div class="es-icon">📭</div><p>ไม่มีรายการในหมวดหมู่นี้</p></div>';
  }
}

// ── 2. กรองแท็บอุปกรณ์ตามหมวดหมู่และช่องค้นหา ─────────────────
function applyTabFiltering() {
  const query = (document.getElementById('historySearchInput')?.value || '').trim().toLowerCase();
  const itemTabs = document.querySelectorAll('.item-tab');
  const emptyNotice = document.getElementById('historyTabsEmpty');
  let visibleCount = 0;

  itemTabs.forEach(tab => {
    const tabGroup = tab.dataset.group;
    const tabSearch = tab.dataset.search || '';

    // หากกำลังพิมพ์ค้นหา จะข้ามการล็อกหมวดหมู่ชั่วคราวเพื่อให้ค้นหาได้ทั่วถึง
    const matchCategory = query ? true : (!currentSelectedGroup || tabGroup === currentSelectedGroup);
    const matchSearch = !query || tabSearch.includes(query);

    if (matchCategory && matchSearch) {
      tab.style.display = '';
      visibleCount++;
    } else {
      tab.style.display = 'none';
    }
  });

  if (emptyNotice) {
    emptyNotice.style.display = visibleCount === 0 ? 'flex' : 'none';
  }
}

// ── 3. พิมพ์ค้นหาใน Search box ─────────────────────────────
function filterHistoryTabs() {
  applyTabFiltering();
  const firstVisible = document.querySelector('.item-tab:not([style*="display: none"])');
  if (firstVisible) {
    selectHistoryTab(firstVisible);
  } else {
    document.getElementById('historyContent').innerHTML = '<div class="empty-state"><div class="es-icon">🔍</div><p>ไม่พบอุปกรณ์ที่ตรงกับคำค้นหา</p></div>';
  }
}

// ── 4. คลิกเลือกแท็บอุปกรณ์ ─────────────────────────────────
function selectHistoryTab(tabEl) {
  if (!tabEl) return;

  document.querySelectorAll('.item-tab').forEach(t => {
    t.classList.remove('active');
    t.setAttribute('aria-selected', 'false');
  });

  tabEl.classList.add('active');
  tabEl.setAttribute('aria-selected', 'true');
  tabEl.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });

  const itemId = parseInt(tabEl.dataset.itemId, 10);
  if (itemId) {
    loadItemHistory(itemId);
  }
}

// ── 5. โหลดข้อมูลแท็บแรกเมื่อเข้าหน้าเว็บ ─────────────────────
document.addEventListener('DOMContentLoaded', () => {
  const defaultCategory = document.querySelector('.category-tab.active');
  if (defaultCategory) {
    selectCategoryTab(defaultCategory);
  }
});

document.addEventListener('DOMContentLoaded', () => {
  const firstTab = document.querySelector('#historyTabBar .folder-tab[data-item-id]');
  if (firstTab) {
    loadItemHistory(parseInt(firstTab.dataset.itemId, 10));
  }
});
</script>