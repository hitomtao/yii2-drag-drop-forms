<?php
namespace masihfathi\form;
use yii\helpers\ArrayHelper;

class FormBase {

/**
 * Filter fields array and retun only fields with attribute name (data fields)
 *
 * @param array $array Array represent elements of form
 * @return array Only fields with attribute name
*/
    public function onlyCorrectDataFields($array = []){
    	$data_fields = [];
    	foreach ($array as $key_row => $row) {

			foreach ($row as $key => $v) {
			
				$field = $array[$key_row][$key];
				
				if (ArrayHelper::keyExists('name', $field) ){
    				array_push($data_fields, $field);
    			}
			}
		}
		return $data_fields;
    }
    
    /**
     * Generate table schema need to create table
     *
     * Get array with data fields and 
     * @param array $array Data fields
     * @return array
    */
    public static function tableSchema($array = []){
    	$schema['id'] = 'pk';
    	
    	foreach ($array as $row) {
			foreach ($row as $v) {
			
				if ( isset($v['name']) ){
					$schema[ $v['name'] ] = ($v['field'] === 'textarea') ? 'text' : 'string';
				}
			}
		}
		return $schema;
    }

    /**
     * Return column type.
     *
     * Need to add column in table SQL
     *
     * @param array $field Data one field.
     * @return null|string 
    */ 
    public function getColumnType($field){

    	if (!ArrayHelper::keyExists('field', $field)){
    		return null;
    	}
    		$type = [
                'input' =>  [
		            'text' =>   'string',
		            'email' =>  'string',
		            'password' => 'string',
		            'date' =>   'string',
		            'number' => 'integer',
		            'url' =>    'string',
		            'tel' =>    'string',
		            'url' =>    'string',
		            'color' =>  'string',
		            'range' =>  'string',
		            'url' =>    'string',
				],
				'textarea' =>   'text',
				'checkbox' =>   'string',
				'radio' =>      'string',
				'select' =>     'string',
		    ];
    			
    			
    	if ($field['field'] === 'input'){
    		return $type[ $field['field'] ][ $field['type'] ];
    	} 

    	return $type[ $field['field'] ];
    	
    }		
    
    /**
     * Return validation rule type.
     * Need to dynamic model validation
     * @param array $f Data field.
     * @return null|array 
    */
    public function ruleType($f){
    	
    		$types = ['input' =>  [
								'text' => 'string',
								'email' => 'email',
								'password' => 'string',
								'date' => 'date',
								'number' => 'integer',
								'url' => 'url',
								'tel' => 'string',
								'color' => 'string',
								'range' => 'string',
							],
						'textarea' => 'string',
						'checkbox' => 'string',
						'radio' => 'string',
						'select' => 'string',
    			];
    			
    	if ($f['field'] === 'input'){
    		return $types[$f['field']][$f['type']];
    	} 
    	
		return $types[$f['field']];
    }
}
    
?>
