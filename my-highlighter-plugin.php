<?php

/*

Plugin Name: Highlight Words in a Blog Post
Plugin URI: https://softwareparticles.com
Description: Highlights words in a blog post.
Version: 1.0.0
Author: Dimitris Kokkinos
Author URI: https://softwareparticles.com
License: GPL-2.0+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/



// Register the options page
add_action('admin_menu', 'hct_plugin_menu');

function hct_plugin_menu() {
    add_options_page('Highlight Words in Posts Options', 'Highlight Words', 'manage_options', 'hct-plugin', 'hct_plugin_options_page');
}

// Create the options page
function hct_plugin_options_page() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    // Check if form submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Sanitize and save the input data
        $words_to_replace = array();
        foreach ($_POST['words_to_replace'] as $word => $link) {

            $word = sanitize_text_field($word);
            $link = sanitize_text_field($link);
            $words_to_replace[$word] = $link;
        }

        update_option('hct_plugin_words_to_replace', $words_to_replace);
    } else {
        // Retrieve the saved options
        $words_to_replace = get_option('hct_plugin_words_to_replace');

    }

    // Output the options form

    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

        <form method="post">

            <?php wp_nonce_field('hct_plugin_save_options', 'hct_plugin_options_nonce'); ?>

            <table class="form-table">

                <tbody>

                    <?php foreach ($words_to_replace as $word => $link) : ?>

                        <tr>

                            <th scope="row"><label for="word-<?php echo esc_attr($word); ?>"><?php echo esc_html($word); ?></label></th>

                            <td><input type="text" id="word-<?php echo esc_attr($word); ?>" name="words_to_replace[<?php echo esc_attr($word); ?>]" value="<?php echo esc_attr($link); ?>" class="regular-text"></td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

            <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes'); ?>"></p>

        </form>

    </div>

    <br/>

	<br/>

    <div class="wrap">

        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

        <form method="post">

            <?php wp_nonce_field('hct_plugin_save_options', 'hct_plugin_options_nonce'); ?>

            <table class="form-table">

                <tbody>

                    <?php foreach ($words_to_replace as $word => $link) : ?>

                        <tr>

                            <th scope="row"><label for="word-<?php echo esc_attr($word); ?>"><?php echo esc_html($word); ?></label></th>

                            <td><input type="text" id="word-<?php echo esc_attr($word); ?>" name="words_to_replace[<?php echo esc_attr($word); ?>]" value="<?php echo esc_attr($link); ?>" class="regular-text"></td>

                        </tr>

                    <?php endforeach; ?>

                    

                    <!-- Add new input field for a word and its corresponding link -->

                    <tr>

                        <th scope="row"><label for="new-word">New Word</label></th>

                        <td><input type="text" id="new-word" name="new_word" class="regular-text"></td>

                    </tr>

                    <tr>

                        <th scope="row"><label for="new-link">Link</label></th>

                        <td><input type="text" id="new-link" name="new_link" class="regular-text"></td>

                    </tr>

                </tbody>

            </table>

            <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes'); ?>"></p>

        </form>

    </div>

    <?php

}



// Define the words to replace

$words_to_replace = array(
    'composite pattern' => 'https://softwareparticles/design-patterns-composite',
    'asdasdasd' => 'https://example.com/essential-page',
    'without a link' => 'https://softwareparticles.com/?p=2566&preview=true',
    'state design pattern' => 'https://softwareparticles.com/design-patterns-state/',
    'tree like structure' => ''
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
