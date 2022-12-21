<?php
$page="reporting.php";
include("apiFunctions.php");
$dblink = db_connect("database name here");

/*********************/
echo '<!DOCTYPE html>';
echo '<html>';
echo '<table>';
echo '<tr>';
echo '<div><h2>&nbsp&nbspName: Jose Pague</h2></div>';
echo '<div><h2>&nbsp&nbspabc123: </h2></div>';
echo '<div><h2>&nbsp&nbspDate: date </h2></div>';
/********* ASSIGNMENT 5 ***********/
/****
*	Question 1
***********/
$sql="SELECT * from `file_queries` where `uid`='cronjob'";// where `user_id`='cronjobs'";
$result=$dblink->query($sql) or
	die("Something went wrong with: $sql<br>".$dblink->error);
$loanArray=array();
echo '<div><h1>&nbsp&nbspAssignment 5</h1></div>';
echo '<div><h2>&nbsp&nbspNumber 1: </h2></div>';
$num = 0;
while ($data=$result->fetch_array(MYSQLI_ASSOC))
{
	echo '<div><h4>&nbsp&nbsp '.$data['account_id'].'</h4></div>';
	$num += 1;
}
echo '<div ><h3>&nbsp&nbsp Total Unique Loans Generated: '.$num.'</h3></div>';
/**************************check this one with number 3 for documents generated************************************************/
/*
$sql="SELECT * from `documents` where `upload_by`='cronjob'";// where `user_id`='cronjobs'";
$result=$dblink->query($sql) or
	die("Something went wrong with: $sql<br>".$dblink->error);
$loanArray=array();
echo '<br><div><h2>&nbsp&nbsp Documents: </h2></div>';
$num = 0;
while ($data=$result->fetch_array(MYSQLI_ASSOC))
{
	echo '<div><h4>&nbsp&nbsp '.$data['name'].'</h4></div>';
	$num += 1;
}
echo '<div><h3>&nbsp&nbsp Total Documents Generated: '.$num.'</h3></div>';
*/
/****
* 	Question 2
***********/
$sql="SELECT OCTET_LENGTH(`content`) AS size_in_bytes from `documents` where `upload_by`='cronjob'";// where `user_id`='cronjobs'";
$result=$dblink->query($sql) or
	die("Something went wrong with: $sql<br>".$dblink->error);
$num = 0;
$length = 0;
while ($data=$result->fetch_array(MYSQLI_ASSOC))
{
	$num += $data['size_in_bytes'];
    $length++;
}
$averageLength = $num/$length;
echo '<br><div><h2>&nbsp&nbsp Number 2: </h2></div>';
echo '<div><h4>&nbsp&nbsp&nbsp&nbsp Total Size of All Documents: '.number_format($num).' bytes</h4></div>';
echo '<div><h4>&nbsp&nbsp&nbsp&nbsp Average Size of All Documents: '.number_format(round($averageLength,5)).' bytes</h4></div>';

/****
* 	Question 3
***********/
$sql="SELECT * from `file_queries` where `uid`='cronjob'";// where `user_id`='cronjobs'";
$result=$dblink->query($sql) or
	die("Something went wrong with: $sql<br>".$dblink->error);
$loanArray=array();
echo '<br><div><h2>&nbsp&nbsp Number 3: </h2></div>';
$num = 0;
$averageLoanNumber = 0;
$lessLoan = 0;
$greaterLoan = 0;
while ($data=$result->fetch_array(MYSQLI_ASSOC))
{
	$num += $data['file_amount'];
	$averageLoanNumber++;
}
$averageLoanNumber = $num/$averageLoanNumber;
$sql="SELECT * from `file_queries` where `uid`='cronjob'";// where `user_id`='cronjobs'";
$result=$dblink->query($sql) or
	die("Something went wrong with: $sql<br>".$dblink->error);
