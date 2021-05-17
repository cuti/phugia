(() => {
  let customerList;

  function refreshDataTable() {
    $.ajax({
      url: 'customer/get-all',
      dataType: 'json',
      success: function (response) {
        customerList = response.data;

        if (response.data.length > 0) {
          const d = response.data.map(c => ([
            c.cus_code,
            c.cus_name,
            c.cus_address,
            c.cus_tax_code,
            c.cus_phone,
            c.cus_mobile,
            c.cus_types,
            c.cus_fax,
            c.cus_email,
            c.cus_website,
            c.cus_citizen_identity_card_number,
            c.cus_citizen_identity_card_date,
            c.cus_citizen_identity_card_issued_by,
            c.cus_payment_terms,
            c.cus_owed_days,
            c.cus_max_owed,
            c.cus_staff,
            c.cus_staff_name,
            c.cus_bank_account,
            c.cus_bank_name,
            c.cus_bank_branch_name,
            c.cus_bank_city_name,
            c.cus_country_name,
            c.cus_city_name,
            c.cus_district_name,
            c.cus_ward_name,
            c.cus_title,
            c.cus_contact_person,
            c.cus_contact_position,
            c.cus_contact_mobile1,
            c.cus_contact_mobile2,
            c.cus_contact_phone,
            c.cus_contact_email,
            c.cus_contact_address,
            c.cus_delivery_location,
            c.cus_is_organization,
            c.cus_is_supplier,
            c.cus_status_name,
          ]));

          tblCustomer.fnClearTable();
          tblCustomer.fnAddData(d);
          tblCustomer.fnDraw();
        }
      }
    });
  }

  const tblCustomer = $('#tblCustomer').dataTable({
    'aoColumnDefs': [
      {
        'aTargets': ['_all'],
        'bSortable': false,
        'sDefaultContent': '',
      },
      // {
      //   'aTargets': [0, 21],
      //   'bSearchable': false,
      // }
    ],
		'bJQueryUI': true,
		'bRetrieve': true,
		'oLanguage': {
			'oPaginate': {
				'sFirst':    'Đầu',
				'sPrevious': 'Trước',
				'sNext':     'Tiếp',
				'sLast':     'Cuối'
			},
			'sInfo':         'Đang xem _START_ đến _END_ trong tổng số _TOTAL_ mục',
			'sInfoEmpty':    'Đang xem 0 đến 0 trong tổng số 0 mục',
			'sInfoFiltered': '(được lọc từ _MAX_ mục)',
			'sInfoPostFix':  '',
			'sLengthMenu':   'Xem _MENU_ mục',
			'sProcessing':   'Đang xử lý...',
			'sSearch':       'Tìm:',
			'sZeroRecords':  'Không tìm thấy dòng nào phù hợp',
		},
		'sPaginationType': 'full_numbers',
		'sScrollX': '100%',
  });

  refreshDataTable();

  $('#detailDlg').width($(window).width() - 40);

  $('#selNhomKH').select2();
})();
