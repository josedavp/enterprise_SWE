<?php
include_once("apiFunctions.php");
function closeAPISession($sid) {// PASSES SESSION ID TO CLOSE SESSION
	//**************************DB CONNECTION******************************//
	$dblink=db_connect("database name here"); 
	//****************************** USER iNFO *****************************//
	$username="username here";
	$password="password here";
	$data="sid=$sid&uid=$username";
	//*************************GETS SESSION INFO****************************//
	
	$ch=curl_init('https://url here.com/api/close_session');
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
	$cinfo=json_decode($result, true); //DECODES RESULT; ["Status: OK","MSG: SID closed successfully","Action: Done"]
	if ($cinfo[0]=="Status: OK")
	{
		echo "\r\nAPI CLOSE Session: ";
		echo "\r\nSession successfully closed!\r\n";
		echo "SID: $sid\r\n";
		echo "Close Session execution time: $execution_time\r\n";
	}	
	else if ($cinfo[0]=="Status: ERROR" && $cinfo[1]=="MSG: Previous Session Found")
	{ // ["Status: ERROR","MSG: Previous Session Found","Action: Must clear session first"]
		//CLEAR OLD SESSION FIRST
		
		//***********************//
		$errorTMP =explode(":",$cinfo[0]); 
		$status= $errorTMP[1];
		errorLog($sid, $status, 'In api_closeSession', $dblink);
		//***********************//
		
		$ch=curl_init('https://url here.com/api/clear_session');
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
		if ($cinfo[0]=="Status: OK") //["Status: OK","MSG: Previous Session Found","Action: Session Cleared"]
		{
			//***********************//
			//INSERT INTO ERROR LOG AS WELL
			//***********************//
			echo "API PREVIOUS CLOSE Session: !\r\n";
			echo "Session successfully Cleared!\r\n";
			echo "SID: $sid\r\n";
			echo "Close Session execution time: $execution_time\r\n";
			
			//return closeAPISession($sid); //not needed
		}
	}
	else 
	{
		$errorTMP =explode(":",$cinfo[0]); 
		$status= $errorTMP[1];
		errorLog($sid, $status, 'In api_closeSession', $dblink);
		getClearSession($sid,$dblink);
	}
	

}
?>
