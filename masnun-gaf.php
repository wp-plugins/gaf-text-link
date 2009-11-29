<?php
/*
Plugin Name: GAF Text Links
Plugin URI: http://masnun.com/?p=708
Description: This plugin lets you display related GAF projects below every posts.
Author: Abu Ashraf Masnun
Version: 1.1
Author URI: http://masnun.com/
*/

/*  Copyright 2009  Abu Ashraf Masnun  (email : masnun@gmail.com )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


add_filter("the_content","masnun_gaf");
add_action("admin_menu","masnun_gaf_add_to_admin");

function masnun_gaf_add_to_admin() {
    add_options_page('GAF Text Link Options', 'GAF Text Link Settings', 'administrator', 'masnun-gaf', 'masnun_gaf_options');
}

function masnun_gaf_options() {
    echo "<h2>GAF Text Link Settings</h2><br>";
    if(!empty($_POST['username'])) {
        update_option("masnun_gaf_username",$_POST['username']);
        echo "<div class=\"updated\"><b>Options Updated!</b></div><br><br><br>";
    }

    $username = get_option("masnun_gaf_username");
    echo "<div class=\"wrap\"><form action=\"\" method=\"post\">";
    echo "<b>Your GAF Username: </b><input type=\"text\" name=\"username\" value=\"{$username}\">";
    echo " <input type=\"submit\" value=\"Update\">";
    echo "</form><br><br>";
    echo "<a href=\"http://www.getafreelancer.com/affiliates/masnun/\" target=\"_blank\">Get an account!</a><br><br>";
    echo "</div>";

}


function getGafAffLink($keyword) {
    $contents = file_get_contents("http://api.getafreelancer.com/Project/Search.json?keyword={$keyword}");
    $username = get_option("masnun_gaf_username");
    if(empty($username)) { $username = "masnun"; }
    $GAF = json_decode($contents,true);
    //foreach($GAF['projects']['items'] as $k => $v) { echo $k."-".$v."\n";}
    $retval = "<br><br><b>Related Free Lance Projects on GAF:</b><br>";
    if( count($GAF['projects']['items']) > 0) {
        $retval .= "<ul>";
        $count = $GAF['projects']['items'];
        if($count > 10) { $count = 10; }
        for($i=0;$i < $count; $i++) {
            $id = $GAF['projects']['items'][$i]['id'];
            $name = $GAF['projects']['items'][$i]['name'];
            $retval .= "<li><a href=\"http://www.getafreelancer.com/projects/{$username}_{$id}.html\" target=\"_blank\">{$name}</a></li>";
        }
        $retval .= "</ul>";
    } else {
        $retval .= "No related projects found on GAF :(";

    }

    return $retval;
}

function masnun_gaf($content) {
    global $post;
    $context = join(",",explode(" ",$post->post_title));
    $GAF_Links = getGafAffLink($context);
    $content .= "<br><br>".$GAF_Links."<br><br>";
    return $content;

}
?>
