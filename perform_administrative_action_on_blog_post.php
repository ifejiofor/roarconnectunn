<?php
require_once 'includes/generalHeaderFile.php';

if ( !isset( $_GET['idOfBlogPost'] ) || !consistsOfOnlyDigits( $_GET['idOfBlogPost'] ) ) {
   header( 'Location: index.php' );
}

if ( !isset( $_GET['requiredAction'] ) && !isset( $_GET['actionPerformed'] ) ) {
   header( 'Location: index.php' );
}

if ( isset( $_GET['requiredAction'] ) && ( $_GET['requiredAction'] != 'performAdminApproval' && $_GET['requiredAction'] != 'performAdminUnapproval' && $_GET['requiredAction'] != 'performAdminDeletion' ) ) {
   header( 'Location: index.php' );
}

if ( isset( $_GET['actionPerformed'] ) && ( $_GET['actionPerformed'] != 'adminApprovalPerformedSuccessfully' && $_GET['actionPerformed'] != 'adminUnapprovalPerformedSuccessfully' && $_GET['actionPerformed'] != 'adminDeletionPerformedSuccessfully' ) ) {
   header( 'Location: index.php' );
}

if ( $_GET['requiredAction'] == 'performAdminApproval' || $_GET['actionPerformed'] == 'adminApprovalPerformedSuccessfully' ) {
   $requiredActionInConciseForm = 'Approve';
   $requiredActionInConciseNounForm = 'Approval';
}
else if ( $_GET['requiredAction'] == 'performAdminUnapproval' || $_GET['actionPerformed'] == 'adminUnapprovalPerformedSuccessfully' ) {
   $requiredActionInConciseForm = 'Unapprove';
   $requiredActionInConciseNounForm = 'Unapproval';
}
else if ( $_GET['requiredAction'] == 'performAdminDeletion' || $_GET['actionPerformed'] == 'adminDeletionPerformedSuccessfully' ) {
   $requiredActionInConciseForm = 'Delete';
   $requiredActionInConciseNounForm = 'Deletion';
}

if ( $_GET['actionPerformed'] != 'adminDeletionPerformedSuccessfully' ) {
   $query = 'SELECT blog_post_id, blog_category_id, blog_post_image_filename, blog_post_caption, blog_post_approval_status, user_id_of_poster, MONTHNAME( blog_post_time_of_posting ) AS month_of_posting, DAYOFMONTH( blog_post_time_of_posting ) AS day_of_posting, YEAR( blog_post_time_of_posting ) AS year_of_posting FROM blog_posts WHERE blog_post_id = ' . $_GET['idOfBlogPost'];
   $resultContainingBlogPostData = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
   $rowContainingBlogPostData = mysqli_fetch_assoc( $resultContainingBlogPostData );

   if ( $_GET['requiredAction'] != 'performAdminDeletion' && !isMainBloggerForThisCategory( $_SESSION['user_id'], $rowContainingBlogPostData['blog_category_id'] ) ) {
      header( 'Location: index.php' );
   }

   $query = 'SELECT blog_category_name FROM blog_categories WHERE blog_category_id = ' . $rowContainingBlogPostData['blog_category_id'];
   $resultContainingBlogCategoryData = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
   $rowContainingBlogCategoryData = mysqli_fetch_assoc( $resultContainingBlogCategoryData );

   $query = 'SELECT firstname FROM users WHERE id = ' . $rowContainingBlogPostData['user_id_of_poster'];
   $resultContainingUserData = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
   $rowContainingUserData = mysqli_fetch_assoc( $resultContainingUserData );

   $directory = 'images/ImagesFor' . ucwords( $rowContainingBlogCategoryData['blog_category_name'] ) . 'Updates';
}

