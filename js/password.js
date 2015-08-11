$(document).ready(function(){
	$('body').on({
		click:function(){
			var thisE = $(this);
			var input = thisE.parents('#search-account').find('input');

			var email = input.val().trim();
			if(email == ''){
				return false;
			}
			$.ajax({
				url:AJAX_DIR+'reset_password_option.php',
				method:'post',
				data:{email:email},
				success:function(resp){
					if(resp != '1'){
						input.val('');
						$('#search-account').addClass('hdn');
						$('#reset-option').html(resp).removeClass('hdn');
						$('#reset-box .bar-title').text('Reset Options');
					}else{
						showPopOverDialog(thisE, thisE.parents('#search-account'), "We can't find an account that links to the email address you just entered")
					}
				}
			});
			
		}
	},'#search-account .continue');
	
	
	
	
	$('body').on({
		click:function(){
			var key = $(this).attr('data-key');			
			$.ajax({
				url:AJAX_DIR+'reset_password_email.php',
				method:'post',
				data:{key:key},
				success:function(resp){
					console.log(resp);
				}
			});
			
		}
	},'#reset-option .continue');
	
	
	
});