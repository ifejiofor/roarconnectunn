<?php
require_once 'includes/utilityFunctions.php';

if ( isset( $_GET['courseCode'] ) ) {
   $_POST['courseCode'] = $_GET['courseCode'];
}

if ( !userIsLoggedInAsAdmin() || !isset( $_POST['courseCode'] ) ) {
   header( 'Location: lecture_notes.php' );
}

require_once 'includes/performBasicInitializations.php';
require_once 'includes/markupFunctions.php';

$nameOfDataToExemptWhenBuildingStringFromGET = 'courseCode';

if ( isset( $_POST['courseCode'] ) && !isset( $_POST['confirmation'] ) ) {
   displayMarkupsCommonToTopOfPages( 'Delete Course', DISPLAY_NAVIGATION_MENU, 'delete_course.php' );

   $query = 'SELECT lecture_note_id FROM lecture_notes WHERE course_code = "' . $_POST['courseCode'] . '"';
   $result = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );

   if ( mysqli_num_rows( $result ) > 0 ) {
?>

            <div id="containerHoldingErrorMessage">
               <h2>Delete Course</h2>
               <p>Before you can delete a course, you must have deleted all the lecture notes associated with that course.</p>
<?php
      if ( mysqli_num_rows( $result ) == 1 ) {
         echo '
               <p>There is currently one lecture note associated with ' . strtoupper( $_POST['courseCode'] ) . '.</p>';
      }
      else {
         echo '
               <p>There are currently ' . mysqli_num_rows( $result ) . ' lecture notes associated with ' . strtoupper( $_POST['courseCode'] ) . '.</p>';

      }
?>

               <p><a href="view_lecture_note_information.php?<?php echo buildStringContainingAllDataFromGET( $nameOfDataToExemptWhenBuildingStringFromGET ) ?>" class="btn btn-default">Click Here to Go Back</a> so that you can delete <?php echo mysqli_num_rows( $result ) == 1 ? 'it' : 'them' ?>.</p>
            </div>
<?php
   }
   else {
      $query = 'SELECT course_title FROM courses WHERE course_code = "' . $_POST['courseCode'] . '"';
      $result = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
      $row = mysqli_fetch_assoc( $result );
?>

            <form method="POST" action="delete_course.php?<?php echo buildStringContainingAllDataFromGET( $nameOfDataToExemptWhenBuildingStringFromGET ) ?>" id="containerHoldingErrorMessage">
               <input type="hidden" name="courseCode" value="<?php echo $_POST['courseCode'] ?>" />

               <h2>Delete Course</h2>
               <p>Are you sure you want to delete this course from RoarConnect's database?</p>
               <ul>
                  <li>Course code: <?php echo strtoupper( $_POST['courseCode'] ) ?></li>
                  <li>Course title: <?php echo $row['course_title'] ?></li>
               </ul>
               <input type="submit" name="confirmation" value="Yes" class="btn btn-danger btn-lg" id="tinyMargin" />
               <input type="submit" name="confirmation" value="No" class="btn btn-danger btn-lg" id="tinyMargin" />
            </form>
<?php
   }

   displayMarkupsCommonToBottomOfPages( DO_NOT_DISPLAY_FOOTER );
}
else if ( isset( $_POST['courseCode'] ) && isset( $_POST['confirmation'] ) && $_POST['confirmation'] == 'No' ) {
   header( 'Location: view_lecture_note_information.php?' . buildStringContainingAllDataFromGET( $nameOfDataToExemptWhenBuildingStringFromGET ) );
}
else if ( isset( $_POST['courseCode'] ) && isset( $_POST['confirmation'] ) && $_POST['confirmation'] == 'Yes' ) {
   $requiredCourse = $_POST['courseCode'];
   $idOfRequiredDepartment = $idOfRequiredFaculty = 0;

   $query = 'SELECT department_id FROM courses WHERE course_code = "' . $requiredCourse . '"';
   $result = mysqli_query( $db, $query ) or die( 'A'.  $markupIndicatingDatabaseQueryFailure );
   $row = mysqli_fetch_assoc( $result );
   $idOfRequiredDepartment = $row['department_id'];

   $query = 'DELETE FROM courses WHERE course_code = "' . $requiredCourse . '"';
   mysqli_query( $db, $query ) or die( 'B' . $markupIndicatingDatabaseQueryFailure );

   $query = 'SELECT course_code FROM courses WHERE department_id = ' . $idOfRequiredDepartment;
   $result = mysqli_query( $db, $query ) or die( 'C'. $markupIndicatingDatabaseQueryFailure );
   $numberOfCoursesOfferedByRequiredDepartment = mysqli_num_rows( $result );

   if ( $numberOfCoursesOfferedByRequiredDepartment == 0 ) {
      $query = 'SELECT faculty_id FROM departments WHERE department_id = ' . $idOfRequiredDepartment;
      $result = mysqli_query( $db, $query ) or die( 'D'. $markupIndicatingDatabaseQueryFailure );
      $row = mysqli_fetch_assoc( $result );
      $idOfRequiredFaculty = $row['faculty_id'];

      $query = 'DELETE FROM departments WHERE department_id = ' . $idOfRequiredDepartment;
      mysqli_query( $db, $query ) or die( 'E'.$markupIndicatingDatabaseQueryFailure );
   }

   $query = 'SELECT department_id FROM departments WHERE faculty_id = ' . $idOfRequiredFaculty;
   $result = mysqli_query( $db, $query ) or die( 'F'. $markupIndicatingDatabaseQueryFailure );
   $numberOfDepartmentsInRequiredFaculty = mysqli_num_rows( $result );

   if ( $numberOfDepartmentsInRequiredFaculty == 0 ) {
      $query = 'DELETE FROM faculties WHERE faculty_id = ' . $idOfRequiredFaculty;
      $result = mysqli_query( $db, $query ) or die( 'G'. $markupIndicatingDatabaseQueryFailure );
   }

   header( 'Location: view_lecture_note_information.php?' . buildStringContainingAllDataFromGET( $nameOfDataToExemptWhenBuildingStringFromGET ) );
}

?>