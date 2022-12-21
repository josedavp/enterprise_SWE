<?php
require("api_create_session.php");
require("api_close_session.php");
require("api_create_query_receive_file.php");
include_once("apiFunctions.php");

//****EXECUTES AND VALIDATES FOR SID ****************/
$sid = createAPISession();

if ($sid != "Error") 
{
	createAPIQuery($sid);
	closeAPISession($sid);
}
else 
{
	//ERROR LOG THIS
	//echo 'Error Session ID: ' . $sid;
	errorLog($sid, 'ERROR', 'In start.php', $dblink);
	getClearSession($sid);
}
?>