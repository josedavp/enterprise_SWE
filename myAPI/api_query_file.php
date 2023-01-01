<?php
include_once("apiFunctions.php");

/***************************/
//EXAMPLE: 
//echo "\r\n$file written to file system\r\n";
//echo "File: $file\r\n";

// 72189306-Personal-20221112_23_16_24.pdf written to file system
// File: 72189306-Personal-20221112_23_16_25.pdf 
/*****************************************************************/
//$sid //session id
//$data="sid=$sid&uid=$username&fid=$file";
///$file=$tmp[4];
//$execution_time = ($time_end - $time_start)/60; //ERROR LOGS //inside of functions.php instead
//$fp=fopen("/var/www/html/receive/$file","wb");  //PATH
//$contentsClean=addslashes($content); //adds slashes \ to contents
/********************************************************************************************************/
//could also try
// function queryFileData($tmp, $file, $data) {}
//// $uid, $file, $data, $time_start, $time_end, $content)
//Status: $cinfo[0], MSG: $cinfo[1] //if ($cinfo[0]=="Status: OK") if ($cinfo[1]=="MSG: Previous Session Found"
////DECODES RESULT; ["Status: OK","MSG: Session Created","1882b403c9d670f93ac36715273ecbbd5618a26b"]

//HANDLES GETTING DATA FROM FUNCTIONS INTO DB, QUERY DATA 
//last thing to do OTHER THAN CRONJOBS
//
// [ACCTID, TITLETYPE, DATE ]   fileType .PDF    <- only one	
//could just turn it into an array, but its fine
/*************************************************************************************************************/
function getMissingDocuments($sid,$fileName,$uploadBy,$uploadDate,$status,$contentsClean,$titleID,$accountID, $dblink) {
	$sql="Insert into `missingDocuments` (`sid`,`name`,`upload_by`,`upload_date`,`status`,`content`,`title_name`,`account_id`) values ('$sid','$fileName','$uploadBy','$uploadDate','$status','$contentsClean', '$titleID','$accountID')";
				$dblink->query($sql) or
					die("Something went wrong with $sql<br>".$dblink->error);
}

