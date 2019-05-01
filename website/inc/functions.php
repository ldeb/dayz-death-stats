<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// PARSE LOGFILE
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function set_time($death_time, $current_datetime) {  // the time (00:01:00) can change in the middle a log file
  $death_datetime = new DateTime( $current_datetime->format('Y-m-d').' '.$death_time);
  if( $death_datetime->format('H:i:s') < $current_datetime->format('H:i:s') ) { // next day
    $death_datetime->add(new DateInterval('P1D'));
  }
  return $death_datetime;
}

function parse_log($CONFIG) {
  $filename = $CONFIG['logfile'];
  $pattern_time = '(?\'time\'\d{2}:\d{2}:\d{2})';
  $pattern_killer_id_pos = '(?\'killer_name\'.+)\s\(steam64id=(?\'killer_id\'.+)\spos=<(?\'killer_pos\'.+)>\)';
  $pattern_victim_id_pos = '(?\'victim_name\'.+)\s\(steam64id=(?\'victim_id\'.+)\spos=<(?\'victim_pos\'.+)>\)';

  // detect file encoding
  $info = finfo_open(FILEINFO_MIME_ENCODING);
  $file_encoding = finfo_buffer($info, @file_get_contents($filename));
  finfo_close($info);

  $handle = @fopen($filename, 'r');
  $results = array();
  $i = 0;
  if ($handle) {
    while (($line = fgets($handle)) !== false) {
      $i++;
      $matches = null;

      if($line != PHP_EOL ) {
          // var_dump($line);
        /////////////////////
        // logfile datetime
        /////////////////////
        if( preg_match('/^Log\sCreated\son\s(\d{4}-\d{2}-\d{2})\sat\s(\d{2}:\d{2}:\d{2})/', $line, $matches) == 1 ) {
          $log_date = new DateTime($matches[1].'T'.$matches[2].'Z');
          echo '<strong>Log file starting date: '.$log_date->format('Y-m-d H:i:s').'</strong><br />';
        } else if( isset($log_date) ){
          /////////////////////
          // Kills
          /////////////////////
          // time | killer killed victim (with/while driving/in) ...
          if( preg_match('/^'.$pattern_time.'\s\|\s'.$pattern_killer_id_pos.'\skilled\s'.$pattern_victim_id_pos.'\s(?\'action\'with|while\sdriving|in)\s(?\'line_end\'.+)\./', $line, $matches) == 1 ) {
            if( preg_match('/^(?\'reason\'.+)\s\[(?\'dist\'\d+)m\]/', $matches['line_end'], $matches2) == 1 ) {           // reason [distm]
              $matches['reason'] = $matches2['reason'];                               // reason (weapon)
              if( isset($matches2['dist']) ) $matches['dist'] = $matches2['dist'];    // distance
              unset($matches['line_end']);
            } else {
              if( isset($matches['line_end']) ) {
                $matches['reason'] = $matches['line_end'];                            // reason (weapon)
                unset($matches['line_end']);
              } else {
                echo 'error1';
              }
            }
          }
          /////////////////////
          // Other kills
          /////////////////////
          // time | victim (bled out from/bled out due to damage from/killed/was exploded by/was cut deep by (barbed wire on a)) killer('s )(reason).
          // time | victim killed by killer.
          else if( preg_match('/^'.$pattern_time.'\s\|\s'.$pattern_victim_id_pos.'\s(?>bled\sout\sfrom|bled\sout\sdue\sto\sdamage\sfrom|killed\sby|was\sexploded\sby|was\scut\sdeep\sby(?>\sbarbed\swire\son\sa)?)\s'.$pattern_killer_id_pos.'(?>\'s|\swith)?\s?(?\'reason\'.+)?\./', $line, $matches) == 1 ) {
          }
          // time | victim stepped on a WeaponName laid by (killer) and died.
          else if( preg_match('/^'.$pattern_time.'\s\|\s'.$pattern_victim_id_pos.'\s(?\'reason\'.+)\slaid\sby\s'.$pattern_killer_id_pos.'\sand\sdied\./', $line, $matches) == 1 ) {
          }
          // time | victim stepped on a WeaponName laid by Unknown Survivor and died.
          else if( preg_match('/^'.$pattern_time.'\s\|\s'.$pattern_victim_id_pos.'\s(?\'reason\'.+)\slaid\sby\s(?\'killer_name\'Unknown\sSurvivor)\sand\sdied\./', $line, $matches) == 1 ) {
            $matches['killer_id'] = null;
            $matches['killer_pos'] = null;
          }
          /////////////////////
          // Death only
          /////////////////////
          // time | victim (died due to/died to/bled out from( cuts by)/died/died with/woke with open wounds and/killed themselves in) reason (somehow)
          else if( preg_match('/^'.$pattern_time.'\s\|\s'.$pattern_victim_id_pos.'\s(?>died\sdue\sto|died\sto|bled\sout\sfrom(?>\scuts\sby)?|died\swith|died|woke\swith\sopen\swounds\sand|killed\sthemselves\sin)\s(?\'reason\'.+)(?>\ssomehow)?\./', $line, $matches) == 1 ) {
          }
          // time | victim bled out from [SHOULD BE FIXED BY FURTHER VERSION OF KILLFEED]
          else if( preg_match('/^'.$pattern_time.'\s\|\s'.$pattern_victim_id_pos.'\s(?>bled\sout\sfrom\s)/', $line, $matches) == 1 ) {
            $matches['reason'] = 'bled out';
            // var_dump($line);
          }
          /////////////////////
          // parse failed, probably hits only
          /////////////////////
          else {
            $results['skipped'][] = $line;
          }

          /////////////////////
          // Commun operations
          /////////////////////
          if( isset($matches) && $matches != null ) {

            // clean array, only keep string keys
            foreach ($matches as $key => $value) if( is_int($key) ) unset($matches[$key]);

            if( isset($matches['time']) ) {
              $matches['time'] = set_time($matches['time'], $log_date);  // update datetime
              $log_date = clone $matches['time'];
              $matches['time'] = $matches['time']->format('Y-m-d H:i:s');
            }

            if( $file_encoding != 'utf-8' && isset($matches['killer_name']) && mb_detect_encoding($matches['killer_name'], 'Windows-1251') == 'Windows-1251' ) {
              $matches['killer_name'] = iconv("Windows-1251", "UTF-8//TRANSLIT", $matches['killer_name']); // convert username charset
            }
            if( $file_encoding != 'utf-8' && isset($matches['victim_name']) && mb_detect_encoding($matches['victim_name'], 'Windows-1251') == 'Windows-1251' ) {
              $matches['victim_name'] = iconv("Windows-1251", "UTF-8//TRANSLIT", $matches['victim_name']);
            }

            // var_dump($matches);

            // 1 => string '21:33:10' (length=8)
            // 2 => string 'KILLERNAME' (length=7)
            // 3 => string '76 561 198 050 277 984' (length=17)
            // 4 => string '1648.1, 3593.0, 133.2' (length=21)
            // 5 => string 'VICTIMNAME' (length=5)
            // 6 => string '76561198422180913' (length=17)
            // 7 => string '1675.1, 3597.0, 133.6' (length=21)
            // 8 => string 'SK 59/66' (length=8)
            // 9 => string '27' (length=2)

            // add to results
            $results['matches'][] = $matches;
          } else {
            // parse failed
          }
        } else {
          // no log datetime defined
        }
      } else {
        // empty line
      }
    } // end while
    fclose($handle);

    // var_dump($results);
    //
    // // DEBUG: Parse errors
    // if( $CONFIG['DEBUG'] && count($skipped) > 0 ){
    //   echo '<strong>'. count($skipped) .' parsing deaths missed!</strong>';
    //   // if( count($skipped) > 0 )
    //   var_dump($skipped);
    // }

  } else {
    echo '<span class="text-danger">Error opening file <strong>'.$filename.'</strong></span>';
  }
  return $results;
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// DATATABLE
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function generete_user_link($label, $user_steamid) {
  $link = ( is_numeric($user_steamid) ) ? '<a href="https://steamcommunity.com/profiles/'.$user_steamid.'" target="_blank" title="View Steam profile">'.$label.'</a>' : '';
  return $link;
}
function generate_table_content($CONFIG, $results) {
  $nc_char = '-';
  foreach ($results as $i => $action): ?>
    <tr>
      <td><?= isset($action['time']) ? $action['time'] : $nc_char; ?></td>
      <td>
        <?php if( isset($action['killer_name']) ) {
          echo $action['killer_name'];
          echo ( $CONFIG['link_to_user_steam_profile'] && isset($action['killer_id']) ) ? ' '.generete_user_link('+', $action['killer_id']) : '';
        } else echo $nc_char;
        ?>
      </td>
      <td>
        <?php if( isset($action['victim_name']) ) {
          echo $action['victim_name'];
          echo ( $CONFIG['link_to_user_steam_profile'] && isset($action['victim_id']) ) ? ' '.generete_user_link('+', $action['victim_id']) : '';
        } else echo $nc_char;
        ?>
      </td>
      <td><?= isset($action['reason']) ? $action['reason'] : $nc_char; ?></td>
      <td><?= isset($action['dist']) ? $action['dist'] : $nc_char; ?></td>
    </tr>
  <?php endforeach;
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// SHOW DEATHS ON MAP
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// $worldspace = '2167.5, 4369.4, 209.4'
function coord2px($worldspace){
  $y_diff = 15360;
  $coords = explode(', ', $worldspace);
  if( count($coords)==3 ) {
    $result = array();
    $result[0] = floatval( $coords[0] ) / 10;
    $result[1] = ($y_diff - floatval( $coords[1] )) / 10;
    $result[2] = floatval( $coords[2] );
    return $result;
  } else {
    return array(0,0,0);
  }
}

function show_player_on_map($player_name, $player_id, $player_pos, $legend, $is_a_killer) {
  $coef = 1;
  $coords = coord2px($player_pos);
  $class = $is_a_killer ? ' killer' : ' victim';
  echo '<div class="elem'.$class.'" title="'.$legend.'" data-toggle="tooltip" data-trigger="click" style="left:'.($coords[0] * $coef).'px; top:'.($coords[1] * $coef).'px;">';
    // echo '<div class="point"></div>';
  echo '</div>';
}

function show_deaths_on_map($CONFIG, $results) {
  foreach($results as $action){

    $killerInvolve = isset($action['killer_name']);
    $legend_date = $action['time'];
    $legend = $legend_date.' | ';

    if( $CONFIG['show_death_details_on_map'] ) {
      $legend.= $killerInvolve ? $action['victim_name']. ' killed by '. $action['killer_name'] : $action['victim_name'].' died';
      $legend.= isset($action['reason']) ? ' ('.$action['reason'].')' : '';
      if( isset($action['dist']) ) $legend.= ' ['.$action['dist'].'m]';
      // else if($killerInvolve) $legend.= ' [bled out]';  // bled out
    }
    show_player_on_map($action['victim_name'], $action['victim_id'], $action['victim_pos'], $legend, false);

    if( $killerInvolve && isset($action['killer_pos']) ) {  // there is a killer involve, show him
      $legend = $legend_date.' | ';
      if( $CONFIG['show_death_details_on_map'] ) {
        $legend.= $action['killer_name']. ' killed '. $action['victim_name'];
        $legend.= isset($action['reason']) ? ' ('.$action['reason'].')' : '';
        if( isset($action['dist']) ) $legend.= ' ['.$action['dist'].'m]';
        // else $legend.= ' [bled out]';  // bled out
      }
      show_player_on_map($action['killer_name'], $action['killer_id'], $action['killer_pos'], $legend, true);
    }
  }
}
