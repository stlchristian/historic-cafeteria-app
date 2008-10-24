<?
//TODO:
session_start();
include('functions.php');
//session_register('beginMeal');
//check if the last 'new meal' time has been retrieved.
connectDB();
if(isset($_COOKIE['chocolateChip'])){
  if($_COOKIE['chocolateChip'] == 'no meal'){
    //echo 'it would have redirected';
    header("Location:mealAdmin.php?e=1");
  } elseif(strtotime($_COOKIE['chocolateChip']) <= time()-(60*60*5)){
    header("Location:mealAdmin.php?e=3");
  }
  //echo "it's not equal to no meal";
} else {
  $query = "SELECT mealTime, mealID from meal order by mealID desc limit 1;";
  $results = mysql_query($query);
  while($dummy = mysql_fetch_row($results)){
    if(!empty($dummy)){
      $begin = $dummy;
    }
  }
  $mealtime = $begin[0];
  $mealID = $begin[1];
  if(empty($mealtime)){
    $mealtime = 'no meal';
  }
  setcookie('chocolateChip',$mealtime,time()+1800);
  setcookie('mealID',$mealID, time()+1800);
  //echo 'the cookie got set<BR><BR>';
  
}

//check if id has been submitted
if(!empty($_POST['barcode'])){
  $stuID = $_POST['barcode'];
  $mealID = $_COOKIE['mealID'];
  $query = "SELECT st.tranID FROM studentMeals as st
            JOIN students as s ON (s.id = st.stuID)
            WHERE s.id = '$stuID' and st.mealID = '$mealID'";
  $hadMeal = runShortQuery($query);
  $sql = "SELECT mealplan, lastname, firstname from students where id = '$stuID' limit 1";
  $results = mysql_query($sql);
  while($dummy = mysql_fetch_assoc($results)){
    if(!empty($dummy)){
      $mealPlan = $dummy;
    }
  }
  if(!empty($hadMeal)){
    $alert = "They student number ".$stuID." has already had this meal.";
  } elseif(empty($mealPlan['mealplan'])) {
    $alert = "Student needs to pay. NO MEAL PLAN.";
  }else{
    $mealID = $_COOKIE['mealID'];
    $query = "INSERT INTO studentMeals(mealID, stuID) VALUES ('$mealID', '$stuID')";
    mysql_query($query);
    if(mysql_error()){
      $alert = 'Contact the tech department at x1250'."\n\r".mysql_error();
    } else {
      $alert = "Have a nice meal. Student ID has been logged."."<BR><BR>"."<font size='+3'>".ucfirst($mealPlan['firstname'])." ".ucfirst($mealPlan['lastname'])."</font>";
    }
  }
}
?>
<head>
  <script type="text/javascript">
  <!--
    function focusing(){
      document.getElementById('barcode').focus();
    }
    function redirect(){
       window.location = 'mealAdmin.php?e=2'
    }
  //-->
  </script>
  <title>Get -a- Meal</title>
</head>
<body onload="javascript:focusing();setTimeout('redirect()',(1000*60*30));">
 <form action="scan.php" method="post">
 <BR><BR><BR>
  <center><b>Scan ID:</b><BR><input type="text" id="barcode" name="barcode"></center>
 </form>
 <center><b><font color="red"><?=$alert?></font></b></center>
</body>
