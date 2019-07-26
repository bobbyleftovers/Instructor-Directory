<?php

namespace BarrelDirectory\Classes\Api;
use WP_REST_Controller;
use WP_REST_Server;
use WP_Query;
use WP_User_Query;
use WP_Error;
use stdClass;
use BarrelDirectory\Classes\Db\Db_Control;
use BarrelDirectory\Classes\Query\Geo_Query;

if ( ! defined( 'WPINC' ) ) {
  die;
}

class Rest_Api extends WP_REST_Controller {

  /**
   * @since 8.5.26
   * @var string
   */
  protected $namespace;

  public function __construct() {
    
    $this->namespace = 'bd-api/v1';
    $this->DB = new Db_Control();
    add_action('rest_api_init', array($this, 'register_routes'));
    // add_action( 'rest_api_init', 'map_meta_fields' );
  
  }
  
  public function register_routes() {
    // return multiple items
    register_rest_route(
      $this->namespace,
      '/instructor',
      array(
        array(
          'methods'             => 'GET',
          'callback'            => array( $this, 'get_instructors' ),
          'args' => array(
            'per_page' => array(
              'description'       => 'Maxiumum number of items to show per page.',
              'type'              => 'integer',
              'validate_callback' => function( $param, $request, $key ) {
                return is_numeric( $param );
               },
              'sanitize_callback' => 'absint',
            ),
            'page' =>  array(
              'description'       => 'Current page of the collection.',
              'type'              => 'integer',
              'validate_callback' => function( $param, $request, $key ) {
                return is_numeric( $param );
               },
              'sanitize_callback' => 'absint'
            ),
            'keyword' =>  array(
              'description'       => 'Add a keyword to search with',
              'type'              => 'string',
              'validate_callback' => function($param, $request, $key) {
                  return is_string( $param );
                },
              'sanitize_callback' => 'sanitize_text_field',
            ),
            'radius' =>  array(
              'description'       => 'Add a radius to search within',
              'type'              => 'string',
              'validate_callback' => function($param, $request, $key) {
                  return is_numeric( $param );
                },
              'sanitize_callback' => 'sanitize_text_field',
            )
          )
        )
      )
    );

    register_rest_route(
      $this->namespace,
      '/studio',
      array(
        array(
          'methods'   => 'GET',
          'callback'  => array( $this, 'get_studios' ),
          'args' => array(
            'per_page' => array(
              'description'       => 'Maxiumum number of items to show per page.',
              'type'              => 'integer',
              'validate_callback' => function( $param, $request, $key ) {
                return is_numeric( $param );
               },
              'sanitize_callback' => 'absint'
            ),
            'page' =>  array(
              'description'       => 'Current page of the collection.',
              'type'              => 'integer',
              'validate_callback' => function( $param, $request, $key ) {
                return is_numeric( $param );
               },
              'sanitize_callback' => 'absint'
            ),
            'keyword' =>  array(
              'description'       => 'Add a keyword to search with',
              'type'              => 'string',
              'validate_callback' => function($param, $request, $key) {
                  return is_string( $param );
                },
              'sanitize_callback' => 'sanitize_text_field'
            ),
            'radius' =>  array(
              'description'       => 'Add a radius to search within',
              'type'              => 'string',
              'validate_callback' => function($param, $request, $key) {
                  return is_numeric( $param );
                },
              'sanitize_callback' => 'absint'
            ),
            'latitude' =>  array(
              'description'       => 'Add latitude for centering geoloaction searches',
              'type'              => 'string',
              'validate_callback' => function($param, $request, $key) {
                  return is_numeric( $param );
                },
              'sanitize_callback' => 'absint'
            ),
            'logitude' =>  array(
              'description'       => 'Add longitude for centering geoloaction searches',
              'type'              => 'string',
              'validate_callback' => function($param, $request, $key) {
                  return is_numeric( $param );
                },
              'sanitize_callback' => 'absint' 
            ),
            'is_geo_query' =>  array(
              'description'       => 'Add a radius to search within',
              'type'              => 'bool',
              'validate_callback' => function($param, $request, $key) {
                  return is_bool( $param );
                },
              // 'sanitize_callback' => 'absint' 
            )
          )
        )
      )
    );
  }

  public function get_instructors ( $request ) {
    // check for params
    $args = array(
      'post_type' => 'instructor',
      'posts_per_page' => ($request['per_page']) ? $request['per_page'] : 12,
      'paged' => ($request['page']) ? $request['page'] : 1,
    );

    if($request['keyword'] && $request['keyword'] !== ''){
      $args['s'] = $request['keyword'];
    }

    if($request['radius'] && $request['latitude'] && $request['longitude']) {
      $args['geo_query'] = array(
        'latitude'  => $request['latitude'],    // this is the latitude of the point we are getting distance from
        'longitude' => $request['longitude'],   // this is the longitude of the point we are getting distance from
        'distance'  => $request['radius'],           // this is the maximum distance to search
        'units'     => 'miles'       // this supports options: miles, mi, kilometers, km
      );
      $args['orderby'] = 'distance'; // this tells WP Query to sort by distance
      $args['order']   = 'ASC';
      $args['posts_per_page'] = -1;
    }

    $instructors = [];
    $query = new WP_Query( $args );
    
    if ( $query->have_posts() ) {
      while ( $query->have_posts() ) {

        global $post;

        // for headers
        $total = $query->found_posts;
        $pages = $query->max_num_pages;

        $query->the_post();
        $instructor = new stdClass();
        $row_data = $this->get_row(get_the_ID());
        $row_data->basic_info_profile_image = (wp_get_attachment_url( $row_data->basic_info_profile_image )) ? wp_get_attachment_url( $row_data->basic_info_profile_image ) : false;

        // ID
        $instructor->id = get_the_ID();
        // title
        $instructor->title = get_the_title();
        // image
        $instructor->image = $row_data->basic_info_profile_image;
        // permalink
        $instructor->slug = $post->post_name;
        // slug
        $instructor->permalink = get_the_permalink();
        // date
        $instructor->date = get_the_date('c');
        // post meta/acf
        $instructor->acf = $this->get_acf(get_the_ID());
        // return the whole row from the custom table
        $instructor->row = $row_data;
        // certifications
        $instructor->certification = get_the_terms(get_the_ID(), 'certification');
        // languages
        $instructor->language = get_the_terms(get_the_ID(), 'language');

        array_push($instructors, $instructor);
      }
      // return the post array
      $response = rest_ensure_response( $instructors );
      $response->header( 'X-WP-Total', (int) $total );
      $response->header( 'X-WP-TotalPages', (int) $pages );
      return $response;
    } else {
      return [];
    }
  }

