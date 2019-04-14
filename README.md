# dayzstats

**Work in progress...**

- Parses [Cleetus KillFeed mod](https://steamcommunity.com/sharedfiles/filedetails/?id=1567872567) log file
- Generates a sortable table and a map view of players deaths onto a webpage

**TO DO**
- Sends a message to your Discord server
- Generate specific player statistics

## Requirements
- web server running PHP (Apache, nginx, ...)
- access to KillFeed.log file
- Discord server

## Installation
Rename *config.default.js* to *config.js*

### Configuration
- Create a Discord webhook on your discord server, then update your webhook URL in *config.js*
- Edit *config.php* / *config.js*

## Credits
- Cleetus KillFeed mod
- Bootstrap
- Datatable
- Discord
