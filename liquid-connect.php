<?php
/*
Plugin Name: LIQUID CONNECT
Plugin URI: https://lqd.jp/wp/plugin/connect.html
Description: LIQUID CONNECT is an engagement tool for web sites.
Author: LIQUID DESIGN Ltd.
Author URI: https://lqd.jp/wp/
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: liquid-connect
Version: 1.1.6
*/
/*  Copyright 2018 LIQUID DESIGN Ltd. (email : info@lqd.jp)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
*/

// ------------------------------------
// Plugin
// ------------------------------------

// cookie
function liquid_connect_cookie() {
    if( empty( $_COOKIE["liquid_connect_target_visitor"] ) ){
        setcookie("liquid_connect_target_visitor", 1, time() + 60, "/");
    }else{
        setcookie("liquid_connect_target_visitor", $_COOKIE["liquid_connect_target_visitor"] + 1, time() + 60, "/");
    }
}
add_action( 'get_header', 'liquid_connect_cookie');

// get_option
$liquid_connect_toggle = get_option( 'liquid_connect_toggle' );

// plugin_action_links
function liquid_connect_plugin_action_links( $links ) {
	$mylinks = '<a href="'.admin_url( 'options-general.php?page=liquid-connect' ).'">'.__( 'Settings', 'liquid-connect' ).'</a>';
    array_unshift( $links, $mylinks);
    return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'liquid_connect_plugin_action_links' );

