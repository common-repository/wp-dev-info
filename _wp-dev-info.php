<?php
/*
Plugin Name: WP Dev Info
Plugin URI: http://www.thatwebguyblog.com/post/wp-developer-info-for-wordpress/
Description: Displays information on both front and back ends during development, such as memory usage, number of database queries and more. 
Author: Michael Ott (@mikeyott)
Version: 1.02
Last Updated: 07/Mar/2014
Author URI: http://michaelott.id.au/
Beta Testers: 
	Matthew Woods (@matty_woods)
	David Evans (@greyearlgrey)
*/

// Register settings
function wp_dev_info_settings_init(){
    register_setting( 'wp_dev_info_settings', 'wp_dev_info_settings' );
}

// Add settings page to menu
function add_settings_page() {
$icon_path = plugins_url( 'icon.png', __FILE__ );
add_menu_page( __( 'WP Dev Info' ), __( 'WP Dev Info' ), 'manage_options', 'wpdssettings', 'wp_dev_info_settings_page' ,$icon_path);
}

// Add actions
add_action( 'admin_init', 'wp_dev_info_settings_init' );
add_action( 'admin_menu', 'add_settings_page' );

// Start settings page
function wp_dev_info_settings_page() {
?>

<div class="wrap">
<h2><?php _e( 'WP Dev Info' );?></h2>

<?php // show saved options message
if($_GET['settings-updated'] == 'true') { ?>
	<div id="message" class="updated fade"><p><strong><?php _e( 'Options saved' ); ?></strong></p></div>
<?php } ?>

<form method="post" action="options.php">
	<?php settings_fields( 'wp_dev_info_settings' ); ?>
    <?php $options = get_option( 'wp_dev_info_settings' ); ?>
    <h3>Additional Options</h3>
    <p>These options are for showing developer information on the <a href="<?php echo home_url(); ?>">front-end</a>. A widget has been automatically added to the admin <a href="index.php">dashboard</a>.</p>
    
    <table class="form-table" style="margin:20px 0 0 0;">
    <tbody>
      <tr>
        <th><strong><?php _e( 'Show on front-end' ); ?></strong></th>
        <td><input id="wp_dev_info_settings[show_front_end]" name="wp_dev_info_settings[show_front_end]" type="checkbox" value="1" <?php if ( isset( $options['show_front_end'] ) ) { checked( '1', $options['show_front_end'] );} ?> class="wpds-opener" /> <label for="wp_dev_info_settings[show_front_end]"><?php _e( 'Show the status toolbar on the front-end (only visible to administrators).' ); ?></label></td>
      </tr>
     </tbody>  
    </table>
	
    <div<?php if ( isset( $options['show_front_end']) == false) { echo " class='wp-status-extra-options'"; } ?>>
    <h3>Show in the front-end toolbar...</h3>
	<table class="form-table">
    <tbody>
      <tr>
        <th><strong><?php _e( 'Show Database Query Count' ); ?></strong></th>
        <td><input id="wp_dev_info_settings[show_db_queries]" name="wp_dev_info_settings[show_db_queries]" type="checkbox" value="1" <?php if ( isset( $options['show_db_queries'] ) ) { checked( '1', $options['show_db_queries'] );} ?> /> <label for="wp_dev_info_settings[show_db_queries]"><?php _e( '' ); ?></label></td>
      </tr>
      <tr>
        <th><strong><?php _e( 'Show RAM usage' ); ?></strong></th>
        <td><input id="wp_dev_info_settings[ram_usage]" name="wp_dev_info_settings[ram_usage]" type="checkbox" value="1" <?php if ( isset( $options['ram_usage'] ) ) { checked( '1', $options['ram_usage'] );} ?> /> <label for="wp_dev_info_settings[ram_usage]"><?php _e( '' ); ?></label></td>
      </tr>
      <tr>
        <th><strong><?php _e( 'Show PHP version' ); ?></strong></th>
        <td><input id="wp_dev_info_settings[phpversion]" name="wp_dev_info_settings[phpversion]" type="checkbox" value="1" <?php if ( isset( $options['phpversion'] ) ) { checked( '1', $options['phpversion'] );} ?> /> <label for="wp_dev_info_settings[phpversion]"><?php _e( '' ); ?></label></td>
      </tr>
      <tr>
        <th><strong><?php _e( 'Show MySQL info' ); ?></strong></th>
        <td><input id="wp_dev_info_settings[mysql_info]" name="wp_dev_info_settings[mysql_info]" type="checkbox" value="1" <?php if ( isset( $options['mysql_info'] ) ) { checked( '1', $options['mysql_info'] );} ?> /> <label for="wp_dev_info_settings[mysql_info]"><?php _e( '' ); ?></label></td>
      </tr>
      <tr>
        <th><strong><?php _e( 'Show Wordpress version' ); ?></strong></th>
        <td><input id="wp_dev_info_settings[wp_version]" name="wp_dev_info_settings[wp_version]" type="checkbox" value="1" <?php if ( isset( $options['wp_version'] ) ) { checked( '1', $options['wp_version'] );} ?> /> <label for="wp_dev_info_settings[wp_version]"><?php _e( '' ); ?></label></td>
      </tr>
    </tbody>  
    </table>
    </div>
    
    <p><input name="submit" id="submit" class="button button-primary" value="Save Settings" type="submit" /></p>
</form>

</div>

<?php }
//sanitize and validate
function options_validate( $input ) {
    global $select_options, $radio_options;
    if ( ! isset( $input['option1'] ) )
        $input['option1'] = null;
    $input['option1'] = ( $input['option1'] == 1 ? 1 : 0 );
    $input['sometext'] = wp_filter_nohtml_kses( $input['sometext'] );
    if ( ! isset( $input['radioinput'] ) )
        $input['radioinput'] = null;
    if ( ! array_key_exists( $input['radioinput'], $radio_options ) )
        $input['radioinput'] = null;
    $input['sometextarea'] = wp_filter_post_kses( $input['sometextarea'] );
    return $input;
}

