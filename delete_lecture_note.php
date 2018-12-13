<?php
require_once 'includes/generalHeaderFile.php';

if ( isset( $_GET['idOfLectureNote'] ) ) {
   $_POST['idOfLectureNote'] = $_GET['idOfLectureNote'];
}

if ( !currentUserIsLoggedInAsAdmin() || !isset( $_POST['idOfLectureNote'] ) ) {
   header( 'Location: lecture_notes.php' );
}

require_once 'includes/performBasicInitializations.php';
require_once 'includes/markupFunctions.php';

$nameOfDataToExemptWhenBuildingStringFromGET = 'idOfLectureNote';

if ( isset( $_POST['idOfLectureNote'] ) && !isset( $_POST['confirmation'] ) ) {
   displayMarkupsCommonToTopOfPages( 'Delete Lecture Note', DISPLAY_NAVIGATION_MENU, 'delete_lecture_note.php' );

   $query = 'SELECT lecture_note_file_name, lecture_note_file_extension, lecture_note_number_of_pages FROM lecture_notes WHERE lecture_note_id = ' . $_POST['idOfLectureNote'];
   $result = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
   $row = mysqli_fetch_assoc( $result );
?>

            <form method="POST" action="delete_lecture_note.php?<?php echo buildStringContainingAllDataFromGET( $nameOfDataToExemptWhenBuildingStringFromGET ) ?>" id="containerHoldingErrorMessage">
               <input type="hidden" name="idOfLectureNote" value="<?php echo $_POST['idOfLectureNote'] ?>" />

               <h2>Delete Lecture Note</h2>
               <p>Are you sure you want to delete this lecture from RoarConnect Lecture Note Portal?</p>
               <ul>
                  <li>Lecture note name: <?php echo ucwords( $row['lecture_note_file_name'] ) ?></li>
                  <li>Lecture note file type: <?php echo getBriefDescriptionOfFileType( $row['lecture_note_file_extension'] ) ?></li>
                  <li>Size of lecture note: <?php echo $row['lecture_note_number_of_pages'] . ( $row['lecture_note_file_extension'] == 'ppt' || $row['lecture_note_file_extension'] == 'pptx' ? ' slides' : ' pages' ) ?></li>
               </ul>
               <input type="submit" name="confirmation" value="Yes" class="btn btn-danger btn-lg" id="tinyMargin" />
               <input type="submit" name="confirmation" value="No" class="btn btn-danger btn-lg" id="tinyMargin" />
            </form>
<?php

   displayMarkupsCommonToBottomOfPages( DO_NOT_DISPLAY_FOOTER );
}
else if ( isset( $_POST['idOfLectureNote'] ) && isset( $_POST['confirmation'] ) && $_POST['confirmation'] == 'No' ) {
   header( 'Location: view_lecture_note_information.php?' . buildStringContainingAllDataFromGET( $nameOfDataToExemptWhenBuildingStringFromGET ) );
}
else if ( isset( $_POST['idOfLectureNote'] ) && isset( $_POST['confirmation'] ) && $_POST['confirmation'] == 'Yes' ) {
   $idOfRequiredLectureNote = $_POST['idOfLectureNote'];

   $query = 'SELECT lecture_note_file_name, lecture_note_file_extension FROM lecture_notes WHERE lecture_note_id = ' . $idOfRequiredLectureNote;
   $result = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
   $row = mysqli_fetch_assoc( $result );
   $filePathOfRequiredLectureNote = 'lectureNotes/' . $row['lecture_note_file_name'] . '.' . $row['lecture_note_file_extension'];

   $query = 'SELECT tag_id FROM relationship_between_tags_and_lecture_notes WHERE lecture_note_id = ' . $idOfRequiredLectureNote;
   $resultContainingIdOfRequiredTags = mysqli_query( $db, $query ) or die( 'A'.  $markupIndicatingDatabaseQueryFailure );

   $query = 'DELETE FROM lecture_notes WHERE lecture_note_id = ' . $idOfRequiredLectureNote;
   mysqli_query( $db, $query ) or die( 'B' . $markupIndicatingDatabaseQueryFailure );

   $query = 'DELETE FROM relationship_between_tags_and_lecture_notes WHERE lecture_note_id = ' . $idOfRequiredLectureNote;
   mysqli_query( $db, $query ) or die( 'B' . $markupIndicatingDatabaseQueryFailure );

   $rowContainingIdOfRequiredTag = mysqli_fetch_assoc( $resultContainingIdOfRequiredTags );
   while ( $rowContainingIdOfRequiredTag != NULL ) {
      $query = 'SELECT lecture_note_id FROM relationship_between_tags_and_lecture_notes WHERE tag_id = ' . $rowContainingIdOfRequiredTag['tag_id'];
      $result = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
      $numberOfLectureNotesStillAssociatedWithRequiredTag = mysqli_num_rows( $result );

      if ( $numberOfLectureNotesStillAssociatedWithRequiredTag == 0 ) {
         $query = 'DELETE FROM tags WHERE tag_id = ' . $rowContainingIdOfRequiredTag['tag_id'];
         mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
      }

      $rowContainingIdOfRequiredTag = mysqli_fetch_assoc( $resultContainingIdOfRequiredTags );
   }

   unlink( $filePathOfRequiredLectureNote );

   header( 'Location: view_lecture_note_information.php?' . buildStringContainingAllDataFromGET( $nameOfDataToExemptWhenBuildingStringFromGET ) );
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