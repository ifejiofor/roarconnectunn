<?php
require_once 'includes/generalHeaderFile.php';

displayMarkupscommonToTopOfPages( 'Food Delivery', DISPLAY_NAVIGATION_MENU, 'view_all_food_vendors.php' );
?>
            <header id="minorHeader">
               <h2>RoarConnect Food Delivery Marketplace</h2>
<?php
      if ( userIsLoggedInAsAdmin() ) {
?>
            <p>
			      <a href="add_or_edit_vendor.php?requiredAction=addVendor" class="btn btn-warning">Add a New Food Vendor</a>
               <a href="view_all_food_orders.php" class="btn btn-warning">View All Food Orders</a>
			   </p>
<?php
      }
?>
               <p>Order from a vendor of your choice and get the food delivered to your doorstep.</p>
            </header>

            <div class="text-center">
<?php
$query = 'SELECT vendor_id, vendor_name FROM vendors WHERE vendor_category = "foods" ORDER BY vendor_name';
$resultContainingVendorData = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
$rowContainingVendorData = mysqli_fetch_assoc( $resultContainingVendorData );

while ( $rowContainingVendorData != NULL ) {
?>

               <div id="displayAsInlineBlock">
                  <a href="choose_category_of_food.php?idOfVendor=<?php echo $rowContainingVendorData['vendor_id'] ?>" id="looksLikeALargeHoverableIcon">
                     <div style="width: 100%; height: 80%; background-image: url( '<?php echo 'images/vendorFliers/' . $rowContainingVendorData['vendor_name'] . '.jpg' ?>' ); background-size: cover;"></div>
                     <h4 id="iconContent">Order for food from <?php echo $rowContainingVendorData['vendor_name'] ?></h4>
                  </a>
<?php
      if ( userIsLoggedInAsAdmin() ) {
?>
                  <p>
                     <a href="add_or_edit_vendor.php?requiredAction=editVendor&idOfVendor=<?php echo $rowContainingVendorData['vendor_id'] ?>" class="btn btn-default btn-sm">Edit Vendor</a>
                     <a href="delete_vendor.php?idOfVendor=<?php echo $rowContainingVendorData['vendor_id'] ?>" class="btn btn-default btn-sm">Delete Vendor</a>
                  </p>
<?php
      }
?>
               </div>
<?php
   $rowContainingVendorData = mysqli_fetch_assoc( $resultContainingVendorData );
}
?>
            </div>
<?php
displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
?>