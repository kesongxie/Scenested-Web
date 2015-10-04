/* global javascript file, contains core functions */
var SIGNUP_ALERT_MESSAGE = new Array();
var DOCUMENT_ROOT = "http://www.lsere.com/";
var INDEX_PAGE = DOCUMENT_ROOT + "index.php";
var AJAX_DIR = DOCUMENT_ROOT+"ajax/";
var AJAX_PHTML_DIR = AJAX_DIR+"phtml/";
var IMGDIR = DOCUMENT_ROOT+'media/';
var DEFAULT_SEARCH_PATH = DOCUMENT_ROOT+'search.php?q='

var BAD_IMAGE_MESSAGE = "A valid image is of type PNG or JPG and it's less than 5M";



function readURL(input,tg) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function (e) {
			tg.attr('src', e.target.result);
		}
		reader.readAsDataURL(input.files[0]);
	}
}

//parent is the outer wrapper that contains all the popover-dialog-wrappers
function showPopOverDialog(thisE, parent, content){
		parent.find('.popover-dialog-wrapper').addClass('hdn');
		var parentDiv = thisE.parents('.popover-throwable');
		var popOver = parentDiv.find('.popover-dialog-wrapper');
		if(popOver.length < 1){
			$.get(AJAX_PHTML_DIR+"popover_dialog.phtml", function(resp) {
				parentDiv.prepend(resp);
				parentDiv.find('.popover-dialog-wrapper').css('top',parentDiv.height()+10);
				parentDiv.find('.popover-dialog-content').html(content);
			});
		}else{
			popOver.removeClass('hdn').show();
		}
	}
	
function hidePopOverDialog(thisE){
	thisE.parents('.popover-throwable').find('.popover-dialog-wrapper').addClass('hdn');
}


function resetDialog(){

	if($('#dialog-popup-overlay').hasClass('hdn')){
		$('#evt-preview').html('').addClass('hdn').removeAttr('data-key');
	}
	
	$('.ol').not('.hdn').last().addClass('hdn');
	$('#popup-dialog-wrapper').addClass('hdn');
	if(!$('#photo-preview').hasClass('hdn')){
		$('#photo-preview').addClass('hdn');
		$('#photo-preview .content-wrapper').html('');
	}
	$('body').css('overflow','auto');
	
	var dialogParent = $('#popup-dialog');
	dialogParent.find('.dialog-header .bar-title').text('');
	dialogParent.find('.dialog-body .body-text').html('');
	dialogParent.find('.dialog-footer .dismiss').text('');
	dialogParent.find('.dialog-footer .action-button').unbind().text('').addClass('hdn');
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
			resetDialog();
			return false;
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
		
}

function setVisibleContent(){
	$('.visible-content').each(function(){
		if( typeof $(this)[0].scrollHeight !== "undefined" &&  $(this)[0].scrollHeight > $(this).innerHeight() ){
			if($(this).parents('.visible-post-scope').find('.visible-control').length < 1){
				$(this).parents('.visible-post-scope').find('.visible-content').after('<div class="visible-control plain-lk pointer inline-blk rdm" >Read more</div>');
			}
		}else{
			$(this).parents('.visible-post-scope').find('.visible-control').remove();
		}
	});
}

function checkPassword(str)
  {
   	//combination of letters and numbers
    // at least six characters
    var re = /(?=.*\d)(?=.*[a-zA-Z]).{6,}/;
    return re.test(str);
}



function setVisibleContentWithParent(parent, text){
	parent.find('.visible-content').each(function(){
		var scrollHeight = $(this)[0].scrollHeight;
		if( scrollHeight > 32 && typeof scrollHeight !== "undefined" &&  scrollHeight > $(this).innerHeight() ){
			if($(this).parents('.visible-post-scope').find('.visible-control').length < 1){
					$(this).parents('.visible-post-scope').find('.visible-content').after('<div class="visible-control plain-lk pointer inline-blk rdm" >'+text+'</div>');
			}
		}
	});	
}


// function resetResizableOption(thisE){
// 	var parentDiv = thisE.parents('.in_con_opt_w');
// 	parentDiv.find('.main').addClass('hdn').css('left','-100%');
// 	var c_group = parentDiv.find('.sub.connection-group');
// 	var w = thisE.parents('.inner-w');
// 	c_group.removeClass('hdn').animate({
// 		'right':'0px'
// 		},{
// 		duration: 200,
// 		complete: function() {
// 			parentDiv.animate({
// 			'height':c_group.height()
// 			},100);
// 			w.css('height',c_group.height());
// 		}
// 	});
// }


function leaveInterestGroup(thisE){
	var parentDiv = thisE.parents('.in_con_border_top');
	var key = parentDiv.attr('data-key');
	$.ajax({
		url:AJAX_DIR+'leaveInterest.php',
		method:'post',
		data: {key:key},
		success:function(resp){
			console.log(resp);
			if(resp != '1'){
				parentDiv.remove();
			}
		}
	
	});
}



function removeActivity(sender){
	var key = sender.parents('.post-wrapper').attr('data-key');
	var postWrapper = sender.parents('.post');
	
	$.ajax({
		url:AJAX_DIR+'deleteActivity.php',
		method:'post',
		data: {key:key},
		success:function(resp){
			console.log(resp);
			postWrapper.css('-webkit-animation',"bounceOutDown 1s").css('animation',"bounceOutDown 1s");
			setTimeout( function() {
				postWrapper.remove();
			}, 500);
		}
	});
}

function removeComment(sender){
	var parentDiv = sender.parents('.post');
	var comment_block = sender.parents('.comment-block .comment-container');
	var comment = sender.parents('.comment');	
	var key = comment.attr('data-key');
	$.ajax({
		url:AJAX_DIR+'deleteComment.php',
		method:'post',
		data: {key:key},
		success:function(resp){
			console.log(resp);
			if(resp!='1'){
				parentDiv.find('.slideshow-comment-wrapper .cmt[data-key='+key+']').remove();
				comment.remove();
				var remainedCommentNum = comment_block.find('.cmt').length;
				remainedCommentNum = (remainedCommentNum >=0)?remainedCommentNum:0;
				parentDiv.find('.cmt-num').text(remainedCommentNum);
			}
			
		}
	});
}




function removeReply(sender){
	var parentDiv = sender.parents('.post');
	var comment_block = sender.parents('.comment-container');
	var reply_wrapper = sender.parents('.reply-wrapper');	
	var key = reply_wrapper.attr('data-key');
	$.ajax({
		url:AJAX_DIR+'deleteReply.php',
		method:'post',
		data: {key:key},
		success:function(resp){
			console.log(resp);
			if(resp!='1'){
				reply_wrapper.remove();
				var remainedCommentNum = comment_block.find('.cmt').length;
				remainedCommentNum = (remainedCommentNum >=0)?remainedCommentNum:0;
				parentDiv.find('.cmt-num').text(remainedCommentNum);
			}
		}
	});
}

function removePopoverReply(sender){
	var reply_wrapper = sender.parents('.reply-wrapper');	
	var key = reply_wrapper.attr('data-key');
	$.ajax({
		url:AJAX_DIR+'deleteReply.php',
		method:'post',
		data: {key:key},
		success:function(resp){
			console.log(resp);
			if(resp!='1'){
				reply_wrapper.remove();
			}
		}
	});
	return false;
}




function removeEventPhoto(sender){
	var photo_preview = sender.parents('#photo-preview');
	var current_act_image =  photo_preview.find('.preview-image-wrapper.act').find('img');
	var key  = current_act_image.attr('data-key');
	var load_key  = current_act_image.attr('data-nxt');
	load_key = (load_key == 'null')?current_act_image.attr('data-prev'):load_key;
	photo_preview.find('.popover').remove();
	if(load_key != 'null'){
		$.ajax({
			url:AJAX_DIR+'remove_evt_pht.php',
			method:'post',
			data: {key:key, load_key:load_key},
			success:function(resp){
				console.log(resp);
				if(resp != '1'){
					$('.previewable[data-key='+current_act_image.attr('data-key')+']').remove();
					//load the image to be display after deleting
					photo_preview.find('.preview-image-wrapper').remove();
					photo_preview.find('.preview-parent-wrapper').html(resp);
					var imgTarget = photo_preview.find('.preview-image-wrapper.act').find('img');
					resizePreviewImage(imgTarget);
					resetNavigable(photo_preview);
					//load cover
					var key = photo_preview.attr('data-key');
					$.ajax({
						url:AJAX_DIR+'load_evt_cover.php',
						method:'post',
						data: {key:key},
						success:function(resp){
							$('.post-wrapper[data-key='+key+']').find('.evt-cover').html(resp);
						}
					});	
				}
			}
		});
	}
}

function isElementInViewport (el) {

    //special bonus for those using jQuery
    if (typeof jQuery === "function" && el instanceof jQuery) {
        el = el[0];
    }

    var rect = el.getBoundingClientRect();

    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && /*or $(window).height() */
        rect.right <= (window.innerWidth || document.documentElement.clientWidth) /*or $(window).width() */
    );
}

function onVisibilityChange (el, callback) {
    return function () {
        /*stop the slideShow*/ console.log('visibility ' + isElementInViewport(el));
    }
}


function loadComment(thisE){
	var postWrapper = thisE.parents('.post');
	var joined_list = postWrapper.find('.joined-list');
	var favor_list = postWrapper.find('.favor-list');
	var key = postWrapper.attr('data-key');
	var commentBlock = postWrapper.find('.regular-comment-wrapper');
	if(commentBlock.hasClass('hdn')){
		//load
		$.ajax({
			url:AJAX_DIR+'load_comment_block.php',
			method:'post',
			data:{key:key},
			success:function(resp){
				favor_list.addClass('hdn');
				joined_list.addClass('hdn');
				commentBlock.find('.comment-container').html(resp);
				postWrapper.find('.cmt-num').text( commentBlock.find('.cmt').length);
				setVisibleContentWithParent(commentBlock, 'Read more')
			}
		});
	}
	commentBlock.toggleClass('hdn');
}

function loadJoinedMember(thisE){
	var parentDiv = thisE.parents('.evt-block');
	var joined_list = parentDiv.find('.joined-list');
	var favor_list = parentDiv.find('.favor-list');
	var commentBlock = parentDiv.find('.regular-comment-wrapper');
	var key = parentDiv.attr('data-key');
	$.ajax({
		url:AJAX_DIR+'load_joined_member.php',
		method:'post',
		data:{key:key},
		success:function(resp){
			if(resp != '1'){
				joined_list.find('.joined-list-inner').html(resp);
				commentBlock.addClass('hdn');
				favor_list.addClass('hdn');
				joined_list.toggleClass('hdn');
			}
		}
	});
}



function loadFavorActivityMember(thisE, always_show){
	var parentDiv = thisE.parents('.post-wrapper');
	var favor_list = parentDiv.find('.favor-list');
	var commentBlock = parentDiv.find('.regular-comment-wrapper');
	var joined_list = parentDiv.find('.joined-list');
	var key = parentDiv.attr('data-key');
	$.ajax({
		url:AJAX_DIR+'load_favor_activity_member.php',
		method:'post',
		data:{key:key},
		success:function(resp){
			if(resp != '1'){
				favor_list.find('.favor-list-inner').html(resp);
				commentBlock.addClass('hdn');
				joined_list.addClass('hdn');
				if(always_show){
					if(parseInt(parentDiv.find('.favor-num').text()) > 0){
						favor_list.removeClass('hdn');
						parentDiv.find('.favor-num').addClass('toggle-activity-favor plain-lk pointer');

					}else{
						favor_list.addClass('hdn');
						parentDiv.find('.favor-num').removeClass('toggle-activity-favor plain-lk pointer');
					}
				}else{
					favor_list.toggleClass('hdn');
				}
			}else{
				parentDiv.find('.favor-num').removeClass('toggle-activity-favor plain-lk pointer');
				favor_list.find('.favor-list-inner').html('');
				favor_list.addClass('hdn');
			}
		}
	});
}



