$.ajaxSetup({	
    beforeSend: function(jqXHR, settings){
		pageData.preloaderShow();
    },
    complete: function() {
		pageData.preloaderHide();	
	},
});


// function modifyData() {

// }



$(document).ready(function() {
	$.ajax({
		type: 'POST',
		url: 'ajax_route.php',
		data: {
			url: 'core/action/update/check_new_version.php',
			route: 'checkNewVersion',
		},
		dataType: 'json',
		async: true,
		beforeSend: function() {
		},
		complete: function() {
		},		
		success: (data) => {
			console.log('dsds');
			$('.notify-list').prepend(data.view);

			if(data.view) {
				$('.notify-empty').hide();
				$('.widget__mark-right-notify').removeClass('hide').addClass('show');
			}

			$(document).find('.top-nav-list').prepend(data.nav);
		}
	});
});



$(document).ready(function() {
	$.ajax({
		type: 'POST',
		url: 'ajax_route.php',
		data: {
			route: 'check_date',
			url: '/core/main/check_date.php',
		},
		async: true,
		beforeSend: function( ){
		},
		complete: function() {
		},		
		success: (data) => {
			$('.app-container').html(data.content);
		}
	});
});





$(document).ready(function() {
	$.ajax({
		type: 'POST',
		url: 'ajax_route.php',
		data: {
			route: 'createBackup',
			url: 'core/action/backup/make-backup.php',
		},
		async: true,
		beforeSend: function( ){
		},
		complete: function() {
		},		
		success: () => {
		}
	});
});



/**
 * download update
 */
$(document).on('click', '.download-update', function(){
	$.ajax({
		type: 'POST',
		url: 'ajax_route.php',
		data: {
			route: 'downloadUpdate',
			url: '/core/action/update/download_update.php',
			get_modal: true
		},
		dataType: 'json',
		success: (data) => {
			if(data.success) {
				pageData.overlayShow();
				$('body').append(data.success);
			}
			if(data.error) {
				$('.update-modal').html(data.error);
			}
		}
	});
});



/** 
 * LOGIN  
 */
	$(document).on('submit', '.login-form', function(e){
		e.preventDefault();
		var user_login = $('.user-login').val();
		var user_password = $('.user-password').val();
		if(validate_all_input($('.input'))) {
			$.ajax({
				type: 'POST',
				url: 'core/auth/auth.php',
				dataType: 'json',
				data: {
					login: user_login,
					password: user_password
				},
				success: (data) => {
					if(data.success) {
						window.location.reload();
					} 
					
					if(data.error) {
						pageData.alert_notice('error', data.error);
					}
				}
			});
		}

	});




/** 
 * MENU START
 */ 
$('body').on('click', '.get_page_action', function(){
    //смотри в option.php
    //получаем ссылку на страницу
    var $result = $('.main');
    var tab = $(this).data('tab');
    var data_page = $(this).data('page-route');

	

    $.ajax({
        type: 'POST',
        url:  'page/route.php',
        data: {
            tab: tab,
            data_page_route: data_page
        },
        success: (data) => {
            //проверка данных на json
            if(data.error) {
                notice_modal('text', 'error');
            } else {
                $result.html(data);
            }

            ui_selected_sidebar(tab);
            ui_selected_tab();
            visible_menu('hide');

			const content_title = $('.content__title').text();

			$('title').html(`Lime Store > ${content_title}`);
        }			
    });
});


//tab
$('body').on('click', '.tab-button', function(){
    $tab_active = 'tab_activ';
    var $result = $('.main')
    var tab = $(this).data('tab');
    var data_page = $(this).data('page');

	$.ajax({
		url:  'page/route.php',
		type: 'POST',
		data: {				
			page: data_page,
			tab: tab,
		},
		success: (data) => {
            $('.tab-button').removeClass($tab_active);
			$('.content').remove();
			$(this).addClass($tab_active);
            ui_selected_tab();
            //проверка данных на json
            if(data.error) {
                notice_modal('text', 'error');
            } else {
                $result.append(data);
            }
		}
	});

	$(this).blur();
	 			
});

//endtab


/** MENU END */

/** send filter / search / autocomplete start */

