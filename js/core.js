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


function closeEditDialog(){
	$('.overlay, #edit-dialog-wrapper').addClass('hdn');
	$('body').removeClass('unscrollable');
}

/*ends scene box editor functions*/

function toggleDialogVerticalVerticalCenterPos(){
	$('#edit-dialog-wrapper').toggleClass('sugue-adjust');
}


function resetSegueDetail(){
	$('.segue-detail').css({'-webkit-animation':'','animation':''});
}

$(document).ready(function(){
	$('button input div img').on('click', function(e){
		e.stopPropagation();

	
	});
	
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
			toggleDialogVerticalVerticalCenterPos();
			var segue_wrapper = $(this).parents('.segue-wrapper');
			var wrapper_width = segue_wrapper.width();
			segue_wrapper.find('#post-photo-layout-segue .photo-layout-body').css('max-height',wrapper_width);
			
			var segue_main = segue_wrapper.find('.segue-main').removeClass('act');
			segue_wrapper.css('height',segue_main.height())
			segue_main.addClass('hdn');
			$('#post-photo-layout-segue').css({'-webkit-animation':'segueSlideInLeft 0.3s','animation':'segueSlideInLeft 0.3s', 'right':'0px'}).addClass('act');
			var segue_height = $('#post-photo-layout-segue').height();
			
			setTimeout(function(){
				segue_wrapper.animate({
					'height': segue_height,
				},100);
			},400);
			segue_wrapper.css('position','relative');
			return false;
		}
	
	},'#attach-post-photo');
	

});

$(document).keyup(function(e){
	if(e.keyCode == 27){
		closeEditDialog();
	}
});

$(document).click(function(){
		closeEditDialog();
});





