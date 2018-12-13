<?php
/*
   This file contains definitions of the various utility functions used by the scripts of RoarConnect
*/

require_once 'performBasicInitializations.php';


function currentUserIsLoggedIn()
{
   return isset($_SESSION['loginStatus']) && $_SESSION['loginStatus'] == 'loggedin';
}


function currentUserIsLoggedInAsAdmin()
{
   return currentUserIsLoggedIn() && isset($_SESSION['loginPrivileges']) && $_SESSION['loginPrivileges'] == 'admin';
}


function getFirstNameOfCurrentUser()
{
   global $db, $markupIndicatingDatabaseQueryFailure;
	$query = "SELECT `firstname` FROM `users` WHERE `id`= '".$_SESSION['user_id']."'";
   $query_run = mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure);
   $query_result = mysqli_fetch_array($query_run);
   return $query_result == NULL ? '' : $query_result['firstname'];
}


function getFirstNameOfUser($userId)
{
   global $db, $markupIndicatingDatabaseQueryFailure;
   $query = 'SELECT firstname FROM users WHERE id = "' . $userId . '"';
   $result = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
   $row = mysqli_fetch_assoc( $result );
   return $row == NULL ? '' : $row['firstname'];
}


function getatt()
{
   global $db;

	$query="SELECT `attribute` FROM `users` WHERE `id`= '".$_SESSION['user_id']."'";

	if($query_run = mysqli_query($db, $query)) {
		$query_result=mysqli_fetch_array($query_run);
		return ucwords($query_result['attribute']);
	}
   else {
		return 'error';
	}
}


function buildStringContainingAllDataFromGET( $nameOfDataToExempt1 = '', $nameOfDataToExempt2 = '', $nameOfDataToExempt3 =  '' )
{
   $stringContainingAllDataFromGET = '';

   foreach ( $_GET as $nameOfData => $valueOfData ) {
      if ( $nameOfData != $nameOfDataToExempt1 && $nameOfData != $nameOfDataToExempt2 && $nameOfData != $nameOfDataToExempt3 ) {
         $stringContainingAllDataFromGET .= '&' . $nameOfData . '=' . $valueOfData;
      }
   }

   return $stringContainingAllDataFromGET;
}


function isAlpha( $char )
{
   $char = strtolower( $char );

   return $char == 'a' || $char == 'b' || $char == 'c' || $char == 'd' || $char == 'e' || $char == 'f'
      || $char == 'g' || $char == 'h' || $char == 'i' || $char == 'j' || $char == 'k' || $char == 'l'
      || $char == 'm' || $char == 'n' || $char == 'o' || $char == 'p' || $char == 'q' || $char == 'r'
      || $char == 's' || $char == 't' || $char == 'u' || $char == 'v' || $char == 'w' || $char == 'x'
      || $char == 'y' || $char == 'z';
}


function isDigit( $char )
{
   return $char == '0' || $char == '1' || $char == '2' || $char == '3' || $char == '4' || $char == '5'
      || $char == '6' || $char == '7' || $char == '8' || $char == '9';
}


function consistsOfOnlyAlphabets( $string )
{
   for ( $index = 0; $index < strlen( $string ); $index++ ) {
      if ( !isAlpha( $string[$index] ) ) {
         break;
      }
   }

   return $index == strlen( $string );
}


function consistsOfOnlyDigits( $string )
{
   for ( $index = 0; $index < strlen( $string ); $index++ ) {
      if ( !isDigit( $string[$index] ) ) {
         break;
      }
   }

   return $index == strlen( $string );

}


function consistsOfOnlyAlphabetsAndSpaces( $string )
{
   for ( $index = 0; $index < strlen( $string ); $index++ ) {
      if ( !isAlpha( $string[$index] ) && $string[$index] != ' ' ) {
         break;
      }
   }

   return $index == strlen( $string );
}


function consistsOfOnlyAlphabetsAndDigits( $string )
{
   for ( $index = 0; $index < strlen( $string ); $index++ ) {
      if ( !isAlpha( $string[$index] ) && !isDigit( $string[$index] ) ) {
         break;
      }
   }

   return $index == strlen( $string );
}


