<?php
require_once 'in.php';
require_once 'require.php';
require_once 'markupsCommonToTopAndBottomOfPages.php';

displayMarkupsCommonToTopOfPages( 'Lecture Note Portal', DISPLAY_NAVIGATION_MENU, 'lecture_notes.php' );
?>
            <header id="minorHeader">
               <h2>Welcome to RoarConnect Lecture Note Portal</h2>
<?php
if ( loggedInAsAdmin() ) {
?>
               <p><a href="upload_lecture_note.php" class="btn btn-warning">Upload a New Lecture Note</a></p>
<?php
}
?>
               <p id="minorTextInMinorHeader">Your much needed lecture note is just one click away. Simply select the appropriate department or use the search bar below.</p>
            </header>

            <form method="GET" action="search_for_lecture_notes.php" class="form-inline text-center" id="searchBar">
               <input type="text" name="searchQuery" placeholder="Search Keyword to get Lecture Note..." class="form-control" id="search" />
               <button type="submit" name="searchButton" class="btn btn-primary">Search</button>
            </form>

            <div id="minorNavigationBar">
               <h2 id="boldSmallSizedTextWithNoMargin">Jump to:</h2>
               <ul class="pagination" id="noMargin">
<?php
$query = 'SELECT faculty_id, faculty_name FROM faculties ORDER BY faculty_name';
$resultContainingFacultyData = mysqli_query( $db, $query );
$rowContainingFacultyData = mysqli_fetch_assoc( $resultContainingFacultyData );

while ( $rowContainingFacultyData != NULL ) {
   echo '
                  <li><a href="#' . $rowContainingFacultyData['faculty_id'] .'">' . $rowContainingFacultyData['faculty_name'] . '</a></li>';

   // store the faculty name and faculty id in an associative array using the faculty name as index and faculty id as value
   $arrayOfFacultyIds[$rowContainingFacultyData['faculty_name']] = $rowContainingFacultyData['faculty_id'];

   $rowContainingFacultyData = mysqli_fetch_assoc( $resultContainingFacultyData );
}
?>

               </ul>
            </div>

<?php
foreach ( $arrayOfFacultyIds as $facultyName => $facultyId ) { // loop through the array created previously (where the faculty name was used as index and faculty id as value)
?>

            <section id="<?php echo $facultyId ?>">
               <div id="smallContainerWithBorderAndAllowsOverflow">
                  <h3 id="overflowingHeader">Faculty of <?php echo $facultyName ?></h3>
                  <ul id="listArrangedInTwoColumnsOnLargeScreens">
<?php
   $query = 'SELECT * FROM departments WHERE faculty_id = ' . $facultyId . ' ORDER BY department_name';
   $resultContainingDepartmentData = mysqli_query( $db, $query );
   $rowContainingDepartmentData = mysqli_fetch_assoc( $resultContainingDepartmentData );

   while ( $rowContainingDepartmentData != NULL ) {
?>
                     <li><a href="choose_year_of_study_of_lecture_note.php?departmentName=<?php echo $rowContainingDepartmentData['department_name'] ?>&departmentId=<?php echo $rowContainingDepartmentData['department_id'] ?>&durationOfProgramme=<?php echo $rowContainingDepartmentData['department_duration_of_programme'] ?>&facultyId=<?php echo $facultyId ?>"><?php echo $rowContainingDepartmentData['department_name'] ?> Lecture Notes</a></li>
<?php
      $rowContainingDepartmentData = mysqli_fetch_assoc( $resultContainingDepartmentData );
   }
?>
                  </ul>
               </div>
            </section>
<?php
}

displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
?>