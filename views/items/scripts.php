<script>
const ALL_ITEMS = <?= json_encode($allItems, JSON_UNESCAPED_UNICODE) ?>;
const PAGE_SIZE = 25;
let currentPage = 1;
let CURRENT_VIEW_IDS = ALL_ITEMS.map(it => it.id);

function filterTable() {
  const query = document.getElementById('searchInput').value.trim().toLowerCase();
  const sort  = document.getElementById('sortSelect').value;
  const tbody = document.getElementById('tableBody');
  const emptyState = document.getElementById('emptyState');
  const pagination = document.getElementById('itemsPagination');

  let filtered = ALL_ITEMS.filter(it => {
    const haystack = (it.name + ' ' + it.group_name).toLowerCase();
    return !query || haystack.includes(query);
  });

  filtered.sort((a, b) => {
    if (sort === 'name_asc')    return a.name.localeCompare(b.name, 'th');
    if (sort === 'name_desc')   return b.name.localeCompare(a.name, 'th');
    if (sort === 'stock_asc')   return a.current_stock - b.current_stock;
    if (sort === 'stock_desc')  return b.current_stock - a.current_stock;
    return 0;
  });

  CURRENT_VIEW_IDS = filtered.map(it => it.id);

  if (filtered.length === 0) {
    tbody.innerHTML = '';
    emptyState.style.display = 'block';
    pagination.innerHTML = '';
    return;
  }
  emptyState.style.display = 'none';

  const groupedMap = {};
  filtered.forEach(it => {
    if (!groupedMap[it.group_name]) groupedMap[it.group_name] = [];
    groupedMap[it.group_name].push(it);
  });
  const groupEntries = Object.entries(groupedMap);

  const pages = [];
  let pageEntries = [];
  let pageCount = 0;
  groupEntries.forEach(([grpName, items]) => {
    let remaining = items;
    while (remaining.length > 0) {
      if (pageCount > 0 && pageCount >= PAGE_SIZE) {
        pages.push(pageEntries);
        pageEntries = [];
        pageCount = 0;
      }
      const spaceLeft = PAGE_SIZE - pageCount;
      const take = pageCount === 0
        ? Math.min(remaining.length, PAGE_SIZE)
        : Math.min(remaining.length, spaceLeft);
      const chunk = remaining.slice(0, take);
      remaining = remaining.slice(take);
      pageEntries.push([grpName, chunk]);
      pageCount += chunk.length;
      if (remaining.length > 0) {
        pages.push(pageEntries);
        pageEntries = [];
        pageCount = 0;
      }
    }
  });
  if (pageEntries.length) pages.push(pageEntries);

  const totalPages = pages.length;
  if (currentPage > totalPages) currentPage = totalPages;
  if (currentPage < 1) currentPage = 1;

  renderItemsPage(pages[currentPage - 1]);
  renderPagination(pagination, totalPages, currentPage, filtered.length, (p) => {
    currentPage = p;
    filterTable();
    document.querySelector('.panel').scrollIntoView({ behavior: 'smooth', block: 'start' });
  });
}