//фильтрация товара
function send_filter(filter_list) {
	//переключаем состяние на активный
	$.ajax({
		type: 'POST',
		url:  'ajax_route.php',
		data: {
			url: 'core/action/stock/get_filter_stock.php',
			route: 'filtredProducts', 
			data: {
				id: filter_list,
				page: pageData.page(),
				type: pageData.type()
			}
		},
        dataType: 'JSON',
		success: (data) => { 

			//выводим в талицу данные
			if(data.table) {
				pageData.innerTable(data.table);	
			}
			if(data.total) {
				pageData.innerTableFooter(data.total);
            }
		}				
	});
}

function send_autocomplete($this) {
    var $delay = 450;
	var min_value_length = 1;   

	var $search_container = $this.closest('.search-container');
	var $append_to = $this.closest($search_container).find('.search-list-content');
    var $preloader = $this.closest($search_container).find('.preloader');
	var search_data = $this.val().trim();
	var data_name = $this.attr('data-name');
	var autocmplt_type = $append_to.attr('data-auto-cmplt-type');
	var option_class_list = $append_to.attr('data-option-class-list');
	var $table = $('.table-list');

	if(search_data.length > min_value_length) {
		$preloader.addClass('flex-cntr').removeClass('hide');
		clearTimeout($this.data('timer'));
		$this.data('timer', setTimeout(function(){
			$.ajax({
				type: 'POST',
				url: 'ajax_route.php',
				data: {
					url: '/core/action/search/autocomplete.php',
					route: 'autocomplete',
					data: {
						value: search_data,
						action: data_name,
						page: pageData.page(),
						type: pageData.type(),
						autocmplt_type: autocmplt_type,
						option_class_list: option_class_list,
					}
				},
				beforeSend: () => {
				},
				complete: () => {
					$preloader.removeClass('flex-cntr').addClass('hide');
				},
				success: (data) => {
					if(data.length <= 0) {
						$append_to.html('Heç nə tapılmadı');
					} else {
						$append_to.html(data);
					}
				}
			});

		}, $delay));					
	} else {
		$append_to.html('no result');
	}	
}

$(document).on('click', '.search-item', function() {
    reset_all_filter();
	//делаем поиск по значению 
	// var search_item_value = $(this).find('.stock-info-text').text();
	var search_item_value = $(this).data('sort-value');

	//for report sort data 
    var sort_data = $(this).data('sort');
    
    var $search_main_table = $('.stock_list_tbody');
	//тут мы получаем тип таблицы (terminal, stock, report и тд)

	$.ajax({
		type: 'POST',
		url: 'ajax_route.php',
		data: {
			route: 'search',
			url: 'core/action/search/search.php',
			data: {
				search_item_value	: search_item_value, 
				page				: pageData.page(), 
				type			    : pageData.type(),
				sort_data 			: sort_data
			}
		},
		dataType: 'json',
		success: (data) => {
			//выводим в талицу данные
			if(data.table) {
				pageData.innerTable(data.table);	
			}
			if(data.total) {
				pageData.innerTableFooter(data.total);
			}

		}			
	}); 
});

/** end send filter */

/** order start*/
$('body').on('click', '.info-stock', function(){
	$modal = $('.modal_view_stock_order');


	pageData.preloaderShow();
	pageData.overlayShow();
	$('.get_order_action').removeClass('click');

	//получаем id проддукта от родительсокого эелемента
	var product_id = $(this).closest('.stock-list').attr("id");		
	//report_order_id
	var order_id = $(this).find('.get_report_order_id').attr('data-sort-value');


	$.ajax({
		type: 'POST',
		url: 'ajax_route.php',
		cache: false,
		data:{
			url: 'core/action/modal/modal.php',
			route: 'modal',
			data: {
				product_id : product_id,
				order_id: order_id, 
				type  : pageData.type(), 
				page  : pageData.page()
			}
		},
		success: (data) => {
			pageData.rightSideModal(data);
		}			

	});
});
/** end order */