function removeFromInterestGroup(thisE){
	var parentDiv =  thisE.parents('.in_con_border_top');
	var hash = parentDiv.attr('data-hash');
	var key  =thisE.parents('.user-profile').attr('data-key');
	$.ajax({
		url:AJAX_DIR+'remove_user_from_inetrest.php',
		method:'post',
		data:{key:key, hash:hash},
		success:function(resp){
			console.log(resp);
			parentDiv.find('.txt_ofl').removeClass('red-act');
			parentDiv.addClass('in_con_w_opt_it selectable').removeAttr('data-hash title').css('cursor','');
			thisE.remove();
		}
	});
}


function prepareAjaxSearchResult(q, show){
	$.ajax({
		url:AJAX_DIR+'search_bar_result.php',
		method:'post',
		data:{q:q},
		success:function(resp){
			if(resp != '1' ){
				var container = $('#global-search-bar #search-result-container')
				container.html(resp);
				if(show){
					container.removeClass('hdn');
				}else{
					container.addClass('hdn');
				}
			}
		}
	
	});
}
function clearAjaxSearchResult(){
	$('#global-search-bar #search-result-container').html('').addClass('hdn');
}

function hideAjaxSearchResult(){
	setTimeout(function(){
		$('#global-search-bar #search-result-container').addClass('hdn');	
	},100);
}

function showAjaxSearchResult(){
	$('#global-search-bar #search-result-container').removeClass('hdn');
}



function resetNavigable(parentDiv){
	var preview_image  =  parentDiv.find('.preview-image-wrapper.act').find('.preview-image');
	var hasNext = preview_image.attr('data-nxt');
	var hasPrev = preview_image.attr('data-prev');
	var nextIcon = parentDiv.find('.gallery-navi.right');
	var prevIcon = parentDiv.find('.gallery-navi.left');
	if(hasNext != 'null'){
		nextIcon.addClass('navigable');
	}else{
		nextIcon.removeClass('navigable');
	}

	if(hasPrev != 'null'){
		prevIcon.addClass('navigable');
	}else{
		prevIcon.removeClass('navigable');
	}
}

function closePhotoPreview(){
	$('#preview-popup-overlay').addClass('hdn');
	$('#photo-preview').addClass('hdn').find('.content-wrapper').html('');
	$('body').css('overflow','auto');
}
	

function resizePreviewImage(preview_image){
	preview_image.load(function(){
	  var height = this.naturalHeight;
	  var width = this.naturalWidth;
	  if(width > height && width/height > 1.3){
			if($(this).height() < 400){
				$(this).addClass('vertical-center');
			}
	  }else if(width == height){
	  		$(this).css({'width':'auto', 'height':'100%','max-width':'100%', 'max-height':'100%'});
	  }else{
			$(this).css({'width':'auto','max-width':'100%', 'max-height':'100%'});
	  }
	});
}


function refreshMessage(){
	$.post(AJAX_DIR+'refreshMessage.php',function(resp){
		if(resp != '1'){
			$('#chat-outer-wrapper #chat-bar .chat-body').html(resp);
		}else{
			$('#chat-outer-wrapper .broken-connect').removeClass('hdn');
		}
	});
}




function setPageTitleWithNewMessageNum(message_num){
 	var page_title = $('#page-title');
	if(page_title.attr('data-m') != message_num){
		if(message_num > 0){
			page_title.text('Message('+message_num+')');
		}else{
			var notification_num = parseInt(page_title.attr('data-n'));
			if( notification_num > 0 ){
				page_title.text('Notification('+notification_num+')');
			}else{
				if(page_title.attr('data-from') !== undefined){
					page_title.text(page_title.attr('data-from'));
				}
				else{
					page_title.text('Higout');
				}
			}
		}
		page_title.attr('data-m',message_num);
	}
}



function setPageTitleWithNewNotificationNum(notification_num){
 	var page_title = $('#page-title');
	if(page_title.attr('data-n') != notification_num){
		if(notification_num > 0){
			page_title.text('Notification('+notification_num+')');
		}else{
			var message_num = parseInt(page_title.attr('data-m'));
			if( message_num > 0 ){
				page_title.text('Message('+message_num+')');
			}else{
				if(page_title.attr('data-from') !== undefined){
					page_title.text(page_title.attr('data-from'));
				}
				else{
					page_title.text('Higout');
				}
			}
		}
		page_title.attr('data-n',notification_num);
	}

}


function loadMessageWithActiveUser(){
	var chat_box = $('#chat-outer-wrapper').find('#chat-box');
	
	if(chat_box.length > 0){
		var key = chat_box.find('.msg').attr('data-key');
		var body = chat_box.find('#chat-box-body');
		var request_url = AJAX_DIR;
		if(chat_box.hasClass('g-c')){
			request_url +='ld_gc_fresh_conversation.php';
		}else{
			request_url +='ld_fresh_conversation.php';
		}
		
		
		$.ajax({
			url:request_url,
			method:'post',
			data:{key:key},
			success:function(resp){
				if(resp.trim() != '1'){
					body.append(resp);
					body.scrollTop(1000000000);
					refreshMessage();
				}
			}
		});
	}
}

function loadIndividualConversation(thisE){
	var key = thisE.attr('data-key');
	$.ajax({
		url:AJAX_DIR+'ld_chat_box.php',
		method:'post',
		data:{key:key},
		success:function(resp){
			console.log(resp);
			refreshMessage();
			thisE.find('.n_r_s').addClass('hdn').text('0');
			$('#chat-side-content #chat-box').remove();
			$('#chat-side-content').append(resp);
			$('#chat-box #chat-box-body').scrollTop(1000000000);
		}				
	});
}


function sendPrivateMessage(thisE){
	var key = thisE.attr('data-key');
	$.ajax({
		url:AJAX_DIR+'send_private_message.php',
		method:'post',
		data:{key:key},
		success:function(resp){
			console.log(resp);
			refreshMessage();
			$('#chat-side-content #chat-box').remove();
			$('#chat-side-content').append(resp);
			$('#chat-box #chat-box-body').scrollTop(1000000000);
		}				
	});
}


	
	
function loadGroupConversation(thisE){
	var key = thisE.attr('data-key');
	$.ajax({
		url:AJAX_DIR+'ld_group_chat_box.php',
		method:'post',
		data:{key:key},
		success:function(resp){
			console.log(resp);
			refreshMessage();
			thisE.find('.n_r_s').addClass('hdn').text('0');
			$('#chat-side-content #chat-box').remove();
			$('#chat-side-content').append(resp);
			$('#chat-box #chat-box-body').scrollTop(1000000000);
		}				
	});
}

function setVisibleForBlurAction(){
	$('.side-blur-action').each(function(){
		var parentDiv = $(this).parents('.side-block-wrapper');
		if(parentDiv.find('.side-block-edit-wrapper').hasClass('hdn')){
			$(this).addClass('hdn');
		}
	})
}


function loadFavorNumberForActivity(parentDiv){
	var key = parentDiv.attr('data-key');
	$.ajax({
		url:AJAX_DIR+'activity_favor_num.php',
		method:'post',
		data:{key:key},
		success:function(resp){
			parentDiv.find('.favor-num').text(resp);
		}				
	});
}

function loadFavorNumberForComment(parentDiv){
	var key = parentDiv.attr('data-key');
	$.ajax({
		url:AJAX_DIR+'comment_favor_num.php',
		method:'post',
		data:{key:key},
		success:function(resp){
			parentDiv.find('.comment-favor-num').text(resp);
		}
	});
}


function loadFavorNumberForReply(parentDiv){
	var key = parentDiv.attr('data-key');
	$.ajax({
		url:AJAX_DIR+'reply_favor_num.php',
		method:'post',
		data:{key:key},
		success:function(resp){
			parentDiv.find('.reply-favor-num').text(resp);
		}
	});
}

function loadCommentFavorPlainList(thisE,key){
	thisE.parents('.popover-throwable').find('.popover-dialog-wrapper').remove();
	$.ajax({
		url:AJAX_DIR+'comment_favor_plain_list.php',
		method:'post',
		data:{key:key},
		success:function(resp){
			if(resp != '1'){
				showPopOverDialog(thisE,thisE.parents('.comment-container'),resp);
			}
		}	
	});
}

function loadReplyFavorPlainList(thisE,key){
	thisE.parents('.popover-throwable').find('.popover-dialog-wrapper').remove();
	$.ajax({
		url:AJAX_DIR+'reply_favor_plain_list.php',
		method:'post',
		data:{key:key},
		success:function(resp){
			if(resp != '1'){
				showPopOverDialog(thisE,thisE.parents('.comment-container'),resp);
			}
		}	
	});
}

$(function(){
	if($('#login-header').length < 1){
		setInterval(function(){
			//fetch new notification
			$.post(AJAX_DIR+'fetchNewQueueNumber.php',function(resp){
				var page_title = $('#page-title');
				if(page_title.attr('data-n') != resp){
					if(parseInt(resp) > 0){
						$('#index-noti-red-spot').text(resp).removeClass('hdn');
					}else{
						$('#index-noti-red-spot').text('0').addClass('hdn');
					}
				}
			
				setPageTitleWithNewNotificationNum(resp);
			});
	
			//fetch new message
			$.post(AJAX_DIR+'fetchNewMessageQueueNumber.php',function(resp){
				var page_title = $('#page-title');
				if(page_title.attr('data-m') != resp){
					refreshMessage();
				}
				setPageTitleWithNewMessageNum(resp);
		
			});
	
			loadMessageWithActiveUser();
		},5000);
	}
});



$(document).keyup(function(evt){
	if(evt.keyCode==27)
	{	
		resetDialog();
		$('.invitation-wrapper').addClass('hdn');
		
	}
});


$(document).click(function(){
	$('.popover').removeClass('act').addClass('hdn');
	$('.trigger-pop').addClass('hdn');
	setVisibleForBlurAction();
});


 $.fn.scrollStopped = function(callback) {
	var $this = $(this), self = this;
	$this.scroll(function(){
		if ($this.data('scrollTimeout')) {
		  clearTimeout($this.data('scrollTimeout'));
		}
		$this.data('scrollTimeout', setTimeout(callback,100,self));
	});
 };




