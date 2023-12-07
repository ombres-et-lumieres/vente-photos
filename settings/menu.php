<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class oetlmenu 
{
	private $nombre_de_type_de_tirage; // Number of print types
	private $nombre_de_tailles_de_tirages; // Number of print sizes
	
	public function __construct()
	{
		// Hook into WordPress admin menu and init
		add_action('admin_menu', array($this, 'create_plugin_menu'));
		add_action('admin_init', [$this, 'display_fields']);
		//add_action('wp_ajax_save_images', [$this, 'save_images']);
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
	
		// Add submenu pages
		
		add_submenu_page(
			'vente-photos',
			'Photos à vendre',
			'Photos à vendre',
			'manage_options',
			'Photos-a-vendre',
			array($this, 'photos_to_sell')
		);
		
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
		echo "<h1>Mode d' emploi</h1>";
	
	}
	
	public function photos_to_sell() {
		echo "ceci est la page vente";
		
		$photos_to_sell = new oetl_Photos_sell_page();
		
		$photos_to_sell -> print_photos();
		
		$photos_to_sell ->oetl_button_render('rechercher', 'rechercher');
	
		
	
	
		// If the form was submitted, perform the search
		if (isset($_POST['rechercher'])) {
			$photos_to_sell->search_for_sale();
		}
		$old_attachments = $photos_to_sell->search_for_sale();
		if ( $old_attachments != get_option( 'ombresetlumieres_photos_for_sale' )){
			$photos_to_sell ->oetl_button_render('afficher', 'afficher');
		}
		
	}
	
	
	public function photos_fields_page_content()
	{
		// Submenu page content
		echo '<form method="post" action="options.php">';
		settings_fields('ombres-et-lumieres-group');
		do_settings_sections('ombres-et-lumieres');
		submit_button();
		echo '</form>';
	}
	
	public function display_fields()
	{
		// Register settings and add fields
		register_setting('ombres-et-lumieres-group', 'nombre_de_type_de_tirage');
		register_setting('ombres-et-lumieres-group', 'nombre_de_tailles_de_tirages');
	
		add_settings_section(
			'ombres-et-lumieres-section',
			'Informations sur les photos',
			null,
			'ombres-et-lumieres'
		);
	
		add_settings_field(
			'nombre_de_type_de_tirage',
			'Nombre de type de tirage',
			[$this, 'nombre_de_type_de_tirage_callback'],
			'ombres-et-lumieres',
			'ombres-et-lumieres-section'
		);
	
		add_settings_field(
			'nombre_de_tailles_de_tirages',
			'Nombre de tailles de tirages',
			[$this, 'nombre_de_tailles_de_tirages_callback'],
			'ombres-et-lumieres',
			'ombres-et-lumieres-section'
		);
	
		// Get the number of print types and sizes
		$this->nombre_de_type_de_tirage = get_option('nombre_de_type_de_tirage');
		$this->nombre_de_tailles_de_tirages = get_option('nombre_de_tailles_de_tirages');
	
		for ($i = 1; $i <= $this->nombre_de_type_de_tirage; $i++) {
			// Register setting for each print type
			register_setting('ombres-et-lumieres-group', 'type_de_tirage_'.$i);
			add_settings_field(
				'type_de_tirage_'.$i,
				'Type de tirage '.$i,
				[$this, 'type_de_tirage_callback'],
				'ombres-et-lumieres',
				'ombres-et-lumieres-section',
				['id' => $i]
			);
		}
	
		for ($j = 1; $j <= $this->nombre_de_tailles_de_tirages; $j++) {
			// Register setting for each print size
			register_setting('ombres-et-lumieres-group', 'taille_de_tirage_'.$j);
			add_settings_field(
				'taille_de_tirage_'.$j,
				'Taille de tirage '.$j,
				[$this, 'taille_de_tirage_callback'],
				'ombres-et-lumieres',
				'ombres-et-lumieres-section',
				['id' => $j]
			);
		}
	}

public function nombre_de_type_de_tirage_callback()
	{
		// Display input field for number of print types
		echo '<input type="number" id="nombre_de_type_de_tirage" name="nombre_de_type_de_tirage" value="'.get_option('nombre_de_type_de_tirage').'"/>';
	}
	
	public function nombre_de_tailles_de_tirages_callback()
	{
		// Display input field for number of print sizes
		echo '<input type="number" id="nombre_de_tailles_de_tirages" name="nombre_de_tailles_de_tirages" value="'.get_option('nombre_de_tailles_de_tirages').'"/>';
	}
	
	public function type_de_tirage_callback($args)
	{
		// Display input field for each print type
		echo '<input type="text" id="type_de_tirage_'.$args['id'].'" name="type_de_tirage_'.$args['id'].'" value="'.get_option('type_de_tirage_'.$args['id']).'"/>';
	}
	
	public function taille_de_tirage_callback($args)
	{
		// Display input field for each print size
		echo '<input type="text" id="taille_de_tirage_'.$args['id'].'" name="taille_de_tirage_'.$args['id'].'" value="'.get_option('taille_de_tirage_'.$args['id']).'"/>';
	}
	

}

new oetlmenu();