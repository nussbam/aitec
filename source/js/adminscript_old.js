$(document).ready(function(){
	
	// Run the init method on document ready:
	chat.init();
	
});

var chat = {
	
	// data holds variables for use in the class:
	
	data : {
		lastID 		: 0,
		noActivity	: 0
	},
	
	// Init binds event listeners and sets up timers:
	
	init : function(){
		
		// Using the defaultText jQuery plugin, included at the bottom:
		$('#name').defaultText('Nickname');
		$('#email').defaultText('Email (Gravatars are Enabled)');
		
		// Converting the #chatLineHolder div into a jScrollPane,
		// and saving the plugin's API in chat.data:
		

		
		// We use the working variable to prevent
		// multiple form submissions:
		
		var working = false;
		
		// Logging a person in the chat:
		
		$('#loginForm').submit(function(){
			
			if(working) return false;
			working = true;
			
			// Using our chatPOST wrapper function
			// (defined in the bottom):
			
			$.chatPOST('loginAdmin',$(this).serialize(),function(r){
				working = false;
				
				if(r.error){
					chat.displayError(r.error);
				}
				else chat.login(r.name,r.gravatar);
			});
			
			return false;
		});



		

		
		// Logging the user in

		
		$('a.logoutButton').live('click',function(){

			//$('#chatTopBar').html(chat.render('loginTopBar',chat.data));

			$('#submitForm').fadeOut(function(){
				$('#loginForm').fadeIn();
				$('#registerForm').fadeIn();
			});


			$('#logoutHolder > span').fadeOut(function(){
				$('#loginForm').fadeIn();
				$(this).remove();
			});
			

			
			$.chatPOST('logout');


			
			return false;
		});
		
		// Checking whether the user is already logged (browser refresh)
		
		/*TODO add a check so the admin doesn't has to login after a form switch
		$.chatGET('checkAdminLogged',function(r){
			if(r.logged){
				chat.login(r.loggedAs.name,r.loggedAs.gravatar);
			}
		});*/
		
		// Self executing timeout functions
		
		(function getChatsTimeoutFunction(){
			chat.getChats(getChatsTimeoutFunction);
		})();
		
		(function getUsersTimeoutFunction(){
			chat.getUsers(getUsersTimeoutFunction);
		})();

		(function getCRUDUsersTimeoutFunction(){
			chat.getCRUDUsers(getCRUDUsersTimeoutFunction);
		})();
		
	},
	
	// The login method hides displays the
	// user's login data and shows the submit form
	
	login : function(name,gravatar){
		
		chat.data.name = name;
		chat.data.name = name;
		chat.data.gravatar = gravatar;
		$('#logoutHolder').html(chat.render('logoutTopBar',chat.data));
		
		$('#loginForm').fadeOut(function(){
			$('#submitForm').fadeIn();
			$('#logoutHolder').fadeIn();
			$('#chatText').focus();
		});

		$('#registerForm').fadeOut(function(){
			$('#submitForm').fadeIn();
			$('#logoutHolder').fadeIn();
			$('#chatText').focus();
		});
		
	},
	
	// The render method generates the HTML markup 
	// that is needed by the other methods:
	
	render : function(template,params){
		
		var arr = [];
		switch(template){

			case 'loginTopBar':
				arr = [
					'<form id="loginForm" method="post" action="">',
					'<input id="login_name" name="name" class="rounded" maxlength="16" />',
						'<input id="login_password" name="password" class="rounded" />',
						'<input type="submit" class="blueButton" value="Login" />',
						'</form>'];
				break;

			case 'logoutTopBar':
				arr = [
				'<span><img src="',params.gravatar,'" width="23" height="23" />',
				'<span class="name">',params.name,
				'</span><a href="" class="logoutButton rounded">Logout</a></span>'];
			break;


			case 'user':
				arr = [
					'<div class="user" title="',params.name,'"><img src="',
					params.gravatar,'" width="30" height="30" onload="this.style.visibility=\'visible\'" /></div>'
				];
			break;

			case 'crudUser':
				arr = [

					'<tr>' +
						'<td>params.id</td>' +
						'<td>params.name</td>' +
						'<td>params.userlevel</td>' +
					'</tr>'

				];
				break;
		}
		
		// A single array join is faster than
		// multiple concatenations
		
		return arr.join('');
		
	},
	

	
	// This method requests the latest chats
	// (since lastID), and adds them to the page.

	
	// Requesting a list with all the users.
	
	getUsers : function(callback){
		$.chatGET('getUsers',function(r){
			
			var users = [];
			
			for(var i=0; i< r.users.length;i++){
				if(r.users[i]){
					users.push(chat.render('user',r.users[i]));
				}
			}
			
			var message = '';
			
			if(r.total<1){
				message = 'No one is online';
			}
			else {
				message = r.total+' '+(r.total == 1 ? 'person':'people')+' online';
			}
			
			users.push('<p class="count">'+message+'</p>');
			
			$('#CRUDUsers').html(users.join(''));
			
			setTimeout(callback,15000);
		});
	},

	getCRUDUsers : function(callback){
		$.chatGET('getCRUDUsers',function(r){

			var crudResult = [];

			crudResult.push('<table>');

			for(var i=0; i< r.result.length;i++){
				if(r.result[i]){
					crudResult.push(chat.render('crudUser',r.result[i]));
				}
			}
			crudResult.push('</table>');



			$('#CRUDUsers').html(crudResult.join(''));

			setTimeout(callback,15000);
		});
	},
	
	// This method displays an error message on the top of the page:
	
	displayError : function(msg){
		var elem = $('<div>',{
			id		: 'chatErrorMessage',
			html	: msg
		});
		
		elem.click(function(){
			$(this).fadeOut(function(){
				$(this).remove();
			});
		});
		
		setTimeout(function(){
			elem.click();
		},5000);
		
		elem.hide().appendTo('body').slideDown();
	}
};

// Custom GET & POST wrappers:

$.chatPOST = function(action,data,callback){
	$.post('php/ajax.php?action='+action,data,callback,'json');
}

$.chatGET = function(action,data,callback){
	$.get('php/ajax.php?action='+action,data,callback,'json');
}

// A custom jQuery method for placeholder text:

$.fn.defaultText = function(value){
	
	var element = this.eq(0);
	element.data('defaultText',value);
	
	element.focus(function(){
		if(element.val() == value){
			element.val('').removeClass('defaultText');
		}
	}).blur(function(){
		if(element.val() == '' || element.val() == value){
			element.addClass('defaultText').val(value);
		}
	});
	
	return element.blur();
}