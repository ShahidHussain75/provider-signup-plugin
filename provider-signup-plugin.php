<?php
/*
Plugin Name: Provider Signup Plugin
Version: 1.0.0
Description: Creates a signup form, user role "Provider," and Listing type "Listings". The [provider_signup_form] shortcode generates a signup form for providers. To display latitude and longitude field on front end [latitude] and [longitude] shortcodes will be used. Please note after regisration Provider users will be redirected to registration-success page.
Author: Shahid Hussain
Author URI: https://github.com/ShahidHussain75
*/

// Register Listing Type
function create_custom_post_type() {
    $labels = array(
        'name'                  => _x( 'Listings', 'Post Type General Name', 'text_domain' ),
        'singular_name'         => _x( 'Listing', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'             => __( 'Listings', 'text_domain' ),
        'name_admin_bar'        => __( 'Listing', 'text_domain' ),
        'archives'              => __( 'Item Archives', 'text_domain' ),
        'attributes'            => __( 'Item Attributes', 'text_domain' ),
        'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
        'all_items'             => __( 'All Items', 'text_domain' ),
        'add_new_item'          => __( 'Add New Item', 'text_domain' ),
        'add_new'               => __( 'Add New', 'text_domain' ),
        'new_item'              => __( 'New Item', 'text_domain' ),
        'edit_item'             => __( 'Edit Item', 'text_domain' ),
        'update_item'           => __( 'Update Item', 'text_domain' ),
        'view_item'             => __( 'View Item', 'text_domain' ),
        'view_items'            => __( 'View Items', 'text_domain' ),
        'search_items'          => __( 'Search Item', 'text_domain' ),
        'not_found'             => __( 'Not found', 'text_domain' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
        'featured_image'        => __( 'Featured Image', 'text_domain' ),
        'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
        'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
        'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
        'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
        'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
        'items_list'            => __( 'Items list', 'text_domain' ),
        'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
        'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
    );
   $args = array(
        'label'                 => __( 'Listing', 'text_domain' ),
        'description'           => __( 'Listing Description', 'text_domain' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
        'taxonomies'            => array( 'category', 'post_tag' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'rewrite'               => array( 'slug' => 'listings' ), // Update the slug
    );
        register_post_type( 'listings', $args ); // Update the post type slug
}
add_action( 'init', 'create_custom_post_type', 0 );

// Add Custom Meta Boxes
function add_custom_post_type_metaboxes() {
    add_meta_box(
        'custom_post_type_metabox',
        'Latitude and Longitude',
        'render_custom_post_type_metabox',
        'listings', // Update the post type slug here
        'normal',
        'high'
    );
}

// Render Custom Meta Box Fields
function render_custom_post_type_metabox( $post ) {
    // Retrieve the current values for latitude and longitude
    $latitude = get_post_meta( $post->ID, 'latitude', true );
    $longitude = get_post_meta( $post->ID, 'longitude', true );
    ?>
    <p>
        <label for="latitude">Latitude:</label>
        <input type="text" id="latitude" name="latitude" value="<?php echo esc_attr( $latitude ); ?>" />
    </p>
    <p>
        <label for="longitude">Longitude:</label>
        <input type="text" id="longitude" name="longitude" value="<?php echo esc_attr( $longitude ); ?>" />
    </p>
    <?php
}

// Save Custom Meta Box Fields
function save_custom_post_type_metabox( $post_id ) {
    if ( isset( $_POST['latitude'] ) ) {
        update_post_meta( $post_id, 'latitude', sanitize_text_field( $_POST['latitude'] ) );
    }
    if ( isset( $_POST['longitude'] ) ) {
        update_post_meta( $post_id, 'longitude', sanitize_text_field( $_POST['longitude'] ) );
    }
}
add_action( 'save_post', 'save_custom_post_type_metabox' );

// Shortcode to Display Latitude
function display_latitude_shortcode() {
    global $post;
    $latitude = get_post_meta( $post->ID, 'latitude', true );
    return $latitude;
}

add_shortcode( 'latitude', 'display_latitude_shortcode' );

// Shortcode to Display Longitude
function display_longitude_shortcode() {
    global $post;
    $longitude = get_post_meta( $post->ID, 'longitude', true );
    return $longitude;
}

add_shortcode( 'longitude', 'display_longitude_shortcode' );


// Register the "Provider" user role
function provider_signup_plugin_add_provider_role() {
    add_role(
        'provider',
        'Provider',
        array(
            // Add capabilities for the "Provider" role
            'read' => true,
            'edit_posts' => true,
            'delete_posts' => true,
            // Add more capabilities as needed
        )
    );
}
register_activation_hook( __FILE__, 'provider_signup_plugin_add_provider_role' );

// Create the signup form shortcode
function provider_signup_plugin_signup_form() {
    ob_start();
    ?>
    <!-- HTML markup for the signup form -->
    <form id="provider-signup-form" method="POST">
        <input type="hidden" name="action" value="provider_signup_submit">
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" required>
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>
        <?php if ( isset( $_GET['registration_success'] ) && $_GET['registration_success'] === 'true' ) : ?>
            <p class="registration-success-message">Registration successful! Please login to continue.</p>
        <?php endif; ?>
        <input type="submit" value="Signup">
        <?php wp_nonce_field( 'provider_signup_nonce', 'provider_signup_nonce_field' ); ?>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode( 'provider_signup_form', 'provider_signup_plugin_signup_form' );

// Handle form submission
function provider_signup_plugin_handle_form_submission() {
    if ( isset( $_POST['action'] ) && $_POST['action'] === 'provider_signup_submit' && isset( $_POST['name'] ) && isset( $_POST['email'] ) && isset( $_POST['password'] ) && isset( $_POST['provider_signup_nonce_field'] ) && wp_verify_nonce( $_POST['provider_signup_nonce_field'], 'provider_signup_nonce' ) ) {
        $name = sanitize_text_field( $_POST['name'] );
        $email = sanitize_email( $_POST['email'] );
        $password = sanitize_text_field( $_POST['password' ] );

        $userdata = array(
            'user_login'    => $email,
            'user_email'    => $email,
            'user_pass'     => $password,
            'display_name'  => $name,
            'role'          => 'provider',
        );

        $user_id = wp_insert_user( $userdata );
        if ( ! is_wp_error( $user_id ) ) {
            // User created successfully
            wp_redirect( add_query_arg( 'registration_success', 'true', home_url( '/registration-success' ) ) );
            exit;
        } else {
            // Handle any errors that occurred during user creation
        }
    }
}
add_action( 'template_redirect', 'provider_signup_plugin_handle_form_submission' );

// Register the custom endpoint for displaying the success message
function provider_signup_plugin_register_endpoint() {
    add_rewrite_endpoint( 'registration-success', EP_ROOT );
}
add_action( 'init', 'provider_signup_plugin_register_endpoint' );

// Flush rewrite rules on plugin activation
function provider_signup_plugin_flush_rewrite_rules() {
    provider_signup_plugin_register_endpoint();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'provider_signup_plugin_flush_rewrite_rules' );

// Display the success message on the custom endpoint
function provider_signup_plugin_display_success_message() {
    $registration_success = get_query_var( 'registration-success' );
if ( null !== $registration_success && $registration_success === 'true' ) {
echo '<p class="registration-success-message">Registration successful! Please login to continue.</p>';
    }
}
add_action( 'template_redirect', 'provider_signup_plugin_display_success_message' );
