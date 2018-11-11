<?php
require_once 'in.php';
require_once 'require.php';
require_once 'markupsCommonToTopAndBottomOfPages.php';

if ( !isset( $_GET['category'] ) || !isset( $_GET['idOfVendor'] ) ) {
   header( 'Location: index.php' );
}

if ( $_GET['category'] != 'Cake' && $_GET['category'] != 'Chicken' && $_GET['category'] != 'Pizza' && $_GET['category'] != 'Shawarma' &&
   $_GET['category'] != 'Rice' && $_GET['category'] != 'Swallow' && $_GET['category'] != 'Spaghetti' && $_GET['category'] != 'Pancake' &&
   $_GET['category'] != 'Yam' && $_GET['category'] != 'Fruit Salad' && $_GET['category'] != 'Indomie' )
{
   header( 'Location: index.php' );
}

if ( !consistsOfOnlyDigits( $_GET['idOfVendor'] ) ) {
   header( 'Location: index.php' );
}

$query = 'SELECT id_new, name_of_item, image_size FROM photo_upload WHERE category = "' . $_GET['category'] . '" AND people_id = "VENDOR_' . $_GET['idOfVendor'] . '" AND checks ="APPROVED"';
$resultContainingFoodData = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );

if ( mysqli_num_rows( $resultContainingFoodData ) == 1 ) {
   header( 'Location: make_order_for_food.php?category=' . $_GET['category'] . '&idOfVendor=' . $_GET['idOfVendor'] );
}
else {
   $query = 'SELECT vendor_name FROM vendors WHERE vendor_id = ' . $_GET['idOfVendor'];
   $resultContainingVendorData = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
   $rowContainingVendorData = mysqli_fetch_assoc( $resultContainingVendorData );

   $customizedStyleForBodyElement = 'background-image: url( \'images/vendorFliers/' . $rowContainingVendorData['vendor_name'] . '.jpg\' ); background-size: cover;';
   displayMarkupsCommonToTopOfPages( 'Choose Type of ' . $_GET['category'], DISPLAY_NAVIGATION_MENU, 'choose_type_of_food.php', $customizedStyleForBodyElement );
?>
            <div id="minorHeader">
               <h2><?php echo $rowContainingVendorData['vendor_name'] ?> Delivery Outlet on RoarConnect</h2>
               <p>Select Type of <?php echo $_GET['category'] ?></p>
            </div>
			
            <div class="text-center">
<?php
   $rowContainingFoodData = mysqli_fetch_assoc( $resultContainingFoodData );
   while ( $rowContainingFoodData != NULL ) {
?>
               <a href="make_order_for_food.php?category=<?php echo ucwords( $_GET['category'] ) ?>&idOfVendor=<?php echo $_GET['idOfVendor'] ?>&idOfRequiredFood=<?php echo $rowContainingFoodData['id_new'] ?>" id="looksLikeALargeHoverableIcon">
                  <div style="width: 100%; height: 80%; background-image: url( '<?php echo 'images/uploaded' . ucwords( $_GET['category'] ) . 'Snapshots/VENDOR_' . $_GET['idOfVendor'] . '@' . $rowContainingFoodData['image_size'] ?>' ); background-size: cover;"></div>
                  <h4 id="iconContent"><?php echo ucwords( $rowContainingFoodData['name_of_item'] ) ?></h4>
               </a>
<?php
      $rowContainingFoodData = mysqli_fetch_assoc( $resultContainingFoodData );
   }
?>
               <p id="boldSmallSizedText"><a href="view_all_food_vendors.php">&lt;&lt;Click here to go back to the Food Delivery Marketplace</a>.</p>
			</div>
<?php
   displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
}
?>