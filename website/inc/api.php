<?php
include('../config.php');
include "Medoo.php";
use Medoo\Medoo;
// Initialize
$database = new Medoo([
    'database_type' => 'mysql',
    'server'        => $CONFIG['db_host'],
    'database_name' => $CONFIG['db_base'],
    'username'      => $CONFIG['db_user'],
    'password'      => $CONFIG['db_pass'],
]);
//
// $data = $database->select('players', [
//     'name',
//     'deaths',
//     'kills'
// ])
if(isset($_GET) && ! empty($_GET)) {
  switch($_GET['mode']){
    case 'causes':
      $data = $database->query("SELECT count(id) as NB, reason FROM `deaths` group by reason order by NB DESC, reason ASC;")->fetchAll();
      echo json_encode($data);
      break;
    default:
      echo json_encode(array());
  }
}
