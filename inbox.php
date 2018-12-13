<?php
require_once 'includes/generalHeaderFile.php';

if ( !currentUserIsLoggedIn() ) {
   header( 'Location: index.php' );
}

if ( isset( $_GET['requiredAction'] ) && !isset( $_GET['idOfMessage'] ) ) {
   header( 'Location: index.php' );
}

if ( isset( $_GET['idOfMessage'] ) && !consistsOfOnlyDigits( $_GET['idOfMessage'] ) ) {
   header( 'Location: index.php' );
}

displayMarkupsCommonToTopOfPages( 'Inbox', DISPLAY_NAVIGATION_MENU, 'inbox.php' );


if ( !$_GET ) {
?>
            <header id="minorHeader">
               <h2>Your RoarConnect Inbox</h2>
            </header>
            
<?php
   $query = 'SELECT message_id, user_id_of_sender, message_title, message_status, HOUR( message_time_of_sending ) AS hour_of_sending, MINUTE( message_time_of_sending ) AS minute_of_sending, MONTHNAME( message_time_of_sending ) AS month_of_sending, DAYOFMONTH( message_time_of_sending ) AS day_of_sending FROM messages WHERE user_id_of_recipient = ' . $_SESSION['user_id'] . ' ORDER BY message_time_of_sending DESC';
   $resultContainingMessages = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );

   if ( mysqli_num_rows( $resultContainingMessages ) == 0 ) {
?>
            <p id="mediumSizedText">No messages.</p>
<?php
   }
   else {
?>
            <table class="table table-hover">
<?php
      $rowContainingMessage = mysqli_fetch_assoc( $resultContainingMessages );
   
      while( $rowContainingMessage != NULL ) {
         if ( $rowContainingMessage['user_id_of_sender'] != NULL ) {
            $query = 'SELECT firstname FROM users WHERE id = ' . $rowContainingMessage['user_id_of_sender'];
            $resultContainingDataAboutSender = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
            $rowContainingDataAboutSender = mysqli_fetch_assoc( $resultContainingDataAboutSender );
            $firstNameOfSender = $rowContainingDataAboutSender['firstname'];
         }
         else {
            $firstNameOfSender = 'RoarConnect';
         }
?>
               <tr <?php echo $rowContainingMessage['message_status'] == 'UNREAD' ? 'id="unreadMessage"' : 'id="readMessage"' ?>>
                  <td><a href="inbox.php?requiredAction=readMessage&idOfMessage=<?php echo $rowContainingMessage['message_id'] ?>" id="linkWithFullWidth"><?php echo $firstNameOfSender ?></a></td>
                  <td><a href="inbox.php?requiredAction=readMessage&idOfMessage=<?php echo $rowContainingMessage['message_id'] ?>" id="linkWithFullWidth"><?php echo $rowContainingMessage['month_of_sending'] . ', ' . $rowContainingMessage['day_of_sending'] . ' at ' . formatTimeAsAmOrPm( $rowContainingMessage['hour_of_sending'] + 5, $rowContainingMessage['minute_of_sending'] ) ?></a></td>
                  <td><a href="inbox.php?requiredAction=readMessage&idOfMessage=<?php echo $rowContainingMessage['message_id'] ?>" id="linkWithFullWidth"><?php echo $rowContainingMessage['message_title'] ?></a></td>
               </tr>
<?php
         $rowContainingMessage = mysqli_fetch_assoc( $resultContainingMessages );
      }
   
?>
            </table>
            
<?php
   }
}
else if ( isset( $_GET['requiredAction'] ) && $_GET['requiredAction'] == 'readMessage' ) {
?>
            <header id="minorHeaderType2">
               <h2>RoarConnect Message</h2>
               <a href="inbox.php">&lt;&lt; Go Back to Inbox</a>
            </header>
            
<?php
   $query = 'SELECT message_id, message_title, user_id_of_sender, message_body FROM messages WHERE message_id = ' . $_GET['idOfMessage'];
   $resultContainingMessage = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
   $rowContainingMessage = mysqli_fetch_assoc( $resultContainingMessage );
   
   if ( $rowContainingMessage['user_id_of_sender'] != NULL ) {
      $query = 'SELECT firstname FROM users WHERE id = ' . $rowContainingMessage['user_id_of_sender'];
      $resultContainingDataAboutSender = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
      $rowContainingDataAboutSender = mysqli_fetch_assoc( $resultContainingDataAboutSender );
      $firstNameOfSender = $rowContainingDataAboutSender['firstname'];
   }
   else {
      $firstNameOfSender = 'RoarConnect';
   }
?>
            <div id="containerWithBorderAndWithoutRoundedCorners">
               <p id="containerHoldingMessageHeading"><span class="col-sm-4" id="labelForMessageHeading">Title</span><span class="col-sm-8" id="messageHeading"><?php echo $rowContainingMessage['message_title'] ?></span></p>
               <p id="containerHoldingMessageHeading"><span class="col-sm-4" id="labelForMessageHeading">Sender</span><span class="col-sm-8" id="messageHeading"><?php echo $firstNameOfSender ?></span></p>
   
               <div id="messageBody"><?php echo $rowContainingMessage['message_body'] ?></div>
               
               <p><?php echo $rowContainingMessage['user_id_of_sender'] != NULL ? '<a href="inbox.php?requiredAction=replyMessage&idOfMessage=' . $rowContainingMessage['message_id'] . '" class="btn btn-link"><span class="glyphicon glyphicon-send"></span> Reply</a>' : '' ?> <a href="inbox.php?requiredAction=deleteMessage&idOfMessage=<?php echo $rowContainingMessage['message_id']?>" class="btn btn-link"><span class="glyphicon glyphicon-trash"></span> Delete</a></p>
            </div>
<?php
   $query = 'UPDATE messages SET message_status = "READ" WHERE message_id = ' . $_GET['idOfMessage'];
   mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
}
else if ( isset( $_GET['requiredAction'] ) && $_GET['requiredAction'] == 'deleteMessage' ) {
   $query = 'DELETE FROM messages WHERE message_id = ' . $_GET['idOfMessage'];
   mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
   header( 'Location: inbox.php' );
}
else if ( isset( $_GET['requiredAction'] ) && $_GET['requiredAction'] == 'replyMessage' ) {
   $query = 'SELECT message_title, user_id_of_sender FROM messages WHERE message_id = ' . $_GET['idOfMessage'];
   $resultContainingMessageToReplyTo = mysqli_query( $globalHandleToDatabase, $query ) or die( $globalDatabaseErrorMarkup );
   $rowContainingMessageToReplyTo = mysqli_fetch_assoc( $resultContainingMessageToReplyTo );
   
   $urlOfSourcePage = 'inbox.php';
   $defaultMessageTitle = substr( $rowContainingMessageToReplyTo['message_title'], 0, 4 ) != 'RE: ' ? 'RE: ' . $rowContainingMessageToReplyTo['message_title'] : $rowContainingMessageToReplyTo['message_title'];
   $defaultIdOfMessageRecipient = $rowContainingMessageToReplyTo['user_id_of_sender'];
   $defaultMessageBody = '';
   
   header( 'Location: send_roarconnect_message.php?urlOfSourcePage=' . $urlOfSourcePage . '&defaultMessageTitle=' . $defaultMessageTitle . '&defaultIdOfMessageRecipient=' . $defaultIdOfMessageRecipient . '&defaultMessageBody='. $defaultMessageBody );
}
else {
   header( 'Location: index.php' );
}


displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
?>