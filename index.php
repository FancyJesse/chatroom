<!DOCTYPE HTML>
<html>
<head>
	<title>Chatroom</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta property="og:title" content="Chatroom - Sample Page" />
	<meta property="og:description" content="A Sample Page for Chatroom" />
</head>
<body>
	<table cellspacing="0" cellpadding="10" height="100%" width="100%" id="main-table">
		<tr>
			<table width="100%" id="chat-table" style="display:block; background-color:#f2f2f2; height:400px; overflow-y:scroll"></table>
		</tr>
		<tr>
			<div style="position:relative">
				<div>
					<strong id="notifier">Please register and login to join the chatroom.</strong>
				</div>
				<div id="login-div">
					<input placeholder="username" type="text" id="username" />
					<input placeholder="password" type="password" id="password" />
					<input placeholder="verify password" type="password" id="password-verify" style="visibility:hidden"/>
					<div>
						<input type="button" id="login-button" value="Login" onclick="login()" />
						<input type="button" id="register-button" value="Register" onclick="register()" />
					</div>
				</div>
				<div>
					<textarea placeholder="Message..." disabled="true" id="entry"></textarea>
				</div>
		</tr>
	</table>
	<footer>
		<ul>
			<li>This is a sample page for project <a href="https://github.com/FancyJesse/chatroom">Chatroom</a></li>
			<li>See a live and more decorated sample on <a href="http://chatroom.fancyjesse.com">chatroom.fancyjesse.com</a></li>
		</ul>
	</footer>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script type="text/javascript">
		var user_id = 0;
		var last_message_time = 0;
		var table = document.getElementById('chat-table');
		var notifier = document.getElementById("notifier");
		updateChat();
		setInterval(updateChat, 1000);
		function addNewMessage(username, message, time){
			var newRow = table.insertRow(table.rows.length);
			var userCell = newRow.insertCell(0)
			userCell.innerHTML='<strong title="' + time +' ">' + username + ': </strong>' + message;
		}
		function updateChat(){
			$.ajax({
				type: 'POST',
				url: 'update.php',
				dataType: 'json',
				data: {'last_message_time':last_message_time},
				success: function (data) {
					$.each(data, function(i, message){
						addNewMessage(message.username,message.message,message.time);
						last_message_time = message.time;
					});
				}
			});
		}
		function send_message(message){
			$.post('send.php', { 'user_id':user_id, 'message':message},
				function(data){
				}
			);
		}
		$(function(){
			$("#entry").keyup(function (event) {
				if(event.which == 13) {
					event.preventDefault();
					var data = $(this).val().trim();
					$(this).val('');
					if(data==''){
						return false;
					}
					if(user_id!=0){
						send_message(data);
					}
					return false;
				}
			});
		});
		function verify(){
			var username = document.getElementById('username').value.trim();
			var secret = document.getElementById('password').value.trim();
			if(username==''){
				notifier.innerHTML="Invalid username.";
				return false;
			}
			if(secret==''){
				notifier.innerHTML="Invalid password.";
				return false;
			}
			return true;
		}
		function login(){
			if(verify()){
				var username = document.getElementById('username').value.trim();
				var secret = document.getElementById('password').value.trim();
				$.post('/scripts/login.php', {'username':username, 'secret':secret},
					function(data){
						if(data!=0){
							data = JSON.parse(data);
							user_id = data.user_id;
							notifier.innerHTML="Welcome, " + data.username + ".";
							$("#login-div").hide();
							$("#entry").attr("disabled", false);

						} else {
							notifier.innerHTML="Invalid username or password.";
							user_id=0;
						}
					}
				);
			} else{
				return false;
			}
		}
		function register(){
			if(verify()){
				var username = document.getElementById('username').value.trim();
				var secret = document.getElementById('password').value.trim();
				var secret_verify = document.getElementById('password-verify').value.trim();
				if(secret_verify==''){
					notifier.innerHTML="Please re-enter your password to register.";
					document.getElementById('password-verify').style.visibility="visible";
					return false;
				}
				if(secret!=secret_verify){
					notifier.innerHTML="Passwords do not match.";
					return false;
				}
				$.post('/scripts/register.php', {'username':username, 'secret':secret, 'secret_verify':secret_verify},
					function(data){
						console.log(data);
						if(data!=0){
							data = JSON.parse(data);
							user_id = data.user_id;
							notifier.innerHTML="Welcome, " + data.username + ".";
							$("#login-div").hide();
							$("#entry").attr("disabled", false);
						} else {
							notifier.innerHTML="Failed to register. Username might be taken.";
							user_id=0;
						}
					}
				);
			} else{
				return false;
			}
		}
	</script>
</body>
</html>
