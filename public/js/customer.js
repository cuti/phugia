(() => {
  $('#tblCustomer').dataTable({
    // aoColumns: [
    //   { 'mData': 'engine' },
    //   { 'mData': 'browser' },
    //   { 'mData': 'platform.inner' },
    //   { 'mData': 'platform.details.0' },
    //   { 'mData': 'platform.details.1' }
    // ],
    aoColumnDefs: [{
      aTargets: [0, 21, 22, 23, 24, 25, 37, 39, 41],
      bVisible: false,
    }],
		'bJQueryUI': true,
		'bScrollCollapse': true,
    'sAjaxSource': 'customer/get-all',
		'sPaginationType': 'full_numbers',
		'sScrollX': '100%',
  });
})();
