<?php
/**
 * db.php  –  PDO singleton connection + shared small view helpers
 * Database: office_supplies | Charset: utf8mb4
 */
declare(strict_types=1);

function getDB(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $host    = '127.0.0.1';
        $port    = '8889';       // แก้จาก 3306 เป็น 8889 (พอร์ต MySQL ของ MAMP)
        $dbname  = 'office_supplies';
        $user    = 'root';
        $pass    = 'root';       // แก้จาก '' เป็น 'root' (รหัสผ่านเริ่มต้นของ MAMP)
        $dsn     = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            http_response_code(500);
            die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
        }
    }
    return $pdo;
}

/**
 * formatPackNote – builds the small "1 แพ็ค / N หน่วย" caption shown
 * under an item's name wherever the item name is displayed.
 * Returns '' when the item has no pack_qty set, so callers can render
 * it unconditionally (empty string = nothing shown).
 *
 * @param int|string|null $packQty  items.pack_qty (nullable)
 * @param string          $unit     items.unit
 */
function formatPackNote($packQty, string $unit): string
{
    $packQty = (int) $packQty;
    if ($packQty < 1) {
        return '';
    }
    return '1 แพ็ค / ' . number_format($packQty) . ' ' . $unit;
}