<?php
/*
Plugin Name: The Last Comment
Plugin URI: http://rubensargsyan.com/wordpress-plugin-the-last-comment/
Description: This widget plugin shows the last comment of the Wordpress blog in the sidebar.
Version: 1.0
Author: Ruben Sargsyan
Author URI: http://rubensargsyan.com/
*/

/*  Copyright 2010 Ruben Sargsyan (email: info@rubensargsyan.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, see <http://www.gnu.org/licenses/>.
*/

$the_last_comment_url = WP_PLUGIN_URL.'/'.str_replace(basename(__FILE__),"",plugin_basename(__FILE__));
$the_last_comment_plugin_title = "The Last Comment";
$the_last_comment_plugin_prefix = "the_last_comment_";

function the_last_comment_load(){
    $the_last_comment_plugin_version = "1.0";

    if(get_option($the_last_comment_plugin_prefix."version")===false){
        add_option($the_last_comment_plugin_prefix."version",$the_last_comment_plugin_version);
    }elseif(get_option($the_last_comment_plugin_prefix."version")<$the_last_comment_plugin_version){
        update_option($the_last_comment_plugin_prefix."version",$the_last_comment_plugin_version);
    }
}

class The_Last_Comment_Widget extends WP_Widget{
     function The_Last_Comment_Widget(){
        $widget_opions = array("classname"=>"the_last_comment_widget","description" =>__("The last comment"));
		$this->WP_Widget("the-last-comment", "The Last Comment", $widget_opions);
     }

     function widget($args, $instance){
        extract($args);

        $title = $instance["title"];
        $post_id = $instance["post_id"];
        $show_author = $instance["show_author"];
        $show_avatar = $instance["show_avatar"];
        $show_date = $instance["show_date"];
        $words_count = $instance["words_count"];

        echo($before_widget);

        echo($before_title.$title.$after_title);

        $arguments = "number=1&status=approve";

        if(is_numeric($post_id) && intval($post_id)!=0){
            $arguments .= "&post_id=".$post_id;
        }

        $the_last_comment = get_comments($arguments);
        $the_last_comment = $the_last_comment[0];

        $the_last_comment_content_words = explode(" ",strip_tags($the_last_comment->comment_content));

        if(count($the_last_comment_content_words)>=$words_count){
            $words_to_show = array();
            for($i=0; $i<$words_count; $i++){
                $words_to_show[] = $the_last_comment_content_words[$i];
            }

            $the_last_comment_content = implode(" ",$words_to_show);
            $the_last_comment_content .= "...";
        }else{
            $the_last_comment_content = strip_tags($the_last_comment->comment_content);
        }

        ?>
        <div class="the_last_comment">
            <?php if($show_author=="yes" || $show_avatar=="yes"){ ?><div class="the_last_comment_author"><?php if($show_avatar=="yes"){ echo(get_avatar($the_last_comment->comment_author_email,32)); } ?> <?php if($show_author=="yes"){ echo(get_comment_author_link($the_last_comment->comment_ID)); } ?></div><? } ?>
            <div class="the_last_comment_content"><?php echo($the_last_comment_content); ?> <a href="<?php echo(esc_url(get_comment_link($the_last_comment->comment_ID))); ?>">&raquo;</a></div>
            <?php if($show_date=="yes"){ ?><div class="the_last_comment_date"><?php echo($the_last_comment->comment_date ); ?></div><? } ?>
        </div>
        <?php

        echo($after_widget);
     }

