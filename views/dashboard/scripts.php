<script src="folder-tabs.js"></script>
<script>
// ── Folder Tab Manager ────────────────────────────────────────
class DashboardFolderManager {
  constructor() {
    this.container = document.getElementById('dashboardFolders');
    this.tabs = this.container.querySelectorAll('.folder-tab');
    this.contents = this.container.querySelectorAll('.folder-content');
    this.activeIndex = 0;
    this.isAnimating = false;
    this.init();
  }

  init() {
    this.tabs.forEach((tab, idx) => {
      tab.addEventListener('click', () => this.switchTab(idx));
      tab.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          this.switchTab(idx);
        }
      });
    });

    this.tabs.forEach((tab) => {
      tab.addEventListener('mouseenter', () => {
        if (!tab.classList.contains('active')) {
          gsap.to(tab, { y: -6, duration: 0.25, ease: 'power2.out' });
        }
      });
      tab.addEventListener('mouseleave', () => {
        if (!tab.classList.contains('active')) {
          gsap.to(tab, { y: 0, duration: 0.25, ease: 'power2.out' });
        }
      });
    });
  }

  switchTab(index) {
    if (this.isAnimating || index === this.activeIndex) return;
    this.isAnimating = true;

    const oldTab = this.tabs[this.activeIndex];
    const newTab = this.tabs[index];
    const oldContent = this.contents[this.activeIndex];
    const newContent = this.contents[index];

    const tl = gsap.timeline({
      onComplete: () => { this.isAnimating = false; },
    });

    tl.to(oldContent, {
      opacity: 0,
      scale: 0.97,
      y: -8,
      duration: 0.35,
      ease: 'power2.in',
    }, 0);

    tl.call(() => {
      oldTab.classList.remove('active');
      newTab.classList.add('active');
      oldContent.classList.remove('active');
      newContent.classList.add('active');
    }, null, 0.2);

    tl.to(newContent, {
      opacity: 1,
      scale: 1,
      y: 0,
      duration: 0.45,
      ease: 'elastic.out(1, 0.55)',
    }, 0.2);

    this.activeIndex = index;
  }
}

document.addEventListener('DOMContentLoaded', () => {
  new DashboardFolderManager();
  console.log('✨ Dashboard Folder Manager Initialized');
});

// ── Card Click Handler ────────────────────────────────────────
let categoryPageData = { items: [], category: '', currentPage: 1 };
const CATEGORY_PAGE_SIZE = 25;

function loadCategoryInTab(category, cardEl) {
  const resultEl = document.getElementById('overviewResult');
  resultEl.innerHTML = '<div class="spinner"></div>';

  fetch('ajax_handler.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'action=get_category_items&category=' + encodeURIComponent(category)
  })
  .then(r => r.json())
  .then(data => {
    if (data.error) {
      resultEl.innerHTML = '<div class="empty-state"><p>เกิดข้อผิดพลาด: ' + data.error + '</p></div>';
      return;
    }
    if (!data.items || data.items.length === 0) {
      resultEl.innerHTML = '<div class="empty-state"><div class="es-icon">📭</div><p>ไม่พบรายการในหมวดนี้</p></div>';
      return;
    }

    categoryPageData = { items: data.items, category, currentPage: 1 };

    const labels = { all: '📦 อุปกรณ์ทั้งหมด', low: '⚠️ สต็อกใกล้หมด', out: '🚫 หมดสต็อก' };
    resultEl.innerHTML = '<div style="margin-top: 20px;"><h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 12px;">' + labels[category] + '</h3>' +
      '<div class="tbl-wrap"><table id="categoryTable"><thead><tr>' +
      '<th style="width:52%;">ชื่อรายการ</th><th style="text-align:center;width:18%;">สต็อก</th>' +
      '<th style="text-align:center;width:14%;">หน่วย</th><th style="text-align:center;width:16%;">สถานะ</th>' +
      '</tr></thead><tbody id="categoryTableBody"></tbody></table></div>' +
      '<div id="categoryPagination" class="pagination"></div></div>';

    renderCategoryPage();
    gsap.to(window, { duration: 0.5, scrollTo: { y: resultEl, offsetY: 100 }, ease: 'power2.inOut' });
  })
  .catch(() => {
    resultEl.innerHTML = '<div class="empty-state"><p>ไม่สามารถโหลดข้อมูลได้</p></div>';
  });
}

