<?php

/**
    enqueue the parent and child theme stylesheets
**/
add_action( 'wp_enqueue_scripts', 'enqueue_child_theme_styles');
function enqueue_child_theme_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
    wp_enqueue_style( 'child-style', get_stylesheet_uri(), array('parent-style')  );
}
 
/*
    Control the visibility of the navigation links based on if a user is logged in
*/
add_action('wp_head','jg_user_nav_visibility');
function jg_user_nav_visibility() {

    if ( is_user_logged_in() ) {
        $output="<style> .nav-login { display: none !important; } </style>";
    } else {
        $output="<style> .nav-account { display: none !important; } </style>";
    }

    echo $output;
}


/**
    Allow customers to login with their email address or username
**/
add_filter('authenticate', 'internet_allow_email_login', 20, 3);
function internet_allow_email_login( $user, $username, $password ) {
    if ( is_email( $username ) ) {
        $user = get_user_by_email( $username );
        if ( $user ) $username = $user->user_login;
    }
    return wp_authenticate_username_password( null, $username, $password );
}


/**
    Change WordPress’ Default “Register For This Site” Message To Your Own Custom Text
**/
add_action('login_message', 'change_reg_message');
function change_reg_message($message)
{
    // change messages that contain 'Register'
    if (strpos($message, 'Register') !== FALSE) {
        $newMessage = "<p>Register For This Site<p>NOTE: We will never sell or rent your personal information to third parties for their use without your consent.</p>";
        return '<p class="message register">' . $newMessage . '<p>';
    }
    else {
            return $message;
    }
}


/**
    Remove featured image on single-product page
**/
add_filter('woocommerce_single_product_image_thumbnail_html', 'remove_featured_image', 10, 3);
function remove_featured_image($html, $attachment_id, $post_id) {
    $featured_image = get_post_thumbnail_id($post_id);
    if ($attachment_id != $featured_image) {
        return $html;
    }
    return '';
}


/**
    Remove Related Products Output on single-product page
**/
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );


/**
    Remove sidebar from all Store pages
**/
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar',10);

function disable_all_widgets( $sidebars_widgets ) {       
    if ( function_exists('is_bbpress') ) {
        if (is_bbpress()) {
            $sidebars_widgets = array(false);
            remove_all_actions('bp_register_widgets');
            unregister_sidebar( 'bp_core_widgets' );
        }
    }
    return $sidebars_widgets;
}

add_filter('sidebars_widgets', 'disable_all_widgets', 1, 1);

