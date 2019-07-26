<?php
class User_Taxonomy {
	// user taxonomy editors and pages
	public function __construct () {
		$this->job_meta_key = '_job_title';
    add_action( 'admin_menu', array($this, 'add_job_title_admin_page') );
    add_filter( 'submenu_file', array($this, 'set_submenu_active') );
    add_action( 'show_user_profile', array($this, 'job_title_select') );
    add_action( 'edit_user_profile', array($this, 'job_title_select') );
    add_action( 'personal_options_update', array($this, 'save_job_title' ) );
    add_action( 'edit_user_profile_update', array($this, 'save_job_title' ) );
	}
  public function add_job_title_admin_page() {
    $taxonomy = get_taxonomy( 'job_title' );
    add_users_page(
      esc_attr( $taxonomy->labels->menu_name ),//page title
      esc_attr( $taxonomy->labels->menu_name ),//menu title
      $taxonomy->cap->manage_terms,//capability
      'edit-tags.php?taxonomy=' . $taxonomy->name//menu slug
    );
  }

  public function set_submenu_active( $submenu_file ) {
    global $parent_file;
    if( 'edit-tags.php?taxonomy=job_title' == $submenu_file ) {
      $parent_file = 'users.php';
    }
    return $submenu_file;
  }

  public function job_title_select ($user) {
    $taxonomy = get_taxonomy( 'job_title' );
	
    if ( !user_can( $user, 'instructor' ) ) {
      return;
    }
    ?>
    <table class="form-table">
      <tr>
        <th>
          <label for="<?= $this->job_meta_key ?>">Job Title</label>
        </th>
        <td><?php
          $user_category_terms = get_terms( array(
            'taxonomy' => 'job_title',
            'hide_empty' => 0
          ) );
          
          $select_options = array();
          
          foreach ( $user_category_terms as $term ) {
            $select_options[$term->term_id] = $term->name;
          }
          
          $meta_values = get_user_meta( $user->ID, $this->job_meta_key, true );
          
          echo $this->form_select(
            $this->job_meta_key,
            $meta_values,
            $select_options,
            '',
            array( 'multiple' =>'multiple' )
          );?>
        </td>
      </tr>
    </table>
    <?php
  }

  public function form_select( $name, $value, $options, $default_var ='', $html_params = array() ) {
    if( empty( $options ) ) {
      $options = array( '' => '---choose---');
    }
  
    $html_params_string = '';
    
    if( !empty( $html_params ) ) {
      if ( array_key_exists( 'multiple', $html_params ) ) {
        $name .= '[]';
      }
      foreach( $html_params as $html_params_key => $html_params_value ) {
        $html_params_string .= " {$html_params_key}='{$html_params_value}'";
      }
    }
  
    echo "<select name='{$name}'{$html_params_string}>";
    
    foreach( $options as $options_value => $options_label ) {
      if( ( is_array( $value ) && in_array( $options_value, $value ) )
        || $options_value == $value ) {
        $selected = " selected='selected'";
      } else {
        $selected = '';
      }
      if( empty( $value ) && !empty( $default_var ) && $options_value == $default_var ) {
        $selected = " selected='selected'";
      }
      echo "<option value='{$options_value}'{$selected}>{$options_label}</option>";
    }
  
    echo "</select>";
  }

  public function save_job_title( $user_id ) {
    $tax = get_taxonomy( 'job_title' );
    $user = get_userdata( $user_id );
    // die($user_id);
    // if ( !user_can( $user, 'instructor' ) ) {
    //   return false;
    // }
    // die(print_r($_POST));
    $new_categories_ids = $_POST[$this->job_meta_key];
    $user_meta = get_user_meta( $user_id, $this->job_meta_key, true );
    $previous_categories_ids = array();
    
    if( !empty( $user_meta ) ) {
      $previous_categories_ids = (array)$user_meta;
    }
    // die($_POST['role']);
    // if( ( current_user_can( 'administrator' ) && $_POST['role'] != 'instructor' ) ) {
    //   delete_user_meta( $user_id, $this->job_meta_key );
    //   $this->job_title_count( $previous_categories_ids, array() );
    // } else {
      update_user_meta( $user_id, $this->job_meta_key, $new_categories_ids );
      $this->job_title_count( $previous_categories_ids, $new_categories_ids );
    // }
  }

  public function job_title_count( $previous_terms_ids, $new_terms_ids ) {
    global $wpdb;
  
    $terms_ids = array_unique( array_merge( (array)$previous_terms_ids, (array)$new_terms_ids ) );
    
    if( count( $terms_ids ) < 1 ) { return; }
    
    foreach ( $terms_ids as $term_id ) {
      $count = $wpdb->get_var(
        $wpdb->prepare(
          "SELECT COUNT(*) FROM $wpdb->usermeta WHERE meta_key = %s AND meta_value LIKE %s",
          $this->job_meta_key,
          '%"' . $term_id . '"%'
        )
      );
      $wpdb->update( $wpdb->term_taxonomy, array( 'count' => $count ), array( 'term_taxonomy_id' => $term_id ) );
    }
  }
}