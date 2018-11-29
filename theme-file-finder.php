<?php
/**
 * @package Theme_File_Finder
 * @version 1.0
 */
/*
Plugin Name: Theme File Finder
Description: This is a simple plugin that displays the current page template being used.
Author: Zach Meyer
Version: 1.0
Author URI: https://zachmeyer.com
*/

// Display the current template name (slug) and where it resides.
function tff_show_current_template() {
  global $template;
  $results ='';

  if ( empty($template) ) :
    return false;
  elseif ( current_user_can( 'manage_options' ) ) :

    $results .= '<div class="tff-template-info">';
    $results .= '<div><strong>The Current Page Template is:</strong></div>';
    $results .= '<h2>' . basename($template) . '</h2>';
    $results .= '<p><strong>You can find the template here:</strong> ' . get_template_directory_uri() . '/' . basename($template) . '</p>';
    $results .= '</div>';
    echo $results;
  endif;
}

// wp_after_admin_bar_render
add_action('wp_footer', 'tff_show_current_template');

function ttf_styles_load() {
  wp_enqueue_style( 'Theme File Finder', plugin_dir_url( __FILE__ ) . 'css/ttf-public.css', array(), '1.0.0', 'all' );
}

add_action('wp_footer', 'ttf_styles_load');


// Get all the posts of the current template;
function tff_get_template_posts() {
  global $template;
  $output = '';
  $template_name = basename($template);


  if ( is_admin() ) {
    return false;
  } else {

    // Add arguments to find the pages that also use this template.
    $args = array(
      'post_type' => 'page',
      'posts_per_page' => -1,
      // Commenting this out because default returns nothing.
      // 'meta_query' => array(
      //   array(
      //     'key' => '_wp_page_template',
      //     'value' => $template_name,
      //   ),
      // ),
    );
    // Create a new query to render them.
    $all_page_query = new WP_Query( $args );

    if ( $all_page_query->have_posts() ) :
      echo '<div class="tff-page-list--item">
      <h2>This is all of the pages and their templates.</h2><hr>';
      while ( $all_page_query->have_posts() ) : $all_page_query->the_post();
        // Is the current template info stored in the meta?
        $current_template = get_post_meta( get_the_ID(), '_wp_page_template', true );
        echo the_title('<h4><a href="' . esc_url( get_permalink() ) . '">', '</a></h4>');
        if ( 'default' === $current_template ) {
          // We know this is a default template but we want the name.
          echo '<p>' . $template_name . '</p>';
        } else {
          echo '<p>' . $current_template . '</p>'; }
          echo '<hr>';
      endwhile;
      echo '</div>';
    endif;
  }
}

add_action( 'wp_footer', 'tff_get_template_posts');