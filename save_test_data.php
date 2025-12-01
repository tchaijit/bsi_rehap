<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Get JSON data from request
$jsonData = file_get_contents('php://input');
$testData = json_decode($jsonData, true);

if (!$testData) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON data']);
    exit;
}

// Define data file path
$dataFile = __DIR__ . '/pdf/report/data.json';
$dataDir = dirname($dataFile);

// Create directory if it doesn't exist
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}

// Read existing data
$allData = [];
if (file_exists($dataFile)) {
    $content = file_get_contents($dataFile);
    if ($content) {
        $allData = json_decode($content, true) ?: [];
    }
}

// Add new test data
$allData[] = $testData;

// Save back to JSON file
$saved = file_put_contents($dataFile, json_encode($allData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

if ($saved === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save data']);
    exit;
}

// Also update data.js for static access (no CORS issues)
$dataJsFile = $dataDir . '/data.js';
$jsContent = "// Physical Fitness Test Data\n";
$jsContent .= "// This file is automatically updated by save_test_data.php\n";
$jsContent .= "var FITNESS_TEST_DATA = " . json_encode($allData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . ";\n";
file_put_contents($dataJsFile, $jsContent);

// Return success response
echo json_encode([
    'success' => true,
    'message' => 'Test data saved successfully',
    'total_records' => count($allData)
]);
?>