function renderCategoryPage() {
  const { items, currentPage } = categoryPageData;
  const totalPages = Math.ceil(items.length / CATEGORY_PAGE_SIZE);
  const start = (currentPage - 1) * CATEGORY_PAGE_SIZE;
  const pageItems = items.slice(start, start + CATEGORY_PAGE_SIZE);

  let html = '';
  pageItems.forEach(it => {
    const st = parseInt(it.current_stock);
    let badgeClass, badgeText;
    if (st === 0) { badgeClass = 'badge-out-stock'; badgeText = 'หมดแล้ว'; }
    else if (st <= parseInt(it.min_stock)) { badgeClass = 'badge-low-stock'; badgeText = 'ใกล้หมด'; }
    else { badgeClass = 'badge-in-stock'; badgeText = 'มีสต็อก'; }

    html += `<tr>
      <td style="font-weight:600;">${escHtml(it.name)}${packNoteHtml(it.pack_qty, it.unit)}</td>
      <td class="stock-num ${st===0?'zero':st<=it.min_stock?'low':''}" style="text-align:center;">${st}</td>
      <td style="text-align:center;">${escHtml(it.unit)}</td>
      <td style="text-align:center;"><span class="badge ${badgeClass}">${badgeText}</span></td>
    </tr>`;
  });
  document.getElementById('categoryTableBody').innerHTML = html;

  renderPagination(document.getElementById('categoryPagination'), totalPages, currentPage, items.length, (p) => {
    categoryPageData.currentPage = p;
    renderCategoryPage();
    document.getElementById('overviewResult').scrollIntoView({ behavior: 'smooth', block: 'start' });
  });
}

// ── Alert table pagination ─────────────────────────────────────
const ALERT_ITEMS = <?= json_encode($alertItems, JSON_UNESCAPED_UNICODE) ?>;
const ALERT_PAGE_SIZE = 25;
let alertCurrentPage = 1;

function renderAlertPage() {
  if (!ALERT_ITEMS.length) return;
  const totalPages = Math.ceil(ALERT_ITEMS.length / ALERT_PAGE_SIZE);
  const start = (alertCurrentPage - 1) * ALERT_PAGE_SIZE;
  const pageItems = ALERT_ITEMS.slice(start, start + ALERT_PAGE_SIZE);

  let html = '';
  pageItems.forEach(it => {
    const st = parseInt(it.current_stock);
    const min = parseInt(it.min_stock);
    const badgeClass = st === 0 ? 'badge-out-stock' : 'badge-low-stock';
    const badgeText  = st === 0 ? 'หมดแล้ว' : 'ใกล้หมด';
    html += `<tr>
      <td>
        <div style="font-weight:600;color:var(--dark);">${escHtml(it.name)}</div>
        ${packNoteHtml(it.pack_qty, it.unit)}
      </td>
      <td class="stock-num ${st === 0 ? 'zero' : 'low'}" style="text-align:center;">
        ${st} <small style="font-weight:400;font-size:.78rem;">${escHtml(it.unit)}</small>
      </td>
      <td style="color:var(--text-muted);font-size:.88rem;text-align:center;">${min} ${escHtml(it.unit)}</td>
      <td style="text-align:center;"><span class="badge ${badgeClass}">${badgeText}</span></td>
    </tr>`;
  });
  document.getElementById('alertTableBody').innerHTML = html;

  renderPagination(document.getElementById('alertPagination'), totalPages, alertCurrentPage, ALERT_ITEMS.length, (p) => {
    alertCurrentPage = p;
    renderAlertPage();
    document.getElementById('alertTable').scrollIntoView({ behavior: 'smooth', block: 'start' });
  });
}

// ── Generic pagination control ────────────────────────────────
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

document.addEventListener('DOMContentLoaded', renderAlertPage);

function escHtml(str) {
  return String(str)
    .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
    .replace(/"/g,'&quot;').replace(/'/g,'&#039;');
}

function packNoteHtml(packQty, unit) {
  const n = parseInt(packQty);
  if (!n || n < 1) return '';
  return `<small class="pack-note">1 แพ็ค / ${n.toLocaleString('th-TH')} ${escHtml(unit)}</small>`;
}

function showToast(msg, type = 'success') {
  const el = document.createElement('div');
  el.className = 'toast ' + type;
  el.innerHTML = (type === 'success' ? '✅ ' : '❌ ') + msg;
  document.getElementById('toastContainer').appendChild(el);
  setTimeout(() => {
    el.style.animation = 'toastOut .3s ease forwards';
    setTimeout(() => el.remove(), 300);
  }, 3000);
}
</script>