$(document).ready(function(){
	setVisibleContent()
	$(window).scroll(function(){
		if(!$('body').hasClass('disable-hover'))
			$('body').addClass('disable-hover');
	});
	
	$(window).scrollStopped(function(){
		if($('body').hasClass('disable-hover'))
    		$('body').removeClass('disable-hover');
	});
	
	$('body').on({
 		mouseover:function(){
 			$(this).datepicker({ dateFormat: "mm-dd-yy" });
 		},
 		focus:function(){
 		 	$(this).datepicker({ dateFormat: "mm-dd-yy" });
 		}
 	},'.add-date');
 	
 	
 	$('body').on({
 		keyup:function(){
 			var q = $(this).val();
 			if(q.trim() != ''){
 				prepareAjaxSearchResult(q,true);
 			}else{
 				clearAjaxSearchResult();
 			}
 		},
		blur:function(){
  			hideAjaxSearchResult();
  		},
  		focus:function(){
 			var q = $(this).val();
 			if(q.trim() != ''){
 				showAjaxSearchResult();
 			}
 		}
 	
 	},'#global-search-bar input');
 
 	
	$('.header #search-submit-form').on("submit",function(event){
		event.preventDefault();
		var keyWord = $(this).find('input[type=text]').val();
		if(keyWord.trim() != '' ){
			window.location.href = DEFAULT_SEARCH_PATH+keyWord;
		}
	});
	
 	

	$('body').on({
		mouseover:function(){
			$('body').css('overflow','hidden');
			$(this).css('overflow','auto');
		},
		mouseleave:function(){
			$('body').css('overflow','auto');
			$(this).css('overflow','hidden');
		}
	},'.child-scrollable');



	$('#loggedin-menu').on({
		click:function(){
			$(this).parents('#loggedin-menu').find('#setting-menu').toggleClass('hdn');
			$('#notification-center').addClass('hdn');
			return false;
		}
	},'#loggin-user-icon');
	
	$('body').on({
		click:function(){
			var notification_center = $('#notification-center');
			notification_center.find('.get-prev').removeClass('red-act');
			notification_center.find('.get-fresh').addClass('red-act');
			notification_center.find('.body.previous').addClass('hdn');
			notification_center.find('.body.fresh').removeClass('hdn');
			var noti = $('#index-noti-red-spot');
			if(parseInt(noti.text()) > 0 ){
				$.get(AJAX_DIR+"ld_popover_notification.php", function(resp) {
					var freshDiv = notification_center.find('.body.child-scrollable.fresh');
					freshDiv.prepend(resp);
					freshDiv.find('.empty-feed').remove();
					noti.text('').addClass('hdn');
					freshDiv.find('.popover-child').each(function(){
						 notification_center.find('.body.previous').find('.popover-child[data-key='+$(this).attr('data-key')+']').remove();
					});
					$.post(AJAX_DIR+"update_notification_queue.php");
					
					
				});
			}
			notification_center.toggleClass('hdn');
			$('#setting-menu').addClass('hdn');
			return false;
		}
	},'#header-notification-delegate');
	
	
	
	
	
	$('body').on({
		click:function(){
			var notification_center = $('#notification-center');
			notification_center.find('.get-prev').removeClass('red-act');
			$(this).addClass('red-act');
			notification_center.find('.body.previous').addClass('hdn');
			notification_center.find('.body.fresh').removeClass('hdn');
			var noti = $('#index-noti-red-spot');	
			if(parseInt(noti.text()) > 0 ){
				$.get(AJAX_DIR+"ld_popover_notification.php", function(resp) {
					var freshDiv = notification_center.find('.body.fresh');
					freshDiv.prepend(resp);
					freshDiv.find('.empty-feed').remove();
					noti.text('').addClass('hdn');
					$.post(AJAX_DIR+"update_notification_queue.php");
				});
				notification_center.show();
			}else{
				notification_center.show();
			}			
		}
	},'#notification-center .get-fresh');
	
	
	
	
	
	$('body').on({
		click:function(){
			var notification_center = $('#notification-center');
			notification_center.find('.get-fresh').removeClass('red-act');
			$(this).addClass('red-act');
			var previous_body = notification_center.find('.body.previous');
			previous_body.removeClass('hdn');
			var freshBody = notification_center.find('.body.fresh');
			var freshBodyChild = freshBody.find('.popover-child');
			freshBody.addClass('hdn');
			if(previous_body.attr('data-set') == 'false'){
				$.get(AJAX_DIR+"ld_previous_popover_notification.php", function(resp) {
					previous_body.html(resp);
					freshBodyChild.each(function(){
						previous_body.find('.popover-child[data-key='+$(this).attr('data-key')+']').remove();
					});
				});
				previous_body.attr('data-set','true');
			}
				
			return false;
		}
	},'#notification-center .get-prev');
	
	
	
	
	
	$('body').on({
		click:function(){
			var parentDiv = $(this).parents('#notification-center');
			var red_act = parentDiv.find('.title .red-act');
			if($(this).hasClass('expanded')){
				var actualHeight;
				if(red_act.hasClass('get-fresh')){
					parentDiv.find('.body.fresh').animate({
						'max-height':'260px',
					},100);
					actualHeight = parentDiv.find('.body.fresh').innerHeight();
				}else{
					parentDiv.find('.body.previous').animate({
						'max-height':'260px',
					},100);
					actualHeight = parentDiv.find('.body.previous').innerHeight();
				}
				if(actualHeight > 260){
					$(this).text('Expand').removeClass('expanded');
				}
			}else{
				if(red_act.hasClass('get-fresh')){
					parentDiv.find('.body.fresh').animate({
						'max-height':'440px',
					},100);
					actualHeight = parentDiv.find('.body.fresh').innerHeight();
				}else{
					parentDiv.find('.body.previous').animate({
						'max-height':'440px',
					},100);
					actualHeight = parentDiv.find('.body.previous').innerHeight();
				}
				if(actualHeight > 260){
					$(this).text('Close').addClass('expanded');
				}
			}
		}
	},'#notification-center-size-toggler');
	
	
	
	
	
	$('body').on({
		click:function(){
			return false;
		}
	},'#notification-center');


	$('body').on({
		click:function(){
			window.location.href=$(this).attr('href');
			return false;
		}
	},'#notification-center a');


	
	$('body').on({
		click:function(){
			var thisE = $(this);
			var key = thisE.parents('.interest-request').attr('data-key');
			$.ajax({
				url:AJAX_DIR+'accept_interest_request.php',
				method:'post',
				data:{key:key},
				success:function(resp){
					console.log(resp);
					if(resp != '1'){
						thisE.parents('.option').find('.ignore').remove();
 						thisE.text('Accepted').removeClass('accept animate-opacity pointer plain-lk');
 					}
				}
			});
		}
	},'.interest-request .accept');
	
	$('body').on({
		click:function(){
			var thisE = $(this);
			var key = thisE.parents('.interest-request').attr('data-key');
			$.ajax({
				url:AJAX_DIR+'ignore_interest_request.php',
				method:'post',
				data:{key:key},
				success:function(resp){
					console.log(resp);
					if(resp != '1'){
						thisE.parents('.option').find('.accept').remove();
 						thisE.text('Ignored').removeClass('ignore animate-opacity pointer plain-lk');
 					}
				}
			});
		}
	},'.interest-request .ignore');
	
	
	$('body').on({
		click:function(){
			var thisE = $(this);
			var key = thisE.parents('.event-invitation-request').attr('data-key');
			$.ajax({
				url:AJAX_DIR+'accept_event_invitation_request.php',
				method:'post',
				data:{key:key},
				success:function(resp){
					console.log(resp);
					if(resp != '1'){
						thisE.parents('.option').find('.ignore').remove();
 						thisE.text('Accepted').removeClass('accept animate-opacity pointer plain-lk');
 						refreshMessage();
 					}
				}
			});
		}
	},'.event-invitation-request .accept');
	
	
	$('body').on({
		click:function(){
			var thisE = $(this);
			var key = thisE.parents('.event-invitation-request').attr('data-key');
			$.ajax({
				url:AJAX_DIR+'ignore_event_invitation_request.php',
				method:'post',
				data:{key:key},
				success:function(resp){
					console.log(resp);
					if(resp != '1'){
						thisE.parents('.option').find('.accept').remove();
 						thisE.text('Ignored').removeClass('ignore animate-opacity pointer plain-lk');
 					}
				}
			});
		}
	},'.event-invitation-request .ignore');
	
	
	
	
	
	
	
	
	
	
	
	$('body').on({
		click:function(){
			resetDialog($(this).parents('#popup-dialog'))
		}
	},'.dismiss');
	
	$('body').on({
		keypress:function(e){
 			if(e.keyCode == 10 || e.keyCode == 13){
 				e.preventDefault();
 				var txtarea = $(this);
 				var text = txtarea.val().trim();
 				var postWrapper = $(this).parents('.post');
 				var commentBlock = postWrapper.find('.comment-block');
 				var keyForPost = postWrapper.attr('data-key');
 				var keyForComment = $(this).attr('data-key');
 				if(keyForPost != keyForComment){
 					return false;	
 				}
 				if(text == ''){
 					return false;
 				}
 				$.ajax({
 					url:AJAX_DIR+'comment.php',
 					method:'post',
 					data:{keyForPost:keyForPost,keyForComment:keyForComment,text:text},
 					success:function(resp){
 						console.log(resp);
 						if(resp != '1'){
 							var comment_container = postWrapper.find('.comment-container');
 							postWrapper.find('.cmt-num').text( comment_container.find('.cmt').length + 1);
 							comment_container.prepend(resp);
 							txtarea.val('').blur();
 							setVisibleContent();
 						}
 					}
 				});
 			}
 			
 		}
	},'.comment-txt');
	
	
	
	$('body').on({
		keypress:function(e){
 			if(e.keyCode == 10 || e.keyCode == 13){
 				e.preventDefault();
 				var txtarea = $(this);
 				var text = txtarea.val().trim();
 				var postWrapper = $(this).parents('.post');
 				var commentBlock = postWrapper.find('.comment-block');
				var parent = txtarea.parents('.comment');
				var replyBlock = parent.find('.reply-block');
 				var keyForTarget = parent.attr('data-key');
 				var keyForComment = $(this).attr('data-key');
 				if(keyForTarget != keyForComment){
 					return false;	
 				}
 				if(text == ''){
 					return false;
 				}
 				$.ajax({
 					url:AJAX_DIR+'reply.php',
 					method:'post',
 					data:{keyForTarget:keyForTarget,keyForComment:keyForComment,text:text},
 					success:function(resp){
 						console.log(resp);
 						if(resp != '1'){
 							var comment_container = postWrapper.find('.comment-container');

 							postWrapper.find('.cmt-num').text( comment_container.find('.cmt').length + 1);
 							replyBlock.prepend(resp);
 							txtarea.val('').blur().addClass('hdn');
 							setVisibleContent();
 							
 							//collapse the textarea
							parent.find('.reply-comment').text('Reply');
						}
 					}
 				});
 			}
 			
 		}
	},'.comment-reply');
	
	

	
	$('body').on({
		keypress:function(e){
 			if(e.keyCode == 10 || e.keyCode == 13){
 				e.preventDefault();
 				var txtarea = $(this);
 				var text = txtarea.val().trim();
				var parent = txtarea.parents('.comment');
 				var keyForTarget = parent.attr('data-key');
 				var keyForComment = $(this).attr('data-key');
 				
 				if(keyForTarget != keyForComment){
 					return false;	
 				}
 				if(text == ''){
 					return false;
 				}
 				$.ajax({
 					url:AJAX_DIR+'reply.php',
 					method:'post',
 					data:{keyForTarget:keyForTarget,keyForComment:keyForComment,text:text},
 					success:function(resp){
 						if(resp != '1'){
 							parent.find('.reply-comment').trigger('click');
 							txtarea.val('');
 							if(parent.find('.inner .popover-throwable').length < 1){
 								parent.find('.noti-text').after('<span class="popover-throwable"><img src="'+IMGDIR+'plane_sent_icon.png" class="request-sent" style="margin-left:3px;opacity:0.4;" title="Reply Sent"></span>');
							}
						}
 					}
 				});
 			}
 			
 		}
	},'.noti-comment-reply');
	
	
	
	
	$('body').on({
		keypress:function(e){
 			if(e.keyCode == 10 || e.keyCode == 13){
 				e.preventDefault();
 				var txtarea = $(this);
 				var text = txtarea.val().trim();
 				var postWrapper = $(this).parents('.post');
 				var commentBlock = postWrapper.find('.comment-block');
				var parent = txtarea.parents('.comment');
				var replyBlock = parent.find('.reply-block');
 				var keyForTarget = parent.attr('data-key');
 				var keyForComment = $(this).attr('data-key');
 				if(keyForTarget != keyForComment){
 					return false;	
 				}
 				if(text == ''){
 					return false;
 				}
 				
 				$.ajax({
 					url:AJAX_DIR+'sub_reply.php',
 					method:'post',
 					data:{keyForTarget:keyForTarget,keyForComment:keyForComment,text:text},
 					success:function(resp){
 					console.log(resp);
 						if(resp != '1'){
 							var comment_container = postWrapper.find('.comment-container');

 							postWrapper.find('.cmt-num').text( comment_container.find('.cmt').length + 1);
 							replyBlock.prepend(resp);
 							txtarea.val('').blur().addClass('hdn');
 							setVisibleContent();
 							//collapse the textarea
							parent.find('.reply-comment').text('Reply');
						}
 					}
 				});
 			}
 			
 		}
	},'.sub-reply');
	
	
	$('body').on({
		keypress:function(e){
 			if(e.keyCode == 10 || e.keyCode == 13){
 				e.preventDefault();
 				var txtarea = $(this);
 				var text = txtarea.val().trim();
				var parent = txtarea.parents('.comment');
 				var keyForTarget = parent.attr('data-key');
 				var keyForComment = $(this).attr('data-key');
 				
 				if(keyForTarget != keyForComment){
 					return false;	
 				}
 				if(text == ''){
 					return false;
 				}
 				
 				$.ajax({
 					url:AJAX_DIR+'sub_reply.php',
 					method:'post',
 					data:{keyForTarget:keyForTarget,keyForComment:keyForComment,text:text},
 					success:function(resp){
 					console.log(resp);
 						if(resp != '1'){
 							parent.find('.reply-comment').trigger('click');
 							txtarea.val('');
 							if(parent.find('.inner .popover-throwable').length < 1){
 								parent.find('.noti-text').after('<span class="popover-throwable"><img src="'+IMGDIR+'plane_sent_icon.png" class="request-sent" style="margin-left:3px;opacity:0.4;" title="Reply Sent"></span>');
							}
						}
 					}
 				});
 			}
 			
 		}
	},'.noti-sub-reply');
	
	
	
	
	
	
	
	
	
	
	$('body').on({
		focus:function(){
			$(this).css('cursor','text');
			$(this).attr('rows','3');
		},
		blur:function(){
			var text=$(this).val();
			if(text==''){
				$(this).css('cursor','pointer');
				$(this).attr('rows','2');
			}
		}
	},'.fcy-txt');
	
	
	
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
	},'.visible-control:not(#photo-preview .visible-control)');
	
	
	$('body').on({
		click:function(evt){
			var preview = $(this).parents('#photo-preview');
			var visible_scope = $(this).parents('.visible-post-scope');
			if($(this).hasClass('rdm')){
				$(this).text('Show less');
				var currentHeight = visible_scope.height();
				visible_scope.find('.visible-content').removeClass('limit-height');
				$(this).removeClass('rdm');
				var newHeight = visible_scope.height();
				preview.find('.media-asset').animate({'height':'-='+(newHeight-currentHeight)},200);

			}else{
				$(this).text('Show detail');
				var currentHeight = visible_scope.height();
				visible_scope.find('.visible-content').addClass('limit-height');
				$(this).addClass('rdm');
				var newHeight = visible_scope.height();
				preview.find('.media-asset').animate({'height':'-='+(newHeight-currentHeight)},200);
			}
			
		}
	},'#photo-preview .visible-control');
	
	
	
	
	
	
	$('body').on({
		mouseover:function(){
			$(this).find('.toggle-operation').removeClass('hdn');
		},
		mouseleave:function(){
			var popover = $(this).find('.popover');
			if(popover.length < 1 || popover.hasClass('hdn')){
				$(this).find('.toggle-operation').addClass('hdn');	
			}
		}
	},'.post-wrapper');
	
	
	$('body').on({
		click:function(){
			$('.trigger-pop').not($(this)).addClass('hdn');
			var selfPopover = $(this).parents('.post-wrapper').find('.popover');
			$('.popover').not(selfPopover).addClass('hdn');
			selfPopover.toggleClass('hdn');
			return false;
		
		}
	},'.post-wrapper .toggle-operation');
	
	
	
	
	$('body').on({
		click:function(){
			loadComment($(this));
			
		}
	},'.post-wrapper .content .toggle-comment');
	
	
	$('body').on({
		click:function(){
			loadJoinedMember($(this));
		}
	},'.post-wrapper .content .toggle-joined');
	
	
	
	$('body').on({
		click:function(){
			presentPopupDialog("Remove Post", "Are you sure to remove this post", "Cancel", "Remove", removeActivity, $(this) );
		}
	},'.remove_activity');
	
	
	$('body').on({
		mouseover:function(){
			$(this).find('.trashcan').removeClass('hdn');
		},
		mouseleave:function(){
			$(this).find('.trashcan').addClass('hdn');
		}
	},'.inner-container');
	
	
	$('body').on({
		click:function(){
			var inner = $(this).parents('.inner').first();
			textarea = inner.find('textarea').first();
			if(textarea.hasClass('hdn')){
				$(this).text('Hide');
			}else{
				$(this).text('Reply');
			}
			textarea.toggleClass('hdn');
			return false;
		}
	},'.comment .reply-comment');
	
	
	$('body').on({
		click:function(){
			presentPopupDialog("Remove Comment", "Do you want to remove this comment", "Cancel", "Remove", removeComment, $(this) );

		}
	},'.comment .trash-comment');
	
	
	$('body').on({
		click:function(){
			presentPopupDialog("Remove Comment", "Do you want to remove this comment", "Cancel", "Remove", removeReply, $(this) );
			
		}
	},'.comment .trash-reply');
	
	$('body').on({
		click:function(){
			presentPopupDialog("Remove Comment", "Do you want to remove this comment", "Cancel", "Remove", removePopoverReply, $(this) );
			
		}
	},'.comment .noti-trash-reply');
	
	
	/*time picker*/
	
	$('body').on({
		click:function(){
			var pos = $(this).offset();
			$('.time_picker_outer_wrapper').css('top',pos.top+30).css('left',pos.left).removeClass('hdn');
			return false;		
		},
		blur:function(){
			var parObj = $(this);
			$('.time_picker_outer_wrapper').find('.time_picker_item').on('click',function(){
				var tm=$(this).text().trim();
				parObj.val(tm+" "+$('.time_picker_ampm_part .asct').text().trim());
				$('.time_picker_item').removeClass('asct');
				$(this).addClass('asct');
				return false;
			});
			$('.time_picker_outer_wrapper').find('.ampm_part_item').on('click',	function(){
				var ampm=$(this).text().trim();
				parObj.val($('.time_picker_time_part .asct').text().trim()+" "+ampm);
				$('.ampm_part_item').removeClass('asct');
				$(this).addClass('asct');
			});
			if(parObj.val().trim() != ''){
				parObj.val($('.time_picker_time_part .asct').text().trim()+' '+$('.time_picker_ampm_part .asct').text().trim());
			}
		}
		
	
	},'.evt_tm');
	
	
	$('body').on({
		click:function(){
			$('#preview-popup-overlay').addClass('hdn');
			$('#evt-preview').addClass('hdn');
			$('body').css('overflow','auto');

		}
	},'.ct .cross');
	

	
	$('body').on({
		change:function(){
			var parentDiv = $(this).parents('.media-asset');
			var content_wrapper = parentDiv.parents('.content-wrapper');
			var photo_preview = parentDiv.parents('#photo-preview');
			var key = photo_preview.attr('data-key');
			var data=new FormData();
			data.append('profile-pic',$(this)[0].files[0]);
			data.append('key',key);
			var thisE = this;
			content_wrapper.find('.popover').remove();
			photo_preview.find('.preview-image-wrapper').remove();
			photo_preview.find('.preview-parent-wrapper').html('<div class="preview-image-wrapper act"><img src="" class="preview-image"></div>');
			var imgTarget = parentDiv.find('.preview-image-wrapper.act').find('img');
			readURL(thisE,imgTarget);
			resizePreviewImage(imgTarget);
			parentDiv.find('.preview-loading-wrapper').removeClass('hdn');
			
			$.ajax({
				url:AJAX_DIR+'upload_evt_pht.php',
				type:'POST',
				processData: false,
				contentType: false,
				data:data,
				success:function(resp){
					if(resp == '1'){
						presentPopupDialog("Bad Image",BAD_IMAGE_MESSAGE, "Got it", "", null, null );
						new_evt_pht_frame.remove();
						preview_loading_wrapper.addClass('hdn');
					}else if(resp !== '2'){
						photo_preview.find('.preview-image-wrapper').remove();
						photo_preview.find('.preview-parent-wrapper').html(resp);
						var imgTarget = parentDiv.find('.preview-image-wrapper.act').find('img');
					
						resizePreviewImage(imgTarget);
						resetNavigable(photo_preview);
						parentDiv.find('.preview-loading-wrapper').addClass('hdn');
					}
					$(thisE).val('');
				}
			});
		}
	},'.upload-evt-pic');
	
	
	
	
	
	// $('body').on({
