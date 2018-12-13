<?php
include_once 'includes/generalHeaderFile.php';

if ( !isset( $_GET['idOfVendor'] ) || !consistsOfOnlyDigits( $_GET['idOfVendor'] ) ) {
   header( 'Location: index.php' );
}

$query = 'SELECT category, image_size FROM photo_upload WHERE people_id = "VENDOR_' . $_GET['idOfVendor'] . '" AND checks = "APPROVED" AND category != "dessert" AND category != "drink"';
$resultContainingFoodData = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );

if ( mysqli_num_rows( $resultContainingFoodData ) == 0 ) {
   header( 'Location: view_all_food_vendors.php' );
}

$filenamesOfSnapshotsToBeDisplayed = array();
$rowContainingFoodData = mysqli_fetch_assoc( $resultContainingFoodData );

while ( $rowContainingFoodData != NULL ) {
   $filenameOfSnapshotHasAlreadyBeenTracked = false;

   foreach ( $filenamesOfSnapshotsToBeDisplayed as $category => $urlOfSnapshot ) {
      if ( $rowContainingFoodData['category'] == $category ) {
         $filenameOfSnapshotHasAlreadyBeenTracked = true;
         break;
      }
   }

   $filenameOfSnapshotHasNotYetBeenTracked = !$filenameOfSnapshotHasAlreadyBeenTracked;

   if ( $filenameOfSnapshotHasNotYetBeenTracked ) {
      $category = $rowContainingFoodData['category'];
      $filenamesOfSnapshotsToBeDisplayed[$category] = $rowContainingFoodData['image_size'];
   }

   $rowContainingFoodData = mysqli_fetch_assoc( $resultContainingFoodData );
}

if ( count( $filenamesOfSnapshotsToBeDisplayed ) == 1 ) {
   header( 'Location: make_order_for_food.php?category=' . ucwords( $category ) . '&idOfVendor=' . $_GET['idOfVendor'] );
}
else if ( count( $filenamesOfSnapshotsToBeDisplayed ) > 1 ) {
   $query = 'SELECT vendor_name FROM vendors WHERE vendor_id = ' . $_GET['idOfVendor'];
   $resultContainingVendorData = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
   $rowContainingVendorData = mysqli_fetch_assoc( $resultContainingVendorData );

   if ( strtolower( $rowContainingVendorData['vendor_name'] ) == 'pizza palace' ) {
      $requiredVendorSellsManyTypesOfFood = true;
   }
   else {
      $requiredVendorSellsManyTypesOfFood = false;
   }

   $customizedStyleForBodyElement = 'background-image: url( \'images/vendorFliers/' . $rowContainingVendorData['vendor_name'] . '.jpg\' ); background-size: contain;';
   displayMarkupsCommonToTopOfPages( 'Choose Type of Food', DISPLAY_NAVIGATION_MENU, 'choose_category_of_food.php', $customizedStyleForBodyElement );
?>
            <div id="minorHeader">
               <h2><?php echo $rowContainingVendorData['vendor_name'] ?> Menu</h2>
               <p>What will you like to eat?</p>
            </div>

            <div class="text-center">
<?php
   foreach ( $filenamesOfSnapshotsToBeDisplayed as $category => $urlOfSnapshot ) {
      $query = 'SELECT name_of_item FROM photo_upload WHERE people_id = "VENDOR_' . $_GET['idOfVendor'] . '" AND checks = "APPROVED" AND category = "' . strtolower( $category ) . '"';
      $resultContainingFoodData = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );

      if ( mysqli_num_rows( $resultContainingFoodData ) == 1 ) {
         $rowContainingFoodData = mysqli_fetch_assoc( $resultContainingFoodData );
         $nameOfItem = $rowContainingFoodData['name_of_item'];
      }
      else {
         $nameOfItem = $category;
      }
      
      if ( currentUserIsLoggedIn() ) {
?>
               <a href="<?php echo $requiredVendorSellsManyTypesOfFood ? 'choose_type_of_food.php' : 'make_order_for_food.php' ?>?category=<?php echo ucwords( $category ) ?>&idOfVendor=<?php echo $_GET['idOfVendor'] ?>" id="looksLikeALargeHoverableIcon">
                  <div style="width: 100%; height: 80%; background-image: url( '<?php echo 'images/uploaded' . ucwords( $category ) . 'Snapshots/VENDOR_' . $_GET['idOfVendor'] . '@' . $urlOfSnapshot ?>' ); background-size: cover;"></div>
                  <h4 id="iconContent"><?php echo ucwords( $nameOfItem ) ?></h4>
               </a>

<?php 
      }
      else {
?>
               <div id="displayAsInlineBlock">
<?php
         $markupToDisplayWithinButton = '<div style="width: 100%; height: 80%; background-image: url( ' . "\'" . 'images/uploaded' . ucwords( $category ) . 'Snapshots/VENDOR_' . $_GET['idOfVendor'] . '@' . $urlOfSnapshot . "\'" . ' ); background-size: cover;"></div> <h4 id="iconContent">' . ucwords( $nameOfItem ) . '</h4>';
         $miscellaneousAttributesOfButton = 'id="looksLikeALargeHoverableIcon"';
         getMarkupForButtonThatWillTellUserToLogInBeforeContinuing( $markupToDisplayWithinButton, $miscellaneousAttributesOfButton );
?>
               </div>
<?php
      }

   }
?>
               <p id="boldSmallSizedText"><a href="view_all_food_vendors.php">&lt;&lt;Click here to go back to the Food Delivery Marketplace</a>.</p>
            </div>
<?php
   if ( !currentUserIsLoggedIn() ) {
      // The markup that the following function gives is the markup for the modal that the buttons got earlier from 'getMarkupButtonThatWillTellUserToLogInBeforeContinuing' brings up 
      getMarkupForModalThatTellsUserToLogInBeforeContinuing();
   }
   
   displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
}
?>