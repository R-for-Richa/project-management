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
        <meta name="description" content="Take a look and manage your projects in project management system!"/>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
        <link href="css/style.css?rnd=999" rel="stylesheet">
        <!-- <link rel="preconnect" href="https://fonts.gstatic.com"> -->
        <link href="css/createForm.css?rnd=141" type="text/css" rel="stylesheet">
        <script src="https://kit.fontawesome.com/1b94fb06eb.js"
        crossorigin="anonymous"></script>
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
                                    <a href="#" class="left-menu__icon">
                                        <i class="fas fa-folder left-menu-icon" data-text="Projects"></i>
                                    </a>
                                    <p class="left-menu__title">Projects</p>
                                </li>
                                <li class="left-menu__item left-menu__item-hover">
                                    <a href="data:text/csv;charset=utf-8, Title, Description, Status, Finished tasks, Total tasks" download="Projects.csv" class="left-menu__icon export">
                                        <i class="fas fa-file-download left-menu-icon" data-text="Export projects"></i>
                                    </a>
                                    <p class="left-menu__title">
                                        <span class="export__span">Export Projects</span>
                                    </p>
                                </li>
                                <li class="left-menu__item">
                                    <a href="history.php" class="left-menu__icon">
                                        <i class="fas fa-history left-menu-icon" data-text="Logs"></i>
                                    </a>
                                    <p class="left-menu__title">Logs</p>
                                </li>
                                <li class="left-menu__item">
                                    <a href="#" class="create-project__JS left-menu__icon">
                                        <i class="fas fa-plus-circle left-menu-icon new-project-btn" data-text="New project"></i>
                                    </a>
                                    <p class="left-menu__title">New project</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- Kairinio menu pabaiga -->
                    <section>
                        <header>
                            <!-- Viršutinė menu juosta su search ir exit laukeliais -->
                            <nav class="navbar">
                                <a class="project-page-title mr-auto" download="Projects.csv">Projects</a>
                                <div class="whole-search">
                                    <!-- board-page-title propects -->
                                    <!-- SEARCH FUNKCIALUMAS -->        
                                    <form id="search-form">
                                        <?php
                                        if (isset($_GET["search"])) {
                                            $SEARCH_QUERY = trim($_GET["search"]);
                                            $SEARCH_QUERY_LENGTH = strlen($SEARCH_QUERY);
                                            if ($SEARCH_QUERY_LENGTH > 0 && $SEARCH_QUERY_LENGTH < 3) {
                                                // $SEARCH_ERROR = "error";
                                            }
                                        } else {
                                            $SEARCH_QUERY = "";
                                        }
                                        echo "<input type=\"text\" id=\"search\" name=\"search\" value=\"" . $SEARCH_QUERY . "\" placeholder=\"Search projects\" class=\"search-form__input\" pattern=\"\w{3,}\" title=\"Enter atleast 3 symbols\">
            <i class=\"fas fa-search\" id=\"search-icon\"></i>";

// if(isset($SEARCH_ERROR)) {
//     echo "<br /><span style=\"color: red\"> " . $SEARCH_ERROR . "</span";               
// }
                                        ?>
                                    </form>        
                                    <div class="form-inline">
                                        <?php
                                        echo '<p class="login-name">' . $_SESSION["username"] . '</p>';
                                        ?>
                                        <form method="POST">
                                            <button class="button" type="submit" name="logout" aria-label="logout">
                                                <i class="fas fa-sign-out-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </nav>
                            <!-- Viršutinės menu juostos pabaiga -->
                        </header>
                        <main>
                            <?php
