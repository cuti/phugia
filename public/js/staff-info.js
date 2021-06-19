(() => {
  let oldFullName, oldDisplayName, oldEmail;

  function enableEditing() {
    $('#txtFullName').attr('readonly', false);
    $('#txtDisplayName').attr('readonly', false);
    $('#txtEmail').attr('readonly', false).focus();

    $('#btnSua').addClass('d-none');
    $('#btnCancel').removeClass('d-none');
    $('#btnSave').removeClass('d-none');
  }

  function disableEditing() {
    $('#txtFullName').attr('readonly', true);
    $('#txtDisplayName').attr('readonly', true);
    $('#txtEmail').attr('readonly', true);

    $('#btnSua').removeClass('d-none');
    $('#btnCancel').addClass('d-none');
    $('#btnSave').addClass('d-none');
  }

  function validateInput() {
    if ($('#txtOldPass').val() === '') {
      Toast.showWarning('Chưa nhập mật khẩu cũ', 3000);
      $('#txtOldPass').focus();
      return false;
    }

    if ($('#txtNewPass').val() === '') {
      Toast.showWarning('Chưa nhập mật khẩu mới', 3000);
      $('#txtNewPass').focus();
      return false;
    }

    if ($('#txtConfirmNewPass').val() === '') {
      Toast.showWarning('Chưa nhập lại mật khẩu mới', 3000);
      $('#txtConfirmNewPass').focus();
      return false;
    }

    if ($('#txtConfirmNewPass').val() !== $('#txtNewPass').val()) {
      Toast.showWarning('Nhập lại mật khẩu mới không khớp', 3000);
      $('#txtConfirmNewPass').focus();
      return false;
    }

    return true;
  }

  $('#changePassModal').on('shown.bs.modal', e => {
    $('#txtOldPass').focus();
  });

  $('#formChangePass').submit(e => {
    e.preventDefault();

    if (!validateInput()) return;

    $.ajax({
      type: 'POST',
      url: 'staff-info/cp',
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
    oldEmail = $('#txtEmail').val();
    enableEditing();
  });

  $('#btnCancel').click(() => {
    $('#txtFullName').val(oldFullName);
    $('#txtDisplayName').val(oldDisplayName);
    $('#txtEmail').val(oldEmail);
    disableEditing();
  });

  $('#btnSave').click(() => {
    if ($('#txtEmail').val().trim() === '') {
      Toast.showWarning('Chưa nhập địa chỉ email.');
      $('#txtEmail').focus();
      return;
    }

    const fullName = Utility.nullIfEmpty($('#txtFullName').val().trim());
    const displayName = Utility.nullIfEmpty($('#txtDisplayName').val().trim());
    const email = Utility.nullIfEmpty($('#txtEmail').val().trim());

    $.ajax({
      type: 'POST',
      url: 'staff-info/ci',
      data: {
        fullName,
        displayName,
        email,
      },
      dataType: 'json',
      success: function (res) {
        if (res.status === 0) {
          Toast.showError(res.message);
        } else {
          Toast.showSuccess('Cập nhật thông tin nhân viên thành công', 3000);
          disableEditing();

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
