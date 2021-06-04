(() => {
  let oldFullName, oldDisplayName;

  $('#changePassModal').on('shown.bs.modal', e => {
    $('#txtOldPass').focus();
  });

  $('#formChangePass').submit(e => {
    e.preventDefault();

    if ($('#txtOldPass').val() === '') {
      Toast.showWarning('Chưa nhập mật khẩu cũ', 3000);
      return;
    }

    if ($('#txtNewPass').val() === '') {
      Toast.showWarning('Chưa nhập mật khẩu mới', 3000);
      return;
    }

    if ($('#txtConfirmNewPass').val() === '') {
      Toast.showWarning('Chưa nhập lại mật khẩu mới', 3000);
      return;
    }

    if ($('#txtConfirmNewPass').val() !== $('#txtNewPass').val()) {
      Toast.showWarning('Nhập lại mật khẩu mới không khớp', 3000);
      return;
    }

    $.ajax({
      type: 'POST',
      url: 'user-info/cp',
      data: {
        oldpass: $('#txtOldPass').val(),
        newpass: $('#txtNewPass').val(),
      },
      dataType: 'json',
      success: function (res) {
        if (res.status === 0) {
          Toast.showError(res.message);
        } else {
          Toast.showSuccess('Đổi mật khẩu thành công');
          $('#changePassModal').modal('hide');
          $('#txtOldPass').val('');
          $('#txtNewPass').val('');
          $('#txtConfirmNewPass').val('');
        }
      }
    });
  });

  $('#btnSua').click(() => {
    oldFullName = $('#txtFullName').val();
    oldDisplayName = $('#txtDisplayName').val();

    $('#txtFullName').attr('readonly', false).focus();
    $('#txtDisplayName').attr('readonly', false);

    $('#btnSua').addClass('d-none');
    $('#btnCancel').removeClass('d-none');
    $('#btnSave').removeClass('d-none');
  });

  $('#btnCancel').click(() => {
    $('#txtFullName').val(oldFullName);
    $('#txtDisplayName').val(oldDisplayName);

    $('#txtFullName').attr('readonly', true);
    $('#txtDisplayName').attr('readonly', true);

    $('#btnSua').removeClass('d-none');
    $('#btnCancel').addClass('d-none');
    $('#btnSave').addClass('d-none');
  });

  $('#btnSave').click(() => {
    const fullName = Utility.nullIfEmpty($('#txtFullName').val().trim());
    const displayName = Utility.nullIfEmpty($('#txtDisplayName').val().trim());

    $.ajax({
      type: 'POST',
      url: 'user-info/ci',
      data: {
        fullName,
        displayName
      },
      dataType: 'json',
      success: function (res) {
        if (res.status === 0) {
          Toast.showError(res.message);
        } else {
          Toast.showSuccess('Cập nhật thông tin người dùng thành công', 3000);

          $('#txtFullName').attr('readonly', true);
          $('#txtDisplayName').attr('readonly', true);

          $('#btnSua').removeClass('d-none');
          $('#btnCancel').addClass('d-none');
          $('#btnSave').addClass('d-none');

          if (displayName) {
            $('#userDropdown>span').text(displayName);
          } else if (fullName) {
            $('#userDropdown>span').text(fullName);
          } else {
            $('#userDropdown>span').text($('#txtUsername').val());
          }
        }
      }
    });
  });
})();
