<!DOCTYPE html>

<?php
require 'includes/utilityFunctions.php';
require 'includes/markupFunctions.php';
error_reporting(0);

displayMarkupsCommonToTopOfPages( 'Sign Up', DO_NOT_DISPLAY_NAVIGATION_MENU, 'register.php' );

if (!userIsLoggedIn()){
    if(!isset($_POST['captcha'])){
        $_SESSION['captcha']=rand(10000, 99999);
    }
    else {
        if(isset($_POST['captcha']) && isset($_POST['first'])&&isset($_POST['email'])&&isset($_POST['phone'])&&isset($_POST['pass'])&&isset($_POST['repass'])){
	        $first=trim(htmlentities($_POST['first']));
	        $email=trim(htmlentities($_POST['email']));
	        $phone=trim(htmlentities($_POST['phone']));
	        $pass=trim(htmlentities($_POST['pass']));
	        $repass= trim(htmlentities($_POST['repass']));
	        $att=trim(htmlentities($_POST['att']));

		    if(!empty($first)&&!empty($email)&&!empty($phone)&&!empty($pass)&&!empty($repass)&& !empty($att)){
			    if(strlen($first)>=4){
				    $new=array(0,1,2,3,4,5,6,7,8,' ',9);			
				    foreach($new as $item){
						if(preg_match("/$item/", $first)){
							$true=true;
							break;
						}else{
							$true=false;
						}
					}
					
					if($true==true){
						echo'<p id="errorMessage">Spaces and numbers are not allowed in firstname.</p>';
					}else{
						$email_new= strtolower(substr($email, strpos($email, '@')+1));
			            if(preg_match('/@/', $email)){
			                if($email_new=='gmail.com'|| $email_new=='rocketmail.com' || $email_new=='yahoo.com' || $email_new=='yahoo.com.ng' || $email_new=='yahoo.co.uk' || $email_new=='gmail.co.uk' || $email_new=='gmail.com.ng' || $email_new=='unn.edu.ng'){
				                require 'includes/performBasicInitializations.php';
							   
								$email_chick="SELECT `email` FROM `users` WHERE `email`='$email'";
								if($email_query=mysqli_query($db, $email_chick)){
									$email_chick_run=mysqli_num_rows($email_query);
									if($email_chick_run==1){
										echo '<p id="errorMessage">The email you selected is already being used by another user.</p>';
										$_SESSION['captcha']=rand(10000, 99999);
									}else{
										if ( consistsOfOnlyDigits( $phone ) && $phone < 9999999999 && $phone > 7000000000 ) {
									    if($pass==$repass){
									        if(strlen($pass)>=5){
										        $net=array(0,1,2,3,4,5,6,7,8,9);
						                        foreach($net as $iitem){
											        if(preg_match("/$iitem/", $pass)){
													    $true=true;
													    break;
													}else{
														$true=false;
													}
									            }
												
                                                if($true==true){
                                                    if($_SESSION['captcha']!= $_POST['captcha']) {
			                                            echo'<p id="errorMessage">The Captcha text you entered is incorrect. Try again.</p>';
			                                            $_SESSION['captcha']=rand(10000, 99999);
                                                    }else {
												        $enter="INSERT INTO `users`(`firstname`, `email`, `phone_number`, `password`, `attribute`) VALUES ('$first', '$email', '$phone', '$pass', '$att')";
										                if($enter_run=mysqli_query($db, $enter)){
                                                  $userIdOfJustRegisteredUser = mysqli_insert_id( $db );
                                                   $query = 'INSERT INTO messages ( message_title, message_body, user_id_of_sender, user_id_of_recipient, message_time_of_sending ) VALUES ( "Welcome Message", "Welcome to RoarConnect, keep userIsLoggedIng in to enjoy the best of quality services and information.", NULL, ' . $userIdOfJustRegisteredUser . ', NOW() )';
                                                  mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
                                                   include  'sendmail.php'; // The code in this file sends an email to the just registered user
										                    header('location:registrationSuccessful.php');
										                }else{
											                echo '<p id="errorMessage">An unexpected error has prevented your registration from completing successfully. Please, cross-check your inputs and click the "Sign Up" button again. If the error still persists, wait a few minutes and try again. Sorry for the inconvenience. We are working hard to resolve the issue as soon as possible.</p>';	
										                }
											        }	
											    }else {
												    echo'<p id="errorMessage">Your password must be a combination of alphabets and numbers.</p>';
												    $_SESSION['captcha']=rand(10000, 99999);
											    }
									        }else {
									            echo '<p id="errorMessage">Your password is too weak. It must be more than five characters long.</p>';
					                            $_SESSION['captcha']=rand(10000, 99999);
									        }
								        }else {
									        echo '<p id="errorMessage">Passwords not match.</p>';
									        $_SESSION['captcha']=rand(10000, 99999);
								        }

										}
										else {
									    	echo '<p id="errorMessage">The phone number you entered is invalid.</p>';
										    $_SESSION['captcha']=rand(10000, 99999);
										}
									}
							    }else {
						            echo '<p id="errorMessage">An unexpected error has prevented your registration from completing successfully. Please, cross-check your inputs and click the "Sign Up" button again. If the error still persists, wait a few minutes and try again. Sorry for the inconvenience. We are working hard to resolve the issue as soon as possible.</p>';
						            $_SESSION['captcha']=rand(10000, 99999);
					            }
			                }else{
				                echo '<p id="errorMessage">The email address you entered is invalid. Please enter a valid email address.</p>';
			                    $_SESSION['captcha']=rand(10000, 99999);
			                }				
			            }else{
				            echo '<p id="errorMessage">The email address you entered is invalid. Please enter a valid email address.</p>';
				            $_SESSION['captcha']=rand(10000, 99999);
			            }
			        } 
				}
			    else{
				   echo'<p id="errorMessage">Firstname is too short. It should be at least four characters long.</p>';
				   $_SESSION['captcha']=rand(10000, 99999);
		        } 

		    }else {
			    echo '<strong id="errorMessage">Some fields are blank!</strong>';
			    $_SESSION['captcha']=rand(10000, 99999);
		    }
        }
    }
?>
      <form role="form" class="form-horizontal" id="looksLikeACardboardPaper" action="register.php" method="POST">
         <h1 id="mediumSizedText">Fill the form below to sign up and start enjoying the unlimited benefits of being a RoarConnect user!</h1>
         <div class="form-group">
            <label for="firstName" class="control-label col-sm-2">First Name:</label>
            <div class="col-sm-10"><input type="text" name="first" class="form-control" id="firstName" maxlength="35" value="<?php if(isset($_POST['first'])){echo $first;} ?>"/></div>
            <div class="help-block col-sm-offset-2 col-sm-10">First name must not contain any spaces or numbers.</div>
			</div>

         <div class="form-group">
            <label for="email" class="control-label col-sm-2">Email:</label>
            <div class="col-sm-10"><input type="text" name="email" class="form-control" id="email" maxlength="35" value="<?php if(isset($_POST['email'])){echo $email;}  ?>"/></div>
            <div class="help-block col-sm-offset-2 col-sm-10">Input a valid email address so as to get updates from us.</div>
			</div>

         <div class="form-group">
            <label for="phone" class="control-label col-sm-2">Phone Number:</label>
            <div class="col-sm-10"><input type="text" name="phone" class="form-control" id="phone" maxlength="35" value="<?php if(isset($_POST['phone'])){echo $phone;}  ?>"/></div>
            <div class="help-block col-sm-offset-2 col-sm-10">Input a valid phone number so as to get the best of services.</div>
         </div>

         <fieldset class="form-group">
            <legend class="control-label col-sm-2" id="boldSmallSizedText">Status:</legend>
            <div class="col-sm-10">
               <div>
                  <input type="radio" name="att" id="aspirant" value="ASPIRANT"/>
                  <label for="aspirant">Aspirant</label>
               </div>
               <div>
                  <input type="radio" name="att" id="fresher" value="FRESHER"/>
                  <label for="fresher">Fresher</label>
               </div>
               <div>
                  <input type="radio" name="att" id="oldStudent" value="OLD STUDENT"/>
                  <label for="oldStudent">Old Student</label>
               </div>
            </div>
            <div class="help-block col-sm-offset-2 col-sm-10">NB: YOUR STATUS CAN BE CHANGED LATER IN HOME PAGE.</div>
         </fieldset>

         <div class="form-group">
            <label for="password" class="control-label col-sm-2">Password:</label>
            <div class="col-sm-10"><input type="password" name="pass" class="form-control" id="password" maxlength="35"/></div>
            <div class="help-block col-sm-offset-2 col-sm-10">Password must be a combination of letters and numbers.</div>
			</div>

         <div class="form-group">
            <label for="retypePassword" class="control-label col-sm-2">Re-type Password:</label>
            <div class="col-sm-10"><input type="password" name="repass" class="form-control" id="retypePassword" maxlength="35"/></div>
         </div>

         <fieldset class="form-group">
            <legend class="control-label col-sm-2" id="boldSmallSizedText">Captcha:</legend>
            <div class="col-sm-10">
               <div>
                  <img src="generate.php"/>
               </div>
               <div>
                  <label for="captchaTextInputField">We need to confirm that you are not a robot. Below, type the text from the box above.</label>
               </div>
               <div>
               <input type="text" class="form-control" id="captchaTextInputField" name="captcha"/>
               </div>
            </div>
         </fieldset>

         <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10"><button type="submit" name="register" class="btn btn-success">Sign Up</button></div>
         </div>
      </form>

<?php
}else{
	echo '<p>';
   getFirstNameOfUser();
   echo ', you are already logged in.</p>';
}

displayMarkupsCommonToBottomOfPages( DO_NOT_DISPLAY_FOOTER );
?>