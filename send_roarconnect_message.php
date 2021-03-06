<?php
// TODO: This code need to still be modified such that only users who are logged in can send message as this will enable that their recipients know it is a real person that sent the message and also make it possible for the reply functionality to work
require_once 'includes/generalHeaderFile.php';

displayMarkupsCommonToTopOfPages( 'Send Message', DISPLAY_NAVIGATION_MENU, 'send_roarconnect_message.php' );

if ( !$_POST ) {
   if ( !isset( $_GET['defaultIdOfMessageRecipient'] ) || !consistsOfOnlyDigits( $_GET['defaultIdOfMessageRecipient'] )  ) {
      header( 'Location: index.php' );
   }
   
   if ( isset( $_GET['recipientIsAVendorManager'] ) && $_GET['recipientIsAVendorManager'] == 'true' && !isset( $_GET['idOfVendor'] ) ) {
      header( 'Location: index.php' );
   }
   
   if ( isset( $_GET['idOfVendor'] ) && !consistsOfOnlyDigits( $_GET['idOfVendor'] ) ) {
      header( 'Location: index.php' );
   }
   
   
   if ( isset( $_GET['recipientIsAVendorManager'] ) && $_GET['recipientIsAVendorManager'] == 'true' ) {
      $query = 'SELECT vendor_name, vendor_email FROM vendors WHERE vendor_id = ' . $_GET['idOfVendor'] . ' AND user_id_of_vendor_manager = ' . $_GET['defaultIdOfMessageRecipient'];
      $resultContainingDataAboutRecipientVendor = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
      $rowContainingDataAboutRecipientVendor = mysqli_fetch_assoc( $resultContainingDataAboutRecipientVendor );
      
      $query = 'SELECT firstname, email FROM users WHERE id = ' . $_GET['defaultIdOfMessageRecipient'];
      $resultContainingDataAboutMessageRecipient = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
      $rowContainingDataAboutMessageRecipient = mysqli_fetch_assoc( $resultContainingDataAboutMessageRecipient );
      
      $nameOfMessageRecipient = $rowContainingDataAboutMessageRecipient['firstname'] . ' (Manager of ' . $rowContainingDataAboutRecipientVendor['vendor_name'] . ')';
      $emailAddressOfMessageRecipient = $rowContainingDataAboutRecipientVendor['vendor_email'];
   }
   else {
      $query = 'SELECT firstname, email FROM users WHERE id = ' . $_GET['defaultIdOfMessageRecipient'];
      $resultContainingDataAboutMessageRecipient = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
      $rowContainingDataAboutMessageRecipient = mysqli_fetch_assoc( $resultContainingDataAboutMessageRecipient );
      
      $nameOfMessageRecipient = $rowContainingDataAboutMessageRecipient['firstname'];
      $emailAddressOfMessageRecipient = $rowContainingDataAboutMessageRecipient['email'];
   }
?>
            <header id="minorHeader">
               <h2>Send Message</h2>
            </header>

            <form method="POST" action="send_roarconnect_message.php" class="form-horizontal" id="looksLikeACardboardPaper">
               <input type="hidden" name="urlOfSourcePage" value="<?php echo isset( $_GET['urlOfSourcePage'] ) ? $_GET['urlOfSourcePage'] : 'inbox.php' ?>"/>
               
               <div class="form-group">
                  <label class="control-label col-sm-2">Title</label>
                  <div class="col-sm-10"><input type="text" name="messageTitle" value="<?php echo isset( $_GET['defaultMessageTitle'] ) ? $_GET['defaultMessageTitle'] : '' ?>" class="form-control" /></div>
               </div>
               
               <div class="form-group">
                  <input type="hidden" name="userIdOfMessageRecipient" value="<?php echo $_GET['defaultIdOfMessageRecipient'] ?>" />
                  <input type="hidden" name="emailAddressOfMessageRecipient" value="<?php echo $emailAddressOfMessageRecipient ?>"/>
                  
                  <label class="control-label col-sm-2">To</label>
                  <div class="col-sm-10"><input type="text" name="firstNameOfMessageRecipient" value="<?php echo $nameOfMessageRecipient ?>" class="form-control" disabled /></div>
               </div>
               
               <div>
                  <label class="control-label">Message Body:</label>
                  <div><textarea name="messageBody" class="form-control" id="bigSizedTextArea"><?php echo isset($_GET['defaultMessageBody']) ? $_GET['defaultMessageBody'] : '' ?></textarea></div>
               </div>
               
               <div class="form-group">
                  <div class="col-sm-8"><button type="submit" name="sendMessageButton" class="btn btn-success">Send</button></div>
               </div>
            </form>
<?php
}
else {
   $messageTitle = htmlentities( trim( $_POST['messageTitle'] ) );
   $messageBody = separateAllLinesOfTextWithParagraphTags( htmlentities( trim( $_POST['messageBody'] ) ) );
   
   $query = 'INSERT INTO messages ( message_title, message_body, user_id_of_sender, user_id_of_recipient, message_time_of_sending ) VALUES ( "' . mysqli_real_escape_string( $globalHandleToDatabase, $messageTitle ) . '", "' . mysqli_real_escape_string( $globalHandleToDatabase, $messageBody ) . '", ' . $_SESSION['user_id'] . ', ' . $_POST['userIdOfMessageRecipient'] . ', NOW() )';
   mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
   
   $email = $_POST['emailAddressOfMessageRecipient'];
   //include 'includes/sendmailinboxmessage.php'; // Send an email that indicates that a RoarConnect message has been sent
?>
            <div id="containerHoldingSuccessMessage">
               <h1>Message Sent Successfully</h1>
               <p>Your message have been sent.</p>
               <p><a href="<?php echo $_POST['urlOfSourcePage'] ?>" class="btn btn-default">Click Here</a> to go back.</p>
            </div>
<?php
}

displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
?>