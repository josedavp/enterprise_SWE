<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>


<!--<link href="assets/css/bootstrap.css" rel="stylesheet" /> -->
<link href="assets/css/bootstrap-fileupload.min.css" rel="stylesheet" />
<!-- JQUERY SCRIPTS -->
<script src="assets/js/jquery-1.12.4.js"></script>
<!-- BOOTSTRAP SCRIPTS -->
<script src="assets/js/bootstrap.js"></script>
<script src="assets/js/bootstrap-fileupload.js"></script>

<?php
$hostname="hostname here";
$username="username here";
$password="password here";
$db="db name here";
$mysqli= new mysqli($hostname, $username, $password, $db);
if (mysqli_connect_errno())
{
    die("Error connecting to database: ".mysqli_connect_error());
}
/*$sql= "Select * from `user_input` where 1";
$result=$mysqli->query($sql) or
    die("Something went wrong with $sql".$mysqli->error);
while ($data=$result->fetch_array(MYSQLI_ASSOC))
{
    echo "<p>Entry $data[auto_id]: $data[input] - $data[user_id]</p>";
}*/

echo '<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand">Enterprise Software Engineering </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
          <li class="nav-item">
          <a class="nav-link active" aria-current="page"  href="https://ec2 url here.compute.amazonaws.com/index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="https://ec2 url here.compute.amazonaws.com/upload.php">Upload</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="https://ec2 url here.amazonaws.com/view.php">View</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="https://ec2 url here.compute.amazonaws.com/search.php">Search</a>
        </li>
      </ul>
    </div>
  </div>
</nav>';

//$sql="Insert into `user_input` (`input`,`user_id`) values ('input from web', 'webuser@mail.com')";
//$mysqli->query($sql) or
//    die("Something went wrong with $sql".$mysqli->error);
//echo "<p> Executed $sql </p>";

?>