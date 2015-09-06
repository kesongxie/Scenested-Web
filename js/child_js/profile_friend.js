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
		//load
		 $.ajax({
			url: AJAX_DIR+'ld_friend.php',
			method:'post',
			data:{label_key:label_key},
			success:function(resp){
				if(resp != '1'){
					$(window).scrollTop(0);
					thisE.css('-webkit-animation',"rubberBand 0.4s").css('animation',"rubberBand 0.4s");
					$('#friend-content-wrapper .friend-content-inner-wrapper').removeClass('blk').addClass('hdn');
					$('#friend-content-wrapper').append(resp);
				}
			}
		});
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
		var key = $('#profile-sider-bar-left #avator-wrapper').attr('data-key');
		$.ajax({
			url: AJAX_DIR+'ld_all_friend.php',
			method:'post',
			data:{key:key},
			success:function(resp){
				if(resp != '1'){
					$(window).scrollTop(0);
					thisE.css('-webkit-animation',"rubberBand 0.4s").css('animation',"rubberBand 0.4s");
					$('#friend-content-wrapper .friend-content-inner-wrapper').removeClass('blk').addClass('hdn');
					$('#friend-content-wrapper').append(resp);
				}
			}
		});
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
		$.ajax({
			url: AJAX_DIR+'ld_friend_by_url.php',
			method:'post',
			data:{url:event.state['friend_request_url']},
			success:function(resp){
				if(resp != '1'){
					$('#friend-content-wrapper').append(resp);
				}
			}
		});
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
	history.pushState({friend_request_url:url, key: key}, null ,url);
});


$(document).ready(function(){
	$('#friend-interest-navi').on({
		click:function(){
			var url = $(this).attr('data-href');
			var key = $(this).attr('data-labelfor');
			history.pushState({friend_request_url:url, key: key}, null ,url);
			loadFriend($(this));
		}
	
	},'.interest-side-label');

	$('#friend-interest-navi').on({
		click:function(){
			var url = $(this).attr('data-href');
			var key = $(this).attr('data-labelfor');
			history.pushState({friend_request_url:url, key: key}, null ,url);
			loadAllFriendForRequestProfilePage($(this));
		}
	},'#all-friend-label');
	

});