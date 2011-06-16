var dealertrend_admin = jQuery.noConflict();

dealertrend_admin(document).ready(function() {
	checklistmakes();
	checklistmodels();
});

function checklistmakes() {
	dealertrend_admin(".vrs-makes")
		.multiselect({
			noneSelectedText: 'Select A Make',
			selectedList: 4,
			classes: 'makes',
			click: function(event, ui){}
		})
		.multiselectfilter();

	dealertrend_admin(".vrs-makes").bind("multiselectclick", function(event, ui){
		dealertrend_admin(this).parent().parent().parent().children('.widget-control-actions').children('.alignright').children('input').size();
	});
}

function checklistmodels() {

	dealertrend_admin(".vrs-models")
		.multiselect({
			noneSelectedText: 'Select A Model',
			classes: 'models',
			selectedList: 4
		})
		.multiselectfilter();
}

dealertrend_admin('body').ajaxSuccess(function(evt, request, settings) {
	checklistmakes();
	checklistmodels();
	if( settings.data.search('/vehiclereferencesystemwidget.*action=save-widget/i') ) {
		// Because of the hack we do to catch the postback of the widget ajax - duplicate buttons are possible...so this fixes that.
		if( settings.data.search('/vehiclereferencesystemwidget.*add_new=multi&action=save-widget/ig') ) {
			dealertrend_admin(".widget-liquid-right .widget-content p button.ui-multiselect.makes").filter(function() {
				return dealertrend_admin(this).text() > '1';
			}).eq(1).hide();
		}
	}
});
