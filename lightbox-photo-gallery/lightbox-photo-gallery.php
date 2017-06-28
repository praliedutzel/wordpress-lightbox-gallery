<?php
/**
 * Plugin Name: Lightbox Photo Gallery
 * Plugin URI: https://github.com/praliedutzel/wordpress-lightbox-gallery
 * Description: Replaces the default behavior of WordPress' galleries so photos are opened in a lightbox.
 * Version: 1.0
 * Author: Pralie Dutzel
 * Author URI: http://praliedutzel.com
 */

/**
 * This plugin also uses the following jQuery plugins:
 *
 * Featherlight v1.7.6
 * https://noelboss.github.io/featherlight/
 * Copyright (c) Noël Bossart
 *
 * Featherlight Gallery Extension v1.7.6
 * https://noelboss.github.io/featherlight/gallery.html
 * Copyright (c) Noël Bossart
 */

if( !defined('ABSPATH') ) {
    die;
}


/**
 * Remove the default gallery shortcode and replace it with our own
 */

remove_shortcode( 'gallery', 'gallery_shortcode' );
add_shortcode( 'gallery', 'lbpg_gallery_shortcode' );


/**
 * Load in the assets for the lightbox plugin and the photo gallery
 */

function lbpg_assets() {
    // CSS assets, including theming for the gallery and lightbox
    wp_enqueue_style( 'featherlight-styles', '//cdn.rawgit.com/noelboss/featherlight/1.7.6/release/featherlight.min.css' );
    wp_enqueue_style( 'featherlight-gallery-styles', '//cdn.rawgit.com/noelboss/featherlight/1.7.6/release/featherlight.gallery.min.css' );
    wp_enqueue_style( 'lbpg-theme-styles', plugins_url( 'css/theme.min.css', __FILE__ ) );

    // JavaScript assets
    wp_enqueue_script( 'featherlight-script', '//cdn.rawgit.com/noelboss/featherlight/1.7.6/release/featherlight.min.js', array('jquery'), '', true );
    wp_enqueue_script( 'featherlight-gallery-script', '//cdn.rawgit.com/noelboss/featherlight/1.7.6/release/featherlight.gallery.min.js', array('jquery'), '', true );
}

add_action( 'wp_enqueue_scripts', 'lbpg_assets' );


/**
 * Add lightbox functionality to WordPress' gallery shortcode
 */

function lbpg_gallery_shortcode( $attr ) {
    $post = get_post();

    static $instance = 0;
    $instance++;

    if ( !empty( $attr['ids'] ) ) {
        // 'ids' is explicitly ordered, unless you specify otherwise.
        if ( empty( $attr['orderby'] ) ) {
            $attr['orderby'] = 'post__in';
        }

        $attr['include'] = $attr['ids'];
    }

    /**
     * Filter the default gallery shortcode output.
     *
     * If the filtered output isn't empty, it will be used instead of generating
     * the default gallery template.
     *
     * @see gallery_shortcode()
     *
     * @param string $output   The gallery output. Default empty.
     * @param array  $attr     Attributes of the gallery shortcode.
     * @param int    $instance Unique numeric ID of this gallery shortcode instance.
     */
    $output = apply_filters( 'post_gallery', '', $attr, $instance );

    if ( $output != '' ) {
        return $output;
    }

    $html5 = current_theme_supports( 'html5', 'gallery' );

    $atts = shortcode_atts( array(
        'order'      => 'ASC',
        'orderby'    => 'menu_order ID',
        'id'         => $post ? $post->ID : 0,
        'itemtag'    => $html5 ? 'figure'     : 'dl',
        'icontag'    => $html5 ? 'div'        : 'dt',
        'captiontag' => $html5 ? 'figcaption' : 'dd',
        'columns'    => 3,
        'size'       => 'thumbnail',
        'include'    => '',
        'exclude'    => '',
        'link'       => 'file'
    ), $attr, 'gallery' );

    $id = intval( $atts['id'] );

    if ( !empty( $atts['include'] ) ) {
        $_attachments = get_posts( array(
            'include'        => $atts['include'],
            'post_status'    => 'inherit',
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'order'          => $atts['order'],
            'orderby'        => $atts['orderby']
        ) );

        $attachments = array();
        foreach ( $_attachments as $key => $val ) {
            $attachments[$val->ID] = $_attachments[$key];
        }
    } elseif ( !empty( $atts['exclude'] ) ) {
        $attachments = get_children( array(
            'post_parent'    => $id,
            'exclude'        => $atts['exclude'],
            'post_status'    => 'inherit',
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'order'          => $atts['order'],
            'orderby'        => $atts['orderby']
        ) );
    } else {
        $attachments = get_children( array(
            'post_parent'    => $id,
            'post_status'    => 'inherit',
            'post_type'      => 'attachment',
            'post_mime_type' => 'image',
            'order'          => $atts['order'],
            'orderby'        => $atts['orderby']
        ) );
    }

    if ( empty( $attachments ) ) {
        return '';
    }

    if ( is_feed() ) {
        $output = '\n';
        foreach ( $attachments as $att_id => $attachment ) {
            $output .= wp_get_attachment_link( $att_id, $atts['size'], true ) . '\n';
        }
        return $output;
    }

    $columns = intval( $atts['columns'] );
    $size_class = sanitize_html_class( $atts['size'] );
    $gallery_div = '<ul class="lbpg-gallery" data-featherlight-gallery data-featherlight-filter="a.lbpg-gallery__link" data-columns="'.$columns.'">';

    /**
     * Filter the default gallery shortcode CSS styles.
     *
     * @param string $gallery_style Default CSS styles and opening HTML div container
     *                              for the gallery shortcode output.
     */
    $output = apply_filters( 'gallery_style', $gallery_div );

    $i = 0;
    foreach ( $attachments as $id => $attachment ) {
        $caption = $attachment->post_excerpt;

        if ( $caption != '' ) {
            $image_output = '<li class="lbpg-gallery__item">
                                <a href="'.wp_get_attachment_image_src( $id, 'full' )[0].'" class="lbpg-gallery__link">
                                    <img src="'.wp_get_attachment_image_src( $id, $atts['size'] )[0].'" alt="'.get_post_meta( $id, '_wp_attachment_image_alt', true ).'" class="lbpg-gallery__thumbnail" />
                                    <p class="lbpg-gallery__caption">'.$caption.'</p>
                                </a>
                            </li>';
        } else {
            $image_output = '<li class="lbpg-gallery__item">
                                <a href="'.wp_get_attachment_image_src( $id, 'full' )[0].'" class="lbpg-gallery__link">
                                    <img src="'.wp_get_attachment_image_src( $id, $atts['size'] )[0].'" alt="'.get_post_meta( $id, '_wp_attachment_image_alt', true ).'" class="lbpg-gallery__thumbnail" />
                                </a>
                            </li>';
        }

        $output .= $image_output;
    }

    $output .= '</ul>';

    return $output;
}


/**
 * Uninstall options
 *
 * Removes the custom shortcode when the plugin is uninstalled
 * and adds the original WP gallery shortcode back in
 */

function lbpg_uninstall() {
    remove_shortcode( 'gallery', 'lbpg_gallery_shortcode' );
    add_shortcode( 'gallery', 'gallery_shortcode' );
}

register_uninstall_hook( __FILE__, 'lbpg_uninstall' );