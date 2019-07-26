<?php

namespace BarrelDirectory\Classes\Acf;

use BarrelDirectory\Classes\Db\Db_Control;

if ( ! defined( 'WPINC' ) ) {
  die;
}

class Acf_Mapping {
  public function __construct ($post_type) {
    // Key/value pairs align all acf meta withn our custom table columns
    $associations = [
      'studio' => [
        // STUDIO FIELD ASSOCIATIONS
        'field_5cfaf7a3000c3' => 'basic_info_about',
        'field_5d2e46ecee20a' => 'basic_info_lyt_instructors',
        // 'field_5d1ccdafbc5ce' => 'basic_info_languages',
        'field_5cfaf7a30b85b' => 'contact_info_email',
        'field_5cfaf7a30b90f' => 'contact_info_phone',
        'field_5cfaf7a30b9f9' => 'contact_info_website',
        'field_5d1a295e59498' => 'location_json',
        'field_5cfc0cf406a36' => 'social_media_facebook_profile',
        'field_5cfc0d0806a37' => 'social_media_instagram_profile',
        'field_5cfc0d0d06a39' => 'social_media_twitter_profile',
        'field_5cfc0d0b06a38' => 'social_media_linkedin_profile'
      ],
      'instructor' => [
        // MEMBER FIELD ASSOCIATIONS
        'field_5cfaf4cb0c630' => 'basic_info_first_name',
        'field_5cfaf4d50c631' => 'basic_info_last_name',
        'field_5cfaf4eb0c633' => 'basic_info_about',
        'field_5d0540a99290c' => 'basic_info_profile_image',
        // 'field_5d06e6e246cc3' => 'basic_info_certifications',
        // 'field_5d1ccfce1b417' => 'basic_info_languages',
        'field_5d13abed82433' => 'location_json',
        'field_5cfaf65ebf0c3' => 'contact_info_email',
        'field_5cfaf66dbf0c4' => 'contact_info_phone',
        'field_5cfaf6a8bf0c5' => 'contact_info_website',
        'field_5cfc0c65e792a' => 'social_media_facebook_profile',
        'field_5cfc0c81e792b' => 'social_media_instagram_profile',
        'field_5cfc0c9ae792c' => 'social_media_twitter_profile',
        'field_5cfc0ca8e792d' => 'social_media_linkedin_profile',
      ]
    ];

    // the following fields are ignored entirely. they are mostly taxonomy terms.
    // these are not attached to the save/update hooks.
    $this->ignored_fields = [
      // instructors
      'field_5d06e6e246cc3', // certifications
      'field_5d1ccfce1b417', // languages

      // studios
      'field_5d1ccdafbc5ce', // languages
    ];

    $this->entry_table = new Db_control();
    $this->post_type = $post_type;
    $this->associations = $associations[$this->post_type];

    add_filter( 'acf/update_value', array( $this, 'ignore_updates' ), 10, 3 );
    add_action( 'acf/save_post', array( $this, 'save_post' ), 1 );
    add_filter( 'acf/load_value', array( $this, 'load_field'), 11, 3 );
  }

  public function ignore_updates ( $value, $post_id, $field ) {
    // Bail if this is not our post type
    if(get_post_type($post_id) !== $this->post_type) return $value;
    // die(print_r($field));
    echo '<br><br>';

    // Disregard updating certain fields as they're already being stored in a custom table.
    if ( array_key_exists($field['key'], $this->associations) || in_array($field['key'], $this->ignored_fields)) {
      return null;
    }

    return $value;
  }

  function save_post( $post_id ) {
    // Bail early if no ACF data or if this is the wrong post type
    if( empty($_POST['acf']) || get_post_type($post_id) !== $this->post_type ) return;
    $id = $post_id;
    // array of field values
    $fields = $_POST['acf'];
    $data = [
      'post_id' => $post_id,
      'post_type' => $this->post_type
    ];
    // print_r($fields);
    $results = $this->map_group($fields, $data, $post_id);
    // die(print_r($results));
    if($this->row_exists($post_id)){
      $this->entry_table->update($post_id, $results);
    } else {
      $this->entry_table->insert($post_id, $results);
    }
  }

  public function map_group ($fields, &$data = [], $post_id) {
    foreach($fields as $key => $value) {
      $recursion = false;
      // echo 'Key: '.$key.'<br>';
      // echo 'Value: ';
      // print_r($value);
      // echo '<br>';
      if( gettype($value) == 'array' ) {
        
        // we need an additional check here to prevent db errors
        $field_data = get_field_object( $key );
        switch($field_data['type']){
          case 'group':
            $recursion = true;
            break;
          default:
            $value = implode(',', $value);
            break;
        }
      }

      // this is the mapbox field
      if( $key === 'field_5d1a295e59498' || $key === 'field_5d13abed82433' ) {

        // make sure json string is has no '\' characters before saving
        $value = str_replace( '\\', '', $value );

        // parse the json object
        $json = json_decode($value);
        $this->save_location($post_id, $json);
      }

      if($recursion) {
        $this->map_group($value, $data, $post_id);
      } else if (array_key_exists($key, $this->associations)) {
        $data[$this->associations[$key]] = $value;
      }
    }
    return $data;
  }

  public function save_location($post_id, $json){
    if($json) {
      $data = [
        'post_id' => $post_id,
        'post_type' => $this->post_type,
        'latitude' => $json->lat,
        'longitude' => $json->lng,
        'location_json' => json_encode($json),
        'address_street' => $json->street,
        'address_city' => $json->city,
        'address_state' => $json->state,
        'address_postal_code' => $json->zip,
      ];
      if($this->row_exists($post_id)){
        $this->entry_table->update($post_id, $data);
      } else {
        $this->entry_table->insert($post_id, $data);
      }
    }
  }

  public function row_exists ($id) {
    // Check if a row with the post ID exists (should use $post global to get ID)
    return $this->entry_table->find($id);
  }

  function load_field( $value, $post_id, $field ) {
    // This is a list of field types (not field names) that need to be converted to an array on load or they will not display correctly
    $array_field_types = ['user', 'post_object'];
  
    // Fetch certain records from custom table
    if (array_key_exists($field['key'], $this->associations)) {
      $row =	$this->entry_table->find( $post_id, $field['name'] ); // get the row from the custom table
      if($row){
        // Check field type against our array field types
        $value = (in_array($field['type'], $array_field_types)) ? explode(',',$row[0]->{$field['name']}) : $row[0]->{$field['name']};
        if ( $row && $row[0]) {
          return $value;
        }
      }
    }
    return $value;

  }
}
