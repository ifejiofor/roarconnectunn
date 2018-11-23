<?php
require_once 'includes/generalHeaderFile.php';

$requiredAction = isset( $_GET['requiredAction'] ) && $_GET['requiredAction'] == 'editCourse' ? 'editCourse' : 'addNewCourse';

displayMarkupsCommonToTopOfPages( $requiredAction == 'editCourse' ? 'Edit Course' : 'Add New Course', DISPLAY_NAVIGATION_MENU, 'add_or_edit_course.php' );


if ( !userIsLoggedInAsAdmin() ) {
   session_destroy();
   displayMarkupToIndicateThatAdminLoginIsRequired();
}

$errorMessageForCourseCode = '';
$errorMessageForCourseTitle = '';
$errorMessageForDepartment = '';
$errorMessageForDurationOfProgramme = '';
$errorMessageForFaculty = '';

$formShouldBeDisplayed = !$_GET || isset( $_GET['formShouldBeDisplayed'] );
$formShouldNotBeDisplayed = !$formShouldBeDisplayed;
$thereIsNoErrorInFormData = true;

if ( $_GET && userIsLoggedInAsAdmin() ) {
   $courseCode = trim( htmlentities( $_GET['courseCode'] ) );
   $courseTitle = trim( htmlentities( $_GET['courseTitle'] ) );
   $department = trim( htmlentities( $_GET['department'] ) );
   $durationOfProgramme = trim( htmlentities( $_GET['durationOfProgramme'] ) );
   $faculty = trim( htmlentities( $_GET['faculty'] ) );

   if ( empty( $courseCode ) ) {
      $errorMessageForCourseCode = '<p class="col-sm-offset-2 col-sm-10" id="errorMessage">Please enter course code.</p>';
      $thereIsNoErrorInFormData = false;
   }
   else if ( strlen( $courseCode ) < 6 || strlen( $courseCode ) > 7 ) {
      $errorMessageForCourseCode = '<p class="col-sm-offset-2 col-sm-10" id="errorMessage">Invalid course code. A valid course code consists of three alphabets, followed by a space and three digits.</p>';
      $thereIsNoErrorInFormData = false;
   }
   else if ( !isAlpha( $courseCode[0] ) || !isAlpha( $courseCode[1] ) || !isAlpha(  $courseCode[2] ) ) {
      $errorMessageForCourseCode = '<p class="col-sm-offset-2 col-sm-10" id="errorMessage">Invalid course code. A valid course code consists of three alphabets, followed by a space and three digits.</p>';
      $thereIsNoErrorInFormData = false;
   }
   else if ( strlen( $courseCode ) == 6 && ( !isDigit( $courseCode[3] ) || !isDigit( $courseCode[4] ) || !isDigit(  $courseCode[5] ) ) ) {
      $errorMessageForCourseCode = '<p class="col-sm-offset-2 col-sm-10" id="errorMessage">Invalid course code. A valid course code consists of three alphabets, followed by a space and three digits.</p>';
      $thereIsNoErrorInFormData = false;
   }
   else if ( strlen( $courseCode ) == 7 && ( $courseCode[3] != ' ' || !isDigit( $courseCode[4] ) || !isDigit( $courseCode[5] ) || !isDigit(  $courseCode[6] ) ) ) {
      $errorMessageForCourseCode = '<p class="col-sm-offset-2 col-sm-10" id="errorMessage">Invalid course code. A valid course code consists of three alphabets, followed by a space and three digits.</p>';
      $thereIsNoErrorInFormData = false;
   }

   if ( empty ( $courseTitle ) ) {
      $errorMessageForCourseTitle = '<p class="col-sm-offset-2 col-sm-10" id="errorMessage">Please enter course title.</p>';
      $thereIsNoErrorInFormData = false;
   }
   else if ( !consistsOfOnlyAlphabetsAndSpaces( $courseTitle ) ) {
      $errorMessageForCourseTitle = '<p class="col-sm-offset-2 col-sm-10" id="errorMessage">Invalid course title. A valid course title must consist of only alphabets and, possibly, spaces.</p>';
      $thereIsNoErrorInFormData = false;
   }

   if ( empty ( $department ) ) {
      $errorMessageForDepartment = '<p class="col-sm-offset-2 col-sm-10" id="errorMessage">Please enter name of department.</p>';
      $thereIsNoErrorInFormData = false;
   }
   else if ( !consistsOfOnlyAlphabetsAndSpaces( $department ) ) {
      $errorMessageForDepartment = '<p class="col-sm-offset-2 col-sm-10" id="errorMessage">Invalid name of department. A valid a valid name of department must consist of only alphabets and, possibly, spaces.</p>';
      $thereIsNoErrorInFormData = false;
   }

   if ( $durationOfProgramme == -1 ) {
      $errorMessageForDurationOfProgramme = '<p class="col-sm-offset-2 col-sm-10" id="errorMessage">Please select a valid duration of programme.</p>';
      $thereIsNoErrorInFormData = false;
   }

   if ( empty ( $faculty ) ) {
      $errorMessageForFaculty = '<p class="col-sm-offset-2 col-sm-10" id="errorMessage">Please enter name of faculty.</p>';
      $thereIsNoErrorInFormData = false;
   }
   else if ( !consistsOfOnlyAlphabetsAndSpaces( $faculty ) ) {
      $errorMessageForFaculty = '<p class="col-sm-offset-2 col-sm-10" id="errorMessage">Invalid name of faculty. A valid a valid name of faculty must consist of only alphabets and, possibly, spaces.</p>';
      $thereIsNoErrorInFormData = false;
   }

   if ( $requiredAction == 'addNewCourse' && $formShouldNotBeDisplayed && $thereIsNoErrorInFormData ) {
      if ( strlen( $courseCode ) == 6 ) {
         addSpaceWithinCourseCode( $courseCode );
      }

      $query = 'SELECT course_title, department_id FROM courses WHERE course_code = "' . $courseCode . '"';
      $resultContainingCourseData = mysqli_query( $db, $query ) or die( 'A' . $markupIndicatingDatabaseQueryFailure );
      $rowContainingCourseData = mysqli_fetch_assoc( $resultContainingCourseData );
      if ( $rowContainingCourseData != NULL ) {
         $query = 'SELECT department_name FROM departments WHERE department_id = ' . $rowContainingCourseData['department_id'];
         $resultContainingDepartmentData = mysqli_query( $db, $query ) or die( 'K' . $markupIndicatingDatabaseQueryFailure );
         $rowContainingDepartmentData = mysqli_fetch_assoc( $resultContainingDepartmentData );
      }

      $courseCodeInputtedByUserAlreadyExistsInDatabase = ( $rowContainingCourseData != NULL ) ? true : false;

      if ( $courseCodeInputtedByUserAlreadyExistsInDatabase ) {
         if ( strtolower( $courseTitle ) != strtolower( $rowContainingCourseData['course_title'] ) ) {
            indicateThatCourseTitleInputtedByUserConflictsWithCourseCodeInputtedByUser();
         }
         else if ( strtolower( $department ) != strtolower( $rowContainingDepartmentData['department_name'] ) ) {
            indicateThatDepartmentInputtedByUserConflictsWithCourseCodeInputtedByUser();
         }
         else {
            indicateThatUserIsAttemptingToAddAnAlreadyExistingCourse();
         }

         displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
         exit( 0 );
      }
   }

   if ( $formShouldNotBeDisplayed && $thereIsNoErrorInFormData ) {
      $query = 'SELECT faculty_id, department_duration_of_programme FROM departments WHERE department_name = "' . $department . '"';
      $resultContainingDepartmentData = mysqli_query( $db, $query ) or die( 'B' . $markupIndicatingDatabaseQueryFailure );
      $rowContainingDepartmentData = mysqli_fetch_assoc( $resultContainingDepartmentData );
      $facultyIdAssociatedWithDepartmentInputtedByUser =
         ( $rowContainingDepartmentData != NULL ) ? $rowContainingDepartmentData['faculty_id'] : NULL;
      $durationOfProgrammeAssociatedWithDepartmentInputtedByUser =
         ( $rowContainingDepartmentData != NULL ) ? $rowContainingDepartmentData['department_duration_of_programme'] : NULL;

      $query = 'SELECT faculty_id FROM faculties WHERE faculty_name = "' . $faculty . '"';
      $resultContainingFacultyData = mysqli_query( $db, $query ) or die( 'C' . $markupIndicatingDatabaseQueryFailure );
      $rowContainingFacultyData = mysqli_fetch_assoc( $resultContainingFacultyData );
      $facultyIdOfFacultyInputtedByUser =
         ( $rowContainingFacultyData != NULL ) ? $rowContainingFacultyData['faculty_id'] : NULL;
      $durationOfProgrammeInputtedByUser = $durationOfProgramme;

      $departmentInputtedByUserAlreadyExistsInDatabase = $rowContainingDepartmentData != NULL ? true : false;

      if ( $departmentInputtedByUserAlreadyExistsInDatabase && $durationOfProgrammeInputtedByUser != $durationOfProgrammeAssociatedWithDepartmentInputtedByUser ) {
         indicateThatDurationOfProgrammeInputtedByUserConflictsWithDepartmentInputtedByUser();
      }
      else if ( $departmentInputtedByUserAlreadyExistsInDatabase && $facultyIdOfFacultyInputtedByUser != $facultyIdAssociatedWithDepartmentInputtedByUser ) {
         indicateThatFacultyInputtedByUserConflictsWithDepartmentInputtedByUser();
      }
      else if ( $requiredAction == 'editCourse' ) {
         updateDatabaseWithCourseDetailsInputtedByUser();
         indicateThatDatabaseHasBeenUpdatedWithCourseInputtedByUser();
      }
      else {
         insertCourseDetailsInputtedByUserIntoDatabase();
         indicateThatCourseInputtedByUserHasBeenInsertedIntoDatabase();
      }

      displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
      exit( 0 );
   }
}

