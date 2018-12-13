<?php
require_once 'includes/generalHeaderFile.php';
define( 'MAXIMUM_QUANTITY_OF_DRINK', 20 );
define( 'DELIVERY_PRICE_WITHIN_CAMPUS', 100 );
define( 'DELIVERY_PRICE_OUTSIDE_CAMPUS', 200 );

if ( !currentUserIsLoggedIn() ) {
   header( 'Location: index.php' );
}

if ( !isset( $_GET['category'] ) || !isset( $_GET['idOfVendor'] ) ) {
   header( 'Location: index.php' );
}

if ( $_GET['category'] != 'Cake' && $_GET['category'] != 'Chicken' && $_GET['category'] != 'Pizza' && $_GET['category'] != 'Shawarma' &&
   $_GET['category'] != 'Rice' && $_GET['category'] != 'Swallow' && $_GET['category'] != 'Spaghetti' && $_GET['category'] != 'Pancake' &&
   $_GET['category'] != 'Yam' && $_GET['category'] != 'Fruit Salad' && $_GET['category'] != 'Indomie'&& 
   $_GET['category'] != 'Meat Pie' && $_GET['category'] != 'Red Velvet Cake' && $_GET['category'] != 'Birthday Cake' && $_GET['category'] != 'Burger' )
{
   header( 'Location: index.php' );
}

if ( !consistsOfOnlyDigits( $_GET['idOfVendor'] ) ) {
   header( 'Location: index.php' );
}

if ( isset( $_GET['idOfRequiredFood'] ) ) {
   if ( !consistsOfOnlyDigits( $_GET['idOfRequiredFood'] ) ) {
      header( 'Location: index.php' );
   }
   else {
      $query = 'SELECT name_of_item FROM photo_upload WHERE id_new = ' . $_GET['idOfRequiredFood'];
	  $result = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
	  $row = mysqli_fetch_assoc( $result );
	  $nameOfRequiredFood = $row['name_of_item'];
   }
}

$query = 'SELECT vendor_name, vendor_email, user_id_of_vendor_manager FROM vendors WHERE vendor_id = ' . $_GET['idOfVendor'];
$resultContainingVendorData = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
$rowContainingVendorData = mysqli_fetch_assoc( $resultContainingVendorData );
$customizedStyleForBodyElement = 'background-image: url( \'images/vendorFliers/' . $rowContainingVendorData['vendor_name'] . '.jpg\' ); background-size: cover;';


$time= time();
$actual_time= date('H:i:s', $time+18000);


if (  $actual_time >= 21 && strtoupper( $rowContainingVendorData['vendor_name'] ) != 'PIZZA PALACE' && strtoupper( $rowContainingVendorData['vendor_name'] ) != 'CHEF D FOODS' ) {
   displayMarkupsCommonToTopOfPages( 'Order for ' . $_GET['category'], DISPLAY_NAVIGATION_MENU, 'make_order_for_food.php', $customizedStyleForBodyElement );
	$actual_time= date('H:i:s', $time+18000);
?>
            <div id="containerHoldingErrorMessage">
               <h2>Sorry, we've closed for the day:</h2>
               <p>Servicing of orders starts from 3pm and ends 9pm daily.</p>
               <p>Current time is <span id="boldSmallSizedText"><?php echo $actual_time ?></span>. Please, make your order at appropriate time.</p>
            </div>
<?php
   displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
   exit( 0 );
}else if(  $actual_time < 15 && strtoupper( $rowContainingVendorData['vendor_name'] ) != 'PIZZA PALACE' ) {
   displayMarkupsCommonToTopOfPages( 'Order for ' . $_GET['category'], DISPLAY_NAVIGATION_MENU, 'make_order_for_food.php', $customizedStyleForBodyElement );
	$actual_time= date('H:i:s', $time+18000);
?>
            <div id="containerHoldingErrorMessage">
               <h2>Sorry, we are not yet open for the day:</h2>
               <p>Servicing of orders starts from 3pm and ends <?php echo strtoupper( $rowContainingVendorData['vendor_name'] ) == 'CHEF D FOODS' ? '8:00pm' : '9:00pm' ?> daily.</p>
               <p>Current time is <span id="boldSmallSizedText"><?php echo $actual_time ?></span>. Please, make your order at appropriate time.</p>
            </div>
<?php
   displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
   exit( 0 );
}


