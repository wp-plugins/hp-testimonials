<?php
/**
* Plugin Name: HP Testimonials
* Plugin URI: http://www.harshitpeer.com/hp_testimonial_wordpress_plugin
* Description: HP Testimonials is a simple Testimonial Plugin which shows your Testimonial in a awesome slider animation and more importantly in the order given by you.
* Version: 1.0.1
* Author: Harshit Peer
* Author URI: http://www.harshitpeer.com/
**/

function hp_testimonials_activate() { 
	require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
	global $wpdb;
	$db_table_name = $wpdb->prefix . 'hp_testimonials';
	if( $wpdb->get_var( "SHOW TABLES LIKE '$db_table_name'" ) != $db_table_name ) {
		if ( ! empty( $wpdb->charset ) )
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		if ( ! empty( $wpdb->collate ) )
			$charset_collate .= " COLLATE $wpdb->collate";
 
		$sql = "CREATE TABLE " . $db_table_name . " (
			`hp_testi_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			`hp_testi_name` varchar(250) NOT NULL DEFAULT '',
			`hp_testi_company` varchar(250),
			`hp_testi_designation` varchar(250),
			`hp_testi_date` int,
			`hp_testi_rating` int,
			`hp_testi_text` text NOT NULL,
			PRIMARY KEY (`hp_testi_id`)
		) $charset_collate;";
		dbDelta( $sql );
	}
        add_option( 'hp_testimonials_slider_effect', 'fade', '', 'yes' );
        add_option( 'hp_testimonials_single_text', 'Featured Testimonial', '', 'yes' );
        add_option( 'hp_testimonials_slider_text', 'Featured Testimonials', '', 'yes' );
}

//Action hook which fires the function above 
register_activation_hook(__FILE__, 'hp_testimonials_activate');

//Following code will create admin menu
function hp_testimonials_admin() {
    add_menu_page( 'HP Testimonials', 'HP Testimonials', 'manage_options', 'hp-testimonials/hp-testimonials-view.php', '', plugins_url( 'assets/images/icon.png', __FILE__ ), 7 );
    add_submenu_page('hp-testimonials/hp-testimonials-view.php','Add New','Add New','manage_options', __FILE__.'hp_testimonials_add_new','hp_testimonials_add_new');
    add_submenu_page('hp-testimonials/hp-testimonials-view.php','Settings','Settings','manage_options', __FILE__.'hp_testimonials_settings','hp_testimonials_settings');
}

add_action('admin_menu', 'hp_testimonials_admin');

if($_POST['action']=='add') {
    global $hp_testi_error,$hp_testi_success;
    $hp_testi_name = filter_input(INPUT_POST, 'hp_testi_name', FILTER_SANITIZE_STRING);
    $hp_testi_company = filter_input(INPUT_POST, 'hp_testi_company', FILTER_SANITIZE_STRING);
    $hp_testi_designation = filter_input(INPUT_POST, 'hp_testi_designation', FILTER_SANITIZE_STRING);
    $hp_testi_rating = filter_input(INPUT_POST, 'hp_testi_rating', FILTER_SANITIZE_NUMBER_INT);
    $hp_testi_text = filter_input(INPUT_POST, 'hp_testi_text', FILTER_SANITIZE_STRING);
    if($hp_testi_name=="") {
        $hp_testi_error = "Name field cannot be empty";
    }
    if($hp_testi_text=="") {
        $hp_testi_error = "Testimonial field cannot be empty";
    }
    if($hp_testi_rating=="") {
        $hp_testi_rating = 5;
    }
    if(!is_numeric($hp_testi_rating)) {
        $hp_testi_error = "Invalid Rating";
    }
    if($hp_testi_error=="") {
        require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
	global $wpdb;
        $db_table_name = $wpdb->prefix . 'hp_testimonials';
        $wpdb->query($wpdb->prepare("INSERT INTO $db_table_name (hp_testi_name, hp_testi_company, hp_testi_designation, hp_testi_date, hp_testi_rating, hp_testi_text) VALUES ( %s, %s, %s, %s, %d, %s )",$hp_testi_name,$hp_testi_company,$hp_testi_designation,time(),$hp_testi_rating,$hp_testi_text));
        $hp_testi_success = "<div class='success'>Successfully Added Testimonial. <a href='".admin_url()."admin.php?page=hp-testimonials/hp-testimonials-view.php'>Click here</a> to view it</div>";
        $_POST = array();
        return;
    } else {
        return;
    }
}

if($_POST['action']=='settings') {
    $settings_slider_effects = filter_input(INPUT_POST, 'settings_slider_effects', FILTER_SANITIZE_STRING);
    $settings_single_text = filter_input(INPUT_POST, 'settings_single_text', FILTER_SANITIZE_STRING);
    $settings_slider_text = filter_input(INPUT_POST, 'settings_slider_text', FILTER_SANITIZE_STRING);
    update_option( 'hp_testimonials_slider_effect', $settings_slider_effects );
    update_option( 'hp_testimonials_single_text', $settings_single_text );
    update_option( 'hp_testimonials_slider_text', $settings_slider_text );
}

function hp_testimonials_settings() {
    wp_enqueue_style( 'hp_testimonials_add_new_css', plugins_url( 'assets/css/style.css', __FILE__ ), false, '1.0', 'all' );
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-effects-fade');
    ?>
<script>
jQuery(document).ready(function($) {
    $("#hp_testi_loading").fadeOut(500);
    $("#hp_testimonials_settings_section").delay(500).fadeIn(1000);
});
</script>
<div style="margin: 30px;" id="hp_testi_loading">Loading... Please Wait</div>
<div style="display: none;" id="hp_testimonials_settings_section">
    <div class="settings_main_logo"></div>
    <div class="testo_settings">
        <form method="post">
            <input type="hidden" name="action" value="settings">
            <table class="settings_table">
                <tr>
                    <td style="width: 30%">Slider Effect</td>
                    <td>
                        <select style="width: 100%" name="settings_slider_effects">
                            <option value="fade" <?php if(get_option( 'hp_testimonials_slider_effect' )=='fade') { echo "selected"; }  ?>>Fade</option>
                            <option value="slide" <?php if(get_option( 'hp_testimonials_slider_effect' )=='slide') { echo "selected"; }  ?>>Slide</option>
                        </select> 
                    </td>
                </tr>
                <tr>
                    <td style="width: 30%">Text above Single Testimonial</td>
                    <td><input style="width: 100%" type="text" name="settings_single_text" value="<?php echo get_option( 'hp_testimonials_single_text' ) ?>"></td>
                </tr>
                <tr>
                    <td style="width: 30%">Text above Slider Testimonial</td>
                    <td><input style="width: 100%" type="text" name="settings_slider_text" value="<?php echo get_option( 'hp_testimonials_slider_text' ) ?>"></td>
                </tr>
                <tr>
                    <td colspan="2"><input style="margin-top: 20px; width: 100%;" type="submit" class="hp_testimonials_button green" value="Save Changes"></td>
                </tr>
            </table>
        </form>
    </div>
</div>
    <?php
}


function hp_testimonials_add_new() {
    global $hp_testi_error,$hp_testi_success;
    wp_enqueue_style( 'hp_testimonials_add_new_css', plugins_url( 'assets/css/style.css', __FILE__ ), false, '1.0', 'all' ); 
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-effects-fade');	
    ?>
<script>
jQuery(document).ready(function($) {
    $("#hp_testi_loading").fadeOut(500);
    $(".hp_testimonials_new_css").delay(500).fadeIn(1000);
});
</script>
<div style="margin: 30px;" id="hp_testi_loading">Loading... Please Wait</div>
<div style="display: none;" class="hp_testimonials_new_css">
    <div class="main_logo"></div>
    <?php if($hp_testi_error!="") { ?> <div class="error"><?php echo $hp_testi_error; ?></div> <?php } ?>
    <?php if($hp_testi_success!="") { ?> <div class="success"><?php echo $hp_testi_success; ?></div> <?php } ?>
    <form id="add_new_form" method="post">
        <input type="hidden" name="action" value="add">
        <input class="styledinput" type="text" placeholder="Name" value="<?php echo $_POST['hp_testi_name']; ?>" name="hp_testi_name"/><br>
        <input class="styledinput" type="text" placeholder="Company (Optional)" value="<?php echo $_POST['hp_testi_company']; ?>" name="hp_testi_company"/><br>
        <input class="styledinput" type="text" placeholder="Designation (Optional)" value="<?php echo $_POST['hp_testi_designation']; ?>" name="hp_testi_designation"/><br>
        <select class="styledselect" name="hp_testi_rating">
            <option value="">Choose Rating on Scale of 0 to 10 (Default is 5)</option>
            <option value="0">0</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            <option value="6">6</option>
            <option value="7">7</option>
            <option value="8">8</option>
            <option value="9">9</option>
            <option value="10">10</option>
        </select> 
        <div style="margin-top: 10px; width: 98%;">
        <?php wp_editor( "Enter Testimonial here", "hp_testi_text" ); ?>
        </div>
        <input style="height: 50px;" type="submit" class="hp_testimonials_button green" value="Add New Testimonial">
    </form>
</div>
    <?php
}

function hp_testimonials_single_function($atts){
    wp_enqueue_style( 'hp_testimonials_global_css', plugins_url( 'assets/css/style.css', __FILE__ ), false, '1.0', 'all' ); 
    extract(shortcode_atts(array(
        'id' => 1,
    ), $atts));
    require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
    global $wpdb;
    $db_table_name = $wpdb->prefix . 'hp_testimonials';
    $sql = "SELECT * FROM ".$db_table_name. " WHERE hp_testi_id=".$id;
    $testiz = $wpdb->get_results($sql);
    if($testiz==null) {
        echo "HP Testimonials ERROR - Invalid ID";
    } else {
        foreach( $testiz as $testi ) {
            $hp_testi_name = $testi->hp_testi_name;
            $hp_testi_company = $testi->hp_testi_company;
            $hp_testi_designation = $testi->hp_testi_designation;
            $hp_testi_date = $testi->hp_testi_date;
            $hp_testi_text = $testi->hp_testi_text;
            ?>
<div class="hp_testimonials">
    <div class="testo_details">
        <div class="featuredtext"><?php echo get_option( 'hp_testimonials_single_text' ) ?></div>
        <p class="text"><img class="startquote" src="<?php echo plugins_url( 'assets/images/startquote.png', __FILE__ ) ?>"><?php echo $hp_testi_text; ?></p>
        <div class="rightdiv">
            <div class="testiodetails"><strong><?php echo $hp_testi_name; ?></strong> - <?php echo $hp_testi_designation; ?>, <?php echo $hp_testi_company; ?></div>
        </div>
        <?php /* <div class="date"><?php echo(date("d M Y",$hp_testi_date)); ?></div> */ ?>
    </div>
</div>
            <?php
        }
    }
}

add_shortcode( 'hp_testimonials_single', 'hp_testimonials_single_function' );

function hp_testimonials_function($atts){
    wp_enqueue_style( 'hp_testimonials_global_css', plugins_url( 'assets/css/style.css', __FILE__ ), false, '1.0', 'all' );
    wp_enqueue_script('jquery');
    wp_enqueue_script( 'jquery_easing', plugins_url( 'assets/js/jquery.easing.1.3.js', __FILE__ ) , false, '1.0', false); 
    wp_enqueue_script( 'anyslider', plugins_url( 'assets/js/jquery.anyslider.js', __FILE__ ) , false, '1.0', false); 
    extract(shortcode_atts(array(
        'n' => 1,
    ), $atts));
    require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
    global $wpdb;
    $db_table_name = $wpdb->prefix . 'hp_testimonials';
    $sql = "SELECT * FROM ".$db_table_name. " ORDER BY hp_testi_rating DESC LIMIT ".$n;
    $testiz = $wpdb->get_results($sql);
    echo "<div class='featuredtext'>",get_option( 'hp_testimonials_slider_text' ),"</div>";
    if($testiz==null) {
        echo "HP Testimonials ERROR - Invalid ID";
    } else {
        echo "<div class='slider slider1'>";
        foreach( $testiz as $testi ) {
            $hp_testi_name = $testi->hp_testi_name;
            $hp_testi_company = $testi->hp_testi_company;
            $hp_testi_designation = $testi->hp_testi_designation;
            $hp_testi_date = $testi->hp_testi_date;
            $hp_testi_text = $testi->hp_testi_text;
            ?>
<div class="hp_testimonials" style="padding: 0px !important; ">
    <div class="testo_details">
        <p class="text"><img class="startquote" src="<?php echo plugins_url( 'assets/images/startquote.png', __FILE__ ) ?>"><?php echo $hp_testi_text; ?></p>
        <div class="rightdiv">
            <div class="testiodetails"><strong><?php echo $hp_testi_name; ?></strong> - <?php echo $hp_testi_designation; ?>, <?php echo $hp_testi_company; ?></div>
        </div>
        <?php /* <div class="date"><?php echo(date("d M Y",$hp_testi_date)); ?></div> */ ?>
    </div>
</div>
            <?php
        }
        
        ?> 
        </div>
        <script>
        jQuery(document).ready(function($) {
            $('.slider1').anyslider({
                animation: '<?php echo get_option( 'hp_testimonials_slider_effect' ) ?>',
                showBullets: false,
                showControls: false
            });
        });
        </script>
        <?php
    }
}

add_shortcode( 'hp_testimonials', 'hp_testimonials_function' );

?>