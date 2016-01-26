function openEditDialog(targetElementId){
	$('.overlay, #edit-dialog-wrapper').removeClass('hdn');
	var parent_wrapper = $('#edit-dialog-wrapper');
	parent_wrapper.find('.segue-wrapper').addClass('hdn');
	var edit_wrapper = parent_wrapper.find(targetElementId);
	edit_wrapper.removeClass('hdn');
	edit_wrapper.css('height', edit_wrapper.find('.segue.act').height());
	edit_wrapper.css('position','relative');
	parent_wrapper.find('.entry-focus').focus();
	$('body').addClass('unscrollable');
}



$(document).ready(function(){
	$(window).resize(function(){
		var dialog_wrapper = $('#edit-profile-dialog-wrapper');
		if(!dialog_wrapper.hasClass('hdn')){
			var window_w = this.innerWidth;
			var edit_cover_wrapper = dialog_wrapper.find('#edit-cover-wrapper');
			if(window_w <= 520){
				edit_cover_wrapper.css('height', $(this).width()/3.2);
			}else{
				edit_cover_wrapper.css('height', '160px');
			}
		}
	});
	$('body').on({
		mouseover:function(){
			$('#a-e-p-overlay, #a-e-p-edit').css({'-webkit-animation':'editSlideUp 0.2s','bottom':'0px'});
		},
		mouseleave:function(){
			$('#a-e-p-overlay, #a-e-p-edit').css({'-webkit-animation':'editSlideDown 0.2s','bottom':'-40px'});
		}
	},'#profile-avator-wrapper');
	
	
	$('body #edit-dialog-wrapper-inner').on({
		click:function(e){
			e.stopPropagation();
			closeEditDialog(true);
		}
	},'#cancel-e-p-button');
	
	
	
	
	
	$('body').on({
		click:function(){
			resetSegueDetail();
			$(this).parents('.segue-detail').css({'-webkit-animation':'','animation':''});
			$('#a-e-p-overlay, #a-e-p-edit').css({'-webkit-animation':'editSlideDown 0.5s','animation':'editSlideDown 0.5s','bottom':'-40px'});
			openEditDialog('#edit-profile-dialog-wrapper');
			return false;
		}
	},'#avator-edit-profile');
	
	$('body').on({
		click:function(){
			resetSegueDetail();
			openEditDialog('#add-scene-dialog-wrapper');
			loadDatePickerSegue('#add-scene-dialog-wrapper');
			return false;
		}
	},'#trigger-add-scene-button');
	
	
	$('#profile-cover-image').draggable({
		containment:'#profile-cover-constraint',
		scroll:false,
		axis:'y'
	});	
	
	$('#profile-avator-image').draggable({
		containment:'#profile-avator-constraint',
		scroll:false,
		axis:'y'
	});	
	
	

	$('#edit-profile-dialog-wrapper').on({
		change:function(){
			var parent_wrapper = $(this).parents('#edit-cover-wrapper');
			var target = parent_wrapper.find('#profile-cover-image');
			var upload_prompt = parent_wrapper.find('.upload-label, .overlay');
			upload_prompt.addClass('hdn');
			parent_wrapper.css('cursor', 'move');
			var constraint  = parent_wrapper.find('#profile-cover-constraint');
			var visible_width = parent_wrapper.width();
			var visible_height = parent_wrapper.height();
			readURL(this, target, false, true, constraint, visible_width, visible_height);
		}
	},'#cover-image-upload');
	
	
	$('#edit-profile-dialog-wrapper').on({
		change:function(){
			var parent_wrapper = $(this).parents('#edit-avator-image-wrapper');
			var target = parent_wrapper.find('#profile-avator-image');
			var upload_prompt = parent_wrapper.find('.upload-label, .overlay');
			upload_prompt.addClass('hdn');
			parent_wrapper.css('cursor', 'move');
			var constraint  = parent_wrapper.find('#profile-avator-constraint');
			var visible_width = parent_wrapper.width();
			var visible_height = parent_wrapper.height();
			readURL(this, target, true, true, constraint, visible_width, visible_height);
		}
	},'#avator-image-upload');
	




	function uploadProfileCover(){
			var dialog_wrapper = $('#edit-profile-dialog-wrapper');
			var parent_wrapper = dialog_wrapper.find('#edit-cover-wrapper');
			var target = parent_wrapper.find('#profile-cover-image');
			var formData = new FormData();
			var adjusted_pos, image_length, adjusted_ratio_width, adjusted_ratio_height, image_container_scale_width, image_container_scale_height;
			
			if(target.attr('data-mode') == DRAG_MODE.verticalMode){
				adjusted_pos = getIntValueFromCSSStyle(target.css('top'));
				image_length = target.height();
				adjusted_ratio_width = 0;
				adjusted_ratio_height = adjusted_pos/image_length;
				image_container_scale_width = 1;
				image_container_scale_height = parent_wrapper.height()/image_length;
			}else{
				adjusted_pos = getIntValueFromCSSStyle(target.css('left'));
				image_length = target.width();
				adjusted_ratio_width = adjusted_pos/image_length;
				adjusted_ratio_height = 0;
				image_container_scale_width = parent_wrapper.width()/image_length;
				image_container_scale_height = 1;
			}
			formData.append('adjusted_ratio_height', -adjusted_ratio_height);
			formData.append('image_container_scale_height', image_container_scale_height);
			formData.append('adjusted_ratio_width', -adjusted_ratio_width);
			formData.append('image_container_scale_width', image_container_scale_width);
			var imageFile = parent_wrapper.find('#cover-image-upload');
			formData.append('file', imageFile[0].files[0]);
			
			var ds = new DataState(imageFile);
			ds.setTargetDataStateToProcessing();
			$.ajax({
				url:AJAXDIR+'upload_cover.php',
				method:'post',
				data:formData,
				processData:false,
				contentType:false,
				success:function(resp){
					if(resp != '1'){
						$('#profile-cover').attr('src',resp);
						ds.setTargetDataStateToReady();
						imageFile.val('');
					}else{
						console.log('error');
					}
				}
			
			});
	}

	function uploadProfileAvator(){
			var dialog_wrapper = $('#edit-profile-dialog-wrapper');
			var parent_wrapper = dialog_wrapper.find('#edit-avator-image-wrapper');
			var target = parent_wrapper.find('#profile-avator-image');
			var formData = new FormData();
			var adjusted_pos, image_length, adjusted_ratio_width, adjusted_ratio_height, image_container_scale_width, image_container_scale_height;
			
			if(target.attr('data-mode') == DRAG_MODE.verticalMode){
				adjusted_pos = getIntValueFromCSSStyle(target.css('top'));
				image_length = target.height();
				adjusted_ratio_width = 0;
				adjusted_ratio_height = adjusted_pos/image_length;
				image_container_scale_width = 1;
				image_container_scale_height = parent_wrapper.height()/image_length;
			}else{
				adjusted_pos = getIntValueFromCSSStyle(target.css('left'));
				image_length = target.width();
				adjusted_ratio_width = adjusted_pos/image_length;
				adjusted_ratio_height = 0;
				image_container_scale_width = parent_wrapper.width()/image_length;
				image_container_scale_height = 1;
			}
			formData.append('adjusted_ratio_height', -adjusted_ratio_height);
			formData.append('image_container_scale_height', image_container_scale_height);
			formData.append('adjusted_ratio_width', -adjusted_ratio_width);
			formData.append('image_container_scale_width', image_container_scale_width);
			var imageFile = parent_wrapper.find('#avator-image-upload');
			formData.append('file', imageFile[0].files[0]);
			
			var ds = new DataState(imageFile);
			ds.setTargetDataStateToProcessing();
		
			$.ajax({
				url:AJAXDIR+'upload_avator.php',
				method:'post',
				data:formData,
				processData:false,
				contentType:false,
				success:function(resp){
					if(resp != '1'){
						$('#profile-avator').attr('src',resp);
						ds.setTargetDataStateToReady();
						imageFile.val('');
					}else{
						console.log('error');
					}
				}
			
			});
	}
	
	function updateProfileBio(bio){
		var bio_text =  bio.text().trim().replace(/\u00a0/g,' '); //replace &nbsp with space
		var ds = new DataState(bio);
		ds.setTargetDataStateToProcessing();
		$.ajax({
			url:AJAXDIR+'update_bio.php',
			method:'post',
			data:{bio_text:bio_text},
			success:function(resp){
				$('#profile-left-content-wrapper #profile-short-bio').html(resp);
				ds.setTargetDataStateToReady();
			}
		});
	}
	
	

	function closeSystemMessage(){
		$('#glob-overlay, #system-message').addClass('hdn');
		$('body').removeClass('unscrollable');
	}
	

	/*  
		@param avator_ds 
			a DataState instance for avator input, containing the the data-state, either processing or ready
			isStateReady returns true when the file finishes its uploading
		@param cover_ds 
			a DataState instance for cover input, containing the the data-state, either processing or ready
			isStateReady returns true when the file finishes its uploading
		@param bio_ds 
			a DataState instance for bio, containing the the data-state, either processing or ready
			isStateReady returns true when the file finishes its uploading
		@param intIdM
			a intervalIdManager instance that contains the information of the intervalId, after the intervalId is set
	*/
	function saveProfileCallBack(avator_ds, cover_ds, bio_ds, intIdM){
		if(avator_ds.isStateReady() && cover_ds.isStateReady() && bio_ds.isStateReady()){
			closeSystemMessage();
			clearInterval(intIdM.getIntervalIdOnElement());
			var edit_wrapper = $('#edit-profile-dialog-wrapper');
			edit_wrapper.find('#edit-cover-wrapper, #edit-avator-image-wrapper').css('cursor','pointer');
			edit_wrapper.find('.upload-label').removeClass('hdn');
			intIdM.destoryIntervalIdOnElement();
		}
	}
	
	
	$('#edit-profile-dialog-wrapper').on({
		click:function(){
			var dialog_wrapper =  $(this).parents('#edit-profile-dialog-wrapper');
			var avatorFile = dialog_wrapper.find('#avator-image-upload');
			
			//update avator
			if(avatorFile[0].files.length > 0){
				uploadProfileAvator();
			}
			
			//update cover
			var coverFile = dialog_wrapper.find('#cover-image-upload');
			if(coverFile[0].files.length > 0){
				uploadProfileCover();
			}
			//update bio
			var bio = dialog_wrapper.find('#edit-short-bio');
			updateProfileBio(bio);
			
			var avator_ds = new DataState(avatorFile);
			var cover_ds = new DataState(coverFile);
			var bio_ds = new DataState(bio);
	
			dialog_wrapper.addClass('hdn');
			var systemMessage = $('#system-message');
			systemMessage.removeClass('hdn');
			
			var intIdM = new intervalIdManager(systemMessage);
			var intervalId = setInterval(saveProfileCallBack, 1000, avator_ds, cover_ds,bio_ds, intIdM);
			intIdM.setIntervalIdOnElement(intervalId);
		}
	},'#save-e-p-button');

	
	$('#edit-profile-dialog-wrapper').on({
		keyup:function(evt){
			if(evt.keyCode == 13){
				var thisE = $(this);
				var scene_name = thisE.val().trim();
				$.ajax({
					url:AJAXDIR+'addSceneName.php',
					method:'post',
					data:{scene_name:scene_name},
					success:function(resp){
						if(resp == '1'){
							alert('the given name has already exsited');
						}else{
							thisE.val('');
							var input_wrapper = $('#add-scene-label-input-wrapper');
							var parent_body = input_wrapper.parents('body');
							var empty_state = parent_body.find('.empty-state');
							var empty_state_visible = true;
							if(!empty_state.hasClass('hdn')){
								empty_state.addClass('hdn');	
							}else{
								empty_state_visible = false;
							}
							input_wrapper.after(resp);
							var respHtml = $.parseHTML(resp);
							var label_text = $(respHtml).find('.scene-label').text();
							appendComma = (empty_state_visible)?'':', ';
							$('#profile-scene-label-wrapper').find('.profile-bio').prepend('<a href="#" class="plain-lk scene-label">'+label_text+'</a>'+appendComma);
							var text_list_text = $('#edit-scene-label .text-list .text');
							var label_texts = text_list_text.text();
							label_texts = label_text+appendComma + label_texts;
							text_list_text.text(label_texts);
						
						}
					}
				});
			}
		}
	},'#add-scene-label-input');
	
	
	
	
	
	
});