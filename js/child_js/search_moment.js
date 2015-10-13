$(document).ready(function(){
	$(window).scroll(function() {
		var thisE = $(this);
		if ($('body').height() <= ($(window).height() + $(window).scrollTop() + 200) ) {
			var feed = $('#search-content-wrapper');
			if(feed.attr('data-fetchable') == 'true' && feed.attr('data-set') != 'false'){
				feed.attr('data-fetchable', 'false');
				var total_feed = feed.find('.post-wrapper').length;
				var left_content = feed.find('.search-moment-content-left');
				var right_content = feed.find('.search-moment-content-right');
				var loading_wrapper = feed.find('.feed-loading-wrapper');
				loading_wrapper.removeClass('hdn');
				$.ajax({
					url:AJAX_DIR+'loadSearchMoreMomentFeed.php',
					method:'post',
					success:function(resp){
						console.log(resp);
						if(resp != '1'){
							var left = $($.parseHTML(resp)).filter('#loading-feed-left').html();								
							var right = $($.parseHTML(resp)).filter('#loading-feed-right').html();	
							left_content.append(left);
							right_content.append(right);
							feed.attr('data-fetchable', 'true');
							loading_wrapper.addClass('hdn');
						}else{
							feed.attr('data-fetchable', 'false');
							feed.attr('data-set','false');
							thisE.unbind('scroll');
							loading_wrapper.addClass('hdn');
						}
					}
				});
			}
		}
	});
});