$thereIsErrorInFormData = !$thereIsNoErrorInFormData;

if ( $formShouldBeDisplayed || $thereIsErrorInFormData ) {
   displayAddOrEditCourseForm();
   displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
   exit( 0 );
}


function displayAddOrEditCourseForm()
{
   global $requiredAction, $errorMessageForCourseCode, $errorMessageForCourseTitle, $errorMessageForDepartment, 
      $errorMessageForDurationOfProgramme, $errorMessageForFaculty;
?>

            <header id="minorHeaderType2">
               <h2 id="boldMediumSizedText"><?php echo $requiredAction == 'editCourse' ? 'Edit information about ' . strtoupper( $_GET['courseCode'] ) : 'Add a new course to RoarConnect Lecture Note Portal' ?></h2>
            </header>

            <form method="GET" action="add_or_edit_course.php" class="form-horizontal" id="looksLikeACardboardPaper">
               <?php echo $requiredAction == 'editCourse' ? '<input type="hidden" name="requiredAction" value="editCourse" />' : '' ?>

               <h3 id="mediumSizedText">Below, fill in the details about the course:</h3>
<?php
   if ( $requiredAction == 'editCourse' ) {
?>
               <div class="form-group">
                  <div class="control-label col-sm-2" id="boldSmallSizedText">Course code of course to be edited:</div>
                  <input type="hidden" name="courseCode" value="<?php echo ( isset( $_GET['courseCode'] ) ? $_GET['courseCode']: '' ) ?>" />
                  <div class="col-sm-10" id="mediumSizedText"><?php echo ( isset( $_GET['courseCode'] ) ? strtoupper( $_GET['courseCode'] ): '' ) ?></div>
                  <?php echo $errorMessageForCourseCode ?>

               </div>
<?php
   }
   else {
?>
               <div class="form-group">
                  <label for="courseCode" class="control-label col-sm-2">Course code:</label>
                  <div class="col-sm-10"><input type="text" name="courseCode" value="<?php echo ( isset( $_GET['courseCode'] ) ? $_GET['courseCode']: '' ) ?>" class="form-control" id="courseCode" /></div>
                  <?php echo $errorMessageForCourseCode ?>

               </div>
<?php
   }
?>

               <div class="form-group">
                  <label for="courseTitle" class="control-label col-sm-2">Course title:</label>
                  <div class="col-sm-10"><input type="text" name="courseTitle" value="<?php echo ( isset( $_GET['courseTitle'] ) ? $_GET['courseTitle']: '' ) ?>" class="form-control" id="courseTitle" /></div>
                  <?php echo $errorMessageForCourseTitle ?>

               </div>

               <div class="form-group">
                  <label for="department" class="control-label col-sm-2">Department offering the course:</label>
                  <div class="col-sm-10"><input type="text" name="department" value="<?php echo ( isset( $_GET['department'] ) ? $_GET['department']: '' ) ?>" class="form-control" id="department"/></div>
                  <?php echo $errorMessageForDepartment ?>

               </div>

               <div class="form-group">
                  <label for="durationOfProgramme" class="control-label col-sm-2">Duration of the department's programme:</label>
                  <div class="col-sm-10">
                     <select name="durationOfProgramme" class="form-control" id="durationOfProgramme">
                        <option  value="-1">---</option>
                        <option <?php echo ( isset( $_GET['durationOfProgramme'] ) && $_GET['durationOfProgramme'] == 4 ? 'selected' : '' ) ?> value="4">4-year Programme</option>
                        <option <?php echo ( isset( $_GET['durationOfProgramme'] ) && $_GET['durationOfProgramme'] == 5 ? 'selected' : '' ) ?> value="5">5-year Programme</option>
                        <option <?php echo ( isset( $_GET['durationOfProgramme'] ) && $_GET['durationOfProgramme'] == 6 ? 'selected' : '' ) ?> value="6">6-year Programme</option>
                     </select>
                  </div>
                  <?php echo $errorMessageForDurationOfProgramme ?>

               </div>

               <div class="form-group">
                  <label for="faculty" class="control-label col-sm-2">Faculty which the department is under:</label>
                  <div class="col-sm-10"><input type="text" name="faculty" value="<?php echo ( isset( $_GET['faculty'] ) ? $_GET['faculty']: '' ) ?>" class="form-control" id="faculty"/></div>
                  <?php echo $errorMessageForFaculty ?>

               </div>

               <div class="form-group">
                  <div class="col-sm-offset-2 col-sm-10"><button type="submit" name="submitButton" class="btn btn-success"><?php echo $requiredAction == 'editCourse' ? 'Edit Course': 'Add Course' ?></button></div>
               </div>
            </form>

<?php
}


