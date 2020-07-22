<?php
require_once 'includes/generalHeaderFile.php';
$query = 'SELECT vendor_name, vendor_email, user_id_of_vendor_manager FROM vendors WHERE vendor_id = ' . $_GET['idOfVendor'];
$resultContainingVendorData = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
$rowContainingVendorData = mysqli_fetch_assoc( $resultContainingVendorData );
$customizedStyleForBodyElement = 'background-image: url( \'assets/images/vendorFliers/' . $rowContainingVendorData['vendor_name'] . '.jpg\' ); background-size: cover;';

displayMarkupsCommonToTopOfPages( 'Order for ' . $_GET['category'], DISPLAY_NAVIGATION_MENU, 'confirm_food_order.php', $customizedStyleForBodyElement );
$nameOfFoodInputtedByUser = 'Birthday Cake';
$_POST['detailedBreakdownOfOrder'] = 'detailedBreakdownOfOrder';
$_POST['alternateFormatOfDetailedBreakdownOfOrderForVerySmallScreens'] = 'alternateFormatOfDetailedBreakdownOfOrderForVerySmallScreens';
?>
            <div id="containerHoldingSuccessMessage">
               <h3>Order Placement Successful!</h3>
               <p>You have successfully placed an order for "<?php echo $nameOfFoodInputtedByUser ?>".</p>
               <p>Very soon our delivery agents will be at your doorstep with your package.</p>
               <p>Meanwhile, below is your order invoice. Please save this page, or screenshot it, as it is the temporary confirmation of your order.</p>

               <div id="looksLikeACardboardPaper">
                  <h3 id="boldMediumSizedText"><?php echo $rowContainingVendorData['vendor_name'] ?> Order Invoice</h3>
                  <?php echo $_POST['detailedBreakdownOfOrder'] . $_POST['alternateFormatOfDetailedBreakdownOfOrderForVerySmallScreens'] ?>

                  <p id="smallSizedText"><span>NB:</span> Payment is on a cash-on-delivery basis only. We do not accept POS or online transfers.</p>
               </div>

               <p id="smallSizedText"><a href="view_all_food_vendors.php">&lt;&lt; Click here to go back to the Food Delivery Marketplace</a>.</p>
            </div>
<?php
displayMarkupsCommonToBottomOfPages(DISPLAY_FOOTER);
?>