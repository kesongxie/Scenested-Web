/*starts scene box editor functions*/
function replacer(match, p1, p2, p3, p4){
 	var re = /^\s$/;
 	var length_before = typeof p1 !== 'undefined'?p1.length:0;
 	var length_after = typeof p4 !== 'undefined'?p4.length:0;
 	var returnValues = '';
 	if( length_before == 0 || (length_before > 0  && re.test(p1))   ){
 		if(length_before > 0){
  			returnValues = p1;
  		}
  	}else{
  		//illegal preceding
  		return p1+p2+p3+p4;
  	}
 	if( length_after == 0 || (length_after > 0  && re.test(p4))   ){
 		returnValues += '<a class="hashtag">'+p2+p3+'</a>';
 		if(length_after > 0){
  			returnValues += p4;
  		}
  	}else{
  		//illegal succeeding
 		 returnValues += p2 + p3 + p4;
 	}
 	return returnValues
 	
}

function getCaretCharacterOffsetWithin(element) {
    var caretOffset = 0;
    var doc = element.ownerDocument || element.document;
    var win = doc.defaultView || doc.parentWindow;
    var sel;
    if (typeof win.getSelection !== "undefined") {
        sel = win.getSelection();
        if (sel.rangeCount > 0) {
            var range = win.getSelection().getRangeAt(0);
            var preCaretRange = range.cloneRange();
            preCaretRange.selectNodeContents(element);
            preCaretRange.setEnd(range.endContainer, range.endOffset);
            caretOffset = preCaretRange.toString().length;
        }
    } else if ( (sel = doc.selection) && sel.type != "Control") {
        var textRange = sel.createRange();
        var preCaretTextRange = doc.body.createTextRange();
        preCaretTextRange.moveToElementText(element);
        preCaretTextRange.setEndPoint("EndToEnd", textRange);
        caretOffset = preCaretTextRange.text.length;
    }
    return caretOffset;
}
function setEndOfContenteditable(contentEditableElement){
    var range,selection;
    if(document.createRange)//Firefox, Chrome, Opera, Safari, IE 9+
    {
        range = document.createRange();//Create a range (a range is a like the selection but invisible)
        range.selectNodeContents(contentEditableElement);//Select the entire contents of the element with the range
        range.collapse(false);//collapse the range to the end point. false means collapse to end rather than the start
        selection = window.getSelection();//get the selection object (allows you to change selection)
        selection.removeAllRanges();//remove any selections already made
        selection.addRange(range);//make the range you have just created the visible selection
    }
    else if(document.selection)//IE 8 and lower
    { 
        range = document.body.createTextRange();//Create a range (a range is a like the selection but invisible)
        range.moveToElementText(contentEditableElement);//Select the entire contents of the element with the range
        range.collapse(false);//collapse the range to the end point. false means collapse to end rather than the start
        range.select();//Select the range (make it the visible selection
    }
}
function moveCaretPosition(thisE, caretPos, nodeEditingIndex){
		var el = thisE.childNodes[nodeEditingIndex];
		var range = document.createRange();
		var sel = window.getSelection();
		if(el.nodeType == 3){
			//this is a text node
			range.setStart(el, caretPos);
		}else{
			range.setStart(el.childNodes[0], caretPos);
		}
		range.collapse(true);
		sel.removeAllRanges();
		sel.addRange(range);
		thisE.focus();
}

function returnIndexForNode(thisE){
		var node;
		node = getEditingNode();
		return $(thisE).contents().index(node);
	}
	
function setCaretPosition(thisE, currentCaretPos){
	var cursor_move = currentCaretPos;
	var nodeIndex = 0;
	var caretPos = 0;
	$(thisE).contents().each(function(i, node){
		if(cursor_move > 0){
			if(node.nodeType == 1){
				//span
				var text_length = $(node).text().length;
				if(cursor_move > text_length){
					cursor_move -= text_length;
					nodeIndex++;
				}else{
					//stop
					caretPos = cursor_move;
					return false;
				}
			}else{
				var text_length = $(node).text().length;
				if(cursor_move > text_length){
					cursor_move -= text_length;
					nodeIndex++;
				}else{
					//stop
					caretPos = cursor_move;
					return false;
				}
			}
		}
	});
	moveCaretPosition(thisE, caretPos, nodeIndex) ;
}	

