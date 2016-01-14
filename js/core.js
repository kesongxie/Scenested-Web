/*\
|*|
|*|  IE-specific polyfill that enables the passage of arbitrary arguments to the
|*|  callback functions of javascript timers (HTML5 standard syntax).
|*|
|*|  https://developer.mozilla.org/en-US/docs/Web/API/window.setInterval
|*|
|*|  Syntax:
|*|  var timeoutID = window.setTimeout(func, delay, [param1, param2, ...]);
|*|  var timeoutID = window.setTimeout(code, delay);
|*|  var intervalID = window.setInterval(func, delay[, param1, param2, ...]);
|*|  var intervalID = window.setInterval(code, delay);
|*|
\*/

// for < IE9 ,setTimeout() or setInterval() callback argument 
if (document.all && !window.setTimeout.isPolyfill) {
  var __nativeST__ = window.setTimeout;
  window.setTimeout = function (vCallback, nDelay /*, argumentToPass1, argumentToPass2, etc. */) {
    var aArgs = Array.prototype.slice.call(arguments, 2);
    return __nativeST__(vCallback instanceof Function ? function () {
      vCallback.apply(null, aArgs);
    } : vCallback, nDelay);
  };
  window.setTimeout.isPolyfill = true;
}

if (document.all && !window.setInterval.isPolyfill) {
  var __nativeSI__ = window.setInterval;
  window.setInterval = function (vCallback, nDelay /*, argumentToPass1, argumentToPass2, etc. */) {
    var aArgs = Array.prototype.slice.call(arguments, 2);
    return __nativeSI__(vCallback instanceof Function ? function () {
      vCallback.apply(null, aArgs);
    } : vCallback, nDelay);
  };
  window.setInterval.isPolyfill = true;
}



/*************** starts global variable ***********/
var mouseX = 0;
var mouseY = 0;
var mouseViewPortX = 0;
var mouseViewPortY = 0;
var TWO_COLUMN_HORIZON_ANGLE_SLOPE = 5;
var TWO_COLUMN_VERTICAL_ANGLE_SLOPE = 0.4;
var ROOTDIR = 'http://localhost:8888/';
var AJAXDIR = ROOTDIR+'ajax/';
var DRAG_MODE =  {'verticalMode':'vertical','horizonMode':'horizon'};
var LAYOUT_MODE = {'twoColumnHorizon':'two-column-horizon','twoColumnVertical':'two-column-vertical'};

/*************** ends global variable ***********/

/*******  DataState object set the data-state attribute of the given element ******/

//The constructor receive a selector of a element 
var DataState = function (target) {
	this.target = target;
	this.dataState = {'processing':'processing','ready':'ready'};
};
DataState.prototype.setTargetDataStateToProcessing = function(){
	this.target.attr('data-state', this.dataState.processing);
}
DataState.prototype.setTargetDataStateToReady = function(){
	this.target.attr('data-state', this.dataState.ready);
}
DataState.prototype.isStateReady = function(){
	return this.target.attr('data-state') == this.dataState.ready;
}
/************************  Ends DataState class declaration ***********************/


