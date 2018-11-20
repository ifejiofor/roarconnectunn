<?php
require_once 'includes/utilityFunctions.php';
require_once 'includes/performBasicInitializations.php';
require_once 'includes/markupFunctions.php';

if(!userIsLoggedIn()){
	header('location:index.php');
}else{
   displayMarkupsCommonToTopOfPages( 'Make a Request', DISPLAY_NAVIGATION_MENU, 'make_a_request.php' );
?>
         <h3>Work in progress on this page. Please check back later.</h3>
<?php
   displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
?>
   </body>
</html>
<?php
}
?>