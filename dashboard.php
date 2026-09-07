<?php
/**
 * dashboard.php – หน้าหลักของระบบ (Main Hub) หน้า home
 */
declare(strict_types=1);
header('Content-Type: text/html; charset=utf-8');

session_start();

require_once __DIR__ . '/db.php';
$pdo = getDB();

// ── KPI Queries ───────────────────────────────────────────────
$totalItems  = (int)$pdo->query('SELECT COUNT(*) FROM items')->fetchColumn();
$lowStock    = (int)$pdo->query('SELECT COUNT(*) FROM items WHERE current_stock > 0 AND current_stock <= min_stock')->fetchColumn();
$outOfStock  = (int)$pdo->query('SELECT COUNT(*) FROM items WHERE current_stock = 0')->fetchColumn();

// ── Alert items ───────────────────────────────────────────────
$alertItems = $pdo->query(
    'SELECT id, group_name, name, current_stock, min_stock, unit, pack_qty
     FROM items
     WHERE current_stock <= min_stock
     ORDER BY current_stock ASC, group_name, name'
)->fetchAll();

// ── Top 5 most requisitioned items ────────────────────────────
$topUsed = $pdo->query(
    'SELECT i.name, i.group_name, i.unit, i.pack_qty,
            SUM(t.quantity) AS total_out
     FROM transactions t
     JOIN items i ON i.id = t.item_id
     WHERE t.type = \'STOCK_OUT\'
     GROUP BY t.item_id
     ORDER BY total_out DESC
     LIMIT 5'
)->fetchAll();

// Helper สำหรับคำนวณแพ็ค
if (!function_exists('formatPackNote')) {
    function formatPackNote($packQty, $unit): string {
        $n = (int)$packQty;
        return $n > 0 ? "1 แพ็ค / " . number_format($n) . " " . htmlspecialchars($unit) : '';
    }
}

// ── 1. ส่วนหัวเว็บไซต์ ──
require_once __DIR__ . '/views/dashboard/header.php';

// ── 2. เมนูนำทาง ──
require_once __DIR__ . '/views/dashboard/navbar.php';
?>

<!-- ── PAGE WRAPPER ──────────────────────────────────────────── -->
<main class="page-wrapper">
  <div class="page-header">
    <h1> Home </h1>
    <p>ภาพรวมสต็อกวัสดุสำนักงานทั้งหมด – อัปเดตแบบเรียลไทม์</p>
  </div>

  <div class="folder-tabs-container" id="dashboardFolders">
    
    <!-- แท็บบาร์ด้านบน -->
    <div class="folder-tab-bar" role="tablist" aria-label="Dashboard tabs">
      <div class="folder-tab active" data-tab="0" role="tab" aria-selected="true" aria-controls="overview-content">
        <span class="folder-tab__label">ภาพรวม</span>
      </div>
      <div class="folder-tab" data-tab="1" role="tab" aria-selected="false" aria-controls="alerts-content">
        <span class="folder-tab__label">แจ้งเตือน</span>
        <span class="folder-tab__badge"><?= count($alertItems) ?></span>
      </div>
      <div class="folder-tab" data-tab="2" role="tab" aria-selected="false" aria-controls="topused-content">
        <span class="folder-tab__label">สินค้าเบิกเยอะ</span>
        <span class="folder-tab__badge"><?= count($topUsed) ?></span>
      </div>
    </div>

    <!-- เนื้อหาแต่ละแท็บ -->
    <div class="folder-content-area">
      <?php 
        // แท็บ 1: ภาพรวม
        require_once __DIR__ . '/views/dashboard/tab-overview.php'; 

        // แท็บ 2: แจ้งเตือน
        require_once __DIR__ . '/views/dashboard/tab-alerts.php'; 

        // แท็บ 3: สินค้าเบิกเยอะ
        require_once __DIR__ . '/views/dashboard/tab-topused.php'; 
      ?>
    </div>

  </div>
</main>

<div id="toastContainer"></div>

<?php
// ── 3. สคริปต์ JavaScript ──
require_once __DIR__ . '/views/dashboard/scripts.php';
?>
</body>
</html>