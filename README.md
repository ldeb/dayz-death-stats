*Paris, France, April-May 2019*

# dayz-death-stats

**Work in progress...** (made for ElCanu's ABFW DayZ SA server)

![Screenshot](/screenshot.jpg?raw=true)

## Functionalities
- Parses [Cleetus KillFeed mod](https://steamcommunity.com/sharedfiles/filedetails/?id=1567872567) (v3.0) log file
- [optional] Generates a sortable table and a map view of players deaths on a webpage
- [optional] possibility to specify a *logfile* parameter in URL to use a different log file path, exemple : `index.php?logfile=KillFeed/KillFeed_2019-03.log`
- [optional] Sends a message to a Discord server, using webhooks, when log file updates
- [optional] Save data to a database (MySQL), enables different statistics (player kill/death ratio, death causes, ...)
- [optional] Load data from the database (MySQL)
- Generate a test log file from KillFeed.json using [test_conf.php](/test_conf.php) for development purposes

**TO DO:**
- fix problem with special characters from javascript fetching database's data
- show log starting date
- Possibility to generate a "deathmap" from all deaths in database
- Generate more statistics
- Automatically add necessary tables to the database if not present
- show more info about players using Steam API
- do not save in cookie: search filters and other statistics then Players

**FLAWS/TO FIX: *server.js***
- can crashes when *KillFeed.log* is renamed or deleted
- datetime logs could be wrong if no kills not restart of the game server happens in more then 24h

## Requirements
- access to *KillFeed.log*'s DayZ SA server file
  - for Unix server: create a [symbolic link](https://www.google.com/search?q=symbolic+link), next to *server.js* file for exemple
  - for Windows server: create a scheduled task (exemple .bat file to execute: `xcopy /Y C:\DAYZSERVER\logs\KillFeed.log C:\dayz-death-stats\`)
- **[optional] Installation for browsing results in a web server**
  - web server running PHP (Apache, nginx, ...)
- **[optional for posting to Discord]**
  - to be able to create a webhook on the desired channel of a Discord server
  - Node.js installed on your server
- **[optional for logging to a database]**
  - MySQL server
  - Node.js installed on your server

## Installation
- `git clone https://github.com/ldeb/dayz-death-stats.git` to your desired folder

### [optional] Installation for browsing results in a web server
- host `website/` folder with your web server
- Edit *config.php* to set various options
- Edit *infos.php* file to personalize website info section
- Remove/edit *robots.txt* if you want to enable search engines to crawl and index your website
- Add eventual tracking code in *head.php*

### [optional] Installation for posting to a Discord channel / logging to a database
- Rename *config.default.json* to *config.json* and edit necessary variables and activate desired functionalities (`"LOG_TO_DISCORD": true`, `"LOG_TO_DATABASE": true`)
- **[optional for posting to Discord]**
  - Create a Discord webhook on your discord server channel, then update your webhook URL in *config.json* (`webhook_url_errors` variable is optional)
- **[optional for logging to a database]**
  - Create a new database on your MySQL server (using an utf8 charset, exemple: `utf8_general_ci`), then update your connection info in *config.json* and *config.php* (`db_host`, `db_base`, `db_user`, `db_pass`)
  - Execute/import [dayz-death-stats.sql](https://github.com/ldeb/dayzstats/blob/master/dayz-death-stats.sql) script in your database to create the necessary tables
- [install node.js](https://nodejs.org/en/download/) on your server
- go to the current cloned directory and install script dependencies with `npm install`
- run script with command `node server.js`
- if needed, stop script with `CTRL+C` command
- **NOTE:** At the moment, logging will only start when a fresh *KillFeed.log* file is generated or a new log file date time is detected

## Updating
- backup your *config.php* and *infos.php* files
- go to the current cloned directory and `git pull`
- resolve eventual conflicts (use `git checkout -- filename` to restore original file)
- compare changes in config files (*config.json* and *config.php*) and adapt if necessary
- stop (`CTRL+C`) and start (`node server.js`) node if *server.js* file changed

## Note
Feel free to contact me for any suggestions/feedback/help

## Credits
- [DayZ Stand Alone](https://store.steampowered.com/agecheck/app/221100/)'s [Cleetus KillFeed mod](https://steamcommunity.com/sharedfiles/filedetails/?id=1567872567)
- [Bootstrap](https://getbootstrap.com/), [Font Awesome](https://fontawesome.com/), [Datatable](https://datatables.net/), [jQuery](https://jquery.com/), [Popper.js](https://popper.js.org/), [Medoo](https://medoo.in/)
- [Node.js](https://nodejs.org/) and [always-tail](https://github.com/jandre/always-tail), [discord.js](https://github.com/discordjs/discord.js), [mysql](https://github.com/mysqljs/mysql), [moment](https://github.com/moment/moment), [lodash](https://github.com/lodash/lodash) modules
