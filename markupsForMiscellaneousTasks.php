<?php
/* 

*/

include_once 'in.php';
define( 'NOT_ADMIN_LOGIN', 1 );
define( 'ADMIN_LOGIN', 2 );


function getMarkupForButtonThatWillTellUserToLogInBeforeContinuing( $textToBeContainedInButton, $miscellaneousAttributesOfButton = '' )
{
?>

                        <div>
                           <noscript>
                              <p class="containerForButtonThatWillTellUserToLogInBeforeContinuingWhenJavascriptIsDisabled"><a href="login.php?additionalMessage=You Must Log In To Continue&urlOfPageToRedirectTo=<?php echo $_SERVER['PHP_SELF'] . buildStringContainingAllDataFromGET() ?>"><?php echo $textToBeContainedInButton ?></a></p>
                           </noscript>
                           <div class="containerForButtonThatWillTellUserToLogInBeforeContinuingWhenJavascriptIsEnabled"></div>
                        </div>

                        <script>
                           <!--
                           textToDisplayInButtonThatWillTellUserToLogInBeforeContinuing[textToDisplayInButtonThatWillTellUserToLogInBeforeContinuing.length] = '<?php echo $textToBeContainedInButton ?>';
                           miscellaneousAttributesOfButtonThatWillTellUserToLogInBeforeContinuing[miscellaneousAttributesOfButtonThatWillTellUserToLogInBeforeContinuing.length] = '<?php echo $miscellaneousAttributesOfButton ?>';
                           //-->
                        </script>
<?php
}


function getMarkupForModalThatTellsUserToLogInBeforeContinuing()
{
?>

            <div role="dialog" class="modal fade" id="myModal2">
               <div class="modal-dialog">
                  <div class="modal-content">

                     <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h3 class="modal-title">You Must Log In To Continue</h3>
                     </div>

                     <div class="modal-body">
                        <?php echo displayMarkupForLoginForm() ?>
                     </div>

                     <div class="modal-footer">
                       <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                     </div>
                  </div>
               </div>
            </div>
<?php
}


function displayMarkupToIndicateThatAdminLoginIsRequired()
{
?>

            <div id="loginPanelBehind"></div>

            <div id="loginPanel">
               <div id="loginPanelHeader"><h2>Admin Login Required to Access this Page</h2></div>

               <div id="loginPanelBody">
                  <?php echo displayMarkupForLoginForm( ADMIN_LOGIN ) ?>
               </div>
            </div>
<?php
}


function displayMarkupForLoginForm( $typeOfLogin = NOT_ADMIN_LOGIN, $urlOfCurrentPage = '' )
{
?>

                        <form role="form" id="looksLikeACardboardPaper" method="POST" action="login.php<?php echo '?' . buildStringContainingAllDataFromGET() ?>">
<?php
   if ( $typeOfLogin == NOT_ADMIN_LOGIN ) {
      echo '
                           <h1 id="mediumSizedText">Log in to RoarConnect</h1>';
   }
   else {
      echo '
                           <h1 id="mediumSizedText">Admin Login Panel</h1>';
   }
?>

                           <div class="form-group">
                              <label for="username">Email:</label>
                              <input type="text" name="email" class="form-control" id="username" maxlength="35" value="<?php if(isset($_POST['email'])){echo $_POST['email'];}  ?>"/>
                           </div>

                           <div class="form-group">
                              <label for="password">Password:</label>
                              <input type="password" name="password" class="form-control" id="password" maxlength="35"/>
                           </div>
<?php
   if ( $typeOfLogin == NOT_ADMIN_LOGIN ) {
      echo '
                           <p class="help-block">Don\'t have a RoarConnect account? <a href="register.php">Click here to sign up with us</a>.</p>
                           <p class="help-block">Forgotten your password? <a href="forgotten.php">Click here to reset your password</a>.</p>
      ';
   }

   if ( $urlOfCurrentPage != 'login.php' ) {
      echo '
                           <input type="hidden" name="urlOfPageToRedirectTo" value="' . $_SERVER['PHP_SELF'] . '" />';
   }
   else if ( isset( $_POST['urlOfPageToRedirectTo'] ) ) {
      echo '
                           <input type="hidden" name="urlOfPageToRedirectTo" value="' . $_POST['urlOfPageToRedirectTo'] . '" />';
   }
   else if ( isset( $_GET['urlOfPageToRedirectTo'] ) ) {
      echo '
                           <input type="hidden" name="urlOfPageToRedirectTo" value="' . $_GET['urlOfPageToRedirectTo'] . '" />';
   }
   else {
      echo '
                           <input type="hidden" name="urlOfPageToRedirectTo" value="homepage.php" />';
   }
?>

                           <input type="hidden" name="typeOfLogin" value="<?php echo $typeOfLogin ?>" />

                           <div class="form-group">
                              <button type="submit" name="login" class="btn btn-success">Log In</button>
                           </div>
                        </form>
<?php
}


function displayMarkupForReasonForAdminActionModal( $typeOfAdminAction, $reason, $idOfModal = 'reasonForAdminAction' )
{
?>

                  <div style="top: 30%;" id="<?php echo $idOfModal ?>" class="modal fade" role="dialog">
                     <div class="modal-dialog">
                        <div class="modal-content">
                           <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal">&times;</button>
                              <h4 class="modal-title">Reason For <?php echo $typeOfAdminAction ?></h4>
                           </div>
                           <div class="modal-body">
                              <p><?php echo $reason ?></p>
                           </div>
                           <div class="modal-footer">
                              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                           </div>
                        </div>
                     </div>
                  </div>
            
<?php
}
?>