if  ( strtoupper( $rowContainingVendorData['vendor_name'] ) == 'PIZZA PALACE' ) {
	  $time= time();
	  $actual_time= date('D H:i:s', $time+18000);
	  $timer= date('H', $time+18000);
	  $dtime= date('D', $time+18000);
   if(  $timer < 12) {
   displayMarkupsCommonToTopOfPages( 'Order for ' . $_GET['category'], DISPLAY_NAVIGATION_MENU, 'make_order_for_food.php', $customizedStyleForBodyElement );
			$actual_time= date('D H:i:s', $time+18000);
?>			
            <div id="containerHoldingErrorMessage">
               <h2>Sorry, we are not yet open for the day:</h2>
               <p>Servicing of orders starts from 12pm and ends 8:00pm daily except Sundays.</p>
               <p>Current time is <span id="boldSmallSizedText"><?php echo $actual_time ?></span>. Please, make your order at appropriate time.</p>
            </div>
<?php
   displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
   exit( 0 );
}else if( $timer >= 20 ) {
   displayMarkupsCommonToTopOfPages( 'Order for ' . $_GET['category'], DISPLAY_NAVIGATION_MENU, 'make_order_for_food.php', $customizedStyleForBodyElement );
	$actual_time= date('D H:i:s', $time+18000);
?>
            <div id="containerHoldingErrorMessage">
               <h2>Sorry, we've closed for the day:</h2>
               <p>Servicing of orders starts from 12pm and ends 8:00pm daily except Sundays.</p>
               <p>Current time is <span id="boldSmallSizedText"><?php echo $actual_time ?></span>. Please, make your order at appropriate time.</p>
            </div>
<?php
   displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
   exit( 0 );
}
else if (  $dtime == 'Sat') {
   displayMarkupsCommonToTopOfPages( 'Order for ' . $_GET['category'], DISPLAY_NAVIGATION_MENU, 'make_order_for_food.php', $customizedStyleForBodyElement );
	$actual_time= date('D  H:i:s', $time+18000);
?>
            <div id="containerHoldingErrorMessage">
               <h2>Sorry, we've closed for the week:</h2>
               <p>We open from Monday to Saturday every week.</p>
               <p>Current time is <span id="boldSmallSizedText"><?php echo $actual_time ?></span>. Please, make your order at appropriate time.</p>
            </div>
<?php
   displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
   exit( 0 );
}
}


if ( strtoupper( $rowContainingVendorData['vendor_name'] ) == 'CHEF D FOODS' ) {
     //displayMarkupsCommonToTopOfPages( 'Order for ' . $_GET['category'], DISPLAY_NAVIGATION_MENU, 'make_order_for_food.php', $customizedStyleForBodyElement );
	$actual_time= date('H:i:s', $time+18000);
   $dtime= date('D', $time+18000);
   if($actual_time >= 20){
    displayMarkupsCommonToTopOfPages( 'Order for ' . $_GET['category'], DISPLAY_NAVIGATION_MENU, 'make_order_for_food.php', $customizedStyleForBodyElement );
?>
            <div id="containerHoldingErrorMessage">
               <h2>Sorry, we've closed for the day:</h2>
               <p>Servicing of orders starts from 3pm and ends 8:00pm daily.</p>
               <p>Current time is <span id="boldSmallSizedText"><?php echo $actual_time ?></span>. Please, make your order at appropriate time.</p>
            </div>
<?php
  displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
   exit( 0 );
   }else if (  $dtime == 'Sun') {
   displayMarkupsCommonToTopOfPages( 'Order for ' . $_GET['category'], DISPLAY_NAVIGATION_MENU, 'make_order_for_food.php', $customizedStyleForBodyElement );
	$actual_time= date('D  H:i:s', $time+18000);
?>
            <div id="containerHoldingErrorMessage">
               <h2>Sorry, we've closed for the week:</h2>
               <p>We open from Monday to Saturday every week.</p>
               <p>Current time is <span id="boldSmallSizedText"><?php echo $actual_time ?></span>. Please, make your order at appropriate time.</p>
            </div>
<?php
  displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
   exit( 0 );
   }
}


