"use strict";
// environment variables
process.env.NODE_ENV = 'development';

const nice_datetime_format = 'YYYY-MM-DD HH:mm:ss';
const sql_datetime_format = 'YYYY-MM-DDTHH:mm:ss';

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// get config variables
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// const config = require('./conf.js');
const _ = require('lodash');
// module variables
const config = require('./config.json');
const defaultConfig = config.development;
const environment = process.env.NODE_ENV || 'development';
const environmentConfig = config[environment];
const finalConfig = _.merge(defaultConfig, environmentConfig);
global.gConfig = finalConfig;

// const util = require('util');
// const iconvlite = require('iconv-lite');  // https://github.com/ashtuchkin/iconv-lite/wiki/Use-Buffers-when-decoding

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// DISCORD - send message to channel
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
var webhook = null;
var webhook_errors = null;

if( global.gConfig.LOG_TO_DISCORD ) {
  const Discord = require('discord.js');
  let splited_webhook = global.gConfig.webhook_url.split('/');
  if( splited_webhook.length >= 7 ) {
    let client_ID = splited_webhook[(splited_webhook.length-2)];
    if( client_ID != 'xxx' ) {
      let webhook_TOKEN = splited_webhook[(splited_webhook.length-1)];
      webhook = new Discord.WebhookClient(client_ID, webhook_TOKEN);
      webhook.listenerCount = function(){return 0};
    }
  }
  // for errors logging:
  splited_webhook = global.gConfig.webhook_url_errors.split('/');
  if( splited_webhook.length >= 7 ) {
    let client_ID = splited_webhook[(splited_webhook.length-2)];
    if( client_ID != 'xxx' ) {
      let webhook_TOKEN = splited_webhook[(splited_webhook.length-1)];
      webhook_errors = new Discord.WebhookClient(client_ID, webhook_TOKEN);
      webhook_errors.listenerCount = function(){return 0};
    }
  }
}
function Discord_send( hook, message, title='', color='' ) {
  let embed = message;
  if( title != '' && color != '' ) {
    embed = new Discord.RichEmbed()
      .setTitle(title)
      .setColor(color)
      .setDescription(message);
  }
  hook.send(embed);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// DATABASE - Mysql connection
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
var pool = null;
if( global.gConfig.LOG_TO_DATABASE ) {
  const { promisify } = require('util');
  var mysql = require('mysql');
  pool = mysql.createPool({
    connectionLimit:  10,
    host:             global.gConfig.db_host,
    user:             global.gConfig.db_user,
    password:         global.gConfig.db_pass,
    database:         global.gConfig.db_base,
    timezone:         'Z'
  });
  pool.getConnection((err, connection) => {
    if (err) {
      if (err.code === 'PROTOCOL_CONNECTION_LOST') {
        console.error('Database connection was closed.')
      }
      if (err.code === 'ER_CON_COUNT_ERROR') {
        console.error('Database has too many connections.')
      }
      if (err.code === 'ECONNREFUSED') {
        console.error('Database connection was refused.')
      }
    }
    if (connection) connection.release()
    return
  })
  pool.query = promisify(pool.query);
}

async function mysql_get_player(steam_id) {
  let sql = "SELECT id FROM player WHERE steam_id='"+ steam_id +"';"; // test if player already exists in db
  try {
    var result = await pool.query(sql)
  } catch(err) {
    throw new Error(err)  // ERROR DURING QUERY: mysql_get_player()
  }
  if(result && result.length > 0) { // player already exists
    let player_id = result[0].id;
    // console.log('player already exists, id:' + player_id);
    return player_id;
  }
  // return null;
}
async function mysql_insert_player(name, steam_id, deaths=0, kills=0) {
  let sql = "INSERT INTO player (name, steam_id, deaths, kills) VALUES ('"+ name +"', '"+ steam_id  + "', "+ deaths +", "+ kills +")";  // insert new player
  try {
    var result = await pool.query(sql)
  } catch(err) {
    throw new Error(err)  // ERROR DURING QUERY: mysql_insert_player()
  }
  console.log('new player inserted!');
  return result.insertId;
}
async function mysql_update_player(id, name, is_victim = true) {
  let sql;
  if( is_victim ) sql = "UPDATE player SET deaths = deaths + 1, name='"+ name +"' WHERE id = "+ id;   // update player with deaths+1
  else sql = "UPDATE player SET kills = kills + 1, name='"+ name +"' WHERE id ="+ id;                 // update player with kills+1
  try {
    var result = await pool.query(sql)
  } catch(err) {
    throw new Error(err)  // ERROR DURING QUERY: mysql_update_player()
  }
  console.log('player\'s deaths/kills count updated!');
  return;
  // console.log('Unabled to update player\'s deaths/kills count')
  // return false;
}
async function mysql_insert_death(datetime, victim_id, victim_pos, killer_id, killer_pos, reason, distance){
  let sql = "INSERT INTO death (date, victim_id, victim_pos, killer_id, killer_pos, reason, distance) VALUES ('"+ datetime +"',"+ victim_id +",'"+ victim_pos +"',"+ killer_id +",'"+ killer_pos +"','"+ reason +"',"+ distance +")";
  try {
    var result = await pool.query(sql)
  } catch(err) {
    throw new Error(err)  // ERROR DURING QUERY: mysql_insert_death()
  }
  console.log('new death inserted!');
  return result.insertId;
  // return null;
}
// EXEMPLE:
// time: '21:33:10',
// user1_name: 'Player1 name',
// user1_id: 'xxxxxxxxx',
// user1_pos: '1648.1, 3593.0, 133.2',
// user2_name: 'Player2 name',
// user2_id: 'xxxxxxxx',
// user2_pos: '1675.1, 3597.0, 133.6',
// action: 'with',
// reason: 'SK 59/66',
// dist: '27'

async function mysql_process_death(details) {
  // THE VICTIM
  let victim_id = await mysql_get_player(details.user2_id);                               // victim already in db ?
  if( victim_id == null ){                                                                // victim not in db
    // TODO: get user steam infos via Steam API
    victim_id = await mysql_insert_player(details.user2_name, details.user2_id, 1, 0);    // add victim to db with deaths=1
  } else {                                                                                // victim already in db
    await mysql_update_player(victim_id, details.user2_name, true);                       // update name and deaths count
  }
  // THE KILLER
  let killer_id = null;
  if( details.user1_name != null && details.user1_id != null ) {                          // is there's a killer involved ?
    killer_id = await mysql_get_player(details.user1_id);                                 // killer already in db ?
    if( killer_id == null ){                                                              // killer not in db
      // TODO: get user steam infos via Steam API
      killer_id = await mysql_insert_player(details.user1_name, details.user1_id, 0, 1);  // add killer to db with kills=1
    } else {                                                                              // killer already in db
      await mysql_update_player(killer_id, details.user1_name, false);                    // update name and kills count
    }
  }
  // THE DEATH
  // console.log('Player(s) are in db, inserting death log...');
  await mysql_insert_death(details.time.format(sql_datetime_format), victim_id, details.user2_pos, killer_id, details.user1_pos, details.reason, details.dist); // insert death log
};

// Queue system
// to prevent issues with database when multiple lines are detected at the same time
var queue_deaths = [];
var queue_watch = null;
var queue_watch_interval = 1000;

function mysql_queue_death(details) {
  queue_deaths.push(details);
  mysql_start_processing_queue();
}
function mysql_start_processing_queue() {
  if(queue_watch != null) clearInterval(queue_watch);
  queue_watch = setInterval(mysql_process_queue, queue_watch_interval);
}
async function mysql_process_queue() {
  clearInterval(queue_watch); // clear timer
  // console.log('processing queue of: ' + queue_deaths.length);
  for( let i = 0; i < queue_deaths.length; i++ ) {
    await mysql_process_death( queue_deaths[i] );
  }
  queue_deaths = [];
  // console.log('end processing queue: ' + queue_deaths.length);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Parse logfile line
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
const moment = require('moment');
const pattern_time = "(?<time>\\d{2}:\\d{2}:\\d{2})";
const pattern_user_id_pos = "(?<user1_name>.+)\\s\\(steam64id=(?<user1_id>.+)\\spos=<(?<user1_pos>.+)>\\)";
const pattern_user_id_pos2 = "(?<user2_name>.+)\\s\\(steam64id=(?<user2_id>.+)\\spos=<(?<user2_pos>.+)>\\)";
let log_datetime = null;

function convert_time_to_datetime(log_datetime, time) {
  let death_datetime = moment( log_datetime.format('YYYY-MM-DD') + 'T' + time);// + 'Z');
  if( death_datetime.format('HH:mm:ss') < log_datetime.format('HH:mm:ss') ) { // next day detected
    death_datetime.add( 1, 'day' );
  }
  return death_datetime;
}
function parse_killfeed_line(data){
  /////////////////////
  // first line
  /////////////////////
  // Log Created on 2019-04-10 at 21:30:44
  let date_parsed = data.match(/^Log\sCreated\son\s(\d{4}-\d{2}-\d{2})\sat\s(\d{2}:\d{2}:\d{2})/);
  if( date_parsed != null && Object.keys(date_parsed).length > 2 ) {
    log_datetime = moment( date_parsed[1] + ' ' + date_parsed[2]);// + 'Z');
    console.log( log_datetime.format(nice_datetime_format) );
    if( webhook != null ) Discord_send( webhook, '------------' + log_datetime.format(nice_datetime_format) + '------------' );
  } else if( log_datetime != null ){
    /////////////////////
    // Kills
    /////////////////////
    // time | killer killed victim (with/while driving) ...
    let kill_parsed = data.match( new RegExp("^"+ pattern_time + "\\s\\|\\s" + pattern_user_id_pos + "\\skilled\\s" + pattern_user_id_pos2 + "\\s(?<action>with|while\\sdriving)\\s(?<line_end>.+)\\.") );
    if( kill_parsed != null && Object.keys(kill_parsed).length >= 9 && kill_parsed.groups.line_end != null ) {
      let kill_parsed2 = kill_parsed.groups.line_end.match( /^(?<reason>.+)\s\[(?<dist>\d+)m\]/ );  // reason [distm]
      if( kill_parsed2 != null ) {
        kill_parsed.groups.reason = kill_parsed2.groups.reason;                                     // reason (weapon)
        if( kill_parsed2.groups.dist != null ) kill_parsed.groups.dist = kill_parsed2.groups.dist;  // distance
        else kill_parsed.groups.dist = null;
        delete kill_parsed.groups.line_end;
      } else {
        if( kill_parsed.groups.line_end != null ) {
          kill_parsed.groups.reason = kill_parsed.groups.line_end;
          kill_parsed.groups.dist = null;
          delete kill_parsed.groups.line_end;
        } else {
          let message = 'Error while parsing: ' + data;
          console.log(message);
          if( webhook_errors != null ) Discord_send( webhook_errors, message );
        }
      }
    }
    /////////////////////
    // Kills (bled out from)
    /////////////////////
    // time | victim bled out from killer's reason
    else {
      kill_parsed = data.match( new RegExp("^"+ pattern_time + "\\s\\|\\s" + pattern_user_id_pos2 + "\\s(?<action>bled\\sout\\sfrom)\\s" + pattern_user_id_pos + "\\'s\\s(?<reason>.+)\\.") );
      if( kill_parsed != null ) {
        kill_parsed.groups.dist = null;
      }
      /////////////////////
      // Death only
      /////////////////////
      // time | victim (died due to/died to/bled out from cuts by/died/woke with open wounds and) reason
      else {
        kill_parsed = data.match( new RegExp("^"+ pattern_time + "\\s\\|\\s" + pattern_user_id_pos2 + "\\s(?<action>died\\sdue\\sto|died\\sto|bled\\sout\\sfrom\\scuts\\sby|died|woke\\swith\\sopen\\swounds\\sand)\\s(?<reason>.+)\\.") );
        if( kill_parsed != null ) {
          kill_parsed.groups.user1_id = null;
          kill_parsed.groups.user1_name = null;
          kill_parsed.groups.user1_pos = null;
          kill_parsed.groups.dist = null;
        }
        /////////////////////
        // parse failed
        /////////////////////
        else {
          let message = "Unabled to parse line:" + data;
          console.log(message);
          if( webhook_errors != null ) Discord_send( webhook_errors, message );
        }
      }
    }

    /////////////////////
    // Commun operations
    /////////////////////
    if( kill_parsed != null ) {
      kill_parsed.groups.time = convert_time_to_datetime(log_datetime, kill_parsed.groups.time);  // convert time to datetime
      log_datetime = kill_parsed.groups.time.clone();

      let message = kill_parsed.groups.time.format(nice_datetime_format) + ' | ';

      // console.log(kill_parsed.groups);

      if( kill_parsed.groups.user2_name != null ) { // victim
        if( kill_parsed.groups.user1_name != null ) { // killer
          message+= '**' + kill_parsed.groups.user1_name + '** killed **' + kill_parsed.groups.user2_name + '** ' + kill_parsed.groups.action + ' *' + kill_parsed.groups.reason + '*';
          message+= ( kill_parsed.groups.dist != null ) ? ' ['+ kill_parsed.groups.dist +'m]' : '';
        } else {
          message+= '**' + kill_parsed.groups.user2_name + '** '+ kill_parsed.groups.action + ' *' + kill_parsed.groups.reason +'*';
        }
        message+= '.';
        console.log(message);
        if( webhook != null ) Discord_send( webhook, message );
        if( pool != null ) mysql_queue_death(kill_parsed.groups); // TODO: queue !
      }
    }

  } else {
    console.log('Log datetime missing! ignoring line...');
  }
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Tail file and parse logfile
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// const fs = require("fs");
const Tail = require('always-tail');
// if (!fs.existsSync(global.gConfig.filename)) fs.writeFileSync(global.gConfig.filename, "");  // create file if not exists
let tail_options= {interval: global.gConfig.tail_interval};//, blockSize: global.gConfig.tail_blockSize};
let tail = new Tail(global.gConfig.filename, '\n', tail_options);
tail.on('line', function(data) {
  parse_killfeed_line(data);
});
tail.on('error', function(data) {
  console.log("error:", data);
});
tail.watch();
// to unwatch and close all file descriptors, tail.unwatch();