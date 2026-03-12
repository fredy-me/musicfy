<?php
require_once 'db.php';

$songs = [];
$query = "SELECT id, singer_name, song_title, cover_image, audio_file, created_at FROM songs ORDER BY created_at DESC, id DESC";
$result = $conn->query($query);

if ($result instanceof mysqli_result) {
    while ($row = $result->fetch_assoc()) {
        $songs[] = $row;
    }
}

$message = $_GET['message'] ?? '';
$messageType = $_GET['type'] ?? 'success';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Musicfy</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="page">
        <header class="hero">
            <div>
                <p class="eyebrow">Simple PHP Music App</p>
                <h1>Musicfy</h1>
                <p class="hero-text">Add songs, upload cover images, and play music with the built-in HTML5 audio player.</p>
            </div>
            <a class="button primary" href="add_song.php">Add New Song</a>
        </header>

        <?php if ($message !== ''): ?>
            <div class="alert <?php echo $messageType === 'error' ? 'error' : 'success'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($songs)): ?>
            <section class="empty-state">
                <h2>No songs added yet</h2>
                <p>Start by adding your first song to the Musicfy library.</p>
                <a class="button primary" href="add_song.php">Go to Add Song</a>
            </section>
        <?php else: ?>
            <section class="song-grid">
                <?php foreach ($songs as $song): ?>
                    <article class="song-card">
                        <div class="cover-wrapper">
                            <img
                                src="<?php echo htmlspecialchars($song['cover_image']); ?>"
                                alt="<?php echo htmlspecialchars($song['song_title']); ?> cover"
                                class="cover-image"
                            >
                        </div>

                        <div class="song-content">
                            <p class="song-meta">Added on <?php echo date('M d, Y', strtotime($song['created_at'])); ?></p>
                            <h2><?php echo htmlspecialchars($song['song_title']); ?></h2>
                            <p class="singer-name"><?php echo htmlspecialchars($song['singer_name']); ?></p>

                            <audio controls preload="metadata" class="audio-player" data-audio-player>
                                <source src="<?php echo htmlspecialchars($song['audio_file']); ?>" type="audio/mpeg">
                                Your browser does not support the audio element.
                            </audio>

                            <div class="card-actions">
                                <button type="button" class="button secondary play-toggle" data-play-toggle>
                                    Play / Pause
                                </button>

                                <form action="delete_song.php" method="POST" onsubmit="return confirm('Delete this song?');">
                                    <input type="hidden" name="id" value="<?php echo (int) $song['id']; ?>">
                                    <button type="submit" class="button danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </div>

    <script>
        const cards = document.querySelectorAll('.song-card');

        cards.forEach((card) => {
            const audio = card.querySelector('[data-audio-player]');
            const button = card.querySelector('[data-play-toggle]');

            button.addEventListener('click', () => {
                document.querySelectorAll('[data-audio-player]').forEach((player) => {
                    if (player !== audio) {
                        player.pause();
                    }
                });

                if (audio.paused) {
                    audio.play();
                } else {
                    audio.pause();
                }
            });
        });
    </script>
</body>
</html>
