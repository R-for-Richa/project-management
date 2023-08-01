<?php
session_start();
if (isset($_SESSION["username"])) {
    if (isset($_POST['logout'])) {
        session_destroy();
        header("location:index.php");
    }
} else {
    header("location:index.php");
}
?>
