<?php
require_once 'includes/generalHeaderFile.php';
define( 'MAXIMUM_QUANTITY_OF_DRINK', 20 );
define( 'DELIVERY_PRICE_WITHIN_CAMPUS', 100 );
define( 'DELIVERY_PRICE_OUTSIDE_CAMPUS', 200 );

$nameOfCurrentVendor = getNameOfCurrentVendor();
$customizedStyleForBodyElement = 'background-image: url( \'assets/images/vendorFliers/' . $nameOfCurrentVendor . '.jpg\' ); background-size: cover;';
displayMarkupsCommonToTopOfPages( 'Order for ' . $_GET['category'], DISPLAY_NAVIGATION_MENU, 'confirm_food_order.php', $customizedStyleForBodyElement );

// TODO: The following initializations are just dummy values
$nameOfFoodInputtedByUser = 'Birthday Cake';
$piecesInputtedByUser = 1;
$namesOfDessertsInputtedByUser = array();
$nameOfDrinkInputtedByUser = NULL;
$deliveryLocationInputtedByUser = NULL;
$quantityOfDrinkInputtedByUser = 0;
$deliveryAddressInputtedByUser = '';
$phoneNumberInputtedByUser = '';

   $detailedBreakdownOfOrder = '
                  <table class="table text-right" id="disappearOnVerySmallScreens">
                     <thead>
                        <tr>
                           <th class="text-right" id="disappearOnSmallScreens">S/N</th>
                           <th class="text-left">Description of Item</th>
                           <th class="text-right">Price Per Quantity</th>
                           <th class="text-right">Quantity</th>
                           <th class="text-right">Subtotal</th>
                        </tr>
                     </thead>

                     <tbody>
   ';

   $query = 'SELECT price FROM photo_upload WHERE name_of_item = "' . $nameOfFoodInputtedByUser . '" AND category = "' . $_GET['category'] . '" AND people_id ="VENDOR_' . $_GET['idOfVendor'] . '" AND checks ="APPROVED"';
   $result = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
   $row = mysqli_fetch_assoc( $result );
   $pricePerQuantityOfFood = $row['price'];
   
   if ( isset( $_POST['sizeOfPizza'] ) ) {
	   if ( $_POST['sizeOfPizza'] == 'small' ) {
		   $pricePerQuantityOfFood = 1500;
	   }
	   else if ( $_POST['sizeOfPizza'] == 'medium' ) {
		   $pricePerQuantityOfFood = 2800;
	   }
	   else if ( $_POST['sizeOfPizza'] == 'large' ) {
		   $pricePerQuantityOfFood = 3500;
	   }
   }
   
   $totalPriceOfOrder = $pricePerQuantityOfFood * $piecesInputtedByUser;
   $serialNumber = 1;

   $detailedBreakdownOfOrder .= '
                        <tr>
                           <td id="disappearOnSmallScreens">' . $serialNumber . '</td>
                           <td class="text-left">' . $nameOfFoodInputtedByUser . ( isset( $_POST['sizeOfPizza'] ) ? ' (' . ucwords( $_POST['sizeOfPizza'] ) . ' size)' : '' ) . '</td>
                           <td>' . $pricePerQuantityOfFood . '</td>
                           <td>' . $piecesInputtedByUser . '</td>
                           <td>' . $pricePerQuantityOfFood * $piecesInputtedByUser . '</td>
                        </tr>
   ';

   $alternateFormatOfDetailedBreakdownOfOrderForVerySmallScreens = '
                  <div id="appearOnVerySmallScreens">
                     <table class="table">
                        <tr>
                           <th colspan=3>' . $nameOfFoodInputtedByUser . '</th>
                        </tr>
                        <tr>
                           <td class="text-left">Price Per Quantity:</td>
                           <td class="text-right">' . $pricePerQuantityOfFood . '</td>
                           <td></td>
                        </tr>
                        <tr>
                           <td class="text-left">Quantity:</td>
                           <td class="text-right"><span id="floatedToTheLeft">&times;</span>' . $piecesInputtedByUser . '</td>
                           <td></td>
                        </tr>
                        <tr>
                           <td class="text-left">Subtotal:</td>
                           <td></td>
                           <td class="text-right">' . $pricePerQuantityOfFood * $piecesInputtedByUser . '</td>
                        </tr>
                     </table>
   ';

   ++$serialNumber;

   for ( $i = 0; $i < count( $namesOfDessertsInputtedByUser ); $i++ ) {
      $nameOfDessert = $namesOfDessertsInputtedByUser[$i];
      $quantityOfDessert = $quantitiesOfDessertsInputtedByUser[$i];

      if ( $nameOfDessert != NULL ) {
         $query = 'SELECT price FROM photo_upload WHERE name_of_item = "' . $nameOfDessert . '" AND category = "dessert" AND people_id ="VENDOR_' . $_GET['idOfVendor'] . '" AND checks ="APPROVED"';
         $result = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
         $row = mysqli_fetch_assoc( $result );
         $pricePerQuantityOfDessert = $row['price'];
         $totalPriceOfOrder += $pricePerQuantityOfDessert * $quantityOfDessert;

         $detailedBreakdownOfOrder .= '
                        <tr>
                           <td id="disappearOnSmallScreens">' . $serialNumber . '</td>
                           <td class="text-left">' . $nameOfDessert . '</td>
                           <td>' . $pricePerQuantityOfDessert . '</td>
                           <td>' . $quantityOfDessert . '</td>
                           <td>' . $pricePerQuantityOfDessert * $quantityOfDessert . '</td>
                        </tr>
         ';

         $alternateFormatOfDetailedBreakdownOfOrderForVerySmallScreens .= '
                     <table class="table">
                        <tr>
                           <th colspan=3>' . $nameOfDessert . '</th>
                        </tr>
                        <tr>
                           <td class="text-left">Price Per Quantity:</td>
                           <td class="text-right">' . $pricePerQuantityOfDessert . '</td>
                           <td></td>
                        </tr>
                        <tr>
                           <td class="text-left">Quantity:</td>
                           <td class="text-right"><span id="floatedToTheLeft">&times;</span>' . $quantityOfDessert . '</td>
                           <td></td>
                        </tr>
                        <tr>
                           <td class="text-left">Subtotal:</td>
                           <td></td>
                           <td class="text-right">' . $pricePerQuantityOfDessert * $quantityOfDessert . '</td>
                        </tr>
                     </table>
         ';

         ++$serialNumber;
      }
   }

   if ( $nameOfDrinkInputtedByUser != NULL ) {
      $query = 'SELECT price FROM photo_upload WHERE name_of_item = "' . $nameOfDrinkInputtedByUser . '" AND category = "drink" AND people_id ="VENDOR_' . $_GET['idOfVendor'] . '" AND checks ="APPROVED"';
      $result = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
      $row = mysqli_fetch_assoc( $result );
      $pricePerQuantityOfDrink = $row['price'];
      $totalPriceOfOrder += $pricePerQuantityOfDrink * $quantityOfDrinkInputtedByUser;

      $detailedBreakdownOfOrder .= '
                        <tr>
                           <td id="disappearOnSmallScreens">' . $serialNumber . '</td>
                           <td class="text-left">' . $nameOfDrinkInputtedByUser . '</td>
                           <td>' . $pricePerQuantityOfDrink . '</td>
                           <td>' . $quantityOfDrinkInputtedByUser . '</td>
                           <td>' . $pricePerQuantityOfDrink * $quantityOfDrinkInputtedByUser . '</td>
                        </tr>
      ';

      $alternateFormatOfDetailedBreakdownOfOrderForVerySmallScreens .= '
                     <table class="table">
                        <tr>
                           <th colspan=3>' . $nameOfDrinkInputtedByUser . '</th>
                        </tr>
                        <tr>
                           <td class="text-left">Price Per Quantity:</td>
                           <td class="text-right">' . $pricePerQuantityOfDrink . '</td>
                           <td></td>
                        </tr>
                        <tr>
                           <td class="text-left">Quantity:</td>
                           <td class="text-right"><span id="floatedToTheLeft">&times;</span>' . $quantityOfDrinkInputtedByUser . '</td>
                           <td></td>
                        </tr>
                        <tr>
                           <td class="text-left">Subtotal:</td>
                           <td></td>
                           <td class="text-right">' . $pricePerQuantityOfDrink * $quantityOfDrinkInputtedByUser . '</td>
                        </tr>
                     </table>
      ';

      ++$serialNumber;
   }

   $deliveryPrice = $deliveryLocationInputtedByUser == 'within campus' ? DELIVERY_PRICE_WITHIN_CAMPUS : DELIVERY_PRICE_OUTSIDE_CAMPUS;
   
   // The delivery price for "PIZZA PALACE" is equal to DELIVERY_PRICE_OUTSIDE_CAMPUS irrespective of the delivery location
   if ( strtoupper( $nameOfCurrentVendor ) == 'PIZZA PALACE' ) {
      $deliveryPrice = DELIVERY_PRICE_OUTSIDE_CAMPUS;
   }
   
   // The delivery price for "Chef D Foods" is equal to DELIVERY_PRICE_WITHIN_CAMPUS irrespective of the delivery location
   if ( strtoupper( $nameOfCurrentVendor ) == 'CHEF D FOODS' && $deliveryLocationInputtedByUser == 'within campus') {
      $deliveryPrice = DELIVERY_PRICE_WITHIN_CAMPUS;
   }else if( strtoupper( $nameOfCurrentVendor ) == 'CHEF D FOODS' && $deliveryLocationInputtedByUser == 'outside campus') {
      $deliveryPrice = 150;
   }
   
   $detailedBreakdownOfOrder .= '
                        <tr>
                           <td id="disappearOnSmallScreens"></td>
                           <td></td>
                           <td></td>
                           <td></td>
                           <td></td>
                        </tr>
                     </tbody>

                     <tfoot>
                        <tr>
                           <td colspan="3" class="text-right" id="boldSmallSizedText">Total:</td>
                           <td id="disappearOnSmallScreens"></td>
                           <td class="text-right">' . $totalPriceOfOrder . '</td>
                        </tr>
                        <tr>
                           <td id="disappearOnSmallScreens"></td>
                           <td class="text-left" id="boldSmallSizedText">Delivery Price:</td>
                           <td class="text-right">' . $deliveryPrice . '</td>
                           <td>' . $piecesInputtedByUser . '</td>
                           <td>' . ( $deliveryPrice * $piecesInputtedByUser ) . '</td>
                        </tr>
                        <tr>
                           <td colspan="3" class="text-right" id="boldSmallSizedText">Final Total:</td>
                           <td id="disappearOnSmallScreens"></td>
                           <td class="text-right">' . ( $totalPriceOfOrder + ( $deliveryPrice * $piecesInputtedByUser ) ) . '</td>
                        </tr>
                     </tfoot>
                  </table>
   ';

   $alternateFormatOfDetailedBreakdownOfOrderForVerySmallScreens .= '
                     <table class="table">
                        <tr>
                           <th colspan=2>Total:</th>
                           <th class="text-right">' . $totalPriceOfOrder . '</th>
                        </tr>
                        <tr>
                           <th>Delivery Price Per Quantity:</th>
                           <th class="text-right">' . $deliveryPrice . '</th>
                           <th></th>
                        </tr>
                        <tr>
                           <th>Quantity:</th>
                           <th class="text-right"><span id="floatedToTheLeft">&times;</span>' . $piecesInputtedByUser . '</th>
                           <th></th>
                        </tr>
                        <tr>
                           <th colspan="2">Total Delivery Price:</th>
                           <th class="text-right">' . ( $deliveryPrice * $piecesInputtedByUser ) . '</th>
                        </tr>
                        <tr>
                           <th colspan="2">Final Total:</th>
                           <th class="text-right">' . ( $totalPriceOfOrder + ( $deliveryPrice * $piecesInputtedByUser ) ) . '</th>
                        </tr>
                     </table>
                  </div>
   ';

   $totalPriceOfOrder += $deliveryPrice * $piecesInputtedByUser;
