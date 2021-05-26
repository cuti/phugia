(() => {
  const select2Default = {
    allowClear: true,
    placeholder: {
      id: '0',
      text: 'Chọn...'
    },
    theme: 'bootstrap4',
  };

  let tblCustomer;
  let city;
  let district;
  let ward;

  function phpDateToVnDate(ymd) {
    const mDate = moment(ymd);

    if (mDate.isValid()) {
      return moment(ymd).format('DD/MM/YYYY');
    }

    return '';
  }

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
    $('#hidCusId').val('');
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

  function setDataDetailDlg(data) {
    const {
      cus_id,
      cus_code,
      cus_name,
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
      cus_is_organization,
      cus_is_supplier,
      cus_active,
      cus_type_ids
    } = data;

    $('#hidCusId').val(cus_id);
    $('#txtMaKH').val(cus_code);
    $('#txtTenKH').val(cus_name);
    $('#txtDiaChi').val(cus_address);
    $('#selNhomKH').val(cus_type_ids.split(',')).trigger('change');
    $('#txtMST').val(cus_tax_code);
    $('#txtDienThoai').val(cus_phone);
    $('#txtDiDong').val(cus_mobile);
    $('#txtFax').val(cus_fax);
    $('#txtEmail').val(cus_email);
    $('#txtWebsite').val(cus_website);
    $('#txtCMND').val(cus_citizen_identity_card_number);
    cus_citizen_identity_card_date && $('#dpNgayCap').data('daterangepicker').setStartDate(new Date(cus_citizen_identity_card_date));
    $('#txtNoiCap').val(cus_citizen_identity_card_issued_by);
    $('#txtDieuKhoanTT').val(cus_payment_terms);
    $('#txtSoNgayDuocNo').val(cus_owed_days);
    $('#txtNoToiDa').val(cus_max_owed);
    $('#txtNhanVien').val(cus_staff);
    $('#txtTenNhanVien').val(cus_staff_name);
    $('#txtTKNH').val(cus_bank_account);
    $('#txtTenNganHang').val(cus_bank_name);
    $('#txtChiNhanhNH').val(cus_bank_branch_name);
    $('#selTinhTKNganHang').val(cus_bank_city_id).trigger('change');
    $('#selQuocGia').val(cus_country_id).trigger('change');
    $('#selTinhTP').val(cus_city_id).trigger('change');
    $('#selQuan').val(cus_district_id).trigger('change');
    $('#selPhuong').val(cus_ward_id).trigger('change');
    $('#txtXungHo').val(cus_title);
    $('#txtNguoiLienHe').val(cus_contact_person);
    $('#txtChucDanh').val(cus_contact_position);
    $('#txtDTDiDong').val(cus_contact_mobile1);
    $('#txtDTDDKhac').val(cus_contact_mobile2);
    $('#txtDTCoDinh').val(cus_contact_phone);
    $('#txtEmailNguoiLienHe').val(cus_contact_email);
    $('#txtDiaChiNguoiLienHe').val(cus_contact_address);
    $('#txtDiaDiemGH').val(cus_delivery_location);
    $('#radToChuc').prop('checked', !!+cus_is_organization);
    $('#radCaNhan').prop('checked', !+cus_is_organization);
    $('#chkNhaCC').prop('checked', !!+cus_is_supplier);
    $('#chkNgungTheoDoi').prop('checked', !+cus_active);
  }

  tblCustomer = $('#tblCustomer').DataTable({
    ajax: {
      url: 'customer/get-all',
    },
    columns: [
      {
        data: null,
        responsivePriority: 1,
        title: 'Chi tiết',
        width: '50px',
      },
      {
        data: null,
        render: () => {
          const btnEdit = '<button type="button" class="btn-edit btn btn-outline-dark btn-sm" title="Điều chỉnh thông tin"><i class="fas fa-edit"></i></button>';
          const btnDelete = '<button type="button" class="btn-delete btn btn-danger btn-sm ml-2" title="Xóa"><i class="fas fa-trash"></i></button>';

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
        render: data => phpDateToVnDate(data),
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
        render: data => phpDateToVnDate(data),
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
        render: data => phpDateToVnDate(data),
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
        targets: ['_all'],
        orderable: false,
        defaultContent: '',
      },
      {
        targets: [0, 1],
        searchable: false,
      }
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
      zeroRecords: 'Không tìm thấy dòng nào phù hợp',
    },
    ordering: false,
    pagingType: 'full_numbers',
    scrollX: true,
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

    const cus_id = $('#hidCusId').val();
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
          showSuccessToast('Lưu khách hàng thành công.', 5000, '300px');
          tblCustomer.ajax.reload();
        } else if (res.message === 'CUS_CODE_DUP') {
          showErrorToast('Mã khách hàng đã tồn tại.', '300px');
        } else {
          showErrorToast('Lưu không thành công, vui lòng kiểm tra lại thông tin khách hàng.', '400px');
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
            tblCustomer.ajax.reload();
          } else if (res.status === 2) {
            pClass = 'text-warning';
            text = `<p>Import thành công ${res.successCount} khách hàng.</p>`;

            if (res.customerCodeExists.length > 0) {
              text += '<p>Các mã khách hàng đã tồn tại: ' + res.customerCodeExists.join(', ') + '</p>';
            }

            if (res.customerCodeError.length > 0) {
              text += '<p>Các mã khách hàng không import được do lỗi khác: ' + res.customerCodeError.join(', ') + '</p>';
            }

            tblCustomer.ajax.reload();
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
    const data = tblCustomer.row($(e.target).closest('tr')).data();

    setDataDetailDlg(data);
    $('#detailDlg').modal('show');
  });

  $('body').on('click', '.btn-delete', e => {
    const data = tblCustomer.row($(e.target).closest('tr')).data();

    // TODO: Confirm delete
  });
})();
