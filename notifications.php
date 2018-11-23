<?php
require_once 'includes/generalHeaderFile.php';

if ( !userIsLoggedIn() ) {
   header( 'Location: index.php' );
}

if ( isset( $_POST['visitNotificationURL'] ) ) {
   $query = 'UPDATE notifications SET notification_status = "READ" WHERE notification_id = ' . $_POST['notificationID'];
   mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
   header( 'Location: ' . $_POST['notificationURL'] );
}

if ( isset( $_POST['clearAllNotifications'] ) ) {
   $query = 'DELETE FROM notifications WHERE user_id_of_recipient = ' . $_SESSION['user_id'];
   mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );

   header( 'Location: notifications.php' );
}

displayMarkupsCommonToTopOfPages( 'Notifications', DISPLAY_NAVIGATION_MENU, 'notifications.php' );

?>
            <header id="minorHeaderType2">
               <h2>RoarConnect Notifications</h2>
               
               <form method="POST" action="notifications.php">
                  <button type="submit" name="clearAllNotifications" class="btn btn-primary">Clear Notifications</button>
               </form>
            </header>
            
            <section>
            
<?php
$query = 'SELECT notification_id, notification_text, notification_status, notification_url, reason_for_notification, id_of_item, MONTHNAME( notification_time_of_notifying ) AS month_of_notifying, DAYOFMONTH( notification_time_of_notifying ) AS day_of_notifying, HOUR( notification_time_of_notifying ) AS hour_of_notifying, MINUTE( notification_time_of_notifying ) AS minute_of_notifying FROM notifications WHERE user_id_of_recipient = ' . $_SESSION['user_id'] . ' ORDER BY notification_time_of_notifying DESC';
$resultContainingNotifications = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );

if ( mysqli_num_rows( $resultContainingNotifications ) == 0 ) {
?>
               <p id="mediumSizedText">No Notifications.</p>
<?php
}
else {
   $rowContainingNotification = mysqli_fetch_assoc( $resultContainingNotifications );

   while ( $rowContainingNotification != NULL ) {
?>
               <div class="text-center" id="containerWithBorderAndWithoutRoundedCorners">
                  <form method="POST" action="notifications.php">
                     <input type="hidden" name="notificationURL" value="<?php echo $rowContainingNotification['notification_url'] ?>" />
                     <input type="hidden" name="notificationID" value="<?php echo $rowContainingNotification['notification_id'] ?>" />

                     <button type="submit" name="visitNotificationURL" style="width: 100%;" class="btn btn-default">
                        <p id="<?php echo $rowContainingNotification['notification_status'] == 'READ' ? 'readMessage' : 'unreadMessage' ?>"><?php echo $rowContainingNotification['notification_text'] ?></p>
                        <p id="<?php echo $rowContainingNotification['notification_status'] == 'READ' ? 'readMessage' : 'unreadMessage' ?>"><?php echo $rowContainingNotification['month_of_notifying'] . ' ' . $rowContainingNotification['day_of_notifying'] . ' at ' . formatTimeAsAmOrPm( $rowContainingNotification['hour_of_notifying'] + 5, $rowContainingNotification['minute_of_notifying'] ) ?></p>
                     </button>
                  </form>
               
<?php
      if ( $rowContainingNotification['reason_for_notification'] == 'DELETION OF PHOTO UPLOAD' || $rowContainingNotification['reason_for_notification'] == 'DELETION OF BLOG POST' ) {
         $queryToRetrieveReasonForDeletion = 'SELECT reason FROM reasons_for_admin_actions_on_items WHERE type_of_item = "' . ( $rowContainingNotification['reason_for_notification'] == 'DELETION OF PHOTO UPLOAD' ? "PHOTO UPLOAD" : "BLOG POST" )  . '" AND id_of_item = ' . $rowContainingNotification['id_of_item'];
         $resultContainingReasonForDeletion = mysqli_query( $db, $queryToRetrieveReasonForDeletion ) or die( $markupIndicatingDatabaseQueryFailure );
         $rowContainingReasonForDeletion = mysqli_fetch_assoc( $resultContainingReasonForDeletion );
         $idOfModal = $rowContainingNotification['reason_for_notification'] == 'DELETION OF PHOTO UPLOAD' ? "reasonForAdminActionOnPhotoUpload" . $rowContainingNotification['id_of_item'] : "reasonForAdminActionOnBlogPost" . $rowContainingNotification['id_of_item'];
?>
                  <p><a href="#" data-toggle="modal" data-target="#<?php echo $idOfModal ?>">Click Here</a> to know why.</p>
                  
<?php
         displayMarkupForReasonForAdminActionModal( 'Deletion', $rowContainingReasonForDeletion['reason'], $idOfModal );
      }
      
?>
               </div>
               
<?php
      $rowContainingNotification = mysqli_fetch_assoc( $resultContainingNotifications );
   }
}
?>
            </section>
<?php
displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
?>