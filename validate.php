<?php
//header("access-control-allow-origin: *");

$id=filter_input(INPUT_GET, 'id',FILTER_VALIDATE_INT);
$secret= filter_input(INPUT_GET, 'secret',FILTER_SANITIZE_STRING);

$con=pg_connect("dbname=gis") or print("cant connect");

$query="UPDATE antraege SET lastchanged=now(), mailchecked=True   WHERE id=$id and secret='$secret' and mailchecked=False;";
$res = pg_query($con, $query);



if ($res) {
   $num = pg_affected_rows($res);
   if ($num==1) {
     echo "Ok Vielen Dank, Ihre E-Mailaddresse wurde best&auml;tigt, sie können jetzt das Fenster schließen.\n";
   } else {
     echo "Fehler, Secret falsch oder E-Mail wurde bereits best&auml;tigt\n";
   }
} else {
     echo "ERROR\n";
}
pg_close($con);

