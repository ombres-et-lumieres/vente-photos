<?php
class cwpai_AccessArray {
	protected $array;

	public function __construct($array) {
		$this->array = $array;
	}

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

	public function __construct($field, $label_text, $html_text) {
		$this->field = $field;
		$this->label_text = $label_text;
		$this->html_text = $html_text;
	}

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

// $cwpai_form_field = getInstance Cwpai_Form_Field($field, $label_text, $html_text);
// $form_fields = $cwpai_form_fields->get_form_fields();

class CWPAI_Attachment_Meta {
	public function __construct() {
		add_filter('attachment_fields_to_edit', array($this, 'cwpai_add_attachment_fields'), 10, 2);
		//add_action('admin_head', array($this, 'cwpai_custom_admin_css'));
	}

	 // public function cwpai_custom_admin_css() {
		// 	echo '<style>
		// 		.compat-field-cwpai_custom_field textarea {
		// 			height: 25vh;
		// 			width: 50%;
		// 		}
		// 	</style>';
		// }
	
	public function metas_utiles($metadonnees){
				$metas_temp = new cwpai_AccessArray($metadonnees);
				$metas_utiles = array (
					'caption' => $metas_temp->getItemKey('caption'),
					'copyright'=> $metas_temp->getItemKey('copyright'),
					'title' => $metas_temp->getItemKey('title'),
				);
				return $metas_utiles;
		}



	
	public function cwpai_add_attachment_fields($form_fields, $post) {
			// Récupérez les métadonnées
			$metas = wp_get_attachment_metadata($post->ID);
			$metas = $metas['image_meta'];
			
			$metas_utiles = $this -> metas_utiles($metas);
			// Convertir le tableau en une chaîne formatée
			$meta_string = print_r($metas_utiles, true);
			
			$label_text = 'attachment metas';
			$html_text = '<pre>';
			
			$form = new Cwpai_Form_Fields($meta_string, $label_text, $html_text);
			$form_fields = $form->get_form_fields();
			
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

	public function Adobe_Xmp_Utiles($xmp){
		$xmp_temp = new cwpai_AccessArray($xmp);
		
		$xmp_utiles = array(
			 'Label' => $xmp_temp ->getItemKey('Label') ,
			 'Credit' => $xmp_temp ->getItemKey('Credit'),
			 'City' => $xmp_temp ->getItemKey('City'),
			'Country' => $xmp_temp ->getItemKey('Country'),
			'State' => $xmp_temp ->getItemKey('State'),
			'Location' => $xmp_temp ->getItemKey('Location'),
			'Title' => $xmp_temp ->getItemKey('Title')[0],
			'Description' => $xmp_temp ->getItemKey('Description')[0],
			'Creator' => $xmp_temp ->getItemKey('Creator')[0],
			'Rights' => $xmp_temp ->getItemKey('Rights')[0],
		);
		return $xmp_utiles;
	}



	public function display_adobe_xmp($form_fields, $post) {
		// Get the XMP data for the attachment
		$xmp_data = $this->adobeXMP->get_xmp($post->ID);
		
		$xmp = $this -> Adobe_Xmp_Utiles($xmp_data);
	
		if(!empty($xmp)) {
			
			
			
			
			
			
			$form_fields['xmp_data'] = array(
				'label' => 'XMP Data',
				'input' => 'html',
				'html' => '<pre>' . print_r($xmp, true) . '</pre>',
				'helps' => 'Adobe XMP Data'
			);
		}

		return $form_fields;
	}
}

// Instantiate the DisplayAdobeXMP class
$displayAdobeXMP = new DisplayAdobeXMP();



class cwpai_ExifDataDisplay {
  public function __construct() {
		add_filter('attachment_fields_to_edit', array($this, 'cwpai_add_exif'), 10, 2);
	 }


	public function Exifs_Utiles($exifs){
		$exif_temp = new cwpai_AccessArray($exifs);
		
		$exif_utiles = array(
			'Copyright' => $exif_temp ->getItemKey('COMPUTED')['Copyright'],
			'ImageDescription' => $exif_temp ->getItemKey('IFD0')['ImageDescription'],
			'Artist' => $exif_temp ->getItemKey('IFD0')['Artist'],
			'Copyright' => $exif_temp ->getItemKey('IFD0')['Copyright'],
			'FileName' => $exif_temp ->getItemKey('FILE')['FileName'],
			'GPS' => $exif_temp ->getItemKey('GPS'),
		);
		 return $exif_utiles;
	 }



	 public function cwpai_add_exif($form_fields, $post) {
		$file = get_attached_file($post->ID);
		$exif = exif_read_data($file, 'ANY_TAG', true);
	
		$exifs_utiles = $this -> Exifs_Utiles($exif);
	
		if ($exif) {
			$form_fields['exif_utiles'] = array(
				'value' => print_r($exifs_utiles, true),
				'label' => __('Exif Data', 'codewp'),
				'input' => 'html',
				'html' => '<pre>' . print_r($exifs_utiles, true) . '</pre>',
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


class cwpai_CompareAndMerge {
	private $cwpai_meta;
	private $cwpai_adobe;
	private $cwpai_exif;

	public function __construct() {
		$this->cwpai_meta = new CWPAI_Attachment_Meta();
		$this->cwpai_adobe = new DisplayAdobeXMP();
		$this->cwpai_exif = new cwpai_ExifDataDisplay();
		add_filter('attachment_fields_to_edit', array($this, 'add_fields_to_attachment_edit'), 10, 2);
	}

	public function merge_meta_adobe_exif($metadonnees, $xmp, $exifs) {
		$meta_array = $this->cwpai_meta->metas_utiles($metadonnees);
		$adobe_array = $this->cwpai_adobe->Adobe_Xmp_Utiles($xmp);
		$exif_array = $this->cwpai_exif->Exifs_Utiles($exifs);

		return $this->compare_and_merge($meta_array, $adobe_array, $exif_array);
	}

	public function compare_and_merge($array1, $array2, $array3) {
		$merged = array();
		$arrays = array($array1, $array2, $array3);

		foreach ($arrays as $array) {
			foreach ($array as $key => $value) {
				if (!isset($merged[$key])) {
					$merged[$key] = $value;
				} else {
					$new_key = $key . '2';
					$merged[$new_key] = $value;
				}
			}
		}

		return $merged;
	}


	public function add_fields_to_attachment_edit($form_fields, $post) {
				$merged_array = $this->compare_and_merge($meta_array, $adobe_array, $exif_array); // utiliser merge_meta_adobe_exif  ?????
		
				foreach($merged_array as $key => $value) {
					$form_fields[$key] = array(
						'label' => $key,
						'input' => 'text',
						'value' => $value
					);
				}
		
				return $form_fields;
		}
	}
	
$cwpai_CompareAndMerge = new cwpai_CompareAndMerge();
	







