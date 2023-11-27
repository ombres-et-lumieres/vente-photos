<?php
 /**
 récupère un tableau dans un tableau
   */

class cwpai_AccessArray {
	protected $array;

	public function __construct($array) {
		$this->array = $array;
	}
 /**
	  récupère un élément via une clef
	   */
	public function getItemKey($key) {
		if (isset($this->array[$key])) {
			return $this->array[$key];
		} else {
			throw new Exception("L'élément $key n'existe pas dans le tableau.");
		}
	}
}

 /**
impression via $form_fields
   */
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

 /**
 	classe qui recherches les métas d' un attachment
   */

class CWPAI_Attachment_Meta {
	public function __construct() {
		add_filter('attachment_fields_to_edit', array($this, 'cwpai_add_attachment_fields'), 10, 2);
		
	}

	/**
		 méthode qui recherches les métas utiles d' un attachment
	   */
	public function cwpai_find_attachment_fields($post) {
					// Récupérez les métadonnées
					$metas = wp_get_attachment_metadata($post->ID);
					$metas = $metas['image_meta'];
					
					$metas_utiles = $this -> metas_utiles($metas);
					
					return $metas_utiles;
				}

	public function metas_utiles($metadonnées){
					$metas_temp = new cwpai_AccessArray($metadonnées);
					$metas_utiles = array (
						'caption' => $metas_temp->getItemKey('caption'),
						'copyright'=> $metas_temp->getItemKey('copyright'),
						'title' => $metas_temp->getItemKey('title'),
					);
					return $metas_utiles;
				}

	
	public function cwpai_add_attachment_fields($form_fields, $post) {
			// Récupérez les métadonnées
			
			
			$metas_utiles = $this -> cwpai_find_attachment_fields($post);
			// Convertir le tableau en une chaîne formatée
		//	$meta_string = print_r($metas_utiles, true);
			
		//	$label_text = 'attachment metas';
		//	$html_text = '<pre>';
			
		//	$form = new Cwpai_Form_Fields($meta_string, $label_text, $html_text);
		//	$form_fields = $form->get_form_fields();
			
			return $form_fields;
		}
}

new CWPAI_Attachment_Meta();

 /**
	 classe qui recherches lesXMP d' adobe
	 nécessite le plugin Adobexmp
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
		//add_filter('attachment_fields_to_edit', array($this, 'display_adobe_xmp'), 10, 2);
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
    	/**
		 méthode qui recherche les xmp utiles
	   */
	public function find_adobe_xmp($post) {
		// Get the XMP data for the attachment
		$xmp_data = $this->adobeXMP->get_xmp($post->ID);
		
		$xmp_utiles = $this -> Adobe_Xmp_Utiles($xmp_data);
		
		return $xmp_utiles;
		
	}

// 	public function display_adobe_xmp($form_fields, $post) {
// 		// Get the XMP data for the attachment
// 		$xmp_utiles = $this -> find_adobe_xmp($post);
// 	
// 		if(!empty($xmp_utiles)) {
// 
// 			$form_fields['xmp_data'] = array(
// 				'label' => 'XMP Data',
// 				'input' => 'html',
// 				'html' => '<pre>' . print_r($xmp_utiles, true) . '</pre>',
// 				'helps' => 'Adobe XMP Data'
// 			);
// 		}
// 
// 		return $form_fields;
// 	}
}

// Instantiate the DisplayAdobeXMP class
$displayAdobeXMP = new DisplayAdobeXMP();

 /**
	 classe qui recherches les exifs d' un fichier photo
   */

