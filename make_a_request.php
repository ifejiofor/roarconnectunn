<?php
require_once 'includes/generalHeaderFile.php';

if(!currentUserIsLoggedIn()){
	header('location:index.php');
}else{
   displayMarkupsCommonToTopOfPages( 'Make a Request', DISPLAY_NAVIGATION_MENU, 'make_a_request.php' );

	if(isset($_POST['text'])&& isset($_POST['describe']) && isset($_POST['phone'])){
		$text=trim(htmlentities($_POST['text']));
		$describe=trim(htmlentities($_POST['describe']));
		$phone=trim(htmlentities($_POST['phone']));
		If(!empty($text) && !empty($describe) && !empty($phone) ){
		  if($phone>'07000000000' && $phone<'09999999999'){
					if((strlen($phone)==11)){
							 $phone;
		$request="SELECT `firstname`, `email` FROM `users` WHERE `id`='".$_SESSION['user_id']."'";
		if($request_new=mysqli_query($db, $request)){
			$request_query=mysqli_fetch_array($request_new);
		     $first=$request_query['firstname'];
           $email = $request_query['email'];
			 $update="INSERT INTO `user_requests`(`firstname`, `request`, `description`, `phone`, `email`) VALUES ('$first','$text','$describe','$phone','$email')";
			if($update_query=mysqli_query($db, $update)){
				echo'<p id="successMessage">Request successfully uploaded.</p>';
			}else{
				echo '<p id="errorMessage">Request upload unsuccessful.</p>';
			}
		}else{
			echo '<p id="errorMessage">Unexpected error encountered while retrieving data.</p>';
		}			
						
					}else{
						echo'<p id="errorMessage">Invalid phone number.</p>';
					}
					
				}else{
					echo '<p id="errorMessage">Invalid phone number.</p>';
				}
			
		}else{
			echo '<p id="errorMessage">Please enter all fields!.</p>';
		}
	}
?>

            <h2>Make a Request</h2>
            <p>Is there any item which you will like to buy but did not find in this platform? Request for it here and you can get a seller who is willing to sell that item to you within the twinkle of an eye.</p>

            <form class="form-horizontal" action="make_a_request.php" method="POST">
               <h3 id="mediumSizedText">Fill the form below to get started:</h3>

               <div class="form-group">
                  <label for="itemRequest" class="control-label col-sm-2">Enter item request:</label>
                  <div class="col-sm-10"><input type="text" name="text" class="form-control" id="itemRequest" maxlength="50"/></div>
               </div>

               <div class="form-group">
                  <label for="briefDescription" class="control-label col-sm-2">Enter a brief description of the item:</label>
                  <div class="col-sm-10"><textarea name="describe" class="form-control" id="briefDescription" maxlength="100"></textarea></div>
               </div>

               <div class="form-group">
                  <label for="phoneNumber" class="control-label col-sm-2">Enter your phone number:</label>
                  <div class="col-sm-10"><input type="text" name="phone" class="form-control" id="phoneNumber" maxlength="15"/></div>
               </div>

               <div class="form-group">
                  <div class="col-sm-offset-2 col-sm-10"><input type="submit" value="Create Request" class="btn btn-success"/></div>
               </div>
            </form>
         </div>
      </div>
<?php
   displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
?>
   </body>
</html>
<?php
}
?>