function isUndoKeyPressed(e){
	if (navigator.appVersion.indexOf("Win")!=-1){
		//windows.	
		return  e.ctrlKey && e.keyCode == 90;
	}else if(navigator.appVersion.indexOf("Mac")!=-1){
		//mac.
		return e.metaKey && e.keyCode == 90;
	}
}

function isRedokeyPressed(e){
	if (navigator.appVersion.indexOf("Win")!=-1){
		//windows.	
		return  e.ctrlKey && e.shiftKey && e.keyCode == 90;
	}else if(navigator.appVersion.indexOf("Mac")!=-1){
		//mac.
		return e.metaKey && e.shiftKey && e.keyCode == 90;
	}
}


function closeEditDialog(reset){
	var edit_dialog = $('#edit-dialog-wrapper');
	$('.overlay').addClass('hdn');
	$('body').removeClass('unscrollable');
	edit_dialog.addClass('hdn');
	if(reset){
		edit_dialog.find('.segue-main').addClass('act').removeClass('hdn');
		edit_dialog.find('.segue-detail').removeClass('act');
	}
}

/*ends scene box editor functions*/

function toggleDialogVerticalVerticalCenterPos(){
	$('#edit-dialog-wrapper').toggleClass('sugue-adjust');
}


function resetSegueDetail(){
	$('.segue-detail').css({'-webkit-animation':'','animation':''});
}


function readURL(input,tg) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function (e) {
			tg.addClass('hdn');
			var url = e.target.result;
			tg.attr('src', url);
			 var image = new Image();
			image.src = url;
			image.onload = function() {
				if(this.width > this.height){
					//landscape
					tg.css('height','100%');
				}else{
					//portrait
					tg.css('width','100%');
				}
				tg.removeClass('hdn');
			};
		}
		reader.readAsDataURL(input.files[0]);
	}
}


/*************** starts global variable ***********/
var mouseX = 0;
var mouseY = 0;
var ANGLE_SLOPE = 5;

/*************** ends global variable ***********/


