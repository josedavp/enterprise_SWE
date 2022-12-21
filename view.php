<link href="assets/css/bootstrap.css" rel="stylesheet" />
<!-- JQUERY SCRIPTS -->
<script src="assets/js/jquery-1.12.4.js"></script>
<!-- BOOTSTRAP SCRIPTS -->
<script src="assets/js/bootstrap.js"></script>
<?php
include("uploadFunctions.php");
$dblink=db_connect("database name here");
$autoid=$_REQUEST['fid'];


echo '<div id="page-inner">';
echo '<h1 class="page-head-line">View Files on DB</h1>';
echo '<div class="panel-body">';
$sql="SELECT * FROM (SELECT documents.name, documents.upload_date, documents.upload_by, documents.auto_id, documents.content FROM documents UNION  SELECT uploadDocuments.name, uploadDocuments.upload_date, uploadDocuments.upload_by,  (uploadDocuments.auto_id + (SELECT MAX(documents.auto_id) FROM documents) + 1000), uploadDocuments.content FROM uploadDocuments) as a2 WHERE a2.auto_id = '$autoid'";
$result=$dblink->query($sql) or
	die("Something went wrong with $sql<br>".$dblink->error);
$data=$result->fetch_array(MYSQLI_ASSOC);


if ($data['path']!=NULL)
	echo '<p>File: <a href="uploads/'.$data['name'].'" target="_blank">'.$data['name'].'</a></p>';
else
{
	$content=$data['content'];
	$fname=date("Y-m-d_H:i:s")."-userid-file.pdf";
	if (!($fp=fopen("/var/www/html/uploads/$fname","w")))
		echo "<p>File could not be loaded at this time</p>";
	else
	{
		fwrite($fp,$content);
		fclose($fp);
		echo '<p>File: <a href="uploads/'.$fname.'" target="_blank">'.$data['name'].'</a></p>';
	}
}
echo '</div>';//end panel-body
echo '</div>';//end page-inner
?>