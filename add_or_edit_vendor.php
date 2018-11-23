<?php
require_once 'includes/generalHeaderFile.php';
define( 'MAXIMUM_ALLOWABLE_FILE_SIZE', 512000 ); // 512000 Bytes is equal to 500 Kilobytes

if ( !isset( $_GET['requiredAction'] ) || ( $_GET['requiredAction'] != 'addVendor' && $_GET['requiredAction'] != 'editVendor' ) ) {
   header( 'Location: index.php' );
}

if ( $_GET['requiredAction'] == 'editVendor' ) {
   if ( !isset( $_GET['idOfVendor'] ) || !consistsOfOnlyDigits( $_GET['idOfVendor'] ) ) {
      header( 'Location: index.php' );
   }
}

$formShouldBeDisplayed = true;

if ( $_POST ) {
   $defaultNameOfVendor = $_POST['nameOfVendor'];
   $defaultCategoryOfVendorManager = $_POST['categoryOfVendor'];
   $defaultIdOfVendorManager = $_POST['idOfVendorManager'];
   $defaultAddressOfVendor = $_POST['addressOfVendor'];
   $defaultEmailOfVendor = $_POST['emailOfVendor'];
   $defaultFirstPhoneNumberOfVendor = $_POST['firstPhoneNumberOfVendor'];
   $defaultSecondPhoneNumberOfVendor = $_POST['secondPhoneNumberOfVendor'];
}
else if ( $_GET['requiredAction'] == 'editVendor' ) {
   $query = 'SELECT * FROM vendors WHERE vendor_id = ' . $_GET['idOfVendor'];
   $result = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
   $row = mysqli_fetch_assoc( $result );

   $defaultNameOfVendor = $row['vendor_name'];
   $defaultCategoryOfVendorManager = $row['vendor_category'];
   $defaultIdOfVendorManager = $row['user_id_of_vendor_manager'];
   $defaultAddressOfVendor = $row['vendor_address'];
   $defaultEmailOfVendor = $row['vendor_email'];
   $defaultFirstPhoneNumberOfVendor = $row['vendor_phone_number_1'];
   $defaultSecondPhoneNumberOfVendor = $row['vendor_phone_number_2'];
}

