<?php
/*
Template Name: Directory - User Admin Area (logged in)
Template Post Type: page
*/

use BarrelDirectory\Classes\Lib\Modules as Lib;

// check that the user is logged in and is an instructor. if not, redirect them
$user = wp_get_current_user();
// if (!is_user_logged_in() || !in_array( 'instructor', (array) $user->roles )) {
//   // send user home
//   header('Location: '.home_url().'/directory/login');
// }
acf_form_head();
get_header();

$user_id = get_current_user_id();

if( get_field('profile_post', 'user_'.$user_id) ){
  $profile = get_field('profile_post', 'user_'.$user_id);
  $form_args = [
    'id'      => 'profile_editor',
    'post_id' => $profile,
    // 'fields' => array('field_5cfaf72026b12'), // we can grab/update individual fields/subgroups to break the form up
    // 'field_groups'=> array('group_585aaa8287a78') // this will grab a group/groups
  ];
}?>
<main id="main_content" tabindex="-1">
  <section class="barrel-directory__container"><?php
    Lib::the_plugin_module('directory-header', array(
      'title' => get_the_title(),
      'title_class' => 'main-title',
      'add_link' => false
    ));?>
    <div class="container">
      <?php acf_form($form_args); ?><br>
      <a class="button" href="<?= wp_logout_url(home_url().'/directory/login'); ?>">Logout</a>
    </div>
</section>
</main><?php
acf_enqueue_uploader();
get_footer();?>