function renderItemsPage(groupEntries) {
  const tbody = document.getElementById('tableBody');
  let html = '';
  groupEntries.forEach(([grpName, items]) => {
    items.forEach((it, idx) => {
      const st  = parseInt(it.current_stock);
      const min = parseInt(it.min_stock);
      let badgeClass, badgeText, stockClass;
      if (st === 0)           { badgeClass='badge-out-stock'; badgeText='หมดแล้ว'; stockClass='zero'; }
      else if (st <= min)     { badgeClass='badge-low-stock';  badgeText='ใกล้หมด'; stockClass='low'; }
      else                    { badgeClass='badge-in-stock';   badgeText='มีสต็อก'; stockClass=''; }

      const isLast  = idx === items.length - 1;
      const isFirst = idx === 0;
      const imgSrc  = 'assets/images/' + escAttr(it.image);

      const rowCls = [isFirst ? 'row-group-start' : '', isLast ? 'row-group-end' : ''].filter(Boolean).join(' ');
      html += `<tr class="${rowCls}" data-group="${escAttr(grpName)}" data-name="${escAttr(it.name)}" data-stock="${st}">`;
      if (idx === 0) {
        html += `<td class="img-cell" rowspan="${items.length}">
          <img src="${imgSrc}" alt="${escHtml(grpName)}" class="item-thumb"
               onerror="this.src='assets/images/default.png'">
        </td>`;
      }
      html += `<td>${escHtml(it.name)}${packNoteHtml(it.pack_qty, it.unit)}</td>
               <td class="col-stock"><span class="stock-num ${stockClass}">${st}</span></td>
               <td class="col-unit">${escHtml(it.unit)}</td>
               <td class="col-status">
                 <span class="badge ${badgeClass}">${badgeText}</span>
               </td>
               <td class="col-actions">
                 <div class="action-buttons">
                   <button type="button" class="btn-edit" title="เบิก / เติมสต็อก / แก้ไขข้อมูล" onclick="openModal(${parseInt(it.id)})">✏️</button>
                   <button type="button" class="btn-edit btn-delete" title="ลบรายการ" onclick="deleteItem(${parseInt(it.id)}, '${escAttr(it.name).replace(/'/g, "\\'")}')">🗑️</button>
                 </div>
               </td>
             </tr>`;
    });
  });
  tbody.innerHTML = html;
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

let currentItemId = null;
let currentStock   = 0;

function openModal(itemId, tabToShow) {
  currentItemId = itemId;
  const fd = new FormData();
  fd.append('action', 'get_item_detail');
  fd.append('item_id', itemId);

  fetch('ajax_handler.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
      if (data.error || !data.item) {
        showToast(data.error || 'ไม่พบรายการ', 'error');
        return;
      }
      populateModal(data.item);
      document.getElementById('itemModalBackdrop').classList.add('open');
      switchModalTab(tabToShow || 'stock-out');
    })
    .catch(() => showToast('เกิดข้อผิดพลาด', 'error'));
}

function populateModal(item) {
  currentStock = parseInt(item.current_stock);
  const min = parseInt(item.min_stock);

  document.getElementById('modalItemName').textContent = item.name;
  document.getElementById('modalStockVal').textContent = item.current_stock;
  document.getElementById('modalStockUnit').textContent = item.unit;
  document.getElementById('modalPackNote').innerHTML =
    packNoteHtml(item.pack_qty, item.unit) || '<span class="no-pack">ไม่ระบุแพ็ค</span>';
  updateModalBadge(currentStock, min);

  document.getElementById('stockInForm').reset();
  document.getElementById('stockOutForm').reset();
  document.getElementById('editDetailsForm').reset();

  document.getElementById('siItemId').value = item.id;
  document.getElementById('siDate').value = '';
  document.getElementById('siQty').max = 1000;
  document.getElementById('siMaxLabel').textContent = '1000';

  document.getElementById('soItemId').value = item.id;
  const soMax = Math.max(currentStock, 1);
  document.getElementById('soQty').max = soMax;
  document.getElementById('soQtyNum').max = soMax;
  document.getElementById('soQty').value = 1;
  document.getElementById('soQtyNum').value = 1;
  document.getElementById('soMaxLabel').textContent = currentStock;
  const soDisabled = currentStock < 1;
  document.getElementById('soQty').disabled = soDisabled;
  document.getElementById('soQtyNum').disabled = soDisabled;
  document.getElementById('soDate').value = '';

  document.getElementById('edItemId').value = item.id;
  document.getElementById('edGroup').value = item.group_name;
  document.getElementById('edName').value = item.name;
  document.getElementById('edUnit').value = item.unit;
  document.getElementById('edPackQty').value = item.pack_qty ?? '';
  document.getElementById('edPreview').src = 'assets/images/' + item.image;
}

function onQtyNumInput() {
  const num = document.getElementById('soQtyNum');
  const slider = document.getElementById('soQty');
  const max = parseInt(slider.max) || 1;
  let v = parseInt(num.value);
  if (isNaN(v)) v = 1;
  v = Math.min(Math.max(v, 1), max);
  num.value = v;
  slider.value = v;
}
function onQtySliderInput() {
  document.getElementById('soQtyNum').value = document.getElementById('soQty').value;
}

