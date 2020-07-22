<?php
require_once 'includes/generalHeaderFile.php';

displayMarkupsCommonToTopOfPages( 'Utility Services', DISPLAY_NAVIGATION_MENU, 'view_all_utility_services.php' );
?>
            <header id="minorHeader">
               <h2>Welcome to RoarConnect's Utility Services Marketplace</h2>
               <p>Below are our recommended utility service providers. If you want to do business with any of them, simply contact them now.</p>
<?php
   if ( currentUserIsLoggedInAsAdmin() ) {
?>
                  <p><a href="add_or_edit_vendor.php?requiredAction=addVendor" class="btn btn-primary">Add New Vendor</a></p>
<?php
   }
?>
            </header>

<?php
$query = 'SELECT vendor_id, vendor_name, vendor_category FROM vendors WHERE vendor_category != "foods" ORDER BY vendor_category, vendor_name';
$resultContainingVendorData = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );

for ( $rowContainingVendorData = mysqli_fetch_assoc( $resultContainingVendorData ); $rowContainingVendorData != NULL; $rowContainingVendorData = mysqli_fetch_assoc( $resultContainingVendorData ) ) {
?>
            <section id="looksLikeABigPaperCard">
               <a href="view_uploads_by_vendor.php?i=<?php echo $rowContainingVendorData['vendor_id'] ?>">
                  <header id="headerOfPaperCard">
                     <h2><?php echo $rowContainingVendorData['vendor_name'] ?></h2>
                  </header>
                  <img src="assets/images/vendorFliers/<?php echo $rowContainingVendorData['vendor_name'] . '.jpg' ?>" alt="<?php echo $rowContainingVendorData['vendor_name'] ?>'s Flier" width="100%" height="auto" />
                  <div class="text-center" id="tinyMargin"><span class="btn btn-default">View Details</span></div>
               </a>
<?php
   if ( currentUserIsLoggedInAsAdmin() ) {
?>
               <div class="text-center" id="tinyMargin">
                  <a href="add_or_edit_vendor.php?requiredAction=editVendor&idOfVendor=<?php echo $rowContainingVendorData['vendor_id'] ?>" class="btn btn-primary">Edit Vendor</a>
                  <a href="delete_vendor.php?idOfVendor=<?php echo $rowContainingVendorData['vendor_id'] ?>" class="btn btn-primary">Delete Vendor</a>
               </div>
<?php
   }
?>
            </section>

<?php
}

displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
?>