// изменить категорию
$('body').on('click', '.submit-save-category', function() {
	let prepare_data = {};

	const category_id = $('.category-id').data('id');

	prepare_data['category_id'] = category_id;

	$('.edit').each(function(){
		if($(this).data('fields-name') && $(this).hasClass('edited')) {
			var data_name = $(this).data('fields-name');
			var val = $(this).val();
			prepare_data[data_name] = val;
		}
	});

	$.ajax({
		type: 'POST',
		url: 'ajax_route.php',
		data: {
			prepare_data,
			url: 'core/action/category/edit-category.php',
			route: 'editCategory',
		},
		dataType: "json",
		success: (data) => {
			pageData.alert_notice(data.type, data.text)
			
			if(data.type == 'success') {	
				for (key in prepare_data) {
					pageData.update_table_row(key, prepare_data[key], category_id);
				}
			}
		}			

	});

});

/** удалить категория start */
$(document).on('click', '.delete-category', function() {
	const id = $(this).data('delete-id');

	$.ajax({
		type: 'POST',
		url: 'ajax_route.php',
		data: {
			url: 'core/action/category/delete-category.php',
			route: 'deleteCategory',
			id: id
		},
		dataType: 'json',
		success: (data) => {
			pageData.alert_notice('success', data.success);
			
			if(data.type == 'success') {
				pageData.rightSideModalHide();
				pageData.overlayHide();

				var $stock = $(`#${id}.stock-list`); 

				$stock.hide(1000, function() {
					$stock.remove();
				});
			}
		}
	});
});
/** удалить категория end */







// изменить категорию
$('body').on('click', '.submit-save-provider', function() {
	let prepare_data = {};

	const provider_id = $('.provider-id').data('id');

	prepare_data['provider_id'] = provider_id;

	$('.edit').each(function(){
		if($(this).data('fields-name') && $(this).hasClass('edited')) {
			var data_name = $(this).data('fields-name');
			var val = $(this).val();
			prepare_data[data_name] = val;
		}
	});

	$.ajax({
		type: 'POST',
		url: 'ajax_route.php',
		data: {
			url: '/core/action/provider/edit-provider.php',
			route: 'editProvider',
			prepare_data,
		},
		dataType: "json",
		success: (data) => {
			pageData.alert_notice(data.type, data.text);

			if(data.type == 'success') {				
				for (key in prepare_data) {
					pageData.update_table_row(key, prepare_data[key], provider_id);
				}
			}
		}			

	});
});


/** удалить поставщик start */
$(document).on('click', '.delete-provider', function() {
	const id = $(this).data('delete-id');

	$.ajax({
		type: 'POST',
		url: 'ajax_route.php',
		data: {
			url: 'core/action/provider/delete-provider.php',
			route: 'deleteProvider',
			id: id,
		},
		dataType: 'json',
		success: (data) => {
			pageData.alert_notice(data.type, data.text);
			if(data.type == 'success') {
				pageData.rightSideModalHide();
				pageData.overlayHide();

				var $stock = $(`#${id}.stock-list`); 

				$stock.hide(1000, function() {
					$stock.remove();
				});
			}

		}
	});
});
/** удалить поставщик end */


/**
 * добавить расходы
 */
$(document).on('click', '.add-submit-rasxod', function() {
	if(is_required_input($(this).closest('.stock-from-container').find('.form-input'))) {
		prepare_data = prepare_form_fields($(this).closest('.stock-from-container'));
		$.ajax({
			type: 'POST',
			url: 'ajax_route.php',
			data: {
				url: 'core/action/expense/add_expense.php',
				route: 'addExpense',
				post_data: prepare_data,
				page: pageData.page(),
				type: pageData.type()
			},
			dataType: "json",
			success: (data) => {
				pageData.alert_notice(data.type, data.text);

				if(data.type == 'success') {
					$('.form-input').val('');

					if(data.table) {
						return pageData.prependTable(data.table);
					}
				}
			}	
		});
	}
});



