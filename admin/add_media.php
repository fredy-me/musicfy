<?php
// admin/add_media.php - upload audio/video and associate with singer/album
require_once __DIR__ . '/../config/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // handle media upload - implement later
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Add Media - Admin</title>
</head>
<body>
    <h1>Add Media</h1>
    <form method="post" enctype="multipart/form-data">
        <label>Title: <input type="text" name="title"></label><br>
        <label>File: <input type="file" name="media"></label><br>
        <label>Type: 
            <select name="type">
                <option value="audio">Audio</option>
                <option value="video">Video</option>
            </select>
        </label><br>
        <button type="submit">Upload</button>
    </form>
</body>
</html>
