<!-- TAB 2: Alerts -->
<div class="folder-content" id="alerts-content" role="tabpanel">
  <div class="folder-content__header">
    <div>
      <h2 class="folder-content__title"> อุปกรณ์ต้องเติมด่วน</h2>
      <p class="folder-content__subtitle"><?= count($alertItems) ?> รายการที่ต้องดำเนินการ</p>
    </div>
  </div>

  <div class="layered-cards">
    <?php if ($alertItems): ?>
      <div class="tbl-wrap">
        <table id="alertTable">
          <thead>
            <tr>
              <th style="width:46%;">ชื่ออุปกรณ์</th>
              <th style="width:20%;text-align:center;">คงเหลือ</th>
              <th style="width:16%;text-align:center;">ขั้นต่ำ</th>
              <th style="width:18%;text-align:center;">สถานะ</th>
            </tr>
          </thead>
          <tbody id="alertTableBody"></tbody>
        </table>
      </div>
      <div id="alertPagination" class="pagination"></div>
    <?php else: ?>
      <div class="empty-state">
        <div class="es-icon">✅</div>
        <p>ไม่มีรายการที่ต้องแจ้งเตือน</p>
      </div>
    <?php endif; ?>
  </div>
</div>