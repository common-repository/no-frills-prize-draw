<?php
/**
* Plugin Name: No Frills Prize Draw 
* Plugin URI: http://www.jamestibbles.co.uk/no-frills-prize-draw
* Description: An easy, quick, customisable prize draw/competition generator. View entrees and auto-pick a winner. Vary your competition requirements: multiple choice, single text or no questions at all. MANY MORE FEATURES available on the PRO version. Check it out at www.jamestibbles.co.uk/no-frills-prize-draw-pro.
* Version: 1.2.1
* Author: James Tibbles
* Author URI: http://www.jamestibbles.co.uk
* Remember to check out the FULL version, with lots of additional features at www.jamestibbles.co.uk/no-frills-prize-draw-pro
**/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


add_action( 'init', 'nfpd_prize_draw_enqueue_global_scripts_and_styles' );
function nfpd_prize_draw_enqueue_global_scripts_and_styles(){
	
}
 function nfpd_load_plugin_textdomain() {
    load_plugin_textdomain( 'no-frills-prize-draw', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'nfpd_load_plugin_textdomain' );

add_action( 'init', 'nfpd_prize_draw_register_tables', 1 );
add_action( 'switch_blog', 'nfpd_prize_draw_register_tables' );
function nfpd_prize_draw_register_tables() {
    global $wpdb;
    $wpdb->nfpd = "{$wpdb->prefix}nfpd";
    $wpdb->nfpd_entries = "{$wpdb->prefix}nfpd_entries";
}


add_action( 'admin_enqueue_scripts', 'nfpd_media_enqueue' );
function nfpd_media_enqueue($hook) {
    try{
        wp_enqueue_media();
    }catch(Exception $e){
    }
}



register_activation_hook(__FILE__,'nfpd_install');
function nfpd_install(){

    global $wpdb;

    $prizedraw_db = $wpdb->prefix . 'nfpd';

    $prizedraw_entry_db = $wpdb->prefix . 'nfpd_entries';
 
    if($wpdb->get_var("show tables like '$prizedraw_db'") != $prizedraw_db) 
    {

        $sql = "CREATE TABLE " . $prizedraw_db . " (
          `id` int(9) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
          `_prize_name` text NOT NULL,
          `_added_text` text NOT NULL,
          `_prizeimg_id` int(6) NOT NULL,
          `_type` varchar(50) NOT NULL,
          `_question` varchar(280) NOT NULL,
          `_answers` text,
          `_answer` varchar(155),
          `_registered` int(1) NOT NULL,
          `_terms_link` varchar(150) NOT NULL,
          `_pp_link` varchar(150) NOT NULL,
          `_winner` tinyint(9) NOT NULL,
          `_amended` datetime NOT NULL,
          `_created` datetime NOT NULL
        ) ENGINE=InnoDB;";

        $sql .= "CREATE TABLE " . $prizedraw_entry_db . " (
            `id` int(9) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `_answer` VARCHAR(250) NOT NULL,
            `_correct` int(1) NOT NULL DEFAULT '0',
            `_name` varchar(75) NOT NULL,
            `_email` varchar(75) NOT NULL,
            `_entrydate` datetime NOT NULL
        ) ENGINE=InnoDB;";
 
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
 
}


function nfpd_update_db_check() {
   /* global $wpdb;
    $prizedraw_d = $wpdb->prefix . 'nfpd';
    $sql="ALTER TABLE " . $prizedraw_d . " MODIFY _answer varchar(155);";
    $wpdb->query(  $sql  );
    $sql="ALTER TABLE " . $prizedraw_d . " MODIFY _answers text;";
    $wpdb->query(  $sql  );   
    //Make privacy, terms amends
    $row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = {$prizedraw_d} AND column_name = '_pp_link'"  );

    if(empty($row)){
       $wpdb->query("ALTER TABLE {$prizedraw_d} ADD _pp_link varchar(200) NOT NULL");
    }
    $row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = {$prizedraw_d} AND column_name = '_terms'"  );

    if(empty($row)){
       $wpdb->query("ALTER TABLE {$prizedraw_d} DROP COLUMN _terms ");
    }*/
}
add_action( 'plugins_loaded', 'nfpd_update_db_check' );


