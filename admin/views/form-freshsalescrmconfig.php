<?php
/**
 * WP Freshsales plugin file.
 *
 * Copyright (C) 2010-2020, Smackcoders Inc - info@smackcoders.com
 */

if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly
global $wpdb;
$active_plugin = get_option('WpLeadBuilderProActivatedPlugin');

$allowed_html = ['div' => ['class' => true, 'id' => true, 'style' => true, ], 
	'a' => ['id' => true, 'href' => true, 'title' => true, 'target' => true, 'class' => true, 'style' => true, 'onclick' => true,], 
	'strong' => [], 
	'i' => ['id' => true, 'onclick' => true, 'style' => true, 'class' => true, 'aria-hidden' => true, 'title' => true ], 
	'p' => ['style' => true, 'name' => true, 'id' => true, ], 
	'img' => ['id' => true, 'style' => true, 'class' => true, 'src' => true, 'align' => true, 'width' => true, 'height' => true, 'border' => true, ], 
	'table' => ['id' => true, 'class' => true, 'style' => true, 'height' => true, 'cellspacing' => true, 'cellpadding' => true, 'border' => true, 'width' => true, 'align' => true, 'background' => true, 'frame' => true, 'rules' => true, ], 
	'tbody' => [], 
	'br' => ['bogus' => true, ], 
	'tr' => ['id' => true, 'class' => true, 'style' => true, ], 
	'th' => ['id' => true, 'class' => true, 'style' => true, ], 
	'hr' => ['id' => true, 'class' => true, 'style' => true,], 
	'h3' => ['style' => true, ], 
	'td' => ['style' => true, 'id' => true, 'align' => true, 'width' => true, 'valign' => true, 'class' => true, 'colspan' => true, ], 
	'span' => ['style' => true, 'class' => true, ], 
	'h1' => ['style' => true, ], 
	'thead' => [], 
	'tfoot' => ['id' => true, 'style' => true, ], 
	'figcaption' => ['id' => true, 'style' => true, ], 
	'h4' => ['id' => true, 'align' => true, 'style' => true, ],
	'h2' => ['id' => true, 'align' => true, 'style' => true, 'class' => true],
	'script' => [],
	'select' => ['id' => true, 'name' => true, 'class' => true, 'data-size' =>true, 'data-live-search' =>true, 'onchange' => true],
	'option' => ['value' => true, 'selected' => true],
	'label' =>['id' => true, 'class' =>true],
	'input' => ['type' => true, 'value' => true, 'id' => true, 'name' => true, 'class' => true, 'onclick' => true],
	'form' => ['method' => true, 'name' => true, 'id' => true, 'action' => true]];


//Check Shortcode available
$check_shortcode = $wpdb->get_results( $wpdb->prepare("select shortcode_name from wp_smackleadbulider_shortcode_manager where crm_type=%s", $active_plugin));
$check_field_manager = $wpdb->get_results( $wpdb->prepare("select field_name from wp_smackleadbulider_field_manager where crm_type=%s", $active_plugin));
$count_shortcode=0;
$count_shortcode = count($check_shortcode);
   if( !empty( $check_field_manager)){
         if( $count_shortcode>1 ){
                $shortcode_available = 'yes';
        }else{
                $shortcode_available = 'no';
        }
}else{
        $shortcode_available = 'yes';
}
$content = "<input type='hidden' id='check_shortcode_availability' value='$shortcode_available'>";
$content .= "<input type='hidden' id='count_shortcode' value='$count_shortcode'>";
echo wp_kses($content, $allowed_html);
// echo "<input type='hidden' id='check_shortcode_availability' value='$shortcode_available'>";
// echo "<input type='hidden' id='count_shortcode' value='$count_shortcode'>";
//END

$config = get_option("wp_{$active_plugin}_settings");

if( $config == "" )
{
        $config_data = 'no';
}
else
{
        $config_data = 'yes';
}

$siteurl = site_url();
?>

