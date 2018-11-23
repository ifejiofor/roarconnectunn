<?php
require_once 'includes/generalHeaderFile.php';

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
            transferBlogPostUnregisteredUserDataToRegisteredUser($_SESSION['user_id'], $row['id']);
         }
         
         $_SESSION['user_id'] = $row['id'];
         $_SESSION['loginStatus'] = 'loggedin';
         $_SESSION['loginPrivileges'] = $row['login_privileges'];
die();
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


function transferBlogPostUnregisteredUserDataToRegisteredUser($idOfUnregisteredUser, $idOfRegisteredUser)
{
   transferBlogPostComments($idOfUnregisteredUser, $idOfRegisteredUser);
   transferBlogPostLikes($idOfUnregisteredUser, $idOfRegisteredUser);
   transferBlogPostLoves($idOfUnregisteredUser, $idOfRegisteredUser);
   transferBlogPostViews($idOfUnregisteredUser, $idOfRegisteredUser);
   transferBlogCategoryViews($idOfUnregisteredUser, $idOfRegisteredUser);
   deleteCachedUser($idOfUnregisteredUser);
}


function transferBlogPostComments($idOfSourceUser, $idOfDestinationUser)
{
   global $db, $markupIndicatingDatabaseQueryFailure;
   $query = 'UPDATE comments_to_blog_posts SET user_id_of_commenter = "' . $idOfDestinationUser . '" WHERE user_id_of_commenter = "' . $idOfSourceUser . '"';
   mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure);
}


function transferBlogPostLikes($idOfSourceUser, $idOfDestinationUser)
{
   global $db, $markupIndicatingDatabaseQueryFailure;

   deletePotentiallyRedundantBlogPostLikes($idOfSourceUser, $idOfDestinationUser);

   $query = 'UPDATE likes_to_blog_posts SET user_id_of_liker = "' . $idOfDestinationUser . '" WHERE user_id_of_liker = "' . $idOfSourceUser . '"';
   mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure);
}


function deletePotentiallyRedundantBlogPostLikes($idOfSourceUser, $idOfDestinationUser)
{
   global $db, $markupIndicatingDatabaseQueryFailure;

   $likesByDestinationUser = getIdOfAllBlogPostsThatAreLikedByUser($idOfDestinationUser);

   $query = 'DELETE FROM likes_to_blog_posts WHERE user_id_of_liker = "' . $idOfSourceUser . '" AND (FALSE';
   for ($i = 0; $i < count($likesByDestinationUser); $i++) {
      $query .= ' OR blog_post_id = "' . $likesByDestinationUser[$i] . '"';
   }

   $query .= ')';
   mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure);
}


function getIdOfAllBlogPostsThatAreLikedByUser($userId)
{
   global $db, $markupIndicatingDatabaseQueryFailure;

   $blogPostId = array();
   $query = 'SELECT blog_post_id FROM likes_to_blog_posts WHERE user_id_of_liker = "' . $userId . '"';
   $result = mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure.$query);

   for ($row = mysqli_fetch_assoc($result); $row != NULL; $row = mysqli_fetch_assoc($result)) {
      $blogPostId[] = $row['blog_post_id'];
   }

   return $blogPostId;
}


function transferBlogPostLoves($idOfSourceUser, $idOfDestinationUser)
{
   global $db;
   $query = 'UPDATE loves_to_blog_posts SET user_id_of_lover = "' . $idOfDestinationUser . '" WHERE user_id_of_lover = "' . $idOfSourceUser . '"';
   mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure);
}


function transferBlogPostViews($idOfSourceUser, $idOfDestinationUser)
{
   global $db;
   $query = 'UPDATE views_to_blog_posts SET user_id_of_viewer = "' . $idOfDestinationUser . '" WHERE user_id_of_viewer = "' . $idOfSourceUser . '"';
   mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure);
}


function transferBlogCategoryViews($idOfSourceUser, $idOfDestinationUser)
{
   global $db;
   $query = 'UPDATE views_to_blog_categories SET user_id_of_viewer = "' . $idOfDestinationUser . '" WHERE user_id_of_viewer = "' . $idOfSourceUser . '"';
   mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure);
}


function deleteCachedUser($idOfUser)
{
   global $db;
   $query = 'DELETE FROM cached_users WHERE id = "' . $idOfUser . '"';
   mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure);
}
?>