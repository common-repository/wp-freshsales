<?php
/**
 * WP Freshsales plugin file.
 *
 * Copyright (C) 2010-2020, Smackcoders Inc - info@smackcoders.com
 */

if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly

include_once(SM_LB_FSALES_DIR.'lib/FreshsalesAnalytics.php');

class mainCrmHelper {

	public $domain = null;

	public $auth_token = null;

	public $username = null;

	public $password = null;

	public $result_emails;

	public $result_ids;

	public $result_products;

	public function __construct() {
		$WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
		$activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
		$get_freshsales_settings_info = get_option("wp_{$activateplugin}_settings");
		$this->auth_token = $get_freshsales_settings_info['auth_token'];
		$this->domain = $get_freshsales_settings_info['domain_url'];
		$this->username = $get_freshsales_settings_info['username'];
		$this->password = $get_freshsales_settings_info['password'];
	}

	public function testLogin( $domain_url , $login, $password )
	{
		$domain_url = $domain_url . '/crm/sales/api/sign_in';
		$auth_key = 'Basic ' . base64_encode( "$login:$password");
		$headers = array( 'Authorization' => $auth_key , 
							'Content-Type' => 'application/json'
						);
        	$args = array(
			'method' => 'POST',
			'sslverify' => false,
			'headers' => $headers
		);

		$result =  wp_remote_post($domain_url, $args ) ;
		$response = wp_remote_retrieve_body($result);
		return $response;
	}

	public function getCrmFields($module) {
		#Fetch all fields based on the module
		$url = $this->domain . '/crm/sales/api/settings/' . strtolower($module) . '/fields';
		$args = array(
			'headers' => array(
            	'Content-type' => 'application/json',
            	'Authorization' => 'Token token='.$this->auth_token
         	)
	    );
		$body =  wp_remote_get($url, $args );
		$response = wp_remote_retrieve_body($body);
		$http_status = wp_remote_retrieve_response_code($body);

		if ($http_status != 200){
	
		}
		$fieldsArray = json_decode($response);
		$config_fields = array();
		if(!empty($fieldsArray)) {
			$i = 0;
			foreach ( $fieldsArray->fields as $item => $fieldInfo ) {
				if($fieldInfo->required == 1) {
					$config_fields['fields'][$i]['wp_mandatory'] = 1;
					$config_fields['fields'][$i]['mandatory'] = 2;
				} else {
					$config_fields['fields'][$i]['wp_mandatory'] = 0;
					$config_fields['fields'][$i]['mandatory'] = 0;
				}
				if($fieldInfo->type == 'dropdown') {
					$optionindex = 0;
					$picklistValues = array();
					foreach($fieldInfo->choices as $option)
					{
						$picklistValues[$optionindex]['id'] = $option->id;
						$picklistValues[$optionindex]['label'] = $option->value;
						$picklistValues[$optionindex]['value'] = $option->value;
						$optionindex++;
					}
					$config_fields['fields'][$i]['type'] = Array ( 'name' => 'picklist', 'picklistValues' => $picklistValues );
				} elseif($fieldInfo->type == 'checkbox') {
					$config_fields['fields'][$i]['type'] = array("name" => 'boolean');
				} elseif($fieldInfo->type == 'number') {
					$config_fields['fields'][$i]['type'] = array("name" => 'integer');
				} else {
					$config_fields['fields'][$i]['type'] = array("name" => $fieldInfo->type);
				}
				if($fieldInfo->base_model == 'LeadCompany') {
					$field_name = 'company_' . $fieldInfo->name;
				} elseif($fieldInfo->base_model == 'LeadDeal') {
					$field_name = 'deal_' . $fieldInfo->name;
				} else {
					$field_name = $fieldInfo->name;
				}
				$config_fields['fields'][$i]['name'] = str_replace(" " , "_", $field_name);
				$config_fields['fields'][$i]['fieldname'] = $field_name;
				$config_fields['fields'][$i]['label'] = $fieldInfo->label;
				$config_fields['fields'][$i]['display_label'] = $fieldInfo->label;
				$config_fields['fields'][$i]['publish'] = 1;
				$config_fields['fields'][$i]['order'] = $fieldInfo->position;
				$config_fields['fields'][$i]['base_model'] = $fieldInfo->base_model;
				$i++;
			}
			$config_fields['check_duplicate'] = 0;
			$config_fields['isWidget'] = 0;
			$users_list = $this->getUsersList();
			$config_fields['assignedto'] = $users_list['id'][0];
			$config_fields['module'] = $module;
			return $config_fields;
		}
	}

	public function getUsersList($module = 'users') {
		$url = $this->domain . '/crm/sales/settings/' . strtolower($module);
		$args = array(
			'headers' => array(
            	'Content-type' => 'application/json',
            	'Authorization' => 'Token token='.$this->auth_token
         	)
	    );
		$body =  wp_remote_get($url, $args );
		$response = wp_remote_retrieve_body($body);
		$http_status = wp_remote_retrieve_response_code($body);
		if ($http_status != 200){
		
		}
		$userInfo = json_decode($response);
		$user_details = array();
		foreach($userInfo->users as $data) {
			$user_details['user_name'][] = $data->email;
			$user_details['id'][] = $data->id;
			$user_details['first_name'][] = '';
			$user_details['last_name'][] = $data->display_name;
		}
		return $user_details;
	}

	public function duplicateCheckEmailField()
	{
		return "email";
	}