// ------------------------------------
// Admin
// ------------------------------------
function liquid_connect_init() {
	load_plugin_textdomain( 'liquid-connect', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'admin_init', 'liquid_connect_init' );

function liquid_connect_admin() {
    add_options_page(
      'LIQUID CONNECT',
      'LIQUID CONNECT',
      'administrator',
      'liquid-connect',
      'liquid_connect_admin_page'
    );
    register_setting(
      'liquid_connect_group',
      'liquid_connect_toggle',
      'liquid_connect_toggle_validation'
    );
}
add_action( 'admin_menu', 'liquid_connect_admin' );

function liquid_connect_toggle_validation( $input ) {
     $input = (int) $input;
     if ( $input === 0 || $input === 1 ) {
          return $input;
     } else {
          add_settings_error(
               'liquid_connect_toggle',
               'liquid_connect_toggle_validation_error',
               __( 'illegal data', 'error' ),
               'error'
          );
     }
}

function liquid_connect_admin_page() {
     global $json_liquid_connect;
     $liquid_connect_toggle = get_option( 'liquid_connect_toggle' );
     if( empty( $liquid_connect_toggle ) ){
          $checked_on = 'checked="checked"';
          $checked_off = '';
     } else {
          $checked_on = '';
          $checked_off = 'checked="checked"';
     }
?>
<div class="wrap">
<h1>LIQUID CONNECT</h1>

<div id="poststuff">

<!-- Recommend -->
<?php if( !empty($json_liquid_connect->recommend) ){ ?>
<div class="postbox">
<h2 style="border-bottom: 1px solid #eee;"><?php _e( 'Recommend', 'liquid-connect' ); ?></h2>
<div class="inside"><?php echo $json_liquid_connect->recommend; ?></div>
</div>
<?php } ?>

<!-- Settings -->
<div class="postbox">
<h2 style="border-bottom: 1px solid #eee;"><?php _e( 'Settings', 'liquid-connect' ); ?></h2>
<div class="inside">
<form method="post" action="options.php">
<?php
     settings_fields( 'liquid_connect_group' );
     do_settings_sections( 'default' );
?>
<table class="form-table">
     <tbody>
     <tr>
          <th scope="row"><?php _e( 'Enable', 'liquid-connect' ); ?> LIQUID CONNECT</th>
          <td>
               <label for="liquid_connect_toggle_on"><input type="radio" id="liquid_connect_toggle_on" name="liquid_connect_toggle" value="0" <?php echo $checked_on; ?>>On</label>
               <label for="liquid_connect_toggle_off"><input type="radio" id="liquid_connect_toggle_off" name="liquid_connect_toggle" value="1" <?php echo $checked_off; ?>>Off</label>
          </td>
     </tr>
     </tbody>
</table>
<?php submit_button(); ?>
</form>
</div>
</div>

</div><!-- /poststuff -->
<hr><a href="https://lqd.jp/wp/" target="_blank">LIQUID PRESS</a>
</div><!-- /wrap -->
<?php }

// ------------------------------------
// Widget
// ------------------------------------
class liquid_connect extends WP_Widget {
	function __construct() {
    	parent::__construct(
			'liquid_connect',
			'LIQUID CONNECT'
        );
    }
    function widget($args, $instance) {
        global $liquid_connect_toggle;
        extract( $args );
        $liquid_connect_id = apply_filters( 'liquid_connect_id', empty( $instance['liquid_connect_id'] ) ? '' : $instance['liquid_connect_id'] );
        $liquid_connect_tracking = apply_filters( 'liquid_connect_tracking', empty( $instance['liquid_connect_tracking'] ) ? '' : $instance['liquid_connect_tracking'] );
        $liquid_connect_target_visitor = apply_filters( 'liquid_connect_target_visitor', empty( $instance['liquid_connect_target_visitor'] ) ? '' : $instance['liquid_connect_target_visitor'] );
        $liquid_connect_target_devices = apply_filters( 'liquid_connect_target_devices', empty( $instance['liquid_connect_target_devices'] ) ? '' : $instance['liquid_connect_target_devices'] );
        $liquid_connect_target_campaign = apply_filters( 'liquid_connect_target_campaign', empty( $instance['liquid_connect_target_campaign'] ) ? '' : $instance['liquid_connect_target_campaign'] );
        $liquid_connect_trigger_page = apply_filters( 'liquid_connect_trigger_page', empty( $instance['liquid_connect_trigger_page'] ) ? '' : $instance['liquid_connect_trigger_page'] );
        $liquid_connect_trigger_position = apply_filters( 'liquid_connect_trigger_position', empty( $instance['liquid_connect_trigger_position'] ) ? '' : $instance['liquid_connect_trigger_position'] );
        $liquid_connect_trigger_display = apply_filters( 'liquid_connect_trigger_display', empty( $instance['liquid_connect_trigger_display'] ) ? '' : $instance['liquid_connect_trigger_display'] );
        $liquid_connect_trigger_date = apply_filters( 'liquid_connect_trigger_date', empty( $instance['liquid_connect_trigger_date'] ) ? '' : $instance['liquid_connect_trigger_date'] );
        $liquid_connect_trigger_date2 = apply_filters( 'liquid_connect_trigger_date2', empty( $instance['liquid_connect_trigger_date2'] ) ? '' : $instance['liquid_connect_trigger_date2'] );
        $liquid_connect_content_ttl = array();
        $liquid_connect_content_ttl[] = apply_filters( 'liquid_connect_content_ttl_01', empty( $instance['liquid_connect_content_ttl_01'] ) ? '' : $instance['liquid_connect_content_ttl_01'] );
        $liquid_connect_content_ttl[] = apply_filters( 'liquid_connect_content_ttl_02', empty( $instance['liquid_connect_content_ttl_02'] ) ? '' : $instance['liquid_connect_content_ttl_02'] );
        $liquid_connect_content_link = array();
        $liquid_connect_content_link[] = apply_filters( 'liquid_connect_content_link_01', empty( $instance['liquid_connect_content_link_01'] ) ? '' : $instance['liquid_connect_content_link_01'] );
        $liquid_connect_content_link[] = apply_filters( 'liquid_connect_content_link_02', empty( $instance['liquid_connect_content_link_02'] ) ? '' : $instance['liquid_connect_content_link_02'] );
        $liquid_connect_content_attr = array();
        $liquid_connect_content_attr[] = apply_filters( 'liquid_connect_content_attr_01', empty( $instance['liquid_connect_content_attr_01'] ) ? '' : $instance['liquid_connect_content_attr_01'] );
        $liquid_connect_content_attr[] = apply_filters( 'liquid_connect_content_attr_02', empty( $instance['liquid_connect_content_attr_02'] ) ? '' : $instance['liquid_connect_content_attr_02'] );
        $liquid_connect_content_txt = array();
        $liquid_connect_content_txt[] = apply_filters( 'liquid_connect_content_txt_01', empty( $instance['liquid_connect_content_txt_01'] ) ? '' : $instance['liquid_connect_content_txt_01'] );
        $liquid_connect_content_txt[] = apply_filters( 'liquid_connect_content_txt_02', empty( $instance['liquid_connect_content_txt_02'] ) ? '' : $instance['liquid_connect_content_txt_02'] );
        $img = array();
        $img[] = apply_filters( 'liquid_connect_content_img_01', empty( $instance['liquid_connect_content_img_01'] ) ? '' : $instance['liquid_connect_content_img_01'] );
        $img[] = apply_filters( 'liquid_connect_content_img_02', empty( $instance['liquid_connect_content_img_02'] ) ? '' : $instance['liquid_connect_content_img_02'] );
        $liquid_connect_content_color_txt = array();
        $liquid_connect_content_color_txt[] = apply_filters( 'liquid_connect_content_color_txt_01', empty( $instance['liquid_connect_content_color_txt_01'] ) ? '' : $instance['liquid_connect_content_color_txt_01'] );
        $liquid_connect_content_color_txt[] = apply_filters( 'liquid_connect_content_color_txt_02', empty( $instance['liquid_connect_content_color_txt_02'] ) ? '' : $instance['liquid_connect_content_color_txt_02'] );
        $liquid_connect_content_color_bg = array();
        $liquid_connect_content_color_bg[] = apply_filters( 'liquid_connect_content_color_bg_01', empty( $instance['liquid_connect_content_color_bg_01'] ) ? '' : $instance['liquid_connect_content_color_bg_01'] );
        $liquid_connect_content_color_bg[] = apply_filters( 'liquid_connect_content_color_bg_02', empty( $instance['liquid_connect_content_color_bg_02'] ) ? '' : $instance['liquid_connect_content_color_bg_02'] );
        $liquid_connect_content_custom = array();
        $liquid_connect_content_custom[] = apply_filters( 'liquid_connect_content_custom_01', empty( $instance['liquid_connect_content_custom_01'] ) ? '' : $instance['liquid_connect_content_custom_01'] );
        $liquid_connect_content_custom[] = apply_filters( 'liquid_connect_content_custom_02', empty( $instance['liquid_connect_content_custom_02'] ) ? '' : $instance['liquid_connect_content_custom_02'] );
    	?>
        <?php echo $before_widget; ?>
<?php if( empty( $_COOKIE['liquid_connect_close'] ) && empty( $liquid_connect_toggle ) ){ ?>
<?php if( empty( $liquid_connect_target_devices ) || $liquid_connect_target_devices == "pc" && !wp_is_mobile() || $liquid_connect_target_devices == "sp" && wp_is_mobile() ){ ?>
<?php if( empty( $liquid_connect_target_campaign ) || preg_match('{'.$liquid_connect_target_campaign.'}', $_SERVER['QUERY_STRING']) ){ ?>
<?php if( empty( $liquid_connect_trigger_page ) || preg_match('{'.$liquid_connect_trigger_page.'}', $_SERVER['REQUEST_URI']) ){ ?>
<?php if( empty( $liquid_connect_trigger_date ) || strtotime(date("Y/m/d")) >= strtotime($liquid_connect_trigger_date) ){ ?>
<?php if( empty( $liquid_connect_trigger_date2 ) || strtotime(date("Y/m/d")) <= strtotime($liquid_connect_trigger_date2) ){ ?>
<?php if( empty( $liquid_connect_target_visitor ) ||
    $liquid_connect_target_visitor == "new" && !empty( $_COOKIE['liquid_connect_target_visitor'] ) && $_COOKIE['liquid_connect_target_visitor'] < 2 ||
    $liquid_connect_target_visitor == "return" && !empty( $_COOKIE['liquid_connect_target_visitor'] ) && $_COOKIE['liquid_connect_target_visitor'] > 1 ){ ?>
<?php if( !empty( $liquid_connect_content_link[1] ) || !empty( $img[1] ) || !empty( $liquid_connect_content_txt[1] ) || !empty( $liquid_connect_content_custom[1] ) ) {
    $i = rand(0, 1);
} else {
    $i = 0;
} ?>
<?php if( empty( $i ) ) {
    $j = "A";
} else {
    $j = "B";
} ?>
<!-- liquid_connect -->
<div class="liquid_connect <?php if( !empty( $liquid_connect_trigger_display ) ) { ?><?php echo $liquid_connect_trigger_display; ?><?php } ?>">
<div class="liquid_connect_inner">
<?php if( !empty( $liquid_connect_content_link[$i] ) ) { ?>
<a class="liquid_connect_content_link" href="<?php echo esc_html($liquid_connect_content_link[$i]); ?>" <?php if( !empty( $liquid_connect_content_attr[$i] ) ) { ?><?php echo 'target="'.$liquid_connect_content_attr[$i].'"'; ?><?php } ?>>
<?php } ?>
<?php if( !empty( $liquid_connect_content_ttl[$i] ) ) { ?>
<div class="liquid_connect_content_ttl"><?php echo $liquid_connect_content_ttl[$i]; ?></div>
<?php } ?>
<?php if( !empty( $img[$i] ) ) { ?>
<img class="liquid_connect_content_img" src="<?php echo esc_html($img[$i]); ?>">
<?php } ?>
<?php if( !empty( $liquid_connect_content_txt[$i] ) ) { ?>
<div class="liquid_connect_content_txt" style="<?php if( !empty( $liquid_connect_content_color_txt[$i] ) ) { ?>color:<?php echo $liquid_connect_content_color_txt[$i]; ?>;<?php } ?> <?php if( !empty( $liquid_connect_content_color_bg[$i] ) ) { ?>background-color:<?php echo $liquid_connect_content_color_bg[$i]; ?>;<?php } ?>">
<?php echo $liquid_connect_content_txt[$i]; ?>
</div>
<?php } ?>
<?php if( !empty( $liquid_connect_content_link[$i] ) ) { ?>
</a>
<?php } ?>
<?php if( !empty( $liquid_connect_content_custom[$i] ) ) { ?>
<div class="liquid_connect_content_custom"><?php echo do_shortcode($liquid_connect_content_custom[$i]); ?></div>
<?php } ?>
<a href="https://lqd.jp/wp/plugin/connect.html?utm_source=referrer&utm_medium=footer&utm_campaign=connect" target="_blank" class="liquid_connect_content_copy"><?php _e( 'LIQUID CONNECT', 'liquid-connect' ); ?></a>
</div>
<!-- /liquid_connect_inner -->
<?php if( !empty( $liquid_connect_trigger_display ) ) { ?>
<div class="liquid_connect_close">&times;</div>
<script>
jQuery(function ($) {
    //close
    $('.liquid_connect_close').on('click', function() {
        document.cookie = "liquid_connect_close=1;path='/';max-age=2592000";
        $('.liquid_connect').remove();
    });
    <?php if( !empty( $liquid_connect_trigger_position ) ) { ?>
    <?php if( $liquid_connect_trigger_position == "middle" ){
        $liquid_connect_trigger_position_value = 0.5;
    } else {
        $liquid_connect_trigger_position_value = 0.05;
    } ?>
    //scroll
    $(window).on('scroll', function() {
        scrollHeight = $(document).height();
        scrollPosition = $(window).height() + $(window).scrollTop();
        if ( (scrollHeight - scrollPosition) / scrollHeight <= <?php echo $liquid_connect_trigger_position_value; ?>) {
            $('.liquid_connect').fadeIn();
            <?php if( !empty( $liquid_connect_tracking ) ) { ?>
            //ga
            gtag('event', 'view_item', {
                'event_label': <?php echo "'".$j." (".$liquid_connect_id.")'"; ?>
            });
            <?php } ?>
        }
    });
    <?php } else { ?>
        $('.liquid_connect').show();
        <?php if( !empty( $liquid_connect_tracking ) ) { ?>
        //ga
        gtag('event', 'view_item', {
            'event_label': <?php echo "'".$j." (".$liquid_connect_id.")'"; ?>
        });
        <?php } ?>
    <?php } ?>
    <?php if( !empty( $liquid_connect_tracking ) ) { ?>
    //ga
    $('.liquid_connect_content_link').on('click', function() {
        gtag('event', 'click', {
            'event_category': 'engagement',
            'event_label': <?php echo "'".$j." (".$liquid_connect_id.")'"; ?>
        });
        return true;
    });
    <?php } ?>
});
</script>
<?php } ?>
</div>
<!-- /liquid_connect -->
<?php } //liquid_connect_target_visitor ?>
<?php } //liquid_connect_trigger_date2 ?>
<?php } //liquid_connect_trigger_date ?>
<?php } //liquid_connect_trigger_page ?>
<?php } //liquid_connect_target_campaign ?>
<?php } //liquid_connect_target_devices ?>
<?php } //liquid_connect_close ?>
        <?php echo $after_widget; ?>
        <?php
    }
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['liquid_connect_id'] = trim($new_instance['liquid_connect_id']);
        $instance['liquid_connect_tracking'] = trim($new_instance['liquid_connect_tracking']);
        $instance['liquid_connect_target_visitor'] = trim($new_instance['liquid_connect_target_visitor']);
        $instance['liquid_connect_target_devices'] = trim($new_instance['liquid_connect_target_devices']);
        $instance['liquid_connect_target_campaign'] = trim($new_instance['liquid_connect_target_campaign']);
        $instance['liquid_connect_trigger_page'] = trim($new_instance['liquid_connect_trigger_page']);
        $instance['liquid_connect_trigger_position'] = trim($new_instance['liquid_connect_trigger_position']);
        $instance['liquid_connect_trigger_display'] = trim($new_instance['liquid_connect_trigger_display']);
        $instance['liquid_connect_trigger_date'] = trim($new_instance['liquid_connect_trigger_date']);
        $instance['liquid_connect_trigger_date2'] = trim($new_instance['liquid_connect_trigger_date2']);
        $instance['liquid_connect_content_ttl_01'] = trim($new_instance['liquid_connect_content_ttl_01']);
        $instance['liquid_connect_content_link_01'] = trim($new_instance['liquid_connect_content_link_01']);
        $instance['liquid_connect_content_attr_01'] = trim($new_instance['liquid_connect_content_attr_01']);
        $instance['liquid_connect_content_txt_01'] = trim($new_instance['liquid_connect_content_txt_01']);
        $instance['liquid_connect_content_img_01'] = trim($new_instance['liquid_connect_content_img_01']);
        $instance['liquid_connect_content_color_txt_01'] = trim($new_instance['liquid_connect_content_color_txt_01']);
        $instance['liquid_connect_content_color_bg_01'] = trim($new_instance['liquid_connect_content_color_bg_01']);
        $instance['liquid_connect_content_custom_01'] = trim($new_instance['liquid_connect_content_custom_01']);
        $instance['liquid_connect_content_ttl_02'] = trim($new_instance['liquid_connect_content_ttl_02']);
        $instance['liquid_connect_content_link_02'] = trim($new_instance['liquid_connect_content_link_02']);
        $instance['liquid_connect_content_attr_02'] = trim($new_instance['liquid_connect_content_attr_02']);
        $instance['liquid_connect_content_txt_02'] = trim($new_instance['liquid_connect_content_txt_02']);
        $instance['liquid_connect_content_img_02'] = trim($new_instance['liquid_connect_content_img_02']);
        $instance['liquid_connect_content_color_txt_02'] = trim($new_instance['liquid_connect_content_color_txt_02']);
        $instance['liquid_connect_content_color_bg_02'] = trim($new_instance['liquid_connect_content_color_bg_02']);
        $instance['liquid_connect_content_custom_02'] = trim($new_instance['liquid_connect_content_custom_02']);
        $instance['liquid_connect_content_custom_02'] = trim($new_instance['liquid_connect_content_custom_02']);
        return $instance;
    }
    function form($instance) {
        $liquid_connect_id = isset( $instance['liquid_connect_id'] ) ? esc_attr( $instance['liquid_connect_id'] ) : uniqid();
        $liquid_connect_tracking = isset( $instance['liquid_connect_tracking'] ) ? esc_attr( $instance['liquid_connect_tracking'] ) : '';
        $liquid_connect_target_visitor = isset( $instance['liquid_connect_target_visitor'] ) ? esc_attr( $instance['liquid_connect_target_visitor'] ) : '';
        $liquid_connect_target_devices = isset( $instance['liquid_connect_target_devices'] ) ? esc_attr( $instance['liquid_connect_target_devices'] ) : '';
        $liquid_connect_target_campaign = isset( $instance['liquid_connect_target_campaign'] ) ? esc_attr( $instance['liquid_connect_target_campaign'] ) : '';
        $liquid_connect_trigger_page = isset( $instance['liquid_connect_trigger_page'] ) ? esc_attr( $instance['liquid_connect_trigger_page'] ) : '';
        $liquid_connect_trigger_position = isset( $instance['liquid_connect_trigger_position'] ) ? esc_attr( $instance['liquid_connect_trigger_position'] ) : '';
        $liquid_connect_trigger_display = isset( $instance['liquid_connect_trigger_display'] ) ? esc_attr( $instance['liquid_connect_trigger_display'] ) : '';
        $liquid_connect_trigger_date = isset( $instance['liquid_connect_trigger_date'] ) ? esc_attr( $instance['liquid_connect_trigger_date'] ) : '';
        $liquid_connect_trigger_date2 = isset( $instance['liquid_connect_trigger_date2'] ) ? esc_attr( $instance['liquid_connect_trigger_date2'] ) : '';
        $liquid_connect_content_ttl_01 = isset( $instance['liquid_connect_content_ttl_01'] ) ? esc_attr( $instance['liquid_connect_content_ttl_01'] ) : '';
        $liquid_connect_content_link_01 = isset( $instance['liquid_connect_content_link_01'] ) ? esc_attr( $instance['liquid_connect_content_link_01'] ) : '';
        $liquid_connect_content_attr_01 = isset( $instance['liquid_connect_content_attr_01'] ) ? esc_attr( $instance['liquid_connect_content_attr_01'] ) : '';
        $liquid_connect_content_txt_01 = isset( $instance['liquid_connect_content_txt_01'] ) ? esc_attr( $instance['liquid_connect_content_txt_01'] ) : '';
        $img_01 = isset( $instance['liquid_connect_content_img_01'] ) ? esc_attr( $instance['liquid_connect_content_img_01'] ) : '';
        $liquid_connect_content_color_txt_01 = isset( $instance['liquid_connect_content_color_txt_01'] ) ? esc_attr( $instance['liquid_connect_content_color_txt_01'] ) : '#333333';
        $liquid_connect_content_color_bg_01 = isset( $instance['liquid_connect_content_color_bg_01'] ) ? esc_attr( $instance['liquid_connect_content_color_bg_01'] ) : '#ffffff';
        $liquid_connect_content_custom_01 = isset( $instance['liquid_connect_content_custom_01'] ) ? esc_attr( $instance['liquid_connect_content_custom_01'] ) : '';
        $liquid_connect_content_ttl_02 = isset( $instance['liquid_connect_content_ttl_02'] ) ? esc_attr( $instance['liquid_connect_content_ttl_02'] ) : '';
        $liquid_connect_content_link_02 = isset( $instance['liquid_connect_content_link_02'] ) ? esc_attr( $instance['liquid_connect_content_link_02'] ) : '';
        $liquid_connect_content_attr_02 = isset( $instance['liquid_connect_content_attr_02'] ) ? esc_attr( $instance['liquid_connect_content_attr_02'] ) : '';
        $liquid_connect_content_txt_02 = isset( $instance['liquid_connect_content_txt_02'] ) ? esc_attr( $instance['liquid_connect_content_txt_02'] ) : '';
        $img_02 = isset( $instance['liquid_connect_content_img_02'] ) ? esc_attr( $instance['liquid_connect_content_img_02'] ) : '';
        $liquid_connect_content_color_txt_02 = isset( $instance['liquid_connect_content_color_txt_02'] ) ? esc_attr( $instance['liquid_connect_content_color_txt_02'] ) : '#333333';
        $liquid_connect_content_color_bg_02 = isset( $instance['liquid_connect_content_color_bg_02'] ) ? esc_attr( $instance['liquid_connect_content_color_bg_02'] ) : '#ffffff';
        $liquid_connect_content_custom_02 = isset( $instance['liquid_connect_content_custom_02'] ) ? esc_attr( $instance['liquid_connect_content_custom_02'] ) : '';
        ?>
        <!-- Contents -->
        <style>
            .liquid_connect_tab { border: 2px solid #000; margin-bottom: .5rem; border-radius: 5px; }
            .liquid_connect_tab[open] .liquid_connect_nav { font-weight: bold; }
            .liquid_connect_tab > p { padding: 0 .5rem; }
            .liquid_connect_nav { padding: .5rem; cursor: pointer; color: #444; display: block; }
        </style>
        <div id="liquid_connect_<?php echo $liquid_connect_id; ?>">
        <p><legend><b><?php _e( 'Contents', 'liquid-connect' ); ?></b></legend></p>
        <!-- Contents 01 -->
        <details class="liquid_connect_tab" open>
        <summary class="liquid_connect_nav">A</summary>
        <p>
           <label for="<?php echo $this->get_field_id( 'liquid_connect_content_ttl_01' ); ?>"><?php _e( 'Title:', 'liquid-connect' ); ?></label>
           <input type="text" class="widefat" colls="20" id="<?php echo $this->get_field_id('liquid_connect_content_ttl_01'); ?>" name="<?php echo $this->get_field_name('liquid_connect_content_ttl_01'); ?>" value="<?php echo $liquid_connect_content_ttl_01; ?>">
        </p>
        <p>
           <label for="<?php echo $this->get_field_id( 'liquid_connect_content_img_01' ); ?>"><a href="<?php echo get_admin_url(); ?>upload.php" target="_blank"><?php _e( 'Image URL:', 'liquid-connect' ); ?></a></label>
           <input type="url" class="widefat" colls="20" id="<?php echo $this->get_field_id('liquid_connect_content_img_01'); ?>" name="<?php echo $this->get_field_name('liquid_connect_content_img_01'); ?>" value="<?php echo $img_01; ?>">
           <?php if($img_01) echo '<img src="'.esc_html($img_01).'" alt="" style="width:100%; display:block;">'; ?>
        </p>
        <p>
           <label for="<?php echo $this->get_field_id( 'liquid_connect_content_link_01' ); ?>"><?php _e( 'Link URL:', 'liquid-connect' ); ?></label>
           <input type="url" class="widefat" colls="20" id="<?php echo $this->get_field_id('liquid_connect_content_link_01'); ?>" name="<?php echo $this->get_field_name('liquid_connect_content_link_01'); ?>" value="<?php echo $liquid_connect_content_link_01; ?>">
        </p>
        <p>
           <input type="checkbox" id="<?php echo $this->get_field_id('liquid_connect_content_attr_01'); ?>" name="<?php echo $this->get_field_name('liquid_connect_content_attr_01'); ?>" value="_blank" <?php checked( '_blank', $liquid_connect_content_attr_01, true ); ?>>
           <label for="<?php echo $this->get_field_id( 'liquid_connect_content_attr_01' ); ?>"><?php _e( 'Open link with new tab', 'liquid-connect' ); ?></label>
        </p>
        <p>
           <label for="<?php echo $this->get_field_id( 'liquid_connect_content_txt_01' ); ?>"><?php _e( 'Button:', 'liquid-connect' ); ?></label>
           <input type="text" class="widefat" colls="20" id="<?php echo $this->get_field_id('liquid_connect_content_txt_01'); ?>" name="<?php echo $this->get_field_name('liquid_connect_content_txt_01'); ?>" value="<?php echo $liquid_connect_content_txt_01; ?>">
        </p>
        <p>
           <label for="<?php echo $this->get_field_id( 'liquid_connect_content_color_txt_01' ); ?>"><?php _e( 'Color Text:', 'liquid-connect' ); ?></label>
           <input type="color" id="<?php echo $this->get_field_id('liquid_connect_content_color_txt_01'); ?>" name="<?php echo $this->get_field_name('liquid_connect_content_color_txt_01'); ?>" value="<?php echo $liquid_connect_content_color_txt_01; ?>">
        </p>
        <p>
           <label for="<?php echo $this->get_field_id( 'liquid_connect_content_color_bg_01' ); ?>"><?php _e( 'Color Background:', 'liquid-connect' ); ?></label>
           <input type="color" id="<?php echo $this->get_field_id('liquid_connect_content_color_bg_01'); ?>" name="<?php echo $this->get_field_name('liquid_connect_content_color_bg_01'); ?>" value="<?php echo $liquid_connect_content_color_bg_01; ?>">
        </p>
        <p>
           <label for="<?php echo $this->get_field_id( 'liquid_connect_content_custom_01' ); ?>"><?php _e( 'Custom HTML:', 'liquid-connect' ); ?></label>
           <textarea class="widefat" id="<?php echo $this->get_field_id('liquid_connect_content_custom_01'); ?>" colls="20" rows="2" name="<?php echo $this->get_field_name('liquid_connect_content_custom_01'); ?>"><?php echo $liquid_connect_content_custom_01; ?></textarea>
        </p>
        </details>
        <!-- Contents 02 -->
        <details class="liquid_connect_tab">
        <summary class="liquid_connect_nav">B</summary>
        <p>
           <label for="<?php echo $this->get_field_id( 'liquid_connect_content_ttl_02' ); ?>"><?php _e( 'Title:', 'liquid-connect' ); ?></label>
           <input type="text" class="widefat" colls="20" id="<?php echo $this->get_field_id('liquid_connect_content_ttl_02'); ?>" name="<?php echo $this->get_field_name('liquid_connect_content_ttl_02'); ?>" value="<?php echo $liquid_connect_content_ttl_02; ?>">
        </p>
        <p>
           <label for="<?php echo $this->get_field_id( 'liquid_connect_content_img_02' ); ?>"><a href="<?php echo get_admin_url(); ?>upload.php" target="_blank"><?php _e( 'Image URL:', 'liquid-connect' ); ?></a></label>
           <input type="url" class="widefat" colls="20" id="<?php echo $this->get_field_id('liquid_connect_content_img_02'); ?>" name="<?php echo $this->get_field_name('liquid_connect_content_img_02'); ?>" value="<?php echo $img_02; ?>">
           <?php if($img_02) echo '<img src="'.esc_html($img_02).'" alt="" style="width:100%; display:block;">'; ?>
        </p>
        <p>
           <label for="<?php echo $this->get_field_id( 'liquid_connect_content_link_02' ); ?>"><?php _e( 'Link URL:', 'liquid-connect' ); ?></label>
           <input type="url" class="widefat" colls="20" id="<?php echo $this->get_field_id('liquid_connect_content_link_02'); ?>" name="<?php echo $this->get_field_name('liquid_connect_content_link_02'); ?>" value="<?php echo $liquid_connect_content_link_02; ?>">
        </p>
        <p>
           <input type="checkbox" id="<?php echo $this->get_field_id('liquid_connect_content_attr_02'); ?>" name="<?php echo $this->get_field_name('liquid_connect_content_attr_02'); ?>" value="_blank" <?php checked( '_blank', $liquid_connect_content_attr_02, true ); ?>>
           <label for="<?php echo $this->get_field_id( 'liquid_connect_content_attr_02' ); ?>"><?php _e( 'Open link with new tab', 'liquid-connect' ); ?></label>
        </p>
        <p>
           <label for="<?php echo $this->get_field_id( 'liquid_connect_content_txt_02' ); ?>"><?php _e( 'Button:', 'liquid-connect' ); ?></label>
           <input type="text" class="widefat" colls="20" id="<?php echo $this->get_field_id('liquid_connect_content_txt_02'); ?>" name="<?php echo $this->get_field_name('liquid_connect_content_txt_02'); ?>" value="<?php echo $liquid_connect_content_txt_02; ?>">
        </p>
        <p>
           <label for="<?php echo $this->get_field_id( 'liquid_connect_content_color_txt_02' ); ?>"><?php _e( 'Color Text:', 'liquid-connect' ); ?></label>
           <input type="color" id="<?php echo $this->get_field_id('liquid_connect_content_color_txt_02'); ?>" name="<?php echo $this->get_field_name('liquid_connect_content_color_txt_02'); ?>" value="<?php echo $liquid_connect_content_color_txt_02; ?>">
        </p>
        <p>
           <label for="<?php echo $this->get_field_id( 'liquid_connect_content_color_bg_02' ); ?>"><?php _e( 'Color Background:', 'liquid-connect' ); ?></label>
           <input type="color" id="<?php echo $this->get_field_id('liquid_connect_content_color_bg_02'); ?>" name="<?php echo $this->get_field_name('liquid_connect_content_color_bg_02'); ?>" value="<?php echo $liquid_connect_content_color_bg_02; ?>">
        </p>
        <p>
           <label for="<?php echo $this->get_field_id( 'liquid_connect_content_custom_02' ); ?>"><?php _e( 'Custom HTML:', 'liquid-connect' ); ?></label>
           <textarea class="widefat" id="<?php echo $this->get_field_id('liquid_connect_content_custom_02'); ?>" colls="20" rows="2" name="<?php echo $this->get_field_name('liquid_connect_content_custom_02'); ?>"><?php echo $liquid_connect_content_custom_02; ?></textarea>
        </p>
        </details>
        </div><!-- /id -->
        <!-- Target -->
        <p><legend><b><?php _e( 'Target', 'liquid-connect' ); ?></b></legend></p>
        <p>
           <label for="<?php echo $this->get_field_id('liquid_connect_target_visitor'); ?>"><?php _e( 'Visitor:', 'liquid-connect' ); ?></label>
           <select class="widefat" id="<?php echo $this->get_field_id('liquid_connect_target_visitor'); ?>" name="<?php echo $this->get_field_name('liquid_connect_target_visitor'); ?>">
               <option value=""></option>
               <option value="new" <?php selected( 'new', $liquid_connect_target_visitor, true ); ?>><?php _e( 'New Visitor', 'liquid-connect' ); ?></option>
               <option value="return" <?php selected( 'return', $liquid_connect_target_visitor, true ); ?>><?php _e( 'Returning Visitor', 'liquid-connect' ); ?></option>
           </select>
        </p>
        <p>
           <label for="<?php echo $this->get_field_id('liquid_connect_target_devices'); ?>"><?php _e( 'Devices:', 'liquid-connect' ); ?></label>
           <select class="widefat" id="<?php echo $this->get_field_id('liquid_connect_target_devices'); ?>" name="<?php echo $this->get_field_name('liquid_connect_target_devices'); ?>">
               <option value=""></option>
               <option value="sp" <?php selected( 'sp', $liquid_connect_target_devices, true ); ?>><?php _e( 'Mobile', 'liquid-connect' ); ?></option>
               <option value="pc" <?php selected( 'pc', $liquid_connect_target_devices, true ); ?>><?php _e( 'Desktop', 'liquid-connect' ); ?></option>
           </select>
        </p>
        <p>
           <label for="<?php echo $this->get_field_id('liquid_connect_target_campaign'); ?>"><?php _e( 'Campaign Source:', 'liquid-connect' ); ?></label>
           <input type="text" class="widefat" colls="20" id="<?php echo $this->get_field_id('liquid_connect_target_campaign'); ?>" name="<?php echo $this->get_field_name('liquid_connect_target_campaign'); ?>" placeholder="utm_source=target" value="<?php echo $liquid_connect_target_campaign; ?>">
        </p>
        <hr>
        <p><legend><b><?php _e( 'Trigger', 'liquid-connect' ); ?></b></legend></p>
        <p>
           <label for="<?php echo $this->get_field_id('liquid_connect_trigger_page'); ?>"><?php _e( 'Page:', 'liquid-connect' ); ?></label>
           <input type="text" class="widefat" colls="20" id="<?php echo $this->get_field_id('liquid_connect_trigger_page'); ?>" name="<?php echo $this->get_field_name('liquid_connect_trigger_page'); ?>" placeholder="slug" value="<?php echo $liquid_connect_trigger_page; ?>">
        </p>
        <p>
           <label for="<?php echo $this->get_field_id('liquid_connect_trigger_display'); ?>"><?php _e( 'Display:', 'liquid-connect' ); ?></label>
           <select class="widefat" id="<?php echo $this->get_field_id('liquid_connect_trigger_display'); ?>" name="<?php echo $this->get_field_name('liquid_connect_trigger_display'); ?>">
               <option value=""><?php _e( 'Banner', 'liquid-connect' ); ?></option>
               <option value="liquid_connect_modal_left" <?php selected( 'liquid_connect_modal_left', $liquid_connect_trigger_display, true ); ?>><?php _e( 'Modal(left)', 'liquid-connect' ); ?></option>
               <option value="liquid_connect_modal_right" <?php selected( 'liquid_connect_modal_right', $liquid_connect_trigger_display, true ); ?>><?php _e( 'Modal(right)', 'liquid-connect' ); ?></option>
               <option value="liquid_connect_modal_center" <?php selected( 'liquid_connect_modal_center', $liquid_connect_trigger_display, true ); ?>><?php _e( 'Modal(center)', 'liquid-connect' ); ?></option>
           </select>
        </p>
        <p>
           <label for="<?php echo $this->get_field_id('liquid_connect_trigger_position'); ?>"><?php _e( 'Scroll Position:', 'liquid-connect' ); ?></label>
           <select class="widefat" id="<?php echo $this->get_field_id('liquid_connect_trigger_position'); ?>" name="<?php echo $this->get_field_name('liquid_connect_trigger_position'); ?>">
               <option value=""></option>
               <option value="middle" <?php selected( 'middle', $liquid_connect_trigger_position, true ); ?>><?php _e( 'Middle', 'liquid-connect' ); ?></option>
               <option value="bottom" <?php selected( 'bottom', $liquid_connect_trigger_position, true ); ?>><?php _e( 'Bottom', 'liquid-connect' ); ?></option>
           </select>
        </p>
        <p>
           <label for="<?php echo $this->get_field_id('liquid_connect_trigger_date'); ?>"><?php _e( 'Date:', 'liquid-connect' ); ?></label>
           <input type="date" id="<?php echo $this->get_field_id('liquid_connect_trigger_date'); ?>" name="<?php echo $this->get_field_name('liquid_connect_trigger_date'); ?>" value="<?php echo $liquid_connect_trigger_date; ?>">
           <?php _e( 'To', 'liquid-connect' ); ?>
           <input type="date" id="<?php echo $this->get_field_id('liquid_connect_trigger_date2'); ?>" name="<?php echo $this->get_field_name('liquid_connect_trigger_date2'); ?>" value="<?php echo $liquid_connect_trigger_date2; ?>">
        </p>
        <hr>
        <p><legend><b><?php _e( 'Tracking', 'liquid-connect' ); ?></b></legend></p>
        <p>
           <select class="widefat" id="<?php echo $this->get_field_id('liquid_connect_tracking'); ?>" name="<?php echo $this->get_field_name('liquid_connect_tracking'); ?>">
               <option value=""></option>
               <option value="1" <?php selected( '1', $liquid_connect_tracking, true ); ?>><?php _e( 'Google Analytics (gtag.js)', 'liquid-connect' ); ?></option>
           </select>
        </p>
        <p>
            <input type="hidden" id="<?php echo $this->get_field_id('liquid_connect_id'); ?>" name="<?php echo $this->get_field_name('liquid_connect_id'); ?>" value="<?php echo $liquid_connect_id; ?>">
        </p>
        <!-- Report -->
        <p><legend><b><?php _e( 'Report', 'liquid-connect' ); ?></b></legend></p>
        <p><?php _e( 'By adding add-ons, you can use advanced features such as content addition and CTR reporting.', 'liquid-connect' ); ?>
        <br><?php _e( 'Get', 'liquid-connect' ); ?>:
        <a href="https://lqd.jp/wp/plugin/connect.html?utm_source=admin&utm_medium=info&utm_campaign=connect" target="_blank"><?php _e( 'Addon', 'liquid-connect' ); ?></a>
        </p>
        <hr>
        <!-- ID -->
        <p><?php _e( 'CONNECT ID:', 'liquid-connect' ); ?> <?php echo $liquid_connect_id; ?></p>
        <?php
    }
}

function liquid_connect_register() {
    if ( function_exists( 'liquid_connect_report' ) ) {
        $liquid_connect_addon = WP_PLUGIN_DIR . '/liquid-connect-addon/liquid-connect-addon.php';
        require_once( $liquid_connect_addon );
        register_widget( 'liquid_connect_addon' );
    } else {
        register_widget( 'liquid_connect' );
    }
}
add_action( 'widgets_init', 'liquid_connect_register' );

// style
function liquid_connect_scripts() {
    wp_enqueue_script( 'jquery' );
    wp_enqueue_style( 'liquid-connect', plugins_url() . '/liquid-connect/css/style.css', array() );
}
if( empty( $liquid_connect_toggle ) ){
    add_action( 'wp_enqueue_scripts', 'liquid_connect_scripts' );
}

// json
if ( is_admin() ) {
    $json_liquid_connect_error = "";
    $json_liquid_connect_url = "https://lqd.jp/wp/data/p/liquid-connect.json";
    $json_liquid_connect = wp_remote_get($json_liquid_connect_url);
    if ( is_wp_error( $json_liquid_connect ) ) {
        $json_liquid_connect_error = $json_liquid_connect->get_error_message().$json_liquid_connect_url;
    }else{
        $json_liquid_connect = json_decode($json_liquid_connect['body']);
    }
}

// notices
function liquid_connect_admin_notices() {
    global $json_liquid_connect, $json_liquid_connect_error;
    if ( isset( $_GET['liquid_admin_notices_dismissed'] ) ) {
        set_transient( 'liquid_admin_notices', 'dismissed', 60*60*24*30 );
    }
    if( !empty($json_liquid_connect->news) && get_transient( 'liquid_admin_notices' ) != 'dismissed' ){
        echo '<div class="notice notice-info" style="position: relative;"><p>'.$json_liquid_connect->news.'</p><a href="?liquid_admin_notices_dismissed" style="position: absolute; right: 10px; top: 10px;">&times;</a></div>';
    }
    if(!empty($json_liquid_connect_error)) {
        echo '<script>console.log("'.$json_liquid_connect_error.'");</script>';
    }
}
add_action( 'admin_notices', 'liquid_connect_admin_notices' );

?>