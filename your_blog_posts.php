<?php
require_once 'includes/utilityFunctions.php';
require_once 'includes/performBasicInitializations.php';
require_once 'includes/markupFunctions.php';
define( 'MAXIMUM_NUMBER_OF_HEADLINES_TO_DISPLAY', 5 );

if(!userIsLoggedIn()){
	header('location:index.php');
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

displayMarkupsCommonToTopOfPages( 'Your Blog Updates', DISPLAY_NAVIGATION_MENU, 'your_blog_posts.php' );
?>
            <header id="minorHeader">
               <h1 id="minorHeader">Your Blog Posts on RoarConnect Dashboard</h1>
			      <p>Here you can view all the blog updates you have posted on RoarConnect</p>
            </header>
            
<?php
$allBlogCategories = array( 'Aspirant Gists', 'Fresher Gists', 'Old Student Gists', 'Scholarships', 'Other', 'Football', 'Basketball' );

foreach ( $allBlogCategories as $key => $category ) {
	$query = 'SELECT blog_category_id, blog_category_name FROM blog_categories WHERE blog_category_name = "' . $category . '"';
	$resultContainingDataAboutBlogCategory = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
	$rowContainingDataAboutBlogCategory = mysqli_fetch_assoc( $resultContainingDataAboutBlogCategory );

   $currentOffset = isset( $_GET['requiredCategory'] ) && $_GET['requiredCategory'] == $rowContainingDataAboutBlogCategory['blog_category_id'] ? $_GET['offset'] : 0;

   $query = 'SELECT blog_post_id, blog_post_image_filename, blog_post_caption, blog_category_id, blog_post_approval_status, MONTHNAME( blog_post_time_of_posting ) AS month_of_posting, DAYOFMONTH( blog_post_time_of_posting ) AS day_of_posting, YEAR( blog_post_time_of_posting ) AS year_of_posting FROM blog_posts WHERE user_id_of_poster = ' . $_SESSION['user_id'] . ' AND blog_category_id = ' . $rowContainingDataAboutBlogCategory['blog_category_id'] . ' ORDER BY blog_post_time_of_posting DESC, blog_post_approval_status LIMIT ' . $currentOffset . ', ' . MAXIMUM_NUMBER_OF_HEADLINES_TO_DISPLAY;
   $resultContainingBlogPosts = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );

   if ( mysqli_num_rows( $resultContainingBlogPosts ) > 0 ) {
?>
            <p id="notFloating"></p>
            <section>
               <header id="minorHeaderType2">
                  <h2><?php echo $category ?> Category</h2>
               </header>
<?php
      $rowContainingBlogPost = mysqli_fetch_assoc( $resultContainingBlogPosts );
      $counter = 1;

      while ( $rowContainingBlogPost != NULL ) {
	      if ( strtoupper( $rowContainingBlogPost['blog_post_approval_status'] ) == 'NEWLY UPLOADED' ) {
		      $approvalStatus = 'Waiting for approval by RoarConnect\'s admin. This post will be made public as soon as it is approved.';
	      }
	      else if ( strtoupper( $rowContainingBlogPost['blog_post_approval_status'] ) == 'APPROVED' ) {
		      $approvalStatus = 'This post has been approved. It can now be viewed by all RoarConnect users.';
	      }
	      else if ( strtoupper( $rowContainingBlogPost['blog_post_approval_status'] ) == 'UNAPPROVED' ) {
		      $approvalStatus = 'This post was unapproved by RoarConnect\'s admin. <a href="#"  data-toggle="modal" data-target="#reasonForAdminAction' . $rowContainingBlogPost['blog_post_id'] . '">Click Here</a> to know why.';
	      }
?>
               <div id="<?php echo $rowContainingBlogPost['blog_post_id'] ?>">
                  <div id="blogHeadlineContainer">
<?php
	if ( $rowContainingBlogPost['blog_post_image_filename'] != NULL ) {
?>
                     <a href="blog.php?category=<?php echo $rowContainingDataAboutBlogCategory['blog_category_name'] ?>&idOfRequiredPost=<?php echo $rowContainingBlogPost['blog_post_id'] ?>"><img src="images/ImagesFor<?php echo $rowContainingDataAboutBlogCategory['blog_category_name'] ?>Updates/<?php echo $rowContainingBlogPost['blog_post_image_filename'] ?>" alt="Image of <?php echo $rowContainingBlogPost['blog_post_caption'] ?>" id="<?php echo $counter % 2 == 0 ? 'blogHeadlineImageEven' : 'blogHeadlineImageOdd' ?>" /></a>
<?php
	}
?>
                     <a href="blog.php?category=<?php echo $rowContainingDataAboutBlogCategory['blog_category_name'] ?>&idOfRequiredPost=<?php echo $rowContainingBlogPost['blog_post_id'] ?>"><h2 id="blogHeadlineText"><?php echo $rowContainingBlogPost['blog_post_caption'] ?></h2></a>
                     <p><span id="boldSmallSizedText">Posted on:</span> <?php echo $rowContainingBlogPost['month_of_posting'] . ' ' . $rowContainingBlogPost['day_of_posting'] . ', ' . $rowContainingBlogPost['year_of_posting'] ?></p>
                     <?php echo isMainBloggerForThisCategory( $_SESSION['user_id'], $rowContainingBlogPost['blog_category_id'] ) ? '' : '<p id="blackTinySizedText">' . $approvalStatus . '</p>' ?>

                     <a href="perform_administrative_action_on_blog_post.php?requiredAction=performAdminDeletion&idOfBlogPost=<?php echo $rowContainingBlogPost['blog_post_id'] ?>&urlOfSourcePage=your_blog_posts.php" class="btn btn-primary btn-sm" id="tinyMargin"><span class="glyphicon glyphicon-trash"></span> Delete</a>
                  </div>
               </div>

<?php
         if ( strtoupper( $rowContainingBlogPost['blog_post_approval_status'] ) == 'UNAPPROVED' ) {
            $query = 'SELECT reason FROM reasons_for_admin_actions_on_items WHERE type_of_item = "BLOG POST" AND id_of_item = ' . $rowContainingBlogPost['blog_post_id'];
            $resultContainingReasonForUnapproval = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
            $rowContainingReasonForUnapproval = mysqli_fetch_assoc( $resultContainingReasonForUnapproval );
            displayMarkupForReasonForAdminActionModal( 'Unapproval', $rowContainingReasonForUnapproval['reason'], 'reasonForAdminAction' . $rowContainingBlogPost['blog_post_id'] );
         }
            
         $counter++;
	      $rowContainingBlogPost = mysqli_fetch_assoc( $resultContainingBlogPosts );
      }
      
      
   $query = 'SELECT blog_post_id FROM blog_posts WHERE user_id_of_poster = ' . $_SESSION['user_id'] . ' AND blog_category_id = ' . $rowContainingDataAboutBlogCategory['blog_category_id'] . ' LIMIT ' . ( $currentOffset + MAXIMUM_NUMBER_OF_HEADLINES_TO_DISPLAY ) . ', 1';
   $resultContainingNextPost = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
   $rowContainingNextPost = mysqli_fetch_assoc( $resultContainingNextPost );

   if ( $rowContainingNextPost != NULL ) {
?>
               <p id="notFloating"><a href="your_blog_posts.php?requiredCategory=<?php echo $rowContainingDataAboutBlogCategory['blog_category_id'] ?>&offset=<?php echo $currentOffset + MAXIMUM_NUMBER_OF_HEADLINES_TO_DISPLAY ?>" id="specialButtonFloatingToTheRight">View More Updates &gt;&gt;</a></p>
<?php
   }
?>
            </section>
            
<?php
   }
}

displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
?>