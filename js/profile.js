global_sch_hover = -1;

function doneWithInterestEditing(editingDiv){
	var parentDiv = editingDiv.parents('.interest-profile');
	editingDiv.css('-webkit-animation',"flipOutY 0.4s").css('animation',"flipOutY 0.4s").addClass('hdn');
	var introDiv = parentDiv.find('.interest-profile-intro');
	introDiv.css('-webkit-animation',"flipInY 0.4s").css('animation',"flipInY 0.4s").removeClass('hdn');
	
	setTimeout(function(){
		editingDiv.css('-webkit-animation',"").css('animation',"");
		introDiv.css('-webkit-animation',"").css('animation',"");
		
	},200);
	
	return parentDiv;
}

function renderVisibleScope(desc,src){
	desc = desc.trim();
	var description = "";
	if(desc != ''){
		if(desc.substr(1,1) != ' '){
			description+='<span  class="first-cap" style="position:relative;top:4px;margin-right:-3px;">'+desc.substr(0,1).toUpperCase()+'</span>';
		}else{
			description+='<span class="first-cap" >'+desc.substr(0,1).toUpperCase()+'</span>';
		}
		description+='<span> '+desc.substr(1)+'</span>';
	}else{
		description = '<div style="margin-top:3px;">There is no description available for this interest yet</div>';
	}
	
	if(src != ''){
		return '<div class="interest-image-label"><img src='+src+'></div>'+description;
	}
	return  description;
}

	
function startSlideComment(evt_wrapper){
	return setInterval(function(){
	var parentDiv = evt_wrapper.find('.slideshow-comment-wrapper');
	var activeElement = parentDiv.find('.cmt.act');
	var nextElementToActive = activeElement.next('.cmt.hdn');
	if(nextElementToActive.length == 0){
		nextElementToActive = parentDiv.find('.cmt').first();
	}
	activeElement.removeClass('act').css('-webkit-animation',"fadeOut 0.5s").css('animation',"fadeOut 0.2s").addClass('hdn');
	nextElementToActive.addClass('act').css('-webkit-animation',"fadeIn 0.2s").css('animation',"fadeIn 0.2s");
	setTimeout(function(){
		nextElementToActive.removeClass('hdn');
	},200);
	},8000);
}



function addFavorEvt(title, desc){
	$.ajax({
		url:AJAX_DIR+'add_favor_event.php',
		method:'post',
		data:{title:title, desc:desc},
		success:function(resp){
			var favor_blk = $('#favor-evt-block');
			var add_evt =favor_blk.find('#add-favor-evt');
			add_evt.addClass('hdn');
			add_evt.find('textarea,input').val('');
			favor_blk.find('#hide-favor-evt-edit').addClass('hdn').removeClass('act');
			favor_blk.find('#show-favor-evt-edit').removeClass('hdn').addClass('act');
			favor_blk.find('#favor-evt-label-wrapper').prepend(resp).removeClass('hdn');
		}	
	});
}

function removeRecentFavor(sender){
	var parentWrappr = sender.parents('#favor-evt-block');
	var parentDiv = sender.parents('.favor-evt-wrapper');
	var key = parentDiv.attr('data-key');
	$.ajax({
		url:AJAX_DIR+'remove_favor.php',
		method:'post',
		data:{key:key},
		success:function(resp){
			console.log(resp);
			parentDiv.remove();
			if(parentWrappr.find('.favor-evt-wrapper').length < 1){
				parentWrappr.find('.empty-mesg').removeClass('hdn');
			}
		}	
	
	});
}

function removeSchool(sender){
	var parentDiv = sender.parents('.school-name-wrapper');
	var side_block = parentDiv.parents('.side-block-wrapper');
	$.ajax({
		url:AJAX_DIR+'remove_school.php',
		method:'post',
		success:function(resp){
			parentDiv.find('.name').text('').attr('href',DEFAULT_SEARCH_PATH);
			parentDiv.find('.remove').remove();
			parentDiv.parents('.side-block-wrapper').find('.school-name-input-wrapper input').val('');
			if(side_block.find('.major-wrapper').text().trim() == ''){
				side_block.find('.empty').removeClass('hdn').addClass('act');
			}
		}	
	
	});
}

