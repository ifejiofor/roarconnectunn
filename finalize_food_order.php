<?php
require_once 'includes/generalHeaderFile.php';
$query = 'SELECT vendor_name, vendor_email, user_id_of_vendor_manager FROM vendors WHERE vendor_id = ' . $_GET['idOfVendor'];
$resultContainingVendorData = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
$rowContainingVendorData = mysqli_fetch_assoc( $resultContainingVendorData );
$customizedStyleForBodyElement = 'background-image: url( \'assets/images/vendorFliers/' . $rowContainingVendorData['vendor_name'] . '.jpg\' ); background-size: cover;';
displayMarkupsCommonToTopOfPages( 'Order for ' . $_GET['category'], DISPLAY_NAVIGATION_MENU, 'confirm_food_order.php', $customizedStyleForBodyElement );


$request="SELECT `firstname`, `email`, `phone_number` FROM `users` WHERE `id`='".$_SESSION['user_id']."'";
if($request_new=mysqli_query($globalHandleToDatabase, $request)){
	$request_query=mysqli_fetch_array($request_new);
	$firstname=$request_query['firstname'];
	$email=$request_query['email'];
   $defaultPhoneNumber = $request_query['phone_number'];
}else{
	echo '<p id="errorMessage">Failed to create order.</p>';
}
?>

            <header id="minorHeaderType2">
               <h2><?php echo $rowContainingVendorData['vendor_name'] ?> Delivery Outlet on RoarConnect</h2>
               <p><a href="view_all_food_vendors.php">&lt;&lt; Click Here to go Back to the Food Delivery Marketplace</a></p>
            </header>

            <form action="confirm_food_order.php?category=<?php echo $_GET['category'] ?>&idOfVendor=<?php echo $_GET['idOfVendor'] ?>" method="POST" name="formForOrderingFood" class="form-horizontal" id="looksLikeACardboardPaper">
               <h3 id="mediumSizedText">This is the final step: Finalize your order for <?php echo isset( $_GET['idOfRequiredFood'] ) ? $nameOfRequiredFood : strtolower( $_GET['category'] ) ?>:</h3>

<?php
   if ( strtoupper( $rowContainingVendorData['vendor_name'] ) == 'CHEF D FOODS' ) {
?>
               <p id="boldSmallSizedRedText">Note that for off-campus deliveries, we can only deliver at designated locations such as: Hilltop Gate, Odim Gate, and Ejima Junction.</p>
<?php
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
displayMarkupsCommonToBottomOfPages(DISPLAY_FOOTER);
?>