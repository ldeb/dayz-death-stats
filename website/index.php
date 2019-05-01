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
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.18/cr-1.5.0/fh-3.1.4/sl-1.3.0/datatables.min.css"/>

    <link rel="stylesheet" type="text/css" href="inc/style.css"/>

  </head>

  <body id="page-top">

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
      <div class="container">
        <a class="navbar-brand js-scroll-trigger" href="index.php">
          <img src="https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/89/89783c79efdebec2cbacb438c39a1439e261abc9.jpg" alt="" title="ElCanu"> ElCanu's
          ABFW DayZ server
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item">
              <a class="nav-link js-scroll-trigger" href="#infos">Infos</a>
            </li>
            <?php if( $CONFIG['use_database'] ) : ?>
            <li class="nav-item">
              <a class="nav-link js-scroll-trigger" href="#stats">Statistics</a>
            </li>
            <?php endif; ?>
            <li class="nav-item">
              <a class="nav-link js-scroll-trigger" href="#killlogs">KillFeed logs</a>
            </li>
            <?php if( $CONFIG['show_deaths_on_map'] ) : ?>
            <li class="nav-item">
              <a class="nav-link js-scroll-trigger" href="#killmap">KillFeed Map</a>
            </li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </nav>

    <header class="bg-light text-white0 p-3" style="margin-top: 56px;">
      <div class="container text-center">
        <h1>ElCanu's Ask Bambies For Weed - DayZ server</h1>
        <p class="lead">
          <a href="https://discord.gg/xgvrRff" target="_blank">Discord</a> |
          <a href="https://www.twitch.tv/ElCanu" target="_blank">Twitch</a> |
          <a href="https://steamcommunity.com/profiles/76561198086409926" target="_blank">Steam page</a>
        </p>
      </div>
    </header>

    <section id="infos" class="">
      <div class="container">
        <div class="row">
          <div class="col-lg-12 mx-auto">

            <h2 class="my-3">Info (Lorem Ipsum)</h2>
            <p class="lead">This is a great place to talk about your webpage. This template is purposefully unstyled so you can use it as a boilerplate or starting point for you own landing page designs! This template features:</p>
            <ul>
              <li>Clickable nav links that smooth scroll to page sections</li>
              <li>Responsive behavior when clicking nav links perfect for a one page website</li>
              <li>Bootstrap's scrollspy feature which highlights which section of the page you're on in the navbar</li>
              <li>Minimal custom CSS so you are free to explore your own unique design options</li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <?php if( $CONFIG['use_database'] ) : ?>
    <section id="stats" class="">
      <div class="container">
        <div class="row">
          <div class="col-lg-12 mx-auto">

            <h2 class="my-3">Statistics</h2>
            <div class="col-lg-12 mx-auto my-4">
            </div>

            <ul class="nav nav-pills" id="pills-tab" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="players-tab" data-toggle="tab" href="#players" role="tab" aria-controls="players" aria-selected="true">Player stats</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="causes-tab" data-toggle="tab" href="#causes" role="tab" aria-controls="causes" aria-selected="false">Death causes</a>
              </li>
            </ul>

            <div class="tab-content mt-4" id="statsContent">
              <div class="tab-pane fade show active" id="players" role="tabpanel" aria-labelledby="players-tab">
                <table class="datatable table table-striped table-hover table-bordered table-sm table-responsive-sm0 small table-players" width="100%">
                  <thead>
                    <tr>
                      <th>rank</th>
                      <th>name</th>
                      <th>kills</th>
                      <th>deaths</th>
                      <th>kill death ratio</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>

              <div class="tab-pane fade" id="causes" role="tabpanel" aria-labelledby="causes-tab">
                <table class="datatable table table-striped table-hover table-bordered table-sm table-responsive-sm0 small table-causes" width="100%">
                  <thead>
                    <tr>
                      <th>count</th>
                      <th>cause</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>

          </div>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <section id="killlogs" class="">
      <div class="container">
        <div class="row">
          <div class="col-lg-12 mx-auto">

            <h2 class="my-3">KillFeed logs</h2>
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

            <table class="datatable table table-striped table-hover table-bordered table-sm table-responsive-sm0 small table-killfeed" width="100%">
              <thead>
                <tr>
                  <th>date</th>
                  <th>killer</th>
                  <th>victim</th>
                  <th>cause</th>
                  <th>distance (m)</th>
                </tr>
              </thead>
              <tbody>
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
      <section id="killmap" class="">

        <div class="container">
          <div class="row">
            <div class="col-lg-12 mx-auto">
              <h2 class="my-3">
                KillFeed Map
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
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
              <label class="btn btn-primary btn-sm active">
                <input type="radio" name="btn_map_type" value="dayz" autocomplete="off" checked> DayZ SA
              </label>
              <label class="btn btn-primary btn-sm">
                <input type="radio" name="btn_map_type" value="mod" autocomplete="off"> DayZ mod
              </label>
            </div>

            <div class="btn-group btn-group-toggle input-group-sm" data-toggle="buttons">
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

            <div class="btn-group btn-group-toggle input-group-sm" data-toggle="buttons">
              <div class="input-group-prepend">
                <label class="input-group-text">Show</label>
              </div>
              <label class="btn btn-secondary text-danger btn-sm active">
                <input type="checkbox" name="btn_map_victims" value="1" autocomplete="off" checked> <strong>victims</strong>
              </label>
              <label class="btn btn-secondary text-success btn-sm active">
                <input type="checkbox" name="btn_map_killers" value="1" autocomplete="off" checked> <strong>killers</strong>
              </label>
              <label class="btn btn-secondary text-white0 btn-sm">
                <input type="checkbox" name="btn_map_grid" value="1" autocomplete="off"> grid
              </label>
            </div>

          </div>

          <div class="map show_victims show_killers">
            <div class="grid"></div>
            <?php if( ! $CONFIG['use_database'] && isset($nbtot) && $nbtot > 0 ) show_deaths_on_map($CONFIG, $results['matches']); ?>
          </div>

        </div>

      </section>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="py-1 bg-dark">
      <div class="container">
        <p class="m-0 text-center text-white">
          &copy; 2019
          <a href="https://github.com/ldeb/dayzstats">dayzstats</a> /
          <a href="https://discord.gg/xgvrRff">ABFW</a> /
          <a href="https://steamcommunity.com/sharedfiles/filedetails/?id=1567872567">KillFeed</a></p>
      </div>
    </footer>

    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <!-- Datatable -->
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.18/cr-1.5.0/fh-3.1.4/sl-1.3.0/datatables.min.js"></script>

    <script>
      var CONFIG_show_death_details_on_map = <?=( $CONFIG['show_death_details_on_map'] ) ? 'true' : 'false' ?>;
      var CONFIG_link_to_user_steam_profile = <?=( $CONFIG['link_to_user_steam_profile'] ) ? 'true' : 'false' ?>;
    </script>

    <script type="text/javascript" src="inc/script.js"></script>

    <script>
      var common_options = {
        lengthMenu: [ 10, 25, 50, 100, 200, 300, 500 ],
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
      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      // Datatable - players
      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      var players_options = Object.assign({
        order: [[ 0, 'asc' ]],
        <?php if( $CONFIG['use_database'] ) : ?>
        processing: true,
        serverSide: true,
        ajax: "inc/server_processing_players.php"
        <?php endif; ?>
      }, common_options);
      var table = $('.datatable.table-players').DataTable(players_options);
      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      // Datatable - causes
      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      var causes_options = Object.assign({
        order: [[ 0, 'desc' ]],
        <?php if( $CONFIG['use_database'] ) : ?>
        ajax: {
            url: 'inc/api.php?mode=causes',
            dataSrc: ''
        },
        processing: true,
        // serverSide: true,
        // ajax: "inc/api.php?mode=causes"
        <?php endif; ?>
      }, common_options);
      var table = $('.datatable.table-causes').DataTable(causes_options);
      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      // Datatable - killfeed
      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      var killfeed_options = Object.assign({
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
                console.log(data, meta);
                let name = data;
                let steam_id_pos = meta.col + 4;
                name += ( CONFIG_link_to_user_steam_profile && row[steam_id_pos] != null ) ? ' ' + generete_user_link('+', row[steam_id_pos]) : '';
                return name;
              },
          },
        ],
        drawCallback: function( settings ) {
          var api = this.api();
          // console.log( api.ajax.json().data );
          show_deaths_on_map(api.ajax.json());
        }
        <?php endif; ?>
      }, common_options);
      var table = $('.datatable.table-killfeed').DataTable(killfeed_options);
    </script>

  </body>
</html>