while ($data=$result->fetch_array(MYSQLI_ASSOC))
{
	echo '<div><h4>&nbsp&nbsp Loan Number: '.$data['account_id']. ' Total Number of Documents Received: '.$data['file_amount'].'</h4></div>';
	//echo '<div>&nbsp&nbsp Loan Number Status: </div>';
	if ($data['file_amount'] > $averageLoanNumber) {
		echo '<div>&nbsp&nbsp Loan Number Status:&nbsp&nbsp Above Average: '. $data['file_amount'] .'</div>';
	}
	else if ($data['file_amount'] < $averageLoanNumber) {
		echo '<div>&nbsp&nbsp Loan Number Status:&nbsp&nbsp  Below Average: '. $data['file_amount'] .'</div>';
	} 
	else if ($data['file_amount'] == $averageLoanNumber) {
		echo '<div>&nbsp&nbsp Loan Number Status:&nbsp&nbsp  Average: '. $data['file_amount'] .'</div>';
	}
	else {
		echo '<div>&nbsp&nbspN/A</div>';
	}
}

echo '<div><h3>&nbsp&nbspTotal Loan Number Documents received: '.$num.'</h3></div>';
echo '<div><h3>&nbsp&nbspAverage Loan Number Documents received: '. round($averageLoanNumber, 4).'</h3></div>'; //thats if we go with decimals

/**********************FIGURE OUT BELOW *********************/

/****
* Question 4
***********/
/*******
* //PART 1
***********/
/***********************************************/
echo '<br><div><h2>&nbsp&nbsp Number 4: </h2></div>';
echo '<div><h3>&nbsp&nbsp 4.1) Missing Documents: </h3></div>';
/***********************************************/
//GETS TITLES 
$sql="SELECT `title` from `titles`";// where `user_id`='cronjobs'";
$result=$dblink->query($sql) or
	die("Something went wrong with: $sql<br>".$dblink->error);
$titleListArray = array();
$num = 0;
while ($data=$result->fetch_array(MYSQLI_ASSOC))
{
	$titleListArray[$num] = $data['title'];
	$num++;
}
/**********************JUST NEED DOCUMENTS WITH TITLE NAME AND ACCOUNT NUMS AND TITLE ARRAY*********************************/
//NESTED WHILE LOOP
//LOOP THROUGH ACCOUNT NUMBERS AND CHECK IF THEY CONTAIN ALL 8 TITLES, IF ANY ARE THEN SUBTRACT THEM
// if empty then your good else get missing document left
$sql= "SELECT DISTINCT `account_id` FROM `documents`;"; //THIS GETS UNIQUE ACCT IDS
$result=$dblink->query($sql) or
	die("Something went wrong with: $sql<br>".$dblink->error);
