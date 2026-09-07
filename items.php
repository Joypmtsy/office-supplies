<?php
/**
 * items.php – หน้าหลักจัดการรายการอุปกรณ์ (Main Orchestrator)
 */
declare(strict_types=1);
header('Content-Type: text/html; charset=utf-8');

session_start();

require_once __DIR__ . '/db.php';
$pdo = getDB();

// ── ดึงข้อมูลแผนกและรายการอุปกรณ์ทั้งหมด ────────────────────────
$departments = $pdo->query('SELECT id, name FROM departments ORDER BY id')->fetchAll();

$allItems = $pdo->query(
    'SELECT id, group_name, name, image, unit, pack_qty, current_stock, min_stock
     FROM items
     ORDER BY group_name, name'
)->fetchAll();

// จัดกลุ่มข้อมูลสำหรับทำ Rowspan ฝั่ง PHP
$groups = [];
foreach ($allItems as $item) {
    $groups[$item['group_name']][] = $item;
}

// ── 1. ส่วนหัวเว็บไซต์ (HTML Head & CSS) ──
require_once __DIR__ . '/views/items/header.php';

// ── 2. แถบเมนูด้านบน (Navbar) ──
require_once __DIR__ . '/views/items/navbar.php';
?>

<main class="page-wrapper">
  <div class="page-header">
    <h1>📋 รายการอุปกรณ์</h1>
    <p>จัดการสต็อก เติม และเบิกวัสดุสำนักงาน</p>
  </div>

  <?php 
    // ── 3. แถบค้นหา และ ตารางแสดงรายการ ──
    require_once __DIR__ . '/views/items/table.php'; 
  ?>
</main>

<?php 
  // ── 4. Popup Modals ทั้งหมด ──
  require_once __DIR__ . '/views/items/modals.php'; 
?>

<div id="toastContainer"></div>

<?php 
  // ── 5. สคริปต์การทำงาน (AJAX, Table Filter, Modal Handlers) ──
  require_once __DIR__ . '/views/items/scripts.php'; 
?>
</body>
</html>