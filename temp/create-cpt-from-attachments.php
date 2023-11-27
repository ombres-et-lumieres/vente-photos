<?php
add_action('init', 'cwpai_create_cpt_from_attachments');
function cwpai_create_cpt_from_attachments() {
	$args = array(
		'post_type' => 'attachment',
		'post_status' => 'inherit',
		'posts_per_page' => -1,
		'meta_query' => array(
			array(
				'key' => '_for_sale',
				'value' => true,
			),
		),
	);

	$attachments = get_posts($args);

	foreach($attachments as $attachment) {
		$post_id = wp_insert_post(array(
			'post_title' => $attachment->post_title,
			'post_type' => 'vente_de_photos',
			'post_status' => 'publish',
		));

		set_post_thumbnail($post_id, $attachment->ID);

		update_post_meta($post_id, 'texte_alternatif', get_post_meta($attachment->ID, '_wp_attachment_image_alt', true));
		update_post_meta($post_id, 'description', $attachment->post_content);
	}
}