function indicateThatCourseTitleInputtedByUserConflictsWithCourseCodeInputtedByUser()
{
   global $db, $markupIndicatingDatabaseQueryFailure, $courseCode, $courseTitle;

   $query = 'SELECT course_code, course_title, department_id FROM courses WHERE course_code = "' . $courseCode . '"';
   $resultContainingCourseData = mysqli_query( $db, $query ) or die( 'X' . $markupIndicatingDatabaseQueryFailure );
   $rowContainingCourseData = mysqli_fetch_assoc( $resultContainingCourseData );

   $query = 'SELECT department_name, department_duration_of_programme, faculty_id FROM departments WHERE department_id = ' . $rowContainingCourseData['department_id'];
   $resultContainingDepartmentData = mysqli_query( $db, $query ) or die( 'y' . $markupIndicatingDatabaseQueryFailure );
   $rowContainingDepartmentData = mysqli_fetch_assoc( $resultContainingDepartmentData );

   $query = 'SELECT faculty_name FROM faculties WHERE faculty_id = ' . $rowContainingDepartmentData['faculty_id'];
   $resultContainingFacultyData = mysqli_query( $db, $query ) or die( 'z' . $markupIndicatingDatabaseQueryFailure );
   $rowContainingFacultyData = mysqli_fetch_assoc( $resultContainingFacultyData );
?>
            <div id="containerHoldingErrorMessage">
               <h2>Invalid Input: <span>Conflicting Course Titles</span></h2>
               <p><?php echo strtoupper( $courseCode ) ?> already exists in RoarConnect's database with a course title of "<?php echo ucwords( $rowContainingCourseData['course_title'] ) ?>."</p>
               <p>But you are attempting to add it with a course title of "<?php echo ucwords( $courseTitle ) ?>."</p>
               <p>If what you want is to edit the course title, visit the <a href="add_or_edit_course.php?requiredAction=editCourse&formShouldBeDisplayed&courseCode=<?php echo $rowContainingCourseData['course_code'] ?>&courseTitle=<?php echo $rowContainingCourseData['course_title'] ?>&department=<?php echo $rowContainingDepartmentData['department_name'] ?>&durationOfProgramme=<?php echo $rowContainingDepartmentData['department_duration_of_programme'] ?>&faculty=<?php echo $rowContainingFacultyData['faculty_name'] ?>" class="btn btn-default btn-sm">Edit Course Page</a> instead.</p>
            </div>

<?php
}