function removeStudy(sender){
	var parentDiv = sender.parents('.major-wrapper');
	var side_block = parentDiv.parents('.side-block-wrapper');
	$.ajax({
		url:AJAX_DIR+'remove_study.php',
		method:'post',
		success:function(resp){
			parentDiv.find('.name').text('').attr('href',DEFAULT_SEARCH_PATH);
			parentDiv.find('.remove').remove();
			parentDiv.parents('.side-block-wrapper').find('.major-name-input-wrapper input').val('');
			if(side_block.find('.school-name-wrapper').text().trim() == ''){
				side_block.find('.empty').removeClass('hdn').addClass('act');;
			}
		}	
	
	});
}





$(document).ready(function(){

	var in_navi_count = 0;
	if($(document).scrollTop() == 0){
		$('.interest-side-label').each(function(){
			in_navi_count++;
			$(this).css('-webkit-animation',' bounceInUp '+(in_navi_count*0.5)+'s');
		});
	}	
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
				console.log(resp);
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
				}else{
					imgTarget.removeClass('hdn');
				}
				
				avator_loading_icon.hide();
			}
		});
	});
	
	
	$('#new-interest-picture').on('change',function(){
		var parentLabel = $(this).parents('label');
		var imgTarget = parentLabel.find('.target-image');
		var old_src = imgTarget.attr('src');
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
				camera.hide();
			}
		});
		
	});
	
	
	$('body').on({
		click:function(){
			var parentDiv = $(this).parents('.interest-content-inner-wrapper');
			parentDiv.find('.interest-profile-moment').addClass('hdn').css('-webkit-animation',"").css('animation','');
			parentDiv.find('.interest-profile-event').removeClass('hdn');
		
		}
	
	},'.bar-evt');
	
	$('body').on({
		click:function(){
			var parentDiv = $(this).parents('.interest-content-inner-wrapper');
			parentDiv.find('.interest-profile-event').addClass('hdn').css('-webkit-animation',"").css('animation','');
			parentDiv.find('.interest-profile-moment').removeClass('hdn');
		}
	},'.bar-moment');
	
	
	
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
	
	
	
	
	
	
	
	$('body').on({
		change:function(){
			var postDiv = $(this).parents('.interest-profile-post');
			var imgTarget =postDiv.find('.target-image');
			var previewContainer = postDiv.find('.preview-container');
			var targetConatiner = previewContainer.find('.target-container');
			var data=new FormData();
			data.append('profile-pic',$(this)[0].files[0]);
			var thisE = this;
			var preview_loading_wrapper = previewContainer.find('.preview-loading-wrapper');
			previewContainer.removeClass('hdn');
			preview_loading_wrapper.removeClass('hdn');

			$.ajax({
				url:AJAX_DIR+'validate_image_label.php',
				type:'POST',
				processData: false,
				contentType: false,
				data:data,
				success:function(resp){
					if(resp == '1'){
						presentPopupDialog("Bad Image",BAD_IMAGE_MESSAGE, "Got it", "", null, null );
						previewContainer.addClass('hdn');
						preview_loading_wrapper.addClass('hdn');
						return false;
					}
					readURL(thisE,imgTarget);
					setTimeout(function(){
 						targetConatiner.removeClass('hdn');
 						preview_loading_wrapper.addClass('hdn');
 					},500);
				}
			});
		}
		
	},'.interest-profile-post .edit-dialog-footer .attched-photo');
	
	
	$('body').on({
		click:function(){
			var postDiv = $(this).parents('.interest-profile-post');
			var previewContainer = $(this).parents('.preview-container');
			var imgTarget = previewContainer.find('.target-image');
			postDiv.find('.edit-dialog-footer .attched-photo').val('');
			previewContainer.addClass('hdn');
			imgTarget.attr('src','');
		}
		
	},'.interest-profile-post .preview-container .cross');
	
	
	
	
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
		$(this).css({'color':'rgb(135, 135, 135)','border-bottom':'0px'});
		
	}
	},'.deactive-navi');
	
	
	
	$('body').on({
		click:function(){
			$(window).scrollTop(0);
			var activelabelFor = $('#interest-content-wrapper .blk').attr('data-key');
			var thisO = $(this);
			var activeSideBarLabel = $('#i-interest-navi').find('.interest-sider-navi[data-labelfor='+activelabelFor+']');
			var add_interest_w = $('#add-new-interest-wrapper');
			if(add_interest_w.hasClass('hdn') == false){
				add_interest_w.css('-webkit-animation',"shake 0.5s").css('animation',"shake 0.5s");
			}else{
				activeSideBarLabel.css('-webkit-animation',"rubberBand 0.4s").css('animation',"rubberBand 0.4s");
				activeSideBarLabel.find('.txt_ofl').removeClass('red-act');
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
			var activeSideBarLabel = $('#i-interest-navi').find('.interest-sider-navi[data-labelfor='+activelabelFor+']');
			$('#add-new-interest-wrapper').addClass('hdn');
			activeSideBarLabel.css('-webkit-animation',"rubberBand 0.4s").css('animation',"rubberBand 0.4s");
			activeSideBarLabel.find('.txt_ofl').addClass('red-act');
			$('#interest-content-wrapper').removeClass('hdn');
			setTimeout(function(){
				activeSideBarLabel.css('-webkit-animation',"").css('animation',"");
			},200);
		}
		
	},'.cancel-button');
	
	
	
	
	$('body').on({
		click:function(){
			$(this).parents('.interest-profile').find('.in_con_opt_w').toggleClass('hdn');
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
			var parentDiv = $(this).parents('.interest-profile');
			parentDiv.find('.interest-profile-intro').css('-webkit-animation',"flipOutY 0.4s").css('animation',"flipOutY 0.4s").addClass('hdn');
			parentDiv.find('.interest-profile-moment').css('-webkit-animation',"flipInY 0.4s").css('animation',"flipInY 0.4s").removeClass('hdn');

		}
	
	},'.interest-profile-intro .dialog-header .post');
	
	
	
	
	
	
	$('body').on({
		click:function(){
			var parentDiv = $(this).parents('.sub');
			doneWithInterestEditing(parentDiv);
		}
	},'.interest-profile .sub .cancel-button');
	
	$('body').on({
		mouseover:function(){
			var control = $(this).find('.visible-control');
			control.css('color','#2459B6');
			
		},
		mouseleave:function(){
			var control = $(this).find('.visible-control');
			control.css('color','#949494');
			
		}
	},'.post');
	
	
	$('body').on({
		click:function(){
			var inner_wrapper = $(this).parents('.interest-content-inner-wrapper');
			
			var parentDiv = $(this).parents('.interest-profile-post');
			var image_label =parentDiv.find('.target-image');
			var updated_image_src = image_label.attr('src');
			
			//elements
			var image_file = parentDiv.find('input[type=file]');
			var description_txtarea = parentDiv.find('.desc');
			var date_input = parentDiv.find('.add-date');
			var caption_input = parentDiv.find('.caption-input');
			
			//values
			key = inner_wrapper.attr('data-key').trim();
			if(key == ''){
				return false;
			}
			
			var desc = description_txtarea.val().trim();
			if(desc == ''){
				presentPopupDialog("Need Some Descriptions", "Please add some descriptions about your moment", "Got it", "", null, null );
				return false;
			}
			
			var date =  date_input.val().trim();
			
			if( date == ''){
				presentPopupDialog("Need A Date", "Please add a date for your moment", "Got it", "", null, null );
				return false;
			}
			
			var caption = caption_input.val().trim();
			var data=new FormData();
			data.append('attached-picture',$(image_file)[0].files[0]);
			data.append('caption',caption);
			data.append('description',desc);
			data.append('date', date);
			data.append('key',key);
			
			var loadingWrapper = parentDiv.find('.loading-icon-wrapper');
			loadingWrapper.show();
			var actionButton = parentDiv.find('.edit-dialog-footer .action-button');
			actionButton.text("Loading...");
			$.ajax({
				url:AJAX_DIR+'post_moment.php',
				type:'POST',
				processData: false,
				contentType: false,
				data:data,
				success:function(resp){
					if(resp == '1'){
						presentPopupDialog("Bad Image",BAD_IMAGE_MESSAGE, "Got it", "", null, null );
						return false;
					}else if(resp == '2'){
						return false;
					}else if(resp == '3'){
						presentPopupDialog("Need some descriptions",'Please add some descriptions about this moment', "Got it", "", null, null );
					}else if(resp == '4'){
						presentPopupDialog("Need a Date",'Please add a date for your moment', "Got it", "", null, null );

					}else{	
						actionButton.text('Post');
						parentDiv.find('.preview-container').addClass('hdn');
						image_label.attr('src','');
						loadingWrapper.hide();
						image_file.val('');
						description_txtarea.val('');
						date_input.val('');
						caption_input.val('');
						doneWithInterestEditing(parentDiv);
						inner_wrapper.find('.interest-content-right').prepend(resp);
						setVisibleContent();
					}
				}
			});
		}
	
	},'.post-moment');
	
	
	
	
	
	$('body').on({
		click:function(){
			var inner_wrapper = $(this).parents('.interest-content-inner-wrapper');
			
			var parentDiv = $(this).parents('.interest-profile-post');
			var image_label =parentDiv.find('.target-image');
			var updated_image_src = image_label.attr('src');
			
			//elements
			var image_file = parentDiv.find('input[type=file]');
			var title_input = parentDiv.find('.evt-title');
			var description_txtarea = parentDiv.find('.evt-desc');
			var location_input =  parentDiv.find('.evt-loca');
			var date_input = parentDiv.find('.evt-date');
			var time_input = parentDiv.find('.evt-time');
			var caption_input = parentDiv.find('.caption-input');
			
			//key
			key = inner_wrapper.attr('data-key').trim();
			if(key == ''){
				return false;
			}
			
			var title = title_input.val().trim();
			if(title == ''){
				presentPopupDialog("Need a Title", "Please add a title for your event", "Got it", "", null, null );
				return false;
			}
			
			var desc = description_txtarea.val().trim();
			if(desc == ''){
				presentPopupDialog("Need Some Descriptions", "Please add some descriptions about your event", "Got it", "", null, null );
				return false;
			}
			
			
			
			var location = location_input.val().trim();
			var date =  date_input.val().trim();
			var time = time_input.val().trim();
			var caption = caption_input.val().trim();
			var data=new FormData();
			data.append('attached-picture',$(image_file)[0].files[0]);
			data.append('caption',caption);
			data.append('title',title);
			data.append('location',location);
			data.append('description',desc);
			data.append('date', date);
			data.append('time', time);
			data.append('key',key);
			var loadingWrapper = parentDiv.find('.loading-icon-wrapper');
			loadingWrapper.show();
			var actionButton = parentDiv.find('.edit-dialog-footer .action-button');
			actionButton.text("Loading...");
			
			$.ajax({
				url:AJAX_DIR+'post_event.php',
				type:'POST',
				processData: false,
				contentType: false,
				data:data,
				success:function(resp){
					// console.log(resp);
					actionButton.text('Post');
					loadingWrapper.hide();
					if(resp == '1'){
						presentPopupDialog("Bad Image",BAD_IMAGE_MESSAGE, "Got it", "", null, null );
						return false;
					}else if(resp == '2'){
						return false;
					}else if(resp == '3'){
						presentPopupDialog("Need a Ttile",'Please add a title for your event', "Got it", "", null, null );
					}else if(resp == '4'){
						presentPopupDialog("Need some descriptions",'Please add some descriptions about your event', "Got it", "", null, null );
					}else if(resp == '5'){
						presentPopupDialog("Date",'It seems the date is not quite right', "Got it", "", null, null );
					}else{	
						parentDiv.find('.preview-container').addClass('hdn');
						image_label.attr('src','');
						parentDiv.find('input, textarea').val('');
						doneWithInterestEditing(parentDiv);
						inner_wrapper.find('.interest-content-right').prepend(resp);
						setVisibleContent();
					}
				}
			});
		}
	
	},'.post-event');
	
	
	$('body').on({
		click:function(){
			var slide_comment_block = $(this).parents('.passed-evt-block').find('.slideshow-comment-wrapper');
			var regular_comment_block = $(this).parents('.passed-evt-block').find('.regular-comment-wrapper');

			if(slide_comment_block.hasClass('hdn')){
				slide_comment_block.removeClass('hdn');
				regular_comment_block.addClass('hdn');
				
			}else{
				clearInterval( $(this).parents('.evt-block').data('timer') );
				slide_comment_block.addClass('hdn');
				loadComment($(this));
			}
		}
	
	},'.passed-evt-block .toggle-slide-comment');
	

	

	$('body').on({
		mouseenter: function() {
			if(!$(this).find('.slideshow-comment-wrapper').hasClass('hdn')){
				$(this).data('timer',startSlideComment($(this))); 
			}
			return false;
		},
		mouseleave: function() {
			if(!$(this).find('.slideshow-comment-wrapper').hasClass('hdn')){
				clearInterval( $(this).data('timer') );
			}
			return false;
		}
	},'.evt-block');
	
	
	$('body').on({
		click:function(){
			var thisE = $(this);
			var key = thisE.attr('data-key');
			if(!thisE.hasClass('set')){
				$.ajax({
					url:AJAX_DIR+'loadUpcomingEvent.php',
					method:'post',
					data:{key:key},
					success:function(resp){
						thisE.addClass('set');
						$('#show-passed-evt').removeClass('act-evt-navi');
						thisE.addClass('act-evt-navi');
						$('#profile-mid-content #passed-evt').addClass('hdn');
						$('#profile-mid-content #upcom-evt').removeClass('hdn').html(resp);
					}	
				});
			}else{
				$('#show-passed-evt').removeClass('act-evt-navi');
				thisE.addClass('act-evt-navi');
				$('#profile-mid-content #passed-evt').addClass('hdn');
				$('#profile-mid-content #upcom-evt').removeClass('hdn');
			}
		}
	},'#show-upcom-evt');
	
	
	$('body').on({
		click:function(){
			$('#show-upcom-evt').removeClass('act-evt-navi');
			$(this).addClass('act-evt-navi');
			$('#profile-mid-content #passed-evt').removeClass('hdn');
			$('#profile-mid-content #upcom-evt').addClass('hdn');
			
		}
	},'#show-passed-evt');
	
	
	
	
	$('body').on({
		keyup:function(evt){
			if(evt.keyCode == 13){
				var add_favor_evt = $(this).parents('#add-favor-evt');
				title = $(this).val().trim();
				if( title == ''){
					showPopOverDialog($(this),add_favor_evt,"Please add a title of the event your recently want to join");
					return false;
				}else{
					hidePopOverDialog($(this));
				}
				//the input is good, now check the textarea
				var textarea = add_favor_evt.find('textarea');
				var desc = textarea.val().trim();
				if(desc == ''){
					textarea.focus();
					showPopOverDialog(textarea,add_favor_evt,"Please add some description about the event your recently want to join");
					return false;
				}else{
					hidePopOverDialog(textarea);
				}
				//the textarea is good
				//submit
				addFavorEvt(title, desc);
				
			}
		},
		blur:function(){
			var parentDiv = $(this).parents('.popover-throwable');
			var popOver = parentDiv.find('.popover-dialog-wrapper');
			if(popOver.length > 0){
				popOver.addClass('hdn');
			}
		}
	
	},'#add-favor-evt input');
	
		$('body').on({
		keypress:function(e){
 			if(e.keyCode == 10 || e.keyCode == 13){
 				e.preventDefault();
 				var add_favor_evt = $(this).parents('#add-favor-evt');
				var desc = $(this).val().trim();
				if(desc == ''){
					showPopOverDialog($(this),add_favor_evt,"Please add some description about the event your recently want to join");
					return false;
				}else{
					hidePopOverDialog($(this));
				}
				
				var input = add_favor_evt.find('input');
				title = input.val().trim();
				if(title == ''){
					showPopOverDialog(input,add_favor_evt,"Please add a title of the event your recently want to join");
					return false;
				}else{
					hidePopOverDialog(input);
				}
				
				addFavorEvt(title, desc);
				
			}
		},
		blur:function(){
			var parentDiv = $(this).parents('.popover-throwable');
			var popOver = parentDiv.find('.popover-dialog-wrapper');
			if(popOver.length > 0){
				popOver.addClass('hdn');
			}
		}
	
	},'#add-favor-evt textarea');
	
	$('body').on({
		click:function(){
			var parentDiv = $(this).parents('#favor-evt-block');
			parentDiv.find('#favor-evt-label-wrapper').addClass('hdn');
			parentDiv.find('#add-favor-evt').removeClass('hdn');
			$(this).addClass('hdn').removeClass('act');
			parentDiv.find('#hide-favor-evt-edit').removeClass('hdn').addClass('act');
			parentDiv.find('.empty-mesg').addClass('hdn');
		}
	},'#favor-evt-block #show-favor-evt-edit');
	
	
	$('body').on({
		click:function(){
			var parentDiv = $(this).parents('#favor-evt-block');
			parentDiv.find('#add-favor-evt').addClass('hdn');
			parentDiv.find('input, textarea').val('');
			parentDiv.find('#favor-evt-label-wrapper').removeClass('hdn');
			$(this).addClass('hdn').removeClass('act');
			parentDiv.find('#show-favor-evt-edit').removeClass('hdn').addClass('act');
			
			if(parentDiv.find('.favor-evt-wrapper').length < 1){
				parentDiv.find('.empty-mesg').removeClass('hdn');
			}
			
		}
	},'#favor-evt-block #hide-favor-evt-edit');
	
	$('body').on({
		mouseover:function(){
			$(this).find('.favor-evt-ic.act').removeClass('hdn');
		},
		mouseleave:function(){
			$(this).find('.favor-evt-ic').addClass('hdn');
		}
	
	},'#favor-evt-block');
	
	
	
	
	
	$('body').on({
		click:function(){
			var thisE = $(this);
			var parent = thisE.parents('#favor-evt-label-wrapper');
			var key = thisE.parents('.favor-evt-wrapper').attr('data-key');
			$.ajax({
				url:AJAX_DIR+'load_favor_evt_desc.php',
				method:'post',
				data:{key:key},
				success:function(resp){
					showPopOverDialog(thisE, parent, resp);
				}	
			});
			return false;
		}
	},'#favor-evt-label-wrapper .favor-evt-label');
	
	
	$('body').on({
		mouseover:function(){
			$(this).find('.remove-favor').removeClass('hdn');
		},
		mouseleave:function(){
			$(this).find('.remove-favor').addClass('hdn');
		}
	},'#favor-evt-label-wrapper .favor-evt-wrapper');
	
	
	
	$('body').on({
		click:function(){
			presentPopupDialog("Remove recent favorite",'Do you want to remove this recent favorite?', "Cancel", "Remove", removeRecentFavor, $(this) );
		}
	
	},'#favor-evt-label-wrapper .favor-evt-wrapper .remove-favor');
	
	$('body').on({
		click:function(){
			var parentDiv = $(this).parents('#favor-evt-block');
			parentDiv.find('#favor-evt-label-wrapper').addClass('hdn');
			parentDiv.find('#add-favor-evt').removeClass('hdn');
			$('#show-favor-evt-edit').addClass('hdn').removeClass('act');
			parentDiv.find('#hide-favor-evt-edit').removeClass('hdn').addClass('act');
			parentDiv.find('.empty-mesg').addClass('hdn');
		}
	
	},'#favor-evt-block #show-favor-edit')
	
	
	
	$('body').on({
		mouseover:function(){
			$(this).find('.side-blur-action.act').removeClass('hdn');
		},
		mouseleave:function(){
			var popover = $(this).find('.action .in_con_opt_w');
			if(!popover.hasClass('act') && $(this).find('.side-block-edit-wrapper').hasClass('hdn')){
				$(this).find('.side-blur-action.act').addClass('hdn');
			}
		}
	
	},'.side-block-wrapper');
	
	$('body').on({
		click:function(){
			var actionDiv = $(this).parents('.side-block-wrapper').find('.action');
			actionDiv.find('.in_con_opt_w').toggleClass('act').toggleClass('hdn');	
			return false;
		}
	},'.side-block-wrapper .show-edit');
	
		$('body').on({
		click:function(){
			var parentDiv = $(this).parents('.side-block-wrapper');
			var editDiv = parentDiv.find('.side-block-edit-wrapper');
			editDiv.addClass('hdn');
			editDiv.find('.edit-input').addClass('hdn');
			parentDiv.find('.front.act').removeClass('hdn');
			parentDiv.find('.side-blur-action.show-edit').addClass('act').removeClass('hdn');
			$(this).removeClass('act').addClass('hdn');
			return false;
		}
	},'.side-block-wrapper .hide-edit');
	
	
	
	$('body').on({
		keyup:function(evt){
			var thisE = $(this);
			var name = thisE.val();
			var parentDiv = thisE.parents('.side-block-wrapper');
			var main = parentDiv.find('.side-block-main-wrapper');
			var edit = parentDiv.find('.side-block-edit-wrapper');
			var empty = parentDiv.find('.empty');
			var input_wrapper = thisE.parents('.edit-input');
			suggest = input_wrapper.find('.school-name-suggest');
			parentDiv.find('.popover-dialog-wrapper').remove();
			if(name.trim() == ''){
				suggest.html('').addClass('hdn');
				return false;
			}
			if(evt.keyCode == 13){
				var hover_act = suggest.find('.light-hover-act .text');
				if(hover_act.length > 0){
					name = hover_act.text().trim();
				}
				if(name.trim() == ''){
					showPopOverDialog(thisE, parentDiv, "Please add a name for your school");
					return false;
				}
				$.ajax({
					url:AJAX_DIR+'save_school.php',
					method:'post',
					data:{name:name},
					success:function(resp){
						console.log(resp);
						if(resp != '1'){
							thisE.val(name);
							empty.removeClass('act').addClass('hdn');
							edit.removeClass('act').addClass('hdn');
							main.addClass('act').removeClass('hdn');
							main.find('.school-name-wrapper .name').text(name).attr('href', DEFAULT_SEARCH_PATH+name);
							parentDiv.find('.side-blur-action.hide-edit').removeClass('act').addClass('hdn');
							parentDiv.find('.side-blur-action.show-edit').addClass('act').removeClass('hdn');
							var school_name_wrapper = parentDiv.find('.school-name-wrapper')
							if(school_name_wrapper.find('.remove').length < 1){
								school_name_wrapper.find('.name').after('<img src="'+IMGDIR+'minus_icon.png" class="remove pointer hdn" height="18" width="18" title="Remove this school" style="margin-right:-2px;float:right">');
							}
							
							
							input_wrapper.addClass('hdn');
						}else{
							suggest.addClass('hdn');
							showPopOverDialog(thisE, parentDiv, "School name not found");
						}
					}	
				});
			}
			
			
			else if(evt.keyCode != 40 && evt.keyCode != 38){
				$.ajax({
					url:AJAX_DIR+'suggest_school.php',
					method:'post',
					data:{name:name},
					success:function(resp){
						suggest.html(resp).removeClass('hdn');
					}	
				});
			}
		},
		keydown:function(e){
			var keyPressed = e.keyCode;
 			if(keyPressed == 13){
 				e.preventDefault();
 				return false;	
 			}else if(keyPressed == 40){
				//down arrow
				var parentDiv = $(this).parents('.side-block-wrapper').find('.school-name-suggest')
				var total_result = parentDiv.find('.suggest-item').length;
				if(total_result > 0){
					global_sch_hover = (++global_sch_hover)%total_result;
					parentDiv.find('.suggest-item').removeClass('light-hover-act');
					parentDiv.find('.suggest-item:eq('+global_sch_hover+')').addClass('light-hover-act');
				}
 			}else if(keyPressed == 38){
 				var parentDiv = $(this).parents('.side-block-wrapper').find('.school-name-suggest')
				var total_result = parentDiv.find('.suggest-item').length;
				if(total_result > 0){
					global_sch_hover = (--global_sch_hover)%total_result;
					parentDiv.find('.suggest-item').removeClass('light-hover-act');
					parentDiv.find('.suggest-item:eq('+global_sch_hover+')').addClass('light-hover-act');
				}
 			}
 		},
		blur:function(){
			var parentDiv = $(this).parents('.popover-throwable');
			var popOver = parentDiv.find('.popover-dialog-wrapper');
			if(popOver.length > 0){
				popOver.addClass('hdn');
			}
		}
	
	},'.side-block-wrapper .school-name-input-wrapper input');
	
	
	
	$('body').on({
		keyup:function(evt){
			var thisE = $(this);
			var name = thisE.val();
			var parentDiv = thisE.parents('.side-block-wrapper');
			var main = parentDiv.find('.side-block-main-wrapper');
			var edit = parentDiv.find('.side-block-edit-wrapper');
			var empty = parentDiv.find('.empty');
			var input_wrapper = thisE.parents('.edit-input');
			suggest = input_wrapper.find('.major-name-suggest');
			if(name.trim() == ''){
				suggest.html('').addClass('hdn');
			}
			if(evt.keyCode == 13){
				if(name.trim() == ''){	
					showPopOverDialog(thisE, parentDiv, "Please add a name for the subject you study");
					return false;
				}
				$.ajax({
					url:AJAX_DIR+'save_major.php',
					method:'post',
					data:{name:name},
					success:function(resp){
						console.log(resp);
						if(resp != '1'){
							empty.removeClass('act').addClass('hdn');
							edit.removeClass('act').addClass('hdn');
							main.addClass('act').removeClass('hdn');
							main.find('.major-wrapper .name').text(name);
							suggest.html(resp).removeClass('hdn');
							parentDiv.find('.side-blur-action.hide-edit').removeClass('act').addClass('hdn');
							parentDiv.find('.side-blur-action.show-edit').addClass('act').removeClass('hdn');
							var major_wrappr = parentDiv.find('.major-wrapper')
							if(major_wrappr.find('.remove').length < 1){
								major_wrappr.find('.name').after('<img src="'+IMGDIR+'minus_icon.png" class="remove pointer hdn" height="18" width="18" title="Remove this study" style="margin-right:-2px;float:right">');
							}
							input_wrapper.addClass('hdn');
						}else{
							showPopOverDialog(thisE, parentDiv, "Major not found");
						}
					}	
				})
			}else{
				$.ajax({
					url:AJAX_DIR+'suggest_major.php',
					method:'post',
					data:{name:name},
					success:function(resp){
						suggest.html(resp).removeClass('hdn');
					}	
				});
			}
		},
		blur:function(){
			var parentDiv = $(this).parents('.popover-throwable');
			var popOver = parentDiv.find('.popover-dialog-wrapper');
			if(popOver.length > 0){
				popOver.addClass('hdn');
			}
		}
	
	},'.side-block-wrapper .major-name-input-wrapper input');
	
	
	
	
	
	
	
	
	
	

	
	
	$('body').on({
		click:function(){
			var parentDiv = $(this).parents('.in_con_opt_w');
			parentDiv.find('.main').addClass('hdn').css('left','-100%');
			var int_group = parentDiv.find('.sub.educ');
			var w = $(this).parents('.inner-w');
			int_group.removeClass('hdn').animate({
				'right':'0px'
				},{
				duration: 200,
				complete: function() {
					parentDiv.animate({
					'height':int_group.height()
					},100);
					w.css('height',int_group.height());
				}
		  		});
		}
	},'.in_con_opt_w .main .update-education');


	$('body').on({
		click:function(){
			var parentDiv = $(this).parents('.side-block-wrapper');
			var edit_wrapper = parentDiv.find('.side-block-edit-wrapper');
			parentDiv.find('.front').addClass('hdn');
			edit_wrapper.removeClass('hdn');
			var input_wrapper = edit_wrapper.find('.school-name-input-wrapper');
			input_wrapper.removeClass('hdn');
			input_wrapper.find('input').focus();
			parentDiv.find('.side-blur-action.show-edit').removeClass('act').addClass('hdn');
			parentDiv.find('.side-blur-action.hide-edit').addClass('act').removeClass('hdn');
			parentDiv.find('.title .in_con_opt_w').addClass('hdn');
		}
	},'.side-block-wrapper .add-school');
	
	
	$('body').on({
		click:function(){
			var parentDiv = $(this).parents('.side-block-wrapper');
			var edit_wrapper = parentDiv.find('.side-block-edit-wrapper');
			parentDiv.find('.front').addClass('hdn');
			edit_wrapper.removeClass('hdn');
			var input_wrapper = edit_wrapper.find('.major-name-input-wrapper');
			input_wrapper.removeClass('hdn');
			input_wrapper.find('input').focus();
			parentDiv.find('.side-blur-action.show-edit').removeClass('act').addClass('hdn');
			parentDiv.find('.side-blur-action.hide-edit').addClass('act').removeClass('hdn');
			parentDiv.find('.title .in_con_opt_w').addClass('hdn');
		}
	},'.side-block-wrapper .add-study');
	
	


	$('body').on({
		click:function(){
			var parentDiv = $(this).parents('.school-name-input-wrapper');
			parentDiv.find('.school-name-suggest').addClass('hdn');
			parentDiv.find('input').val($(this).text().trim()).focus();
			
		}
	},'.school-name-suggest .school-name-item');

	$('body').on({
		click:function(){
			var parentDiv = $(this).parents('.major-name-input-wrapper');
			parentDiv.find('.major-name-suggest').addClass('hdn');
			parentDiv.find('input').val($(this).text().trim()).focus();
			
		}
	},'.major-name-suggest .major-name-item');

	
	
	$('body').on({
		mouseover:function(){
			$(this).find('.remove').removeClass('hdn');
		},
		mouseleave:function(){
			$(this).find('.remove').addClass('hdn');
		}
	},'.school-name-wrapper');
	
	
	$('body').on({
		click:function(){
			var school_name = $(this).parents('.school-name-wrapper').find('.name').text().trim();
			presentPopupDialog("Remove School", "Do you want to remove school \""+school_name+"\"", "Cancel", "Remove", removeSchool, $(this) );
		}
	},'.school-name-wrapper .remove');
	
	
	$('body').on({
		mouseover:function(){
			$(this).find('.remove').removeClass('hdn');
		},
		mouseleave:function(){
			$(this).find('.remove').addClass('hdn');
		}
	},'.major-wrapper');
	
	
	$('body').on({
		click:function(){
			var major_name = $(this).parents('.major-wrapper').find('.name').text().trim();
			presentPopupDialog("Remove Study", "Do you want to remove the study \""+major_name+"\"", "Cancel", "Remove", removeStudy, $(this) );
		}
	},'.major-wrapper .remove');

	
});