  public function get_studios ( $request ) {
    // check for params
    $args = array(
      'post_type' => 'studio',
      'posts_per_page' => ($request['per_page']) ? $request['per_page'] : 12,
      'paged' => ($request['page']) ? $request['page'] : 1,
    );

    if($request['keyword'] && $request['keyword'] !== ''){
      $args['s'] = $request['keyword'];
    }

    if($request['radius'] && $request['latitude'] && $request['longitude']) {
      $args['geo_query'] = array(
        // 'lat_field' => 'latitude',  // this is the name of the meta field storing latitude
        // 'lng_field' => 'longitude', // this is the name of the meta field storing longitude 
        'latitude'  => $request['latitude'],    // this is the latitude of the point we are getting distance from
        'longitude' => $request['longitude'],   // this is the longitude of the point we are getting distance from
        'distance'  => $request['radius'],           // this is the maximum distance to search
        'units'     => 'miles'       // this supports options: miles, mi, kilometers, km
      );
      $args['orderby'] = 'distance'; // this tells WP Query to sort by distance
      $args['order']   = 'ASC';
      $args['posts_per_page'] = -1;
    }

    $studios = [];
    $query = new WP_Query( $args );
    
    if ( $query->have_posts() ) {
      while ( $query->have_posts() ) {

        global $post;

        // for headers
        $total = $query->found_posts;
        $pages = $query->max_num_pages;

        $query->the_post();
        $row = $this->get_row(get_the_ID());
        $studio = new stdClass();

        // add stuff to row data for lyt instructors
        $instructors = explode(',', $row->basic_info_lyt_instructors);
        $modified_instructors = [];

        foreach($instructors as $instructor_id) {
          if ($instructor_id) {
            // permalink, title, id
            $modified_instructors[] = [
              'id' => $instructor_id,
              'permalink' => get_the_permalink($instructor_id),
              'title' => get_the_title($instructor_id)
            ];
          }
        }
        $row->basic_info_lyt_instructors = $modified_instructors;

        // ID
        $studio->id = get_the_ID();
        // title
        $studio->title = get_the_title();
        // images
        $studio->image = ( has_post_thumbnail() ) ? esc_url(get_the_post_thumbnail_url($post->ID)) : '';
        // permalink
        $studio->slug = $post->post_name;
        // slug
        $studio->permalink = get_the_permalink();
        // date
        $studio->date = get_the_date('c');
        // post meta/acf
        $studio->acf = $this->get_acf(get_the_ID());
        // return the whole row from the custom table
        $studio->row = $row;
        // languages
        $studio->language = get_the_terms(get_the_ID(), 'language');

        array_push($studios, $studio);
      }
      // return the post array
      $response = rest_ensure_response( $studios );
      $response->header( 'X-WP-Total', (int) $total );
      $response->header( 'X-WP-TotalPages', (int) $pages );
      return $response;
    } else {
      return [];
    }
  }

  public function post_permissions_check ( $request ) {
    
    if ( is_user_logged_in() ) {

      if ( 'edit' === $request['context'] &&
        ( ! current_user_can( 'connections_edit_entry' ) || ! current_user_can( 'connections_edit_entry_moderated' ) )
      ) {

        return new WP_Error(
          'rest_forbidden_context',
          'Permission denied. Current user does not have required capabilityies assigned.', 'connections',
          array( 'status' => rest_authorization_required_code() )
        );
      }

    } else {

      return new WP_Error(
        'rest_forbidden_context',
        'Permission denied. Login required.',
        array( 'status' => rest_authorization_required_code() )
      );
    }

    return true;
  
  }
  
  public function get_permissions_check ( $request ) {
    
    return true;

  }
  
  public function delete_permissions_check ( $request ) {
  
    return false;
  
  }

  public function get_row ($id) {
    $row = $this->DB->find($id);
    $row = $row[0];
    return $row;
  }
  public function get_acf($id) {
  
    include_once( ABSPATH . 'wp-admin/Classes/plugin.php' );
  
    // check if acf is active before doing anything
    if( is_plugin_active('advanced-custom-fields-pro/acf.php') || is_plugin_active('advanced-custom-fields/acf.php') ) {
  
      // get fields
      $acf_fields = get_fields($id);
      // if we have fields
      if( $acf_fields ) {
        return $acf_fields;
      }
  
    } else {
       // no acf, return false
       return false;
    }
  }
}