<?php
/*
Template Name: Directory - Password Reset Form
Template Post Type: page
*/

use BarrelDirectory\Classes\Lib\Modules as Lib;

get_header();?>
<main id="main_content" class="directory__wrap" tabindex="-1">
  <section class="barrel-directory__container"><?php
    Lib::the_plugin_module('directory-header', array(
      'title' => 'Reset your Password',
      'title_class' => 'h2 main-title--admin',
      'wrapper_classes' => 'directory__header--login'
    ));?>
    <div class="container"><?php
      Lib::the_plugin_module('password-reset-form', array());?>
    </div>
  </section>
</main>

<?php get_footer();
