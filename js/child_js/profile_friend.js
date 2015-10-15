function loadAllFriend(){
	var key = $('#profile-sider-bar-left #avator-wrapper').attr('data-key');
	$.ajax({
		url: AJAX_DIR+'ld_all_friend.php',
		method:'post',
		data:{key:key},
		success:function(resp){
			if(resp != '1'){
				$(window).scrollTop(0);
				$('#all-friend-label').css('-webkit-animation',"rubberBand 0.4s").css('animation',"rubberBand 0.4s");
				$('#friend-content-wrapper .friend-content-inner-wrapper').removeClass('blk').addClass('hdn');
				$('#friend-content-wrapper').append(resp);
			}
		}
	});
}

function loadInterestFriendByKey(thisE, label_key){
	
	$.ajax({
		url: AJAX_DIR+'ld_friend.php',
		method:'post',
		data:{label_key:label_key},
		success:function(resp){
			console.log(resp);
			if(resp != '1'){
				$(window).scrollTop(0);
				thisE.css('-webkit-animation',"rubberBand 0.4s").css('animation',"rubberBand 0.4s");
				$('#friend-content-wrapper .friend-content-inner-wrapper').removeClass('blk').addClass('hdn');
				$('#friend-content-wrapper').append(resp);
			}
		}
	});
}



function loadFriend(thisE){
	var  label_key = thisE.attr('data-labelfor');
	var inner_wrapper = $('#friend-content-wrapper').find('.friend-content-inner-wrapper[data-key='+label_key+']'); //block for selected interest
	if(inner_wrapper.length > 0){
		//show
		$(window).scrollTop(0);
		thisE.css('-webkit-animation',"rubberBand 0.4s").css('animation',"rubberBand 0.4s");
		$('#friend-content-wrapper .friend-content-inner-wrapper').removeClass('blk').addClass('hdn');
		inner_wrapper.removeClass('hdn').addClass('blk');
	}else{
		 loadInterestFriendByKey(thisE, label_key);
	}
	thisE.parents('#friend-interest-navi').find('.interest-side-label .txt_ofl').removeClass('red-act');
	thisE.find('.txt_ofl').addClass('red-act');
	setTimeout(function(){
		thisE.css('-webkit-animation',"").css('animation',"");
	},200);
}

function loadAllFriendForRequestProfilePage(thisE){
	var  label_key = thisE.attr('data-labelfor');
	var inner_wrapper = $('#friend-content-wrapper').find('.friend-content-inner-wrapper[data-key='+label_key+']'); //block for selected interest
	if(inner_wrapper.length > 0){
		//show
		$(window).scrollTop(0);
		thisE.css('-webkit-animation',"rubberBand 0.4s").css('animation',"rubberBand 0.4s");
		$('#friend-content-wrapper .friend-content-inner-wrapper').removeClass('blk').addClass('hdn');
		inner_wrapper.removeClass('hdn').addClass('blk');
	}else{
		loadAllFriend();
	}
	thisE.parents('#friend-interest-navi').find('.interest-side-label .txt_ofl').removeClass('red-act');
	setTimeout(function(){
		thisE.css('-webkit-animation',"").css('animation',"");
	},200);

}




window.onpopstate=function(event){
	var request_friends_container = $('.friend-content-inner-wrapper[data-key='+event.state['key']+']');
	var activate_label = $('.interest-sider-navi[data-labelfor='+event.state['key']+']');
	if(request_friends_container.length > 0){
		$('.friend-content-inner-wrapper').addClass('hdn').removeClass('blk');
		request_friends_container.addClass('blk').removeClass('hdn');
	}else{
		var label_key = event.state['key'];
		if(label_key != -1){
			loadInterestFriendByKey(activate_label, label_key);
		}else{
			loadAllFriend();
		}
	}
	activate_label.css('-webkit-animation',"rubberBand 0.4s").css('animation',"rubberBand 0.4s");
	activate_label.parents('#friend-interest-navi').find('.interest-side-label .txt_ofl').removeClass('red-act');
	activate_label.find('.txt_ofl').addClass('red-act');
	setTimeout(function(){
		activate_label.css('-webkit-animation',"").css('animation',"");
	},200);	
}


$(window).load(function(){
	var url =  window.location.pathname;
 	var key = $('#friend-interest-navi .interest-sider-navi.active').attr('data-labelfor');
	history.replaceState({key: key}, null ,url);
});


$(document).ready(function(){
	$('#friend-interest-navi').on({
		click:function(){
			$('#profile-mid-content').attr('data-fetchable', 'true').removeAttr('data-set');
			var url = $(this).attr('data-href');
			var key = $(this).attr('data-labelfor');
			$('.interest-sider-navi').removeClass('active');
			$(this).addClass('active');
			history.pushState({key: key}, null ,url);
			loadFriend($(this));
		}
	},'.interest-side-label');

	$('#friend-interest-navi').on({
		click:function(){
			$('#profile-mid-content').attr('data-fetchable', 'true').removeAttr('data-set');
			var url = $(this).attr('data-href');
			var key = $(this).attr('data-labelfor');
			$('.interest-sider-navi').removeClass('active');
			$(this).addClass('active');
			history.pushState({key: key}, null ,url);
			loadAllFriendForRequestProfilePage($(this));
		}
	},'#all-friend-label');
	
		
	$(window).scroll(function() {
		var thisE = $(this);
		if ($('body').height() <= ($(window).height() + $(window).scrollTop() + 200) ) {
			var feed = $('#profile-mid-content');
			if(feed.attr('data-fetchable') == 'true' && feed.attr('data-set') != 'false'){
				feed.attr('data-fetchable', 'false');
				var u_key = $('#profile-sider-bar-left #avator-wrapper').attr('data-key');
				var key = $('#friend-interest-navi .interest-sider-navi.active').attr('data-labelfor');		
				var loading_wrapper = feed.find('.friend-content-inner-wrapper.blk[data-key='+key+']').find('.feed-loading-wrapper');
				loading_wrapper.removeClass('hdn');
				$.ajax({
					url:AJAX_DIR+'loadProfileFriendFeed.php',
					method:'post',
					data:{u_key:u_key, key:key},
					success:function(resp){
						console.log(resp);
						if(resp != '1'){
							loading_wrapper.addClass('hdn');
							loading_wrapper.before(resp);
							feed.attr('data-fetchable', 'true');
						}else{
							loading_wrapper.addClass('hdn');
							feed.attr('data-fetchable', 'false');
							feed.attr('data-set','false');
						}
					}
				});
			}
		}
	});

	
	
});