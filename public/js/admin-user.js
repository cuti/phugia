(() => {
  let tblUser;
  let editingUserId;

  function select2SelectedId(id) {
    const data = $(`#${id}`).select2('data');

    if (data.length === 0) {
      return null;
    }

    return data[0].id;
  }

  function confirmDeleteUser(username) {
    return Swal.fire({
      cancelButtonText: 'Hủy',
      confirmButtonText: 'Xóa',
      customClass: {
        confirmButton: 'bg-danger text-white',
        icon: 'border-danger text-danger',
      },
      html: `Vui lòng xác nhận bạn muốn xóa người dùng<br><strong>${username}</strong>`,
      icon: 'warning',
      reverseButtons: true,
      showCancelButton: true,
      title: 'Xóa người dùng?',
      width: '500px',
    });
  }

  function confirmResetPass(username) {
    return Swal.fire({
      cancelButtonText: 'Hủy',
      confirmButtonText: 'Đồng ý',
      customClass: {
        confirmButton: 'bg-danger text-white',
        icon: 'border-danger text-danger',
      },
      html: `Vui lòng xác nhận bạn muốn thiết lập lại mật khẩu cho người dùng<br><strong>${username}</strong>`,
      icon: 'warning',
      reverseButtons: true,
      showCancelButton: true,
      title: 'Thiết lập lại mật khẩu?',
      width: '560px',
    });
  }

  function clearDetailDlg() {
    editingUserId = undefined;
    $('#txtUsername').val('');
    $('#txtFullname').val('');
    $('#txtDisplayName').val('');
    $('#txtPassword').val('').prop('readonly', false);
    $('#selDepartment').val(null).trigger('change');
    $('#txtEmail').val('');
    $('#chkActive').prop('checked', true);
  }

  function setDataDetailDlg(data) {
    const {
      user_id,
      user_fullname,
      user_display_name,
      user_username,
      user_department_id,
      user_email,
      user_active,
      role_id
    } = data;

    editingUserId = user_id;
    $('#txtUsername').val(user_username);
    $('#txtFullname').val(user_fullname);
    $('#txtDisplayName').val(user_display_name);
    $('#txtPassword').val('(Đã mã hóa)').prop('readonly', true);
    $('#selDepartment').val(user_department_id).trigger('change');
    $('#selRole').val(role_id).trigger('change');
    $('#txtEmail').val(user_email);
    $('#chkActive').prop('checked', !!+user_active);
  }

  function validateInput() {
    if ($('#txtUsername').val().trim() === '') {
      Toast.showWarning('Chưa nhập username.');
      return false;
    }

    if ($('#txtPassword').val() === '') {
      Toast.showWarning('Chưa nhập mật khẩu.');
      return false;
    }

    if ($('#txtEmail').val().trim() === '') {
      Toast.showWarning('Chưa nhập địa chỉ email.');
      return false;
    }

    return true;
  }

  tblUser = $('#tblUser').DataTable({
    ajax: {
      url: 'user/get-all',
    },
    columns: [
      {
        className: 'text-center',
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
        title: 'Thao tác',
        width: '110px',
      },
      {
        data: 'user_active',
        render: data => {
          let fa_icon, tooltip, btnClass;

          if (+data) {
            fa_icon = 'user-check';
            tooltip = 'Khóa người dùng';
            btnClass = 'success';
          } else {
            fa_icon = 'user-lock';
            tooltip = 'Mở khóa người dùng';
            btnClass = 'danger';
          }

          const btnToggleActive = `<button type="button" class="btn-toggle-active btn btn-outline-${btnClass} btn-sm" title="${tooltip}">
              <i class="fas fa-${fa_icon}"></i>
            </button>`;

          return btnToggleActive;
        },
        title: 'Trạng thái',
        width: '40px',
      },
      {
        data: 'user_username',
        title: 'Username',
        width: '120px',
      },
      {
        data: 'role_name',
        title: 'Nhóm người dùng',
        width: '180px',
      },
      {
        data: 'user_email',
        title: 'Email',
        width: '180px',
      },
      {
        data: 'user_fullname',
        title: 'Họ và tên',
        width: '170px',
      },
      {
        data: 'user_display_name',
        title: 'Tên hiển thị',
        width: '150px',
      },
      {
        data: 'user_department',
        title: 'Phòng',
        width: '170px',
      },
      {
        data: 'user_created',
        render: data => Utility.phpDateToVnDate(data),
        title: 'Ngày tạo',
        type: 'date',
        width: '100px',
      },
      {
        data: 'user_created_by_username',
        title: 'Người tạo',
        width: '120px',
      },
      {
        data: 'user_last_updated',
        render: data => Utility.phpDateToVnDate(data),
        title: 'Ngày cập nhật sau cùng',
        type: 'date',
        width: '100px',
      },
      {
        data: 'user_last_updated_by_username',
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
    $('#txtUsername').focus();
  });

  $('#btnNewUser').click(() => {
    clearDetailDlg();
    $('#detailDlg').modal('show');
  });

  $('#formUserDetail').submit(e => {
    e.preventDefault();

    if (!validateInput()) return;

    const user_id = editingUserId;
    const user_fullname = Utility.nullIfEmpty($('#txtFullname').val().trim());
    const user_display_name = Utility.nullIfEmpty($('#txtDisplayName').val().trim());
    const user_department_id = select2SelectedId('selDepartment');
    const role_id = select2SelectedId('selRole');

    const data = {
      user: {
        user_fullname,
        user_display_name,
        user_username: $('#txtUsername').val().trim(),
        user_department_id,
        user_email: $('#txtEmail').val().trim(),
        user_active: $('#chkActive').is(':checked') ? 1 : 0,
      },
      roleId: role_id,
    };

    let url;

    if (user_id) {
      data.userId = user_id;
      url = 'user/update';
    } else {
      data.user.user_password = $('#txtPassword').val();
      url = 'user/insert';
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
          Toast.showSuccess('Lưu người dùng thành công.');
          tblUser.ajax.reload();
        } else if (res.message === 'UNAME_DUP') {
          Toast.showError('Username đã tồn tại.');
        } else {
          Toast.showError('Lưu không thành công, vui lòng kiểm tra lại thông tin người dùng.', '400px');
        }
      }
    });
  });

  $('body').on('click', '.btn-edit', e => {
    const data = tblUser.row($(e.target).closest('tr')).data();

    setDataDetailDlg(data);
    $('#detailDlg').modal('show');
  });

  $('body').on('click', '.btn-delete', e => {
    const data = tblUser.row($(e.target).closest('tr')).data();
    const { user_id, user_username, user_email } = data;

    confirmDeleteUser(user_username).then(result => {
      if (result.isConfirmed) {
        $.ajax({
          data: JSON.stringify({
            usrId: user_id,
            uname: user_username,
            email: user_email,
          }),
          contentType: 'application/json',
          url: 'user/delete',
          type: 'POST',
          dataType: 'json',
          success: function (res) {
            if (res.message === 'SESSION_END') {
              Toast.showError('Đã hết phiên làm việc, vui lòng đăng nhập lại để tiếp tục sử dụng');
              return;
            }

            if (res.status === 1) {
              Toast.showSuccess('Xóa người dùng thành công.');
              tblUser.ajax.reload();
            } else {
              Toast.showError('Lỗi, không xóa được người dùng.');
            }
          }
        });
      }
    });
  });

  $('body').on('click', '.btn-reset-pass', e => {
    const data = tblUser.row($(e.target).closest('tr')).data();
    const { user_username } = data;

    confirmResetPass(user_username).then(result => {
      if (result.isConfirmed) {
        $.ajax({
          data: JSON.stringify({ username: user_username }),
          contentType: 'application/json',
          url: 'user/rp',
          type: 'POST',
          dataType: 'json',
          success: function (res) {
            if (res.message === 'SESSION_END') {
              Toast.showError('Đã hết phiên làm việc, vui lòng đăng nhập lại để tiếp tục sử dụng');
              return;
            }

            if (res.status === 1) {
              Toast.showSuccess('Thiết lập lại mật khẩu cho người dùng thành công. Một email với mật khẩu mới đã được gửi đến hộp thư của người dùng.', 10000);
            } else {
              Toast.showError('Lỗi, không thiết lập lại mật khẩu cho người dùng được.');
            }
          }
        });
      }
    });
  });

  $('body').on('click', '.btn-toggle-active', e => {
    const data = tblUser.row($(e.target).closest('tr')).data();
    const { user_id } = data;

    $.ajax({
      data: JSON.stringify({ usrId: user_id }),
      contentType: 'application/json',
      url: 'user/change-status',
      type: 'POST',
      dataType: 'json',
      success: function (res) {
        if (res.message === 'SESSION_END') {
          Toast.showError('Đã hết phiên làm việc, vui lòng đăng nhập lại để tiếp tục sử dụng');
          return;
        }

        if (res.status === 1) {
          Toast.showSuccess('', 1500, '90px');
          tblUser.ajax.reload();
        } else {
          Toast.showError('Lỗi, không đổi trạng thái người dùng được.');
        }
      }
    });
  });
})();
