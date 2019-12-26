<?php 

if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die('Direct access not allowed');
    exit();
};

$seatsRow = 6;
$rows = 20;
$free = $seatsRow*$rows;
$reserved = 0;
$purchased = 0;

$db_host = "localhost";
$db_user = "s261427";
$db_pass = "persogib";
$db_name = "s261427";

/* General functions */
function enforceHttps(){
    // Force connection through HTTPS
    if(!isset($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] != "on") {
        //Tell the browser to redirect to the HTTPS URL.
        header("Location: https://" . $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
        exit;
    }
}

function testForCookies() {
    if (isset($_GET['cookies'])) {
        if (isset($_COOKIE['test'])) {
            return true;
        } else {
            die(header('Location: ./forbiddenAccess.php'));
        }
    } else {
        setcookie('test', "testvalue");
        if(isset($_GET["msg"]))
            die(header("Location: https://" . $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]. "&cookies=1"));
        else die(header("Location: https://" . $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]. "?cookies=1"));
    }
}

/* Session related functions */

function setSession($username) {
    $_SESSION['timestamp'] = time();
    $_SESSION['username'] = $username;
    $_SESSION['logged-in'] = 'y';
}

function destroySession() {
    session_start();
    $_SESSION = array();
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
            );
    }
    
    session_destroy();
}

/* Handle errors or bad paths */

function loginRegisterPrepare() {
    if(isset($_SESSION['username'])) { // if already logged in
        header("location: ./index.php");
        exit;
    }
}

function handleErrorLogin() {
    $var = "";
    if (isset($_GET['msg'])) {
        switch($_GET['msg']) {
            case 'error1':
                $var="Please provide a correct username!";
                break;
            case 'error2':
                $var="Incorrect username or password!";
                break;
            case 'error3':
                $var="Login failed!";
                break;
            case 'sessionExp':
                $var="Session expired, please login again!";
                break;
            default:
                break;
                
        }
    }
    return $var;
}

function handleErrorRegister() {
    $var = "";
    if (isset($_GET['msg'])) {
        if($_GET['msg'] == 'error1')
            $var="Passwords don't match!";
        else if($_GET['msg'] == 'error2')
            $var="Password must contain at least one lower-case alphabetic character,
                and at least one other character that is either alphabetical uppercase
                or numeric!";
        else if($_GET['msg'] == 'error3')
            $var="Registration failed!";
    } else
        $var="";
        
        return $var;
}

function handleErrorIndex() {
    $var = "";
    if (isset($_GET['msg'])) {
        if($_GET['msg'] == 'success')
            $var="Seat(s) purchased!";
            
        else if($_GET['msg'] == 'error')
            $var="Seat(s) not purchased!";
    } else
        $var="";
        
        return $var;
}

/* DB functions */

function connectToDB($host, $user, $pass, $table) {
    $server = mysqli_connect($host, $user, $pass, $table);
    if (!$server) {
       echo "Connection error!";
       return false;
    }
    return $server;
}

function registerUser($server, $table, $username, $password) {
    $query = "INSERT INTO $table (username, password) VALUES ('$username', '$password')";
    return (mysqli_query($server, $query)) ? true : false; 
}

function validateLogin($server, $username, $password) {
    $res = mysqli_query($server, "SELECT password FROM users WHERE username='$username'");
    if($res) {
        $row = mysqli_fetch_array($res);
        if($row['password'] == $password)
            return 1;
        
        else return 2;
    }
    return 3;
}

function checkPassword($pass1) {
    if(preg_match('`[a-z]`',$pass1) && (preg_match('`[A-Z]`',$pass1) ||  preg_match('`[0-9]`',$pass1)))
        return true;
    return false;
}

function sanitizeString($conn, $var) {
    $var = strip_tags($var);
    $var = htmlentities($var);
    $var = stripslashes($var);
    $var = mysqli_real_escape_string($conn, $var);
    return $var;
}

function addReservation($server, $seatId, $username) {
    $query = "INSERT INTO reservations(seatId, username, status) VALUES ('$seatId', '$username', 1)";  
    $res = mysqli_query($server, $query);
    
    return ($res != false) ? true : false;
}

function updateReservation($server, $seatId, $username) {
    $query = "UPDATE reservations SET username='$username' WHERE seatId='$seatId'";
    $res = mysqli_query($server, $query);
    
    return ($res != false) ? true : false;
}

