jQuery(document).ready(function($) {
	// Open the WordPress media library when the button is clicked
	$('#upload-images-button').click(function(e) {
		e.preventDefault();

		var frame = wp.media({
			title: 'Sélectionner ou Télécharger des Images',
			button: {
				text: 'Utiliser ces images'
			},
			multiple: true
		});

		// Handle results from media manager.
		frame.on('select', function() {
			var selection = frame.state().get('selection');
			var images = [];

			selection.map(function(attachment) {
				attachment = attachment.toJSON();
				images.push(attachment);
			});

			// Display the selected images on the "Vente De Photos" page
			var imagesList = $('#images-list');
			imagesList.empty();

			images.forEach(function(image) {
				imagesList.append('<img src="' + image.url + '" alt="' + image.title + '">');
			});

			// Save the list of selected images for future use
// 			var data = {
// 				action: 'save_selected_images',
// 				images: images
// 			};
// 
// 			$.post(ajaxurl, data, function(response) {
// 				console.log(response);
// 			});
			
			// Send the selected images to the server via AJAX
			$.post(OmbresEtLumieresAjax.ajaxUrl, {
				action: 'save_images',
				images: images
			}, function(response) {
				// TODO: Handle the response from the server
				console.log(response);
			});
			
			
			
		});

		frame.open();
	});
});