function consistsOfOnlyAlphabetsAndDigitsAndSpaces( $string )
{
   for ( $index = 0; $index < strlen( $string ); $index++ ) {
      if ( !isAlpha( $string[$index] ) && !isDigit( $string[$index] ) && $string[$index] != ' ' ) {
         break;
      }
   }

   return $index == strlen( $string );
}


function isMainBloggerForThisCategory( $idOfUser, $idOfBlogCategory )
{
	global $db;
	
	$query = 'SELECT * FROM blog_categories WHERE user_id_of_main_blogger = ' . $idOfUser . ' AND blog_category_id = ' . $idOfBlogCategory;
	$result = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
	$row = mysqli_fetch_assoc( $result );
	
	return $row != NULL;
}


function formatTimeAsAmOrPm( $hour, $minute ) {
   if ( $hour >= 0 && $hour <= 11 ) {
      return $hour . ':' . addLeadingZeroIfNecessary( $minute ) . ' AM';
   }
   else if ( $hour == 12 ) {
      if ( $minute == 0 ) {
         return $hour . ':' . addLeadingZeroIfNecessary( $minute ) . ' NOON';
      }
      else {
         return $hour . ':' . addLeadingZeroIfNecessary( $minute ) . ' PM';
      }
   }
   else { // hour is greater than 12
      return ( $hour % 12 ) . ':' . addLeadingZeroIfNecessary( $minute ) . ' PM';
   }
}


function addLeadingZeroIfNecessary( $minute )
{
	if ( $minute >= 0 && $minute <= 9 ) {
		return '0' . $minute;
	}
	else {
		return $minute;
	}
}


function separateAllLinesOfTextWithParagraphTags( $text )
{
   $textContainingParagraphTags = "";
   $buffer = "";
   
   for ( $i = 0; $i < strlen( $text ); $i++ ) {
      if ( $text[$i] != "\n" ) {
         $buffer .= $text[$i];
      }
      else if ( $buffer != "" ) {
         $textContainingParagraphTags .= "<p>" .  $buffer . "</p>";
         $buffer = "";
      }
   }
   
   if ( $buffer != "" ) {
      $textContainingParagraphTags .= "<p>" .  $buffer . "</p>";
   }
   
   return $textContainingParagraphTags;
}


function getTextFromFirstParagraph($htmlText, $maximumNumberOfCharactersToGet = 200)
{
   $start = strpos($htmlText, '<p>');

   if ($start === FALSE) {
      return '';
   }

   $stop = strpos($htmlText, '</p>', $start);

   if ($stop === FALSE) {
      $stop = strlen($htmlText);
   }

   $word = '';
   $textFromFirstParagraph = '';
   for ($i = $start + 3, $count = 0; $i < $stop && $count < $maximumNumberOfCharactersToGet; $i++, $count++ ) {
      if (isAlpha($htmlText[$i]) || isDigit($htmlText[$i]) ) {
         $word .= $htmlText[$i];
      }
      else {
         $textFromFirstParagraph .= $word . $htmlText[$i];
         $word = '';
      }
   }

   return $textFromFirstParagraph;
}


function getDataAboutApprovedBlogPost($blogPostId)
{
   global $db;
   $query = 'SELECT *, MONTHNAME( blog_post_time_of_posting ) AS month_of_posting, DAYOFMONTH( blog_post_time_of_posting ) AS day_of_posting, YEAR( blog_post_time_of_posting ) AS year_of_posting FROM blog_posts WHERE blog_post_id = "' . $blogPostId . '" AND blog_post_approval_status = "APPROVED"';
   $result = mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure);
   return mysqli_fetch_assoc($result);
}


function blogPostHasNeverBeenViewedByCurrentUser($blogPostId)
{
   global $db;
   $query = 'SELECT * FROM views_to_blog_posts WHERE user_id_of_viewer = "' . $_SESSION['user_id'] . '" AND blog_post_id = "' . $blogPostId . '"';
   $result = mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure);
   return mysqli_num_rows($result) == 0;
}