//echo '<div><h3>Missing Documents: </h3></div>';
$accountArray = array();
$titleNameArray = array();
$titleCheckArray = array();
$missingDocumentArray = array();
$successfulDocumentArray = array();
//$tmpArray = array();
while ($data=$result->fetch_array(MYSQLI_ASSOC)) //this goes through row by row  by each account_ID
{
	$num = 0;// RESETS IT
	//$accountArray[$num] = $data['account_id'];
	$name = $data['account_id'];
	$tmpArray = [];
	/********************SQL QUERY FOR TITLE NAME MOSTLY***************************/
	$sql2= "SELECT `title_name`, `account_id` FROM `documents` where `account_id` like '$name';"; //this gets contents of first account
	$result2=$dblink->query($sql2) or
		die("Something went wrong with: $sql<br>".$dblink->error);
	/***********************************************/
	//echo  $accountArray[$num];
	while ($data2=$result2->fetch_array(MYSQLI_ASSOC)) //this goes through row by row  by each account_ID
	{
		$titleCheckArray = $titleListArray;// PASSES ARRAY FOR FUTURE VERIFICATION
		$titleName = $data2['title_name']; //GETS SPECIFIC TITLE ASSOCIATE TO ACCT ID //NOT WORKING THOUGH TO GET TITLE
		//echo  'HERE WHILE: '.$titleName;
		array_push($tmpArray, $titleName);
		$num++;
	}
	//AFTER GOING INSIDE INNER WHILE LOOP AND CHECKING ACCT ID INFO IT GOES TO VERIFY CONTENTS AS SHOWN BELOW
	//echo $titleListArray;
	//echo $titleCheckArray;
	$missingDocumentArray = array_diff($titleListArray, $tmpArray);
	//echo 'OK: '. $missingDocumentArray;
	if (!empty($missingDocumentArray)) {
		$loanNames = implode(",", $missingDocumentArray);
		//echo 'HERE: '. $loanNames;
		//CLOSE BUT NOT WORKING YET
		echo '<div>&nbsp&nbsp&nbsp&nbspLoan Number: ' .$data['account_id'] . '&nbsp&nbsp&nbsp&nbspDocuments Missing:    '.$loanNames.'</div>';
	}
	else {
		//echo 'ELSE HERE: '. $loanNames;
		//echo '<div> Loan Number:   ' .$data['account_id'] . '     Documents Missing:    '.$loanNames.'</div>';
	}
}
/*******
* //PART 2
********/
//IS PART 2 LOGIC CORRECT IN END?
echo '<div><h3>&nbsp&nbsp 4.2) Completed Documents: </h3></div>';
$sql= "SELECT DISTINCT `account_id` FROM `documents`;"; //THIS GETS UNIQUE ACCT IDS
$result=$dblink->query($sql) or
	die("Something went wrong with: $sql<br>".$dblink->error);
//echo '<div><h3>Missing Documents: </h3></div>';
$accountArray = array();
$titleNameArray = array();
$titleCheckArray = array();
$missingDocumentArray = array();
$successfulDocumentArray = array();
//$tmpArray = array();
while ($data=$result->fetch_array(MYSQLI_ASSOC)) //this goes through row by row  by each account_ID
{
	$num = 0;// RESETS IT
	//$accountArray[$num] = $data['account_id'];
	$name = $data['account_id'];
	$tmpArray = [];
	/********************SQL QUERY FOR TITLE NAME MOSTLY***************************/
	$sql2= "SELECT `title_name`, `account_id` FROM `documents` where `account_id` like '$name';"; //this gets contents of first account
	$result2=$dblink->query($sql2) or
		die("Something went wrong with: $sql<br>".$dblink->error);
	/***********************************************/
	//echo  $accountArray[$num];
	while ($data2=$result2->fetch_array(MYSQLI_ASSOC)) //this goes through row by row  by each account_ID
	{
		$titleCheckArray = $titleListArray;// PASSES ARRAY FOR FUTURE VERIFICATION
		$titleName = $data2['title_name']; //GETS SPECIFIC TITLE ASSOCIATE TO ACCT ID //NOT WORKING THOUGH TO GET TITLE
		//echo  'HERE WHILE: '.$titleName;
		array_push($tmpArray, $titleName);
		$num++;
	}
	//AFTER GOING INSIDE INNER WHILE LOOP AND CHECKING ACCT ID INFO IT GOES TO VERIFY CONTENTS AS SHOWN BELOW
	//echo $titleListArray;
	//echo $titleCheckArray;
	$missingDocumentArray = array_diff($titleListArray, $tmpArray);
	//echo 'OK: '. $missingDocumentArray;
	if (empty($missingDocumentArray)) { //IF EMPTY THEN THEY SHOULD HAVE ALL MATCHED?????
		//$loanNames = implode(",", $missingDocumentArray);
		//echo 'HERE: '. $loanNames;
		//CLOSE BUT NOT WORKING YET
		echo '<div>&nbsp&nbsp Successful Loan Numbers:   </div>'; //data2 or $data since $data has the value your looking for? 
		echo '<div>&nbsp&nbsp ' .$data['account_id'] . '</div>'; // not sure if it matters
	}
	else {//is this correct?
        //echo 'None';
		//echo 'ELSE HERE: '. $loanNames;
		//echo '<div> Loan Number:   ' .$data['account_id'] . '     Documents Missing:    '.$loanNames.'</div>';
	}
}
/*******
* //PART 3
********/
/*********************************************************************/
echo '<div><h3>&nbsp&nbsp 4.3) Total Number of Documents Received: </h3></div>';
/***********************************************/
$sql="SELECT `title` from `titles`";// where `user_id`='cronjobs'";
$result=$dblink->query($sql) or
	die("Something went wrong with: $sql<br>".$dblink->error);