add_action( 'admin_menu', 'nfpd_prize_draw_admin_menu' );
function nfpd_prize_draw_admin_menu() {
	add_menu_page( 'View All Prize Draws', 'Prize Draw Details', 'edit_pages', 'nfpd_prize_draw', 'nfpd_prize_draw_edit_draw', 'dashicons-awards', 12  );
    add_submenu_page( 'nfpd_prize_draw', 'View Prize Draw Entries', 'View Prize Draw Entries', 'edit_pages', 'nfpd_prize_draw_view_draw_entries', 'nfpd_prize_draw_view_draw_entries');
    add_submenu_page( 'null', 'Select Prize Draw Winner', 'Select Prize Draw Winner', 'edit_pages', 'nfpd_pick_winner','nfpd_pick_winner');
    add_submenu_page( 'null', 'Select Prize Draw CSV', 'Select Prize Draw CSV', 'edit_pages', 'nfpd_prize_draw_export', 'nfpd_prize_draw_export');
    add_submenu_page( 'nfpd_prize_draw', 'Shortcode', 'Shortcode', 'edit_pages', 'nfpd_shortcode_generator', 'nfpd_shortcode_generator');
}

//Show all current prize draws in one table
function nfpd_prize_draw_view_all_draws(){ 
    wp_enqueue_style('style-css', plugins_url( '/css/style.css' , __FILE__ ));
    wp_enqueue_script( 'all-draws', plugins_url( '/js/all-draws-page.js' , __FILE__ ), array('jquery'), '1.0.0', true );
    wp_enqueue_script( 'draw-admin', plugins_url( '/js/draw_admin.js' , __FILE__ ), array('jquery'), '1.0.0', true );
    wp_enqueue_style('admin-css', plugins_url( '/css/admin.css' , __FILE__ ));
    include('admin/nfpd-view-all-admin.php'); 
}


//count number of entries in a draw
function nfpd_prize_draw_entries_total($id){
    global $wpdb;
    return $wpdb->get_var("SELECT COUNT(id) FROM {$wpdb->nfpd_entries} ");
 }


//Create a new draw    
function nfpd_prize_draw_edit_draw(){
    wp_enqueue_style('style-css', plugins_url( '/css/style.css' , __FILE__ ));
    wp_enqueue_script( 'media-library-popup', plugins_url( '/js/media-library-popup.js' , __FILE__ ), array('jquery'), '1.0.0', true );
    wp_enqueue_script( 'draw-admin', plugins_url( '/js/draw_admin.js' , __FILE__ ), array('jquery'), '1.0.0', true );
    wp_enqueue_style('admin-css', plugins_url( '/css/admin.css' , __FILE__ ));
    include('admin/nfpd-edit-draw-admin.php');  
}

//Shortcode generator page
function nfpd_shortcode_generator(){
    wp_enqueue_style('style-css', plugins_url( '/css/style.css' , __FILE__ ));
    wp_enqueue_script( 'shortcode-generator', plugins_url( '/js/shortcode-generator.js' , __FILE__ ), array('jquery'), '1.0.0', true );
    wp_enqueue_style('admin-css', plugins_url( '/css/admin.css' , __FILE__ ));
    include('admin/nfpd-shortcode-generator-admin.php'); 
}


