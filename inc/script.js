$(document).ready( function () {
  // Bootstrap
  $('[data-toggle="tooltip"]').tooltip({
    boundary: 'window'
  });

  // Datatable
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

});
