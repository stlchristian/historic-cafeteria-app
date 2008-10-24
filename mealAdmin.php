<?
//TODO:  finish the formating for time expiration; currently very tacky

include('functions.php');
$check = '68839b25c58e564a33e4bfee94fa4333';
$loggedIn = false;
$error = $_GET['e'];
if($error == 1){
  $error = 'Please start a new meal prior to checking people out.';
} elseif($error == 2){
  $error = 'Time Expired';
  $select = 'blah';
} elseif($error = 3) {
  $error = "The newest meal is over 5 hours old. Please start a new meal";
  $select = '';
} else {
  $select = '';
}
if(!empty($_POST['login'])){
  $password = $_POST['password'];
  $password = md5($password);
  if($password == $check){
    $loggedIn = true;
  } else {
    $loggedIn = false;
  }
} elseif(!empty($_POST['startNew'])){
  connectDB();
  $time = date('Y-m-d H:i:s');
  $query = "INSERT INTO meal(mealTime) VALUES ('$time');";
  mysql_query($query);
  echo mysql_error();
  setcookie('chocolateChip','',time());
  header("Location:index.php");
}
if(!empty($_POST['continue'])){
  $continue = $_POST['continue'];
  if($continue == 'New'){
    $select = '';
  } elseif($continue == 'Continue'){
    setcookie('chocolateChip','',time());
    header("Location:scan.php");
  }
}
?>
<HTML>
<HEAD>
 <TITLE><?=(empty($error))?'Start a new meal':$error?></TITLE>
</HEAD>
<BODY>
<center><?=$error?></center>
<? if($select == 'blah'): ?>
<BR>
<center>Would you like to start a new meal, or continue the last one?</center>
<form action='mealAdmin.php' method="post">
<BR><BR>
  <center><input type='submit' name="continue" value="Continue"> Or <input type='submit' name=continue value='New'></center>
</form>
<? else: ?>
<? if($loggedIn == true):?>
<form action="mealAdmin.php" method="post">
<center> <BR<BR><BR>
<input type="submit" name="startNew" value="Start a new meal">
</center>
<form>
<? elseif($loggedIn == false):?>
<form action="mealAdmin.php" method="post">
<center>
<BR><BR>
<table>
  <tr>
    <td>
      Password:
    </td>
    <td>
      <input type="password" name="password"><BR>
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center">
      <input type='submit' value="Login" name="login">
    </td>
  </tr>
<table>
</center>
</form>
<? endif;?>
<? endif;?>
</BODY>
</HTML>
