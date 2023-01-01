<?php
function db_connect($db)
{
	$hostname=" host name here";
    $username=" user name here";
    $password="password here";
	
    $dblink=new mysqli($hostname,$username,$password,$db);
    if (mysqli_connect_errno())
    {
        die("Error connecting to database: ".mysqli_connect_error());   
    }
	return $dblink;
}

function redirect ( $uri )
{ ?>
	<script type="text/javascript">
	<!--
	document.location.href="<?php echo $uri; ?>";
	-->
	</script>
<?php die;
}

function errorLog($sid, $status, $userMSG, $dblink) 
{
	//GET STATUS
	//$errorTMP =explode(":",$cinfo[0]); 
	//$status= $errorTMP[1];
	$uploadDate=date("Y-m-d H:i:s");
	
	$sql="Insert into `error_log` (`sid`,`date`,`status`,`userMSG`) values ('$sid','$uploadDate','$status','$userMSG')";
	$dblink->query($sql) or
		die("Something went wrong with $sql<br>".$dblink->error);
}

function getClearSession ($sid,$dblink) 
{
	$username="username here";
	$password="password here";
	$data="username=$username&password=$password";
	$ch=curl_init('https://   url here .com/api/clear_session');
	curl_setopt($ch, CURLOPT_POST,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array (
		'content-type: application/x-www-form-urlencoded',
		'content-length: ' . strlen($data))
	);
	$time_start = microtime(true);
	$result = curl_exec($ch);
	$time_end = microtime(true);
	$execution_time = ($time_end - $time_start)/60;
	curl_close($ch);
	
	$sql="UPDATE `sessions` SET `active` = '1' WHERE `sid` = '$sid'";
	$dblink->query($sql) or
		die("Something went wrong with $sql<br>".$dblink->error);
	
	
	echo "FUNCTION CLEAR SESSION: !\r\n";
	echo "Session successfully Cleared!\r\n";
	echo "SID: $sid\r\n";
	echo "CLEAR Session execution time: $execution_time\r\n";
}

?>
