<?php
/*
   NB: In this file, $_GET['i'] holds the id of the current vendor
*/
include_once 'includes/generalHeaderFile.php';
define('THRESHOLD_NUMBER_OF_FOODS', 7);

if ( !isset( $_GET['i'] ) || !consistsOfOnlyDigits( $_GET['i'] ) ) {
   header( 'Location: view_all_food_vendors.php' );
}

$query = 'SELECT name_of_item, price, category, image_size FROM photo_upload WHERE people_id = "VENDOR_' . $_GET['i'] . '" AND checks = "APPROVED" AND category != "dessert" AND category != "drink" ORDER BY category, name_of_item';
$resultContainingFoodData = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
$numberOfFoodsSoldByCurrentVendor = mysqli_num_rows( $resultContainingFoodData );

if ( $numberOfFoodsSoldByCurrentVendor == 0 ) {
   header( 'Location: view_all_food_vendors.php' );
}

$nameOfCurrentVendor = getNameOfCurrentVendor();
$customizedStyleForBodyElement = 'background-image: url( \'assets/images/vendorFliers/' . $nameOfCurrentVendor . '.jpg\' ); background-size: contain;';
displayMarkupsCommonToTopOfPages( 'Choose Type of Food', DISPLAY_NAVIGATION_MENU, 'choose_category_of_food.php', $customizedStyleForBodyElement );
?>
            <div id="minorHeader">
               <h2>What do you want to eat?</h2>
               <p>You are at <?php echo formatNameAsPossessive($nameOfCurrentVendor) ?> Outlet on RoarConnect</p>
            </div>

            <div <?php echo $numberOfFoodsSoldByCurrentVendor < THRESHOLD_NUMBER_OF_FOODS ? 'class="row"' : '' ?>>
<?php
$lastDisplayedCategory = NULL;

for ($rowContainingFoodData = mysqli_fetch_assoc($resultContainingFoodData); $rowContainingFoodData != NULL; $rowContainingFoodData = mysqli_fetch_assoc($resultContainingFoodData)) {
   if ($numberOfFoodsSoldByCurrentVendor >= THRESHOLD_NUMBER_OF_FOODS && $rowContainingFoodData['category'] != $lastDisplayedCategory) {
      if ($lastDisplayedCategory != NULL) {
?>
                  </div>
               </section>

<?php
      }
?>
               <section id="wideGenericSection">
                  <h3 id="nonOverflowedHeading"><?php echo ucwords($rowContainingFoodData['category']) ?></h3>
                  <div class="row">
<?php
      $lastDisplayedCategory = $rowContainingFoodData['category'];
   }
?>
                     <div class="col-sm-6">
                        <div class="container-fluid" id="foodSummaryContainer">
                           <img src="<?php echo 'assets/images/photoUploads/VENDOR_' . $_GET['i'] . '@' . $rowContainingFoodData['image_size'] ?>" width="auto" height="80px" />
                           <h4 id="heading"><?php echo ucwords( $rowContainingFoodData['name_of_item'] ) ?></h4>
                           <p id="subDetails"><span style="text-decoration: line-through">N</span><?php echo formatNumberAsCommaSeparated( $rowContainingFoodData['price'] ) ?></p>
                           <form method="POST" action="confirm_food_order.php?category=<?php echo ucwords( $rowContainingFoodData['category'] ) ?>&idOfVendor=<?php echo $_GET['i'] ?>">
                              <input type="hidden" name="nameOfFood" value="<?php echo $rowContainingFoodData['name_of_item'] ?>">
                              <input type="hidden" name="pieces" value="1">
                              <button type="submit" class="btn btn-default pull-right">Order Now</button>
                           </form>
                        </div>
                     </div>
<?php
}

if ($lastDisplayedCategory != NULL) {
?>
                  </div>
               </section>
<?php
}
?>

               <div class="container-fluid"><a href="view_all_food_vendors.php"><span class="fa fa-angle-double-left"></span> Back to Food Delivery Marketplace</a></div>
            </div>
<?php   
displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
?>