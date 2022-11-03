<?php defined('ABSPATH') || exit;

/**
 * Plugin Name:  TokenICO
 * Version:      1.1.2
 * Plugin URI:   https://tokenico.beycanpress.com
 * Description:  Cryptocurrency presale (ICO & IDO) plugin for WordPress
 * Author URI:   https://beycanpress.com
 * Author:       BeycanPress
 * Tags:         Cryptocurrency presale (ICO & IDO) plugin for WordPress, Binance Smart Chain token presale, Ethereum token presale, PHP ICO & IDO presale script
 * Text Domain:  tokenico
 * License:      GPLv3
 * License URI:  https://www.gnu.org/licenses/gpl-3.0.tr.html
 * Domain Path:  /languages
 * Requires at least: 5.0
 * Tested up to: 5.9
 * Requires PHP: 7.3
*/

require __DIR__ . '/vendor/autoload.php';
new \BeycanPress\Tokenico\Loader(__FILE__);