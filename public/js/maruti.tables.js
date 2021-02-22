
$(document).ready(function(){
	
	$('.data-table').dataTable({
		"oLanguage": {
			// "url":"//cdn.datatables.net/plug-ins/1.10.19/i18n/Vietnamese.json"
			"sProcessing":   "Đang xử lý...",
			"sLengthMenu":   "Xem _MENU_ mục",
			"sZeroRecords":  "Không tìm thấy dòng nào phù hợp",
			"sInfo":         "Đang xem _START_ đến _END_ trong tổng số _TOTAL_ mục",
			"sInfoEmpty":    "Đang xem 0 đến 0 trong tổng số 0 mục",
			"sInfoFiltered": "(được lọc từ _MAX_ mục)",
			"sInfoPostFix":  "",
			"sSearch":       "Tìm:",
			"sUrl":          "",
			"oPaginate": {
				"sFirst":    "Đầu",
				"sPrevious": "Trước",
				"sNext":     "Tiếp",
				"sLast":     "Cuối"
			}
		},
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"sDom": '<""l>t<"F"fp>'
		
	});
	
	$('input[type=checkbox],input[type=radio],input[type=file]').uniform();
	
	//$('select').select2();
	
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
