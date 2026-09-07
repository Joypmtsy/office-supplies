<?php
/**
 * history.php – หน้าหลักประวัติการเบิก (Main Controller & View Importer)
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

// 1. ส่วนหัวเว็บไซต์ (HTML Head & CSS)
require_once __DIR__ . '/views/history/header.php';

// 2. แถบเมนูด้านบน (Navbar)
require_once __DIR__ . '/views/history/navbar.php';
?>

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
    <!-- 3. แถบค้นหา และ แถบแท็บเลือกอุปกรณ์ -->
    <?php require_once __DIR__ . '/views/history/tab_bar.php'; ?>
  <?php endif; ?>
</main>

<!-- ── Toast container ───────────────────────────────────────── -->
<div id="toastContainer"></div>

<?php
// 4. สคริปต์การทำงาน (AJAX, Pagination, Tab Selection)
require_once __DIR__ . '/views/history/scripts.php';
?>
</body>
</html>