<?php



	session_start(); 
	if (isset($_POST['login']))
	{ 
		$login = $_POST['login']; 
		if ($login == '') 
		{ 
			unset($login);
		} 
	} 
	//заносим введенный пользователем логин в переменную $login, если он пустой, то уничтожаем переменную
	if (isset($_POST['password'])) 
	{ 
		$password=$_POST['password']; 
		if ($password =='') 
		{ 
			unset($password);
		} 
	}
	//заносим введенный пользователем пароль в переменную $password, если он пустой, то уничтожаем переменную
	 
	if (empty($login) or empty($password)) //если пользователь не ввел логин или пароль, то выдаём ошибку и останавливаем выполнение скрипта
	{
		header('Refresh: 5; URL=http://'.$_SERVER["HTTP_HOST"].'/socket/index.php'); 
			exit ("<br /><br />Вы ввели не всю информацию, вернитесь назад и заполните все поля!<br/>Вы будете перенаправлены на главную страницу через 5 секунд.");
	}
	 
	$login = stripslashes($login);//удаляет экранирование символов, произведенное функцией addslashes()
	 
	$login = htmlspecialchars($login);//преобразует специальные символы в HTML-сущности (обрабатываем их, чтобы теги и скрипты не работали на случай от действий умников-спамеров)
	 
	$password = stripslashes($password); //удаляет экранирование символов, произведенное функцией addslashes()
	$password = htmlspecialchars($password);
	
	$login = trim($login);//удаляет пробелы (или другие символы) из начала и конца строки
	
	$password = trim($password);
	
	$db_host = 'localhost';
	$db_user = 'root';
	$db_password = '';
	$database = 'chat2';

	mysql_connect($db_host, $db_user, $db_password);
	mysql_select_db($database);
	
	
	
	$result = mysql_query("SELECT * FROM `chat_users` WHERE `users_name`='".$login."'");
	$myrow = mysql_fetch_array($result);
	
	
	
	if (empty($myrow['users_password']))
	{
		header('Refresh: 5; URL=http://'.$_SERVER["HTTP_HOST"].'/socket/index.php'); 
		exit ("<br /><br />Извините, введённый вами login или пароль неверный!<br/>Вы будете перенаправлены на главную страницу через 5 секунд.");
	}
	
	else 
	{
		if ($myrow['users_password'] == md5( $password ))
		{
		
			$_SESSION['login']=$myrow['users_name'];
			$_SESSION['id']=$myrow['users_id'];
			
			header('Refresh: 5; URL=http://'.$_SERVER["HTTP_HOST"].'/socket/client.php'); //redirect с задержкой
			echo 'Вы будете перенаправлены на главную страницу через 5 секунд.'; //вывод сообщения
			
			echo "<br /><br />Поздравляем! Вы успешно вошли на сайт! 
			<br /><a href='client.php'>Главная страница</a>";
		}
		 
		else 
		{
			header('Refresh: 5; URL=http://'.$_SERVER["HTTP_HOST"].'/socket/index.php'); 
			exit ("<br /><br />Извините, введённый вами login или пароль неверный!<br/>Вы будете перенаправлены на главную страницу через 5 секунд.");
		}
	}
	session_write_close();
?>