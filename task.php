<?php

session_start();

if (isset($_GET['title']) && (!isset($_COOKIE['Projektas']))) {
    setcookie("Projektas", $_GET['title'], time() + (3600));
}

if(isset($_GET['Projekto_id']) && (!isset($_COOKIE['Projekto_id']))){
    setcookie("Projekto_id", $_GET['Projekto_id'], time() + (3600));
}

if (isset($_SESSION["username"])) {
    if (isset($_POST['logout'])) {
        session_destroy();
        setcookie("Projektas", "", time() - 3600);
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
    <meta content='width=device-width; initial-scale=1.0;' name='viewport' />
    <meta name="description" content="Take a look and manage your tasks in project management system!"/>
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous"> -->
    <link href="css/style.css?rnd=124" rel="stylesheet">
    <!-- <link rel="preconnect" href="https://fonts.gstatic.com"> -->
    <link href="css/createForm.css?rnd=124" type="text/css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/1b94fb06eb.js"
    crossorigin="anonymous"></script>
    <script>
        function displayError (){
            document.querySelector('.pop-up__update').classList.add('pop-up__JS');
            const blur = document.createElement('div');
            blur.classList.add('blur__JS');
            document.body.appendChild(blur);
        }
    </script>
</head>
<body>
    <!-- Kairinis menu -->
    <div class="left-menu"> 
        <div class="left-menu__controls">
            <button class="left-menu__show-btn left-menu__btn" aria-label="Show menu">
                <i class="fas fa-bars" id="hamburger"></i>
            </button>
            <button class="left-menu__hide-btn left-menu__btn" aria-label="close">
                <i class="fas fa-times" id="cancel"></i>
            </button>
        </div>
        <div class="left-menu__list">
            <ul class="left-menu__items sidebar-tasks">
           
                <li class="left-menu__item">
                    <a href="main.php" class="left-menu__icon" aria-label="Main page">
                        <i class="fas fa-folder left-menu-icon" data-text="Projects"></i>
                    </a>
                    <a href="main.php" class="left-menu__title">Projects</a>
                </li>
                <li class="left-menu__item">
                    <a href="board.php?Projekto_id=<?php echo isset($_GET['Projekto_id']) ? $_GET['Projekto_id'] : '';?>&title=<?php echo isset($_GET['title']) ? $_GET['title'] : '';?>" class="left-menu__icon" aria-label="Dashboard page">
                        <i class="fas fa-th-large left-menu-icon" data-text="Task Board"></i>
                    </a>
                    <p class="left-menu__title">Task board</p>
                </li>

                <li class="left-menu__item">
                    <a href="history.php" class="left-menu__icon" aria-label="Logs page">
                        <i class="fas fa-history left-menu-icon history-btn" data-text="Logs"></i>
                    </a>
                    <p class="left-menu__title">History</p>
                </li>
                <li class="left-menu__item">
                    <a class="create-project__JS left-menu__icon" aria-label="create">
                        <i class="fas fa-plus-circle left-menu-icon new-task-btn" data-text="New task"></i>
                    </a>
                    <p class="left-menu__title">New task</p>
                </li>
            </ul>
        </div>
    </div>
    <!-- Kairinio menu pabaiga -->
    <section>
<header>
    <!-- Viršutinė menu juosta su search ir exit laukeliais -->
    <nav class="navbar tasks__navbar">
        
        <?php 
        
        if(isset($_COOKIE["Projektas"])){
            echo "<h2 class=\"project-page-title  tasks__title mr-auto\"> <span class=\"tasks__title--uppercase\">".$_COOKIE["Projektas"]."</span> / Tasks</h2>";
        }else if (isset($_GET['title'])) {
            echo "<h2 class=\"project-page-title  tasks__title mr-auto\"> <span class=\"tasks__title--uppercase\">".$_GET['title']."</span> / Tasks</h2>";
        }else{
            echo "<h2 class=\"project-page-title tasks__title  mr-auto\"> - / Tasks</h2>";
        }?>
        <div class="whole-search tasks__search"> <!-- SEARCH FUNKCIALUMAS -->        
        <form id="search-form">
        <?php              
            if(isset($_GET["search"])) {
                $SEARCH_QUERY = trim($_GET["search"]);
                $SEARCH_QUERY_LENGTH = strlen($SEARCH_QUERY);                
            } else {
                $SEARCH_QUERY = "";
            }
            echo "<input = type=\"hidden\" name=\"title\" value=\"".$_GET['title']."\"><input = type=\"hidden\" name=\"Projekto_id\" value=\"".$_GET['Projekto_id']."\"><input type=\"text\" id=\"search\" name=\"search\" value=\"" . $SEARCH_QUERY . "\" placeholder=\"Search tasks\" class=\"search-form__input\" pattern=\"\w{3,}||[0-9]+\" title=\"Enter at least 3 symbols\">
            <i class=\"fas fa-search\" id=\"search-icon\"></i>";
        ?>
        </form>
        <div class="form-inline">
            <?php
            echo '<p class="login-name">' . $_SESSION["username"] . '</p>';
            ?>
            <form method="POST">
                <button class="button" type="submit" name="logout" aria-label="logout"><i class="fas fa-sign-out-alt" aria-label="logout"></i></button>
            </form>
            </div>
        </div>
    </nav>
</header>
            <main>
                <?php

    if(isset($_COOKIE['Projekto_id']) || isset($_GET['Projekto_id'])){
        try {
            $connectM = new PDO("mysql:host=$host; dbname=$dbName", $user, $pass);
            $connectM->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            if (isset($_COOKIE['Projekto_id'])) {
                $queryM = "SELECT uzduotys.* FROM uzduotys 
                    INNER JOIN projektu_uzduotys ON uzduotys.Uzduoties_id = projektu_uzduotys.Uzduoties_id 
                    INNER JOIN projektai ON projektu_uzduotys.Projekto_id = projektai.Projekto_id 
                    WHERE projektai.Projekto_id = ".$_COOKIE['Projekto_id']."
                    AND (uzduotys.Pavadinimas LIKE '%".$SEARCH_QUERY."%' OR uzduotys.Uzduoties_id LIKE '%".$SEARCH_QUERY."%')
                    ORDER BY uzduotys.Eiles_nr DESC";
            } 
            else {
                $queryM = "SELECT uzduotys.* FROM uzduotys 
                    INNER JOIN projektu_uzduotys ON uzduotys.Uzduoties_id = projektu_uzduotys.Uzduoties_id 
                    INNER JOIN projektai ON projektu_uzduotys.Projekto_id = projektai.Projekto_id 
                    WHERE projektai.Projekto_id = ".$_GET['Projekto_id']."
                    ORDER BY uzduotys.Eiles_nr DESC";   
            }
            $result = $connectM->prepare($queryM);
            $result->execute();
            $number = $result->rowCount(); //paskutines eilus stilizavimui reikalinga
            $i = 1;
            if($number === 0){
                $_SESSION['empty'] = true;
            }
            if( $number == 0 && isset($SEARCH_QUERY)) {
                echo "<div class=\"error-search-group\"> <img src=\"projects.png\" class=\"error-search-img\"> <span class=\"error-search-message\"> Task with this name does not exist</span></div>";
            } else {
            echo "
            <table class=\"table--fixed tasks-table\">
                <thead class=\"tasks__thead\" style=\"position: relative;\">
                    <tr>
                        <th class='project-name-spacing tasks__th'>ID</th>
                        <th class='tasks__th--width tasks__th tasks__th--radius tasks__th--text-align'>Title</th>
                        <th class='tasks__th--width tasks__th'>Description</th>
                        <th class='tasks__th'>Priority</th>
                        <th class='tasks__th'>Status</th>
                        <th class='tasks__th'>Created</th>
                        <th class='tasks__th'>Modified</th>
                        <th class='round-border tasks__th'></th>
                    </tr>";
            }
          
            echo "</thead>";
            while($row = $result->fetch(PDO::FETCH_ASSOC)){
                if ($i == $number)
                {
                    // spausdinama eilutė su "Sukurti projektą mygtuku
                    echo "

                    <tr>
                        <td class='tasks__td'>" . $row['Uzduoties_id'] . "</td>
                        <td class='tasks__td'>" . $row['Pavadinimas'] . "</td>
                        <td class='tasks__td'><div class=\"project-description-tasks__JS\" id='shortened-description'><a href='#'>" . $row['Aprasymas'] . "</a></div></td>
                        <td class=\"tasks__td tasks__priority-" . $row['Prioritetas'] . "\"\">" . $row['Prioritetas'] . "</td>
                        <td class='tasks__td'>" . $row['Busena'] . "</td>
                        <td class='tasks__td'>" . $row['Sukurimo_data'] . "</td>
                        <td class='tasks__td'>" . $row['Naujinimo_data'] . "</td>
                        <td class='tasks__td'>
                            <button class=\"update1-project__JS\" aria-label=\"edit\">
                                <i class='far fa-edit edit-task-btn icon--mobile edit-task-btn--padding'></i>
                            </button>";

                            if (isset($_COOKIE['Projekto_id']) && isset($_COOKIE['Projektas'])){
                                echo "<button class=\"delete1-project__JS\" data-id-project= \"" . $_COOKIE['Projekto_id'] . "\" data-title=\"" . $_COOKIE['Projektas'] . "\" data-id=\"" . $row['Uzduoties_id'] . "\" aria-label=\"delete\">";
                                } else {
                                echo "<button class=\"delete1-project__JS\" data-id-project= \"" . $_GET['Projekto_id'] . "\" data-title=\"" . $_GET['title'] . "\" data-id=\"" . $row['Uzduoties_id'] . "\" aria-label=\"delete\">";
                                }
                                echo "

                                <i class='far fa-trash-alt delete-task-btn icon--mobile'></i>
                            </button>
                            <button class=\"button\" id='create-button' style='padding: 0;' aria-label=\"create a task\">
                                <i class='fas fa-plus-circle create-task__JS add-task-btn' id='plus-button'></i>
                            </button>
                        </td>
                    </tr>";
                                break;
                            }
                            // spausdinamos kitos lentelės eilutės
                            echo "
                    <tr class='tasks__tr--border-bottom'>
                        <td class='tasks__td'>" . $row['Uzduoties_id'] . "</td>

                        <td class='tasks__td'>" . $row['Pavadinimas'] . "</td>
                        <td class='tasks__td'><div class=\"project-description-tasks__JS\" id='shortened-description'><a href='#'>" . $row['Aprasymas'] . "</a></div></td>

                        <td class=\"tasks__td tasks__priority-" . $row['Prioritetas'] . "\">" . $row['Prioritetas'] . "</td>
                        <td class='tasks__td'>" . $row['Busena'] . "</td>
                        <td class='tasks__td'>" . $row['Sukurimo_data'] . "</td>
                        <td class='tasks__td'>" . $row['Naujinimo_data'] . "</td>
                        <td class='tasks__td'>
                            <button class=\"update1-project__JS\" aria-label=\"update\">
                                <i class='far fa-edit edit-task-btn icon--mobile edit-task-btn--padding'></i>
                            </button>";

                            if (isset($_COOKIE['Projekto_id']) && isset($_COOKIE['Projektas'])){
                            echo "<button class=\"delete1-project__JS\" data-id-project= \"" . $_COOKIE['Projekto_id'] . "\" data-title=\"" . $_COOKIE['Projektas'] . "\" data-id=\"" . $row['Uzduoties_id'] . "\" aria-label=\"delete\">";
                            } else {
                            echo "<button class=\"delete1-project__JS\" data-id-project= \"" . $_GET['Projekto_id'] . "\" data-title=\"" . $_GET['title'] . "\" data-id=\"" . $row['Uzduoties_id'] . "\" aria-label=\"delete\">";
                            }
                            echo "
                                <i class='far fa-trash-alt delete-task-btn icon--mobile'></i>
                            </button>
                        </td>
                    </tr>";
                            $i++;
                        }
                    } catch (PDOException $error) {  //Jei nepavyksta prisijungti ismeta klaidos pranesima
                        echo $error->getMessage();
                    }
                }
                echo "
        </table>
        <br>";
                if (isset($_SESSION['empty']) && isset($_GET['title']) && !isset($_COOKIE['Projektas'])) {
                    echo "
        <button id='create-button' class=\"tasks__add-btn\" aria-label=\"create a task\">
            <i class='fas fa-plus-circle create-project__JS tasks__add-btn-i' id='plus-button'></i>
        </button>";

                    unset($_SESSION['empty']);

                } else if (isset($_SESSION['empty']) && isset($_COOKIE['Projektas'])) {
                    echo "
        <button id='create-button' class=\"tasks__add-btn\" aria-label=\"create a task\">
            <i class='fas fa-plus-circle create-project__JS tasks__add-btn-i' id='plus-button'></i>
        </button>";
            
                    unset($_SESSION['empty']);
                }

//Pridedamas html blur'as, jei nesekminga uzklausa ('toks pavadinimas jau yra' ir t.t.)
                echo isset($_POST['taskTitle']) ? '<div class="blur__JS"></div>' : '';                
            ?>

                <div class="pop-up <?php echo isset($_POST['taskTitle']) ? 'pop-up__JS' : ''; ?>">
                    <h2 class="pop-up__h2">Create a new task</h2>
                    <form method="POST" class="pop-up__form">

                        <!-- Task title -->
                        <input style="text-align:left;" class="pop-up__input" type="text" maxlength="30" name="taskTitle" placeholder="Task title" 
                               onfocus="this.placeholder = ''" onblur="this.placeholder = 'Task title'" required>

                        <!-- Task description -->
                        <label for="description" class="pop-up__placeholder">Description</label><textarea class="pop-up__textarea" maxlength="200" name="taskDescription" rows="6"></textarea>


                        <div class="task_insert">


                            <!-- Task priority -->
                            <input id="radioLow" type="radio" value="Low" name="taskPriority" placeholder="Task priority" 
                                   onfocus="this.placeholder = ''" onblur="this.placeholder = 'Task priority'" checked>
                            <label for="radioLow">Low</label>

                            <input id="radioMedium" type="radio" value="Middle" name="taskPriority" placeholder="Task priority" 
                                   onfocus="this.placeholder = ''" onblur="this.placeholder = 'Task priority'" >
                            <label for="radioMedium">Middle</label>

                            <input id="radioHight" type="radio" value="High" name="taskPriority" placeholder="Task priority" 
                                   onfocus="this.placeholder = ''" onblur="this.placeholder = 'Task priority'" >
                            <label for="radioHight">High</label>
                        </div>

                        <!-- Task status -->

                        <div  class="task_status">
                            <!-- Task priority -->

                            <input id="radioTodo" type="radio" value="To do" name="taskStatus" placeholder="Task status" 
                                   onfocus="this.placeholder = ''" onblur="this.placeholder = 'Task status'" checked>
                            <label for="radioTodo">To do</label>
                            
                             <input id="radioInProgress" type="radio" value="In Progress" name="taskStatus" placeholder="Task status" 
                                   onfocus="this.placeholder = ''" onblur="this.placeholder = 'Task status'">
                            <label for="radioInProgress" style="width: auto">In Progress</label>

                            <input id="radioFinished" type="radio" value="Done" name="taskStatus" placeholder="Task status" 
                                   onfocus="this.placeholder = ''" onblur="this.placeholder = 'Task status'" >
                            <label for="radioFinished">Done</label>
                        </div>



                        <!-- Task button -->
                        <div class="pop-up--flex">
                            <input type="submit" name="create" value="Create" class="pop-up__create-btn pop-up__input" id="project-btn">
                            <div role="button" class="pop-up__cancel-btn">Cancel</div>
                        </div>
  </form>
  <?php
                        if (isset($_POST['taskTitle'])) {
                            $create = new Project();
                            $create->createTask($_POST['taskTitle'], $_POST['taskDescription'], $_POST['taskPriority'], $_POST['taskStatus'], $_SESSION['userId']);
                        }
                        if (isset($_SESSION['message'])) {
                            echo "<p class='pop-up__error'>" . $_SESSION['message'] . "</p>";
                            unset($_SESSION['message']);
                        }
                        ?>
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
                            <div role="button" class="pop-up__cancel-btn">Cancel</div>
                        </div>
                        
                        <?php
                        if (isset($_POST['updateTitle'])) {
                            $update = new Project();
                            $update->updateTask($_POST['updateTitle'], $_POST['updateDescription'], $_POST['updatepriority'], $_POST['updatestatus'], $_POST['updateId'], $_GET["Projekto_id"], $_GET["title"], 'task.php');
                        }
                        if (isset($_SESSION['updateError'])) {
                              echo "<p class='pop-up__error'>".$_SESSION['updateError']."</p>";
                            unset($_SESSION['updateError']);
                        }
                        ?>
                    </form>
                </div>
                <div class="pop-up__delete1">
                    <h2 class="pop-up__h2">Delete a Task</h2>
                    <form method="POST" class="pop-up__form">
                        <p class="pop-up__alert-msg">Are you sure you want to delete this task?</p>
                        <div class="pop-up--flex">
                            <a href="#" class="pop-up__confirm-btn1">Delete</a>
                            <div role="button" class="pop-up__cancel-btn pop-up__cancel-btn--bg" aria-label="Keep a task">Keep</div>
                        </div>                   
                    </form>
                </div>
            </main>
            <script src="./js/main.js?rnd=227"></script>
        </section>
    </body>


</html>