function onSiQtyNumInput() {
  const num = document.getElementById('siQtyNum');
  const slider = document.getElementById('siQty');
  let v = parseInt(num.value);
  if (isNaN(v)) v = 1;
  if (v < 1) v = 1;
  if (v > parseInt(slider.max)) {
    slider.max = v;
    document.getElementById('siMaxLabel').textContent = v;
  }
  num.value = v;
  slider.value = v;
}
function onSiQtySliderInput() {
  document.getElementById('siQtyNum').value = document.getElementById('siQty').value;
}

function navigateModalItem(dir) {
  if (!CURRENT_VIEW_IDS.length) return;
  let idx = CURRENT_VIEW_IDS.findIndex(id => parseInt(id) === parseInt(currentItemId));
  if (idx === -1) idx = 0;
  let newIdx = (idx + dir + CURRENT_VIEW_IDS.length) % CURRENT_VIEW_IDS.length;

  const activeBtn = document.querySelector('.modal-tab-btn.active');
  let tab = 'stock-out';
  if (activeBtn) {
    if (activeBtn.classList.contains('edit-tab')) tab = 'edit';
    else if (activeBtn.classList.contains('stock-in-tab')) tab = 'stock-in';
    else if (activeBtn.classList.contains('stock-out-tab')) tab = 'stock-out';
  }
  openModal(CURRENT_VIEW_IDS[newIdx], tab);
}

function closeModal() {
  document.getElementById('itemModalBackdrop').classList.remove('open');
}

function switchModalTab(tab) {
  const tabMap = {
    'stock-in':  { btn: '.stock-in-tab',  content: 'tab-stock-in'  },
    'stock-out': { btn: '.stock-out-tab', content: 'tab-stock-out' },
    'edit':      { btn: '.edit-tab',      content: 'tab-edit'      },
  };
  const target = tabMap[tab];
  if (!target) return;

  document.querySelectorAll('.modal-tab-btn').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.modal-tab-content').forEach(c => c.classList.remove('active'));

  document.querySelector(target.btn).classList.add('active');
  document.getElementById(target.content).classList.add('active');
}

function refreshGroupNameList() {
  const uniqueGroups = [...new Set(ALL_ITEMS.map(it => it.group_name))].sort((a, b) => a.localeCompare(b, 'th'));
  const datalist = document.getElementById('groupNameList');
  datalist.innerHTML = uniqueGroups.map(g => `<option value="${escAttr(g)}"></option>`).join('');
}

function openAddModal() {
  document.getElementById('addItemForm').reset();
  document.getElementById('addPreview').src = 'assets/images/default.png';
  refreshGroupNameList();
  document.getElementById('addModalBackdrop').classList.add('open');
}

function closeAddModal() {
  document.getElementById('addModalBackdrop').classList.remove('open');
}

function previewAddImage(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => { document.getElementById('addPreview').src = e.target.result; };
    reader.readAsDataURL(input.files[0]);
  }
}

function submitAddItem(e) {
  e.preventDefault();
  const stock = parseInt(document.getElementById('addStock').value);
  const minStock = parseInt(document.getElementById('addMinStock').value);
  if (isNaN(stock) || stock < 0)    { showToast('กรุณาป้อนจำนวนสต็อกที่ถูกต้อง', 'error'); return; }
  if (isNaN(minStock) || minStock < 0) { showToast('กรุณาป้อนสต็อกขั้นต่ำที่ถูกต้อง', 'error'); return; }

  const fd = new FormData(document.getElementById('addItemForm'));
  fd.append('action', 'add_item');

  fetch('ajax_handler.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        showToast('เพิ่มรายการ "' + data.name + '" สำเร็จ');
        ALL_ITEMS.push({
          id: data.item_id,
          group_name: data.group_name,
          name: data.name,
          image: data.image,
          unit: data.unit,
          pack_qty: data.pack_qty,
          current_stock: data.current_stock,
          min_stock: data.min_stock,
        });
        filterTable();
        closeAddModal();
      } else {
        showToast(data.error || 'เกิดข้อผิดพลาด', 'error');
      }
    })
    .catch(() => showToast('เกิดข้อผิดพลาด', 'error'));
}

