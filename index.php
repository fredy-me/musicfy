<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "config/db.php";

$singers = mysqli_query($conn, "SELECT * FROM singers");
?>

<link rel="stylesheet" href="assets/css/style.css">

<div class="grid">
<?php while ($s = mysqli_fetch_assoc($singers)) { ?>
    <a href="pages/singer.php?id=<?php echo $s['id']; ?>" class="card">
        <img src="assets/images/singers/<?php echo $s['image']; ?>">
        <h3><?php echo $s['name']; ?></h3>
    </a>
<?php } ?>
</div>
