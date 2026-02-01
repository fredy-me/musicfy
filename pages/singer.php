<?php
include "../config/db.php";
$id = $_GET['id'];
$media = mysqli_query($conn, "SELECT * FROM media WHERE singer_id=$id");
?>

<?php while($m = mysqli_fetch_assoc($media)) { ?>

<h4><?= $m['title'] ?></h4>

<?php if($m['type']=="audio") { ?>
  <audio controls src="../assets/audio/<?= $m['file'] ?>"></audio>
<?php } else { ?>
  <video controls width="400" src="../assets/video/<?= $m['file'] ?>"></video>
<?php } ?>

<?php } ?>
