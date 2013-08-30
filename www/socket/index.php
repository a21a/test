<?php // Заключаем форму в php-скрипт для возможности прописівать скрипты прямо в этом файле
 
//  вся процедура работает на сессиях. В сессии хранятся данные  пользователя, пока он находится на сайте. Запускается сессия в начале странички
	session_start();
	echo "
	<html>
	<head>
	<link rel='stylesheet' type='text/css' href='style.css' />
	<title>Форма входа на PHP</title>
	</head><body>";

	if(isset($_SESSION['login']))
	{
		$login='Здравствуйте, '.$_SESSION['login'].'!';
	}
	// Проверяем, пусты ли переменные логина и id пользователя
	if (empty($_SESSION['login']) or empty($_SESSION['id']))// Если пусты, то
	{
		echo 
		"<form id='forma' action='loginChecker.php' method='post'>
			<div style='width:300px;position:relative;margin:0 auto;border:2px solid blue;padding:15px;margin-top:200px;'>
				<div style='height: 30px;width: 300px;font-size: 15pt;text-align: center;display: inline-block;font-weight: bold;'>
					Вход на сайт
				</div>
				<div style='width:300px;height:30px;'>
					<div style='width: 90px;height: 30px;display: inline-block;float: left;padding-right: 10px;text-align:right;'>Логин</div>
					<div><input type='text' name='login'></div>
				</div>
				<div style='width:300px;'>
					<div style='width: 90px;height: 30px;display: inline-block;float: left;padding-right: 10px;text-align:right;'>Пароль</div>
					<div><input type='password' name='password'></div>
				</div>
				<div style='height:30px;width:300px;display:inline; text-align:center;'>
					<input type='submit' name='submit' style='width:154px;' value='Войти'> <br>
				</div>
			</div>
			</form>";
	}
	else// Если не пусты, то
	{
		echo "<br /><br />Вы вошли на сайт, как ".$_SESSION['login']."<br><br />";
		echo ('<a href="client.php">НА САЙТ</a>');
		echo ('<form action="close.php" method="POST">
					<input type="submit" value="Выход"/>
				</form>');
	}
	echo"
	</body></html>";
	session_write_close();
?>