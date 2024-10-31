<?php
if ( ! current_user_can( 'edit_posts' ) ) {
    echo "<div class='error'>".__("You do not have the correct privileges to access this page",'nfpd')."</div>"; 
    die();
}
global $wpdb;
?>
<div class="wrap left"><h2 style='margin-bottom:20px;'><?php echo __("Prize Draw Details",'nfpd');?></h2>    
<?php

//first if any ids need to be deleted, do this here
if(isset($_GET['delid'])){
    if(nfpd_prize_draw_pro_remove_draw()){
        $html.="<div class='success'>".__("Prize draw has been successfully cleared",'nfpd').".</div>";
    }else{
        $html.="<div class='error'>".__("Prize draw could not be cleared",'nfpd')."./div>";
    }
}



if(@$_GET['edited']==1){
    echo '<div class="success">'.__("Your prize draw was updated successfully",'nfpd').'.</div>';
}else if(@$_GET['edited']==-1){
    echo '<div class="error">'.__("Error - could not update prize draw",'nfpd').'.</div>';
}

//store any amends that have occured    
if(isset($_POST['prize-name'])){
    echo __("Please wait...",'nfpd');
    if(nfpd_prize_draw_new_draw_form_sanitation()){
        echo '<script>window.location = "admin.php?page=nfpd_prize_draw&edited=1";</script>'; exit; 
    }else{
        echo '<script>window.location = "admin.php?page=nfpd_prize_draw&edited=-1";</script>'; exit; 
    }
}

//get current number of entries
$num_entries = 0;
$num_entries = $wpdb->get_var("SELECT count(id) as cnt FROM {$wpdb->nfpd_entries}");

$row = $wpdb->get_row("SELECT _prize_name as nam, _added_text as descr, _prizeimg_id as thumb,_terms_link, _pp_link,_type, _winner, _question, _answer,_answers,_registered FROM {$wpdb->nfpd} limit 0,1");

$imageid = $row->thumb;

$winner_id = $row->_winner;

$prize_name = stripslashes($row->nam);
$registered = $row->_registered;

$pplink = $row->_pp_link;

$termslink = $row->_terms_link;  


$additional_text = stripslashes($row->descr);

$type = $row->_type; 

$question = stripslashes($row->_question);

$answer = stripslashes($row->_answer);

if($type == "multi"){
	$answers = rtrim(str_replace("|","\n",stripslashes($row->_answers)),"\n");
}else{
	$answers = stripslashes($row->_answers);
}

$url = wp_get_attachment_image_src($imageid, "thumbnail", false);  

if(!$url[0]){
        $url[0] = plugins_url( '../img/missing_image.jpg', __FILE__ );
        $img_code = "<img src='".$url[0]."' style='max-width:200px;'  />";   
}else{
         $img_code = wp_get_attachment_image( $row->thumb, 'thumbnail' );
}


