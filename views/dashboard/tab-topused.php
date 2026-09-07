<!-- TAB 3: Top Used -->
<div class="folder-content" id="topused-content" role="tabpanel">
  <div class="folder-content__header">
    <div>
      <h2 class="folder-content__title">5 อันดับอุปกรณ์เบิกเยอะสุด</h2>
      <p class="folder-content__subtitle">สินค้าที่ถูกเบิกใช้งานมากที่สุด</p>
    </div>
  </div>

  <div class="layered-cards">
    <?php if ($topUsed): ?>
      <div class="tbl-wrap">
        <table id="topUsedTable">
          <thead>
            <tr>
              <th style="width:10%;">#</th>
              <th style="width:60%;">รายการ</th>
              <th style="width:30%;text-align:center;">รวมเบิก</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($topUsed as $rank => $it): ?>
            <tr>
              <td style="font-weight:700;color:var(--accent);text-align:center;">
                <?= $rank + 1 ?>
              </td>
              <td>
                <div style="font-weight:600;"><?= htmlspecialchars($it['name']) ?></div>
                <?php $packNote = formatPackNote($it['pack_qty'], $it['unit']); ?>
                <?php if ($packNote !== ''): ?>
                  <small class="pack-note"><?= htmlspecialchars($packNote) ?></small>
                <?php endif; ?>
              </td>
              <td style="font-weight:700;text-align:center;">
                <?= number_format((int)$it['total_out']) ?>
                <small style="font-weight:400;color:var(--text-muted);"><?= htmlspecialchars($it['unit']) ?></small>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="empty-state">
        <p>ยังไม่มีประวัติการเบิก</p>
      </div>
    <?php endif; ?>
  </div>
</div>