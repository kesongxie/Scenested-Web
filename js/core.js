/* global javascript file, contains core functions */
var SIGNUP_ALERT_MESSAGE = new Array();
var DOCUMENT_ROOT = "http://localhost:8888/lsere/";
var INDEX_PAGE = DOCUMENT_ROOT + "index.php";
var AJAX_DIR = DOCUMENT_ROOT+"ajax/";
var AJAX_PHTML_DIR = AJAX_DIR+"phtml/"
var NOTIFICATION_CENTER_ON = false;
var BAD_IMAGE_MESSAGE = "A valid image is of type PNG or JPG and it's less than 5M"

function readURL(input,tg) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function (e) {
			tg.attr('src', e.target.result);
		}
		reader.readAsDataURL(input.files[0]);
	}
}



function resetDialog(parentElement){
	$('#dialog-popup-overlay').addClass('hdn');
	$('#popup-dialog-wrapper').addClass('hdn');
	parentElement.find('.dialog-header .bar-title').text('');
	parentElement.find('.dialog-body .body-text').html('');
	parentElement.find('.dialog-footer .dismiss').text('');
	parentElement.find('.dialog-footer .action-button').text('').addClass('hdn');
}

//sender is the element which triggered the popup dialog
function setDialog(parentElement,title, body, dismissButtonnText, actionButtonText, action, sender){
	parentElement.find('.dialog-header .bar-title').text(title);
	parentElement.find('.dialog-body .body-text').html(body);
	parentElement.find('.dialog-footer .dismiss').text(dismissButtonnText);
	if(actionButtonText != ''){
		var actionButton = parentElement.find('.dialog-footer .action-button');
		actionButton.text(actionButtonText).removeClass('hdn');
		actionButton.on('click',function(){
			action(sender);
		});	
	}
}

function presentPopupDialog(title, body, dismissButtonnText, actionButtonText, action, sender ){
	$('#dialog-popup-overlay').removeClass('hdn');
	$('#popup-dialog-wrapper').removeClass('hdn');
	if($('body').find('#popup-dialog').length < 1){
		$('#popup-dialog-wrapper').load( AJAX_PHTML_DIR+"popup_dialog.phtml",function(){
			setDialog($(this),title, body, dismissButtonnText, actionButtonText, action, sender );
		});
	}else{
		setDialog($('#popup-dialog-wrapper'),title, body, dismissButtonnText, actionButtonText, action, sender );
	}
	if(actionButtonText != '' && action != null){
		$('#popup-dialog').on('click',function(){
			action(sender);
		})
	}
		
}

function setVisibleContent(){
	$('.visible-content').each(function(){
		if($(this)[0].scrollHeight > $(this).innerHeight() ){
			if($(this).parents('.visible-post-scope').find('.visible-control').length < 1){
				$(this).parents('.visible-post-scope').append('<div class="visible-control plain-lk pointer inline-blk rdm" >Read more</div>');
			}
		}else{
			$(this).parents('.visible-post-scope').find('.visible-control').remove();
		}
	});
}


$(document).keyup(function(evt){
	if(evt.keyCode==27)
	{	
		resetDialog($('#popup-dialog'));
	}
});


$(document).click(function(){
	$('.popover').hide();
	if(NOTIFICATION_CENTER_ON){
		//collapse
		$('#side-bar-notification-center').animate({
				'right':'-26%',	
		},200);
	}
});



$(document).ready(function(){
	setVisibleContent();

	$('#loggedin-menu').on({
		click:function(){
			$(this).parents('#loggedin-menu').find('.popover').toggle();
			return false;
		}
	},'#loggin-user-icon');
	
	$('#loggedin-menu').on({
		click:function(){
			if(!NOTIFICATION_CENTER_ON){
				$('#side-bar-notification-center').animate({
					'right':'0',	
				},200);
				$('#loggedin-menu #index-noti-red-spot').hide();
				NOTIFICATION_CENTER_ON = true;
			}else{
				$('#side-bar-notification-center').animate({
					'right':'-26%',	
				},200);
				NOTIFICATION_CENTER_ON = false;
			}
			
			return false;
		}
	},'#header-notification-delegate');
	
	$('body').on({
		click:function(){
			return false;
		}
	},'#side-bar-notification-center');
	
	
	
	$('body').on({
		click:function(){
			resetDialog($(this).parents('#popup-dialog'))
		}
	},'.dismiss');
	
	
	
	$('body').on({
		click:function(){
			if($(this).hasClass('rdm')){
				$(this).text('Show less');
				$(this).parents('.visible-post-scope').find('.visible-content').removeClass('limit-height');
				$(this).removeClass('rdm');
			}else{
				$(this).text('Read more');
				$(this).parents('.visible-post-scope').find('.visible-content').addClass('limit-height');
				$(this).addClass('rdm');
			}
		}
	},'.visible-control');
	
	
	
});