<?php 
require_once './session.php';
require_once './functions.php';
require_once './header.php';

loginRegisterPrepare();
$errorText = handleErrorLogin();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="content-type">
         <link rel = "stylesheet" type = "text/css" href = "style.css" />
        <title> Wizz Air - Login </title>
	</head>
  
  	<body>  
      	<?php 
      	     setHeader();
      	     setNavMenu('login');?>    
      	     	
    	<div id="page-container">
    		<div id="content-wrap">
    			<?php require_once './setup.php';?>
            	<p class="errorMsg"><?php echo $errorText;?></p>
            	<h1> Enter login credentials: </h1>    	
            	    	
            	<form action="./validation.php" method="post">
            		<label for="username"> Username: </label>
            		<input type="email" name="username" id="username" placeholder="email@domain.com" required> <br><br>
            		<label for="password"> Password: </label>
            		<input type="password" name="password" id="password" placeholder="*******" required>
            		<input type="submit" name="login" value="Login">
            	</form>	
            </div>
         </div>
         
    	<?php require_once  './footer.php'; ?>
	</body>
</html>