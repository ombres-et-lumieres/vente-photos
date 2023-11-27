<?php
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


class Cwpai_GPS_Processor {
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