// 		click:function(){
// 			var thumb_src = $(this).find('img').attr('src');
// 			var src = thumb_src.replace('thumb_','');
// 			$(this).parents('#evt-preview').find('.pht .display-img').attr('src',src);
// 		}
// 	
// 	},'.thumb-evt-pht.thumb');
	
	// $('body').on({
// 		mouseover:function(){
// 			$(this).find('.remove-evt-pht').removeClass('hdn');
// 		},
// 		mouseleave:function(){
// 			$(this).find('.remove-evt-pht').addClass('hdn');
// 		}
// 	},'#evt-preview .thumb-evt-pht');
// 	
	
	
	$('body').on({
		click:function(){
 			removeEventPhoto($(this));
 			return false;
		}
	
	},'#photo-preview .remove-evt-pht');
	
	
	$('body').on({
		mouseover:function(){
			$(this).find('.toggle-operation').removeClass('hdn');
			return false;
		},
		mouseleave:function(){
			var popover = $(this).find('.popover');
			if(popover.length < 1 || popover.hasClass('hdn')){
				$(this).find('.toggle-operation').addClass('hdn');	
			}
			
		}
	
	},'.operation-triggeable');
	
	$('body').on({
		click:function(){
			var thisE = $(this);
			var parentDiv = thisE.parents('.operation-triggeable');
			$('.trigger-pop').not($(this)).addClass('hdn');
			if(parentDiv.find('.fri-oper').length < 1){
				var key = parentDiv.attr('data-key');
				$.ajax({
 					url:AJAX_DIR+'friend_operation.php',
 					method:'post',
 					data:{key:key},
 					success:function(resp){
 						parentDiv.append(resp);
 						var operationDiv = parentDiv.find('.fri-oper');
						$('.fri-oper.popover').not(operationDiv).addClass('hdn');
						operationDiv.removeClass('hdn');
					}
 				});
			}else{
				var operationDiv = parentDiv.find('.fri-oper');
				$('.popover').not(operationDiv).addClass('hdn');
				operationDiv.toggleClass('hdn');
			}
			return false;
			
			
		}
	},'.user-profile.operation-triggeable .toggle-operation');
	
	
	
	// $('body').on({
