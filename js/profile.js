function doneWithInterestEditing(editingDiv){
	var parentDiv = editingDiv.parents('.interest-profile');
	editingDiv.css('-webkit-animation',"flipOutY 0.4s").css('animation',"flipOutY 0.4s").addClass('hdn');
	parentDiv.find('.interest-profile-intro').css('-webkit-animation',"flipInY 0.4s").css('animation',"flipInY 0.4s").removeClass('hdn');
	return parentDiv;
}

function renderVisibleScope(desc,src){
	desc = desc.trim();
	var description = "";
	if(desc.substr(1,1) != ' '){
		description+='<span  class="first-cap" style="position:relative;top:0px;margin-right:-3px;">'+desc.substr(0,1)+'</span>';
	}else{
		description+='<span class="first-cap" >'+desc.substr(0,1)+'</span>';
	}
	description+='<span>'+desc.substr(1)+'</span>';
	return '<div class="visible-content limit-height"><div class="interest-image-label"><img src='+src+'></div>'+description+'</div>';
}



$(document).ready(function(){

	var in_navi_count = 0;
	$('.interest-sider-navi').each(function(){
		in_navi_count++;
		$(this).css('-webkit-animation',' bounceInUp '+(in_navi_count*0.5)+'s');
	});


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
					presentPopupDialog("Bad Image", BAD_IMAGE_MESSAGE, "Got it", "", null, null );
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
					presentPopupDialog("Bad Image", BAD_IMAGE_MESSAGE, "Got it", "", null, null );
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
		var loading_icon = parentLabel.find('.loading-icon-wrapper');
		var camera = parentLabel.find('.camera-center');
		parentLabel.find('.picture-upload-black-overlay').hide();
		var thisE = this;
		var data=new FormData();
		data.append('profile-pic',$(this)[0].files[0]);
		readURL(thisE,imgTarget);

		$.ajax({
			url:AJAX_DIR+'validate_image_label.php',
			type:'POST',
			processData: false,
			contentType: false,
			data:data,
			success:function(resp){
				if(resp == '1'){
					presentPopupDialog("Bad Image",BAD_IMAGE_MESSAGE, "Got it", "", null, null );
					imgTarget.attr('src',old_src);
					if(old_src == ''){
						imgTarget.addClass('hdn');	
						camera.show();
						return false;
					}
				}
				imgTarget.removeClass("hdn");
				loading_icon.show();
				camera.hide();
				loading_icon.hide();
			}
		});
		
	});
	
	
	$('body').on({
		change:function(){
				var imgTarget = $(this).parents('.interest-profile-edit').find('.target-image');
				var data=new FormData();
				data.append('profile-pic',$(this)[0].files[0]);
				var thisE = this;
				$.ajax({
					url:AJAX_DIR+'validate_image_label.php',
					type:'POST',
					processData: false,
					contentType: false,
					data:data,
					success:function(resp){
						if(resp == '1'){
							presentPopupDialog("Bad Image",BAD_IMAGE_MESSAGE, "Got it", "", null, null );
							return false;
						}
						$(thisE).attr('data-set','true');
						readURL(thisE,imgTarget);
						imgTarget.removeClass("hdn");
					}
				});
		}
	},'.interest-picture');
	
	
	
	
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
			var parentDiv = $('#add-new-interest');
			var image_label =parentDiv.find('.target-image');
			
			//elements
			var image_file = parentDiv.find('input[type=file]');
			var name_input =parentDiv.find('input[type=text]');
			var description_txtarea = parentDiv.find('textarea');
			var select = parentDiv.find('select'); //experience select
			
			//values
			var name = name_input.val().trim();
			if(name == ''){
				presentPopupDialog("Need a Name", "Please add a name for your interest", "Got it", "", null, null );
				return false;
			}
			var description = description_txtarea.val();
			var experience = $('option:selected', select).attr('data-option');
			
			var data=new FormData();
			data.append('image-label',$(image_file)[0].files[0]);
			data.append('name', name);
			data.append('description',description);
			data.append('experience',experience);
			
			$.ajax({
				url:AJAX_DIR+'add_interest.php',
				type:'POST',
				processData: false,
				contentType: false,
				data:data,
				success:function(resp){
					if(resp == '1'){
						presentPopupDialog("Bad Image",BAD_IMAGE_MESSAGE, "Got it", "", null, null );
					}else if(resp == '2'){
						presentPopupDialog("Need a Name", "Please add a name for your interest", "Got it", "", null, null );
					}else if(resp == '3'){
						presentPopupDialog("Interest Existed",'The interest <span class="plain-lk pointer" style="color:#062AA3">'+ name + '</span> has already existed', "Got it", "", null, null );
					}else{	
						$('#add-new-interest-wrapper').css('-webkit-animation',"zoomOut 0.5s").css('animation',"zoomOut 0.5s").addClass('hdn');
						$('.interest-content-inner-wrapper').addClass('hdn');
						
						
						var mid_content = $($.parseHTML(resp)).filter('#node-mid-content');								
						var side_content = $($.parseHTML(resp)).filter('#node-side-content');			
						$('#interest-content-wrapper').append(mid_content.html()).removeClass('hdn');
						side_content.children('.interest-side-label').css('-webkit-animation',' bounceInUp 1s');
						$('#interest-navi').append(side_content.html());

						setVisibleContent();
						//reset elements
						parentDiv.find('input, textarea, select').val('');
						parentDiv.find('.camera-center').show();
						image_label.attr('src','').addClass('hdn');
						
					}
				}
			});
		}
	
	},'#add-interest');
	
	$('body').on({
		click:function(){
			var activelabelFor = $('#interest-content-wrapper .blk').attr('data-key');
			var thisO = $(this);
			var activeSideBarLabel = $('#interest-navi').find('.interest-sider-navi[data-labelfor='+activelabelFor+']');
			var add_interest_w = $('#add-new-interest-wrapper');
			if(add_interest_w.hasClass('hdn') == false){
				add_interest_w.css('-webkit-animation',"shake 0.5s").css('animation',"shake 0.5s");
			}else{
				activeSideBarLabel.css('-webkit-animation',"rubberBand 0.4s").css('animation',"rubberBand 0.4s");
				$('#interest-content-wrapper').addClass('hdn');
				add_interest_w.removeClass('hdn').css('-webkit-animation',"zoomIn 0.3s").css('animation',"zoomIn 0.3s");
			}
			setTimeout(function(){
				add_interest_w.css('-webkit-animation',"").css('animation',"");
				activeSideBarLabel.css('-webkit-animation',"").css('animation',"");
			},200);
			
			add_interest_w.find('.in-txt-n').focus();
		}
	},'#add-new-interest-navi');
	
	$('#add-new-interest').on({
		click:function(){
			var activelabelFor = $('#interest-content-wrapper .blk').attr('data-key');
			var activeSideBarLabel = $('#interest-navi').find('.interest-sider-navi[data-labelfor='+activelabelFor+']');
			$('#add-new-interest-wrapper').addClass('hdn');
			activeSideBarLabel.css('-webkit-animation',"rubberBand 0.4s").css('animation',"rubberBand 0.4s");
			$('#interest-content-wrapper').removeClass('hdn');
			setTimeout(function(){
				activeSideBarLabel.css('-webkit-animation',"").css('animation',"");
			},200);
		}
		
	},'.cancel-button');
	
	$('#interest-navi').on({
		click:function(){
			var  label_key = $(this).attr('data-labelfor');
			var inner_wrapper = $('#interest-content-wrapper').find('.interest-content-inner-wrapper[data-key='+label_key+']'); //block for selected interest
			var thisE = $(this);
			$('#add-new-interest-wrapper').addClass('hdn'); //hide the interest edit div
			$('#interest-content-wrapper').removeClass('hdn'); //show the interest content div
			if(inner_wrapper.length > 0){
				//show
				thisE.css('-webkit-animation',"rubberBand 0.4s").css('animation',"rubberBand 0.4s");
				$('#interest-content-wrapper .interest-content-inner-wrapper').removeClass('blk').addClass('hdn');
				inner_wrapper.removeClass('hdn').addClass('blk');
			}else{
				//load
				$.ajax({
					url: AJAX_DIR+'ld_interest.php',
					method:'post',
					data:{label_key:label_key},
					success:function(resp){
						if(resp != '1'){
							thisE.css('-webkit-animation',"rubberBand 0.4s").css('animation',"rubberBand 0.4s");
							$('#interest-content-wrapper .interest-content-inner-wrapper').removeClass('blk').addClass('hdn');
							$('#interest-content-wrapper').append(resp);
							setVisibleContent();
						}
					}
				});
			}
			setTimeout(function(){
				thisE.css('-webkit-animation',"").css('animation',"");
			},200);
		}
	},'.interest-side-label');
	
	
	$('body').on({
		click:function(){
			$(this).parents('.interest-profile').find('.in_con_opt_w').toggle();
			return false;
		}
	
	},'.interest-profile .option');
	
	
	$('body').on({
		click:function(){
			var parentDiv = $(this).parents('.interest-profile');
			parentDiv.find('.interest-profile-intro').css('-webkit-animation',"flipOutY 0.4s").css('animation',"flipOutY 0.4s").addClass('hdn');
			var edit_wrapper = parentDiv.find('.interest-profile-edit');
			edit_wrapper.css('-webkit-animation',"flipInY 0.4s").css('animation',"flipInY 0.4s").removeClass('hdn');
			edit_wrapper.find('.in-txt-n').focus();
			setTimeout(function(){
				edit_wrapper.css('-webkit-animation',"").css('animation',"");
			
			},200);

		}
	
	},'.interest-profile .edit_interest');
	
	
	$('body').on({
		click:function(){
			var parentDiv = $(this).parents('.interest-profile-edit');
			var image_label =parentDiv.find('.target-image');
			var updated_image_src = image_label.attr('src');
			
			//elements
			var image_file = parentDiv.find('input[type=file]');
			var name_input =parentDiv.find('input[type=text]');
			var description_txtarea = parentDiv.find('textarea');
			var select = parentDiv.find('select'); //experience select
			
			//values
			var name = name_input.val().trim();
			if(name == ''){
				presentPopupDialog("Need a Name", "Please add a name for your interest", "Got it", "", null, null );
				return false;
			}
			var imageSet = image_file.attr('data-set');
			var key = image_file.attr('data-key');
			var description = description_txtarea.val();
			var exp = parentDiv.find('select').val();
			var experience = $('option:selected', select).attr('data-option');
			var data=new FormData();
			data.append('image-label',$(image_file)[0].files[0]);
			data.append('key', key);
			data.append('name', name);
			data.append('imageSet', imageSet);
			data.append('description',description);
			data.append('experience',experience);
			
			$.ajax({
				url:AJAX_DIR+'update_interest.php',
				type:'POST',
				processData: false,
				contentType: false,
				data:data,
				success:function(resp){
					if(resp == '1'){
						presentPopupDialog("Bad Image",BAD_IMAGE_MESSAGE, "Got it", "", null, null );
					}else if(resp == '2'){
						presentPopupDialog("Need a Name", "Please add a name for your interest", "Got it", "", null, null );
					}else if(resp == '3'){
						presentPopupDialog("Interest Existed",'The interest <span class="plain-lk pointer" style="color:#062AA3">'+ name + '</span> has already existed', "Got it", "", null, null );
					}else{	
						var sideBarParent = $('#interest-navi').find('.interest-side-label[data-labelfor='+key+']');
						//modify the side bar
						sideBarParent.find('img').attr('src',updated_image_src);
						sideBarParent.find('.txt_ofl').text(name).attr('title',name);
						sideBarParent.css('-webkit-animation',"rubberBand 0.4s").css('animation',"rubberBand 0.4s");
						var intro = doneWithInterestEditing(parentDiv).find('.interest-profile-intro');
						setTimeout(function(){
							sideBarParent.css('-webkit-animation',"").css('animation',"");
						},200);
						//modify the intro
						intro.find('.main-title').text(name);
						intro.find('.exp').text(exp);
						intro.find('.visible-content').html(renderVisibleScope(description,updated_image_src ));
						setVisibleContent();
					}
				}
			});
		
		}
	
	},'.interest-profile .interest-profile-edit .update_interest');
	
	$('body').on({
		click:function(){
			var parentDiv = $(this).parents('.interest-profile-edit');
			doneWithInterestEditing(parentDiv);
		}
	},'.interest-profile .interest-profile-edit .cancel-button');
	
	$('body').on({
		mouseover:function(){
			var control = $(this).find('.visible-control');
			control.css('color','#2459B6');
			
		},
		mouseleave:function(){
			var control = $(this).find('.visible-control');
			control.css('color','#949494');
			
		}
	},'.interest-profile');
	
	
});