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
$select_steam_ids = $CONFIG['link_to_user_steam_profile'] ? ", p.steam_id" : "";
$table =
   "(
      SELECT (@row_number:=@row_number + 1) AS num, p.id, p.name, p.deaths, p.kills, IF(p.deaths=0, 9999, p.kills/p.deaths) AS ratio".$select_steam_ids."
      FROM players p, (SELECT @row_number:=0) AS t
      ORDER BY ratio DESC, p.deaths ASC, p.kills DESC, name ASC
    ) res";

// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
  array( 'db' => 'num', 'dt' => 'rank' ),
  array( 'db' => 'name', 'dt' => 'name' ),
  array( 'db' => 'kills', 'dt' => 'kills' ),
  array( 'db' => 'deaths', 'dt' => 'deaths' ),
  array( 'db' => 'ratio', 'dt' => 'ratio' ),
  // array(
  //     'db'        => 'deaths',  // doesn't matter
  //     'dt'        => 4,
  //     'formatter' => function( $d, $row ) {
  //       $ratio = 0;
  //       $deaths = intval($row['deaths']);
  //       $kills = intval($row['kills']);
  //       if ( $deaths > 0 ) {
  //         $ratio = round( $kills / $deaths, 4 );
  //       } else $ratio = $kills;
  //       return $ratio;
  //     }
  //   )
);

if( ! $CONFIG['link_to_user_steam_profile'] ) {
  // array_push($columns, array( 'db' => 'name', 'dt' => 1 ));
} else {
  // array_push($columns, array(
  //     'db'        => 'name',
  //     'dt'        => 1,
  //     'formatter' => function( $d, $row ) {
  //       $res = $row['name'];
  //       $res.= ( isset($row['steam_id']) ) ? ' '.generete_user_link('+', $row['steam_id']) : '';
  //       return $res;
  //     }
  //   )
  // );
	array_push($columns, array( 'db' => 'steam_id', 'dt' => 'steam_id' ));
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
