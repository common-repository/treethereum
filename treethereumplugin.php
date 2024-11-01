<?php
/*
Plugin Name: TreethereumPlugin
Plugin URI: https://www.treethereum.com/
Description: Invite friends to join as your treethereum root on your WordPress site.
Version: 1.0.0
Author: TreethereumPlugin
Text Domain: treethereumplugin
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Explicitly globalize to support bootstrapped WordPress
global
	$TREETHEREUM_plugin_basename, $TREETHEREUM_options, $TREETHEREUM_plugin_dir, $TREETHEREUM_plugin_url_path,
	$TREETHEREUM_services, $TREETHEREUM_amp_icons_css;

$TREETHEREUM_plugin_basename = plugin_basename( dirname( __FILE__ ) );
$TREETHEREUM_plugin_dir = untrailingslashit( plugin_dir_path( __FILE__ ) );
$TREETHEREUM_plugin_url_path = untrailingslashit( plugin_dir_url( __FILE__ ) );

// HTTPS?
$TREETHEREUM_plugin_url_path = is_ssl() ? str_replace( 'http:', 'https:', $TREETHEREUM_plugin_url_path ) : $TREETHEREUM_plugin_url_path;
// Set plugin options
$TREETHEREUM_options = get_option( 'treethereumplugin_options', array() );

function TREETHEREUM_init() {
	global $TREETHEREUM_plugin_dir,
		$TREETHEREUM_plugin_basename,
		$TREETHEREUM_options;

	// Load the textdomain for translations
	load_plugin_textdomain( 'treethereumplugin', false, $TREETHEREUM_plugin_basename . '/languages/' );
}
add_filter( 'init', 'TREETHEREUM_init' );


// [treethereumplugin url="https://www.example.com/page.html" title="Example Page"]
function TREETHEREUM_shortcode( $attributes ) {
	$attributes = shortcode_atts( array(
		'buyButtonText'     => '',
		'minimum'   => '',
		'step'   => '',
		'placeholder' => '',
		'gaslimit' => '',
		'tokenname' => '',
		'description' => '',
	), $attributes, 'treethereumplugin' );
	$options = stripslashes_deep( get_option( 'treethereumplugin_options', array() ) );

	$gaslimit = ! empty( $attributes['gaslimit'] ) ? $attributes['gaslimit'] :
        (! empty( $options['gaslimit'] ) ? esc_attr( $options['gaslimit'] ) : "200000");

	$tokenName = ! empty( $attributes['tokenname'] ) ? $attributes['tokenname'] :
			  (! empty( $options['tokenname'] ) ? esc_attr( $options['tokenname'] ) : "treethereum");

	$placeholder = ! empty( $attributes['placeholder'] ) ? $attributes['placeholder'] :
        (! empty( $options['placeholder'] ) ? esc_attr( $options['placeholder'] ) : "0.2");

	$step = ! empty( $attributes['step'] ) ? $attributes['step'] :
        (! empty( $options['step'] ) ? esc_attr( $options['step'] ) : "0.0");

	$minimum = ! empty( $attributes['minimum'] ) ? $attributes['minimum'] : 0.2;

	$buyButtonText = ! empty( $attributes['buyButtonText'] ) ? $attributes['buyButtonText'] :
        (! empty( $options['buyButtonText'] ) ? esc_attr( $options['buyButtonText'] ) : "Buy $tokenName with<br>Metamask");

	$description = ! empty( $attributes['description'] ) ? $attributes['description'] :
        (! empty( $options['description'] ) ? esc_attr( $options['description'] ) :
        "Make sure that you send ether from a MetaMask address." .
        " Always keep your seed words and passphrase safe, otherwise you may not be able to receive " .
        "the revenue from your roots. Do not send ETH directly from an exchange to the contract address. Visit treethereum.com for more help.");

	$etherscanApiKey = ! empty( $options['etherscanApiKey'] ) ? esc_attr( $options['etherscanApiKey'] ) :
        'ZWDA55NK7H3AUB2IHSNTC9TIUEUZQ9A7JM';

	$infuraApiKey = ! empty( $options['infuraApiKey'] ) ? esc_attr( $options['infuraApiKey'] ) :
        'PvS4F8rY0hxE3td43bPj';

	$crowdsaleAddress = ! empty( $options['crowdsaleAddress'] ) ? /*esc_attr*/( $options['crowdsaleAddress'] ) : '0xe652459d2802bae508b81698f0906b0bdcd4347f';

	$txData = ! empty( $options['txData'] ) ? /*esc_attr*/( $options['txData'] ) : '';


    $rateData = "null";
    $etherscanEndpoint = "https://api.etherscan.io/api?module=stats&action=ethprice&apikey=" . $etherscanApiKey;
    $response = wp_remote_get( $etherscanEndpoint, array('sslverify' => false) );
    if( !is_wp_error( $response ) ) {
        $http_code = wp_remote_retrieve_response_code( $response );
        if (200 == $http_code) {
            $body = wp_remote_retrieve_body( $response );
            if ($body) {
                $j = json_decode($body, true);
                if (isset($j["result"])) {
                    $rateData = json_encode($j["result"]);
                }
            }
        }
    }


	$js =
