<?php
require_once 'includes/generalHeaderFile.php';

if( !userIsLoggedIn() ){
   header('Location: index.php');
}

if ( !isset( $_GET['category'] ) ) {
   header( 'Location:index.php' );
}

if ( $_GET['category'] != 'Books' && $_GET['category'] != 'Gadgets' && $_GET['category'] != 'Wears' && $_GET['category'] != 'Rooms' &&
   $_GET['category'] != 'Painting' && $_GET['category'] != 'Catering' && $_GET['category'] != 'GraphicDesigning' && $_GET['category'] != 'ElectricalWorks' &&
   $_GET['category'] != 'Cake' && $_GET['category'] != 'Chicken' && $_GET['category'] != 'Pizza' && $_GET['category'] != 'Rice' &&
   $_GET['category'] != 'Spaghetti' && $_GET['category'] != 'Swallow' && $_GET['category'] != 'Yam' && $_GET['category'] != 'Fruit Salad' && $_GET['category'] != 'Dessert' &&
   $_GET['category'] != 'Drink' && $_GET['category'] != 'Sharwarma' && $_GET['category'] != 'Meat Pie' && $_GET['category'] != 'Red Velvet Cake' && $_GET['category'] != 'Birthday Cake' && $_GET['category'] != 'Burger' )
{
   header( 'Location:index.php' );
}

if ( isset( $_GET['editItemForVendor'] ) && isset( $_GET['idOfItem'] ) && isset( $_GET['idOfVendor'] ) ) {
   if ( !consistsOfOnlyDigits( $_GET['idOfItem'] ) || !consistsOfOnlyDigits( $_GET['idOfVendor'] ) ) {
     header( 'Location: index.php' );
   }

   $query = 'SELECT user_id_of_vendor_manager FROM vendors WHERE vendor_id = ' . $_GET['idOfVendor'];
   $result = mysqli_query( $db, $query );
   $row = mysqli_fetch_assoc( $result );
   if ( $row['user_id_of_vendor_manager'] != $_SESSION['user_id'] ) {
      header( 'Location: index.php' );
   }

   $query = 'SELECT people_id, category FROM photo_upload WHERE id_new = ' . $_GET['idOfItem'];
   $result = mysqli_query( $db, $query );
   $row = mysqli_fetch_assoc( $result );
   if ( $row['people_id'] != 'VENDOR_' . $_GET['idOfVendor'] || $row['category'] != strtolower( $_GET['category'] ) ) {
      header( 'Location: index.php' );
   }

   $_POST['editItemForVendor'] = $_GET['editItemForVendor'];
   $_POST['idOfItem'] = $_GET['idOfItem'];
   $_POST['idOfVendor'] = $_GET['idOfVendor'];
}

if ( $_GET['category'] == 'Books' ) {
   $categoryInSingularForm = 'book';
}
else if ( $_GET['category'] == 'Gadgets' ) {
   $categoryInSingularForm = 'gadget';
}
else if ( $_GET['category'] == 'Wears' ) {
   $categoryInSingularForm = 'wear';
}
else if ( $_GET['category'] == 'Rooms' ) {
   $categoryInSingularForm = 'room';
}
else if ( $_GET['category'] == 'Painting' ) {
   $categoryInSingularForm = 'painting job';
}
else if ( $_GET['category'] == 'Catering' ) {
   $categoryInSingularForm = 'catering work';
}
else if ( $_GET['category'] == 'GraphicsDesigning' ) {
   $categoryInSingularForm = 'graphics design work';
}
else if ( $_GET['category'] == 'ElectricalWorks' ) {
   $categoryInSingularForm = 'electrical work';
}
else if ( $_GET['category'] == 'Cake' || $_GET['category'] == 'Indomie' || $_GET['category'] == 'Chicken' || $_GET['category'] == 'Dessert' || $_GET['category'] == 'Drink' || $_GET['category'] == 'Pizza' ||
   $_GET['category'] == 'Rice' || $_GET['category'] == 'Shawarma' || $_GET['category'] == 'Spaghetti' || $_GET['category'] == 'Swallow' || $_GET['category'] == 'Yam' || $_GET['category'] == 'Fruit Salad' ||
	$_GET['category'] == 'Pancake' || $_GET['category'] == 'Meat Pie' || $_GET['category'] == 'Red Velvet Cake' || $_GET['category'] == 'Birthday Cake' || $_GET['category'] == 'Burger' )
{
   $categoryInSingularForm = 'food';
}

