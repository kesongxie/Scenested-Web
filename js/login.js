var SIGNUP_ERROR = '';
/*Alert Messages for sign up form, keys are the input elements' ids, values are the messages*/
SIGNUP_ALERT_MESSAGE["signup-iden"] = "Please enter your email address";
SIGNUP_ALERT_MESSAGE["signup-re-iden"] = "The emails you entered don't match";
SIGNUP_ALERT_MESSAGE["signup-firstname"] = "Please enter your first name";
SIGNUP_ALERT_MESSAGE["signup-lastname"] = "Please enter your last name";
SIGNUP_ALERT_MESSAGE["signup-password"] = "Please create your password";
SIGNUP_ALERT_MESSAGE["signup-re-password"] = "The passwords you entered don't match";



$(document).ready(function(){
	$('body').on({
		click:function(){
			$('#login-form').addClass('hdn');
			$('#signup-form').removeClass('hdn');
		}
	},'#prompt-sign-up, #prompt-log-in-header-top');
	
	$('body').on({
		click:function(){
			$('#signup-form').addClass('hdn');
			$('#signup-form .sign-up-alert').remove();
			$('#login-form').removeClass('hdn');
		}
	},'#prompt-log-in, #prompt-sign-up-header-top');
	
	$('#login-data-form').on("submit",function(event){
		event.preventDefault();
		var data = $(this).serialize();
		var thisE = $(this);
		$.ajax({
			url: AJAX_DIR + 'login.php',
			method: 'post',
			data:{data:data},
			success:function(resp){
				console.log(resp);
				if(resp == '0'){
					window.location.href = INDEX_PAGE;
				}else{
					thisE.css('-webkit-animation',"shake 0.3s").css('animation',"shake 0.3s");
					setTimeout(function(){
						thisE.css('-webkit-animation',"").css('animation',"");
					},1000);
				}
			}
		});
	});
	
	
	
	
	
	$('#signup-data-form').on("submit",function(event){
		event.preventDefault();
		var data = $(this).serialize();
		var loadingDiv = $('#signup-form .loading-container-wrapper');
		loadingDiv.removeClass('hdn');
		$.ajax({
			url: AJAX_DIR+ 'signup.php',
			method: 'post',
			data:{data:data},
			success:function(resp){
				loadingDiv.addClass('hdn');
				$('#signup-data-form .sign-up-alert').remove();
				if(resp == '0'){
					//animate successfully sign up
					$('#signup_succeed #signup-success-on').html($('#signup-iden').val().trim().toLowerCase()).removeClass('hdn');
					$('#signup-body-footer-wrapper').remove();
				}else{
					switch (resp){
						case '1': $('#signup-iden').parent('div').prepend('<span class="sign-up-alert">Please enter your email address</span>');break;
						case '2': $('#signup-iden').parent('div').prepend('<span class="sign-up-alert">The email address you entered seems invalid');break;
						case '3': $('#signup-iden').parent('div').prepend('<span class="sign-up-alert">The email address you entered has been used</span>');break;
						case '4': $('#signup-re-iden').parent('div').prepend('<span class="sign-up-alert">The emails you entered don\'t match</span>');break;
						case '5': $('#signup-firstname').parent('div').prepend('<span class="sign-up-alert">Please enter your first name</span>');break;
						case '6': $('#signup-lastname').parent('div').prepend('<span class="sign-up-alert">Please enter your last name</span>');break;
						case '7': $('#signup-password').parent('div').prepend('<span class="sign-up-alert">Please create your password</span>');break;
						case '8': $('#signup-password').parent('div').prepend('<span class="sign-up-alert">The password should be at least 6 characters</span>');break;
						case '9': $('#signup-re-password').parent('div').prepend('<span class="sign-up-alert">The passwords you entered don\'t match</span>');break;
						case '10': $('#signup-gender').parent('div').prepend('<span class="sign-up-alert">Please select your gender</span>');break;
						default:break;
					}
				}
			}
		});
	});
	
	$('#signup-form-body').on({
		focus:function(){
			$(this).parent('div').find('.sign-up-alert').hide();
		},
		blur:function(){
			var fieldId = $(this).attr('id');
			var thisE = $(this);
			if(thisE.val().trim() == ''){
				if(fieldId == "signup-re-iden" && $('#signup-iden').val().trim() == ''){
					//if the previous sign up iden is empty, display that one instead of saying mismatch
					fieldId = "signup-iden";
					thisE = $('#signup-iden');
				}
				if(fieldId == "signup-re-password" && $('#signup-re-password').val().trim() == ''){
					fieldId = "signup-password";
					thisE = $('#signup-password');
				}
				//check whether the there is any signuqp-alert already
				var parentDiv = thisE.parent('div');
				if(parentDiv.find('.sign-up-alert').length < 1){
					$('#signup-field-wrapper .sign-up-alert').remove();
					//prepend
					parentDiv.prepend('<span class="sign-up-alert">'+ SIGNUP_ALERT_MESSAGE[fieldId] +'</span>');
				}
			}else{
				var parentDiv = $(this).parent('div');
				var errorMessage = '';
				var valid = false;
				if(fieldId == "signup-iden"){
					//check whether the email is valid or not
					var email = thisE.val();
					$.ajax({
						url: AJAX_DIR + 'validate_email.php',
						method: 'post',
						data: {email:email},
						success:function(resp){
							if(resp != '0'){
								if(resp == '1' ){
									$('#signup-field-wrapper .sign-up-alert').remove();
									parentDiv.prepend('<span class="sign-up-alert">The email address you entered seems invalid</span>');

								}else if(resp == '2'){
									$('#signup-field-wrapper .sign-up-alert').remove();
									parentDiv.prepend('<span class="sign-up-alert">The email address you entered has been used</span>');
								}
							}
						}
					});
				}
				else if(fieldId == 'signup-re-iden'){
					if($(this).val().trim().toLowerCase() != $('#signup-iden').val().trim().toLowerCase() ){
						$('#signup-field-wrapper .sign-up-alert').remove();
						errorMessage = "The emails you entered don't match";
					}
				}
				else if(fieldId == 'signup-password'){
					if($(this).val().length < 6){
						$('#signup-field-wrapper .sign-up-alert').remove();
						errorMessage = "The password should be at least 6 characters"
					}
				}
				else if(fieldId == 'signup-re-password'){
					if($(this).val() != $('#signup-password').val() ){
						$('#signup-field-wrapper .sign-up-alert').remove();
						errorMessage = "The passwords you entered don't match";
					}
				}
				if(errorMessage != ''){
					parentDiv.prepend('<span class="sign-up-alert">'+ errorMessage +'</span>');
				}
			}
		}
	},'.text');
	
	

	
	
	
	
});