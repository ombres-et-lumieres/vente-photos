<?php
/**
 * Class to access array items
 */
class cwpai_AccessArray {
  protected $array;

  /**
   * Constructor function
   * @param array $array
   */
  public function __construct($array) {
	$this->array = $array;
  }

  /**
   * Function to get item from array by key
   * @param string $key
   * @throws Exception if the key is not present in the array
   * @return mixed
   */
  public function getItemKey($key) {
	if (isset($this->array[$key])) {
	  return $this->array[$key];
	} else {
	  throw new Exception("L'élément $key n'existe pas dans le tableau.");
	}
  }
}

class Cwpai_Form_Fields {
  private $field;
  private $label_text;
  private $html_text;

  /**
   * Constructor function
   * @param array $field
   * @param string $label_text
   * @param string $html_text
   */
  public function __construct($field, $label_text, $html_text) {
	$this->field = $field;
	$this->label_text = $label_text;
	$this->html_text = $html_text;
  }

  /**
   * Function to get form fields
   * @return array
   */
  public function get_form_fields() {
	$form_fields['field'] = array(
	  'value' => print_r($this->field, true),
	  'label' => __($this->label_text, 'codewp'),
	  'input' => 'html',
	  'html' => '<pre>' . print_r($this->field, true) . '</pre>' . $this->html_text,
	);

	return $form_fields;
  }
}

// $tab = array('a' => 'value1', 'b' => array('value2', 'value3'), 'c' => 'value4');
// $arrayAccess = new cwpai_AccessArray($tab);
// 
// print_r($arrayAccess->getItemKey
 // Cela affichera Array ( [0] => value2 [1] => value3 )

/**
 * Class to handle attachment metadata
 */
class CWPAI_Attachment_Meta {
	public function __construct() {
		add_filter('attachment_fields_to_edit', array($this, 'cwpai_add_attachment_fields'), 10, 2);
		add_action('admin_head', array($this, 'cwpai_custom_admin_css'));
	}

	 public function cwpai_custom_admin_css() {
			echo '<style>
				.compat-field-cwpai_custom_field textarea {
					height: 25vh;
					width: 50%;
				}
			</style>';
		}
	
	
	public function cwpai_add_attachment_fields($form_fields, $post) {
			// Récupérez les métadonnées
			$metas = wp_get_attachment_metadata($post->ID);
			$metas = $metas['image_meta'];
			
			$metas_temp = new cwpai_AccessArray($metas);
			
			$metas_utiles = array (
				// 'caption' => $metas['caption'],
				// 'copyright'=> $metas['copyright'],
				// 'title' => $metas['title'],
				'caption' => $metas_temp->getItemKey('caption'),
				'copyright'=> $metas_temp->getItemKey('copyright'),
				'title' => $metas_temp->getItemKey('title'),
			);
			
			// Convertir le tableau en une chaîne formatée
			$meta_string = print_r($metas_utiles, true);
			
			$form_fields['cwpai_custom_field'] = array(
				'label' => 'Mes Métadonnées',
				'input' => 'textarea',
				'value' => $meta_string,
			);
		
			return $form_fields;
		}
	}

new CWPAI_Attachment_Meta();

/**
 * Class to display Adobe XMP data
 */
class DisplayAdobeXMP {
	// Instance of the adobeXMPforWP class
	private $adobeXMP;

	public function __construct() {
		// Hook into the 'plugins_loaded' action
		add_action('plugins_loaded', array($this, 'initialize'));
	}

	public function initialize() {
		// Get the instance of adobeXMPforWP
		$this->adobeXMP = adobeXMPforWP::get_instance();

		// Hook into the 'attachment_fields_to_edit' action
		add_filter('attachment_fields_to_edit', array($this, 'display_adobe_xmp'), 10, 2);
	}

	public function display_adobe_xmp($form_fields, $post) {
		// Get the XMP data for the attachment
		$xmp_data = $this->adobeXMP->get_xmp($post->ID);
		$xmp_data_utiles = array(['IFD0']['ImageDescription'],['IFD0']['Copyright'], ['GPS']);
		

		if(!empty($xmp_data)) {
			$form_fields['xmp_data'] = array(
				'label' => 'XMP Data',
				'input' => 'html',
				'html' => '<pre>' . print_r($xmp_data_utiles, true) . '</pre>',
				'helps' => 'Adobe XMP Data'
			);
		}

		return $form_fields;
	}
}

// Instantiate the DisplayAdobeXMP class
$displayAdobeXMP = new DisplayAdobeXMP();