displayMarkupsCommonToTopOfPages( 'Order for ' . $_GET['category'], DISPLAY_NAVIGATION_MENU, 'make_order_for_food.php', $customizedStyleForBodyElement );

$userShouldViewForm = true;
$userShouldConfirmOrder = false;
$orderPlacementSuccessful = false;

if ( $_GET['category'] == '' ) {
   $typeOfFood = 'SNACK';
}
else {
   $typeOfFood = 'MAJOR FOOD';
}

if ( strtoupper( $rowContainingVendorData['vendor_name'] ) == 'CHEF D FOODS' &&
     ( strtoupper( $_GET['category'] ) == 'PIZZA' || strtoupper( $_GET['category'] ) == 'CAKE' || strtoupper( $_GET['category'] ) == 'CHICKEN' ||
     strtoupper( $_GET['category'] ) == 'BURGER' || strtoupper( $_GET['category'] ) == 'BIRTHDAY CAKE'|| strtoupper( $_GET['category'] ) == 'RED VELVET CAKE' || strtoupper( $_GET['category'] ) == 'MEAT PIE' ) )
 {
   $typeOfFood = 'SNACK';
}

$request="SELECT `firstname`, `email`, `phone_number` FROM `users` WHERE `id`='".$_SESSION['user_id']."'";
if($request_new=mysqli_query($globalHandleToDatabase, $request)){
	$request_query=mysqli_fetch_array($request_new);
	$firstname=$request_query['firstname'];
	$email=$request_query['email'];
   $defaultPhoneNumber = $request_query['phone_number'];
}else{
	echo '<p id="errorMessage">Failed to create order.</p>';
}		
								
