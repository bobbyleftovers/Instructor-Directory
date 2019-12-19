<?php
use BarrelDirectory\Classes\DB\Db_Control;
use BarrelDirectory\Classes\Util\Template_Utils as Tmpl;

// set up the viewer
$row = false;
if($is_published){
  $DB = new Db_Control();
  $row = $DB->find($profile_id)[0];
}
$languages = [];
if(get_the_terms($profile_id,'language')){
  foreach(get_the_terms($profile_id,'language') as $language){
    if(!in_array($language->name, $languages)) $languages[] = $language->name;
  }
  $languages = (sizeof($languages) > 0) ? implode(', ', $languages) : 'N/A';
} else {
  $languages = 'N/A';
}

// Get the primary studio
$primary_found = false;
$primary_studio_html = 'N/A';

while(have_rows('your_studios_studio',$profile_id) && !$primary_found) {
  the_row();
  if(get_sub_field('primary_studio', $profile_id)){
    $primary_found = true;
    if(get_sub_field('studio_type') === 'lyt_studio'){
      $primary_studio = $DB->find(get_sub_field('lyt_studio', $profile_id));
      $primary_studio = $primary_studio[0];
      $primary_studio_html = get_the_title(get_sub_field('lyt_studio', $profile_id)).', '.$primary_studio->address_city.', '.$primary_studio->address_state;
    } else {
      if(have_rows('studio_address', $profile_id)){
        the_row();
        $primary_studio_html = get_sub_field('studio_name', $profile_id).',<br>'.get_sub_field('studio_city', $profile_id).', '.get_sub_field('studio_state', $profile_id);
      }
    }
  }
}?>

<section class="profile-viewer"><?php
  if(!$is_published){?>
    <div class="profile-draft-status">
      <p class="profile-draft-status__message">Your profile has not been completed yet! It will remain hidden to visitors until filled out with the necessary details.<br><a href="<?= site_url()?>/directory/my-account/profile-editor" class="profile-draft-status__link">Start Here</a></p>
    </div><?php
  }?>
  <div class="profile-section profile-section--main">
    <div class="profile-section__heading">
      <h3 class="h3">Personal Info</h3>
      <a href="<?= site_url() ?>/directory/my-account/profile-editor" class="profile-section__heading--edit-link">Edit Info</a>
    </div>
    <div class="profile__part--contact">
      <div class="profile__detail">
        <h5 class="label profile__detail--label">Full Name</h5>
        <span class="profile__detail--data"><?= get_the_title($profile_id) ?></span>
      </div>
      <div class="profile__detail">
        <h5 class="label profile__detail--label">Email</h5>
        <span class="profile__detail--data"><?= ($row->contact_info_email) ? $row->contact_info_email : get_field('contact_info_email', $profile_id) ?></span>
      </div>
      <div class="profile__detail">
        <h5 class="label profile__detail--label">Website</h5>
        <span class="profile__detail--data"><?= ($row->contact_info_website) ? Tmpl::formatted_website($row->contact_info_website) : 'N/A' ?></span>
      </div>
      <div class="profile__detail">
        <h5 class="label profile__detail--label">Primary Studio</h5>
        <span class="profile__detail--data capitalize"><?= $primary_studio_html ?></span>
      </div>
      <div class="profile__detail">
        <h5 class="label profile__detail--label">Phone</h5>
        <span class="profile__detail--data"><?= ($row->contact_info_phone) ? Tmpl::formatted_phone($row->contact_info_phone) : 'N/A' ?></span>
      </div>
      <div class="profile__detail">
        <h5 class="label profile__detail--label">Languages</h5>
        <span class="profile__detail--data"><?= $languages ?></span>
      </div>
      <div class="profile__detail profile__detail--desktop">
        <h5 class="label profile__detail--label">LYT Certifications</h5><?php
        if(get_the_terms($profile_id,'certification')) {?>
          <div class="certifications__badges"><?php
              foreach(get_the_terms($profile_id, 'certification') as $certification){
                echo '<span class="certifications__badge icon icon--' . $certification->slug . '"></span>';
              }?>
          </div><?php
        }?>
      </div>
    </div>
    <div class="profile__part--body">
      <div class="profile__detail">
        <h3 class="label">Personal Bio</h3>
        <span><?= stripslashes( wpautop( $row->basic_info_about, true ) ) ?></span>
      </div>
      <div class="profile__detail profile__detail--mobile">
        <h4 class="label profile__detail--label">LYT Certifications</h4><?php
        if(get_the_terms($profile_id,'certification')) {?>
          <div class="certifications__badges"><?php
              foreach(get_the_terms($profile_id, 'certification') as $certification){
                echo '<span class="certifications__badge icon icon--' . $certification->slug . '"></span>';
              }?>
          </div><?php
        }?>
      </div>
    </div>
  </div>
  <div class="profile-section profile-section--location">
    <div class="profile-section__heading profile__heading--location">
      <h4 class="h3">Location</h3>
      <a href="<?= site_url() ?>/directory/my-account/location-editor" class="profile-section__heading--edit-link">Edit Location</a>
    </div>
    <div class="profile__part--contact">
      <div class="profile__detail">
        <h4 class="label profile__detail--label">Location</h4>
        <span class="profile__detail--data capitalize"><?= ($row->address_city &&$row->address_state) ? $row->address_city .', '. $row->address_state : 'N/A' ?></span>
      </div>
    </div>
  </div>
</section>