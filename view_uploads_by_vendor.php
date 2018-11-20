 <?php
require_once 'includes/utilityFunctions.php';
require_once 'includes/performBasicInitializations.php';
require_once 'includes/markupFunctions.php';

if ( !isset( $_GET['vendor'] ) || !consistsOfOnlyDigits( $_GET['vendor'] ) ) {
   header( 'Location: index.php' );
}
else {
   $query = 'SELECT * FROM vendors WHERE vendor_id = ' . $_GET['vendor'];
   $resultContainingDataAboutVendor = mysqli_query($db, $query) or die( $markupIndicatingDatabaseQueryFailure );
   $rowContainingDataAboutVendor = mysqli_fetch_assoc( $resultContainingDataAboutVendor );

   if ( $rowContainingDataAboutVendor == NULL ) {
      header( 'Location: index.php' );
   }
   else if ( $rowContainingDataAboutVendor['vendor_category'] == 'gadgets' ) {
      $descriptionOfItemSold = 'gadgets we sell';
      $nameOfItemSold = 'gadgets';
   }
   else if ( $rowContainingDataAboutVendor['vendor_category'] == 'wears' ) {
      $descriptionOfItemSold = 'wears we sell';
      $nameOfItemSold = 'wears';
   }
   else if ( $rowContainingDataAboutVendor['vendor_category'] == 'books' ) {
      $descriptionOfItemSold = 'books we sell';
      $nameOfItemSold = 'books';
   }
   else if ( $rowContainingDataAboutVendor['vendor_category'] == 'rooms' ) {
      $descriptionOfItemSold = 'hostel bedspaces and rooms we have for rent';
      $nameOfItemSold = 'hostel bedspaces and rooms';
   }
   else if ( $rowContainingDataAboutVendor['vendor_category'] == 'catering' ) {
      $descriptionOfItemSold = 'cakes, snacks, and other catering jobs we have done';
      $nameOfItemSold = 'catering services';
   }
   else if ( $rowContainingDataAboutVendor['vendor_category'] == 'electricalWorks' ) {
      $descriptionOfItemSold = 'electrical jobs we have done';
      $nameOfItemSold = 'electrical services';
   }
   else if ( $rowContainingDataAboutVendor['vendor_category'] == 'graphicsDesigning' ) {
      $descriptionOfItemSold = 'graphics designing and video editing jobs we have done';
      $nameOfItemSold = 'graphics designing services';
   }
   else if ( $rowContainingDataAboutVendor['vendor_category'] == 'painting' ) {
      $descriptionOfItemSold = 'painting jobs we have done';
      $nameOfItemSold = 'painting services';
   }else if ( $rowContainingDataAboutVendor['vendor_category'] == 'HaircutService' ) {
      $descriptionOfItemSold = 'Haircut Service we have done';
      $nameOfItemSold = 'Haircut Services';
   }else if ( $rowContainingDataAboutVendor['vendor_category'] == 'arts' ) {
      $descriptionOfItemSold = 'Art Works we have done';
      $nameOfItemSold = 'Art Works';
   }else if ( $rowContainingDataAboutVendor['vendor_category'] == 'BeautyService' ) {
      $descriptionOfItemSold = 'Beauty Services Rendered';
      $nameOfItemSold = 'Beauty Service';
   }else if ( $rowContainingDataAboutVendor['vendor_category'] == 'DataServices' ) {
      $descriptionOfItemSold = 'Data offered';
      $nameOfItemSold = 'Data';
   }

   displayMarkupsCommonToTopOfPages( $rowContainingDataAboutVendor['vendor_name'], DISPLAY_NAVIGATION_MENU, 'view_uploads_by_vendor.php?vendor=' . $_GET['vendor'] );
?>
            <header id="minorHeader">
               <h2><?php echo $rowContainingDataAboutVendor['vendor_name'] ?> on RoarConnect</h2>
               <p><a href="view_all_items.php?category=<?php echo ucwords( $rowContainingDataAboutVendor['vendor_category'] ) ?>">&lt;&lt;Click here to go back to the <?php echo ucwords( $nameOfItemSold ) ?> Marketplace</a></p>
               <p id="minorTextInMinorHeader">Below, are the various <?php echo $descriptionOfItemSold ?> at <?php echo $rowContainingDataAboutVendor['vendor_name'] ?>. If you like to do business with us, simply contact us via our phone number<?php if ( $rowContainingDataAboutVendor['vendor_phone_number_1'] != NULL && $rowContainingDataAboutVendor['vendor_phone_number_2'] != NULL ) { echo ' (' . $rowContainingDataAboutVendor['vendor_phone_number_1'] . ' or ' . $rowContainingDataAboutVendor['vendor_phone_number_2'] . ')'; } else if ( $rowContainingDataAboutVendor['vendor_phone_number_1'] != NULL ) { echo ' (' . $rowContainingDataAboutVendor['vendor_phone_number_1'] . ')'; } ?> or visit our store<?php echo $rowContainingDataAboutVendor['vendor_address'] != NULL ? ' at ' . $rowContainingDataAboutVendor['vendor_address'] : '' ?>.</p>
            </header>

<?php

	$query = 'SELECT people_id, name_of_item, image_size, brief_descripition, price, category, negotiable FROM photo_upload WHERE checks = "APPROVED" AND people_id = "VENDOR_' . $_GET['vendor'] . '" ORDER BY id_new';
	$resultContainingDataAboutItemForSale = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );

   if( mysqli_num_rows( $resultContainingDataAboutItemForSale ) > 0 ) {
	   while( $rowContainingDataAboutItemForSale = mysqli_fetch_assoc($resultContainingDataAboutItemForSale) ) {
         $directory = 'images/uploaded' . ucwords( $rowContainingDataAboutItemForSale['category'] ) . 'Snapshots';
		   $userr_id = $rowContainingDataAboutItemForSale['people_id'];
		   $size = $rowContainingDataAboutItemForSale['image_size'];
		   $file = "$userr_id@$size";
?>

            <div id="looksLikeASmallPaperCard">
               <div id="headerOfPaperCard">
                  <img src ="<?php echo $directory . '/' . $file ?>" alt="<?php echo 'Snapshot of ' . $rowContainingDataAboutItemForSale['name_of_item'] ?>" />
                  <h4><?php echo ucwords( $rowContainingDataAboutItemForSale['name_of_item'] ) ?></h4>
               </div>
               <div id="bodyOfPaperCard">
                  <p><span id="boldSmallSizedText">Description:</span> <?php echo $rowContainingDataAboutItemForSale['brief_descripition'] ?></p>
                  <p><span id="boldSmallSizedText">Price:</span> <?php echo $rowContainingDataAboutItemForSale['price'] . ( $rowContainingDataAboutItemForSale['negotiable'] == 'YES' ? ' (negotiable)' : ' (non-negotiable)' ) ?></p>
                  <div class="text-center" id="tinyMargin">
                     <a href="send_roarconnect_message.php?urlOfSourcePage=view_uploads_by_vendor.php&defaultMessageTitle=<?php echo $rowContainingDataAboutItemForSale['name_of_item'] ?>&defaultIdOfMessageRecipient=<?php echo $rowContainingDataAboutVendor['user_id_of_vendor_manager'] ?>&recipientIsAVendorManager=true&idOfVendor=<?php echo $rowContainingDataAboutVendor['vendor_id'] ?>" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-send"></span> Send a Message</a>
                  </div>
               </div>
            </div>
<?php
		}
   }

	displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );	
}
?>