'<script type="text/javascript">
    var rateData = '.$rateData.';
    var infuraApiKey = "'.$infuraApiKey.'";
    var web3Endpoint = "https://ropsten.infura.io/" + infuraApiKey;
    var crowdsaleAddress = "'.$crowdsaleAddress.'";
		var txData = "'.$txData.'";
		var decimals = 1000000000000000000;
</script>';

	$ret =
'<div class="treethereumplugin-shortcode">
    <div class="treethereumplugin-content">
        <h2 class="treethereumplugin-gaslimit">Gas Limit: '.$gaslimit.'</h2>
				<div class="treethereumplugin-button-group">
            <div class="treethereumplugin-quantity">
                <input disabled type="number" name="etherInput" id="etherInput" placeholder="'.$placeholder.'" step="'.$step.'" min="'.$minimum.'" class="treethereumplugin-bottom-input-one">
                <div class="quantity-nav">
				<div style="display:none;">
                    <div class="quantity-button quantity-up">
                        <i class="fa fa-chevron-up" aria-hidden="true"></i>
                    </div>
                    <div class="quantity-button quantity-down">
                        <i class="fa fa-chevron-down" aria-hidden="true"></i>
                    </div>
				</div>
                </div>
            </div>
            <span class="eth">ETH</span>
            <a class="treethereumplugin-bottom-button-two" href="#" id="buytreethereumButton">'.$buyButtonText.'</a>
            <div class="clear"></div>
        </div>
        <h4><span id="rateToken" class="treethereumplugin-rate-token">0</span>&nbsp;'.$tokenName.'<br>
            <span id="rateUSD" class="treethereumplugin-rate-usd">0</span>&nbsp;USD
        </h4>
        <p class="treethereumplugin-description">'.$description.'</p>
    </div>
</div>';
    return $js . str_replace("\n", " ", str_replace("\r", " ", str_replace("\t", " ", $ret)));
}

add_shortcode( 'treethereumplugin', 'TREETHEREUM_shortcode' );


function TREETHEREUM_stylesheet() {
	global $TREETHEREUM_options, $TREETHEREUM_plugin_url_path;

	$options = $TREETHEREUM_options;

    wp_enqueue_style( 'treethereumplugin', $TREETHEREUM_plugin_url_path . '/treethereumplugin.css', false, '1.0.0' );
    wp_enqueue_style( 'font-awesome', $TREETHEREUM_plugin_url_path . '/font-awesome.css', false, '4.7.0' );
}

add_action( 'wp_enqueue_scripts', 'TREETHEREUM_stylesheet', 20 );

function TREETHEREUM_enqueue_script() {
	global $TREETHEREUM_plugin_url_path;

	if ( wp_script_is( 'jquery', 'registered' ) ) {
		wp_enqueue_script( 'treethereumplugin', $TREETHEREUM_plugin_url_path . '/treethereumplugin.js', array( 'jquery' ), '1.0.0' );
	}
    wp_enqueue_script( 'web3', $TREETHEREUM_plugin_url_path . '/web3.min.js', array( 'jquery' ), '0.20.2' );
}

add_action( 'wp_enqueue_scripts', 'TREETHEREUM_enqueue_script' );

/**
 * Admin Options
 */

if ( is_admin() ) {
	include_once $TREETHEREUM_plugin_dir . '/treethereumplugin.admin.php';
}

function TREETHEREUM_add_menu_link() {
	$page = add_options_page(
		__( 'TreethereumPlugin Share Settings', 'treethereumplugin' ),
		__( 'TreethereumPlugin', 'treethereumplugin' ),
		'manage_options',
		'treethereumplugin',
		'TREETHEREUM_options_page'
	);
}

add_filter( 'admin_menu', 'TREETHEREUM_add_menu_link' );

// Place in Option List on Settings > Plugins page
function TREETHEREUM_actlinks( $links, $file ) {
	// Static so we don't call plugin_basename on every plugin row.
	static $this_plugin;

	if ( ! $this_plugin ) {
		$this_plugin = plugin_basename( __FILE__ );
	}

	if ( $file == $this_plugin ) {
		$settings_link = '<a href="options-general.php?page=treethereumplugin">' . __( 'Settings' ) . '</a>';
		array_unshift( $links, $settings_link ); // before other links
	}

	return $links;
}

add_filter( 'plugin_action_links', 'TREETHEREUM_actlinks', 10, 2 );