/******************************************************************************/
	function getSession($sid, $uploadBy, $status, $dblink) {
		$uploadDate=date("Y-m-d H:i:s"); //GETS DATE
		//	$tmp =explode(":",$cinfo[0]); 
		//	$status= $tmp[1];
		//$uploadBy = 'vib296';
		//should you add a salt to a session id? how likely is it to be the same?
				//sessions table //no need for auto_id? status? active?
			//$active = 0; //correct?
		$sql="SELECT `sid` FROM `sessions` WHERE `sid` = '$sid'";
		$result = $dblink->query($sql) or
				die("Something went wrong with $sql".$dblink->error);
		$resultCheck = mysqli_num_rows($result);
		
		if($resultCheck == 0) { //kinda an issue for future use since its in a for loop, having to go into the DB a lot to do this
			$sql="UPDATE `sessions` SET `active` = '1' WHERE `sid` != '$sid'";
			$dblink->query($sql) or
				die("Something went wrong with $sql<br>".$dblink->error);
			
			$sql="Insert into `sessions` (`sid`,`uid`,`date`,`status`,`message`,`active`) values ('$sid','$uploadBy','$uploadDate','$status', 'Success', '0')";
			$dblink->query($sql) or
				die("Something went wrong with $sql<br>".$dblink->error);
		}
		else {
			echo "SID Already in SESSION";
		}
		/*
		else {
			$sql="Insert into `sessions` (`sid`,`uid`,`date`,`status`,`message`,`active`) values ('$sid','$uploadBy','$uploadDate','$status', 'Success', '0')";
			$dblink->query($sql) or
				die("Something went wrong with $sql<br>".$dblink->error);
		}
		*/
	}

	function getDocuments ($sid,$fileName,$uploadDate,$uploadBy,$status,$fileType,$contentsClean,$titleID,$accountID, $dblink) {
		//documents table //works just validate info if it needs something added or removed
		$sql="SELECT `name` FROM `documents` WHERE `name` = '$fileName'";
		$result = $dblink->query($sql) or
				die("Something went wrong with $sql".$dblink->error);
		$resultCheck = mysqli_num_rows($result);
		//echo "RESULTCheck: $resultCheck\r\n";
		if($resultCheck == 0) { //inactive
			//$active = 'active';
			//$sql="UPDATE `sessions` SET `active` = '$active' WHERE `sid` != '$sid'";
			$sql="Insert into `documents` (`name`,`upload_date`,`upload_by`,`status`,`file_type`,`content`,`title_name`, `account_id`) values ('$fileName','$uploadDate','$uploadBy','$status','$fileType','$contentsClean', '$titleID','$accountID')";
			$dblink->query($sql) or
				die("Something went wrong with $sql<br>".$dblink->error);
		} 
		else  //active
		{
			//error log duplicate file
			echo "DUPLICATE FILE";
			errorLog($sid, 'ERROR', 'Duplicate File: '. $fileName, $dblink);
			getMissingDocuments($sid,$fileName,$uploadBy,$uploadDate,$status,$contentsClean,$titleID,$accountID, $dblink);
			//$active = 'inactive';
			/*
			$sql="UPDATE `documents` SET 
			`name` = '$fileName',
			`path` = ' ',
			`upload_date`= '$uploadDate',
			`upload_by` = '$uploadBy',
			`status` = '$status',
			`active`,
			`file_type` = '$fileType',
			`content` = '$contentsClean',
			`title_name` = '$titleID',
			`account_id` = '$accountID' WHERE `name` = '$fileName'";
			$dblink->query($sql) or
				die("Something went wrong with $sql<br>".$dblink->error);	
				*/
		}
		
	}
	
	
	function getQueryTable ($sid, $status, $file_amount, $actionInfo, $accountID,$uploadBy, $dblink) {
		//file_queries table   //works sort of think about columns needed or removed
			$sql="SELECT `sid` FROM `file_queries` WHERE `sid` = '$sid'";
		$result = $dblink->query($sql) or
				die("Something went wrong with $sql".$dblink->error);
		$resultCheck = mysqli_num_rows($result);
		
		if($resultCheck == 0) { //kinda an issue for future use since its in a for loop, having to go into the DB a lot to do this
			$sql="Insert into `file_queries` (`sid`,`status`,`file_amount`,`msg`,`action`,`account_id`,`uid`) values ('$sid','$status','$file_amount','Successful','$actionInfo','$accountID','$uploadBy')";
		$dblink->query($sql) or
			die("Something went wrong with $sql<br>".$dblink->error);
		}
		else {
			//echo "SID Already in QUERY";
		}
	
	}


	function getTitle ($titleID, $dblink) {
		$sql="SELECT `title` FROM `titles` WHERE `title` = '$titleID'";
		$result = $dblink->query($sql) or
				die("Something went wrong with $sql".$dblink->error);
		$resultCheck = mysqli_num_rows($result);
		//echo "RESULTCheck: $resultCheck\r\n";
		if($resultCheck == 0) {
			$sql="Insert into `titles` (`title`) values ('$titleID')";
			$dblink->query($sql) or
				die("Something went wrong with $sql<br>".$dblink->error);
			//echo "INSIDE IF \r\n";
		} 
		else //nothing
		{
			//echo "Title is Already In DataBase: \r\n";
		}
	}


	function getUser($uploadBy, $dblink) {
		//users table
		$sql="SELECT `uid` FROM `users` WHERE `uid` = '$uploadBy'";
		$result = $dblink->query($sql) or
				die("Something went wrong with $sql".$dblink->error);
		$resultCheck = mysqli_num_rows($result);
		//echo "RESULTCheck: $resultCheck\r\n";
		if($resultCheck == 0) {
			$sql="Insert into `users` (`uid`) values ('$uploadBy')";
			$dblink->query($sql) or
				die("Something went wrong with $sql<br>".$dblink->error);
			//echo "INSIDE IF \r\n";
		} 
		else //nothing
		{
			//echo "User is Already In DataBase: \r\n";
		}
	}
	/*************************************************************************************************************/	

