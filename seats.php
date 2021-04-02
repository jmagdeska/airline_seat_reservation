<?php 
require_once './session.php';
require_once './functions.php';

global $free, $reserved, $purchased;
if(isset($_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
    && isset($_SESSION['username'])){
    updateSeatAvailability();
    echo $purchased,",",$reserved,",",$free;
}
else {
    echo "login";
} 

?>