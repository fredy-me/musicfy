<?php
// admin/add_singer.php - simple starter form
require_once __DIR__ . '/../config/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // handle singer upload (name, image, bio) - implement later
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Add Singer - Admin</title>
</head>
<body>
    <h1>Add Singer</h1>
    <form method="post" enctype="multipart/form-data">
        <label>Name: <input type="text" name="name"></label><br>
        <label>Photo: <input type="file" name="photo"></label><br>
        <label>Bio:<br><textarea name="bio" rows="5"></textarea></label><br>
        <button type="submit">Add Singer</button>
    </form>
</body>
</html>
