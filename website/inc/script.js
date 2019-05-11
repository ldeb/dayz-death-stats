"use strict";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Relative player
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function clear_relations() {
  $('.map .relation line').remove();
}
function show_relative_player(death_id, is_killer) {
  // $('.map .relation').remove();
  // $('.map .relation line').remove();
  let points = $('.elem[data-death_id="'+ death_id +'"]');
  if( points.length > 1) {
    let top = parseFloat(points.eq(0).css('top'));
    let left = parseFloat(points.eq(0).css('left'));
    let bottom = parseFloat(points.eq(1).css('top'));
    let right = parseFloat(points.eq(1).css('left'));
    let color = 'rgb(255,255,255)';
    let html = '<line class="line" x1="'+ left +'" y1="'+ top +'" x2="'+ right +'" y2="'+ bottom +'" style="stroke:'+ color +';stroke-width:1" />';
    $('.map .relation').append(html);
    $('.map .relation').html($('.map .relation').html()); // trick to update SVG on page
  }
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Update events
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
      let is_killer = $(this).hasClass('killer');
      show_relative_player($(this).data('death_id'), is_killer);
    });
    // let is_killer = $(this).hasClass('killer');
    // show_relative_player($(this).data('death_id'), is_killer);
  });
  // $('[data-toggle="tooltip"]').tooltip('dispose'); // not working as expected
  $('.arrow, .tooltip-inner').remove();
  $('[data-toggle="tooltip"]').tooltip();

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  // Related killfeed link
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $('.killfeed_search_for').on('click', function(e){
    let text = $(this).html();
    if(table_killfeed) {
      table_killfeed.search(text).draw();
      setTimeout(function() { $('#deathslogs input[type="search"]').focus(); }, 100);
    }
    // e.preventDefault();  // --> try using smooth scroll instead
  });
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Links
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Related killfeed link
function generete_killfeed_search_link(name) {
  return '<a href="#deathslogs" class="killfeed_search_for js-scroll-trigger" title="show related deaths" data-toggle="tooltip">' + name + '</a>';
}
// Player's Steam link
function generete_user_steam_link(label, user_steamid) {
  let link = ( parseInt(user_steamid) != NaN ) ? '<a href="https://steamcommunity.com/profiles/' + user_steamid + '" target="_blank" title="view Steam profile" data-toggle="tooltip">' + label + '</a>' : '';
  return link;
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// MAP
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
  let legend = '<em>'+elem[0]+'</em>' + ' <br>';
  // let killerInvolve = (elem[1] != null);
  if( ! is_a_killer ) {
    legend+= killerInvolve ? '<span class=&quot;text-danger text-nowrap&quot;>'+elem[2]+'</span>' + ' <br><span class=&quot;fas fa-skull-crossbones&quot;></span> <strong>killed by</strong> <br>' + '<span class=&quot;text-success text-nowrap&quot;>'+elem[1]+'</span>' : '<span class=&quot;text-danger text-nowrap&quot;>'+elem[2]+'</span>' + ' <br><span class=&quot;fas fa-skull-crossbones&quot;></span> <strong>died</strong>';
  } else {
    legend+= '<span class=&quot;text-success text-nowrap&quot;>'+elem[1]+'</span>' + ' <br><span class=&quot;fas fa-crosshairs&quot;></span> <strong>killed</strong> <br>' + '<span class=&quot;text-danger text-nowrap&quot;>'+elem[2]+'</span>';
  }
  legend+= ( elem[3] != null ) ? ' <br>(' + elem[3] + ')' : '';   // reason
  legend+= ( elem[4] != null ) ? ' [' + elem[4] + 'm]' : '';      // dist
  return legend;
}
// {0: "2019-04-25 22:46:39", 1: "Player_killer ", 2: "Player_victim_spe ", 3: "WeaponName", 4: 6, 5: "xxxxxxxxxxxxxxxx", 6: "yyyyyyyyyyyyyy", 7: "1648.1, 3593.0, 133.2", 8: "1675.1, 3597.0, 133.6"}
var coef = 1;
function show_player_on_map(death_id, player_pos, legend, is_a_killer) {
  let div_class = is_a_killer ? ' killer' : ' victim';
  let coords = coord2px(player_pos);
  let html = '<div class="elem' + div_class +'" title="'+ legend +'" data-death_id="'+ death_id +'" data-html="true" data-toggle="tooltip" data-trigger="click" style="left:'+ (coords[0] * coef) + 'px; top:' + (coords[1] * coef) + 'px;"></div>';
  $('.map').append(html);
}
function show_deaths_on_map(json){
  $('#killmap .nbtot').html('(' + json.recordsFiltered + '/' + json.recordsTotal + ')');
  $('.map .elem').remove(); // remove previous points
  clear_relations();

  for (var index in json.data) {
    let elem = json.data[index];
    let killerInvolve = (elem[1] != null);
    let legend = '';
    if(CONFIG_show_death_details_on_map) {
      legend = generate_legend(elem, killerInvolve, false);
    }
    if( elem[8] != null ) {                             // victim
      show_player_on_map(elem[9], elem[8], legend, false);
    }
    if( killerInvolve && elem[8] != null ) {            // killer
      if(CONFIG_show_death_details_on_map) {
        legend = generate_legend(elem, killerInvolve, true);
      }
      if( elem[7] != null ) {
        show_player_on_map(elem[9], elem[7], legend, true);
      }
      show_relative_player(elem[9], true);  // Relations (lines)
    }
  }
  // update_events();
}

(function($) {
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  // Smooth scrolling using jQuery easing (OFF)
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  // $('a.js-scroll-trigger[href*="#"]:not([href="#"])').click(function() {
  //   if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
  //     var target = $(this.hash);
  //     target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
  //     if (target.length) {
  //       $('html, body').animate({
  //         scrollTop: (target.offset().top - 56)
  //       }, 1000, "easeInOutExpo");
  //       return false;
  //     }
  //   }
  // });
  // // Closes responsive menu when a scroll trigger link is clicked
  // $('.js-scroll-trigger').click(function() {
  //   $('.navbar-collapse').collapse('hide');
  // });
  // // Activate scrollspy to add active class to navbar items on scroll
  // $('body').scrollspy({
  //   target: '#mainNav',
  //   offset: 56
  // });

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  // Update events
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

    if( $(this).val() == 'half' ) { // go to top when switching to half-sized map
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
  $('input[name="btn_map_relations"]').on('change', function(){
    if( $(this).prop('checked') ) $('.map').addClass('show_relations');
    else $('.map').removeClass('show_relations');
  });
  $('input[name="btn_map_grid"]').on('change', function(){
    if( $(this).prop('checked') ) $('.map').addClass('show_grid');
    else $('.map').removeClass('show_grid');
  });

  // tooltip rollout
  $('.map').on('click', function(){
    rollout_tooltips();
  });

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  // Clear cookies
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $('.btn-clear-cookies').on('click', function(e){
    if(table_players) table_players.state.clear();
    if(table_causes) table_causes.state.clear();
    if(table_killfeed) table_killfeed.state.clear();
    window.location.reload();
    e.preventDefault();
  })

})(jQuery);
