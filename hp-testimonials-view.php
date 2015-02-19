<?php
require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
global $wpdb;
wp_enqueue_style( 'hp_testimonials_view', plugins_url( 'assets/css/style.css', __FILE__ ), false, '1.0', 'all' ); 
wp_enqueue_style( 'jquery_ui', plugins_url( 'assets/css/jquery-ui.css', __FILE__ ), false, '1.0', 'all' ); 
wp_enqueue_script('jquery');
wp_enqueue_script('jquery-ui-dialog'); 
wp_enqueue_script('jquery-effects-fade');	
if(isset($_POST['action'])) {
    if($_POST['action']=='delete') {
        $testo_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
	$table_prefix = $wpdb->prefix;
	$main_table = $table_prefix."hp_testimonials";
	$deletemain = "DELETE FROM ".$main_table." WHERE hp_testi_id = ".$testo_id;
        $wpdb->query($deletemain);
    }
    if($_POST['action']=="editview") {
        $testo_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
	$table_prefix = $wpdb->prefix;
	$main_table = $table_prefix."hp_testimonials";
        $edit_testo_data = $wpdb->get_row("SELECT * FROM $main_table WHERE hp_testi_id = $testo_id");
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
        <form style="margin: 20px;" id="edit_form" method="post">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="hp_testi_id" value="<?php echo $edit_testo_data->hp_testi_id; ?>">
            <input class="styledinput" type="text" placeholder="Name" value="<?php echo $edit_testo_data->hp_testi_name; ?>" name="hp_testi_name"/><br>
            <input class="styledinput" type="text" placeholder="Company (Optional)" value="<?php echo $edit_testo_data->hp_testi_company; ?>" name="hp_testi_company"/><br>
            <input class="styledinput" type="text" placeholder="Designation (Optional)" value="<?php echo $edit_testo_data->hp_testi_designation; ?>" name="hp_testi_designation"/><br>
            <select class="styledselect" name="hp_testi_rating">
                <option value="">Choose Rating on Scale of 0 to 10 (Default is 5)</option>
                <option value="0" <?php if($edit_testo_data->hp_testi_rating==0) { echo "selected"; }  ?>>0</option>
                <option value="1" <?php if($edit_testo_data->hp_testi_rating==1) { echo "selected"; }  ?>>1</option>
                <option value="2" <?php if($edit_testo_data->hp_testi_rating==2) { echo "selected"; }  ?>>2</option>
                <option value="3" <?php if($edit_testo_data->hp_testi_rating==3) { echo "selected"; }  ?>>3</option>
                <option value="4" <?php if($edit_testo_data->hp_testi_rating==4) { echo "selected"; }  ?>>4</option>
                <option value="5" <?php if($edit_testo_data->hp_testi_rating==5) { echo "selected"; }  ?>>5</option>
                <option value="6" <?php if($edit_testo_data->hp_testi_rating==6) { echo "selected"; }  ?>>6</option>
                <option value="7" <?php if($edit_testo_data->hp_testi_rating==7) { echo "selected"; }  ?>>7</option>
                <option value="8" <?php if($edit_testo_data->hp_testi_rating==8) { echo "selected"; }  ?>>8</option>
                <option value="9" <?php if($edit_testo_data->hp_testi_rating==9) { echo "selected"; }  ?>>9</option>
                <option value="10" <?php if($edit_testo_data->hp_testi_rating==10) { echo "selected"; }  ?>>10</option>
            </select> 
            <div style="margin-top: 10px; width: 98%;">
            <?php wp_editor($edit_testo_data->hp_testi_text, "hp_testi_text".$edit_testo_data->hp_testi_id ); ?>
            </div>
            <input style="height: 50px;" type="submit" class="hp_testimonials_button green" value="Edit Testimonial">
        </form>
</div>
        <?php
        return;
    }
    if($_POST['action']=='edit') {
	global $hp_testi_error,$hp_testi_success;
        $hp_testi_id = filter_input(INPUT_POST, 'hp_testi_id', FILTER_SANITIZE_NUMBER_INT);
        $temp_var_hp_testi = "hp_testi_text".$hp_testi_id;
        $hp_testi_name = filter_input(INPUT_POST, 'hp_testi_name', FILTER_SANITIZE_STRING);
        $hp_testi_company = filter_input(INPUT_POST, 'hp_testi_company', FILTER_SANITIZE_STRING);
        $hp_testi_designation = filter_input(INPUT_POST, 'hp_testi_designation', FILTER_SANITIZE_STRING);
        $hp_testi_rating = filter_input(INPUT_POST, 'hp_testi_rating', FILTER_SANITIZE_NUMBER_INT);
        $hp_testi_text = filter_input(INPUT_POST, $temp_var_hp_testi, FILTER_SANITIZE_STRING);
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
            $db_table_name = $wpdb->prefix . 'hp_testimonials';
            $wpdb->query($wpdb->prepare("UPDATE {$db_table_name} SET hp_testi_name = %s, hp_testi_company = %s, hp_testi_designation = %s, hp_testi_rating = %d, hp_testi_text = %s WHERE hp_testi_id = %d",$hp_testi_name,$hp_testi_company,$hp_testi_designation,$hp_testi_rating,$hp_testi_text,$hp_testi_id));
            $hp_testi_success = "<div class='success'>Successfully Edited Testimonial.</div>";
            $_POST = array();
        }
    }
}
$db_table_name = $wpdb->prefix . 'hp_testimonials';
$sql = "SELECT hp_testi_id, hp_testi_name, hp_testi_date, hp_testi_rating FROM ".$db_table_name;
$resultset_hp_testimonials = $wpdb->get_results($sql);
$i = 1;
if($resultset_hp_testimonials==NULL) {
    $no_result = TRUE;
} else {
    foreach($resultset_hp_testimonials as $result_hp_testimonials) {
        $hp_testimonial_result[$i][1] = $result_hp_testimonials->hp_testi_id;
        $hp_testimonial_result[$i][2] = $result_hp_testimonials->hp_testi_name;
        $hp_testimonial_result[$i][3] = $result_hp_testimonials->hp_testi_date;
        $hp_testimonial_result[$i][6] = $result_hp_testimonials->hp_testi_rating;
        $i++;
    }
}
 
