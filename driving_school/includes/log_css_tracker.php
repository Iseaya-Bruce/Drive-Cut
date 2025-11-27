<?php
// log_css_tracker.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if ($data) {
        $logFile = __DIR__ . "/css_tracker_log.json";

        // If file doesn't exist yet, initialize as empty array
        if (!file_exists($logFile)) {
            file_put_contents($logFile, json_encode([]));
        }

        // Load current logs
        $logs = json_decode(file_get_contents($logFile), true);

        // Append new entry
        $logs[] = [
            "page" => $data['page'],
            "classes" => $data['classes'],
            "timestamp" => date("Y-m-d H:i:s")
        ];

        // Save back
        file_put_contents($logFile, json_encode($logs, JSON_PRETTY_PRINT));

        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid data"]);
    }
}
