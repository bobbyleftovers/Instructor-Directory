<?php
// title
// title class
// has link?
// link url
// link text
?>

<section class="container">
	<div class="directory__header <?= ($add_link) ? 'directory__header--grid' : '' ?>">
		<h1 class="text-center <?=$title_class?>"><?=$title?></h1><?php
		if($add_link){?>
			<a href="<?= $link_url?>" class="label back-link">
				<span class="back-link-text"><span class="icon icon--angle-left"></span><?=$link_text?></span>
			</a><?php
		}?>
	</div>
</section>