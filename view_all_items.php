<?php
if ( !isset( $_GET['category'] ) ) {
   header( 'Location: index.php' );
}
else if ( $_GET['category'] != 'Gadgets' && $_GET['category'] != 'Books' && $_GET['category'] != 'Wears' && $_GET['category'] != 'Rooms' ) {
   header( 'Location: view_all_utility_services.php?category=' . $_GET['category'] );
}
else {
	require_once 'includes/utilityFunctions.php';
	require_once 'includes/performBasicInitializations.php';
   require_once 'includes/markupFunctions.php';

   if ( $_GET['category'] == 'Gadgets' ) {
      $categoryInSingularForm = 'Gadget';
      $categoryInPluralForm = 'Gadgets';
   }
   else if ( $_GET['category'] == 'Books' ) {
      $categoryInSingularForm = 'Book';
      $categoryInPluralForm = 'Books';
   }
   else if ( $_GET['category'] == 'Wears' ) {
      $categoryInSingularForm = 'Wear';
      $categoryInPluralForm = 'Wears';
   }
   else if ( $_GET['category'] == 'Rooms' ) {
      $categoryInSingularForm = 'Room';
      $categoryInPluralForm = 'Rooms';
   }

   displayMarkupsCommonToTopOfPages( 'Buy ' . $categoryInPluralForm, DISPLAY_NAVIGATION_MENU, 'view_all_items.php?category=' . $_GET['category'] );
?>
            <header id="minorHeader">
               <h2>Welcome to RoarConnect <?php echo $categoryInSingularForm ?> Marketplace</h2>
               <p id="minorTextInMinorHeader">Here, you can buy any <?php echo strtolower( $categoryInSingularForm ) ?> of your choice.</p>
            </header>

<?php
	$query = "SELECT `Name_of_item`, `people_id`, `Image_size`, `Brief_Descripition`, `Price`, `Negotiable` FROM `photo_upload` WHERE `checks`='APPROVED' AND `Category`='" . $_GET['category'] . "' AND `people_id` NOT LIKE 'VENDOR_%' ORDER BY `id_new`";
	$resultContainingDataAboutItemForSale = mysqli_query($db, $query) or die( $markupIndicatingDatabaseQueryFailure );

   if( mysqli_num_rows( $resultContainingDataAboutItemForSale ) > 0 ){
?>
            <section>
               <header id="minorHeaderType2">
                  <h3>For Sale by Fellow RoarConnect Users</h3>
                  <p>Below are some <?php echo strtolower( $categoryInPluralForm ) ?> which fellow RoarConnect users have put up for sale. Feel free to strike a deal with the seller of any <?php echo strtolower( $categoryInSingularForm ) ?> of your choice.</p>
               </header>
<?php
      $directory = 'images/uploaded' . ucwords( $categoryInPluralForm ) . 'Snapshots';

	   while( $rowContainingDataAboutItemForSale=mysqli_fetch_assoc($resultContainingDataAboutItemForSale) ) {
		   $userr_id = $rowContainingDataAboutItemForSale['people_id'];
		   $size = $rowContainingDataAboutItemForSale['Image_size'];
		   $file = "$userr_id@$size";

         $query = 'SELECT firstname, phone_number FROM users WHERE id = ' . $rowContainingDataAboutItemForSale['people_id'];
         $resultContainingDataAboutUser = mysqli_query($db, $query) or die( $markupIndicatingDatabaseQueryFailure );
         $rowContainingDataAboutUser = mysqli_fetch_assoc( $resultContainingDataAboutUser );
?>

               <div id="looksLikeABigPaperCard">
                  <div id="headerOfPaperCard">
                     <img src ="<?php echo $directory . '/' . $file ?>" alt="<?php echo 'Snapshot of ' . $rowContainingDataAboutItemForSale['Name_of_item'] ?>" />
                     <h4><?php echo ucwords( $rowContainingDataAboutItemForSale['Name_of_item'] ) ?></h4>
                  </div>
                  <div id="bodyOfPaperCard">
                     <p><span id="boldSmallSizedText">Description:</span> <?php echo $rowContainingDataAboutItemForSale['Brief_Descripition'] ?></p>
                     <p><span id="boldSmallSizedText">Price:</span> <?php echo $rowContainingDataAboutItemForSale['Price'] . ( $rowContainingDataAboutItemForSale['Negotiable'] == 'YES' ? ' (negotiable)' : ' (non-negotiable)' ) ?></p>
                     <div class="text-center" id="tinyMargin">
                        <p>Do you like this <?php echo strtolower( $categoryInSingularForm ) ?>?</p>
                        <p>Then contact its seller, <?php echo $rowContainingDataAboutUser['firstname'] . ' on ' . $rowContainingDataAboutUser['phone_number'] ?>.</p>
<?php
         if ( userIsLoggedIn() ) {
?>
                        <p>Or <a href="send_roarconnect_message.php?urlOfSourcePage=view_all_items.php&defaultMessageTitle=<?php echo 'Request to Buy ' . ucwords( $rowContainingDataAboutItemForSale['Name_of_item'] ) ?>&defaultIdOfMessageRecipient=<?php echo $rowContainingDataAboutItemForSale['people_id'] ?>" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-send"></span> Send a Message</a>.</p>
<?php
         }
?>
                     </div>
                  </div>
               </div>
<?php
		}
?>
            </section>

<?php
   }
   
   
   $query = 'SELECT vendor_id, vendor_name FROM vendors WHERE vendor_category = "' . $_GET['category'] . '"';
   $result = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );

   if ( mysqli_num_rows( $result ) > 0 ) {
?>
            <section>
               <header id="minorHeaderType2">
                  <h3><?php echo mysqli_num_rows( $result ) == 1 ? 'Storefront' : 'Storefronts' ?> of RoarConnect Special <?php echo mysqli_num_rows( $result ) == 1 ? ' Vendor' : ' Vendors' ?></h3>
<?php
      if ( userIsLoggedInAsAdmin() ) {
?>
                  <p><a href="add_or_edit_vendor.php?requiredAction=addVendor" class="btn btn-primary" id="boldSmallSizedText">Add a New Vendor</a></p>
<?php
      }
?>
               </header>
<?php
      $row = mysqli_fetch_assoc( $result );
      while ( $row != NULL ) {
?>

               <div id="smallContainerWithBorderAndAllowsOverflow">
                  <h2 id="overflowingHeader"><?php echo $row['vendor_name'] ?></h2>
                  <img src="images/vendorFliers/<?php echo $row['vendor_name'] . '.jpg' ?>" alt="<?php echo $row['vendor_name'] ?>'s Flier" width="100%" height="100%" />

                  <div class="text-center">
<?php
         if ( userIsLoggedIn() ) {
?>
                     <a href="view_uploads_by_vendor.php?vendor=<?php echo $row['vendor_id'] ?>" class="btn btn-default btn-lg" id="boldSmallSizedText">Click Here</a>
<?php
         }
         else {
?>
                     <div id="displayAsInlineBlock">
<?php
            $markupToDisplayWithinButton = 'Click Here';
            $miscellaneousAttributesOfButton = 'class="btn btn-default btn-lg" id="boldSmallSizedText"';
            getMarkupForButtonThatWillTellUserToLogInBeforeContinuing( $markupToDisplayWithinButton, $miscellaneousAttributesOfButton );
?>
                     </div>
<?php
         }
?>

                     to view all <?php echo strtolower( $categoryInPluralForm ) ?> sold by <?php echo $row['vendor_name'] ?>.
                  </div>
<?php
      if ( userIsLoggedInAsAdmin() ) {
?>
                  <p class="text-center">Also, you may either</p>
                  <p class="text-center"><a href="add_or_edit_vendor.php?requiredAction=editVendor&idOfVendor=<?php echo $row['vendor_id'] ?>" class="btn btn-primary">Click Here</a> to edit details about <?php echo $row['vendor_name'] ?>.</p>
                  <p class="text-center">Or you may <a href="delete_vendor.php?idOfVendor=<?php echo $row['vendor_id'] ?>" class="btn btn-primary">Click Here</a> to delete <?php echo $row['vendor_name'] ?> from RoarConnect's database</p>
<?php
      }
?>
               </div>
<?php
         $row = mysqli_fetch_assoc( $result );
      }
?>
            </section>

<?php
   }

      
   if ( !userIsLoggedIn() ) {
      getMarkupForModalThatTellsUserToLogInBeforeContinuing();
   }
   
	displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );	
}
?>