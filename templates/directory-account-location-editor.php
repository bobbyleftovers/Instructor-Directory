<?php
/*
Template Name: Directory - User Admin Area (logged in)
Template Post Type: page
*/

use BarrelDirectory\Classes\Lib\Modules as Lib;

// check that the user is logged in and is an instructor. if not, redirect them
$user = wp_get_current_user();
if (!is_user_logged_in() || !in_array( 'instructor', (array) $user->roles )) {
  // send user home
  header('Location: '.home_url().'/directory/login');
}
acf_form_head();
get_header();

$user_id = get_current_user_id();
// $user_id = 25;

if( get_field('profile_post', 'user_'.$user_id) ){
  $profile = get_field('profile_post', 'user_'.$user_id);
}?>
<main id="main_content" class="directory__wrap" tabindex="-1">
  <section class="barrel-directory__container account-admin"><?php
    Lib::the_plugin_module('directory-header', array(
      'title' => 'Set Your Location',
      'title_class' => 'h2 main-title--admin',
      'admin' => true,
      'wrapper_classes' => 'directory__header--admin directory__header--title-link',
      'right_link_url' => wp_logout_url(home_url().'/directory/login'),
      'right_link_copy' => 'Log Out',
      'is_published' => ($profile_post->post_status === 'publish') ? true : false,
      'left_link_copy' => 'Go Back',
      'left_link_url' => site_url().'/directory/my-account'
    ));
    Lib::the_plugin_module('acf-form', array(
      'id' => 'profile-location-editor',
      'post_id' => $profile,
      'fields' => array('field_5d13abed82433'),
      'submit_value' => 'Set Location',
      'updated_message' => 'Location updated. <a href="'. site_url().'/directory/my-account">Go to my account</a>.'
    ));?>
  </section>
</main><?php
// acf_enqueue_uploader();
get_footer();?>
