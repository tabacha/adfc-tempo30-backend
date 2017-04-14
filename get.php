<?php
//header("access-control-allow-origin: *");

$id=filter_input(INPUT_GET, 'id',FILTER_VALIDATE_INT);
$secret= filter_input(INPUT_GET, 'secret',FILTER_SANITIZE_STRING);

$con=pg_connect("dbname=gis") or print("cant connect");

$query="SELECT name, strasse, hausnr,  plz, email, ST_X(pos) AS lon, ".
       "ST_Y(pos) AS lat, newsletter, ".
       "saveanschrift, showinmap,  bezirk, mailchecked, lastchanged, ".
       "created,  antragdate, lastasked, antrag_strasse, mailcontact, ".
       "adrchecked, status, belegwiderspruchabgabe, belegantragsabgabe, ".
       "urteildatum, klagedatum, widerspruchantwort, widersprucheingang, ". 
       "antwortaufantrag, lastasked, bezirk, id, secret ".
       "FROM antraege  WHERE id=$1 and secret=$2;";

$result = pg_prepare($con, "my_query", $query);

$result = pg_execute($con, "my_query", array($id, $secret));
if (!$result)  {
  echo "An error occured.\n";
  exit;
}
$row = pg_fetch_array($result, NULL, PGSQL_ASSOC);
print json_encode($row);
