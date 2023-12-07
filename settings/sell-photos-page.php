<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class oetl_Photos_sell_page
{
	public function print_photos(){
		if(get_option( 'ombresetlumieres_photos_for_sale' )){
			 $attachments = get_option( 'ombresetlumieres_photos_for_sale' );
		 
		
			// Prepare HTML for the results
			$html = '<div id="photos-for-sale">';
		 
		   foreach ($attachments as $attachment) {
			   // Get the small size image URL
			   $image_url = wp_get_attachment_image_src($attachment->ID, 'thumbnail')[0];
			   $html .= '<img src="' . $image_url . '" />';
		   }
		
			$html  = $html . '</div>';
			// Return the result
			echo $html;
		}
	}
	
	public function oetl_button_render($name, $value) {
		echo "choisissez les photos";
		echo '<form method="post">';
		echo '<input type="submit" name=" '. $name . '" value="' . $value . '">';
		echo '</form>';
	}
	
	public function search_for_sale() {
			// Define arguments for the media search
			$args = array(
				'post_type'   => 'attachment',
				'post_status' => 'inherit',
				'posts_per_page' => -1,
				'meta_query'  => array(
					array(
						'key'     => '_for_sale',
						'value'   => true,
					),
				),
			);
		
			// Execute the search
			$attachments = get_posts($args);
			
			$attachments_old = get_option( 'ombresetlumieres_photos_for_sale' );
			
			// Save the results as an option
			update_option('ombresetlumieres_photos_for_sale', $attachments);
			
			return $attachments_old;
			
		}
		
}
//new oetl_Photos_sell_page();