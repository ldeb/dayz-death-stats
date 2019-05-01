<?php
include('../config.php');
include('functions.php');
/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simply to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

// DB table to use
// $table = 'deaths';
$select_steam_ids = $CONFIG['link_to_user_steam_profile'] ? ", killer.steam_id AS killer_steam_id, victim.steam_id AS victim_steam_id" : "";
$select_player_pos = $CONFIG['show_deaths_on_map'] ? ", d.victim_pos, d.killer_pos" : "";
$table =
   "(
      SELECT d.id, d.date, d.reason, d.distance, killer.name AS killer_name, victim.name AS victim_name".$select_player_pos.$select_steam_ids."
      FROM deaths d
      LEFT JOIN players killer ON killer.id = d.killer_id
      LEFT JOIN players victim ON victim.id = d.victim_id
    ) res";

// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
  array( 'db' => 'date', 'dt' => 0 ),
  array( 'db' => 'killer_name', 'dt' => 1 ),
  array( 'db' => 'victim_name', 'dt' => 2 ),
  array( 'db' => 'reason', 'dt' => 3 ),
  array( 'db' => 'distance', 'dt' => 4 ),
);

if( ! $CONFIG['link_to_user_steam_profile'] ) {
  // array_push($columns, array( 'db' => 'killer_name', 'dt' => 1 ));
  // array_push($columns, array( 'db' => 'victim_name', 'dt' => 2 ));
} else {
  // array_push($columns, array(
  //     'db'        => 'killer_name',
  //     'dt'        => 1,
  //     'formatter' => function( $d, $row ) {
  //       $res = $row['killer_name'];
  //       $res.= ( isset($row['killer_steam_id']) ) ? ' '.generete_user_link('+', $row['killer_steam_id']) : '';
  //       return $res;
  //     }
  //   )
  // );
  // array_push($columns, array(
  //     'db'        => 'victim_name',
  //     'dt'        => 2,
  //     'formatter' => function( $d, $row ) {
  //       $res = $row['victim_name'];
  //       $res.= ( isset($row['victim_steam_id']) ) ? ' '.generete_user_link('+', $row['victim_steam_id']) : '';
  //       return $res;
  //     }
  //   )
  // );
	array_push($columns, array( 'db' => 'killer_steam_id', 'dt' => 5 ));
  array_push($columns, array( 'db' => 'victim_steam_id', 'dt' => 6 ));
}
if( $CONFIG['show_deaths_on_map'] ) {
	array_push($columns, array( 'db' => 'killer_pos', 'dt' => 7 ));
  array_push($columns, array( 'db' => 'victim_pos', 'dt' => 8 ));
}
// SQL server connection information
$sql_details = array(
  'host' => $CONFIG['db_host'],
  'db'   => $CONFIG['db_base'],
  'user' => $CONFIG['db_user'],
  'pass' => $CONFIG['db_pass'],
);


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

require( 'ssp.class.php' );

echo json_encode(
  SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
);
