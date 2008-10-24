<?

//TODO:

session_start();

include('functions.php');

//session_register('beginMeal');

//check if the last 'new meal' time has been retrieved.

connectDB();

if(isset($_COOKIE['chocolateChip'])){

  if($_COOKIE['chocolateChip'] == 'no meal'){

    header("Location:mealAdmin.php?e=1");

  } elseif(strtotime($_COOKIE['chocolateChip']) <= time()-(60*60*5)){

    header("Location:mealAdmin.php?e=3");

  }

} else {

  $query = "SELECT mealTime, mealId from meal order by mealId desc limit 1;";

  $results = mysql_query($query);

  while($dummy = mysql_fetch_row($results)){

    if(!empty($dummy)){

      $begin = $dummy;

    }

  }

  $mealtime = $begin[0];

  $mealId = $begin[1];

  if(empty($mealtime)){

    $mealtime = 'no meal';

  }

  setcookie('chocolateChip',$mealtime,time()+1800);

  setcookie('mealId',$mealId, time()+1800);

  //echo 'the cookie got set<BR><BR>';

  

}



//check if id has been submitted

if(!empty($_POST['barcode'])){

  $studentId = $_POST['barcode'];

  $mealId = $_COOKIE['mealId'];

  $query = "SELECT studentmeals.tranId FROM studentmeals 

            JOIN students ON (students.studentId = studentmeals.studentId)

            WHERE students.studentId = '$studentId' and studentmeals.mealId = '$mealId'";

  $hadMeal = runShortQuery($query);

  $sql = "SELECT mealplan, lastname, firstname from students where studentId = '$studentId' limit 1";

  $results = mysql_query($sql);

  while($dummy = mysql_fetch_assoc($results)){

    if(!empty($dummy)){

      $mealPlan = $dummy;

    }

  }

  if(!empty($hadMeal)){

    $alert = "They student number ".$studentId." has already had this meal.";

  } elseif(empty($mealPlan['mealplan'])) {

    $alert = "Student needs to pay. NO MEAL PLAN.";

  }else{

    $mealId = $_COOKIE['mealId'];

    $query = "INSERT INTO studentmeals(mealId, studentId) VALUES ('$mealId', '$studentId')";

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

