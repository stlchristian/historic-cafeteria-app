<?php //reports for meals, and types.
include_once('functions.php');
connectDb();
$sql = "SELECT mealid, mealTime from meal order by mealId desc limit 15";
$bar = mysql_query($sql);
echo mysql_error();
while($dummy = mysql_fetch_row($bar)){
  if(!empty($dummy)){
    $mealsContainer[] = $dummy;
  }
}
foreach($mealsContainer as $meals){
    $query = "SELECT COUNT(*) from studentMeals where mealId = '$meals[0]'";
    $foo = mysql_query($query);
    echo mysql_error();
    while($dummy = mysql_fetch_row($foo)){
      $peopleAt[$meals[1]] = $dummy[0];
    }

}
//$peopleAt = array_reverse($peopleAt);
?>
<html>
<head>
<title>Meal Reports</title>
<link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<center>
<BR><BR>
(Ordered from most recent, to oldest)
<BR><BR>
<table>
  <tr>
    <td class="count">&nbsp;</td>
    <td><b>Date:</b></td>
    <td><b>Meal:</b></td>
    <td><b># of people:</b></td>
  </tr>
  <? $i = 0;$j = 1; if(!empty($peopleAt)): foreach($peopleAt as $count=>$meal):$type = strtotime($count); $count = date('n/j/y h:i a',strtotime($count));?>
  <tr <?=($i % 2 == 0)?'bgcolor="#DFDFDF"':''?>>
    <td class='count'><?=$j++?></td>
    <td><?=$count?></td>
    <td><?
    if(date('a',$type) == 'am'){
      if(date('h',$type) > 9){
        echo 'Lunch';
      }else{
        echo 'Breakfast';
      }
    }else{
      if(date('h', $type) > 2){
        echo 'Dinner';
      }else{
        echo 'Lunch';
      }
    }
    ?> </td>
    <td><?=$meal?></td>
  <tr>
  <? $i++; endforeach; ?>
  <? else:?>
  <tr>
    <td colspan='3'>No Meals to be shown.</td>
  <? endif;?>
</table>
</center>
</body>
</html>