// 		click:function(){
// 			$(this).find('.popover').addClass('hdn');
// 			return false;
// 		}
// 	
// 	},'.hover-user-profile');
	
	$('body').on({
		click:function(){
			window.location.href = $(this).attr('href');
		}
	
	},'.user-profile a');
	
	
	
	$('body').on({
		click:function(){
			var thisE = $(this);
			var parentDiv = thisE.parents('.operation-triggeable');
			if(parentDiv.find('.in_con_opt_w').length < 1){
				var key = parentDiv.find('.preview-image-wrapper.act img').attr('data-key');
				$.ajax({
 					url:AJAX_DIR+'load_evt_pht_option.php',
 					method:'post',
 					data:{key:key},
 					success:function(resp){
 						console.log(resp);
 						parentDiv.append(resp);
 						var operationDiv = parentDiv.find('.in_con_opt_w');
						operationDiv.removeClass('hdn');
 					}
 				});
			}else{
				var operationDiv = parentDiv.find('.in_con_opt_w');
				operationDiv.toggleClass('hdn');
			}
			return false;
		}
	},'.media-asset .toggle-operation');
	
	
	
	
	
	$('body').on({
		click:function(){
			var target_name = $(this).parents('.user-profile').find('.fullname').text();
			presentPopupDialog("Remove from Interest Group", "Do you want to remove \""+target_name+"\" from this interest group", "Cancel", "Remove", removeFromInterestGroup, $(this) );
			return false;
		}
	},'.user-profile.operation-triggeable .remove-from-interest');
	
	$('body').on({
		click:function(){
			presentPopupDialog("Leave Interest Group", "Do you want to leave this interest group", "No", "Yes", leaveInterestGroup, $(this) );
			return false;
		}
	},'.user-profile.operation-triggeable .leave-interest-group');
	
	
	
	
	$('body').on({
		mouseover:function(){
			$(this).find('.profile-pic').css('transform','scale(1.06)');
		}, 
		mouseleave:function(){
			$(this).find('.profile-pic').css('transform','scale(1)');
		}
	
	},'.user-profile');
	
	
	
	
	
	$('body').on({
		click:function(){
			return false;
		}
	},'.inner-w');
	
	$('body').on({
		click:function(){
			var parentDiv = $(this).parents('.in_con_opt_w');
			parentDiv.find('.main').addClass('hdn').css('left','-100%');
			var int_group = parentDiv.find('.sub.int-group');
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
	},'.in_con_opt_w .main .add-interest-group');
	
	
	$('body').on({
		click:function(){
			var parentDiv = $(this).parents('.in_con_opt_w');
			parentDiv.find('.main').addClass('hdn').css('left','-100%');
			var c_group = parentDiv.find('.sub.connection-group');
			var w = $(this).parents('.inner-w');
			c_group.removeClass('hdn').animate({
				'right':'0px'
				},{
				duration: 200,
				complete: function() {
					parentDiv.animate({
					'height':c_group.height()
					},100);
					w.css('height',c_group.height());
				}
		  		});
		}
	},'.in_con_opt_w .main .view-connection');
	
	
	$('body').on({
		click:function(){
			var parentDiv = $(this).parents('.in_con_opt_w');
			parentDiv.find('.sub').addClass('hdn').css('right','-100%');
			var main = parentDiv.find('.main');
			var w = $(this).parents('.inner-w');
			main.removeClass('hdn').animate({
				'left':'0px'
				},{
				duration: 200,
				complete: function() {
					parentDiv.animate({
					'height':main.height()
					},100);
					w.css('height',main.height());

				}
		  		});
		}
	},'.in_con_opt_w .sub .back-to-main');
	
	$('body').on({
		click:function(){
			var parentDiv = $(this).parents('.sub');
			if($(this).css('padding-left')=='10px'){
				parentDiv.find('.selectable').css('padding-left','10px').removeClass('asct');
				$(this).addClass('asct');
				$(this).animate({"padding-left": '+=10'},100);
				if(parentDiv.find('.asct').length == 1){
					parentDiv.find('.action-button.request').removeClass('un-requestable').addClass('requestable');
				}else{
					parentDiv.find('.action-button.request').removeClass('requestable').addClass('un-requestable');
				}
			}
			else{
				$(this).removeClass('asct');
				$(this).animate({"padding-left": '10px'},100);	
				if(parentDiv.find('.asct').length == 1){
					parentDiv.find('.action-button.request').removeClass('un-requestable').addClass('requestable');
				}else{
					parentDiv.find('.action-button.request').removeClass('requestable').addClass('un-requestable');
				}
			}
		}
	},'.in_con_opt_w .sub .selectable');

	
	
	$('body').on({
		click:function(){
			var parentDiv = $(this).parents('.user-profile.operation-triggeable');
			var keyForTarget = parentDiv.attr('data-key');
			var selected_interest = parentDiv.find('.selectable.asct');
			if(selected_interest.length == 1){
				$(this).removeClass('requestable').addClass('un-requestable');
				var keyForInterest = selected_interest.attr('data-key');
				$.ajax({
 					url:AJAX_DIR+'interest_request.php',
 					method:'post',
 					data:{keyForTarget:keyForTarget,keyForInterest:keyForInterest},
 					success:function(resp){
 					console.log(resp);
 						if(resp != '1'){	
 							selected_interest.removeClass('in_con_w_opt_it asct selectable').animate({"padding-left": '6px'},100).css('cursor','default').attr('title','Request Sent');
							selected_interest.find('.txt_ofl').after('<img src="'+IMGDIR+'plane_sent_icon.png" class="request-sent" title="Request Sent" >');
						}
 					}
 				});
				
			}
		}
	},'.in_con_opt_w .sub .action-button.request');

	
	$('body').on({
		click:function(){
			var thumb_src =  $(this).attr('src');
			var src = thumb_src.replace('thumb_','');
			var key = $(this).attr('data-key');
			var from  =$(this).attr('data-sourcefrom');
			var postKey = $(this).parents('.evt').attr('data-key');
			$.ajax({
				url:AJAX_DIR+'ld_preview_photo.php',
				method:'post',
				data:{key:key,from:from},
				success:function(resp){
					$('body').css('overflow','hidden');
					var photo_preview = $('#photo-preview');
					photo_preview.attr('data-key',postKey);
					var content_wrapper = photo_preview.find('.content-wrapper');
					content_wrapper.html(resp);
					var preview_image = photo_preview.find('.preview-image');
					preview_image.attr({'src':src,'data-key':key}).removeClass('hdn');
					preview_image.parents('.preview-image-wrapper').addClass('act');
					resizePreviewImage(preview_image); 
					$('#preview-popup-overlay').removeClass('hdn');
					photo_preview.removeClass('hdn');
					setVisibleContentWithParent(photo_preview,'Show detail');
					if(from == 'e'){
						resetNavigable(content_wrapper);
					}
				}
			})
		}
	
	},'.previewable');
	
	
	


	$('body').on({
		click:function(){
			var parentDiv = $(this).parents('.media-asset');
			var currentAct =  parentDiv.find('.preview-image-wrapper.act');
			var directNext = true;
			var needToLoad = true;
			var key;
			parentDiv.find('.popover').remove();

			if($(this).hasClass('right')){
				key  = currentAct.find('.preview-image').attr('data-nxt');
				var nextElement = currentAct.next();
				if(nextElement.hasClass('preview-image-wrapper')){
					parentDiv.find('.preview-image-wrapper').not(nextElement).removeClass('act').addClass('hdn');
					nextElement.addClass('act').removeClass('hdn');
					needToLoad =  false;
				}
				
			}else if($(this).hasClass('left')){
				key  = currentAct.find('.preview-image').attr('data-prev');
				var prevElement =  currentAct.prev();
				if(prevElement.hasClass('preview-image-wrapper')){
					parentDiv.find('.preview-image-wrapper').not(prevElement).removeClass('act').addClass('hdn');
					prevElement.addClass('act').removeClass('hdn');
					needToLoad =  false;
				}
				directNext = false;
			}else{
				return false;
			}
			
		
			if(key != 'null' && needToLoad){
				//load the image
				$.ajax({
					url:AJAX_DIR+'ld_navi_evt_photo.php',
					method:'post',
					data:{key:key},
					success:function(resp){
						currentAct.removeClass('act').addClass('hdn');
						if(directNext){
							currentAct.after(resp);
						}else{
							currentAct.before(resp);
						}
						
						var preview_image =  parentDiv.find('.preview-image-wrapper.act .preview-image')
						resizePreviewImage(preview_image);
						resetNavigable(parentDiv);
					}				
				});
			}else{
				resetNavigable(parentDiv);
			}
		}
	},'.gallery-navi.navigable');

	
	

	$('body').on({
		click:function(){
			closePhotoPreview();
		}
	},'#photo-preview');
	
	$('body').on({
		click:function(){
			return false;
		}
	
	},'#photo-preview .navigate .gallery-navi');
	
	$('body').on({
		click:function(){
			closePhotoPreview();
		},
		mouseover:function(){
			return false;
		}
	},'#photo-preview .navigate')
	
	
	
	
	
	
	$('body').on({
		click:function(){
			$(this).parents('.content-wrapper').find('.popover').remove();
			return false;
		}
	},'#photo-preview .content-wrapper .media-asset');
	
	
	$('body').on({
		click:function(evt){
			evt.stopPropagation();
		}
	},'#photo-preview .content-wrapper .media-asset label');
	
	
	
	
	$('body').on({
		click:function(){
			window.location.href=$(this).attr('href');
		}
	},'#photo-preview .content-wrapper a');
	
	 $('body').on({
		click:function(){
			$(this).parents('.content-wrapper').find('.popover').remove();
			return false;
		}
	},'#photo-preview .content-wrapper .media-detail')
	
	
	
	$('body').on({
		click:function(){
			loadIndividualConversation($(this));
		}
	},'#chat-bar .individual-contact');
	
	$('body').on({
		click:function(){
			$(this).parents('.in_con_opt_w').addClass('hdn');
			loadIndividualConversation($(this).parents('.user-profile'));
		}
	},'.user-profile .individual-contact');
	
	$('body').on({
		click:function(){
			$(this).parents('.hover-avator-wrapper').html('').addClass('hdn');
			sendPrivateMessage($(this).parents('.user-profile'));
		}
	},'.hover-avator-wrapper .user-profile .individual-contact');
	
	
	
	$('body').on({
		click:function(){
			location.reload();
		}	
	},'#chat-bar .refresh');
	
	
	
	$('body').on({
		click:function(){
			loadGroupConversation($(this));
		}
	},'.group-contact');
	
	
	
	$('body').on({
		click:function(){
			var thisE = $(this);
			var parentDiv = thisE.parents('#chat-bar');
			var key = $(this).attr('data-key');
			var footer = parentDiv.find('.chat-footer')
			var request_url = AJAX_DIR;
			var isIndividual = true;
			if($(this).hasClass('individual-contact')){
				request_url+='add_individual_to_contact_list.php';
			}else{
				request_url+='add_group_to_contact_list.php';
				isIndividual = false;
			}
			
			$.ajax({
				url:request_url,
				method:'post',
				data:{key:key},
				success:function(resp){
					if(resp != '1'){
						if(isIndividual){
							loadIndividualConversation(thisE);
						}else{
							loadGroupConversation(thisE);
						}
						parentDiv.find('.search-body').addClass('hdn');
						parentDiv.find('.chat-body').removeClass('hdn');
						footer.animate({
						'bottom':'-40'
						},100);
						footer.find('input').val('');
					}
				}				
			});
		}
		
	
	},'.search-body .contact');
	
	
	
	
	$('body').on({
		keypress:function(e){
 			if(e.keyCode == 10 || e.keyCode == 13){
 				e.preventDefault();
 				
 				var txtarea = $(this);
 				var chat_bar = txtarea.parents('#chat-outer-wrapper').find('#chat-bar');
 				var body = txtarea.parents('#chat-box').find('#chat-box-body');
				var text = txtarea.val();
 				if(text.trim() == ''){
 					return false;
 				}
 				var key = txtarea.attr('data-key');
 				$.ajax({
 					url:AJAX_DIR+'message.php',
 					method:'post',
 					data:{key:key, text:text},
 					success:function(resp){
 						if(resp != '1'){
 							txtarea.val('');
 							body.append(resp);
 							body.scrollTop(1000000000);
 							refreshMessage();
 						}
 					}
 				});
 			}
 			
 		}
	},'#chat-box.i-c .msg');
	
	
	
		
	$('body').on({
		keypress:function(e){
 			if(e.keyCode == 10 || e.keyCode == 13){
 				e.preventDefault();
 				
 				var txtarea = $(this);
 				var chat_bar = txtarea.parents('#chat-outer-wrapper').find('#chat-bar');
 				var body = txtarea.parents('#chat-box').find('#chat-box-body');
				var text = txtarea.val();
 				if(text.trim() == ''){
 					return false;
 				}
 				var key = txtarea.attr('data-key');
 				$.ajax({
 					url:AJAX_DIR+'message_group.php',
 					method:'post',
 					data:{key:key, text:text},
 					success:function(resp){
 						console.log(resp);
 						if(resp != '1'){
 							txtarea.val('').blur();
 							body.append(resp);
 							body.scrollTop(1000000000);
 							refreshMessage();
 						}
 					}
 				});
 			}
 			
 		}
	},'#chat-box.g-c .msg');
	
	
	
	
	$('body').on({
		click:function(){
			$(this).parents('#chat-box').remove();
		}
	},'#chat-box #close-chat-box');
	
	$('body').on({
		click:function(){
			var chat_box = $(this).parents('#chat-box');
			var	request_url = AJAX_DIR;
			var side_target;
			var key = chat_box.find('.msg').attr('data-key');
			if(chat_box.hasClass('i-c')){
				request_url += 'rm_list_msg.php';
				side_target = chat_box.parents('#chat-outer-wrapper').find('#chat-bar .individual-contact[data-key='+key+']');
			}else{
				request_url += 'rm_group_list_msg.php'; 
				side_target = chat_box.parents('#chat-outer-wrapper').find('#chat-bar .group-contact[data-key='+key+']');
			}
			$.ajax({
				url:request_url,
				method:'post',
				data:{key:key},
				success:function(resp){
					console.log(resp);
					if(resp != '1'){
						chat_box.remove();
						side_target.remove();
					}
				}
 			});
			
		}
	
	},'#chat-box .remove-chatting-list');	
	
	
	$('body').on({
		click:function(){
			var chat_box = $(this).parents('#chat-box');
			var key = chat_box.find('.msg').attr('data-key');
			$.ajax({
				url:AJAX_DIR+'load_event_group_joined_member.php',
				method:'post',
				data:{key:key},
				success:function(resp){
					console.log(resp);
					chat_box.find('#main-conversation').addClass('hdn');
					chat_box.find('#side-member').html(resp).removeClass('hdn');
				}
 			});
			
		}
	
	},'#chat-box .joined-member');	
	
	$('body').on({
		click:function(){
			var chat_box = $(this).parents('#chat-box');
			var key = chat_box.find('.msg').attr('data-key');
			$.ajax({
				url:AJAX_DIR+'load_event_group_event_info.php',
				method:'post',
				data:{key:key},
				success:function(resp){
					console.log(resp);
					chat_box.find('#main-conversation, #side-member').addClass('hdn');
					var event_info = chat_box.find('#side-event-info');
					event_info.html(resp).removeClass('hdn');
					setVisibleContentWithParent(event_info, 'Read more');
				}
 			});
			
		}
	
	},'#chat-box .event-info');	
	
	
	
	$('body').on({
		click:function(){
			var chat_box = $(this).parents('#chat-box');
			chat_box.find('#side-member').html('').addClass('hdn');
			chat_box.find('#main-conversation').removeClass('hdn');
		}
	},'.chat-header .back-to-main');
	
	
	
	
	
	
	$('body').on({
		click:function(){
			var selfPopover = $(this).parents('#chat-box').find('.in_con_opt_w');
			selfPopover.toggleClass('hdn');
			return false;
		}
	},'#chat-box .toggle-option');
	
	
	$('body').on({
		click:function(){
			var parentDiv = $(this).parents('#chat-bar');
			var footer = parentDiv.find('.chat-footer')
			if(footer.css('bottom') != '-4px'){
				footer.animate({
				'bottom':'-4'
				},100);
				footer.find('input').focus();
			}
		}
	
	},'#chat-side-content #chat-search');
	
	
	$('body').on({
		keyup:function(){
 			var q = $(this).val();
 			var parentDiv = $(this).parents('#chat-outer-wrapper');
 			var search_body = parentDiv.find('.search-body');
 			
 			var main_body = parentDiv.find('.chat-body');
 			if(q.trim() != ''){
 				$.ajax({
 					url:AJAX_DIR+'searchContact.php',
 					method:'post',
 					data:{q:q},
 					success:function(resp){
 						main_body.addClass('hdn');
 						search_body.html(resp).removeClass('hdn');
 					}
 				});
 			}else{
 				main_body.removeClass('hdn');
 				search_body.html('').addClass('hdn');
 			}
 		},
 		blur:function(){
 			var q = $(this).val();
 			if(q.trim() == ''){
 				var parentDiv = $(this).parents('#chat-bar');
				var footer = parentDiv.find('.chat-footer')
				footer.animate({
				'bottom':'-40'
				},100);
 			}
 		}
	},'#chat-outer-wrapper #search-contact');
	
	
	$('body').on({
		click:function(){
			var thisE = $(this);
			var parentDiv = thisE.parents('.evt-block');
			var key =parentDiv.attr('data-key');
			if(key.trim() == ''){
				return false;
			}
			var joined_num_div = parentDiv.find('.joined-num');
			var joined_label = parentDiv.find('.joined-label');
			$.ajax({
				url:AJAX_DIR+'joinEvent.php',
				method:'post',
				data:{key:key},
				success:function(resp){
					console.log(resp);
					var joined_num = joined_num_div.first().text();
					joined_num_div.text(++joined_num);
					joined_label.text('people going');
					thisE.removeClass('evt-join').addClass('evt-joined').text('Joined');
					loadJoinedMember(thisE);
					refreshMessage();
				}
 			});
		}
	
	},'.evt-block .evt-join');
	
	
	$('body').on({
		click:function(){
			var thisE = $(this);
			var parentDiv = thisE.parents('.evt-block');
			var key = parentDiv.attr('data-key');
			var joined_num_div = parentDiv.find('.joined-num');
			var joined_label = parentDiv.find('.joined-label');
			$.ajax({
				url:AJAX_DIR+'unjoinEvent.php',
				method:'post',
				data:{key:key},
				success:function(resp){
					console.log(resp);
					
					var joined_num = joined_num_div.first().text();
					joined_num = (--joined_num > 1)?joined_num:1;
					joined_num_div.text(joined_num);
					if(joined_num < 2){
						joined_label.text('person going');
					}
					thisE.removeClass('evt-joined').addClass('evt-join').text('Join');
					loadJoinedMember(thisE);
					refreshMessage();
				}
			});
		},
		mouseover:function(){
			$(this).text('Unjoin');
		},
		mouseleave:function(){
			$(this).text('Joined');
		}
	
	},'.evt-block .evt-joined');
	
	
	
	$('body').on({
		click:function(){
			var parentDiv = $(this).parents('.post-wrapper');
			var key = parentDiv.attr('data-key');
			var thisE = $(this);
			$.ajax({
				url:AJAX_DIR+'favor_activity.php',
				method:'post',
				data:{key:key},
				success:function(resp){
					console.log(resp);
					thisE.removeClass('favor-activity').addClass('undo-favor-activity').attr('title','Undo Favor');
					loadFavorNumberForActivity(parentDiv);
					loadFavorActivityMember(thisE, true);
					
				}
			});
		}
	
	},'.post-wrapper .favor-activity');
	
	$('body').on({
		click:function(){
			var parentDiv = $(this).parents('.post-wrapper');
			var key = parentDiv.attr('data-key');
			var thisE = $(this);
			$.ajax({
				url:AJAX_DIR+'undo_favor_activity.php',
				method:'post',
				data:{key:key},
				success:function(resp){
					console.log(resp);
					thisE.removeClass('undo-favor-activity').addClass('favor-activity').attr('title','Favor');
					loadFavorNumberForActivity(parentDiv);
					loadFavorActivityMember(thisE, true);
				}	
			});
		}
	
	},'.post-wrapper .undo-favor-activity')
	
	
	
	
	
	$('body').on({
		click:function(){
			loadFavorActivityMember($(this), false);
		}
	
	},'.post-wrapper .toggle-activity-favor');
});

