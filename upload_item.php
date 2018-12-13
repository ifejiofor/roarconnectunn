<?php
require_once 'includes/generalHeaderFile.php';

if(!currentUserIsLoggedIn()) {
   header('location:index.php');
}
else {
   if ( isset( $_GET['uploadItemForVendor'] ) && isset( $_GET['idOfVendor'] ) ) {
      if ( !consistsOfOnlyDigits( $_GET['idOfVendor'] ) ) {
         header( 'Location: index.php' );
      }

      $query = 'SELECT user_id_of_vendor_manager FROM vendors WHERE vendor_id = ' . $_GET['idOfVendor'];
      $result = mysqli_query( $globalHandleToDatabase, $query );
      $row = mysqli_fetch_assoc( $result );
      if ( $row['user_id_of_vendor_manager'] != $_SESSION['user_id'] ) { // the current user is not the manager of the required vendor
         header( 'Location: index.php' );
      }
      else {
         $_POST['uploadItemForVendor'] = $_GET['uploadItemForVendor'];
         $_POST['idOfVendor'] = $_GET['idOfVendor'];
      }
   }

   displayMarkupsCommonToTopOfPages( 'Upload Item', DISPLAY_NAVIGATION_MENU, 'upload_item.php' );

   $userNeedsToViewForm = true;
   $userNeedsToBeRemindedThatHeCanOnlyUploadOneItemPerCategory = false;
   $itemHasBeenUploadedSuccessfully = false;

   $name = $_FILES['file']['name'];
   $type = $_FILES['file']['type'];
   $size = $_FILES['file']['size'];
   $tmp_name = $_FILES['file']['tmp_name'];
   $extend = strtolower(substr($name, strpos($name, '.')+1));

   if ( isset( $name ) && isset( $_POST['describe'] ) && isset( $_POST['money'] )  && isset( $_POST['item'] ) ) {
	   $category = $_POST['category'];
	   $describe = $_POST['describe'];
	   $money = $_POST['money'];
	   $negotiate = $_POST['negotiate'];
	   $item = $_POST['item'];

      if ( $category == 'gadgets' ) {
         $categoryInSingularForm = 'gadget';
      }
      else if ( $category == 'wears' ) {
         $categoryInSingularForm = 'wear';
      }
      else if ( $category == 'books' ) {
         $categoryInSingularForm = 'book';
      }
      else if ( $category == 'rooms' ) {
         $categoryInSingularForm = 'room';
      }
      else if ( $category == 'painting' ) {
         $categoryInSingularForm = 'painting job';
      }
      else if ( $category == 'catering' ) {
         $categoryInSingularForm = 'catering work';
      }
      else if ( $category == 'graphicsDesigning' ) {
         $categoryInSingularForm = 'graphics design work';
      }
      else if ( $category == 'electricalWorks' ) {
         $categoryInSingularForm = 'electrical work';
      }
      else if ( $category == 'cake' || $category == 'indomie' || $category == 'chicken' || $category == 'dessert' || $category == 'drink' || $category == 'pizza' ||
         $category == 'rice' || $category == 'shawarma' || $category == 'spaghetti' || $category == 'swallow' || $category == 'yam' || $category == 'fruit salad' ||
		 $category == 'pancake' || $category == 'meat pie' || $category == 'red velvet cake' || $category == 'birthday cake' || $category == 'burger' )
      {
         $categoryInSingularForm = 'food';
      }

  
	   if ( !empty( $name ) && !empty( $category ) && !empty( $item ) && !empty( $money ) && !empty( $negotiate ) ) {
		   if( ( $extend == 'jpg' || $extend == 'jpeg' || $extend == 'png') && ( $type == 'image/jpeg' || $type == 'image/jpg' || $type == 'image/png') ) {
		      if( $size <= 51200 )	{

               $uploadQueryExecutedSuccessfully = false;
	            if( isset( $_POST['uploadItemForVendor'] ) ){
                  $user_id = 'VENDOR_' . $_POST['idOfVendor'];
		            $upload = "INSERT INTO `photo_upload`(`People_id`, `Name_of_item`, `Category`, `Brief_Descripition`, `Price`, `Negotiable`, `Image_size`) VALUES ('$user_id','$item','$category','$describe','$money','$negotiate','$item.$extend')";
         		   $uploadQueryExecutedSuccessfully = mysqli_query( $globalHandleToDatabase, $upload ) or die( $globalDatabaseErrorMarkup );
	            }
               else{
		            $check1 = "SELECT `people_id` FROM `photo_upload` WHERE `people_id`= '".$_SESSION['user_id']."' AND `Category`= '" . $category . "'";
		            $query_check = mysqli_query( $globalHandleToDatabase, $check1 );
		            if( mysqli_num_rows( $query_check ) >= 1 ) {
                     $userNeedsToViewForm = false;
                     $userNeedsToBeRemindedThatHeCanOnlyUploadOneItemPerCategory = true;
                     $itemHasBeenUploadedSuccessfully = false;
		            }
                  else {
                     $user_id = $_SESSION['user_id'];
		               $upload = "INSERT INTO `photo_upload`(`People_id`, `Name_of_item`, `Category`, `Brief_Descripition`, `Price`, `Negotiable`, `Image_size`) VALUES ('$user_id','$item','$category','$describe','$money','$negotiate','$item.$extend')";
         		      $uploadQueryExecutedSuccessfully = mysqli_query( $globalHandleToDatabase, $upload ) or die( $globalDatabaseErrorMarkup );
	               }
	            }

               if ( $uploadQueryExecutedSuccessfully ) {
   			      $location = 'images/uploaded' . ucwords( $category ) . 'Snapshots/';
			         $user_new = "$user_id@$item.$extend";
			         if( ( move_uploaded_file( $tmp_name, $location . $user_new ) ) ) {
			            $userNeedsToViewForm = false;
                     $userNeedsToBeRemindedThatHeCanOnlyUploadOneItemPerCategory = false;
                     $itemHasBeenUploadedSuccessfully = true;
			         }
                  else {
				         echo '<p id="errorMessage">Item uploaded but its snapshot could not be saved due to an unexpected error.</p>';
			         }
               }
		      }
            else {
			      echo'<p id="errorMessage">Please, choose a file not greater than 50kb.</p>';
		      }
		   }
         else {
			   echo'<p id="errorMessage">Please, choose either a jpeg, a jpg, or a png file.</p>';
		   }
	   }
      else {
		   echo '<p id="errorMessage">Please enter all fields</p>';
	   }
   }

   if ( $userNeedsToViewForm ) {

      if ( isset( $_POST['uploadItemForVendor'] ) ) {
         $query = 'SELECT vendor_name, vendor_category FROM vendors WHERE vendor_id = ' . $_POST['idOfVendor'];
         $resultContainingVendorData = mysqli_query( $globalHandleToDatabase, $query );
         $rowContainingVendorData = mysqli_fetch_assoc( $resultContainingVendorData );
?>

            <header id="minorHeaderType2">
               <h2>Upload Item for <?php echo $rowContainingVendorData['vendor_name'] ?></h2>
            </header>
<?php
      }
      else {
?>

            <header id="minorHeaderType2">
               <h2>Upload and Sell Items Fast</h2>
               <p>Looking for buyers to buy your old books? Looking for where to sell clothes? Want to sell any electronic gadget? Want to sell hostel bedspaces or rent out rooms in your lodge? You are at the right place.</p>
            </header>
<?php
      }
?>

            <form action="upload_item.php" method="POST" class="form-horizontal" enctype="multipart/form-data" id="looksLikeACardboardPaper">
<?php
      if ( isset( $_POST['uploadItemForVendor'] ) ) {
?>
               <h3 id="mediumSizedText">Enter details of the item:</h3>
<?php
      }
      else {
?>
               <h3 id="mediumSizedText">Simply upload the details of the item and get real buyers in no time.</h3>
<?php
      }
?>

               <div class="form-group">
                  <label for="nameOfItem" class="control-label col-sm-2">Name of item:</label>
                  <div class="col-sm-10"><input type="text" name="item" class="form-control" id="nameOfItem" maxlength="200" value="<?php if(isset($_POST['item'])){echo $item;} ?>" /></div>
               </div>

               <div class="form-group">
                  <label for="briefDescription" class="control-label col-sm-2">Brief description of the item:</label>
                  <div class="col-sm-10"><textarea name="describe" class="form-control" id="briefDescription" maxlength="500"><?php if(isset($_POST['describe'])){echo $describe;} ?></textarea></div>
               </div>

<?php
      if ( isset( $_POST['uploadItemForVendor'] ) ) {
?>
               <input type="hidden" name="uploadItemForVendor" value="<?php echo $_POST['uploadItemForVendor'] ?>" />
               <input type="hidden" name="idOfVendor" value="<?php echo $_POST['idOfVendor'] ?>" />
<?php
         if ( $rowContainingVendorData['vendor_category'] == 'foods' ) {
?>
            <div class="form-group">
               <label for="categoryOfFood" class="control-label col-sm-2">Category of item:</label>
               <div class="col-sm-10">
                  <select name="category" class="form-control" id="categoryOfFood">
                     <option value="">---</option>
                     <option value="birthday cake"<?php echo isset( $_POST['category'] ) && $_POST['category'] == 'birthday cake' ? ' selected' : '' ?>>Birthday Cake</option>
                     <option value="burger"<?php echo isset( $_POST['category'] ) && $_POST['category'] == 'burger' ? ' selected' : '' ?>>Burger</option>
                     <option value="cake"<?php echo isset( $_POST['category'] ) && $_POST['category'] == 'cake' ? ' selected' : '' ?>>Cake</option>
                     <option value="chicken"<?php echo isset( $_POST['category'] ) && $_POST['category'] == 'chicken' ? ' selected' : '' ?>>Chicken</option>
                     <option value="dessert"<?php echo isset( $_POST['category'] ) && $_POST['category'] == 'dessert' ? ' selected' : '' ?>>Extras</option>
                     <option value="drink"<?php echo isset( $_POST['category'] ) && $_POST['category'] == 'drink' ? ' selected' : '' ?>>Drink</option>
                     <option value="fruit salad"<?php echo isset( $_POST['category'] ) && $_POST['category'] == 'fruit salad' ? ' selected' : '' ?>>Fruit Salad</option>
                     <option value="meat pie"<?php echo isset( $_POST['category'] ) && $_POST['category'] == 'meat pie' ? ' selected' : '' ?>>Meat Pie</option>
                     <option value="pancake"<?php echo isset( $_POST['category'] ) && $_POST['category'] == 'pancake' ? ' selected' : '' ?>>Pancake</option>
                     <option value="pizza"<?php echo isset( $_POST['category'] ) && $_POST['category'] == 'pizza' ? ' selected' : '' ?>>Pizza</option>
                     <option value="red velvet cake"<?php echo isset( $_POST['category'] ) && $_POST['category'] == 'red velvet cake' ? ' selected' : '' ?>>Red Velvet Cake</option>
                     <option value="rice"<?php echo isset( $_POST['category'] ) && $_POST['category'] == 'rice' ? ' selected' : '' ?>>Rice</option>
                     <option value="shawarma"<?php echo isset( $_POST['category'] ) && $_POST['category'] == 'shawarma' ? ' selected' : '' ?>>Shawarma</option>
                     <option value="indomie"<?php echo isset( $_POST['category'] ) && $_POST['category'] == 'indomie' ? ' selected' : '' ?>>Indomie</option>
                     <option value="spaghetti"<?php echo isset( $_POST['category'] ) && $_POST['category'] == 'spaghetti' ? ' selected' : '' ?>>Spaghetti</option>
                     <option value="swallow"<?php echo isset( $_POST['category'] ) && $_POST['category'] == 'swallow' ? ' selected' : '' ?>>Swallow</option>
                     <option value="yam"<?php echo isset( $_POST['category'] ) && $_POST['category'] == 'yam' ? ' selected' : '' ?>>Yam</option>
					 </select>
               </div>
               <?php echo $errorMessageForCategoryOfVendor ?>

            </div>
<?php
         }
         else {
?>
               <input type="hidden" name="category" value="<?php echo $rowContainingVendorData['vendor_category'] ?>" />
<?php
         }
      }
      else {
?>
               <fieldset class="form-group">
                  <legend class="control-label col-sm-2" id="boldSmallSizedText">Select the category to which the item belongs to:</legend>
                  <div class="col-sm-10">
                     <div>
                        <input type="radio" name="category" id="wears" value="wears"/>
                        <label for="wears">Wears</label>
                     </div>
                     <div>
                        <input type="radio" name="category" id="gadgets" value="gadgets"/>
                        <label for="gadgets">Gadgets</label>
                     </div>
                     <div>
                        <input type="radio" name="category" id="books" value="books"/>
                        <label for="books">Books</label>
                     </div>
                     <div>
                        <input type="radio" name="category" id="rooms" value="rooms"/>
                        <label for="rooms">Rooms or Hostel Bedspaces</label>
                     </div>
                  </div>
               </fieldset>
<?php
      }
?>

               <div class="form-group">
                  <label for="snapshot" class="control-label col-sm-2">Upload a snapshot of the item:</label>
                  <div class="col-sm-10"><input type="file" name="file" id="snapshot" maxlength="20" value="<?php if(isset($_POST['name'])){echo $name;} ?>"/></div>
               </div>

               <div class="form-group">
                  <label for="money" class="control-label col-sm-2">Price of item:</label>
                  <div class="col-sm-10"><input type="text" name="money" class="form-control" id="money" maxlength="20" value="<?php if(isset($_POST['money'])){echo $money;} ?>"/></div>
               </div>

               <fieldset class="form-group">
                  <legend class="control-label col-sm-2" id="boldSmallSizedText">Is the price negotiable?</legend>
                  <div class="col-sm-10">
                     <div>
                        <input type="radio" name="negotiate" id="yes" value="YES"/>
                        <label for="yes">Yes</label>
                     </div>
                     <div>
                        <input type="radio" name="negotiate" id="no" value="NO"/>
                        <label for="no">No</label>
                     </div>
                  </div>
               </fieldset>

               <div class="form-group">
                  <div class="col-sm-offset-2 col-sm-10"><input type="submit" value="Upload Item" class="btn btn-success"/></div>
               </div>
            </form>
<?php
   }
   else if ( $userNeedsToBeRemindedThatHeCanOnlyUploadOneItemPerCategory ) {
?>
            <div id="containerHoldingErrorMessage">
               <h2>Upload Not Permitted</h2>
               <p>You already have a <?php echo $categoryInSingularForm ?> previously uploaded for sale, yet you are attempting to upload another <?php echo $categoryInSingularForm ?>.</p>
               <p>As a user operating a RoarConnect Free Account, you have access to upload only one item per category at a time.</p>
               <p>You may either visit the <a href="your_upload.php?category=<?php echo ucwords( $category ) ?>" class="btn btn-default btn-sm">Your Uploads Page</a> and delete the previously uploaded <?php echo $categoryInSingularForm ?> so that you can upload the current <?php echo $categoryInSingularForm ?>.</p>
               <p>Or you may <a href="#" class="btn btn-default btn-sm">Subscribe for a Premium Account</a> so that you can have access to upload more than one item at a time per category.</p>
            </div>
<?php
   }
   else if ( $itemHasBeenUploadedSuccessfully ) {
?>
            <div id="containerHoldingSuccessMessage">
               <h2>Successfully Uploaded your <?php echo ucwords( $categoryInSingularForm ) ?></h2>
<?php
      if ( isset( $_POST['uploadItemForVendor'] ) ) {
         $query = 'SELECT vendor_name, vendor_category FROM vendors WHERE vendor_id = ' . $_POST['idOfVendor'];
         $resultContainingVendorData = mysqli_query( $globalHandleToDatabase, $query );
         $rowContainingVendorData = mysqli_fetch_assoc( $resultContainingVendorData );
?>
               <p>You have successfully uploaded your <?php echo $categoryInSingularForm ?>. You can be sure that potential customers will see this <?php echo $categoryInSingularForm . ' at ' . $rowContainingVendorData['vendor_name'] ?>'s Outlet on RoarConnect.</p>
<?php
      }
      else {
?>
               <p>You have successfully uploaded your <?php echo $categoryInSingularForm ?> for sale. No doubt, you will soon start getting phone calls from potential buyers.</p>
<?php
      }
?>
               <p>Below are the details of the uploaded <?php echo $categoryInSingularForm ?>:</p>
               <ul>
                  <li><span>Name of <?php echo $categoryInSingularForm ?>:</span> <?php echo ucwords ( $item ) ?></li>
                  <li><span>Description of <?php echo $categoryInSingularForm ?>:</span> <?php echo $describe ?></li>
                  <li><span>Price:</span> <?php echo $money . ' (' . ( $negotiate == 'YES' ? 'negotiable' : 'non-negotiable' ) . ')' ?></li>
               </ul>

<?php
      if ( isset( $_POST['uploadItemForVendor'] ) ) {
?>
               <p>You may want to</p>
               <p><a href="your_uploads_as_manager_of_vendor.php" class="btn btn-default btn-sm">Click Here</a> to view all your uploads for <?php echo $rowContainingVendorData['vendor_name'] ?></p>
               <p>or</p>
               <p><a href="upload_item.php?uploadItemForVendor&idOfVendor=<?php echo $_POST['idOfVendor'] ?>" class="btn btn-default btn-sm">Click Here</a> to upload another <?php echo ucwords( $categoryInSingularForm ) . ' for ' . $rowContainingVendorData['vendor_name'] ?></a></p>
<?php
      }
      else {
?>
               <p>You may want to <a href="your_upload.php?category=<?php echo ucwords( $category ) ?>" class="btn btn-default btn-sm">View Your <?php echo ucwords( $categoryInSingularForm ) ?> Uploads</a> or <a href="edit_item.php?category=<?php echo ucwords( $category ) ?>" class="btn btn-default btn-sm">Edit Details of the <?php echo ucwords( $categoryInSingularForm ) ?></a></p>
<?php
      }
?>
            </div>
<?php
   }

   displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
}
?>