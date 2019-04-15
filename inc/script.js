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
  table.column('0').order('desc').draw();=

})(jQuery); // End of use strict
