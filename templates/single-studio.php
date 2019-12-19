<?php 
use BarrelDirectory\Classes\Db\Db_Control;
use BarrelDirectory\Classes\Util\Template_Utils as Tmpl;
use BarrelDirectory\Classes\Lib\Modules as Lib;

// Get custom field data for this post
$DB = new Db_Control();
$row = $DB->find(get_the_ID());
$row = $row[0];
$studio_languages = [];
if(get_the_terms(get_the_ID(),'language')){
  foreach(get_the_terms(get_the_ID(),'language') as $language){
    if(!in_array($language->name, $studio_languages)) $studio_languages[] = $language->name;
  }
}

// Gather dat from the teachers loop for the list of teachers and add their languages to the list of languages if needed
$teachers_output = '';
$teacher_data = explode(',', $row->basic_info_lyt_instructors);
foreach ( $teacher_data as $key => $teacher_id) {

  // Profile links
  $teachers_output .= '<a href="'.get_the_permalink($teacher_id).'">'.get_the_title($teacher_id).'</a>';
  if($key < sizeof($teacher_data) - 1) {
    $teachers_output .= ', ';
  }

  // Language list
  if(get_the_terms($teacher_id,'language')){
    foreach(get_the_terms($teacher_id,'language') as $language){
      if(!in_array($language->name, $studio_languages)) $studio_languages[] = $language->name;
    }
  }
}

get_header(); ?>
    
<main id="main_content" class="directory__wrap" itemprop="mainContentOfPage" tabindex="-1">
  <div class="barrel-directory__container"><?php
    the_post();
    Lib::the_plugin_module('directory-header', array(
      'title' => 'Directory',
      'title_class' => 'main-title',
      'left_link_url' => site_url().'/directory',
      'left_link_copy' => __('Back to All', 'mvl'),
      'wrapper_classes' => 'directory__header--grid'
    ));?>
    <section class="post container">
      <div class="dir-post__image">
        <div class="dir-post__image-inner">
          <?php Lib::the_plugin_module('image', array(
            'image' => get_post_thumbnail_id(),
            'class' => 'post__image__image image--square',
            'cover' => true
          )); ?>
        </div>
      </div>
      <div class="dir-post__content">
        <article class="dir-single-content">
          <div class="dir-single-content__header dir-single-content--part">
            <div class="title__wrap">
              <h2 class="title__h2 secondary-title"><?= get_the_title() ?></h2>
              <h3 class="title__label label"><?= $row->post_type ?></h3>
            </div>

            <div class="dir-single-content__header--social"><?php
              if($row->social_media_facebook_profile) { ?>
                <a class="social-link" target="_blank" href="<?= $row->social_media_facebook_profile ?>" title="<?= __('Facebook', 'mvl'); ?>">
                  <span class="icon icon--facebook"></span>
                </a><?php
              }
              if($row->social_media_instagram_profile){?>
                <a class="social-link" target="_blank" href="<?= $row->social_media_instagram_profile ?>" title="<?= __('Instagram', 'mvl'); ?>">
                  <span class="icon icon--instagram"></span>
                </a><?php
              }
              if($row->social_media_twitter_profile){?>
                <a class="social-link" target="_blank" href="<?= $row->social_media_twitter_profile ?>" title="<?= __('Twitter', 'mvl'); ?>">
                  <span class="icon icon--twitter"></span>
                </a><?php
              }
              if($row->social_media_youtube_profile){?>
                <a class="social-link" target="_blank" href="<?= $row->social_media_youtube_profile ?>" title="<?= __('YouTube', 'mvl'); ?>">
                  <span class="icon icon--youtube"></span>
                </a><?php
              }?>
            </div>
          </div>
          <div class="dir-single-content__details dir-single-content--part dir-single-content__details--studio">
            <div class="dir-single-content__detail detail--address">
              <h5 class="label">Address</h5><?php
              if($row->address_city && $row->address_state) {?>
                <a href="<?= Tmpl::get_gmaps_search_link(get_the_title(get_sub_field('lyt_studio')).' '.$row->address_street.' '.$row->address_city.', '.$row->address_state.' '.$row->address_postal_code) ?>" target="_blank">
                  <?= $row->address_street.'<br>'.$row->address_city.', '.$row->address_state.' '.$row->address_postal_code ?>
                </a><?php
              } else {
                echo '<span>N/A</span>';
              }?>
            </div>
            <div class="dir-single-content__detail detail--website">
              <h5 class="label">Website</h5><?php
              if($row->contact_info_website){ ?>
                <a href="<?=$row->contact_info_website?>" target="_blank"><span><?= Tmpl::formatted_website($row->contact_info_website)?></span></a><?php
              } else {
                echo '<span>N/A</span>';
              }?>
            </div>
            <div class="dir-single-content__detail detail--phone">
              <h5 class="label">Phone</h5><?php
              if($row->contact_info_phone) {?>
                <a href="tel:<?=urlencode($row->contact_info_phone)?>"><span><?=Tmpl::formatted_phone($row->contact_info_phone)?></span></a><?php
              } else {
                echo '<span>N/A</span>';
              }?>
            </div>
            <div class="dir-single-content__detail detail--email">
              <h5 class="label">Email</h5><?php
              if($row->contact_info_email){ ?>
                <a href="mailto:<?=$row->contact_info_email?>"><span><?=$row->contact_info_email?></span></a><?php
              } else {
                echo '<span>N/A</span>';
              }?>
            </div>
            <div class="dir-single-content__detail detail--language">
              <h5 class="label">Languages</h5><?php
              if($studio_languages) { ?>
                <span class="detail__languages"><?php
                  for($i = 0; $i < sizeof($studio_languages); $i++){
                    echo $studio_languages[$i];
                    echo ($i === sizeof($studio_languages) - 1) ? '' : ', ';
                  }?>
                </span><?php
              } else {
                echo '<span>N/A</span>';
              }?>
            </div>
          </div><?php
          if($row->basic_info_about) { ?>
            <div class="dir-single-content__about dir-single-content--part">
              <h5 class="label">About</h5>
              <?= wpautop( $row->basic_info_about, true )?>
            </div><?php
          }?>
          <div class="dir-single-content__instructors dir-single-content--part">
            <h5 class="label">Current LYT Certified Teachers</h5>
            <?= $teachers_output ?>
          </div>
        </article>
      </div>
    </section>
  </div>
</main>
<?php get_footer(); ?>
