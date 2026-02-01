<?php
include "../config/db.php";

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $image = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

    move_uploaded_file($tmp, "../assets/images/singers/$image");

    mysqli_query($conn, "INSERT INTO singers(name,image) VALUES('$name','$image')");
}
?>

<form method="POST" enctype="multipart/form-data">
  <input type="text" name="name" placeholder="Singer Name" required>
  <input type="file" name="image" required>
  <button name="submit">Add Singer</button>
</form>
