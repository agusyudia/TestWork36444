<?php
/**
 * Storefront engine room
 *
 * @package storefront
 */

/**
 * Assign the Storefront version to a var
 */
$theme              = wp_get_theme( 'storefront' );
$storefront_version = $theme['Version'];

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 980; /* pixels */
}

$storefront = (object) array(
	'version'    => $storefront_version,

	/**
	 * Initialize all the things.
	 */
	'main'       => require 'inc/class-storefront.php',
	'customizer' => require 'inc/customizer/class-storefront-customizer.php',
);

require 'inc/storefront-functions.php';
require 'inc/storefront-template-hooks.php';
require 'inc/storefront-template-functions.php';
require 'inc/wordpress-shims.php';

if ( class_exists( 'Jetpack' ) ) {
	$storefront->jetpack = require 'inc/jetpack/class-storefront-jetpack.php';
}

if ( storefront_is_woocommerce_activated() ) {
	$storefront->woocommerce            = require 'inc/woocommerce/class-storefront-woocommerce.php';
	$storefront->woocommerce_customizer = require 'inc/woocommerce/class-storefront-woocommerce-customizer.php';

	require 'inc/woocommerce/class-storefront-woocommerce-adjacent-products.php';

	require 'inc/woocommerce/storefront-woocommerce-template-hooks.php';
	require 'inc/woocommerce/storefront-woocommerce-template-functions.php';
	require 'inc/woocommerce/storefront-woocommerce-functions.php';
}

if ( is_admin() ) {
	$storefront->admin = require 'inc/admin/class-storefront-admin.php';

	require 'inc/admin/class-storefront-plugin-install.php';
}

/**
 * NUX
 * Only load if wp version is 4.7.3 or above because of this issue;
 * https://core.trac.wordpress.org/ticket/39610?cversion=1&cnum_hist=2
 */
if ( version_compare( get_bloginfo( 'version' ), '4.7.3', '>=' ) && ( is_admin() || is_customize_preview() ) ) {
	require 'inc/nux/class-storefront-nux-admin.php';
	require 'inc/nux/class-storefront-nux-guided-tour.php';
	require 'inc/nux/class-storefront-nux-starter-content.php';
}

/**
 * Note: Do not add any custom code here. Please use a custom plugin so that your customizations aren't lost during updates.
 * https://github.com/woocommerce/theme-customisations
 */


// Add Custome Plugin 
// Register Custom Post Type: Cities
function register_cities_post_type() {
    $labels = array(
        'name'          => __( 'Cities', 'textdomain' ),
        'singular_name' => __( 'City', 'textdomain' ),
    );

    $args = array(
        'labels'       => $labels,
        'public'       => true,
        'has_archive'  => true,
        'supports'     => array( 'title', 'editor', 'thumbnail' ),
        'menu_icon'    => 'dashicons-location-alt',
        'show_in_rest' => true,  // Ensure this is set to true
    );

    register_post_type( 'cities', $args );
}
add_action( 'init', 'register_cities_post_type' );