function indicateThatDepartmentInputtedByUserConflictsWithCourseCodeInputtedByUser()
{
   global $db, $markupIndicatingDatabaseQueryFailure, $courseCode, $department;
   $query = 'SELECT course_code, course_title, department_id FROM courses WHERE course_code = "' . $courseCode . '"';
   $resultContainingCourseData = mysqli_query( $db, $query ) or die( 'L' . $markupIndicatingDatabaseQueryFailure );
   $rowContainingCourseData = mysqli_fetch_assoc( $resultContainingCourseData );

   $query = 'SELECT department_name, department_duration_of_programme, faculty_id FROM departments WHERE department_id = ' . $rowContainingCourseData['department_id'];
   $resultContainingDepartmentData = mysqli_query( $db, $query ) or die( 'L' . $markupIndicatingDatabaseQueryFailure );
   $rowContainingDepartmentData = mysqli_fetch_assoc( $resultContainingDepartmentData );

   $query = 'SELECT faculty_name FROM faculties WHERE faculty_id = ' . $rowContainingDepartmentData['faculty_id'];
   $resultContainingFacultyData = mysqli_query( $db, $query ) or die( 'L' . $markupIndicatingDatabaseQueryFailure );
   $rowContainingFacultyData = mysqli_fetch_assoc( $resultContainingFacultyData );
?>
            <div id="containerHoldingErrorMessage">
               <h2>Invalid Input: <span>Conflicting Departments</span></h2>
               <p><?php echo strtoupper( $courseCode ) ?> already exists in RoarConnect's as being offered by <?php echo ucwords( $rowContainingDepartmentData['department_name'] ) ?> department.</p>
               <p>But you are attempting to specify that it is offered by <?php echo ucwords( $department ) ?> department.</p>
               <p>If what you want is to edit department, visit the <a href="add_or_edit_course.php?requiredAction=editCourse&formShouldBeDisplayed&courseCode=<?php echo $rowContainingCourseData['course_code'] ?>&courseTitle=<?php echo $rowContainingCourseData['course_title'] ?>&department=<?php echo $rowContainingDepartmentData['department_name'] ?>&durationOfProgramme=<?php echo $rowContainingDepartmentData['department_duration_of_programme'] ?>&faculty=<?php echo $rowContainingFacultyData['faculty_name'] ?>" class="btn btn-default btn-sm">Edit Course Page</a> instead.</p>
            </div>

<?php
}


