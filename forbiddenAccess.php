<?php 
require_once './functions.php';

if (isset($_COOKIE['test'])) {
    die('Direct access not allowed');
    exit();
};

echo "<p style='color:red'>Please enable cookies to continue the navigation</p>";

?>

