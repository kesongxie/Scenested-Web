$(document).ready(function(){
	$(window).scroll(function() {
		var thisE = $(this);
		if ($('body').height() <= ($(window).height() + $(window).scrollTop() + 200) ) {
			var feed = $('#search-content-wrapper');
			if(feed.attr('data-fetchable') == 'true' && feed.attr('data-set') != 'false'){
				feed.attr('data-fetchable', 'false');
				var loading_wrapper = feed.find('.feed-loading-wrapper');
				loading_wrapper.removeClass('hdn');
				$.ajax({
					url:AJAX_DIR+'loadSearchMorePeopleFeed.php',
					method:'post',
					success:function(resp){
						if(resp != '1'){
							loading_wrapper.addClass('hdn');
							loading_wrapper.before(resp);
							feed.attr('data-fetchable', 'true');
						
						}else{
							loading_wrapper.addClass('hdn');
							feed.attr('data-fetchable', 'false');
							feed.attr('data-set','false');
							thisE.unbind('scroll');
						}
					}
				});
			}
		}
	});
});