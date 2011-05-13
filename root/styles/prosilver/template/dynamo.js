// Click an image, and it will change it in the avatar etc
function changeImage(layer_id, item_id, position) {
	// I can totally do this without jQuery
	// If the image already exists, just change the img src
	var theDiv = document.getElementById(layer_id);
	var theImg = (theDiv != null) ? theDiv.firstChild : null;
	if (theImg != null) {
		// If the item id is 0, then just make the image empty lol
		if (item_id == 0) {
			// Delete the whole div
			console.log("delete shit");
			theDiv.parentNode.removeChild(theDiv);
		} else {
			theImg.setAttribute('src', 'images/dynamo/' + layer_id + '-' + item_id + '.png');
		}
	} else {
		// Doesn't exist, append the div
		var toAppend = '<div id="' + layer_id + '" style="position: absolute; z-index: ' + position + ';"><img src="images/dynamo/' + layer_id + '-' + item_id + '.png" /></div>';
		document.getElementById("item-images").innerHTML += toAppend;
	}
}
