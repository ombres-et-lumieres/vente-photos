<?php
class CustomAttachmentMetadata {

	public function __construct() {
		add_filter('attachment_fields_to_edit', array($this, 'add_custom_field'), 10, 2);
		add_filter('attachment_fields_to_save', array($this, 'save_custom_field'), 10, 2);
	}

	public function add_custom_field($form_fields, $post) {
		$form_fields['a_vendre'] = array(
			'label' => __('A vendre?', 'codewp'),
			'input' => 'html',
			'html' => '<input type="checkbox" id="attachments-'.$post->ID.'-a_vendre" name="attachments['.$post->ID.'][a_vendre]" value="1"'.(get_post_meta($post->ID, '_a_vendre', true) == 1 ? ' checked="checked"' : '').' />',
			'value' => get_post_meta($post->ID, '_a_vendre', true),
			'helps' => __('If checked, this item is for sale.', 'codewp')
		);
		return $form_fields;
	}

	public function save_custom_field($post, $attachment) {
		if(isset($attachment['a_vendre'])) {
			update_post_meta($post['ID'], '_a_vendre', 1);
		} else {
			update_post_meta($post['ID'], '_a_vendre', 0);
		}
		return $post;
	}

	public function dump_attachment_metadata($post_id) {
		$metadata = wp_get_attachment_metadata($post_id);
		return $metadata;
	}
}

//$new_instance = new CustomAttachmentMetadata();


//$metadata_output = $new_instance->dump_attachment_metadata($post_id);
//var_dump($metadata_output);

class CWPAI_Attachment_Meta {
	public function __construct() {
		add_filter('attachment_fields_to_edit', array($this, 'cwpai_add_attachment_fields'), 10, 2);
		add_action('admin_head', array($this, 'cwpai_custom_admin_css'));
	}

 	public function cwpai_custom_admin_css() {
			echo '<style>
				.compat-field-cwpai_custom_field textarea {
					height: 100vh;
					width: 50%;
				}
			</style>';
		}
	
	
	public function cwpai_add_attachment_fields($form_fields, $post) {
			// Récupérez les métadonnées
			$metas = wp_get_attachment_metadata($post->ID);
			$metas = $metas['image_meta'];
			
			// Convertir le tableau en une chaîne formatée
			$meta_string = print_r($metas, true);
			
			$form_fields['cwpai_custom_field'] = array(
				'label' => 'Mes Métadonnées',
				'input' => 'textarea',
				'value' => $meta_string,
			);
		
			return $form_fields;
		}
	}

new CWPAI_Attachment_Meta();



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

		if(!empty($xmp_data)) {
			$form_fields['xmp_data'] = array(
				'label' => 'XMP Data',
				'input' => 'html',
				'html' => '<pre>' . print_r($xmp_data, true) . '</pre>',
				'helps' => 'Adobe XMP Data'
			);
		}

		return $form_fields;
	}
}

// Instantiate the DisplayAdobeXMP class
$displayAdobeXMP = new DisplayAdobeXMP();



class cwpai_IPTCData {
	private $image_path;
	private $iptc_data;

	public function __construct($image_path) {
		$this->image_path = $image_path;
		$this->iptc_data = array();
		$this->extractIPTC();
	}

	private function extractIPTC() {
		if(is_readable($this->image_path)) {
			getimagesize($this->image_path, $info);

			if(isset($info['APP13'])) {
				$iptc = iptcparse($info['APP13']);

				if(is_array($iptc)) {
					$this->iptc_data = $iptc;
				}
			}
		}
	}

	public function getIPTCData() {
		return $this->iptc_data;
	}
}

class cwpai_IPTCAdmin {
	public function __construct() {
		add_filter('attachment_fields_to_edit', array($this, 'add_iptc_fields'), 10, 2);
	}

	public function add_iptc_fields($form_fields, $post) {
		$file_path = get_attached_file($post->ID);
		$iptc = new cwpai_IPTCData($file_path);

		$iptc_data = $iptc->getIPTCData();

		foreach ($iptc_data as $key => $value) {
			if (is_array($value)) {
			$html = implode(',', $value);
			} else {
				$html = $value;
			}
			
			$form_fields[$key] = array(
				'label' => $key,
				'input' => 'html',
				'html' => $html,
			);
		}

		return $form_fields;
	}
}

new cwpai_IPTCAdmin();


class cwpai_ExifDataDisplay {
  public function __construct() {
		add_filter('attachment_fields_to_edit', array($this, 'cwpai_add_exif'), 10, 2);
 	}

	 public function cwpai_add_exif($form_fields, $post) {
		$file = get_attached_file($post->ID);
		$exif = exif_read_data($file, 'ANY_TAG', true);
	
		if ($exif) {
	  	$form_fields['exif'] = array(
			'value' => print_r($exif, true),
			'label' => __('Exif Data', 'codewp'),
			'input' => 'html',
			'html' => '<pre>' . print_r($exif, true) . '</pre>',
	  	);
		}
	
		if ($exif && isset($exif['GPS'])) {
	  	$form_fields['gps'] = array(
			'value' => print_r($this->cwpai_get_readable_gps_data($exif['GPS']), true),
			'label' => __('GPS Data', 'codewp'),
			'input' => 'html',
			'html' => '<pre>' . print_r($this->cwpai_get_readable_gps_data($exif['GPS']), true) . '</pre>',
	  	);
		}
	
		return $form_fields;
  }

  public function cwpai_get_readable_gps_data($gps_data) {
		if (!$gps_data) {
	  	return 'No GPS data found';
		}
  
		$lat_ref = $gps_data['GPSLatitudeRef'];
		$long_ref = $gps_data['GPSLongitudeRef'];
  	
		$lat = $this->cwpai_convert_to_decimal($gps_data['GPSLatitude']);
		$long = $this->cwpai_convert_to_decimal($gps_data['GPSLongitude']);
  	
		return "Latitude: $lat $lat_ref, Longitude: $long $long_ref";
  }
  
  public function cwpai_convert_to_decimal($gps) {
		if (!is_array($gps)) {
	  	return null;
		}
  
		$degrees = count($gps) > 0 ? $this->cwpai_gps_to_num($gps[0]) : 0;
		$minutes = count($gps) > 1 ? $this->cwpai_gps_to_num($gps[1]) : 0;
		$seconds = count($gps) > 2 ? $this->cwpai_gps_to_num($gps[2]) : 0;
  	
		// coordinate = degrees + (minutes / 60) + (seconds / 3600)
		return $degrees + ($minutes / 60.0) + ($seconds / 3600.0);
  }
  
  public function cwpai_gps_to_num($coord_part) {
		$parts = explode('/', $coord_part);
  	
		if (count($parts) <= 0)
	  	return 0;
  	
		if (count($parts) == 1)
	  	return $parts[0];
  	
		return floatval($parts[0]) / floatval($parts[1]);
  }
}
new cwpai_ExifDataDisplay();


// class cwpai_AccessArray {
// 	protected $array;
// 
// 	public function __construct($array) {
// 		$this->array = $array;
// 	}
// 
// 	public functiongetItemKey($index) {
// 		return $this->array[$index];
// 	}
// }


// $myArray = array(
// 	array('foo', 'bar'),
// 	array('baz', 'qux'),
// );

//$accessArray = new cwpai_AccessArray($myArray);
//print_r($accessArray->getItemKey
 // Renvoie le deuxième tableau ['baz', 'qux']


?>