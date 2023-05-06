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

function word_highlighter() {
    wp_enqueue_script( 'inline-code-plugin', plugin_dir_url( __FILE__ ) . 'inline-code-plugin.js', array(), '1.0', true );
}

add_action( 'wp_enqueue_scripts', 'word_highlighter', 9999 );




$words_to_replace = array(
    'composite pattern' => 'https://softwareparticles/design-patterns-composite',
    
);

// Define the callback function to replace the words
function highlight_code_tokens($content) {
    global $words_to_replace;

    // Check if the current post is not the same as the link URL
    $current_url = get_permalink(); // Get the URL of the current post
    $current_url_host = parse_url($current_url, PHP_URL_HOST); // Get the host name of the current URL

    // Loop through the words to replace
    foreach ($words_to_replace as $word => $link) {

        $regex = '/(?<!<span class="sp-important-word">)\b(' . preg_quote($word) . ')\b(?!<\/span>)/i'; // Create a regex pattern to match the word with word boundary and exclude the word already inside a span with class "sp-important-word"

        if (!empty($link)) {

            $link_host = parse_url($link, PHP_URL_HOST); // Get the host name of the link URL

            if ($current_url_host != $link_host) {
                // Replace the word with a span element with class "sp-important-word" and link
                $content = preg_replace($regex, '<span class="sp-important-word"><a href="' . esc_url($link) . '">$1</a></span>', $content);
            } else {

                // Replace the word with a span element with class "sp-important-word" without link
                $content = preg_replace($regex, '<span class="sp-important-word">$1</span>', $content);
            }
        } else {

            // Replace the word with a span element with class "sp-important-word" without link
            $content = preg_replace($regex, '<span class="sp-important-word">$1</span>', $content);
        }
    }

    return $content;
}

add_filter( 'the_content', 'highlight_code_tokens' );