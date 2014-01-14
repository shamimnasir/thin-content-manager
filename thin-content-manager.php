<?php
/*
Plugin Name: Thin Content Manager
Plugin URI: http://pluginspire.com
Author: msfreed
Author URI: http://pluginspire.com
Description: See the body word count to identify pages with thin content, then select pages to disallow in robots.txt and insert robots noindex,nofollow tags into.
Version: 1.0.0
License: GPLv3
*/

defined('ABSPATH') OR exit;

require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');

define('TCM_TXTDOMAIN', 'thin_content_manager');
define('TCM_OPTION', 'tcm_option');

if(is_admin())
{

	add_action('wp_ajax_tcm_option', array('tcm_hooks', 'tcm_option_update'));
	$admin = new tcm_admin_management();
	add_action('admin_menu', array($admin, 'plugin_menu'));

	$plugin = plugin_basename(__FILE__);
	add_filter("plugin_action_links_$plugin", array($admin, 'settings_link'));
	add_filter('plugin_row_meta', array($admin, 'plugin_row'), 10, 2);
}
else
{
	add_action('wp_head', array('tcm_hooks', 'tcm_process_head'));
}


class tcm_util
{
	static function word_count($string)
	{
		return str_word_count(strip_tags($string));
	}
}


class tcm_hooks
{
	static function tcm_option_update()
	{
		if($_GET['action'] == 'tcm_option')
			update_post_meta((int)$_GET['post_id'], TCM_OPTION, (int)$_GET['option']);
	
		die();
	}

	static function tcm_process_head()
	{
		global $wp_query;
		if($wp_query->post)
		{
			$meta = get_post_meta($wp_query->post->ID);

			if(!empty($meta) && $meta['tcm_option'][0] == 1)
				echo '<meta name="robots" content="noindex,nofollow" />';
		}
	}
}

class tcm_admin_management
{
	function settings_link($links)
	{
		$settings_link = '<a href="options-general.php?page=thin-content-manager">Settings</a>';
		array_unshift($links, $settings_link);
		return $links;
	}

	function plugin_row($links, $file)
	{
		if ($file == plugin_basename(__FILE__))
		{
			$links[] = '<a href="http://pluginspire.com/thin-content-manager-pro" target="_blank">Get the Pro Edition</a>';
		}

		return $links;
	}


	function plugin_menu()
	{
		add_options_page('Thin Content Manager', 'Thin Content Manager', 'manage_options', 'thin-content-manager', array($this, 'plugin_options'));
	}

	function plugin_options()
	{
		$this->render_settings();
		$this->render_report_header();

		$wp_list_table = new tcm_management_table();
		$wp_list_table->prepare_items($_POST['s']);
		$wp_list_table->display();

		$this->render_report_footer();
	}

	function render_settings()
	{
 		?>
		<script>
			jQuery(function(){
				jQuery(".tcm_option_update").change(function(){
					jQuery.get(ajaxurl + '?action=tcm_option&option='+jQuery(this).val()+'&post_id='+jQuery(this).data('id'))
				});
			});
		</script>
		<div class="wrap">
			<form method="POST" action="">
				<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
    			<h2>Thin Content Manager</h2>

    			<h3>Automation Settings</h3>
				<p style="color:red;">
    				Automation settings are not available for the free version of this plugin. <a href="http://pluginspire.com/thin-content-manager-pro" target="_lank">Upgrade to Thin Content Manager Pro</a>
    				to have noindex/nofollow tags automatically inserted into "thin content" pages, based on the minimum word count you specify
    				(which may optionally include 'Comments' word count). Note: Automation settings may easily be overwritten on a page-by-page basis.
    			</p>

    			<p>
					<input type="checkbox" disabled="disabled"/>
    				Automatically insert noindex/nofollow tags into the &lt;head&gt; section of all "thin content pages" (can be overwritten on a page-by-page basis)
    			</p>

    			<p>
    				Consider pages with less than
    				<input type="text" size="25" class="small-text code" value="n/a" disabled="disabled"/>
    				words to be "thin content pages"
    			</p>

	    		<p style="margin-bottom:.2em;">
	    			Word count includes:
	    			<fieldset style="border:none;">
	    				<label for="bodyOnly" style="display:block;">
							<input type="radio" id="bodyOnly" style="margin-left:20px;margin-right:20px;" disabled="disabled" />
							Body content only
    					</label>
    					<label for="bodyAndComments" style="display:block;">
							<input type="radio" id="bodyAndComments" value="2" style="margin-left:20px;margin-right:20px;" disabled="disabled" />
							Body content + comments
						</label>
					</fieldset>
				</p>

				<p class="submit">
					<input type="button" class="button button-primary" value="Save Changes" disabled="disabled">
				</p>
			</form>
			<form method="POST" action="">
		<?php
	}

	function render_report_header()
	{
		?>
		<h3 style="margin-bottom:0;">Noindex/Nofollow Tag Manager</h3>
		<p style="color:red;">
			The Automated option is not available for the free version of this plugin (upgrade to <a href="http://pluginspire.com/thin-content-manager-pro" target="_lank">Pro now</a>).
		</p>
		<p>
			Select Override: YES to manually insert noindex/nofollow tags into the &lt;head&gt; section of the page.<br/>
			Leave Override: NO selected if you do not want to insert noindex/nofollow tags on the page.
		</p>
		<?php
	}

