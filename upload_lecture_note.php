<?php
require_once 'includes/generalHeaderFile.php';

define( 'MAXIMUM_ALLOWABLE_FILE_SIZE', 5242880000); // 5242880000 Bytes is equal to 5000 Megabytes
define( 'HIGHER_TAG_RELEVANCE', 20 );
define( 'LOWER_TAG_RELEVANCE', 10 );

$errorMessageForFileToUpload = '';
$errorMessageForNumberOfPages = '';
$errorMessageForCodeOfSelectedCourse = '';
$errorMessageForTags = '';

$lectureNoteHasNotYetBeenUploaded = true;

if ( $_POST && currentUserIsLoggedInAsAdmin() ) {
   $targetFileBaseName = basename( $_FILES['fileToUpload']['name'] );
   $targetFilePath = 'lectureNotes/' . $targetFileBaseName;
   $sourceFilePath = $_FILES['fileToUpload']['tmp_name'];
   $fileSize = $_FILES['fileToUpload']['size'];
   $fileExtension = pathinfo( $targetFilePath, PATHINFO_EXTENSION );
   $numberOfPages = trim( htmlentities( $_POST['numberOfPages'] ) );
   $codeOfSelectedCourse = trim( htmlentities( $_POST['codeOfSelectedCourse'] ) );
   $namesOfSpecifiedTags = explode( ',', $_POST['tags'] );

   for ( $index = 0; $index < sizeof( $namesOfSpecifiedTags ); $index++ ) {
      $namesOfSpecifiedTags[$index] = trim( $namesOfSpecifiedTags[$index] );
   }

   $thereIsNoErrorInFormData = true;

   if ( !is_uploaded_file( $sourceFilePath ) ) {
      $errorMessageForFileToUpload = '<p class="col-sm-offset-2 col-sm-10" id="errorMessage">You did not select any file. Please select a file.</p>';
      $thereIsNoErrorInFormData = false;
   }
   else if ( $fileExtension != 'pdf' && $fileExtension != 'doc' && $fileExtension != 'docx' &&
      $fileExtension != 'ppt' && $fileExtension != 'pptx' )
   {
      $errorMessageForFileToUpload = '<p class="col-sm-offset-2 col-sm-10" id="errorMessage">Invalid file type. Valid file types are pdf, doc, docx, ppt, and pptx files</p>';
      $thereIsNoErrorInFormData = false;
   }
   else if ( $fileSize > MAXIMUM_ALLOWABLE_FILE_SIZE ) {
      $errorMessageForFileToUpload = '<p class="col-sm-offset-2 col-sm-10" id="errorMessage">File is too large.</p>';
      $thereIsNoErrorInFormData = false;
   }
   else if ( file_exists( $targetFilePath ) ) {
      $errorMessageForFileToUpload = '<p class="col-sm-offset-2 col-sm-10" id="errorMessage">The file you are trying to upload has already been previously uploaded.</p>';
      $thereIsNoErrorInFormData = false;
   }

   if ( $numberOfPages == '' ) {
      $errorMessageForNumberOfPages = '<p class="col-sm-offset-2 col-sm-10" id="errorMessage">Enter number of pages/slides in the file.</p>';
      $thereIsNoErrorInFormData = false;
   }
   else if ( !consistsOfOnlyDigits( $numberOfPages ) ) {
      $errorMessageForNumberOfPages = '<p class="col-sm-offset-2 col-sm-10" id="errorMessage">Please, enter a vaid number.</p>';
      $thereIsNoErrorInFormData = false;
   }

   if ( $codeOfSelectedCourse == -1 ) {
      $errorMessageForCodeOfSelectedCourse = '<p class="col-sm-offset-2 col-sm-10" id="errorMessage">Please, select course code.</p>';
      $thereIsNoErrorInFormData = false;
   }

   for ( $index = 0; $index < sizeof( $namesOfSpecifiedTags ); $index++ ) {
      if ( !consistsOfOnlyAlphabetsAndDigits( $namesOfSpecifiedTags[$index] ) ) {
         $errorMessageForTags = '<p class="col-sm-offset-2 col-sm-10" id="errorMessage">Each tag must consist only of alphabets and/or digits without any spaces.</p>';
         $thereIsNoErrorInFormData = false;
         break;
      }
   }


   if ( $thereIsNoErrorInFormData ) {
      move_uploaded_file( $sourceFilePath, $targetFilePath ) or die( '<p id="errorMessage">An unexpected error prevented the upload from completing successfully.</p>' );
      $query = 'INSERT INTO lecture_notes ( lecture_note_file_name, lecture_note_file_extension, lecture_note_number_of_pages, course_code )
         VALUES ( "' . removeFileExtension( $targetFileBaseName ) . '", "' . $fileExtension . '", ' . $numberOfPages . ', "' . $codeOfSelectedCourse . '" )';
      mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
      $idOfLectureNoteThatWasJustUploaded = mysqli_insert_id( $globalHandleToDatabase );

      $query = 'SELECT tag_id, tag_name FROM tags WHERE 0';

      for ( $index = 0; $index < sizeof( $namesOfSpecifiedTags ); $index++ ) {
         $query .= ' OR tag_name = "' . $namesOfSpecifiedTags[$index] . '"';
      }

      $namesOfDefaultTags = getArrayContainingNamesOfDefaultTags();

      for ( $index = 0; $index < sizeof( $namesOfDefaultTags ); $index++ ) {
         $query .= ' OR tag_name = "' . $namesOfDefaultTags[$index] . '"';
      }

      $result = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
      $row = mysqli_fetch_assoc( $result );
      while ( $row != NULL ) {
         $nameOfTag = $row['tag_name'];
         $idsOfTagsAlreadyInDatabase[$nameOfTag] = $row['tag_id'];
         $row = mysqli_fetch_assoc( $result );
      }

      $relevancesOfTagsToBeInsertedIntoDatabase = array();

      for ( $index = 0; $index < sizeof( $namesOfSpecifiedTags ); $index++ ) {
         $nameOfTag = strtolower( $namesOfSpecifiedTags[$index] );
         if ( !isset( $idsOfTagsAlreadyInDatabase[$nameOfTag] ) && !isset( $relevancesOfTagsToBeInsertedIntoDatabase[$nameOfTag] ) ) {
            /* The current nameOfTag is not already in database and is not yet among those destined to be inserted 
               into database, so include it among those destined to be inserted to database.
            */
            $relevancesOfTagsToBeInsertedIntoDatabase[$nameOfTag] = LOWER_TAG_RELEVANCE;
         }
      }

      for ( $index = 0; $index < sizeof( $namesOfDefaultTags ); $index++ ) {
         $nameOfTag = strtolower( $namesOfDefaultTags[$index] );
         if ( !isset( $idsOfTagsAlreadyInDatabase[$nameOfTag] ) && !isset( $relevancesOfTagsToBeInsertedIntoDatabase[$nameOfTag] ) ) {
            /* The current nameOfTag is not already in database and is not yet among those destined to be inserted 
               into database, so include it among those destined to be inserted to database.
            */
            $relevancesOfTagsToBeInsertedIntoDatabase[$nameOfTag] = HIGHER_TAG_RELEVANCE;
         }
      }

      foreach ( $relevancesOfTagsToBeInsertedIntoDatabase as $nameOfTag => $relevance ) {
         if ( $nameOfTag != '' ) {
            $query = 'INSERT INTO tags ( tag_name, tag_relevance ) VALUES ( "' . $nameOfTag . '", ' . $relevance . ' )';
            mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
            $idsOfTagsThatWereJustInsertedIntoDatabase[$nameOfTag] = mysqli_insert_id( $globalHandleToDatabase );
         }
      }

      foreach ( $idsOfTagsAlreadyInDatabase as $nameOfTag => $idOfTag ) {
         $query = 'INSERT INTO relationship_between_tags_and_lecture_notes ( tag_id, lecture_note_id )
            VALUES ( ' . $idOfTag . ', ' . $idOfLectureNoteThatWasJustUploaded . ')';
         mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
      }

      foreach ( $idsOfTagsThatWereJustInsertedIntoDatabase as $nameOfTag => $idOfTag ) {
         $query = 'INSERT INTO relationship_between_tags_and_lecture_notes ( tag_id, lecture_note_id )
            VALUES ( ' . $idOfTag . ', ' . $idOfLectureNoteThatWasJustUploaded . ')';
         mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
      }

      $lectureNoteHasNotYetBeenUploaded = false;
   }
}

