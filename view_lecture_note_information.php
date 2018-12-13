<?php
if ( !$_GET ) {
   header( 'Location: lecture_notes.php' );
}
else {
   require_once 'includes/generalHeaderFile.php';
   displayMarkupsCommonToTopOfPages( $_GET['departmentName'] . ' Lecture Notes', DISPLAY_NAVIGATION_MENU, 'view_lecture_note_information.php' );
?>
            <header id="minorHeader">
               <h2><?php echo $_GET['departmentName'] . ' ' . getYearOfStudyInWords( $_GET['yearOfStudy'], $_GET['durationOfProgramme'] ) ?> Lecture Notes</h2>
               <p>Find below some <?php echo $_GET['departmentName'] . ' ' . getYearOfStudyInWords( $_GET['yearOfStudy'], $_GET['durationOfProgramme'] ) ?> lecture notes. Download anyone you wish.</p>
               <p><a href="lecture_notes.php#<?php echo $_GET['facultyId'] ?>">&lt;&lt;Go Back to Lecture Note Portal</a></p>
            </header>

<?php
   $query = 'SELECT course_code, course_title FROM courses WHERE department_id = ' . $_GET['departmentId'] . ' AND course_year_of_study = ' . $_GET['yearOfStudy'] . ' ORDER BY course_code';
   $resultContainingCourseData = mysqli_query( $globalHandleToDatabase, $query );
   $rowContainingCourseData = mysqli_fetch_assoc( $resultContainingCourseData );

   while ( $rowContainingCourseData != NULL ) {
?>

            <section id="wideContainerWithBorder">
               <header class="col-sm-2">
                  <h3 id="boldMediumSizedText"><?php echo $rowContainingCourseData['course_code'] ?></h3>
                  <h4 id="tinySizedText"><?php echo $rowContainingCourseData['course_title'] ?></h4>
<?php
      if ( currentUserIsLoggedInAsAdmin() ) {
         $query = 'SELECT department_name, department_duration_of_programme, faculty_id FROM departments WHERE department_id = ' . $_GET['departmentId'];
         $resultContainingDepartmentData = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
         $rowContainingDepartmentData = mysqli_fetch_assoc( $resultContainingDepartmentData );

         $query = 'SELECT faculty_name FROM faculties WHERE faculty_id = ' . $rowContainingDepartmentData['faculty_id'];
         $resultContainingFacultyData = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
         $rowContainingFacultyData = mysqli_fetch_assoc( $resultContainingFacultyData );


         echo '
                  <p>
                     <a href="add_or_edit_course.php?requiredAction=editCourse&formShouldBeDisplayed&courseCode=' . $rowContainingCourseData['course_code'] . '&courseTitle=' . $rowContainingCourseData['course_title'] . '&department=' . $rowContainingDepartmentData['department_name'] . '&durationOfProgramme=' . $rowContainingDepartmentData['department_duration_of_programme'] . '&faculty=' . $rowContainingFacultyData['faculty_name'] . '" class="btn btn-sm btn-default">Edit Course</a>
                     <a href="delete_course.php?courseCode=' . $rowContainingCourseData['course_code'] . buildStringContainingAllDataFromGET() . '" class="btn btn-sm btn-default">Delete Course</a>
                  </p>
         ';
      }
?>
               </header>

               <div class="col-sm-10">
<?php
      $query = 'SELECT lecture_note_id, lecture_note_file_name, lecture_note_file_extension, lecture_note_number_of_pages FROM lecture_notes WHERE course_code = "' . $rowContainingCourseData['course_code'] . '" ORDER BY lecture_note_file_name';
      $resultContainingLectureNoteData = mysqli_query( $globalHandleToDatabase, $query );
      $rowContainingLectureNoteData = mysqli_fetch_assoc( $resultContainingLectureNoteData );

      while ( $rowContainingLectureNoteData != NULL ) {
?>
                  <div id="looksLikeASmallPaperCard">
                     <header id="headerOfPaperCard">
                        <h5><?php echo $rowContainingLectureNoteData['lecture_note_file_name'] ?></h5>
                     </header>

                     <div id="bodyOfPaperCard">
                        <p><?php echo getBriefDescriptionOfFileType( $rowContainingLectureNoteData['lecture_note_file_extension'] ) ?></p>
                        <p><?php echo $rowContainingLectureNoteData['lecture_note_number_of_pages'] . ( $rowContainingLectureNoteData['lecture_note_file_extension'] == 'ppt' || $rowContainingLectureNoteData['lecture_note_file_extension'] == 'pptx' ? ' Slides' : ' Pages' ) ?></p>
<?php
         if ( currentUserIsLoggedIn() ) {
?>
                        <p><a href="lectureNotes/<?php echo $rowContainingLectureNoteData['lecture_note_file_name'] . '.' . $rowContainingLectureNoteData['lecture_note_file_extension'] ?>" target="_blank"><span class="glyphicon glyphicon-download-alt"></span> Download</a></p>
<?php
         }
         else {
            getMarkupForButtonThatWillTellUserToLogInBeforeContinuing( '<span class="glyphicon glyphicon-download-alt"></span> Download' );
         }

         if ( currentUserIsLoggedInAsAdmin() ) {
?>
                        <p><a href="delete_lecture_note.php?idOfLectureNote=<?php echo $rowContainingLectureNoteData['lecture_note_id'] . buildStringContainingAllDataFromGET() ?>" class="btn btn-sm btn-default">Delete Lecture Note</a></p>
<?php
         }
?>
                     </div>
                  </div>

<?php
         $rowContainingLectureNoteData = mysqli_fetch_assoc( $resultContainingLectureNoteData );
      }
?>
               </div>
            </section>
<?php
      $rowContainingCourseData = mysqli_fetch_assoc( $resultContainingCourseData );
   }

   if ( !currentUserIsLoggedIn() ) {
      // The markup that the following function gives is the markup for the modal that the buttons got earlier from 'getMarkupButtonThatWillTellUserToLogInBeforeContinuing' brings up 
      getMarkupForModalThatTellsUserToLogInBeforeContinuing();
   }

   displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
}


function getYearOfStudyInWords( $yearOfStudyInNumericForm, $durationOfProgrammeInNumericForm )
{
   if ( $yearOfStudyInNumericForm < 1 || $yearOfStudyInNumericForm > $durationOfProgrammeInNumericForm ) {
      header( 'Location: lecture_notes.php' );
   }

   if ( $yearOfStudyInNumericForm ==  $durationOfProgrammeInNumericForm ) {
      return 'Final Year';
   }
   else if ( $yearOfStudyInNumericForm == 5 ) {
      return 'Fifth Year';
   }
   else if ( $yearOfStudyInNumericForm == 4 ) {
      return 'Forth Year';
   }
   else if ( $yearOfStudyInNumericForm == 3 ) {
      return 'Third Year';
   }
   else if ( $yearOfStudyInNumericForm == 2 ) {
      return 'Second Year';
   }
   else if ( $yearOfStudyInNumericForm == 1 ) {
      return 'First Year';
   }
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