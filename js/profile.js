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
	
	$('body #edit-dialog-wrapper-inner').on({
		click:function(e){
			e.stopPropagation();
			closeEditDialog(true);
		}
	},'#save-e-p-button');
	
	
	
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
			return false;
		}
	},'#add-scene-button');
	

	
	
});