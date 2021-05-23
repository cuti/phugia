(() => {
  const select2Default = {
    allowClear: true,
    placeholder: {
      id: '0',
      text: 'Chọn...'
    },
    theme: 'bootstrap4',
  };

  let customerList;
  let city;
  let district;
  let ward;

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
            c.cus_active,
          ]));

          tblCustomer.fnClearTable();
          tblCustomer.fnAddData(d);
          tblCustomer.fnDraw();
        }
      }
    });
  }

  refreshDataTable();

  function clearSelect2(id) {
    $(`#${id}`).html('');
    $(`#${id}`).trigger('select2:select');
  }

  function select2SelectedId(select2Id) {
    const data = $(`#${select2Id}`).select2('data');

    if (data.length === 0) {
      return null;
    }

    return data[0].id;
  }

  function nullIfEmpty(params) {
    if (params === '') {
      return null;
    }
    return params;
  }

  function showSuccessToast(text, duration, width) {
    Swal.fire({
      customClass: {
        htmlContainer: 'text-white',
        icon: 'border-white text-white',
        popup: 'bg-success',
      },
      icon: 'success',
      showConfirmButton: false,
      text,
      timer: duration,
      timerProgressBar: true,
      toast: true,
      width,
    });
  }

  function showWarningToast(text, duration, width) {
    Swal.fire({
      customClass: {
        htmlContainer: 'text-dark',
        icon: 'border-dark text-dark',
        popup: 'bg-warning',
      },
      icon: 'warning',
      showConfirmButton: false,
      text,
      timer: duration,
      timerProgressBar: true,
      toast: true,
      width,
    });
  }

  function showErrorToast(text, width) {
    Swal.fire({
      confirmButtonText: 'Đóng',
      customClass: {
        actions: 'justify-content-end',
        confirmButton: 'bg-white text-dark',
        htmlContainer: 'text-white',
        icon: 'border-white',
        popup: 'bg-danger',
      },
      icon: 'error',
      text,
      toast: true,
      width,
    });
  }

  function clearDetailDlg() {
    $('#txtMaKH').val('');
    $('#txtTenKH').val('');
    $('#txtDiaChi').val('');
    $('#selNhomKH').val(null).trigger('change');
    $('#txtMST').val('');
    $('#txtDienThoai').val('');
    $('#txtDiDong').val('');
    $('#txtFax').val('');
    $('#txtEmail').val('');
    $('#txtWebsite').val('');
    $('#txtCMND').val('');
    $('#dpNgayCap').val('');
    $('#txtNoiCap').val('');
    $('#txtDieuKhoanTT').val('');
    $('#txtSoNgayDuocNo').val('');
    $('#txtNoToiDa').val('');
    $('#txtNhanVien').val('');
    $('#txtTenNhanVien').val('');
    $('#txtTKNH').val('');
    $('#txtTenNganHang').val('');
    $('#txtChiNhanhNH').val('');
    $('#selTinhTKNganHang').val(null).trigger('change');
    $('#selQuocGia').val(null).trigger('change');
    $('#selTinhTP').val(null).trigger('change');
    $('#selQuan').val(null).trigger('change');
    $('#selPhuong').val(null).trigger('change');
    $('#txtXungHo').val('');
    $('#txtNguoiLienHe').val('');
    $('#txtChucDanh').val('');
    $('#txtDTDiDong').val('');
    $('#txtDTDDKhac').val('');
    $('#txtDTCoDinh').val('');
    $('#txtEmailNguoiLienHe').val('');
    $('#txtDiaChiNguoiLienHe').val('');
    $('#txtDiaDiemGH').val('');
    $('#radToChuc').prop('checked', true);
    $('#chkNhaCC').prop('checked', false);
    $('#chkNgungTheoDoi').prop('checked', false);
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
        'sFirst': 'Đầu',
        'sPrevious': 'Trước',
        'sNext': 'Tiếp',
        'sLast': 'Cuối'
      },
      'sInfo': 'Đang xem _START_ đến _END_ trong tổng số _TOTAL_ mục',
      'sInfoEmpty': 'Đang xem 0 đến 0 trong tổng số 0 mục',
      'sInfoFiltered': '(được lọc từ _MAX_ mục)',
      'sInfoPostFix': '',
      'sLengthMenu': 'Xem _MENU_ mục',
      'sProcessing': 'Đang xử lý...',
      'sSearch': 'Tìm:',
      'sZeroRecords': 'Không tìm thấy dòng nào phù hợp',
    },
    'sPaginationType': 'full_numbers',
    'sScrollX': '100%',
  });

  (function getCustomerTypes() {
    $.ajax({
      url: 'customer-type/get-all',
      dataType: 'json',
      success: function (res) {
        $('#selNhomKH').select2({
          allowClear: true,
          data: res.results,
          dropdownParent: $('#selNhomKH').parent(),
          placeholder: 'Chọn...',
          theme: 'bootstrap4',
        });
      }
    });
  })();

  (function getCountry() {
    $.ajax({
      url: 'country/get-all',
      dataType: 'json',
      success: function (res) {
        const data = res.results.map(d => ({
          id: d.id,
          text: d.vn_name || d.text
        }));

        $('#selQuocGia').select2({
          ...select2Default,
          data,
          dropdownParent: $('#selQuocGia').parent(),
        });

        $('#selQuocGia').on('select2:select', function (e) {
          const countryId = e.params.data.id;
          const citiesByCountry = city.filter(c => c.country_id === countryId);
          let opts = '';

          for (let i = 0, l = citiesByCountry.length; i < l; i++) {
            const opt = citiesByCountry[i];
            opts += `<option value="${opt.id}">${opt.text}</option>`;
          }

          $('#selTinhTP').html(opts);

          let params;

          if (citiesByCountry.length > 0) {
            params = {
              data: {
                id: citiesByCountry[0].id
              }
            };
          }

          $('#selTinhTP').trigger({
            type: 'select2:select',
            params,
          });
        });

        $('#selQuocGia').on('select2:clear', function () {
          clearSelect2('selTinhTP');
        });
      }
    });
  })();

  (function getCity() {
    $.ajax({
      url: 'city/get-all',
      dataType: 'json',
      success: function (res) {
        city = res.results;

        $('#selTinhTKNganHang').select2({
          ...select2Default,
          data: city,
          dropdownParent: $('#selTinhTKNganHang').parent(),
        });

        $('#selTinhTP').select2({
          ...select2Default,
          dropdownParent: $('#selTinhTP').parent(),
        });

        $('#selTinhTP').on('select2:select', function (e) {
          let opts = '';
          let districtsByCity = [];

          if (e.params) {
            const cityId = e.params.data.id;
            districtsByCity = district.filter(d => d.city_id === cityId);

            for (let i = 0, l = districtsByCity.length; i < l; i++) {
              const opt = districtsByCity[i];
              opts += `<option value="${opt.id}">${opt.text}</option>`;
            }
          }

          $('#selQuan').html(opts);

          let params;

          if (districtsByCity.length > 0) {
            params = {
              data: {
                id: districtsByCity[0].id
              }
            };
          }

          $('#selQuan').trigger({
            type: 'select2:select',
            params,
          });
        });

        $('#selTinhTP').on('select2:clear', function () {
          clearSelect2('selQuan');
        });
      }
    });
  })();

  (function getDistrict() {
    $.ajax({
      url: 'district/get-all',
      dataType: 'json',
      success: function (res) {
        district = res.results;

        $('#selQuan').select2({
          ...select2Default,
          dropdownParent: $('#selQuan').parent(),
        });

        $('#selQuan').on('select2:select', function (e) {
          let opts = '';

          if (e.params) {
            const districtId = e.params.data.id;
            const wardsByDistrict = ward.filter(w => w.district_id === districtId);

            for (let i = 0, l = wardsByDistrict.length; i < l; i++) {
              const opt = wardsByDistrict[i];
              opts += `<option value="${opt.id}">${opt.text}</option>`;
            }
          }

          $('#selPhuong').html(opts);
        });

        $('#selQuan').on('select2:clear', function () {
          clearSelect2('selPhuong');
        });
      }
    });
  })();

  (function getWard() {
    $.ajax({
      url: 'ward/get-all',
      dataType: 'json',
      success: function (res) {
        ward = res.results;

        $('#selPhuong').select2({
          ...select2Default,
          dropdownParent: $('#selPhuong').parent(),
        });
      }
    });
  })();

  $('#dpNgayCap').daterangepicker({
    autoApply: true,
    locale: {
      format: 'DD/MM/YYYY',
      daysOfWeek: 'CN,T2,T3,T4,T5,T6,T7'.split(','),
      monthNames: 'Tháng 1,Tháng 2,Tháng 3,Tháng 4,Tháng 5,Tháng 6,Tháng 7,Tháng 8,Tháng 9,Tháng 10,Tháng 11,Tháng 12'.split(','),
      firstDay: 1
    },
    showDropdowns: true,
    singleDatePicker: true,
  });

  $('#btnThemKH').click(() => {
    clearDetailDlg();
    $('#detailDlg').modal('show');
  });

  $('#btnSaveCustomer').click(() => {
    if ($('#txtMaKH').val().trim() === '') {
      showWarningToast('Chưa nhập mã khách hàng.', 5000, '300px');
      return;
    }

    if ($('#txtTenKH').val().trim() === '') {
      showWarningToast('Chưa nhập tên khách hàng.', 5000, '300px');
      return;
    }

    const cus_address = nullIfEmpty($('#txtDiaChi').val().trim());
    const cus_tax_code = nullIfEmpty($('#txtMST').val());
    const cus_phone = nullIfEmpty($('#txtDienThoai').val().trim());
    const cus_mobile = nullIfEmpty($('#txtDiDong').val().trim());
    const cus_fax = nullIfEmpty($('#txtFax').val().trim());
    const cus_email = nullIfEmpty($('#txtEmail').val().trim());
    const cus_website = nullIfEmpty($('#txtWebsite').val().trim());
    const cus_citizen_identity_card_number = nullIfEmpty($('#txtCMND').val());
    const cus_citizen_identity_card_date = $('#dpNgayCap').val() === '' ? null : $('#dpNgayCap').data('daterangepicker').startDate.format('YYYY-MM-DD');
    const cus_citizen_identity_card_issued_by = nullIfEmpty($('#txtNoiCap').val().trim());
    const cus_payment_terms = nullIfEmpty($('#txtDieuKhoanTT').val().trim());
    const cus_owed_days = nullIfEmpty($('#txtSoNgayDuocNo').val());
    const cus_max_owed = nullIfEmpty($('#txtNoToiDa').val());
    const cus_staff = nullIfEmpty($('#txtNhanVien').val().trim());
    const cus_staff_name = nullIfEmpty($('#txtTenNhanVien').val().trim());
    const cus_bank_account = nullIfEmpty($('#txtTKNH').val().trim());
    const cus_bank_name = nullIfEmpty($('#txtTenNganHang').val().trim());
    const cus_bank_branch_name = nullIfEmpty($('#txtChiNhanhNH').val().trim());
    const cus_bank_city_id = select2SelectedId('selTinhTKNganHang');
    const cus_country_id = select2SelectedId('selQuocGia');
    const cus_city_id = select2SelectedId('selTinhTP');
    const cus_district_id = select2SelectedId('selQuan');
    const cus_ward_id = select2SelectedId('selPhuong');
    const cus_title = nullIfEmpty($('#txtXungHo').val().trim());
    const cus_contact_person = nullIfEmpty($('#txtNguoiLienHe').val().trim());
    const cus_contact_position = nullIfEmpty($('#txtChucDanh').val().trim());
    const cus_contact_mobile1 = nullIfEmpty($('#txtDTDiDong').val().trim());
    const cus_contact_mobile2 = nullIfEmpty($('#txtDTDDKhac').val().trim());
    const cus_contact_phone = nullIfEmpty($('#txtDTCoDinh').val().trim());
    const cus_contact_email = nullIfEmpty($('#txtEmailNguoiLienHe').val().trim());
    const cus_contact_address = nullIfEmpty($('#txtDiaChiNguoiLienHe').val().trim());
    const cus_delivery_location = nullIfEmpty($('#txtDiaDiemGH').val().trim());
    const cus_types = $('#selNhomKH').select2('data').map(d => d.id);

    const data = {
      customer: {
        cus_code: $('#txtMaKH').val().trim().toUpperCase(),
        cus_name: $('#txtTenKH').val().trim(),
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

    $.ajax({
      data: JSON.stringify(data),
      contentType: 'application/json',
      url: 'customer/insert',
      type: 'POST',
      dataType: 'json',
      success: function (res) {
        if (res.status === 1) {
          showSuccessToast('Lưu khách hàng thành công.', 5000, '300px');
        } else if (res.message === 'CUS_CODE_DUP') {
          showErrorToast('Mã khách hàng đã tồn tại.', '300px');
        } else {
          showErrorToast('Lưu không thành công, vui lòng kiểm tra lại thông tin khách hàng.', '400px');
        }

        refreshDataTable();
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

  $('#btnImportCustomer').click(() => {
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
            refreshDataTable();
          } else if (res.status === 2) {
            pClass = 'text-warning';
            text = `<p>Import thành công ${res.successCount} khách hàng.</p>`;

            if (res.customerCodeExists.length > 0) {
              text += '<p>Các mã khách hàng đã tồn tại: ' + res.customerCodeExists.join(', ') + '</p>';
            }

            if (res.customerCodeError.length > 0) {
              text += '<p>Các mã khách hàng không import được do lỗi khác: ' + res.customerCodeError.join(', ') + '</p>';
            }

            refreshDataTable();
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
})();