function indicateThatUserIsAttemptingToAddAnAlreadyExistingCourse()
{
   global $db, $markupIndicatingDatabaseQueryFailure, $courseCode;

   $query = 'SELECT course_title FROM courses WHERE course_code = "' . $courseCode . '"';
   $resultContainingCourseData = mysqli_query( $db, $query ) or die( 'L' . $markupIndicatingDatabaseQueryFailure );
   $rowContainingCourseData = mysqli_fetch_assoc( $resultContainingCourseData );
?>
            <div id="containerHoldingErrorMessage">
               <h2>Course Already Exists</h2>
               <p><?php echo strtoupper( $courseCode ) ?> (<?php echo ucwords( $rowContainingCourseData['course_title'] ) ?>) already exists in RoarConnect's database.</p>
               <p>There is no need to add it again.</p>
               <p>You may simply <a href="upload_lecture_note.php" class="btn btn-default btn-sm">Upload Lecture Notes for the Course</a> or visit the <a href="lecture_notes.php" class="btn btn-default btn-sm">Lecture Note Portal</a> to view lecture notes already uploaded for the course.</p>
            </div>

<?php
}


function indicateThatFacultyInputtedByUserConflictsWithDepartmentInputtedByUser()
{
   global $db, $markupIndicatingDatabaseQueryFailure, $requiredAction,
      $courseCode, $courseTitle, $department, $durationOfProgramme, $faculty;

   $query = 'SELECT course_code, course_title, department_id FROM courses WHERE course_code = "' . $courseCode . '"';
   $resultContainingCourseData = mysqli_query( $db, $query ) or die( 'L' . $markupIndicatingDatabaseQueryFailure );
   $rowContainingCourseData = mysqli_fetch_assoc( $resultContainingCourseData );

   $query = 'SELECT department_name, department_duration_of_programme, faculty_id FROM departments WHERE department_name = "' . $department . '"';
   $resultContainingDepartmentData = mysqli_query( $db, $query ) or die( 'L' . $markupIndicatingDatabaseQueryFailure );
   $rowContainingDepartmentData = mysqli_fetch_assoc( $resultContainingDepartmentData );

   $query = 'SELECT faculty_name FROM faculties WHERE faculty_id = ' . $rowContainingDepartmentData['faculty_id'];
   $resultContainingFacultyData = mysqli_query( $db, $query ) or die( 'L' . $markupIndicatingDatabaseQueryFailure );
   $rowContainingFacultyData = mysqli_fetch_assoc( $resultContainingFacultyData );
?>
            <div id="containerHoldingErrorMessage">
               <h2>Invalid Input: <span>Conflicting Faculty</span></h2>
               <p>You specified <?php echo ucwords( $department ) ?> department to be under faculty of <?php echo ucwords( $faculty ) ?>.</p>
               <p>But from what is previously stored in RoarConnect's database, <?php echo ucwords( $department ) ?> department is under faculty of <?php echo $rowContainingFacultyData['faculty_name'] ?>.</p>
               <p>If you are sure that <?php echo ucwords( $department ) ?> is now under faculty of <?php echo ucwords( $faculty ) ?>, then <a href="update_department_data.php?formShouldBeDisplayed&nameOfDepartment=<?php echo $department ?>&nameOfFaculty=<?php echo $faculty ?>" class="btn btn-default btn-sm">Update Department Data</a> to reflect this change.</p>
               <p>If not, then <a href="add_or_edit_course.php?requiredAction=<?php echo $requiredAction ?>&formShouldBeDisplayed&courseCode=<?php echo $courseCode ?>&courseTitle=<?php echo $courseTitle ?>&department=<?php echo $department ?>&durationOfProgramme=<?php echo $durationOfProgramme ?>&faculty=<?php echo $rowContainingFacultyData['faculty_name'] ?>" class="btn btn-default btn-sm">Continue</a> with faculty being set as <?php echo ucwords( $rowContainingFacultyData['faculty_name'] ) ?>.</p>
            </div>

<?php
}