if ( isset( $_GET['requiredAction'] ) ) {
   if ( !isset( $_GET['confirmation'] ) ) {  // neither "Yes" nor "No" buttons have been clicked
      displayMarkupsCommonToTopOfPages( $requiredActionInConciseForm . ' Item', DISPLAY_NAVIGATION_MENU, 'perform_administrative_action_on_blog_post.php' );
?>

            <div id="containerHoldingErrorMessage">
               <h2><?php echo $requiredActionInConciseForm ?> Blog Post</h2>
               <p>Are you sure you want to <?php echo strtolower( $requiredActionInConciseForm ) ?> this blog post?</p>
<?php
	      if ( $rowContainingBlogPostData['blog_post_image_filename'] != NULL ) {
?>
               <img src="images/ImagesFor<?php echo $rowContainingBlogCategoryData['blog_category_name'] ?>Updates/<?php echo $rowContainingBlogPostData['blog_post_image_filename'] ?>" alt="Image of <?php echo $rowContainingBlogPostData['blog_post_caption'] ?>" width="auto" height="100px" id="floatedToTheLeftAndHasMarginOnLargeScreens" />
<?php
	      }
?>
               <ul>
                  <li><span id="boldSmallSizedText">Caption:</span> <?php echo $rowContainingBlogPostData['blog_post_caption'] ?></li>
                  <li><span id="boldSmallSizedText">Category:</span> <?php echo $rowContainingBlogCategoryData['blog_category_name'] ?></li>
                  <li><span id="boldSmallSizedText">Posted by:</span> <?php echo ucwords( $rowContainingUserData['firstname'] ) ?></li>
                  <li><span id="boldSmallSizedText">Date of Posting:</span> <?php echo $rowContainingBlogPostData['month_of_posting'] . ' ' . $rowContainingBlogPostData['day_of_posting'] . ', ' . $rowContainingBlogPostData['year_of_posting'] ?></li>
                  <li><span id="boldSmallSizedText">Status:</span> <?php echo ucwords( strtolower( $rowContainingBlogPostData['blog_post_approval_status'] ) ) ?></li>
               </ul>

               <form method="GET" action="perform_administrative_action_on_blog_post.php" id="notFloating">
                  <input type="hidden" name="requiredAction" value="<?php echo $_GET['requiredAction'] ?>" />
                  <input type="hidden" name="idOfBlogPost" value="<?php echo $_GET['idOfBlogPost'] ?>" />
                  <?php echo isset( $_GET['urlOfSourcePage'] ) ? '<input type="hidden" name="urlOfSourcePage" value="' . $_GET['urlOfSourcePage'] . '" />' : '' ?>

                  <input type="submit" name="confirmation" value="Yes" class="btn btn-danger btn-lg" id="tinyMargin" />
                  <input type="submit" name="confirmation" value="No" class="btn btn-danger btn-lg" id="tinyMargin" />
               </form>
            </div>
<?php
      displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
   }
   else if ( $_GET['confirmation'] == 'Yes' ) {  // User clicked "Yes" button

      if ( $_GET['requiredAction'] == 'performAdminApproval' || $_GET['requiredAction'] == 'performAdminUnapproval' ) {
         $urlOfPageWhereActionWillBePerformed = 'approve_or_unapprove_blog_post.php';
      }
      else if ( $_GET['requiredAction'] == 'performAdminDeletion' ) {
         $urlOfPageWhereActionWillBePerformed = 'delete_blog_post.php';
      }

      if ( $_GET['requiredAction'] == 'performAdminApproval' ) {
         header( 'Location: ' . $urlOfPageWhereActionWillBePerformed . '?requiredAction=' . $_GET['requiredAction'] . '&idOfBlogPost=' . $_GET['idOfBlogPost'] );
      }
      else if ( $_GET['requiredAction'] == 'performAdminDeletion' && $rowContainingBlogPostData['user_id_of_poster'] == $_SESSION['user_id'] ) {
         header( 'Location: ' . $urlOfPageWhereActionWillBePerformed . '?requiredAction=' . $_GET['requiredAction'] . '&idOfBlogPost=' . $_GET['idOfBlogPost'] . ( isset( $_GET['urlOfSourcePage'] ) ? '&urlOfSourcePage=' . $_GET['urlOfSourcePage'] : '' ) );
      }
      else {
         displayMarkupsCommonToTopOfPages( $requiredActionInConciseForm . ' Item', DISPLAY_NAVIGATION_MENU, 'perform_administrative_action_on_item.php' );
         $directory = 'images/uploaded' . ucwords( $rowContainingBlogPostData['category'] ) . 'Snapshots';
?>

            <div class="panel panel-primary jumbotron" id="noPaddingOnSmallScreens">
               <h2 class="panel-heading text-center">You requested to <?php echo strtolower( $requiredActionInConciseForm ) ?> the following blog post:</h2>

               <div class="panel-body">
<?php
	      if ( $rowContainingBlogPostData['blog_post_image_filename'] != NULL ) {
?>
                  <img src="images/ImagesFor<?php echo $rowContainingBlogCategoryData['blog_category_name'] ?>Updates/<?php echo $rowContainingBlogPostData['blog_post_image_filename'] ?>" alt="Image of <?php echo $rowContainingBlogPostData['blog_post_caption'] ?>" width="auto" height="100px" id="floatedToTheLeftAndHasMarginOnLargeScreens" />
<?php
	      }
?>
                  <ul>
                     <li id="tinyPadding"><span id="boldSmallSizedText">Caption:</span> <?php echo $rowContainingBlogPostData['blog_post_caption'] ?></li>
                     <li id="tinyPadding"><span id="boldSmallSizedText">Category:</span> <?php echo $rowContainingBlogCategoryData['blog_category_name'] ?></li>
                     <li id="tinyPadding"><span id="boldSmallSizedText">Posted by:</span> <?php echo ucwords( $rowContainingUserData['firstname'] ) ?></li>
                     <li id="tinyPadding"><span id="boldSmallSizedText">Date of Posting:</span> <?php echo $rowContainingBlogPostData['month_of_posting'] . ' ' . $rowContainingBlogPostData['day_of_posting'] . ', ' . $rowContainingBlogPostData['year_of_posting'] ?></li>
                     <li id="tinyPadding"><span id="boldSmallSizedText">Status:</span> <?php echo ucwords( strtolower( $rowContainingBlogPostData['blog_post_approval_status'] ) ) ?></li>
                  </ul>

                  <form method="GET" action="<?php echo $urlOfPageWhereActionWillBePerformed ?>" class="form-vertical" id="notFloating">
                     <input type="hidden" name="requiredAction" value="<?php echo $_GET['requiredAction'] ?>" />
                     <input type="hidden" name="idOfBlogPost" value="<?php echo $_GET['idOfBlogPost'] ?>" />
                     <?php echo isset( $_GET['urlOfSourcePage'] ) ? '<input type="hidden" name="urlOfSourcePage" value="' . $_GET['urlOfSourcePage'] . '" />' : '' ?>

                     <label for="reason" id="mediumSizedText">Why do you want to <?php echo strtolower( $requiredActionInConciseForm ) ?> this blog post?</label>
                     <p class="help-block" id="smallSizedText">It is necessary that you specify your reason so that the poster of this blog post can perform the necessary actions to avoid <?php echo strtolower( $requiredActionInConciseNounForm ) ?> next time.</p>
                     <input type="text" name="reason" class="form-control" id="reason" placeholder="Type your reason here..." required autofocus />
                     <button type="submit" class="btn btn-primary"><?php echo $requiredActionInConciseForm ?> Blog Post</button>
                  </form>
               </div>
            </div>
<?php
         displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
      }

   }
   else if ( $_GET['confirmation'] == 'No' ) { // User Clicked "No" button
      header( 'Location: ' . ( isset( $_GET['urlOfSourcePage'] ) ? $_GET['urlOfSourcePage'] : 'manage_blog_posts.php' . '?type=' . ucwords( strtolower( $rowContainingBlogPostData['blog_post_approval_status'] ) ) ) . '#' . $_GET['idOfBlogPost'] );
   }
}
else if ( isset( $_GET['actionPerformed'] ) ) {
   displayMarkupsCommonToTopOfPages( $requiredActionInConciseForm . ' Blog Post', DISPLAY_NAVIGATION_MENU, 'perform_administrative_action_on_blog_post.php' );
?>
            <div id="containerHoldingSuccessMessage">
               <h2>Successfully <?php echo $requiredActionInConciseForm ?>d Blog Post</h2>
               <p>The required blog post has been successfully <?php echo strtolower( $requiredActionInConciseForm ) ?>d.</p>

<?php
   if ( $_GET['actionPerformed'] == 'adminDeletionPerformedSuccessfully' ) {
      if ( $_GET['idOfPoster'] != $_SESSION['user_id'] ) {
         $query = 'INSERT INTO notifications( notification_text, user_id_of_recipient, notification_time_of_notifying, notification_url, reason_for_notification, id_of_item ) VALUES( "Your ' . ucwords( $_GET['category'] ) . ' Blog Post have been ' . $requiredActionInConciseForm . 'd", ' . $_GET['idOfPoster'] . ', NOW(), "your_blog_posts.php", "DELETION OF BLOG POST", ' . $_GET['idOfBlogPost'] . ' )';
         mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
      }
?>
               <p><a href="<?php echo isset( $_GET['urlOfSourcePage'] ) ? $_GET['urlOfSourcePage'] : 'manage_blog_posts.php?type=' . $_GET['type'] ?>" class="btn btn-default btn-sm">&lt;&lt; Click Here</a> to go back to <?php echo isset( $_GET['urlOfSourcePage'] ) ?  'Your' : 'the ' . $_GET['type'] ?> Blog Posts Page.</p>
<?php
   }
   else {
      $reasonForNotification = $_GET['actionPerformed'] == 'adminApprovalPerformedSuccessfully' ? 'APPROVAL OF BLOG POST' : 'UNAPPROVAL OF BLOG POST';
      $query = 'INSERT INTO notifications( notification_text, user_id_of_recipient, notification_time_of_notifying, notification_url, reason_for_notification, id_of_item ) VALUES( "Your ' . ucwords( $rowContainingBlogCategoryData['blog_category_name'] ) . ' Blog Post have been ' . $requiredActionInConciseForm . 'd", ' . $rowContainingBlogPostData['user_id_of_poster'] . ', NOW(), "your_blog_posts.php#' . $rowContainingBlogPostData['blog_post_id'] . '", "' . $reasonForNotification . '", ' . $rowContainingBlogPostData['blog_post_id'] . ' )';
      mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );

	   if ( $rowContainingBlogPostData['blog_post_image_filename'] != NULL ) {
?>
               <img src="images/ImagesFor<?php echo $rowContainingBlogCategoryData['blog_category_name'] ?>Updates/<?php echo $rowContainingBlogPostData['blog_post_image_filename'] ?>" alt="Image of <?php echo $rowContainingBlogPostData['blog_post_caption'] ?>" width="auto" height="100px" id="floatedToTheLeftAndHasMarginOnLargeScreens" />
<?php
	   }
?>
               <ul>
                  <li><span id="boldSmallSizedText">Caption:</span> <?php echo $rowContainingBlogPostData['blog_post_caption'] ?></li>
                  <li><span id="boldSmallSizedText">Category:</span> <?php echo $rowContainingBlogCategoryData['blog_category_name'] ?></li>
                  <li><span id="boldSmallSizedText">Posted by:</span> <?php echo ucwords( $rowContainingUserData['firstname'] ) ?></li>
                  <li><span id="boldSmallSizedText">Date of Posting:</span> <?php echo $rowContainingBlogPostData['month_of_posting'] . ' ' . $rowContainingBlogPostData['day_of_posting'] . ', ' . $rowContainingBlogPostData['year_of_posting'] ?></li>
                  <li><span id="boldSmallSizedText">Status:</span> <?php echo ucwords( strtolower( $rowContainingBlogPostData['blog_post_approval_status'] ) ) ?></li>
               </ul>

               <p id="notFloating">The blog post can now be found among other <?php echo strtolower( $requiredActionInConciseForm ) ?>d blog posts.</p>
               <p><a href="manage_blog_posts.php?type=<?php echo $requiredActionInConciseForm . 'd#' . $_GET['idOfBlogPost'] ?>" class="btn btn-default btn-sm">Click Here</a> to view the list of <?php echo strtolower( $requiredActionInConciseForm ) ?>d blog posts.</p>
<?php
   }
?>
            </div>
<?php
   displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
}
?>