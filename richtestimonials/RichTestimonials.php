<?php

/**
 * Plugin Name: Rich Testimonial
 * Plugin URI: http://sushanttayade.in
 * Description: This plugin provides features like adding testimonials and sorting them.
 * Version: 1.0.0
 * Author: Sushant Tayade
 * Author URI: http://sushanttayade.in
 * License: GPL2
 */


function richtestimonial_setup_post_type()
{

    // register the "richtestimonial" custom post type
    register_post_type('rich_testimonial',
                       [
                           'labels'      => [
                               'name'          => __('Rich Testimonials'),
                               'singular_name' => __('Rich Testimonial'),
                               'add_new' => 'Add New Testimonial',
    						   'add_new_item' => 'Add New Testimonial',
    						   'edit_item' => 'Edit Testimonial',
						       'new_item' => 'New Testimonial',
						       'all_items' => 'All Testimonials',
						       'view_item' => 'View Testimonial',
                           ],
                           'public'      => true,
                           'supports' => array( 'title', 'thumbnail' ),
                           'has_archive' => true,
                           'rewrite'     => ['slug' => 'rich_testimonial'], // my custom slug
                       ]
    );

}
add_action( 'init', 'richtestimonial_setup_post_type' );



add_action( 'admin_init', 'my_admin' );

function my_admin(){
add_meta_box( 'rt_given_by_meta_box',
        'Testimonial',
        'display_rt_given_by_meta_box',
        'rich_testimonial', 'normal', 'high'
    );	
}




function display_rt_given_by_meta_box($testimonial){
	// Retrieve current name of the Director and Movie Rating based on review ID
    $testimonial_giver = esc_html( get_post_meta( $testimonial->ID, 'testimonial_giver', true ) );
    $designation = esc_html( get_post_meta( $testimonial->ID, 'designation', true ) );
    $course = esc_html( get_post_meta( $testimonial->ID, 'course', true ) );
    $city = esc_html( get_post_meta( $testimonial->ID, 'city', true ) );
    $testimonial_desc = esc_html( get_post_meta( $testimonial->ID, 'testimonial_desc', true ) );
    ?>
    <table>
        <tr>
            <td style="width: 100%">Name</td>
            <td><input type="text" size="80" name="testimonial_giver" value="<?php echo $testimonial_giver; ?>" /></td>
        </tr>
        <tr>
            <td style="width: 100%">Designation</td>
            <td><input type="text" size="80" name="designation" value="<?php echo $designation; ?>" /></td>
        </tr>
        <tr>
            <td>Course</td>
            <td>
                <select style="width: 100px" name="course">
                
                    <option value="KMP - I"> KPM I</option>
                    <option value="KMP - II"> KPM II</option>
                
                </select>
            </td>
        </tr>
        <tr>
            <td style="width: 100%">City</td>
            <td><input type="text" size="80" name="city" value="<?php echo $city; ?>" /></td>
        </tr>
        <tr>
            <td style="width: 100%">Description</td>
            <td><textarea name="testimonial_desc" value="<?php echo $testimonial_desc; ?>"></textarea></td>
        </tr>
        
    </table>
    <?php
}


add_action( 'save_post', 'add_new_testimonial_fields', 10, 2 );

function add_new_testimonial_fields($testimonial_id, $testimonial){
	// Check post type for movie reviews
    if ( $testimonial->post_type == 'rich_testimonial' ) {
        // Store data in post meta table if present in post data
        if ( isset( $_POST['testimonial_giver'] ) && $_POST['testimonial_giver'] != '' ) {
            update_post_meta( $testimonial_id, 'testimonial_giver', $_POST['testimonial_giver'] );
        }
        if ( isset( $_POST['designation'] ) && $_POST['designation'] != '' ) {
            update_post_meta( $testimonial_id, 'designation', $_POST['designation'] );
        }
        if ( isset( $_POST['course'] ) && $_POST['course'] != '' ) {
            update_post_meta( $testimonial_id, 'course', $_POST['course'] );
        }
        if ( isset( $_POST['city'] ) && $_POST['city'] != '' ) {
            update_post_meta( $testimonial_id, 'city', $_POST['city'] );
        }
        if ( isset( $_POST['testimonial_desc'] ) && $_POST['testimonial_desc'] != '' ) {
            update_post_meta( $testimonial_id, 'testimonial_desc', $_POST['testimonial_desc'] );
        }
    }
}


 
function richtestimonial_install()
{
    // trigger our function that registers the custom post type
    richtestimonial_setup_post_type();
 
    // clear the permalinks after the post type has been registered
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'richtestimonial_install' );



function section_feed_shortcode( $atts ) {
extract( shortcode_atts( array( 'limit' => -1, 'type' => 'post'), $atts ) );

$paged = get_query_var('paged') ? get_query_var('paged') : 1;  

query_posts(  array ( 
    'posts_per_page' => $limit, 
    'post_type' => 'rich_testimonial', 
    'order' => 'ASC', 
    'orderby' =>'menu_order', 
    'paged' => $paged ) );  

while ( have_posts() ) { the_post();?>

    <article>
            <div class="rt-item">
 
                <!-- Display featured image in right-aligned floating div -->
                <div style="float: left; margin: 10px">
                    <?php
                        
                        if(has_post_thumbnail()){
                            the_post_thumbnail( array( 100, 100 ) );
                        }
                        else{
                            echo '<img src="' . plugins_url( 'richtestimonials/user.png' ) . '" width="100" height="100"/>';
                        }

                    ?>
                </div>
                <div style="float: left;">
 
                    <!-- Display Testimonial -->
                    <?php 
                    $testimonial_giver = esc_html( get_post_meta( get_the_ID(), 'testimonial_giver', true ) );
                    $designation = esc_html( get_post_meta( get_the_ID(), 'designation', true ) );
                    $course = esc_html( get_post_meta( get_the_ID(), 'course', true ) );
                    $city = esc_html( get_post_meta( get_the_ID(), 'city', true ) );
                    ?>
                    <span class="rt_giver"><?php echo $testimonial_giver; ?></span>
                    <br />

                    <span class="rt_designation"><?php echo $designation; ?></span>
                    <br />

                    <span class="rt_course"><?php echo $course; ?></span>
                    <br />

                    <span class="rt_city"><?php echo $city; ?></span>
                    
                </div>
 
                
            </div>
            <div style="clear:both;">
                <span class="rt_desc"><?php echo esc_html( get_post_meta( get_the_ID(), 'testimonial_desc', true ) ); ?></span>
            
            </div>
 
        </article>
<?php
}

wp_reset_query();

}
add_shortcode( 'feed', 'section_feed_shortcode' );


function wpse_load_plugin_css() {
    $plugin_url = plugin_dir_url( __FILE__ );

    wp_enqueue_style( 'rt_style', $plugin_url . 'css/rt_style.css' );
}
add_action( 'wp_enqueue_scripts', 'wpse_load_plugin_css' );


?>


<script>
    $(".rt-item img").click(function(){
        alert("Hello");
    });

    alert("Hello");
</script>