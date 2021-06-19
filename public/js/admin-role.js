(() => {
  let tblRole;
  let treePermission;
  let adminNode;
  let operNode;
  let editingRoleId;

  function confirmDeleteRole(roleName) {
    return Swal.fire({
      cancelButtonText: 'Hủy',
      confirmButtonText: 'Xóa',
      customClass: {
        confirmButton: 'bg-danger text-white',
        icon: 'border-danger text-danger',
      },
      html: `Vui lòng xác nhận bạn muốn xóa nhóm người dùng<br><strong>${roleName}</strong>`,
      icon: 'warning',
      reverseButtons: true,
      showCancelButton: true,
      title: 'Xóa nhóm người dùng?',
      width: '500px',
    });
  }

  function clearDetailDlg() {
    editingRoleId = undefined;
    $('#txtRoleName').val('');
    $('#chkActive').prop('checked', true);

    treePermission.visit(n => {
      n.setExpanded(false, { noAnimation: true, noEvents: true });
      n.setSelected(false, { noEvents: true });
    });

    adminNode.setExpanded(true, { noAnimation: true, noEvents: true });
    operNode.setExpanded(true, { noAnimation: true, noEvents: true });
  }

  function setDataDetailDlg(data) {
    const {
      role_id,
      role_name,
      role_active
    } = data;

    editingRoleId = role_id;
    $('#txtRoleName').val(role_name);
    $('#chkActive').prop('checked', !!+role_active);

    treePermission.visit(n => {
      n.setExpanded(false, { noAnimation: true, noEvents: true });
      n.setSelected(false, { noEvents: true });
    });

    adminNode.setExpanded(true, { noAnimation: true, noEvents: true });
    operNode.setExpanded(true, { noAnimation: true, noEvents: true });

    $.ajax({
      url: 'role/menu-actions',
      data: { roleId: role_id },
      // dataType: 'json',
      success: function (res) {
        const d = res.data;

        for (let i = 0; i < d.length; i++) {
          const actionNode = treePermission.getNodeByKey(d[i].rm_menu_id + '_' + d[i].rm_action_id);

          if (actionNode) {
            actionNode.setSelected(true, { noEvents: true });
            actionNode.getParent().setExpanded(true, { noAnimation: true, noEvents: true });
            actionNode.getParent().getParent().setExpanded(true, { noAnimation: true, noEvents: true });
          }
        }

        adminNode.setExpanded(true, { noAnimation: true, noEvents: true });
        operNode.setExpanded(true, { noAnimation: true, noEvents: true });
      }
    });
  }

  function validateInput() {
    if ($('#txtRoleName').val().trim() === '') {
      Toast.showWarning('Chưa nhập tên nhóm.');
      return false;
    }

    return true;
  }

  function getSelectedPermissions() {
    const selectedNodes = treePermission.getSelectedNodes();
    const menuActionArr = $.map(selectedNodes, n => ({
      menu: n.parent.key,
      action: +n.key.split('_')[1],
    }));
    const parentMenuActionArr = selectedNodes.reduce((prev, cur) => {
      const menu = cur.parent.parent.key;

      if (!prev.find(p => p.menu === menu)) {
        prev.push({
          menu,
          action: 4
        });
      }

      return prev;
    }, []);

    return [...parentMenuActionArr, ...menuActionArr];
  }

  tblRole = $('#tblRole').DataTable({
    ajax: {
      url: 'role/get-all',
    },
    autoWidth: false,
    columns: [
      {
        className: 'text-center',
        data: null,
        render: () => {
          const btnEdit = `<button type="button" class="btn-edit btn btn-outline-secondary btn-sm" title="Điều chỉnh thông tin">
              <i class="fas fa-edit"></i>
            </button>`;
          const btnDelete = `<button type="button" class="btn-delete btn btn-outline-danger btn-sm ml-2" title="Xóa">
              <i class="fas fa-users-slash"></i>
            </button>`;

          return btnEdit + btnDelete;
        },
        title: 'Thao tác',
      },
      {
        data: 'role_active',
        render: data => {
          let fa_icon, tooltip, btnClass;

          if (+data) {
            fa_icon = 'check';
            tooltip = 'Khóa nhóm người dùng';
            btnClass = 'success';
          } else {
            fa_icon = 'lock';
            tooltip = 'Mở khóa nhóm người dùng';
            btnClass = 'danger';
          }

          const btnToggleActive = `<button type="button" class="btn-toggle-active btn btn-outline-${btnClass} btn-sm" title="${tooltip}">
              <i class="fas fa-${fa_icon}"></i>
            </button>`;

          return btnToggleActive;
        },
        title: 'Trạng thái',
      },
      {
        data: 'role_name',
        title: 'Tên nhóm',
      },
      {
        data: 'staff_count',
        title: 'Số lượng nhân viên',
      },
      {
        data: 'role_created',
        render: data => Utility.phpDateToVnDate(data),
        title: 'Ngày tạo',
        type: 'date',
      },
      {
        data: 'role_created_by_username',
        title: 'Người tạo',
      },
      {
        data: 'role_last_updated',
        render: data => Utility.phpDateToVnDate(data),
        title: 'Ngày cập nhật sau cùng',
        type: 'date',
      },
      {
        data: 'role_last_updated_by_username',
        title: 'Người cập nhật sau cùng',
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

  treePermission = $.ui.fancytree.createTree('#treePermission', {
    source: {
      url: 'menu/get-permission-tree'
    },
    checkbox: true,
    clickFolderMode: 3,
    selectMode: 3,
    init: (e, d) => {
      adminNode = d.tree.getNodeByKey('admin');
      operNode = d.tree.getNodeByKey('default');
    }
  });

  $('#detailDlg').on('shown.bs.modal', e => {
    $('#txtRoleName').focus();
  });

  $('#btnNewRole').click(() => {
    clearDetailDlg();
    $('#detailDlg').modal('show');
  });

  $('#formRoleDetail').submit(e => {
    e.preventDefault();

    if (!validateInput()) return;

    const data = {
      role: {
        role_name: $('#txtRoleName').val().trim(),
        role_active: $('#chkActive').is(':checked') ? 1 : 0,
      },
      role_menu: getSelectedPermissions(),
    };

    let url;

    if (editingRoleId) {
      data.role.role_id = editingRoleId;
      url = 'role/update';
    } else {
      url = 'role/insert';
    }

    $.ajax({
      data: JSON.stringify(data),
      contentType: 'application/json',
      url,
      type: 'POST',
      dataType: 'json',
      success: function (res) {
        if (res.status === 1) {
          Toast.showSuccess('Lưu nhóm người dùng thành công.');
          tblRole.ajax.reload();
          setTimeout(() => {
            $('#detailDlg').modal('hide');
          }, 1000);
        } else if (res.message === 'NAME_DUP') {
          Toast.showError('Tên nhóm đã tồn tại.');
        } else {
          Toast.showError('Lưu không thành công, vui lòng kiểm tra lại thông tin nhóm người dùng.', '400px');
        }
      }
    });
  });

  $('body').on('click', '.btn-edit', e => {
    const data = tblRole.row($(e.target).closest('tr')).data();

    setDataDetailDlg(data);
    $('#detailDlg').modal('show');
  });

  $('body').on('click', '.btn-delete', e => {
    const data = tblRole.row($(e.target).closest('tr')).data();
    const { role_id, role_name } = data;

    confirmDeleteRole(role_name).then(result => {
      if (result.isConfirmed) {
        $.ajax({
          data: JSON.stringify({
            role: {
              role_id,
              role_name,
            }
          }),
          contentType: 'application/json',
          url: 'role/delete',
          type: 'POST',
          dataType: 'json',
          success: function (res) {
            if (res.status === 1) {
              Toast.showSuccess('Xóa nhóm người dùng thành công.');
              tblRole.ajax.reload();
            } else {
              Toast.showError('Lỗi, không xóa được nhóm người dùng.');
            }
          }
        });
      }
    });
  });

  $('body').on('click', '.btn-toggle-active', e => {
    const data = tblRole.row($(e.target).closest('tr')).data();
    const { role_id, role_active } = data;

    $.ajax({
      data: JSON.stringify({
        role: {
          role_id,
          role_active: !+role_active,
        }
      }),
      contentType: 'application/json',
      url: 'role/change-status',
      type: 'POST',
      dataType: 'json',
      success: function (res) {
        if (res.status === 1) {
          Toast.showSuccess('', 1500, '90px');
          tblRole.ajax.reload();
        } else {
          Toast.showError('Lỗi, không đổi trạng thái nhóm người dùng được.');
        }
      }
    });
  });
})();
