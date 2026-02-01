<?php
include "config/db.php";
$singers = mysqli_query($conn, "SELECT * FROM singers");
?>

<link rel="stylesheet" href="assets/css/style.css">

<div class="grid">
<?php while($s = mysqli_fetch_assoc($singers)) { ?>
  <a href="pages/singer.php?id=<?= $s['id'] ?>" class="card">
    <img src="assets/images/singers/<?= $s['image'] ?>">
    <h3><?= $s['name'] ?></h3>
  </a>
<?php } ?>
</div>