let _pendingDeleteId = null;
let _pendingDeleteName = null;

function deleteItem(itemId, itemName) {
  _pendingDeleteId = itemId;
  _pendingDeleteName = itemName;
  document.getElementById('deleteConfirmText').textContent =
    'ยืนยันการลบรายการ "' + itemName + '" ?\nประวัติการเบิก/เติมของรายการนี้จะถูกลบไปด้วย และไม่สามารถกู้คืนได้';
  document.getElementById('deleteConfirmBackdrop').classList.add('open');
}

function cancelDeleteConfirm() {
  _pendingDeleteId = null;
  _pendingDeleteName = null;
  document.getElementById('deleteConfirmBackdrop').classList.remove('open');
}

function confirmDeleteYes() {
  const itemId = _pendingDeleteId;
  const itemName = _pendingDeleteName;
  document.getElementById('deleteConfirmBackdrop').classList.remove('open');
  if (itemId === null) return;

  const fd = new FormData();
  fd.append('action', 'delete_item');
  fd.append('item_id', itemId);

  fetch('ajax_handler.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        showToast('ลบรายการ "' + itemName + '" สำเร็จ');
        const idx = ALL_ITEMS.findIndex(i => parseInt(i.id) === parseInt(data.item_id));
        if (idx !== -1) ALL_ITEMS.splice(idx, 1);
        filterTable();
      } else {
        showToast(data.error || 'เกิดข้อผิดพลาด', 'error');
      }
    })
    .catch(() => showToast('เกิดข้อผิดพลาด', 'error'))
    .finally(() => {
      _pendingDeleteId = null;
      _pendingDeleteName = null;
    });
}

function previewImage(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => { document.getElementById('edPreview').src = e.target.result; };
    reader.readAsDataURL(input.files[0]);
  }
}

function submitStockIn(e) {
  e.preventDefault();
  const qty = parseInt(document.getElementById('siQty').value);
  if (!qty || qty < 1) { showToast('กรุณาป้อนจำนวนที่ถูกต้อง', 'error'); return; }

  const fd = new FormData(document.getElementById('stockInForm'));
  fd.append('action', 'stock_in');

  fetch('ajax_handler.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        showToast('เติมสต็อกสำเร็จ +' + qty + ' ' + data.unit);
        refreshRow(data.item_id, data.new_stock, data.unit, data.min_stock, data.group_name, data.name, data.image, data.pack_qty);
        currentStock = data.new_stock;
        document.getElementById('modalStockVal').textContent = data.new_stock;
        const soMax = Math.max(data.new_stock, 1);
        document.getElementById('soQty').max = soMax;
        document.getElementById('soQtyNum').max = soMax;
        document.getElementById('soMaxLabel').textContent = data.new_stock;
        document.getElementById('soQty').disabled = data.new_stock < 1;
        document.getElementById('soQtyNum').disabled = data.new_stock < 1;
        document.getElementById('stockInForm').reset();
        document.getElementById('siDate').value = '';
        document.getElementById('siQty').max = 1000;
        document.getElementById('siMaxLabel').textContent = '1000';
        updateModalBadge(data.new_stock, data.min_stock);
      } else {
        showToast(data.error || 'เกิดข้อผิดพลาด', 'error');
      }
    })
    .catch(() => showToast('เกิดข้อผิดพลาด', 'error'));
}

