<?php
require_once 'includes/generalHeaderFile.php';

displayMarkupscommonToTopOfPages( 'Food Delivery', DISPLAY_NAVIGATION_MENU, 'view_all_food_vendors.php' );
?>
            <header id="minorHeader">
               <h2>RoarConnect's Food Delivery Marketplace</h2>
               <p>Order food from a vendor of your choice and get it delivered to your doorstep.</p>
<?php
      if ( currentUserIsLoggedInAsAdmin() ) {
?>
            <div>
			      <a href="add_or_edit_vendor.php?requiredAction=addVendor" class="btn btn-primary">Add New Food Vendor</a>
               <a href="view_all_food_orders.php" class="btn btn-primary">View All Food Orders</a>
			   </div>
<?php
      }
?>
            </header>

            <section class="text-center">
<?php
$query = 'SELECT vendor_id, vendor_name FROM vendors WHERE vendor_category = "foods" ORDER BY vendor_name';
$resultContainingVendorData = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
$rowContainingVendorData = mysqli_fetch_assoc( $resultContainingVendorData );

while ( $rowContainingVendorData != NULL ) {
?>

               <div <?php echo currentUserIsLoggedInAsAdmin() ? 'class="panel panel-primary" ' : '' ?>id="displayAsInlineBlock">
                  <a href="choose_category_of_food.php?idOfVendor=<?php echo $rowContainingVendorData['vendor_id'] ?>" id="looksLikeALargeHoverableIcon">
                     <div id="iconImage" style="background-image: url( '<?php echo 'images/vendorFliers/' . $rowContainingVendorData['vendor_name'] . '.jpg' ?>' ); background-size: cover;"></div>
                     <h4 id="iconContent"><?php echo $rowContainingVendorData['vendor_name'] ?></h4>
                  </a>
<?php
      if ( currentUserIsLoggedInAsAdmin() ) {
?>
                  <div>
                     <a href="add_or_edit_vendor.php?requiredAction=editVendor&idOfVendor=<?php echo $rowContainingVendorData['vendor_id'] ?>" class="btn btn-primary btn-sm">Edit Vendor</a>
                     <a href="delete_vendor.php?idOfVendor=<?php echo $rowContainingVendorData['vendor_id'] ?>" class="btn btn-primary btn-sm">Delete Vendor</a>
                  </div>
<?php
      }
?>
               </div>
<?php
   $rowContainingVendorData = mysqli_fetch_assoc( $resultContainingVendorData );
}
?>
            </section>

            <section>
               <h3>Featured Foods</h3>
               <p>About three foods (such as, jollof rice and salad, etc) that we think that users may like to buy in one click.</p>
            </section>
<?php
displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
?>