function blogPostHasPreviouslyBeenViewedByCurrentUser($blogPostId)
{
   return !blogPostHasNeverBeenViewedByCurrentUser($blogPostId);
}


function blogPostIsNotInArray($blogPost, $arrayOfBlogPosts)
{
   foreach ($arrayOfBlogPosts as $currentBlogPost) {
      if ($blogPost['blog_post_id'] == $currentBlogPost['blog_post_id']) {
         return false;
      }
   }

   return true;
}


function getArrayOfDataAboutBlogPostsThatCurrentUserCouldBeInterestedIn($minimumNumberOfRows = 10)
{
   $idOfLastBlogPost = 0;
   $dataAboutBlogPosts = array();
   $result = getResultContainingBlogPostsThatCurrentUserCouldBeInterestedIn($minimumNumberOfRows);

   for ($row = mysqli_fetch_assoc($result); $row != NULL; $row = mysqli_fetch_assoc($result)) {
      if ($idOfLastBlogPost != $row['blog_post_id']) {
         $row['blog_post_relevance'] += 120 * getNumberOfBlogCategoryViewsByCurrentUser($row['blog_category_id']);
         $dataAboutBlogPosts[] = $row;
         $idOfLastBlogPost = $row['blog_post_id'];
      }
   }

   $dataAboutBlogPosts = sortBlogPostsAccordingToDecreasingRelevance($dataAboutBlogPosts);
   return $dataAboutBlogPosts;
}


function getResultContainingBlogPostsThatCurrentUserCouldBeInterestedIn($minimumNumberOfRows)
{
   global $db, $markupIndicatingDatabaseQueryFailure;

   $idsOfRecentlyViewedBlogPosts = getArrayOfIdsOfBlogPostsThatWereRecentlyViewedByCurrentUser();
   $tagIdsOfRecentlyViewedBlogPosts = getArrayOfTagIdsOfBlogPosts($idsOfRecentlyViewedBlogPosts);

   $query = 'SELECT blog_posts.blog_post_id, blog_posts.blog_post_caption, blog_posts.blog_post_image_filename, blog_posts.blog_category_id, blog_posts.blog_post_relevance 
      FROM blog_posts INNER JOIN relationship_between_tags_and_blog_posts ON blog_posts.blog_post_id = relationship_between_tags_and_blog_posts.blog_post_id
      WHERE FALSE';

   for ($i = 0; $i < count($tagIdsOfRecentlyViewedBlogPosts); $i++) {
      $query .= ' OR relationship_between_tags_and_blog_posts.blog_tag_id = ' . $tagIdsOfRecentlyViewedBlogPosts[$i];
   }

   $query .= ' ORDER BY blog_posts.blog_post_relevance DESC, blog_post_id LIMIT ' . ($minimumNumberOfRows * count($tagIdsOfRecentlyViewedBlogPosts));
   $result = mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure);
   return $result;
}


function getArrayOfIdsOfBlogPostsThatWereRecentlyViewedByCurrentUser()
{
   global $db, $markupIndicatingDatabaseQueryFailure;

   $idsOfBlogPosts = array();
   $query = 'SELECT blog_post_id FROM views_to_blog_posts WHERE user_id_of_viewer = ' . $_SESSION['user_id'] . ' ORDER BY time_of_viewing DESC LIMIT 10';

   $result = mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure);

   for ($row = mysqli_fetch_assoc($result); $row != NULL; $row = mysqli_fetch_assoc($result)) {
      $idsOfBlogPosts[] = $row['blog_post_id'];
   }

   return $idsOfBlogPosts;
}


function getArrayOfTagIdsOfBlogPosts($arrayOfBlogPostIds)
{
   global $db, $markupIndicatingDatabaseQueryFailure;
   $tagIds = array();
   $query = 'SELECT blog_tag_id FROM relationship_between_tags_and_blog_posts WHERE FALSE';

   for ($index = 0; $index < count($arrayOfBlogPostIds); $index++) {
      $query .= ' OR blog_post_id = ' . $arrayOfBlogPostIds[$index];
   }

   $query .= ' ORDER BY blog_tag_id';
   $result = mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure);

   for ($index = 0, $row = mysqli_fetch_assoc($result); $row != NULL; $row = mysqli_fetch_assoc($result)) {
      if ($index == 0 || $tagIds[$index - 1] != $row['blog_tag_id']) {
         $tagIds[] = $row['blog_tag_id'];
         $index++;
      }
   }

   return $tagIds;
}


