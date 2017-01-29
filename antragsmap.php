<?php
$con=pg_connect("dbname=gis") or print("cant connect");

$result = pg_query($con, "SELECT id, status, ST_X(pos) as lon, ST_Y(pos) AS lat FROM antraege WHERE mailchecked=true AND showinmap=true;");

$resultArray = pg_fetch_all($result);
print json_encode($resultArray);