     function update($new_instance, $old_instance){
        $instance = $old_instance;
        if(strip_tags($new_instance["title"])!=""){
            $instance["title"] = strip_tags($new_instance["title"]);
        }
        if(is_numeric($new_instance["post_id"]) && intval($new_instance["post_id"])>=0){
            $instance["post_id"] = intval($new_instance["post_id"]);
        }elseif(is_numeric($old_instance["post_id"]) && intval($old_instance["post_id"])>=0){
            $instance["post_id"] = intval($old_instance["post_id"]);
        }else{
            $instance["post_id"] = 0;
        }
        if($new_instance["show_author"]=="yes"){
            $instance["show_author"] = "yes";
        }else{
            $instance["show_author"] = "no";
        }
        if($new_instance["show_avatar"]=="yes"){
            $instance["show_avatar"] = "yes";
        }else{
            $instance["show_avatar"] = "no";
        }
        if($new_instance["show_date"]=="yes"){
            $instance["show_date"] = "yes";
        }else{
            $instance["show_date"] = "no";
        }
        if(is_numeric($new_instance["words_count"]) && intval($new_instance["words_count"])>0){
            $instance["words_count"] = intval($new_instance["words_count"]);
        }elseif(is_numeric($old_instance["words_count"]) && intval($old_instance["words_count"])>0){
            $instance["words_count"] = intval($old_instance["words_count"]);
        }else{
            $instance["words_count"] = 20;
        }

		return $instance;
     }

     function form($instance){
        $instance = wp_parse_args((array)$instance,array("title"=>"The Last Comment","post_id"=>"0","show_author"=>"yes","show_avatar"=>"","show_date"=>"","words_count"=>"20"));
		$title = $instance["title"];
        $post_id = $instance["post_id"];
        $show_author = $instance["show_author"];
        $show_avatar = $instance["show_avatar"];
        $show_date = $instance["show_date"];
        $words_count = $instance["words_count"];
        ?>
        <p><label for="<?php echo($this->get_field_id("title")); ?>">Title:</label>
        <input class="widefat" id="<?php echo($this->get_field_id("title")); ?>" name="<?php echo($this->get_field_name("title")); ?>" type="text" value="<?php echo(esc_attr($title)); ?>" /></p>
        <p><label for="<?php echo($this->get_field_id("post_id")); ?>">Post ID:</label>
        <input id="<?php echo($this->get_field_id("post_id")); ?>" name="<?php echo($this->get_field_name("post_id")); ?>" type="text" value="<?php echo(esc_attr($post_id)); ?>" size="3" />
        <small>Set 0 to show the last comment of all posts and pages.</small></p>
        <p><label for="<?php echo($this->get_field_id("show_author")); ?>">Show Author:</label>
        <input id="<?php echo($this->get_field_id("show_author")); ?>" name="<?php echo($this->get_field_name("show_author")); ?>" type="checkbox" value="yes" <?php if($show_author=="yes"){ echo('checked="checked"'); } ?> /></p>
        <p><label for="<?php echo($this->get_field_id("show_avatar")); ?>">Show Avatar:</label>
        <input id="<?php echo($this->get_field_id("show_avatar")); ?>" name="<?php echo($this->get_field_name("show_avatar")); ?>" type="checkbox" value="yes" <?php if($show_avatar=="yes"){ echo('checked="checked"'); } ?> /></p>
        <p><label for="<?php echo($this->get_field_id("show_date")); ?>">Show Date:</label>
        <input id="<?php echo($this->get_field_id("show_date")); ?>" name="<?php echo($this->get_field_name("show_date")); ?>" type="checkbox" value="yes" <?php if($show_date=="yes"){ echo('checked="checked"'); } ?> /></p>
        <p><label for="<?php echo($this->get_field_id("words_count")); ?>">Words Count:</label>
        <input id="<?php echo($this->get_field_id("words_count")); ?>" name="<?php echo($this->get_field_name("words_count")); ?>" type="text" value="<?php echo(esc_attr($words_count)); ?>" size="3" />
        <small>Set maximum count of words of the last comment which will be shown.</small></p>
     <?php
     }
}

function the_last_comment_widget_init(){
	if(!is_blog_installed()){
	    return;
	}

    register_widget('the_last_comment_Widget');

	do_action('widgets_init');
}

add_action('init', 'the_last_comment_widget_init', 1);
add_action('plugins_loaded','the_last_comment_load');
?>