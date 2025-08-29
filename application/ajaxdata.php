<?php
//var_dump($_POST);

$servername = "localhost";
$username = "makentin_mbuser";
$password = "J4B1c07wC%E[";
$dbname = "makentin_mbilling";

$conn = new mysqli($servername, $username, $password, $dbname);


 if(isset($_POST['email_id'])){ 
global $conn;
 
$sql = "INSERT INTO guest_user (email_id,contact_no)
VALUES ('".$_POST['email_id']."',".$_POST['mob_no'].")";

$query=$conn->query($sql);
echo $query;

 }
 
  

?>