<?php

class CptBookMain
{

    public function __construct()
    {
        add_action('init', array($this, 'create_book_cpt'));
        add_action('add_meta_boxes', array($this, 'add_meta_box'));
        add_action('admin_enqueue_scripts', array($this, 'backend_scripts_color_picker'));
        add_action('wp_enqueue_scripts', array($this, 'frontend_book_style'));
        add_action("save_post", array($this, 'save_meta'));
        add_shortcode('book', array($this, 'cpt_book_shortcode'));
    }

    public function create_book_cpt()
    {
        register_post_type('book', array(
            'labels'      => array(
                'name'               => esc_html('Books', 'cpt-book'),
                'singular_name'      => esc_html('Book', 'cpt-book'),
                'menu_name'          => esc_html('Books', 'cpt-book'),
                'name_admin_bar'     => esc_html('Book', 'cpt-book'),
                'add_new'            => esc_html('Add Book', 'cpt-book'),
                'add_new_item'       => esc_html('Add Book', 'cpt-book'),
                'new_item'           => esc_html('New Book', 'cpt-book'),
                'edit_item'          => esc_html('Edit Book', 'cpt-book'),
                'view_item'          => esc_html('View Books', 'cpt-book'),
                'all_items'          => esc_html('All Books', 'cpt-book'),
                'search_items'       => esc_html('Search Books', 'cpt-book'),
                'not_found'          => esc_html('No books found.', 'cpt-book'),
                'featured_image'     => esc_html('Books Cover Image', 'cpt-book'),
                'set_featured_image' => esc_html('Set Books image', 'cpt-book'),
            ),
            'public'      => true,
            'has_archive' => true,
            'rewrite'     => array(
                'slug' => 'book'
            ),
            'show_in_menu' => true,
            'menu_position' => 5,
            'menu_icon'   => 'dashicons-carrot',
            'supports'    => array('title', 'editor', 'thumbnail'),
        ));
    }


    public function add_meta_box()
    {
        add_meta_box("author_name", esc_html("Author", "cpt-book"), array($this, "author_name_html"), "book", 'advanced');
        add_meta_box("color_id", esc_html("Color", "cpt-book"), array($this, "color_html"), "book", 'advanced');
    }

    public function author_name_html($post)
    { ?>
        <div class="row">
            <div class="label"><?php echo esc_html('Author name', 'cpt-book') ?></div>
            <div class="fields">

                <input type="text" name="author_name" value="<?php echo esc_html(get_post_meta($post->ID, 'meta_author', true)) ?>" />
            </div>
        </div>

    <?php }

    function backend_scripts_color_picker()
    {

        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
    }

    function frontend_book_style()
    {
        $plugin_url = plugin_dir_url((__FILE__));
        wp_enqueue_style('style1', $plugin_url . '/assets/css/book-style.css');
    }


    function color_html($post)
    {
        $custom = get_post_custom($post->ID);
        $color_id = (isset($custom['color_id'][0])) ? $custom['color_id'][0] : '';
        wp_nonce_field('color_html', 'color_html_nonce');
    ?>
        <script>
            jQuery(document).ready(function($) {
                $('.color_field').each(function() {
                    $(this).wpColorPicker();
                });
            });
        </script>
        <div class="row">
            <div class="label"><?php esc_attr_e('Choose a color for your book', 'cpt-book'); ?></div>
            <div class="fields">
                <input class="color_field" type="hidden" name="color_id" value="<?php esc_attr_e($color_id); ?>" />
            </div>
        </div>
        <?php }

    public function save_meta($post_id)
    {

        if (isset($_POST["author_name"])) :
            update_post_meta($post_id, 'meta_author', $_POST["author_name"]);
        endif;

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (!current_user_can('edit_pages')) {
            return;
        }

        if (!isset($_POST['color_id']) || !wp_verify_nonce($_POST['color_html_nonce'], 'color_html')) {
            return;
        }
        $color_id = (isset($_POST['color_id']) && $_POST['color_id'] != '') ? $_POST['color_id'] : '';
        update_post_meta($post_id, 'color_id', $color_id);
    }


    //Shortcode
    function cpt_book_shortcode($attr)
    {
        $args_shortcode = shortcode_atts(array(
            'id' => ''
        ), $attr);

        global $post;
        $args = array(
            'post_type' => 'book',
            'posts_per_page' => 1,
            'p' => $args_shortcode['id']
        );

        $the_query = new WP_Query($args);

        if ($the_query->have_posts()) :
            while ($the_query->have_posts()) : $the_query->the_post(); ?>
                <div class="book" style="background:<?php echo get_post_meta($post->ID,  'color_id', true);  ?>">
                    <h3><?php the_title() ?></h3>
                    <p><?php echo get_post_meta($post->ID,  'meta_author', true); ?></p>
                </div>
<?php endwhile;
        endif;

        wp_reset_postdata();
    }
}
$cptBookMain = new CptBookMain();