?>
<script>
jQuery(document).ready(function($) {
    $("#hp_testi_loading").fadeOut(500);
    $(".testo_table").delay(500).fadeIn(1000);
});
</script>
<div style="margin: 30px;" id="hp_testi_loading">Loading... Please Wait</div>
<div style="display: none;" class="testo_table">
    <div class="top_logo_area">
        <div class="view_main_logo"></div>
        <div class="top_rating_shortcode">
            Use this shortcode to display top 5 Rated Testomonial <span style="font-weight: bold; color:#80a307;">[hp_testimonials n=<span style="color:red;">5</span>]</span><br> (Replace <span style="color:red;">5</span> with any number of testimonial you want to show)
        </div>
    </div>
    <?php if($hp_testi_error!="") { ?> <div class="error"><?php echo $hp_testi_error; ?></div> <?php } ?>
    <?php if($hp_testi_success!="") { ?> <div class="success"><?php echo $hp_testi_success; ?></div> <?php } ?>
    <?php
    if($no_result==TRUE) {
        echo "<h3 style='margin-top: 60px; margin-left: 20px;'>No Testimonials Found</h3>";
    } else {
    ?>
    <table id="view_testo_table" style="width: 95%; margin-left: 20px; margin-top: 30px;" cellspacing="0" >
    <tr>
        <th>ID</th>
        <th>Testimonial By</th>
        <th>Rating</th>
        <th>Date (Server Time)</th>
        <th>ShortCode</th>
        <th>Edit</th>
        <th>Delete</th>
    </tr>
    <?php for($j=1;$j<$i;$j++) { ?>
    <tr>
        <td><?php echo $hp_testimonial_result[$j][1]; ?></td>
        <td><?php echo $hp_testimonial_result[$j][2]; ?></td>
        <td><?php echo $hp_testimonial_result[$j][6]; ?></td>
        <td><?php echo(date("d M Y",$hp_testimonial_result[$j][3])); ?></td>
        <td>[hp_testimonials_single id=<?php echo $hp_testimonial_result[$j][1]; ?>]</td>
        <td>  
            <form id="edit<?php echo $hp_testimonial_result[$j][1]; ?>" method="post">
        	<input type="hidden" name="action" value="editview">
        	<input type="hidden" name="id" value="<?php echo $hp_testimonial_result[$j][1]; ?>">
        	<img style="cursor: pointer;" src="<?php echo plugins_url( 'assets/images/edit_btn.png', __FILE__ ); ?>" onclick="editFunction<?php echo $hp_testimonial_result[$j][1]; ?>()">
            </form>
            <script>
            function editFunction<?php echo $hp_testimonial_result[$j][1]; ?>() {
                document.getElementById("edit<?php echo $hp_testimonial_result[$j][1]; ?>").submit();
            }
            </script>
        </td>
        <td>
            <form id="delete<?php echo $hp_testimonial_result[$j][1]; ?>" method="post">
        	<input type="hidden" name="action" value="delete">
        	<input type="hidden" name="id" value="<?php echo $hp_testimonial_result[$j][1]; ?>">
        	<img style="cursor: pointer;" src="<?php echo plugins_url( 'assets/images/delete_btn.png', __FILE__ ); ?>" onclick="deleteFunction<?php echo $hp_testimonial_result[$j][1]; ?>()">
            </form>
            <script>
            function deleteFunction<?php echo $hp_testimonial_result[$j][1]; ?>() {
                document.getElementById("delete<?php echo $hp_testimonial_result[$j][1]; ?>").submit();
            }
            </script>
        </td>
    </tr>
    <?php }} ?>
</div>