function indicateThatDurationOfProgrammeInputtedByUserConflictsWithDepartmentInputtedByUser()
{
   global $db, $markupIndicatingDatabaseQueryFailure, $requiredAction,
      $courseCode, $courseTitle, $department, $durationOfProgramme, $faculty;

   $query = 'SELECT course_code, course_title, department_id FROM courses WHERE course_code = "' . $courseCode . '"';
   $resultContainingCourseData = mysqli_query( $db, $query ) or die( 'L' . $markupIndicatingDatabaseQueryFailure );
   $rowContainingCourseData = mysqli_fetch_assoc( $resultContainingCourseData );

   $query = 'SELECT department_name, department_duration_of_programme, faculty_id FROM departments WHERE department_name = "' . $department . '"';
   $resultContainingDepartmentData = mysqli_query( $db, $query ) or die( 'L' . $markupIndicatingDatabaseQueryFailure );
   $rowContainingDepartmentData = mysqli_fetch_assoc( $resultContainingDepartmentData );

   $query = 'SELECT faculty_name FROM faculties WHERE faculty_id = ' . $rowContainingDepartmentData['faculty_id'];
   $resultContainingFacultyData = mysqli_query( $db, $query ) or die( 'L' . $markupIndicatingDatabaseQueryFailure );
   $rowContainingFacultyData = mysqli_fetch_assoc( $resultContainingFacultyData );
?>
            <div id="containerHoldingErrorMessage">
               <h2>Invalid Input: <span>Conflicting Durations of Programme</span></h2>
               <p>You specified <?php echo ucwords( $department ) ?> department to run a programme of <?php echo $durationOfProgramme ?> years.</p>
               <p>But from what is previously stored in RoarConnect's database, <?php echo ucwords( $department ) ?> department runs a programme of <?php echo $rowContainingDepartmentData['department_duration_of_programme'] ?> years.</p>
               <p>If you are sure that <?php echo ucwords( $department ) ?> now runs a <?php echo $durationOfProgramme ?>-year programme, then <a href="update_department_data.php?formShouldBeDisplayed&nameOfDepartment=<?php echo $department ?>&durationOfProgramme=<?php echo $durationOfProgramme ?>" class="btn btn-default btn-sm">Update Department Data</a> to reflect this change.</p>
               <p>If not, then <a href="add_or_edit_course.php?requiredAction=<?php echo $requiredAction ?>&formShouldBeDisplayed&courseCode=<?php echo $courseCode ?>&courseTitle=<?php echo $courseTitle ?>&department=<?php echo $department ?>&durationOfProgramme=<?php echo $rowContainingDepartmentData['department_duration_of_programme'] ?>&faculty=<?php echo $faculty ?>" class="btn btn-default btn-sm">Continue</a> with duration programme being set at <?php echo $rowContainingDepartmentData['department_duration_of_programme'] ?> years.</p>
            </div>
<?php
}