if( $_POST ) {
	$nameOfFoodInputtedByUser= trim( htmlentities( $_POST['nameOfFood'] ) );
	$piecesInputtedByUser = trim(htmlentities ( $_POST['pieces'] ) );
	$nameOfDrinkInputtedByUser = trim( htmlentities ( $_POST['nameOfDrink'] ) );
	$quantityOfDrinkInputtedByUser = trim( htmlentities ( $_POST['quantityOfDrink'] ) );
	$deliveryLocationInputtedByUser = trim( htmlentities( $_POST['location'] ) );
	$deliveryAddressInputtedByUser = trim( htmlentities( $_POST['address'] ) );
	$phoneNumberInputtedByUser = trim( htmlentities( $_POST['phone'] ) );

   $namesOfDessertsInputtedByUser = [];
   $quantitiesOfDessertsInputtedByUser = [];

   if ( isset( $_POST['atLeastOneDessertWasSelectedByUser'] ) ) {
      $index = 0;

      while ( isset( $_POST['nameOfDessert' . $index] ) ) {
         $namesOfDessertsInputtedByUser[] = $_POST['nameOfDessert' . $index];
         $quantitiesOfDessertsInputtedByUser[] = $_POST['quantityOfDessert' . $index];
         ++$index;
      }
   }

	if(!empty( $deliveryLocationInputtedByUser ) && !empty( $piecesInputtedByUser ) && !empty( $phoneNumberInputtedByUser ) && !empty( $deliveryAddressInputtedByUser ) && !empty( $nameOfFoodInputtedByUser ) ) {
		if( $phoneNumberInputtedByUser > 7000000000 && $phoneNumberInputtedByUser < 9999999999 ) {
		    if ( !isset( $_POST['sizeOfPizza'] ) || $_POST['sizeOfPizza'] != '' ) {	
			    if( $piecesInputtedByUser < 99 && $piecesInputtedByUser > 0 ){
					
                    if ( !isset( $_POST['confirmation'] ) ) {
                        $userShouldViewForm = false;
                        $userShouldConfirmOrder = true;
                        $orderPlacementSuccessful = false;
                    }
                    else if ( $_POST['confirmation'] == 'Yes' ) {
                        $extraThingsAddedToOrder = '';

                        if ( $nameOfDrinkInputtedByUser != NULL ) {
                           $extraThingsAddedToOrder .= 'NAME: ' . $nameOfDrinkInputtedByUser . '; QUANTITIY: ' . $quantityOfDrinkInputtedByUser . '
						      ';
                        }

                        for ( $i = 0; $i < count( $namesOfDessertsInputtedByUser ); $i++ ) {
                        $extraThingsAddedToOrder .= ' | NAME: ' . $namesOfDessertsInputtedByUser[$i] . '; QUANTITIY: ' . $quantitiesOfDessertsInputtedByUser[$i] . '
						      ';
                        }

					    $insert = "INSERT INTO `orders` ( `order_name_of_item`, `order_quantity_of_item`, `order_miscellaneous_additions`, `order_total_price`, `order_delivery_location`, `order_delivery_address`, `order_phone_number_of_orderer`, `user_id_of_orderer`, `vendor_id` ) VALUES ( '$nameOfFoodInputtedByUser', '$piecesInputtedByUser', '$extraThingsAddedToOrder', '" . $_POST['totalPriceOfOrder'] . "', '$deliveryLocationInputtedByUser', '$deliveryAddressInputtedByUser', '$phoneNumberInputtedByUser','" . $_SESSION['user_id'] . "', " . $_GET['idOfVendor'] . ")";

					    if($check=mysqli_query($globalHandleToDatabase, $insert)){
							$totalprice=$_POST['totalPriceOfOrder'];
                     $deliveryPrice = $_POST['totalDeliveryPriceOfOrder'];
                     
                     $messageBody = '
                        <p>Hello,</p>
                        <p>You have an food order. Below are details of the order.</p>
                        <h1 style="font-family: sans-serif; font-size: 2em; color: #fff; background-color: #000066; padding: 5px;">Order Details</h1>

                        <table style="border-collapse: collapse; width: 100%;">
                           <tr style="font-family: sans-serif; font-size: 1em; color: #000; background-color: #f2f2f2;"><th style="text-align: left; padding: 8px;">NAME OF FOOD VENDOR: </th> <td style="text-align: left; padding: 8px;">' . $rowContainingVendorData['vendor_name'] . '</td></tr>
                           <tr style="font-family: sans-serif; font-size: 1em; color: #000;"><th style="text-align: left; padding: 8px;">FIRSTNAME OF ORDERER: </th> <td style="text-align: left; padding: 8px;">' . $firstname . '</td></tr>
                           <tr style="font-family: sans-serif; font-size: 1em; color: #000; background-color: #f2f2f2;"><th style="text-align: left; padding: 8px;">NAME OF FOOD: </th> <td style="text-align: left; padding: 8px;">' . $nameOfFoodInputtedByUser . ( isset( $_POST['sizeOfPizza'] ) ? ' (' . ucwords( $_POST['sizeOfPizza'] ) . ' size)' : '' ) . '</td></tr>
                           <tr style="font-family: sans-serif; font-size: 1em; color: #000;"><th style="text-align: left; padding: 8px;">QUANTITIY: </th> <td style="text-align: left; padding: 8px;">' . $piecesInputtedByUser . '</td></tr>
                           <tr style="font-family: sans-serif; font-size: 1em; color: #000; background-color: #f2f2f2;"><th style="text-align: left; padding: 8px;">EXTRAS</th> <td style="text-align: left; padding: 8px;">' . $extraThingsAddedToOrder.'</td></tr>
                           <tr style="font-family: sans-serif; font-size: 1em; color: #000;"><th style="text-align: left; padding: 8px;">Price of Food:</th> <td style="text-align: left; padding: 8px;">'. ( $totalprice - $deliveryPrice ) . '</td></tr>
                           <tr style="font-family: sans-serif; font-size: 1em; color: #000; background-color: #f2f2f2;"><th style="text-align: left; padding: 8px;">Price of Delivery:</th> <td style="text-align: left; padding: 8px;">'.$deliveryPrice . '</td></tr>
                           <tr style="font-family: sans-serif; font-size: 1em; color: #000;"><th style="text-align: left; padding: 8px;">Total Price:</th> <td style="text-align: left; padding: 8px;">'.$totalprice . '</td></tr>
                           <tr style="font-family: sans-serif; font-size: 1em; color: #000; background-color: #f2f2f2;"><th style="text-align: left; padding: 8px;">LOCATION</th> <td style="text-align: left; padding: 8px;">' . ucwords( $deliveryLocationInputtedByUser ) .'</td></tr>
                           <tr style="font-family: sans-serif; font-size: 1em; color: #000;"><th style="text-align: left; padding: 8px;">ADDRESS</th> <td style="text-align: left; padding: 8px;">' . $deliveryAddressInputtedByUser .'</td></tr>
                           <tr style="font-family: sans-serif; font-size: 1em; color: #000; background-color: #f2f2f2;"><th style="text-align: left; padding: 8px;">PHONE NUMBER</th> <td style="text-align: left; padding: 8px;">' . $phoneNumberInputtedByUser .'</td></tr>
                           </table>';

                     $query = 'INSERT INTO messages( message_title, message_body, user_id_of_recipient, message_time_of_sending ) VALUES( "Food Order", "' . mysqli_real_escape_string( $globalHandleToDatabase, $messageBody ) . '", ' . $rowContainingVendorData['user_id_of_vendor_manager'] . ', NOW() )';
                     mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
                     
                     include 'sendmailorder.php';
                      include 'foodconfimationmail.php';
						   $userShouldViewForm = false;
                     $userShouldConfirmOrder = false;
                     $orderPlacementSuccessful = true;	
					    }else{
						    echo '<p id="errorMessage">Unsuccessful Order</p>';
					    }
                    }
		        }else{
				    echo '<p id="errorMessage">You cannot request less than 1 plates or more than 98 plates.</p>';
		        }
			}
			else {
				echo '<p id="errorMessage">Please, select the size of pizza</p>';
			}

		}else{
			echo '<p id="errorMessage">Enter a valid phone number.</p>';
		}			
	}else{
		echo '<p id="errorMessage">Some fields are blank.</p>';
	}
}

