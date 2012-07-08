// Some simple js to enhance editing etc

$(document).ready(function () {
	// On the add new item page, set the price to the default price of the layer
	// Only the first time it's changed
	var priceInput = $('#item_price');
	$('#dynamo_item_layer').change(function () {
		var selected = $(this).find('option:selected');
		if (priceInput.val() == '') {
			priceInput.val($(selected).attr('data-price'));
		}
	});
});
