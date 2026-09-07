<?php
// จัดกลุ่มอุปกรณ์ตาม group_name ล่วงหน้าฝั่ง PHP
$groupedItems = [];
foreach ($allItems as $item) {
    $grp = trim($item['group_name'] ?? 'ทั่วไป');
    $groupedItems[$grp][] = $item;
}
$firstGroup = array_key_first($groupedItems);
?>

<!-- Toolbar: Search box -->
<div class="toolbar">
  <div class="search-wrap">
    <span class="search-icon">🔍</span>
    <input type="text" id="historySearchInput" placeholder="ค้นหาชื่ออุปกรณ์หรือหมวดหมู่..."
           aria-label="ค้นหาอุปกรณ์" oninput="filterHistoryTabs()">
  </div>
</div>

<div class="folder-tabs-container" id="historyFolders" style="height:auto;">
  
  <!-- ── แท็บชั้นที่ 1: เลือกหมวดหมู่ (Category Tab Bar) ── -->
  <div class="folder-tab-bar category-bar" id="categoryTabBar" role="tablist" aria-label="เลือกหมวดหมู่อุปกรณ์" 
       style="display:flex; flex-wrap:wrap; gap:6px; padding-bottom:8px; border-bottom:2px solid var(--border, #e2e8f0); margin-bottom:8px;">
    <?php foreach (array_keys($groupedItems) as $idx => $grpName): ?>
      <div class="folder-tab category-tab<?= $idx === 0 ? ' active' : '' ?>"
           data-group="<?= htmlspecialchars($grpName) ?>"
           role="tab" tabindex="0"
           onclick="selectCategoryTab(this)"
           onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();selectCategoryTab(this);}">
        <span class="folder-tab__label"><?= htmlspecialchars($grpName) ?></span>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- ── แท็บชั้นที่ 2: เลือกอุปกรณ์ภายในหมวดหมู่ (Item Tab Bar) ── -->
  <div class="folder-tab-bar item-bar" id="historyTabBar" role="tablist" aria-label="เลือกอุปกรณ์">
    <?php 
    $firstItemGlobal = true;
    foreach ($groupedItems as $grpName => $items): 
      foreach ($items as $item):
        $isActive = $firstItemGlobal;
        $firstItemGlobal = false;
    ?>
      <div class="folder-tab item-tab<?= $isActive ? ' active' : '' ?>"
           data-item-id="<?= (int)$item['id'] ?>"
           data-group="<?= htmlspecialchars($grpName) ?>"
           data-search="<?= htmlspecialchars(mb_strtolower($grpName . ' ' . $item['name'])) ?>"
           role="tab" tabindex="0"
           aria-selected="<?= $isActive ? 'true' : 'false' ?>"
           onclick="selectHistoryTab(this)"
           onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();selectHistoryTab(this);}">
        <span class="folder-tab__label"><?= htmlspecialchars($item['name']) ?></span>
      </div>
    <?php 
      endforeach;
    endforeach; 
    ?>
    <div id="historyTabsEmpty" class="folder-tab" style="display:none; cursor:default;">
      <span class="folder-tab__label">ไม่พบรายการในหมวดนี้</span>
    </div>
  </div>

  <!-- Content area โหลดข้อมูล AJAX -->
  <div class="folder-content-area">
    <div id="historyContent">
      <div class="spinner"></div>
    </div>
  </div>
</div>