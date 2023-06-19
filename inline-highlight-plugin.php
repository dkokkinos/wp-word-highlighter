<?php

/*

Plugin Name: Inline highlight Words in a Blog Post
Plugin URI: https://softwareparticles.com
Description: Highlights words in a blog post.
Version: 1.0.0
Author: Dimitris Kokkinos
Author URI: https://softwareparticles.com
License: GPL-2.0+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

function word_highlighter()
{
    wp_enqueue_script('inline-code-plugin', plugin_dir_url(__FILE__) . 'inline-code-plugin.js', array(), '1.0', true);
}

add_action('wp_enqueue_scripts', 'word_highlighter', 9999);