	public function assignedToFieldId()
	{
		return "owner_id";
	}

	public function checkEmailPresent( $module , $email )
	{
		$module = strtolower($module);
		$result_emails = array();
		$result_lastnames=[];
		$result_ids = array();
		if($module == 'leads') {
			$search_filter = 'filtered_search/lead';
			$postArray = json_encode (array(
				'filter_rule' => array(
						array(
							'attribute' => 'lead_email.email',
							'operator'  => 'is_in',
							'value'     => $email,
						)
					)
				));
		} else if($module == 'contacts') {
			$search_filter = 'filtered_search/contact';
			$postArray = json_encode (array(
				'filter_rule' => array(
						array(
							'attribute' => 'contact_email.email',
							'operator'  => 'is_in',
							'value'     => $email,
						)
					)
				));
		}
		$url = $this->domain . '/crm/sales/api/' . $search_filter;
		
		$args = array(
			'headers' => array(
            	'Content-type' => 'application/json',
            	'Authorization' => 'Token token='.$this->auth_token
         	),
         	'body' => $postArray
	    );
		$body =  wp_remote_post($url, $args );
		$response = wp_remote_retrieve_body($body);
		$http_status = wp_remote_retrieve_response_code($body);
		if ($http_status != 200){
		
		}
		$records = json_decode($response);
		$email_present = "no";
		if( $records->meta->total >= 0 ) {
			$result_lastnames[] = isset($records->{$module}[0]) ? $records->{$module}[0]->display_name : ""; //"Last Name";
			$result_emails[] = isset($records->{$module}[0]) ? $records->{$module}[0]->email : "";
			$result_ids[] = isset($records->{$module}[0]) ? $records->{$module}[0]->id : "";
			if(isset($records->{$module}[0])) {
			if($email == $records->{$module}[0]->email)
				$email_present = "yes";}
		}
		$this->result_emails = $result_emails;
		$this->result_ids = $result_ids;
		if($email_present == 'yes')
			return true;
		else
			return false;
	}

	public function mapUserCaptureFields( $user_firstname , $user_lastname , $user_email )
	{
		$post = array();
		$post['first_name'] = $user_firstname;
		$post['last_name'] = $user_lastname;
		$post[$this->duplicateCheckEmailField()] = $user_email;
		return $post;
	}

	public function createRecordOnUserCapture( $module , $module_fields )
	{
		return $this->createRecord( $module , $module_fields );
	}

	public function createRecord($module, $lead_info )
	{
		$module='deals';
		$data=[];
		$module = strtolower($module);
		$url = $this->domain . '/crm/sales/api/' . $module;
		//$url = 'https://smack-380973024421372799.myfreshworks.com/crm/sales/api/contacts';
		// $auth_string = "$this->username:$this->password";
		if($module == 'deals') {
			$index = 'deal';
		} elseif ($module == 'contacts') {
			$index = 'contact';
		}
		$data_array = array();
		foreach($lead_info as $key => $val) {
			if(strpos($key, 'company_') !== false) {
				$key = str_replace('company_', '', $key);
				$data_array[$index]['company'][$key] = $val;
			} elseif(strpos($key, 'deal_') !== false) {
				if($key === 'deal_deal_product_id') {
					$key = 'deal_product_id';
				} else {
					$key = str_replace( 'deal_', '', $key );
				}
				$data_array[$index]['deal'][$key] = $val;
			}
			elseif(strpos($key,'emails')!== false) {
				$data_array[$index]['email'] = $val;
			}
			elseif(strpos($key,'phone_numbers')!== false) {
				
				unset($key);
			}
			elseif(strpos($key,'submitcontactformwidget')!== false) {
				
				unset($key);
			}
			elseif(strpos($key,'owner_id')!== false) {
				
				unset($key);
			}
			
			else {
				$data_array[$index][ $key ] = $val;
			}
		}
		$data_array = json_encode($data_array['deal']);
		$args = array(
			'headers' => array(
				'Authorization' => 'Token token='.$this->auth_token,
				'Content-Type' => 'application/json'
         	),
         	'body' => $data_array
	    );
		$body =  wp_remote_post($url, $args );
		$response = wp_remote_retrieve_body($body);
		$http_status = wp_remote_retrieve_response_code($body);

		if ($http_status != 200){
		
		}
		$records = json_decode($response);
		if(isset($records->{$index}->id)) {
			$data['result'] = "success";
			$data['failure'] = 0;
		} else {
			$data['result'] = "failure";
			$data['failure'] = 1;
			$data['reason'] = "Freshsales encountered an error. CODE: " . $http_status . " Response: " . $response; #"failed adding entry";
		}
		return $data;
	}

	public function updateEmailPresentRecord( $module , $contact_id , $contact_info)
	{
		$module = strtolower($module);
                $url = $this->domain . '/crm/sales/api/' . $module . '/' . $contact_id;
                // $auth_string = "$this->username:$this->password";
                if($module == 'leads') {
                        $index = 'lead';
                } elseif ($module == 'contacts') {
                        $index = 'contact';
                }
                $data_array = $contact_info;
                $args = array(
					'headers' => array(
		            	'Content-type' => 'application/json',
		            	'Authorization' => 'Token token='.$this->auth_token
		         	),
		         	'body' => $data_array
			    );
				$body =  wp_remote_post($url, $args );
				$response = wp_remote_retrieve_body($body);
                $records = json_decode($response);
		return $records;
	}
}
