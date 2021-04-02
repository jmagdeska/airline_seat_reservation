<?php 
require_once './session.php';
require_once './functions.php';
require_once './header.php';

$msgText = handleErrorIndex(); 
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="content-type">
        <script type="text/javascript" src="scriptFunctions.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
        <link rel = "stylesheet" type = "text/css" href = "style.css" />
        <title> Wizz Air </title>
	</head>
  
  	<body>    	
		<?php setHeader();
              setNavMenu('index'); ?>
    	 
    	<div id="page-container">
    		<div id="content-wrap">
    			<?php require_once './setup.php';?>
    			<?php indexWelcomeMessage();?>   			
    			
          		<h3 class="purple"> Flight: Milan Malpensa (MXP) -> Skopje (SKP) </h3>
          		<p> <b> Departure: </b> 29/07/2019 15:25 </p>
          		
          		<?php totalSeats(); ?>
          		                    		
          		<?php if(isset($_SESSION['logged-in']) && $_SESSION['logged-in'] == 'y') {
          		    echo "<h4> Click on a seat to reserve: </h4>";          		    
          		}?>
          		
          		<p id="msgText" class='success'> <?php 
          		if(isset($_GET['msg']) && $_GET['msg'] == 'error')
          		    echo "<script> document.getElementById('msgText').className = 'errorMsg'; </script>";
          		echo $msgText;?></p>
          		
          		<div id="planeMap"> </div>
          		<div id="scriptText"> <?php makeSeatMap(); ?> </div>
           		
           		<div id="buttons" style="display: none;">
               		<button id='updateBtn' onclick='updateSeatMap();'> Update </button> 
                  	<button id='buyBtn' onclick='buySeats();'> Buy </button> 
           		</div>
           		
           		<?php showHideButtons(); ?>          		
  			</div>
      	</div>	
      	<?php require_once './footer.php'; ?>
  	</body>
</html>