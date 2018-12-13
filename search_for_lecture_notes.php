<?php

if ( !isset( $_GET['searchQuery'] ) ) {
   header( 'Location: lecture_notes.php' );
}
else {
   require_once 'includes/generalHeaderFile.php';

   $searchQuery = $_GET['searchQuery'];
   $delimiters = ' ~!@#$%^&*()_+{}|:"<>?`-=[]\;\',./';

   $index = 0;
   $tokensFromSearchQuery[$index] = strtolower( strtok( $searchQuery, $delimiters ) );
   while ( $tokensFromSearchQuery[$index] != false ) {
      $index++;
      $tokensFromSearchQuery[$index] = strtolower( strtok( $delimiters ) );
   }

   $lectureNoteRelevances = array();

   for ( $index = 0; $index < sizeof( $tokensFromSearchQuery ); $index++ ) {
      $query = 'SELECT tag_id, tag_relevance FROM tags WHERE tag_name = "' . $tokensFromSearchQuery[$index] . '"';
      $resultContainingTagData = mysqli_query( $db, $query );
      $rowContainingTagData = mysqli_fetch_assoc( $resultContainingTagData );

      $query = 'SELECT lecture_note_id FROM relationship_between_tags_and_lecture_notes WHERE tag_id = "' . $rowContainingTagData['tag_id'] . '"';
      $resultContainingRelationshipData = mysqli_query( $db, $query ) or die($markupIndicatingDatabaseQueryFailure);
      $rowContainingRelationshipData = mysqli_fetch_assoc( $resultContainingRelationshipData );

      while ( $rowContainingRelationshipData != NULL ) {
         $idOfLectureNote = $rowContainingRelationshipData['lecture_note_id'];
         settype( $idOfLectureNote, 'string' );

         if ( !isset( $lectureNoteRelevances[$idOfLectureNote] ) ) {
            $lectureNoteRelevances[$idOfLectureNote] = $rowContainingTagData['tag_relevance'];
         }
         else {
            $lectureNoteRelevances[$idOfLectureNote] += $rowContainingTagData['tag_relevance'];
         }

         $rowContainingRelationshipData = mysqli_fetch_assoc( $resultContainingRelationshipData );
      }
   }

   displayMarkupsCommonToTopOfPages( 'Lecture Note Search Results', DISPLAY_NAVIGATION_MENU, 'search_for_lecture_notes.php' );
   displayMarkupForSearchBar('search_for_lecture_notes.php', 'Search lecture notes');
?>
            <header id="minorHeader">
               <h2>Search Results</h2>
            </header>

            <p><a href="lecture_notes.php">&lt;&lt; Go Back to Lecture Note Portal</a></p>
<?php
   if ( sizeof( $lectureNoteRelevances ) == 0 ) {
?>
            <p>No results found.</p>
<?php
   }
   else {
      arsort( $lectureNoteRelevances );

      foreach ( $lectureNoteRelevances as $lectureNoteId => $relevance ) {
         $query = 'SELECT lecture_note_id, lecture_note_file_name, lecture_note_file_extension, lecture_note_number_of_pages, course_code FROM lecture_notes
            WHERE lecture_note_id = ' . $lectureNoteId;
         $resultContainingLectureNoteData = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
         $rowContainingLectureNoteData = mysqli_fetch_assoc( $resultContainingLectureNoteData );

         $query = 'SELECT course_code, course_title, department_id FROM courses WHERE course_code = "' . $rowContainingLectureNoteData['course_code'] . '"';
         $resultContainingCourseData = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
         $rowContainingCourseData = mysqli_fetch_assoc( $resultContainingCourseData );

         $query = 'SELECT department_name FROM departments WHERE department_id = ' . $rowContainingCourseData['department_id'];
         $resultContainingDepartmentData = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
         $rowContainingDepartmentData = mysqli_fetch_assoc( $resultContainingDepartmentData );
?>

            <div id="containerWithBorderAndWithoutRoundedCorners">
<?php
         if ( currentUserIsLoggedIn() ) {
?>
               <h3 id="noMargin"><a href="lectureNotes/<?php echo $rowContainingLectureNoteData['lecture_note_file_name'] . '.' . $rowContainingLectureNoteData['lecture_note_file_extension'] ?>" target="_blank"><?php echo ucwords( $rowContainingLectureNoteData['lecture_note_file_name'] ) ?></a></h3>
<?php
         }
         else {
            getMarkupForButtonThatWillTellUserToLogInBeforeContinuing( ucwords( $rowContainingLectureNoteData['lecture_note_file_name'] ) );
         }
?>

               <div>
                  <p id="noMargin"><?php echo $rowContainingLectureNoteData['lecture_note_number_of_pages'] . ( $rowContainingLectureNoteData['lecture_note_file_extension'] == 'ppt' || $rowContainingLectureNoteData['lecture_note_file_extension'] == 'pptx' ? ' Slides' : ' Pages' ) ?></p>
                  <p id="noMargin"><?php echo getBriefDescriptionOfFileType( $rowContainingLectureNoteData['lecture_note_file_extension'] ) ?></p>
                  <p id="noMargin">Contains notes on <?php echo  $rowContainingCourseData['course_code'] . ' (' . $rowContainingCourseData['course_title'] . ')' ?></p>
                  <p id="noMargin">Offered in <?php echo $rowContainingDepartmentData['department_name'] ?> department</p>
<?php
         if ( currentUserIsLoggedIn() ) {
?>
                  <p id="noMargin"><a href="lectureNotes/<?php echo $rowContainingLectureNoteData['lecture_note_file_name'] . '.' . $rowContainingLectureNoteData['lecture_note_file_extension'] ?>" target="_blank"><span class="glyphicon glyphicon-download-alt"></span> Download</a></p>
<?php
         }
         else {
            getMarkupForButtonThatWillTellUserToLogInBeforeContinuing( '<span class="glyphicon glyphicon-download-alt"></span> Download' );
         }
?>
<?php
         if ( currentUserIsLoggedInAsAdmin() ) {
?>
                  <p id="noMargin"><a href="delete_lecture_note.php?idOfLectureNote=<?php echo $rowContainingLectureNoteData['lecture_note_id'] . buildStringContainingAllDataFromGET() ?>" class="btn btn-sm btn-default">Delete Lecture Note</a></p>
<?php
         }
?>
               </div>
            </div>
<?php
      }
   }

   if ( !currentUserIsLoggedIn() ) {
      // The markup that the following function gives is the markup for the modal that the buttons got earlier from 'getMarkupButtonThatWillTellUserToLogInBeforeContinuing' brings up 
      getMarkupForModalThatTellsUserToLogInBeforeContinuing();
   }


   displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
}


function getBriefDescriptionOfFileType( $fileExtension )
{
   if ( $fileExtension == 'pdf' ) {
      return 'PDF Document';
   }
   else if ( $fileExtension == 'doc' || $fileExtension == 'docx' ) {
      return 'Microsoft Word Document';
   }
   else if ( $fileExtension == 'ppt' || $fileExtension == 'pptx' ) {
      return 'Microsoft PowerPoint Presentation';
   }
   else {
      return $fileExtension;
   }
}
?>