// добавить поставщика
$('body').on('click', '.add-submit-provider', function() {
	let prepare_data = {};

	if($(this).closest('.modal-form').length) {
		if(is_required_input($(this).closest('.modal-form').find('.form-input'))) {
			prepare_data = prepare_form_fields($(this).closest('.modal-form'));
			$.ajax({
				type: 'POST',
				url: 'core/action/provider/modal_add_provider.php',
				data: {
					post_data: prepare_data,
				},
				dataType: "json",
				success: (data) => {
					if(data.success) {
						$('.opened-custom-modal').find('.provider-input-value').val(data.provider_name)
						$('.opened-custom-modal').find('.hidden-provider-id').val(data.provider_id);

						// $('.provider-input-value').val(data.provider_name);
						// $('.hidden-provider-id').val(data.provider_id);

						pageData.rightSideModalHide();
						pageData.overlayHide();
					}
				}			
	
			});
		}
	}

	if($(this).closest('.stock-from-container').length) {
		if(is_required_input($(this).closest('.stock-from-container').find('.form-input'))) {
			prepare_data = prepare_form_fields($(this).closest('.stock-from-container'));
			$.ajax({
				type: 'POST',
				url: 'ajax_route.php',
				data: {
					route: 'addProvider',
					url: '/core/action/provider/add-provider.php',
					post_data: prepare_data,
					page: pageData.page(),
					type: pageData.type()
				},
				dataType: "json",
				success: (data) => {
					var error 	= data['error'];
					var success = data['success'];
					
					if(error) {
						pageData.alert_notice('error', error);
					}
	
					if(success) {
						pageData.alert_notice('success', 'Ок');
						$('.form-input').val('');
	
						if(data.table) {
							return pageData.prependTable(data.table);
						}
					}
				}			
	
			});
		}
	}
});


/**
 * изменить данные расхода
 */
 $('body').on('click', '.submit-save-rasxod', function() {
	let prepare_data = {};

	if(is_required_input($(this).closest('.modal_order_form').find('.input-required'))) {
		const rasxod_id = $('.rasxod-id').data('id');

		prepare_data['rasxod_id'] = rasxod_id;
	
		$('.edit').each(function(){
			if($(this).data('fields-name') && $(this).hasClass('edited')) {
				var data_name = $(this).data('fields-name');
				var val = $(this).val();
				prepare_data[data_name] = val;
			}
		});
	
		$.ajax({
			type: 'POST',
			url: 'ajax_route.php',
			data: {
				url: 'core/action/expense/edit_expense.php',
				route: 'editExpense',
				data: prepare_data
			},
			dataType: "json",
			success: (data) => {
				if(data.type == 'success') {
					pageData.alert_notice(data.type, data.text);
	
					for (key in prepare_data) {
						pageData.update_table_row(key, prepare_data[key], rasxod_id);
					}				
				} else {
					pageData.alert_notice('error', error)
				}
			}			
	
		});
	}
});


/**
 * удалить расход
 */
 $(document).on('click', '.delete-rasxod', function() {
	const id = $(this).data('delete-id');

	$.ajax({
		type: 'POST',
		url: 'ajax_route.php',
		data: {
			url: 'core/action/expense/delete_expense.php',
			route: 'deleteExpense',
			id: id
		},
		dataType: 'json',
		success: (data) => {
			pageData.alert_notice(data.type, data.text);
			
			if(data.type == 'success') {
				pageData.rightSideModalHide();
				pageData.overlayHide();

				var $stock = $(`#${id}.stock-list`); 

				$stock.hide(1000, function() {
					$stock.remove();
				});
			}

		}
	});
});


// загрузить баркоды товара
 $(document).on('click', '.load-product-barcode', function() {
	var data = $(this).data('value');

	remove_modal();

	$.ajax({
		type: 'POST',
		url: 'ajax_route.php',
		data: {
			url: '/core/action/barcode/edit_product_barcode_modal.php',
			route: 'editProductBarcodeModal',
			data: data
		},
		dataType: 'json',
		success: (data) => {
			$('.container').append(data.res);
		}
	});
});

 $(document).on('click', '.load-product-history', function() {
	var data = $(this).data('value');

	remove_modal();

	$.ajax({
		type: 'POST',
		url: 'ajax_route.php',
		data: {
			url: '/core/action/stock/get_products_history.php',
			route: 'getProductHistory',
			data: data
		},
		dataType: 'json',
		success: (data) => {
			$('.container').append(data.res);
		}
	});
});


