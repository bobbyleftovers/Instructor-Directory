<?php
/*
Template Name: Directory - Loop/Search
Template Post Type: page
*/

use BarrelDirectory\Classes\Lib\Modules as Lib;
get_header();?>

<main id="main_content" class="directory__wrap" tabindex="-1">
  <section class="barrel-directory__container"><?php
    Lib::the_plugin_module('directory-header', array(
      'title' => 'Directory',
      'title_class' => 'main-title',
      'wrapper_classes' => 'directory__header--loop'
    ));?>
   <div id="profile-directory"><directory-main /></div>
  </section>
</main>

<?php get_footer(); ?>
