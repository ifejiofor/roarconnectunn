<?php
require_once 'in.php';
require_once 'require.php';
require_once 'markupsCommonToTopAndBottomOfPages.php';

if(!loggin()){
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