<?php 
    require_once './functions.php';
    
    if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
        die('Direct access not allowed');
        exit();
    };
    
    session_start();
    $sessionTime = 120; // 2 mins
    
    if(isset($_SESSION['username'])) {
        if (!isset($_SESSION['reservedSeats']))
            $_SESSION['reservedSeats'] = array();
        
        if(isset($_SESSION['timestamp'])) {
            $t = time() - $_SESSION['timestamp'];
            if($t > $sessionTime) {                
                destroySession();
                header("Location: ./login.php?msg=sessionExp");
                exit();
            }
        }
        $_SESSION['timestamp'] = time();
    }    
?>