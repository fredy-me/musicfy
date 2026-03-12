<?php
require_once 'db.php';

$errors = [];
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $singerName = trim($_POST['singer_name'] ?? '');
    $songTitle = trim($_POST['song_title'] ?? '');

    if ($singerName === '') {
        $errors[] = 'Singer name is required.';
    }

    if ($songTitle === '') {
        $errors[] = 'Song title is required.';
    }

    if (!isset($_FILES['cover_image']) || $_FILES['cover_image']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Please upload a cover image.';
    }

    if (!isset($_FILES['audio_file']) || $_FILES['audio_file']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Please upload an MP3 audio file.';
    }

    $coverPath = '';
    $audioPath = '';

    if (empty($errors)) {
        $coverDirectory = __DIR__ . '/uploads/covers/';
        $musicDirectory = __DIR__ . '/uploads/music/';

        if (!is_dir($coverDirectory)) {
            mkdir($coverDirectory, 0777, true);
        }

        if (!is_dir($musicDirectory)) {
            mkdir($musicDirectory, 0777, true);
        }

        $coverExtension = strtolower(pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION));
        $audioExtension = strtolower(pathinfo($_FILES['audio_file']['name'], PATHINFO_EXTENSION));

        $allowedImageExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        $allowedAudioExtensions = ['mp3'];

        if (!in_array($coverExtension, $allowedImageExtensions, true)) {
            $errors[] = 'Cover image must be JPG, JPEG, PNG, or WEBP.';
        }

        if (!in_array($audioExtension, $allowedAudioExtensions, true)) {
            $errors[] = 'Audio file must be in MP3 format.';
        }

        if (empty($errors)) {
            $coverFileName = uniqid('cover_', true) . '.' . $coverExtension;
            $audioFileName = uniqid('song_', true) . '.' . $audioExtension;

            $coverPath = 'uploads/covers/' . $coverFileName;
            $audioPath = 'uploads/music/' . $audioFileName;

            $coverUploaded = move_uploaded_file($_FILES['cover_image']['tmp_name'], __DIR__ . '/' . $coverPath);
            $audioUploaded = move_uploaded_file($_FILES['audio_file']['tmp_name'], __DIR__ . '/' . $audioPath);

            if (!$coverUploaded || !$audioUploaded) {
                $errors[] = 'Failed to upload files. Please try again.';

                if ($coverUploaded && file_exists(__DIR__ . '/' . $coverPath)) {
                    unlink(__DIR__ . '/' . $coverPath);
                }

                if ($audioUploaded && file_exists(__DIR__ . '/' . $audioPath)) {
                    unlink(__DIR__ . '/' . $audioPath);
                }
            }
        }
    }

    if (empty($errors)) {
        $statement = $conn->prepare(
            "INSERT INTO songs (singer_name, song_title, cover_image, audio_file, created_at) VALUES (?, ?, ?, ?, NOW())"
        );

        if ($statement === false) {
            $errors[] = 'Failed to prepare database query.';
        } else {
            $statement->bind_param('ssss', $singerName, $songTitle, $coverPath, $audioPath);

            if ($statement->execute()) {
                header('Location: index.php?message=' . urlencode('Song added successfully.'));
                exit;
            }

            $errors[] = 'Failed to save song to the database.';

            if ($coverPath !== '' && file_exists(__DIR__ . '/' . $coverPath)) {
                unlink(__DIR__ . '/' . $coverPath);
            }

            if ($audioPath !== '' && file_exists(__DIR__ . '/' . $audioPath)) {
                unlink(__DIR__ . '/' . $audioPath);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Song | Musicfy</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="page small-page">
        <header class="hero form-hero">
            <div>
                <p class="eyebrow">Music Upload Form</p>
                <h1>Add Song</h1>
                <p class="hero-text">Upload a cover image and an MP3 file, then save both paths in MySQL.</p>
            </div>
            <a class="button secondary" href="index.php">Back to Song List</a>
        </header>

        <?php if (!empty($errors)): ?>
            <div class="alert error">
                <ul class="error-list">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="add_song.php" method="POST" enctype="multipart/form-data" class="song-form">
            <div class="form-group">
                <label for="singer_name">Singer Name</label>
                <input
                    type="text"
                    id="singer_name"
                    name="singer_name"
                    placeholder="e.g. Burna Boy"
                    value="<?php echo htmlspecialchars($_POST['singer_name'] ?? ''); ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="song_title">Song Title</label>
                <input
                    type="text"
                    id="song_title"
                    name="song_title"
                    placeholder="e.g. Last Last"
                    value="<?php echo htmlspecialchars($_POST['song_title'] ?? ''); ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="cover_image">Cover Image</label>
                <input type="file" id="cover_image" name="cover_image" accept=".jpg,.jpeg,.png,.webp,image/*" required>
            </div>

            <div class="form-group">
                <label for="audio_file">Audio File (MP3)</label>
                <input type="file" id="audio_file" name="audio_file" accept=".mp3,audio/mpeg" required>
            </div>

            <button type="submit" class="button primary full-width">Save Song</button>
        </form>
    </div>
</body>
</html>
