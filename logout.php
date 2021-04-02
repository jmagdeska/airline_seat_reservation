<?php
require_once './session.php';
require_once './functions.php';

if(isset($_SESSION['logged-in']) && $_SESSION['logged-in'] == 'y') {
    destroySession();
    header("Location: ./index.php");
    exit();
}
else {
    header("Location: ./login.php?msg=sessionExp");
    exit();
}
?>