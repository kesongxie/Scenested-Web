/* global javascript file, contains core functions */
var SIGNUP_ALERT_MESSAGE = new Array();
var DOCUMENT_ROOT = "http://localhost:8888/lsere/";
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



function setVisibleContentWithParent(parent, text){
	parent.find('.visible-content').each(function(){
		if( typeof $(this)[0].scrollHeight !== "undefined" &&  $(this)[0].scrollHeight > $(this).innerHeight() ){
			if($(this).parents('.visible-post-scope').find('.visible-control').length < 1){
				$(this).parents('.visible-post-scope').find('.visible-content').after('<div class="visible-control plain-lk pointer inline-blk rdm" >Read more</div>');
			}
		}// else{
// 			$(this).parents('.visible-post-scope').find('.visible-control').remove();
// 		}
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
	var evt_wrapper_key = sender.parents('#evt-preview').attr('data-key');
	var pht_num =  sender.parents('.ct-pht-all').find('.thumb').length;
	var parentDiv = sender.parents('.thumb-evt-pht');
	var key = parentDiv.attr('data-key');
	$.ajax({
		url:AJAX_DIR+'remove_evt_pht.php',
		method:'post',
		data: {key:key},
		success:function(resp){
			console.log(resp);
			if(resp != '1'){
				var label_message = "";
				pht_num = (--pht_num >= 0)?pht_num:0;
				label_message = pht_num +' Photo'+ ((pht_num > 1 )?'s':'');
				$('.evt-block[data-key='+evt_wrapper_key+']').find('.total-photos-label').html(label_message);
				parentDiv.remove();
			}
		}
	});
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
	var key = postWrapper.attr('data-key');
	var commentBlock = postWrapper.find('.regular-comment-wrapper');
	if(commentBlock.hasClass('hdn')){
		//load
		$.ajax({
			url:AJAX_DIR+'load_comment_block.php',
			method:'post',
			data:{key:key},
			success:function(resp){
				commentBlock.find('.comment-container').html(resp);
				postWrapper.find('.cmt-num').text( commentBlock.find('.cmt').length);
				setVisibleContentWithParent(commentBlock, 'Read more')
			}
		});
	}
	commentBlock.toggleClass('hdn');
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



$(function(){
	setInterval(function(){
		$.post(AJAX_DIR+'fetchNewQueueNumber.php',function(resp){
			if(parseInt(resp)>0){
				$('#index-noti-red-spot').text(resp).removeClass('hdn');
			}else{
				$('#index-noti-red-spot').text('0').addClass('hdn');
			}
		});
	},5000);
});


$(document).keyup(function(evt){
	if(evt.keyCode==27)
	{	
		resetDialog();
	}
});


$(document).click(function(){
	$('.popover').hide();
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
		},
		mouseleave:function(){
			$('body').css('overflow','auto');
		}
	},'.child-scrollable');



	$('#loggedin-menu').on({
		click:function(){
			$(this).parents('#loggedin-menu').find('#setting-menu').toggle();
			$('#notification-center').hide();
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
					$.post(AJAX_DIR+"update_notification_queue.php");
				});
			}
			notification_center.toggle();
			$('#setting-menu').hide();
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
			$(this).find('.toggle-operation').addClass('hdn');
			$(this).find('.popover').hide();
		}
	},'.post-wrapper');
	
	
	$('body').on({
		click:function(){
			$('.popover').hide();
			$(this).parents('.post-wrapper').find('.popover').toggle();
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
			$('.time_picker_outer_wrapper').css('top',pos.top+30).css('left',pos.left).show();
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
			var parentDiv = $(this).parents('#evt-preview');
			var evt_wrapper_key = parentDiv.attr('data-key');
			var pht_num = parentDiv.find('.ct-pht-all').find('.thumb').length;
			var new_evt_pht_frame = parentDiv.find('.evt-pht-new-frame');
			$('.ct-pht-all').prepend(new_evt_pht_frame.clone());
			var imgTarget =new_evt_pht_frame.find('.target-image');
			var targetConatiner = new_evt_pht_frame.find('.target-container');
			var key = parentDiv.attr('data-key');
			
			var data=new FormData();
			data.append('profile-pic',$(this)[0].files[0]);
			data.append('key',key);
			
			var thisE = this;
			var preview_loading_wrapper = new_evt_pht_frame.find('.preview-loading-wrapper');
		
			$.ajax({
				url:AJAX_DIR+'upload_evt_pht.php',
				type:'POST',
				processData: false,
				contentType: false,
				data:data,
				success:function(resp){
					console.log(resp);
					if(resp == '1'){
						presentPopupDialog("Bad Image",BAD_IMAGE_MESSAGE, "Got it", "", null, null );
						new_evt_pht_frame.remove();
						preview_loading_wrapper.addClass('hdn');
						return false;
					}
					readURL(thisE,imgTarget);
					imgTarget.after('<img src="'+IMGDIR+'c_icon.png" class="remove-evt-pht pointer animate-opacity hdn">');
					new_evt_pht_frame.removeClass('hdn');
					preview_loading_wrapper.removeClass('hdn');
					targetConatiner.removeClass('hdn');
					new_evt_pht_frame.removeClass('hdn evt-pht-new-frame').addClass('thumb').attr('data-key',resp);
 					imgTarget.removeClass('target-image').addClass('vertical-center').unwrap();
					var label_message = "";
					pht_num = (++pht_num >= 0)?pht_num:0;
					label_message = pht_num +' Photo'+ ((pht_num > 1 )?'s':'');
					$('.evt-block[data-key='+evt_wrapper_key+']').find('.total-photos-label').html(label_message);
					$(thisE).val('');
					
					if(resp != '2'){
						setTimeout(function(){
							preview_loading_wrapper.remove();
						},2000);
 					}
				}
			});
		}
		
	},'.upload-evt-pic');
	
	
	$('body').on({
		click:function(){
			var thumb_src = $(this).find('img').attr('src');
			var src = thumb_src.replace('thumb_','');
			$(this).parents('#evt-preview').find('.pht .display-img').attr('src',src);
		}
	
	},'.thumb-evt-pht.thumb');
	
	$('body').on({
		mouseover:function(){
			$(this).find('.remove-evt-pht').removeClass('hdn');
		},
		mouseleave:function(){
			$(this).find('.remove-evt-pht').addClass('hdn');
		}
	},'#evt-preview .thumb-evt-pht');
	
	
	
	$('body').on({
		click:function(){
			presentPopupDialog("Remove Photo", "Do you want to remove this photo", "Cancel", "Remove", removeEventPhoto, $(this) );
			return false;
		}
	
	},'#evt-preview .thumb-evt-pht .remove-evt-pht');
	
	
	$('body').on({
		mouseover:function(){
			$(this).find('.toggle-operation').removeClass('hdn');
		},
		mouseleave:function(){
			$(this).find('.popover').hide();
			$(this).find('.toggle-operation').addClass('hdn');
		}
	
	},'.operation-triggeable');
	
	$('body').on({
		click:function(){
			var thisE = $(this);
			var parentDiv = thisE.parents('.operation-triggeable');
		
			if(parentDiv.find('.fri-oper').length < 1){
				var key = parentDiv.attr('data-key');
				$.ajax({
 					url:AJAX_DIR+'friend_operation.php',
 					method:'post',
 					data:{key:key},
 					success:function(resp){
 						parentDiv.append(resp);
 						var operationDiv = parentDiv.find('.fri-oper');
						$('.popover').not(operationDiv).hide();
						operationDiv.show();
 					}
 				});
			}else{
				var operationDiv = parentDiv.find('.fri-oper');
				$('.popover').not(operationDiv).hide();
				operationDiv.toggle();
			}
			return false;
		}
	},'.user-profile.operation-triggeable .toggle-operation');
	
	
	$('body').on({
		click:function(){
			var target_name = $(this).parents('.user-profile').find('.fullname').text();
		
			presentPopupDialog("Remove from Interest Group", "Do you want to remove \""+target_name+"\" from this interest group", "Cancel", "Remove", removeFromInterestGroup, $(this) );
			return false;
		}
	},'.user-profile.operation-triggeable .remove-from-interest');
	
	
	
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
			if($(this).css('padding-left')=='6px'){
				parentDiv.find('.selectable').css('padding-left','6px').removeClass('asct');
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
				$(this).animate({"padding-left": '6px'},100);	
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
			$.ajax({
				url:AJAX_DIR+'ld_preview_photo.php',
				method:'post',
				data:{key:key,from:from},
				success:function(resp){
					$('body').css('overflow','hidden');
					var photo_preview = $('#photo-preview');
					var content_wrapper = photo_preview.find('.content-wrapper');
					content_wrapper.html(resp);
					var preview_image = photo_preview.find('.preview-image');
					preview_image.attr('src',src).removeClass('hdn');
					preview_image.load(function(){
					  var height = this.naturalHeight;
					  var width = this.naturalWidth;
					  if(width >= height){
					  		preview_image.css('width','100%');
					  		if(preview_image.height() < 400){
					  			preview_image.addClass('vertical-center');
					  		}
 					  }else{
 					  		preview_image.css({'max-width':'100%', 'max-height':'100%'});
 					  }
					});
					$('#preview-popup-overlay').removeClass('hdn');
					photo_preview.removeClass('hdn');
					setVisibleContentWithParent(photo_preview,'Show detail');
				}
			})
		}
	
	},'.previewable');
	


	
	
});