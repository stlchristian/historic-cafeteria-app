<?

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
$mealId = $_COOKIE['mealId'];
  if(!empty($_POST['comp']))
  	$comp = $_POST['comp'];
  else
  	$comp = false;

if(!empty($_POST['barcode'])){

  $studentId = $_POST['barcode'];
  	
  if($studentId > 100000){
  
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
	  
	  //Read the pictures directory since php is itself case insensitive.
	  $dir = opendir("../studentpics");
	  $pic = '';
	  while($file = readdir($dir)){
	     //check for the student id in the file name, and then just use that instead.
	     $found = strpos($file, $studentId);
		 if($found !== false){
		   //set up the picture and exit.
		   $pic = "../studentpics/".$file;
		   break;
		 }
	  }
	  //close the directory to make things happy.
	  closedir($dir);
	  
	  //If we didnt' find the picture file, show the no pic image.
	  if($pic == ''){
	    $pic = "../studentpics/noImageAvailable.jpg";
	  }
	  
	  
	  if(!empty($hadMeal)){
	    
	    $alert = "The student number ".$studentId." has already had this meal.<br><br><img src='../studentpics/".$pic."' height='350px' width='350px' >";
	
	  } elseif(empty($mealPlan['mealplan'])) {
	
	    $alert = "Student needs to pay. NO MEAL PLAN.<br><br><img src='../studentpics/".$pic."' height='350px' width='350px' >";
	
	  }else{
	
	    $mealId = $_COOKIE['mealId'];
	
	    $query = "INSERT INTO studentmeals(mealId, studentId) VALUES ('$mealId', '$studentId')";
	
	    mysql_query($query);
	
	    if(mysql_error()){
	
	      $alert = 'Contact the tech department at x1250'."\n\r".mysql_error();
	
	    } else {
	
	      $alert = "Have a nice meal. Student ID has been logged."."<BR><BR>"."<font size='+3'>".ucfirst($mealPlan['firstname'])." ".ucfirst($mealPlan['lastname'])."</font> <br><br><img src='../studentpics/".$pic."' height='350px' width='350px' >";
	
	
	    }
	
	  }
  }else {
  $sql = "SELECT sm.tranId FROM studentmeals as sm 
          JOIN faculty as f ON (f.facultyId = sm.studentId)
          WHERE f.facultyId = '$studentId' and sm.mealId = '$mealId'";
   connectDB();
   $hadMeal = runShortQuery($sql);
   //echo '  asdfa  '. $hadMeal;
   //TODO: Do I need to add error checking for the same person eating the same meal twice or is that allowed?
   $remainQuery = "SELECT meals, fname, lname FROM faculty where facultyId = '$studentId'";
   $res = mysql_query($remainQuery);
   echo mysql_error();
   $remaining = mysql_fetch_assoc($res);
   if($comp == false){
	   if($remaining['meals'] == 0){
	   		$alert = "Faculty must pay: NO MEALS remaining.";
	   }
	    else {
	   		$left = $remaining['meals'] - 1;
	   		$update = "UPDATE faculty SET meals = '$left' WHERE facultyId = '$studentId'";
	   		mysql_query($update);
	   		if(mysql_error()){
		      $alert = 'Contact the tech department at x1250'."\n\r".mysql_error();
		    }
	   		$mealId = $_COOKIE['mealId'];
		    $query = "INSERT INTO studentmeals(mealId, studentId) VALUES ('$mealId', '$studentId')";
		    mysql_query($query);
		    if(mysql_error()){
		      $alert = 'Contact the tech department at x1250'."\n\r".mysql_error();
		    }else{
	   			if($left < 5){
	   				if ($left==0)$left="NO MEALS";
	   				$alert = "Enjoy your meal. WARNING: $left remain! Add more meals soon."."<BR><BR>"."<font size='+3'>".ucfirst($remaining['fname'])." ".ucfirst($remaining['lname'])."</font><br><br>";
	   			}
		    	else $alert = "Enjoy your meal. Faculty meal has been logged."."<BR><BR>"."<font size='+3'>".ucfirst($remaining['fname'])." ".ucfirst($remaining['lname'])."</font><br><br>Left: $left";
		    }
	   }
  	}else{
  		$mealId = $_COOKIE['mealId'];
	    $query = "INSERT INTO studentmeals(mealId, studentId) VALUES ('$mealId', '$studentId')";
	    mysql_query($query);
	    if(mysql_error()){
	      $alert = 'Contact the tech department at x1250'."\n\r".mysql_error();
	    }else{
   			$alert = "Enjoy your meal. Faculty meal has been logged."."<BR><BR>"."<font size='+3'>".ucfirst($remaining['fname'])." ".ucfirst($remaining['lname'])."</font><br><br>Left: $remaining[meals]";
	    }
  	}
  }
}else if($comp != false){
	$insert = "insert into studentmeals(mealId, studentId) values ('$mealId', 0000)";
	mysql_query($insert);
	echo mysql_error();
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
    function markComp(){
    //var x = confirm("Are you sure you want to make this meal complimentary?")
    //alert(x)
    //if(x == true){
    	document.getElementById('comp').value = 1;
    //	document.getElementById('scanForm').submit();
    //}
    //return false;
    }
    function submitting(){
    	if(document.getElementById('comp').checked == true){
    		var x = confirm("Are you sure you want to make this meal coplimentary?")
    		if(x == false){
    			document.getElementById('comp').value = 0;
    			return false
    		}else{
    			return true
    		}
    	}
    }
    document.getElementById("comp").value = 0;

  //-->

  </script>

  <title>Get -a- Meal</title>

</head>

<body onLoad="javascript:focusing();setTimeout('redirect()',(1000*60*30));">

 <form id="scanForm" action="scan.php" method="post" onSubmit="return submitting();">

 <BR><BR><BR>

  <center><b>Scan ID:</b><BR><input type="text" id="barcode" name="barcode"></center>
  <center><input type="checkbox" value="1" name="comp" id="comp"> Complimentary</center>
 </form>
<br>
 <center><b><font color="red"><?=$alert?></font></b></center>

</body>

