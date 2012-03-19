// Going jQuery-less is just not worth it

$(document).ready(function() {
	// Abstractified so it can be used by both "restore" buttons
	var restoreLayers = function(suffix) {
		$('#item-images').find('img').each(function() {
			var thisID = $(this).attr('id');
			var imageID = $(this).attr('data-' + suffix);
			var radio = '#' + thisID + '-' + imageID + '-radio';
			if (!$(radio).attr('checked')) {
				$(radio).attr('checked', 'true').change();
			}
		});
	};

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
		restoreLayers('original');
	});

	// Restore all the default items (no item for layers without a default)
	$('#restore-default').click(function() {
		restoreLayers('default');
	});
});
