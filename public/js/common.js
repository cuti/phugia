/**
 * Dependencies:
 *    - Sweet alert 2
 *    - Bootstrap 4
 */
const Toast = {
  /**
   * Show success notification.
   * @param {string} text
   * @param {number} duration Default: 5000 (5s)
   * @param {string} width px and % are supported. Default: 300px
   */
  showSuccess: function (text, duration = 5000, width = '300px') {
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
  },
  /**
   * Show warning notification.
   * @param {string} text
   * @param {number} duration Default: 5000 (5s)
   * @param {string} width px and % are supported. Default: 300px
   */
  showWarning: function (text, duration = 5000, width = '300px') {
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
  },
  /**
   * Show error notification.
   * @param {string} text
   * @param {string} width px and % are supported. Default: 300px
   */
  showError: function (text, width = '300px') {
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
  },
};

const Utility = {
  nullIfEmpty: function (params) {
    if (params === '') {
      return null;
    }
    return params;
  },
};