function getCurrentHoveringOffset(){
	 var curElement = $('.hover-user-profile:hover');
	 if(curElement.length > 0){
	 	var offset = curElement.offset();
	 	var width = curElement.width();
	 	var height = curElement.height();
	 	return {offset:offset, width:width, height:height};
	 }
	 return false;
}

$(function($) {
	var initMouse ={x:-1,y:-1};
	var vpMouse ={x:-1,y:-1};
	var preDiv = {w:0,h:0};
	var mouseMove = {x:-1,y:-1};
    $('body').delegate('.hover-user-profile','mouseover mouseleave', function(event) {
    	if(event.type === 'mouseover' ){
    		var key = $(this).attr('data-key');
    		tOut = setTimeout(function(){
    		initMouse.x = event.pageX;
    		initMouse.y = event.pageY;
    		
    		vpMouse.x = event.clientX;
    		vpMouse.y = event.clientY;
    		
    		var window_w = $(window).width();
    		var window_h = $(window).height();
    		$('.hover-avator-wrapper').html('');
    		
    		$.ajax({
        		url:AJAX_DIR+'load_user_hover_avator.php',
        		method:'post',
        		data:{key:key},
        		success:function(data){
        			if(data != '1'){
        				$('.hover-avator-wrapper').html(data).removeClass('hdn');
						preDiv.h =  $('.hover-avator-wrapper').height();
						preDiv.w =  $('.hover-avator-wrapper').width();
						var hover_elememnt_properties = getCurrentHoveringOffset();
						if(hover_elememnt_properties != false){
							var newTop;
							if( preDiv.h + vpMouse.y > window_h){
								newTop = hover_elememnt_properties.offset.top - preDiv.h + 10;
								$('.hover-avator-wrapper').css('top',newTop).show();
							}else{
								newTop = hover_elememnt_properties.offset.top + hover_elememnt_properties.height + 10;
								$('.hover-avator-wrapper').css('top',newTop).show();
							}
							var newLeft;
							if( preDiv.w + vpMouse.x > window_w){
								newLeft = hover_elememnt_properties.offset.left - (preDiv.w - hover_elememnt_properties.width) + 20;
								$('.hover-avator-wrapper').css('left',newLeft);
							}else{
								newLeft = hover_elememnt_properties.offset.left;
								$('.hover-avator-wrapper').css('left', newLeft);
							}
						}
					}
        		}
        	
        	});
			},'1000');
		}
    	else if(event.type === 'mouseleave'){
    		clearTimeout(tOut);
    	}
	});
	
	
	
	$(document).mousemove(function(event){
		var hoverAvator = $('.hover-avator-wrapper');
		if(hoverAvator.length > 0 && !hoverAvator.hasClass('hdn')){
	    	mouseMove.x = event.pageX;
	    	mouseMove.y = event.pageY;
	    	var hoverDivOffSet = hoverAvator.offset();
	    	var overRange = false;
	    	if(mouseMove.y > hoverDivOffSet.top + hoverAvator.height() + 60){
	    		overRange = true;
	    	}else if(mouseMove.x > hoverDivOffSet.left + hoverAvator.width() + 60){
	    		overRange = true;
	    	}
	    	if((initMouse.x-mouseMove.x)>30 || (initMouse.y-mouseMove.y)>30 || overRange  ){	
				if(!($('.hover-avator-wrapper:hover').length != 0)){
					$('.hover-avator-wrapper').html('').addClass('hdn');
				}
			}
     	}
    });
    
    
    
    $('body').on({
    	mouseleave:function(){
			 $(this).html('').addClass('hdn');
		}
	},'.hover-avator-wrapper');
	
	
	
	$('body').delegate('.comment-thumb','mouseover mouseleave', function(event) {
		var thisE = $(event.target);
		if(event.type === 'mouseover' ){
    		var key = thisE.parents('.comment').attr('data-key');
    		tOut = setTimeout(function(){
    			loadCommentFavorPlainList(thisE, key);
    		},'500');
		}
    	else if(event.type === 'mouseleave'){
    		clearTimeout(tOut);
    		thisE.parents('.popover-throwable').find('.popover-dialog-wrapper').remove();
    	}
	});
	
	
	$('body').delegate('.reply-thumb','mouseover mouseleave', function(event) {
		var thisE = $(event.target);
		if(event.type === 'mouseover' ){
    		var key = thisE.parents('.comment').attr('data-key');
    		tOut = setTimeout(function(){
    			loadReplyFavorPlainList(thisE, key);
    		},'500');
		}
    	else if(event.type === 'mouseleave'){
    		clearTimeout(tOut);
    		thisE.parents('.popover-throwable').find('.popover-dialog-wrapper').remove();
    	}
	});
	
	
	$('body').on({
		click:function(){
			var thisE = $(this);
			var parentDiv = thisE.parents('.comment');
			var key = parentDiv.attr('data-key');
			$.ajax({
				url:AJAX_DIR+'favor_comment.php',
				method:'post',
				data:{key:key},
				success:function(resp){
					console.log(resp);
					thisE.addClass('undo-favor-comment').removeClass('favor-comment');
					loadFavorNumberForComment(parentDiv);
					loadCommentFavorPlainList(thisE, key);
					return false;
				}	
			});
		}
	},'.favor-comment');
	
	
	$('body').on({
		click:function(){
			var thisE = $(this);
			var parentDiv = thisE.parents('.comment');
			var key = parentDiv.attr('data-key');
			$.ajax({
				url:AJAX_DIR+'undo_favor_comment.php',
				method:'post',
				data:{key:key},
				success:function(resp){
					console.log(resp);
					thisE.removeClass('undo-favor-comment').addClass('favor-comment');
					loadFavorNumberForComment(parentDiv);
					loadCommentFavorPlainList(thisE, key);
					return false;
				}	
			});
		}
	},'.undo-favor-comment');
	
	
	
	$('body').on({
		click:function(){
			var thisE = $(this);
			var parentDiv = thisE.parents('.comment');
			var key = parentDiv.attr('data-key');
			$.ajax({
				url:AJAX_DIR+'favor_reply.php',
				method:'post',
				data:{key:key},
				success:function(resp){
					console.log(resp);
					thisE.addClass('undo-favor-reply').removeClass('favor-reply');
					loadFavorNumberForReply(parentDiv);
					loadReplyFavorPlainList(thisE, key);
					return false;
				}	
			});
		}
	},'.favor-reply');
	
	$('body').on({
		click:function(){
			var thisE = $(this);
			var parentDiv = thisE.parents('.comment');
			var key = parentDiv.attr('data-key');
			$.ajax({
				url:AJAX_DIR+'undo_favor_reply.php',
				method:'post',
				data:{key:key},
				success:function(resp){
					console.log(resp);
					thisE.removeClass('undo-favor-reply').addClass('favor-reply');
					loadFavorNumberForReply(parentDiv);
					loadReplyFavorPlainList(thisE, key);
					return false;
				}	
			});
		}
	},'.undo-favor-reply');
	
	
	 $('body').on({
		click:function(){
			var key = $(this).parents('.evt-block').attr('data-key');
			$.ajax({
				url:AJAX_DIR+'load_event_invitation_block.php',
				method:'post',
				data:{key:key},
				success:function(resp){
					console.log(resp);
					$('#dialog-popup-overlay').removeClass('hdn');
					$('#evt-invitation-wrapper').html(resp).removeClass('hdn').attr('data-key',key);
				}	
			});
		}
	},'.invite-friend');
	
	
	
	$('body').on({
		click:function(){
			var thisE = $(this);
			var key = thisE.attr('data-key');
			var parentDiv = $(this).parents('.invitation-wrapper');
			var post_key = parentDiv.attr('data-key');
			var url;
			if(parentDiv.is('#evt-invitation-wrapper')){
				url = 'invitation_load_friend.php';
			}else if(parentDiv.is('#evt-include-friend-wrapper')){
				url = 'include_load_friend.php';
			}
			$.ajax({
				url:AJAX_DIR + url,
				method:'post',
				data:{key:key, post_key:post_key},
				success:function(resp){
					if(resp != '1'){
						parentDiv.find('.group-label').removeClass('active');
						thisE.css('-webkit-animation',"rubberBand 0.4s").css('animation',"rubberBand 0.4s").addClass('active');;
						parentDiv.find('.interest-side-label .txt_ofl').removeClass('red-act');
						thisE.find('.txt_ofl').addClass('red-act');
						var contact = parentDiv.find('.right-content .contact');
						contact.find('.contact-inner').html(resp).removeClass('hdn');
						contact.find('.suggest-contact-inner').addClass('hdn');
						parentDiv.find('#invitation-search-wrapepr input').val('');
						setTimeout(function(){
							thisE.css('-webkit-animation',"").css('animation',"");
						},200);	
					}
				}	
			});
		}
	},'.invitation-wrapper .interest-side-label');
	
	
	
	$('body').on({
		click:function(){
			var thisE = $(this);
			var parentDiv = $(this).parents('.invitation-wrapper');
			var post_key = parentDiv.attr('data-key');
			var url;
			if(parentDiv.is('#evt-invitation-wrapper')){
				url = 'invitation_load_all_friend.php';
			}else if(parentDiv.is('#evt-include-friend-wrapper')){
				url = 'include_load_all_friend.php';
			}
			$.ajax({
				url:AJAX_DIR+url,
				method:'post',
				data:{post_key:post_key},
				success:function(resp){
					if(resp != '1'){
						parentDiv.find('.group-label').removeClass('active');
						thisE.css('-webkit-animation',"rubberBand 0.4s").css('animation',"rubberBand 0.4s").addClass('active');
						parentDiv.find('.interest-side-label .txt_ofl').removeClass('red-act');
						var contact = parentDiv.find('.right-content .contact');
						contact.find('.contact-inner').html(resp).removeClass('hdn');
						contact.find('.suggest-contact-inner').addClass('hdn');
						parentDiv.find('#invitation-search-wrapepr input').val('');
						setTimeout(function(){
							thisE.css('-webkit-animation',"").css('animation',"");
						},200);	
					}
				}	
			});
		}
	},'.invitation-wrapper .all-friend');
	
	$('body').on({
		click:function(){
			$('.invitation-wrapper').addClass('hdn');
		}
	},'.invitation-wrapper .dismiss');
	
	
	$('body').on({
		keyup:function(){
			var q = $(this).val();
			var parentDiv = $(this).parents('.invitation-wrapper');
			var key = parentDiv.find('.group-label.active').attr('data-key');
			var parentContact = parentDiv.find('.right-content .contact');
			if(q.trim() != ''){
				var pkey = parentDiv.attr('data-key');
				$.ajax({
					url:AJAX_DIR+'invitation_friend_search.php',
					method:'post',
					data:{q:q, key:key, pkey:pkey},
					success:function(resp){
						if(resp != '1'){
							parentContact.find('.contact-inner').addClass('hdn');
							parentContact.find('.suggest-contact-inner').html(resp).removeClass('hdn');
						}
					}	
				});
			}else{
				parentContact.find('.suggest-contact-inner').html('').addClass('hdn');
				parentContact.find('.contact-inner').removeClass('hdn');
			}
		},
	},'.invitation-wrapper #invitation-search-wrapepr input');
	
	$('body').on({
		click:function(){
			var target_image_url = $(this).find('.contact-pic').attr('src');
			var key =  $(this).attr('data-key');
			var name = $(this).find('.name').text().trim();
			var parentDiv = $(this).parents('.invitation-wrapper');
			var icon_to_add = parentDiv.find('.selected-icon-avator[data-key='+key+']');
			if(icon_to_add.length < 1){
			 	parentDiv.find('#selected-preview-wrapper').prepend('<img src="'+target_image_url+'" class="selected-icon-avator" data-key="'+key+'" data-name="'+name+'">');
			}else{
				icon_to_add.remove();
			}
			var remaining_target_count = $('#selected-bar .selected-icon-avator').length;
			parentDiv.find('#invitation-selected-num').text(remaining_target_count);
			var selected_bar = parentDiv.find('#selected-bar');
			selected_bar.find('#selected-preview-wrapper').removeClass('hdn');
			var inner_option_wrapper = parentDiv.find('.right-content .inner-option-wrapper');
			var inviteButton = inner_option_wrapper.find('.action-button');
			if(remaining_target_count > 0){
				inviteButton.removeClass('un-requestable').addClass('requestable');
				if(remaining_target_count > 2){
					$('#bar-icon-wrapper').css('width','300px');
				}else{
					 $('#bar-icon-wrapper').css('width','inherit');
				}
			}else{
				inviteButton.addClass('un-requestable').removeClass('requestable');
			}
			
			inner_option_wrapper.find('.after-action').addClass('hdn');
			inner_option_wrapper.find('.before-action').removeClass('hdn');
		}
	},'.invitation-wrapper .invitation-contact.selectable');
	
	$('body').on({
		click:function(){
			var parentDiv = $(this).parents('.invitation-wrapper');
			$(this).find('#invitation-selected-bar-wrapper').addClass('red-act');
			$(this).find('#invitation-selected-num').addClass('red-act');
			$('#invitation-invited-bar-wrapper').removeClass('red-act');
			$('#invitation-invited-bar-wrapper').find('#invitation-invited-num').removeClass('red-act');
		
			var selected_icon = $('#selected-bar .selected-icon-avator');
			var selected_detail = parentDiv.find('#selected-detail');
			var sub_inner = parentDiv.find('#selected-detail .sub-inner');
			parentDiv.find('#invited-detail').addClass('hdn');
			if(selected_icon.length < 1){
				sub_inner.html('<div style="margin:10px;">The selected list is empty</div>');
				selected_detail.toggleClass('hdn');
			}else{
				sub_inner.html('');
				selected_icon.each(function(){
				var name = $(this).attr('data-name');
				var key = $(this).attr('data-key');
				var url = $(this).attr('src');
				sub_inner.append('<div class="in_con_w_opt_it selected-list popover-list" style="padding: 10px 10px;cursor:default;" data-key="'+key+'">'+
								'<img class="label-image" src="'+url+'">'+
								'<div class="inline-blk txt_ofl name" style="width: 104px;margin-top:3px;font-size:13px;">'+name+'</div>'+
								'<img class="remove-from-selected pointer animate-opacity remove-from hdn" src="'+IMGDIR+'minus_icon.png" title="Remove from selected list" height="16" wdith="16" style="float:right;margin-top:4px;">'+

				'</div>');
				});
				
				selected_detail.toggleClass('hdn');
			}
			return false;
		}
	},'.invitation-wrapper #selected-bar #toggle-selected-detail');
	
	
	
	$('body').on({
		click:function(){
			$('#evt-invitation-wrapper #selected-detail').addClass('hdn');
			$(this).addClass('red-act');
			$(this).find('#invitation-invited-num').addClass('red-act');
			$('#invitation-selected-bar-wrapper').removeClass('red-act');
			$('#invitation-selected-bar-wrapper').find('#invitation-selected-num').removeClass('red-act');
			var parentDiv = $(this).parents('#evt-invitation-wrapper');
			var key = parentDiv.attr('data-key');
			var detail = $('#evt-invitation-wrapper #invited-detail');
			
			if(parseInt($('#invitation-invited-num').attr('data-num')) > 0 ){
				if(detail.hasClass('hdn')){
					$.ajax({
						url:AJAX_DIR+'load_event_invited_list.php',
						method:'post',
						data:{key:key},
						success:function(resp){
							if(resp != '1'){
								detail.removeClass('hdn');
								detail.find('.sub-inner').html(resp);
							}
						}			
					});
				}else{
					detail.addClass('hdn');
				}
			}else{
				detail.find('.sub-inner').html('<div style="margin:10px;">The invited list is empty</div>');
				detail.toggleClass('hdn');
			}
			return false;
	
		}
	},'#evt-invitation-wrapper #selected-bar #invitation-invited-bar-wrapper');
	
	
	
	
	
	$('body').on({
		mouseover:function(){
			$(this).find('.remove-from').removeClass('hdn');
		},
		mouseleave:function(){
			$(this).find('.remove-from').addClass('hdn');
		}
	},'.invitation-wrapper .popover-list');
	
	
	$('body').on({
		click:function(){
			var parentDiv = $(this).parents('.invitation-wrapper');
			var selected_list = $(this).parents('.selected-list');
			var key = selected_list.attr('data-key');
			parentDiv.find('#selected-bar .selected-icon-avator[data-key='+key+']').remove();
			selected_list.remove();
			var remaining_target_count = $('#selected-bar .selected-icon-avator').length;
			parentDiv.find('#invitation-selected-num').text(remaining_target_count);
			var seletced_bar = parentDiv.find('#selected-bar');
			if(remaining_target_count > 0){
				seletced_bar.addClass('pointer');
				parentDiv.find('.action-button').removeClass('un-requestable').addClass('requestable');
			}else{
				seletced_bar.removeClass('pointer');
				parentDiv.find('.action-button').addClass('un-requestable').removeClass('requestable');
				$('#selected-detail').addClass('hdn');
			}
		}
	},'.invitation-wrapper #selected-detail .remove-from-selected');
	
	$('body').on({
		click:function(){
			var invited_list = $(this).parents('.invited-list');
			var key = invited_list.attr('data-key');
			var key_for = invited_list.find('.label-image').attr('data-key');
			var thisE = $(this);
			var detail = $('#evt-invitation-wrapper #invited-detail');
			$.ajax({
				url:AJAX_DIR+'delete_event_invitation.php',
				method:'post',
				data:{key:key},
				success:function(resp){
					invited_list.remove();
					var remaining_target_count = detail.find('.invited-list').length;
					$('#evt-invitation-wrapper #invitation-invited-num').text(remaining_target_count).attr('data-num',remaining_target_count);
					if(remaining_target_count == 0){
						detail.find('.sub-inner').html('<div style="margin:10px;">The invited list is empty</div>');
					}
					var contact = $('#evt-invitation-wrapper .contact-inner .list-item.invitation-contact[data-key='+key_for+']');
					contact.find('.invitation-sent-wrapper').animate({
						'margin-right':'-100px'
					},100);
					var inner_option_wrapper = $('#evt-invitation-wrapper .right-content .inner-option-wrapper');

					contact.addClass('selectable pointer').removeClass('unselectable');
				}
			});			
			
		}
	},'#evt-invitation-wrapper #invited-detail .remove-from-invitation');
	
	
	
	
	$('body').on({
		click:function(){
			if($(this).hasClass('requestable')){
				var parentDiv = $(this).parents('#evt-invitation-wrapper');
				var key = parentDiv.attr('data-key');
				if(key.trim() != ''){
					var keys = '';
					$('#selected-bar .selected-icon-avator').each(function(){
						keys += $(this).attr('data-key')+',';
					});
					$.ajax({
						url:AJAX_DIR+'event_invitation.php',
						method:'post',
						data:{key:key, keys:keys},
						success:function(resp){
							if(resp != '1'){
								var selected_bar = parentDiv.find('#selected-bar');
								var selected_avator = selected_bar.find('.selected-icon-avator');
								var selected_num = selected_avator.length;
								var invited = selected_bar.find('#invitation-invited-num');
								var invited_num = parseInt(invited.attr('data-num'));
								invited_num+=selected_num;
								invited.text(invited_num).attr('data-num',invited_num);
								selected_bar.find('#invitation-selected-num').text('0');
								selected_bar.find('#selected-preview-wrapper').addClass('hdn');
								selected_bar.animate({
									'max-width':'140px'
									},200,function(){
									var content_inner = parentDiv.find('.content-inner');
									selected_avator.each(function(){
										var contact_selectable = content_inner.find(' .list-item.invitation-contact.selectable[data-key='+$(this).attr('data-key')+']')
										contact_selectable.removeClass('selectable pointer');
										contact_selectable.find('.invitation-sent-wrapper').animate({
											'margin-right':'10px'
										},100,function(){
											selected_avator.remove();
											selected_bar.css('max-width','220px');
										});
									});
								});
								var inner_option_wrapper = parentDiv.find('.right-content .inner-option-wrapper');
								parentDiv.find('#invitation-invited-bar-wrapper').addClass('pointer');
								inner_option_wrapper.find('.after-action').removeClass('hdn');
								inner_option_wrapper.find('.before-action').addClass('hdn');
								$('#bar-icon-wrapper').css('width','inherit');
							}
						}	
					});
				}
			}
		}
	
	},'#evt-invitation-wrapper .option-wrapper .action-button');
	
	
	/*starts with include friend popup dialog javascript*/
	
	 $('body').on({
		click:function(){
			var key = $(this).parents('.evt-block').attr('data-key');
			$.ajax({
				url:AJAX_DIR+'load_event_include_friend_block.php',
				method:'post',
				data:{key:key},
				success:function(resp){
					console.log(resp);
					$('#dialog-popup-overlay').removeClass('hdn');
					$('#evt-include-friend-wrapper').html(resp).removeClass('hdn').attr('data-key',key);
				}	
			});
		}
	},'.include-friend');
	
	
	
	
	$('body').on({
		click:function(){
			if($(this).hasClass('requestable')){
				var parentDiv = $(this).parents('#evt-include-friend-wrapper');
				var key = parentDiv.attr('data-key');
				if(key.trim() != ''){
					var keys = '';
					$('#selected-bar .selected-icon-avator').each(function(){
						keys += $(this).attr('data-key')+',';
					});
					
					$.ajax({
						url:AJAX_DIR+'event_include_friends.php',
						method:'post',
						data:{key:key, keys:keys},
						success:function(resp){
							console.log(resp);
							if(resp != '1'){
								var selected_bar = parentDiv.find('#selected-bar');
								var selected_avator = selected_bar.find('.selected-icon-avator');
								var selected_num = selected_avator.length;
								var invited = selected_bar.find('#invitation-invited-num');
								var invited_num = parseInt(invited.attr('data-num'));
								invited_num+=selected_num;
								invited.text(invited_num).attr('data-num',invited_num);
								selected_bar.find('#invitation-selected-num').text('0');
								selected_bar.find('#selected-preview-wrapper').addClass('hdn');
								selected_bar.animate({
									'max-width':'140px'
									},200,function(){
									var content_inner = parentDiv.find('.content-inner');
									selected_avator.each(function(){
										var contact_selectable = content_inner.find('.list-item.invitation-contact.selectable[data-key='+$(this).attr('data-key')+']')
										contact_selectable.removeClass('selectable pointer');
										contact_selectable.find('.invitation-sent-wrapper').animate({
											'margin-right':'10px'
										},100,function(){
											selected_avator.remove();
											selected_bar.css('max-width','220px');
										});
									});
								});
								var inner_option_wrapper = parentDiv.find('.right-content .inner-option-wrapper');
								parentDiv.find('#invitation-invited-bar-wrapper').addClass('pointer');
								inner_option_wrapper.find('.after-action').removeClass('hdn');
								inner_option_wrapper.find('.before-action').addClass('hdn');
								$('#bar-icon-wrapper').css('width','inherit');
							}
						}	
					});
				}
			}
		}
	
	},'#evt-include-friend-wrapper .option-wrapper .action-button');
	
	
	$('body').on({
		click:function(){
			var parentDiv = $(this).parents('#evt-include-friend-wrapper');
			parentDiv.find('#selected-detail').addClass('hdn');
			$(this).addClass('red-act');
			$(this).find('#invitation-invited-num').addClass('red-act');
			$('#invitation-selected-bar-wrapper').removeClass('red-act');
			$('#invitation-selected-bar-wrapper').find('#invitation-selected-num').removeClass('red-act');
			var key = parentDiv.attr('data-key');
			var detail = parentDiv.find('#invited-detail');
			
			if(parseInt($('#invitation-invited-num').attr('data-num')) > 0 ){
				if(detail.hasClass('hdn')){
					$.ajax({
						url:AJAX_DIR+'load_event_included_list.php',
						method:'post',
						data:{key:key},
						success:function(resp){
							if(resp != '1'){
								detail.removeClass('hdn');
								detail.find('.sub-inner').html(resp);
							}
						}			
					});
				}else{
					detail.addClass('hdn');
				}
			}else{
				detail.find('.sub-inner').html('<div style="margin:10px;">The invited list is empty</div>');
				detail.toggleClass('hdn');
			}
			return false;
	
		}
	},'#evt-include-friend-wrapper #selected-bar #invitation-invited-bar-wrapper');
	
	
	$('body').on({
		click:function(){
			var parentDiv = $(this).parents('#evt-include-friend-wrapper');
			var invited_list = $(this).parents('.invited-list');
			var key = invited_list.attr('data-key');
			var key_for = invited_list.find('.label-image').attr('data-key');
			var thisE = $(this);
			var detail = parentDiv.find('#invited-detail');
			$.ajax({
				url:AJAX_DIR+'delete_event_include.php',
				method:'post',
				data:{key:key},
				success:function(resp){
					console.log(resp);
					invited_list.remove();
					var remaining_target_count = detail.find('.invited-list').length;
					parentDiv.find('#invitation-invited-num').text(remaining_target_count).attr('data-num',remaining_target_count);
					if(remaining_target_count == 0){
						detail.find('.sub-inner').html('<div style="margin:10px;">The included list is empty</div>');
					}
					var contact = parentDiv.find('.contact-inner .list-item.invitation-contact[data-key='+key_for+']');
					contact.find('.invitation-sent-wrapper').animate({
						'margin-right':'-100px'
					},100);
					var inner_option_wrapper = parentDiv.find('.right-content .inner-option-wrapper');

					contact.addClass('selectable pointer').removeClass('unselectable');
				}
			});			
			
		}
	},'#evt-include-friend-wrapper #invited-detail .remove-from-invitation');
	
	
	
	$('body').on({
		click:function(){
			var thisE = $(this);
			var key = thisE.parents('.event-include-request').attr('data-key');
			$.ajax({
				url:AJAX_DIR+'accept_event_include_request.php',
				method:'post',
				data:{key:key},
				success:function(resp){
					console.log(resp);
					if(resp != '1'){
						thisE.parents('.option').find('.ignore').remove();
 						thisE.text('Accepted').removeClass('accept animate-opacity pointer plain-lk');
 						refreshMessage();
 					}
				}
			});
		}
	},'#header-notification-delegate .event-include-request .accept');
	
	
	
	
	
	
	
	/*ends with include friend popup dialog javascript*/
	
	
});



