<?php
include_once 'includes/generalHeaderFile.php';

displayMarkupsCommonToTopOfPages( 'Registration Successful', DO_NOT_DISPLAY_NAVIGATION_MENU, 'registrationSuccessful.php' );
?>
      <div class="container" id="containerHoldingSuccessMessage">
         <h2>Registration Successful!</h2>
         <p>Congratulations! You have successfully signed up with RoarConnect.</p>
         <p>Welcome to the family.</p>
         <p>Our exciting services are now only a click of the mouse away from you.</p>
         <p>Simply <a href="login.php" class="btn btn-default btn-md">Click Here to Login and Get Started</a>.</p>
      </div>

<?php
displayMarkupsCommonToBottomOfPages( DO_NOT_DISPLAY_FOOTER );
?>