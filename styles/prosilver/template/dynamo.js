// Going jQuery-less is just not worth it

$(document).ready(function() {
	// Click an image, and it will change it in the demo
	$('.item-button').change(function(event) {
		var layerID = $(this).attr('data-layer');
		var itemID = $(this).attr('data-item');
		var selector = '#layer-' + layerID;

		if (itemID == 0) {
			$('#layer-' + layerID).attr('src', 'images/spacer.gif');
		} else {
			var imageSrc = $(this).parent().find('img').attr('src');
			$(selector).attr('src', imageSrc);
		}
	});

	// Restore all the original items
	$('#restore-original').click(function() {
		$('#item-images').find('img').each(function() {
			var thisID = $(this).attr('id');
			var original = $(this).attr('data-original');
			var radio = '#' + thisID + '-' + original + '-radio';
			if (!$(radio).attr('checked')) {
				$(radio).attr('checked', 'true').change();
			}
		});
	});
});
