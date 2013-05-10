<?php
  $photoSet = ($_GET['set'])?$_GET['set']:$photoSet;
  include('../inc/flickrSet.php');
  $flickrUsr = 'neworleanshog';
  $apiKey = '0b5febc3341732163d9aff154d5bb26c';
  $flickrURL = "http://api.flickr.com/services/rest/?method=flickr.photosets.getPhotos&extras=url_o&photoset_id=$photoSet&api_key=$apiKey&nsid=55110416@N03&lang=en-us&format=json&jsoncallback=?";
  $albumTitle = $flickrSets[$photoSet];  
  if (empty($albumTitle)) { $albumTitle = 'Very Cool'; }

  $description[1] = 'Check out the amazing photos from our New Orleans HOG photo album: '.$albumTitle;
  $description[2] = 'Check out these great photos of our members, their Harley-Davidsons and our adventures together: '.$albumTitle;
  $description[3] = 'You simply must see these awesome photos from our photo album '.$albumTitle.' on NewOrleansHOG.com';

  $showDesc = $description[array_rand($description)];   

  $flickrURL = "http://api.flickr.com/services/feeds/photoset.gne?set=$photoSet&nsid=55110416@N03&format=json&jsoncallback=?";
include('../inc/universalHead.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en"> 
  <head>
    <title><?php echo $albumTitle.' Photos - New Orleans Harley Owners Group (HOG)'; ?></title> <!-- this is replaced with set title from flickr by json call -->
    <meta property="og:title" content="<?php echo $albumTitle.' Photos - New Orleans Harley Owners Group (HOG)'; ?>" />
    <meta http-equiv="Content-Type"	content="text/html; charset=iso-8859-1" /> 
    <meta name="keywords" content="New Orleans,Harley Owners Group,HOG,Harley-Davidson" /> 
    <meta name="description" content="<?php echo $showDesc; ?>" /> 
    <?php include('../inc/universalHead.php'); ?> 

		<link rel="stylesheet" href="/css/galleriffic-2.css" type="text/css" />
		<script type="text/javascript" src="/inc/js/jquery.js"></script>
    <script type="text/javascript" src="/inc/js/jquery.galleriffic.js"></script>
		<script type="text/javascript" src="/inc/js/jquery.opacityrollover.js"></script>
		<!-- We only want the thunbnails to display when javascript is disabled -->
		<script type="text/javascript">
			document.write('<style>.noscript { display: none; }</style>');
		</script>

  </head>
    <body>
    
    <div id="container">

      <div id="header">
      </div><!-- end header-->
      <div id="middleleft">
        <div id="middleright">
          <!-- begin left menu-->
            <?php include('../inc/menu.php'); ?>
          <!-- end menu-->
          <div id="content">    
 
    				<center><div id="setTitle"><h1>Loading Photo Album...</h1>If this page does not load within 30 seconds <a href="/photo-album.php">return to the album list</a> and try again<br/><br/></div>
              
    				</center>
    

				<!-- Start Advanced Gallery Html Containers -->

				<br />
        <div id="thumbs" class="navigation">
					<ul class="thumbs noscript">
						<li>
							<a class="thumb" name="leaf" href="" title="">
								<img src="" alt="Title #0" />
							</a>
							<div class="caption">
							  <div class="image-title"></div>
								<div class="image-desc"></div>
							</div>
						</li>
					</ul>
				</div>
        <div id="controls" class="controls" align="center"></div>
        <div class="caption"></div>  
          <div id="gallery" class="content">
  					<div class="slideshow-container">
  						<div id="loading" class="loader"></div>
  						<div id="slideshow" class="slideshow"></div>
  					</div>
  				</div>
				<div id="caption" class="caption-container" style="left:10%"></div>
				<div id="thumbs" class="navigation">
					<ul class="thumbs noscript">
            <li>
							
              <a class="thumb" name="" href="" title="">
								<img src="" alt="" />
							</a>
						</li>
					</ul>
				</div>
				
				<div style="clear: both;"></div>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
        $.getJSON("<?=$flickrURL?>", function(data){
          var setTitle = data.title;
          setTitle = setTitle.replace("Content from", "");
            $("#setTitle").replaceWith("<h1>"+setTitle+"</h1><a target=\"_blank\" href=\"http://www.flickr.com/photos/<?=$flickrUsr?>/sets/<?=$photoSet?>/show\">View Videos & Photos from this Album Full Screen on Flickr</a><br><a href=\"/photo-album.php\">Return to Albums</a>");
            $("title").replaceWith("<title>"+setTitle+" Photo Album from New Orleans HOG</title>");


						$.each(data.items, function(i,item){
							var newthumb = $("ul.thumbs").children("li:first").clone();
							var baseimg = item.media.m; 
							
							var thumbimg = baseimg.replace("_m.jpg", "_s.jpg");
							$(newthumb).find("img").attr("src", thumbimg);
							
							var disimg = baseimg.replace("_m.jpg", "_z.jpg");
							$(newthumb).find(".thumb").attr("href", disimg);
							
							var lgeimg = baseimg.replace("_m.jpg", "_b.jpg");
              $(newthumb).find(".download").children("a").attr("href", lgeimg);
							
							var title = item.title;
							var description = item.description;

							var desc = $("<div />").append(description);
							if ($(desc).children().size() == 3) {
								description = $(desc).children("p:last").html();
							} else {
								description = "";
							}
							
							$(newthumb).find(".image-title").empty().html(title);
							$(newthumb).find(".image-desc").empty().html(description);
							$(newthumb).find(".image-auth").empty().html(item.author);
							
							$("ul.thumbs").append(newthumb);
						});	
						
						$("ul.thumbs").children("li:first").remove();
						
						// Initially set opacity on thumbs and add
						// additional styling for hover effect on thumbs
						var onMouseOutOpacity = 0.67;
						$('#thumbs ul.thumbs li').opacityrollover({
							mouseOutOpacity:   onMouseOutOpacity,
							mouseOverOpacity:  1.0,
							fadeSpeed:         'fast',
							exemptionSelector: '.selected'
						});
						
						// Initialize Advanced Galleriffic Gallery
						var gallery = $('#thumbs').galleriffic({
							delay:                     3500,
							numThumbs:                 8,
							preloadAhead:              8,
							enableTopPager:            false,
							enableBottomPager:         true,
							maxPagesToShow:            15,
							imageContainerSel:         '#slideshow',
							controlsContainerSel:      '#controls',
							captionContainerSel:       '#caption',
							loadingContainerSel:       '#loading',
							renderSSControls:          true,
							renderNavControls:         true,
							playLinkText:              '<h3>Play Slideshow</h3>',
							pauseLinkText:             '<h3>Pause Slideshow</h3>',
							prevLinkText:              '&lsaquo; Previous Photo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
							nextLinkText:              '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Next Photo &rsaquo;',
							nextPageLinkText:          'Next &rsaquo;',
							prevPageLinkText:          '&lsaquo; Prev',
							enableHistory:             false,
							autoStart:                 false,
							syncTransitions:           true,
							defaultTransitionDuration: 900,
							onSlideChange:             function(prevIndex, nextIndex) {
								// 'this' refers to the gallery, which is an extension of $('#thumbs')
								this.find('ul.thumbs').children()
									.eq(prevIndex).fadeTo('fast', onMouseOutOpacity).end()
									.eq(nextIndex).fadeTo('fast', 1.0);
							},
							onPageTransitionOut:       function(callback) {
								this.fadeTo('fast', 0.0, callback);
							},
							onPageTransitionIn:        function() {
								this.fadeTo('fast', 1.0);
							}
						});						
				});
			
				// We only want these styles applied when javascript is enabled
				$('div.navigation').css({'width' : '800px', 'float' : 'left'});
				$('div.content').css('display', 'block');
			});

		</script>
<?php include('../inc/fb_like.php'); ?>

          </div><!-- end content-->
        </div><!-- end middleright-->
      </div><!-- end middleleft-->      

      <!-- begin footer-->
        <?php include('../inc/footer.php'); ?>
      <!-- end footer-->
    </div><!-- end container-->
  </body>
</html>