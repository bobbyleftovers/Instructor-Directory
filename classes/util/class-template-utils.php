<?php
namespace BarrelDirectory\Classes\Util;
Class Template_Utils {
  public function image ($image, $class = '', $size = 'full', $sizes = '', $alt = '') {
    $id = null;
    if(is_numeric($image)){
      $id = $image;
    }
    $img = ( wp_get_attachment_image_src( $id, $size ) ) ? wp_get_attachment_image_src( $id, $size )[0] : BARREL_DIRECTORY_ASSETS_URI . '/svg/image-placeholder.svg';
    $blank = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
    $html = '<img %s src="%s" data-normal="%s" data-retina="%s" srcset="%s" alt="%s" %s>';?>
    <figure class="image <?=$class?>"><?php
      printf(
        $html,
        "class=\"image__img ${class}\"",
        $blank,
        $img,
        $img,
        wp_get_attachment_image_srcset($id, $size) ? wp_get_attachment_image_srcset($id, $size) : $img,
        $alt,
        empty( $sizes ) ? '' : "sizes=\"${sizes}\""
      );?>
    </figure><?php
  }

  public function formatted_phone ($phone = null) {
    if($phone) {
      $phone = preg_replace("/[^0-9]/", '', $phone );
      if(  preg_match( '/(\d{3})(\d{3})(\d{4})$/', $phone,  $matches ) ){
          $phone = $matches[1] . '.' .$matches[2] . '.' . $matches[3];
      }
    }
    return $phone;
  }

  function validate_phone ($mobile) {
    return preg_match('/^[0-9]{10}+$/', $mobile);
  }

  public function formatted_website ($website = null) {
    if($website) {
      $website = str_replace('https://', '', $website);
      $website = str_replace('http://', '', $website);
      $website = str_replace('www.', '', $website);
      $website = str_replace('/', '', $website);
    }
    return $website;
  }

  public function get_gmaps_search_link($string){
    return 'https://www.google.com/maps/search/?api=1&query=' . urlencode($string);
  }
}