// Add jQuery into admin head only for this plugin
if($_GET['page']=='wpdssettings') {
	add_action('admin_head', 'wpds_admin_scripts');
	function wpds_admin_scripts() { 
	$wpds_admin_scripts_head = '<script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>';
	  echo $wpds_admin_scripts_head;
	  print "\n";
	}
}

// Add CSS into front-end head
$options = get_option('wp_dev_info_settings'); if (is_array($options) && $options['show_front_end'] == true) {
	add_action('wp_head', 'devstatuscss');
	function devstatuscss() { 
	$sbx_head = '
	<!--/ WP Dev Info CSS /-->
	<link rel="stylesheet" href="' . plugins_url( 'wp-dev-info.css', __FILE__ ) . '" type="text/css" media="all" />';
	  echo $sbx_head;
	  print "\n";
	}
}

// Add CSS into admin head
	add_action('admin_head', 'devstatuscssadmin');
	function devstatuscssadmin() { 
	$wpds_admin_head = '
	<!--/ WP Dev Info Admin CSS /-->
	<link rel="stylesheet" href="' . plugins_url( 'wp-dev-info-admin.css', __FILE__ ) . '" type="text/css" media="all" />
	<script type="text/javascript">
	$(document).ready(function(){
		// Toggle the additional options
		$(".wpds-opener").click(function() {
			$(".wp-status-extra-options").slideToggle("fast");
		});
	});
	</script>
	';
	echo $wpds_admin_head;
	print "\n";
}