function getNumberOfBlogCategoryViewsByCurrentUser($blogCategoryId)
{
   global $db, $markupIndicatingDatabaseQueryFailure;
   $query = 'SELECT number_of_blog_posts_viewed FROM views_to_blog_categories WHERE blog_category_id = ' . $blogCategoryId . ' AND user_id_of_viewer = ' . $_SESSION['user_id'];
   $result = mysqli_query($db, $query);
   $row = mysqli_fetch_assoc($result);
   return $row == NULL ? 0 : $row['number_of_blog_posts_viewed'];
}


function sortBlogPostsAccordingToDecreasingRelevance($arrayOfDataAboutBlogPosts)
{
   for ($pass = 1; $pass < count($arrayOfDataAboutBlogPosts); $pass++) {
      for ($i = 0; $i < count($arrayOfDataAboutBlogPosts) - $pass; $i++) {
         if ($arrayOfDataAboutBlogPosts[$i]['blog_post_relevance'] < $arrayOfDataAboutBlogPosts[$i + 1]['blog_post_relevance']) {
            $temp = $arrayOfDataAboutBlogPosts[$i];
            $arrayOfDataAboutBlogPosts[$i] = $arrayOfDataAboutBlogPosts[$i + 1];
            $arrayOfDataAboutBlogPosts[$i + 1] = $temp;
         }
      }
   }

   return $arrayOfDataAboutBlogPosts;
}


