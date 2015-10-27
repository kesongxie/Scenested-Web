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
						$('#search-account').remove();
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
			var reset_option = $('#reset-option');	
			var reset_sent = $('#reset-sent');
			$.ajax({
				url:AJAX_DIR+'reset_password_email.php',
				method:'post',
				data:{key:key},
				success:function(resp){
					if(resp != '1'){
						$('#reset-box .bar-title').text('Reset email sent');
						reset_sent.find('.target-address').text(reset_option.find('.target-address').text());
						reset_sent.removeClass('hdn');
						reset_option.remove();
					}
				}
			});
			
		}
	},'#reset-option .continue');
	
	
	
	$('body').on({
		click:function(){
			var password_input = $('#reset-password-input');
			var reset_password_input = $('#reset-password-input-repeat');
			
			var p = password_input.val();
			var r_p = reset_password_input.val();
			
			if(!checkPassword(p)){
				showPopOverDialog(password_input, password_input.parents('#reset-password'), "Strong passwords combine numbers, letters, punctuations and at least six characters")
				return false;
			}
			
			if(p != r_p){
				showPopOverDialog(reset_password_input, password_input.parents('#reset-password'), "The two passwords don't match")
				return false;
			}
			key =  $(this).attr('data-key');
			$.ajax({
				url:AJAX_DIR+'reset_password.php',
				method:'post',
				data:{key:key, p:p},
				success:function(resp){
					if(resp == '1'){
						showPopOverDialog(password_input, password_input.parents('#reset-password'), "Strong passwords combine numbers, letters, punctuations and at least six characters")
					}else{
						//succeed
						$('#reset-password').remove();
						$('#reset-password-succeed').removeClass('hdn');
					}
				}
			
			})
		}
	},'#reset-password .submit')
	
	
});