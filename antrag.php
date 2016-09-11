<?php
header("access-control-allow-origin: *");
$basename = "Tempo30_Antrag";

$ext=".docx";
$doctype= "Word2007";

//$ext=".html"; $doctype= "HTML";

//$ext=".odt"; $doctype= "ODText";

$json= $_POST['data'];
//$json='{"str":"Cuxhavener Straße","name":"Max Mustermann","hausnr":"78","plz":"21149","lat":"53.4715402","lon":"9.90065095","antrag":["Waltershofer Straße","Cuxhavener Straße"],"polizei":[{"bemerkung":"PK-Grenzen","region":"Harburg","pk":"PK 47","vd":"VD 4","polizeirev":"47","name":"PK47 Neugraben","strasse":"Neugrabener Markt 3","plz":"21149","ort":"Hamburg","tel":"040 428 65-4710"}],"ort":[{"bezirk_name":"Harburg","stadtteil":"Hausbruch","ortsteilnummer":"714","bezirk":"7"}],"laerm_tag":[{"klasse":"5"}],"laerm_nacht":[{"klasse":"5"}],"luftdaten":[{"gid":"2183","name_12":"Bundesstrae B73","no2_i1_gb":"30.15055","pm10_i1_gb":"25.4","pm25_i1_gb":"18.7","st_astext":"MULTILINESTRING((9.89456688524801 53.4717257209532,9.89706589630153 53.4716198843808,9.89755441490041 53.4715843238093,9.89812061423444 53.4715334425004,9.89875985895506 53.4714404078619))","st_distance":"4.47805368471465"}]}';
#print $json;
$data=json_decode($json);
#print var_dump($data);
#print var_dump($data->{'polizei'}[0]->{'name'});
#exit;
header( "Content-Type:   application/octet-stream" );// you should look for the real header that a word2007 document needs!!!
header( 'Content-Disposition: attachment; filename='.$basename.$ext );

require_once 'bootstrap.php';

// Creating the new document...
$phpWord = new \PhpOffice\PhpWord\PhpWord();

/* Note: any element you append to a document must reside inside of a Section. */

$phpWord->setDefaultParagraphStyle(
    array(
        'alignment'  => \PhpOffice\PhpWord\SimpleType\Jc::BOTH,
//        'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(12),
        'spacing'    => 120,
    )
);

// Adding Text element with font customized using named font style...
/*$adfcTextStyleName = 'ADFCtext';
$phpWord->addFontStyle(
    $adfcTextStyleName,
    array('name' => 'Arial', 'size' => 12)
);*/

