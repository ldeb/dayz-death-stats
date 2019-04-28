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
      SELECT p.id, p.name, p.deaths, p.kills".$select_steam_ids."
      FROM players p
    ) res";

// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
  array( 'db' => 'kills', 'dt' => 1 ),
  array( 'db' => 'deaths', 'dt' => 2 ),
  array(
      'db'        => 'deaths',
      'dt'        => 3,
      'formatter' => function( $d, $row ) {
        $ratio = 0;
        $deaths = intval($row['deaths']);
        $kills = intval($row['kills']);
        // if ( $kills > $deaths ) {
        //   if ( $deaths > 0 ) {
        //     $ratio = round( $kills / $deaths, 3 );
        //   } else $ratio = $kills;
        // }
        // else if ( $deaths > $kills  ) {
        //   if ( $kills > 0 ) {
        //     $ratio = round( $deaths / $kills, 3 );
        //   } else $ratio = $deaths;
        //   $ratio = $ratio * -1;
        // }
        if ( $deaths > 0 ) {
          $ratio = round( $kills / $deaths, 4 );
        } else $ratio = $kills;
        return $ratio;
      }
    )
);

if( ! $CONFIG['link_to_user_steam_profile'] ) {
  array_push($columns, array( 'db' => 'name', 'dt' => 0 ));
} else {
  array_push($columns, array(
      'db'        => 'name',
      'dt'        => 0,
      'formatter' => function( $d, $row ) {
        $res = $row['name'];
        $res.= ( isset($row['steam_id']) ) ? ' '.generete_user_link('+', $row['steam_id']) : '';
        return $res;
      }
    )
  );
	array_push($columns, array( 'db' => 'steam_id', 'dt' => 5 ));
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
