<?php  session_start();
include "db_config.php";

if(isset($_GET['title'])){
    try {
        $conn = new PDO("mysql:host=$host; dbname=$dbName", $user, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->beginTransaction();
        
        $ivykis = 'Task / Delete';
        $naujinimas = date("Y-m-d H:i:s");
        
        try{
            $sql = "DELETE FROM projektu_uzduotys WHERE Uzduoties_id='" . $_GET["Uzduoties_id"] ."'";
            $sql3 = "DELETE FROM uzduotys WHERE Uzduoties_id='" . $_GET["Uzduoties_id"] . "'";
            $sql2 = "INSERT INTO history VALUES (?,?,?,?,?,?)";
    
            $conn->exec($sql);
            $conn->exec($sql3);
            
            $statement2 = $conn->prepare($sql2);
            $statement2->execute(['',$ivykis,$_GET['title'], $naujinimas, $_SESSION['userId'], $_SESSION['username']]);
            
            $conn->commit();
            header("Location: task.php?title=".$_GET["title"]."&Projekto_id=".$_GET["Projekto_id"]."");
        
        
        }catch(Exception $e){
            $conn->rollBack();
             $_SESSION['message'] =  "Database connection lost.";
        }
        }
    catch(PDOException $e)
        {
        echo $sql . "
    " . $e->getMessage();
        }
}
