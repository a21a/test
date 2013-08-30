<?php

include 'class_db_MYSQL.php';
$db;


class Server 
{
	
	static private $SessionUserLogin;
	static private $SessionChannelID;
	static private $SessionUserID;
	
	static private $actions;
	
	
	static function Run($user_login,$user_id,$channel_id) 
	{
		
		self::$SessionChannelID=$channel_id;
		self::$SessionUserLogin=$user_login;
		self::$SessionUserID=$user_id;
		
		foreach (glob('sockets/*') as $sock) 
		{
			if (filesize($sock) > 2048) 
			{
				unlink($sock);
			}
		}
		$action = 'action'.$_POST['action'];
		if (is_callable('self::'.$action)) 
		{
			self::$action();
			self::Send();
		}
	}
	
	/*
	 * Эта функция пишет действие в соккеты.
	 * Если передан параметр $self, то исключает указанный в этом параметре соккет.
	 */
	static function AddToSock($action, $params = '', $self = null) {
		foreach (glob('sockets/*') as $sock) {
			if ($self && strpos($sock, $self) !== false) {
				continue;
			}
			$f = fopen($sock, 'a+b') or die('socket not found');
			flock($f, LOCK_EX);
			fwrite($f, '{action: "'.$action.'", params: {'.$params.'}}'."\r\n");
			fclose($f);
		}
	}
	
	/*
	 * Эта функция добавляет действие в стек для отправки в текущем запросе.
	 */
	static function AddToSend($action, $params = '') {
		self::$actions[] = '{action: "'.$action.'", params: {'.$params.'}}';
	}
	
	/*
	 * Отправка стека действий на выполнение клиенту.
	 */
	static function Send() {
		if (self::$actions) {
			exit('{actions: ['.implode(', ', self::$actions).']}');
		}
	}
	
	/*
	 * Действие.
	 * Соединение с сервером.
	 * Создает соккет и отправляет его идентификатор клиенту.
	 */
	static function actionConnect() 
	{
		$sock = md5(microtime().rand(1, 1000));
		fclose(fopen('sockets/'.$sock, 'a+b'));
		self::AddToSock('Print', 'message: "Client '.self::$SessionUserLogin.' connected."', $sock);
		self::AddToSend('Connect', 'sock: "'.$sock.'"');
	}
	
	/*
	 * Действие.
	 * Отсоединение от сервера.
	 * Удаляет соккет.
	 */
	static function actionDisconnect() 
	{
		$sock = $_POST['sock'];
		unlink('sockets/'.$sock);
		self::AddToSock('Print', 'message: "Client disconnected."');
		self::AddToSend('Disconnect');
	}
	
	/*
	 * Действие.
	 * Отправляет введенные данные всем клиентам.
	 */
	static function actionSend() {
		global $db;
		$sock = $_POST['sock'];
		$data = htmlspecialchars(trim($_POST['data']), ENT_QUOTES);
		if (strlen($data)) 
		{
			$db->query("INSERT INTO `chat_messages`( `user_id`, `channel_id`, `text`, `time_written`, `hide`) VALUES (".self::$SessionUserID.",".self::$SessionChannelID.",'".$data."',NOW(),0)");
			self::AddToSock('Print', 'message: "'.self::$SessionUserLogin." : ".$data.'"', $sock);
			self::AddToSend('Print', 'message: "'.self::$SessionUserLogin." : ".$data.'"');
		}
	}
	
	/*
	 * Действие.
	 * Слушает соккет до момента когда в нем появятся данные или же до истечения таймаута.
	 */
	static function actionRead() 
	{
		$sock = $_POST['sock'];
		$time = time();
		while ((time() - $time) < 30) 
		{
			if ($data = file_get_contents('sockets/'.$sock)) 
			{
				$f = fopen('sockets/'.$sock, 'r+b') or die('socket not found');
				ftruncate($f, 0);
				flock($f, LOCK_EX);
				fwrite($f, '');
				fclose($f);
				$data = trim($data, "\r\n");
				foreach (explode("\r\n", $data) as $action) 
				{
					self::$actions[] = $action;
				}
				self::Send();
			}
			usleep(250);
		}
	}
	
}

session_start();

if(!empty($_SESSION['login']) and !empty($_SESSION['id']))
{
	$v_login=$_SESSION['login'];
	$v_ID=$_SESSION['id'];
	$v_channelID=$_SESSION['channel_id'];
	session_write_close();

	$db = new db_MYSQL(array('root' => $user, '' => $pass,'chat2' => $db, 'charset' => 'utf8'));
	
	
	if (@$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') 
	{
			header('content-type: text/plain; charset=utf-8');
			Server::Run($v_login,$v_ID,$v_channelID);
	}
	
}

session_write_close();
?>