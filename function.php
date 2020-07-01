
// Bikin Kolom Durasi
add_filter('manage_unit_posts_columns', 'myown_add_post_columns');

function myown_add_post_columns($columns) {
    $columns['vibe_durasi'] = 'DURASI';
    return $columns;
}

// Display data di kolom
add_action('manage_unit_posts_custom_column', 'myown_render_post_columns', 10, 2);

function myown_render_post_columns($column_name, $id) {
    switch ($column_name) {
    case 'vibe_durasi':
        // show my_field
        $my_fieldvalue = get_post_meta( $id, 'vibe_duration', TRUE);
        echo $my_fieldvalue;
    }
}

// bikin kolom di quick edit
add_action('quick_edit_custom_box',  'myown_add_quick_edit', 10, 2);

function myown_add_quick_edit($column_name, $post_type) {
    if ($column_name != 'vibe_durasi') return;
    ?>
    <fieldset class="inline-edit-col-left">
        <div class="inline-edit-col">
            <span class="title">DURASI</span>
            <input id="vibe_duration" type="hidden" name="vibe_duration_noncename" value="" />
            <input id="vibe_duration" type="text" name="vibe_duration" value=""/>
        </div>
    </fieldset>
     <?php
}



// buat save data
add_action('save_post', 'myown_save_quick_edit_data');   

function myown_save_quick_edit_data($post_id) {     
  // verify if this is an auto save routine.         
  if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )          
      return $post_id;         
  // Check permissions     
  if ( 'unit' == $_POST['post_type'] ) {         
    if ( !current_user_can( 'edit_page', $post_id ) )             
      return $post_id;     
  } else {         
    if ( !current_user_can( 'edit_post', $post_id ) )         
    return $post_id;     
  }        
  // Authentication passed now we save the data       
  if (isset($_POST['vibe_duration']) && ($post->post_type != 'revision')) {
        $my_fieldvalue = esc_attr($_POST['vibe_duration']);
        if ($my_fieldvalue)
            update_post_meta( $post_id, 'vibe_duration', $my_fieldvalue);
        else
            delete_post_meta( $post_id, 'vibe_duration');
    }
    return $my_fieldvalue;
}

// Add to our admin_init function
add_action('admin_footer', 'myown_quick_edit_javascript');

function myown_quick_edit_javascript() {
    global $current_screen;
    if (($current_screen->post_type != 'unit')) return;

    ?>
<script type="text/javascript">
function set_myfield_value(fieldValue, nonce) {
        // refresh the quick menu properly
        inlineEditPost.revert();
        console.log(fieldValue);
        jQuery('#vibe_duration').val(fieldValue);
}
</script>
 <?php 
}

// Add to our admin_init function 
add_filter('post_row_actions', 'myown_expand_quick_edit_link', 10, 2);   
function myown_expand_quick_edit_link($actions, $post) {     
    global $current_screen;     
    if (($current_screen->post_type != 'unit')) 
        return $actions;
    $nonce = wp_create_nonce( 'vibe_duration_'.$post->ID);
    $myfielvalue = get_post_meta( $post->ID, 'vibe_duration', TRUE);
    $actions['inline hide-if-no-js'] = '<a href="#" class="editinline" title="';     
    $actions['inline hide-if-no-js'] .= esc_attr( __( 'Edit this item inline' ) ) . '"';
    $actions['inline hide-if-no-js'] .= " onclick=\"set_myfield_value('{$myfielvalue}')\" >";
    $actions['inline hide-if-no-js'] .= __( 'Quick Edit' );
    $actions['inline hide-if-no-js'] .= '</a>';
    return $actions;
}
