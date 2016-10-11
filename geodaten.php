<?php
header("access-control-allow-origin: *");

function radiusQuery($geom, $point, $radius) {
   return 'ST_Within('. $geom. ', ST_Transform(ST_Buffer(ST_Transform('.$point.', 3857), '.$radius.'), 4326))';
}

function distanceExpr($geom1, $geom2) {
   return 'ST_Distance(ST_Transform('.$geom1.', 3857),ST_Transform('.geom2.',3857))';
}

function queryMaxValue($attr, $table, $geom, $point, $radius) {
   return 'SELECT MAX('. $attr.') FROM '.$table.' WHERE ST_Distance(ST_Transform('.$point.', 3857) , ST_Transform('.$geom.', 3857)) < '.$radius;
}
$lat = filter_input(INPUT_GET, 'lat',FILTER_VALIDATE_FLOAT);
$lon = filter_input(INPUT_GET, 'lon',FILTER_VALIDATE_FLOAT);

if (!(($lat) && ($lon))) {
   echo 'WRONG INPUT';
   exit;
};

$point= 'ST_GeometryFromText( \'POINT ( '.$lon.' '.$lat.' )\', 4326)';
$con=pg_connect("dbname=gis") or print("cant connect");

$query = 'SELECT bemerkung,  region,  pk, vd , p.polizeirev, name, strasse, plz, ort, tel FROM PKGrenzen g, polizei p WHERE p.polizeirev=g.polizeirev and ST_Within('.$point.', wkb_geometry);';
$result = pg_query($con, $query);

if (!$result)  {
                  echo "An error occured.\n";
		  exit;
}
print '{"polizei":[';
$again="";
while ($row = pg_fetch_array($result, NULL, PGSQL_ASSOC)) {
      print $again . json_encode($row);
      $again=",";
}

print '],"ort":[';
$query = 'SELECT bezirk_name, stadtteil, ortsteilnummer, bezirk FROM verwaltungsgrenzen WHERE ST_Within('. $point .', wkb_geometry);';
$result = pg_query($con, $query);

if (!$result)  {
                  echo "An error occured.\n";
		  exit;
}
$again="";
while ($row = pg_fetch_array($result, NULL, PGSQL_ASSOC)) {
      print $again . json_encode($row);
      $again=",";
}

print '],"laerm_tag":[';

$query = queryMaxValue('klasse','laerm_tag', $point , 'wkb_geometry',25);
$result = pg_query($con, $query);

if (!$result)  {
                  echo "An error occured.\n";
		  exit;
}
$again="";
while ($row = pg_fetch_array($result, NULL, PGSQL_ASSOC)) {
      print $again . json_encode($row);
      $again=",";
}

print '],"laerm_nacht":[';

$query = queryMaxValue('klasse','laerm_nacht', $point , 'wkb_geometry',25);
$result = pg_query($con, $query);

if (!$result)  {
                  echo "An error occured.\n";
		  exit;
}
$again="";
while ($row = pg_fetch_array($result, NULL, PGSQL_ASSOC)) {
      print $again . json_encode($row);
      $again=",";
}



print '],"luftdaten":[';

$radius = 500;


$query = 'SELECT gid, name_12, no2_i1_gb, pm10_i1_gb, pm25_i1_gb,ST_AsText( geom), '.distanceExpr($point,'geom').' FROM luftdaten2015 WHERE ' . radiusQuery('geom',$point, $radius). ' AND ((no2_i1_gb!=0) OR (pm10_i1_gb!=0) OR (pm25_i1_gb!=0)) ORDER BY st_distance;';

$result = pg_query($con, $query);

if (!$result)  {
                  echo "An error occured.\n";
		  exit;
}
$again="";
while ($row = pg_fetch_array($result, NULL, PGSQL_ASSOC)) {
      print $again . json_encode($row);
      $again=",";
}


print  "]}";

pg_close($con);
