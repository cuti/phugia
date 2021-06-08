(() => {
  function showError(message) {
    $('#lblNote').text(message).removeClass('invisible');

    setTimeout(() => {
      $('#lblNote').text('&nbsp;').addClass('invisible');
    }, 5000);
  }

  $('input[name=username]').focus();

  if ($('#lblNote').text()) {
    $('#lblNote').removeClass('invisible');

    setTimeout(() => {
      $('#lblNote').text('&nbsp;').addClass('invisible');
    }, 5000);
  }

  $('form').submit(e => {
    e.preventDefault();

    const username = $('input[name=username]').val().trim();

    if (username === '') {
      showError('Chưa nhập tài khoản.');
      return;
    }

    $.ajax({
      type: 'POST',
      url: 'forgot-password/rp',
      data: { username },
      dataType: 'json',
      success: function (res) {
        if (res.status === 1) {
          Toast.showSuccess('Một email với mật khẩu mới đã được gửi đến hộp thư của bạn.', 5000);
          setTimeout(() => {
            location = location.href.replace(/forgot-password/, 'login');
          }, 5000);
        } else {
          showError('Không thiết lập lại mật khẩu được, vui lòng thử lại sau hoặc liên hệ quản trị hệ thống.');
        }
      }
    });
  });
})();