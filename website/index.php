<?php
include('config.php');
include('inc/functions.php');
if( isset( $_GET['logfile']) ) {
  $CONFIG['logfile'] = $_GET['logfile'];
}
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>ElCanu's ABFW DayZ server</title>
    <link rel="icon" href="img/avatar.png" type="image/png">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!-- Datatable -->
    <!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css"> -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.18/cr-1.5.0/fh-3.1.4/rg-1.1.0/sl-1.3.0/datatables.min.css"/>

    <link rel="stylesheet" type="text/css" href="inc/style.css"/>

  </head>

  <body id="page-top">

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
      <div class="container">
        <a class="navbar-brand js-scroll-trigger" href="index.php">
          <img src="https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/89/89783c79efdebec2cbacb438c39a1439e261abc9.jpg" alt=""> ElCanu's
          ABFW DayZ server
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav ml-auto">
            <?php if( $CONFIG['use_info_section'] ) : ?>
            <li class="nav-item">
              <a class="nav-link btn0 btn-sm0 btn-outline-secondary0 mx-1 js-scroll-trigger" href="#infos"><span class="fas fa-info-circle"></span> Infos</a>
            </li>
            <?php endif; ?>
            <?php if( $CONFIG['use_database'] ) : ?>
            <li class="nav-item">
              <a class="nav-link btn0 btn-sm0 btn-outline-secondary0 mx-1 js-scroll-trigger" href="#stats"><span class="fas fa-chart-bar"></span> Statistics</a>
            </li>
            <?php endif; ?>
            <li class="nav-item">
              <a class="nav-link btn0 btn-sm0 btn-outline-secondary0 mx-1 js-scroll-trigger" href="#deathslogs"><span class="fas fa-list-ul"></span> Deaths logs</a>
            </li>
            <?php if( $CONFIG['show_deaths_on_map'] ) : ?>
            <li class="nav-item">
              <a class="nav-link btn0 btn-sm0 btn-outline-secondary0 mx-1 js-scroll-trigger" href="#deathmap"><span class="fas fa-map-marked-alt"></span> Deaths Map</a>
            </li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </nav>

    <?php include 'infos.php' ?>

    <?php if( $CONFIG['use_database'] ) : ?>
    <section id="stats" class="">
      <div class="container">
        <div class="row">
          <div class="col-lg-12 mx-auto">

            <h2 class="mb-3">
              <span class="fas fa-chart-bar"></span> Statistics
            </h2>
            <div class="col-lg-12 mx-auto my-4">
            </div>

            <ul class="nav nav-tabs btn-group btn-group-toggle" role="tablist">
              <li class="nav-item">
                <a class="nav-link active font-weight-bold0" id="players-tab" data-toggle="tab" href="#players" role="tab" aria-controls="players" aria-selected="true"><span class="fas fa-user-friends"></span> Players</a>
              </li>
              <li class="nav-item">
                <a class="nav-link font-weight-bold0" id="causes-tab" data-toggle="tab" href="#causes" role="tab" aria-controls="causes" aria-selected="false"><span class="fas fa-cross"></span> Death causes</a>
              </li>
            </ul>

            <div class="tab-content mt-4">

              <button type="button" class="btn btn-sm btn-outline-secondary btn-clear-cookies float-right" title="clear cookies" data-toggle="tooltip"><span class="fas fa-trash-alt"></span></button>

              <div class="tab-pane fade show active" id="players" role="tabpanel" aria-labelledby="players-tab">
                <table class="datatable table table-striped table-hover table-bordered table-sm table-responsive-sm0 table-players" width="100%">
                  <thead class="bg-secondary text-white">
                    <tr>
                      <th class="font-weight-normal"><span class="fas fa-signal"></span> kill death ratio</th>
                      <th class="font-weight-normal"><span class="fas fa-star"></span> rank</th>
                      <th class="font-weight-normal"><span class="fas fa-user"></span> name</th>
                      <th class="font-weight-normal"><span class="fas fa-crosshairs"></span> kills</th>
                      <th class="font-weight-normal"><span class="fas fa-skull-crossbones"></span> deaths</th>
                    </tr>
                  </thead>
                  <tbody class="small"></tbody>
                </table>
              </div>

              <div class="tab-pane fade" id="causes" role="tabpanel" aria-labelledby="causes-tab">
                <table class="datatable table table-striped table-hover table-bordered table-sm table-responsive-sm0 table-causes" width="100%">
                  <thead class="bg-secondary text-white">
                    <tr>
                      <th class="font-weight-normal"><span class="fas fa-calculator"></span> count</th>
                      <th class="font-weight-normal"><span class="fas fa-bolt"></span> cause</th>
                    </tr>
                  </thead>
                  <tbody class="small"></tbody>
                </table>
              </div>

            </div>

          </div>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <section id="deathslogs" class="">
      <div class="container">
        <div class="row">
          <div class="col-lg-12 mx-auto">

            <h2 class="mb-3"><span class="fas fa-list-ul"></span> Deaths logs</h2>
            <?php
            if( ! $CONFIG['use_database'] ) :
              $results = parse_log($CONFIG);
              // var_dump($results);

              // DEBUG: Parse errors
              if( $CONFIG['DEBUG'] && isset($results) && isset($results['skipped']) && ! empty($results['skipped']) ) : ?>
                <div class="card">
                  <div class="card-header" id="headingMissed">
                    <h2 class="mb-0">
                      <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseMissed" aria-expanded="false" aria-controls="collapseMissed">
                        <?=count($results['skipped'])?> parsing deaths missed <em>(probably hits only)</em></strong>
                      </button>
                    </h2>
                  </div>
                  <div id="collapseMissed" class="collapse" aria-labelledby="headingMissed">
                    <div class="card-body">
                      <?php
                      //var_dump($results['skipped']);
                      foreach ($results['skipped'] as $key => $value) {
                        echo '<small>'.$value.'</small><br>';
                      }
                      ?>
                    </div>
                  </div>
                </div>
              <?php
              endif;
            endif;?>
          </div>

          <div class="col-lg-12 mx-auto my-4">

            <button type="button" class="btn btn-sm btn-outline-secondary btn-clear-cookies float-right" title="clear cookies" data-toggle="tooltip"><span class="fas fa-trash-alt"></span></button>

            <table class="datatable table table-striped table-hover table-bordered table-sm table-responsive-sm0 table-killfeed" width="100%">
              <thead class="bg-secondary text-white">
                <tr>
                  <th class="font-weight-normal"><span class="fas fa-calendar-alt"></span> date</th>
                  <th class="font-weight-normal"><span class="fas fa-crosshairs"></span> killer</th>
                  <th class="font-weight-normal"><span class="fas fa-skull-crossbones"></span> victim</th>
                  <th class="font-weight-normal"><span class="fas fa-bolt"></span> cause</th>
                  <th class="font-weight-normal"><span class="fas fa-ruler-horizontal"></span> distance (m)</th>
                </tr>
              </thead>
              <tbody class="small">
                <?php
                if( ! $CONFIG['use_database']) :
                  if( isset($results) && isset($results['matches']) && ! empty($results['matches']) ) {
                    $nbtot = count($results['matches']);
                    generate_table_content($CONFIG, $results['matches']);
                  } else {
                    $nbtot = 0;
                  }
                endif;
                ?>
              </tbody>
            </table>

          </div>
        </div>
      </div>
    </section>

    <?php if( $CONFIG['show_deaths_on_map'] ) : ?>
      <section id="deathmap" class="">

        <div class="container">
          <div class="row">
            <div class="col-lg-12 mx-auto">
              <h2 class="mb-4">
                <span class="fas fa-map-marked-alt"></span> Deaths Map
                <span class="nbtot small">
                  <?php if( ! $CONFIG['use_database'] && isset($nbtot) ): ?>
                    (<?=$nbtot?> deaths)
                  <?php endif; ?>
                </span>
              </h2>
            </div>
          </div>
        </div>

        <div class="container-fluid position-relative mx-auto map_container">

          <div class="map_options">
            <!-- <label>Maps options :</label> -->
            <div class="btn-group btn-group-toggle input-group-sm mr-2" data-toggle="buttons">
              <label class="btn btn-primary btn-sm active">
                <input type="radio" name="btn_map_type" value="dayz" autocomplete="off" checked> DayZ SA
              </label>
              <label class="btn btn-primary btn-sm">
                <input type="radio" name="btn_map_type" value="mod" autocomplete="off"> DayZ mod
              </label>
            </div>

            <div class="btn-group btn-group-toggle input-group-sm mr-2" data-toggle="buttons">
              <div class="input-group-prepend">
                <label class="input-group-text" for="btn_map_zoom">Zoom &times;</label>
              </div>
              <label class="btn btn-secondary btn-sm">
                <input type="radio" name="btn_map_zoom" value="half" autocomplete="off"> ½
              </label>
              <label class="btn btn-secondary btn-sm active">
                <input type="radio" name="btn_map_zoom" value="1" autocomplete="off" checked> 1
              </label>
              <label class="btn btn-secondary btn-sm">
                <input type="radio" name="btn_map_zoom" value="2" autocomplete="off"> 2
              </label>
              <label class="btn btn-secondary btn-sm">
                <input type="radio" name="btn_map_zoom" value="4" autocomplete="off"> 4
              </label>
            </div>

            <div class="btn-group btn-group-toggle input-group-sm mr-2" data-toggle="buttons">
              <div class="input-group-prepend">
                <label class="input-group-text">Show</label>
              </div>
              <label class="btn btn-secondary text-danger btn-sm active">
                <input type="checkbox" name="btn_map_victims" value="1" autocomplete="off" checked><span class="fas fa-skull-crossbones"></span> <strong>victims</strong>
              </label>
              <label class="btn btn-secondary text-success btn-sm active">
                <input type="checkbox" name="btn_map_killers" value="1" autocomplete="off" checked><span class="fas fa-crosshairs"></span> <strong>killers</strong>
              </label>
              <?php if( $CONFIG['use_database']) : ?>
              <label class="btn btn-secondary text-white btn-sm">
                <input type="checkbox" name="btn_map_relations" value="1" autocomplete="off">relations
              </label>
              <?php endif; ?>
              <label class="btn btn-secondary text-white0 btn-sm">
                <input type="checkbox" name="btn_map_grid" value="1" autocomplete="off">grid
              </label>
            </div>

          </div>

          <div class="map show_victims show_killers">
            <div class="grid"></div>
            <?php if( ! $CONFIG['use_database'] && isset($nbtot) && $nbtot > 0 ) show_deaths_on_map($CONFIG, $results['matches']); ?>
            <svg class="relation" height="1536" width="1536"></svg>
          </div>

        </div>

      </section>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="py-1 bg-dark">
      <div class="container">
        <p class="m-0 text-center text-white">
          &copy; 2019
          <a class="text-light" href="https://github.com/ldeb/dayz-death-stats">dayz-death-stats</a> /
          <a class="text-light" href="https://discord.gg/xgvrRff">ABFW</a> /
          <a class="text-light" href="https://steamcommunity.com/sharedfiles/filedetails/?id=1567872567">KillFeed</a>
          <a href="#" class="text-link btn-clear-cookies text-white float-right" title="clear cookies" data-toggle="tooltip"><span class="fas fa-trash-alt"></span></a>
        </p>
      </div>
    </footer>

    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <!-- Datatable -->
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.18/cr-1.5.0/fh-3.1.4/rg-1.1.0/sl-1.3.0/datatables.min.js"></script>
    <!-- Fontawesome -->
    <!-- <script defer src="https://use.fontawesome.com/releases/v5.8.2/js/all.js" integrity="sha384-DJ25uNYET2XCl5ZF++U8eNxPWqcKohUUBUpKGlNLMchM7q4Wjg2CUpjHLaL8yYPH" crossorigin="anonymous"></script> -->
    <script defer src="https://use.fontawesome.com/releases/v5.8.2/js/solid.js" integrity="sha384-+2/MEhV42Ne5nONkjLVCZFGh5IaEQmfXyvGlsibBiATelTFbVGoLB1sqhczi0hlf" crossorigin="anonymous"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.8.2/js/fontawesome.js" integrity="sha384-Ia7KZbX22R7DDSbxNmxHqPQ15ceNzg2U4h5A8dy3K47G2fV1k658BTxXjp7rdhXa" crossorigin="anonymous"></script>


    <script>
      var CONFIG_show_death_details_on_map = <?=( $CONFIG['show_death_details_on_map'] ) ? 'true' : 'false' ?>;
      var CONFIG_link_to_user_steam_profile = <?=( $CONFIG['link_to_user_steam_profile'] ) ? 'true' : 'false' ?>;
    </script>

    <script type="text/javascript" src="inc/script.js"></script>

    <script>
      var common_options = {
        dom: //"<'text-right mb-3'B>" +
        "<'row'<'col'l><'col-12 col-sm col-md-5'f>>" +
        "<'row'<'col'i><'col'p>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'row'<'col'i><'col'p>>",
        fixedHeader: {
          headerOffset: $('#mainNav').outerHeight(),
          header: true,
          footer: true
        },
        // colReorder: true,
        select: 'single',
        stateSave: true,
        deferRender: true,  // ajax
      };
      <?php if( $CONFIG['use_database'] ) : ?>
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Datatable - players
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        var players_options = Object.assign({
          lengthMenu: [ 5, 10, 20, 50, 100 ],
        	pageLength: 10,
          order: [[ 1, 'asc' ]],
          processing: true,
          serverSide: true,
          ajax: "inc/server_processing_players.php",
          columns: [
            { "data": "ratio" , "orderable": false },
            { "data": "rank" },//, "orderData": [ 0, 1 ] },
            { "data": "name" },
            { "data": "kills" },
            { "data": "deaths" },
            // { "data": "steam_id" }
          ],
          rowGroup: {
            dataSrc: ['ratio'],
            startRender: function ( rows, group ) {
              let val = ( group == 9999 ) ? '∞' : group;
              return val;//'<span>' + $('.table-players thead th').eq(0).html() + ':</span> ' + val;
              // return '<span class="font-weight-bold" title="kill death ratio" data-toggle="tooltip"><span class="fas fa-signal"></span> ' + val + '</span>';
            }
          },
          columnDefs: [
            {
              "targets": [2], // player
              "render":
                function ( data, type, row, meta ) {
                  let name = generete_killfeed_search_link(data);
                  let steam_id_pos = 'steam_id';//meta.col + 4;
                  name += ( CONFIG_link_to_user_steam_profile && row[steam_id_pos] != null ) ? ' ' + generete_user_steam_link('+', row[steam_id_pos]) : '';
                  return name;
                },
            },
            {
              "targets": [4], // death
              "render":
                function ( data, type, row, meta ) {
                  let deaths = ( parseInt(data) == 0 ) ? '<span class="text-success">0</span>' : data;
                  return deaths;
                },
            },
            {
              "targets": [0], // ratio (infinity?)
              "render":
                function ( data, type, row, meta ) {
                  let ratio = ( row['deaths'] == 0 ) ? '<strong0>∞</strong0>' : data;  //<span class="fas fa-infinity"></span>
                  return ratio;
                },
            },
          ],
          drawCallback: function( settings ) {
            update_events();
          }
        }, common_options);
        var table_players = $('.datatable.table-players').DataTable(players_options);

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Datatable - causes
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        var causes_options = Object.assign({
          lengthMenu: [ 5, 10, 20, 50, 100 ],
        	pageLength: 10,
          order: [[ 0, 'desc' ], [ 1, 'asc' ]],
          ajax: {
              url: 'inc/api.php?mode=causes',
              dataSrc: ''
          },
          processing: true,
          // rowGroup: {
          //   dataSrc: [0]
          // },
          columnDefs: [
            // {
            //   "targets": 0
            //   "searchable": false,
            //   "orderable": false,
            // },
            {
              "targets": [1], // cause
              "render":
                function ( data, type, row, meta ) {
                  let text = generete_killfeed_search_link(data);
                  return text;
                },
            },
          ],
          drawCallback: function( settings ) {
            update_events();
          }
        }, common_options);
        var table_causes = $('.datatable.table-causes').DataTable(causes_options);
      <?php endif; ?>

      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      // Datatable - killfeed
      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      var killfeed_options = Object.assign({
        lengthMenu: [ 5, 10, 20, 50, 100, 200 ],
      	pageLength: 10,
        order: [[ 0, 'desc' ]],
        <?php if( $CONFIG['use_database'] ) : ?>
        processing: true,
        serverSide: true,
        ajax: "inc/server_processing.php",
        columnDefs: [
          {
            "targets": [1, 2], // killer, victim
            "render":
              function ( data, type, row, meta ) {
                let name = generete_killfeed_search_link(data);
                let steam_id_pos = meta.col + 4;
                name += ( CONFIG_link_to_user_steam_profile && row[steam_id_pos] != null ) ? ' ' + generete_user_steam_link('+', row[steam_id_pos]) : '';
                return name;
              },
          },
        ],
        drawCallback: function( settings ) {
          // console.log('killfeed.drawCallback()');
          var api = this.api();
          show_deaths_on_map(api.ajax.json());
          update_events();
        }
        <?php endif; ?>
      }, common_options);
      var table_killfeed = $('.datatable.table-killfeed').DataTable(killfeed_options);
    </script>

  </body>
</html>
