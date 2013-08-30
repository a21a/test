<?php // Заключаем форму в php-скрипт для возможности прописівать скрипты прямо в этом файле
 
//  вся процедура работает на сессиях. В сессии хранятся данные  пользователя, пока он находится на сайте. Запускается сессия в начале странички
	session_start();

	if (empty($_SESSION['login']) or empty($_SESSION['id']))// Если пусты, то
	{
		header('URL=http://'.$_SERVER["HTTP_HOST"].'/socket/index.php'); 
	}
	else
	{
		$_SESSION['channel_id']=1;
	}
	session_write_close();
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
	
	<head>
		<title>Socket App Client</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="css/style.css" />
		<style type="text/css">
			
			#log {
				border: 1px solid #999999;
				height: 250px;
				overflow: auto;
				margin: 10px 0;
				padding: 10px;
				width:80%;
				display:inline-block;

			}
			#users_in_room {
				border: 1px solid #999999;
				height: 250px;
				overflow: auto;
				margin: 10px 0;
				padding: 10px;
				width:15%;
				display:inline-block;
			}
			
		</style>
		<script type="text/javascript" src="lib/jquery.js"></script>
		<script type="text/javascript">
			
			$(document).ready(function() {
				
				/*
				 * Настройка AJAX
				 */
				$.ajaxSetup({url: 'server.php', type: 'post', dataType: 'json'});
				
				/*
				 * События кнопок и поля ввода
				 */
				$('#btnConnect').click(user.Connect);
				$('#btnDisconnect').click(user.Disconnect);
				$('#btnSend').click(user.Send);
				$('#btnClear').click(user.Clear);
				$('#input')
					.keydown(function(e){ if (e.keyCode == 13) { user.Send(); return false; } })
					.keypress(function(e){ if (e.keyCode == 13) { return false; } })
					.keyup(function(e){ if (e.keyCode == 13) { return false; } })
				;
				
			});
			
		</script>
		<script type="text/javascript">
			
			/*
			 * Лог
			 */
			var log = {
				
				print: function(s) 
				{
					$('#log').append('<div>'+s+'</div>').get(0).scrollTop += 100;
				},
				clear:function()
				{
					
					$('#log').empty();
				}
			};
			
			/*
			 * Действия присылаемые с сервера
			 */
			var actions = {
				
				Connect: function(params) {
					log.print('Connected.');
					log.print('Sock: '+params.sock);
					user.sock = params.sock;
					user.conn = true;
					user.Read();
				},
				
				Disconnect: function(params) {
					log.print('Disconnected.');
				},
				
				Print: function(params) {
					log.print(params.message);
				}
				
			};
			
			/*
			 * Пользователь (клиент)
			 */
			var user = {
				
				sock: null,
				
				conn: false,
				
				busy: false,
				
				read: null,
				
				/*
				 * Эта функция обрабатывает приходящие с сервера действия и выполняет их.
				 */
				onSuccess: function(data) {
					if (typeof data.actions == 'object') {
						for (var i = 0; i < data.actions.length; i++) {
							if (typeof actions[data.actions[i].action] == 'function') {
								actions[data.actions[i].action](data.actions[i].params);
							}
						}
					}
				},
				
				/*
				 * Эта функция выполняется по завершении ajax-запроса.
				 */
				onComplete: function(xhr) {
					if (xhr.status == 404) {
						actions.Disconnect();
					}
					user.busy = false;
				},
				
				/*
				 * Эта функция выполняется по завершении запроса-слушания.
				 * При удачном завершении запроса (==200) моментальное возобновление прослушивания соккета.
				 * При неудачном (!=200) возобновление через 5 секунд.
				 */
				onCompleteRead: function(xhr) {
					if (xhr.status == 200) {
						user.Read();
					} else {
						setTimeout(user.Read, 5000);
					}
				},
				
				/*
				 * Действие.
				 * Соединение с сервером.
				 */
				Connect: function() {
					if (user.conn == false && user.busy == false) {
						log.print('Connecting...');
						user.busy = true;
						$.ajax({
							data: 'action=Connect',
							success: user.onSuccess,
							complete: user.onComplete
						});
					}
				},
				
				
				
				
				Clear: function()
				{
					log.clear();
				},
				
				
				
				
				/*
				 * Действие.
				 * Отсоединение от сервера.
				 */
				 
				 
				 
				Disconnect: function() {
					if (user.conn && user.busy == false && user.read) {
						log.print('Disconnecting...');
						user.busy = true;
						$.ajax({
							data: 'action=Disconnect&sock='+user.sock,
							success: user.onSuccess,
							complete: user.onComplete
						});
						user.sock = null;
						user.conn = false;
						user.read.abort();
					}
				},
				
				/*
				 * Действие.
				 * Отправка данных на сервер.
				 */
				Send: function() {
					if (user.conn) {
						var data = $.trim($('#input').val());
						if (!data) {
							return;
						}
						$.ajax({
							data: 'action=Send&sock='+user.sock+'&data='+data,
							success: user.onSuccess,
							complete: user.onComplete
						});
						$('#input').val('');
					} else {
						log.print('Please connect.');
					}
				},
				
				/*
				 * Действие.
				 * Прослушивание соккета.
				 */
				Read: function() {
					if (user.conn) {
						user.read = $.ajax({
							data: 'action=Read&sock='+user.sock,
							success: user.onSuccess,
							complete: user.onCompleteRead
						});
					}
				}
				
			};
			
		</script>
	</head>
	
	<body>
		

			<input id="btnConnect" type="button" value="Connect" />
			<input id="btnDisconnect" type="button" value="Disconnect" />
			<input id="btnClear" type="button" value="Clear" />
			<div>
				<div id="log"></div>
				<div id="users_in_room"></div>
			</div>
			<input id="input" type="text" />
			<input id="btnSend" type="button" value="Send" />
	</body>
	
</html>