$query = 'SELECT vendor_name FROM vendors WHERE vendor_id = ' . $_GET['idOfVendor'];
$resultContainingVendorData = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
$rowContainingVendorData = mysqli_fetch_assoc( $resultContainingVendorData );

if ( $userShouldViewForm ) {
?>

            <header id="minorHeaderType2">
               <h2><?php echo $rowContainingVendorData['vendor_name'] ?> Delivery Outlet on RoarConnect</h2>
               <p><a href="view_all_food_vendors.php">&lt;&lt; Click Here to go Back to the Food Delivery Marketplace</a></p>
            </header>

            <form action="make_order_for_food.php?category=<?php echo $_GET['category'] ?>&idOfVendor=<?php echo $_GET['idOfVendor'] ?>" method="POST" name="formForOrderingFood" class="form-horizontal" id="looksLikeACardboardPaper">
               <h3 id="mediumSizedText">Fill the form below to order for <?php echo isset( $_GET['idOfRequiredFood'] ) ? $nameOfRequiredFood : strtolower( $_GET['category'] ) ?>:</h3>

<?php
   if ( strtoupper( $rowContainingVendorData['vendor_name'] ) == 'CHEF D FOODS' ) {
?>
               <p id="boldSmallSizedRedText">Note that for off-campus deliveries, we can only deliver at designated locations such as: Hilltop Gate, Odim Gate, and Ejima Junction.</p>
<?php
   }

   if ( strtoupper( $rowContainingVendorData['vendor_name'] ) == 'PIZZA PALACE' && $_GET['category'] == 'Pizza' ) {
	  

?>
               <div class="form-group">
			      <label class="control-label col-sm-2">Select Size of Pizza:</label>
				  <div class="col-sm-10">
				     <select name="sizeOfPizza" class="form-control">
				        <option value="">---</option>
				        <option value="small" <?php isset( $_POST['sizeOfPizza'] ) && $_POST['sizeOfPizza'] == 'small'  ? 'selected' : '' ?>>Small Size</option>
				        <option value="medium" <?php isset( $_POST['sizeOfPizza'] ) && $_POST['sizeOfPizza'] == 'medium'  ? 'selected' : '' ?>>Medium Size</option>
				        <option value="large" <?php isset( $_POST['sizeOfPizza'] ) && $_POST['sizeOfPizza'] == 'large'  ? 'selected' : '' ?>>Large Size</option>
				     </select>
				  </div>
			   </div>
<?php
   }

   if ( isset( $_GET['idOfRequiredFood'] ) ) {
?>
               <input type="hidden" name="nameOfFood" value="<?php echo $nameOfRequiredFood ?>" />
<?php
   }
   else {
      $query = 'SELECT name_of_item, price FROM photo_upload WHERE category = "' . $_GET['category'] . '" AND people_id ="VENDOR_' . $_GET['idOfVendor'] . '" AND checks ="APPROVED"';
      $result = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );

	  if ( mysqli_num_rows( $result ) == 1 ) {
         $row = mysqli_fetch_assoc( $result );
?>
               <input type="hidden" name="nameOfFood" value="<?php echo $row['name_of_item'] ?>" />
<?php
      }
      else {
?>
               <fieldset class="form-group">
                  <legend class="control-label col-sm-2" id="boldSmallSizedText">Select the type of <?php echo strtolower( $_GET['category'] ) ?> you want:</legend>
                  <div class="col-sm-10">
                     <select name="nameOfFood" class="form-control">
                        <option value="">---</option>
<?php
         $row = mysqli_fetch_assoc( $result );
         while ( $row != NULL ) {
?>
                        <option value="<?php echo $row['name_of_item'] ?>" <?php echo isset( $_POST['nameOfFood'] ) && $_POST['nameOfFood'] == $row['name_of_item'] ? 'selected' : '' ?>><?php echo $row['name_of_item'] ?></option>
<?php
            $row = mysqli_fetch_assoc( $result );
         }

?>
                     </select>
                  </div>
               </fieldset>
<?php
      }
   }
   
   
   if ( strtoupper( $rowContainingVendorData['vendor_name'] ) == 'CHEF D FOODS' && strtoupper( $_GET['category'] ) == 'RED VELVET CAKE' ) {
      $textToDisplayInLabelForNumberOfPieces = 'Enter number of layers:';
   }
   else if ( $typeOfFood == 'MAJOR FOOD' ) {
      $textToDisplayInLabelForNumberOfPieces = 'Enter quantity:';
   }
   else { // typeOfFood is equal to 'SNACK'
      $textToDisplayInLabelForNumberOfPieces = 'How many pieces do you need?';
   }
