<?php
print "{validated:";
$con=pg_connect("dbname=gis") or print("cant connect");

$result = pg_query($con, "SELECT COUNT(*) from antraege WHERE mailchecked=true;");
$row = pg_fetch_row($result);

print $row[0];

print ",no_validated:";

$result = pg_query($con, "SELECT COUNT(*) from antraege WHERE mailchecked=false;");
$row = pg_fetch_row($result);

print $row[0];
print "}";


pg_close($con);