function deleteReservation($server, $seatId, $username) {
    $query = "DELETE FROM reservations WHERE seatId='$seatId' AND username='$username'";
    $res = mysqli_query($server, $query);
    
    return ($res != false) ? true : false;
}

function unreserveFailedPurchase($server, $array, $username) {
    foreach($_SESSION['reservedSeats'] as $seatId => $value) {
        if(!in_array($seatId, $array))
            $res2 = deleteReservation($server, $seatId, $username);
    }
}

function purchaseSeat($server, $seatId, $username) {
    $seatId = sanitizeString($server, $seatId);
    $username = sanitizeString($server, $username);
    
    $query = "UPDATE reservations SET status=2 WHERE seatId='$seatId'";
    $res = mysqli_query($server, $query);
    
    return ($res != false) ? true : false;
}

/* Seat map functions */

function makeSeatMap() {
    global $seatsRow, $rows;
    $loggedIn = 'n';
    if(isset($_SESSION['logged-in'])) $loggedIn = 'y';
        
    echo "<script> configMap('",$rows,"', '",$seatsRow,"', '",$loggedIn,"'); </script>";    
    colorSeats();
}

function colorSeats() {
    global $free, $reserved, $purchased, $db_host, $db_user, $db_pass, $db_name;
    $server = connectToDB($db_host, $db_user, $db_pass, $db_name);
    if($server){
        $res = mysqli_query($server, "SELECT * FROM reservations WHERE status!=0");
        if($res) {
            $row = mysqli_fetch_array($res);
            $colors = [1 => '#ff6600', 2 => 'red', 3 => 'yellow'];
            while($row != NULL){
                $id = $row['seatId'];
                $status = $row['status'];
                
                if($status == 1){
                    if(isset($_SESSION['username']) && $row['username'] == $_SESSION['username']) {
                        echo "<script type='text/javascript'> configStyle('",$id,"', 1, '",$colors[3],"'); </script> ";
                        $_SESSION['reservedSeats'][$id] = 1;
                    }
                    else echo "<script type='text/javascript'> configStyle('",$id,"', 1, '",$colors[$status],"'); </script> ";
                }                
                if($status == 2){
                    echo "<script type='text/javascript'> configStyle('",$id,"', 2, '",$colors[$status],"'); </script> ";
                }
                $row = mysqli_fetch_array($res);
            }
            updateSeatAvailability();
            echo "<script> document.getElementById('seatNumbers').innerHTML += '<b>Purchased:</b> ", $purchased, "&ensp; <b> Reserved: </b>", $reserved, " <b>&ensp; Free: </b> ", $free, "'; </script> ";
            mysqli_free_result($res);
            mysqli_close($server);
        }
    }
    else echo "fail!";
}

/* Index elements functions */

function indexWelcomeMessage() {
    if(isset($_SESSION['logged-in']) && isset($_SESSION['username']))
        echo "<h1> Welcome ", $_SESSION['username'], "!</h1>";
    else echo "<h1> Welcome! </h1>";
}

function showHideButtons() {
    if(isset($_SESSION['logged-in']) && isset($_SESSION['username'])) 
        echo "<script> document.getElementById('buttons').style.display = 'block'; </script>";
}

function totalSeats() {
    global $rows, $seatsRow;
    echo "<p><b> Total seats: </b>",$rows*$seatsRow, "</p>";
    echo "<p id='seatNumbers'> </p>";
}

function updateSeatAvailability() {
    global $free, $reserved, $purchased, $rows, $seatsRow, $db_host, $db_user, $db_pass, $db_name;
    $server = connectToDB($db_host, $db_user, $db_pass, $db_name);
    if($server){
        $res = mysqli_query($server, "SELECT COUNT(*) FROM reservations WHERE status=1");
        if($res) {
            $r = mysqli_fetch_array($res);
            $reserved = $r[0];
            mysqli_free_result($res);
        }
        $res1 = mysqli_query($server, "SELECT COUNT(*) FROM reservations WHERE status=2");
        if($res1) {
            $p = mysqli_fetch_array($res1);
            $purchased = $p[0];
            mysqli_free_result($res1);
        }    
        mysqli_close($server);
        
        $free = ($seatsRow*$rows) - $reserved - $purchased;
    }
}

?>