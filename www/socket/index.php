<?php // ��������� ����� � php-������ ��� ����������� ���������� ������� ����� � ���� �����
 
//  ��� ��������� �������� �� �������. � ������ �������� ������  ������������, ���� �� ��������� �� �����. ����������� ������ � ������ ���������
	session_start();
	echo "
	<html>
	<head>
	<link rel='stylesheet' type='text/css' href='style.css' />
	<title>����� ����� �� PHP</title>
	</head><body>";

	if(isset($_SESSION['login']))
	{
		$login='������������, '.$_SESSION['login'].'!';
	}
	// ���������, ����� �� ���������� ������ � id ������������
	if (empty($_SESSION['login']) or empty($_SESSION['id']))// ���� �����, ��
	{
		echo 
		"<form id='forma' action='loginChecker.php' method='post'>
			<div style='width:300px;position:relative;margin:0 auto;border:2px solid blue;padding:15px;margin-top:200px;'>
				<div style='height: 30px;width: 300px;font-size: 15pt;text-align: center;display: inline-block;font-weight: bold;'>
					���� �� ����
				</div>
				<div style='width:300px;height:30px;'>
					<div style='width: 90px;height: 30px;display: inline-block;float: left;padding-right: 10px;text-align:right;'>�����</div>
					<div><input type='text' name='login'></div>
				</div>
				<div style='width:300px;'>
					<div style='width: 90px;height: 30px;display: inline-block;float: left;padding-right: 10px;text-align:right;'>������</div>
					<div><input type='password' name='password'></div>
				</div>
				<div style='height:30px;width:300px;display:inline; text-align:center;'>
					<input type='submit' name='submit' style='width:154px;' value='�����'> <br>
				</div>
			</div>
			</form>";
	}
	else// ���� �� �����, ��
	{
		echo "<br /><br />�� ����� �� ����, ��� ".$_SESSION['login']."<br><br />";
		echo ('<a href="client.php">�� ����</a>');
		echo ('<form action="close.php" method="POST">
					<input type="submit" value="�����"/>
				</form>');
	}
	echo"
	</body></html>";
	session_write_close();
?>