// Output into footer
add_action('wp_footer', 'wpdiptions');
function wpdiptions() { ?>

	<?php
	if (current_user_can('administrator')) { // Only show on front end for administrators
    $options = get_option('wp_dev_info_settings'); if (is_array($options) && $options['show_front_end'] == true) {  ?>
        <ul class="wp-dev-info-environment">
        <?php if ($options['show_db_queries'] == true) { 
            echo "<li><span>" . get_num_queries() . "</span> queries in <span>";
            timer_stop(1);
            echo "</span> secs</li>\n";
        }
        ?>
        <?php if ($options['phpversion'] == true) { 
            echo "<li>PHP <span>v" . PHP_VERSION . "</span></li>\n";
        }
        ?>
        <?php if ($options['mysql_info'] == true) { 
            echo "<li>MySQL <span>v" . mysql_get_server_info() . "</span></li>\n";
        }
        ?>
        <?php if ($options['wp_version'] == true) { 
            $wp_version = get_bloginfo(version);
            echo "<li>Wordpress <span>v" . $wp_version . "</span></li>\n";
        }
        ?>
        <?php if ($options['ram_usage'] == true) { 
			$ramused = round(memory_get_usage() / 1024 / 1024, 2);
			$ramavailable = esc_html((int)get_cfg_var('memory_limit'));
			$rampercentage = ($ramused * 100) / $ramavailable;
            if ($rampercentage >= 75 && ($rampercentage < 90)) { $warning = " class='first-warning'"; }
			elseif ($rampercentage > 90) { $warning = " class='second-warning'"; }
            echo "<li>RAM <span".$warning.">" . $ramused . 'M</span> of <span>' . $ramavailable . "</span> used (" . round($rampercentage, 1) . "%)</li>\n";
          }
		}
        ?>
        <?php if (is_array($options) && $options['show_front_end'] == true) { echo "</ul>"; } ?>
    <?php } ?>
    
<?php }

// Add dashboard widget
function wpds_dashboard_widget_function() {
	$ramused = round(memory_get_usage() / 1024 / 1024, 2);
	$ramavailable = esc_html((int)get_cfg_var('memory_limit'));
	$rampercentage = ($ramused * 100) / $ramavailable;
	if ($rampercentage <= 10) { $moveem = " class='moveem'"; $warningbg = "#5dc500"; }
	if ($rampercentage > 10 && ($rampercentage < 75)) { $warning = " class='no-warning'"; $warningbg = "#5dc500"; }
	if ($rampercentage >= 75 && ($rampercentage < 90)) { $warning = " class='first-warning'"; $warningbg = "#ff8a00"; }
	if ($rampercentage > 90) { $warning = " class='second-warning'"; $warningbg = "#ff0000"; }
	echo "<ul class='wpds-dash'>";
	echo "<li><span>IP Address</span>" . esc_html($_SERVER['SERVER_ADDR']) . "</li>";
	echo "<li><span>PHP</span>v" . PHP_VERSION . "</li>";
	echo "<li><span>MySQL</span>v" . mysql_get_server_info() . "</li>";
	echo "<li><span>Wordpress</span>v" . get_bloginfo(version) . "</li>";
	echo "</ul>";
	echo "<p><strong>PHP Memory:</strong> <span".$warning.">" . $ramused . 'm</span> of <span>' . $ramavailable . "m</span> used (" . round($rampercentage, 1) . "%)</p>";
	echo "<div class='progress-back'><div class='progress-bar' style='width:" . $rampercentage . "%; background:" . $warningbg . "'><em" . $moveem . ">" . round($rampercentage, 1) . "%</em></div></div>";
}
function add_wpds_dashboard_widgets() {
	wp_add_dashboard_widget('wpds_dashboard_widget', 'WP Dev Info', 'wpds_dashboard_widget_function');
}
add_action('wp_dashboard_setup', 'add_wpds_dashboard_widgets' );

function footer_memory_info () {
	$ramused = round(memory_get_usage() / 1024 / 1024, 2);
	$ramavailable = esc_html((int)get_cfg_var('memory_limit'));
	$rampercentage = ($ramused * 100) / $ramavailable;
    echo "PHP Memory:</strong> <span".$warning.">" . $ramused . 'm</span> of <span>' . $ramavailable . "m</span> used (" . round($rampercentage, 1) . "%)";
}
add_filter('admin_footer_text', 'footer_memory_info');