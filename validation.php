<?php 
    require_once './functions.php';    

    if(empty($_SESSION)) 
        session_start();
    
    if(isset($_POST['username'])) {        
        //handle register
        if(isset($_POST['register'])) {     
            $server = connectToDB($db_host, $db_user, $db_pass, $db_name);
            if($server != false) {                
                $username = sanitizeString($server, $_POST['username']);
                $pass1 = sanitizeString($server, $_POST['pass1']);
                $pass2 = sanitizeString($server, $_POST['pass2']);
            
                if($pass1 != $pass2) {  
                    header("Location: ./register.php?msg=error1");                
                    exit();               
                }
                else {
                    if(checkPassword($pass1)) {
                        if(registerUser($server, 'users', $username, md5($pass1))) {
                            mysqli_close($server);
                            setSession($username);  
                            header("Location: ./index.php");
                            exit();	
                        }
                        else {
                            header("Location: ./register.php?msg=error3");
                            exit();
                        }
                    }
                    else {
                        header("Location: ./register.php?msg=error2");
                        exit(); 
                    }
                }
            }
            else {
                header("Location: ./register.php?msg=error3");
                exit();   
            }                 
        }
        
        // handle login
        else if(isset($_POST['login'])) {
            $server = connectToDB($db_host, $db_user, $db_pass, $db_name);
            if($server != false) {            
                $username = sanitizeString($server, $_POST['username']);
                $pass = md5(sanitizeString($server, $_POST['password']));
                
                $res = validateLogin($server, $username, $pass);
                mysqli_close($server);
                
                if($res == 1) {    
                    setSession($username);
                    header("Location: ./index.php");
                    exit();	                     
                }
                else if($res == 2) {
                    header("Location: ./login.php?msg=error2");
                    exit();
                }
                else {
                    header("Location: ./login.php?msg=error1");
                    exit();
                }  
            }
            else {
                header("Location: ./login.php?msg=error3");
                exit();
            }
        }
    }
    else {
        header("Location: ./index.php");
        exit();
    }
?>