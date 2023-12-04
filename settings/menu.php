<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class oetlmenu 
{
	public function __construct()
	{
		// Hook into WordPress admin menu and init
		add_action('admin_menu', array($this, 'create_plugin_menu'));
	}
	
	public function create_plugin_menu()
	{
		// Add main menu page
		add_menu_page(
			'Ombres Et Lumieres',
			'Vente De Photos',
			'manage_options',
			'vente-photos',
			array( $this, 'main_page_content'),
			'dashicons-camera',
			'100'
		);
	
		// Add submenu page
		add_submenu_page(
			'vente-photos',
			'Informations Sur Les Photos',
			'Informations Sur Les Photos',
			'manage_options',
			'informations-sur-les-photos',
			array($this, 'photos_fields_page_content')
		);
	}
	
	public function main_page_content() {
		echo "ceci est la page principale";
	}
	
	public function photos_fields_page_content()
	{
		echo "ceci est la page secondaire";
	
		// Submenu page content
		 echo '<form method="post" action="options.php">';
		 settings_fields('ombres-et-lumieres-group');
		 do_settings_sections('ombres-et-lumieres');
		 submit_button();
		 echo '</form>';
		
		
	}
	

}

new oetlmenu();