/************************  intervalIdManager sets, gets and remove intervalId on the receiving parameter in the constructor   ***********************/
var intervalIdManager = function(ele){
	this.ele = ele;
}
intervalIdManager.prototype.setIntervalIdOnElement = function(intervalId){
	this.ele.attr('data-intId', intervalId);
}
intervalIdManager.prototype.getIntervalIdOnElement = function(){
	return this.ele.attr('data-intId');
}
intervalIdManager.prototype.destoryIntervalIdOnElement = function(){
	this.ele.removeAttr('data-intId');
}
/***********************  ends intervalIdManager  class declaration ***********************/

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
		if(typeof el !== 'undefined'){
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


/*
	@param constraint
		 constraint within which the tg allowed to drag, adjustable is set to true 
	@param v_w
		 visible width of the image
	@param v_h
		 visible height of the image
				
	
*/
function readURL(input,tg, resize, adjustable, constraint, v_w, v_h){
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function (e) {
			//tg.addClass('hdn');
			var url = e.target.result;
			tg.attr('src', url);
			 var image = new Image();
			image.src = url;
			image.onload = function() {
				if(resize){
					if(this.width > this.height){
						//landscape
						tg.css({'height':'100%', 'width':'auto'});
					}else{
						//portrait
						tg.css({'height':'auto', 'width':'100%'});
					}
				}
				//tg.removeClass('hdn');
				
				if(adjustable){
					//calculate the size, position of the constraint
					var tg_width = tg.width();
					var tg_height = tg.height();
					if(tg_width/tg_height <= v_w/v_h){
						//adjust vertically
						tg.attr('data-mode', DRAG_MODE.verticalMode);
						tg.css({'width':'100%', 'height':'auto', 'left':'0px', 'top':'0px', 'buttom':'0px', 'right':'0px'});
						var tg_h = tg.height();
						var constraint_height = 2 * tg_h - v_h;
						var position_adjust = -(constraint_height - tg_h);
						constraint.css({'height': constraint_height,'width':'100%', 'top':position_adjust, 'left':'0px'});
						tg.draggable('option', 'axis', 'y');
					}else{
						//adjust horizontally
						tg.attr('data-mode', DRAG_MODE.horizonMode);
						tg.css({'height':'100%', 'width':'auto', 'left':'0px', 'top':'0px', 'buttom':'0px', 'right':'0px'});
						var tg_w = tg.width();
						var constraint_width = 2 * tg_w - v_w;
						var position_adjust = -(constraint_width - tg_w);
						constraint.css({'width': constraint_width, 'height':'100%','left':position_adjust, 'top':'0px'});
						tg.draggable('option', 'axis', 'x');
					}
				}
			};
		}
		reader.readAsDataURL(input.files[0]);
	}
}



function resetTwoColumnLayout(){
	var containers = $('#layout-draggable').find('.photo-segment-container');
	containers.removeClass('two-column-vertical').addClass('two-column-horizon');
	setLayoutMode(LAYOUT_MODE.twoColumnHorizon);
	containers.find('.photo-segment').css({'left':'0px', 'top':'0px', 'right':'0px', 'bottom':'0px'});
	containers.find('.photo').attr('src','').css('margin', '0px');
}


function setLayoutMode(mode){
	$('#layout-draggable').attr('data-layout-mode',mode); 
}

function getLayoutMode(){
	return $('#layout-draggable').attr('data-layout-mode'); 
}

function isPostPhotoModified(){
	return $('#attach-post-photo-banner').attr('data-modified') == 'true';
}

function setPostPhotoModified(){
	$('#attach-post-photo-banner').attr('data-modified','true');
}

function unsetPostPhotoModified(){
	$('#attach-post-photo-banner').attr('data-modified','false');
}




function ImageValidator(fileInput, callback){
	var data = new FormData();
	data.append('file', fileInput.files[0]);
	$.ajax({
		url:AJAXDIR+'validateImageFile.php',
		type:'post',
		data:data,
		processData: false, 
		contentType: false,
		success:function(resp){
			if(resp == '0'){
				callback(fileInput);
			}else{
				console.log('invalid');
			}
		}
	});
}
	


function addPostPhoto(fileInput){
		var $this=  $(fileInput);
		var add_scene_wrapper = $('#edit-dialog-wrapper #add-scene-dialog-wrapper');
		var banner =  add_scene_wrapper.find('#attach-post-photo-banner');
		var label = banner.find('#attach-post-photo-label');
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
					label.before('<div class="post-photo-thumbnail pending"><img class="photo-thumbnail" ><img class="remove-circle-icon" src="'+IMGDIR+'remove_icon.png" height="14" width="14"></div>');
					var thumb_wrapper =  banner.find('.post-photo-thumbnail.pending');
					var photo_thumb = thumb_wrapper.find('.photo-thumbnail');
					readURL(fileInput, photo_thumb);
					thumb_wrapper.removeClass('pending');
					add_scene_wrapper.find('#post-photo-layout-segue')
					
					var next_input_will_load = $this.parents('.photo-segment-container').next().find('.attach-post-photo');
					next_input_will_load.addClass('next');
					$this.removeClass('next').addClass('set');
					var next_id =next_input_will_load.attr('id');
					label.attr('for',next_id);
				}
		});
		setPostPhotoModified();
	}



