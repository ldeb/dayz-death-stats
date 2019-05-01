"use strict";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Bootstrap
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// tooltip and tooltip rollout
var elem_with_click_tooltips = $('.elem[data-toggle="tooltip"][data-trigger="click"]');
function rollout_tooltips() {
  elem_with_click_tooltips.tooltip('hide');
}
function update_events(){
  elem_with_click_tooltips = $('.elem[data-toggle="tooltip"][data-trigger="click"]');
  elem_with_click_tooltips.each(function( index, elem ){
    $(this).on('click', function(e){
      e.stopPropagation();
      elem_with_click_tooltips.not($(this)).tooltip('hide');
    });
  });
  // $('[data-toggle="tooltip"]').tooltip('dispose');
  $('.arrow, .tooltip-inner').remove();
  $('[data-toggle="tooltip"]').tooltip();

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  // PLAYER link
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $('.player_link').on('click', function(e){
    // e.preventDefault();
    let name = $(this).html();
    if(table_killfeed) {
      table_killfeed.search(name).draw();
      setTimeout(function() { $('#killlogs input[type="search"]').focus(); }, 100);
    }
  });

}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Player links
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// killfeed link
function generete_user_killfeed_link(name) {
  return '<a href="#killlogs" class="player_link" title="show player\'s killfeed" data-toggle="tooltip">' + name + '</a>';
}

// Steam link
function generete_user_steam_link(label, user_steamid) {
  let link = ( parseInt(user_steamid) != NaN ) ? '<a href="https://steamcommunity.com/profiles/' + user_steamid + '" target="_blank" title="View Steam profile" data-toggle="tooltip">' + label + '</a>' : '';
  return link;
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// MAP points
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
var y_diff = 15360;
function coord2px(worldspace){
  let coords = worldspace.split(', ');
  if( coords.length == 3 ) {
    let result = [];
    result[0] = parseFloat( coords[0] ) / 10;
    result[1] = (y_diff - parseFloat( coords[1] )) / 10;
    result[2] = parseFloat( coords[2] );
    return result;
  } else {
    return [0,0,0];
  }
}
function generate_legend(elem, killerInvolve, is_a_killer) {
  let legend = elem[0] + ' | ';
  if( ! is_a_killer ) {
    legend+= killerInvolve ? elem[2] + ' killed by ' + elem[1] : elem[1] + ' died';
  } else {
    legend+= elem[1] + ' killed ' + elem[2];
  }
  legend+= ( elem[3] != null ) ? ' (' + elem[3] + ')' : '';   // reason
  legend+= ( elem[4] != null ) ? ' [' + elem[4] + 'm]' : '';  // dist
  return legend;
}
// {0: "2019-04-25 22:46:39", 1: "Player_killer ", 2: "Player_victim_spe ", 3: "WeaponName", 4: 6, 5: "xxxxxxxxxxxxxxxx", 6: "yyyyyyyyyyyyyy", 7: "1648.1, 3593.0, 133.2", 8: "1675.1, 3597.0, 133.6"}
var coef = 1;
function show_player_on_map(player_pos, legend, is_a_killer) {
  let div_class = is_a_killer ? ' killer' : ' victim';
  let coords = coord2px(player_pos);
  let html = '<div class="elem' + div_class +'" title="'+ legend +'" data-toggle="tooltip" data-trigger="click" style="left:'+ (coords[0] * coef) + 'px; top:' + (coords[1] * coef) + 'px;">';
  // console.log(html);
  $('.map').append(html);
}
function show_deaths_on_map(json){
  $('#killmap .nbtot').html('(' + json.recordsFiltered + '/' + json.recordsTotal + ')');
  $('.map .elem').remove(); // remove previous points
  // console.log(json);
  for (var index in json.data) {
    let elem = json.data[index];
    // console.log(elem);
    let killerInvolve = (elem[1] != null);
    let legend = '';
    if(CONFIG_show_death_details_on_map) {
      legend = generate_legend(elem, killerInvolve, false);
    }
    if( elem[7] != null ) {                             // victim
      show_player_on_map(elem[7], legend, false);
    }
    if( killerInvolve && elem[8] != null ) {            // killer
      if(CONFIG_show_death_details_on_map) {
        legend = generate_legend(elem, killerInvolve, true);
      }
      show_player_on_map(elem[8], legend, true);
    }
  }
  // update_events();
}

(function($) {
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
  update_events();

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  // MAP options
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  // Map style
  $('input[name="btn_map_type"]').on('change', function(){
    $('.map').removeClass(function (index, className) {
      return (className.match (/(^|\s)type_\S+/g) || []).join(' ');
    });
    $('.map').addClass('type_'+ $(this).val());
  });
  // Zoom
  $('input[name="btn_map_zoom"]').on('change', function(){
    $('.map').removeClass(function (index, className) {
      return (className.match (/(^|\s)zoom_\S+/g) || []).join(' ');
    });
    $('.map').addClass('zoom_'+ $(this).val());

    if( $(this).val() == 'half' ) { // go to top when switching to half map
      $('.map_container').scrollTop(0);
    }
  });
  // Hide/show victims/killers/grid
  $('input[name="btn_map_victims"]').on('change', function(){
    if( $(this).prop('checked') ) $('.map').addClass('show_victims');
    else $('.map').removeClass('show_victims');
  });
  $('input[name="btn_map_killers"]').on('change', function(){
    if( $(this).prop('checked') ) $('.map').addClass('show_killers');
    else $('.map').removeClass('show_killers');
  });
  $('input[name="btn_map_grid"]').on('change', function(){
    if( $(this).prop('checked') ) $('.map').addClass('show_grid');
    else $('.map').removeClass('show_grid');
  });

  // tooltip rollout
  $('.map').on('click', function(){
    rollout_tooltips();
  });
  // var elem_with_click_tooltips = $('.elem[data-toggle="tooltip"][data-trigger="click"]');
  // function rollout_tooltips() {
  //   elem_with_click_tooltips.tooltip('hide');
  // }
  // $('.map').on('click', function(){
  //   rollout_tooltips();
  // });
  // $('.elem[data-toggle="tooltip"][data-trigger="click"]').each(function( index, elem ){
  //   $(this).on('click', function(e){
  //     // e.preventDefault();
  //     e.stopPropagation();
  //     elem_with_click_tooltips.not($(this)).tooltip('hide');
  //   });
  // });

})(jQuery); // End of use strict
