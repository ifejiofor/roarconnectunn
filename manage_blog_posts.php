<?php
require_once 'includes/generalHeaderFile.php';
define( 'MAXIMUM_NUMBER_OF_HEADLINES_TO_DISPLAY', 10 );

if(!userIsLoggedIn()){
	header('location:index.php');
}

if ( !isset( $_GET['type'] ) ) {
   header( 'Location: index.php' );
}

if ( $_GET['type'] != 'Newly Uploaded' && $_GET['type'] != 'Approved' && $_GET['type'] != 'Unapproved' ) {
   header( 'Location: index.php' );
}

if ( isset( $_GET['offset'] ) && !isset( $_GET['requiredCategory'] ) )  {
	header( 'Location: index.php' );
}

if ( isset( $_GET['offset'] ) && !consistsOfOnlyDigits( $_GET['offset'] ) )  {
	header( 'Location: index.php' );
}

if ( isset( $_GET['requiredCategory'] ) && !consistsOfOnlyDigits( $_GET['requiredCategory'] ) )  {
	header( 'Location: index.php' );
}

if ( $_GET['type'] == 'Newly Uploaded' ) {
   $generalDescriptionOfPosts = 'all blog posts that were newly posted by other RoarConnect users';
}
else if ( $_GET['type'] == 'Approved' ) {
   $generalDescriptionOfPosts = 'all blog posts that were posted by other RoarConnect users and have been approved';
}
else if ( $_GET['type'] == 'Unapproved' ) {
   $generalDescriptionOfPosts = 'all blog posts that were posted by other RoarConnect users and have been unapproved';
}

$query = 'SELECT * FROM blog_categories WHERE user_id_of_main_blogger = ' . $_SESSION['user_id'];
$resultContainingCategoriesWhichLoggedInUserIsMainBloggerOf = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );

if ( mysqli_num_rows( $resultContainingCategoriesWhichLoggedInUserIsMainBloggerOf ) == 0 ) {
	header( 'Location: index.php' );
}

$rowContainingCategoriesWhichLoggedInUserIsMainBloggerOf = mysqli_fetch_assoc( $resultContainingCategoriesWhichLoggedInUserIsMainBloggerOf );

while ( $rowContainingCategoriesWhichLoggedInUserIsMainBloggerOf != NULL ) {
	$idOfCategoriesWhichLoggedInUserIsMainBloggerOf[] = $rowContainingCategoriesWhichLoggedInUserIsMainBloggerOf['blog_category_id'];
	$rowContainingCategoriesWhichLoggedInUserIsMainBloggerOf = mysqli_fetch_assoc( $resultContainingCategoriesWhichLoggedInUserIsMainBloggerOf );
}

displayMarkupsCommonToTopOfPages( 'Manage Blog Posts', DISPLAY_NAVIGATION_MENU, 'manage_blog_posts.php' );
?>
            <header id="minorHeader">
               <h1 id="minorHeader">Manage <?php echo $_GET['type'] ?> Blog Posts</h1>
               <p>Here, you can manage <?php echo $generalDescriptionOfPosts ?>.</p>
            </header>
            
