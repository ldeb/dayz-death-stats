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
    // container: 'body',
    // boundary: 'window'
  });

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
  var elem_with_click_tooltips = $('.elem[data-toggle="tooltip"][data-trigger="click"]');
  function rollout_tooltips() {
    elem_with_click_tooltips.tooltip('hide');
  }
  $('.map').on('click', function(){
    rollout_tooltips();
  });
  $('.elem[data-toggle="tooltip"][data-trigger="click"]').each(function( index, elem ){
    $(this).on('click', function(e){
      // e.preventDefault();
      e.stopPropagation();
      elem_with_click_tooltips.not($(this)).tooltip('hide');
    });
  });

})(jQuery); // End of use strict