// Jungiamės prie duombazės
                            try {
                                $connectM = new PDO("mysql:host=$host; dbname=$dbName", $user, $pass);
                                $connectM->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                $querySelect = "SELECT
                projektai.Projekto_id,
                projektai.Pavadinimas,
                projektai.Aprasymas,
                projektai.Busena,
                projektai.Sukurimo_data,
                SUM(case when uzduotys.Busena ='Done' then 1 else 0 end) as Finished_tasks,
                SUM(case when uzduotys.Busena ='To Do' then 1 else 0 end) as Todo_tasks,
                SUM(case when uzduotys.Busena ='In Progress' then 1 else 0 end) as InProgress_tasks,
                COUNT(uzduotys.Busena) as Total_tasks
                FROM projektai
                LEFT JOIN projektu_uzduotys ON projektu_uzduotys.Projekto_id = projektai.Projekto_id
                LEFT JOIN uzduotys ON uzduotys.Uzduoties_id = projektu_uzduotys.Uzduoties_id
                RIGHT JOIN komandos ON komandos.Projekto_id = projektai.Projekto_id
                RIGHT JOIN vartotojai ON vartotojai.Vartotojo_id = komandos.Vartotojas
                WHERE vartotojai.Vartotojo_id = " . $_SESSION['userId'] . "";
                                $queryWhere = !isset($SEARCH_ERROR) ? " AND projektai.Pavadinimas LIKE '%" . $SEARCH_QUERY . "%' " : " ";
                                $queryOrder = "GROUP BY 1 ORDER BY Sukurimo_data DESC";
                                $queryM = $querySelect . " " . $queryWhere . " " . $queryOrder;
                                $linkCSV = "data:text/csv;charset=utf-8, Title, Description, Status, Finished tasks, Total tasks\n";
                                $linkCSV_tasks = "data:text/csv;charset=utf-8, ID, Title, Description, Priority, Status, Created, Modified\n";
                                $result = $connectM->prepare($queryM);
                                $result->execute();
                                $number = $result->rowCount(); //paskutines eilus stilizavimui reikalinga
                                $i = 1;

                                // count naudosime, jei noresime nustatyti eiluciu skaiciu
                                // $count = 1;
                                function activeProgressBar($totalTasks, $finishedTasks, $i) {
                                    if ($totalTasks == 0) {
                                        $greenBarLength = 0;
                                    } else {
                                        $percent = $finishedTasks / $totalTasks;
                                        $greenBarLength = $percent * 130; //dauginu iš 130, nes toks yra progress bar width (css)
                                    }
                                    echo "
            <style>
            #progressId" . $i . " {
                width: " . $greenBarLength . "px;
                background-color: #c0f292;
                height: 12px;
                border-radius: 10px;
                margin-top: -3px;
            }
            </style>";
                                }

                                //  isspausdinamas projektu sarasas
                                if ($number == 0 && isset($SEARCH_QUERY)) {
                                    echo "<div class=\"error-search-group\"> <img src=\"projects.png\" class=\"error-search-img\"> <span class=\"error-search-message\"> Project with this name does not exist</span></div>";
                                } else {
                                    echo "<table class='projects-table projects-table__main'>";
                                    echo "<thead>";
                                    echo "<tr>
        <th class='project-name-spacing projects-th-width'>PROJECT NAME</th>
        <th class=\"projects-description projects-th-width\">DESCRIPTION</th>
        <th class=\"projects-status\">STATUS</th>
        <th class='completion-spacing'>COMPLETION</th>
        <th class='round-border projects-functions'></th>
        </tr>";
                                    echo "</thead>";
                                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                        $linkCSV .= "&quot;" . $row['Pavadinimas'] . "&quot;,&quot;" . $row['Aprasymas'] . "&quot;," . $row['Busena'] . "," . $row['Finished_tasks'] . "," . $row['Total_tasks'] . "\n";
                                        // $linkCSV .= $row['Pavadinimas'].",".$row['Aprasymas'].",".$row['Busena'].",".$row['Finished_tasks'].",".$row['Total_tasks']."\n";
                                        activeProgressBar($row['Total_tasks'], $row['Finished_tasks'], $i);
                                        if ($i == $number) {
                                            // spausdinama eilutė su "Sukurti projektą mygtuku
                                            echo "<tr>
          <td class='d-none'>" . $row['Projekto_id'] . "</td>
          <td><a href=\"task.php?Projekto_id=" . $row['Projekto_id'] . "&title=" . $row['Pavadinimas'] . "\" class=\"projects__title-hover\">" . $row['Pavadinimas'] . "</td>
          <td><div class=\"project-description__JS\" id='shortened-description' aria-label=\"update\"><a href='#'>" . $row['Aprasymas'] . "</a></div></td>
          <td>" . $row['Busena'] . "</td>
          <td class='progresss'>
          <p class='progress-numbers'>" . $row['Finished_tasks'] . "/" . $row['Total_tasks'] . "</p>
          <div class='round'><div id='progressId" . $i . "'></div></div><div class='hover-info'>Total: " . $row['Total_tasks'] . ", To do: " . $row['Todo_tasks'] . ", In Progress: " . $row['InProgress_tasks'] . ", Finished: " . $row['Finished_tasks'] . "</div></td>
          <td class='td-spacing projects-functions'>
          <button class=\"update-project__JS\"  aria-label=\"update\"><i class='far fa-edit icon--mobile'></i></button>
          <button class=\"delete-project__JS\" id=\"" . $row['Projekto_id'] . "\" aria-label=\"delete\">
          <i class='far fa-trash-alt icon--mobile'></i>
          </button>
          <button class=\"button\" aria-label=\"archive\"><i class='fas fa-archive icon--mobile'></i></button>
          <form method='post' style='display:inline-block'>
                <button class=\"button export\" aria-label=\"export\" type='submit' name='id' value='" . $row['Projekto_id'] . "'>
                <i class='fas fa-arrow-down icon--mobile'></i>
                </button>
          </form>
          <button class=\"button\" id='create-button' aria-label=\"create a project\">
          <i class='fas fa-plus-circle create-project__JS add-project-btn' id='plus-button' data-link=\"" . $linkCSV . "\"></i></button></td></tr>";
                                            break;
                                        }
                                        // spausdinamos kitos lentelės eilutės
                                        echo "<tr>
                <td class='d-none'>" . $row['Projekto_id'] . "</td>
                <td class='grey-border'><a class=\"projects__title-hover\"href=\"task.php?Projekto_id=" . $row['Projekto_id'] . "&title=" . $row['Pavadinimas'] . "\">" . $row['Pavadinimas'] . "</td>
                <td class='grey-border'><div class=\"project-description__JS\" id='shortened-description' aria-label=\"update\"><a href='#' aria-label=\"update\">" . $row['Aprasymas'] . "</a></div></td>
                <td class='grey-border'>" . $row['Busena'] . "</td>
                <td class='grey-border progresss'>
                <p class='progress-numbers'>" . $row['Finished_tasks'] . "/" . $row['Total_tasks'] . "</p>
                <div class='round'><div id='progressId" . $i . "'></div></div><div class='hover-info'>Total: " . $row['Total_tasks'] . ", To do: " . $row['Todo_tasks'] . ", In Progress: " . $row['InProgress_tasks'] . ", Finished: " . $row['Finished_tasks'] . "</div></td>
                <td class='grey-border projects-functions'>
                <button class=\"update-project__JS\" aria-label=\"update\"><i class='far fa-edit icon--mobile'></i></button>
                <button class=\"delete-project__JS\" id=\"" . $row['Projekto_id'] . "\" aria-label=\"update\">
                    <i class='far fa-trash-alt icon--mobile'></i>
                    </button> 

                <button class=\"button\" aria-label=\"archive\"><i class='fas fa-archive icon--mobile'></i></button>
                <form method='post' style='display:inline-block'>
                <button class=\"button export\" type='submit' name='id' value='" . $row['Projekto_id'] . "' aria-label=\"export\">
                <i class='fas fa-arrow-down icon--mobile'></i>
                </button></td></tr>
                </form>";
                                        $i++;
                                    }
                                    echo "</table>";
                                }
                                echo "<br>";
                            } catch (PDOException $error) {  //Jei nepavyksta prisijungti ismeta klaidos pranesima
                                echo $error->getMessage();
                            }

                            if (isset($_POST['id'])) { //paspaudus vieno iš projektų csv atsisiuntimo mygtuką, įvykdoma sql užklausa, kuri išrenka to projekto užduotis ir jas atsiunčia csv formatu
                                $ID = $_POST['id'];
                                $query = "SELECT * FROM uzduotys WHERE Projekto_id = " . $ID . " ORDER BY Eiles_nr DESC";
                                $result = $connectM->prepare($query);
                                $result->execute();

                                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                    $linkCSV_tasks .= "" . $row['Uzduoties_id'] . "," . $row['Pavadinimas'] . "," . $row['Aprasymas'] . "," . $row['Prioritetas'] . "," . $row['Busena'] . "," . $row['Sukurimo_data'] . "," . $row['Naujinimo_data'] . "\n";
                                }
                                echo "<div id='dom-target' style='display: none;'>";
                                echo htmlspecialchars($linkCSV_tasks);
                                echo "</div>";
                                ?>
                                <script>
                                    var div = document.getElementById('dom-target');
                                    var val = '<?php echo $ID ?>';
                                    var data = div.textContent;

                                    var hiddenElement = document.createElement('a');
                                    hiddenElement.href = encodeURI(data);
                                    hiddenElement.target = '_blank';
                                    hiddenElement.download = 'Project_ID' + val + '_tasks.csv';
                                    hiddenElement.click();

                                </script> 
                                <?php
                            }