function displayLatestStuffs()
{
   global $db;

   /*
      Retrieve and display a few latest blog post updates
   */
   if ( currentUserIsLoggedIn() ) {
      $attributeOfUser = getatt();
      
      if ( $attributeOfUser == 'ASPIRANT' ) {
         $queryContainingBlogCategoriesThatShouldNotBeDisplayed = 'SELECT blog_category_id FROM blog_categories WHERE blog_category_name = "Fresher Gists" OR blog_category_name = "Old Student Gists"';
      }
      else if ( $attributeOfUser == 'FRESHER' ) {
         $queryContainingBlogCategoriesThatShouldNotBeDisplayed = 'SELECT blog_category_id FROM blog_categories WHERE blog_category_name = "Aspirant Gists" OR blog_category_name = "Old Student Gists"';
      }
      else if ( $attributeOfUser == 'OLD STUDENT' ) {
         $queryContainingBlogCategoriesThatShouldNotBeDisplayed = 'SELECT blog_category_id FROM blog_categories WHERE blog_category_name = "Aspirant Gists" OR blog_category_name = "Fresher Gists"';
      }
   }
   else { // user is not logged in
      $queryContainingBlogCategoriesThatShouldNotBeDisplayed = 'SELECT blog_category_id FROM blog_categories WHERE blog_category_name = "Aspirant Gists" OR blog_category_name = "Fresher Gists" OR blog_category_name = "Old Student Gists"';
   }
   
   $resultContainingBlogCategoriesThatShouldNotBeDisplayed = mysqli_query( $db, $queryContainingBlogCategoriesThatShouldNotBeDisplayed ) or die( $markupIndicatingDatabaseQueryFailure );
   $rowContainingBlogCategoriesThatShouldNotBeDisplayed = mysqli_fetch_assoc( $resultContainingBlogCategoriesThatShouldNotBeDisplayed );

   $query = 'SELECT blog_post_id, blog_post_caption, blog_post_image_filename, blog_category_id FROM blog_posts WHERE blog_post_approval_status = "APPROVED"';
   
   while ( $rowContainingBlogCategoriesThatShouldNotBeDisplayed != NULL ) {
      $query .= ' AND blog_category_id != ' . $rowContainingBlogCategoriesThatShouldNotBeDisplayed['blog_category_id'];
      $rowContainingBlogCategoriesThatShouldNotBeDisplayed = mysqli_fetch_assoc( $resultContainingBlogCategoriesThatShouldNotBeDisplayed );
   }
   
   $query .= ' ORDER BY blog_post_time_of_posting DESC LIMIT 4';
   
   $resultContainingLatestBlogPosts = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );

   if ( mysqli_num_rows( $resultContainingLatestBlogPosts ) > 0 ) {
?>
               <section>
                  <div id="ashColouredContainerWithoutPadding">
                     <header id="minorHeaderType2">
                        <h1>Latest Updates</h1>
                     </header>
                  </div>
                  
<?php
      $rowContainingLatestBlogPost = mysqli_fetch_assoc( $resultContainingLatestBlogPosts );
      
      while ( $rowContainingLatestBlogPost != NULL ) {
         $query = 'SELECT blog_category_name FROM blog_categories WHERE blog_category_id = ' . $rowContainingLatestBlogPost['blog_category_id'];
         $resultContainingDataAboutBlogCategory = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
         $rowContainingDataAboutBlogCategory = mysqli_fetch_assoc( $resultContainingDataAboutBlogCategory );
?>
                  <a href="blog.php?category=<?php echo $rowContainingDataAboutBlogCategory['blog_category_name'] ?>&i=<?php echo $rowContainingLatestBlogPost['blog_post_id'] ?>" id="blogHeadlineContainer">
<?php
	if ( $rowContainingLatestBlogPost['blog_post_image_filename'] != NULL ) {
?>
                     <img src="images/ImagesFor<?php echo $rowContainingDataAboutBlogCategory['blog_category_name'] ?>Updates/<?php echo $rowContainingLatestBlogPost['blog_post_image_filename'] ?>" alt="Image of <?php echo $rowContainingLatestBlogPost['blog_post_caption'] ?>" id="<?php echo $counter % 2 == 0 ? 'blogHeadlineImageEven' : 'blogHeadlineImageOdd' ?>" />
<?php
	}
?>
                     <h2 id="blogHeadlineText"><?php echo $rowContainingLatestBlogPost['blog_post_caption'] ?></h2>
                  </a>
                  
<?php
         $rowContainingLatestBlogPost = mysqli_fetch_assoc( $resultContainingLatestBlogPosts );
      }
?>
               </section>
               
               <p id="notFloating"></p>
               
<?php
   }
   
   /*
      Retrieve and display a few lecture notes
   */
   $query = 'SELECT lecture_note_id FROM lecture_notes ORDER BY lecture_note_id DESC LIMIT 1';
   $resultContainingLectureNoteWithHighestId = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
   $rowContainingLectureNoteWithHighestId = mysqli_fetch_assoc( $resultContainingLectureNoteWithHighestId );
   $highestLectureNoteId = $rowContainingLectureNoteWithHighestId['lecture_note_id'];
   
   $query = 'SELECT lecture_note_file_name, course_code FROM lecture_notes WHERE lecture_note_id = ' . rand( 1, $highestLectureNoteId ) . ' OR lecture_note_id = ' . rand( 1, $highestLectureNoteId ) . ' OR lecture_note_id = ' . rand( 1, $highestLectureNoteId ) . ' OR lecture_note_id = ' . rand( 1, $highestLectureNoteId );
   $resultContainingLectureNotes = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
   
   if ( mysqli_num_rows( $resultContainingLectureNotes ) > 0 ) {
?>
               <section>
                  <div id="ashColouredContainerWithoutPadding">
                     <header id="minorHeaderType2">
                        <h1>Popular Lecture Notes</h1>
                     </header>
                  </div>
                  
<?php
      $rowContainingLectureNote = mysqli_fetch_assoc( $resultContainingLectureNotes );
      
      while ( $rowContainingLectureNote != NULL ) {
         $query = 'SELECT course_year_of_study, department_id FROM courses WHERE course_code = "' . $rowContainingLectureNote['course_code'] . '"';
         $resultContainingDataAboutAssociatedCourse = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
         $rowContainingDataAboutAssociatedCourse = mysqli_fetch_assoc( $resultContainingDataAboutAssociatedCourse );
         
         $query = 'SELECT * FROM departments WHERE department_id = ' . $rowContainingDataAboutAssociatedCourse['department_id'];
         $resultContainingDataAboutAssociatedDepartment = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
         $rowContainingDataAboutAssociatedDepartment = mysqli_fetch_assoc( $resultContainingDataAboutAssociatedDepartment );
?>
                  <a href="view_lecture_note_information.php?departmentId=<?php echo $rowContainingDataAboutAssociatedDepartment['department_id'] ?>&departmentName=<?php echo $rowContainingDataAboutAssociatedDepartment['department_name'] ?>&durationOfProgramme=<?php echo $rowContainingDataAboutAssociatedDepartment['department_duration_of_programme'] ?>&facultyId=<?php echo $rowContainingDataAboutAssociatedDepartment['faculty_id'] ?>&yearOfStudy=<?php echo $rowContainingDataAboutAssociatedCourse['course_year_of_study'] ?>" id="looksLikeASmallPaperCard">
                     <header id="headerOfPaperCard">
                        <h2><?php echo $rowContainingLectureNote['lecture_note_file_name'] ?></h2>
                     </header>
                     
                     <div id="bodyOfPaperCard">
                        <p>Contains notes on <?php echo $rowContainingLectureNote['course_code'] ?></p>
                        <p>(<?php echo $rowContainingDataAboutAssociatedDepartment['department_name'] ?> department)</p>
                     </div>
                  </a>
                  
<?php
         $rowContainingLectureNote = mysqli_fetch_assoc( $resultContainingLectureNotes );
      }
?>
               </section>
                              
<?php
   }

   /*
      Retrieve and display a few latest photo uploads
   */
   $query = 'SELECT * FROM photo_upload WHERE checks = "APPROVED" AND people_id NOT LIKE "VENDOR_%" ORDER BY id_new DESC LIMIT 4';
   $resultContainingLatestPhotoUploads = mysqli_query( $db, $query );
   
   if ( mysqli_num_rows( $resultContainingLatestPhotoUploads ) > 0 ) {
?>
               <section>
                  <div id="ashColouredContainerWithoutPadding">
                     <header id="minorHeaderType2">
                        <h1>Latest Items for Sale</h1>
                     </header>
                  </div>
<?php
      $rowContainingLatestPhotoUpload = mysqli_fetch_assoc( $resultContainingLatestPhotoUploads );
      while ( $rowContainingLatestPhotoUpload != NULL ) {
?>
               <a href="view_all_items.php?category=<?php echo ucwords( $rowContainingLatestPhotoUpload['category'] ) ?>" id="looksLikeASmallPaperCard">
                  <div id="headerOfPaperCard">
                     <img src ="images/uploaded<?php echo ucwords( $rowContainingLatestPhotoUpload['category'] ) ?>Snapshots/<?php echo $rowContainingLatestPhotoUpload['people_id'] . '@' . $rowContainingLatestPhotoUpload['image_size'] ?>" alt="Snapshot of <?php echo $rowContainingLatestPhotoUpload['name_of_item'] ?>" />
                     <h4><?php echo $rowContainingLatestPhotoUpload['name_of_item'] ?></h4>
                  </div>
                  <div id="bodyOfPaperCard">
                     <p><span id="boldSmallSizedText">Description:</span> <?php echo $rowContainingLatestPhotoUpload['brief_descripition'] ?></p>
                     <p><span id="boldSmallSizedText">Price:</span> <?php echo $rowContainingLatestPhotoUpload['price'] . ' (' . ( $rowContainingLatestPhotoUpload['negotiable'] == 'YES' ? 'negotiable' : 'non-negotiable' ) . ')' ?></p>
                  </div>
               </a>
<?php
         $rowContainingLatestPhotoUpload = mysqli_fetch_assoc( $resultContainingLatestPhotoUploads );
      }
?>
               </section>
               
<?php
   }
}
?>