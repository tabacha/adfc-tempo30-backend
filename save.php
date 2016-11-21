<?php
//header("access-control-allow-origin: *");

function random_password( $length = 8 ) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $password = substr( str_shuffle( $chars ), 0, $length );
    return $password;
}
/*
*/
$sql['newsletter']=filter_input(INPUT_POST, 'newsletter',FILTER_VALIDATE_BOOLEAN);
$sql['saveanschrift']= filter_input(INPUT_POST, 'saveAnschrift',FILTER_VALIDATE_BOOLEAN);
$sql['showinmap']=filter_input(INPUT_POST, 'showInMap',FILTER_VALIDATE_BOOLEAN);
$sql['mailcontact']=filter_input(INPUT_POST, 'mailContact',FILTER_VALIDATE_BOOLEAN);
$sql['nolimit']=filter_input(INPUT_POST, 'noLimit',FILTER_VALIDATE_BOOLEAN);
$sql['name']=filter_input(INPUT_POST, 'name',FILTER_UNSAFE_RAW);
$sql['strasse']=filter_input(INPUT_POST, 'strasse',FILTER_UNSAFE_RAW);
$sql['plz']=filter_input(INPUT_POST, 'plz',FILTER_SANITIZE_STRING);
$sql['hausnr']=filter_input(INPUT_POST, 'hausnr',FILTER_SANITIZE_STRING);
$sql['email']=filter_input(INPUT_POST, 'email',FILTER_VALIDATE_EMAIL);
$sql['bezirk']=filter_input(INPUT_POST, 'bezirk',FILTER_SANITIZE_STRING);
$sql['antrag_strasse']=filter_input(INPUT_POST, 'antrag_strasse',FILTER_UNSAFE_RAW);
$sql['mailchecked']=false;
$sql['secret']=random_password(6);

$pos="";

if (($sql['showinmap']) || ($sql['nolimit'])) {
   $lat = filter_input(INPUT_POST, 'lat',FILTER_VALIDATE_FLOAT);
   $lon = filter_input(INPUT_POST, 'lon',FILTER_VALIDATE_FLOAT);
   if (!(($lat) && ($lon))) {
      echo 'WRONG INPUT';
      exit;
   };
   $pos= 'ST_GeometryFromText( \'POINT ( '.$lon.' '.$lat.' )\', 4326)';
}

$con=pg_connect("dbname=gis") or print("cant connect");

$res= pg_insert($con,'antraege',$sql);
if ($res) {
   $insert_query = pg_query($con, "SELECT lastval();");
   $insert_row = pg_fetch_row($insert_query);
   $id = $insert_row[0];

   if ($pos != '') {
      $query="UPDATE antraege SET pos=$pos WHERE id=$id;";
      $res = pg_query($con, $query);
   }
}
if ($res) {
     echo "OK $id\n";
     if ($sql['email']) {
       $url='https://tools.adfc-hamburg.de/tempo30-backend/master/validate.php?id='.$id."&secret=".$sql['secret'];
      $header = 'From: webmaster@tools.adfc-hamburg.de' . "\n" .
       'Reply-To: laeuft@hamburg.adfc.de' . "\n" .
       'X-Mailer: PHP/' . phpversion(). "\n";
       $header .= "Content-Type: text/plain; charset = \"UTF-8\";\n";
       $header .= "Content-Transfer-Encoding: 8bit\n";
       $header .= "\n";

      $nachricht = "Sie haben auf hamburg.adfc.de einen Tempo-30-Antrag gestellt. Bitte bestätigen Sie Ihre E-Mail-Adresse, indem Sie auf folgenden Link Klicken.:\n".$url."\nSollten Sie diesen Antrag nicht gestellt haben, ignorieren Sie bitte diese E-Mail. Für Rückfragen stehen wir Ihnen gerne zur Verfügung.\n\nIhr ADFC Hamburg";
          mail($sql['email'], 'Ihr Tempo30 Antrag', $nachricht, $header);
     };
} else {
     echo "ERROR\n";
}
pg_close($con);