//Validate upload details on new prize draw
function nfpd_prize_draw_new_draw_form_sanitation() {

    global $wpdb;

   
    $ok = true;

    // if the submit button is clicked, send the email
    if ( is_admin() && isset($_POST) && wp_verify_nonce($_POST['nfpd_nonce'], 'no-frills-edit-prize-nonce')  ) {
    

         //delete old prize draw
        $sql = "TRUNCATE " . $wpdb->nfpd;
            
        $result = $wpdb->query($sql);


        // sanitize form values
        $prize_name = sanitize_text_field( $_POST["prize-name"] );
        $prize_additional_text = esc_textarea(sanitize_text_field($_POST["additional_text"])); 
        $multiple_choice_answers = $_POST['answers'];
        $multi_answer = sanitize_text_field( $_POST["multi-answer"] );
        $single_answer = sanitize_text_field( $_POST["single-answer"] );
        $question = sanitize_text_field( $_POST["question"] );

        if($question!= "" && $multiple_choice_answers!=""){
            $answer = $multiple_choice_answers;
            $type = "multi";
            if($multi_answer>0){
                $real_answer = $multi_answer;
            }
        }else if($question!= "" && $multiple_choice_answers==""){
            $answer = "";
            $type = "single";
            if($single_answer!=""){
                $real_answer = $single_answer;
            }else{
                $real_answer = "";
            }
            
        }else if($question== "" && $single_answer!=""){
            $real_answer = $single_answer;
            $type = "single";         
        }else{
            $type = "none";
        }

        if(isset($_POST['prize_img'])){
            $prizeid = $_POST['prize_img'];
        }else{
            $prizeid = 0;  
        }
        if(isset($_POST['registered'])){
                $registered = 1;
        }else{
                $registered = 0;  
        }

        $terms_link = sanitize_text_field($_POST['terms_link']);
        $pp_link = sanitize_text_field($_POST['pp_link']);

        
        $data = array(
                '_prize_name' => $prize_name,
                '_added_text'    => $prize_additional_text,
                '_prizeimg_id'    => $prizeid,
                '_type' => $type,
                '_question' => $question,
                '_answers' => $answer,
                '_answer' => $real_answer,
                '_terms_link' => $terms_link,
                '_pp_link' => $pp_link,
                '_winner' => '0',
                '_registered' => $registered,
                '_created'    => current_time( 'mysql' ),
                '_amended'    => current_time( 'mysql' )
        );
        $format = array(
                '%s',
                '%s',
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%d',
                '%d',
                '%s',
                '%s'
        );
        
        $success=$wpdb->insert( $wpdb->nfpd, $data, $format );
        
        if(!$success){
                $ok = false;
        }

    }

    return $ok;
}


//Remove a draw after delete button is pressed
function nfpd_prize_draw_pro_remove_draw(){

    if ( is_admin() && isset($_GET['delid'])){

        global $wpdb;

        $sql = "TRUNCATE " . $wpdb->nfpd;
            
        $result = $wpdb->query($sql);

        $sql = "TRUNCATE " . $wpdb->nfpd_entries;
            
        $result = $wpdb->query($sql);



        return true;

    }else{

        return false;

    }

}


//Remove a draw after delete button is pressed
function nfpd_prize_draw_remove_draw_entries(){

    if ( is_admin() && isset($_GET['delid'])){

        global $wpdb;

        $sql = "TRUNCATE " . $wpdb->nfpd_entries;
            
        $result = $wpdb->query($sql);

        return true;

    }else{

        return false;

    } 

}




//View a list of entries for a prize draw
function nfpd_prize_draw_view_draw_entries(){
    wp_enqueue_style('style-css', plugins_url( '/css/style.css' , __FILE__ ));
    wp_enqueue_style('admin-css', plugins_url( '/css/admin.css' , __FILE__ ));
    include('admin/nfpd-view-entrees-admin.php');      
}



function nfpd_comp_completed(){

    global $wpdb;

    $winnerid = $wpdb->get_var("SELECT _winner FROM {$wpdb->nfpd}");

    return ($winnerid>0); 

}



//Ajax query to return image object for prize thumbnail
function nfpd_prize_draw_get_prize_image() {

    // get the submitted parameters
   $id = $_POST['id'];

   $img = wp_get_attachment_image( $id, 'thumbnail' );

   echo $img;

   exit;
}
add_action( 'wp_ajax_get_prize_image', 'nfpd_prize_draw_get_prize_image' );





