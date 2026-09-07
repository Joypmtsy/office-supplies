<div class="toolbar">
  <div class="search-wrap">
    <span class="search-icon">🔍</span>
    <input type="text" id="searchInput" placeholder="ค้นหาชื่อหรือกลุ่มรายการ..."
           aria-label="ค้นหาอุปกรณ์" oninput="currentPage = 1; filterTable();">
  </div>
  <div class="select-wrap">
    <select id="sortSelect" aria-label="เรียงลำดับ" onchange="currentPage = 1; filterTable();">
      <option value="name_asc">เรียงชื่อ ก → ฮ</option>
      <option value="name_desc">เรียงชื่อ ฮ → ก</option>
      <option value="stock_asc">สต็อกน้อย → มาก</option>
      <option value="stock_desc">สต็อกมาก → น้อย</option>
    </select>
  </div>
  <button type="button" class="btn btn-primary" onclick="openAddModal()">➕ เพิ่มรายการใหม่</button>
</div>

<div class="panel">
  <div class="tbl-wrap">
    <table id="itemsTable">
      <thead>
        <tr>
          <th style="width:8%;">รูปภาพ</th>
          <th style="width:46%;">รายการ</th>
          <th class="col-stock" style="width:10%;">สต็อก</th>
          <th class="col-unit" style="width:9%;">หน่วย</th>
          <th class="col-status" style="width:16%;">สถานะ</th>
          <th class="col-actions" style="width:11%;"></th>
        </tr>
      </thead>
      <tbody id="tableBody">
        <?php
        foreach ($groups as $groupName => $items):
          $count = count($items);
          $first = true;
          $lastIdx = $count - 1;
          foreach ($items as $idx => $item):
            $st  = (int)$item['current_stock'];
            $min = (int)$item['min_stock'];
            if ($st === 0) {
                $badgeClass = 'badge-out-stock'; $badgeText = 'หมดแล้ว';
                $stockClass = 'zero';
            } elseif ($st <= $min) {
                $badgeClass = 'badge-low-stock'; $badgeText = 'ใกล้หมด';
                $stockClass = 'low';
            } else {
                $badgeClass = 'badge-in-stock'; $badgeText = 'มีสต็อก';
                $stockClass = '';
            }
            $imageSrc = 'assets/images/' . htmlspecialchars($item['image']);
            $rowClass  = trim(($first ? 'row-group-start ' : '') . ($idx === $lastIdx ? 'row-group-end' : ''));
        ?>
        <tr class="<?= $rowClass ?>"
            data-group="<?= htmlspecialchars($groupName) ?>"
            data-name="<?= htmlspecialchars($item['name']) ?>"
            data-stock="<?= $st ?>">
          <?php if ($first): ?>
          <td class="img-cell" rowspan="<?= $count ?>">
            <img src="<?= $imageSrc ?>"
                 alt="<?= htmlspecialchars($groupName) ?>"
                 class="item-thumb"
                 onerror="this.src='assets/images/default.png'">
          </td>
          <?php endif; ?>
          <td>
            <?= htmlspecialchars($item['name']) ?>
            <?php 
              $packNote = '';
              $n = (int)$item['pack_qty'];
              if ($n > 1) {
                  $packNote = '1 แพ็ค / ' . number_format($n) . ' ' . htmlspecialchars($item['unit']);
              }
            ?>
            <?php if ($packNote !== ''): ?>
              <small class="pack-note"><?= $packNote ?></small>
            <?php endif; ?>
          </td>
          <td class="col-stock"><span class="stock-num <?= $stockClass ?>"><?= $st ?></span></td>
          <td class="col-unit"><?= htmlspecialchars($item['unit']) ?></td>
          <td class="col-status">
            <span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span>
          </td>
          <td class="col-actions">
            <div class="action-buttons">
              <button type="button" class="btn-edit" title="เบิก / เติมสต็อก / แก้ไขข้อมูล"
                      onclick="openModal(<?= (int)$item['id'] ?>)">✏️</button>
              <button type="button" class="btn-edit btn-delete" title="ลบรายการ"
                      onclick="deleteItem(<?= (int)$item['id'] ?>, '<?= htmlspecialchars($item['name'], ENT_QUOTES) ?>')">🗑️</button>
            </div>
          </td>
        </tr>
        <?php
            $first = false;
          endforeach;
        endforeach; 
        ?>
      </tbody>
    </table>

    <div id="emptyState" class="empty-state" style="display:none;">
      <div class="es-icon">🔍</div>
      <p>ไม่พบรายการที่ค้นหา</p>
    </div>

    <div id="itemsPagination" class="pagination"></div>
  </div>
</div>