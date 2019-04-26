<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Generates a fake log file from KillFeed.json, to test dayzstats
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$conf_file = 'KillFeed/KillFeed.json';
$dest_file = 'KillFeed/KillFeed_test.log';

$messages = array();
$strJsonFileContents = file_get_contents($conf_file);
$array = json_decode($strJsonFileContents, true);
// var_dump($array);
foreach ($array as $key => $value) {
  if(is_array($value) && isset( $value['Message']) ) {
    $msg = str_replace(['KillerInfo', 'VictimInfo', 'KillRange'], ['Player_killer (steam64id=xxxxxxxxxxxxxxxx pos=<1648.1, 3593.0, 133.2>)', 'Player_victim (steam64id=yyyyyyyyyyyyyy pos=<1675.1, 3597.0, 133.6>)', '6'], $value['Message']);
    array_push($messages, $msg);
  }
}
// var_dump($messages);
$death_datetime = new DateTime( '2019-04-24T22:46:00Z' );
$contents = 'Log Created on '.$death_datetime->format('Y-m-d \a\t H:i:s').PHP_EOL;

foreach ($messages as $key => $value) {
  $death_datetime->add(new DateInterval('PT2S'));
  $contents.= $death_datetime->format('H:i:s').' | '.$value.PHP_EOL;
}
// echo $contents;
file_put_contents($dest_file, $contents);

echo $dest_file. ' generated.';
