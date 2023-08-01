<?php
session_start();
include_once('db_config.php');

try {
     $connect = new PDO("mysql:host=$host; dbname=$dbName", $user, $pass);
     $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
     if (isset($_POST["login"])) {
           if(empty($_POST["username"]) || empty($_POST["password"]))
           {
                $message = '<span>All fields are required</span>';
           }
           else
           {
          $query = "SELECT * FROM vartotojai WHERE El_pastas = ? AND Slaptazodis = ?";
          $statement = $connect->prepare($query);
          $statement->execute([$_POST["username"], $_POST["password"]]);
          $count = $statement->rowCount();
          if ($count > 0) {
               $statement->setFetchMode(2);
               $result = $statement->fetchAll();
               $_SESSION["username"] = $result[0]['Vardas'];
               $_SESSION["userId"] = $result[0]['Vartotojo_id'];
               header("location:main.php");
               
               
               $ivykis = 'Prisijungimas';
               $date1 = date("Y-m-d H:i:s");
               $sql3 = "INSERT INTO history VALUES (?,?,?,?,?,?)";
               $statement3 = $connect->prepare($sql3);
               $statement3->execute(['', $ivykis, '', $date1, $_SESSION["userId"], $_SESSION['username']]);
               $connect->commit();
               
               
          } else {
               $message = 'Invalid email or password';
          }
           }
     }
} catch (PDOException $error) {
     $message = 'Something went wrong';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <meta name="description" content="Login page">
     <title>Sign in</title>
     <link href="css/style.css" type="text/css" rel="stylesheet">
     <link rel="preconnect" href="https://fonts.gstatic.com">
     <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200;300;500&display=swap" rel="stylesheet">
</head>

<body>
     <div class="header-login">
          <h1 class="login-title">SIGN IN</h1>
     </div>
    <div>
     <form class="form-login" method="POST" id="login-form">
          <input class="input input-login" type="text" name="username" placeholder="Your email" pattern="^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$" title="Enter a valid email address" required>
          <input class="input" type="password" name="password" placeholder="Your password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required>
          <input type="submit" name="login" value="Login" class="input login-btn" id="login-btn"><br>
          
     </form>
          
          <?php
          if (!empty($message)) {
               echo '<p class="error-login">' . $message . '</p><br>';
          }
          ?>

    </div>
</body>
</html>
