<?php
/**
 * WP Freshsales plugin file.
 *
 * Copyright (C) 2010-2020, Smackcoders Inc - info@smackcoders.com
 */

if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly


class FsalesSmLBAdmin {

	public function __construct() {
    
        }

  public function setEventObj()
  {
    $obj = new mainCrmHelper();
    return $obj;
  }
  
	public function user_module_mapping_view() {
		include ('views/form-usermodulemapping.php');
	}

	public function mail_sourcing_view() {
		include('views/form-campaign.php');
	}

	public function new_lead_view() {
	       global $lb_crm;
               include ('views/form-managefields.php');
        }

	public function new_contact_view() {
               global $lb_crm;
               $module = "Contacts";
               $lb_crm->setModule($module);
               include ('views/form-managefields.php');
        }


	public function show_form_crm_forms() {
               include ('views/form-crmforms.php');
        }


	public function show_freshsales_crm_config() {
               include ('views/form-freshsalescrmconfig.php');
        }
      

  public function freshsalesSettings( $configData ) {
    $freshsales_config_array = $configData['REQUEST'];
    $fieldNames = array(
      'username' => __('Freshsales Username' , SM_LB_URL ),
      'password' => __('Freshsales Password' , SM_LB_URL ),
      'domain_url' => __('Freshsales Domain URL' , SM_LB_URL ),
      'smack_email' => __('Smack Email' , SM_LB_URL ),
      'email' => __('Email id' , SM_LB_URL ),
      'emailcondition' => __('Emailcondition' , SM_LB_URL ),
      'debugmode' => __('Debug Mode' , SM_LB_URL ),
    );

    foreach ($fieldNames as $field=>$value){
      $config=[];
      $result=[];
      if(isset($freshsales_config_array[$field]))
      {
        $config[$field] = trim($freshsales_config_array[$field]);
      }
    }
    require_once(SM_LB_FSALES_DIR. "includes/freshsalesFunctions.php");
    $FunctionsObj = new mainCrmHelper();
    $testlogin_result = $FunctionsObj->testLogin( $freshsales_config_array['domain_url'] ,$freshsales_config_array['username'], $freshsales_config_array['password'] );
    $check_is_valid_login = json_decode($testlogin_result);
    if(isset($check_is_valid_login->login) && $check_is_valid_login->login == 'success') {
      $successresult = "Settings Saved ";
      $result['error'] = 0;
      $result['success'] = $successresult;
      $WPCapture_includes_helper_Obj = new WPCapture_includes_helper_PRO();
      $activateplugin = $WPCapture_includes_helper_Obj->ActivatedPlugin;
      $config['auth_token'] = $check_is_valid_login->auth_token;
      update_option("wp_{$activateplugin}_settings", $config);
    }
    else
    {
      $freshsales_crm_config_error = "Please Verify your Freshsales Credentials";

      $result['error'] = 1;
      $result['errormsg'] = $freshsales_crm_config_error ;
      $result['success'] = 0;
    }
    return $result;
  }

	
}

global $lb_crm;
$lb_crm = new FsalesSmLBAdmin();
