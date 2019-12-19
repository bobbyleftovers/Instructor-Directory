<?php
namespace BarrelDirectory\Classes\Admin;
/************************************************
 * Profile Editor
 * --------------
 * This class handles submissions and performs server-side validation
 * on edits made to user profiles. It also registers ACF 'save_post'
 * actions to initiate the save. This relies on acf_form() being used
 * as well as a module for ACF forms, which takes in acf_form() args
 * and spits out a front-end ACF form or form-fragment.
 */

use BarrelDirectory\Classes\Lib\Modules as Lib;
use BarrelDirectory\Classes\Db\Db_Control;
use BarrelDirectory\Classes\Acf\Acf_Mapping;
use WP_Error;

Class Profile_Editor {
  public function __construct ($user_id = null, $profile_id = null) {
    $this->entry_table = new Db_Control();
    $this->form_is_valid = false;
    $this->form_data = null;
    $this->user_id = $user_id;
    $this->profile_id = $profile_id;
    $this->db_row = false;

    if($this->user_id){
      $this->db_row = ($this->row_exists($this->$profile_id)) ? $this->entry_table->find($this->$profile_id) : false;
      $this->user_data = get_userdata( $this->user_id );
    }
  }

  public function register_actions () {
    add_action( 'acf/save_post', function ( $post_id ) {
      $this->form_data = $_POST;

      // save submitted data
      if( !is_admin() && (isset($_POST['_acf_screen']) && isset($_POST['user_id']) && isset($_POST['profile_id'])) ) {
        // save data for the profile editor
        $this->user_id = $_POST['user_id'];
        $this->profile_id = $_POST['profile_id'];
        $this->main_validation();

        if($this->form_is_valid) {
          $this->submit_form();
        }
      } else if ( !is_admin() && isset($_POST['acf']['field_5d13abed82433'])) {
        // save data for the location editor
        $this->profile_id = $_POST['_acf_post_id'];
        $this->user_id = null;
        $this->location_validation();

        if($this->form_is_valid) {
          $this->submit_form();
        }
      }
    }, 15 );

    // validate submitted data
    add_filter('acf/validate_save_post', function () {
      if(is_admin()) return;

      // clear all errors
      acf_reset_validation_errors();

      if( !is_admin() && ( (isset($_POST['user_id']) && isset($_POST['profile_id']))) || isset($_POST['_acf_post_id']) ) {
        $this->form_data = $_POST;
        $this->profile_id = (isset($_POST['profile_id'])) ? $_POST['profile_id'] : $_POST['_acf_post_id'];

        // validate data for the profile editor

        if(isset($_POST['acf']['field_5d13abed82433'])){
          $this->location_validation();
        } else {
          $this->main_validation();
        }
      }
    }, 10, 0);
  }

  public function location_validation () {
    $this->form_is_valid = true;
  }

  public function main_validation () {
    $this->form_is_valid = true;
    
    if (!empty($_POST['acf']['_validate_email'])) {
      acf_add_validation_error('', __('Spam Detected', 'acf'));
    }
    if (!$this->form_data['first_name']){
      // First name cannot be blank
      acf_add_validation_error( 'first_name', 'First name is required' );
    }
    if (!$this->form_data['last_name']){
      // last name cannot be blank
      acf_add_validation_error( 'last_name', 'Last Name is required');
    }
    if (!$this->form_data['email'] || !$this->is_email()){
      // email cannot be blank
      acf_add_validation_error( 'email', 'Email is required');
      $this->form_is_valid = false;
    }
    if ($this->form_data['phone'] && !$this->is_phone_number($this->form_data['phone'])){
      acf_add_validation_error( 'phone', 'Must be a 10-digit phone number');
      $this->form_is_valid = false;
    }
    if (!$this->form_data['certifications']){
      // Please choose at least one certification
      acf_add_validation_error( 'certifications', 'Please choose at least one certification level');
      $this->form_is_valid = false;
    }
    if (!$this->form_data['about']){
      // Please fill out the bio
      acf_add_validation_error( 'about', 'Description cannot be blank');
      $this->form_is_valid = false;
    }
    if ($this->form_data['website'] && !$this->is_url($this->form_data['website'])) {
      // URL is invlaid
      acf_add_validation_error( 'website', 'If adding a personal website, must be a valid URL');
      $this->form_is_valid = false;
    }
    if ($this->form_data['facebook'] && !$this->is_url($this->form_data['facebook'])) {
      // URL is invlaid
      acf_add_validation_error( 'facebook', 'If adding Facebook, must be a valid URL');
      $this->form_is_valid = false;
    }
    if ($this->form_data['instagram'] && !$this->is_url($this->form_data['instagram'])) {
      // URL is invlaid
      acf_add_validation_error( 'instagram', 'If adding Instagram, must be a valid URL');
      $this->form_is_valid = false;
    }
    if ($this->form_data['twitter'] && !$this->is_url($this->form_data['twitter'])) {
      // URL is invlaid
      acf_add_validation_error( 'twitter', 'If adding Twitter, must be a valid URL');
      $this->form_is_valid = false;
    }
    if ($this->form_data['youtube'] && !$this->is_url($this->form_data['youtube'])) {
      // URL is invlaid
      acf_add_validation_error( 'youtube', 'If adding YouTube, must be a valid URL');
      $this->form_is_valid = false;
    }

    // Use the field key for acf fields
    if ($this->form_data['acf']['field_5d13abed82433']){} // location jso n
    if ($this->form_data['acf']['field_5d0540a99290c']){} // profile imag e
    if ($this->form_data['acf']['field_5cfaf830b9af4']){} // studio s
  }

  public function submit_form () {
    // store all changes here for one update at the end
    $custom_db_data = [];

    // set user data and post data to keep in sync for user name and email
    if (($this->form_data['first_name'] !== $this->get_user_prop('first_name')) || ($this->form_data['last_name'] !== $this->get_user_prop('last_name'))) {
      $this->update_username();
      $custom_db_data['basic_info_first_name'] = $this->form_data['first_name'];
      $custom_db_data['basic_info_last_name'] = $this->form_data['last_name'];
    }
    if ($this->form_data['email'] !== $this->get_user_prop('user_email')) {
      $custom_db_data['contact_info_email'] = $this->form_data['email'];

      // update user data
      wp_update_user([
        'ID' => $this->user_id,
        'user_email' => $this->form_data['email'],
        'show_admin_bar_front' => false
      ]);
    }
    if (($this->form_data['phone'] && !$this->row_exists()) || ( $this->row_exists() && $this->form_data['phone'] !== $this->db_row->contact_info_phone)) {
      $custom_db_data['contact_info_phone'] = $this->form_data['phone'];
    }
    if ($this->form_data['certifications'] !== $this->get_user_prop('certifications')) {
      // update post taxonomy terms
      wp_set_post_terms( $this->profile_id, implode($this->form_data['certifications'],','), 'certification', false );
    }
    if (($this->form_data['about'] && !$this->row_exists()) || ( $this->row_exists() && $this->form_data['about'] !== $this->db_row->basic_info_about)) {
      $custom_db_data['basic_info_about'] = stripslashes($this->form_data['about']);
    }
    if ($this->form_data['languages']) {
      // update post taxonomy terms
       wp_set_post_terms( $this->profile_id, implode($this->form_data['languages'],','), 'language', false );
    }
    if (($this->form_data['website'] && !$this->row_exists()) || ( $this->row_exists() && $this->form_data['website'] !== $this->db_row->contact_info_website)) {
     
      $custom_db_data['contact_info_website'] = $this->form_data['website'];
    }
    if (($this->form_data['facebook'] && !$this->row_exists()) || ( $this->row_exists() && $this->form_data['facebook'] !== $this->db_row->social_media_facebook_profile)) {
      $db_data = [
        'social_media_facebook_profile' => $this->form_data['facebook']
      ];
      $custom_db_data['social_media_facebook_profile'] = $this->form_data['facebook'];
    }
    if (($this->form_data['instagram'] && !$this->row_exists()) || ( $this->row_exists() && $this->form_data['instagram'] !== $this->db_row->social_media_instagram_profile)) {
      $db_data = [
        'social_media_instagram_profile' => $this->form_data['instagram']
      ];
      $custom_db_data['social_media_instagram_profile'] = $this->form_data['instagram'];
    }
    if (($this->form_data['twitter'] && !$this->row_exists()) || ( $this->row_exists() && $this->form_data['twitter'] !== $this->db_row->social_media_twitter_profile)) {
      $db_data = [
        'social_media_twitter_profile' => $this->form_data['twitter']
      ];
      $custom_db_data['social_media_twitter_profile'] = $this->form_data['twitter'];
    }
    if (($this->form_data['youtube'] && !$this->row_exists()) || ( $this->row_exists() && $this->form_data['youtube'] !== $this->db_row->social_media_youtube_profile)) {
      $db_data = [
        'social_media_youtube_profile' => $this->form_data['youtube']
      ];
      $custom_db_data['social_media_youtube_profile'] = $this->form_data['youtube'];
    }
  
    // Use the field key for acf fields
    if ($this->form_data['acf']['field_5d13abed82433']) {
      // update location
      $location_data = json_decode(stripslashes($this->form_data['acf']['field_5d13abed82433']));
      $custom_db_data['address_state'] = $location_data->state;
      $custom_db_data['address_city'] = $location_data->city;
      $custom_db_data['address_street'] = $location_data->street;
      $custom_db_data['address_postal_code'] = $location_data->zip;
      $custom_db_data['latitude'] = $location_data->lat;
      $custom_db_data['longitude'] = $location_data->lng;
      $custom_db_data['location_json'] = json_encode($location_data);
    }
    
    if(!empty($this->form_data['acf']['field_5d0540a99290c'])){
      $custom_db_data['basic_info_profile_image'] = $this->form_data['acf']['field_5d0540a99290c'];
    }
    $custom_db_data['post_type'] = 'instructor';
    $custom_db_data['post_id'] = $this->profile_id;
    $custom_db_data['associated_user'] = $this->user_id;

    // set post status to 'published'
    wp_update_post([
      'ID' => $this->profile_id,
      'post_status' => 'publish'
    ]);
  
    if ($this->row_exists()) {
      $this->entry_table->update($this->profile_id, $custom_db_data);
    } else {
      $this->entry_table->insert($custom_db_data);
    }

    // redirect to my-account
    header('Location: '.home_url().'/directory/my-account');
    exit;
  }

  public function row_exists ($id = false) {
    if(!$id) $id = $this->profile_id;

    // Check if a row with the post ID exists
    return ($id) ? $this->entry_table->find($id) : false;
  }

  public function is_phone_number($phone) {
    //eliminate every char except 0-9
    $phone = preg_replace("/[^0-9]/", '', $phone);

    //if we have 10 digits left, it's probably valid.
    return (strlen($phone) === 10 && is_numeric($phone)) ? true : false;
  }

  public function is_url ($url) {
    return filter_var($url, FILTER_VALIDATE_URL, [FILTER_FLAG_SCHEME_REQUIRED, FILTER_FLAG_HOST_REQUIRED]);
  }

  public function is_email () {
    return true;
  }

  public function update_username () {
    // update user data
    $display_name = (isset($this->form_data['first_name'])) ? $this->form_data['first_name'].' ' : $this->get_user_prop('first_name').' ';
    $display_name .= (isset($this->form_data['last_name'])) ? $this->form_data['last_name'] : $this->get_user_prop('last_name');

    wp_update_user([
      'ID' => $this->user_id,
      'first_name' => (isset($this->form_data['first_name'])) ? $this->form_data['first_name'] : $this->get_user_prop('first_name'),
      'last_name' => (isset($this->form_data['last_name'])) ? $this->form_data['last_name'] : $this->get_user_prop('last_name'),
      'display_name' => $display_name,
      'show_admin_bar_front' => false
    ]);

    // update post title and slug
    wp_update_post([
      'ID' => $this->profile_id,
      'post_title' => $display_name,
      'post_name' => strtolower(str_replace(' ', '-', $display_name))
    ]);
  }

  public function get_user_prop ($prop) {
    return $this->user_data->$prop;
  }

  public function get_image_editor () {
    Lib::the_plugin_module('acf-form', [
      'id' => 'profile_image_editor',
      'form' => false,
      'post_id' => $this->profile_id,
      'fields' => array('field_5d0540a99290c')
    ]);
  }

  public function get_studios_editor () {
    Lib::the_plugin_module('acf-form', [
        'id' => 'studios_editor',
        'form' => false,
        'post_id' => $this->profile_id,
        'fields' => array('field_5cfaf830b9af4')
    ]);
  }
}