/* global javascript file, contains core functions */
var SIGNUP_ALERT_MESSAGE = new Array();
var DOCUMENT_ROOT = "http://localhost:8888/lsere/";
var INDEX_PAGE = DOCUMENT_ROOT + "index.php";
var AJAX_DIR = DOCUMENT_ROOT+"ajax/";
var AJAX_PHTML_DIR = AJAX_DIR+"phtml/"
// var NOTIFICATION_CENTER_ON = false;
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



function resetDialog(){
	$('#dialog-popup-overlay').addClass('hdn');
	$('#popup-dialog-wrapper').addClass('hdn');
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
		if($(this)[0].scrollHeight > $(this).innerHeight() ){
			if($(this).parents('.visible-post-scope').find('.visible-control').length < 1){
				$(this).parents('.visible-post-scope').find('.visible-content').after('<div class="visible-control plain-lk pointer inline-blk rdm" >Read more</div>');
			}
		}else{
			$(this).parents('.visible-post-scope').find('.visible-control').remove();
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
	var comment_block = sender.parents('.comment-block .comment-container');
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

// function loadComment(thisE){
// 	var postWrapper = thisE.parents('.post');
// 	var key = postWrapper.attr('data-key');
// 	var commentBlock = postWrapper.find('.comment-block');
// 	if(commentBlock.hasClass('hdn')){
// 		//load
// 		$.ajax({
// 			url:AJAX_DIR+'load_comment_block.php',
// 			method:'post',
// 			data:{key:key},
// 			success:function(resp){
// 				commentBlock.find('.comment-container').html(resp);
// 				postWrapper.find('.cmt-num').text( commentBlock.find('.cmt').length);
// 				setVisibleContent();
// 			}
// 		});
// 	}
// 	commentBlock.toggleClass('hdn');
// }


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
				setVisibleContent();
			}
		});
	}
	commentBlock.toggleClass('hdn');
}




$(document).keyup(function(evt){
	if(evt.keyCode==27)
	{	
		resetDialog();
	}
});


$(document).click(function(){
	$('.popover').hide();
	// if(NOTIFICATION_CENTER_ON){
// 		//collapse
// 		$('#side-bar-notification-center').animate({
// 				'right':'-26%',	
// 		},200);
// 	}
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
	setVisibleContent();
	
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

	$('#loggedin-menu').on({
		click:function(){
			$(this).parents('#loggedin-menu').find('.popover').toggle();
			return false;
		}
	},'#loggin-user-icon');
	
	// $('#loggedin-menu').on({
// 		click:function(){
// 			if(!NOTIFICATION_CENTER_ON){
// 				$('#side-bar-notification-center').animate({
// 					'right':'0',	
// 				},200);
// 				$('#loggedin-menu #index-noti-red-spot').hide();
// 				NOTIFICATION_CENTER_ON = true;
// 			}else{
// 				$('#side-bar-notification-center').animate({
// 					'right':'-26%',	
// 				},200);
// 				NOTIFICATION_CENTER_ON = false;
// 			}
// 			
// 			return false;
// 		}
// 	},'#header-notification-delegate');
	
	// $('body').on({
// 		click:function(){
// 			return false;
// 		}
// 	},'#side-bar-notification-center');
	
	
	
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
	},'.visible-control');
	
	
	
	$('body').on({
		mouseover:function(){
			$(this).find('.toggle-operation').removeClass('hdn');
		},
		mouseleave:function(){
			$(this).find('.toggle-operation').addClass('hdn');;
		}
	},'.post-wrapper');
	
	
	$('body').on({
		click:function(){
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
	},'.comment-block .inner-container');
	
	
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
		}
	},'.comment-block .comment .reply-comment');
	
	
	$('body').on({
		click:function(){
			presentPopupDialog("Remove Comment", "Do you want to remove this comment", "Cancel", "Remove", removeComment, $(this) );

		}
	},'.comment-block .comment .trash-comment');
	
	
	$('body').on({
		click:function(){
			presentPopupDialog("Remove Comment", "Do you want to remove this comment", "Cancel", "Remove", removeReply, $(this) );

		}
	},'.comment-block .comment .trash-reply');
	
	
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
	

	
	
	
});