?>

               <div class="form-group" id="mediumSizedBottomMargin">
                  <label for="numberOfPieces" class="control-label col-sm-2"><?php echo $textToDisplayInLabelForNumberOfPieces ?></label>
                  <div class="col-sm-10"><input type="number" name="pieces" value="<?php echo isset( $_POST['pieces'] ) ? $_POST['pieces'] : '' ?>" class="form-control" id="numberOfPieces" maxlength="2"/></div>
               </div>
<?php

   if ( $typeOfFood == 'MAJOR FOOD' ) {
      $query = 'SELECT name_of_item, price FROM photo_upload WHERE category = "dessert" AND people_id ="VENDOR_' . $_GET['idOfVendor'] . '" AND checks ="APPROVED"';
      $result = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );

      if ( mysqli_num_rows( $result ) > 0 ) { // The required vendor also delivers desserts
?>
               <div id="mediumSizedBottomMargin"><fieldset id="sectionOfFormMeantForAllowingUserToSelectDessert"></fieldset></div>

               <script>
                  <!--
<?php
         $row = mysqli_fetch_assoc( $result );

         while ( $row != NULL ) {
?>
                  namesOfAllDessertsSoldByVendor.push( '<?php echo $row['name_of_item'] ?>' );
<?php
            $row = mysqli_fetch_assoc( $result );
         }

         if ( isset( $_POST['atLeastOneDessertWasSelectedByUser'] ) ) {
?>
                  var namesOfDessertsInputtedByUser = [];
                  var quantitiesOfDessertsInputtedByUser = [];
<?php
            for ( $i = 0; $i < count( $namesOfDessertsInputtedByUser ); $i++ ) {
?>
                  namesOfDessertsInputtedByUser[<?php echo $i ?>] = '<?php echo $namesOfDessertsInputtedByUser[$i] ?>';
                  quantitiesOfDessertsInputtedByUser[<?php echo $i ?>] = '<?php echo $quantitiesOfDessertsInputtedByUser[$i] ?>';
<?php
            }
?>
                  displayDessertsPreviouslySelectedByUser( namesOfDessertsInputtedByUser, quantitiesOfDessertsInputtedByUser );
<?php
         }
         else {
?>
                  displayButtonForSelectingAnInitialDessert();
<?php
         }
?>
                  -->
               </script>
<?php
      }


      $query = 'SELECT name_of_item, price FROM photo_upload WHERE category = "drink" AND people_id ="VENDOR_' . $_GET['idOfVendor'] . '" AND checks ="APPROVED"';
      $result = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );

      if ( mysqli_num_rows( $result ) > 0 ) {
?>

               <fieldset class="form-group" id="mediumSizedBottomMargin">
                  <legend class="text-center" id="boldSmallSizedText">(Optional) Will you like to add drinks to your order?</legend>

                  <div class="col-sm-6">
                     <label for="nameOfDrink" class="col-sm-6 control-label">Select drink:</label>
                     <div class="col-sm-6">
                        <select name="nameOfDrink" class="form-control" id="nameOfDrink">
                           <option value="">---</option>
<?php
         $row = mysqli_fetch_assoc( $result );

         while ( $row != NULL ) {
?>
                           <option value="<?php echo $row['name_of_item'] ?>" <?php echo isset( $_POST['nameOfDrink'] ) && $_POST['nameOfDrink'] == $row['name_of_item'] ? 'selected': '' ?>><?php echo $row['name_of_item'] ?></option>
<?php
            $row = mysqli_fetch_assoc( $result );
         }
?>
                        </select>
                     </div>
                  </div>

                  <div class="col-sm-6">
                     <label for="quantityOfDrink" class="col-sm-6 control-label">Select quantity:</label>
                     <div class="col-sm-6">
                        <select name="quantityOfDrink" class="form-control" id="quantityOfDrink">
                           <option value="0">---</option>
<?php
         for ( $i = 1; $i <= MAXIMUM_QUANTITY_OF_DRINK; $i++ ) {
?>
                           <option value="<?php echo $i ?>" <?php echo isset( $_POST['quantityOfDrink'] ) && $_POST['quantityOfDrink'] == $i ? 'selected': '' ?>><?php echo $i ?></option>
<?php
         }
?>
                        </select>
                     </div>
                  </div>
               </fieldset>
<?php
      }
   }
