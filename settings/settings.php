<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
class oetlChampsPhotos
{
    public function __construct() {
        /**
         * register wp_setting_init to the admin_init action hook
         */
        add_action('admin_init', array($this,'wp_setting_init'));		
    }
    
    public function wp_setting_init() {
        // register a new setting for "reading" page
        register_setting('ombres-et-lumieres-group', 'nombre_de_type_de_tirage');
        register_setting('ombres-et-lumieres-group', 'nombre_de_tailles_de_tirages');
     
        // register a new section in the "reading" page
        add_settings_section(
            'ombres-et-lumieres-section',
            'Informations sur les photos',
            null, //callback
            'ombres-et-lumieres'
        );
        
        add_settings_field(
            'nombre_de_type_de_tirage',
            'Nombre de type de tirage',
            array( $this, 'nombre_de_type_de_tirage_callback' ),
            'ombres-et-lumieres',
            'ombres-et-lumieres-section',
        );
        
        add_settings_field(
            'nombre_de_tailles_de_tirages',
            'Nombre de tailles de tirages',
            array( $this, 'nombre_de_tailles_de_tirages_callback'),
            'ombres-et-lumieres',
            'ombres-et-lumieres-section'
        );
    }
    
    public function nombre_de_type_de_tirage_callback()
    {
        // Display input field for number of print types
        ?>
        <input type="number" id="nombre_de_type_de_tirage" name="nombre_de_type_de_tirage"  value=" <?php get_option('nombre_de_type_de_tirage') ?>"/>
        <?php
    }
    
    public function nombre_de_tailles_de_tirages_callback()
    {
        echo "il y a quelqu' un";
        // Display input field for number of print sizes
        ?>
        <input type="number" id="nombre_de_tailles_de_tirages" name="nombre_de_tailles_de_tirages" value="<?php get_option('nombre_de_tailles_de_tirages')?>"/>
        <?php
    }
    
    public function photos_fields()
    {
        echo '<form method="post" action="options.php">';
        settings_fields('ombres-et-lumieres-group');
        do_settings_sections('ombres-et-lumieres');
        submit_button();
        echo '</form>';
    }
    
    
    
}
new oetlChampsPhotos();

?>