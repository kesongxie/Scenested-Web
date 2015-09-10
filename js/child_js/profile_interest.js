window.onpopstate=function(event){
	var request_interests_container = $('.interest-content-inner-wrapper[data-key='+event.state['key']+']');
	var activate_label = $('.interest-sider-navi[data-labelfor='+event.state['key']+']');
	if(request_interests_container.length > 0){
		$('.interest-content-inner-wrapper').addClass('hdn').removeClass('blk');
		request_interests_container.addClass('blk').removeClass('hdn');
	}else{
		var label_key = event.state['key'];
		$.ajax({
			url: AJAX_DIR+'ld_interest.php',
			method:'post',
			data:{label_key:label_key},
			success:function(resp){
				if(resp != '1'){
					$('#interest-content-wrapper').append(resp);
					$('.interest-content-inner-wrapper').addClass('hdn').removeClass('blk');
					var container = $('.interest-content-inner-wrapper[data-key='+event.state['key']+']');
					container.addClass('blk').removeClass('hdn');
					setVisibleContentWithParent(container, "Read more");
				}
			}
		});
	}
	activate_label.css('-webkit-animation',"rubberBand 0.4s").css('animation',"rubberBand 0.4s");
	activate_label.parents('#i-interest-navi').find('.interest-side-label .txt_ofl').removeClass('red-act');
	activate_label.find('.txt_ofl').addClass('red-act');
	setTimeout(function(){
		activate_label.css('-webkit-animation',"").css('animation',"");
	},200);	
}

function deleteInterest(sender){
	var key = sender.parents('.interest-content-inner-wrapper').attr('data-key');
	$.ajax({
		url:AJAX_DIR+'deleteInterest.php',
		method:'post',
		data: {key:key},
		success:function(resp){
			console.log(resp);
			resetDialog();
			var inner_wrapper = $('#interest-content-wrapper .interest-content-inner-wrapper[data-key='+key+']');
			inner_wrapper.css('-webkit-animation',"bounceOutDown 1s").css('animation',"bounceOutDown 1s");
			var side_label = $('#i-interest-navi .interest-side-label[data-labelfor='+key+']');
			side_label.css('-webkit-animation',"bounceOutDown 1s").css('animation',"bounceOutDown 1s");
			setTimeout( function() {
				inner_wrapper.remove();
				side_label.remove();
			}, 500);
			setTimeout( function() {
				var labels =  $('#i-interest-navi').find('.interest-side-label');
				if(labels.length >= 1){
					var firstInterestLabel = labels.first();
					firstInterestLabel.addClass('active');
					loadInterest(firstInterestLabel);
					resetInterestState(false);
				}else{
					$('#interest-content-wrapper').addClass('hdn');
					$('#add-new-interest-wrapper').removeClass('hdn');
					$('#add-new-interest-wrapper').find('.cancel-button').addClass('hdn');
				}
			}, 600);
		}	
	});
}


function resetInterestState(push){
	var label_to_be_active =  $('#i-interest-navi .interest-sider-navi.active');
	if(label_to_be_active.length > 0){
		var url = $('#i-interest-navi .interest-sider-navi.active').attr('data-href');
		var key = $('#i-interest-navi .interest-sider-navi.active').attr('data-labelfor');
		if(push){
			history.pushState({key: key}, null ,url);
		}else{
			history.replaceState({key: key}, null ,url);
		}
	}else{
		
	}
}


$(window).load(function(){
	var url = $('#i-interest-navi .interest-sider-navi.active').attr('data-href');
	var key = $('#i-interest-navi .interest-sider-navi.active').attr('data-labelfor');
	history.replaceState({key: key}, null ,url);
});


$(document).ready(function(){
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
						$('.interest-content-inner-wrapper').addClass('hdn').removeClass('blk');
						$('#i-interest-navi .interest-sider-navi').removeClass('active');
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
						
						resetInterestState(true);
						setTimeout(function(){
							add_interest_wrapper.css('-webkit-animation',"").css('animation',"");
						},200);
					}
				}
			});
		}
	
	},'#add-interest');
	
	$('body').on({
		click:function(){
			presentPopupDialog("Delete Interest",'Are you sure to delete this interest?<div style="color:#c4140e;font-size:13px;">*Note, all the interest posts and friends and other relavant data in this interest will lost</div>', "Cancel", "Delete", deleteInterest, $(this) );
		}
	
	},'.interest-profile .remove_interest');
	

});