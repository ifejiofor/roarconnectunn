 <?php
require_once 'includes/generalHeaderFile.php';

if ( !isset( $_GET['i'] ) || !consistsOfOnlyDigits( $_GET['i'] ) ) {
   header( 'Location: index.php' );
}

$query = 'SELECT * FROM vendors WHERE vendor_id = ' . $_GET['i'];
$resultContainingDataAboutVendor = mysqli_query($globalHandleToDatabase, $query) or die( $globalDatabaseErrorMarkup );
$rowContainingDataAboutVendor = mysqli_fetch_assoc( $resultContainingDataAboutVendor );

if ( $rowContainingDataAboutVendor == NULL ) {
   header( 'Location: index.php' );
}

$customizedStyleForBodyElement = 'background-image: url( \'assets/images/vendorFliers/' . getNameOfCurrentVendor() . '.jpg\' );';
displayMarkupsCommonToTopOfPages( $rowContainingDataAboutVendor['vendor_name'], DISPLAY_NAVIGATION_MENU, 'view_uploads_by_vendor.php?i=' . $_GET['i'], $customizedStyleForBodyElement );
?>
            <header id="minorHeader">
               <h2><?php echo formatNameAsPossessive($rowContainingDataAboutVendor['vendor_name']) ?> Storefront on RoarConnect</h2>
<?php
if ( $rowContainingDataAboutVendor['vendor_phone_number_1'] != NULL && $rowContainingDataAboutVendor['vendor_phone_number_2'] != NULL ) {
?>
               <p>Phone: <?php echo $rowContainingDataAboutVendor['vendor_phone_number_1'] . ', ' . $rowContainingDataAboutVendor['vendor_phone_number_2'] ?></p>
<?php
}
else if ( $rowContainingDataAboutVendor['vendor_phone_number_1'] != NULL ) {
?>
               <p>Phone: <?php echo $rowContainingDataAboutVendor['vendor_phone_number_1'] ?></p>
<?php
}
else if ( $rowContainingDataAboutVendor['vendor_phone_number_2'] != NULL ) {
?>
               <p>Phone: <?php echo $rowContainingDataAboutVendor['vendor_phone_number_2'] ?></p>
<?php
}

if ($rowContainingDataAboutVendor['vendor_address'] != NULL) {
?>
               <p>Physical Store Address: <?php echo $rowContainingDataAboutVendor['vendor_address'] ?></p>
<?php
}
?>
            </header>
<?php
$query = 'SELECT people_id, name_of_item, image_size, brief_descripition, price, negotiable FROM photo_upload WHERE checks = "APPROVED" AND people_id = "VENDOR_' . $_GET['i'] . '" ORDER BY id_new';
$resultContainingDataAboutItemForSale = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );

if( mysqli_num_rows( $resultContainingDataAboutItemForSale ) > 0 ) {
?>
            <h3 class="text-center">Below, are the some of the <?php echo getPhraseThatDescribesItemSoldByVendor($rowContainingDataAboutVendor['vendor_category']) ?> at <?php echo $rowContainingDataAboutVendor['vendor_name'] ?></h3>
<?php
   while( $rowContainingDataAboutItemForSale = mysqli_fetch_assoc($resultContainingDataAboutItemForSale) ) {
      $directory = 'assets/images/photoUploads/';
      $userr_id = $rowContainingDataAboutItemForSale['people_id'];
      $size = $rowContainingDataAboutItemForSale['image_size'];
      $file = "$userr_id@$size";
?>

            <div id="looksLikeASmallPaperCard">
               <div id="headerOfPaperCard">
                  <img src ="<?php echo $directory . $file ?>" />
                  <h4><?php echo ucwords( $rowContainingDataAboutItemForSale['name_of_item'] ) ?></h4>
               </div>
               <div id="bodyOfPaperCard">
                  <p><span id="boldSmallSizedText">Description:</span> <?php echo $rowContainingDataAboutItemForSale['brief_descripition'] ?></p>
                  <p><span id="boldSmallSizedText">Price:</span> <?php echo $rowContainingDataAboutItemForSale['price'] . ( $rowContainingDataAboutItemForSale['negotiable'] == 'YES' ? ' (negotiable)' : ' (non-negotiable)' ) ?></p>
                  <div class="text-center" id="tinyMargin">
                     <a href="send_roarconnect_message.php?urlOfSourcePage=view_uploads_by_vendor.php&defaultMessageTitle=Request to Buy '<?php echo $rowContainingDataAboutItemForSale['name_of_item'] ?>'&defaultIdOfMessageRecipient=<?php echo $rowContainingDataAboutVendor['user_id_of_vendor_manager'] ?>&recipientIsAVendorManager=true&idOfVendor=<?php echo $rowContainingDataAboutVendor['vendor_id'] ?>" class="btn btn-default">Buy Now</a>
                  </div>
               </div>
            </div>
<?php
   }
}
?>

            <div class="container-fluid"><a href="view_all_utility_services.php"><span class="fa fa-angle-double-left"></span> Back to Utility Services Marketplace</a></div>
<?php
displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );


function getPhraseThatDescribesItemSoldByVendor($categoryOfVendor)
{
   $categoryOfVendor = strtolower($categoryOfVendor);

   if ( $categoryOfVendor == 'gadgets' ) {
      $descriptionOfItemSold = 'gadgets we sell';
   }
   else if ( $categoryOfVendor == 'wears' ) {
      $descriptionOfItemSold = 'wears we sell';
   }
   else if ( $categoryOfVendor == 'books' ) {
      $descriptionOfItemSold = 'books we sell';
   }
   else if ( $categoryOfVendor == 'rooms' ) {
      $descriptionOfItemSold = 'hostel bedspaces and rooms we have for rent';
   }
   else if ( $categoryOfVendor == 'catering' ) {
      $descriptionOfItemSold = 'cakes, snacks, and other catering jobs we have done';
   }
   else if ( $categoryOfVendor == 'electricalWorks' ) {
      $descriptionOfItemSold = 'electrical jobs we have done';
   }
   else if ( $categoryOfVendor == 'graphicsDesigning' ) {
      $descriptionOfItemSold = 'graphics designing and video editing jobs we have done';
   }
   else if ( $categoryOfVendor == 'painting' ) {
      $descriptionOfItemSold = 'painting jobs we have done';
   }
   else if ( $categoryOfVendor == 'haircutservice' ) {
      $descriptionOfItemSold = 'Haircut Service we have done';
   }
   else if ( $categoryOfVendor == 'arts' ) {
      $descriptionOfItemSold = 'Art Works we have done';
   }
   else if ( $categoryOfVendor == 'beautyservice' ) {
      $descriptionOfItemSold = 'Beauty Services Rendered';
   }
   else if ( $categoryOfVendor == 'dataservices' ) {
      $descriptionOfItemSold = 'Data offered';
   }
   else {
      $descriptionOfItemSold = 'Items we sell';
   }

   return $descriptionOfItemSold;
}
?>