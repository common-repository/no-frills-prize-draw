<?php
 if ( ! current_user_can( 'edit_posts' ) ) {
    echo "<div class='error'>".__("You do not have the correct privileges to access this page","no-frills-prize-draw")."</div>"; 
    die();
}
global $wpdb;  
 
    if ( is_admin() ) {

        ?>
        <div class="wrap left">
            <h2 style='margin-bottom:20px;'><?php echo __("Prize Draw Entries","no-frills-prize-draw");?></h2><?php

            if(isset($_GET['delid'])){

               if(nfpd_prize_draw_remove_user($_GET['delid'])){
                    ?><div class="success"><?php echo __("User has been removed from this prize draw","no-frills-prize-draw");?>.</div><?php
               }else{            
                    ?><div class="error"><?php echo __("User could not be removed from this prize draw. A user can only be removed once, and if the winner has not yet been chosen","no-frills-prize-draw");?>.</div><?php
               }

            }

            if(@$_GET['picked']==1){
                    ?><div class="success"><?php echo __("A winner was successfully chosen. See details below","no-frills-prize-draw");?>.</div><?php
            }else if(@$_GET['picked']==-1){        
                    ?><div class="error"><?php echo __("A winner could not be picked at this time","no-frills-prize-draw");?>.</div><?php
            }

            if(!isset($_GET['id']) ){
                
                     $id = 0;
                    ?><!--div class="error">Missing ID</div--><?php

            }else{

                $id = $_GET['id'];

            }

                    global $wpdb;

                    $prize_details = $wpdb->get_row("SELECT * FROM {$wpdb->nfpd} ");

                    $prize_title = stripslashes($prize_details->_prize_name);
                    $winner = $prize_details->_winner;
                    $picked= $prize_details->_amended;
                    $realanswer = stripslashes($prize_details->_answer);
                    ?>      
                    <p style="padding-left:4px;"><i><?php echo __("All entries for prize draw","no-frills-prize-draw");?> (<?php echo $prize_title; ?>).</i></p><?php

                    $curtime = time();

                    if($winner>0){
                        $canrem = false;
                        $winner_details = $wpdb->get_row("SELECT b._answer as _answ, b._name as n, b._email as e, b.id as wid FROM {$wpdb->nfpd} a LEFT JOIN {$wpdb->nfpd_entries} b ON a._winner=b.id ");
                        ?>
                        <div class='winner_details'><p><?php echo __("A winner was picked for this prize draw on","no-frills-prize-draw");?> <?php echo date("jS F Y", strtotime($picked)); ?>. <?php echo __("Please contact them directly to send the prize out.</p><p><strong><u>Winner Details</u></strong>","no-frills-prize-draw");?></p><p style="padding-bottom:20px;">
                        <?php if($winner_details->_answ!=""){?><strong><?php echo __("Winner Answer","no-frills-prize-draw");?>:</strong> <?php echo $winner_details->_answ."<br>"; }?>                
                        <strong>Name:</strong> <?php echo $winner_details->n; ?><br><strong><?php echo __("Email","no-frills-prize-draw");?>:</strong> <?php echo $winner_details->e;
                        ?></p></div>                
                        <?php
                        $wid = $winner_details->wid;
                    }else{
                        $canrem = true;
                        ?><p style="padding-bottom:20px;padding-left:4px;"><?php echo __("A winner has not yet been chosen for this draw","no-frills-prize-draw");?>. <?php
                        $num_entrees = $wpdb->get_var("SELECT count(id) FROM {$wpdb->nfpd_entries}");
                        if($num_entrees==0){?>
                            <?php echo __("Nobody has enterred the prize draw yet","no-frills-prize-draw");?>.
                        <?php }else if($realanswer!="") { ?>
                            <?php echo __("Click the button below to randomly select a winner","no-frills-prize-draw");?>.<?php 
                        }else{ ?>
                           <?php echo __("Choose your winner manually using the options next to each entry, or click below to pick a winner randomly","no-frills-prize-draw");?>. <?php
                        }
                        if($num_entrees>0) {?><form action="" method="get"><input type="hidden" name="custom_main_nonce" value="<?php echo wp_create_nonce('no-frills-select-winner-nonce'); ?>" /><input type="hidden" name="page" value="nfpd_pick_winner" /><input type="hidden" name="id" value="<?php echo $id; ?>" /><input type='submit' class='button button-primary' id='button-winner' value='<?php echo __("Pick A Winner Now","no-frills-prize-draw");?>' /></form><br><br></p><?php }
                    } 

                    //set up paginate
                   
                    $pageurl = 'admin.php';

                    $perpage = 100;

                    if(@$_GET['paged']>0) $page = $_GET['paged'];

                    else $page = 1;

                    $total_results = nfpd_prize_draw_entries_total($id);

                    $totalpages = ceil($total_results / $perpage);


                    //Get this page's results

                    $startpos = $perpage*($page-1);

                   /* if(@$_GET['email']!=""){
                            $email = $_GET['email'];
                            if(@$_GET['filter']=="1" && $realanswer!=""){
                                $entries = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->nfpd_entries} WHERE (_email LIKE '%%%s%%' or _email = %s) and _correct=1 limit {$startpos},{$perpage}",array($email,$email) ));
                            }else{
                                $entries = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->nfpd_entries} WHERE (_email LIKE '%%%s%%' or _email = %s) limit {$startpos},{$perpage}",array($email,$email) ));
                            }
                    }else{*/
                            if(@$_GET['filter']=="1" && $realanswer!=""){
                                $entries = $wpdb->get_results("SELECT * FROM {$wpdb->nfpd_entries} WHERE  _correct=1 limit {$startpos},{$perpage}");
                            }else{
                                $entries = $wpdb->get_results("SELECT * FROM {$wpdb->nfpd_entries} limit {$startpos},{$perpage}");
                            }
                    /*}   */ 
                    ?>
                    <?php if($realanswer!="") {?><form class='define' action="" method="get"><input type='hidden' name='page' value='nfpd_prize_draw_view_draw_entries'/><?php echo __("Filter","no-frills-prize-draw");?>: <select id='filter' name='filter'><option value='0'><?php echo __("All","no-frills-prize-draw");?></option><option value='1' <?php if(@$_GET['filter']=="1"){echo " selected";}?>><?php echo __("Only Correct Entries","no-frills-prize-draw");?></option></select> <input type="submit" value='<?php echo __("Refine results","no-frills-prize-draw");?>' class='button' /></form><?php } ?>
                    <table class='prizedraw-table wp-list-table widefat fixed striped posts'><thead><tr><th class='center'><?php echo __("Nickname","no-frills-prize-draw");?></th><th class="center"><?php echo __("Email","no-frills-prize-draw");?></th><th class="center"><?php echo __("Answer","no-frills-prize-draw");?></th><?php  if($realanswer!="" ){echo"<th class='center'>".__("Correct Answer?","no-frills-prize-draw")."</th>";} ?><th class="center"><?php echo __("Date Entered","no-frills-prize-draw");?></th><th class='optionstd'><?php echo __("Options","no-frills-prize-draw");?></th></tr></thead>
                    <?php 

                    if(count($entries)<1){
                        if($realanswer!="" ){$cols=8;}else{$cols=7;}
                         echo "<tr><td colspan='".$cols."'><i>".__("No entries found","no-frills-prize-draw")."</i></td></tr>";
                     
                    }
                    foreach($entries as $ent){

                        if($realanswer!="" ){
                            if($ent->_correct == 1){
                                $corr_ans = "YES";
                            }else{
                                $corr_ans = "NO";
                            }
                        }else{
                            $corr_ans = "-";
                        }

                        $_ans = str_replace("|","<br>",$ent->_answer);
                        $_address = $ent->_address;
                     
                        echo "<tr";

                        if($_ans==""){$_ans = "-";}
                        if($ent->id ==$wid){
                            echo " class='winner_line'";
                            echo "><td class='center'>".$ent->_name."</td><td class='center'>".$ent->_email."</td><td class='center'>".$_ans."</td>";
                        }else{
                            echo "><td class='center'>".$ent->_name."</td><td class='center'><i>**Hidden</i></td><td class='center'>".$_ans."</td>";
                        }
                        if($realanswer!="" ){ echo "<td class='center'>".$corr_ans."</td>"; }
                        
                        echo "<td class='center'>".date("jS F, Y", strtotime($ent->_entrydate))."</td><td class='optionstd'>";
                     
                        if($canrem){
                            echo "<form action='' method='get' onsubmit='return confdel();'><input type='hidden' name='page' value='nfpd_prize_draw_view_draw_entries' /><input type='hidden' name='custom_meta_box_nonce' value='".wp_create_nonce('no-frills-delete-user-nonce')."' /><input type='hidden' name='delid' value='".$ent->id."'><input type='submit' value='".__("Remove User","no-frills-prize-draw")."' class='button button-danger' /></form>";
                            if($realanswer==""  && $winner==0){
                               if($num_entrees>0) echo "<form action='' method='get' onsubmit=''><input type='hidden' name='page' value='nfpd_pick_winner' /><input type='hidden' name='custom_nonce' value='".wp_create_nonce('no-frills-select-winner-nonce')."' /><input type='hidden' name='uid' value='".$ent->id."'><input type='submit' value='".__("Select as Winner","no-frills-prize-draw")."' class='button' /></form>"; 
                            }     
                        }else{
                            if($realanswer=="" && $winner==0){
                               if($num_entrees>0) echo "<form action='' method='get' onsubmit=''><input type='hidden' name='page' value='nfpd_pick_winner' /><input type='hidden' name='custom_nonce' value='".wp_create_nonce('no-frills-select-winner-nonce')."' /><input type='hidden' name='uid' value='".$ent->id."'><input type='submit' value='".__("Select as Winner","no-frills-prize-draw")."' class='button' /></form>"; 
                            }else{
                                echo "<i>".__("None","no-frills-prize-draw")."</i>";
                            }
                        }

                        echo "</td></tr>"; 
                    }
                    ?>
                    </table>
                    <?php 

                    //paginate
                    $pages = "";
                    if($total_results>$perpage){
                        list($pages) = nfpd_prize_draw_custom_pagination($pageurl,$totalpages,$perpage,$page,$_GET['email'],$_GET['filter'],'nfpd_prize_draw_viewy_draw_entries'); //$_GET['email']
                    }

                    echo "<div class='no-frills-pagination'>".$pages."<p>".__("Total Number of Entries","no-frills-prize-draw").": ".$total_results."</p></div>";
                    echo "<form action = '' target='_blank' method='get' style='width:100%;margin:auto;text-align:center;'><input type='hidden' name='page' value='nfpd_prize_draw_export' /><input type='button' class='button' value='".__("Go Back","no-frills-prize-draw")."' onclick='document.location.href=\"admin.php?page=nfpd_prize_draw\"'> <input type='submit' value='".__("Download CSV file","no-frills-prize-draw")."' class='button button-primary' /> </form>";
                    ?>
                    <br><br><small style='text-align:left;'>** Only the winner's email is visible. This is for the user's protection. Please adhere to all privacy policy laws and do not miss-use the user's data for anything other than that which is agreed upon within your sites own privacy policy and terms.</small><br>
        
                    <script>
                    function confdel(){ if(confirm("<?php echo __("You are about to delete a user from this draw","no-frills-prize-draw"); ?>.")){return true;}else{return false;}}
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
                    <?php

        

    }

?>
             
             <div class='question'>
                <div class='questionmark'></div><div class='questiontext'>
                    <strong><?php echo __("How do I display prize draws on my website?","no-frills-prize-draw");?></strong><br>
                    <?php echo __("Prize draws can be added anywhere on your website via the use of <i>Shortcodes</i>. <br>Simply click the \"<a href='/wp-admin/admin.php?page=nfpd_shortcode_generator'>Shortcodes</a>\" link, then copy and paste the code directly in to a new page or post","no-frills-prize-draw");?>.
            </div></div>