<?php
require_once 'includes/performBasicInitializations.php';
require_once 'includes/utilityFunctions.php';
require_once 'includes/markupFunctions.php';

if ( !userIsLoggedIn() ) {
	header( 'Location: index.php' );
}
else {
   displayMarkupsCommonToTopOfPages( 'Update Status', DISPLAY_NAVIGATION_MENU, 'update_status.php' );

   if ( isset( $_POST['update'] ) && !isset( $_POST['att'] ) ) {
      echo '<p id="errorMessage">Please, select your new status.</p>';
   }

	if(isset($_POST['att']) ){
		$attr=trim(htmlentities($_POST['att']));
		if(!empty($attr)){
	$query="SELECT `attribute` FROM `users` WHERE `id`= '".$_SESSION['user_id']."'";
	if($query_run=mysqli_query($db, $query)){
		$query_result=mysqli_fetch_array($query_run);
		$query_result['attribute'];
		  $attribute=strtoupper($query_result['attribute']);
		 if($attribute=='FRESHER' || $attribute=='ASPIRANT' || $attribute=='OLD STUDENT'){
			$update="UPDATE `users` SET `attribute`='$attr' WHERE `id`='".$_SESSION['user_id']."'";
			if($update_run=(mysqli_query($db, $update))){
				header('Location: index.php');
			}else{
				echo '<p id="errorMessage">Unable to update status due to database error. Please, try again later.</p>';
			}
		 }else{
			 echo '<p id="errorMessage">Error! Invalid status currently in database.</p>';
		 }
	
	}else{
	   echo '<p id="errorMessage">Unable to update status due to an unespected database error. Please, try again later.</p>';
	}
	}
	}
?>
            <h3 id="boldMediumSizedText">Update your status to get more relevant content in RoarConnect</h3>

            <form action="update_status.php" method="POST" id="looksLikeACardboardPaper">
	            <h4 id="mediumSizedText">Has your status changed from aspirant to fresher? Or has it changed from fresher to old student?</h4>

               <fieldset>
                  <legend id="boldSmallSizedText">Then, select your new status here:</legend>
                  <div>
                     <div>
                        <input type="radio" name="att" id="aspirant" value="ASPIRANT"/>
                        <label for="aspirant">Aspirant</label>
                     </div>
                     <div>
                        <input type="radio" name="att" id="fresher" value="FRESHER"/>
                        <label for="fresher">Fresher</label>
                     </div>
                     <div>
                        <input type="radio" name="att" id="oldStudent" value="OLD_STUDENT"/>
                        <label for="oldStudent">Old Student</label>
                     </div>
                  </div>
               </fieldset>

               <div>
                  <input type="submit" value= "Update" name="update" class="btn btn-success">
               </div>
	         </form>
<?php
   displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
}
?>