<?php
session_start();
if (isset($_COOKIE["Projektas"])) {
    setcookie("Projektas", "", time() - 3600);
}
if (isset($_COOKIE["Projekto_id"])) {
    setcookie("Projekto_id", "", time() - 3600);
}
if (isset($_SESSION["username"])) {
    if (isset($_POST['logout'])) {
        session_destroy();
        header("location:index.php");
        include_once('atsijungimas.php');
    }
} else {
    header("location:index.php");
}
//auto-loader pakrauna reikiamas klases
require_once 'includes/auto-loader.inc.php';
include_once('db_config.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Project manager</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Take a look and manage your tasks in project management system via dashboard!"/>
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous"> -->
    <link href="css/style.css?rnd=215" rel="stylesheet">
    <link href="css/board.css?rnd=721" rel="stylesheet">
    <!-- <link rel="preconnect" href="https://fonts.gstatic.com"> -->
    <link href="css/createForm.css?rnd=212" type="text/css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200;300;500&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/1b94fb06eb.js" crossorigin="anonymous"></script>
</head>

<body>
     <!-- Kairinis menu -->
     <div class="left-menu"> 
        <div class="left-menu__controls">
            <button class="left-menu__show-btn left-menu__btn" aria-label="Open">
                <i class="fas fa-bars" id="hamburger"></i>
            </button>
            <button class="left-menu__hide-btn left-menu__btn" aria-label="Close">
                <i class="fas fa-times" id="cancel"></i>
            </button>
        </div>
        <div class="left-menu__list">
            <ul class="left-menu__items">
           
                <li class="left-menu__item">
                    <a href="main.php" class="left-menu__icon">
                        <i class="fas fa-folder left-menu-icon" data-text="Projects"></i>
                    </a>
                    <a href="main.php" class="left-menu__title">Projects</a>
                </li>
                <li class="left-menu__item">
                    <a href="#" href="#" class="left-menu__icon">
                        <i class="fas fa-th-large left-menu-icon task-board-btn" data-text="Task board"></i>
                    </a>
                    <p class="left-menu__title">Task board</p>
                </li>

                <li class="left-menu__item">
                    <a href="history.php" class="left-menu__icon">
                        <i class="fas fa-history left-menu-icon" data-text="Logs"></i>
                    </a>
                    <p class="left-menu__title">History</p>
                </li>
            </ul>
        </div>
    </div>
    <!-- Kairinio menu pabaiga -->
    <section>
        <header>
            <!-- Viršutinė menu juosta su antrašte ir log out -->
            <nav class="navbar tasks__navbar">
                <div class="board-heading">
                    <a class="project-page-title board-page-title" href="main.php">Projects/ <?php echo isset($_GET['title']) ? $_GET['title'] : '..'; ?>/ Tasks/ Task board</a>
                </div>
                
                <div class="form-inline form__logout">
                    <?php
                    echo '<p class="login-name login-name__board">' . $_SESSION["username"] . '</p>';
                    ?>
                    <form method="POST">
                        <button class="button" type="submit" name="logout" aria-label="logout"><i class="fas fa-sign-out-alt" ></i></button>
                    </form>
                </div>
            </nav>
            
            <!-- Viršutinės menu juostos pabaiga -->
        </header>
        <!-- TASK BOARDS -->
        <main>
        <?php

        if (isset($_GET['Projekto_id']) && !empty($_GET['Projekto_id'])) {
            try {
                $todoFirst = true;
                $inProgressFirst = true;
                $doneFirst = true;
                $connectM = new PDO("mysql:host=$host; dbname=$dbName", $user, $pass);
                $connectM->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $queryM = "
                SELECT uzduotys.* FROM uzduotys 
                    INNER JOIN projektu_uzduotys ON uzduotys.Uzduoties_id = projektu_uzduotys.Uzduoties_id 
                    INNER JOIN projektai ON projektu_uzduotys.Projekto_id = projektai.Projekto_id 
                    WHERE projektai.Projekto_id = ".$_GET['Projekto_id']."
                    ORDER BY Busena Desc, Eiles_nr DESC
                ";
                $result = $connectM->prepare($queryM);
                $result->execute();
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    if ($row['Busena'] === 'To do') {
                        if($todoFirst){
                            $todoFirst = false;
                            echo "
                            <div class=\"board-container\">
                            <div class=\"task-board\">
                            <div class=\"board-header\">To do</div>
                            <div class=\"board-background\">
                            ";
                        }
                        echo "
                                <div class=\"board-data update-dashboard__JS drag-item\" data-priority=\"".$row['Prioritetas']."\" data-status=\"To do\" data-id=\"".$row['Uzduoties_id']."\" data-description=\"".$row['Aprasymas']."\" draggable=\"true\"><span class=\"dot dot--".$row['Prioritetas']."\"></span>".$row['Pavadinimas']."</div>";
                    }else if($row['Busena'] === 'In Progress'){
                        if($todoFirst){
                            $todoFirst = false;
                            echo "
                            <div class=\"board-container\">
                            <div class=\"task-board\">
                            <div class=\"board-header\">To do</div>
                            <div class=\"board-background\">
                            ";
                        }
                        if($inProgressFirst){
                            $inProgressFirst = false;
                            echo"
                            </div>
                            </div>
                            <div class=\"task-board\">
                                    <div class=\"board-header\">In Progress</div>
                                    <div class=\"board-background\">
                            ";
                        }
                        echo "
                                <div class=\"board-data update-dashboard__JS drag-item\" data-priority=\"".$row['Prioritetas']."\" data-status=\"In Progress\" data-id=\"".$row['Uzduoties_id']."\" data-description=\"".$row['Aprasymas']."\" draggable=\"true\"><span class=\"dot dot--".$row['Prioritetas']."\"></span>".$row['Pavadinimas']."</div>";
                    }else if($row['Busena'] === 'Done'){
                        if($todoFirst){
                            $todoFirst = false;
                            echo "
                            <div class=\"board-container\">
                            <div class=\"task-board\">
                            <div class=\"board-header\">To do</div>
                            <div class=\"board-background\">
                            ";
                        }
                        if($inProgressFirst){
                            $inProgressFirst = false;
                            echo"
                            </div>
                            </div>
                            <div class=\"task-board\">
                                    <div class=\"board-header\">In Progress</div>
                                    <div class=\"board-background\">
                            ";
                        }
                        if($doneFirst){
                            $doneFirst = false;
                            echo "
                            </div>
                            </div>
                            <div class=\"task-board\">
                                    <div class=\"board-header\">Done</div>
                                    <div class=\"board-background\">
                            ";
                        }
                        echo "
                        <div class=\"board-data update-dashboard__JS drag-item\" data-priority=\"".$row['Prioritetas']."\" data-status=\"Done\"  data-id=\"".$row['Uzduoties_id']."\" data-description=\"".$row['Aprasymas']."\" draggable=\"true\"><span class=\"dot dot--".$row['Prioritetas']."\"></span>".$row['Pavadinimas']."</div>";
                    }
                }
                if($todoFirst && $inProgressFirst && $doneFirst){
                    echo "
                        <div class=\"board-container\">
                        <div class=\"task-board\">
                        <div class=\"board-header\">To do</div>
                        <div class=\"board-background\">
                        </div>
                        </div>
                        <div class=\"task-board\">
                                <div class=\"board-header\">In Progress</div>
                                <div class=\"board-background\">
                        </div>
                        </div>
                        <div class=\"task-board\">
                                <div class=\"board-header\">Done</div>
                                <div class=\"board-background\">
                        ";
                }else if($inProgressFirst && $doneFirst){
                    echo "
                    </div>
                    </div>
                    <div class=\"task-board\">
                    <div class=\"board-header\">In Progress</div>
                    <div class=\"board-background\">
                    </div>
                    </div>
                    <div class=\"task-board\">
                            <div class=\"board-header\">Done</div>
                            <div class=\"board-background\">
                    ";
                }
                else if($doneFirst){
                    echo "
                        </div>
                        </div>
                        <div class=\"task-board\">
                                <div class=\"board-header\">Done</div>
                                <div class=\"board-background\">
                    ";
                }
            } catch (PDOException $error) {  //Jei nepavyksta prisijungti ismeta klaidos pranesima
                echo $error->getMessage();
            }
        }
        ?>
                </div>
            </div>
        </div>
        
        <div class="pop-up__update1">
                    <h2 class="pop-up__h2">Update Task</h2>
                    <form method="POST" class="pop-up__form">
                        <input style="text-align:left;" class="pop-up__input pop-up__update-title1" type="text" name="updateTitle" maxlength="30" placeholder="Task Title" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Task Title'" required>


                        <textarea class="pop-up__textarea pop-up__update-description1" placeholder="Description" name="updateDescription" maxlength="200" rows="2"></textarea>
                        <input type="hidden" class="pop-up__update-id1" name="updateId"/>
                        
                        
                                  <div class="task_insert">


                            <!-- Task priority -->
                            <input style="display: none"class="pop-up__update-priority" id="radioLow11" type="radio" value="Low" name="updatepriority" placeholder="Task Priority" 
                                   onfocus="this.placeholder = ''" onblur="this.placeholder = 'Task priority'" required >
                            <label style="display: none" for="radioLow11">Low</label>

                            <input class="pop-up__update-priority priority-Low" id="radioLow1" type="radio" value="Low" name="updatepriority" placeholder="Task Priority" 
                                   onfocus="this.placeholder = ''" onblur="this.placeholder = 'Task priority'" required >
                            <label  for="radioLow1">Low</label>

                            <input class="pop-up__update-priority priority-Middle" id="radioMedium1" type="radio" value="Middle" name="updatepriority" placeholder="Task Priority" 
                                   onfocus="this.placeholder = ''" onblur="this.placeholder = 'Task priority'" required>
                            <label for="radioMedium1">Middle</label>

                            <input class="pop-up__update-priority priority-High" id="radioHight1" type="radio" value="High" name="updatepriority" placeholder="Task Priority" 
                                   onfocus="this.placeholder = ''" onblur="this.placeholder = 'Task priority'"required>
                            <label for="radioHight1">High</label>
                        </div>

                        <!-- Task status -->

                         <div  class="task_status">
                            <!-- Task priority -->

                            <input style="display: none" class="pop-up__update-status " id="radioTodo11" type="radio" value="To do" name="updatestatus" placeholder="Task Status" 
                                   onfocus="this.placeholder = ''" onblur="this.placeholder = 'Task status'" required >
                            <label style="display: none"  for="radioTodo11">To do</label>

                            <input  class="pop-up__update-status status-To" id="radioTodo1" type="radio" value="To do" name="updatestatus" placeholder="Task Status" 
                                   onfocus="this.placeholder = ''" onblur="this.placeholder = 'Task status'" required >
                            <label   for="radioTodo1">To do</label>

                             <input class="pop-up__update-status status-In" id="radioInProgress1" type="radio" value="In Progress" name="updatestatus" placeholder="Task Status" 
                                   onfocus="this.placeholder = ''" onblur="this.placeholder = 'Task status'" required>
                            <label for="radioInProgress1" style="width: auto">In Progress</label>

                            <input class="pop-up__update-status status-Done" id="radioFinished1" type="radio" value="Done" name="updatestatus" placeholder="Task Status" 
                                   onfocus="this.placeholder = ''" onblur="this.placeholder = 'Task status'" required>
                            <label for="radioFinished1">Done</label>
                        </div>
                        <div class="pop-up--flex">
                            <input type="submit" name="update" value="Update" class="pop-up__update-btn pop-up__input" id="project-btn">
                            <div role="button" class="pop-up__cancel-btn" aria-label="Cancel">Cancel</div>
                        </div>
                        <?php
                        if (isset($_POST['updateTitle'])) {
                            $update = new Project();
                            $update->updateTask($_POST['updateTitle'], $_POST['updateDescription'], $_POST['updatepriority'], $_POST['updatestatus'], $_POST['updateId'], $_GET["Projekto_id"], $_GET["title"], 'board.php');
                        }
                        if (isset($_SESSION['updateError'])) {
                            echo "<p class='pop-up__error'>".$_SESSION['updateError']."</p>";
                            echo $_POST['updateId'];
                            unset($_SESSION['updateError']);
                        }
                        ?>
                    </form>
                </div>
        </main>
        <script src="./js/main.js?rnd=121" defer></script>
    </section>
    </body>
</html>