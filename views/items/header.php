<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>รายการอุปกรณ์ – Office Supplies</title>
  <meta name="description" content="รายการวัสดุสำนักงานทั้งหมด พร้อมสถานะสต็อกและการจัดการ">
  <link rel="stylesheet" href="style.css">
  <style>
    td.img-cell { border-right: 1.5px solid var(--border); }
    #itemsTable { table-layout: fixed; }
    #itemsTable th, #itemsTable td { overflow-wrap: break-word; }
    #itemsTable td.img-cell {
      background: var(--surface, #fff);
      border-top-left-radius: var(--radius-lg);
      border-bottom-left-radius: var(--radius-lg);
      overflow: hidden;
      border-bottom: 2px solid var(--border);
    }
    #itemsTable tbody tr td:last-child {
      background: var(--surface, #fff);
      border-top-right-radius: var(--radius-lg);
      border-bottom-right-radius: var(--radius-lg);
    }
    #itemsTable .row-group-end td { border-bottom: 2px solid var(--border); }
    input[type="file"] {
      font-family: 'Sarabun', sans-serif;
      font-size: .85rem; padding: 6px;
      border: 1.5px dashed var(--border);
      border-radius: var(--radius-sm);
      width: 100%; cursor: pointer;
      background: #f8f6f0;
    }
    input[type="file"]:hover { border-color: var(--accent); }
    .col-status { text-align: center; }
    .col-actions { text-align: center; }
    .col-stock, .col-unit { text-align: center; }
    #itemsTable td.col-stock, #itemsTable td.col-unit { text-align: center; }
    .col-actions .action-buttons {
      display: flex; align-items: center; justify-content: center; gap: 0;
    }
    .btn-delete:hover { background: var(--out-stock-bg) !important; }
  </style>
</head>
<body>