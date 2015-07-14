function prepare(){
	var q = $('#search-submit-form input').val();
	if(q.trim() != ''){
		prepareAjaxSearchResult(q, false);
	}else{
		clearAjaxSearchResult();
	}
}

$(document).ready(function(){
	$('body').on({
	mouseover:function(){
		$(this).css({'color':'rgb(88, 86, 86)','border-bottom':'3px solid rgb(10, 43, 138)', 'border-radius': '0px'});
	},
	mouseleave:function(){
		$(this).css({'color':'rgb(135, 135, 135)','border-bottom':'0px'});
	}
	},'.deactive-navi');
	
	
	
	

});
