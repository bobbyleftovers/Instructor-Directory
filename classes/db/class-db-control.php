<?php
/***
 * * @implements Db_Interface
 */

namespace BarrelDirectory\Classes\Db;

if ( ! defined( 'WPINC' ) ) {
  die;
}

class Db_Control{

  public function __construct() {
    global $wpdb;
    $this->table_name = $wpdb->prefix . ENTRY_TABLE_NAME;
  }

  public function insert ( $data ) {
    global $wpdb;
    echo $this->table_name    .' -> ';
    var_dump($data);
    return $wpdb->insert($this->table_name, $data);
  }

  public function update ( $post_id, $data ) {
    global $wpdb, $post;
    return $wpdb->update($this->table_name, $data, ['post_id' => $post_id]);
  }

  public function delete ($post_id) {
    global $wpdb;
    if ( $wpdb->get_var( $wpdb->prepare( 'SELECT post_id FROM '. $wpdb->prefix . ENTRY_TABLE_NAME .' WHERE post_id = %d', $post_id ) ) ) {
      $wpdb->query( $wpdb->prepare( 'DELETE FROM '. $wpdb->prefix . ENTRY_TABLE_NAME .' WHERE post_id = %d', $post_id ) );
    }
  }

  public function find ( $id, $column_names = '*' ) {
    global $wpdb;
    if(!$id) return false;
    $sql = '
      SELECT ' . $column_names . '
      FROM ' . $this->table_name . '
      WHERE post_id=' . $id . '
      LIMIT 1;';

    $result = $wpdb->get_results($sql);
    return (sizeof($result) > 0) ? $result : false;
  }

  public function findByField ( $field_name, $field_value, $post_id = false ) {
    global $wpdb;
    $sql = '
      SELECT *
      FROM ' . $this->table_name . '
      WHERE ' . $field_name . '=' . $field_value . '
    ';
    $sql .= ($post_id) ? ' AND WHERE post_id=' . $post_id . ';' : ';';
    
    $result = $wpdb->get_results($sql);
    return (sizeof($result) > 0) ? $result : false;
  }

  public function findAll ($limit = 1000) {
    global $wpdb, $post;
  }

}