// добавить категорию
$('body').on('click', '.add-submit-category', function() {
	let prepare_data = {};

	if($(this).closest('.modal-form').length) {
		if(is_required_input($(this).closest('.modal-form').find('.form-input'))) {
			prepare_data = prepare_form_fields($(this).closest('.modal-form'));
			$.ajax({
				type: 'POST',
				url: 'core/action/category/modal_add_category.php',
				data: {
					post_data: prepare_data,
				},
				dataType: "json",
				success: (data) => {
					if(data.success) {
					$('.opened-custom-modal').find('.category-input-value').val(data.category_name);

					$('.opened-custom-modal').find('.hidden-category-id').val(data.category_id);


						// $('.category-input-value').val(data.category_name);
						// $('.hidden-category-id').val(data.category_id);
						pageData.rightSideModalHide();
						pageData.overlayHide();						
					}
				}			
			});
		}		
	}

	if($(this).closest('.stock-from-container').length) {
		if(is_required_input($(this).closest('.stock-from-container').find('.form-input'))) {
			prepare_data = prepare_form_fields($(this).closest('.stock-from-container'));
			$.ajax({
				type: 'POST',
				url: 'ajax_route.php',
				data: {
					route: 'addCategory',
					url: 'core/action/category/add_category.php',
					post_data: prepare_data,
					page: pageData.page(),
					type: pageData.type()
				},
				dataType: "json",
				success: (data) => {
					pageData.alert_notice(data.type, data.text);
					
					if(data.type == 'success') {
						$('.form-input').val('');
	
						if(data.table) {
							return pageData.prependTable(data.table);
						}
					}
				}			
	
			});
		}
	}
});




// сохранить баркод
$(document).on('click', '.save-edit-stock-barcode', function() {
	var productId = $(this).data('id');

	let new_barcode_list = [];
	let removed_barcode_list = [];
	let edited_barcode_list = [];


	$('.edit-barcode-input').each(function(index, key){
		
		// НОВЫЙ БАРКОД
		if($(this).hasClass('new') && !$(this).parent().hasClass('hide')) {
			new_barcode_list.push($(this).val());
		}

		// УДАЛЕННЫЫЙ БАРКОД
		if(!$(this).hasClass('new') && $(this).parent().hasClass('hide')) {
			removed_barcode_list.push($(this).data('barcode-id'));		
		}		

		if($(this).hasClass('deleted')) {
			removed_barcode_list.push($(this).data('barcode-id'));
		}			

		// ИЗМЕНЕННЫЙ БАРКОД
		if(!$(this).hasClass('new') && !$(this).parent().hasClass('hide')) {
			if($(this).hasClass('edited')) {
				edited_barcode_list.push({
					barcodeId: $(this).data('barcode-id'),
					value: $(this).val()
				});
			}
		}		
	});

	$.ajax({
		type: 'POST',
		url: 'ajax_route.php',
		data: {
			url: 'core/action/barcode/set_product_barcode.php',
			route: 'setProductBarcode',
			productId: productId,
			new_barcode_list: new_barcode_list,
			removed_barcode_list: removed_barcode_list,
			edited_barcode_list: edited_barcode_list
		},
		dataType: 'json',
		success: (data) => {				
			pageData.alert_notice(data.type, data.text);

				$('.edit-barcode-input').removeClass(['edited', 'new']);

				$(this).closest('.modal-container').find('.form-fields.hide').remove();
			
			if(data.newAddedBarcode) {
				data.newAddedBarcode.forEach((item) => {
					 $('.edit-barcode-input').each(function() {
						var val = $(this).val();

						if(item.barcodeValue == val) {
							$(this).attr('data-barcode-id', item.barcodeId);
						}
					 });
				});
			}
		}
	});
});



// создать новый фильтер
$(document).on('click', '.submit-create-filter', function() {
	let filter_option_list = [];
	var filter_title = $('.add-filter-title').val(); 


	$('.add-filter-option').each(function() {
		if($(this).val() !== '') {
			filter_option_list.push({'value': $(this).val()}); 
		}
	});


	if(is_required_input($(this).closest('.filter-form-content').find('.input-required'))) { 
		$.ajax({
			type: 'POST',
			url: 'ajax_route.php',
			data: {
				route: 'addFilter',
				url: 'core/action/filter/add-filter.php',
				filter_title: filter_title,
				filter_option: filter_option_list,
				page: pageData.page(),
				type: pageData.type()				
			},
			dataType: 'json',
			success: (data) => {
				pageData.alert_notice(data.type, data.text);

				if(data.type == 'success') {
					$('.form-input').val('');
					pageData.prependTable(data.table);					
				}
			}
		});
	}	

});