if ( $_POST && userIsLoggedInAsAdmin() ) {
   $thereIsErrorInFormData = false;

   if ( $_GET['requiredAction'] == 'addVendor' ) {
      if ( !is_uploaded_file( $_FILES['vendorFlier']['tmp_name'] ) ) {
         $errorMessageForVendorFlier = '<p class="col-sm-offset-2 col-sm-10" id="errorMessage">You did not select any file. Please select a file.</p>';
         $thereIsErrorInFormData = true;
      }
      else if ( $_FILES['vendorFlier']['type'] != 'image/jpeg' ) {
         $errorMessageForVendorFlier = '<p class="col-sm-offset-2 col-sm-10" id="errorMessage">Invalid file. A valid file must be a JPG file.</p>';
         $thereIsErrorInFormData = true;
      }
      else if ( $_FILES['vendorFlier']['size'] > MAXIMUM_ALLOWABLE_FILE_SIZE ) {
         $errorMessageForVendorFlier = '<p class="col-sm-offset-2 col-sm-10" id="errorMessage">File is too large. A valid file must be less than 500KB</p>';
         $thereIsErrorInFormData = true;
      }
   }

   if ( $_POST['nameOfVendor'] == '' ) {
      $errorMessageForNameOfVendor = '<p class="col-sm-offset-2 col-sm-10" id="errorMessage">Please, enter name of vendor</p>';
      $thereIsErrorInFormData = true;
   }
   else if ( $_GET['requiredAction'] == 'addVendor' ) {
      $query = 'SELECT vendor_name FROM vendors WHERE vendor_name = "' . $_POST['nameOfVendor'] . '"';
      $result = mysqli_query( $db, $query );
      if ( mysqli_num_rows( $result ) == 1 ) {
         $errorMessageForNameOfVendor = '<p class="col-sm-offset-2 col-sm-10" id="errorMessage">A vendor already exists with the same name. Please, enter another name.</p>';
         $thereIsErrorInFormData = true;
      }
   }

   if ( $_POST['categoryOfVendor'] == '-1' )
   {
      $errorMessageForCategoryOfVendor = '<p class="col-sm-offset-2 col-sm-10" id="errorMessage">Please, select a category.</p>';
      $thereIsErrorInFormData = true;
   }

   if ( $_POST['idOfVendorManager'] == '-1' ) {
      $errorMessageForIdOfVendorManager = '<p class="col-sm-offset-2 col-sm-10" id="errorMessage">Please, select the email address of the manager of this vendor.</p>';
      $thereIsErrorInFormData = true;
   }

   if ( $_POST['firstPhoneNumberOfVendor'] == '' ) {
      $errorMessageForFirstPhoneNumberOfVendor = '<p class="col-sm-offset-2 col-sm-10" id="errorMessage">Please, enter primary phone number of vendor.</p>';
      $thereIsErrorInFormData = true;
   }
   else if ( !consistsOfOnlyDigits( $_POST['firstPhoneNumberOfVendor'] ) || strlen( $_POST['firstPhoneNumberOfVendor'] ) != 11 ) {
      $errorMessageForFirstPhoneNumberOfVendor = '<p class="col-sm-offset-2 col-sm-10" id="errorMessage">Invalid phone number.</p>';
      $thereIsErrorInFormData = true;
   }

   if ( $_POST['secondPhoneNumberOfVendor'] != '' ) {
      if ( !consistsOfOnlyDigits( $_POST['secondPhoneNumberOfVendor'] ) || strlen( $_POST['secondPhoneNumberOfVendor'] ) != 11 ) {
         $errorMessageForFirstPhoneNumberOfVendor = '<p class="col-sm-offset-2 col-sm-10" id="errorMessage">Invalid phone number.</p>';
         $thereIsErrorInFormData = true;
      }
   }

   $thereIsNoErrorInFormData = !$thereIsErrorInFormData;
   if ( $thereIsNoErrorInFormData ) {
      if ( $_GET['requiredAction'] == 'addVendor' ) {
         $query = 'INSERT INTO vendors ( vendor_name, vendor_address, vendor_email, vendor_phone_number_1, vendor_phone_number_2, vendor_category, user_id_of_vendor_manager )
            VALUES ( "' . addslashes( trim( $_POST['nameOfVendor'] ) ) . '", "' . addslashes( trim( $_POST['addressOfVendor'] ) ) . '", "' . $_POST['emailOfVendor'] . '", "' . $_POST['firstPhoneNumberOfVendor'] . '", "' . $_POST['secondPhoneNumberOfVendor'] . '", "' . $_POST['categoryOfVendor'] . '", ' . $_POST['idOfVendorManager'] . ' )';
         mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
         $idOfLatestVendor = mysqli_insert_id( $db );

         $temporaryFilePathOfVendorFlier = $_FILES['vendorFlier']['tmp_name'];
         $permanentFilePathOfVendorFlier = 'images/vendorFliers/' . $_POST['nameOfVendor'] . '.jpg';
         move_uploaded_file( $temporaryFilePathOfVendorFlier, $permanentFilePathOfVendorFlier ) or die( '<p id="errorMessage">Unable to upload vendor flier.</p>' );
      }
      else {
         $query = 'SELECT vendor_name FROM vendors WHERE vendor_id = ' . $_GET['idOfVendor'];
         $result = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
         $row = mysqli_fetch_assoc( $result );
         if ( strtolower( $row['vendor_name'] ) != strtolower( $_POST['nameOfVendor'] ) ) {
            $fileNameOfOldFlier = 'images/vendorFliers/' . $row['vendor_name'] . '.jpg';
            $fileNameOfNewFlier = 'images/vendorFliers/' . $_POST['nameOfVendor'] . '.jpg';

            $handleToOldFlier = fopen( $fileNameOfOldFlier, 'r' ) or die( '<p id="errorMessage">An unexpected error occurred.</p>' );
            $handleToNewFlier = fopen( $fileNameOfNewFlier, 'w' ) or die( '<p id="errorMessage">An unexpected error occurred.</p>' );

            $oldFlierData = fread( $handleToOldFlier, filesize( $fileNameOfOldFlier ) ) or die( '<p id="errorMessage">An unexpected error occurred.</p>' );
            fwrite( $handleToNewFlier, $oldFlierData ) or die( '<p id="errorMessage">An unexpected error occurred.</p>' );

            fclose( $handleToOldFlier );
            fclose( $handleToNewFlier );

            unlink( $fileNameOfOldFlier );
         }

         $query = 'UPDATE vendors SET vendor_name = "' . addslashes( trim( $_POST['nameOfVendor'] ) ) . '", vendor_address = "' . addslashes( trim( $_POST['addressOfVendor'] ) ) . '", vendor_email = "' . $_POST['emailOfVendor'] . '", vendor_phone_number_1 = "' . $_POST['firstPhoneNumberOfVendor'] . '", vendor_phone_number_2 = "' . $_POST['secondPhoneNumberOfVendor'] . '", vendor_category = "' . $_POST['categoryOfVendor'] . '", user_id_of_vendor_manager = ' . $_POST['idOfVendorManager'] . '
            WHERE vendor_id = ' . $_GET['idOfVendor'];
         mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
      }

      $formShouldBeDisplayed = false;
   }
}

displayMarkupsCommonToTopOfPages( $_GET['requiredAction'] == 'addVendor' ? 'Add New Vendor' : 'Edit Vendor', DISPLAY_NAVIGATION_MENU, 'add_or_edit_vendor.php' );

