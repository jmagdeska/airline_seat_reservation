<?php 

if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die('Direct access not allowed');
    exit();
};

echo "<div id='footer'> 
  		<p class='footer-right'> © Jana Magdeska 2019. All rights reserved. </p>
  		<div style='clear: both'></div>      	
  	</div>";
?>
