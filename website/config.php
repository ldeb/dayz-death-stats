<?php
// WEBSITE INFO
$CONFIG['WEBSITE_TITLE'] = "ElCanu's ABFW DayZ server";
$CONFIG['WEBSITE_IMG'] = 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/89/89783c79efdebec2cbacb438c39a1439e261abc9.jpg';  // 'img/avatar.png';
$CONFIG['use_info_section'] = false;                // Show info section on the website (infos.php)

// ENABLE/DISABLE FUNCTIONALITIES
$CONFIG['link_to_user_steam_profile'] = true;       // (Spoiler alert!)
$CONFIG['show_deaths_on_map'] = true;               // displays deaths/kills on the map (spoiler alert!)
$CONFIG['show_death_details_on_map'] = true;        // show info about each kill on the map (who killed who, spoiler alert!)

// DATABASE connexion
$CONFIG['use_database'] = true;                     // Load data from the database
$CONFIG['db_host'] = 'localhost';                   // database host
$CONFIG['db_base'] = 'dayzstats';                   // database name
$CONFIG['db_user'] = 'root';                        // database Login
$CONFIG['db_pass'] = '';                            // database password
$CONFIG['db_port'] = '3306';                        // database port

// Ignore these settings if using DATABASE connexion
$CONFIG['DEBUG'] = true;                            // show number of unpasred deaths (for debugging purposes)
$CONFIG['logfile'] = 'KillFeed/KillFeed_test.log';  // relative filepath to Cleetus KillFeed mod log file
