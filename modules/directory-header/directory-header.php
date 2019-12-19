<?php
// Some setup
// $link_after_title = ($add_link && $link_after_title) ? $link_after_title : false;
// $wrapper_classes .= ($add_link && !$link_after_title) ? ' directory__header--grid' : null;
// $wrapper_classes .= ($add_link && $link_after_title) ? ' directory__header--title-link' : null;
$left_link_url = ($left_link_url) ? $left_link_url : false;
$left_link_copy = ($left_link_copy) ? $left_link_copy : false;
$right_link_url = ($right_link_url) ? $right_link_url : false;
$right_link_copy = ($right_link_copy) ? $right_link_copy : false;
$admin = ($admin) ? $admin : false;
?>

<section class="container">
  <div class="directory__header <?= $wrapper_classes ?>"><?php
    if($left_link_url && $left_link_copy){?>
      <a href="<?= $left_link_url?>" class="label back-link back-link--left">
        <span class="icon icon--angle-left"></span>
        <span class="back-link-text"><?= $left_link_copy ?></span>
      </a><?php
    } ?>
    <div class="diretory-header__title-wrap"><?php echo '<h1 class="text-center '. $title_class .'">'. $title;?></h1></div><?php
    if($right_link_url && $right_link_copy){?>
      <a href="<?= $right_link_url?>" class="label back-link back-link--right">
        <span class="back-link-text"><?= $right_link_copy ?></span>
      </a><?php
    }
    echo (isset($description)) ? '<p class="main-title__description">'.$description.'</p>' : null; ?>
    
  </div>
</section>