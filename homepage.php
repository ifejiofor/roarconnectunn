<?php
require_once 'includes/utilityFunctions.php';
require_once 'includes/performBasicInitializations.php';
require_once 'includes/markupFunctions.php';

if ( !userIsLoggedIn() ) {
   header( 'Location: index.php' );
}
else {
   displayMarkupsCommonToTopOfPages( 'Home', DISPLAY_NAVIGATION_MENU, 'homepage.php' );
?>
            
            <div id="mainContainerInHomepage">
               <h2 id="headingInMainContainerInHomepage">Welcome to RoarConnect</h2>
<?php
   displayLatestStuffs();
?>
            </div>
<?php
   displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
}
?>