if ( isset( $_POST['editItemForVendor'] ) ) {
   $query = 'SELECT vendor_name FROM vendors WHERE vendor_id = ' . $_POST['idOfVendor'];
   $resultContainingVendorData = mysqli_query( $db, $query );
   $rowContainingVendorData = mysqli_fetch_assoc( $resultContainingVendorData );
   displayMarkupsCommonToTopOfPages( 'Edit ' . $categoryInSingularForm . ' for ' . $rowContainingVendorData['vendor_name'], DISPLAY_NAVIGATION_MENU, 'edit_item.php' );

   $query = "SELECT `Name_of_item`,`Brief_Descripition`, `Price`, `negotiable` FROM `photo_upload` WHERE `id_new`= '" . $_POST['idOfItem'] . "'";
}
else {
   displayMarkupsCommonToTopOfPages( 'Edit ' . $categoryInSingularForm, DISPLAY_NAVIGATION_MENU, 'edit_item.php' );

   $query = "SELECT `Name_of_item`,`Brief_Descripition`, `Price`, `negotiable` FROM `photo_upload` WHERE `people_id`= '".$_SESSION['user_id']."' AND `category` = '" . $_GET['category'] . "'";
}

$picks=mysqli_query($db, $query) or die( $markupIndicatingDatabaseQueryFailure );
$picker=mysqli_fetch_array($picks);
$olditem= $picker['Name_of_item'];
$olddescribe= $picker['Brief_Descripition'];
$oldprice= $picker['Price'];
$oldNegotiate = $picker['negotiable'];

$itemHasNotYetBeenEdited = true;

if (isset($_POST['describe']) && isset($_POST['money']) && isset($_POST['item'])){
   $describe = $_POST['describe'];
   $money = $_POST['money'];
   $negotiate = $_POST['negotiate'];
   $item = $_POST['item'];

	if(!empty($item) && !empty($money) && !empty($negotiate)){
      if ( isset( $_POST['editItemForVendor'] ) ) {
	      $update="UPDATE `photo_upload` SET `Name_of_item`='$item',`Brief_Descripition`='$describe',`Price`='$money',`Negotiable`='$negotiate', `checks` = 'NEWLY UPLOADED' WHERE `id_new`= '" . $_POST['idOfItem'] . "'";
      }
      else {
	      $update="UPDATE `photo_upload` SET `Name_of_item`='$item',`Brief_Descripition`='$describe',`Price`='$money',`Negotiable`='$negotiate', `checks` = 'NEWLY UPLOADED' WHERE `people_id`= '".$_SESSION['user_id']."' AND `Category`= '". $_GET['category']."'";
      }

	   if ($update_run=mysqli_query($db, $update)){
		   $itemHasNotYetBeenEdited = false;
	   }
      else{
		   echo'<p id="errorMessage">Update unsuccessful. Try again later.</p>';
	   }
   }
   else {
	   echo'<p id="errorMessage">Enter all fields.</p>';
   }
}


