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
	//������� ��������� ������������� ����� � ���������� $login, ���� �� ������, �� ���������� ����������
	if (isset($_POST['password'])) 
	{ 
		$password=$_POST['password']; 
		if ($password =='') 
		{ 
			unset($password);
		} 
	}
	//������� ��������� ������������� ������ � ���������� $password, ���� �� ������, �� ���������� ����������
	 
	if (empty($login) or empty($password)) //���� ������������ �� ���� ����� ��� ������, �� ����� ������ � ������������� ���������� �������
	{
		header('Refresh: 5; URL=http://'.$_SERVER["HTTP_HOST"].'/socket/index.php'); 
			exit ("<br /><br />�� ����� �� ��� ����������, ��������� ����� � ��������� ��� ����!<br/>�� ������ �������������� �� ������� �������� ����� 5 ������.");
	}
	 
	$login = stripslashes($login);//������� ������������� ��������, ������������� �������� addslashes()
	 
	$login = htmlspecialchars($login);//����������� ����������� ������� � HTML-�������� (������������ ��, ����� ���� � ������� �� �������� �� ������ �� �������� �������-��������)
	 
	$password = stripslashes($password); //������� ������������� ��������, ������������� �������� addslashes()
	$password = htmlspecialchars($password);
	
	$login = trim($login);//������� ������� (��� ������ �������) �� ������ � ����� ������
	
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
		exit ("<br /><br />��������, �������� ���� login ��� ������ ��������!<br/>�� ������ �������������� �� ������� �������� ����� 5 ������.");
	}
	
	else 
	{
		if ($myrow['users_password'] == md5( $password ))
		{
		
			$_SESSION['login']=$myrow['users_name'];
			$_SESSION['id']=$myrow['users_id'];
			
			header('Refresh: 5; URL=http://'.$_SERVER["HTTP_HOST"].'/socket/client.php'); //redirect � ���������
			echo '�� ������ �������������� �� ������� �������� ����� 5 ������.'; //����� ���������
			
			echo "<br /><br />�����������! �� ������� ����� �� ����! 
			<br /><a href='client.php'>������� ��������</a>";
		}
		 
		else 
		{
			header('Refresh: 5; URL=http://'.$_SERVER["HTTP_HOST"].'/socket/index.php'); 
			exit ("<br /><br />��������, �������� ���� login ��� ������ ��������!<br/>�� ������ �������������� �� ������� �������� ����� 5 ������.");
		}
	}
	session_write_close();
?>