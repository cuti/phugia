(() => {
  let tblProduct;

  function confirmDeleteProduct(prdName) {
    return Swal.fire({
      cancelButtonText: 'Hủy',
      confirmButtonText: 'Xóa',
      customClass: {
        confirmButton: 'bg-danger text-white',
        icon: 'border-danger text-danger',
      },
      html: `Vui lòng xác nhận bạn muốn xóa vật tư<br><strong>${prdName}</strong>`,
      icon: 'warning',
      reverseButtons: true,
      showCancelButton: true,
      title: 'Xóa vật tư?',
      width: '500px',
    });
  }

  function clearDetailDlg() {
    $('#hidPrdId').val('');
    $('#txtMaVT').val('');
    $('#txtTenVT').val('');
    $('#txtDonViTinh').val('');
    $('#txtHangSX').val('');
  }

  function setDataDetailDlg(data) {
    const {
      product_id,
      product_code,
      product_name,
      product_unit_measure,
      product_manufacturer
    } = data;

    $('#hidPrdId').val(product_id);
    $('#txtMaVT').val(product_code);
    $('#txtTenVT').val(product_name);
    $('#txtDonViTinh').val(product_unit_measure);
    $('#txtHangSX').val(product_manufacturer);
  }

  tblProduct = $('#tblProduct').DataTable({
    ajax: {
      url: 'product/get-all',
    },
    columns: [
      {
        data: null,
        render: () => {
          const btnEdit = '<button type="button" class="btn-edit btn btn-outline-dark btn-sm" title="Điều chỉnh thông tin"><i class="fas fa-edit"></i></button>';
          const btnDelete = '<button type="button" class="btn-delete btn btn-danger btn-sm ml-2" title="Xóa"><i class="fas fa-trash"></i></button>';

          return btnEdit + btnDelete;
        },
        title: 'Thao tác',
        width: '80px',
      },
      {
        data: 'product_code',
        title: 'Mã vật tư',
        width: '120px',
      },
      {
        data: 'product_name',
        title: 'Tên vật tư',
        width: '300px',
      },
      {
        data: 'product_unit_measure',
        title: 'Đơn vị tính',
        width: '120px',
      },
      {
        data: 'product_manufacturer',
        title: 'Hãng sản xuất',
        width: '200px',
      },
      {
        data: 'product_created',
        render: data => Utility.phpDateToVnDate(data),
        title: 'Ngày tạo',
        type: 'date',
        width: '100px',
      },
      {
        data: 'product_created_by_username',
        title: 'Người tạo',
        width: '120px',
      },
      {
        data: 'product_last_updated',
        render: data => Utility.phpDateToVnDate(data),
        title: 'Ngày cập nhật sau cùng',
        type: 'date',
        width: '100px',
      },
      {
        data: 'product_last_updated_by_username',
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
        targets: [0],
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

  $('#btnThemVT').click(() => {
    clearDetailDlg();
    $('#detailDlg').modal('show');
  });

  $('#btnSaveProduct').click(() => {
    if ($('#txtMaVT').val().trim() === '') {
      Toast.showWarning('Chưa nhập mã vật tư.');
      return;
    }

    if ($('#txtTenVT').val().trim() === '') {
      Toast.showWarning('Chưa nhập tên vật tư.');
      return;
    }

    const product_id = $('#hidPrdId').val();
    const product_unit_measure = Utility.nullIfEmpty($('#txtDonViTinh').val().trim());
    const product_manufacturer = Utility.nullIfEmpty($('#txtHangSX').val().trim());

    const data = {
      product: {
        product_code: $('#txtMaVT').val().trim().toUpperCase(),
        product_name: $('#txtTenVT').val().trim(),
        product_unit_measure,
        product_manufacturer,
      },
    };

    let url;

    if (product_id !== '') {
      data.prdId = product_id;
      url = 'product/update';
    } else {
      url = 'product/insert';
    }

    $.ajax({
      data: JSON.stringify(data),
      contentType: 'application/json',
      url,
      type: 'POST',
      dataType: 'json',
      success: function (res) {
        if (res.status === 1) {
          Toast.showSuccess('Lưu vật tư thành công.');
          tblProduct.ajax.reload();
        } else if (res.message === 'PRD_CODE_DUP') {
          Toast.showError('Mã vật tư đã tồn tại.');
        } else {
          Toast.showError('Lưu không thành công, vui lòng kiểm tra lại thông tin vật tư.', '400px');
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

  $('#btnImportProduct').click(() => {
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
        url: 'product/import',
        type: 'POST',
        dataType: 'json',
        success: function (res) {
          let pClass;
          let text;

          if (res.status === 1) {
            pClass = 'text-success';
            text = '<p>Import thành công tất cả vật tư.</p>';
            tblProduct.ajax.reload();
          } else if (res.status === 2) {
            pClass = 'text-warning';
            text = `<p>Import thành công ${res.successCount} vật tư.</p>`;

            if (res.productCodeExists.length > 0) {
              text += '<p>Các mã vật tư đã tồn tại: ' + res.productCodeExists.join(', ') + '</p>';
            }

            if (res.productCodeError.length > 0) {
              text += '<p>Các mã vật tư không import được do lỗi khác: ' + res.productCodeError.join(', ') + '</p>';
            }

            tblProduct.ajax.reload();
          } else {
            pClass = 'text-danger';
            text = '<p>Không import được vật tư nào.</p>';

            if (res.productCodeExists.length > 0) {
              text += '<p>Các mã vật tư đã tồn tại: ' + res.productCodeExists.join(', ') + '</p>';
            }

            if (res.productCodeError.length > 0) {
              text += '<p>Các mã vật tư không import được do lỗi khác: ' + res.productCodeError.join(', ') + '</p>';
            }
          }

          $('#importDlg .modal-body').append(`<div id="importDlg-result" class="${pClass}"><hr>${text}</div>`);
        }
      });
    }

    reader.readAsDataURL(file);
  });

  $('body').on('click', '.btn-edit', e => {
    const data = tblProduct.row($(e.target).closest('tr')).data();

    setDataDetailDlg(data);
    $('#detailDlg').modal('show');
  });

  $('body').on('click', '.btn-delete', e => {
    const data = tblProduct.row($(e.target).closest('tr')).data();
    const { product_id, product_code, product_name } = data;

    confirmDeleteProduct(data.product_name).then(result => {
      if (result.isConfirmed) {
        $.ajax({
          data: JSON.stringify({
            prdId: product_id,
            prdCode: product_code,
            prdName: product_name,
          }),
          contentType: 'application/json',
          url: 'product/delete',
          type: 'POST',
          dataType: 'json',
          success: function (res) {
            if (res.status === 1) {
              Toast.showSuccess('Xóa vật tư thành công.');
              tblProduct.ajax.reload();
            } else {
              Toast.showError('Lỗi, không xóa được vật tư.');
            }
          }
        });
      }
    });
  });
})();
