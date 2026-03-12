<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?message=' . urlencode('Invalid request.') . '&type=error');
    exit;
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($id <= 0) {
    header('Location: index.php?message=' . urlencode('Song ID is missing.') . '&type=error');
    exit;
}

$selectStatement = $conn->prepare("SELECT cover_image, audio_file FROM songs WHERE id = ?");

if ($selectStatement === false) {
    header('Location: index.php?message=' . urlencode('Failed to prepare delete query.') . '&type=error');
    exit;
}

$selectStatement->bind_param('i', $id);
$selectStatement->execute();
$result = $selectStatement->get_result();
$song = $result->fetch_assoc();

if (!$song) {
    header('Location: index.php?message=' . urlencode('Song not found.') . '&type=error');
    exit;
}

$deleteStatement = $conn->prepare("DELETE FROM songs WHERE id = ?");

if ($deleteStatement === false) {
    header('Location: index.php?message=' . urlencode('Failed to prepare delete statement.') . '&type=error');
    exit;
}

$deleteStatement->bind_param('i', $id);

if ($deleteStatement->execute()) {
    $coverPath = __DIR__ . '/' . $song['cover_image'];
    $audioPath = __DIR__ . '/' . $song['audio_file'];

    if (is_file($coverPath)) {
        unlink($coverPath);
    }

    if (is_file($audioPath)) {
        unlink($audioPath);
    }

    header('Location: index.php?message=' . urlencode('Song deleted successfully.'));
    exit;
}

header('Location: index.php?message=' . urlencode('Failed to delete song.') . '&type=error');
exit;