?>

               <fieldset class="form-group">
                  <legend class="control-label col-sm-2" id="boldSmallSizedText">Where is your preferred delivery location?</legend>
                  <div class="col-sm-10">
                     <div>
                        <input type="radio" name="location" id="withinCampus" value="within campus" <?php echo isset( $_POST['location'] ) && $_POST['location'] == 'within campus' ? ' checked': '' ?> />
                        <label for="withinCampus"> <?php if(strtoupper( $rowContainingVendorData['vendor_name'] ) != 'CHEF D FOODS'){?> Within Campus<?php }else{?>Girls Hostels <?php }?> </label>
                     </div>
<?php
   if (strtoupper( $rowContainingVendorData['vendor_name'] ) == 'CHEF D FOODS') {
?>
                     <div>
                        <input type="radio" name="location" id="otherPlacesWithinCampus" value="outside campus" />
                        <label for="otherPlacesWithinCampus"> Other Places Within Campus (including Nkrumah)</label>
                     </div>
<?php
   }
?>
                     <div>
                        <input type="radio" name="location" id="others" value="outside campus" <?php echo isset( $_POST['location'] ) && $_POST['location'] == 'outside campus' ? ' checked': '' ?> />
                        <label for="others"> Outside Campus</label>
                     </div>
                  </div>
               </fieldset>

               <div class="form-group">
                  <label for="address" class="control-label col-sm-2">Address of delivery location:</label>
                  <div class="col-sm-10"><input type="text" name="address" class="form-control" id="address" maxlength="70" placeholder="E.g., Room 20, Instagram Lodge, Hilltop" value="<?php echo isset( $_POST['address'] ) ? $_POST['address']: '' ?>" /></div>
               </div>

               <div class="form-group">
                  <label for="phoneNumber" class="control-label col-sm-2">Your phone number:</label>
                  <div class="col-sm-10"><input type="text" name="phone" value="<?php echo isset( $_POST['phone'] ) ? $_POST['phone'] : $defaultPhoneNumber ?>" class="form-control" id="phoneNumber" maxlength="11"/></div>
               </div>

               <div class="form-group">
                  <div class="col-sm-offset-2 col-sm-10"><input type="submit" name="Order" value="Order" class="btn btn-success"/></div>
               </div>
            </form>
