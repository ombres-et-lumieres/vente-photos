<?php
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
 
 
	 /**
		 méthode pour comparer et assembler trois tableaux
		*/
	 
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
	 /**
	  recherches tous les métas d' un attachment et assezmble les tableaux
	*/
	 public function find_all_metas_utiles($post){
		 $metas_utiles = $this->cwpai_meta -> cwpai_find_attachment_fields($post);
		 $xmp_utiles = $this->cwpai_adobe -> find_adobe_xmp($post);
		 $exifs_utiles = $this->cwpai_exif -> cwpai_find_exif($post);
		 
		 return $this->compare_and_merge($metas_utiles, $xmp_utiles, $exifs_utiles);
	 }
	 
	public function print_all_metas_utiles($form_fields, $post){
		 $merged_array = $this->find_all_metas_utiles($post); // utiliser merge_meta_adobe_exif  ?????
		 
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
	