function nfpd_prize_draw_export(){
    wp_enqueue_style('style-css', plugins_url( '/css/style.css' , __FILE__ ));

    if(is_admin() && !isset($_GET['del-csv-id'])){

        global $wpdb;

        $content = "Name,Email,Answer,Correct? (if applicable),Date Entered,\n";    
        
        $entries = $wpdb->get_results("SELECT a.*, a._answer as _answ, b._answer,a.id as uid,b._winner as winner FROM {$wpdb->nfpd_entries} a LEFT JOIN {$wpdb->nfpd} b ON b.id>0");//SELECT a.*, a._answer as _answ, b._answer,b.id as uid,a._winner as winner FROM {$wpdb->nfpd_entries} a LEFT JOIN {$wpdb->nfpd} b ON b.id>0");

        if(count($entries)<1){ 
             $content .= "No entries found,,,,,\n";    
        }

        foreach($entries as $ent){ 

            $realanswer = ($ent->_answer);

           
            $curtime = time();

            if($realanswer!="" ){
                if($ent->_correct == 1){
                    $corr_ans = "YES";
                }else if($ent->_correct == 0){
                    $corr_ans = "NO";
                }else{
                    $corr_ans = "N/A";
                }
            }else{
                $corr_ans = "N/A";
            }
            $_answ = str_replace("|"," | ",$ent->_answ);
            $_answ = str_replace(","," ",$_answ);
            if($ent->winner==$ent->uid){
                $winner = "WINNER";
                $email = $ent->_email;
            }else{
                $winner = "";
                $email = "Hidden";
            }
            $content .= $ent->_name.",".$email.",".$_answ.",".$corr_ans.",".date("jS F Y", strtotime($ent->_entrydate)).",".$winner."\n"; 
         
        }
        $rnd = md5(rand(0,9999));
        $csv_handler = fopen (plugin_dir_path( __FILE__ ).'export/prizedraw-entries-'.$rnd.'.csv','w');
        fwrite ($csv_handler,$content);
        fclose ($csv_handler);

        ?>
        <div class="wrap">
        <h2><?php echo __("Prize Draw Export","no-frills-prize-draw");?></h2>    
        <p><?php echo __("CSV file has been generated. Please click the link below to download the csv file","no-frills-prize-draw");?>.</p><p><strong><a href='<?php echo plugins_url( 'export/prizedraw-entries-'.$rnd.'.csv', __FILE__ ); ?>'>-<?php echo __("CLICK HERE TO DOWNLOAD CSV FILE","no-frills-prize-draw");?>-</a></strong></p>   
        <h2><?php echo __('User Data, GDPR and other Privacy Protection Laws',"no-frills-prize-draw");?></h2>
     <p>
        <?php echo __("Due to changes in privacy protection laws the csv file will only display the winners email address. This is for the user's protection. Please adhere to all privacy policy laws and do not miss-use the user's data for anything other than that which is agreed upon within your own privacy policy and terms.","no-frills-prize-draw");?>
     </p>
        <h2><?php echo __("Downloaded it? Now Delete It","no-frills-prize-draw");?></h2><p><?php echo __("For security please now <a href='admin.php?page=nfpd_prize_draw_export&del-csv-id=<?php echo $rnd; ?>'>REMOVE THE CSV</a> from the server. You can rebuild the csv file any time from the draw entries details page","no-frills-prize-draw");?>.</p>   
        </div><?php

    }else if( is_admin() && isset($_GET['del-csv-id']) ){
        ?><div class="wrap">
        <h2><?php echo __("Prize Draw Export Removal","no-frills-prize-draw");?></h2> <?php
        $file = plugin_dir_path( __FILE__ ).'export/prizedraw-entries-'.$_GET['del-csv-id'].".csv";

       if (file_exists($file)) 
        {
            if(unlink($file))
            {
                ?><p><?php echo __("Thanks, this csv file has now been removed. You can rebuild the csv file any time from the draw entries details page","no-frills-prize-draw");?>.</p><p><?php echo __("You can now close this page","no-frills-prize-draw");?>.</p><?php
            } 
            else 
            {
                 ?><p><?php echo __("We couldn't delete this file right now","no-frills-prize-draw");?> (<?php echo $file;?>). <?php echo __("Please try again. If you still cannot delete please contact us with the filename and we will remove manually","no-frills-prize-draw");?>.</p><?php
            }
        }else{
            ?><p><?php echo __("This csv file has been removed. You can rebuild the csv file any time from the draw entries details page","no-frills-prize-draw");?>.</p><p><?php echo __("You can now close this page","no-frills-prize-draw");?>.</p><?php
        }
               
        ?>          
       
        </div><?php

    }

}


