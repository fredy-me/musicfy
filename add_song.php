<?php
require_once 'db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $singerName = trim($_POST['singer_name'] ?? '');
    $songTitle = trim($_POST['song_title'] ?? '');
    $audioUrl = trim($_POST['audio_url'] ?? '');

    if ($singerName === '') {
        $errors[] = 'Singer name is required.';
    }

    if ($songTitle === '') {
        $errors[] = 'Song title is required.';
    }

    if (!isset($_FILES['cover_image']) || $_FILES['cover_image']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Please upload a cover image.';
    }

    if ($audioUrl === '') {
        $errors[] = 'Music URL is required.';
    } elseif (!filter_var($audioUrl, FILTER_VALIDATE_URL)) {
        $errors[] = 'Please enter a valid music URL.';
    }

    $coverPath = '';

    if (empty($errors)) {
        $coverDirectory = __DIR__ . '/uploads/covers/';

        if (!is_dir($coverDirectory)) {
            mkdir($coverDirectory, 0777, true);
        }

        $coverExtension = strtolower(pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION));

        $allowedImageExtensions = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($coverExtension, $allowedImageExtensions, true)) {
            $errors[] = 'Cover image must be JPG, JPEG, PNG, or WEBP.';
        }

        if (empty($errors)) {
            $coverFileName = uniqid('cover_', true) . '.' . $coverExtension;
            $coverPath = 'uploads/covers/' . $coverFileName;
            $coverUploaded = move_uploaded_file($_FILES['cover_image']['tmp_name'], __DIR__ . '/' . $coverPath);

            if (!$coverUploaded) {
                $errors[] = 'Failed to upload files. Please try again.';

                if ($coverUploaded && file_exists(__DIR__ . '/' . $coverPath)) {
                    unlink(__DIR__ . '/' . $coverPath);
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
            $statement->bind_param('ssss', $singerName, $songTitle, $coverPath, $audioUrl);

            if ($statement->execute()) {
                header('Location: index.php?message=' . urlencode('Song added successfully.'));
                exit;
            }

            $errors[] = 'Failed to save song to the database.';

            if ($coverPath !== '' && file_exists(__DIR__ . '/' . $coverPath)) {
                unlink(__DIR__ . '/' . $coverPath);
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
                <p class="hero-text">Upload a cover image and paste a music URL, then save the details in MySQL.</p>
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
                <label for="audio_url">Music URL</label>
                <input
                    type="url"
                    id="audio_url"
                    name="audio_url"
                    placeholder="https://example.com/song.mp3"
                    value="<?php echo htmlspecialchars($_POST['audio_url'] ?? ''); ?>"
                    required
                >
            </div>

            <button type="submit" class="button primary full-width">Save Song</button>
        </form>
    </div>
</body>
</html>