<div class="clearfix"></div> 
    
    <div style="display:flex;" >
    <div class="panel" id="panel" style="width:80%;background-color:white">
          <div class="panel-body">
          <div class="form-group ">    
				<div class="col-md-8">
				<div class="crm_head">

	            <h3>Freshsales CRM</h3>
                </div>
				</div>
				<div class="col-md-4" id="crm_select_dropdown">  
            <label id="inneroptions" class="leads-builder-crm" ><?php echo esc_html__('Which CRM do you use?', 'wp-leads-builder-any-crm' ); ?></label>
            <?php $ContactFormPluginsObj = new ContactFormPROPlugins();echo wp_kses($ContactFormPluginsObj->getPluginActivationHtml(),$allowed_html);
            ?>
        </div>
          </div>

              <input type="hidden" id="get_config" value="<?php echo esc_attr($config_data) ?>" >
              <span id="save_config" style="font:14px;width:200px;"></span>
              <input type="hidden" id="get_config" value="<?php echo esc_attr($config_data) ?>" >
              <input type="hidden" id="revert_old_crm_pro" value="<?php echo esc_attr($active_plugin) ?>">
            
              <form id="smack-salesforce-settings-form" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post">
                <?php wp_nonce_field('sm-leads-builder'); ?>
                <input type="hidden" name="smack-salesforce-settings-form" value="smack-salesforce-settings-form" />
                <input type="hidden" id="plug_URL" value="<?php echo esc_url(SM_LB_URL);?>" />
  
                <div class="clearfix"></div>
                  <hr>
                  <div class="mt30"> 
                      <div class="form-group col-md-12">  
                          <label id="inneroptions" style="margin-top:20px"class="leads-builder-heading">Configure your Freshsales CRM settings below!
                          </label>
                      </div>
                  </div>
                <div class="clearfix"></div>
                  <div class="mt20">  
                    <div class="form-group col-md-12">
                        <div class="col-md-2 label-space">
                          <label id="innertext" class="leads-builder-label"> <?php echo esc_html__('Domain URL', 'wp-leads-builder-any-crm' ); ?> </label>
                        </div>
                        <div class="col-md-8">   
                            <input type='text' style="border-radius: 7px;border-color:#9b9797" class='smack-vtiger-settings form-control'  name='domain_url' id='domain_url' value="<?php echo esc_url(isset($config['domain_url'])?$config['domain_url']:'') ?>"onmouseover="this.style.borderColor='#1caf9a'" onmouseout="this.style.borderColor='#9b9797'" />
                        </div>    
                    </div>
                    <div class="form-group col-md-12" style="margin-top:10px">
                      <div class="col-md-2 label-space">
                          <label id="innertext" class="leads-builder-label"> <?php echo esc_attr__('Username', 'wp-leads-builder-any-crm' ); ?>  </label>
                      </div>
                      <div class="col-md-3">   
                          <input type='text' style="border-radius: 7px;border-color:#9b9797" class='smack-vtiger-settings form-control' name='username' id='username' value="<?php echo esc_attr(isset($config['username'])?$config['username']:'') ?>"onmouseover="this.style.borderColor='#1caf9a'" onmouseout="this.style.borderColor='#9b9797'" />
                      </div> 
                      <div class="col-md-2 label-space">
                          <label id="innertext" class="leads-builder-label"> <?php echo esc_attr__('Password', 'wp-leads-builder-any-crm' ); ?>  </label>
                      </div>
                      <div class="col-md-3">   
                          <input type='password' style="border-radius: 7px;border-color:#9b9797" class='smack-vtiger-settings form-control' name='password' id='password' value="<?php echo esc_attr(isset($config['password'])?$config['password']:'') ?>"onmouseover="this.style.borderColor='#1caf9a'" onmouseout="this.style.borderColor='#9b9797'" />
                      </div>      
                    </div>
                  </div>
                  <input type="hidden" id="posted" name="posted" value="<?php echo esc_attr('posted');?>">
                  <input type="hidden" id="site_url" name="site_url" value="<?php echo esc_url($siteurl) ;?>">
                  <input type="hidden" id="active_plugin" name="active_plugin" value="<?php echo esc_attr($active_plugin); ?>">
                  <input type="hidden" id="leads_fields_tmp" name="leads_fields_tmp" value="smack_freshsales_leads_fields-tmp">
                  <input type="hidden" id="contact_fields_tmp" name="contact_fields_tmp" value="smack_freshsales_contacts_fields-tmp">
                  <div class="pull-right1">            		
                      <span>
                          <input type="button" class="save_config_button" id="Save_crm_config" value="<?php echo esc_attr__('Save CRM Configuration' , 'wp-leads-builder-any-crm' );?>" id="save"  class="smack-btn smack-btn-primary btn-radius" onclick="saveCRMConfiguration(this.id);" />
                      </span> 
                  </div>			
              </form>
              <div id="loading-sync" style="display: none; background:url(<?php echo esc_url(plugins_url('assets/images/ajax-loaders.gif',dirname(__FILE__,2)));?>) no-repeat center"><?php echo esc_html__('' , 'wp-leads-builder-any-crm' ); ?></div>
                <div id="loading-image" style="display: none; background:url(<?php echo esc_url(plugins_url('assets/images/ajax-loaders.gif',dirname(__FILE__,2)));?>) no-repeat center"><?php echo esc_html__('' , 'wp-leads-builder-any-crm' ); ?></div>
                </div>
              </div>
          <div class="card" >
            <h2 class="title2" style="font-size:medium;font-weight:bold">WP Leads Builder for CRM PRO*</h2>
            <hr class="divider"/>
                <b style="font-size: small;font-style: italic;color:#1caf9a">* Use your favorite CRM</b>
                <p style="padding-left: 11%;">Works with JoForce CRM, Zoho CRM, Vtiger CRM, Salesforce CRM, Freshsales, Zoho CRM Plus,SugarCRM and SuiteCRM</p>
                <b style="font-size: small;font-style: italic;color:#1caf9a">* Create New Form or Use Existing Form</b>
                <div style="padding-left: 11%;"><p>Integrate the existing Contact Form 7, Gravity Form, Ninja Form & our default forms to build CRM Leads/Contacts</p></div>
                <b style="font-size: small;font-style: italic;color:#1caf9a">* Bring all your WordPress users</b> 
                <div style="padding-left: 11%;"><p>Capture the WordPress users as Leads or Contacts into the CRM</p></div>
                <b style="font-size: small;font-style: italic;color:#1caf9a">* Integrate with WooCommerce</b> 
                <div style="padding-left: 11%;"><p>Capture the failed order customer information as Leads and successful order customer details as Contacts into the CRM</p></div>
              <a class="cus-button-1" href="https://www.smackcoders.com/wp-leads-builder-any-crm-pro.html?utm_source=plugin&utm_campaign=promo_widget&utm_medium=pro_edition" target="blank">Buy Now!</a>
          </div>
</div>  
<div class="container" style="width:100%;">	
    <div class="modal fade" id="smack_confirm_modal" style='margin-top:1%;display:none'>
        <div id="overlay"></div>
        <div class="modal-dialog">
            <!-- Modal content-->
            <form class="modal-content" style='width:525px;'>
                <div class="modal-body">
                    <h5><b><center class='popup_content'>Switching to another CRM Will make Your Old ShortCodes Disabled</center></b></h5>
                    <br/>
                    <!-- <div class="delete-butrons" style="float:right"> -->
                    <button  type="button" onclick="document.getElementById('smack_confirm_modal').style.display='none'" class="popup_cancel_button"><span>Cancel</span></button>
                    <button  type="button" id="confirmnow" onclick='changecrm();' class="popup_confirm_button"><span>Confirm</span></button>
                    <!-- </div> -->
                </div>
            </form>
        </div>