<?php
foreach ( $idOfCategoriesWhichLoggedInUserIsMainBloggerOf as $key => $categoryId ) {
	$query = 'SELECT blog_category_name FROM blog_categories WHERE blog_category_id = ' . $categoryId;
	$resultContainingDataAboutBlogCategory = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
	$rowContainingDataAboutBlogCategory = mysqli_fetch_assoc( $resultContainingDataAboutBlogCategory );
?>
            <p id="notFloating"></p>
            <section>
               <header id="minorHeaderType2">
                  <h2><?php echo $rowContainingDataAboutBlogCategory['blog_category_name'] ?> Category</h2>
               </header>
<?php
   $currentOffset = isset( $_GET['requiredCategory'] ) && $_GET['requiredCategory'] == $categoryId ? $_GET['offset'] : 0;

   $query = 'SELECT blog_post_id, blog_category_id, blog_post_image_filename, blog_post_caption, user_id_of_poster, MONTHNAME( blog_post_time_of_posting ) AS month_of_posting, DAYOFMONTH( blog_post_time_of_posting ) AS day_of_posting, YEAR( blog_post_time_of_posting ) AS year_of_posting FROM blog_posts WHERE blog_post_approval_status = "' . $_GET['type'] . '" AND blog_category_id = ' . $categoryId . ' AND user_id_of_poster != ' . $_SESSION['user_id'] . ' ORDER BY blog_post_time_of_posting DESC LIMIT ' . $currentOffset . ', ' . MAXIMUM_NUMBER_OF_HEADLINES_TO_DISPLAY;
   $resultContainingBlogPosts = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );

   if ( mysqli_num_rows( $resultContainingBlogPosts ) == 0 ) {
?>
               <p id="mediumSizedText">No Blogs.</p>
<?php   
   }
   else {
      $rowContainingBlogPosts = mysqli_fetch_assoc( $resultContainingBlogPosts );
      $counter = 1;

      while ( $rowContainingBlogPosts != NULL ) {
         $query = 'SELECT firstname FROM users WHERE id = ' . $rowContainingBlogPosts['user_id_of_poster'];
         $resultContainingDataAboutPoster = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
         $rowContainingDataAboutPoster = mysqli_fetch_assoc( $resultContainingDataAboutPoster );
?>

               <div id="<?php echo $rowContainingBlogPosts['blog_post_id'] ?>">
                  <div id="blogHeadlineContainer">
<?php
	      if ( $rowContainingBlogPosts['blog_post_image_filename'] != NULL ) {
?>
                     <a href="blog.php?category=<?php echo $rowContainingDataAboutBlogCategory['blog_category_name'] ?>&idOfRequiredPost=<?php echo $rowContainingBlogPosts['blog_post_id'] ?>"><img src="images/ImagesFor<?php echo $rowContainingDataAboutBlogCategory['blog_category_name'] ?>Updates/<?php echo $rowContainingBlogPosts['blog_post_image_filename'] ?>" alt="Image of <?php echo $rowContainingBlogPosts['blog_post_caption'] ?>" id="<?php echo $counter % 2 == 0 ? 'blogHeadlineImageEven' : 'blogHeadlineImageOdd' ?>" /></a>
<?php
	      }
?>
                     <a href="blog.php?category=<?php echo $rowContainingDataAboutBlogCategory['blog_category_name'] ?>&idOfRequiredPost=<?php echo $rowContainingBlogPosts['blog_post_id'] ?>"><h2 id="blogHeadlineText"><?php echo $rowContainingBlogPosts['blog_post_caption'] ?></h2></a>
                     <p><span id="boldSmallSizedText">Posted by:</span> <?php echo $rowContainingDataAboutPoster['firstname'] ?></p>
                     <p><span id="boldSmallSizedText">Date of Posting:</span> <?php echo $rowContainingBlogPosts['month_of_posting'] . ' ' . $rowContainingBlogPosts['day_of_posting'] . ', ' . $rowContainingBlogPosts['year_of_posting'] ?></p>
               
<?php
   if ( $_GET['type'] == 'Newly Uploaded' ) {
?>
                     <a href="perform_administrative_action_on_blog_post.php?requiredAction=performAdminApproval&idOfBlogPost=<?php echo $rowContainingBlogPosts['blog_post_id'] ?>" class="btn btn-primary btn-sm" id="tinyMargin"><span class="glyphicon glyphicon-ok"></span> Approve</a>
                     <a href="perform_administrative_action_on_blog_post.php?requiredAction=performAdminUnapproval&idOfBlogPost=<?php echo $rowContainingBlogPosts['blog_post_id'] ?>" class="btn btn-primary btn-sm" id="tinyMargin"><span class="glyphicon glyphicon-remove"></span> Unapprove</a>
                     <a href="perform_administrative_action_on_blog_post.php?requiredAction=performAdminDeletion&idOfBlogPost=<?php echo $rowContainingBlogPosts['blog_post_id'] ?>" class="btn btn-primary btn-sm" id="tinyMargin"><span class="glyphicon glyphicon-trash"></span> Delete</a>
<?php
   }
   else if ( $_GET['type'] == 'Approved' ) {
?>
                     <a href="perform_administrative_action_on_blog_post.php?requiredAction=performAdminUnapproval&idOfBlogPost=<?php echo $rowContainingBlogPosts['blog_post_id'] ?>" class="btn btn-primary btn-sm" id="tinyMargin"><span class="glyphicon glyphicon-remove"></span> Unapprove</a>
                     <a href="perform_administrative_action_on_blog_post.php?requiredAction=performAdminDeletion&idOfBlogPost=<?php echo $rowContainingBlogPosts['blog_post_id'] ?>" class="btn btn-primary btn-sm" id="tinyMargin"><span class="glyphicon glyphicon-trash"></span> Delete</a>
<?php
   }
   else if ( $_GET['type'] == 'Unapproved' ) {
?>
                     <a href="perform_administrative_action_on_blog_post.php?requiredAction=performAdminApproval&idOfBlogPost=<?php echo $rowContainingBlogPosts['blog_post_id'] ?>" class="btn btn-primary btn-sm" id="tinyMargin"><span class="glyphicon glyphicon-ok"></span> Approve</a>
                     <a href="perform_administrative_action_on_blog_post.php?requiredAction=performAdminDeletion&idOfBlogPost=<?php echo $rowContainingBlogPosts['blog_post_id'] ?>" class="btn btn-primary btn-sm" id="tinyMargin"><span class="glyphicon glyphicon-trash"></span> Delete</a>
<?php
   }
?>
                  </div>
               </div>
               
<?php
         $counter++;
	      $rowContainingBlogPosts = mysqli_fetch_assoc( $resultContainingBlogPosts );
      }
   }
   
   $query = 'SELECT blog_post_id FROM blog_posts WHERE blog_post_approval_status = "' . $_GET['type'] . '" AND blog_category_id = ' . $categoryId . ' AND user_id_of_poster != ' . $_SESSION['user_id'] . '  LIMIT ' . ( $currentOffset + MAXIMUM_NUMBER_OF_HEADLINES_TO_DISPLAY ) . ', 1';
   $resultContainingNextPost = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
   $rowContainingNextPost = mysqli_fetch_assoc( $resultContainingNextPost );

   if ( $rowContainingNextPost != NULL ) {
?>
            <a href="manage_blog_posts.php?type=<?php echo $_GET['type'] ?>&requiredCategory=<?php echo $categoryId ?>&offset=<?php echo $currentOffset + MAXIMUM_NUMBER_OF_HEADLINES_TO_DISPLAY ?>" id="specialButtonFloatingToTheRight">View More Updates &gt;&gt;</a>
<?php
   }
?>
            </section>
            
<?php
}

displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
?>