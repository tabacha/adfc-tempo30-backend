<?php

$sql=array();
function parseDate($param) {
  global $sql;
  $tmp=filter_input(INPUT_POST, $param, FILTER_SANITIZE_STRING);
  try {
    $tmpDate = DateTime::createFromFormat('Y-m-d', $tmp);
    if ($tmpDate == FALSE) {
      $sql[$param] = NULL;
    } else {
      $sql[$param] = $tmpDate->format('Y-m-d');
    };
  } catch (Exception $e) {
    $sql[$param] = NULL;
    unset($e);
  }
}

$id=filter_input(INPUT_POST, 'id',FILTER_VALIDATE_INT);
$secret= filter_input(INPUT_POST, 'secret',FILTER_SANITIZE_STRING);

$options=array('options'=>array('min_range'=>0, 'max_range'=>15));
$sql['status']=filter_input(INPUT_POST, 'status',FILTER_VALIDATE_INT, $options);

$sql['belegwiderspruchabgabe'] = filter_input(INPUT_POST, 'belegwiderspruchabgabe',
  FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

$sql['belegantragsabgabe'] =  filter_input(INPUT_POST, 'belegantragsabgabe',
  FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);


parseDate('urteildatum');
parseDate('klagedatum');
parseDate('widerspruchantwort');
parseDate('widersprucheingang');
parseDate('antwortaufantrag');
parseDate('antragdate');

$now= new DateTime();
$sql['lastchanged']= $now->format('Y-m-d H:i:s');

$con=pg_connect("dbname=gis") or print("cant connect");
$result = pg_update($con, 'antraege', $sql, array('id'=>$id,'secret'=>$secret));
if (!$result)  {
  echo "An error occured.\n";
  exit;
}
$row = array('status' => 1);
print json_encode($row);

pg_close($con);
