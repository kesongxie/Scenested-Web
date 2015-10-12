$(window).scroll(function() {
	var thisE = $(this);
	if ($('body').height() <= ($(window).height() + $(window).scrollTop() + 200) ) {
	
		console.log('ff');
		return false;
		var feed = $('#search-content-wrapper');
		if(feed.attr('data-fetchable') == 'true' && feed.attr('data-set') != 'false'){
			feed.attr('data-fetchable', 'false');
			var loading_wrapper = feed.find('.feed-loading-wrapper');
			loading_wrapper.removeClass('hdn');
			$.ajax({
				url:AJAX_DIR+'loadSearchMorePeopleFeed.php',
				method:'post',
				success:function(resp){
					console.log(resp);
					if(resp != '1'){
						feed.append(resp);
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