// изменить фильтер
$(document).on('click', '.submit-save-edited-filter', function() {
	let edit_option_list = [];
	let deleted_option_list = [];
	let add_new_option = [];


	var filter_id = $(this).closest('.modal_order_form').data('order-id');
	let filter_name = $('.edit-filter-name-input').val();

	$('.edit-filter-option').each(function() {
		if($(this).hasClass('edited') && !$(this).hasClass('new')) {
			edit_option_list.push({
				'id': $(this).data('id'),
				'value': $(this).val()
			});
		}
	});

	$('.input-removed').each(function() {
		if(!$(this).hasClass('new')) {
			deleted_option_list.push($(this).data('id'));
		}
	});

	$('.form-input.new').each(function() {
		if(!$(this).hasClass('input-removed')) {
			add_new_option.push({
				'value': $(this).val(),
				'type_id': filter_id
			});
		}
	});
	
		$.ajax({
			type: 'POST',
			url: 'ajax_route.php',
			data: {
				route: 'editFilter',
				url: 'core/action/filter/edit-filter.php',
				filter_id: filter_id,
				filter_name: filter_name,
				option_list: edit_option_list,
				deleted_option_list: deleted_option_list,
				add_new_option: add_new_option,
				page: pageData.page(),
				type: pageData.type()
			},
			dataType: 'json',
			success: (data) => {
				pageData.alert_notice(data.type, data.text);


				if(data.type == 'success') {
					pageData.update_table_row('filter_name', filter_name, filter_id);
				}				
			}
		});
		
});

$(document).on('click', '.delete-filter', function(){
	var id = $(this).data('delete-id');

	$.ajax({
		type: 'POST',
		url: 'ajax_route.php',
		data: {
			route: 'deleteFilter',
			url: 'core/action/filter/deleteFilter.php',
			id: id
		},
		success: (data) => {
			pageData.alert_notice(data.type, data.text);

            if(data.type == 'success') {
				pageData.rightSideModalHide();
				pageData.overlayHide();

				var $stock = $(`.stock-list#${id}`); 

				$stock.hide(1000, function() {
					$stock.remove();
				});
            }				
		}
	});
});


// расширенный поиск 
$(document).on('click', '.advanced-search-submit', function() {
	
	let stock_category_list = [];
	let stock_provider_list = [];


	// Название продукта
	var stock_name = $(this).closest('.stock-from-container').find('.advanced-search-input--stock-name').val();

	var stock_description = $(this).closest('.stock-from-container').find('.advanced-search-input--stock-description').val();


	// Категория
	$(this).closest('.stock-from-container').find('.advanced-search-input--stock-category').each(function(){
		let id = $(this).val();
		if(id.length > 0) {
			stock_category_list.push(id);
		}
	});


	// Поставщик
	$(this).closest('.stock-from-container').find('.advanced-search-input--stock-provider').each(function(){
		let id = $(this).val();

		if(id.length > 0) {
			stock_provider_list.push(id);
		}
	});

	// месяц для отчета
	let report_month = $(this).closest('.stock-from-container').find('.advanced-search-input--report-month-picker').val();
	let report_day = $(this).closest('.stock-from-container').find('.advanced-search-input--report-day-picker').val();

	const post_list = {};
	$('.advanced').each(function(){
		let val = $(this).val();

		var data_fields = $(this).data('fields-name');

		if(val) {
			if($(`.advanced[data-fields-name=${data_fields}]`).length > 1) {
				if(!post_list[data_fields]) {
					post_list[data_fields] = [];
				}
		
				post_list[data_fields].push($(this).val());
			} else {
				post_list[data_fields] = [val];
			}


		}

	});



	// data-
	$.ajax({
		type: 'POST',
		url: 'ajax_route.php',
		data: {
			url: 'core/action/search/advanced_search.php',
			route: 'advancedSearch',
			data: {
				page: pageData.page(),
				type: pageData.type(),
				post_list: post_list,
			}
		},
		dataType: 'json',		
		success: (data) => {


			if(data.table) {
				pageData.innerTable(data.table);	
			}
			if(data.total) {
				pageData.innerTableFooter(data.total);
			}			
		}
	});

});


