<?php
require_once 'includes/generalHeaderFile.php';

if ( currentUserIsLoggedIn() ) {
   header( 'Location: homepage.php' );
}
else {
   displayMarkupsCommonToTopOfPages( 'Welcome', DISPLAY_NAVIGATION_MENU, 'index.php' );
   displayLatestStuffs();
   displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
}
?>