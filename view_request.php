<?php
require_once 'includes/generalHeaderFile.php';

if(!currentUserIsLoggedIn()){
	header('location:index.php');
}else{
   displayMarkupsCommonToTopOfPages( 'View Request', DISPLAY_NAVIGATION_MENU, 'view_request.php' );
?>
            <h3>Requests on RoarConnect</h3>
            <p>Below is a list of requests made so far by RoarConnect users. If have any of the below items and want to sell your own, why not give the appropriate requester a call?</p>

<?php
$view="SELECT `firstname`, `request`, `description`, `phone`, `email` FROM `user_requests` ORDER BY `id`";
if($view_run=mysqli_query($db, $view)){
		if(mysqli_num_rows($view_run)>=1){
?>
            <table class="table table-hover">
               <thead>
                  <tr class="info">
                     <th>Request</th>
                     <th>Brief Discription of Item</th>
                     <th>Name of Requester</th>
                     <th>Phone Number of Requester</th>
                     <th></th>
                  </tr>
               </thead>
               <tbody>
<?php
			while($look=mysqli_fetch_assoc($view_run)){
				 $first=$look['firstname'];
			      $request=$look['request'];
				$describe=$look['description'];
				 $phone=$look['phone'];
             $email = $look['email'];
             
             $query = 'SELECT id FROM users WHERE email = "' . $email . '"';
             $resultContainingDataAboutUser = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
             $rowContainingDataAboutUser = mysqli_fetch_assoc( $resultContainingDataAboutUser );
             $idOfUserWhoMadeTheRequest = $rowContainingDataAboutUser['id'];
?>
                  <tr>
                     <td><?php echo $request ?></td>
                     <td><?php echo $describe ?></td>
                     <td><?php echo $first ?></td>
                     <td><?php echo $phone ?></td>
                     <td><a href="send_roarconnect_message.php?urlOfSourcePage=view_request.php&defaultMessageTitle=<?php echo $look['request'] ?>&defaultIdOfMessageRecipient=<?php echo $idOfUserWhoMadeTheRequest ?>" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-send"></span> Send Message</a></td>
                  </tr>
<?php
			}
?>
               </tbody>
            </table>
<?php
		}else{
			echo '<p>No result found. Check back later.</p>';
		}
	
}else{
	echo '<p id="errorMessage">Error in retrieving data.</p>';
}
}
?>

         </div>
      </div>
<?php
   displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
?>
   </body>
</html>
