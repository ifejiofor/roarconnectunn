<?php
require_once 'in.php';
require_once 'require.php';
require_once 'markupsCommonToTopAndBottomOfPages.php';
require_once 'markupsForMiscellaneousTasks.php';
error_reporting(0);

displayMarkupsCommonToTopOfPages( 'Log In', DO_NOT_DISPLAY_NAVIGATION_MENU, 'login.php' );

if ( loggin() ) {
	echo '
      <p id="mediumSizedText">';
   getfield();
   echo ', you are already logged in.
      </p>
   ';
}
else {
   if(isset($_POST['email']) && isset($_POST['password']) && isset($_POST['urlOfPageToRedirectTo'])){
	$email=trim(htmlentities($_POST['email']));
	$pass=trim(htmlentities($_POST['password']));
   $urlOfPageToRedirectTo = trim(htmlentities($_POST['urlOfPageToRedirectTo']));

	if(!empty($email) || !empty($pass)){
		if (!empty($email)){
			if(!empty($pass)){
				$pass_new=$pass;
			$query="SELECT `id` , `login_privileges` FROM `users` WHERE `email`= '".$email."' AND `password` = '".$pass."'";
				if ($query_run=mysqli_query($db, $query)){
					$query_num_row=mysqli_num_rows($query_run);
					if($query_num_row==0){
						echo '<p id="errorMessage">Invalid email address or password.</p>';
					}else if($query_num_row==1){
				  $uery=mysqli_fetch_array($query_run);
				   $user_id =$uery['id'];
				   $_SESSION['user_id']=$user_id;
               $_SESSION['loginPrivileges'] = $uery['login_privileges'];
				  
				  header( 'Location: ' . $urlOfPageToRedirectTo . '?' . buildStringContainingAllDataFromGET() );
						
						
					}else{
							echo '<p id="errorMessage">Invalid email address or password.</p>';
					}
				}
	
			}else{
				echo'<p id="errorMessage">Please enter your password.</p>';
			}
		}else{
				echo'<p id="errorMessage">Please enter an email address.</p>';
			}
	}else {
		echo'<p id="errorMessage">Please enter your email address and your password.</p>';
	}
}

   if ( isset( $_GET['additionalMessage'] ) ) {
      echo '
         <h3 id="mediumSizedText">' . $_GET['additionalMessage'] . '</h3>';
   }

   if ( isset( $_POST['typeOfLogin'] ) ) {
      $typeOfLogin = $_POST['typeOfLogin'];
   }
   else {
      $typeOfLogin = NOT_ADMIN_LOGIN;
   }

   displayMarkupForLoginForm( $typeOfLogin, 'login.php' );
}

displayMarkupsCommonToBottomOfPages( DO_NOT_DISPLAY_FOOTER );
?>