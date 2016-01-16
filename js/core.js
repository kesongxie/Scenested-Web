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
 	var re = /^[^#@]$/;
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

function isShiftKeyPressed(e){
	return e.shiftKey;
}


/*
	 @param thisE
		thisE is the contenteditable DOM itself.
	@param e
		e is the event 
*/

function richilizeText(thisE, e){
	var $this = $(thisE);
	$this.parents('.segue-wrapper').css('height','');
	var old_text = $(this).attr('data-val');
	var text = $this.text(); 
	var caretPos = getCaretCharacterOffsetWithin(thisE);
	var text_length = $this.text().length;
	if(caretPos < text_length){
		$this.attr('data-end','false');
	}else{
		$this.attr('data-end','true');
	}
	if(old_text != text){
		$this.attr('data-val',text);
		text = text.replace(/\n/,'');
		text = text.replace(/ /g, '\u00a0'); //replace white space with &nbsp
		var rich_text = text.replace(/(.?)(#|@)(\w+)(\W?)/g, replacer);
		
		if(isShiftKeyPressed(e)){
			return false;
		}
		var selObj = window.getSelection(); 
		if(selObj.toString().length == 0 ) //if the user is not selecting anything
		{
			if($this.attr('data-end') == 'true'){
				$this.html(rich_text);
				setEndOfContenteditable(thisE);
			} else{
				var caretPos = getCaretCharacterOffsetWithin(thisE); //this is the caret pos regarding the entire node
				$this.html(rich_text);
				setCaretPosition(thisE, caretPos);
			}
		}
	}
}

function getCaretCharacterOffsetWithin(element){
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
					readURL(fileInput, photo_thumb, true);
					thumb_wrapper.removeClass('pending');
					$this.removeClass('next').addClass('set');
					var next_input_will_load = label.find('input.attach-post-photo').not('.set').first();
					if(next_input_will_load.length > 0){
						next_input_will_load.addClass('next');
						var next_id =next_input_will_load.attr('id');
						label.attr('for',next_id);
					}
				}
		});
		setPostPhotoModified();
	}



function getIntValueFromCSSStyle(style){
	return parseInt(style.replace('px',''));
}


/*
	resize two images to fit in a container with specific width, and 
	resize two images with same height.
	@param width1
		   the width of the first image
	@param height1
		   the height of the first image
	@param width2
		   the width of the second image
	@param height2
		   the height of the second image
	@param container_width
		   the width of the container the fits these two images
*/
function getWidthsPercentageWithEqualHeightOfTwoImages(width1, height1, width2, height2, container_width){
	var resized_width1;
	resized_width1 = (height2 * container_width )/width2
	resized_width1 /= height1/width1 + height2/width2;
	var resized_width1_percentage = resized_width1/container_width;
	var resized_width2_percentage = 1 - resized_width1_percentage;
	return {"resized_width1_percentage":resized_width1_percentage, "resized_width2_percentage":resized_width2_percentage}

}




$(document).ready(function(){
	
	// var pics = $('.post-image-picture');
// 	var img_wrapper_1 = pics.find('.post-attached-img-wrapper').first();
// 	var img_wrapper_2 = pics.find('.post-attached-img-wrapper').last()
// 	
// 	var width1, width2, height1, height2, container_width = 600;
// 	var image_object1 = new Image();
// 	var img_1 = img_wrapper_1.find('.post-attached-img');
// 	
// 	image_object1.src = img_1.attr("src");
// 	image_object1.onload = function() {
// 		width1 = this.width;
// 		height1 = this.height;
// 		
// 		var img_2 = img_wrapper_2.find('.post-attached-img');
// 		var image_object2 = new Image();
// 		image_object2.src = img_2.attr("src");
// 		image_object2.onload = function() {
// 			width2 = this.width;
// 			height2 = this.height;
// 			
// 			
// 			
// 			var percentages = getWidthsPercentageWithEqualHeightOfTwoImages(width1, height1, width2, height2, container_width);
// 			img_wrapper_1.css('width',percentages['resized_width1_percentage']*100+'%' );
// 			img_wrapper_2.css('width',percentages['resized_width2_percentage']*100+'%');
// 		};
// 	};
// 	
	
	
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
	
	
	
	/*
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
	*/
	

	
	
	
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
			}else if(isShiftKeyPressed(e)){
				// console.log('shift key pressed');
			}
			
		},
		keypress:function(e){
		 	return e.which != 13; //disable return key press
		},
		keyup:function(e){
			richilizeText(this, e);
		},
		paste:function(e){
		  	e.preventDefault();
		  	console.log('fire');
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
		focus:function(e){
			setEndOfContenteditable(this);
			richilizeText(this, e);
		},
		drop:function(e){
			e.preventDefault();
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
			var input_will_be_load = label.find('input.attach-post-photo.set').last();
			if(input_will_be_load.hasClass('set')){
				input_will_be_load.removeClass('set').addClass('next'); //mark as next, so that is available 
			}
			label.attr('for',input_will_be_load.attr('id')); //the id should match the new available input
			input_will_be_load.val(''); //reset the input value
			var input_to_be_removed = input_will_be_load.next();
			if(input_to_be_removed.hasClass('next')){
				input_to_be_removed.removeClass('next');
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
	
	$('#add-scene-dialog-wrapper').on({
		click:function(){
			var formData = new FormData();
			var input_set = $('#attach-post-photo-label .attach-post-photo.set');
			var input_length = input_set.length;
			if(input_length > 0 && input_length < 5){
				input_set.each(function(index){
					if(this.files && this.files.length > 0){
						formData.append('file_'+index, this.files[0]);
					}
				});
			}
			formData.append('file_length',input_length);
			$.ajax({
				url:AJAXDIR+'testCropImage.php',
				type:'post',
				data:formData,
				processData: false, //prevent the data to be transformed into string automatically
 				contentType: false, //false, tell jquery not to send any content type header
				success:function(resp){
					console.log(resp);
				}
			});
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

	
	



