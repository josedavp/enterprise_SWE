<?php
//work on this first
//create query first
//then next file pass data into DB
include_once("apiFunctions.php");
require("api_query_file.php");

function createAPIQuery($sid) {
	//**************************DB CONNECTION******************************//
	$dblink=db_connect("db name here"); 
	
	//****************************** USER iNFO *****************************//
	$username="username here";
	$password="password here";
	$data="sid=$sid&uid=$username";
	//*************************GETS FILES ****************************//
	$ch=curl_init('https://url here.com/api/query_files');
	curl_setopt($ch,CURLOPT_POST,1);
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
	$cinfo=json_decode($result,true);
	/*******************************VALIDATES SESSION INFO*************************************/
	if ($cinfo[0]=="Status: OK")
	{
		if ($cinfo[1]=="Action: None")
		{
			echo "\r\n No New files to import found\r\n";
			echo "SID: $sid\r\n";
			echo "Username: $username\r\n";
			echo "Query Files Execution Time: $execution_time\r\n";
		}
		else
		{
			/*******************************COUNTS FILES*************************************/
			$tmp=explode(":", $cinfo[1]);
			$files=explode(",",$tmp[1]);
			echo "Number of new files to import found: ".count($files)."\r\n";
			echo "Files:\r\n";
			//*********************************ECHOS NUMBER OF FILES******************************************//
			/*foreach($files as $key=>$value)
			{
				echo $value. "\r\n"; //ECHOING OUT ACTUAL AMOUNT OF FILES
			}*/
			
			//$unique = array_unique($files);
			//$dupes = array_diff_key($files, $unique);
			
			
			
			foreach($files as $key=>$value)
			{
				//******************************WHERE YOU REQUEST THE FILE DATA***********************************//
				$tmp=explode("/",$value);
				
				if (isset($tmp[4])) {
					//could validate in small if statement if its unqiue or not?
					
					$file=$tmp[4];

					echo "\r\nFile: $file\r\n";
					$data="sid=$sid&uid=$username&fid=$file";
					$ch=curl_init('https://url here.com/api/request_file');
					curl_setopt($ch,CURLOPT_POST,1);
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
					$content=$result;
		
					$fp=fopen("/var/www/html/receive/$file","wb");

					fwrite($fp,$content);
					fclose($fp);
					echo "$file written to file system\r\n";
					$contentsClean=addslashes($content); //CONTENTS, FILE CONTENTS*/
					//could pass an array instead.
					queryFileData($sid, $value, $cinfo,$file, $files,$contentsClean, $dblink);
				}
				else 
				{
					echo "\r\nSKIPPED FILE:";
					$errorTMP =explode(":",$cinfo[0]); 
					$status= $errorTMP[1];
					errorLog($sid, $status, 'api_create_QRF: ', $dblink);
					//no clear session since it will just loop to the next file
				}
			}
			echo "Query Files Execution Time: $execution_time\r\n";
		}
	}
	else 
	{
		//error log
		//maybe try again? if couldnt query data?
		echo "APICQR LAST ELSE\r\n";
		$errorTMP =explode(":",$cinfo[0]); 
		$status= $errorTMP[1];
		errorLog($sid, $status, 'api_create_QRF', $dblink);
		getClearSession($sid, $dblink); //should I?
		//createAPISession();  //restarts it?
	}
	
}
?>