// Add Meta Box for Latitude and Longitude
function add_city_meta_box() {
    add_meta_box(
        'city_location_meta',
        __( 'City Location', 'textdomain' ),
        'render_city_meta_box',
        'cities',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'add_city_meta_box' );

function render_city_meta_box( $post ) {
    $latitude  = get_post_meta( $post->ID, '_latitude', true );
    $longitude = get_post_meta( $post->ID, '_longitude', true );

    wp_nonce_field( 'city_meta_nonce_action', 'city_meta_nonce' );

    echo '<label for="latitude">' . __( 'Latitude:', 'textdomain' ) . '</label>';
    echo '<input type="text" id="latitude" name="latitude" value="' . esc_attr( $latitude ) . '" size="25" /><br>';
    echo '<label for="longitude">' . __( 'Longitude:', 'textdomain' ) . '</label>';
    echo '<input type="text" id="longitude" name="longitude" value="' . esc_attr( $longitude ) . '" size="25" />';
}

function save_city_meta_box( $post_id ) {
    if ( ! isset( $_POST['city_meta_nonce'] ) || ! wp_verify_nonce( $_POST['city_meta_nonce'], 'city_meta_nonce_action' ) ) {
        return;
    }

    if ( isset( $_POST['latitude'] ) ) {
        update_post_meta( $post_id, '_latitude', sanitize_text_field( $_POST['latitude'] ) );
    }
    if ( isset( $_POST['longitude'] ) ) {
        update_post_meta( $post_id, '_longitude', sanitize_text_field( $_POST['longitude'] ) );
    }
}
add_action( 'save_post', 'save_city_meta_box' );

// Register Custom Taxonomy: Countries
function register_countries_taxonomy() {
    $labels = array(
        'name'          => __( 'Countries', 'textdomain' ),
        'singular_name' => __( 'Country', 'textdomain' ),
    );

    $args = array(
        'labels'       => $labels,
        'public'       => true,
        'hierarchical' => true,
        'show_in_rest' => true,
    );

    register_taxonomy( 'countries', 'cities', $args );
}
add_action( 'init', 'register_countries_taxonomy' );

// Create a Widget to Display City and Temperature
class City_Temperature_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'city_temperature_widget',
            __( 'City Temperature', 'textdomain' ),
            array( 'description' => __( 'Display a city and its current temperature.', 'textdomain' ) )
        );
    }

    public function widget( $args, $instance ) {
        $city_id = ! empty( $instance['city_id'] ) ? $instance['city_id'] : 0;

        if ( $city_id ) {
            $city_name = get_the_title( $city_id );
            $latitude  = get_post_meta( $city_id, '_latitude', true );
            $longitude = get_post_meta( $city_id, '_longitude', true );

            $api_key = '352e891a7481549efeeb7fd3e9ceae08';
            $api_url = "https://api.openweathermap.org/data/3.0/onecall/overview?lat={$latitude}&lon={$longitude}&appid={$api_key}";

            $response = wp_remote_get( $api_url );
            if ( is_array( $response ) && ! is_wp_error( $response ) ) {
                $weather_data = json_decode( wp_remote_retrieve_body( $response ), true );
                $temperature  = $weather_data['main']['temp'];
                echo $args['before_widget'];
                echo $args['before_title'] . $city_name . $args['after_title'];
                echo '<p>Temperature: ' . $temperature . ' °C</p>';
                echo $args['after_widget'];
            }
        }
    }

    public function form( $instance ) {
        $city_id = ! empty( $instance['city_id'] ) ? $instance['city_id'] : 0;

        echo '<label for="' . $this->get_field_id( 'city_id' ) . '">' . __( 'Select City:', 'textdomain' ) . '</label>';
        echo '<select id="' . $this->get_field_id( 'city_id' ) . '" name="' . $this->get_field_name( 'city_id' ) . '">';
        echo '<option value="">' . __( '-- Select City --', 'textdomain' ) . '</option>';

        $cities = get_posts( array( 'post_type' => 'cities', 'numberposts' => -1 ) );
        foreach ( $cities as $city ) {
            echo '<option value="' . $city->ID . '"' . selected( $city_id, $city->ID, false ) . '>' . $city->post_title . '</option>';
        }
        echo '</select>';
    }

    public function update( $new_instance, $old_instance ) {
        $instance            = $old_instance;
        $instance['city_id'] = intval( $new_instance['city_id'] );
        return $instance;
    }
}

function register_city_temperature_widget() {
    register_widget( 'City_Temperature_Widget' );
}
add_action( 'widgets_init', 'register_city_temperature_widget' );

// Add AJAX Search for Cities
function ajax_search_cities() {
    global $wpdb;
    $search = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';

    $results = $wpdb->get_results( $wpdb->prepare( "
        SELECT post_title
        FROM $wpdb->posts
        WHERE post_type = 'cities' AND post_status = 'publish' AND post_title LIKE %s
    ", '%' . $wpdb->esc_like( $search ) . '%' ) );

    wp_send_json( $results );
}
add_action( 'wp_ajax_search_cities', 'ajax_search_cities' );
add_action( 'wp_ajax_nopriv_search_cities', 'ajax_search_cities' );

// End Custome Plugin