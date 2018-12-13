<?php
require_once 'includes/generalHeaderFile.php';
define( 'MAXIMUM_NUMBER_OF_HEADLINES_TO_DISPLAY', 12 );

displayMarkupsCommonToTopOfPages('News and Gists', DISPLAY_NAVIGATION_MENU);
displayMarkupForSearchBar('search_for_blog_post.php', 'Search news and gists');
?>
            <header id="minorHeader">
               <h2>RoarConnect brings you the most exciting news and gists within and outside UNN</h2>
            </header>

            <section>
<?php
$latestBlogPosts = getArrayOfDataAboutLatestBlogPosts(MAXIMUM_NUMBER_OF_HEADLINES_TO_DISPLAY + 1);

for ($index = 0; $index < MAXIMUM_NUMBER_OF_HEADLINES_TO_DISPLAY && $index < count($latestBlogPosts); $index++) {
   $blogPost = $latestBlogPosts[$index];
?>
               <div class="col-md-6 col-lg-4">
                  <a href="blog.php?i=<?php echo $blogPost['blog_post_id'] ?>" id="blogHeadlineContainer">
<?php
	if ( $blogPost['blog_post_image_filename'] != NULL ) {
?>
                     <img src="images/blogImages/<?php echo $blogPost['blog_post_image_filename'] ?>" id="<?php echo $index % 2 == 0 ? 'blogHeadlineImageEven' : 'blogHeadlineImageOdd' ?>" />
<?php
	}
?>
                     <h3 id="blogHeadlineCaption"><?php echo $blogPost['blog_post_caption'] ?></h3>
                     <p id="blogHeadlineSubdetails"><span class="glyphicon glyphicon-calendar"></span> <?php echo $blogPost['blog_post_day_of_posting'] . ' ' . $blogPost['blog_post_month_of_posting'] . ', ' . $blogPost['blog_post_year_of_posting'] ?></p>
                     <p id="blogHeadlineDetails"><?php echo getTextFromFirstParagraph($blogPost['blog_post_text']) ?>... <span id="readMore">Read More</span></p>
                  </a>
               </div>
<?php
   $globalBlogPostsDisplayedInCurrentPage[] = $blogPost;
}
?>
            </section>

            <section class="container-fluid" id="notFloating">
<?php
$currentOffset = isset( $_GET['offset'] ) && consistsOfOnlyDigits( $_GET['offset'] ) ? $_GET['offset'] : 0;

if ( $index < count($latestBlogPosts) ) {
?>
               <a href="blog_home.php?offset=<?php echo $currentOffset + MAXIMUM_NUMBER_OF_HEADLINES_TO_DISPLAY ?>" id="specialButtonFloatingToTheRight">More Gists <span class="fa fa-angle-double-right"></span></a>
<?php
}

if ($currentOffset > 0) {
?>
               <a href="blog_home.php?offset=<?php echo $currentOffset - MAXIMUM_NUMBER_OF_HEADLINES_TO_DISPLAY ?>" id="specialButtonFloatingToTheRight"><span class="fa fa-angle-double-left"></span> Previous Gists</a>
<?php
}
?>
            </section>
<?php
displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
?>