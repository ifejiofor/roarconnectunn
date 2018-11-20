<?php
require 'includes/utilityFunctions.php';
require 'includes/performBasicInitializations.php';
require_once 'includes/markupFunctions.php';

if (!userIsLoggedIn()) {
	header('Location: index.php');
}
else if ( !isset( $_GET['category'] ) ) {
   header( 'Location:index.php' );
}
else if ( $_GET['category'] != 'Books' && $_GET['category'] != 'Gadgets' && $_GET['category'] != 'Wears' && $_GET['category'] != 'Rooms' ) {
   header( 'Location:index.php' );
}
else {
   if ( $_GET['category'] == 'Gadgets' ) {
      $categoryInSingularForm = 'Gadget';
   }
   else if ( $_GET['category'] == 'Books' ) {
      $categoryInSingularForm = 'Book';
   }
   else if ( $_GET['category'] == 'Wears' ) {
      $categoryInSingularForm = 'Wear';
   }
   else if ( $_GET['category'] == 'Rooms' ) {
      $categoryInSingularForm = 'Room';
   }

   displayMarkupsCommonToTopOfPages( 'Your ' . $_GET['category'] . ' uploads', DISPLAY_NAVIGATION_MENU, 'your_upload.php?category=' . $_GET['category'] );
?>
            <header id="minorHeaderType2">
               <h3>Your <?php echo ucwords( $categoryInSingularForm ) ?> Upload</h3>
<?php
	$directory = 'images/uploaded'. ucwords( $_GET['category'] ) . 'Snapshots';
if($handle=opendir($directory. '/')){
	require'includes/performBasicInitializations.php';

	$query="SELECT `id_new`, `people_id`, `Name_of_item`, `Brief_Descripition`, `Price`, `Negotiable`, `Image_size`, `checks` FROM `photo_upload` WHERE `people_id`= '".$_SESSION['user_id']."' AND `Category`='" . $_GET['category'] . "'";
	$query_run=mysqli_query($db, $query);
		if(mysqli_num_rows($query_run)>=1){
?>
               <p>Here is the <?php echo strtolower( $categoryInSingularForm ) ?> you have uploaded on RoarConnect</p>
            </header>
<?php
         $query_result=mysqli_fetch_assoc($query_run);
		 $userr_id=($query_result['people_id']);
		 $size=($query_result['Image_size']);
		 $find= "$userr_id@$size";

		 while($file=readdir($handle)){
		if($file!='.' && $file!='..'){
			if(preg_match("/$find/", $file)){
?>

            <div id="looksLikeABigPaperCard">
               <div id="headerOfPaperCard">
                  <img src ="<?php echo $directory . '/' . $file ?>" alt="<?php echo 'Snapshot of ' . $query_result['Name_of_item'] ?>" />
                  <h4><?php echo ucwords( $query_result['Name_of_item'] ) ?></h4>
               </div>
               <div id="bodyOfPaperCard">
                  <p><span id="boldSmallSizedText">Description:</span> <?php echo $query_result['Brief_Descripition'] ?></p>
                  <p><span id="boldSmallSizedText">Price:</span> <?php echo $query_result['Price'] ?></p>
                  <p><span id="boldSmallSizedText">Price Negotiable?</span> <?php echo $query_result['Negotiable'] ?></p>
                  <p id="tinySizedText"><?php if ( $query_result['checks'] == 'APPROVED' ) { echo 'Approved by RoarConnect\'s admin.'; } else if ( $query_result['checks'] == 'UNAPPROVED' ) { echo 'Your upload was not approved by RoarConnect\'s admin. <a href="#" data-toggle="modal" data-target="#reasonForAdminAction">Click Here</a> to know why.'; } else { echo 'Waiting for approval by RoarConnect\'s admin. Don\'t worry this upload will be approved as soon as it is confirmed genuine.'; } ?></p>
                  <div class="text-center" id="tinyMargin">
                     <a href="edit_item.php?category=<?php echo $_GET['category'] ?>" name="editButton" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-edit"></span> Edit</a>
                     <a href="delete_item.php?category=<?php echo $_GET['category'] ?>" name="deleteButton" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-trash"></span> Delete</a>
                     <a href="delete_item.php?category=<?php echo $_GET['category'] ?>" name="soldButton" class="btn btn-primary btn-sm" id="tinyMargin"><span class="glyphicon glyphicon-ok"></span> Mark as Sold</a>
                  </div>
               </div>
            </div>
<?php
			}
	}
	
}
            if ( $query_result['checks'] == 'UNAPPROVED' ) {
               $queryToRetrieveReasonForUnapproval = 'SELECT reason FROM reasons_for_admin_actions_on_items WHERE type_of_item = "PHOTO UPLOAD" AND id_of_item = ' . $query_result['id_new'];
               $resultContainingReasonForUnapproval = mysqli_query( $db, $queryToRetrieveReasonForUnapproval ) or die( $markupIndicatingDatabaseQueryFailure );
               $rowContainingReasonForUnapproval = mysqli_fetch_assoc( $resultContainingReasonForUnapproval );
               displayMarkupForReasonForAdminActionModal( 'Unapproval', $rowContainingReasonForUnapproval['reason'] );
            }
		}else{
?>
            </header>
            <p id="mediumSizedText">You have no uploaded <?php echo strtolower( $_GET['category'] ) ?>.</p>
            <p>Is there any <?php echo strtolower( $categoryInSingularForm ) ?> you want to sell, <a href="upload_item.php" class="btn btn-default btn-sm">Click Here</a> to upload the <?php echo strtolower( $categoryInSingularForm ) ?> and get real buyers very fast.</p>
<?php
		}
}

displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
}
?>