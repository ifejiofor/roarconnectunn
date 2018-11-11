<?php
   require_once 'in.php';
   require_once 'require.php';
   require_once 'markupsCommonToTopAndBottomOfPages.php';

   if ( !loggedInAsAdmin() ) {
      header('Location: index.php');
   }

   if ( !isset($_GET['idOfVendor']) || !consistsOfOnlyDigits($_GET['idOfVendor']) ) {
      header('Location: index.php');
   }

   if ( !isset($_GET['confirmation']) ) {
      $query = 'SELECT * FROM vendors WHERE vendor_id = ' . $_GET['idOfVendor'];
      $result = mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure);
      $rowContainingVendorData = mysqli_fetch_assoc( $result );

      $query = 'SELECT username FROM users WHERE id = ' . $rowContainingVendorData['user_id_of_vendor_manager'];
      $result = mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure);
      $rowContainingUserData = mysqli_fetch_assoc($result);

      displayMarkupsCommonToTopOfPages('Delete Vendor', DISPLAY_NAVIGATION_MENU, 'delete_vendor.php');
?>
            <div id="containerHoldingErrorMessage">
               <h3>Delete Vendor</h3>
               <p>Are you sure you want to delete this vendor?</p>
               <p>Warning: If you delete this vendor, all the uploads by this vendor will also be irretrievably deleted.</p>

               <ul>
                  <li><span id="boldSmallSizedText">Name of vendor:</span> <?php echo $rowContainingVendorData['vendor_name'] ?></li>
                  <li><span id="boldSmallSizedText">Category of vendor:</span> <?php echo $rowContainingVendorData['vendor_category'] ?></li>
                  <li><span id="boldSmallSizedText">Username of manager of vendor:</span> <?php echo $rowContainingUserData['username'] ?></li>
                  <li><span id="boldSmallSizedText">Primary phone number of vendor:</span> <?php echo $rowContainingVendorData['vendor_phone_number_1'] ?></li>
<?php
      if ( $rowContainingVendorData['vendor_phone_number_2'] != '' ) {
?>
                  <li><span id="boldSmallSizedText">Alternate phone number of vendor:</span> <?php echo $rowContainingVendorData['vendor_phone_number_2'] ?></li>
<?php
      }

      if ( $rowContainingVendorData['vendor_address'] != '' ) {
?>
                  <li><span id="boldSmallSizedText">Contact address of vendor:</span> <?php echo $rowContainingVendorData['vendor_address'] ?></li>
<?php
      }

      if ( $rowContainingVendorData['vendor_email'] != '' ) {
?>
                  <li><span id="boldSmallSizedText">Email address of vendor:</span> <?php echo $rowContainingVendorData['vendor_email'] ?></li>
<?php
      }
?>
               </ul>

               <form method="GET" action="delete_vendor.php">
                  <input type="hidden" name="idOfVendor" value="<?php echo $_GET['idOfVendor'] ?>" />
                  <input type="submit" name="confirmation" value="Yes" class="btn btn-danger btn-lg" />
                  <input type="submit" name="confirmation" value="No" class="btn btn-danger btn-lg" />
               </form>
            </div>
<?php
      displayMarkupsCommonToBottomOfPages(DISPLAY_FOOTER);
   }
   else if ( $_GET['confirmation'] == 'No' ) {
      $query = 'SELECT vendor_category FROM vendors WHERE vendor_id = ' . $_GET['idOfVendor'];
      $result = mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure);
      $row = mysqli_fetch_assoc( $result );

      header('Location: view_all_items.php?category='. ucwords($row['vendor_category']));
   }
   else if ( $_GET['confirmation'] == 'Yes' ) {
      $query = 'SELECT vendor_name, vendor_category FROM vendors WHERE vendor_id = ' . $_GET['idOfVendor'];
      $result = mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure);
      $row = mysqli_fetch_assoc( $result );
      $nameOfVendor = $row['vendor_name'];
      $categoryOfVendor = $row['vendor_category'];

      $query = 'SELECT image_size, category FROM photo_upload WHERE people_id = "VENDOR_' . $_GET['idOfVendor'] . '"';
      $result = mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure);
      $row = mysqli_fetch_assoc($result);

      while ( $row != NULL ) {
         $directoryStoringSnapshotOfUpload = 'images/uploaded' . ucwords($row['category']) . 'Snapshots';
         $fileNameOfSnapshotOfUpload = 'VENDOR_' . $_GET['idOfVendor'] . '@' . $row['image_size'];
         unlink($directoryStoringSnapshotOfUpload . '/' . $fileNameOfSnapshotOfUpload);
         $row = mysqli_fetch_assoc($result);
      }

      unlink ( 'images/vendorFliers/' . $nameOfVendor . '.jpg' );

      $query = 'DELETE FROM photo_upload WHERE people_id = "VENDOR_' . $_GET['idOfVendor'] . '"';
      mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure);

      $query = 'DELETE FROM vendors WHERE vendor_id = ' . $_GET['idOfVendor'];
      mysqli_query($db, $query) or die($markupIndicatingDatabaseQueryFailure);

      displayMarkupsCommonToTopOfPages('Delete Vendor', DISPLAY_NAVIGATION_MENU, 'delete_vendor.php');
?>
            <div id="containerHoldingSuccessMessage">
               <h3>Successfully Deleted Vendor</h3>
               <p>You have successfully deleted the required vendor from RoarConnect's database.</p>
               <p><a href="view_all_items.php?category=<?php echo ucwords($categoryOfVendor) ?>" class="btn btn-default">Click Here</a> to go back to the <?php echo ucwords($categoryOfVendor) ?> Marketplace.</p>
            </div>
<?php
      displayMarkupsCommonToBottomOfPages(DISPLAY_FOOTER);
   }
?>