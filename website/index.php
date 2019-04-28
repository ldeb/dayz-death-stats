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
              <a class="nav-link js-scroll-trigger" href="#players">Player stats</a>
            </li>
            <?php endif; ?>
            <li class="nav-item">
              <a class="nav-link js-scroll-trigger" href="#killlogs">KillFeed logs</a>
            </li>
            <li class="nav-item">
              <a class="nav-link js-scroll-trigger" href="#killmap">KillFeed Map</a>
            </li>
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
    <section id="players" class="">
      <div class="container">
        <div class="row">
          <div class="col-lg-12 mx-auto">

            <h2 class="my-3">Player stats</h2>
            <div class="col-lg-12 mx-auto my-4">
              <table class="datatable table table-players table-striped table-sm table-bordered">
                <thead>
                  <tr>
                    <th>name</th>
                    <th>kills</th>
                    <th>deaths</th>
                    <th>kill death ratio</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
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
            <?php
            if($CONFIG['use_database']) : ?>

              <table class="datatable table table-killfeed table-striped table-sm table-bordered">
                <thead>
                  <tr>
                    <th>date</th>
                    <th>killer</th>
                    <th>victim</th>
                    <th>cause</th>
                    <th>distance (m)</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>

            <?php
            else :
              if( isset($results) && isset($results['matches']) && ! empty($results['matches']) ) {
                $nbtot = count($results['matches']);
                generate_table($CONFIG, $results['matches']);
              } else {
                $nbtot = 0;
              }
            endif;
            ?>
          </div>
        </div>
      </div>
    </section>

    <section id="killmap" class="">

      <div class="container">
        <div class="row">
          <div class="col-lg-12 mx-auto">
            <h2 class="my-3">KillFeed Map<?php if( isset($nbtot) ): ?> <small>(<?=$nbtot?> deaths)</small><?php endif; ?></h2>
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
          <?php if( isset($nbtot) && $nbtot > 0 ) show_deaths_on_map($CONFIG, $results['matches']); ?>
        </div>

      </div>

    </section>

    <!-- Footer -->
    <footer class="py-1 bg-dark">
      <div class="container">
        <p class="m-0 text-center text-white">Copyright &copy; ABFW 2019</p>
      </div>
    </footer>

    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <!-- Datatable -->
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.18/cr-1.5.0/fh-3.1.4/sl-1.3.0/datatables.min.js"></script>

    <script type="text/javascript" src="inc/script.js"></script>

    <script>
      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      // Datatable - killfeed
      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      var table = $('.datatable.table-killfeed').DataTable({
        lengthMenu: [ 10, 25, 50, 100, 200, 300, 500 ],
        order: [[ 0, 'desc' ]],
        // scrollY: 400,
        // paging: false,
        // lengthChange: true,
        fixedHeader: {
          headerOffset: $('#mainNav').outerHeight(),
          header: true,
          footer: true
        },
        colReorder: true,
        select: 'single',
        stateSave: true,
        deferRender: true,  // ajax
        <?php if( $CONFIG['use_database'] ) : ?>
        processing: true,
        serverSide: true,
        ajax: "inc/server_processing.php"
        <?php endif; ?>
      });
      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      // Datatable - players
      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      var table = $('.datatable.table-players').DataTable({
        lengthMenu: [ 10, 25, 50, 100, 200, 300, 500 ],
        order: [[ 3, 'asc' ]],
        fixedHeader: {
          headerOffset: $('#mainNav').outerHeight(),
          header: true,
          footer: true
        },
        colReorder: true,
        select: 'single',
        stateSave: true,
        deferRender: true,  // ajax
        <?php if( $CONFIG['use_database'] ) : ?>
        processing: true,
        serverSide: true,
        ajax: "inc/server_processing_players.php"
        <?php endif; ?>
      });
    </script>

  </body>
</html>