//Paginate the user results
function nfpd_prize_draw_custom_pagination($pageurl,$total,$resultsperpage,$pagenum,$email,$filter,$pageis){
  
  $count = $total;

  $did="";

  $em = "";

  $pagination = "";

  if($pagenum>1){$pagenum_prev = $pagenum-1;}else{$pagenum_prev=1;}

  if($pagenum<$count){$pagenum_next = $pagenum+1;}else{$pagenum_next=$count;}

  if($pagenum==1){$poff = " off";}else{$poff="";}

  if(isset($email)) $em = "<input type='hidden' name='email' value='".$email."' />";

  if(isset($filter)) $fi = "<input type='hidden' name='filter' value='".$filter."' />";

  $page = "<input type='hidden' name='page' value='".$pageis."' />";

  
  $pagination = "<form method='get' action='".$pageurl."'><input type='hidden' name='paged' value='1'/><input type='submit' class='pagination".$poff."' value='« ' >".$em.$page.$fi."</form> <form method='get' action='".$pageurl."'><input type='hidden' name='paged' value='".$pagenum_prev."'/><input type='submit' class='pagination".$poff."' value='‹ Previous' >".$em.$page.$fi."</form>";

  //get numbered pages
  for($i=$pagenum-5;$i<=$pagenum+5;$i++){
    if($i>0 && $i<=$count) {
        if($pagenum == $i){
          //on this page
            $pagination .= "<span class='pagination'> ".($i)."</span> ";
        }else{
          //not on this page
            $pagination .= "<form method='get' action='".$pageurl."'><input type='hidden' name='paged' value='".($i)."'/>".$em.$page.$fi."<input type='submit' class='pagination' value='".($i)."' ></form>";
          }
    }
  }

  if($pagenum==$total){$poff = " off";}else{$poff="";}

  $pagination .= "<form method='get' action='".$pageurl."'><input type='hidden' name='paged' value='".$pagenum_next."'/><input type='submit' class='pagination".$poff."' value='Next ›' >".$em.$fi."</form> <form method='get' action='".$pageurl."'><input type='hidden' name='paged' value='".$count."'/><input type='submit' class='pagination".$poff."' value='»' >".$em.$page.$fi."</form>";


  return array($pagination);

}


//Admin can remove a user from a prize draw before the draw winner is picked
function nfpd_prize_draw_remove_user($uid){

     $ok = false;

     if ( is_admin() && wp_verify_nonce($_GET['custom_meta_box_nonce'], 'no-frills-delete-user-nonce') ){

        global $wpdb;

        $deets = $wpdb->get_row("SELECT _winner FROM {$wpdb->nfpd}");

        if($deets->_winner == 0){

            if($wpdb->delete( $wpdb->nfpd_entries, array( 'id' => $uid, ), array( '%d' ) )){
                $ok = true;
            }

        }else{
            $ok = false;
        }

     }

     return $ok;
}