function submitStockOut(e) {
  e.preventDefault();
  const qty = parseInt(document.getElementById('soQty').value);
  if (!qty || qty < 1) { showToast('กรุณาป้อนจำนวนที่ถูกต้อง', 'error'); return; }
  if (qty > currentStock) { showToast('จำนวนที่เบิกเกินสต็อกที่มี', 'error'); return; }

  const fd = new FormData(document.getElementById('stockOutForm'));
  fd.append('action', 'stock_out');

  fetch('ajax_handler.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        showToast('เบิกอุปกรณ์สำเร็จ -' + qty + ' ' + data.unit);
        refreshRow(data.item_id, data.new_stock, data.unit, data.min_stock, data.group_name, data.name, data.image, data.pack_qty);
        currentStock = data.new_stock;
        document.getElementById('modalStockVal').textContent = data.new_stock;
        const soMax = Math.max(data.new_stock, 1);
        document.getElementById('soQty').max = soMax;
        document.getElementById('soQtyNum').max = soMax;
        document.getElementById('soMaxLabel').textContent = data.new_stock;
        document.getElementById('soQty').value = 1;
        document.getElementById('soQtyNum').value = 1;
        document.getElementById('soQty').disabled = data.new_stock < 1;
        document.getElementById('soQtyNum').disabled = data.new_stock < 1;
        document.getElementById('stockOutForm').reset();
        document.getElementById('soDate').value = '';
        updateModalBadge(data.new_stock, data.min_stock);
      } else {
        showToast(data.error || 'เกิดข้อผิดพลาด', 'error');
      }
    })
    .catch(() => showToast('เกิดข้อผิดพลาด', 'error'));
}

function submitEditDetails(e) {
  e.preventDefault();
  const fd = new FormData(document.getElementById('editDetailsForm'));
  fd.append('action', 'update_item');

  fetch('ajax_handler.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        showToast('บันทึกการแก้ไขสำเร็จ');
        const idx = ALL_ITEMS.findIndex(i => i.id == data.item_id);
        if (idx !== -1) {
          ALL_ITEMS[idx].name       = data.name;
          ALL_ITEMS[idx].group_name = data.group_name;
          ALL_ITEMS[idx].unit       = data.unit;
          ALL_ITEMS[idx].pack_qty   = data.pack_qty;
          ALL_ITEMS[idx].image      = data.image;
        }
        filterTable();
        closeModal();
      } else {
        showToast(data.error || 'เกิดข้อผิดพลาด', 'error');
      }
    })
    .catch(() => showToast('เกิดข้อผิดพลาด', 'error'));
}

function refreshRow(itemId, newStock, unit, minStock, groupName, name, image, packQty) {
  const idx = ALL_ITEMS.findIndex(i => parseInt(i.id) === parseInt(itemId));
  if (idx !== -1) {
    ALL_ITEMS[idx].current_stock = newStock;
    ALL_ITEMS[idx].unit          = unit;
    ALL_ITEMS[idx].min_stock     = minStock;
    ALL_ITEMS[idx].name          = name;
    ALL_ITEMS[idx].group_name    = groupName;
    ALL_ITEMS[idx].image         = image;
    ALL_ITEMS[idx].pack_qty      = packQty;
  }
  filterTable();
}

function updateModalBadge(st, min) {
  let badgeClass, badgeText;
  if (st === 0)       { badgeClass='badge-out-stock'; badgeText='หมดแล้ว'; }
  else if (st <= min) { badgeClass='badge-low-stock';  badgeText='ใกล้หมด'; }
  else                { badgeClass='badge-in-stock';   badgeText='มีสต็อก'; }
  document.getElementById('modalCurrentBadge').innerHTML =
    `<span class="badge ${badgeClass}">${badgeText}</span>`;
}

function escHtml(str) {
  return String(str)
    .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
    .replace(/"/g,'&quot;').replace(/'/g,'&#039;');
}
function escAttr(str) { return escHtml(str); }

function packNoteHtml(packQty, unit) {
  const n = parseInt(packQty);
  if (!n || n < 1) return '';
  return `<small class="pack-note">1 แพ็ค / ${n.toLocaleString('th-TH')} ${escHtml(unit)}</small>`;
}

function showToast(msg, type = 'success') {
  const el = document.createElement('div');
  el.className = 'toast ' + type;
  el.innerHTML = (type === 'success' ? '✅ ' : '❌ ') + escHtml(msg);
  document.getElementById('toastContainer').appendChild(el);
  setTimeout(() => {
    el.style.animation = 'toastOut .3s ease forwards';
    setTimeout(() => el.remove(), 300);
  }, 3200);
}

filterTable();
</script>