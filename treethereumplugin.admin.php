<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
//
function TREETHEREUM_options_page() {

	// Require admin privs
	if ( ! current_user_can( 'manage_options' ) )
		return false;

	$new_options = array();

	// Which tab is selected?
	$possible_screens = array( 'default', 'floating' );
	$current_screen = ( isset( $_GET['action'] ) && in_array( $_GET['action'], $possible_screens ) ) ? $_GET['action'] : 'default';

	if ( isset( $_POST['Submit'] ) ) {

		// Nonce verification
		check_admin_referer( 'treethereumplugin-update-options' );

        // Standard options screen

        $new_options['tokenname']        = ( ! empty( $_POST['TREETHEREUM_token_name'] )       /*&& is_numeric( $_POST['TREETHEREUM_token_name'] )*/ )       ? sanitize_text_field($_POST['TREETHEREUM_token_name'])       : 'Treethereum Token';
        $new_options['gaslimit']         = ( ! empty( $_POST['TREETHEREUM_gaslimit'] )         && is_numeric( $_POST['TREETHEREUM_gaslimit'] ) )         ? intval(sanitize_text_field($_POST['TREETHEREUM_gaslimit']))         : 200000;
        $new_options['placeholder']      = ( ! empty( $_POST['TREETHEREUM_placeholder'] )      /*&& is_numeric( $_POST['TREETHEREUM_placeholder'] )*/ )      ? sanitize_text_field($_POST['TREETHEREUM_placeholder'])      : 'Input ETH amount';
        $new_options['step']             = ( ! empty( $_POST['TREETHEREUM_step'] )             && is_numeric( $_POST['TREETHEREUM_step'] ) )             ? floatval(sanitize_text_field($_POST['TREETHEREUM_step']))             : 0.0;
        $new_options['buyButtonText']    = ( ! empty( $_POST['TREETHEREUM_buyButtonText'] )    /*&& is_numeric( $_POST['TREETHEREUM_buyButtonText'] )*/ )    ? sanitize_text_field($_POST['TREETHEREUM_buyButtonText'])    : 'Buy Treethereum with Metamask';
        $new_options['description']      = ( ! empty( $_POST['TREETHEREUM_description'] )      /*&& is_numeric( $_POST['TREETHEREUM_description'] )*/ )      ? sanitize_text_field($_POST['TREETHEREUM_description'])      : '';
        $new_options['etherscanApiKey']  = ( ! empty( $_POST['TREETHEREUM_etherscanApiKey'] )  /*&& is_numeric( $_POST['TREETHEREUM_etherscanApiKey'] )*/ )  ? sanitize_text_field($_POST['TREETHEREUM_etherscanApiKey'])  : '';
        $new_options['infuraApiKey']     = ( ! empty( $_POST['TREETHEREUM_infuraApiKey'] )     /*&& is_numeric( $_POST['TREETHEREUM_infuraApiKey'] )*/ )     ? sanitize_text_field($_POST['TREETHEREUM_infuraApiKey'])     : '';
        $new_options['crowdsaleAddress'] = ( ! empty( $_POST['TREETHEREUM_crowdsaleAddress'] )     /*&& is_numeric( $_POST['TREETHEREUM_crowdsaleAddress'] )*/ )     ? sanitize_text_field($_POST['TREETHEREUM_crowdsaleAddress'])     : '';
				$new_options['txData']     			 = ( ! empty( $_POST['TREETHEREUM_txData'] )     /*&& is_numeric( $_POST['TREETHEREUM_txData'] )*/ )     						 ? sanitize_text_field($_POST['TREETHEREUM_txData'])     : '';


		// Get all existing treethereumplugin options
		$existing_options = get_option( 'treethereumplugin_options', array() );

		// Merge $new_options into $existing_options to retain treethereumplugin options from all other screens/tabs
		if ( $existing_options ) {
			$new_options = array_merge( $existing_options, $new_options );
		}

        if ( get_option('treethereumplugin_options') ) {
            update_option('treethereumplugin_options', $new_options);
        } else {
            $deprecated=' ';
            $autoload='no';
            add_option('treethereumplugin_options', $new_options, $deprecated, $autoload);
        }

		?>
		<div class="updated"><p><?php _e( 'Settings saved.' ); ?></p></div>
		<?php

	} else if ( isset( $_POST['Reset'] ) ) {
		// Nonce verification
		check_admin_referer( 'treethereumplugin-update-options' );

		delete_option( 'treethereumplugin_options' );
	}

	$options = stripslashes_deep( get_option( 'treethereumplugin_options', array() ) );

	?>

	<div class="wrap">

	<h1><?php _e( 'treethereum Plugin Settings', 'treethereumplugin' ); ?></h1>

	<h2 class="nav-tab-wrapper">
		<a href="<?php echo admin_url( 'options-general.php?page=treethereumplugin' ); ?>" class="nav-tab<?php if ( 'default' == $current_screen ) echo ' nav-tab-active'; ?>"><?php esc_html_e( 'Standard' ); ?></a>
	</h2>

	<form id="treethereumplugin_admin_form" method="post" action="">

	<?php wp_nonce_field('treethereumplugin-update-options'); ?>

		<table class="form-table">

		<?php if ( 'default' == $current_screen ) : ?>
			<tr valign="top">
			<th scope="row"><?php _e("treethereum options", 'treethereumplugin'); ?></th>
			<td><fieldset>
				<label>
                    <input class="hidden" name="TREETHEREUM_token_name" type="text" maxlength="32" placeholder="treethereum" value="<?php echo ! empty( $options['tokenname'] ) ? esc_attr( $options['tokenname'] ) : 'treethereum'; ?>">
                    <p></p>
                </label>
			</fieldset></td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php _e("Gas Limit", 'treethereumplugin'); ?></th>
			<td><fieldset>
				<label>
                    <input class="text" name="TREETHEREUM_gaslimit" type="number" min="0" step="10000" maxlength="8" placeholder="200000" value="<?php echo ! empty( $options['gaslimit'] ) ? esc_attr( $options['gaslimit'] ) : '200000'; ?>">
                    <p>The gas limit to buy treethereum, 200000 is recommended.</p>
                </label>
			</fieldset></td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php _e("Placeholder", 'treethereumplugin'); ?></th>
			<td><fieldset>
				<label>
                    <input disabled class="text" name="TREETHEREUM_placeholder" type="text" maxlength="128" placeholder="0.2" value="<?php echo ! empty( $options['placeholder'] ) ? esc_attr( $options['placeholder'] ) : '0.2'; ?>">
                    <p>0.2 ETH is the cost to join the treethereum forest. You could add more, but it is not recommended.</p>
                </label>
			</fieldset></td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php _e("ETH Step", 'treethereumplugin'); ?></th>
			<td><fieldset>
				<label>
                    <input class="text" name="TREETHEREUM_step" type="number" min="0" step="0.2" maxlength="8" placeholder="0.2" value="<?php echo ! empty( $options['step'] ) ? esc_attr( $options['step'] ) : '0.1'; ?>">
                    <p>The step to adjust ETH amount with up/down buttons</p>
                </label>
			</fieldset></td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php _e("Buy Button Text", 'treethereumplugin'); ?></th>
			<td><fieldset>
				<label>
                    <input class="text" name="TREETHEREUM_buyButtonText" type="text" maxlength="128" placeholder="Buy treethereum with Metamask" value="<?php echo ! empty( $options['buyButtonText'] ) ? esc_attr( $options['buyButtonText'] ) : 'Buy treethereum with MetaMask'; ?>">
                    <p>The text to display on the BUY button</p>
                </label>
			</fieldset></td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php _e("Description", 'treethereumplugin'); ?></th>
			<td><fieldset>
                <textarea class="large-text" name="TREETHEREUM_description" type="text" maxlength="10240" placeholder="Add some notes"><?php echo ! empty( $options['description'] ) ? esc_textarea( $options['description'] ) : ''; ?></textarea>
			</fieldset></td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php _e("Transaction data", 'treethereumplugin'); ?></th>
			<td><fieldset>
				<label>
                    <input class="text" name="TREETHEREUM_txData" type="text" maxlength="1024" placeholder="0x" value="<?php echo ! empty( $options['txData'] ) ? esc_attr( $options['txData'] ) : ''; ?>">
                    <p>Enter your ethereum address to get revenue from new users. You should have already bought a treethereum tree, starts with '0x' without quotes. <a href="https://www.treethereum.com/" target="_blank"></a></p>
                </label>
			</fieldset></td>
			</tr>



			<tr valign="top">
			<th scope="row"><?php _e("Etherscan Api Key", 'treethereumplugin'); ?></th>
			<td><fieldset>
				<label>
                    <input class="text" name="TREETHEREUM_etherscanApiKey" type="text" maxlength="35" placeholder="Put your Etherscan Api Key here" value="<?php echo ! empty( $options['etherscanApiKey'] ) ? esc_attr( $options['etherscanApiKey'] ) : ''; ?>">
                    <p>The API key for the <a target="_blank" href="https://etherscan.io/myapikey">https://etherscan.io</a>.
                        You can <a target="_blank" href="https://etherscan.io/register">register</a> on this site to get one or leave blank to use the default provided for you.</p>
                    <p>Install some of the <a target="_blank" href="https://codex.wordpress.org/Class_Reference/WP_Object_Cache#Persistent_Cache_Plugins">persistent hash WP plugins</a>
                        to overcome the etherscan API limits. In this case the API would be queried only once per 5 minutes. <a href="https://www.treethereum.com/" target="_blank"></a></p>
                </label>
			</fieldset></td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php _e("Infura.io Api Key", 'treethereumplugin'); ?></th>
			<td><fieldset>
				<label>
                    <input class="text" name="TREETHEREUM_infuraApiKey" type="text" maxlength="35" placeholder="Put your Infura Api Key here" value="<?php echo ! empty( $options['infuraApiKey'] ) ? esc_attr( $options['infuraApiKey'] ) : ''; ?>">
                    <p>The API key for the <a target="_blank" href="https://infura.io/register.html">https://infura.io/</a>.
                        You need to register on this site to get one or leave blank to use the default provided for you.</p>
                </label>
			</fieldset></td>
			</tr>

			<tr valign="top">
			<th scope="row"><?php _e("The contract address", 'treethereumplugin'); ?></th>
			<td><fieldset>
				<label>
                    <input disabled class="text" name="TREETHEREUM_crowdsaleAddress" type="text" maxlength="45" placeholder="0xe652459d2802bae508b81698f0906b0bdcd4347f" value="<?php echo ! empty( $options['crowdsaleAddress'] ) ? esc_attr( $options['crowdsaleAddress'] ) : ''; ?>">
                    <p>The treethereum contract address.</p>
                </label>
			</fieldset></td>
			</tr>

		<?php endif; ?>

		</table>

		<p class="submit">
			<input class="button-primary" type="submit" name="Submit" value="<?php _e('Save Changes', 'treethereumplugin' ) ?>" />
			<input id="TREETHEREUM_reset_options" type="submit" name="Reset" onclick="return confirm('<?php _e('Are you sure you want to delete all treethereum plugin options?', 'treethereumplugin' ) ?>')" value="<?php _e('Reset', 'treethereumplugin' ) ?>" />
		</p>

	</form>

    <a href="https://www.treethereum.com/">Visit us for additional help and promos.</a>
    </div>

<?php

}