function getIntValueFromCSSStyle(style){
	return parseInt(style.replace('px',''));
}


$(document).ready(function(){
	
	$('#edit-dialog-wrapper-inner').click(function(){
	//	return false;
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
	},'.text-list-wrapper#edit-scene-label');
	
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
			ImageValidator(this, addPostPhoto);
		}
	},'.attach-post-photo.next');
	
	
	
	
	
	
	
	
	$('body #edit-dialog-wrapper-inner').on({
		click:function(){
			var $this = $(this);
			var banner = $this.parents('#attach-post-photo-banner');
			var label = banner.find('#attach-post-photo-label');
			var expand_text = label.find('.expand-text');
			var current_width = parseInt(label.width());
			$this.parents('.post-photo-thumbnail').remove();	
		
			var input_will_be_load = $('#layout-draggable .photo-segment-container input.attach-post-photo.set').last();
			if(input_will_be_load.hasClass('set')){
				input_will_be_load.removeClass('set').addClass('next'); //mark as next, so that is available 
			}
			label.attr('for',input_will_be_load.attr('id')); //the id should match the new available input
			input_will_be_load.val(''); //reset the input value
			
			var parent_input_to_be_removed = input_will_be_load.parents('.photo-segment-container').next('.photo-segment-container');
			if(parent_input_to_be_removed.length > 0){
				var input_to_be_removed = parent_input_to_be_removed.find('.attach-post-photo');
				if(input_to_be_removed.hasClass('next')){
					input_to_be_removed.removeClass('next');
				}
			}
		
			label.animate({
				'width':'+=128px'
			}, {duration:300, 
				start:function(){
					if( current_width >= 128){
						expand_text.removeClass('hdn');
					}
				}
			});
			setPostPhotoModified();	
			return false;
		}
	},'.post-photo-thumbnail .remove-circle-icon');
	

	
	var startDragX = 0, startDragY = 0;
	var stopDragX = 0, stopDragY = 0;
	
	var initial_rect;
	
	/*$('.photo').draggable({
		containment:'#photo-constriant',
		axis:'y',
		scroll:false,
		revert:'valid',
		opacity:0.8,
		zIndex:100,
		start:function(event,ui){
			startDragX = mouseX;
			startDragY = mouseY;
			initial_rect = ui.helper.parents('.photo-segment-container')[0].getBoundingClientRect();
		},
		drag:function(event,ui){
			
			if(initial_rect.left < mouseViewPortX && mouseViewPortX < initial_rect.right && initial_rect.top < mouseViewPortY && mouseViewPortY < initial_rect.bottom){
				console.log('in');
				//$(this).draggable( "option", "containment", "#photo-constriant" );
// 				$(this).draggable( "option", "revert", "false" );
			}else{	
				console.log('out');
				$(this).draggable( "option", "containment", "" );
// 				$(this).draggable( "option", "revert", "invalid" );
			}
		},
		stop:function(event,ui){
			
		}
	});
*/

	
	var drag_left = false;
	var drag_down = false;
	var swap = false;
	
 	/*$('.photo-segment-container').droppable({
		accept:'.photo',
		tolerance:'pointer',
		over:function(event, ui){
			var mode = getLayoutMode();
			var des_container = $(this);
			var src_target = ui.draggable; //photo
			var des_container_queue = des_container.attr('data-container-queue');
			var src_target_queue = src_target.parent('.photo-segment').attr('data-segment-queue');
			if(des_container_queue != src_target_queue){
				des_container.find('.photo').addClass('drop-photo-scale-animation');
				var title_text = $('#post-photo-layout-segue .dialog-title-text');
				stopDragX = mouseX;
				stopDragY = mouseY;
				if(mode == LAYOUT_MODE.twoColumnHorizon){
					var slope = (stopDragY - startDragY)/(stopDragX - startDragX);
					if( -TWO_COLUMN_HORIZON_ANGLE_SLOPE < slope && slope < TWO_COLUMN_HORIZON_ANGLE_SLOPE ){
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
				}else if(mode == LAYOUT_MODE.twoColumnVertical){
					var slope = (stopDragY - startDragY)/(stopDragX - startDragX);
					if( -TWO_COLUMN_VERTICAL_ANGLE_SLOPE < slope && slope < TWO_COLUMN_VERTICAL_ANGLE_SLOPE){
						title_text.text('Swap');
						swap = true;
					}else{
						drag_down = (stopDragY - startDragY > 0);
						drag_left = (drag_down) ? (slope > 0?false:true) : (slope > 0?true:false);
						if(drag_down){
							title_text.text('Drag to bottom');	
						}else{
							title_text.text('Drag to top');	
						}
						swap = false;	
					}
				}
			}
		},
		drop:function(event, ui){
		 	var des_container = $(this); //container
			var src_target = ui.draggable; //photo
			
			var des_target = des_container.find('.photo'); //photo
			var src_container = src_target.parents('.photo-segment-container'); //container
			
			var des_container_queue = des_container.attr('data-container-queue');
			var src_target_queue = src_target.parent('.photo-segment').attr('data-segment-queue');
			
			if(des_container_queue != src_target_queue){
				//swap the element 
				var changeToVertical = (!drag_down && drag_left )  || (drag_down && !drag_left) ;
				if(swap ||  changeToVertical){
					var src_url = src_target.attr('src');
					var des_url = des_target.attr('src');
					src_target.attr('src',des_url);
					des_target.attr('src', src_url);
				}
				if(!swap){
					if(getLayoutMode() == LAYOUT_MODE.twoColumnHorizon){
						src_container.removeClass('two-column-horizon').addClass('two-column-vertical');
						des_container.removeClass('two-column-horizon').addClass('two-column-vertical');
						src_target.draggable('option','axis','x');
						des_target.draggable('option','axis','x');
						setLayoutMode(LAYOUT_MODE.twoColumnVertical);
					}else{
						src_container.removeClass('two-column-vertical').addClass('two-column-horizon');
						des_container.removeClass('two-column-vertical').addClass('two-column-horizon');
						src_target.draggable('option','axis','y');
						des_target.draggable('option','axis','y');
						setLayoutMode(LAYOUT_MODE.twoColumnHorizon);
					}
					
				}
			}
			
				$('#layout-draggable .photo-segment .photo').css({'left':'0px', 'top':'0px'});
		
			$('.photo-segment-container .photo').removeClass('drop-photo-scale-animation');
			var title_text = $('#post-photo-layout-segue .dialog-title-text');
			title_text.text($(title_text).attr('data-default-title'));
			
		}, 
		deactivate:function(event,ui){
			return false;
		}
	
	});
	*/
	
	
	
	
	
	
	/* --------droppable working, now adjustable---------- */
	

	$('.photo-segment').draggable({
		containment:'#layout-draggable',
		scroll:false,
		revert:"invalid",
		opacity:0.8,
		zIndex:100,
		helper:function(){
			return '<div></div>';	
		},
		start:function(event, ui){
			startDragX = mouseX;
			startDragY = mouseY;
			initial_rect = $(this).parents('.photo-segment-container')[0].getBoundingClientRect();
		},
		drag:function(event,ui){
			if(initial_rect.left < mouseViewPortX && mouseViewPortX < initial_rect.right && initial_rect.top < mouseViewPortY && mouseViewPortY < initial_rect.bottom){
				//don't move the element at all, using the default helper that make the element looks like static
				var photo = $(this).find('.photo');
				var mode = getLayoutMode();
				if(mode == LAYOUT_MODE.twoColumnHorizon){
					var max_draggable_distance = photo.height() - 256;
					if(mouseY - startDragY > 0){
						//down
						var current_margin_top = parseInt(photo.css('margin-top'));
						if(current_margin_top < 0){
							current_margin_top +=4;
							current_margin_top = (current_margin_top > 0)?0:current_margin_top;
							photo.animate({'margin-top': current_margin_top+'px'}, 0);
						}
					}else{
						var current_margin_top = parseInt(photo.css('margin-top'));
						if( current_margin_top > -max_draggable_distance  ){
							current_margin_top -=4;
							current_margin_top = (current_margin_top < -max_draggable_distance)?-max_draggable_distance:current_margin_top;
							photo.animate({'margin-top': current_margin_top+'px'}, 0);
						}	
					}
				}else if(mode == LAYOUT_MODE.twoColumnVertical){
					var max_draggable_distance = photo.width() - 256;
					if(mouseX - startDragX > 0){
						//down
						var current_margin_top = parseInt(photo.css('margin-left'));
						if(current_margin_top < 0){
							current_margin_top +=4;
							current_margin_top = (current_margin_top > 0)?0:current_margin_top;
							photo.animate({'margin-left': current_margin_top+'px'}, 0);
						}
					}else{
						var current_margin_top = parseInt(photo.css('margin-left'));
						if( current_margin_top > -max_draggable_distance  ){
							current_margin_top -=4;
							current_margin_top = (current_margin_top < -max_draggable_distance)?-max_draggable_distance:current_margin_top;
							photo.animate({'margin-left': current_margin_top+'px'}, 0);
						}	
					}
				}
			}else{	
				//when the cursor move out of the original container, make the helper becomes original that make it look it's moving
				
				//$(this).addClass('hdn');
				$(this).draggable( "option", "helper", "original" );
			}
		},
		stop:function(event,ui){
					
		}
	});
	
	
	$('.photo-segment-container').droppable({
		accept:'.photo-segment',
		tolerance:'pointer',
		over:function(event, ui){
			var mode = getLayoutMode();
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
				if(mode == LAYOUT_MODE.twoColumnHorizon){
					var slope = (stopDragY - startDragY)/(stopDragX - startDragX);
					if( -TWO_COLUMN_HORIZON_ANGLE_SLOPE < slope && slope < TWO_COLUMN_HORIZON_ANGLE_SLOPE ){
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
				else if(mode == LAYOUT_MODE.twoColumnVertical){
					var slope = (stopDragY - startDragY)/(stopDragX - startDragX);
					if( -TWO_COLUMN_VERTICAL_ANGLE_SLOPE < slope && slope < TWO_COLUMN_VERTICAL_ANGLE_SLOPE){
						title_text.text('Swap');
						swap = true;
					}else{
						drag_down = (stopDragY - startDragY > 0);
						drag_left = (drag_down) ? (slope > 0?false:true) : (slope > 0?true:false);
						if(drag_down){
							title_text.text('Drag to bottom');	
						}else{
							title_text.text('Drag to top');	
						}
						swap = false;	
					}
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
					//swap 
					var src_content = src_container.find('.swap-content-container');
					src_container.find('.photo-segment').html(des_container.find('.swap-content-container'));
					des_container.find('.photo-segment').html(src_content);
					
				}
				
				var currentMode = getLayoutMode();
				if(!swap){
					if( currentMode == LAYOUT_MODE.twoColumnHorizon){
						src_container.removeClass('two-column-horizon').addClass('two-column-vertical');
						des_container.removeClass('two-column-horizon').addClass('two-column-vertical');
						src_target.draggable('option','axis','x');
						des_target.draggable('option','axis','x');
						setLayoutMode(LAYOUT_MODE.twoColumnVertical);
					}else{
						src_container.removeClass('two-column-vertical').addClass('two-column-horizon');
						des_container.removeClass('two-column-vertical').addClass('two-column-horizon');
						src_target.draggable('option','axis','y');
						des_target.draggable('option','axis','y');
						setLayoutMode(LAYOUT_MODE.twoColumnHorizon);
					}
				}
				var photo_segment = $('#layout-draggable .photo-segment');
				photo_segment.css({'left':'0px', 'top':'0px', 'right':'0px', 'bottom':'0px'});
				photo_segment.find('.photo').css('margin', '0px');
				
				var src_photo = src_container.find('.photo');
				src_photo.attr('data-width', src_photo.width());
				src_photo.attr('data-height', src_photo.height());
		
				var des_photo = des_container.find('.photo');
				des_photo.attr('data-width', des_photo.width());
				des_photo.attr('data-height', des_photo.height());
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
				segue_wrapper.css('position','relative');
				var wrapper_width = segue_wrapper.width();
				var segue_main = segue_wrapper.find('.segue-main').removeClass('act');
				var post_layout_segue = $('#post-photo-layout-segue');
				var layout_body = post_layout_segue.find('.photo-layout-body');
				if(isPostPhotoModified()){
					resetTwoColumnLayout();
					banner.find('.photo-thumbnail').each(function(index){
						var container = layout_body.find('.photo-segment-container[data-container-queue="'+(index+1)+'"]');
						container.removeClass('hdn');
						var photo = container.find('.photo').attr('src', $(this).attr('src'));
						photo.attr('data-width', photo.width());
						photo.attr('data-height', photo.height());
						
					});
					unsetPostPhotoModified();
				}
			
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
	
	

	
	
	
	$('#add-scene-dialog-wrapper').on({
		click:function(){
			var formData = new FormData();
			var layout_mode = getLayoutMode();
			var layout_draggable = $('#layout-draggable');
			if(layout_mode == LAYOUT_MODE.twoColumnHorizon){
				layout_draggable.find('.photo-segment-container').each(function(index){
					var $this = $(this);
					var $input = $this.find('input.attach-post-photo');
					if($input.hasClass('set') && $input.get(0).files.length > 0){
						formData.append('file_'+index, $input.get(0).files[0]);
						var $photo = $this.find('.photo');
						var adjusted_margin = getIntValueFromCSSStyle($photo.css('margin-top'));
						var image_height = getIntValueFromCSSStyle($photo.attr('data-height'));
						var adjusted_margin_ratio = adjusted_margin/image_height;
						var image_container_scale = $this.height()/image_height;
						var ratio = 'image_container_scale='+image_container_scale+'&adjusted_margin_ratio='+adjusted_margin_ratio;
						formData.append('ratio_'+index, ratio);
					}
				});
			}else if(layout_mode == LAYOUT_MODE.twoColumnVertical){
				layout_draggable.find('.photo-segment-container').each(function(index){
					var $this = $(this);
					var $input = $this.find('input.attach-post-photo');
					if($input.hasClass('set') && $input.get(0).files.length > 0){
						formData.append('file_'+index, $input.get(0).files[0]);
						var $photo = $this.find('.photo');
						var adjusted_margin = getIntValueFromCSSStyle($photo.css('margin-left'));
						var image_height = getIntValueFromCSSStyle($photo.attr('data-width'));
						var adjusted_margin_ratio = adjusted_margin/image_height;
						var image_container_scale = $this.width()/image_height;
						var ratio = 'image_container_scale='+image_container_scale+'&adjusted_margin_ratio='+adjusted_margin_ratio;
						formData.append('ratio_'+index, ratio);
					}
				});
			}
		
			formData.append('layout_mode', layout_mode);
		
			
			// $.ajax({
// 				url:AJAXDIR+'testCropImage.php',
// 				type:'post',
// 				data:formData,
// 				processData: false, //prevent the data to be transformed into string automatically
//  				contentType: false, //false, tell jquery not to send any content type header
// 				success:function(resp){
// 					console.log(resp);
// 				}
// 			});
			return false;
		}
	
	},'#add-scene-button');
		
	
	$('body').on({
		click:function(){
			closeEditDialog();
		}
	},'#edit-dialog-wrapper #cancel-scene-button');
	
	

});

$(document).keyup(function(e){
	if(e.keyCode == 27){
		closeEditDialog();
	}
});

$(document).click(function(){
		//closeEditDialog(false);
});


$(document).on('mousemove',function(e){
		mouseX = e.pageX;
		mouseY = e.pageY;
		mouseViewPortX = e.clientX;
		mouseViewPortY = e.clientY;
		
});

	
	