$(document).ready(function(){
	// $('button input div img').on('click', function(e){
// 		e.stopPropagation();
// 
// 	
// 	});
	
	$('#edit-dialog-wrapper-inner').click(function(){
		return false;
	});	


	$('body #edit-dialog-wrapper-inner').on({
		click:function(e){
			var segue_wrapper = $(this).parents('.segue-wrapper');
			var segue_main = segue_wrapper.find('.segue-main');
			segue_wrapper.css('height',segue_main.height());
			segue_main.addClass('hdn');
			$('#add-scene-segue').css({'-webkit-animation':'segueSlideInLeft 0.3s','animation':'segueSlideInLeft 0.3s', 'right':'0px'});
			var segue_height = $('#add-scene-segue').height();
			
			setTimeout(function(){
				segue_wrapper.animate({
					'height': segue_height
				},100);
			},400);
			segue_wrapper.css('position','relative');
			return false;
		}
	},'.option-bar#add-scene-bar');
	
	$('body #edit-dialog-wrapper-inner').on({
		click:function(e){
			toggleDialogVerticalVerticalCenterPos();
			var segue_wrapper = $(this).parents('.segue-wrapper');
			var segue_main = segue_wrapper.find('.segue-main');
			segue_main.removeClass('hdn');
			var segue_detail = $(this).parents('.segue-detail');
			segue_detail.css({'-webkit-animation':'segueSlideOutRight 0.3s','animation':'segueSlideOutRight 0.3s','right':'-100%'});
			var segue_height = segue_main.height();
			setTimeout(function(){
				segue_wrapper.animate({
					'height': segue_height
				},100, function(){
					segue_wrapper.css('position','');
				});
			},400);
			
 			return false;
		}
	
	},'.segue-to-main-action');
	
	
	
	
	/*necessary variables for editing*/
	var nodeEditing;
	var nodeIndexEditing = -1;
	var history_text = '';

	$('body').on({
		keydown:function(e){
			if(isRedokeyPressed(e)){
				$(this).html(history_text);
				setEndOfContenteditable(this);
			}else if(isUndoKeyPressed(e)){
				history_text = $(this).html();
				$(this).html('');
			}
		},
		keypress:function(e){
		 	return e.which != 13; //disable return key press
		},
		keyup:function(e){
			$(this).parents('.segue-wrapper').css('height','');
			var old_text = $(this).attr('data-val');
			var text = $(this).text(); 
			var caretPos = getCaretCharacterOffsetWithin(this);
			var text_length = $(this).text().length;
			if(caretPos < text_length){
				$(this).attr('data-end','false');
			}else{
				$(this).attr('data-end','true');
			}
			if(old_text != text){
				$(this).attr('data-val',text);
				text = text.replace(/\n/,'');
				text = text.replace(/ /g, '\u00a0'); //replace white space with &nbsp
				var rich_text = text.replace(/(.?)(#|@)(\w+)(\W?)/g, replacer);
				if($(this).attr('data-end') == 'true'){
					$(this).html(rich_text);
					setEndOfContenteditable(this);
				} else{
					var caretPos = getCaretCharacterOffsetWithin(this); //this is the caret pos regarding the entire node
					$(this).html(rich_text);
					setCaretPosition(this, caretPos);
				}
			}
		},
		paste:function(e){
		  	e.preventDefault();
  			var pasted_text = e.originalEvent.clipboardData.getData('Text');
			var oldcaretPos = getCaretCharacterOffsetWithin(this);
			var newCaretPos = oldcaretPos + pasted_text.length;
			var old_text = $(this).text();
			var new_text = old_text.substring(0, oldcaretPos)+pasted_text+old_text.substring(oldcaretPos);
			$(this).html(new_text);
			setCaretPosition(this, newCaretPos);
		},
		
		click:function(){
			var caretPos = getCaretCharacterOffsetWithin(this);
			var text_length = $(this).text().length;
			if(caretPos < text_length){
				$(this).attr('data-end','false');
			}else{
				$(this).attr('data-end','true');
			}
		},
		focus:function(){
			setEndOfContenteditable(this);
		}
	},'.content-editable');

	
	$('body #edit-dialog-wrapper-inner').on({
		click:function(e){
			e.stopPropagation();
		}
	
	},'#attach-post-photo');
	
	
	
	$('body #edit-dialog-wrapper-inner').on({
		click:function(e){
		 	e.stopPropagation();
 		}
	},'#attach-post-photo-label');
	
	
	$('body #edit-dialog-wrapper-inner').on({
		change:function(){
			var thisE = this;
			var label = $(this).parents('#attach-post-photo-label');
			var expand_text = label.find('.expand-text');
			var current_width = parseInt(label.width());
			
			label.animate({
				'width':'-=128px'
			}, {
					duration:300, 
					start:function(){
						if( current_width <= 256){
							expand_text.addClass('hdn');
						}
					},
					complete:function(){
						var banner = $('#attach-post-photo-banner');
						var label = banner.find('#attach-post-photo-label');
						label.before('<div class="post-photo-thumbnail pending"><img class="photo-thumbnail" ><img class="remove-circle-icon" src="'+IMGDIR+'remove_icon.png" height="14" width="14"></div>');
						var thumb_wrapper =  banner.find('.post-photo-thumbnail.pending');
						var photo_thumb = thumb_wrapper.find('.photo-thumbnail');
						readURL(thisE, photo_thumb);
						thumb_wrapper.removeClass('pending');
						$(thisE).val('');
					}
			});
		}
	},'#attach-post-photo');
	
	$('body #edit-dialog-wrapper-inner').on({
		click:function(){
			var banner = $(this).parents('#attach-post-photo-banner');
			var label = banner.find('#attach-post-photo-label');
			var expand_text = label.find('.expand-text');
			var current_width = parseInt(label.width());
			
			$(this).parents('.post-photo-thumbnail').remove();
			label.animate({
				'width':'+=128px'
			}, {duration:300, 
				start:function(){
					if( current_width >= 128){
						expand_text.removeClass('hdn');
					}
				}
			});
		}
	},'.post-photo-thumbnail .remove-circle-icon');
	
	
// 	 $( "#layout-draggable" ).sortable({
//      	 revert: true
//     });
	
	var startDragX = 0, startDragY = 0;
	var stopDragX = 0, stopDragY = 0;
	
	$('.photo-segment').draggable({
		containment:'#layout-draggable',
		axis:'y',
		scroll:false,
		revert:"invalid",
		start:function(){
			startDragX = mouseX;
			startDragY = mouseY;
		},
		drag:function(event,ui){
			$(this).css({'z-index':9999, 'opacity':'0.8'});
		},
		stop:function(event,ui){
			$(this).css({'z-index':'', 'opacity':'1'});
		}
	});
	
	// $('.photo').draggable({
// 		containment:'parent',
// 		scroll:false,
// 		drag:function(event,ui){
// 		
// 		// 	ui.helper.addClass('drop-photo-scale-animation');
// 		},
// 		stop:function(event,ui){
// 			// ui.helper.removeClass('drop-photo-scale-animation');
// 
// 		}
// 	});
// 	
	
	var drag_left = false;
	var drag_down = false;
	var swap = false;
	
	$('.photo-segment-container').droppable({
		accept:'.photo-segment',
		tolerance:'intersect',
		over:function(event, ui){
			var des_container = $(this);
			var des_container = $(this);
			var src_target = ui.draggable;
			var des_container_queue = des_container.attr('data-container-queue');
			var src_target_queue = src_target.attr('data-segment-queue');
			if(des_container_queue != src_target_queue){
				des_container.find('.photo').addClass('drop-photo-scale-animation');
				var title_text = $('#post-photo-layout-segue .dialog-title-text');
				stopDragX = mouseX;
				stopDragY = mouseY;
				
				
				var slope = (stopDragY - startDragY)/(stopDragX - startDragX);
				if( -ANGLE_SLOPE < slope && slope < ANGLE_SLOPE ){
					drag_down = (stopDragY - startDragY > 0);
					drag_left = (drag_down) ? (slope > 0?false:true) : (slope > 0?true:false);
					if(drag_left){
						title_text.text('Drag to left');	

					}else{
						title_text.text('Drag to right');	
					}
					swap = false;
				}else{
					title_text.text('Swap');
					swap = true;
				}
			}
			
			
		
			
			
		},
		drop:function(event, ui){
			var des_container = $(this);
			var src_target = ui.draggable;
			var des_target = des_container.find('.photo-segment');
			var src_container = src_target.parents('.photo-segment-container');
			
			var des_container_queue = des_container.attr('data-container-queue');
			var src_target_queue = src_target.attr('data-segment-queue');
			
			if(des_container_queue != src_target_queue){
				//swap the element 
				var changeToVertical = (!drag_down && drag_left )  || (drag_down && !drag_left) ;
				if(swap ||  changeToVertical){
					var src_content = src_container.find('.photo');
					src_container.find('.photo-segment').html(des_container.find('.photo'));
					des_container.find('.photo-segment').html(src_content);
				}
				
				if(!swap){
					src_container.removeClass('two-column-horizon').addClass('two-column-vertical');
					des_container.removeClass('two-column-horizon').addClass('two-column-vertical');
				}
				
			}
			$('#layout-draggable .photo-segment').css({'top':'0px'});
			$('.photo-segment-container .photo').removeClass('drop-photo-scale-animation');
			var title_text = $('#post-photo-layout-segue .dialog-title-text');
			title_text.text($(title_text).attr('data-default-title'));
			
		}, 
		deactivate:function(event,ui){
			return false;
		}
	
	});
	
	
	$('body #edit-dialog-wrapper-inner').on({
		click:function(){
			var banner = $(this).parents('#attach-post-photo-banner');
			var length = banner.find('.post-photo-thumbnail').length;
			if(length > 1){
				toggleDialogVerticalVerticalCenterPos();
				var segue_wrapper = $(this).parents('.segue-wrapper');
				var wrapper_width = segue_wrapper.width();
				var segue_main = segue_wrapper.find('.segue-main').removeClass('act');
				var post_layout_segue = $('#post-photo-layout-segue');
				var layout_body = post_layout_segue.find('.photo-layout-body');
				banner.find('.photo-thumbnail').each(function(index){
					var container = layout_body.find('.photo-segment-container[data-container-queue="'+(index+1)+'"]');
					container.find('.photo').attr('src', $(this).attr('src'));
					container.removeClass('hdn');
				});
				segue_wrapper.css('height',segue_main.height());
				segue_main.addClass('hdn');
				post_layout_segue.css({'-webkit-animation':'segueSlideInLeft 0.3s','animation':'segueSlideInLeft 0.3s', 'right':'0px'}).addClass('act');
				var segue_height = post_layout_segue.height();

				setTimeout(function(){
					segue_wrapper.animate({
						'height': segue_height,
					},100);
				},400);
				segue_wrapper.css('position','relative');
			 
			 }
			 
			 
		}
	},'.post-photo-thumbnail');
	
	
	
	
	

});

$(document).keyup(function(e){
	if(e.keyCode == 27){
		closeEditDialog();
	}
});

$(document).click(function(){
		closeEditDialog(false);
});


$(document).on('mousemove',function(e){
		mouseX = e.pageX;
		mouseY = e.pageY;
});

	
	



