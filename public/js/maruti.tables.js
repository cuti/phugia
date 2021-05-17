
$(document).ready(function(){

	// $('.data-table').dataTable({
	// 	bJQueryUI: true,
	// 	bScrollCollapse: true,
	// 	oLanguage: {
	// 		oPaginate: {
	// 			sFirst:    "Đầu",
	// 			sPrevious: "Trước",
	// 			sNext:     "Tiếp",
	// 			sLast:     "Cuối"
	// 		},
	// 		sInfo:         "Đang xem _START_ đến _END_ trong tổng số _TOTAL_ mục",
	// 		sInfoEmpty:    "Đang xem 0 đến 0 trong tổng số 0 mục",
	// 		sInfoFiltered: "(được lọc từ _MAX_ mục)",
	// 		sInfoPostFix:  "",
	// 		sLengthMenu:   "Xem _MENU_ mục",
	// 		sProcessing:   "Đang xử lý...",
	// 		sSearch:       "Tìm:",
	// 		// sUrl:          "https://cdn.datatables.net/plug-ins/1.10.19/i18n/Vietnamese.json",
	// 		sZeroRecords:  "Không tìm thấy dòng nào phù hợp",
	// 	},
	// 	"sDom": '<""l>t<"F"fp>',
	// 	"sPaginationType": "full_numbers",
	// 	"sScrollX": "100%",
	// });

	$('input[type=checkbox],input[type=radio],input[type=file]').uniform();

	// $('select').select2();

	$("span.icon input:checkbox, th input:checkbox").click(function() {
		var checkedStatus = this.checked;
		var checkbox = $(this).parents('.widget-box').find('tr td:first-child input:checkbox');
		checkbox.each(function() {
			this.checked = checkedStatus;
			if (checkedStatus == this.checked) {
				$(this).closest('.checker > span').removeClass('checked');
			}
			if (this.checked) {
				$(this).closest('.checker > span').addClass('checked');
			}
		});
	});
});
