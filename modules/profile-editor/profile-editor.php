<?php
use BarrelDirectory\Classes\DB\Db_Control;
// use BarrelDirectory\Classes\Util\Template_Utils as Tmpl;
use BarrelDirectory\Classes\Admin\Profile_Editor as ProfileEdit;

$editor = new ProfileEdit($user_id, $profile_id);
// set up the viewer
$row = false;
if($is_published){
  $DB = new Db_Control();
  $row = $DB->find($profile_id)[0];
}?>

<section class="profile-editor" data-plugin-module="profile-editor">
  <form id="profile-editor-form" action="<?= $_SERVER['REQUEST_URI'] ?>" method="POST"><?php
    if(!$is_published){?>
      <div class="profile-draft-status">
        <p class="profile-draft-status__message">Your profile has not been completed yet! It will remain hidden to visitors until filled out with the necessary details.</p>
      </div><?php
    }?>
    <fieldset class="fieldset">
      <div class="input-wrap input-wrap__profile-image input--acf">
        <?php $editor->get_image_editor();?>
      </div>
    </fieldset>
    <fieldset class="fieldset">
      <div class="input-wrap input-wrap__first-name input--manual">
        <label for="" class="label">First Name<sup>*</sup></label>
        <input class="input validate-input" placeholder="First Name" type="text" name="first_name" placeholder="First Name" value="<?=($editor->get_user_prop('first_name')) ? $editor->get_user_prop('first_name') : null ?>" required />
      </div>
      <div class="input-wrap input-wrap__last-name input--manual">
        <label for="" class="label">Last Name<sup>*</sup></label>
        <input class="input validate-input" placeholder="Last Name" type="text" name="last_name" placeholder="Last Name" value="<?=($editor->get_user_prop('last_name')) ? $editor->get_user_prop('last_name') : null ?>" required />
      </div>
      <div class="input-wrap input-wrap__email input--manual">
        <label for="" class="label">Email<sup>*</sup></label>
        <input class="input validate-input" placeholder="Email" type="text" name="email" placeholder="Email" value="<?=($editor->get_user_prop('user_email')) ? $editor->get_user_prop('user_email') : null ?>" required />
      </div>
      <div class="input-wrap input-wrap__phone input--manual">
        <label for="" class="label">Phone Number (optional)</label>
        <input id="phone" class="input validate-input" placeholder="Phone Number" type="text" name="phone" placeholder="(234) 122-3434" value="<?=(isset($row->contact_info_phone)) ? $row->contact_info_phone : null ?>"/>
      </div>
    </fieldset>
    <fieldset class="fieldset">
      <div class="input-wrap input-wrap__studios input--acf">
        <label for="" class="label">Your Studios<sup>*</sup></label>
        <?php $editor->get_studios_editor();?>
      </div>
    </fieldset>
    <fieldset class="fieldset">
      <div class="input-wrap input-wrap__certifications input--manual">
        <div class="certification-inner">
          <label for="" class="label">Certifications (You may choose a max of 3)<sup>*</sup></label>
          <div class="checkboxes"><?php
            $all_certifications = get_terms( 'certification', array(
              'hide_empty' => false,
              'orderby'  => 'slug',
              'order'    => 'ASC'
            ));
            $profile_certifications = get_the_terms( $profile_id, 'certification' );
            $i = 0;
            foreach($all_certifications as $cert){?>
              <div class="checkbox__outter">
                <div class="checkbox-wrap">
                  <input type="checkbox" class="input__checkbox" data-index="'.$i.'" name="certifications[]" value="<?= $cert->slug ?>"<?php
                    foreach($profile_certifications as $p_cert){
                      if($p_cert->slug === $cert->slug){
                        echo ' checked ';
                        break;
                      }
                      $i++;
                    }?>
                  />
                  <!-- <span class="checkbox-check">&nbsp;</span> -->
                  <span class="checkbox-label"><?= $cert->name ?></span>
                </div>
              </div><?php
            }?>
          </div>
        </div>
      </div>
    </fieldset>
    <fieldset class="fieldset">
      <div class="input-wrap input-wrap__about">
        <label for="" class="label">Personal Bio<sup>*</sup></label>
        <textarea class="input validate-input" placeholder="" type="text" name="about" placeholder="Tell us about yourself"><?=(isset($row->basic_info_about)) ? $row->basic_info_about : null ?></textarea>
      </div>
    </fieldset>
    <fieldset class="fieldset">
      <div class="input-wrap input-wrap__languages">
        <label for="" class="label">Languages (Mark all that apply)</label>
        <div class="checkboxes"><?php
          $all_languages = get_terms( 'language', array(
            'hide_empty' => false,
            'orderby'  => 'slug',
            'order'    => 'ASC'
          ));
          $profile_languages = get_the_terms( $profile_id, 'language' );
          foreach($all_languages as $lang){?>
            <div class="checkbox__outter w-50">
              <div class="checkbox-wrap">
                <input type="checkbox" name="languages[]" value="<?= $lang->slug ?>" <?php
                  foreach($profile_languages as $p_lang){
                    if($p_lang->slug === $lang->slug){
                      echo ' checked ';
                      break;
                    }
                  }?>
                />
                <!-- <span class="checkbox-check">&nbsp;</span> -->
                <span class="checkbox-label"><?= $lang->name ?></span>
              </div>
            </div><?php
          }?>
        </div>
      </div>
    </fieldset>
    <fieldset class="fieldset">
      <div class="input-wrap input-wrap__website">
        <label for="" class="label">Personal Website (optional)</label>
        <input class="input input-website validate-input" placeholder="Your Site" type="text" name="website" placeholder="Your personal website" value="<?=(isset($row->contact_info_website)) ? $row->contact_info_website : null ?>"/>
      </div>
      <div class="input-wrap input-wrap__facebook">
        <label for="" class="label">Facebook (optional)</label>
        <input class="input input-website validate-input" placeholder="Facebook" type="text" name="facebook" placeholder="" value="<?=(isset($row->social_media_facebook_profile)) ? $row->social_media_facebook_profile : null ?>"/>
      </div>
      <div class="input-wrap input-wrap__instagram">
        <label for="" class="label">Instagram (optional)</label>
        <input class="input input-website validate-input" placeholder="Instagram" type="text" name="instagram" placeholder="" value="<?=(isset($row->social_media_instagram_profile)) ? $row->social_media_instagram_profile : null ?>"/>
      </div>
      <div class="input-wrap input-wrap__twitter">
        <label for="" class="label">Twitter (optional)</label>
        <input class="input input-website validate-input" placeholder="Twitter" type="text" name="twitter" placeholder="" value="<?=(isset($row->social_media_twitter_profile)) ? $row->social_media_twitter_profile : null ?>"/>
      </div>
      <div class="input-wrap input-wrap__youtube">
        <label for="" class="label">YouTube (optional)</label>
        <input class="input input-website validate-input" placeholder="YouTube" type="text" name="youtube" placeholder="" value="<?=(isset($row->social_media_youtube_profile)) ? $row->social_media_youtube_profile : null ?>"/>
      </div>
    </fieldset>
    <fieldset class="fieldset">
      <div class="input-wrap input-wrap__submit">
        <input type="hidden" name="user_id" value="<?=$user_id?>"/>
        <input type="hidden" name="profile_id" value="<?=$profile_id?>"/>
        <a href="<?= site_url()?>/directory/my-account" class="profile-editor__cancel show-desktop">Cancel</a>
        <input id="profile-submit" class="button" type="submit" name="submit" value="Save Changes"/>
        <a href="<?= site_url()?>/directory/my-account" class="profile-editor__cancel show-mobile">Cancel</a>
      </div>
    </fieldset>
  </form>
</section>