//pick a prize draw winner
function nfpd_pick_winner(){
    wp_enqueue_style('style-css', plugins_url( '/css/style.css' , __FILE__ ));

    $ok = false;

    if ( is_admin() ){

        global $wpdb;

        ?>
        <div class="wrap">
        <h2>Picking a Winner...</h2>
        <p>Please wait while we pick a winner. You will be redirected shortly.</p></div><?php

        $deets = $wpdb->get_row("SELECT _answer, _winner FROM {$wpdb->nfpd} limit 0,1");

        if($deets->_winner == 0){

            if(isset($_REQUEST['uid']) && wp_verify_nonce($_REQUEST['custom_nonce'], 'no-frills-select-winner-nonce')  ){
                $uid = $_REQUEST['uid'];
                if($deets->_answer!=""){
                    $sql = "SELECT id as uid from {$wpdb->nfpd_entries} where _correct=1 and id=%d";
                }else{
                    $sql = "SELECT id as uid from {$wpdb->nfpd_entries} where id=%d";
                }
                $winner = $wpdb->get_row($wpdb->prepare($sql, array($uid)));

            }else if(!isset($_REQUEST['uid']) && wp_verify_nonce($_REQUEST['custom_main_nonce'], 'no-frills-select-winner-nonce')  ){

                if($deets->_answer!=""){


                    $sql = " SELECT id as uid FROM {$wpdb->nfpd_entries} WHERE id >= (SELECT FLOOR( MAX(id) * RAND()) FROM {$wpdb->nfpd_entries}  where _correct=1 ) and _correct=1 ORDER BY id LIMIT 1;";//"SELECT id as uid, FLOOR(1 + RAND() * id) as 'rand_ind' from {$wpdb->nfpd_entries} where _correct=1 ORDER BY 'rand_int' LIMIT 0,1";
                }else{
                    $sql = " SELECT id as uid FROM {$wpdb->nfpd_entries} WHERE id >= (SELECT FLOOR( MAX(id) * RAND()) FROM {$wpdb->nfpd_entries} ) ORDER BY id LIMIT 1;";//SELECT id as uid, FLOOR(1 + RAND() * id) as 'rand_ind' from {$wpdb->nfpd_entries} ORDER BY 'rand_int' LIMIT 0,1";
                }
                $winner = $wpdb->get_row($sql);

            }else{
                print "Error<BR>";
                die();
            }           
            
            $winner_id = $winner->uid;

            if($winner_id>=1){

                //Save winner
                $ok=$wpdb->update( 

                    $wpdb->nfpd, 

                    array( 
                    '_winner' => $winner_id,
                    '_amended'    => current_time( 'mysql' )
                    ), 
                    array( 'id' => 1 ), 
                    array( 
                        '%d',                   
                        '%s'
                    ), 
                    array( '%d' ) 

                );

            }

        }

    }

    if($ok){
        ?><script>document.location.href='admin.php?page=nfpd_prize_draw_view_draw_entries&picked=1';</script><?php

    }else{
        ?><script>document.location.href='admin.php?page=nfpd_prize_draw_view_draw_entries&picked=-1';</script><?php
    }
    
    
}




