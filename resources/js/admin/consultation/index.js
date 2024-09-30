/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!**************************************************!*\
  !*** ./resources/js/admin/consultation/index.js ***!
  \**************************************************/
$(document).ready(function () {
  $('body').on('click', '#crudTable tr', function (e) {
    window.location = $(e.target).closest('tr').find('a.open-link').attr('href');
  });
  function getStatusColor(status) {
    switch (status) {
      case 'pending':
        return 'warning';
      case 'active':
        return 'info';
      case 'completed':
        return 'success';
      case 'rejected':
        return 'danger';
      default:
        return 'danger';
    }
  }
  setInterval(function () {
    var baseUrl = window.location.href.split('/admin')[0];
    $.ajax({
      url: baseUrl + '/admin/consultation-list',
      type: 'GET',
      dataType: 'json',
      success: function success(data) {
        // Clear the current list view
        $('#crudTable tbody').empty();

        // Loop through the JSON data and create new rows for the list view
        var count = 0;
        var max = $('select[name=crudTable_length]').val();
        var scriptElement = document.querySelector('[src*="index.js"]');
        var billing = scriptElement.getAttribute('data-billing');
        data.forEach(function (item) {
          if (count++ >= max) return;
          var row_class = count % 2 === 0 ? 'odd' : 'even';
          var row = '<tr class="' + row_class + '">';

          // Add the columns based on the fields in your list view
          row += '<td><span>' + [item.user.first_name, item.user.last_name].filter(Boolean).join(' ') + '</span></td>';
          row += '<td><span>' + item.user.email + '</span></td>';
          row += '<td><span>' + item.user.phone + '</span></td>';
          row += '<td><span>' + item.user.dob + '</span></td>';
          if (billing === 'true') {
            row += '<td><span>' + item.insurance.insurance_company + '</span></td>';
            row += '<td><span>' + item.insurance.member_id + '</span></td>';
          }
          row += '<td><span><i class="fas fa-circle text-' + getStatusColor(item.status) + ' status-dot animated zoomIn mr-1"></i> ' + item.status.charAt(0).toUpperCase() + item.status.slice(1) + '</span></td>';
          row += '<td><span><a href="' + baseUrl + '/admin/consultation/' + item.id + '/edit" class="open-link btn btn-sm btn-link"><i class="la la-edit"></i> Open</a></span></td>';
          row += '</tr>';

          // Append the new row to the list view
          $('#crudTable tbody').append(row);
        });
      },
      error: function error(jqXHR, textStatus, errorThrown) {
        console.error('Error updating list view:', textStatus, errorThrown);
      }
    });
  }, 30000);
});
/******/ })()
;