<?php 
use BarrelDirectory\Classes\Db\Db_Control;
use BarrelDirectory\Classes\Util\Template_Utils as Tmpl;
use BarrelDirectory\Classes\Lib\Modules as Lib;

// Get custom DB field data for this post
$DB = new Db_Control();
$row = $DB->find(get_the_ID());
$row = $row[0];
$location = json_decode($row->location_json);

// Initialize vars
$languages = [];
$primary_found = false;
$primary_studio_html = false;

// Set up the languages
if(get_the_terms(get_the_ID(),'language')){
  foreach(get_the_terms(get_the_ID(),'language') as $language){
    if(!in_array($language->name, $languages)) $languages[] = $language->name;
  }
}

// Get the primary studio
while(have_rows('your_studios_studio') && !$primary_found){
  the_row();
  if(get_sub_field('primary_studio')){
    $primary_found = true;
    if(get_sub_field('studio_type') === 'lyt_studio'){
      $primary_studio = $DB->find(get_sub_field('lyt_studio'));
      $primary_studio = $primary_studio[0];
      $primary_studio_html = '<a class="primary-studio__link" target="_blank" href="'.Tmpl::get_gmaps_search_link(get_the_title(get_sub_field('lyt_studio')).' '.$primary_studio->address_street.' '.$primary_studio->address_city.', '.$primary_studio->address_state.' '.$primary_studio->address_postal_code).'"><span>'.get_the_title(get_sub_field('lyt_studio')).'<br>'.$primary_studio->address_city.','.$primary_studio->address_state.'</span></a>';
    } else {
      if(have_rows('studio_address')){
        the_row();
        $primary_studio_html = '<a class="primary-studio__link" target="_blank" href="'.Tmpl::get_gmaps_search_link(get_sub_field('studio_name').' '.get_sub_field('studio_street').get_sub_field('studio_city').', '.get_sub_field('studio_state').' '.get_sub_field('studio_postal_code')).'"><span>'.get_sub_field('studio_name').'<br>'.get_sub_field('studio_city').', '.get_sub_field('studio_state').'</span></a>';
      }
    }
  }
}
if (!$primary_found){
  while(have_rows('your_studios_studio') && !$primary_found){
    the_row();
    if(get_sub_field('studio_type') === 'lyt_studio'){
      $primary_studio = $DB->find(get_sub_field('lyt_studio'));
      $primary_studio = $primary_studio[0];
      $primary_studio_html = '<a class="primary-studio__link" target="_blank" href="'.Tmpl::get_gmaps_search_link(get_the_title(get_sub_field('lyt_studio')).' '.$primary_studio->address_street.' '.$primary_studio->address_city.', '.$primary_studio->address_state.' '.$primary_studio->address_postal_code).'"><span>'.get_the_title(get_sub_field('lyt_studio')).'<br>'.$primary_studio->address_city.','.$primary_studio->address_state.'</span></a>';
    } else {
      if(have_rows('studio_address')){
        the_row();
        $primary_studio_html = '<a class="primary-studio__link" target="_blank" href="'.Tmpl::get_gmaps_search_link(get_sub_field('studio_name').' '.get_sub_field('studio_street').get_sub_field('studio_city').', '.get_sub_field('studio_state').' '.get_sub_field('studio_postal_code')).'"><span>'.get_sub_field('studio_name').'<br>'.get_sub_field('studio_city').', '.get_sub_field('studio_state').'</span></a>';
      }
    }
    break;
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
          <?php
          Lib::the_plugin_module('image', array(
            'image' => $row->basic_info_profile_image,
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
          <div class="dir-single-content__details dir-single-content--part dir-single-content__details--instructor">
            <div class="dir-single-content__detail detail--address">
              <h5 class="label">Location</h5>
              <span><?= ($location->city && $location->state) ? $location->city. ', '.$location->state : 'N/A' ?></span>
            </div>
            <div class="dir-single-content__detail detail--primary-studio">
              <h5 class="label">Primary Studio</h5>
              <?= ($primary_studio_html) ? $primary_studio_html : 'N/A'?>
            </div>
            <div class="dir-single-content__detail detail--email">
                <h5 class="label">Email</h5><?php
                if($row->contact_info_email){?>
                  <a href="mailto:<?=$row->contact_info_email?>" target="_blank"><span><?= $row->contact_info_email?></span></a><?php
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
            <div class="dir-single-content__detail detail--website">
                <h5 class="label">Website</h5><?php
                if($row->contact_info_website){ ?>
                  <a href="<?=$row->contact_info_website?>" target="_blank"><span><?= Tmpl::formatted_website($row->contact_info_website)?></span></a><?php
                } else {
                  echo '<span>N/A</span>';
                }?>
            </div>
            <div class="dir-single-content__detail detail--language">
                <h5 class="label">Languages</h5><?php
                if($languages) {
                  echo '<span class="detail__languages">';
                  for($i = 0; $i < sizeof($languages); $i++){
                    echo $languages[$i];
                    echo ($i === sizeof($languages) - 1) ? '' : ', ';
                  }
                  echo '</span>';
                } else {
                  echo '<span>N/A</span>';
                } ?>
            </div>
          </div><?php
          if(get_the_terms(get_the_ID(),'certification')) {?>
            <div class="dir-single-content__certifications dir-single-content--part">
              <h5 class="label">LYT Certifications</h5>
              <div class="certifications__badges"><?php
                  foreach(get_the_terms(get_the_ID(),'certification') as $certification){
                    echo '<span class="certifications__badge icon icon--' . $certification->slug . '"></span>';
                  }?>
              </div>
            </div><?php
          }
          if($row->basic_info_about) { ?>
            <div class="dir-single-content__about dir-single-content--part">
              <h5 class="label">Personal Bio</h5>
              <?= wpautop( $row->basic_info_about, true )?>
            </div><?php
          }
          if(have_rows('your_studios_studio')) { ?>
            <div class="dir-single-content__classes dir-single-content--part">
              <h5 class="label">Take a Class With Me</h5>
              <div class="dir-single-content__my-studios"><?php
                while(have_rows('your_studios_studio')){
                  the_row();?>
                  <div class="my-studio__row"><?php

                  if(get_sub_field('studio_type') === 'lyt_studio'){
                    $lyt_row = $DB->find(get_sub_field('lyt_studio'));
                    $lyt_row = $lyt_row[0];?>
                    <div class="my-studio">
                      <span class="my-studio__name"><?= get_the_title(get_sub_field('lyt_studio'))?></span><br>
                      <a class="my-studio__link--map"
                        href="<?= Tmpl::get_gmaps_search_link(get_the_title(get_sub_field('lyt_studio')).' '.$lyt_row->address_street.' '.$lyt_row->address_city.', '.$lyt_row->address_state.' '.$lyt_row->address_postal_code) ?>"
                        target="_blank">
                        <?= $lyt_row->address_street.'<br>'.$lyt_row->address_city.', '.$lyt_row->address_state.' '.$lyt_row->address_postal_code ?>
                      </a>
                    </div>
                    <div class="my-studio__link--site">
                      <a href="<?= $lyt_row->contact_info_website?>" class="button">View Studio</a>
                    </div><?php
                  } else {?>
                    <div class="my-studio"><?php
                      $website = (get_sub_field('studio_website')) ? get_sub_field('studio_website') : false;
                      if(have_rows('studio_address')):
                        the_row();?>
                        <span class="my-studio__name"><?= get_sub_field('studio_name')?></span><br>
                        <a class="my-studio__link--map" 
                          href="<?= Tmpl::get_gmaps_search_link(get_sub_field('studio_name').' '.get_sub_field('studio_street').get_sub_field('studio_city').', '.get_sub_field('studio_state').' '.get_sub_field('studio_postal_code'))?>"
                          class="my-studio__address"
                          target="_blank">
                            <?= get_sub_field('studio_street').'<br>'.get_sub_field('studio_city').', '.get_sub_field('studio_state').' '.get_sub_field('studio_postal_code')?>
                        </a><?php
                      endif;?>
                    </div><?php
                    if ($website) {?>
                      <div class="my-studio--link">
                        <a href="<?= $website?>" target="_blank" class="button">View Studio</a>
                      </div><?php
                    }
                  }?>

                  </div><?php
                } ?>
              </div>
            </div><?php
          }?>
        </article>
      </div>
    </section>
  </div>
</main>
<?php get_footer(); ?>