?>
            <div id="containerHoldingErrorMessage">
               <h3><?php echo $nameOfCurrentVendor ?> Order</h3>

               <div class="container-fluid">
                  <?php echo $detailedBreakdownOfOrder . $alternateFormatOfDetailedBreakdownOfOrderForVerySmallScreens ?>

               </div>

               <form method="POST" action="confirm_food_order.php?category=<?php echo $_GET['category'] ?>&idOfVendor=<?php echo $_GET['idOfVendor'] ?>">
                  <input type="hidden" name="totalPriceOfOrder" value="<?php echo $totalPriceOfOrder ?>">
                  <input type="hidden" name="totalDeliveryPriceOfOrder"  value="<?php echo $deliveryPrice * $piecesInputtedByUser ?>" />
                  <input type="hidden" name="detailedBreakdownOfOrder" value='<?php echo $detailedBreakdownOfOrder ?>'>
                  <input type="hidden" name="alternateFormatOfDetailedBreakdownOfOrderForVerySmallScreens" value='<?php echo $alternateFormatOfDetailedBreakdownOfOrderForVerySmallScreens ?>'>

                  <input type="hidden" name="nameOfFood" value="<?php echo $nameOfFoodInputtedByUser ?>" />
                  <?php echo isset( $_POST['sizeOfPizza'] ) ? '<input type="hidden" name="sizeOfPizza" value="' . $_POST['sizeOfPizza'] . '" />' : '' ?>
                  <input type="hidden" name="pieces" value="<?php echo $piecesInputtedByUser ?>" />
                  <input type="hidden" name="nameOfDrink" value="<?php echo $nameOfDrinkInputtedByUser ?>" />
                  <input type="hidden" name="quantityOfDrink" value="<?php echo $quantityOfDrinkInputtedByUser ?>" />
                  <input type="hidden" name="location" value="<?php echo $deliveryLocationInputtedByUser ?>" />
                  <input type="hidden" name="address" value="<?php echo $deliveryAddressInputtedByUser ?>" />
                  <input type="hidden" name="phone" value="<?php echo $phoneNumberInputtedByUser ?>" />
<?php
   if ( isset( $_POST['atLeastOneDessertWasSelectedByUser'] ) ) {

      for ( $i = 0; $i < count( $namesOfDessertsInputtedByUser ); $i++ ) {
?>
                  <input type="hidden" name="nameOfDessert<?php echo $i ?>" value="<?php echo $namesOfDessertsInputtedByUser[$i] ?>" />
                  <input type="hidden" name="quantityOfDessert<?php echo $i ?>" value="<?php echo $quantitiesOfDessertsInputtedByUser[$i] ?>" />
<?php
      }
?>
                  <input type="hidden" name="atLeastOneDessertWasSelectedByUser" value="true" />
<?php
   }
?>

                  <p>Will you like to continue with this order?</p>
                  <input type="submit" name="confirmation" value="Yes" class="btn btn-warning btn-lg" />
                  <input type="submit" name="confirmation" value="No" class="btn btn-warning btn-lg" />
               </form>
            </div>
<?php
displayMarkupsCommonToBottomOfPages(DISPLAY_FOOTER);
?>