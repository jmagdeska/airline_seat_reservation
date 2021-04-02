<?php 

if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die('Direct access not allowed');
    exit();
};

function setHeader() {
    echo "<div class='header'>";
    echo "<a href='./index.php' id='logo'> <img src='logo.png' width='80' height='40'></a>";
    echo "<h2 id='headerText'>  WizzAir booking site </h2></div>";
}

function setNavMenu($current) {
    echo "<div class='sidenav'>
        <img src='menu.png' height='40'>
        <a href='./index.php' id='index'>Home</a>";
        if(isset($_SESSION['logged-in']) && $_SESSION['logged-in'] == 'y')
            echo "<a href='./logout.php' id='logout'>Logout</a>";
        
        else {
            echo "<a href='./login.php' id='login'>Login</a>";
            echo "<a href='./register.php' id='register'>Register</a>";
        }             
        echo "</div>";
}
?>