<?php
session_start();
require '../includes/db_connection.php';

// Pastikan user_id disimpan di session setelah login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Anda harus login untuk mendaftar.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_id = $_POST['event_id'];
    $user_id = $_SESSION['user_id']; // Mengambil user_id dari session

    try {
        // Cek apakah pengguna sudah terdaftar untuk event ini
        $checkStmt = $pdo->prepare("SELECT * FROM registrations WHERE id = ? AND event_id = ?");
        $checkStmt->execute([$user_id, $event_id]);

        if ($checkStmt->rowCount() > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Anda telah register ke event ini.']);
            exit;
        }

        // Hitung jumlah peserta saat ini untuk event tersebut
        $participantCountStmt = $pdo->prepare("SELECT COUNT(*) as participant_count FROM registrations WHERE event_id = ?");
        $participantCountStmt->execute([$event_id]);
        $participantCount = $participantCountStmt->fetch(PDO::FETCH_ASSOC)['participant_count'];

        // Ambil batas maksimum peserta dari tabel events
        $maxParticipantsStmt = $pdo->prepare("SELECT max_participants FROM events WHERE event_id = ?");
        $maxParticipantsStmt->execute([$event_id]);
        $maxParticipants = $maxParticipantsStmt->fetch(PDO::FETCH_ASSOC)['max_participants'];

        // Cek apakah peserta telah mencapai batas maksimum
        if ($participantCount >= $maxParticipants) {
            echo json_encode(['status' => 'error', 'message' => 'Partisipan event telah penuh.']);
            exit;
        }

        // Melakukan pendaftaran
        $stmt = $pdo->prepare("INSERT INTO registrations (id, event_id, registered_at) VALUES (?, ?, NOW())");
        if ($stmt->execute([$user_id, $event_id])) {
            echo json_encode(['status' => 'success']);
        } else {
            $errorInfo = $stmt->errorInfo();
            error_log(print_r($errorInfo, true)); // Log kesalahan
            echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan pada database.']);
        }
    } catch (Exception $e) {
        error_log($e->getMessage()); // Log kesalahan
        echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
