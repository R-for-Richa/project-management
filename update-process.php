
<?php
include_once('header.php');
 include_once('db_config.php');
    try {


    $servername='localhost';
    $username='root';
    $password='';
    $dbname = "projektas";
    $conn=mysqli_connect($servername,$username,$password,"$dbname");
    if(!$conn){
        die('Could not Connect My Sql:'.mysql_error());
    }
    if(count($_POST)>0) {
            mysqli_query($conn,"UPDATE projektai set Projekto_id='" . $_POST['Projekto_id'] . "', Pavadinimas='" . $_POST['Pavadinimas'] . "', Aprasymas='" . $_POST['Aprasymas'] . "' WHERE Projekto_id='" . $_POST['Projekto_id'] . "'");
            $message = "Record Modified Successfully";
        }
            $result = mysqli_query($conn,"SELECT * FROM projektai WHERE Projekto_id='" . $_GET['Projekto_id'] . "'");
            $row= mysqli_fetch_array($result);
        } catch (PDOException $error) {  //Jei nepavyksta prisijungti ismeta klaidos pranesima
        echo $error->getMessage();
        }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Update Employee Data</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <link href="css/style.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200;300;500&display=swap" rel="stylesheet">
</head>
<header>
    <nav class="navbar navbar-dark bg-dark">
        <a class="navbar-brand mr-auto">Project manager</a>
        <div class="form-inline">
            <?php
            echo '<p class="login-name">' . $_SESSION["username"] . '</p>';
            ?>
            <form class="form-unstyled" method="POST">
                <button type="submit" name="logout" class="btn btn-outline-success my-2 my-sm-0">Log out</button>
            </form>
        </div>
    </nav>
</header>

<body>
    <form name="frmUser" method="post" action="">


      <br><td><label>Pavadinimas:</label></td><br>

<input type="hidden" name="Projekto_id" class="txtField" value="<?php echo $row['Projekto_id']; ?>">
<input type="hidden" name="userid"  value="<?php echo $row['userid']; ?>">

        <input type="text" name="Pavadinimas" value="<?php echo $row['Pavadinimas']; ?>">

  <br><td><label>Aprasymas:</label></td><br>
        <input type="text" name="Aprasymas" value="<?php echo $row['Aprasymas']; ?>">
        <br>


<input type="submit" name="submit" value="Update" class="buttom">

    </form>

     <div><?php if(isset($message)) { echo $message; } ?>
        </div>
        <div style="padding-bottom:5px;">
            <a href="main.php">Project List</a>
        </div>
</body>

</html>