	function render_report_footer()
	{
		?> 
			</form>
		</div>
		<?php
	}
}

class tcm_management_table extends WP_List_Table
{
    function __construct()
	{
		parent::__construct( array(
			'singular' => 'tcm_post',
			'plural'   => 'tcm_post',
			'ajax'     => false
			));
	}

    function get_columns()
	{

        return array(
			'post_title'    => __('Page/Post Title', TCM_TXTDOMAIN),
        	'post_type'		=> __('Type', TCM_TXTDOMAIN),
			'post_words'    => __('Body Word Count', TCM_TXTDOMAIN),
			'comment_words'	=> __('Comments Word Count', TCM_TXTDOMAIN),
			'total_words'	=> __('Total Word Count', TCM_TXTDOMAIN),
			'tcm_option'	=> __("Noindex/Nofollow Tags?", TCM_TXTDOMAIN),
			);
    }

    function get_sortable_columns()
	{
        return array(
			'post_title' => array('post_title', false),
			'post_type'  => array('post_type', false),
			'tcm_option' => array('tcm_option', false)
			);
    }


    function prepare_items($search)
	{
        global $wpdb;

        $perpage = 50;

		$meta_def = 2;

		$filter = '';

		$orderby = '';
		if(!empty($_GET['orderby']))
		{
			$orderby = 'ORDER BY ';

			if($_GET['orderby'] == 'tcm_option')
				$orderby = $orderby . "ifnull(m.meta_value, '$meta_def')";
			else
				$orderby = $orderby . mysql_real_escape_string($_GET['orderby']);
		}
		if(!empty($_GET['order']))
			$orderby = $orderby . ' ' . mysql_real_escape_string($_GET['order']);

		$limit = '';
		if(!empty($_GET['paged']))
		{
			$paged = ($_GET['paged'] - 1) * $perpage;
			$limit = "LIMIT $paged, $perpage";
		}
		else
			$limit = "LIMIT $perpage";

		$where = "WHERE p.post_type in ('post', 'page') and p.post_status = 'publish' $filter";
		
		$query = "SELECT count(*) FROM $wpdb->posts p $where";
		$totalitems = $wpdb->get_var($query);

		$query = "
			SELECT p.ID, p.post_title, p.post_type, p.post_content, ifnull(m.meta_value, '$meta_def') tcm_option
			FROM $wpdb->posts p
				LEFT JOIN $wpdb->postmeta m on m.post_id = p.ID and m.meta_key = 'tcm_option'
			$where $orderby $limit";
		$results = $wpdb->get_results($query);

        $this->set_pagination_args(array(
            'total_items' 	=> $totalitems,
            'total_pages' 	=> ceil($totalitems / $perpage),
            'per_page' 		=> $perpage
        	));

        $this->_column_headers = array(
            $this->get_columns(),
            array(),
            $this->get_sortable_columns(),
        );

        $GLOBALS['wp_filter']["manage_{$GLOBALS['screen']->id}_screen_columns"];

        $this->items = $results;
    }

    function display_rows()
    {
        $records = $this->items;
        list($columns, $hidden) = $this->get_column_info();

        if(!empty($records))
        {
        	foreach($records as $rec)
	        {

				$word_count = tcm_util::word_count($rec->post_content);

				echo '<tr id="record_' . $rec->ID . "\">\n";

	            foreach ($columns as $column_name => $column_display_name)
	            {	               
	                $style = '';
	                if (in_array($column_name, $hidden))
	                	$style = ' style="display:none;"';

					$class = "class='$column_name column-$column_name'";
	                $attributes = $class . $style;

	                switch ($column_name)
	                {
	                    case "post_title":
	                    	echo "<td $attributes ><a href=\"post.php?post=" . $rec->ID . '&action=edit" target="_blank">' . stripslashes($rec->post_title) . "</a></td>\n";
	                    	break;

	                    case "post_type":
	                   		echo "<td $attributes >" . ($rec->post_type == 'page' ? 'Page' : 'Post') . "</td>\n";
	                    	break;

	                    case "post_words":
	                    	echo "<td $attributes >$word_count</td>\n";
	                    	break;

	                    case "comment_words":
	                    	echo "<td $attributes >n/a</td>\n";
	                    	break;

	                    case "total_words":
	                    	echo "<td $attributes >n/a</td>\n";
	                    	break;

	                    case "tcm_option":
	                        echo "<td $attributes >\n";
	                        echo ' <select name="tcm_option[' . $rec->ID . ']" data-id="' . $rec->ID . "\" class=\"tcm_option_update\">\n";
		        			echo "  <option value=\"0\" disabled=\"disabled\">Automated</option>\n";
	                        echo '  <option value="1"' . ($rec->tcm_option == 1 ? 'selected="selected"' : '') . ">Override: YES</option>\n";
	                        echo '  <option value="2"' . ($rec->tcm_option == 2 ? 'selected="selected"' : '') . ">Override: NO</option>\n";
	                        echo " </select>\n";
	                        echo "</td>\n";
	                        break;
	                }
	            }

	            echo '</tr>';
	        }
	    }
    }
}