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
$(document).ready(function(){
	$('body').on({
		click:function(){
			var parentDiv = $(this).parents('.interest-profile');
			parentDiv.find('.interest-profile-intro').css('-webkit-animation',"flipOutY 0.4s").css('animation',"flipOutY 0.4s").addClass('hdn');
			parentDiv.find('.interest-profile-moment').css('-webkit-animation',"flipInY 0.4s").css('animation',"flipInY 0.4s").removeClass('hdn');

		}
	
	},'.interest-profile-intro .dialog-header .post');

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
		click:function(){
			var parentDiv = $(this).parents('.sub');
			doneWithInterestEditing(parentDiv);
		}
	},'.interest-profile .sub .cancel-button');
	
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
	
	
	
	
	$('body').on({
		click:function(){
			var index_post_wrapper = $(this).parents('.feed-right').find('#index-post-wrapper');
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
			data.append('i','1');
			
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
						index_post_wrapper.after(resp);
						setVisibleContent();
					}
				}
			});
		}
	
	},'.post-moment');
	
	
		
	
	$('body').on({
		click:function(){
			var index_post_wrapper = $(this).parents('.feed-right').find('#index-post-wrapper');
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
			data.append('i','1');
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
						index_post_wrapper.after(resp);
						setVisibleContent();
					}
				}
			});
		}
	
	},'.post-event');
	
	
	
	
	
	
	$('body').on({
		click:function(){
			var parentDiv = $(this).parents('.interest-content-inner-wrapper');
			var switcher = parentDiv.find('.switcher');
			if(switcher.length > 0){
				//  switcher.toggle('hdn');
				switcher.toggleClass('hdn');
			}else{
				//load switcher
				var key = parentDiv.attr('data-key');
				$.ajax({
 					url:AJAX_DIR+'index_switcher_post.php',
 					method:'post',
 					data:{key:key},
 					success:function(resp){
 						parentDiv.append(resp);
 					}
 				});
			}
			return false;
		}
	},'#index-post-wrapper .interest-profile .bar-title.option');
	
	
	
	$('body').on({
		click:function(){
			var parentDiv = $(this).parents('#index-post-wrapper');
			var inner = $(this).parents('.interest-content-inner-wrapper');
			var activeKey = inner.attr('data-key');
			var key = $(this).attr('data-key');
			$(this).parents('.switcher').addClass('hdn');
			if(activeKey != key){
				var request_inner = parentDiv.find('.interest-content-inner-wrapper[data-key='+key+']');
				if(request_inner.length > 0){
					inner.addClass('hdn');
					request_inner.removeClass('hdn');
				}else{
					$.ajax({
						url:AJAX_DIR+'load_index_post.php',
						method:'post',
						data:{key:key},
						success:function(resp){
							parentDiv.append(resp);
							inner.addClass('hdn');
						}
			
					});
				}
			}
		}
	},'#index-post-wrapper .interest-profile .switcher .in_con_w_opt_it');
	
	
	
	$('#add-new-interest').on({
		click:function(){
			var parentDiv = $('#add-new-interest');
			var loading_wrapper = parentDiv.find('.loading-icon-wrapper');
			var actionButton = parentDiv.find('.edit-dialog-footer .action-button');
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
			
			loading_wrapper.show();
			actionButton.text("Loading...");
			$.ajax({
				url:AJAX_DIR+'add_interest.php',
				type:'POST',
				processData: false,
				contentType: false,
				data:data,
				success:function(resp){
					loading_wrapper.hide();
					actionButton.text("Add Interest");
					if(resp == '1'){
						presentPopupDialog("Bad Image",BAD_IMAGE_MESSAGE, "Got it", "", null, null );
					}else if(resp == '2'){
						presentPopupDialog("Need a Name", "Please add a name for your interest", "Got it", "", null, null );
					}else if(resp == '3'){
						presentPopupDialog("Interest Existed",'The interest <span class="plain-lk pointer" style="color:#062AA3">'+ name + '</span> has already existed', "Got it", "", null, null );
					}else{	
						var add_interest_wrapper = $('#add-new-interest-wrapper');
						add_interest_wrapper.css('-webkit-animation',"zoomOut 0.5s").css('animation',"zoomOut 0.5s").addClass('hdn');
						$('.interest-content-inner-wrapper').addClass('hdn');
						var mid_content = $($.parseHTML(resp)).filter('#node-mid-content');								
						var side_content = $($.parseHTML(resp)).filter('#node-side-content');			
						$('#interest-content-wrapper').append(mid_content.html()).removeClass('hdn');
						side_content.children('.interest-side-label').css('-webkit-animation',' bounceInUp 1s');
						$('#i-interest-navi').append(side_content.html());

						setVisibleContentWithParent(mid_content.find('.interest-content-inner-wrapper'),'Read more');
						//reset elements
						parentDiv.find('input, textarea, select').val('');
						parentDiv.find('.camera-center').show();
						image_label.attr('src','').addClass('hdn');
						
						setTimeout(function(){
							add_interest_wrapper.css('-webkit-animation',"").css('animation',"");
						},200);
					}
				}
			});
		}
	
	},'#add-interest');
	
	
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
	
	
	
	
	
	
	
	
	
	
});