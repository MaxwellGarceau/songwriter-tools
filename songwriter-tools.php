<?php
/**
 * Plugin Name:       Upload Song
 * Plugin URI:        https://resume.maxgarceau.com/
 * Description:       Upload music to the WP Media library from the front end of your website
 * Version:           0.1.0
 * Requires at least: 6.6
 * Requires PHP:      7.2
 * Author:            Max Garceau
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       songwriter-tools
 *
 * @package           songwriter-tools
 */

use Max_Garceau\Songwriter_Tools\Songwriter_Tools;
use Max_Garceau\Songwriter_Tools\Endpoints\Api;
use Max_Garceau\Songwriter_Tools\Endpoints\Auth;
use Max_Garceau\Songwriter_Tools\Endpoints\Validation;
use Max_Garceau\Songwriter_Tools\Endpoints\Controllers\Song_Controller;
use Max_Garceau\Songwriter_Tools\Includes\Register_Songs_CPT;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Load Composer autoloader if it exists.
 */
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// TODO: Switch to PHP-DI for dependency injection
$songwriter_tools = new Songwriter_Tools(
    new Register_Songs_CPT(),
    new Api( new Auth(), new Validation(), new Song_Controller() )
);
$songwriter_tools->init();

/**
 * Registers a custom block category for Songwriter Tools.
 *
 * This function adds a new block category called "Songwriter Tools" to the list of available
 * block categories in the WordPress block editor. This custom category can be used to group
 * blocks related to songwriting tools, making them easier to find and organize.
 *
 * @param array   $categories Array of block categories.
 * @param WP_Post $post       Post being edited.
 * @return array Modified array of block categories.
 */
function songwriter_tools_register_block_category( $categories, $post ) {
    // Add a custom category called "Songwriter Tools"
    return array_merge(
        $categories,
        [
            [
                'slug'  => 'songwriter-tools',
                'title' => __( 'Songwriter Tools', 'songwriter-tools' ),
            ],
        ]
    );
}
add_filter( 'block_categories_all', 'songwriter_tools_register_block_category', 10, 2 );

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function songwriter_tools_upload_song_block_init() {
	register_block_type_from_metadata( __DIR__ . '/build/blocks/upload-song' );
}
add_action( 'init', 'songwriter_tools_upload_song_block_init' );