// добавляем новый инпут для форымы
$(document).on('click', '.append-new-input', function() {

	let fields_name = $(this).data('fields-name');

	$.ajax({
		type: 'POST',
		url: 'ajax_route.php',
		data: {
			url: 'core/action/add_more_fields.php',
			route: 'appendMoreFields',
			data: {
				fields_name: fields_name
			}
		},
		beforeSend: function( ){
			
		},
		complete: function() {
		
		},		
		dataType: 'json',
		success: (data) => {
			$(this).parent().before(data.fields);
		}
	});
});



// отправить ключь лицензии
$(document).ready(function(){
	$('.send-license-key').on('click', function() {

		var key = $('.license-key').val();

		$.ajax({
			type: 'POST',
			url: 'ajax_route.php',
			data: {
				route: 'activeLicense',
				url: 'core/action/admin/action/active_license.php',
				data: key,
			},
			dataType: 'json',
			success: (data) => {
				
				if(data.alert_type == 'error') {
					pageData.alert_notice(data.alert_type, data.text);
				} else {
					$('.link-container').removeClass('display-none').addClass('flex');
				}
			}
		});
	
	});
	
});




$(document).on('click', '.add-seller', function() {
	var seller_name = $('.add-seller-name').val();
	var seller_password = $('.add-seller-password').val();

	if(is_required_input($(this).closest('.stock-from-container').find('.form-input'))) {

		$.ajax({
			type: 'POST',
			url: 'ajax_route.php',
			dataType: 'json',
			data: {
				route: 'addUser',
				url: '/core/action/admin/user/add-user.php',
				seller_name: seller_name,
				seller_password: seller_password,
				page: pageData.page(),
				type: pageData.type()
			},
			success: (data) => {
				console.log(data);
				pageData.alert_notice(data.type, data.text);

				if(data.type == 'success') {
					$('.form-input').val('');
					
					pageData.prependTable(data.table);
					// pageData
				}
			}
		});

	} 

});

$(document).on('click', '.switcher-new-state', function() {
	if(!$(this).hasClass('old')) {
		$(this).addClass('new');
	}
});

$(document).on('click', '.submit-save-seller-info', function() {
	let prepare_data = prepare_forms($(this).closest('.modal_order_form'), $('.edit-seller.edited'));

	$item = $(this).closest('.modal_order_form').find('.edit-seller');

	var id = $('.edit-seller-id').val();

	prepare_data['seller_id'] = id;

	let newAccessData = [];
	let delAccessData = [];
	let newAccessAction = [];
	let delAccessAction = [];

	$('.user-access-data-switcher').each(function() {
		if($(this).hasClass('new') && $(this).hasClass('switcher-active')) {
			newAccessData.push($(this).data('id'));
		}

		if($(this).hasClass('old') && !$(this).hasClass('switcher-active')) {
			delAccessData.push($(this).data('id'));
		}
	});

	$('.user-access-action-switcher').each(function() {
		if($(this).hasClass('new') && $(this).hasClass('switcher-active')) {
			newAccessAction.push($(this).data('id'));
		}

		if($(this).hasClass('old') && !$(this).hasClass('switcher-active')) {
			delAccessAction.push($(this).data('id'));
		}
	});	

	if(is_required_input($item)) {
		$.ajax({
			type: 'POST',
			url: 'ajax_route.php',
			dataType: 'json',
			data: {
				route: 'editUser',
				url: 'core/action/admin/seller/edit-user.php',
				prepare_data: prepare_data,
				newAccessData: newAccessData,
				delAccessData: delAccessData,
				newAccessAction: newAccessAction,
				delAccessAction: delAccessAction,
			},
			success: (data) => {
				pageData.alert_notice(data.type, data.text);

				if(data.type == 'success') {
					for (key in prepare_data) {
						pageData.update_table_row(key, prepare_data[key], id);
					}

					$('.switcher-new-state').removeClass(['old', 'new']);
					$('.switcher-new-state.switcher-active').addClass('old');
				}
			}	
		});
	}

	$('.edit-seller').removeClass('edited');
});