class cwpai_ExifDataDisplay {
  public function __construct() {
	//	add_filter('attachment_fields_to_edit', array($this, 'cwpai_add_exif'), 10, 2);
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
	 /**
		 méthode qui recherche les exifs utiles
		*/
	 public function cwpai_find_exif($post) {
			 $file = get_attached_file($post->ID);
			 $exif = exif_read_data($file, 'ANY_TAG', true);
		 
			 $exifs_utiles = $this -> Exifs_Utiles($exif);
		 
			 return $exifs_utiles;
	   }

	//  public function cwpai_add_exif($form_fields, $post) {
 // 
	// 	$exifs_utiles = $this -> cwpai_find_exif($post);
	// 
	// 	if ($exifs_utiles) {
	// 		$form_fields['exif_utiles'] = array(
	// 			'value' => print_r($exifs_utiles, true),
	// 			'label' => __('Exif Data', 'codewp'),
	// 			'input' => 'html',
	// 			'html' => '<pre>' . print_r($exifs_utiles, true) . '</pre>',
	// 		  );
	// 		}
	// 		
	// 	
	// 
	// 	return $form_fields;
 //  }

  // public function cwpai_get_readable_gps_data($gps_data) {
	// 	if (!$gps_data) {
	// 	  return 'No GPS data found';
	// 	}
  // 
	// 	$lat_ref = $gps_data['GPSLatitudeRef'];
	// 	$long_ref = $gps_data['GPSLongitudeRef'];
	//   
	// 	$lat = $this->cwpai_convert_to_decimal($gps_data['GPSLatitude']);
	// 	$long = $this->cwpai_convert_to_decimal($gps_data['GPSLongitude']);
	//   
	// 	return "Latitude: $lat $lat_ref, Longitude: $long $long_ref";
  // }
  // 
  // public function cwpai_convert_to_decimal($gps) {
	// 	if (!is_array($gps)) {
	// 	  return null;
	// 	}
  // 
	// 	$degrees = count($gps) > 0 ? $this->cwpai_gps_to_num($gps[0]) : 0;
	// 	$minutes = count($gps) > 1 ? $this->cwpai_gps_to_num($gps[1]) : 0;
	// 	$seconds = count($gps) > 2 ? $this->cwpai_gps_to_num($gps[2]) : 0;
	//   
	// 	// coordinate = degrees + (minutes / 60) + (seconds / 3600)
	// 	return $degrees + ($minutes / 60.0) + ($seconds / 3600.0);
  // }
  // 
  // public function cwpai_gps_to_num($coord_part) {
	// 	$parts = explode('/', $coord_part);
	//   
	// 	if (count($parts) <= 0)
	// 	  return 0;
	//   
	// 	if (count($parts) == 1)
	// 	  return $parts[0];
	//   
	// 	return floatval($parts[0]) / floatval($parts[1]);
  // }
}
new cwpai_ExifDataDisplay();


class cwpai_CompareAndMerge {

	private $attachment_meta;
	private $adobe_xmp;
	private $exif_data;

	public function __construct() {
		$this->attachment_meta = new CWPAI_Attachment_Meta();
		$this->adobe_xmp = new DisplayAdobeXMP();
		$this->exif_data = new cwpai_ExifDataDisplay();
		add_filter('attachment_fields_to_edit', array($this, 'print_all_metas_utiles'), 10, 2);
		add_action('admin_head', array($this, 'cwpai_custom_admin_css'));
	}

	public function retrieve_arrays($post) {
		$attachment_fields = $this->attachment_meta->cwpai_find_attachment_fields($post);
		$adobe_xmp_fields = $this->adobe_xmp->find_adobe_xmp($post);
		$exif_data_fields = $this->exif_data->cwpai_find_exif($post);

		return [$attachment_fields, $adobe_xmp_fields, $exif_data_fields];
	}

 		public function cwpai_custom_admin_css() {
				echo '<style>
				textarea {
					height: 10vh;
					width: 50%;
 				}
 				</style>';
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
	   
	public function sort_and_merge($post) {
		$arrays = $this->retrieve_arrays($post);
		$merged_array = array_merge($arrays[0], $arrays[1], $arrays[2]);

		asort($merged_array);
		return $merged_array;
	}
	
	public function print_all_metas_utiles($form_fields, $post){
		$array_sorted = $this -> sort_and_merge($post);
		
	  foreach($array_sorted as $key => $value) {
		  
		  if ($key == 'GPS') {
			$form_fields['gps'] = array(
			  'value' => $this->cwpai_get_readable_gps_data($value),
			  'label' => 'GPS',
			  'input' => 'textarea',
			);
		  }
		  else {
			$form_fields[$key] = array(
				  'label' => $key,
				  'input' => 'textarea',
				  'value' => $value
			  );
			  
		  }
	  }		 
	  return $form_fields;
	}
}

new cwpai_CompareAndMerge();



