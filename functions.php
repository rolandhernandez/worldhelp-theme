<?php
function my_theme_enqueue_styles() { 
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );

add_action( 'init', 'add_sponsorship_shortcode' );

function sponsorships_function($atts) { 
    $a = shortcode_atts( array(
        'country' => 'US',
        ), $atts );

    global $wpdb;
    $sql;
    if(array_key_exists("country", $a )) {
        $sql = $wpdb->prepare("SELECT * FROM sponsorship WHERE country = %s", $a['country']);
    } else {
        $sql = $wpdb->prepare("SELECT * FROM sponsorship");
    }
    $results = $wpdb->get_results( $sql , ARRAY_A );

    $cards = "";
    foreach ($results as $result) {
        $cards .= '<div class="sponsorship-card">';
        $cards .= '<h3>'.$result["first_name"]." ".$result["last_name"].'</h3>';
        $cards .= '<img width="300px" src="'.$result["picture"].'" />';

        $request = wp_remote_get( 'http://www.geognos.com/api/en/countries/info/'.$result["country"].'.json' );
        $body = wp_remote_retrieve_body( $request );
        $data = json_decode( $body );

        $cards .= '<p>Country: '.$data->Results->Name.'</p>';
        $cards .= '<p>Capital: '.$data->Results->Capital->Name.'</p>';
        $cards .= do_shortcode('[button]Sponsor Me[/button]');
        $cards .= '</div>';
        
    }
    return $cards;
}

function add_sponsorship_shortcode() {
    add_shortcode('sponsorships', 'sponsorships_function');
}