/***
*	FUNCTION: queryFileData();
*	HANDLES QUERY, RETRIEVES DATA FROM FILE AND PUTS IT INTO DB
*
***/

function queryFileData($sid, $value, $cinfo,$file, $files,$contentsClean, $dblink) {// $uid, $file, $data, $time_start, $time_end, $content) { //FORGOT DO I NEED CONTENT?
	//$dblink=db_connect("docStorage"); 
	//****************************** USER iNFO *****************************//
	$username=" username here";
	$password="password here";
	//******************************WHERE YOU GET FILE DATA***********************************//		
	//$tmp=explode("/",$value); //is this correct, what exactly is value?
	//$file=$tmp[4]; //gets the file

	//**************************BELOW MAYBE USE************************//
	$uploadDate=date("Y-m-d H:i:s"); //GETS DATE
	$uploadDName=date("Y-m-d_H:i:s"); 
	
	
	
	//CAN YOU CHECK IF DATE == CRONJOBS SET UP ELSE ITS UPLOADBY IS BY ABC123?
	
	
	
	

	
	//***************CORRECT DATA**************//
	$fileName = str_replace(" ","_",$file);

	$file_amount = count($files);
	$tmp=explode("-",$file); //seperates by - into elements
	$accountID=$tmp[0];
	$titleID=$tmp[1];
	$dateIDTMP=$tmp[2];
	
	//checks if file is imcomplete/ empty
	if (!$accountID || !$titleID  ||  !$dateIDTMP ) {
		$errorTMP =explode(":",$cinfo[0]); 
		$status= $errorTMP[1];
		$userMSG = $fileName;
		errorLog($sid, $status,$userMSG, $dblink);
	}
	//echo "\r\nInside API QUERY FILE; ACCTID: $accountID TitleID: $titleID datIDTMP: $dateIDTMP\r\n";
	$tmp=explode(".",$dateIDTMP); //seperates last part by . into date and .pdf filetype; elements
	$dateID = $tmp[0];
	$fileType = $tmp[1];
	//echo "\r\nFILETYPE: $fileType\r\n";
	//echo "DATEID: $dateID fileType: $fileType\r\n";
	//echo "STATUS: $cinfo[0]\r\n";
	$tmp =explode(":",$cinfo[0]); 
	$status= $tmp[1];
	//echo "STATUS HERE: $status\r\n";
	
	//$msgInfo =$cinfo[0];  //status
	$tmp=explode(":",$cinfo[2]);
	$actionInfo =$tmp[1]; 
	
	//echo "MSGInfo[0]: $msgInfo\r\n"; //MSG
	//echo "actionInfo: $actionInfo\r\n"; //MSG
	//echo "MSG[1]: $msgInfo[1]\r\n"; //MSG
	//echo "MSG[2]: $msgInfo[2]\r\n"; //MSG
	//$tmp = explode(",",$msgInfo[1]);
	
	
	//***************ABOVE CORRECT DATA**************//
	$uploadBy = 'cronjob';
	//**************************Organizes Data for DataBase************************//
	getSession($sid, $uploadBy, $status, $dblink);
	getDocuments ($sid,$fileName,$uploadDate,$uploadBy,$status,$fileType,$contentsClean,$titleID,$accountID, $dblink);
 	getQueryTable ($sid, $status, $file_amount, $actionInfo, $accountID,$uploadBy, $dblink);
	getTitle ($titleID, $dblink);
	getUser($uploadBy, $dblink);
	
	
	}
?>
