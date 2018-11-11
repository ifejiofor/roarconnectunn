<?php
require_once 'in.php';
require_once 'require.php';
require_once 'markupsCommonToTopAndBottomOfPages.php';
require_once 'markupsForMiscellaneousTasks.php';

if ( !$_GET ) {
   header( 'Location: lecture_notes.php' );
}

$errorMessageForDepartment = '';
$errorMessageForDurationOfProgramme = '';
$errorMessageForFaculty = '';

$thereIsErrorInFormData = false;
$formShouldBeDisplayed = isset( $_GET['formShouldBeDisplayed'] );

$nameOfDepartment = trim( htmlentities( $_GET['nameOfDepartment'] ) );
$durationOfProgramme = trim( htmlentities( $_GET['durationOfProgramme'] ) );
$nameOfFaculty = trim( htmlentities( $_GET['nameOfFaculty'] ) );

if ( empty ( $nameOfDepartment ) ) {
   $errorMessageForDepartment = '<p class="col-sm-offset-2 col-sm-10" id="errorMessage">Please enter name of department.</p>';
   $thereIsErrorInFormData = true;
}
else if ( !consistsOfOnlyAlphabetsAndSpaces( $nameOfDepartment ) ) {
   $errorMessageForDepartment = '<p class="col-sm-offset-2 col-sm-10" id="errorMessage">Invalid name of department. A valid a valid name of department must consist of only alphabets and, possibly, spaces.</p>';
   $thereIsErrorInFormData = true;
}

if ( isset( $_GET['durationOfProgramme'] ) && $durationOfProgramme == -1 ) {
   $errorMessageForDurationOfProgramme = '<p class="col-sm-offset-2 col-sm-10" id="errorMessage">Please select a valid duration of programme.</p>';
   $thereIsErrorInFormData = true;
}

if ( isset( $_GET['nameOfFaculty'] ) && empty ( $nameOfFaculty ) ) {
   $errorMessageForFaculty = '<p class="col-sm-offset-2 col-sm-10" id="errorMessage">Please enter name of faculty.</p>';
   $thereIsErrorInFormData = true;
}
else if ( isset( $_GET['nameOfFaculty'] ) && !consistsOfOnlyAlphabetsAndSpaces( $nameOfFaculty ) ) {
   $errorMessageForFaculty = '<p class="col-sm-offset-2 col-sm-10" id="errorMessage">Invalid name of faculty. A valid a valid name of faculty must consist of only alphabets and, possibly, spaces.</p>';
   $thereIsErrorInFormData = true;
}


displayMarkupsCommonToTopOfPages( 'Update Department Data', DISPLAY_NAVIGATION_MENU, 'update_department_data.php' );

if ( !loggedInAsAdmin() ) {
   session_destroy();
   displayMarkupToIndicateThatAdminLoginIsRequired();
}

if ( $formShouldBeDisplayed || $thereIsErrorInFormData ) {
   displayUpdateDepartmentDataForm();
}
else if ( loggedInAsAdmin() ) {
   if ( isset( $_GET['durationOfProgramme'] ) ) {
      $query = 'UPDATE departments SET department_duration_of_programme = ' . $durationOfProgramme . ' WHERE department_name = "' . $nameOfDepartment . '"';
      mysqli_query( $db, $query ) or die( 'A' . $markupIndicatingDatabaseQueryFailure );

      indicateThatDepartmentDataHasBeenUpdatedSuccessfully();
   }
   else if ( isset( $_GET['nameOfFaculty'] ) ) {
      $query = 'SELECT faculty_id FROM departments WHERE department_name = "' . $nameOfDepartment . '"';
      $result = mysqli_query( $db, $query ) or die( 'B' . $markupIndicatingDatabaseQueryFailure );
      $row = mysqli_fetch_assoc( $result );
      $idOfFacultyAssociatedWithDepartment = $row['faculty_id'];

      $query = 'SELECT department_name FROM departments WHERE faculty_id = ' . $idOfFacultyAssociatedWithDepartment;
      $result = mysqli_query( $db, $query ) or die( 'C' . $markupIndicatingDatabaseQueryFailure );
      $facultyAssociatedWithDepartmentIsNotAlsoAssociatedWithAnotherDepartment = mysqli_num_rows( $result ) == 1;

      if ( $facultyAssociatedWithDepartmentIsNotAlsoAssociatedWithAnotherDepartment ) {
         $query = 'DELETE FROM faculties WHERE faculty_id = ' . $idOfFacultyAssociatedWithDepartment;
         mysqli_query( $db, $query ) or die( 'D' . $markupIndicatingDatabaseQueryFailure );
      }

      $query = 'SELECT faculty_id FROM faculties WHERE faculty_name = "' . $nameOfFaculty . '"';
      $result = mysqli_query( $db, $query ) or die( 'E' . $markupIndicatingDatabaseQueryFailure );
      $row = mysqli_fetch_assoc( $result );

      if ( $row != NULL ) {
         $idOfFaculty = $row['faculty_id'];
      }
      else {
         $query = 'INSERT INTO faculties ( faculty_name ) VALUES ( "' . ucwords( $nameOfFaculty ) . '" )';
         mysqli_query( $db, $query ) or die( 'F' . $markupIndicatingDatabaseQueryFailure );
         $idOfFaculty = mysqli_insert_id( $db );
      }

      $query = 'UPDATE departments SET faculty_id = ' . $idOfFaculty . ' WHERE department_name = "' . $nameOfDepartment . '"';
      mysqli_query( $db, $query ) or die( 'G' . $markupIndicatingDatabaseQueryFailure );

      indicateThatDepartmentDataHasBeenUpdatedSuccessfully();
   }
}

displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );


function displayUpdateDepartmentDataForm()
{
   global $errorMessageForDepartment, $errorMessageForDurationOfProgramme, $errorMessageForFaculty;
?>

            <form method="GET" action="update_department_data.php" class="form-horizontal" id="looksLikeACardboardPaper">
               <h2 id="mediumSizedText">Update Department Data</h2>

               <div class="form-group">
                  <label for="department" class="control-label col-sm-2">Department offering the course:</label>
                  <div class="col-sm-10"><input type="text" name="nameOfDepartment" value="<?php echo ( isset( $_GET['nameOfDepartment'] ) ? $_GET['nameOfDepartment']: '' ) ?>" class="form-control" id="department" readonly /></div>
                  <?php echo $errorMessageForDepartment ?>

               </div>

<?php
   if ( isset( $_GET['durationOfProgramme'] ) ) {
?>
               <div class="form-group">
                  <label for="durationOfProgramme" class="control-label col-sm-2">Duration of the department's programme:</label>
                  <div class="col-sm-10">
                     <select name="durationOfProgramme" class="form-control" id="durationOfProgramme">
                        <option  value="-1">---</option>
                        <option <?php echo $_GET['durationOfProgramme'] == 4 ? 'selected' : '' ?> value="4">4-year Programme</option>
                        <option <?php echo $_GET['durationOfProgramme'] == 5 ? 'selected' : '' ?> value="5">5-year Programme</option>
                        <option <?php echo $_GET['durationOfProgramme'] == 6 ? 'selected' : '' ?> value="6">6-year Programme</option>
                     </select>
                  </div>
                  <?php echo $errorMessageForDurationOfProgramme ?>

               </div>
<?php
   }
   else if ( isset( $_GET['nameOfFaculty'] ) ) {
?>
               <div class="form-group">
                  <label for="faculty" class="control-label col-sm-2">Faculty which the department is under:</label>
                  <div class="col-sm-10"><input type="text" name="nameOfFaculty" value="<?php echo $_GET['nameOfFaculty'] ?>" class="form-control" id="faculty"/></div>
                  <?php echo $errorMessageForFaculty ?>

               </div>
<?php
   }
?>
               <div class="form-group">
                  <div class="col-sm-offset-2 col-sm-10"><button type="submit" name="submitButton" class="btn btn-success">Update Department Data</button></div>
               </div>
            </form>

<?php
}


function indicateThatDepartmentDataHasBeenUpdatedSuccessfully()
{
   global $nameOfDepartment, $durationOfProgramme, $nameOfFaculty;
?>
            <div id="containerHoldingSuccessMessage">
               <h2>Successfully Updated Department Data</h2>
               <p>
                  You have successfully updated
<?php
   if ( isset( $_GET['durationOfProgramme'] ) ) {
      echo '
                  the duration of programme offered in ' . ucwords( $nameOfDepartment ) . ' department to ' . $durationOfProgramme . ' years.';
   }
   else if ( isset( $_GET['nameOfFaculty'] ) ) {
      echo '
                  ' . ucwords( $nameOfDepartment ) . ' department to be under faculty of ' . ucwords( $nameOfFaculty ) . '.';
   }
?>
               </p>

               <p>You may like to <a href="add_or_edit_course.php" class="btn btn-default btn-sm">Add a New Course for the Department</a> or <a href="lecture_notes.php" class="btn btn-default btn-sm">Go to the Lecture Note Portal</a> to view courses and lecture notes already associated with the department.</p>
            </div>
<?php
}