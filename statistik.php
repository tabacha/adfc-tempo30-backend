<?php

$jsonresult = array();
$con=pg_connect("dbname=gis") or print("cant connect");

$result = pg_query($con, "SELECT COUNT(*) from antraege WHERE mailchecked=true;");
$row = pg_fetch_row($result);

$jsonresult['validated_by_email']=$row[0];

$result = pg_query($con, "SELECT COUNT(*) from antraege WHERE adrchecked=true;");
$row = pg_fetch_row($result);

$jsonresult['validated_by_adr']=$row[0];


$result = pg_query($con, "SELECT COUNT(*) from antraege WHERE mailchecked=false and adrchecked=false;");
$row = pg_fetch_row($result);

$jsonresult['no_validated']=$row[0];

$result = pg_query($con, "SELECT bezirk, COUNT(*)  from antraege where mailchecked=true group by bezirk;");

$resultArray = pg_fetch_all($result);
$jsonresult['validated_by_bezirk']=$resultArray;

$result = pg_query($con, "SELECT bezirk, COUNT(*)  from antraege where mailchecked=true and showinmap=true group by bezirk;");

$resultArray = pg_fetch_all($result);
$jsonresult['in_map_by_bezirk']=$resultArray;

$result = pg_query($con, "SELECT bezirk, COUNT(*)  from antraege where mailchecked=true and saveanschrift=true group by bezirk;");

$resultArray = pg_fetch_all($result);
$jsonresult['with_adr_by_bezirk']=$resultArray;

$result = pg_query($con, "SELECT bezirk, COUNT(*)  from antraege where mailchecked=true and newsletter=true group by bezirk;");

$resultArray = pg_fetch_all($result);
$jsonresult['newsletter_by_bezirk']=$resultArray;


$result = pg_query($con, "select lastchanged from antraege order by lastchanged desc limit 1;");
$row = pg_fetch_row($result);

$jsonresult['last_db_changed']=$row[0];


$jsonresult['timestamp']=date('Y-m-d H:i:s');
header('Content-Type: application/json; charset=utf-8');
echo json_encode($jsonresult,JSON_PRETTY_PRINT| JSON_NUMERIC_CHECK| JSON_UNESCAPED_UNICODE);

pg_close($con);

exit;
