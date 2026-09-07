<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ประวัติการเบิก – Office Supplies</title>
  <meta name="description" content="ประวัติการเบิกและเติมสต็อกวัสดุสำนักงานแยกตามรายการ">
  <link rel="stylesheet" href="style.css">
  <style>
    /* Item-detail header shown above the transaction list */
    .item-detail-flex {
      display: flex; align-items: center; gap: 16px; flex-wrap: wrap;
    }
    .item-detail-flex .item-thumb { width: 68px; height: 68px; flex-shrink: 0; }
    .item-detail-meta { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-top: 6px; }
    .item-detail-meta .stock-num { font-size: 1.3rem; }

    /* One transaction row */
    .txn-card { cursor: default; }
    .txn-card:hover { transform: none; border-color: var(--border); background: var(--surface); }
    .txn-card__top {
      display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;
    }
    .txn-card__date { font-weight: 700; color: var(--dark); font-size: .95rem; }
    .txn-card__meta { color: var(--text-muted); font-size: .85rem; margin-top: 4px; line-height: 1.7; }
    .txn-card__qty { font-weight: 800; font-size: 1.15rem; text-align: right; white-space: nowrap; }
    .txn-card__qty.in { color: #1d8a3d; }
    .txn-card__qty.out { color: var(--out-stock-bg); }

    /* Item tab bar: no icon badge needed, plain text tabs */
    #historyTabBar .folder-tab { min-width: unset; padding: 10px 16px; }
    #historyTabBar .folder-tab__label { max-width: 220px; overflow: hidden; text-overflow: ellipsis; }
  </style>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
</head>
<body>