<?php 
require_once './session.php';
require_once './functions.php';

if(isset($_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') 
    && isset($_GET['seatId']) && isset($_SESSION['username'])){
    
    $seatId = $_GET['seatId'];
    $username = $_SESSION['username'];
    
    $server = connectToDB($db_host, $db_user, $db_pass, $db_name);
    if($server) {
        if(isset($_SESSION['reservedSeats'][$seatId])) { // unreserve seat
            unset($_SESSION['reservedSeats'][$seatId]);
            
            $res = mysqli_query($server, "SELECT * FROM reservations WHERE seatId='$seatId'");
            $row = mysqli_fetch_array($res);
            if($row['username'] == sanitizeString($server, $username)) {
                $res = deleteReservation($server, $seatId, $username);
                echo ($res == false) ? "fail" : "unreserved";
            }
            else if($row['status'] == 1) {
                $res = updateReservation($server, $seatId, $username);
                if($res == false) echo "fail";
                else {
                    $_SESSION['reservedSeats'][$seatId] = 1;
                    echo "reserved";
                }    
            }                
        }
        else {
            $res = mysqli_query($server, "SELECT * FROM reservations WHERE seatId='$seatId'");
            $row = mysqli_fetch_array($res);
            
            if($row == NULL) { // seat is free
                $res = addReservation($server, $seatId, $username);
                if($res == false) echo "fail";
                else {
                    $_SESSION['reservedSeats'][$seatId] = 1;                    
                    echo "reserved";
                }                
            }
            else if($row['status'] == 1) { // reserved seat
                mysqli_autocommit($server, FALSE);
                $res = mysqli_query($server, "SELECT status FROM reservations WHERE seatId='$seatId' FOR UPDATE");
                $row = mysqli_fetch_array($res);
                if($row != 2) {
                    $res = updateReservation($server, $seatId, $username);
                    if($res == false) echo "fail";
                    else {
                        $_SESSION['reservedSeats'][$seatId] = 1;
                        echo "reserved";
                    }
                    $server->commit();
                }
                else {
                    $server->rollback();
                    echo "bought";                
                }
            }
            else echo "bought";     
        }     
        mysqli_close($server);      
    }
    else echo "fail";    
}
else {
    echo "login";
} 
?>