//Pridedamas html blur'as, jei nesekminga uzklausa ('toks pavadinimas jau yra' ir t.t.)
                            echo isset($_POST['title']) ? '<div class="blur__JS"></div>' : '';
                            ?>
                            <div class="pop-up <?php echo isset($_POST['title']) ? 'pop-up__JS' : ''; ?>">
                                <h2 class="pop-up__h2">Create a new project</h2>
                                <form method="POST" class="pop-up__form">
                                    <input style="text-align:left;" class="pop-up__input" type="text" name="title" maxlength="30" placeholder="Project title" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Project title'" required>
                                    <label for="description" class="pop-up__placeholder">Description</label><textarea class="pop-up__textarea" name="description"   maxlength="200" rows="6"></textarea>
                                    <div class="pop-up--flex">
                                        <input type="submit" name="create" value="Create" class="pop-up__create-btn pop-up__input" id="project-btn">
                                        <div role="button" class="pop-up__cancel-btn" aria-label="Cancel creation">Cancel</div>
                                    </div>
                                    <?php
                                    if (isset($_POST['title'])) {
                                        $create = new Project();
                                        $create->createProject($_POST['title'], $_POST['description'], $_SESSION['userId']);
                                    }
                                    if (isset($_SESSION['message'])) {
                                        echo "<p class='pop-up__error'>" . $_SESSION['message'] . "</p>";
                                        unset($_SESSION['message']);
                                    }
                                    ?>
                                </form>
                            </div>
                            <div class="pop-up__update">
                                <h2 class="pop-up__h2">Update a Project</h2>
                                <form method="POST" class="pop-up__form">
                                    <input style="text-align:left;" class="pop-up__input pop-up__update-title" type="text" name="updateTitle" maxlength="30" placeholder="Project title" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Project title'" required>
                                    <textarea class="pop-up__textarea pop-up__update-description" placeholder="Description" name="updateDescription" maxlength="200" rows="2"></textarea>
                                    <input type="hidden" class="pop-up__update-id" name="updateId"/>
                                    <div class="pop-up--flex">
                                        <input type="submit" name="update" value="Update" class="pop-up__update-btn pop-up__input" id="project-btn">
                                        <div role="button" class="pop-up__cancel-btn" aria-label="Cancel update">Cancel</div>
                                    </div>
                                    <?php
                                    if (isset($_POST['updateTitle'])) {
                                        $update = new Project();
                                        $update->updateProject($_POST['updateTitle'], $_POST['updateDescription'], $_POST['updateId'], $_SESSION['userId']);
                                    }
                                    if (isset($_SESSION['updateError'])) {
                                        echo "<p class='pop-up__error'>" . $_SESSION['updateError'] . "</p>";
                                        unset($_SESSION['updateError']);
                                    }
//php automatiskai neatnaujina stulpelio 'Sukurimo_data" laiko, del to panaudojau sia komanda. Ji suranda, kur yra neatnaujintas laikas ir ta laika pakeicia i esama
                                    $queryTime = "UPDATE projektai SET Sukurimo_data = CURRENT_TIMESTAMP WHERE Sukurimo_data LIKE '%00:00:00'";
                                    $resultime = $connectM->prepare($queryTime);
                                    $resultime->execute();
                                    ?>
                                </form>
                            </div>
                            <div class="pop-up__delete">
                                <h2 class="pop-up__h2">Delete a Project</h2>
                                <form method="POST" class="pop-up__form">
                                    <p class="pop-up__alert-msg">Are you sure you want to delete this project?</p>
                                    <div class="pop-up--flex">
                                        <a href="#" class="pop-up__confirm-btn">Delete</a>
                                        <div role="button" class="pop-up__cancel-btn pop-up__cancel-btn--bg" aria-label="Cancel deletion">Keep</div>
                                    </div>
                                </form>
                            </div>
                        </main>
                        <script src="./js/main.js?rnd=214" defer></script>
                    </section>
                    </body>
                    </html>