displayMarkupsCommonToTopOfPages( 'Upload Lecture Note', DISPLAY_NAVIGATION_MENU, 'upload_lecture_note.php' );

if ( !currentUserIsLoggedInAsAdmin() ) {
   session_destroy();
   displayMarkupToIndicateThatAdminLoginIsRequired();
}

if ( $lectureNoteHasNotYetBeenUploaded ) {
?>

            <h2 id="boldMediumSizedText">Upload Lecture Note</h2>

            <form method="POST" action="upload_lecture_note.php" enctype="multipart/form-data" class="form-horizontal" id="looksLikeACardboardPaper">
               <h3 id="mediumSizedText">To upload a lecture note, fill the form below:</h3>

               <div class="form-group">
                  <label for="fileToUpload" class="control-label col-sm-2">Select file to upload:</label>
                  <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAXIMUM_ALLOWABLE_FILE_SIZE ?>" />
                  <div class="col-sm-10"><input type="file" name="fileToUpload" id="fileToUpload"/></div>
                  <p class="help-block col-sm-offset-2 col-sm-10">Ensure that the filename gives a clue about the contents of the lecture note.</p>
                  <?php echo $errorMessageForFileToUpload ?>

               </div>

               <div class="form-group">
                  <label for="numberOfPages" class="control-label col-sm-2">How many pages/slides are in the lecture note?</label>
                  <div class="col-sm-10"><input type="number" name="numberOfPages" <?php echo ( isset( $_POST['numberOfPages'] ) ? 'value="' . $_POST['numberOfPages'] . '"': '' ) ?> class="form-control" id="numberOfPages" /></div>
                  <?php echo $errorMessageForNumberOfPages ?>

               </div>

               <div class="form-group">
                  <label for="selectCourse" class="control-label col-sm-2">What course is contained in the lecture note?</label>
                  <div class="col-sm-10">
                     <select name="codeOfSelectedCourse" class="form-control" id="selectCourse">
                        <option value="-1">---</option>
<?php
$query = 'SELECT course_code FROM courses ORDER BY course_code';
$result = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
$row = mysqli_fetch_assoc( $result );

while ( $row != NULL ) {
   echo '
                        <option value="' . $row['course_code'] . '" ' . ( isset( $_POST['codeOfSelectedCourse'] ) && $_POST['codeOfSelectedCourse'] == $row['course_code'] ? 'selected': '' ) . '>' . $row['course_code'] . '</option>';
   $row = mysqli_fetch_assoc( $result );
}
?>

                     </select>
                  </div>
                  <p class="help-block col-sm-offset-2 col-sm-10">If the appropriate course is not in the list, <a href="add_or_edit_course.php">click here to add the new course</a>. Then reload this page.</p>
                  <?php echo $errorMessageForCodeOfSelectedCourse ?>

               </div>

               <div class="form-group">
                  <label for="tags" class="control-label col-sm-2">(Optional) Enter a comma seperated list of tags:</label>
                  <div class="col-sm-10"><input type="text" name="tags" <?php echo ( isset( $_POST['tags'] ) ? 'value="' . $_POST['tags'] . '"': '' ) ?> class="form-control" id="tags" /></div>
                  <p class="help-block col-sm-offset-2 col-sm-10">The tags will be used to match search queries when a user uses the search bar to search for lecture notes.</p>
                  <?php echo $errorMessageForTags ?>

               </div>

               <div class="form-group">
                  <div class="col-sm-offset-2 col-sm-10"><button type="submit" name="uploadButton" class="btn btn-success">Upload</button></div>
               </div>
            </form>

<?php
}
else {
?>

            <div id="containerHoldingSuccessMessage">
               <h2>Successfully Uploaded New Lecture Note</h2>
               <p>You have successfully uploaded a new lecture note. Below are the details of the lecture note:</p>
               <ul>
                  <li>Name of lecture note: <?php echo $targetFileBaseName ?></li>
                  <li>Size of lecture note: <?php echo $numberOfPages . ( $fileExtension == 'ppt' || $fileExtension == 'pptx' ? ' slides' : ' pages' ) ?></li>
                  <li>Course contained in lecture note: <?php echo $codeOfSelectedCourse ?></li>
               </ul>
               <p>You may like to <a href="upload_lecture_note.php" class="btn btn-default btn-sm">Upload Another Lecture Note</a> or <a href="lecture_notes.php" class="btn btn-default btn-sm">Go to the Lecture Note Portal</a> and view the newly uploaded lecture note.</p>
            </div>

<?php
}

displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );



function removeFileExtension( $fileNameWithExtension )
{
   $fileNameWithoutExtension = ' ';

   for ( $i = 0; $fileNameWithExtension[$i] != '.'; $i++ ) {
      $fileNameWithoutExtension[$i] = $fileNameWithExtension[$i];
   }

   return $fileNameWithoutExtension;
}

function getArrayContainingNamesOfDefaultTags()
{
   global $globalHandleToDatabase, $globalDatabaseErrorMarkup;
   $indexForNamesOfDefaultTags = 0;

   $lectureNoteFileName = basename( $_FILES['fileToUpload']['name'] );
   $lectureNoteFileNameWithoutExtension = removeFileExtension( $lectureNoteFileName );
   $lectureNoteFileExtension = pathinfo( $lectureNoteFileName, PATHINFO_EXTENSION );

   $temporaryArray = explode( ' ', $lectureNoteFileNameWithoutExtension );
   for ( $indexForTemporaryArray = 0; $indexForTemporaryArray < sizeof( $temporaryArray ); $indexForTemporaryArray++ ) {
      $namesOfDefaultTags[$indexForNamesOfDefaultTags++] = trim( $temporaryArray[$indexForTemporaryArray] );
   }

   $namesOfDefaultTags[$indexForNamesOfDefaultTags++] = trim( $lectureNoteFileExtension );

   $query = 'SELECT course_code, course_title, course_year_of_study, department_id FROM courses WHERE course_code = "' . $_POST['codeOfSelectedCourse'] . '"';
   $result = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
   $row = mysqli_fetch_assoc( $result );

   $temporaryArray = explode( ' ', $row['course_code'] );
   $namesOfDefaultTags[$indexForNamesOfDefaultTags++] = trim( $temporaryArray[0] );
   $namesOfDefaultTags[$indexForNamesOfDefaultTags++] = trim( $temporaryArray[1] );
   $namesOfDefaultTags[$indexForNamesOfDefaultTags++] = trim( $temporaryArray[0] ) . trim( $temporaryArray[1] );

   $temporaryArray = explode( ' ', $row['course_title'] );
   for ( $indexForTemporaryArray = 0; $indexForTemporaryArray < sizeof( $temporaryArray ); $indexForTemporaryArray++ ) {
      $namesOfDefaultTags[$indexForNamesOfDefaultTags++] = trim( $temporaryArray[$indexForTemporaryArray] );
   }

   if ( $row['course_year_of_study'] == 1 ) {
      $namesOfDefaultTags[$indexForNamesOfDefaultTags++] = '1st';
      $namesOfDefaultTags[$indexForNamesOfDefaultTags++] = 'first';
   }
   else if ( $row['course_year_of_study'] == 2 ) {
      $namesOfDefaultTags[$indexForNamesOfDefaultTags++] = '2nd';
      $namesOfDefaultTags[$indexForNamesOfDefaultTags++] = 'second';
   }
   else if ( $row['course_year_of_study'] == 3 ) {
      $namesOfDefaultTags[$indexForNamesOfDefaultTags++] = '3rd';
      $namesOfDefaultTags[$indexForNamesOfDefaultTags++] = 'third';
   }
   else if ( $row['course_year_of_study'] == 4 ) {
      $namesOfDefaultTags[$indexForNamesOfDefaultTags++] = '4th';
      $namesOfDefaultTags[$indexForNamesOfDefaultTags++] = 'fourth';
   }
   else if ( $row['course_year_of_study'] == 5 ) {
      $namesOfDefaultTags[$indexForNamesOfDefaultTags++] = '5th';
      $namesOfDefaultTags[$indexForNamesOfDefaultTags++] = 'fifth';
   }
   else if ( $row['course_year_of_study'] == 6 ) {
      $namesOfDefaultTags[$indexForNamesOfDefaultTags++] = '6th';
      $namesOfDefaultTags[$indexForNamesOfDefaultTags++] = 'sixth';
   }

   $yearOfStudy = $row['course_year_of_study'];
   $query = 'SELECT department_name, department_duration_of_programme, faculty_id FROM departments WHERE department_id = ' . $row['department_id'];
   $result = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
   $row = mysqli_fetch_assoc( $result );

   $temporaryArray = explode( ' ', $row['department_name'] );
   for ( $indexForTemporaryArray = 0; $indexForTemporaryArray < sizeof( $temporaryArray ); $indexForTemporaryArray++ ) {
      $namesOfDefaultTags[$indexForNamesOfDefaultTags++] = trim( $temporaryArray[$indexForTemporaryArray] );
   }

   if ( $yearOfStudy == $row['department_duration_of_programme'] ) {
      $namesOfDefaultTags[$indexForNamesOfDefaultTags++] = 'final';
   }

   $query = 'SELECT faculty_name FROM faculties WHERE faculty_id = ' . $row['faculty_id'];
   $result = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
   $row = mysqli_fetch_assoc( $result );

   $temporaryArray = explode( ' ', $row['faculty_name'] );
   for ( $indexForTemporaryArray = 0; $indexForTemporaryArray < sizeof( $temporaryArray ); $indexForTemporaryArray++ ) {
      $namesOfDefaultTags[$indexForNamesOfDefaultTags++] = trim( $temporaryArray[$indexForTemporaryArray] );
   }

   return $namesOfDefaultTags;
}
?>