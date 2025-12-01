<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Read data from JSON file
$dataFile = __DIR__ . '/data.json';

if (!file_exists($dataFile)) {
    echo json_encode([]);
    exit;
}

$content = file_get_contents($dataFile);
$data = json_decode($content, true);

if ($data === null) {
    echo json_encode([]);
    exit;
}

echo json_encode($data);
?>