<?php
}
else if ( $userShouldConfirmOrder ) {
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
   if ( strtoupper( $rowContainingVendorData['vendor_name'] ) == 'PIZZA PALACE' ) {
      $deliveryPrice = DELIVERY_PRICE_OUTSIDE_CAMPUS;
   }
   
   // The delivery price for "Chef D Foods" is equal to DELIVERY_PRICE_WITHIN_CAMPUS irrespective of the delivery location
   if ( strtoupper( $rowContainingVendorData['vendor_name'] ) == 'CHEF D FOODS' && $deliveryLocationInputtedByUser == 'within campus') {
      $deliveryPrice = DELIVERY_PRICE_WITHIN_CAMPUS;
   }else if( strtoupper( $rowContainingVendorData['vendor_name'] ) == 'CHEF D FOODS' && $deliveryLocationInputtedByUser == 'outside campus') {
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
               <h3><?php echo $rowContainingVendorData['vendor_name'] ?> Order</h3>

               <div  id="smallContainerWithoutBorder">
                  <?php echo $detailedBreakdownOfOrder . $alternateFormatOfDetailedBreakdownOfOrderForVerySmallScreens ?>

               </div>

               <form method="POST" action="make_order_for_food.php?category=<?php echo $_GET['category'] ?>&idOfVendor=<?php echo $_GET['idOfVendor'] ?>">
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
}
else if ( $orderPlacementSuccessful ) {
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
}

displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
?>