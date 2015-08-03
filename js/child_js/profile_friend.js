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


$(document).ready(function(){
	$('#friend-interest-navi').on({
		click:function(){
			loadFriend($(this));
		}
	
	},'.interest-sider-navi');


	

});