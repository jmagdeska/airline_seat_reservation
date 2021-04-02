<?php 
require_once './session.php';
require_once './functions.php';

if(isset($_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
    && isset($_SESSION['username']) && isset($_SESSION['reservedSeats'])) {
    $alreadyPurchased = array();
    $username = $_SESSION['username'];
    $fail = false;
    
    $server = connectToDB($db_host, $db_user, $db_pass, $db_name);
    if($server) {
        mysqli_autocommit($server, FALSE);
            
        foreach ($_SESSION['reservedSeats'] as $seatId => $value) {
            $res1 = mysqli_query($server, "SELECT * FROM reservations WHERE seatId='$seatId' FOR UPDATE");
            $row = mysqli_fetch_array($res1);
            
            if($row['status'] == 1 && $row['username'] == $username) {
                $query = "UPDATE reservations SET status=2 WHERE seatId='$seatId'";
                $res = mysqli_query($server, $query);
                
                if($res == false) {
                    $fail = true;
                    array_push($alreadyPurchased, $seatId);
                }
            }
            else {
                $fail = true;
            }
        }
        if($fail == true) $server->rollback();          
        else $server->commit();
        mysqli_close($server);
        
        if($fail == true) {
            $server1 = connectToDB($db_host, $db_user, $db_pass, $db_name);
            if($server)
                unreserveFailedPurchase($server1, $alreadyPurchased, $username);
        }
        
        $_SESSION['reservedSeats'] = array();
        echo ($fail == false) ? "success" : "fail";
    }
    else echo "fail";    
}
else {
    echo "login";
}
?>