forEach ( $data->{'antrag'} as $strasse) {
$section = $phpWord->addSection();
$section->addText("Absender: ".$data->{'name'}." ".$data->{'str'}." ".$data->{'plz'}." Hamburg");
$section->addTextBreak();
$section->addText("An");
$section->addText($data->{'polizei'}[0]->{'name'});
$section->addText("- Straßenverkehrsbehörde -");
$section->addText($data->{'polizei'}[0]->{'strasse'});
$section->addText($data->{'polizei'}[0]->{'plz'}." ".$data->{'polizei'}[0]->{'ort'});
$section->addTextBreak();
$d=strtotime("now");
/*$section->addText("Hamburg den ".date("d.m.Y",$d), 
  array(
        'alignment'  => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT
    ));
*/
$section->addTextBreak();
$section->addText('Antrag auf verkehrsbeschränkende Maßnahmen, z.B. Tempo 30, auf der '.$strasse.' nach § 45 Abs. 1 Satz 2 Nr. 3 StVO', array('name' => 'Arial', 'size' => 13, 'bold' => true));
$section->addTextBreak();
$section->addText('Sehr geehrte Damen und Herren,');

$section->addText('als Anwohnerin der '.$data->{'str'}.' bin ich stark durch Verkehrslärm sowie Schadstoffemissionen '.
'betroffen und in Sorge um meine Gesundheit und die meiner Familie. Ich beantrage daher bei Ihnen '.
'für die '.$strasse.' unverzüglich verkehrsbeschränkende Maßnahmen nach § 45 StVO anzuordnen.');

$section->addText('Die '.$strasse.' liegt im '.$data->{'ort'}[0]->{'bezirk_name'}.' und ist überwiegend durch Wohngebäude gesäumt, in '.
'denen viele Familien mit kleinen Kindern leben und die unmittelbar an die Straße angrenzen. Dies gilt '.
'auch für das Gebäude in dem unsere Wohnung liegt. Dabei sind das Wohnzimmer sowie das '.
'Kinderzimmer unserer Wohnung direkt zur Straße hin ausgerichtet.');

$section->addText('Die Nutzung der Wohnung wird derzeit durch den starken Verkehr auf der '.$strasse.' und die '.
'hiervon ausgehenden Emissionen in unzumutbarer Art und Weise gestört. So ist z.B. tagsüber eine '.
'normale Unterhaltung bei geöffnetem Fenster nicht möglich. Nachts wiederum ist ein ungestörtes '.
'Durchschlafen aufgrund des Verkehrslärms nicht garantiert. Dies ist vor allem auch auf den hohen '.
'Anteil an LKW und Linienbussen zurückzuführen, die täglich auf der '.$strasse.' verkehren. Diese '.
'Fahrzeuge tragen dabei auch, da sie in der Regel dieselbetrieben fahren, maßgeblich zu einer '.
'unverhältnismäßig hohen Abgasbelastung – hierbei vor allem Stickstoffdioxid und Feinstaub – bei.');

if ((count($data->{'luftdaten'})>0)) {
$section->addText('Folgende Luftdaten wurden laut Behörde für Umwelt und Energie Hamburg gemessen:');

forEach ( $data->{'luftdaten'} as $ld) {
   //"gid":"2183","name_12":"Bundesstrae B73","no2_i1_gb":"30.15055","pm10_i1_gb":"25.4","pm25_i1_gb":"18.7","st_astext":"MULTILINESTRING((9.89456688524801 53.4717257209532,9.89706589630153 53.4716198843808,9.89755441490041 53.4715843238093,9.89812061423444 53.4715334425004,9.89875985895506 53.4714404078619))","st_distance":"4.47805368471465"
   $section->addText(sprintf('        In %d m Entfernung: %.1f NO²  &#181;g/m³ in 1 h, %.1f PM10  &#181;g/m³ in 1 h, %.1f PM2,5  &#181;g/m³ in 1 h.',$ld->{'st_distance'},  $ld->{'no2_i1_gb'}, $ld->{'pm10_i1_gb'}, $ld->{'pm25_i1_gb'})   );
   if ($ld->{'pm25_i1_gb'} >25) {
      $section->addText('Der Grenzwert von 25 PM2.5  &#181;g/m³ in 1 h wurde überschritten.');
   } else if ($ld->{'pm25_i1_gb'} >20) {
      $section->addText('Der Grenzwert von 20 PM2.5  &#181;g/m³ in 1 h, der ab den Jahr 2020 gelten wird, wurde überschritten.');
   }
   if ($ld->{'pm10_i1_gb'} >40 ) {
      $section->addText('Der Grenzwert von 40 PM10  &#181;g/m³ in 1 h wurde überschritten.');
    }
   if ($ld->{'no2_i1_gb'} >40 ) {
      $section->addText('Der Grenzwert von 40 NO² &#181;g/m³ in 1 h wurde überschritten.');
    }
}

}


$dbATag = array(
    "1" => "55 bis 60 dB(A)",
    "2" => "60 bis 65 dB(A)",
    "3" => "65 bis 70 dB(A)",
    "4" => "70 bis 75 dB(A)",
    "5" => "über 75 dB(A)",
);

$dbANacht = array(
    "1" => "45 bis 50 dB(A)",
    "2" => "50 bis 55 dB(A)",
    "3" => "55 bis 60 dB(A)",
    "4" => "60 bis 65 dB(A)",
    "5" => "65 bis 70 dB(A)",
    "6" => "über 70 dB(A)",
);


if ((count($data->{'laerm_tag'})>0) && (count($data->{'laerm_nacht'})>0)) {
  $section->addText('Laut der von der Behörde für Umwelt und Energie veröffentlichten Lärmkarte ist die Lärmbelastung bei meiner Wohnung am Tag in der Klasse:'.$data->{'laerm_tag'}[0]->{'klasse'} . ', also '.$dbATag{$data->{'laerm_tag'}[0]->{'klasse'}}.
 ', und in der Nacht in der Klasse: '.$data->{'laerm_nacht'}[0]->{'klasse'}.', also '.
 $dbANacht{$data->{'laerm_nacht'}[0]->{'klasse'}}.'.' );

} else {
if (count($data->{'laerm_tag'})>0) {
  $section->addText('Laut der von der Behörde für Umwelt und Energie veröffentlichten Lärmkarte ist die Lärmbelastung bei meiner Wohnung am Tag in der Klasse:'.$data->{'laerm_tag'}[0]->{'klasse'} . ', also '.$dbATag{$data->{'laerm_tag'}[0]->{'klasse'}}.'.');
}
if (count($data->{'laerm_nacht'})>0) {
  $section->addText('Laut der von der Behörde für Umwelt und Energie veröffentlichten Lärmkarte ist die Lärmbelastung bei meiner Wohnung in der Nacht in der Klasse: '.$data->{'laerm_nacht'}[0]->{'klasse'}.', also '.$dbANacht{$data->{'laerm_nacht'}[0]->{'klasse'}}.'.' );
}
}

$section->addText('Bei Lärmwerten von mehr als 49 dB(A) in der Nacht bzw. 59 dB(A) am Tage ist davon auszugehen, '.
'dass zunehmend erhebliche Belästigungen und gesundheitliche Beschwerden auftreten. Nach '.
'Auffassung des Bundesverwaltungsgerichts ist die zuständige Straßenverkehrsbehörde daher bei '.
'Erreichen dieser Werte verpflichtet im Ermessenswege konkrete lärmmindernde Maßnahmen zu '.
'erwägen und die Belange der Betroffenen mit den Belangen des Verkehrs abzuwägen. Bei Werten '.
'von mehr als 60 dB(A) in der Nacht bzw. 70 dB(A) am Tage könne weiterhin davon ausgegangen '.
'werden, dass den Betroffenen in der Regel ein Rechtsanspruch auf Lärmschutz zustehe, da bei diesen '.
'Werten eine erhebliche Gesundheitsgefährdung vorliegt. Letzteres gilt nach Ansicht des '.
'Bundesverwaltungsgerichts übrigens auch dann, wenn die Grenzwerte für Stickstoffdioxid oder '.
'Feinstaub, die im Rahmen der 39. BImSchV definiert sind, erreicht oder überschritten werden. In '.
'diesen Fällen ist die zuständige Straßenverkehrsbehörde daher nicht nur zur Prüfung sondern '.
'ausdrücklich auch zum einem Einschreiten verpflichtet.');


$section->addText('Um den erheblichen Störungen und Gesundheitsgefahren, denen wir uns derzeit durch die '.
'Verkehrsemissionen ausgesetzt sehen, entgegen zu wirken, beantrage ich eine Begrenzung der '.
'Geschwindigkeit auf der '.$strasse.' auf maximal 30 km/h. Diese Maßnahme kann ohne größeren '.
'Aufwand und ohne tiefgreifende Eingriffe in das Verkehrsnetz kurzfristig umgesetzt werden und hat '.
'– wie z.B. der Hamburger Lärmaktionsplan verdeutlicht –, insbesondere in Verbindung mit einer '.
'gleichzeitigen Verstetigung des Verkehrs, ein sehr hohes Potenzial um den Verkehrslärm und die '.
'Abgasbelastung auf der Beispielstraße nachhaltig zu senken.'); 

$section->addText('Eine Einführung von Tempo 30 auf der '.$strasse.' ist darüber hinaus auch aus Gründen der '.
'Sicherheit und Ordnung des Verkehrs dringend geboten. Denn die Straße wird als Wohn- und '.
'Verbindungsstraße auch stark durch Radfahrer und Fußgänger genutzt, wobei der Fahrradverkehr '.
'auf der Straße geführt wird. Insbesondere Kinder, die die '.$strasse.' tagtäglich auf ihrem Weg '.
'von und zur Schule nutzen, werden durch den unangemessen schnellen Kfz-Verkehr gefährdet.');

$section->addText('Hilfsweise beantrage ich die Vornahme anderer straßenverkehrsrechtlicher und/ oder allgemeiner '.
'Maßnahmen zum Schutz vor Lärm und Abgasen auf der '.$strasse.'.');

$section->addText('Ich bitte darum, die Maßnahmen in enger Abstimmung mit den Anwohnerinnen und Anwohnern zu '.
'treffen und diese bei der Entscheidung über den Antrag und im weiteren Verfahren angemessen zu beteiligen.');
$section->addTextBreak();

$d=strtotime("+30 Days");

$section->addText('Sollte die Zuständigkeit für die Einrichtung verkehrsbeschränkender Maßnahmen nach § 45 Abs. 1 '.
'Satz 2 Nr. 3 StVO bei einer anderen Hamburger Behörde als der Ihren angesiedelt sein, bitte ich Sie, '.
'dieses Schreiben ggf. weiter zu leiten und mich diesbezüglich zu informieren. '.
'Über eine Rückmeldung bis zum '.date("d.m.Y",$d).' freue ich mich.');
$section->addTextBreak();

$section->addText('Mit freundlichen Grüßen');
$section->addTextBreak();
$section->addTextBreak();

$section->addText('Hamburg, '.date("d.m.Y").', '.$data->{'name'});
$section->addPageBreak();
}
/*// Saving the document as OOXML file...
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save('helloWorld.docx');

// Saving the document as ODF file...
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'ODText');
$objWriter->save('helloWorld.odt');

// Saving the document as HTML file...
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
$objWriter->save('helloWorld.html');*/
//$h2d_file_uri = tempnam( "", "htd" );
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter( $phpWord, $doctype);
$objWriter->save( "php://output" );
