$(window).scroll(function() {
			var thisE = $(this);
			if ($('body').height() <= ($(window).height() + $(window).scrollTop() + 200) ) {
				var key = $('#profile-sider-bar-left #avator-wrapper').attr('data-key');
				var feed = $('#profile-mid-content');
				if(feed.attr('data-fetchable') == 'true' && feed.attr('data-set') != 'false'){
					feed.attr('data-fetchable', 'false');
					var loading_wrapper = feed.find('.feed-loading-wrapper');
					loading_wrapper.removeClass('hdn');
					var total_feed = feed.find('.post-wrapper').length;
					var left_content = feed.find('.event-content-left');
					var right_content = feed.find('.event-content-right');
					
					if(total_feed % 2 != 0){
						//even, the last post is at the right hand side
						var last_key =left_content.find('.post-wrapper').last().attr('data-key');
					}else{
						//odd, the last post is at the left hand side
						var last_key = right_content.find('.post-wrapper').last().attr('data-key');
					}
					
					$.ajax({
						url:AJAX_DIR+'loadProfileEventFeed.php',
						method:'post',
						data: {last_key:last_key, key:key},
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
								feed.attr('data-set','false');
								thisE.unbind('scroll');
								loading_wrapper.addClass('hdn');
							}
						}
					});
				}
			}
    });