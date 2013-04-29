<?php
/*
Plugin Name: Debug Actions / Filters
Version: 0.1
Plugin URI:	https://github.com/shrkey/debugactions
Description: Quick plugin to fire actions and filters from the admin area - dev sites only for obvious reasons
Author: Shrkey
Author URI: http://shrkey.com

Copyright 2013 (email: team@shrkey.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/**
* Debug actions class
*/
class debugactions
{

	var $actionoutput;
	var $filteroutput;

	function __construct()
	{
		add_action( 'admin_menu', array( &$this, 'add_menu') );
	}

	function add_menu() {
		$hook = add_management_page( __('Debug Actions','debugactions'), __('Debug Actions','debugactions'), 'manage_options', 'debugactions//', array( &$this, 'show_debug_actions'));

		add_action( 'load-' . $hook, array( &$this, 'process_debug_actions' ) );
	}

	function process_debug_actions() {

		if( !empty($_POST['debugactions_action'])) {

			check_admin_referer('debugactions');

			ob_start();

			$call = array();
			$parameters = explode("\n", $_POST['debugactions_actionparameters']);
			$parameters = array_map('trim', $parameters);

			$call[] = $_POST['debugactions_action'];

			foreach($parameters as $p) {
				array_push( $call, $p );
			}

			var_dump( call_user_func_array('do_action', $call ));
			$this->actionoutput = ob_get_contents();
			ob_end_clean();
		}

		if( !empty($_POST['debugactions_filter'])) {

			check_admin_referer('debugfilters');

			ob_start();
			$call = array();
			$parameters = explode("\n", $_POST['debugactions_filterparameters']);
			$parameters = array_map('trim', $parameters);

			$call[] = $_POST['debugactions_filter'];

			foreach($parameters as $p) {
				array_push( $call, $p );
			}

			var_dump( call_user_func_array('apply_filters', $call ));
			$this->filteroutput = ob_get_contents();
			ob_end_clean();

		}

	}

	function show_debug_actions() {
		$messages = array();
		$messages[1] = __('Operation processed.', 'debugactions');
		$messages[2] = __('Operations could not be processed.', 'debugactions');

		?>
		<div class="wrap">
		<div class="icon32" id="icon-options-general"><br></div>
		<h2><?php _e( 'Debug Actions / Filters', 'debugactions' ); ?></h2>

		<?php
		if ( isset($_GET['msg']) ) {
			echo '<div id="message" class="updated fade"><p>' . $messages[ (int) $_GET['msg'] ] . '</p></div>';
			$_SERVER['REQUEST_URI'] = remove_query_arg(array('msg'), $_SERVER['REQUEST_URI']);
		}
		?>

		<form action="?page=<?php echo esc_attr($_GET['page']); ?>" method="post">
			<input type='hidden' name='action' value='updateexpirepassword' />
			<?php
			wp_original_referer_field( true, 'previous' );
			wp_nonce_field( 'debugactions' );

			?>
				<h3><?php _e('Process Action','debugactions'); ?></h3>
				<p><?php _e('Enter the action in the top text box and the parameters in the large textarea - one per line.','debugactions'); ?></p>
				<?php
				if(!empty($this->actionoutput)) {
					?>
					<div class="updated fade"><p>
					<pre><?php echo $this->actionoutput; ?></pre>
					</p></div>
					<?php
				}
				?>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row">
								<?php _e( 'Action','debugactions' ); ?>
							</th>
							<td>
								<input name='debugactions_action' value='<?php echo esc_attr( (isset($_POST['debugactions_action'])) ? $_POST['debugactions_action'] : '' ); ?>' style='width: 50em;' />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<?php _e( 'Parameters','debugactions' ); ?>
							</th>
							<td>
								<textarea name='debugactions_actionparameters' style='width: 50em; height: 10em;'><?php echo esc_textarea( (isset($_POST['debugactions_actionparameters'])) ? $_POST['debugactions_actionparameters'] : ''); ?></textarea>
							</td>
						</tr>
					</tbody>
				</table>

				<p class="submit">
					<input type="submit" value="<?php _e( 'Process', 'debugactions' ); ?>" class="button button-primary" id="submit" name="submit">
				</p>
				</form>

				<form action="?page=<?php echo esc_attr($_GET['page']); ?>" method="post">
					<input type='hidden' name='action' value='updateexpirepassword' />
					<?php
					wp_original_referer_field( true, 'previous' );
					wp_nonce_field( 'debugfilters' );
				?>

				<h3><?php _e('Process Filter','debugactions'); ?></h3>
				<p><?php _e('Enter the filter in the top text box and the parameters in the large textarea - one per line.','debugactions'); ?></p>
				<?php
				if(!empty($this->filteroutput)) {
					?>
					<div class="updated fade"><p>
					<pre><?php echo $this->filteroutput; ?></pre>
					</p></div>
					<?php
				}
				?>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row">
								<?php _e( 'Filter','debugactions' ); ?>
							</th>
							<td>
								<input name='debugactions_filter' value='<?php echo esc_attr( (isset($_POST['debugactions_filter'])) ? $_POST['debugactions_filter'] : ''); ?>' style='width: 50em;' />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<?php _e( 'Parameters','debugactions' ); ?>
							</th>
							<td>
								<textarea name='debugactions_filterparameters' style='width: 50em; height: 10em;'><?php echo esc_textarea( (isset($_POST['debugactions_filterparameters'])) ? $_POST['debugactions_filterparameters'] : ''); ?></textarea>
							</td>
						</tr>
					</tbody>
				</table>

			<p class="submit">
				<input type="submit" value="<?php _e( 'Process', 'debugactions' ); ?>" class="button button-primary" id="submit" name="submit">
			</p>

		</form>

		</div>
		<?php
	}


}

$debugactions = new debugactions();