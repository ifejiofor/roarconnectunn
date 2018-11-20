<?php
require_once 'includes/performBasicInitializations.php';
require_once 'includes/utilityFunctions.php';
require_once 'includes/markupFunctions.php';

if (userIsLoggedIn()) {
   header('Location: index.php');
}

displayMarkupsCommonToTopOfPages( 'Log In', DO_NOT_DISPLAY_NAVIGATION_MENU, 'login.php' );

if(userHasPressedLoginButton()){
   if (empty($_POST['email']) && empty($_POST['password'])) {
      echo '<p id="errorMessage">Please enter your email address and password.</p>';
   }
   else if (empty($_POST['email'])) {
      echo '<p id="errorMessage">Please enter your password.</p>';
   }
   else if (empty($_POST['password'])) {
      echo '<p id="errorMessage">Please enter your email address and password.</p>';
   }
   else {
      $query = "SELECT `id`, `password`, `login_privileges` FROM `users` WHERE `email` = '" . mysqli_real_escape_string($db, $_POST['email']). "'";
      $query_run = mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure);
      $row = mysqli_fetch_assoc($query_run);

      if ($row == NULL) {
         echo '<p id="errorMessage">The email address you entered is not registered with any account in our database. Do you want to register a new account? Then <a href="register.php">Click here</a>.</p>';
      }
      else if ($row['password'] != $_POST['password']) {
         echo '<p id="errorMessage">The password you entered is incorrect</p>';
      }
      else {
         if ($row['id'] != $_SESSION['user_id'] && !isRegisteredUser($_SESSION['user_id'])) {
            transferUnregisteredUserDataToRegisteredUser($_SESSION['user_id'], $row['id']);
         }
         
         $_SESSION['user_id'] = $row['id'];
         $_SESSION['loginStatus'] = 'loggedin';
         $_SESSION['loginPrivileges'] = $row['login_privileges'];
         header( 'Location: ' . $_POST['urlOfPageToRedirectTo'] . '?' . buildStringContainingAllDataFromGET() );
      }
   }
}

if ( isset( $_GET['additionalMessage'] ) ) {
?>
         <h3 id="mediumSizedText"><?php $_GET['additionalMessage'] ?></h3>
<?php
}

displayMarkupForLoginForm( isset($_POST['typeOfLogin']) ? $_POST['typeOfLogin'] : NOT_ADMIN_LOGIN, basename($_SERVER['PHP_SELF']) );
displayMarkupsCommonToBottomOfPages( DO_NOT_DISPLAY_FOOTER );


function userHasPressedLoginButton()
{
   return isset($_POST['loginButton']);
}


function isRegisteredUser($idOfUser)
{
   global $db;
   $query = 'SELECT id FROM users WHERE id = "' . $idOfUser . '"';
   $result = mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure);
   return mysqli_num_rows($result) != 0;
}


function transferUnregisteredUserDataToRegisteredUser($idOfUnregisteredUser, $idOfRegisteredUser)
{
   transferCommentsToBlogPosts($idOfUnregisteredUser, $idOfRegisteredUser);
   transferLikesToBlogPosts($idOfUnregisteredUser, $idOfRegisteredUser);
   transferLovesToBlogPosts($idOfUnregisteredUser, $idOfRegisteredUser);
   transferViewsToBlogPosts($idOfUnregisteredUser, $idOfRegisteredUser);
   transferViewsToBlogCategories($idOfUnregisteredUser, $idOfRegisteredUser);
   deleteCachedUser($idOfUnregisteredUser);
}


function transferCommentsToBlogPosts($from, $to)
{
   global $db;
   $query = 'UPDATE comments_to_blog_posts SET user_id_of_commenter = "' . $to . '" WHERE user_id_of_commenter = "' . $from . '"';
   mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure);
}


function transferLikesToBlogPosts($from, $to)
{
   global $db;
   $query = 'UPDATE likes_to_blog_posts SET user_id_of_liker = "' . $to . '" WHERE user_id_of_liker = "' . $from . '"';
   mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure);
}


function transferLovesToBlogPosts($from, $to)
{
   global $db;
   $query = 'UPDATE loves_to_blog_posts SET user_id_of_lover = "' . $to . '" WHERE user_id_of_lover = "' . $from . '"';
   mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure);
}


function transferViewsToBlogPosts($from, $to)
{
   global $db;
   $query = 'UPDATE views_to_blog_posts SET user_id_of_viewer = "' . $to . '" WHERE user_id_of_viewer = "' . $from . '"';
   mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure);
}


function transferViewsToBlogCategories($from, $to)
{
   global $db;
   $query = 'UPDATE views_to_blog_categories SET user_id_of_viewer = "' . $to . '" WHERE user_id_of_viewer = "' . $from . '"';
   mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure);
}


function deleteCachedUser($idOfUser)
{
   global $db;
   $query = 'DELETE FROM cached_users WHERE id = "' . $idOfUser . '"';
   mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure);
}
?>