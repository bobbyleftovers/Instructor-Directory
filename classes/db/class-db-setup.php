<?php

namespace BarrelDirectory\Classes\Db;

if ( ! defined( 'WPINC' ) ) {
  die;
}

class Db_Setup {
  public function __construct() {}

  static function on_activation () {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . ENTRY_TABLE_NAME;
    $sql = "CREATE TABLE $table_name (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
      post_id mediumint(9) NOT NULL UNIQUE,
      post_type text,
      basic_info_first_name text,
      basic_info_last_name text,
      basic_info_about text,
      basic_info_lyt_instructors text,
      basic_info_profile_image mediumint(9),
      address_street text,
      address_city text,
      address_state text,
      address_postal_code mediumint(9),
      latitude text,
      longitude text,
      location_json text,
      contact_info_email text,
      contact_info_phone text,
      contact_info_website text,
      social_media_facebook_profile text,
      social_media_instagram_profile text,
      social_media_twitter_profile text,
      social_media_youtube_profile text,
      PRIMARY KEY  id (id)
    ) $charset_collate;";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    dbDelta( $sql );
  }

  static function on_deactivation () {}

  static function on_uninstall () {
    global $wpdb;
    $table_name = $wpdb->prefix . ENTRY_TABLE_NAME;
    $sql = "DROP TABLE IF EXISTS $table_name;";
    $wpdb->query($sql);
  }
}
