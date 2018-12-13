<?php
require_once 'includes/generalHeaderFile.php';

displayMarkupsCommonToTopOfPages( 'Reset Password', DO_NOT_DISPLAY_NAVIGATION_MENU, 'reset.php' );

if(currentUserIsLoggedIn()){
	echo '
      <p id="mediumSizedText">';
   getFirstNameOfCurrentUser();
   echo ', you are already logged in.
      </p>
   ';
}
else{
   if ( !isset($_COOKIE['email']) || empty($_COOKIE['email']) ) { // The reset link was not sent to user's email
		header( 'Location: forgotten.php' );
	}
   else {
	 if(isset($_POST['npass']) && isset($_POST['rpass'])&& isset($_POST['email'])){
		 $npass= trim(htmlentities($_POST['npass']));
		 $rpass= trim(htmlentities($_POST['rpass']));
		 $email=trim(htmlentities($_POST['email']));
			if(!empty($npass) && !empty($rpass) && !empty($email)){
				if($email==$_COOKIE['email']){
				if(strlen($npass)>=5){
				if($npass==$rpass){
					$net=array(0,1,2,3,4,5,6,7,8,9);
				foreach($net as $iitem){
				if(preg_match("/$iitem/", $npass)){
				$newPasswordContainsAtLeastOneNumber = true;
				break;
				}else{
					$newPasswordContainsAtLeastOneNumber=false;
				}
			   }

         if($newPasswordContainsAtLeastOneNumber){
					require_once 'includes/performBasicInitializations.php';
	$query="UPDATE `users` SET `password`='$npass' WHERE `email`= '".$email."'";
	if($query_run=mysqli_query($db, $query)){
		setcookie('passwardResetSuccessful', 'true', time()+3600);
      header( 'Location: pjjfgintjbysygshgshshsssn.php' );
	}else{
		die( '<p id="errorMessage">An unexpected error prevented your password from being reset successfully. Please, cross-check your input and click the "Reset" button again. If the error still persists, wait a few minutes and try again. Sorry for the inconvenience. We are working hard to resolve the issue as soon as possible.</p>' );
	}
				
			}else{
				echo'<p id="errorMessage">Password must be a combination of numbers and alphabets.</p>';
			}
	
			}else{
					echo '<p id="errorMessage">Passwords not match.</p>';
				}
			}else{
				echo '<p id="errorMessage">The password you selected is too weak. A strong password should be at least five characters long.</p>';
			}
			}else{
				echo '<p id="errorMessage">The email you entered is not correct. Enter the email which you got this reset link from.</p>';
			}
		}
      else {
         echo '<p id="errorMessage">You must fill out all fields.</p>';
      }
	 }
	}

   if ( !isset( $_COOKIE['passwardResetSuccessful'] ) ) { // password reset was not successful
?>

	   <form role="form" class="container" id="looksLikeACardboardPaper" action="reset.php" method="POST">
         <h1 id="mediumSizedText">Enter your email address, your new password, and its confirmation, then click the "Reset Password" button.</h1>

         <div class="form-group">
            <label for="email">Email:</label>
            <input type="text" name="email" class="form-control" id="email" maxlength="35" col="35" rows="21" value="<?php if(isset($_POST['email'])){echo $email;}  ?>"/>
         </div>

         <div class="form-group">
            <label for="newPassword">New Password:</label>
            <input type="password" name="npass" class="form-control" id="newPassword" maxlength="40"/>
         </div>

         <div class="form-group">
            <label for="repeatPassword">Repeat Password</label>
            <input type="password" name="rpass" class="form-control" id="repeatPassword" maxlength="40"/>
         </div>

         <div class="form-group">
	         <input type="submit" value="Reset Password" class="btn btn-success"/>
         </div>
	   </form>
<?php
   }
   else {
?>
      <div class="container" id="containerHoldingSuccessMessage">
         <h2>Your password has been reset successfully</h2>
         <p>Welcome back to the RoarConnect family!</p>
         <p>You can now <a href="login.php" class="btn btn-default btn-md">Login With Your New Password</a>.</p>
      </div>
<?php
   }
}

displayMarkupsCommonToBottomOfPages( DO_NOT_DISPLAY_FOOTER );
?>