<?php
// ziiquatic - Public Portfolio API
// Security Hardening: GET Only, Security Headers, Safe JSON Delivery

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method !== 'GET') {
    http_response_code(405);
    header('Allow: GET');
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Cache-Control: no-cache, no-store, must-revalidate');

$dataFile = __DIR__ . '/../data/projects.json';

if (file_exists($dataFile)) {
    $content = @file_get_contents($dataFile);
    if (!empty(trim((string)$content))) {
        // Verifikasi validitas JSON
        $decoded = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            echo $content;
            exit;
        }
    }
}

// Fallback jika file kosong atau tidak valid
echo json_encode([]);