function indicateThatDatabaseHasBeenUpdatedWithCourseInputtedByUser()
{
   global $courseCode, $courseTitle, $department, $durationOfProgramme, $faculty;
?>

            <div id="containerHoldingSuccessMessage">
               <h2>Successfully Edited Course</h2>
               <p>You have successfully edited the details of the course, <?php echo strtoupper( $courseCode ) ?>. Below are the new details of the course:</p>
               <ul>
                  <li><span>Course code:</span> <?php echo strtoupper( $courseCode ) ?></li>
                  <li><span>Course title:</span> <?php echo ucwords( $courseTitle ) ?></li>
                  <li><span>Department:</span> <?php echo ucwords( $department ) ?></li>
                  <li><span>Duration of programme:</span> <?php echo $durationOfProgramme ?> years.</li>
                  <li><span>Faculty:</span> <?php echo ucwords( $faculty ) ?></li>
               </ul>
               <p>You may like to <a href="upload_lecture_note.php" class="btn btn-default btn-sm">Upload Lecture Note for the Course</a> or <a href="lecture_notes.php" class="btn btn-default btn-sm">Go to the Lecture Note Portal</a> to view lecture notes already uploaded for the course.</p>
            </div>
<?php
}


function indicateThatCourseInputtedByUserHasBeenInsertedIntoDatabase()
{
   global $courseCode, $courseTitle, $department, $durationOfProgramme, $faculty;
?>

            <div id="containerHoldingSuccessMessage">
               <h2>Successfully Added Course</h2>
               <p>Your required course has been successfully added to RoarConnect's database with the following details:</p>
               <ul>
                  <li><span>Course code:</span> <?php echo strtoupper( $courseCode ) ?></li>
                  <li><span>Course title:</span> <?php echo ucwords( $courseTitle ) ?></li>
                  <li><span>Department:</span> <?php echo ucwords( $department ) ?></li>
                  <li><span>Duration of programme:</span> <?php echo $durationOfProgramme ?> years.</li>
                  <li><span>Faculty:</span> <?php echo ucwords( $faculty ) ?></li>
               </ul>
               <p>You may like to <a href="add_or_edit_course.php" class="btn btn-default btn-sm">Add Another Course</a> or <a href="upload_lecture_note.php" class="btn btn-default btn-sm">Upload Lecture Notes for the New Course</a>.</p>
            </div>
<?php
}


function addSpaceWithinCourseCode( &$courseCode )
{
   for ( $i = strlen( $courseCode ) - 1; isDigit( $courseCode[$i] ); $i-- ) {
      $courseCode[$i + 1] = $courseCode[$i];
   }

   $courseCode[$i + 1] = ' ';
}


function getYearOfStudy( $courseCode )
{
   for ( $i = strlen( $courseCode ) - 1; isDigit( $courseCode[$i] ); $i-- )
      ;

   return $courseCode[$i + 1];
}


function updateDatabaseWithCourseDetailsInputtedByUser()
{
   global $db, $courseCode, $courseTitle, $faculty, $department, $durationOfProgramme;
   $idOfFormerDepartment = 0;
   $idOfFormerFaculty = 0;

   $query = 'SELECT faculty_id FROM faculties WHERE faculty_name = "' . $faculty . '"';
   $result = mysqli_query( $db, $query ) or die( 'E' . $markupIndicatingDatabaseQueryFailure );
   $row = mysqli_fetch_assoc( $result );
   if ( $row != false ) {
      $idOfFaculty = $row['faculty_id'];
   }
   else {
      $query = 'INSERT INTO faculties ( faculty_name ) VALUES ( "' . ucwords( $faculty ) . '" )';
      mysqli_query( $db, $query ) or die( 'F' . $markupIndicatingDatabaseQueryFailure );
      $idOfFaculty = mysqli_insert_id( $db );
   }

   $query = 'SELECT department_id FROM departments WHERE department_name = "' . $department . '"';
   $result = mysqli_query( $db, $query ) or die( 'G' . $markupIndicatingDatabaseQueryFailure );
   $row = mysqli_fetch_assoc( $result );
   if ( $row != NULL ) {
      $idOfDepartment = $row['department_id'];
   }
   else {
      $query = 'INSERT INTO departments ( department_name, department_duration_of_programme, faculty_id )
         VALUES ( "' . ucwords( $department ) . '", ' . $durationOfProgramme . ', ' . $idOfFaculty . ' )';
      mysqli_query( $db, $query ) or die( 'H' . $markupIndicatingDatabaseQueryFailure );
      $idOfDepartment = mysqli_insert_id( $db );
   }

   $query = 'SELECT department_id FROM courses WHERE course_code = "' . $courseCode . '"';
   $result = mysqli_query( $db, $query ) or die( 'Z'.  $markupIndicatingDatabaseQueryFailure );
   $row = mysqli_fetch_assoc( $result );
   $idOfFormerDepartment = $row['department_id'];

   $query = 'UPDATE courses
      SET course_title = "' . ucwords( $courseTitle ) . '", department_id = ' . $idOfDepartment . '
      WHERE course_code = "' . $courseCode . '"';
   mysqli_query( $db, $query ) or die( 'J' . $markupIndicatingDatabaseQueryFailure );

   $query = 'SELECT course_code FROM courses WHERE department_id = ' . $idOfFormerDepartment;
   $result = mysqli_query( $db, $query ) or die( 'C'. $markupIndicatingDatabaseQueryFailure );
   $numberOfCoursesOfferedByFormerDepartment = mysqli_num_rows( $result );

   if ( $numberOfCoursesOfferedByFormerDepartment == 0 ) {
      $query = 'SELECT faculty_id FROM departments WHERE department_id = ' . $idOfFormerDepartment;
      $result = mysqli_query( $db, $query ) or die( 'D'. $markupIndicatingDatabaseQueryFailure );
      $row = mysqli_fetch_assoc( $result );
      $idOfFormerFaculty = $row['faculty_id'];

      $query = 'DELETE FROM departments WHERE department_id = ' . $idOfFormerDepartment;
      mysqli_query( $db, $query ) or die( 'E'.$markupIndicatingDatabaseQueryFailure );
   }

   $query = 'SELECT department_id FROM departments WHERE faculty_id = ' . $idOfFormerFaculty;
   $result = mysqli_query( $db, $query ) or die( 'F'. $markupIndicatingDatabaseQueryFailure );
   $numberOfDepartmentsInFormerFaculty = mysqli_num_rows( $result );

   if ( $numberOfDepartmentsInFormerFaculty == 0 ) {
      $query = 'DELETE FROM faculties WHERE faculty_id = ' . $idOfFormerFaculty;
      $result = mysqli_query( $db, $query ) or die( 'G'. $markupIndicatingDatabaseQueryFailure );
   }

}


