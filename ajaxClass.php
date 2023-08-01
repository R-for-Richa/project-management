<?php
session_start();
require_once 'includes/auto-loader.inc.php';
if (isset($_POST['id']) && isset($_POST['status']) && isset($_POST['title']) && isset($_POST['description'])) {
  
  $update = new Project();
  $update->updateTask($_POST['title'], $_POST['description'], $_POST['priority'], 
          $_POST['status'], $_POST['id'], 'success', 'success', 'success');
}