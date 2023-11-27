<?php



// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if class already exists before creating it
if (!class_exists('OmbresEtLumieres')) {
    class OmbresEtLumieres
    {
        public function __construct()
        {
            // Hook into the 'add_meta_boxes' action
            add_action('add_meta_boxes', array($this, 'add_custom_meta_box'));

            // Hook to save the meta box data when post is saved
            add_action('save_post', array($this, 'save_custom_meta_box_data'));
        }

        public function add_custom_meta_box()
        {
            // Add meta box for attachments
            add_meta_box('ombres_et_lumieres_meta', 'Informations sur la vente de photos', array($this, 'render_custom_meta_box'), 'attachment');
        }

        public function render_custom_meta_box($post)
        {
            // Use nonce for verification
            wp_nonce_field(plugin_basename(__FILE__), 'ombres_et_lumieres_nonce');

            // Get the current values if they exist
            $a_vendre = get_post_meta($post->ID, 'a_vendre', true);

            // Checkbox for 'À vendre?'
            echo '<label for="a_vendre">À vendre?</label>';
            echo '<input type="checkbox" id="a_vendre" name="a_vendre" value="1" ' . checked(1, $a_vendre, false) . ' />';
        }

        public function save_custom_meta_box_data($post_id)
        {
            // Check if our nonce is set and verify that the nonce is valid.
            if (!isset($_POST['ombres_et_lumieres_nonce']) || !wp_verify_nonce($_POST['ombres_et_lumieres_nonce'], plugin_basename(__FILE__))) {
                return $post_id;
            }

            // Check user's permissions.
            if ('attachment' == $_POST['post_type']) {
                if (!current_user_can('edit_page', $post_id)) {
                    return $post_id;
                }
            }

            // Sanitize user input and update the meta field in the database.
            $a_vendre = isset($_POST['a_vendre']) ? '1' : '0';
            update_post_meta($post_id, 'a_vendre', $a_vendre);
        }
    }
    // Instantiate the plugin class
    $ombresEtLumieres = new OmbresEtLumieres();
}