/* global javascript file, contains core functions */
var SIGNUP_ALERT_MESSAGE = new Array();
var DOCUMENT_ROOT = "http://localhost:8888/lsere/";
var INDEX_PAGE = DOCUMENT_ROOT + "index.php";
var AJAX_DIR = DOCUMENT_ROOT+"ajax/";
var NOTIFICATION_CENTER_ON = false;

function readURL(input,tg) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function (e) {
			tg.attr('src', e.target.result);
		}
		reader.readAsDataURL(input.files[0]);
	}
}

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
	$('#loggedin-menu').on({
		click:function(){
			$('.popover').toggle();
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
	
	
});