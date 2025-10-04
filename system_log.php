<?php

function system_log($message) {
    $logDir  = __DIR__ . '/logs'; // creates a "logs" folder beside this PHP file
    $logFile = $logDir . '/system_logs.txt';

    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }

    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
    $logMessage = "[{$timestamp}] [{$ip}] {$message}\n";

    file_put_contents($logFile, $logMessage, FILE_APPEND);
}
