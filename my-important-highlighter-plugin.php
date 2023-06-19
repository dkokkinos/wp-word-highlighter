<?php

/*

Plugin Name: Highlight sp-important-word Words in a Blog Post
Plugin URI: https://softwareparticles.com
Description: Highlights words in a blog post and add sp-important-word in class
Version: 1.0.0
Author: Dimitris Kokkinos
Author URI: https://softwareparticles.com
License: GPL-2.0+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/



// Register the options page
add_action('admin_menu', 'hct_plugin_menu');

function hct_plugin_menu()
{
    add_options_page('Highlight Words in Posts Options', 'Highlight Words', 'manage_options', 'hct-plugin', 'hct_plugin_options_page');
}

// Create the options page
function hct_plugin_options_page()
{
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

    <br />

    <br />

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


// Define the callback function to replace the words
function sp_highlight_words($content)
{
    if (!is_single())
        return $content;

    $words_to_replace = array(
        'Composite Pattern' => 'https://softwareparticles/design-patterns-composite',
        'Composite Design Pattern' => 'https://softwareparticles/design-patterns-composite',
        'FileZilla' => 'https://filezilla-project.org/',
        '.NET Compiler Platform SDK' => 'https://learn.microsoft.com/en-us/dotnet/csharp/roslyn-sdk/',
        'Microsoft.CodeAnalysis.Common' => 'https://www.nuget.org/packages/Microsoft.CodeAnalysis.Common/',
        'PHP' => '',
        'JavaScript' => '',
        'XPath' => '',
        'HTML' => '',
        'WordPress' => ''
    );

    // Use DOMDocument to parse the post content
    $dom = new DOMDocument();
    $dom->loadHTML($content);

    // Loop through each word in the array
    foreach ($words_to_replace as $word => $url) {
        // Use XPath to find all text nodes containing the word inside p elements, excluding those that are already contained in a span with class sp-important-word
        $xpath = new DOMXPath($dom);
        $nodes = $xpath->query("//p//text()[contains(., '$word') and not(ancestor::span[contains(@class,'sp-important-word')])]");

        // Loop through each text node and replace all occurrences of the word with a span element containing a link to the URL
        foreach ($nodes as $node) {
            $split = explode($word, $node->nodeValue);
            $new_node = $dom->createDocumentFragment();
            $new_node->appendChild($dom->createTextNode($split[0]));
            for ($i = 1; $i < count($split); $i++) {
                $span = $dom->createElement('span');
                $span->setAttribute('class', 'sp-important-word');
                if ($url >  0) {
                    $a = $dom->createElement('a');
                    $a->setAttribute('href', $url);
                    $a->nodeValue = $word;
                    $span->appendChild($a);
                } else {
                    $span->nodeValue = $word;
                }
                $new_node->appendChild($span);
                $new_node->appendChild($dom->createTextNode($split[$i]));
            }
            $node->parentNode->replaceChild($new_node, $node);
        }
    }

    // Get the updated post content
    $updated_content = $dom->saveHTML();
    // Return the updated post content
    return $updated_content;
}

add_filter('the_content', 'sp_highlight_words');
