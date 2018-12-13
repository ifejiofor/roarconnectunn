<?php
require_once 'includes/generalHeaderFile.php';

if (currentUserIsLoggedIn()) {
   header('Location: index.php');
}

displayMarkupsCommonToTopOfPages( 'Log In', DO_NOT_DISPLAY_NAVIGATION_MENU, 'login.php' );

if(currentUserPressedLoginButton()){
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
      $query = "SELECT `id`, `password`, `login_privileges` FROM `users` WHERE `email` = '" . mysqli_real_escape_string($globalHandleToDatabase, $_POST['email']). "'";
      $query_run = mysqli_query($globalHandleToDatabase, $query) or die($globalDatabaseErrorMarkup.$query);
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


function currentUserPressedLoginButton()
{
   return isset($_POST['loginButton']);
}


function isRegisteredUser($idOfUser)
{
   global $globalHandleToDatabase;
   $query = 'SELECT id FROM users WHERE id = "' . $idOfUser . '"';
   $result = mysqli_query($globalHandleToDatabase, $query) or die($globalDatabaseErrorMarkup.$query);
   return mysqli_num_rows($result) != 0;
}


function transferUnregisteredUserDataToRegisteredUser($idOfUnregisteredUser, $idOfRegisteredUser)
{
   transferBlogPostComments($idOfUnregisteredUser, $idOfRegisteredUser);
   transferBlogPostLikes($idOfUnregisteredUser, $idOfRegisteredUser);
   transferBlogPostLoves($idOfUnregisteredUser, $idOfRegisteredUser);
   transferBlogPostViews($idOfUnregisteredUser, $idOfRegisteredUser);
   transferBlogCategoryViews($idOfUnregisteredUser, $idOfRegisteredUser);
   deleteCachedUser($idOfUnregisteredUser);
}


function transferBlogPostComments($sourceUser, $destinationUser)
{
   global $globalHandleToDatabase, $globalDatabaseErrorMarkup;
   $query = 'UPDATE comments_to_blog_posts SET user_id_of_commenter = "' . $destinationUser . '" WHERE user_id_of_commenter = "' . $sourceUser . '"';
   mysqli_query($globalHandleToDatabase, $query) or die($globalDatabaseErrorMarkup.$query);
}


function transferBlogPostLikes($sourceUser, $destinationUser)
{
   transferBlogPostPassions('like', $sourceUser, $destinationUser);
}


function transferBlogPostPassions($passionType, $sourceUser, $destinationUser)
{
   global $globalHandleToDatabase, $globalDatabaseErrorMarkup;
   deleteRedundantBlogPostPassions($passionType, $sourceUser, $destinationUser);
   $query = 'UPDATE ' . $passionType . 's_to_blog_posts SET user_id_of_' . $passionType . 'r = "' . $destinationUser . '" WHERE user_id_of_' . $passionType . 'r = "' . $sourceUser . '"';
   mysqli_query($globalHandleToDatabase, $query) or die($globalDatabaseErrorMarkup.$query);
}


function deleteRedundantBlogPostPassions($passionType, $sourceUser, $destinationUser)
{
   global $globalHandleToDatabase, $globalDatabaseErrorMarkup;

   $query = 'DELETE FROM ' . $passionType . 's_to_blog_posts WHERE user_id_of_' . $passionType . 'r = "' . $sourceUser . '" AND (FALSE';
   $potentiallyRedundant = getIdOfAllBlogPostsThatUserIsPassionateAbout($passionType, $destinationUser);

   for ($i = 0; $i < count($potentiallyRedundant); $i++) {
      $query .= ' OR blog_post_id = "' . $potentiallyRedundant[$i] . '"';
   }

   $query .= ')';
   mysqli_query($globalHandleToDatabase, $query) or die($globalDatabaseErrorMarkup.$query);
}


function getIdOfAllBlogPostsThatUserIsPassionateAbout($passionType, $userId)
{
   global $globalHandleToDatabase, $globalDatabaseErrorMarkup;

   $query = 'SELECT blog_post_id FROM ' . $passionType . 's_to_blog_posts WHERE user_id_of_' . $passionType . 'r = "' . $userId . '"';
   $result = mysqli_query($globalHandleToDatabase, $query) or die($globalDatabaseErrorMarkup.$query);
   $idOfBlogPosts = array();

   for ($row = mysqli_fetch_assoc($result); $row != NULL; $row = mysqli_fetch_assoc($result)) {
      $idOfBlogPosts[] = $row['blog_post_id'];
   }

   return $idOfBlogPosts;
}


function transferBlogPostLoves($sourceUser, $destinationUser)
{
   transferBlogPostPassions('love', $sourceUser, $destinationUser);
}


function transferBlogPostViews($sourceUser, $destinationUser)
{
   global $globalHandleToDatabase, $globalDatabaseErrorMarkup;
   deleteRedundantBlogPostViews($sourceUser, $destinationUser);
   $query = 'UPDATE views_to_blog_posts SET user_id_of_viewer = "' . $destinationUser . '" WHERE user_id_of_viewer = "' . $sourceUser . '"';
   mysqli_query($globalHandleToDatabase, $query) or die($globalDatabaseErrorMarkup.$query);
}


function deleteRedundantBlogPostViews($sourceUser, $destinationUser)
{
   global $globalHandleToDatabase, $globalDatabaseErrorMarkup;

   $idOfCurrentBlogCategory = 0;
   $redundancyCountOfCurrentBlogCategory = 0;
   $redundantBlogPostViews = getResultContainingRedundantBlogPostViews($sourceUser, $destinationUser);

   for ($row = mysqli_fetch_assoc($redundantBlogPostViews); $row != NULL; $row = mysqli_fetch_assoc($redundantBlogPostViews)) {
      $query = 'DELETE FROM views_to_blog_posts WHERE blog_post_id = "' . $row['blog_post_id'] . '" AND user_id_of_viewer = "' . $sourceUser . '"';
      mysqli_query($globalHandleToDatabase, $query) or die($globalDatabaseErrorMarkup.$query);

      $query = 'UPDATE blog_posts SET blog_post_number_of_views = blog_post_number_of_views - 1, blog_post_relevance = (TIMESTAMPDIFF(minute, "2018-01-01 00:00:00", blog_post_time_of_posting) + (60 * blog_post_number_of_views)) WHERE blog_post_id = "' . $row['blog_post_id'] . '"';
      mysqli_query($globalHandleToDatabase, $query) or die($globalDatabaseErrorMarkup.$query);

      if ($row['blog_category_id'] == $idOfCurrentBlogCategory) {
         $redundancyCountOfCurrentBlogCategory++;
      }
      else {
         $query = 'UPDATE views_to_blog_categories SET number_of_blog_posts_viewed = number_of_blog_posts_viewed - ' . $redundancyCountOfCurrentBlogCategory . ' WHERE blog_category_id = "' . $idOfCurrentBlogCategory . '" AND user_id_of_viewer = "' . $destinationUser . '"';
         mysqli_query($globalHandleToDatabase, $query) or die($globalDatabaseErrorMarkup.$query);

         $idOfCurrentBlogCategory = $row['blog_category_id'];
         $redundancyCountOfCurrentBlogCategory = 1;
      }
   }

   $query = 'UPDATE views_to_blog_categories SET number_of_blog_posts_viewed = number_of_blog_posts_viewed - ' . $redundancyCountOfCurrentBlogCategory . ' WHERE blog_category_id = "' . $idOfCurrentBlogCategory . '" AND user_id_of_viewer = "' . $destinationUser . '"';
   mysqli_query($globalHandleToDatabase, $query) or die($globalDatabaseErrorMarkup.$query);
}


function getResultContainingRedundantBlogPostViews($sourceUser, $destinationUser)
{
   global $globalHandleToDatabase, $globalDatabaseErrorMarkup;

   $query = 'SELECT views_to_blog_posts.blog_post_id, blog_posts.blog_category_id FROM
      views_to_blog_posts INNER JOIN blog_posts ON views_to_blog_posts.blog_post_id = blog_posts.blog_post_id
      WHERE views_to_blog_posts.user_id_of_viewer = "' . $sourceUser . '" AND (FALSE';
   $potentiallyRedundant = getIdOfAllBlogPostsThatUserHasViewed($destinationUser);

   for ($i = 0; $i < count($potentiallyRedundant); $i++) {
      $query .= ' OR views_to_blog_posts.blog_post_id = "' . $potentiallyRedundant[$i] . '"';
   }

   $query .= ') ORDER BY blog_posts.blog_category_id';
   $result = mysqli_query($globalHandleToDatabase, $query) or die($globalDatabaseErrorMarkup.$query);
   return $result;
}


function getIdOfAllBlogPostsThatUserHasViewed($userId)
{
   global $globalHandleToDatabase, $globalDatabaseErrorMarkup;

   $idOfBlogPosts = array();
   $query = 'SELECT blog_post_id FROM views_to_blog_posts WHERE user_id_of_viewer = "' . $userId . '"';
   $result = mysqli_query($globalHandleToDatabase, $query) or die($globalDatabaseErrorMarkup.$query);

   for ($row = mysqli_fetch_assoc($result); $row != NULL; $row = mysqli_fetch_assoc($result)) {
      $idOfBlogPosts[] = $row['blog_post_id'];
   }

   return $idOfBlogPosts;
}


function transferBlogCategoryViews($sourceUser, $destinationUser)
{
   global $globalHandleToDatabase, $globalDatabaseErrorMarkup;

   $blogCategories = getIdOfAllBlogCategories();

   for ($i = 0; $i < count($blogCategories); $i++) {
      $numberOfViewsBySourceUser = getNumberOfBlogPostsViewedInBlogCategory($sourceUser, $blogCategories[$i]);
      $numberOfViewsByDestinationUser = getNumberOfBlogPostsViewedInBlogCategory($destinationUser, $blogCategories[$i]);

      if ($numberOfViewsBySourceUser != NULL && $numberOfViewsByDestinationUser != NULL) {
         $query = 'UPDATE views_to_blog_categories SET number_of_blog_posts_viewed = number_of_blog_posts_viewed + ' . $numberOfViewsBySourceUser . ' WHERE user_id_of_viewer = "' . $destinationUser . '" AND blog_category_id = "' . $blogCategories[$i] . '"';
         mysqli_query($globalHandleToDatabase, $query) or die($globalDatabaseErrorMarkup.$query);

         $query = 'DELETE FROM views_to_blog_categories WHERE user_id_of_viewer = "' . $sourceUser . '" AND blog_category_id = "' . $blogCategories[$i] . '"';
         mysqli_query($globalHandleToDatabase, $query) or die($globalDatabaseErrorMarkup.$query);
      }
      else if ($numberOfViewsBySourceUser != NULL) {
         $query = 'UPDATE views_to_blog_categories SET user_id_of_viewer = "' . $destinationUser . '" WHERE user_id_of_viewer = "' . $sourceUser . '" AND blog_category_id = "' . $blogCategories[$i] . '"';
         mysqli_query($globalHandleToDatabase, $query) or die($globalDatabaseErrorMarkup.$query);
      }
   }
}


function getIdOfAllBlogCategories()
{
   global $globalHandleToDatabase, $globalDatabaseErrorMarkup;

   $idOfBlogCategories = array();
   $query = 'SELECT blog_category_id FROM blog_categories';
   $result = mysqli_query($globalHandleToDatabase, $query) or die($globalDatabaseErrorMarkup.$query);

   for ($row = mysqli_fetch_assoc($result); $row != NULL; $row = mysqli_fetch_assoc($result)) {
      $idOfBlogCategories[] = $row['blog_category_id'];
   }

   return $idOfBlogCategories;
}


function getNumberOfBlogPostsViewedInBlogCategory($userId, $blogCategoryId)
{
   global $globalHandleToDatabase, $globalDatabaseErrorMarkup;
   $query = 'SELECT number_of_blog_posts_viewed FROM views_to_blog_categories WHERE user_id_of_viewer = "' . $userId . '" AND blog_category_id = "' . $blogCategoryId . '"';
   $result = mysqli_query($globalHandleToDatabase, $query) or die($globalDatabaseErrorMarkup.$query);
   $row = mysqli_fetch_assoc($result);
   return $row == NULL ? NULL : $row['number_of_blog_posts_viewed'];
}


function deleteCachedUser($idOfUser)
{
   global $globalHandleToDatabase;
   $query = 'DELETE FROM cached_users WHERE id = "' . $idOfUser . '"';
   mysqli_query($globalHandleToDatabase, $query) or die($globalDatabaseErrorMarkup.$query);
}
?>