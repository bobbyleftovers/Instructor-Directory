<?php
use BarrelDirectory\Classes\Lib\Media;

$module = 'data-plugin-module="image"';
if (!empty($cover)) {
  $class .= ' image--cover';
}
if (!empty($contain)) {
  $class .= ' image--contain';
}
if (!empty($top)) {
  $class .= ' image--top';
}
if (empty($size)) {
  $size = '';
}
if (empty($attributes)) {
  $attributes = '';
}
if (!isset($use_srcset)) {
  $use_srcset = true;
}
if (empty($image)) {
  $image = get_field( 'featured_image', 'options' );
}
?>
<figure class="js-wrap image <?= $class ?>" <?= $module; ?> <?= $attributes; ?>>
  <?php
    if (!empty($image)) {
      Media::the_lazy_img($image, $size, 'image__img', $size, $alt, $use_srcset);
    }

    if (!empty($content)) {
      echo $content;
    }
  ?>
</figure>
