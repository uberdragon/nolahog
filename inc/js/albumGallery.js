jQuery(document).ready(function($) {
    $.getJSON("http://api.flickr.com/services/feeds/groups_pool.gne?id=neworleanshog&lang=en-us&format=json&jsoncallback=?", function(data){
            $.each(data.items, function(i,item){
                var newthumb = $("ul.thumbs").children("li:first").clone();
                var baseimg = item.media.m;
                
                var thumbimg = baseimg.replace("_m.jpg", "_s.jpg");
                $(newthumb).find("img").attr("src", thumbimg);
                
                var disimg = baseimg.replace("_m.jpg", ".jpg");
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
                delay:                     2500,
                numThumbs:                 12,
                preloadAhead:              10,
                enableTopPager:            true,
                enableBottomPager:         true,
                maxPagesToShow:            7,
                imageContainerSel:         '#slideshow',
                controlsContainerSel:      '#controls',
                captionContainerSel:       '#caption',
                loadingContainerSel:       '#loading',
                renderSSControls:          true,
                renderNavControls:         true,
                playLinkText:              'Play Slideshow',
                pauseLinkText:             'Pause Slideshow',
                prevLinkText:              '&lsaquo; Previous Photo',
                nextLinkText:              'Next Photo &rsaquo;',
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
    $('div.navigation').css({'width' : '300px', 'float' : 'left'});
    $('div.content').css('display', 'block');
});
