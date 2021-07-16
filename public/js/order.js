(() => {
  const select2Default = {
    allowClear: true,
    placeholder: {
      id: '0',
      text: 'Chọn...'
    },
    theme: 'bootstrap4',
  };

  let products;
  let selectedCustomers = [];
  let tblOrder;
  let tblProduct;

  function clearSelect2(id) {
    $(`#${id}`).html('');
    $(`#${id}`).trigger('select2:select');
  }

  function select2SelectedId(id) {
    const data = $(`#${id}`).select2('data');

    if (data.length === 0) {
      return null;
    }

    return data[0].id;
  }

  function confirmDeleteOrder() {
    return Swal.fire({
      cancelButtonText: 'Hủy',
      confirmButtonText: 'Xóa',
      customClass: {
        confirmButton: 'bg-danger text-white',
        icon: 'border-danger text-danger',
      },
      html: `Vui lòng xác nhận bạn muốn xóa đơn hàng này?`,
      icon: 'warning',
      reverseButtons: true,
      showCancelButton: true,
      title: 'Xóa đơn hàng?',
      width: '500px',
    });
  }

  function clearDetailDlg() {
    $('#hidOrderId').val('');
    $('#selCustomer').val(null).trigger('change');
    $('#txtOrderDescription').val('');
    $('#dpDocumentDate').val('');
    $('#dpInvoiceDate').val('');
    $('#txtInvoiceNumber').val('');
    tblProduct.clear().draw();
  }

  function setDataDetailDlg(data) {
    const {
      order_id,
      order_customer_id,
      order_description,
      order_document_date,
      order_invoice_date,
      order_invoice_number,
    } = data;

    $('#hidOrderId').val(order_id);
    $('#selCustomer').val(order_customer_id).trigger('change');
    $('#txtOrderDescription').val(order_description);
    order_document_date && $('#dpDocumentDate').data('daterangepicker').setStartDate(new Date(order_document_date));
    order_invoice_date && $('#dpInvoiceDate').data('daterangepicker').setStartDate(new Date(order_invoice_date));
    $('#txtInvoiceNumber').val(order_invoice_number);


    // TODO request order detail
    // tblProduct.clear().draw();
  }

  tblOrder = $('#tblOrder').DataTable({
    ajax: {
      url: 'customer/get-all',
    },
    columns: [
      {
        data: 'cus_id',
        width: '30px',
      },
      {
        data: null,
        render: () => {
          const btnEdit = `<button type="button" class="btn-edit btn btn-outline-secondary btn-sm" title="Điều chỉnh thông tin">
              <i class="fas fa-edit"></i>
            </button>`;
          const btnDelete = `<button type="button" class="btn-delete btn btn-outline-danger btn-sm ml-2" title="Xóa">
              <i class="fas fa-trash"></i>
            </button>`;

          return btnEdit + btnDelete;
        },
        responsivePriority: 1,
        title: 'Thao tác',
        width: '80px',
      },
      {
        data: 'cus_code',
        responsivePriority: 1,
        title: 'Mã khách hàng',
        width: '150px',
      },
      {
        data: 'cus_name',
        responsivePriority: 1,
        title: 'Tên khách hàng',
        width: '150px',
      },
      {
        data: 'cus_address',
        responsivePriority: 1,
        title: 'Địa chỉ',
        width: '200px',
      },
      {
        data: 'cus_phone',
        responsivePriority: 1,
        title: 'Điện thoại',
        width: '100px',
      },
      {
        data: 'cus_mobile',
        responsivePriority: 1,
        title: 'ĐT di động',
        width: '100px',
      },
      {
        data: 'cus_tax_code',
        responsivePriority: 2,
        title: 'Mã số thuế',
        width: '100px',
      },
      {
        data: 'cus_types',
        responsivePriority: 2,
        title: 'Nhóm KH, NCC',
        width: '100px',
      },
      {
        data: 'cus_fax',
        responsivePriority: 2,
        title: 'Fax',
        width: '100px',
      },
      {
        data: 'cus_email',
        responsivePriority: 2,
        title: 'Email',
        width: '100px',
      },
      {
        data: 'cus_website',
        responsivePriority: 2,
        title: 'Website',
        width: '100px',
      },
      {
        data: 'cus_citizen_identity_card_number',
        responsivePriority: 2,
        title: 'Số CMND',
        width: '100px',
      },
      {
        data: 'cus_citizen_identity_card_date',
        render: data => Utility.phpDateToVnDate(data),
        title: 'Ngày cấp',
        responsivePriority: 2,
        type: 'date',
        width: '100px',
      },
      {
        data: 'cus_citizen_identity_card_issued_by',
        responsivePriority: 2,
        title: 'Nơi cấp',
        width: '100px',
      },
      {
        data: 'cus_payment_terms',
        responsivePriority: 2,
        title: 'Điều khoản TT',
        width: '100px',
      },
      {
        data: 'cus_owed_days',
        title: 'Số ngày được nợ',
        responsivePriority: 2,
        type: 'num',
        width: '100px',
      },
      {
        data: 'cus_max_owed',
        render: $.fn.dataTable.render.number('.', ',', 0, '', ' đ'),
        title: 'Số nợ tối đa',
        responsivePriority: 2,
        type: 'num',
        width: '100px',
      },
      {
        data: 'cus_staff',
        responsivePriority: 2,
        title: 'Nhân viên',
        width: '100px',
      },
      {
        data: 'cus_staff_name',
        responsivePriority: 2,
        title: 'Tên nhân viên',
        width: '100px',
      },
      {
        data: 'cus_bank_account',
        responsivePriority: 2,
        title: 'TK ngân hàng',
        width: '100px',
      },
      {
        data: 'cus_bank_name',
        responsivePriority: 2,
        title: 'Tên ngân hàng',
        width: '100px',
      },
      {
        data: 'cus_bank_branch_name',
        responsivePriority: 2,
        title: 'Chi nhánh TK ngân hàng',
        width: '100px',
      },
      {
        data: 'cus_bank_city_name',
        responsivePriority: 2,
        title: 'Tỉnh/TP TK ngân hàng',
        width: '100px',
      },
      {
        data: 'cus_country_name',
        responsivePriority: 2,
        title: 'Quốc gia',
        width: '100px',
      },
      {
        data: 'cus_city_name',
        responsivePriority: 2,
        title: 'Tỉnh/TP',
        width: '100px',
      },
      {
        data: 'cus_district_name',
        responsivePriority: 2,
        title: 'Quận/Huyện',
        width: '100px',
      },
      {
        data: 'cus_ward_name',
        responsivePriority: 2,
        title: 'Phường/Xã',
        width: '100px',
      },
      {
        data: 'cus_title',
        responsivePriority: 2,
        title: 'Xưng hô',
        width: '100px',
      },
      {
        data: 'cus_contact_person',
        responsivePriority: 2,
        title: 'Người liên hệ',
        width: '100px',
      },
      {
        data: 'cus_contact_position',
        responsivePriority: 2,
        title: 'Chức danh',
        width: '100px',
      },
      {
        data: 'cus_contact_mobile1',
        responsivePriority: 2,
        title: 'ĐT di động',
        width: '100px',
      },
      {
        data: 'cus_contact_mobile2',
        responsivePriority: 2,
        title: 'ĐTDĐ khác',
        width: '100px',
      },
      {
        data: 'cus_contact_phone',
        responsivePriority: 2,
        title: 'ĐT cố định',
        width: '100px',
      },
      {
        data: 'cus_contact_email',
        responsivePriority: 2,
        title: 'Email',
        width: '100px',
      },
      {
        data: 'cus_contact_address',
        responsivePriority: 2,
        title: 'Địa chỉ',
        width: '100px',
      },
      {
        data: 'cus_delivery_location',
        responsivePriority: 2,
        title: 'Địa điểm giao hàng',
        width: '100px',
      },
      {
        data: 'cus_is_organization',
        render: data => {
          let input = '<input type="checkbox" disabled';

          if (data === '1') {
            input += ' checked';
          }

          return input + '>';
        },
        responsivePriority: 2,
        title: 'Tổ chức/Cá nhân',
        width: '100px',
      },
      {
        data: 'cus_is_supplier',
        render: data => {
          let input = '<input type="checkbox" disabled';

          if (data === '1') {
            input += ' checked';
          }

          return input + '>';
        },
        responsivePriority: 2,
        title: 'Là nhà cung cấp',
        width: '100px',
      },
      {
        data: 'cus_active',
        render: data => {
          let input = '<input type="checkbox" disabled';

          if (data === '1') {
            input += ' checked';
          }

          return input + '>';
        },
        responsivePriority: 2,
        title: 'Theo dõi',
        width: '100px',
      },
      {
        data: 'cus_created',
        render: data => Utility.phpDateToVnDate(data),
        responsivePriority: 2,
        title: 'Ngày tạo',
        type: 'date',
        width: '100px',
      },
      {
        data: 'cus_created_by_username',
        responsivePriority: 2,
        title: 'Người tạo',
        width: '100px',
      },
      {
        data: 'cus_last_updated',
        render: data => Utility.phpDateToVnDate(data),
        responsivePriority: 2,
        title: 'Ngày cập nhật sau cùng',
        type: 'date',
        width: '100px',
      },
      {
        data: 'cus_last_updated_by_username',
        responsivePriority: 2,
        title: 'Người cập nhật sau cùng',
        width: '100px',
      },
    ],
    columnDefs: [
      {
        targets: 0,
        checkboxes: {
          selectRow: true,
          selectCallback: (nodes, selected) => {
            const selectedNodesCount = nodes.length;

            if (selected) {
              for (let i = 0; i < selectedNodesCount; i++) {
                const tr = $(nodes[i]).closest('tr');
                const data = tblOrder.row(tr).data();

                if (selectedCustomers.indexOf(data.cus_id) === -1) {
                  selectedCustomers.push(data.cus_id);
                }
              }
            } else {
              for (let i = 0; i < selectedNodesCount; i++) {
                const tr = $(nodes[i]).closest('tr');
                const data = tblOrder.row(tr).data();
                selectedCustomers.splice(selectedCustomers.indexOf(data.cus_id), 1);
              }
            }

            console.log(selectedCustomers);
          }
        },
      },
      {
        targets: ['_all'],
        orderable: false,
        defaultContent: '',
      },
      {
        targets: [0, 1],
        searchable: false,
      },
    ],
    language: {
      emptyTable: 'Không có dữ liệu',
      info: 'Đang xem _START_ đến _END_ trong tổng số _TOTAL_ mục',
      infoEmpty: 'Đang xem 0 đến 0 trong tổng số 0 mục',
      infoFiltered: '(được lọc từ _MAX_ mục)',
      lengthMenu: 'Xem _MENU_ mục',
      loadingRecords: 'Đang lấy dữ liệu...',
      paginate: {
        first: 'Đầu',
        last: 'Cuối',
        next: 'Tiếp',
        previous: 'Trước',
      },
      processing: 'Đang xử lý...',
      search: 'Tìm ',
      select: {
        rows: '%d dòng được chọn'
      },
      zeroRecords: 'Không tìm thấy dòng nào phù hợp',
    },
    ordering: false,
    pagingType: 'full_numbers',
    scrollX: true,
    select: {
      style: 'multi'
    }
  });

  (function getCustomerTypes() {
    $.ajax({
      url: 'customer-type/get-all',
      dataType: 'json',
      success: function (res) {
        $('#selNhomKH').select2({
          allowClear: true,
          data: res.data,
          dropdownParent: $('#selNhomKH').parent(),
          placeholder: 'Chọn...',
          theme: 'bootstrap4',
        });
      }
    });
  })();

  $('#btnNewOrder').click(() => {
    clearDetailDlg();
    $('#detailDlg').modal('show');
  });

  $('#btnRefresh').click(() => {
    selectedCustomers = [];
    tblOrder.clear().draw();
    tblOrder.ajax.reload();
  });

  $('#btnSaveOrder').click(() => {
    if ($('#txtCustomerCode').val().trim() === '') {
      Toast.showWarning('Chưa nhập mã khách hàng.');
      return;
    }

    if ($('#txtCustomerName').val().trim() === '') {
      Toast.showWarning('Chưa nhập tên khách hàng.');
      return;
    }

    const cus_id = $('#hidOrderId').val();
    const cus_address = Utility.nullIfEmpty($('#txtDiaChi').val().trim());
    const cus_tax_code = Utility.nullIfEmpty($('#txtMST').val());
    const cus_phone = Utility.nullIfEmpty($('#txtDienThoai').val().trim());
    const cus_mobile = Utility.nullIfEmpty($('#txtDiDong').val().trim());
    const cus_fax = Utility.nullIfEmpty($('#txtFax').val().trim());
    const cus_email = Utility.nullIfEmpty($('#txtEmail').val().trim());
    const cus_website = Utility.nullIfEmpty($('#txtWebsite').val().trim());
    const cus_citizen_identity_card_number = Utility.nullIfEmpty($('#txtCMND').val());
    const cus_citizen_identity_card_date = $('#dpNgayCap').val() === '' ? null : $('#dpNgayCap').data('daterangepicker').startDate.format('YYYY-MM-DD');
    const cus_citizen_identity_card_issued_by = Utility.nullIfEmpty($('#txtNoiCap').val().trim());
    const cus_payment_terms = Utility.nullIfEmpty($('#txtDieuKhoanTT').val().trim());
    const cus_owed_days = Utility.nullIfEmpty($('#txtSoNgayDuocNo').val());
    const cus_max_owed = Utility.nullIfEmpty($('#txtNoToiDa').val());
    const cus_staff = Utility.nullIfEmpty($('#txtNhanVien').val().trim());
    const cus_staff_name = Utility.nullIfEmpty($('#txtTenNhanVien').val().trim());
    const cus_bank_account = Utility.nullIfEmpty($('#txtTKNH').val().trim());
    const cus_bank_name = Utility.nullIfEmpty($('#txtTenNganHang').val().trim());
    const cus_bank_branch_name = Utility.nullIfEmpty($('#txtChiNhanhNH').val().trim());
    const cus_bank_city_id = select2SelectedId('selTinhTKNganHang');
    const cus_country_id = select2SelectedId('selQuocGia');
    const cus_city_id = select2SelectedId('selTinhTP');
    const cus_district_id = select2SelectedId('selQuan');
    const cus_ward_id = select2SelectedId('selPhuong');
    const cus_title = Utility.nullIfEmpty($('#txtXungHo').val().trim());
    const cus_contact_person = Utility.nullIfEmpty($('#txtNguoiLienHe').val().trim());
    const cus_contact_position = Utility.nullIfEmpty($('#txtChucDanh').val().trim());
    const cus_contact_mobile1 = Utility.nullIfEmpty($('#txtDTDiDong').val().trim());
    const cus_contact_mobile2 = Utility.nullIfEmpty($('#txtDTDDKhac').val().trim());
    const cus_contact_phone = Utility.nullIfEmpty($('#txtDTCoDinh').val().trim());
    const cus_contact_email = Utility.nullIfEmpty($('#txtEmailNguoiLienHe').val().trim());
    const cus_contact_address = Utility.nullIfEmpty($('#txtDiaChiNguoiLienHe').val().trim());
    const cus_delivery_location = Utility.nullIfEmpty($('#txtDiaDiemGH').val().trim());
    const cus_types = $('#selNhomKH').select2('data').map(d => d.id);

    const data = {
      customer: {
        cus_code: $('#txtCustomerCode').val().trim().toUpperCase(),
        cus_name: $('#txtCustomerName').val().trim(),
        cus_address,
        cus_tax_code,
        cus_phone,
        cus_mobile,
        cus_fax,
        cus_email,
        cus_website,
        cus_citizen_identity_card_number,
        cus_citizen_identity_card_date,
        cus_citizen_identity_card_issued_by,
        cus_payment_terms,
        cus_owed_days,
        cus_max_owed,
        cus_staff,
        cus_staff_name,
        cus_bank_account,
        cus_bank_name,
        cus_bank_branch_name,
        cus_bank_city_id,
        cus_country_id,
        cus_city_id,
        cus_district_id,
        cus_ward_id,
        cus_title,
        cus_contact_person,
        cus_contact_position,
        cus_contact_mobile1,
        cus_contact_mobile2,
        cus_contact_phone,
        cus_contact_email,
        cus_contact_address,
        cus_delivery_location,
        cus_is_organization: $('#radToChuc').is(':checked') ? 1 : 0,
        cus_is_supplier: $('#chkNhaCC').is(':checked') ? 1 : 0,
        cus_active: $('#chkNgungTheoDoi').is(':checked') ? 0 : 1,
      },
      customerTypes: cus_types,
    };

    let url;

    if (cus_id !== '') {
      data.cusId = cus_id;
      url = 'customer/update';
    } else {
      url = 'customer/insert';
    }

    $.ajax({
      data: JSON.stringify(data),
      contentType: 'application/json',
      url,
      type: 'POST',
      dataType: 'json',
      success: function (res) {
        if (res.status === 1) {
          Toast.showSuccess('Lưu khách hàng thành công.');
          tblOrder.ajax.reload();
        } else if (res.message === 'CUS_CODE_DUP') {
          Toast.showError('Mã khách hàng đã tồn tại.');
        } else {
          Toast.showError('Lưu không thành công, vui lòng kiểm tra lại thông tin khách hàng.', '400px');
        }
      }
    });
  });

  $('#fileUpload').on('change', function () {
    const file = this.files[0];

    $('#importDlg-filename').text(file.name);
    $('#importDlg').modal('show');
  });

  $('#importDlg').on('hidden.bs.modal', function () {
    $('#fileUpload').val('');
    $('#importDlg-result').remove();
  });

  $('#btnImportOrder').click(() => {
    const DELIMITER = 'base64,';
    const file = $('#fileUpload').get(0).files[0];
    const reader = new FileReader();

    reader.onload = function () {
      let result = reader.result;

      result = result.substr(result.indexOf(DELIMITER) + DELIMITER.length);

      $.ajax({
        data: {
          fileContent: result,
          fileName: file.name,
        },
        url: 'customer/import',
        type: 'POST',
        dataType: 'json',
        success: function (res) {
          let pClass;
          let text;

          if (res.status === 1) {
            pClass = 'text-success';
            text = '<p>Import thành công tất cả khách hàng.</p>';
            tblOrder.ajax.reload();
          } else if (res.status === 2) {
            pClass = 'text-warning';
            text = `<p>Import thành công ${res.successCount} khách hàng.</p>`;

            if (res.customerCodeExists.length > 0) {
              text += '<p>Các mã khách hàng đã tồn tại: ' + res.customerCodeExists.join(', ') + '</p>';
            }

            if (res.customerCodeError.length > 0) {
              text += '<p>Các mã khách hàng không import được do lỗi khác: ' + res.customerCodeError.join(', ') + '</p>';
            }

            tblOrder.ajax.reload();
          } else {
            pClass = 'text-danger';
            text = '<p>Không import được khách hàng nào.</p>';

            if (res.customerCodeExists.length > 0) {
              text += '<p>Các mã khách hàng đã tồn tại: ' + res.customerCodeExists.join(', ') + '</p>';
            }

            if (res.customerCodeError.length > 0) {
              text += '<p>Các mã khách hàng không import được do lỗi khác: ' + res.customerCodeError.join(', ') + '</p>';
            }
          }

          $('#importDlg .modal-body').append(`<div id="importDlg-result" class="${pClass}"><hr>${text}</div>`);
        }
      });
    }

    reader.readAsDataURL(file);
  });

  $('body').on('click', '.btn-edit', e => {
    const data = tblOrder.row($(e.target).closest('tr')).data();

    setDataDetailDlg(data);
    $('#detailDlg').modal('show');
  });

  $(window).resize(() => tblOrder.draw());
})();
