<?
function connectDB(){
  mysql_connect('localhost', 'tech','dr61q7a');
  mysql_select_db('tech');
  echo mysql_error();
}
function runQuery($query){
  $results = mysql_query($query);
  echo "<BR>".mysql_error()."<BR>";
  while($dummy = mysql_fetch_row($results)){
    if(!empty($dummy)){
      $return[] = $dummy;
    }
  }
  echo "<BR>".mysql_error()."<BR>";
  return $return;
}
function runShortQuery($query){
  $results = mysql_query($query);
  echo "<BR>".mysql_error()."<BR>";
  while($dummy = mysql_fetch_row($results)){
    if(!empty($dummy)){
      $return = $dummy;
    }
  }
  echo "<BR>".mysql_error()."<BR>";
  return $return;
}
?>