$image = "<div class='album_thumbnail' data-id='".$row->id."'>".$img_code."</div>";    


    //output screen
    ?>
    <div class="wrap">
        <p><i><?php echo __("Amend prize draw details below (* = required)","no-frills-prize-draw");?>.</i></p>
    </div>

    		<form  method="post" action="" enctype="multipart/form-data" id="editing_form" onsubmit="return validate();">
    		<input type="hidden" name="nfpd_nonce" value="<?php echo wp_create_nonce('no-frills-edit-prize-nonce'); ?>" />
            <input type="hidden" id="answers" name="answers" value="">
            <h3><?php echo __("Basic Prize Draw Details","no-frills-prize-draw");?></h3>            
            <div class='form-option'><?php echo __("Prize Draw Title","no-frills-prize-draw");?>*<br />
    	       <input type="text" name="prize-name" id="prize-name" value="<?php echo ( isset( $prize_name ) ? esc_attr( $prize_name ) : '' ); ?>" size="150" required />    
            </div>
            <div class='form-option'><?php echo __("Additional Text","no-frills-prize-draw");?><br />
                <textarea rows="10" cols="35" name="additional_text" id="additional_text"><?php echo( isset( $additional_text ) ? esc_attr( $additional_text ) : '' ); ?></textarea>
            </div>
            <div class="thumbnailer"><div class='prize-title'><?php echo __("Prize Image","no-frills-prize-draw");?></div>
               <?php echo $image; ?>
                <?php if ($winner_id<1){?> <input id="prize_img" name="prize_img" type="hidden" value="<?php echo $imageid; ?>" />
               <input id="prize_selector_button" class="button img-button" name="library_selector_button" type="button" value="<?php echo __("Change Image","no-frills-prize-draw");?>" /><?php } ?>
            </div> 
            <hr>
            <h3><?php echo __("Question / Answer","no-frills-prize-draw");?></h3>
            <div class="form-option"><?php echo __("Prize Draw Type","no-frills-prize-draw");?>*<br>
                <select id="draw_type" name="draw_type">
                    <option value='none' <?php  if($type=="none"){ echo "selected";}?>><?php echo __("Open (No questions)","no-frills-prize-draw");?></option>
                    <option value='multi' <?php  if($type=="multi"){ echo "selected";}?>><?php echo __("Multiple Choice Question/Answer","no-frills-prize-draw");?></option>
                    <option value='single' <?php  if($type=="single"){ echo "selected";}?>><?php echo __("Free-text Question/Response","no-frills-prize-draw");?></option>
                </select>
            </div>        
            <div class='form-option question-div <?php if($type!="none" && $type!=""){ echo "show";}?>'>Question","no-frills-prize-draw");?>*<br />
                <input type="text" name="question" id="question" maxlength="280" value="<?php echo( isset( $question ) ? esc_attr( $question ) : '' ); ?>" />
            </div>     
            <div class='form-option multiple-answer-div  <?php if($type=="multi" && $type!=""){ echo "show";}?>'><?php echo __("Multiple Answers","no-frills-prize-draw");?>*<br /><i><?php echo __("One answer per line","no-frills-prize-draw");?></i><br>
                <textarea rows="10" cols="35" name="multi-answers" id="multi-answers"><?php if($type=="multi"){ echo( isset( $answers ) ? esc_attr( $answers ) : '' ); } ?></textarea>
            </div>   
            <div class='form-option answer-correct-check-div <?php if($type!="none" && $type!=""){ echo "show";}?>'><?php echo __("Is there a correct answer?","no-frills-prize-draw");?><br>
                <select name="single-answer-has-correct" id="single-answer-has-correct"><option value='0' selected><?php echo __("NO","no-frills-prize-draw");?></option><option value='1' <?php if ( $answer!="" || $answer>0 ){ echo " selected";}?>><?php echo __("YES","no-frills-prize-draw");?></option></select>
            </div>     
            <div class='form-option multiple-answer-ans-div <?php if($type=="multi" && $type!="none" && $answer>0  && $type!=""){ echo "show";} ?>'><?php echo __("Correct Answer","no-frills-prize-draw");?>*<br /><i><?php echo __("Which line number is the correct answer?","no-frills-prize-draw");?></i><br>
                <input type="number" name="multi-answer" id="multi-answer" maxlength="3" value="<?php if($type=="multi" && $answer>0){echo $answer; } ?>" />
            </div>  
            <div class='form-option single-answer-div <?php if($type!="multi" && $type!="none" && $answer!=""  && $type!=""){ echo "show";} ?>'><?php echo __("Answer","no-frills-prize-draw");?>*<br /><i><?php echo __("Does not need to be case-specific","no-frills-prize-draw");?></i><br>
                <input type="text" name="single-answer"  id="single-answer" maxlength="150" value="<?php if($type!="multi" && isset( $answer )){ echo esc_attr( $answer ); } ?>" />
            </div>
            <hr>
            <h3><?php echo __("Additional Requirements","no-frills-prize-draw");?></h3>

            <div class='form-option termslink-div'><?php echo __("Terms &amp; Conditions Link","no-frills-prize-draw");?>*<br /><i><?php echo __("Provide the URL to your Terms &amp; Conditions","no-frills-prize-draw");?></i><br>
                 <input type="text" name="terms_link"  id="terms_link" maxlength="150" required placeholder="ie. http://www.yoursite.com/terms/" value="<?php echo($termslink); ?>"  />
            </div>     
            <div class='form-option pplink-div'><?php echo __("Privacy Policy Link","no-frills-prize-draw");?>*<br /><i><?php echo __("Provide the URL to your Privacy Policys","no-frills-prize-draw");?></i><br>
                 <input type="text" name="pp_link"  id="pp_link" maxlength="150" required placeholder="ie. http://www.yoursite.com/pp/" value="<?php echo($pplink); ?>"  />
            </div>                    
            <div class='form-option'><?php echo __("Registered users only?","no-frills-prize-draw");?><br /><i><?php echo __("If checked, log in and register links will be provided. Name and email details will be removed, data will be taken from the stored user details","no-frills-prize-draw");?>.</i><br>
                <input type="checkbox" name="registered"  id="registered" value="1"  <?php if($registered==1) echo " checked"; ?> /> <?php echo __("Users must be registered","no-frills-prize-draw");?>.
            </div> 

            <input type='button' class='button' value='<?php echo __("Go Back","no-frills-prize-draw");?>' onclick='document.location.href="admin.php?page=nfpd_prize_draw"'> <input type='button' class='button button-danger' value='<?php echo __("Remove Draw Details &amp; Entries","no-frills-prize-draw");?>' onclick='forceconfdel()'> <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __("Save Prize Draw Details","no-frills-prize-draw");?>"></form>

            <form class='inline-form' action='admin.php' method='get' id='nfpd_prize_draw_delete_form' onSubmit='return confdel()' ><input type='hidden' name='page' value='nfpd_prize_draw' /><input type='hidden' name='delid' value='1' /></form>
            <script>
            function forceconfdel(){
                $('#nfpd_prize_draw_delete_form').submit();
            }
            function confdel(){
                <?php if ($winner_id>0 || $num_entries==0){?> 
                if(confirm("<?php echo __("You are about to clear this prize draw and all entries. You will lose all the user entry data. Please download the CSV from the admin entries page to retain this data first. Click Ok to remove all data or Cancel to go back.","no-frills-prize-draw");?>")){return true;}else{return false;}
                <?php }else{?>
                    alert("<?php echo __("Prize draws cannot be cleared until a winner has been picked.","no-frills-prize-draw");?>");return false;<?php                    
                }?>
            }
            </script>
  </div><div class="wrap right">
    <div class='box'>
        <h2><?php echo __("Get No Frills Prize Draw PRO","no-frills-prize-draw");?></h2>
        <p><?php echo __("Need more features for your prize draws &amp; competitions? Check out <strong>No Frills Prize Draw PRO</strong>. Features include:-","no-frills-prize-draw");?></p>
        <ul>
            <li><?php echo __("Multiple prize draws / competitions that can be run concurrently","no-frills-prize-draw");?></li>
    <li><?php echo __("Include start and closing dates","no-frills-prize-draw");?></li>
    <li><?php echo __("Customise prize draws as text or multiple choice entry","no-frills-prize-draw");?></li>
    <li><?php echo __("Customise for use as prize draws, competitions or simple surveys","no-frills-prize-draw");?></li>
    <li><?php echo __("Custom Fields","no-frills-prize-draw");?></li>
    <li><?php echo __("Auto-email confirmation with opt-out","no-frills-prize-draw");?></li>
    <li><?php echo __("Multiple entry control","no-frills-prize-draw");?></li>
    <li><?php echo __("Social sharing","no-frills-prize-draw");?></li>
    <li><?php echo __("Display all prize draws as dropdown or simple list","no-frills-prize-draw");?></li>
    <li><?php echo __("Customisable/translatable front-end static text","no-frills-prize-draw");?></li>
    <li><?php echo __("Compatible with Wordpress translate system","no-frills-prize-draw");?></li>
    <li><?php echo __("GDPR friendly","no-frills-prize-draw");?></li>
    <li><?php echo __("PHP 7 compatible","no-frills-prize-draw");?></li>
    <li><?php echo __("Buy once, use <em>anywhere</em> on <em>any website</em>","no-frills-prize-draw");?></li>
        </ul>
        <div class='center'><a href='http://www.jamestibbles.co.uk/no-frills-prize-draw-pro' class='buybutton' target="_blank"><?php echo __("Go PRO Now","no-frills-prize-draw");?></a></div>
    </div>
    <br><Br>
    <div class='box'>
        <h2><?php echo __("Please rate this plug-in","no-frills-prize-draw");?></h2>
        <p><?php echo __("Please help support this plug-in by rating and/or reviewing it","no-frills-prize-draw");?>.</p>
        <div class='center'><a href='https://wordpress.org/plugins/no-frills-prize-draw/' class='buybutton' target="_blank"><?php echo __("Rate This Plug-in","no-frills-prize-draw");?></a></div>
    </div>
    <Br><Br>
    <div class='box'>
        <h2>Other Plug-ins</h2>
        <p>If you liked this plug-in why not check out the others. Click the button below to see what else is available.</p>
        <div class='center'><a href='http://www.jamestibbles.co.uk/more-plugins' class='buybutton' target="_blank">View more Plug-ins</a></div>
    </div>

  </div>
            
             <div class='question'>
                <div class='questionmark'></div><div class='questiontext'>
                    <strong><?php echo __("How do I display prize draws on my website?","no-frills-prize-draw");?></strong><br>
                    <?php echo __("Prize draws can be added anywhere on your website via the use of <i>Shortcodes</i>. <br>Simply click the \"<a href='/wp-admin/admin.php?page=nfpd_shortcode_generator'>Shortcodes</a>\" link, then copy and paste the code directly in to a new page or post","no-frills-prize-draw");?>.
            </div></div>