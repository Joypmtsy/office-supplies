<!-- TAB 1: Overview -->
<div class="folder-content active" id="overview-content" role="tabpanel">
  <div class="folder-content__header">
    <div>
      <h2 class="folder-content__title">ภาพรวมสต็อก</h2>
      <p class="folder-content__subtitle">สรุปจำนวนรายการและสถานะคงคลัง</p>
    </div>
  </div>

  <!-- KPI Cards Grid -->
  <div class="cards-grid" role="list" style="margin: 0;">
    <div class="stat-card layered-card" role="listitem" tabindex="0"
         onclick="loadCategoryInTab('all', this)" onkeydown="if(event.key==='Enter')loadCategoryInTab('all',this)">
      <div class="stat-card__badge">→</div>
      <div class="stat-card__icon blue">📦</div>
      <div class="stat-card__value"><?= $totalItems ?></div>
      <div class="stat-card__label">อุปกรณ์ทั้งหมด</div>
      <div class="stat-card__sub">รายการในระบบทั้งหมด</div>
    </div>

    <div class="stat-card layered-card" role="listitem" tabindex="0"
         onclick="loadCategoryInTab('low', this)" onkeydown="if(event.key==='Enter')loadCategoryInTab('low',this)">
      <div class="stat-card__badge">→</div>
      <div class="stat-card__icon orange">⚠️</div>
      <div class="stat-card__value"><?= $lowStock ?></div>
      <div class="stat-card__label">สต็อกใกล้หมด</div>
      <div class="stat-card__sub">สต็อก 0 &lt; จำนวน ≤ ขั้นต่ำ</div>
    </div>

    <div class="stat-card layered-card" role="listitem" tabindex="0"
         onclick="loadCategoryInTab('out', this)" onkeydown="if(event.key==='Enter')loadCategoryInTab('out',this)">
      <div class="stat-card__badge">→</div>
      <div class="stat-card__icon red">🚫</div>
      <div class="stat-card__value"><?= $outOfStock ?></div>
      <div class="stat-card__label">สินค้าหมดสต็อก</div>
      <div class="stat-card__sub">สต็อก = 0</div>
    </div>
  </div>

  <!-- Dynamic Result (loaded via AJAX) -->
  <div id="overviewResult" style="margin-top: 20px;"></div>
</div>