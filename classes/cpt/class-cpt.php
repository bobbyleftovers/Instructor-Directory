<?php

namespace BarrelDirectory\Classes\Cpt;

if ( ! defined( 'WPINC' ) ) {
  die;
}

class Cpt {
  public function __construct () {
    add_action( 'init', array($this, 'cpts'), 0 );
    add_action( 'init', array($this, 'taxonomies'), 0 );
  }

  // Add CPTs
  public function cpts() {
    $loc_labels = array(
      'name'                => 'Studios',
      'singular_name'       => 'Studio',
      'menu_name'           => 'LYT Studio Directory',
      'parent_item_colon'   => 'Parent Item:',
      'all_items'           => 'All Studios',
      'view_item'           => 'View Item',
      'add_new_item'        => 'Add New Studio',
      'add_new'             => 'Add New',
      'edit_item'           => 'Edit Studio',
      'update_item'         => 'Update Studio',
      'search_items'        => 'Search Studios',
      'not_found'           => 'Not found',
      'not_found_in_trash'  => 'Not found in Trash',
    );
    $loc_rewrite = array(
      'slug'                => 'studio',
      'with_front'          => false,
      'pages'               => true,
      'feeds'               => true,
    );
    $loc_args = array(
      'label'               => 'studio',
      'description'         => 'LYT-Certified Studios',
      'labels'              => $loc_labels,
      'supports'            => array( 'title', 'thumbnail' ),
      'hierarchical'        => false,
      'menu_position'       => 9,
      'publicly_queryable'  => true,
      'query_var'           => true,
      'public'              => true,
      'rewrite'             => $loc_rewrite,
      'capability_type'     => 'post',
      'show_in_rest'       => true,
    );
    register_post_type( 'studio', $loc_args );

    $mem_labels = array(
      'name'                => 'Instructors',
      'singular_name'       => 'Instructor',
      'menu_name'           => 'Instructor Directory',
      'parent_item_colon'   => 'Parent Item:',
      'all_items'           => 'All Instructors',
      'view_item'           => 'View Item',
      'add_new_item'        => 'Add New Instructor',
      'add_new'             => 'Add New',
      'edit_item'           => 'Edit Instructor',
      'update_item'         => 'Update Instructor',
      'search_items'        => 'Search Instructors',
      'not_found'           => 'Not found',
      'not_found_in_trash'  => 'Not found in Trash',
    );
    $mem_rewrite = array(
      'slug'                => 'instructor',
      'with_front'          => false,
      'pages'               => true,
      'feeds'               => true,
    );
    $mem_args = array(
      'label'               => 'instructor',
      'description'         => 'instructors at MVL',
      'labels'              => $mem_labels,
      'supports'            => array( 'title' ),
      'hierarchical'        => false,
      'menu_position'       => 9,
      'publicly_queryable'  => true,
      'query_var'           => true,
      'public'              => true,
      'rewrite'             => $mem_rewrite,
      'capability_type'     => 'post',
      'show_in_rest'        => true
    );
    register_post_type( 'instructor', $mem_args );
  }

  // Add taxonomies
  public function taxonomies() {
    // Languages
    $lang_labels = array(
      'name'                       => _x( 'Languages', 'Taxonomy General Name', 'text_domain' ),
      'singular_name'              => _x( 'Language', 'Taxonomy Singular Name', 'text_domain' ),
      'menu_name'                  => __( 'Languages', 'text_domain' ),
      'all_items'                  => __( 'All Items', 'text_domain' ),
      'new_item_name'              => __( 'New Item Name', 'text_domain' ),
      'add_new_item'               => __( 'Add New Item', 'text_domain' ),
      'edit_item'                  => __( 'Edit Item', 'text_domain' ),
      'update_item'                => __( 'Update Item', 'text_domain' ),
      'separate_items_with_commas' => __( 'Separate items with commas', 'text_domain' ),
      'search_items'               => __( 'Search Items', 'text_domain' ),
      'add_or_remove_items'        => __( 'Add or remove items', 'text_domain' ),
      'choose_from_most_used'      => __( 'Choose from the most used items', 'text_domain' ),
      'not_found'                  => __( 'Not Found', 'text_domain' ),
    );
    $lang_args = array(
      'labels'                     => $lang_labels,
      // 'hierarchical'               => false,
      'public'                     => true,
      // 'show_ui'                    => true,
      // 'show_admin_column'          => true,
      // 'show_in_nav_menus'          => false,
      // 'show_tagcloud'              => true,
    );
    register_taxonomy( 'language', [ 'studio', 'instructor' ], $lang_args );

    // Certifications
    $cert_labels = array(
      'name'                       => _x( 'Certifications', 'Taxonomy General Name', 'text_domain' ),
      'singular_name'              => _x( 'Certification', 'Taxonomy Singular Name', 'text_domain' ),
      'menu_name'                  => __( 'Certifications', 'text_domain' ),
      'all_items'                  => __( 'All Items', 'text_domain' ),
      'new_item_name'              => __( 'New Item Name', 'text_domain' ),
      'add_new_item'               => __( 'Add New Item', 'text_domain' ),
      'edit_item'                  => __( 'Edit Item', 'text_domain' ),
      'update_item'                => __( 'Update Item', 'text_domain' ),
      'separate_items_with_commas' => __( 'Separate items with commas', 'text_domain' ),
      'search_items'               => __( 'Search Items', 'text_domain' ),
      'add_or_remove_items'        => __( 'Add or remove items', 'text_domain' ),
      'choose_from_most_used'      => __( 'Choose from the most used items', 'text_domain' ),
      'not_found'                  => __( 'Not Found', 'text_domain' ),
    );
    $cert_args = array(
      'labels'                     => $cert_labels,
      // 'hierarchical'               => false,
      'public'                     => true,
      // 'show_ui'                    => true,
      // 'show_admin_column'          => true,
      // 'show_in_nav_menus'          => false,
      // 'show_tagcloud'              => true,
    );
    register_taxonomy( 'certification', [ 'instructor' ], $cert_args );

    // Job Titles?
    $job_title_labels = array(
      'name'                       => _x( 'Job Titles', 'Taxonomy General Name', 'text_domain' ),
      'singular_name'              => _x( 'Job Title', 'Taxonomy Singular Name', 'text_domain' ),
      'menu_name'                  => __( 'Job Titles', 'text_domain' ),
      'all_items'                  => __( 'All Items', 'text_domain' ),
      'new_item_name'              => __( 'New Item Name', 'text_domain' ),
      'add_new_item'               => __( 'Add New Item', 'text_domain' ),
      'edit_item'                  => __( 'Edit Item', 'text_domain' ),
      'update_item'                => __( 'Update Item', 'text_domain' ),
      'separate_items_with_commas' => __( 'Separate items with commas', 'text_domain' ),
      'search_items'               => __( 'Search Items', 'text_domain' ),
      'add_or_remove_items'        => __( 'Add or remove items', 'text_domain' ),
      'choose_from_most_used'      => __( 'Choose from the most used items', 'text_domain' ),
      'not_found'                  => __( 'Not Found', 'text_domain' ),
    );
    $job_title_args = array(
      'labels'                     => $job_title_labels,
      // 'hierarchical'               => false,
      'public'                     => true,
      // 'show_ui'                    => true,
      // 'show_admin_column'          => true,
      // 'show_in_nav_menus'          => false,
      // 'show_tagcloud'              => true,
      // 'update_count_callback' => function() {
			// 	return; //important
			// }
    );
    // register_taxonomy( 'job_title', [ 'studio', 'instructor' ], $job_title_args );
  }
}
