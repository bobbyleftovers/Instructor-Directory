<?php
use BarrelDirectory\Classes\Admin\User_Registration;
$reg = new User_Registration(); ?>

<div class="register-form" data-plugin-module="register-form">
  <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="post" class="directory__register admin-form">

    <div class="registration__input validate input-wrap registration__input--fname w-50 pad-right">
      <label id="fname-reg-label" for="firstname">First Name</label>
      <input aria-labelled-by="fname-reg-label" tabindex="1" class="w-100" type="text" name="fname" value="<?= ( isset( $_POST['fname']) ? $_POST['fname'] : null ) ?>">
    </div>
    
    <div class="registration__input validate input-wrap registration__input--lname w-50 pad-left">
      <label id="lname-reg-label" for="website">Last Name</label>
      <input aria-labelled-by="lname-reg-label" tabindex="2" class="w-100" type="text" name="lname" value="<?= ( isset( $_POST['lname']) ? $_POST['lname'] : null ) ?>">
    </div>
    
    <div class="registration__input validate input-wrap registration__input--email  w-100">
      <label id="email-reg-label" for="email">Email <strong>*</strong></label>
      <input aria-labelled-by="email-reg-label" tabindex="3" class="w-100" type="text" name="email" value="<?= ( isset( $_POST['email']) ? $_POST['email'] : null ) ?>">
    </div>

    <div class="registration__input validate input-wrap registration__input--pass w-100">
      <label id="password-reg-label" for="password">Password <strong>*</strong></label>
      <input aria-labelled-by="password-reg-label" tabindex="4" class="w-100" type="password" name="password" value="">
    </div>

    <div class="registration__input input-wrap registration__input--checkbox w-100">
      <div class="checkbox-wrap">
        <input aria-label="newsletter singup" tabindex="5" type="checkbox" name="newsletter" value="<?= ( isset( $_POST['newsletter'] ) ? $_POST['newsletter'] : null ) ?>">
        <span class="checkbox-check">&nbsp;</span>
      </div>
      <label for="newsletter">Sign me up for the newsletter</label>
    </div>

    <div class="registration__input input-wrap registration__input--submit w-100">
      <input aria-label="submit registration" tabindex="6" class="button w-100" type="submit" name="submit" value="Register"/>
    </div>

    <p class="w-100 text-center admin-form__bottom-link">Already have an account? <a href="<?=site_url().'/directory/login'?>">Sign in here</a></p><?php

    if ( isset($_POST['submit'] ) ) {
      $reg->init_user($_POST['fname'].' '.$_POST['lname'], $_POST['password'], $_POST['email'], $_POST['fname'], $_POST['lname']);
      $reg->registration_validation();
    } ?>
  </form>
</div>