$(document).on('click', '.delete-seller', function() {
	var seller_id = $(this).data('delete-id');
	$.ajax({
		type: 'POST',
		url: 'ajax_route.php',
		data: {
			route: 'deleteUser',
			url: 'core/action/admin/seller/delete_seller.php',
			seller_id: seller_id,
		},
		success: (data) => {
			pageData.alert_notice(data.type, data.text);


            if(data.type == 'success') {
				pageData.rightSideModalHide();
				pageData.overlayHide();

				var $stock = $(`.stock-list#${seller_id}`); 

				$stock.hide(1000, function() {
					$stock.remove();
				});
            }			
		}	
	});
});


$(document).on('click', '.add-warehouse', function() {
	$this_form = $(this).closest('.stock-from-container');
	const prepare = prepare_form_data($this_form, '.warehouse-add-input', 'fields-name');

	$.ajax({
		type: 'POST',	 	
		url: 'ajax_route.php',
		data: {
			route: 'addWarehouse',
			url: '/core/action/warehouse/add-warehouse.php',
			page: pageData.page(),
			type: pageData.type(),
			data_row: prepare
		},
		success: (data) => {
			pageData.alert_notice(data.type, data.text);
			
			$('.form-input').val('');

			if(data.table) {
				return pageData.prependTable(data.table);
			}			

		}
	});

});



$(document).on('click', '.submit-save-warehouse', function() {
	const prepare = prepare_forms($(this).closest('.modal_order_form'), '.edit-warehouse-input', 'data-fields-name');

	$.ajax({
		type: 'POST',
		url: 'ajax_route.php',
		data: {
			route: 'editWarehouseInfo',
			url: 'core/action/warehouse/edit-warehouse.php',
			prepare: prepare
		},
		success: (data) => {
			pageData.alert_notice(data.type, data.text);

			for (key in prepare) {
				pageData.update_table_row(key, prepare[key], prepare.warehouse_id);
			}
		}
	});
});

$(document).on('click', '.delete-warehouse', function() {
	const id = $(this).data('delete-id');

	$.ajax({
		type: 'POST',
		url: 'ajax_route.php',
		data: {
			route: 'deleteWarehouse',
			url: 'core/action/warehouse/delete-warehouse.php',
			id: id
		},
		success: (data) => {
			pageData.alert_notice(data.type, data.text);

			pageData.rightSideModalHide();
			pageData.overlayHide();

			var $stock = $(`.stock-list#${id}`); 

			$stock.hide(1000, function() {
				$stock.remove();
			});
		}
	});
});


$(document).on('click', '.submit-save-edited-payment-method', function(){

	const prepare_data = prepare_form_data($(this).closest('.modal_order_form'), '.edit-payment-method-info.edited', 'fields-name');

	$.ajax({
		type: 'POST',
		url: 'ajax_route.php',
		data: {
			prepare_data,
			route: 'editPaymentMethod',
			url: 'core/action/payment-method/edit-payment-method.php',
		},
		success: (data) => {
			pageData.alert_notice(data.type, data.text);

			for (key in prepare_data) {
				pageData.update_table_row(key, prepare_data[key], prepare_data.payment_method_id);
			}	

		}
	});

});



$(document).on('click', '.add-payment-method', function() {

	const prepare_data = prepare_form_data(
		$(this).closest('.stock-from-container'),
		'.create-payment-method-input.edited',
		'fields-name'		
	);


	$.ajax({
		type: 'POST',
		url: 'ajax_route.php',
		data: {
			route: 'addPaymentMethod',
			url: 'core/action/payment-method/add-payment-method.php',
			prepare_data: prepare_data,
			page: pageData.page(),
			type: pageData.type()
		},
		success: (data) => {
			pageData.alert_notice(data.type, data.text);	
			
			if(data.type == 'success') {
				pageData.prependTable(data.table);
				$('.form-input').val('');				
			}
		}
	});
});


$(document).on('click', '.delete-payment-method', function() {
	const id = $(this).data('delete-id');

	$.ajax({
		type: 'POST',
		url: 'ajax_route.php',
		data: {
			url: 'core/action/payment-method/delete-payment-method.php',
			route: 'deletePaymentMethod',
			payment_method_id: id
		},
		success: (data) => {
			pageData.alert_notice(data.type, data.text);

			var $item = $(`#${id}.stock-list`); 

			$item.hide(1000, function() {
				$item.remove();
			});
			
			pageData.overlayHide();
			pageData.rightSideModalHide();
		}
	});
});




