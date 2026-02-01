<?php
include "../config/db.php";
$singers = mysqli_query($conn, "SELECT * FROM singers");

if (isset($_POST['submit'])) {
    $singer_id = $_POST['singer'];
    $title = $_POST['title'];
    $type = $_POST['type'];
    $file = $_FILES['file']['name'];
    $tmp = $_FILES['file']['tmp_name'];

    $folder = $type == "audio" ? "audio" : "video";
    move_uploaded_file($tmp, "../assets/$folder/$file");

    mysqli_query($conn, "INSERT INTO media(singer_id,title,type,file)
                         VALUES('$singer_id','$title','$type','$file')");
}
?>

<form method="POST" enctype="multipart/form-data">
  <select name="singer">
    <?php while($s = mysqli_fetch_assoc($singers)) { ?>
      <option value="<?= $s['id'] ?>"><?= $s['name'] ?></option>
    <?php } ?>
  </select>

  <input type="text" name="title" placeholder="Song / Video Title" required>

  <select name="type">
    <option value="audio">Audio</option>
    <option value="video">Video</option>
  </select>

  <input type="file" name="file" required>
  <button name="submit">Upload</button>
</form>
