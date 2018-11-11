<?php
/*
   This is an include file.
   This file contains functions that display markups that are common to the top and bottom of all pages of RoarConnect.
*/

require_once 'in.php';
require_once 'require.php';
require_once 'markupsForMiscellaneousTasks.php';
define( 'DO_NOT_DISPLAY_NAVIGATION_MENU', 1 );
define( 'DISPLAY_NAVIGATION_MENU', 2 );
define( 'DISPLAY_FOOTER', 3 );
define( 'DO_NOT_DISPLAY_FOOTER', 4 );


function displayMarkupsCommonToTopOfPages( $titleOfCurrentPage, $navigationMenuStatus, $urlOfCurrentPage, $customizedStyleForBodyElement = NULL )
{
   global $db;
?>
<!DOCTYPE html>

<html lang="en">
   <head>
      <title><?php echo $titleOfCurrentPage ?> | RoarConnect</title>
      <meta name="description" content="" />
      <meta charset="utf-8"/>
      <meta name="viewport" content="width=device-width, initial-scale=1"/>
      <link rel="favicon icon" href="images/icons/RoarConnectFavicon.jpg"/>
      <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.min.css"/>
      <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css"/>
      <link rel="stylesheet" href="assets/customized_css/main.css"/>
      <script src="assets/customized_js/main.js"></script>
   </head>

   <body <?php echo ( $customizedStyleForBodyElement == NULL ? '' : 'style="' . $customizedStyleForBodyElement . '"' ); ?>>
      <header class="container-fluid" id="mainHeader">
         <a href="index.php"><img src="images/RoarConnectLogoSmall.jpg" alt="RoarConnect" id="mainLogoInMainHeader" /></a>
<?php
   if ( $navigationMenuStatus == DISPLAY_NAVIGATION_MENU ) {
?>

         <nav class="navbar" id="mainNavigation">
               <div class="navbar-header">
                  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navigationContents">
                     <span class="icon-bar"></span>
                     <span class="icon-bar"></span>
                     <span class="icon-bar"></span>
                  </button>
               </div>

               <div class="collapse navbar-collapse" id="navigationContents">
                  <ul class="navbar-nav">
                     <li><a href="blog_home.php" <?php echo $urlOfCurrentPage == 'blog_home.php' || $urlOfCurrentPage == 'blog.php' ? 'id="current"' : '' ?>><span class="glyphicon glyphicon-bullhorn"></span> News and Gists</a></li>
                     <li><a href="view_all_food_vendors.php" <?php echo $urlOfCurrentPage == 'view_all_food_vendors.php' ? 'id="current"' : '' ?>><span class="glyphicon glyphicon-cutlery"></span> Food Delivery</a></li>
                     <li><a href="lecture_notes.php" <?php echo $urlOfCurrentPage == 'lecture_notes.php' ? 'id="current"' : '' ?>><span class="glyphicon glyphicon-download-alt"></span> Lecture Notes</a></li>
                     <li><a href="view_all_utility_services.php" <?php echo $urlOfCurrentPage == 'view_all_utility_services.php' ? 'id="current"' : '' ?>><span class="glyphicon glyphicon-gift"></span> Utility Services</a></li>
<?php
      if ( loggin() ) {
	      $query = 'SELECT blog_category_name FROM blog_categories WHERE user_id_of_main_blogger = ' . $_SESSION['user_id'];
	      $resultContainingBlogPostCategories = mysqli_query( $db, $query) or die( $markupIndicatingDatabaseQueryFailure );
	      $userIsAMainBlogger = mysqli_num_rows( $resultContainingBlogPostCategories ) > 0;
	  
         $query = 'SELECT vendor_id, vendor_name FROM vendors WHERE user_id_of_vendor_manager = ' . $_SESSION['user_id'];
         $resultContainingVendorData = mysqli_query( $db, $query) or die( $markupIndicatingDatabaseQueryFailure );
         $userIsAVendorManager = mysqli_num_rows( $resultContainingVendorData ) > 0;

         $query = 'SELECT notification_id FROM notifications WHERE user_id_of_recipient = ' . $_SESSION['user_id'] . ' AND notification_status = "UNREAD"';
         $resultContainingAllUnreadNotifications = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
         $numberOfUnreadNotifications = mysqli_num_rows( $resultContainingAllUnreadNotifications );
         
         $query = 'SELECT message_id FROM messages WHERE user_id_of_recipient = ' . $_SESSION['user_id'] . ' AND message_status = "UNREAD"';
         $resultContainingAllUnreadMessages = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
         $numberOfUnreadMessages = mysqli_num_rows( $resultContainingAllUnreadMessages );
?>
                     <li id="dropdown">
                        <a id="dropdownLabel"><span class="glyphicon glyphicon-user"></span> <?php echo loggedInAsAdmin() ? 'Hello, Admin!' : 'Hello, ' . getfield() ?> <span class="glyphicon glyphicon-chevron-down"></span></a>
                        <ul id="dropdownContent">
                           <li><a href="#"><span class="glyphicon glyphicon-tint"></span> My Profile</a></li>
                           <li><a href="notifications.php"><span class="glyphicon glyphicon-bell"></span><?php echo $numberOfUnreadNotifications > 0 ? '<span class="badge" id="badgeForNotifications">' . $numberOfUnreadNotifications . '</span>' : '' ?> Notifications</a></li>
                           <li><a href="inbox.php"><span class="glyphicon glyphicon-envelope"></span><?php echo $numberOfUnreadMessages > 0 ? '<span class="badge" id="badgeForInbox">' . $numberOfUnreadMessages . '</span>' : '' ?> Messages</a></li>
                           <li><a href="your_blog_posts.php" <?php echo $urlOfCurrentPage == 'your_blog_posts.php' ? 'id="currentSublink"' : '' ?>><span class="glyphicon glyphicon-upload"></span> My Posts</a></li>
<?php
         if ( $userIsAMainBlogger ) {
?>
                           <li><a href="manage_blog_posts.php"><span class="glyphicon glyphicon-tasks"></span> Manage Posts</a></li>
<?php
         }

         if ( $userIsAVendorManager ) {
?>
                           <li><a href="your_uploads_as_manager_of_vendor.php"><span class="glyphicon glyphicon-upload"></span> My Uploads</a></li>
<?php
         }

         if ( loggedInAsAdmin() ) {
?>
                           <li><a href="all_roarconnect_uploads.php"><span class="glyphicon glyphicon-tasks"></span> Manage Uploads</a></li>
<?php
         }
?>
                           <li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Log Out</a></li>
                        </ul>
                     </li>
<?php
      }
      else {
?>
                     <li>
                        <noscript>
                           <span id="containerForLoginButtonWhenJavaScriptIsDisabled"><a href="login.php?urlOfPageToRedirectTo=<?php echo $_SERVER['PHP_SELF'] . buildStringContainingAllDataFromGET() ?>" id="solidButton"><span class="glyphicon glyphicon-log-in"></span> Login/Register</a></span>
                        </noscript>
                        <span id="containerForLoginButtonWhenJavaScriptIsEnabled"></span>
                     </li>
<?php
      }
?>
                  </ul>
               </div>
         </nav>
<?php
   }
?>
      </header>

<?php
   if (!loggin()) {
?>
      <div role="dialog" class="modal fade" id="myModal">
         <div class="modal-dialog">
            <div class="modal-content">

               <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">Log In</h4>
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
?>
      <div class="container-fluid" id="noPadding">
         <div class="col-sm-9">
<?php
}


function displayMarkupsCommonToBottomOfPages( $footerStatus = DO_NOT_DISPLAY_FOOTER )
{
?>
         </div> <!-- closing tag of div.col-sm-9 -->

         <aside class="col-sm-3">
            <div class="container-fluid" style="padding-top: 100px;">
               Adverts, related blog posts, featured foods, featured vendors, featured lecture notes shows here.
            </div>
         </aside>
      </div> <!-- closing tag of div.container-fluid -->
<?php
   if ( $footerStatus == DISPLAY_FOOTER ) {
?>

      <footer class="container-fluid" id="mainFooter">
         <a href="#mainHeader" id="linkBackToTopOfPage" title="Back to Top">
            <div><span class="glyphicon glyphicon-chevron-up"></span></div>
            <div><span class="glyphicon glyphicon-chevron-up"></span></div>
         </a>

         <section class="col-sm-7">
            <div class="text-center">
               <a href="index.php"><img src="images/RoarConnectLogoSmall.jpg" id="footerImage"/></a>
               <p>... Connecting users to services</p>
            </div>

            <div id="multilineTextInFooter">
               <p>RoarConnect connects you to all the interesting news and gists within UNN.</p>
               <p>Apart from this, RoarConnect connects you to food vendors within UNN.</p>
               <p>RoarConnect also maintains a comprehensive lecture note database from where you can download lecture notes of any course offered in UNN.</p>
               <p>Visit our social media pages for more information.</p>
            </div>

            <p class="text-center">
               <a href="https://facebook.com/roar.connect.5" title="Facebook"><span class="fa fa-facebook"></span></a>
               <a href="https://instagram.com/roarconnect/" title="Instagram"><span class="fa fa-instagram"></span></a>
               <a href="https://twitter.com/@roarconnect/" title="Twitter"><span class="fa fa-twitter"></span></a>
               <a href="mailto:roarconnect@roarconnectunn.com" title="Email"><span class="fa fa-envelope"></span></a>
            </p>
         </section>

         <section class="col-sm-5">
            <div id="containerHoldingQuickLinks">
               <h3>Quick Links</h3>
               <ul>
                  <li><a href="index.php">Home</a></li>
                  <li><a href="blog_home.php">News and Gists</a></li>
                  <li><a href="view_all_food_vendors.php">Food Delivery</a></li>
                  <li><a href="lecture_notes.php">Lecture Notes</a></li>
                  <li><a href="view_all_utility_services.php">Utility Services</a></li>
               </ul>
            </div>
         </section>

         <section class="col-sm-12">
            <p class="text-center">Copyright &copy; RoarConnect 2018</p>
            <p class="col-sm-12 text-center">This website was designed and built by <a href="https://facebook.com/ife.ejiofor">Ifechukwu Ejiofor</a> and <a href="#">Somukene Obiekwe</a></p>
         </section>
      </footer>
<?php
   }
?>

      <script src="assets/jquery/jquery.min.js"></script>
      <script src="assets/bootstrap/js/bootstrap.min.js"></script>
      <script>
         <!--
         document.getElementById( 'containerForLoginButtonWhenJavaScriptIsEnabled' ).innerHTML = '<a href="#" data-toggle="modal" data-target="#myModal"><span class="glyphicon glyphicon-log-in"></span> Login/Register</a>';

         var nodeListContainingElements = document.getElementsByClassName( 'containerForButtonThatWillTellUserToLogInBeforeContinuingWhenJavascriptIsEnabled' );

         for ( i = 0; i < nodeListContainingElements.length; i++ ) {
            nodeListContainingElements[i].innerHTML = '<a ' + miscellaneousAttributesOfButtonThatWillTellUserToLogInBeforeContinuing[i] + ' href="#" data-toggle="modal" data-target="#myModal2">' + textToDisplayInButtonThatWillTellUserToLogInBeforeContinuing[i] + '</a>';
         }
      </script>
 
      <script>
         <!--
         document.getElementById( 'postNewBlogUpdateButton' ).addEventListener( 'click', displayMarkupForPostNewBlogUpdateForm );

         function displayMarkupForPostNewBlogUpdateForm()
         {
	         document.getElementById( 'containerHoldingPostNewBlogUpdateForm' ).setAttribute( 'class', 'show' );
         }
         -->
      </script>

      <script>
         <!--
         document.getElementById('dropdown').addEventListener('mouseover', setOverflowToBeVisible);
         document.getElementById('dropdown').addEventListener('mouseout', setOverflowToBeHidden);

         function setOverflowToBeVisible()
         {
            document.getElementById('navigationContents').style.overflow = 'visible';
         }

         function setOverflowToBeHidden()
         {
            document.getElementById('navigationContents').style.overflow = 'hidden';
         }
         -->
      </script>
   </body>
</html>
<?php
}
?>