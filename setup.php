<?php 
require_once './functions.php';
require_once './noScript.php';

if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die('Direct access not allowed');
    exit();
};

testForCookies();

if(isset($_SESSION['username']) && isset($_SESSION['logged-in']))
    enforceHttps();

?>