(() => {
  let tblStaff;
  let editingStaffId;

  function select2SelectedId(id) {
    const data = $(`#${id}`).select2('data');

    if (data.length === 0) {
      return null;
    }

    return data[0].id;
  }

  function confirmDeleteUser(fullname) {
    return Swal.fire({
      cancelButtonText: 'Hủy',
      confirmButtonText: 'Xóa',
      customClass: {
        confirmButton: 'bg-danger text-white',
        icon: 'border-danger text-danger',
      },
      html: `Vui lòng xác nhận bạn muốn xóa nhân viên<br><strong>${fullname}</strong>`,
      icon: 'warning',
      reverseButtons: true,
      showCancelButton: true,
      title: 'Xóa nhân viên?',
      width: '500px',
    });
  }

  function confirmResetPass(fullname) {
    return Swal.fire({
      cancelButtonText: 'Hủy',
      confirmButtonText: 'Đồng ý',
      customClass: {
        confirmButton: 'bg-danger text-white',
        icon: 'border-danger text-danger',
      },
      html: `Vui lòng xác nhận bạn muốn thiết lập lại mật khẩu cho nhân viên<br><strong>${fullname}</strong>`,
      icon: 'warning',
      reverseButtons: true,
      showCancelButton: true,
      title: 'Thiết lập lại mật khẩu?',
      width: '560px',
    });
  }

  function clearDetailDlg() {
    editingStaffId = undefined;
    $('#txtFullname').val('');
    $('input[name=gender]').prop('checked', false).parent().removeClass('active');
    $('#selBirthYear').val(null).trigger('change');
    $('#txtStaffCode').val('');
    $('#txtEmail').val('');
    $('#txtDisplayName').val('');
    $('#selDepartment').val(null).trigger('change');
    $('#txtPosition').val('');
    $('#txtUsername').val('');
    $('#txtPassword').val('').prop('readonly', false);
    $('#selRole').val(null).trigger('change');
    $('#chkActive').prop('checked', true);
  }

  function setDataDetailDlg(data) {
    const {
      staff_id,
      staff_fullname,
      staff_gender,
      staff_birth_year,
      staff_code,
      staff_email,
      staff_display_name,
      staff_department_id,
      staff_position,
      staff_username,
      staff_active,
      role_id
    } = data;

    editingStaffId = staff_id;
    $('#txtFullname').val(staff_fullname);

    switch (staff_gender) {
      case 'Nam':
        $('#genderMale').prop('checked', true).parent().addClass('active');
        $('#genderFemale').prop('checked', false).parent().removeClass('active');
        break;
      case 'Nữ':
        $('#genderMale').prop('checked', false).parent().removeClass('active');
        $('#genderFemale').prop('checked', true).parent().addClass('active');
        break;
      default:
        $('input[name=gender]').prop('checked', false).parent().removeClass('active');
        break;
    }

    $('#selBirthYear').val(staff_birth_year).trigger('change');
    $('#txtStaffCode').val(staff_code);
    $('#txtEmail').val(staff_email);
    $('#txtDisplayName').val(staff_display_name);
    $('#selDepartment').val(staff_department_id).trigger('change');
    $('#txtPosition').val(staff_position);
    $('#txtUsername').val(staff_username);
    $('#txtPassword').val('(Đã mã hóa)').prop('readonly', true);
    $('#selRole').val(role_id).trigger('change');
    $('#chkActive').prop('checked', !!+staff_active);
  }

  function validateInput() {
    if ($('#txtFullname').val().trim() === '') {
      Toast.showWarning('Chưa nhập họ và tên.', 2000);
      return false;
    }

    if ($('input[name=gender]:checked').length === 0) {
      Toast.showWarning('Chưa chọn giới tính.', 2000);
      return false;
    }

    if ($('#selBirthYear').val() === null) {
      Toast.showWarning('Chưa chọn năm sinh.', 2000);
      return false;
    }

    if ($('#txtEmail').val().trim() === '') {
      Toast.showWarning('Chưa nhập địa chỉ email.', 2000);
      return false;
    }

    if ($('#txtUsername').val().trim() === '') {
      Toast.showWarning('Chưa nhập username.', 2000);
      return false;
    }

    if ($('#txtPassword').val() === '') {
      Toast.showWarning('Chưa nhập mật khẩu.', 2000);
      return false;
    }

    if ($('#selRole').val() === null) {
      Toast.showWarning('Chưa chọn nhóm người dùng.', 2000);
      return false;
    }

    return true;
  }

  tblStaff = $('#tblStaff').DataTable({
    ajax: {
      url: 'staff/get-all',
    },
    columns: [
      {
        className: 'center',
        data: null,
        render: () => {
          const btnEdit = `<button type="button" class="btn-edit btn btn-outline-secondary btn-sm" title="Điều chỉnh thông tin">
              <i class="fas fa-user-edit"></i>
            </button>`;
          const btnDelete = `<button type="button" class="btn-delete btn btn-outline-danger btn-sm ml-2" title="Xóa">
              <i class="fas fa-user-slash"></i>
            </button>`;
          const btnResetPass = `<button type="button" class="btn-reset-pass btn btn-outline-secondary btn-sm ml-2" title="Cấp lại mật khẩu">
              <i class="fas fa-key"></i>
            </button>`;

          return btnEdit + btnDelete + btnResetPass;
        },
        responsivePriority: 1,
        title: 'Thao tác',
        width: '110px',
      },
      {
        className: 'center',
        data: 'staff_active',
        render: data => {
          let fa_icon, tooltip, btnClass;

          if (+data) {
            fa_icon = 'user-check';
            tooltip = 'Khóa nhân viên';
            btnClass = 'success';
          } else {
            fa_icon = 'user-lock';
            tooltip = 'Mở khóa nhân viên';
            btnClass = 'danger';
          }

          const btnToggleActive = `<button type="button" class="btn-toggle-active btn btn-outline-${btnClass} btn-sm" title="${tooltip}">
              <i class="fas fa-${fa_icon}"></i>
            </button>`;

          return btnToggleActive;
        },
        responsivePriority: 1,
        title: 'Trạng thái',
        width: '40px',
      },
      {
        data: 'staff_code',
        responsivePriority: 1,
        title: 'Mã nhân viên',
        width: '120px',
      },
      {
        data: 'staff_username',
        responsivePriority: 1,
        title: 'Username',
        width: '120px',
      },
      {
        data: 'staff_fullname',
        responsivePriority: 2,
        title: 'Họ và tên',
        width: '170px',
      },
      {
        data: 'staff_display_name',
        responsivePriority: 3,
        title: 'Tên hiển thị',
        width: '150px',
      },
      {
        data: 'staff_gender',
        responsivePriority: 4,
        title: 'Giới tính',
        width: '150px',
      },
      {
        data: 'staff_birth_year',
        responsivePriority: 5,
        title: 'Năm sinh',
        width: '120px',
      },
      {
        data: 'staff_position',
        responsivePriority: 6,
        title: 'Vị trí công việc',
        width: '200px',
      },
      {
        data: 'role_name',
        responsivePriority: 7,
        title: 'Nhóm người dùng',
        width: '180px',
      },
      {
        data: 'staff_email',
        responsivePriority: 8,
        title: 'Email',
        width: '180px',
      },
      {
        data: 'staff_department',
        responsivePriority: 9,
        title: 'Phòng',
        width: '170px',
      },
      {
        className: 'center',
        data: 'staff_created',
        render: data => Utility.phpDateToVnDate(data),
        responsivePriority: 10,
        title: 'Ngày tạo',
        type: 'date',
        width: '100px',
      },
      {
        data: 'staff_created_by_username',
        responsivePriority: 11,
        title: 'Người tạo',
        width: '120px',
      },
      {
        className: 'center',
        data: 'staff_last_updated',
        render: data => Utility.phpDateToVnDate(data),
        responsivePriority: 12,
        title: 'Ngày cập nhật sau cùng',
        type: 'date',
        width: '100px',
      },
      {
        data: 'staff_last_updated_by_username',
        responsivePriority: 13,
        title: 'Người cập nhật sau cùng',
        width: '120px',
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
      zeroRecords: 'Không tìm thấy dòng nào phù hợp',
    },
    ordering: false,
    pagingType: 'full_numbers',
    scrollX: true,
  });

  $('#selBirthYear').select2({
    dropdownParent: $('#selBirthYear').parent(),
    theme: 'bootstrap4'
  });

  (function getDepartment() {
    $.ajax({
      url: 'department/get-all',
      dataType: 'json',
      success: function (res) {
        if (res.message === 'SESSION_END') {
          Toast.showError('Đã hết phiên làm việc, vui lòng đăng nhập lại để tiếp tục sử dụng');
          return;
        }

        const data = res.data.map(d => ({
          id: d.id,
          text: d.text
        }));

        $('#selDepartment').select2({
          allowClear: true,
          data,
          dropdownParent: $('#selDepartment').parent(),
          placeholder: {
            id: '0',
            text: 'Chọn...'
          },
          theme: 'bootstrap4',
        });
      }
    });
  })();

  (function getRole() {
    $.ajax({
      url: 'role/get-list',
      dataType: 'json',
      success: function (res) {
        if (res.message === 'SESSION_END') {
          Toast.showError('Đã hết phiên làm việc, vui lòng đăng nhập lại để tiếp tục sử dụng');
          return;
        }

        $('#selRole').select2({
          data: res.data,
          dropdownParent: $('#selRole').parent(),
          theme: 'bootstrap4',
        });
      }
    });
  })();

  $('#detailDlg').on('shown.bs.modal', e => {
    $('#txtFullname').focus();
  });

  $('#btnNewStaff').click(() => {
    clearDetailDlg();
    $('#detailDlg').modal('show');
  });

  $('#formStaffDetail').submit(e => {
    e.preventDefault();

    if (!validateInput()) return;

    const staffId = editingStaffId;
    const staff_fullname = Utility.nullIfEmpty($('#txtFullname').val().trim());
    const staff_gender = $('#genderMale').is(':checked') ? 'Nam' : 'Nữ';
    const staff_birth_year = +$('#selBirthYear').val();
    const staff_code = Utility.nullIfEmpty($('#txtStaffCode').val().trim());
    const staff_email = $('#txtEmail').val().trim();
    const staff_display_name = Utility.nullIfEmpty($('#txtDisplayName').val().trim());
    const staff_department_id = select2SelectedId('selDepartment');
    const staff_position = Utility.nullIfEmpty($('#txtPosition').val().trim());
    const staff_username = $('#txtUsername').val().trim();
    const staff_active = $('#chkActive').is(':checked') ? 1 : 0;
    const roleId = select2SelectedId('selRole');

    const data = {
      staff: {
        staff_fullname,
        staff_gender,
        staff_birth_year,
        staff_code,
        staff_email,
        staff_display_name,
        staff_department_id,
        staff_position,
        staff_username,
        staff_active,
      },
      roleId,
    };

    let url;

    if (staffId) {
      data.staffId = staffId;
      url = 'staff/update';
    } else {
      data.staff.staff_password = $('#txtPassword').val();
      url = 'staff/insert';
    }

    $.ajax({
      data: JSON.stringify(data),
      contentType: 'application/json',
      url,
      type: 'POST',
      dataType: 'json',
      success: function (res) {
        if (res.message === 'SESSION_END') {
          Toast.showError('Đã hết phiên làm việc, vui lòng đăng nhập lại để tiếp tục sử dụng');
          return;
        }

        if (res.status === 1) {
          Toast.showSuccess('Lưu nhân viên thành công.');
          tblStaff.ajax.reload();
        } else if (res.message === 'UNAME_DUP') {
          Toast.showError('Username đã tồn tại.');
        } else {
          Toast.showError('Lưu không thành công, vui lòng kiểm tra lại thông tin nhân viên.', '400px');
        }
      }
    });
  });

  $('body').on('click', '.btn-edit', e => {
    const data = tblStaff.row($(e.target).closest('tr')).data();

    setDataDetailDlg(data);
    $('#detailDlg').modal('show');
  });

  $('body').on('click', '.btn-delete', e => {
    const data = tblStaff.row($(e.target).closest('tr')).data();
    const { staff_id, staff_fullname, staff_username, staff_email } = data;

    confirmDeleteUser(staff_fullname).then(result => {
      if (result.isConfirmed) {
        $.ajax({
          data: JSON.stringify({
            staffId: staff_id,
            username: staff_username,
            email: staff_email,
          }),
          contentType: 'application/json',
          url: 'staff/delete',
          type: 'POST',
          dataType: 'json',
          success: function (res) {
            if (res.message === 'SESSION_END') {
              Toast.showError('Đã hết phiên làm việc, vui lòng đăng nhập lại để tiếp tục sử dụng');
              return;
            }

            if (res.status === 1) {
              Toast.showSuccess('Xóa nhân viên thành công.');
              tblStaff.ajax.reload();
            } else {
              Toast.showError('Lỗi, không xóa được nhân viên.');
            }
          }
        });
      }
    });
  });

  $('body').on('click', '.btn-reset-pass', e => {
    const data = tblStaff.row($(e.target).closest('tr')).data();
    const { staff_fullname, staff_username } = data;

    confirmResetPass(staff_fullname).then(result => {
      if (result.isConfirmed) {
        $.ajax({
          data: JSON.stringify({ username: staff_username }),
          contentType: 'application/json',
          url: 'staff/rp',
          type: 'POST',
          dataType: 'json',
          success: function (res) {
            if (res.message === 'SESSION_END') {
              Toast.showError('Đã hết phiên làm việc, vui lòng đăng nhập lại để tiếp tục sử dụng');
              return;
            }

            if (res.status === 1) {
              Toast.showSuccess('Thiết lập lại mật khẩu cho người dùng thành công. Một email với mật khẩu mới đã được gửi đến hộp thư của nhân viên.', 10000);
            } else {
              Toast.showError('Lỗi, không thiết lập lại mật khẩu cho người dùng được.');
            }
          }
        });
      }
    });
  });

  $('body').on('click', '.btn-toggle-active', e => {
    const data = tblStaff.row($(e.target).closest('tr')).data();
    const { staff_id } = data;

    $.ajax({
      data: JSON.stringify({ staffId: staff_id }),
      contentType: 'application/json',
      url: 'staff/change-status',
      type: 'POST',
      dataType: 'json',
      success: function (res) {
        if (res.message === 'SESSION_END') {
          Toast.showError('Đã hết phiên làm việc, vui lòng đăng nhập lại để tiếp tục sử dụng');
          return;
        }

        if (res.status === 1) {
          Toast.showSuccess('', 1500, '90px');
          tblStaff.ajax.reload();
        } else {
          Toast.showError('Lỗi, không đổi trạng thái nhân viên được.');
        }
      }
    });
  });
})();
