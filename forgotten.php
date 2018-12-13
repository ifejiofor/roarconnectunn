<?php
require_once 'includes/generalHeaderFile.php';
require_once "includes/Mail.php";
error_reporting(1);

displayMarkupsCommonToTopOfPages( 'Forgotten Password', DO_NOT_DISPLAY_NAVIGATION_MENU, 'forgotten.php' );

if ( currentUserIsLoggedIn() ) {
	echo '
      <p id="mediumSizedText">';
   getFirstNameOfCurrentUser();
   echo ', you are already logged in.
      </p>
   ';
}
else {
	if(isset($_POST['email'])){
		$email=trim(htmlentities($_POST['email']));
		
		if(!empty($email)){
			$chick="SELECT `email` FROM `users` WHERE `email`= '$email'";
			
			if($chick_check=mysqli_query($db, $chick)){
				$query_check=mysqli_num_rows($chick_check);
				
				if($query_check==1){
					$email_new= strtolower(substr($email, strpos($email, '@')+1));
					
					if($email_new=='gmail.com'|| $email_new=='rocketmail.com' || $email_new=='yahoo.com' || $email_new=='yahoo.com.ng' || $email_new=='yahoo.co.uk' || $email_new=='gmail.co.uk' || $email_new=='gmail.com.ng' || $email_new=='unn.edu.ng'){
						require 'sendForgottenPasswordEmail.php';
					}else{
						echo '<p id="errorMessage">The email you entered is invalid. Please, enter a valid email.</p>';
					}
				}else{
					echo'<p id="errorMessage">No user with email of ' . $email . ' found. Please, confirm that you entered the correct email.</p>';
				}
			
			} else{
				echo '<p id="errorMessage">An unexpected error occurred. Please, cross-check your input and click the "Reset" button again. If the error still persists, wait a few minutes and try again. Sorry for the inconvenience. We are working hard to resolve the issue as soon as possible.</p>';
			}
		}else{
			echo '<p id="errorMessage">You did not enter any email address. Please enter your email address.</p>';
		}
	}

	if ( !isset($_COOKIE['email']) || empty( $_COOKIE['email'] ) ) { // The reset link hss not been successfully sent to user's email
?>

      <form role="form" class="container" id="looksLikeACardboardPaper" action="forgotten.php" method="POST">
         <h1 id="mediumSizedText">Enter your email address so that we can send you a reset link.</h1>

         <div class="form-group">
            <label for="email">Your Email:</label>
            <input type="text" name="email" class="form-control" id="email" maxlength="35" value="<?php if(isset($_POST['email'])){echo $email;}  ?>"/>
         </div>

         <div class="form-group">
            <input type="submit" value= "Send Reset Link" name="register" class="btn btn-success"/>
         </div>
      </form>
<?php
   }
   else {
?>
      <div class="container" id="containerHoldingSuccessMessage">
         <h2>Reset link successfully sent to your email.</h2>
         <p>To proceed with your password reset, go to your email inbox and click on the link just sent to you.</p>
      </div>
<?php
   }

}

displayMarkupsCommonToBottomOfPages( DO_NOT_DISPLAY_FOOTER );
?>