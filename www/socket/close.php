<?php
session_start();//открытие сессии
unset($_SESSION['login']);//закрытие сессии по логину
session_destroy();//удаление сессии
header('Refresh: 1; URL=http://'.$_SERVER["HTTP_HOST"].'/socket/index.php'); 
session_write_close();
?>