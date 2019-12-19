<?php
/*
Template Name: Directory - Password Reset Request Form
Template Post Type: page
*/

use BarrelDirectory\Classes\Lib\Modules as Lib;

get_header();?>
<main id="main_content" class="directory__wrap" tabindex="-1">
  <section class="barrel-directory__container"><?php
    Lib::the_plugin_module('directory-header', array(
      'title' => (isset( $_REQUEST['checkemail'] ) && $_REQUEST['checkemail']) ? 'Reset Link Sent' :'Forgot Your Password?',
      'description' => (isset( $_REQUEST['checkemail'] ) && $_REQUEST['checkemail']) ? '<strong class="password-reset__message">Check your email for a link to reset your password.</strong>' : 'Please enter an email and we will send you a password reset link',
      'title_class' => 'h2 main-title--admin',
      'wrapper_classes' => 'directory__header--login'
    ));?>
    <div class="container"><?php
      Lib::the_plugin_module('password-reset-request', array());?>
    </div>
  </section>
</main>

<?php get_footer();