$titleListArray = array();
$num = 0;
while ($data=$result->fetch_array(MYSQLI_ASSOC))
{
	$titleListArray[$num] = $data['title'];
	$num++;
}

$sql= "SELECT `title_name` FROM `documents`;"; //THIS GETS UNIQUE ACCT IDS
$result=$dblink->query($sql) or
	die("Something went wrong with: $sql<br>".$dblink->error);
//echo '<div><h3>Missing Documents: </h3></div>';
$countTitleArray = array();
for ($i = 0; $i < count($titleListArray); $i++)
{
	$countTitleArray[$i] = 0; //sets all values to 0 first
}
// can do it this way increase count in array 0-8. with a for loop make all 8 elements in array to 0; then go to them.
while ($data=$result->fetch_array(MYSQLI_ASSOC)) //this goes through row by row of titles in documents and tallys them up
{
	for ($j = 0; $j < count($titleListArray); $j++) {
		if ($titleListArray[$j] == $data['title_name']) {
			$countTitleArray[$j] = $countTitleArray[$j]+1; //increments depending where it matches. 
			//verify order of titles in list and how its being pulled to. 
		}//NOT DONE YET AND STILL NEED TO ECHO IT
	}
}//IN THE END ALSO EXPLAIN TO PROF ABOUT DATA AND MISSING DATA AND WHY 
//EXPLAIN WHAT YOU LEARNED AND HAD TO CHANGE, YOUR MISTAKES AS WELL AND WHEN IT OCCURED AND WHAT YOU DID TO FIX IT
//ALSO TURN IN ASSIGNMENT 4 WITH NEW DATA AGAIN AND TURN OFF CRONJOB  AFTER NOV 30TH

for ($k=0; $k < count($countTitleArray); $k++) { //NEEDS TO DISPLAY DATA FOR EACH TITLE//NOT DONE MAYBE? just verify later
	echo '<div>&nbsp&nbsp&nbsp&nbspTotal Number of '. $titleListArray[$k] .':&nbsp'. $countTitleArray[$k] . '</div>';
}
/***********************************/
//****************EXPLAIN SITUATION HERE BELOW*********/
echo '<br><div><b>&nbsp&nbsp REASON FOR DATA INCONSISTENCIES:  </b></div>';
echo '<div>&nbsp&nbsp Data had multiple bugs initially on Nov 14-15th. Nov 23, 26, 29th in the documents and file_query table. Along with syntax errors and a incorrect cronjob execution (Had to change it twice from incorrect time and day). Which prevented the data from being properly added into the DataBase. But from changing my SQL Query, adding/correcting if statements to prevent duplicates (I had an error in my file_query function), running multiple times separate requests using my \'abc123\', echoing out the data and creating functions to extract the data I needed. I was able to debug most errors and modify my database to become more efficient. As of now I still have a few else statements with echos, but they are simply temporary methods of seeing what is being outputted such as past (hopefully) unintentional duplicates in the Query File or Document Function. I also realized having a few more tables would make my system more rebust by compartmentalizing error logs, missing/duplicate files, and other forms of data for better readability and data extraction. This assignment made me think greatly on the structure of my DataBase and its ability to get, process, and display the data I need. Each day a request was made, I saw faults that I did not expect to see when I manually requested files. But I was able to correct most of them through trial and error. I also had to speed up and create brand new cron jobs (mostly for the last two days) in order to meet and exceed the minimum data amount required. But my system seems to be fine with the changes made. Question 4, Part 2 has the code needed to search and display the successful loan numbers but I have not received one as of yet with my current queries. I did my best though! </div>';
/*********************************/
//$sql= 
	//"SELECT DISTINCT `file_amount` , b.`account_id` as loan_num  FROM `file_queries` b   LEFT JOIN `documents` f ON f.`account_id` = b.`account_id`";// where `documents.upload_by`='cronjob'";
