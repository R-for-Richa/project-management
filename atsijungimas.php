<?php
include_once('db_config.php');
$connect = new PDO("mysql:host=$host; dbname=$dbName", $user, $pass);
$connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$ivykis = 'Atsijungimas';
$date1 = date("Y-m-d H:i:s");
$sql3 = "INSERT INTO history VALUES (?,?,?,?,?,?)";
$statement3 = $connect->prepare($sql3);
$statement3->execute(['', $ivykis, '', $date1, $_SESSION["userId"], $_SESSION['username']]);
$connect->commit();
?>