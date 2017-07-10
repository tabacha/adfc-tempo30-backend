<?php

$id=filter_input(INPUT_POST, 'id',FILTER_VALIDATE_INT);
$secret= filter_input(INPUT_POST, 'secret',FILTER_SANITIZE_STRING);

$sql['newsletter'] = filter_input(INPUT_POST, 'newsletter',
  FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
$sql['mailcontact'] = filter_input(INPUT_POST, 'mailcontact',
    FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
$sql['saveanschrift'] = filter_input(INPUT_POST, 'saveanschrift',
    FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
$sql['showinmap'] = filter_input(INPUT_POST, 'showinmap',
    FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
$sql['automail'] = filter_input(INPUT_POST, 'automail',
    FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);    
$lat=filter_input(INPUT_POST, 'lat',FILTER_VALIDATE_FLOAT);
$lon=filter_input(INPUT_POST, 'lon',FILTER_VALIDATE_FLOAT);
$sql['name']=filter_input(INPUT_POST, 'name',FILTER_UNSAFE_RAW);
$sql['strasse']=filter_input(INPUT_POST, 'strasse',FILTER_UNSAFE_RAW);
$sql['hausnr']=filter_input(INPUT_POST, 'hausnr',FILTER_UNSAFE_RAW);
$sql['plz']=filter_input(INPUT_POST, 'plz',FILTER_SANITIZE_STRING);
$sql['bezirk']=filter_input(INPUT_POST, 'bezirk',FILTER_SANITIZE_STRING);
$sql['antrag_strasse']=filter_input(INPUT_POST, 'antrag_strasse',FILTER_UNSAFE_RAW);

$pos='';
 if (($lat) && ($lon)) {
   $pos= 'ST_GeometryFromText( \'POINT ( '.$lon.' '.$lat.' )\', 4326)';
 } else {
   $sql['pos'] = NULL;
 };

 $now= new DateTime();
 $sql['lastchanged']= $now->format('Y-m-d H:i:s');

 $con=pg_connect("dbname=gis") or print("cant connect");
 $result = pg_update($con, 'antraege', $sql, array('id'=>$id,'secret'=>$secret));
 if (!$result)  {
   echo "An error occured.\n";
   exit;
 }
if ($pos != '') {
 $query="UPDATE antraege SET pos=$pos WHERE id=$id;";
      $res = pg_query($con, $query);

}

$row = array('status' => 1);
print json_encode($sql);