if ( $itemHasNotYetBeenEdited ) {
   if ( isset( $_POST['editItemForVendor'] ) ) {
?>

            <header id="minorHeaderType2">
               <h2>Edit <?php echo ucwords( $categoryInSingularForm ) . ' for ' . $rowContainingVendorData['vendor_name'] ?></h2>
            </header>
<?php
   }
   else {
?>

            <header id="minorHeaderType2">
               <h2>Edit <?php echo ucwords( $categoryInSingularForm ) ?></h2>
            </header>
<?php
   }
?>
            <form class="form-horizontal" action="edit_item.php?category=<?php echo $_GET['category'] ?>" method="POST" enctype="multipart/form-data" id="looksLikeACardboardPaper">
               <h3 id="mediumSizedText">Fill the form below to edit the details of this <?php echo $categoryInSingularForm ?>:</h3>

               <div class="form-group">
                  <label for="nameOfItem" class="control-label col-sm-2">Name of <?php echo $categoryInSingularForm ?>:</label>
                  <div class="col-sm-10"><input type="text" name="item" class="form-control" id="nameOfItem" maxlength="50" value="<?php if(!isset($_POST{'item'})){echo $olditem;} else{echo $_POST{'item'};} ?>" /></div>
               </div>

               <div class="form-group">
                  <label for="briefDescription" class="control-label col-sm-2">Brief description of the <?php echo $categoryInSingularForm ?>:</label>
                  <div class="col-sm-10"><textarea name="describe" class="form-control" id="briefDescription" maxlength="150"><?php if(!isset($_POST{'describe'})){echo $olddescribe;} else{echo $_POST{'describe'};} ?></textarea></div>
               </div>

               <div class="form-group">
                  <label for="money" class="control-label col-sm-2">Price of the <?php echo $categoryInSingularForm ?>:</label>
                  <div class="col-sm-10"><input type="text" name="money" class="form-control" id="money" maxlength="30" value="<?php if(!isset($_POST{'money'})){echo $oldprice;} else{echo $_POST{'money'};} ?>"/></div>
               </div>

               <fieldset class="form-group">
                  <legend class="control-label col-sm-2" id="boldSmallSizedText">Is the price negotiable?</legend>
                  <div class="col-sm-10">
                     <div>
                        <input type="radio" name="negotiate" id="yes" value="YES" <?php if ( !isset( $_POST['negotiate'] ) && $oldNegotiate == 'YES' ) {echo 'checked';} else if ( isset( $_POST['negotiate'] ) && $_POST['negotiate'] == 'YES' ) { echo 'checked'; } ?> />
                        <label for="yes">Yes</label>
                     </div>
                     <div>
                        <input type="radio" name="negotiate" id="no" value="NO" <?php if ( !isset( $_POST['negotiate'] ) && $oldNegotiate == 'NO' ) {echo 'checked';} else if ( isset( $_POST['negotiate'] ) && $_POST['negotiate'] == 'NO' ) { echo 'checked'; } ?> />
                        <label for="no">No</label>
                     </div>
                  </div>
               </fieldset>
<?php
   if ( isset( $_POST['editItemForVendor'] ) ) {
?>
               <input type="hidden" name="editItemForVendor" value="<?php echo $_POST['editItemForVendor'] ?>"/>
               <input type="hidden" name="idOfItem" value="<?php echo $_POST['idOfItem'] ?>"/>
               <input type="hidden" name="idOfVendor" value="<?php echo $_POST['idOfVendor'] ?>"/>
<?php
   }
?>

               <div class="form-group">
                  <div class="col-sm-offset-2 col-sm-10"><input type="submit" value="Edit <?php echo $categoryInSingularForm ?>" class="btn btn-success"/></div>
               </div>
            </form>
<?php
}
else {
?>
            <div id="containerHoldingSuccessMessage">
               <h2>Successfully Edited <?php echo ucwords( $categoryInSingularForm ) ?> Upload</h2>
<?php
   if ( isset( $_POST['editItemForVendor'] ) ) {
?>
               <p>You have successfully edited the details of the required <?php echo $categoryInSingularForm ?> for <?php echo $rowContainingVendorData['vendor_name'] ?>:</p>
<?php
   }
   else {
?>
               <p>You have successfully edited the details of your <?php echo $categoryInSingularForm ?> upload.</p>
<?php
   }
?>
               <p>Below are the current details:</p>

               <ul>
                  <li><span>Name of <?php echo $categoryInSingularForm ?>:</span> <?php echo ucwords ( $item ) ?></li>
                  <li><span>Description of <?php echo $categoryInSingularForm ?>:</span> <?php echo $describe ?></li>
                  <li><span>Price:</span> <?php echo $money . ' (' . ( $negotiate == 'YES' ? 'negotiable' : 'non-negotiable' ) . ')' ?></li>
               </ul>
<?php
      if ( isset( $_POST['editItemForVendor'] ) ) {
?>
               <p>You may want to</p>
               <p><a href="your_uploads_as_manager_of_vendor.php" class="btn btn-default btn-sm">Click Here</a> to view all your <?php echo ucwords( $categoryInSingularForm ) . ' uploads for ' . $rowContainingVendorData['vendor_name'] ?></p>
               <p>or</p>
               <p><a href="view_uploads_by_vendor.php?vendor=<?php echo $_POST['idOfVendor'] ?>" class="btn btn-default btn-sm">Click Here</a> to visit <?php echo $rowContainingVendorData['vendor_name'] ?>'s Outlet on RoarConnect</p>
<?php
      }
      else {
?>
               <p>You may want to <a href="your_upload.php?category=<?php echo $_GET['category'] ?>" class="btn btn-default btn-sm">View all Your <?php echo ucwords( $categoryInSingularForm ) ?> Uploads</a> or <a href="view_all_items.php?category=<?php echo $_GET['category'] ?>" class="btn btn-default btn-sm">Buy <?php echo ucwords( $_GET['category'] ) ?> from RoarConnect Users</a></p>
<?php
      }
?>
            </div>
<?php
}

displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
?>