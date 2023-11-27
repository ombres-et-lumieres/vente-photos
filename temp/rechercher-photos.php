<?php
add_action('admin_menu', 'cwpai_add_admin_menu');
add_action('admin_init', 'cwpai_settings_init');

function cwpai_add_admin_menu() {
	add_options_page('Recherche de photos', 'Recherche de photos', 'manage_options', 'recherche_photos', 'cwpai_options_page');
}

function cwpai_settings_init() {
	register_setting('pluginPage', 'cwpai_settings');

	add_settings_section(
		'cwpai_pluginPage_section',
		__('Rechercher des photos à vendre', 'codewp'),
		'cwpai_settings_section_callback',
		'pluginPage'
	);

	add_settings_field(
		'cwpai_rechercher_button',
		__('Rechercher', 'codewp'),
		'cwpai_rechercher_button_render',
		'pluginPage',
		'cwpai_pluginPage_section'
	);
}

function cwpai_rechercher_button_render() {
	echo '<input type="submit" name="cwpai_rechercher" value="Rechercher">';
}

function cwpai_options_page() {
	?>
	<form action="options.php" method="post">
		<?php
		settings_fields('pluginPage');
		do_settings_sections('pluginPage');
		submit_button();
		?>
	</form>
	<?php
}

function cwpai_settings_section_callback() {
	if (isset($_POST['cwpai_rechercher'])) {
		cwpai_create_cpt_from_attachments();
	}
}

function cwpai_create_cpt_from_attachments() {
	// Votre code précédent ici
}