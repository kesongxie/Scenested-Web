$(document).ready(function(){
		$(window).scroll(function() {
			var thisE = $(this);
			if ($('body').height() <= ($(window).height() + $(window).scrollTop() + 200) ) {
				var key = $('#profile-sider-bar-left #avator-wrapper').attr('data-key');
				var feed = $('#profile-mid-content');
				var left_content = feed.find('.photo-content-left');
				var right_content = feed.find('.photo-content-right');
				var l_c = left_content.find('.previewable[data-sourcefrom=c]').last().attr('data-key');
				var r_c = right_content.find('.previewable[data-sourcefrom=c]').last().attr('data-key');
				var l_e = left_content.find('.previewable[data-sourcefrom=e]').last().attr('data-key');;
				var r_e = right_content.find('.previewable[data-sourcefrom=e]').last().attr('data-key');
				var l_p = left_content.find('.previewable[data-sourcefrom=p]').last().attr('data-key');
				var r_p = right_content.find('.previewable[data-sourcefrom=p]').last().attr('data-key');
				var l_m = left_content.find('.previewable[data-sourcefrom=m]').last().attr('data-key');
				var r_m = right_content.find('.previewable[data-sourcefrom=m]').last().attr('data-key');
				if(feed.attr('data-fetchable') == 'true' && feed.attr('data-set') != 'false'){
					feed.attr('data-fetchable', 'false');
					var loading_wrapper = feed.find('.feed-loading-wrapper');
					loading_wrapper.removeClass('hdn');
					var total_feed = feed.find('.previewable').length;
					
					$.ajax({
						url:AJAX_DIR+'loadProfilePhotoFeed.php',
						method:'post',
						data: {l_c:l_c, r_c:r_c, l_e:l_e, r_e:r_e, l_p:l_p,r_p:r_p, l_m:l_m, r_m:r_m, key:key },
						success:function(resp){
							if(resp != '1'){
								var left = $($.parseHTML(resp)).filter('#loading-feed-left').html();								
								var right = $($.parseHTML(resp)).filter('#loading-feed-right').html();	
								left_content.append(left);
								right_content.append(right);
								feed.attr('data-fetchable', 'true');
								loading_wrapper.addClass('hdn');
							}else{
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