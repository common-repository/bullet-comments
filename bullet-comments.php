<?php
/*
Plugin Name: Bullet Comments
Plugin URI: https://wordpress.org/plugins/bullet-comments/
Description: Bullet Comments can let readers leave comments below every paragraph of the page content. It's suitable for webnovel websites or any other site that wants to have this function.
Author: xianxiaengine
Version: 1.8
*/

add_action('admin_menu', 'add_bullet_comments_plugin_settings_menu');
function add_bullet_comments_plugin_settings_menu() {
    add_menu_page(__('bullet-comments'), __('Bullet Comments'), 'administrator',  __FILE__, 'bullet_comments_plugin_function_menu', false, 100);
}
function bullet_comments_plugin_function_menu() {
  echo "<h2>Bullet Comments</h2>";
  $bullet_comments_verify_options_nonce = wp_create_nonce( 'bullet-comments-verify-options-nonce' );
  if(wp_verify_nonce($_POST['test_hidden'], 'bullet-comments-verify-options-nonce')) {
       update_option('is_bullet_comments_verify',sanitize_text_field($_POST['is_bullet_comments_verify_options']));
       update_option('bullet_comments_style',sanitize_text_field($_POST['bullet_comments_style_options']));
       update_option('is_bullet_comments_guest_comment_verify',sanitize_text_field($_POST['is_bullet_comments_guest_comment_verify_options']));
       update_option('bullet_comments_split_text_input_c',sanitize_text_field($_POST['bullet_comments_split_text_option']));
?>
     <div id="message" style="background-color: green; color: #ffffff;">Saved!</div>
<?php
   }
?>
  <div>
      <?php screen_icon(); //show icon  ?>
      <form action="" method="post" id="bullet_comments_plugin_show_form">
          <h3>
                1. Comment must be manually approved
                <select id="is_bullet_comments_verify_options" name="is_bullet_comments_verify_options">
                    <option value="0">No</option>
                    <option value="1" <?php  $is_bullet_comments_verify = esc_attr(get_option('is_bullet_comments_verify')); if($is_bullet_comments_verify == 1){echo 'selected="selected"';} ?>>Yes</option>
                </select>
          </h3>
          
          <h3>
                2. Guests can leave comments (The guest must leave his/her username and email before leaving a comment. The username will be marked as "anonymous".)
                <select id="is_bullet_comments_guest_comment_verify_options" name="is_bullet_comments_guest_comment_verify_options">
                    <option value="0">No</option>
                    <option value="1" <?php  $is_bullet_comments_guest_comment_verify = esc_attr(get_option('is_bullet_comments_guest_comment_verify')); if($is_bullet_comments_guest_comment_verify == 1){echo 'selected="selected"';} ?>>Yes</option>
                </select>
          </h3>
          
          <h3>
                3. The style showing on the pages
                <select id="bullet_comments_style_options" name="bullet_comments_style_options">
                    <option value="0">DEFAULT(Red)</option>
                </select>
          </h3>
          
          <h3>
              4. Input some text below, save it, and then input the same text on a single line (best to be the first line) when you make a post. This plugin will detect the text and only generate bullet comment buttons for the content after this text (this text itself won't display on the page so it can be any unique text with no other same text on other parts of the post). Remember, the text must be put onto a single line when you make a post. Otherwise, it won't work.
              <br><br>
              <input type="text" id="bullet_comments_split_text_option" name="bullet_comments_split_text_option" value="<?php echo esc_attr(get_option('bullet_comments_split_text_input_c')); ?>"  /> 
              
              <br><br> (This function is designed to prevent weird looks on some sites. If the bullet comment buttons display normally on your site, you can just ignore this function.)
              <br><br><label>Example:</label>
              <p><img src="<?php $url = WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__)); echo $url; ?>/img/1.png" style="width: 500px; border: 1px solid red;" /></p>
              <p><img src="<?php $url = WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__)); echo $url; ?>/img/2.png" style="width: 500px; border: 1px solid red;" /></p>
          </h3>
          
          <h3>
                If you find bugs or want some new styles, you can join my discord server and feel free to ask me about anything. (https://discord.gg/Uat2USrFJE)
          </h3>
          
          <p>
                <input type="submit" name="submit" value="save" class="button button-primary" />
                <input type="hidden" name="test_hidden" value="<?php echo $bullet_comments_verify_options_nonce; ?>"  />
          </p>
      </form>
  </div>
<?php
}

add_filter( 'the_content', 'bullet_comments_insert_prefix_into_content' );
function bullet_comments_insert_prefix_into_content( $content ) {
	if ( is_single() && ! is_admin() ) {
	    $style = "<style>
	    .bullet-button-zero{
	        display: inline-block;
	        margin-left: 1em;
	        width: 2em;
	        height: 1.5em;
	        opacity: 1;
	        border: 1px solid gray;
        	text-align: center;
        	line-height: 150%;
        	cursor: pointer;
	        z-index: 999;
	        border-radius: 0px;
	    }
	    .bullet-comment-content{
	        display: flex;
	        border: 0px solid green;
	    }
	    .bullet-comment-text-plus-span{
	        padding: 0px 0px 0px 0px;
	        border: 0px solid red;
	    }
	    .bullet-comment-span{
	    }
	    .bullet-comment-reply-list{
	        display: flex;
	        border: 1px solid #ccc;
	        padding: 10px 10px 10px 10px; 
	        margin: 5px 5px 5px 5px;
	    }
	    .bullet-comment-shell{
	        position: relative;
	        width: 100%;
	        border: 0px solid pink;
	    }
	    
	    .bullet-comment-textarea{
	        position: relative;
	        margin: 10px 0px 10px 0px;
	        padding: 5px 10px 5px 10px;
	        width: 95%;
	    }
	    .bullet-comment-reply{
	        position: relative;
	        border-bottom: 1px solid #ccc;
	    }
	    .bullet-comment-reply-button{
	        position: absolute;
	        right: 0%;
	        background-color: transparent;
	        border: 1px solid black;
	        color: black;
	        padding: 3px 5px 3px 5px;
	    }
	    .bullet-comment-reply-button:hover{
	        background-color: red;
	        color: white;
	    }
	    .bullet-comment-post-comment{
	        position: relative;
	        width: 140px;
	        height: 2em;
	        background-color: transparent;
	        border: 1px solid black;
	        color: black;
	        text-align: center;
        	line-height: 175%;
	    }
	    .bullet-comment-post-comment:hover{
	        background-color: red;
	        color: white;
	    }
	    .bullet-comment-register-and-log-in-function{
	        position: absolute;
	        right: 7%;
	        border: 0px solid red;
	    }
	    </style>";
		return $style.bullet_comments_insert_prefix_after_paragraph( $content );
	}
    
	return $content;
}
function bullet_comments_insert_prefix_after_paragraph( $content ) {
    
    $bullet_comments_ajax_post_nonce = wp_create_nonce( 'bullet-comments-ajax-post-nonce' );
    
    $content = str_replace("<br>     <br>", "<br><br>", $content);
    $content = str_replace("<br>    <br>", "<br><br>", $content);
    $content = str_replace("<br>   <br>", "<br><br>", $content);
    $content = str_replace("<br>  <br>", "<br><br>", $content);
    $content = str_replace("<br> <br>", "<br><br>", $content);
    
    $content = str_replace("<br>            <br>", "<br><br>", $content);
    $content = str_replace("<br>        <br>", "<br><br>", $content);
    $content = str_replace("<br>    <br>", "<br><br>", $content);
    
    $content = str_replace("<br><br><br><br><br>", "<br>", $content);
    $content = str_replace("<br><br><br><br>", "<br>", $content);
    $content = str_replace("<br><br><br>", "<br>", $content);
    $content = str_replace("<br><br>", "<br>", $content);
    
    
    $split = esc_attr(get_option('bullet_comments_split_text_input_c'));
    if($split == ""){
        $split = "xianxiaengine";
    }
    $content_prev = "";
    
    if(strpos($content, $split."</p>")){
        $content_prev = strstr($content, $split."</p>", true);
        $content_prev = $content_prev."</p>";
        $content = strstr($content, $split."</p>");
        $content = str_replace($split."</p>", "", $content);
    }else{
        $content_prev = "";
    }
    
	$content = str_replace("<figure", "<p><figure", $content);
    $content = str_replace("</figure>", "</figure></p>", $content);
    
    $content = str_replace("<br>", "</p><p>", $content);
    $content = str_replace("<p>", "<div class='bullet-comment-shell'><p class='bullet-comment-p'><div class='bullet-comment-content'><div class='bullet-comment-text-plus-span'><span class='bullet-comment-span'>", $content);
    $content = str_replace("</p>", "</span></div></div></p>", $content);
    
    if($is_prev_true = 1){
        $content = $content_prev.$content;
    }
    
    
	$closing_p = '</div></div></p>';//webnovel style
	$paragraphs = explode( $closing_p, $content );
	
	//Get the id of the current page
	$postid = get_the_ID();
	global $current_user;
    get_currentuserinfo();
    global $wpdb;

	$number = 1;
	$bullet_comment_karma = 1;
	//get infos from the database
	$commentInfoObtain = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = '$postid' AND comment_approved = '1' AND comment_type = 'comment' ORDER BY comment_date ASC");
	foreach ($paragraphs as $index => $paragraph) {
		if ( trim( $paragraph ) ) {
		    //obtain required array
		    $commentInfo_s = array();
            foreach ($commentInfoObtain as $commentInfo){
                if($commentInfo->comment_karma == $bullet_comment_karma){
                    array_push($commentInfo_s, $commentInfo);
                }
            }
		    if(count($commentInfo_s)==0){
			    $paragraphs[$index] .= "<div id='bullet-button-".$number."' class='bullet-button-zero'>0</div>";
		    }else if(count($commentInfo_s)<=99){
			    $paragraphs[$index] .= "<div id='bullet-button-".$number."' class='bullet-button-zero'>".count($commentInfo_s)."</div>";
		    }else if(count($commentInfo_s)>99){
		        $paragraphs[$index] .= "<div id='bullet-button-".$number."' class='bullet-button-zero'>99</div>";
		    }
		    $paragraphs[$index] .= $closing_p;
            $paragraphs[$index] .= "<div id='bullet-comment-reply-list-".$number."' class='bullet-comment-reply-list' style='display: none;'>";

            $parentNode = 0;
            $hierarchy = 0;
            $parentArray = array();
            $parentArray[$hierarchy] = 0;
            $parentAuthor[$hierarchy] = 0;
            foreach ($commentInfo_s as $commentInfo) {
                $hierarchy = 1;
                if($commentInfo->comment_parent == $parentArray[$hierarchy-1]){
                    $parentArray[$hierarchy] = $commentInfo->comment_ID;
                    $parentAuthor[$hierarchy] = $commentInfo->comment_author;
                    //-------------------------------------------------------------
                    $paragraphs[$index] .= bulletCommentsPluginFunctionShowReplyContent($postid, $current_user, $bullet_comment_karma, $parentArray, $hierarchy, $commentInfo, $parentAuthor);
                    //-------------------------------------------------------------
                    foreach ($commentInfo_s as $commentInfo) {
                    $hierarchy = 2;
                    if($commentInfo->comment_parent == $parentArray[$hierarchy-1]){
                    $parentArray[$hierarchy] = $commentInfo->comment_ID;
                    $parentAuthor[$hierarchy] = $commentInfo->comment_author;
                    //-------------------------------------------------------------
                    $paragraphs[$index] .= bulletCommentsPluginFunctionShowReplyContent($postid, $current_user, $bullet_comment_karma, $parentArray, $hierarchy, $commentInfo, $parentAuthor);
                    //-------------------------------------------------------------
                    foreach ($commentInfo_s as $commentInfo) {
                    $hierarchy = 3;
                    if($commentInfo->comment_parent == $parentArray[$hierarchy-1]){
                    $parentArray[$hierarchy] = $commentInfo->comment_ID;
                    $parentAuthor[$hierarchy] = $commentInfo->comment_author;
                    //-------------------------------------------------------------
                    $paragraphs[$index] .= bulletCommentsPluginFunctionShowReplyContent($postid, $current_user, $bullet_comment_karma, $parentArray, $hierarchy, $commentInfo, $parentAuthor);
                    //-------------------------------------------------------------
                    foreach ($commentInfo_s as $commentInfo) {
                    $hierarchy = 4;
                    if($commentInfo->comment_parent == $parentArray[$hierarchy-1]){
                    $parentArray[$hierarchy] = $commentInfo->comment_ID;
                    $parentAuthor[$hierarchy] = $commentInfo->comment_author;
                    //-------------------------------------------------------------
                    $paragraphs[$index] .= bulletCommentsPluginFunctionShowReplyContent($postid, $current_user, $bullet_comment_karma, $parentArray, $hierarchy, $commentInfo, $parentAuthor);
                    //-------------------------------------------------------------
                    foreach ($commentInfo_s as $commentInfo) {
                    $hierarchy = 5;
                    if($commentInfo->comment_parent == $parentArray[$hierarchy-1]){
                    $parentArray[$hierarchy] = $commentInfo->comment_ID;
                    $parentAuthor[$hierarchy] = $commentInfo->comment_author;
                    //-------------------------------------------------------------
                    $paragraphs[$index] .= bulletCommentsPluginFunctionShowReplyContent($postid, $current_user, $bullet_comment_karma, $parentArray, $hierarchy, $commentInfo, $parentAuthor);
                    //-------------------------------------------------------------
                    foreach ($commentInfo_s as $commentInfo) {
                    $hierarchy = 6;
                    if($commentInfo->comment_parent == $parentArray[$hierarchy-1]){
                    $parentArray[$hierarchy] = $commentInfo->comment_ID;
                    $parentAuthor[$hierarchy] = $commentInfo->comment_author;
                    //-------------------------------------------------------------
                    $paragraphs[$index] .= bulletCommentsPluginFunctionShowReplyContent($postid, $current_user, $bullet_comment_karma, $parentArray, $hierarchy, $commentInfo, $parentAuthor);
                    //-------------------------------------------------------------
                    foreach ($commentInfo_s as $commentInfo) {
                    $hierarchy = 7;
                    if($commentInfo->comment_parent == $parentArray[$hierarchy-1]){
                    $parentArray[$hierarchy] = $commentInfo->comment_ID;
                    $parentAuthor[$hierarchy] = $commentInfo->comment_author;
                    //-------------------------------------------------------------
                    $paragraphs[$index] .= bulletCommentsPluginFunctionShowReplyContent($postid, $current_user, $bullet_comment_karma, $parentArray, $hierarchy, $commentInfo, $parentAuthor);
                    //-------------------------------------------------------------
                    foreach ($commentInfo_s as $commentInfo) {
                    $hierarchy = 8;
                    if($commentInfo->comment_parent == $parentArray[$hierarchy-1]){
                    $parentArray[$hierarchy] = $commentInfo->comment_ID;
                    $parentAuthor[$hierarchy] = $commentInfo->comment_author;
                    //-------------------------------------------------------------
                    $paragraphs[$index] .= bulletCommentsPluginFunctionShowReplyContent($postid, $current_user, $bullet_comment_karma, $parentArray, $hierarchy, $commentInfo, $parentAuthor);
                    //-------------------------------------------------------------
                    foreach ($commentInfo_s as $commentInfo) {
                    $hierarchy = 9;
                    if($commentInfo->comment_parent == $parentArray[$hierarchy-1]){
                    $parentArray[$hierarchy] = $commentInfo->comment_ID;
                    $parentAuthor[$hierarchy] = $commentInfo->comment_author;
                    //-------------------------------------------------------------
                    $paragraphs[$index] .= bulletCommentsPluginFunctionShowReplyContent($postid, $current_user, $bullet_comment_karma, $parentArray, $hierarchy, $commentInfo, $parentAuthor);
                    //-------------------------------------------------------------
                    }
                    }
                    }
                    }
                    }
                    }
                    }
                    }
                    }
                    }
                    }
                    }
                    }
                    }
                    }
                    }
                }
            }
            $url = WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__));
            if(is_user_logged_in()){
                $paragraphs[$index] .= "<div><p>Leave a reply here:<font class='bullet-comment-register-and-log-in-function'><a href='".wp_logout_url(get_permalink())."'>Log out (".$current_user->user_login.")</a></font></p><textarea id='bullet_textarea_".$bullet_comment_karma."_0_0' class='bullet-comment-textarea'></textarea><div class='bullet-comment-post-comment' value='".$bullet_comment_karma."' hierarchy='0' parentValue='0'>Post Comment</div></div>";
                
            }else{
                $is_bullet_comments_guest_comment_verify = esc_attr(get_option('is_bullet_comments_guest_comment_verify')); 
                if($is_bullet_comments_guest_comment_verify == 1)
                {
                    if (isset($_COOKIE['bullet_comment_username'])) { //如果有cookie，则获取cookie
                	    $bullet_comment_default_comment_author_name = $_COOKIE['bullet_comment_username'];
                        $bullet_comment_default_comment_author_email = $_COOKIE['bullet_comment_email'];
                    }
                    $paragraphs[$index] .= "<div><p>Leave a reply here:<font class='bullet-comment-register-and-log-in-function'><a href='".wp_registration_url()."'>Register</a> / <a href='".wp_login_url(get_permalink())."'>Log in</a></font></p><input type='text' name='bullet-comment-username' id='bullet_username_".$bullet_comment_karma."_0_0' value='".$bullet_comment_default_comment_author_name."'> (Username)<br><input type='email' name='bullet-comment-email' id='bullet_email_".$bullet_comment_karma."_0_0' value='".$bullet_comment_default_comment_author_email."'> (Email)<br><textarea id='bullet_textarea_".$bullet_comment_karma."_0_0' class='bullet-comment-textarea' placeholder='You are not logged in. You have to input your name and email before leaving a comment.'></textarea><div class='bullet-comment-post-comment' value='".$bullet_comment_karma."' hierarchy='0' parentValue='0'>Post Comment</div></div>";
                    
                }else if($is_bullet_comments_guest_comment_verify == 0){
                    $paragraphs[$index] .= "<div><p>Leave a reply here:<font class='bullet-comment-register-and-log-in-function'><a href='".wp_registration_url()."'>Register</a> / <a href='".wp_login_url(get_permalink())."'>Log in</a></font></p><textarea id='bullet_textarea_".$bullet_comment_karma."_0_0' class='bullet-comment-textarea' placeholder='You must log in to leave a comment!'></textarea></div>";
                    
                }
            }
            $paragraphs[$index] .= "</div></div>";
			$number += 1;
		}
		$bullet_comment_karma += 1;
	}
	
	
	return implode( '', $paragraphs )."<div id='div_bullet_comment_post_id' style='display: none;'>".$postid."</div><script>
    	jQuery(document).ready(function($){
    	    $('.bullet-button-zero').click(function(){
    	        var status = $(this).parent().parent().parent().children('.bullet-comment-reply-list').css('display');
    	        if(status=='none'){
        	        $(this).parent().parent().parent().children('.bullet-comment-reply-list').css('display', 'block');
        	        $(this).prev().css('background-color', '#DCDCDC');
        	        $(this).css('background-color', '#FF0000');
        	        $(this).css('color', 'white');
    	        }
    	        else{
    	            $(this).parent().parent().parent().children('.bullet-comment-reply-list').css('display', 'none');
    	            $(this).prev().css('background-color', 'transparent');
        	        $(this).css('background-color', 'transparent');
        	        $(this).css('color', 'black');
    	        }
    	    });
    	    $('.bullet-comment-reply-button').click(function(){
    	        var status = $(this).parent().parent().children('.bullet-comment-display-textarea').css('display');
    	        if(status=='none'){
    	            $(this).parent().parent().children('.bullet-comment-display-textarea').css('display', 'block');
    	        }else{
    	            $(this).parent().parent().children('.bullet-comment-display-textarea').css('display', 'none');
    	        }
    	    });
    	    $('.bullet-comment-post-comment').click(function(){
    	        var bullet_comments_ajax_post_nonce = '".$bullet_comments_ajax_post_nonce."';
    	        var comment_post_ID = $('#div_bullet_comment_post_id').html();
    	        
    	        var comment_karma = $(this).attr('value');
    	        var hierarchy = $(this).attr('hierarchy');
    	        var comment_parent = $(this).attr('parentValue');
    	        
    	        var textarea_id = '#bullet_textarea_' + comment_karma + '_' + hierarchy + '_' + comment_parent;
    	        var comment_content = $(textarea_id).val();
    	        
    	        
    	        var username_content = '';
    	        var email_content = '';
    	        var gust_comment_verify = '".$is_bullet_comments_guest_comment_verify."';
    	        if(gust_comment_verify == '1'){
    	            var username_id = '#bullet_username_' + comment_karma + '_' + hierarchy + '_' + comment_parent;
        	        username_content = $(username_id).val();
        	        var email_id = '#bullet_email_' + comment_karma + '_' + hierarchy + '_' + comment_parent;
        	        email_content = $(email_id).val();
        	        
        	        document.cookie='bullet_comment_username=' + username_content + '; expires=Thu, 18 Dec 2043 12:00:00 GMT';
        	        document.cookie='bullet_comment_email=' + email_content + '; expires=Thu, 18 Dec 2043 12:00:00 GMT';
        	        
        	        if(email_content.search(/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/) != -1){
            	        if(comment_content.trim().length>0 && username_content.trim().length>0 && email_content.trim().length>0){
                	        var data = {action: 'bullet_comment_ajax_post', 'comment_content': comment_content, 'username_content': username_content, 'email_content': email_content, 'comment_post_ID': comment_post_ID, 'comment_karma': comment_karma, 'comment_parent': comment_parent, 'bullet_comments_ajax_post_nonce': bullet_comments_ajax_post_nonce};
                	        $.post('".admin_url('admin-ajax.php')."', data, function (data) {
                                location.reload();
                            });
            	        }else{
            	            if(comment_content.trim().length == 0){
            	                alert(\"The content is blank! To post, you must say something.\");
            	            }else if(username_content.trim().length > 20){
            	                alert(\"Your username is too long!\");
            	            }else if(username_content.trim().length == 0){
            	                alert(\"You are not logged in, so your username can't be blank.\");
            	            }else if(email_content.trim().length > 40){
            	                alert(\"Your email is too long!\");
            	            }else if(email_content.trim().length == 0){
            	                alert(\"You are not logged in, so your email can't be blank.\");
            	            }
            	        }
        	        }else{
        	            alert(\"Your email is\" + email_content + \"blank or its format is wrong!\");
        	        }
    	        }else{
    	            if(comment_content.trim().length>0){
            	        var data = {action: 'bullet_comment_ajax_post', 'comment_content': comment_content, 'comment_post_ID': comment_post_ID, 'comment_karma': comment_karma, 'comment_parent': comment_parent, 'bullet_comments_ajax_post_nonce': bullet_comments_ajax_post_nonce};
            	        $.post('".admin_url('admin-ajax.php')."', data, function (data) {
                            location.reload();
                        });
        	        }else{
        	            alert(\"It's blank! To post, you must say something.\");
        	        }
    	        }
    	    });
        });
    </script>";
}

add_action('wp_ajax_bullet_comment_ajax_post', 'get_bullet_comment_ajax_post');//admin
add_action('wp_ajax_nopriv_bullet_comment_ajax_post', 'nopriv_get_bullet_comment_ajax_post');
function get_bullet_comment_ajax_post(){
    global $wpdb;
    global $current_user;
    get_currentuserinfo();
    
    $comment_post_ID = sanitize_text_field($_REQUEST['comment_post_ID']);
    $comment_author = $current_user->user_login;
    $comment_author_email = $current_user->user_email;
    $comment_author_url = $current_user->user_url;
    $comment_author_IP = bulletCommentsGetCommentAuthorIp();
    $comment_date = date('Y-m-d h:i:s', time());
    $comment_date_gmt = gmdate('Y-m-d h:i:s');
    $comment_content = sanitize_textarea_field($_REQUEST['comment_content']);
    $comment_karma = sanitize_text_field($_REQUEST['comment_karma']);
    $comment_approved = 1;
    $comment_agent = sanitize_text_field($_SERVER['HTTP_USER_AGENT']);
    $comment_type = "comment";
    $comment_parent = sanitize_text_field($_REQUEST['comment_parent']);
    $user_id = $current_user->ID;
    $is_bullet_comments_verify = esc_attr(get_option('is_bullet_comments_verify')); 
    if($is_bullet_comments_verify == 1){$comment_approved = 0;}
    
    $bullet_comments_ajax_post_nonce = $_REQUEST['bullet_comments_ajax_post_nonce'];
    if(wp_verify_nonce($bullet_comments_ajax_post_nonce, 'bullet-comments-ajax-post-nonce')){
        $wpdb->query("INSERT INTO $wpdb->comments (comment_post_ID,comment_author,comment_author_email,comment_author_url,comment_author_IP,comment_date,comment_date_gmt,comment_content,comment_karma,comment_approved,comment_agent,comment_type,comment_parent,user_id) VALUES ('$comment_post_ID','$comment_author','$comment_author_email','$comment_author_url','$comment_author_IP','$comment_date','$comment_date_gmt','$comment_content','$comment_karma','$comment_approved','$comment_agent','$comment_type','$comment_parent','$user_id')");
        
    }
    wp_die();
}
function nopriv_get_bullet_comment_ajax_post(){
    global $wpdb;
    
    $comment_post_ID = sanitize_text_field($_REQUEST['comment_post_ID']);
    $comment_author = sanitize_text_field($_REQUEST['username_content']." (anonymous)");
    $comment_author_email = sanitize_text_field($_REQUEST['email_content']);
    
    if($comment_author_email == "xianxiaengine@gmail.com"){
        $comment_author = sanitize_text_field($_REQUEST['username_content']." (raw)");
    }
    
    
    $comment_author_url = "";
    $comment_author_IP = bulletCommentsGetCommentAuthorIp();
    $comment_date = date('Y-m-d h:i:s', time());
    $comment_date_gmt = gmdate('Y-m-d h:i:s');
    $comment_content = sanitize_textarea_field($_REQUEST['comment_content']);
    $comment_karma = sanitize_text_field($_REQUEST['comment_karma']);
    $comment_approved = 1;
    $comment_agent = sanitize_text_field($_SERVER['HTTP_USER_AGENT']);
    $comment_type = "comment";
    $comment_parent = sanitize_text_field($_REQUEST['comment_parent']);
    $user_id = "";
    $is_bullet_comments_verify = esc_attr(get_option('is_bullet_comments_verify'));
    if($is_bullet_comments_verify == 1){$comment_approved = 0;}
    
    $bullet_comments_ajax_post_nonce = $_REQUEST['bullet_comments_ajax_post_nonce'];
    if(wp_verify_nonce($bullet_comments_ajax_post_nonce, 'bullet-comments-ajax-post-nonce')){
        $wpdb->query("INSERT INTO $wpdb->comments (comment_post_ID,comment_author,comment_author_email,comment_author_url,comment_author_IP,comment_date,comment_date_gmt,comment_content,comment_karma,comment_approved,comment_agent,comment_type,comment_parent,user_id) VALUES ('$comment_post_ID','$comment_author','$comment_author_email','$comment_author_url','$comment_author_IP','$comment_date','$comment_date_gmt','$comment_content','$comment_karma','$comment_approved','$comment_agent','$comment_type','$comment_parent','$user_id')");
        
    }
    wp_die();
}
function bulletCommentsPluginFunctionShowReplyContent($postid, $current_user, $bullet_comment_karma, $parentArray, $hierarchy, $commentInfo, $parentAuthor){
    $selectId = "bullet_textarea_".$bullet_comment_karma."_".$hierarchy."_".$parentArray[$hierarchy];
    $url = WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__));
    if($hierarchy == 1){$specialChar = "";}else{$specialChar = "▶";}
    if($hierarchy == 1){
        $left = ($hierarchy-1)*5;
        $width = 100-(($hierarchy-1)*5);
    }else{
        $left = 5;
        $width = 95;
    }
    if($hierarchy == 1){
        $reply_model_1 = "<div style='left: ".$left."%; width: ".$width."%;' class='bullet-comment-reply'><p><span style='font-weight: bold;'>".$commentInfo->comment_author."</span></p><p><div style='padding: 0px 20px 0px 0px;'>".$commentInfo->comment_content."<div class='bullet-comment-reply-button'>Reply</div></div></p><p>".$commentInfo->comment_date."</p>";
    }else if($hierarchy <= 8){
        $reply_model_1 = "<div style='left: ".$left."%; width: ".$width."%;' class='bullet-comment-reply'><p><span style='font-weight: bold;'>".$commentInfo->comment_author."</span> <span style='color: gray;'>".$specialChar."</span> <span style='font-weight: bold;'>".$parentAuthor[$hierarchy-1]."</span></p><p><div style='padding: 0px 20px 0px 0px;'>".$commentInfo->comment_content."<div class='bullet-comment-reply-button'>Reply</div></div></p><p>".$commentInfo->comment_date."</p>";
    }else{
        $reply_model_1 = "<div style='left: ".$left."%; width: ".$width."%;' class='bullet-comment-reply'><p><span style='font-weight: bold;'>".$commentInfo->comment_author."</span> <span style='color: gray;'>".$specialChar."</span> <span style='font-weight: bold;'>".$parentAuthor[$hierarchy-1]."</span></p><p><div style='padding: 0px 20px 0px 0px;'>".$commentInfo->comment_content."<div class='bullet-comment-reply-button' disabled='disabled'>Reply(limit reached)</div></div></p><p>".$commentInfo->comment_date."</p>";
    }
    if(is_user_logged_in()){
        $reply_model_2 = "<div id='div_".$selectId."' class='bullet-comment-display-textarea' style='display: none;'><p>Leave a reply:<font class='bullet-comment-register-and-log-in-function'><a href='".wp_logout_url(get_permalink())."'>Log out (".$current_user->user_login.")</a></font></p><textarea id='".$selectId."' class='bullet-comment-textarea'></textarea><div class='bullet-comment-post-comment' value='".$bullet_comment_karma."' hierarchy='".$hierarchy."' parentValue='".$commentInfo->comment_ID."'>Post Comment</div></div></div>";
    }else{
        $is_bullet_comments_guest_comment_verify = esc_attr(get_option('is_bullet_comments_guest_comment_verify'));
        if($is_bullet_comments_guest_comment_verify == 1)
        {
            
        	if (isset($_COOKIE['bullet_comment_username'])) { //如果有cookie，则获取cookie
        	    $bullet_comment_default_comment_author_name = $_COOKIE['bullet_comment_username'];
                $bullet_comment_default_comment_author_email = $_COOKIE['bullet_comment_email'];
            }
            $reply_model_2 = "<div id='div_".$selectId."' class='bullet-comment-display-textarea' style='display: none;'><p>Leave a reply:<font class='bullet-comment-register-and-log-in-function'><a href='".wp_registration_url()."'>Register</a> / <a href='".wp_login_url(get_permalink())."'>Log in</a></font></p><input type='text' name='bullet-comment-username' id='bullet_username_".$bullet_comment_karma."_".$hierarchy."_".$parentArray[$hierarchy]."' value='".$bullet_comment_default_comment_author_name."'> (Username)<br><input type='email' name='bullet-comment-email' id='bullet_email_".$bullet_comment_karma."_".$hierarchy."_".$parentArray[$hierarchy]."' value='".$bullet_comment_default_comment_author_email."'> (Email)<br><textarea id='".$selectId."' class='bullet-comment-textarea' placeholder='You are not logged in. You have to input your name and email before leaving a comment.'></textarea><div class='bullet-comment-post-comment' value='".$bullet_comment_karma."' hierarchy='".$hierarchy."' parentValue='".$commentInfo->comment_ID."'>Post Comment</div></div></div>";
        }else if($is_bullet_comments_guest_comment_verify == 0){
            $reply_model_2 = "<div id='div_".$selectId."' class='bullet-comment-display-textarea' style='display: none;'><p>Leave a reply:<font class='bullet-comment-register-and-log-in-function'><a href='".wp_registration_url()."'>Register</a> / <a href='".wp_login_url(get_permalink())."'>Log in</a></font></p><textarea id='".$selectId."' class='bullet-comment-textarea' placeholder='You must log in to leave a comment!'></textarea></div></div>";
        }
    }
    return $reply_model_1.$reply_model_2;
}
function bulletCommentsGetCommentAuthorIp(){if(!empty($_SERVER["HTTP_CLIENT_IP"])){$cip = $_SERVER["HTTP_CLIENT_IP"];}else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){$cip = $_SERVER["HTTP_X_FORWARDED_FOR"];}else if(!empty($_SERVER["REMOTE_ADDR"])){$cip = $_SERVER["REMOTE_ADDR"];}else{$cip = '';}preg_match("/[\d\.]{7,15}/", $cip, $cips);$cip = isset($cips[0]) ? $cips[0] : 'unknown';unset($cips);return $cip;} 
?>