function insertCourseDetailsInputtedByUserIntoDatabase()
{
   global $db, $courseCode, $courseTitle, $faculty, $department, $durationOfProgramme;

   $query = 'SELECT faculty_id FROM faculties WHERE faculty_name = "' . $faculty . '"';
   $result = mysqli_query( $db, $query ) or die( 'E' . $markupIndicatingDatabaseQueryFailure );
   $row = mysqli_fetch_assoc( $result );
   if ( $row != false ) {
      $idOfFaculty = $row['faculty_id'];
   }
   else {
      $query = 'INSERT INTO faculties ( faculty_name ) VALUES ( "' . ucwords( $faculty ) . '" )';
      mysqli_query( $db, $query ) or die( 'F' . $markupIndicatingDatabaseQueryFailure );
      $idOfFaculty = mysqli_insert_id( $db );
   }

   $query = 'SELECT department_id FROM departments WHERE department_name = "' . $department . '"';
   $result = mysqli_query( $db, $query ) or die( 'G' . $markupIndicatingDatabaseQueryFailure );
   $row = mysqli_fetch_assoc( $result );
   if ( $row != NULL ) {
      $idOfDepartment = $row['department_id'];
   }
   else {
      $query = 'INSERT INTO departments ( department_name, department_duration_of_programme, faculty_id )
         VALUES ( "' . ucwords( $department ) . '", ' . $durationOfProgramme . ', ' . $idOfFaculty . ' )';
      mysqli_query( $db, $query ) or die( 'H' . $markupIndicatingDatabaseQueryFailure );
      $idOfDepartment = mysqli_insert_id( $db );
   }

   $query = 'INSERT INTO courses ( course_code, course_title, course_year_of_study, department_id )
      VALUES ( "' . strtoupper( $courseCode ) . '", "' . ucwords( $courseTitle ) . '", ' . getYearOfStudy( $courseCode ) . ', ' . $idOfDepartment . ' )';
   mysqli_query( $db, $query ) or die( 'I' . $markupIndicatingDatabaseQueryFailure );
}


function getNameOfFaculty( $idOfFaculty )
{
   global $db;

   $query = 'SELECT faculty_name FROM faculties WHERE faculty_id = ' . $idOfFaculty;
   $result = mysqli_query( $db, $query ) or die( 'J' . $markupIndicatingDatabaseQueryFailure );
   $row = mysqli_fetch_assoc( $result );

   if ( $row != NULL ) {
      return $row['faculty_name'];
   }
   else {
      return '';
   }
}
?>