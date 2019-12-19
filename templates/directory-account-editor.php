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
$user_post = get_userdata( $user_id );

if( get_field('profile_post', 'user_'.$user_id) ){
  $profile_id = get_field('profile_post', 'user_'.$user_id);
}
$profile_post = get_post($profile_id);?>

<main id="main_content" class="directory__wrap" tabindex="-1">
  <section class="barrel-directory__container account-admin"><?php
    Lib::the_plugin_module('directory-header', array(
      'title' => 'Edit Your Account',
      'title_class' => 'h2 main-title--admin',
      'wrapper_classes' => 'directory__header--admin directory__header--title-link',
      'admin' => true,
      'right_link_url' => wp_logout_url(home_url().'/directory/login'),
      'right_link_copy' => 'Log Out',
      'is_published' => ($profile_post->post_status === 'publish') ? true : false,
      'left_link_copy' => 'Go Back',
      'left_link_url' => site_url().'/directory/my-account'
    ));?>
    <div class="container"><?php
      Lib::the_plugin_module('profile-editor', array(
        'is_published' => ($profile_post->post_status === 'publish') ? true : false,
        'profile_id' => $profile_id,
        'user_id' => $user_id,
        'profile_post' => $profile_post,
        'user_post' => $user_post,
        'updated_message' => 'Profile updated. <a href="'. site_url().'/directory/my-account">Go to my account</a>.'
      ));?>
    </div>
</section>
</main><?php
acf_enqueue_uploader();
// wp_enqueue_media();
get_footer();?>
