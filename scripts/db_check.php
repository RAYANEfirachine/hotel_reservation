<?php
require __DIR__ . '/../vendor/autoload.php';

$dsn = 'mysql:host=127.0.0.1;port=3306;dbname=hotelDB2;charset=utf8mb4';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $tables = ['user' => '`user`', 'room_type' => 'room_type', 'room' => 'room', 'reservation' => 'reservation', 'payment' => 'payment'];
    foreach ($tables as $key => $table) {
        $st = $pdo->query("SELECT COUNT(*) as c FROM $table");
        $row = $st->fetch(PDO::FETCH_ASSOC);
        echo "$key: " . ($row['c'] ?? 0) . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
