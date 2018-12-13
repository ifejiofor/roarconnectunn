<?php
require_once 'includes/generalHeaderFile.php';

if ( currentUserIsLoggedIn() ) {
   header( 'Location: homepage.php' );
}
else {
   displayMarkupsCommonToTopOfPages( 'Welcome', DISPLAY_NAVIGATION_MENU, 'index.php' );
?>
         <div id="containerHoldingMyCarousel">
         <div class="container carousel slide" id="myCarousel"  data-ride="carousel">

            <div class="carousel-inner" role="listbox">
               <div class="item active">
                  <a href="view_all_food_vendors.php">
                     <img src="images/carouselImages/snacks.jpg" alt="Snacks" width="100%" height="auto">
                     <div class="carousel-caption" id="noPaddingOnSmallScreens">
                        <h1>Food Delivery</h1>
                        <p>We deliver food from your choice restaurant to your doorstep, Cool?</p>
                     </div>
                  </a>
               </div>

               <div class="item">
                  <a href="blog_home.php?category=Football">
                     <img src="images/carouselImages/money.jpg" alt="Football" width="100%" height="auto">
                     <div class="carousel-caption" id="noPaddingOnSmallScreens">
                        <h1>Sports</h1>
                        <p>Why not get an instant reward for your passion? Predict weekly matches and win instant prizes!</p>
                     </div>
                  </a>
               </div>
               
               <div class="item">
                  <a href="choose_category_of_food.php?idOfVendor=28">
                     <img src="images/carouselImages/sizzlingPizza.jpg" alt="Sizzling Pizza" width="100%" height="auto">
                     <div class="carousel-caption" id="noPaddingOnSmallScreens">
                        <h1>Sizzling Pizza</h1>
                        <p>Get it delivered to your doorstep.</p>
                     </div>
                  </a>
               </div>
               
               <div class="item">
                  <a href="blog_home.php?category=Other">
                     <img src="images/carouselImages/news.jpg" alt="News" width="100%" height="auto">
                     <div class="carousel-caption" id="noPaddingOnSmallScreens">
                        <h1>News</h1>
                        <p>Get latest updates on UNN admission and students' news here.</p>
                     </div>
                  </a>
               </div>

               <div class="item">
                  <a href="view_all_items.php?category=Wears">
                     <img src="images/carouselImages/womenWears.jpg" alt="Women Wears" width="100%" height="auto">
                     <div class="carousel-caption" id="noPaddingOnSmallScreens">
                        <h1>Women's Wears</h1>
                        <p>We give you the best sales and deals on women wears, accessories etc.<br /> Look no more we have got you covered.</p>
                     </div>
                  </a>
               </div>

               <div class="item">
                  <a href="view_all_items.php?category=Wears">
                     <img src="images/carouselImages/menWears.jpg" alt="Men Wears" width="100%" height="auto">
                     <div class="carousel-caption" id="noPaddingOnSmallScreens">
                        <h1>Men's Wears</h1>
                        <p>We can't just forget the men. Can we?<br /> Get the best deals on men clothing and you just don't know what you might get.</p>
                     </div>
                  </a>
               </div>

               <div class="item">
                  <a href="view_all_items.php?category=Books">
                     <img src="images/carouselImages/usedBooks.jpg" alt="Used Books" width="100%" height="auto">
                     <div class="carousel-caption" id="noPaddingOnSmallScreens">
                        <h1>Read and Sell</h1>
                        <p>Sell your used books for good price. You never can tell how useful it maybe.<br /> Discourage book loitering!</p>
                     </div>
                  </a>
               </div>

               <div class="item">
                  <a href="view_all_items.php?category=Gadgets">
                     <img src="images/carouselImages/buyAndSell.jpg" alt="Buy and Sell" width="100%" height="auto">
                     <div class="carousel-caption" id="noPaddingOnSmallScreens">
                        <h1>Buy and Sell</h1>
                        <p>Looking for a platform to sell your products? Simply upload the products here and get real buyers fast.</p>
                     </div>
                  </a>
               </div>

               <div class="item">
                  <a href="view_all_items.php?category=Gadgets">
                     <img src="images/carouselImages/secondHandGoods.jpg" alt="Second Hand Goods" width="100%" height="auto">
                     <div class="carousel-caption" id="noPaddingOnSmallScreens">
                        <h1>Second Hand Goods</h1>
                        <p>Do you want to get people to buy your second-hand goods? Do you want to buy second-hand goods? Then you are at the right place.</p>
                     </div>
                  </a>
               </div>

               <div class="item">
                  <a href="lecture_notes.php">
                     <img src="images/carouselImages/lectureNotes.jpg" alt="Lecture Notes" width="100%" height="auto">
                     <div class="carousel-caption" id="noPaddingOnSmallScreens">
                        <h1>Download Lecture Notes</h1>
                        <p>Every student needs lecture notes to survive. Get your lecture notes with ease here.</p>
                     </div>
                  </a>
               </div>

               <div class="item">
                  <a href="view_all_utility_services.php?category=Catering">
                     <img src="images/carouselImages/gadgets.jpg" alt="Gadgets" width="100%" height="auto">
                     <div class="carousel-caption" id="noPaddingOnSmallScreens">
                        <h1>Utility Services</h1>
                        <p>Get all types of services at your door step with just one dial!</p>
                     </div>
                  </a>
               </div>

               <div class="item">
                  <a href="mailto:roarconnect@roarconnectunn.com">
                     <img src="images/carouselImages/advertisement.jpg" alt="Advertisement" width="100%" height="auto">
                     <div class="carousel-caption" id="noPaddingOnSmallScreens">
                        <h1>Advertise Here</h1>
                        <p>Want to advertise your events, ideas, goods, and services, then contact us!</p>
                     </div>
                  </a>
               </div>

               <div class="item">
                  <a href="#">
                     <img src="images/carouselImages/request.jpg" alt="Make a Request" width="100%" height="auto">
                     <div class="carousel-caption" id="noPaddingOnSmallScreens">
                        <h1>Make a Request</h1>
                        <p>Do you need a product or service and you can't find it on the platform <br />We still got you covered, just make the request and we will attend to your demand!</p>
                     </div>
                  </a>
               </div>

               <div class="item">
                  <a href="index.php">
                     <img src="images/carouselImages/RoarConnectLogo.jpg" alt="RoarConnect Logo" width="100%" height="auto">
                     <div class="carousel-caption" id="noPaddingOnSmallScreens">
                        <h1>RoarConnectunn.com</h1>
                     </div>
                  </a>
               </div>

               <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
                  <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                  <span class="sr-only">Previous</span>
               </a>

               <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
                  <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                  <span class="sr-only">Next</span>
               </a>
            </div>
         </div>
         </div>
         
<?php
   displayLatestStuffs();
   displayMarkupsCommonToBottomOfPages( DISPLAY_FOOTER );
}
?>