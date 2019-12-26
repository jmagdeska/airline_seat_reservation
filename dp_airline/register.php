<?php 
require_once './session.php';
require_once './functions.php';
require_once './header.php';

loginRegisterPrepare();
$errorText = handleErrorRegister();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="content-type">
         <link rel = "stylesheet" type = "text/css" href = "style.css" />
        <title> Wizz Air - Register</title>
	</head>
  
  	<body>  
      	<?php 
      	     setHeader();
      	     setNavMenu('register'); 
      	?>
    	<div id="page-container">
    		<div id="content-wrap">
    			<?php require_once './setup.php';?>
            	<p class="errorMsg"><?php echo $errorText;?></p>    	
            	<h1> Enter register information: </h1>
            	
            	<form action="./validation.php" method="post">
            		<label for="username"> Username: </label>
            		<input type="email" name="username" id="username" placeholder="email@domain.com" required> <br><br>
            		<label for="pass1"> Password: </label>
            		<input type="password" name="pass1" id="pass1" placeholder="*******" required> <br><br>
            		<label for="pass2"> Repeat password: </label>
            		<input type="password" name="pass2" id="pass2" placeholder="*******" required>
            		<input type="submit" name="register" value="Register">
            	</form>	
    		</div>    	
    	</div>
    	
    	<?php require_once  './footer.php'; ?>
	</body>
</html>