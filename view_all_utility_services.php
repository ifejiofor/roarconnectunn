<?php
if ( !isset( $_GET['category'] ) ) {
   header( 'Location: index.php' );
}
else if ( $_GET['category'] != 'Painting'&& $_GET['category'] != 'BeautyService' && $_GET['category'] != 'HaircutService' && $_GET['category'] != 'DataServices' && $_GET['category'] != 'Catering' && $_GET['category'] != 'Arts' && $_GET['category'] != 'ElectricalWorks'  && $_GET['category'] != 'GraphicsDesigning' ) {
   header( 'Location: index.php' );
}
else {
	require_once 'includes/generalHeaderFile.php';

   if ( $_GET['category'] == 'Painting' ) {
      $nameOfService = 'Painting';
      $nameOfServiceProvider = 'Painter';
   }
   else if ( $_GET['category'] == 'Catering' ) {
      $nameOfService = 'Catering';
      $nameOfServiceProvider = 'Caterer';
   }
   else if ( $_GET['category'] == 'ElectricalWorks' ) {
      $nameOfService = 'Electrical';
      $nameOfServiceProvider = 'Electrician';
   }else if ( $_GET['category'] == 'Arts' ) {
      $nameOfService = 'Art';
      $nameOfServiceProvider = 'Artist';
   }else if ( $_GET['category'] == 'HaircutService' ) {
      $nameOfService = 'HaircutService';
      $nameOfServiceProvider = 'Barber';
   }else if ( $_GET['category'] == 'GraphicsDesigning' ) {
      $nameOfService = 'Graphics Designing and Video Editing';
      $nameOfServiceProvider = 'Graphic Designer and Video Editor';
   }else if ( $_GET['category'] == 'BeautyService' ) {
      $nameOfService = 'Beauty Service';
      $nameOfServiceProvider = 'Beautician';
   }else if ( $_GET['category'] == 'DataServices' ) {
      $nameOfService = 'Data';
      $nameOfServiceProvider = 'Data Provider';
   }

   displayMarkupsCommonToTopOfPages( 'Contact a ' . $nameOfServiceProvider, DISPLAY_NAVIGATION_MENU, 'view_all_utility_services.php?category=' . $_GET['category'] );
?>
            <header id="minorHeader">
               <h2>Welcome to RoarConnect <?php echo $nameOfService ?> Services Marketplace</h2>
               <p id="minorTextInMinorHeader">Here, you can contact a competent <?php echo strtolower( $nameOfServiceProvider ) ?> to do your <?php echo strtolower( $nameOfService ) ?> works.</p>
            </header>

<?php
   $query = 'SELECT vendor_id, vendor_name FROM vendors WHERE vendor_category = "' . $_GET['category'] . '"';
   $result = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );

   if ( mysqli_num_rows( $result ) > 0 ) {
?>
            <section>
               <header id="minorHeaderType2">
                  <h3><?php echo mysqli_num_rows( $result ) == 1 ? 'Storefront' : 'Storefronts' ?> of RoarConnect Special <?php echo $nameOfServiceProvider . ( mysqli_num_rows( $result ) == 1 ? '' : 's' ) ?></h3>
<?php
      if ( userIsLoggedInAsAdmin() ) {
?>
                  <p><a href="add_or_edit_vendor.php?requiredAction=addVendor" class="btn btn-warning">Add a New <?php echo $nameOfServiceProvider ?></a></p>
<?php
      }
?>
               </header>
<?php
      $row = mysqli_fetch_assoc( $result );
      while ( $row != NULL ) {
?>

               <div id="smallContainerWithBorderAndAllowsOverflow">
                  <h2 id="overflowingHeader"><?php echo $row['vendor_name'] ?></h2>
                  <img src="images/vendorFliers/<?php echo $row['vendor_name'] . '.jpg' ?>" alt="<?php echo $row['vendor_name'] ?>'s Flier" width="100%" height="auto" />

                  <div class="text-center">
<?php
         if ( userIsLoggedIn() ) {
?>
                     <a href="view_uploads_by_vendor.php?vendor=<?php echo $row['vendor_id'] ?>" class="btn btn-default btn-lg" id="boldSmallSizedText">Click Here</a>
<?php
         }
         else {
?>
                     <div id="displayAsInlineBlock">
<?php
            $markupToDisplayWithinButton = 'Click Here';
            $miscellaneousAttributesOfButton = 'class="btn btn-default btn-lg" id="boldSmallSizedText"';
            getMarkupForButtonThatWillTellUserToLogInBeforeContinuing( $markupToDisplayWithinButton, $miscellaneousAttributesOfButton );
?>
                     </div>
<?php
         }
?>

                     to preview some <?php echo strtolower( $nameOfService ) ?> jobs done by <?php echo $row['vendor_name'] ?>.
                  </div>
<?php
      if ( userIsLoggedInAsAdmin() ) {
?>
                  <p class="text-center">Also, you may either</p>
                  <p class="text-center"><a href="add_or_edit_vendor.php?requiredAction=editVendor&idOfVendor=<?php echo $row['vendor_id'] ?>" class="btn btn-warning">Click Here</a> to edit details about <?php echo $row['vendor_name'] ?>.</p>
                  <p class="text-center">Or you may <a href="delete_vendor.php?idOfVendor=<?php echo $row['vendor_id'] ?>" class="btn btn-warning">Click Here</a> to delete <?php echo $row['vendor_name'] ?> from RoarConnect's database</p>
<?php
      }
?>
               </div>
<?php
         $row = mysqli_fetch_assoc( $result );
      }
?>
            </section>

<?php
   }
   
   if ( !userIsLoggedIn() ) {
      getMarkupForModalThatTellsUserToLogInBeforeContinuing();
   }

	displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );	
}
?>