/*
//INCLUDES TITLE NAME
SELECT DISTINCT `title_name`,`file_amount` , b.`account_id` as loan_num FROM `file_queries` b LEFT JOIN `documents` f ON f.`account_id` = b.`account_id`;
*/
/*
$result=$dblink->query($sql) or
	die("Something went wrong with: $sql<br>".$dblink->error);
$loanArray=array();
echo '<br><div><h2>Number 4: </h2></div>';
$i = 0;
$fileQuery_Array = array();
while ($data=$result->fetch_array(MYSQLI_ASSOC))
{
	$fileQuery_Array[$i] = $data;
	$i++;
}

$sql = "SELECT `account_id`, COUNT(account_id) as account_Sum
FROM `documents`
GROUP BY `account_id`
HAVING COUNT(`account_id`) > 0";

$result=$dblink->query($sql) or
	die("Something went wrong with: $sql<br>".$dblink->error);
$documents_Array = array();
$i = 0;
while ($data=$result->fetch_array(MYSQLI_ASSOC))
{
	$documents_Array[$i] = $data;
	$i++;
}

$success_Array = array();
$failed_Array = array();
$j = 0;
$k = 0;
$length = count($documents_Array);
for($i =0; $i < $length; $i++)  {
	if ($fileQuery_Array[$i]['file_amount'] == $documents_Array[$i]['account_Sum']) {
		$success_Array[$k] = $documents_Array[$i]['account_id'];
		//echo '<div><h4>Successful Loan Numbers: '. $success_Array[$i] .'</h4></div>';
		$k++;
	}
	else if ($fileQuery_Array[$i]['file_amount'] != $documents_Array[$i])
	{
		//$j++;
		$failed_Array[$j] = $documents_Array[$i]['account_id'];
		//echo '<div><h4>Failed Loan Numbers: '. $failed_Array[$i] .'</h4></div>';
		$j++;
		
	}
} 

$length = count($failed_Array);
echo '<div><h3>Total Failed Loan Numbers: '.$length.' </h3></div>';
echo '<div><h3>Failed Loan Numbers:  </h3></div>';
for($i =0; $i < $length; $i++)  {
	echo '<div><h4>'. $failed_Array[$i] .'</h4></div>';
}
*/
/***********MAY NO LONGER BE NEEDED?******************/
/*
$sql="SELECT * from `missingDocuments` where `upload_by`='cronjob'";// where `user_id`='cronjobs'";
$result=$dblink->query($sql) or
	die("Something went wrong with: $sql<br>".$dblink->error);
echo '<div><h3>Missing Documents: </h3></div>';
while ($data=$result->fetch_array(MYSQLI_ASSOC))
{
	echo '<div><h4>'.$data['name'].'</h4></div>';
}
*/
/*****************************/
/*
$length = count($success_Array);
echo '<br><div><h3>Total Successful Loan Numbers: ' . $length . ' </h3></div>';
echo '<div><h3>Successful Loan Numbers: </h3></div>';
for($i =0; $i < $length; $i++)  {
	echo '<div><h4> '. $success_Array[$i] .'</h4></div>';
}
*/




echo '</tr>';
echo '</table>';
echo '</html>';
?>