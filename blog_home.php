<?php
require_once 'includes/generalHeaderFile.php';
define( 'MAXIMUM_ALLOWABLE_IMAGE_SIZE', 512000 ); // 512000 Bytes is equal to 500 MB
define( 'MAXIMUM_NUMBER_OF_HEADLINES_TO_DISPLAY', 12 );

displayMarkupsCommonToTopOfPages('Latest News and Gists', DISPLAY_NAVIGATION_MENU);
?>
            <header id="minorHeader">
               <h2>Latest news and gists within and around UNN</h2>
            </header>
<?php
displayMarkupForSearchBar('search_for_blog_post.php', 'Search news and gists');

$currentOffset = isset( $_GET['offset'] ) && consistsOfOnlyDigits( $_GET['offset'] ) ? $_GET['offset'] : 0;

$query = 'SELECT *, MONTHNAME(blog_post_time_of_posting) AS blog_post_month_of_posting, DAYOFMONTH(blog_post_time_of_posting) AS blog_post_day_of_posting, YEAR(blog_post_time_of_posting) AS blog_post_year_of_posting FROM blog_posts WHERE blog_post_approval_status = "APPROVED" ORDER BY blog_post_inherent_relevance DESC LIMIT ' . $currentOffset . ', ' .  ( MAXIMUM_NUMBER_OF_HEADLINES_TO_DISPLAY + 1);
$resultContainingBlogPosts = mysqli_query( $db, $query ) or die( $markupIndicatingDatabaseQueryFailure );
$rowContainingBlogPosts = mysqli_fetch_assoc( $resultContainingBlogPosts );
?>

            <section>
<?php
$counter = 0;
while ( $counter < MAXIMUM_NUMBER_OF_HEADLINES_TO_DISPLAY && $rowContainingBlogPosts != NULL ) {
?>
               <div class="col-md-6 col-lg-4">
                  <a href="blog.php?i=<?php echo $rowContainingBlogPosts['blog_post_id'] ?>" id="blogHeadlineContainer">
<?php
	if ( $rowContainingBlogPosts['blog_post_image_filename'] != NULL ) {
?>
                     <img src="images/blogImages/<?php echo $rowContainingBlogPosts['blog_post_image_filename'] ?>" id="<?php echo $counter % 2 == 0 ? 'blogHeadlineImageEven' : 'blogHeadlineImageOdd' ?>" />
<?php
	}
?>
                     <h3 id="blogHeadlineCaption"><?php echo $rowContainingBlogPosts['blog_post_caption'] ?></h3>
                     <p id="blogHeadlineSubdetails"><span class="glyphicon glyphicon-calendar"></span> <?php echo $rowContainingBlogPosts['blog_post_day_of_posting'] . ' ' . $rowContainingBlogPosts['blog_post_month_of_posting'] . ', ' . $rowContainingBlogPosts['blog_post_year_of_posting'] ?></p>
                     <p id="blogHeadlineDetails"><?php echo getTextFromFirstParagraph($rowContainingBlogPosts['blog_post_text']) ?>... <span id="readMore">Read More</span></p>
                  </a>
               </div>
<?php
   $counter++;
	$rowContainingBlogPosts = mysqli_fetch_assoc( $resultContainingBlogPosts );
}
?>
            </section>

            <section class="container-fluid">
<?php
if ( $rowContainingBlogPosts != NULL ) {
?>
               <a href="blog_home.php?offset=<?php echo $currentOffset + MAXIMUM_NUMBER_OF_HEADLINES_TO_DISPLAY ?>" id="specialButtonFloatingToTheRight">Next &gt;&gt;</a>
<?php
}

if ($currentOffset > 0) {
?>
               <a href="blog_home.php?offset=<?php echo $currentOffset - MAXIMUM_NUMBER_OF_HEADLINES_TO_DISPLAY ?>" id="specialButtonFloatingToTheRight">&lt;&lt; Previous</a>
<?php
}
?>

            </section>
<?php
displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
?>