<?php
require_once 'includes/generalHeaderFile.php';

if ( !userIsLoggedInAsAdmin() ) {
	header( 'Location: index.php' );
}

if ( isset( $_POST['requiredAction'] ) && $_POST['requiredAction'] == 'clearOrdersOfVendor' ) {
	$query = 'DELETE FROM orders WHERE vendor_id = ' . $_POST['idOfVendor'];
	mysqli_query( $db, $query );
}

displayMarkupsCommonToTopOfPages( 'Food Orders', DISPLAY_NAVIGATION_MENU, 'view_all_food_orders.php' );
?>
            <h1 id="minorHeaderType2">Food Orders on RoarConnect</h1>
			
            <table class="table table-striped">
               <thead>
                  <tr>
            	     <th>Vendor Name</th>
            		 <th>Number of Orders</th>
					 <th> </th>
            	  </tr>
               </thead>
   
               <tbody>
<?php

$query = 'SELECT vendor_id, vendor_name FROM vendors WHERE vendor_category = "foods"';
$resultContainingVendorData = mysqli_query( $db, $query );
$rowContainingVendorData = mysqli_fetch_assoc( $resultContainingVendorData );

while ( $rowContainingVendorData != NULL ) {
	$query = 'SELECT order_id FROM orders WHERE vendor_id = ' . $rowContainingVendorData['vendor_id'];
	$resultContainingOrders = mysqli_query( $db, $query );
?>
                  <tr>
                     <td><?php echo $rowContainingVendorData['vendor_name'] ?></td>
                     <td><?php echo mysqli_num_rows( $resultContainingOrders ) ?></td>
					 <td>
					    <form method="POST" action="view_all_food_orders.php">
						   <input type="hidden" name="idOfVendor" value="<?php echo $rowContainingVendorData['vendor_id'] ?>"/>
						   <button type="submit" name="requiredAction" value="clearOrdersOfVendor" class="btn btn-warning">Clear</button>
						</form>
					 </td>
                  </tr>

<?php
	$rowContainingVendorData = mysqli_fetch_assoc( $resultContainingVendorData );
}

?>               </tbody>
            </table>
<?php

// Show breakdown of PIZZA PALACE's orders
?>
            <h1 id="minorHeaderType2">Breakdown of PIZZA PALACE's orders</h1>
			
            <table class="table table-striped">
               <thead>
                  <tr>
	                 <th>Category of Food</th>
		             <th>Number of Orders</th>
	              </tr>
               </thead>
   
               <tbody>
<?php
$query = 'SELECT vendor_id FROM vendors WHERE vendor_name = "PIZZA PALACE"';
$resultContainingVendorData = mysqli_query( $db, $query );
$rowContainingVendorData = mysqli_fetch_assoc( $resultContainingVendorData );
$vendorIdOfPizzaPalace = $rowContainingVendorData['vendor_id'];

$categoriesOfFoodSoldByPizzaPalace = array( 'pizza', 'shawarma', 'pancake' );

foreach ( $categoriesOfFoodSoldByPizzaPalace as $key => $categoryOfFood ) {
	$query = 'SELECT name_of_item FROM photo_upload WHERE category = "' . $categoryOfFood . '"';
	$resultContainingFoodData = mysqli_query( $db, $query );
	$rowContainingFoodData = mysqli_fetch_assoc( $resultContainingFoodData );
	
	$query = 'SELECT order_id FROM orders WHERE vendor_id = ' . $vendorIdOfPizzaPalace . ' AND ( 0';
	while ( $rowContainingFoodData != NULL ){
		$query .= ' OR order_name_of_item = "' . $rowContainingFoodData['name_of_item'] . '"';
		$rowContainingFoodData = mysqli_fetch_assoc( $resultContainingFoodData );		
	}
	
	$query .= ' )';
	$resultContainingOrders = mysqli_query( $db, $query );
?>
                  <tr>
                     <td><?php echo ucwords( $categoryOfFood ) ?></td>
                     <td><?php echo mysqli_num_rows( $resultContainingOrders ) ?></td>
                  </tr>

<?php
}

?>               </tbody>
            </table>
<?php
displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
?>