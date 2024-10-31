<?php
if ( ! current_user_can( 'edit_posts' ) ) {
    echo "<div class='error'>".__("You do not have the correct privileges to access this page","no-frills-prize-draw")."</div>"; 
    die();
}
?>
<div class="wrap left"><h2 style='margin-bottom:20px;'><?php echo __("Shortcodes","no-frills-prize-draw");?></h2> 
    
    <p><?php echo __("The front-end of the prize draw is handled by a simple shortcode that can be added to any page or post using the <a href=\"https://codex.wordpress.org/Shortcode_API\" target=\"_blank\">standard shortcode</a> method, or via the <a href=\"https://developer.wordpress.org/reference/functions/do_shortcode/\" target=\"_blank\">PHP 'do_shortcode'</a> method","no-frills-prize-draw");?>.</p>

   <p><?php echo __("Copy and paste the shortcode below in to any page or post","no-frills-prize-draw");?>.</p>
    <div id='entry-shortcode'style="border:dashed 1px #ccc; background-color: #fff;margin:20px 0 20px 0;padding:5px 10px;width:70%;min-height:5px;">[nfpd_entry_page]</div> 
    

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