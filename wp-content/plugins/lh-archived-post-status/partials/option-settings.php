<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
<form name="form1" method="post" action="">
<?php wp_nonce_field( self::return_plugin_namespace()."-nonce", self::return_plugin_namespace()."-nonce", false ); ?>
<table class="form-table">
<tr valign="top">
<th scope="row"><label for="<?php echo self::return_publicly_available(); ?>"><?php _e("Can Archived Posts be read publicly:", self::return_plugin_namespace()); ?></label></th>
<td><select name="<?php echo self::return_publicly_available(); ?>" id="<?php echo self::return_publicly_available(); ?>">
<option value="1" <?php  if ($options[self::return_publicly_available()] == 1){ echo 'selected="selected"'; }  ?>>Yes - But not in the, main loop, frontpage, or feed</option>
<option value="0" <?php  if ($options[self::return_publicly_available()] == 0){ echo 'selected="selected"';}  ?>>No - only logged in users can view archived posts</option>
</select>
</td>
</tr>
<tr valign="top">
<th scope="row"><label for="<?php echo self::return_title_label_field_name(); ?>"><?php _e("Title Label:", self::return_plugin_namespace()); ?></label>
</th>
<td><input type="text" name="<?php echo self::return_title_label_field_name(); ?>" id="<?php echo self::return_title_label_field_name(); ?>" value="<?php echo $options[self::return_title_label_field_name()] ; ?>" size="20" /><br/><?php _e("This label will appear after the title for archived posts on the front end of your website", self::return_plugin_namespace()); ?>
</td>
</tr>


<tr valign="top">
<th scope="row"><label for="<?php echo self::return_message_field_name(); ?>"><?php _e("Archive Message:", self::return_plugin_namespace()); ?></label></th>
<td>
<?php
$settings = array( 'media_buttons' => false );
wp_editor( $options[self::return_message_field_name()], self::return_message_field_name(), $settings);
?>
</td>
</tr>
</table>



<?php submit_button( 'Save Changes' );
$roles = bp_blogs_get_allowed_roles();

print_r($roles);

	$suggestions = bp_core_get_suggestions( array(
		'group_id' => -145,  // A negative value will exclude this group's members from the suggestions.
		'limit'    => 10,
		'term'     => 'Keiran',
		'type'     => 'members',
	) );
	
	print_r($suggestions);
	
LH_Buddypress_multi_network_plugin::prepare_all_users();
	
//	print_r($bp);
	
	

?>
</form>