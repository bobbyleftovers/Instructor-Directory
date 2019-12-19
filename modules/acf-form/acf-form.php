<?php
$form_args = [];
$form_args['form'] = (isset($form)) ?  $form : true;
(isset($id)) ? $form_args['id'] = $id : null;
(isset($post_id)) ? $form_args['post_id'] = $post_id : null
(isset($fields)) ? $form_args['fields'] = $fields : null
(isset($submit_value)) ? $form_args['submit_value'] = $submit_value : null
(isset($updated_message)) ? $form_args['updated_message'] = $updated_message: null;
?>

<div class="acf-form-container">
  <?php acf_form($form_args);?>
</div>