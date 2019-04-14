(function($) {
  "use strict"; // Start of use strict
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  // Smooth scrolling using jQuery easing
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $('a.js-scroll-trigger[href*="#"]:not([href="#"])').click(function() {
    if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
      if (target.length) {
        $('html, body').animate({
          scrollTop: (target.offset().top - 56)
        }, 1000, "easeInOutExpo");
        return false;
      }
    }
  });
  // Closes responsive menu when a scroll trigger link is clicked
  $('.js-scroll-trigger').click(function() {
    $('.navbar-collapse').collapse('hide');
  });
  // Activate scrollspy to add active class to navbar items on scroll
  $('body').scrollspy({
    target: '#mainNav',
    offset: 56
  });

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  // Bootstrap
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $('[data-toggle="tooltip"]').tooltip({
    boundary: 'window'
  });

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  // Datatable
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  var table = $('.datatable').DataTable({
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

    processing: true,
    // autoWidth: false,
    // stateSave: true,
    deferRender: true,  // ajax

    "lengthMenu": [ 10, 25, 50, 100, 200, 300, 500 ]
  });

  table.column('0').order('desc').draw();

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  // discord
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  // const client = new Discord.Client();
  // client.on('message', msg => {
  //   const guildTag = msg.channel.type === 'text' ? `[${msg.guild.name}]` : '[DM]';
  //   const channelTag = msg.channel.type === 'text' ? `[#${msg.channel.name}]` : '';
  //   console.log(`${guildTag}${channelTag} ${msg.author.tag}: ${msg.content}`);
  // });
  // client.login(app_ID);

  let splited_webhook = webhook_url.split('/');
  let client_ID = splited_webhook[(splited_webhook.length-2)];
  let webhook_TOKEN = splited_webhook[(splited_webhook.length-1)];
  // Create a new webhook
  const hook = new Discord.WebhookClient(client_ID, webhook_TOKEN);

  function Discord_send( message, title='', color='' ) {

    let embed = message;

    if( title != '' && color != '' ) {
      embed = new Discord.RichEmbed()
        .setTitle(title)
        .setColor(color)
        .setDescription(message);
      console.log(embed);
    } else {
    }
    hook.send(embed);
  }


  $('.discord_test').on('click', function(e){

    Discord_send( 'a message', 'The title', '0xFF0000' );


    // $.get("KillFeed/KillFeed.log", function(data){
    //   // let conv = new iconv.Iconv('windows-1251', 'utf8');
    //   // data = conv.convert(data).toString();
    //   console.log("Data: " + data);
    // });


    // $.ajaxSetup({
    //   beforeSend: function (jqXHR, settings) {
    //     if (settings.dataType === 'binary') {
    //       settings.xhr().responseType = 'arraybuffer';
    //       settings.processData = false;
    //     }
    //   }
    // })
    //
    // $.ajax({
    //   url: 'KillFeed/KillFeed.log',
    //   dataType: 'binary',
    //   method: 'GET',
    //   // contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
    //   // contentType: 'application/x-www-form-urlencoded; charset=windows-1251',
    //   // encoding: 'binary'
    // })
    // .done( function ( data, textStatus, jqXHR ) {
    //   // data = new Buffer(data, 'binary');
    //   // let conv = new iconv.Iconv('windows-1251', 'utf8');
    //   // data = conv.convert(data).toString();
    //   console.log(data); //ArrayBuffer
    //   console.log(new Blob([data])) // Blob
    // })
    // .fail(function( jqXHR, textStatus, errorThrown ) {
    //   console.log(textStatus, errorThrown);
    // });


    // var xhr = new XMLHttpRequest();
    // xhr.open('GET', 'KillFeed/KillFeed.log', true);
    // // xhr.responseType = 'blob';
    // xhr.responseType = 'arraybuffer';
    // xhr.onload = function(e) {
    //   if (this.status == 200) {
    //     // get binary data as a response
    //     // var blob = this.response;
    //     // console.log(blob);
    //
    //     // response is unsigned 8 bit integer
    //     var responseArray = new Uint8Array(this.response);
    //     console.log(responseArray);
    //   }
    // };
    // xhr.send();


    e.preventDefault();
  })

})(jQuery); // End of use strict
