<?php
session_start();//�������� ������
unset($_SESSION['login']);//�������� ������ �� ������
session_destroy();//�������� ������
header('Refresh: 1; URL=http://'.$_SERVER["HTTP_HOST"].'/socket/index.php'); 
session_write_close();
?>