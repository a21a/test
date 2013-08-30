<?php
if ($data = file_get_contents('sockets/9f0c2782349a9d6f51361dcff335ae70')) 
{
	echo "<br/>data1:";
	print_r($data);
	$f = fopen('sockets/9f0c2782349a9d6f51361dcff335ae70', 'r+b') or die('socket not found');
	ftruncate($f, 0);
	flock($f, LOCK_EX);
	fwrite($f, '');
	fclose($f);
	$data = trim($data, "\r\n");
	echo "<br/>data2:";
	print_r($data);
	foreach (explode("\r\n", $data) as $action) 
	{
		self::$actions[] = $action;
		echo "<br/>data3:";
		print_r($action);
	}
	self::Send();
}
?>