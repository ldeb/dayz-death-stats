<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Generates a fake log file from KillFeed.json, to test dayzstats
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$conf_file = 'KillFeed/KillFeed.json';
$dest_file = 'KillFeed/KillFeed_test.log';

$y_diff = 15360;
$messages = array();
$strJsonFileContents = @file_get_contents($conf_file);
if( $strJsonFileContents == false ) die('<strong>'.$conf_file.'</strong> not found!');
$array = json_decode($strJsonFileContents, true);
// var_dump($array);
foreach ($array as $key => $value) {
  if(is_array($value) && isset( $value['Message']) ) {
    $pos1 = rand(0, $y_diff) . ', ' . rand(0, $y_diff) . ', ' . rand(0, 100);
    $pos2 = rand(0, $y_diff) . ', ' . rand(0, $y_diff) . ', ' . rand(0, 100);
    $msg = str_replace(['KillerInfo', 'VictimInfo', 'KillRange'], ['Player_killer (steam64id=xxxxxxxxxxxxxxxx pos=<'.$pos1.'>)', 'Player_victim (steam64id=yyyyyyyyyyyyyy pos=<'.$pos2.'>)', '6'], $value['Message']);
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

echo '<strong>'.$dest_file.'</strong> generated.';