//Show prize draw entry details
function nfpd_prize_draw_entry($atts){
    wp_enqueue_style('style-css', plugins_url( '/css/style.css' , __FILE__ ));

    $html = "";
    
    global $wpdb;

    if(!isset($_POST['entry_name']) && !isset($_POST['entry_email']) ){ 

            $html .= "<form id='win-prize-form' class='win-prize-form' action='' method='post'>";

            $html .= "<input type='hidden' name='entry_nonce_check' value='".wp_create_nonce('no-frills-enter-prize-nonce')."'";

            $data = $wpdb->get_row("SELECT * FROM {$wpdb->nfpd} where _winner<1 limit 0,1;");
            
            if($data->_prize_name==""){

                $html .= "<p class='error-text'>".__("Unfortunately there is no prize draw available","no-frills-prize-draw")."</p>";

            }else if($data->_registered==1 && !is_user_logged_in()){

                $html .= "<p class='warning-text'>".__("This prize draw is only open to registered members","no-frills-prize-draw").".</p>";

                $html .= "<p class='register-text'>".__("Not a member?","no-frills-prize-draw")." <a href='".wp_registration_url()."'>".__("Click Here","no-frills-prize-draw")."</a> ".__("to register now","no-frills-prize-draw").".</p>";

                $html .= "<p class='login-text'>".__("Already a member?","no-frills-prize-draw")." <a href='".wp_login_url()."'>".__("Click Here","no-frills-prize-draw")."</a> ".__("to log in now","no-frills-prize-draw").".</p>";

            }else{
                
                $html .= "<h3 class='prize-title'>".stripslashes($data->_prize_name)."</h3>";

                //If there is an image SHOW HERE
                if($data->_prizeimg_id>0){
                    $imageid = $data->_prizeimg_id;
                    $url = wp_get_attachment_image_src($imageid, "full", false);  
                    if($url[0]){
                        $html .= "<div class='prize-image'><img src='".$url[0]."' alt='".($data->_prize_name)."' title='".($data->_prize_name)."'  /></div>";   
                    }
                }

                if($data->_added_text!=""){
                    $html .= "<p class='prize-additional-text'>".stripslashes($data->_added_text)."</p>";
                }

                $html .= '<form action="" method="post"  enctype="multipart/form-data">';

                if($data->_type=="none"){

                    //no questions required

                }else if($data->_question!=""){

                    $html .= "<p class='prize-question'><strong class='q-sign'>Q.</strong> ".stripslashes($data->_question)."</p>";

                    if($data->_type=="single"){

                        $html .= "<div class='prize-answer-field prize-single'><strong class='a-sign'>A.</strong> <input type='text' required value='' placeholder='".__("Enter answer here...","no-frills-prize-draw")."' id='prize-answer' name='prize-answer' ".stripslashes($data->_question)." /></div>";

                    }else if($data->_type=="multi"){

                        $html .= "<div class='prize-mulitple-choice'><p class='a-intro-text'><strong class='a-sign'>A.</strong> ";

                        $answers_array = explode("|",$data->_answers);

                         if($data->_answer==""){

                            $html .= __("Select one or more from the choices below...","no-frills-prize-draw")."</p>";

                            //can select more than one answer, as there is no right answer
                            $count = 0;
                            foreach($answers_array as $ans){
                                if($ans!=""){
                                    $count++;
                                    $html .= "<div class='multiple-answer'><label><input type='checkbox' value='".$count."' name='prize-answer[]' />".$ans."<span></span></label></div>";
                                }
                            }


                         }else{

                            $html .= __("Select one from the choices below...","no-frills-prize-draw");
                            $html .= "</p>";

                            //can only select one answer
                            $count = 0;
                            foreach($answers_array as $ans){
                                if($ans!=""){
                                    $count++;
                                    $html .= "<div class='multiple-answer'><label><input type='radio' value='".$count."' name='prize-answer[]' required />".$ans."<span></span></label></div>";
                                }
                            }

                         }

                        $html .= "</div>";

                    }

                }



                //Show Name and email, auto-filling if logged in

                if(is_user_logged_in()){
                        global $current_user;
                        get_currentuserinfo();

                        if($current_user->display_name!=""){
                            $name = $current_user->display_name;
                        }else if($current_user->user_name!=""){
                            $name = $current_user->user_name;
                        }else if($current_user->user_firstname!=""){
                            $name = $current_user->user_firstname. " " .substr($current_user->user_lastname, 0, 1).".";
                        }else if($current_user->user_fullname!=""){
                            $name = $current_user->user_fullname;
                        }else{
                            $name = "";
                        }
                        $email = $current_user->user_email;


                }else{
                        $name = "";
                        $email = "";
                }
                $html .= "<div class='basic_fields'><h3>".__("Please Enter Your Details","no-frills-prize-draw")."</h3>";
                $html .=  "<div class='name-text'><label>".__("Your Nickname","no-frills-prize-draw")."*</label> <input type='text' maxlength='60' id='entry_name' name='entry_name' required value='".$name."'  /></div>";
                $html .=  "<div class='email-text'><label>".__("Your Email","no-frills-prize-draw")."*</label> <input type='email' maxlength='100' id='entry_email' name='entry_email' required value='".$email."'  /></div>";
                $html .=  "</div>";


                $html .= "<div class='submit_fields'>";
                $html .=  "<div class='terms-div'><label><input type='checkbox' value='1' name='terms' required />".__("You must agree to our","no-frills-prize-draw")." <a href='".$data->_terms_link."' target='_blank'>".__("Terms &amp; Conditions","no-frills-prize-draw")."</a><span></span></label></div>";
               $html .=  "<div class='pp-div'><label><input type='checkbox' value='1' name='pp' required />".__("You must agree to our","no-frills-prize-draw")." <a href='".$data->_pp_link."' target='_blank'>".__("Privacy Policy","no-frills-prize-draw")."</a><span></span></label></div>";
               

                $html .= "<input type='submit' id='submit_entry' name='submit_entry' value='".__("Submit Entry","no-frills-prize-draw")."'>";
                $html .=  "</div>";

                $html .= "</form>";

            }

    }else if(
        isset($_POST['entry_name']) && 
        isset($_POST['entry_email']) && 
        wp_verify_nonce($_POST['entry_nonce_check'], 'no-frills-enter-prize-nonce') 
    ){

            $ok = false;

            //before anything else, check user hasn't entered already
            $times_entered = $wpdb->get_var($wpdb->prepare("SELECT count(id) as cnt FROM {$wpdb->nfpd_entries} where _email=%s",array($_POST['entry_email'])));
                

            if($times_entered>0){

                $html .= "<h2>You have already entered this prize draw</h2>";
                $html .= "<p>You can only enter once.</p>";
                $html .= "<p>&nbsp;</p>";
                $html .= "<p><a href='javascript:history.back();'>- Click here to go back -</a></p>";

            }else{
                $data = $wpdb->get_row(("SELECT * FROM {$wpdb->nfpd} where _winner='' limit 0,1;"));
                
                if($data->_prize_name!=""){

                    if($data->_registered==0 || ($data->_registered==1 && is_user_logged_in())){

                        
                            $name = $_POST['entry_name'];
                            $email = $_POST['entry_email'];
                            $real_answer = $data->_answer;
                            
                            $given_answer = $_POST['prize-answer'];
                            if($data->_type=="multi"){
                                $possible_answers = explode("|",($data->_answers));
                                if(count($possible_answers)==0){
                                    $possible_answers[0] = $data->_answers;
                                }
                               
                                $given_answer = "";
                                foreach($_POST['prize-answer'] as $curr_ans_id){
                                    $given_answer .= $possible_answers[$curr_ans_id-1]." + ";
                                }
                                $given_answer = rtrim($given_answer," + ");
                            }

                            //is correct?
                            if($data->_type=="none" || $real_answer==""){$correct = -1;}
                            else if($data->_type=="single" && strtolower($real_answer)==strtolower(@$_POST['prize-answer'])){$correct = 1;}
                            else if($data->_type=="multi" && $real_answer!="" && $real_answer==$_POST['prize-answer'][0]){$correct = 1;}
                            else{$correct = 0;}


                            $data = array(  
                                    '_answer' => sanitize_text_field($given_answer),
                                    '_correct' => $correct,
                                    '_name' => sanitize_text_field($name),
                                    '_email' => sanitize_text_field($email),
                                    '_entrydate'    => current_time( 'mysql' )
                            );
                            
                            $format = array(
                                    '%s',
                                    '%d',
                                    '%s',
                                    '%s',
                                    '%s'                
                            );
                            
                            $success=$wpdb->insert( $wpdb->nfpd_entries, $data, $format );
                            
                            if($success){
                                $ok = true;
                            }

                    }

                }


                if(!$ok){
                    $html .= "<h2>".__("There was an error. Missing data.","no-frills-prize-draw")."</h2>";
                    $html .= "<p>".__("We couldn't save your details at this time.","no-frills-prize-draw")."</p>";
                    $html .= "<p>&nbsp;</p>";
                    $html .= "<p><a href='javascript:history.back();'>- ".__("Click here to go back and try again","no-frills-prize-draw")." -</a></p>";
                }else{
                    $html .= "<h2>". __("Thanks for entering!","no-frills-prize-draw")."</h2>";
                    $html .= "<p>". __("We will notify the winner once the competition ends.</p><p><strong>Good luck!</strong></p>","no-frills-prize-draw");
                }

            }

    }else{
            $html .= "<h2>".__("There was an error. Missing data.","no-frills-prize-draw")."</h2>";
            $html .= "<p>".__("We couldn't save your details at this time.","no-frills-prize-draw")."</p>";
            $html .= "<p>&nbsp;</p>";
    }


    return $html;

}
add_shortcode("nfpd_entry_page", "nfpd_prize_draw_entry");