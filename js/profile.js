$(document).ready(function(){
	$('.avator-inner-bottom').on({
		mouseover:function(){
			$(this).find('.avator-upload-inner-wrapper').fadeIn('fast');
		},
		mouseleave:function(){
			$(this).find('.avator-upload-inner-wrapper').fadeOut('fast');
		}
	},'#avator-upload-label');
	
	$('#upload-profile').on('change',function(){
		var imgTarget = $('#avator-upload-label .profile-image');
		var old_src = imgTarget.attr('src');
		var avator_loading_icon = $('#avator-upload-label .overlay');
		readURL(this,imgTarget);
		avator_loading_icon.show();
		var data=new FormData();
		data.append('profile-pic',$(this)[0].files[0]);
		$.ajax({
			url:AJAX_DIR+'uld_avtor.php',
			type:'POST',
			processData: false,
			contentType: false,
			data:data,
			success:function(resp){
				if(resp == '1'){
					imgTarget.attr('src',old_src);
				}
				avator_loading_icon.hide();
			}
		});
	});
	
	
	$('#upload-cover').on('change',function(){
		var imgTarget = $('#avator-wrapper .avator-inner-top .profile-image');
		var old_src = imgTarget.attr('src');
		var avator_loading_icon = $('.avator-inner-top .overlay');
		readURL(this,imgTarget);
		avator_loading_icon.removeClass("hdn");
		var data=new FormData();
		data.append('profile-pic',$(this)[0].files[0]);
		$.ajax({
			url:AJAX_DIR+'uld_cover.php',
			type:'POST',
			processData: false,
			contentType: false,
			data:data,
			success:function(resp){
				if(resp == '1'){
					imgTarget.attr('src',old_src);
				}
				avator_loading_icon.hide();
			}
		});
	});
	
	
	$('#new-interest-picture').on('change',function(){
		var parentLabel = $(this).parents('label');
		var imgTarget = parentLabel.find('.target-image');
		var old_src = imgTarget.attr('src');
		var avator_loading_icon = parentLabel.find('.loading-icon-wrapper');
		parentLabel.find('.camera-center').hide();
		parentLabel.find('.picture-upload-black-overlay').hide();
		readURL(this,imgTarget);
		imgTarget.removeClass("hdn");
	});
	
	
	
	$('#profile-sider-bar-left').on({
		mouseover:function(){
			$(this).find('label').animate({
			'bottom':'0px'
			},100);
		},
		mouseleave:function(){
			$(this).find('label').animate({
			'bottom':'-30px'
			},100);
		}
	
	},'.avator-inner-top');
	
	
	$('body').on({
		mouseover:function(){
			$(this).find(".camera-center").fadeIn('fast');
			$(this).find(".picture-upload-black-overlay").fadeIn('fast');
			
		},
		mouseleave:function(){
			if($(this).find(".target-image").attr('src') != ''){
				$(this).find(".camera-center").fadeOut('fast');
			}
			$(this).find(".picture-upload-black-overlay").fadeOut('fast');

		}
	
	},'.picture-upload-wrapper');
	
	
	
	$('body').on({
	mouseover:function(){
		$(this).css({'color':'rgb(88, 86, 86)','border-bottom':'3px solid rgb(10, 43, 138)', 'border-radius': '0px'});
	},
	mouseleave:function(){
		$(this).css({'color':'#777777','border-bottom':'0px'});
		
	}
	},'.deactive-navi');
	
	$('#add-new-interest').on({
		click:function(){
			$('#add-new-interest').css('-webkit-animation',"zoomOut 0.5s").css('animation',"zoomOut 0.5s");
		}
	
	},'#add-interest');
	
	$('body').on({
		click:function(){
			var thisO = $(this);
			var add_interest_w = $('#add-new-interest-wrapper');
			var add_new_interest = add_interest_w.find('#add-new-interest');
			if(add_new_interest.hasClass('hdn') == false){
				add_interest_w.css('-webkit-animation',"shake 0.5s").css('animation',"shake 0.5s");
			}else{
				add_new_interest.removeClass('hdn').css('-webkit-animation',"zoomIn 0.3s").css('animation',"zoomIn 0.3s");
			}
			setTimeout(function(){
				add_interest_w.css('-webkit-animation',"").css('animation',"");
			},1000);
		}
	},'.interest-sider-navi');
	
	
	
	
	
});