if ( !userIsLoggedInAsAdmin() ) {
   session_destroy();
   displayMarkupToIndicateThatAdminLoginIsRequired();
}

if ( $formShouldBeDisplayed ) {
?>
         <form method="POST" action="add_or_edit_vendor.php?<?php echo buildStringContainingAllDataFromGET() ?>" class="form-horizontal" id="looksLikeACardboardPaper" enctype="multipart/form-data">
            <h3 id="boldMediumSizedText"><?php echo $_GET['requiredAction'] == 'addVendor' ? 'Add New Vendor' : 'Edit Vendor' ?></h3>

<?php
   if ( $_GET['requiredAction'] == 'addVendor' ) {
?>
            <div class="form-group">
               <label for="vendorFlier" class="control-label col-sm-2">Flier of Vendor:</label>
               <div class="col-sm-10"><input type="file" name="vendorFlier" id="vendorFlier" /></div>
               <?php echo $errorMessageForVendorFlier ?>

            </div>
<?php
   }
?>

            <div class="form-group">
               <label for="nameOfVendor" class="control-label col-sm-2">Name of vendor:</label>
               <div class="col-sm-10"><input type="text" name="nameOfVendor" value="<?php echo $defaultNameOfVendor ?>" class="form-control" id="nameOfVendor" /></div>
               <?php echo $errorMessageForNameOfVendor ?>

            </div>

            <div class="form-group">
               <label for="categoryOfVendor" class="control-label col-sm-2">Category of vendor:</label>
               <div class="col-sm-10">
                  <select name="categoryOfVendor" class="form-control" id="categoryOfVendor">
                     <option value="-1">---</option>
                     <option value="arts"<?php echo $defaultCategoryOfVendorManager == 'arts' ? ' selected' : '' ?>>Arts</option>
                     <option value="BeautyService"<?php echo $defaultCategoryOfVendorManager == 'BeautyService' ? ' selected' : '' ?>>Beauty Service</option>
                     <option value="books"<?php echo $defaultCategoryOfVendorManager == 'books' ? ' selected' : '' ?>>Books</option>
                     <option value="catering"<?php echo $defaultCategoryOfVendorManager == 'catering' ? ' selected' : '' ?>>Catering</option>
                     <option value="electricalWorks"<?php echo $defaultCategoryOfVendorManager == 'electricalWorks' ? ' selected' : '' ?>>Electrical Works</option>
                     <option value="gadgets"<?php echo $defaultCategoryOfVendorManager == 'gadgets' ? ' selected' : '' ?>>Gadgets</option>
                     <option value="foods"<?php echo $defaultCategoryOfVendorManager == 'foods' ? ' selected' : '' ?>>Foods</option>
                     <option value="graphicsDesigning"<?php echo $defaultCategoryOfVendorManager == 'graphicsDesigning' ? ' selected' : '' ?>>Graphics Designing</option>
                     <option value="HaircutService"<?php echo $defaultCategoryOfVendorManager == 'HaircutService' ? ' selected' : '' ?>>Haircut Service</option>
                     <option value="painting"<?php echo $defaultCategoryOfVendorManager == 'painting' ? ' selected' : '' ?>>Painting</option>
                     <option value="rooms"<?php echo $defaultCategoryOfVendorManager == 'rooms' ? ' selected' : '' ?>>Rooms</option>
                     <option value="wears"<?php echo $defaultCategoryOfVendorManager == 'wears' ? ' selected' : '' ?>>Wears</option>
                      <option value="DataServices"<?php echo $defaultCategoryOfVendorManager == 'DataServices' ? ' selected' : '' ?>>DataServices</option>
                  </select>
               </div>
               <?php echo $errorMessageForCategoryOfVendor ?>

            </div>

            <div class="form-group">
               <label for="userNameOfVendorManager" class="control-label col-sm-2">Manager of vendor:</label>
               <div class="col-sm-10">
                  <select name="idOfVendorManager" class="form-control" id="userNameOfVendorManager">
                     <option value="-1">---</option>
<?php
   $query = 'SELECT id, email FROM users ORDER BY email';
   $result = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
   $row = mysqli_fetch_assoc( $result );
   while ( $row != NULL ) {
?>
                     <option value="<?php echo $row['id'] ?>"<?php echo $row['id'] == $defaultIdOfVendorManager ? ' selected' : '' ?>><?php echo $row['email'] ?></option>
<?php
      $row = mysqli_fetch_assoc( $result );
   }
?>
                  </select>
               </div>
               <?php echo $errorMessageForIdOfVendorManager ?>

            </div>

            <div class="form-group">
               <label for="firstPhoneNumberOfVendor" class="control-label col-sm-2">Primary Phone number of vendor:</label>
               <div class="col-sm-10"><input type="text" name="firstPhoneNumberOfVendor" value="<?php echo $defaultFirstPhoneNumberOfVendor ?>" class="form-control" id="firstPhoneNumberOfVendor" /></div>
               <?php echo $errorMessageForFirstPhoneNumberOfVendor ?>

            </div>

            <div class="form-group">
               <label for="secondPhoneNumberOfVendor" class="control-label col-sm-2">(Optional) Alternate Phone number of vendor:</label>
               <div class="col-sm-10"><input type="text" name="secondPhoneNumberOfVendor" value="<?php echo $defaultSecondPhoneNumberOfVendor ?>" class="form-control" id="secondPhoneNumberOfVendor" /></div>
               <?php echo $errorMessageForSecondPhoneNumberOfVendor ?>

            </div>

            <div class="form-group">
               <label for="addressOfVendor" class="control-label col-sm-2">(Optional) Contact address of vendor:</label>
               <div class="col-sm-10"><input type="text" name="addressOfVendor" value="<?php echo $defaultAddressOfVendor ?>" class="form-control" id="addressOfVendor" /></div>
               <?php echo $errorMessageForAddressOfVendor ?>

            </div>

            <div class="form-group">
               <label for="emailOfVendor" class="control-label col-sm-2">(Optional) Email address of vendor:</label>
               <div class="col-sm-10"><input type="text" name="emailOfVendor" value="<?php echo $defaultEmailOfVendor ?>" class="form-control" id="emailOfVendor" /></div>
               <?php echo $errorMessageForEmailOfVendor ?>

            </div>

            <div class="form-group">
               <div class="col-sm-offset-2 col-sm-10"><button type="submit" class="btn btn-success"><?php echo $_GET['requiredAction'] == 'addVendor' ? 'Add New Vendor' : 'Edit Vendor' ?></button></div>
            </div>
         </form>
<?php
}
else {
   $query = 'SELECT email FROM users WHERE id = ' . $_POST['idOfVendorManager'];
   $result = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
   $rowContainingUserData = mysqli_fetch_assoc( $result );
?>
         <div id="containerHoldingSuccessMessage">
            <h2>Successfully <?php echo $_GET['requiredAction'] == 'addVendor' ? 'Added New Vendor' : 'Edited Vendor Details' ?>.</h2>
            <p>You have successfully <?php echo $_GET['requiredAction'] == 'addVendor' ? 'added a new Vendor' : 'edited the details of the required vendor' ?>.</p>
            <p><?php echo $_GET['requiredAction'] == 'addVendor' ? 'Below are the details of the new vendor:' : 'Below are the current details of the vendor:' ?></p>

            <ul>
               <li><span id="boldSmallSizedText">Name of vendor:</span> <?php echo $_POST['nameOfVendor'] ?></li>
               <li><span id="boldSmallSizedText">Category of vendor:</span> <?php echo ucwords( $_POST['categoryOfVendor'] ) ?></li>
               <li><span id="boldSmallSizedText">Email address of vendor manager:</span> <?php echo $rowContainingUserData['email'] ?></li>
               <li><span id="boldSmallSizedText">Primary phone number of vendor:</span> <?php echo $_POST['firstPhoneNumberOfVendor'] ?></li>
<?php
   if ( $_POST['addressOfVendor'] != '' ) {
?>
               <li><span id="boldSmallSizedText">Contact address of vendor:</span> <?php echo $_POST['addressOfVendor'] ?></li>
<?php
   }

   if ( $_POST['emailOfVendor'] != '' ) {
?>
               <li><span id="boldSmallSizedText">Email address of vendor:</span> <?php echo $_POST['emailOfVendor'] ?></li>
<?php
   }
?>
<?php
   if ( $_POST['secondPhoneNumberOfVendor'] != '' ) {
?>
               <li><span id="boldSmallSizedText">Alternate phone number of vendor:</span> <?php echo $_POST['secondPhoneNumberOfVendor'] ?></li>
<?php
   }
?>
            </ul>
<?php
   if ( $_GET['requiredAction'] == 'addVendor' ) {
?>
            <p>You may want to <a href="add_or_edit_vendor.php?requiredAction=editVendor&idOfVendor=<?php echo $idOfLatestVendor ?>" class="btn btn-default btn-sm">Edit this Vendor</a> or <a href="view_uploads_by_vendor.php?vendor=<?php echo $idOfLatestVendor ?>" class="btn btn-default btn-sm">View the Outlet of this Vendor</a>.</p>
<?php
}
else {
?>
            <p>You may want to <a href="view_uploads_by_vendor.php?vendor=<?php echo $_GET['idOfVendor'] ?>" class="btn btn-default btn-sm">View the Outlet of this Vendor</a>.</p>
<?php
}
?>
         </div>
<?php
}

displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
?>
