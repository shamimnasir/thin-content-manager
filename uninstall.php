<?php

defined('ABSPATH') OR exit;
defined('WP_UNINSTALL_PLUGIN') OR exit;

function tcm_uninstall()
{
	if (!is_user_logged_in() && !current_user_can('manage_options'))
		wp_die(__( 'Authorized Access Required', 'thin_content_manager'));

	if (!is_multisite())
	{
		global $wpdb;
		delete_option('tcm_option');
		$wpdb->query("delete from $wpdb->postmeta where meta_key = 'tcm_option'");
	}
	else
	{
		global $wpdb;
		$blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
		$original_blog_id = get_current_blog_id();
		foreach ($blog_ids as $blog_id) 
		{
			switch_to_blog($blog_id);
			delete_site_option('tcm_option');
		}
		switch_to_blog($original_blog_id);
	}

	return;
}

tcm_uninstall();
?>