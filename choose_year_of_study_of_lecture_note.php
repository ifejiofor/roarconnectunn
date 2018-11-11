<?php
if ( !$_GET ) {
   header( 'Location: lecture_notes.php' );
}
else {
   require_once 'in.php';
   require_once 'require.php';
   require_once 'markupsCommonToTopAndBottomOfPages.php';

   displayMarkupsCommonToTopOfPages( $_GET['departmentName'] . ' Lecture Notes', DISPLAY_NAVIGATION_MENU, 'choose_year_of_study_of_lecture_note.php' );
?>
            <form method="GET" action="view_lecture_note_information.php" class="text-center jumbotron panel panel-primary" id="noPaddingOnSmallScreens">
               <h2 class="panel-heading" id="disappearOnSmallScreens">You requested to view lecture notes from <?php echo $_GET['departmentName'] ?> department.</h2>
               <div class="panel-body">
                  <p>Below, select the year of study of the lecture note you want:</p>

                  <input type="hidden" name="departmentId" value="<?php echo $_GET['departmentId'] ?>"/>
                  <input type="hidden" name="departmentName" value="<?php echo $_GET['departmentName'] ?>"/>
                  <input type="hidden" name="durationOfProgramme" value="<?php echo $_GET['durationOfProgramme'] ?>"/>
                  <input type="hidden" name="facultyId" value="<?php echo $_GET['facultyId'] ?>"/>

                  <button type="submit" name="yearOfStudy" value="1" class="btn btn-default">First Year</button>
                  <button type="submit" name="yearOfStudy" value="2" class="btn btn-default">Second Year</button>
                  <button type="submit" name="yearOfStudy" value="3" class="btn btn-default">Third Year</button>
<?php
   if ( $_GET['durationOfProgramme'] == 4 ) {
?>
                  <button type="submit" name="yearOfStudy" value="4" class="btn btn-default">Final Year</button>
<?php
   }
   else if ( $_GET['durationOfProgramme'] == 5 ) {
?>
                  <button type="submit" name="yearOfStudy" value="4" class="btn btn-default">Fourth Year</button>
                  <button type="submit" name="yearOfStudy" value="5" class="btn btn-default">Final Year</button>
<?php
   }
   else if ( $_GET['durationOfProgramme'] == 6 ) {
?>
                  <button type="submit" name="yearOfStudy" value="4" class="btn btn-default">Fourth Year</button>
                  <button type="submit" name="yearOfStudy" value="5" class="btn btn-default">Fifth Year</button>
                  <button type="submit" name="yearOfStudy" value="6" class="btn btn-default">Final Year</button>
<?php
   }
?>
               </div>
               <p class="panel-footer" id="boldSmallSizedText">Or, if you wish, <a href="lecture_notes.php#<?php echo $_GET['facultyId'] ?>">click here to go back to Lecture Note Portal</a>.</p>
            </form>
<?php
   displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
}
?>