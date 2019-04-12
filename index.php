<?php
include('config.php');
include('inc/functions.php');
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>ElCanu's ABFW DayZ server</title>

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
            <li class="nav-item">
              <a class="nav-link js-scroll-trigger" href="#killstats">KillFeed Stats</a>
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

    <section id="infos" class="pt-5">
      <div class="container">
        <div class="row">
          <div class="col-lg-12 mx-auto">
            <h2 class="my-3">Info</h2>
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

    <section id="killstats" class="pt-5">
      <div class="container">
        <div class="row">
          <div class="col-lg-12 mx-auto">
            <h2 class="my-3">KillFeed Stats</h2>
            <?php
            $results = parse_log($CONFIG);
            ?>
          </div>
          <div class="col-lg-12 mx-auto my-4">
            <?php
            generate_table($results);
            ?>
          </div>
        </div>
      </div>
    </section>

    <section id="killmap" class="pt-5">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12 mx-auto">
            <h2 class="container my-3">KillFeed Map <small>(<?=count($results)?> deaths)</small></h2>
              <div class="map">
            	  <div class="grille"></div>
                <?php show_deaths_on_map($CONFIG, $results); ?>
              </div>

          </div>
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
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <!-- Datatable -->
    <!-- <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script> -->
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.18/cr-1.5.0/fh-3.1.4/sl-1.3.0/datatables.min.js"></script>

    <script type="text/javascript" src="inc/script.js"></script>

  </body>
</html>