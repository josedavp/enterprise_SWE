<?php
include_once("apiFunctions.php");

//STATUS, MESSAGE, ACTION
function createAPISession() {
	//**************************DB CONNECTION******************************//
	$dblink=db_connect("database name here"); 
	//$dblink=db_connect("docStorage");
	//****************************** USER iNFO *****************************//
	$username="username here";
	$password="password here";
	$data="username=$username&password=$password";
	//*************************GETS SESSION INFO****************************//
	$ch=curl_init('https://url here.com/api/create_session');
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
	/************************CONVERTS JSON STRING TO PHP VALUE/ARRAY **************/
	$cinfo=json_decode($result,true);
	//**************************CHECKS IF SESSION IS MADE**************************//
	if ($cinfo[0]=="Status: OK" && $cinfo[1]=="MSG: Session Created") {//DECODES RESULT; ["Status: OK","MSG: Session Created","1882b403c9d670f93ac36715273ecbbd5618a26b"]
		$sid=$cinfo[2];
		$data="sid=$sid&uid=$username";
		echo "\r\nSession Created SuccessfulLy!\r\n";
		echo "SID $sid\r\n";
		echo "Create Session Execution Time: $execution_time\r\n";
		return $sid;
	}
	else if ($cinfo[0]=="Status: ERROR" && $cinfo[1]=="MSG: Previous Session Found") { // ["Status: ERROR","MSG: Previous Session Found","Action: Must clear session first"]
		//CLEAR OLD SESSION FIRST
		/************ ERROR LOG ****************/
		$errorTMP =explode(":",$cinfo[0]); 
		$status= $errorTMP[1];
		$sid=$cinfo[2];
		errorLog($sid, $status, 'api_CREATE_SESSION', $dblink);
		/***************************************/
		$data="username=$username&password=$password";
		$ch=curl_init('https:// url here .com/api/clear_session');
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
		if ($cinfo[0]=="Status: OK")
		{
			echo "\r\nAPI PREVIOUS CREATE Session: \r\n";
			echo "Session successfully closed!\r\n";
			echo "SID: $sid\r\n";
			echo "Close Session execution time: $execution_time\r\n";
			createAPISession();
		}
	}
	else 
	{
		//ERROR LOG THIS
		//GET STATUS
		$sid=$cinfo[2];
		$errorTMP =explode(":",$cinfo[0]); 
		$status= $errorTMP[1];
		errorLog($sid, $status, 'ELSE: api_CREATE_SESSION', $dblink);
		getClearSession($sid,$dblink);
	}

}
?>
