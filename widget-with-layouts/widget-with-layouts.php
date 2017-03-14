<?php
/**
 * Plugin Name: Widget with layouts
 */


function widget_with_layouts_init_lang(){
	load_plugin_textdomain('widget-with-layouts', false, dirname( plugin_basename( __FILE__ ) ). '/language/');
}
add_action('plugins_loaded', 'widget_with_layouts_init_lang');

function add_style_widget_with_layouts() {
    wp_enqueue_style('widget-with-layouts', plugins_url('/css/widget-with-layouts.css', __FILE__ ));
}
add_action('wp_enqueue_scripts', 'add_style_widget_with_layouts');

function register_widget_with_layouts() {
	register_widget('WidgetsWithLayouts');
}
add_action('widgets_init', 'register_widget_with_layouts');

class WidgetsWithLayouts extends WP_Widget {
    function __construct() {
        parent::__construct(false, __('Widget with layouts', 'widget-with-layouts'));
    }

    function widget($args, $instance) {
        if (!empty($instance['icl_language']) && $instance['icl_language'] != 'multilingual' && $instance['icl_language'] != ICL_LANGUAGE_CODE) {
            return '';
        }
        echo $args['before_widget'];
        $post_id = $instance['post-id'];
        $layout = $instance['layout'];
        if ($post_id && $layout) {
            $post = get_post($post_id);
            if ($layout == 1) { ?>
                <div class="wwl-image-with-hover">
                    <div class="wwl-image-with-hover-info">
                        <?php if (has_post_thumbnail($post_id)) {
                            echo get_the_post_thumbnail($post_id, 'full');
                        } ?>
                        <h2><?php echo get_the_title($post_id); ?></h2>
                    </div>
                    <div class="wwl-image-with-hover-content">
                        <?php echo wpautop($post->post_content); ?>
                    </div>
                </div>
            <?php } elseif ($layout == 2) { ?>
                <div class="wwl-image-floated-left">
                    <div class="wwl-image-floated-left-img">
                        <?php if (has_post_thumbnail($post_id)) {
                            echo get_the_post_thumbnail($post_id, 'full');
                        } ?>
                    </div>
                    <div class="wwl-image-floated-left-info">
                        <h2><?php echo get_the_title($post_id); ?></h2>
                        <div class="wwl-image-floated-left-content">
                            <?php echo wpautop($post->post_content); ?>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
                <div class="wwl-image-floated-right">
                    <div class="wwl-image-floated-right-info">
                        <h2><?php echo get_the_title($post_id); ?></h2>
                        <div class="wwl-image-floated-right-content">
                            <?php echo wpautop($post->post_content); ?>
                        </div>
                    </div>
                    <div class="wwl-image-floated-right-img">
                        <?php if (has_post_thumbnail($post_id)) {
                            echo get_the_post_thumbnail($post_id, 'full');
                        } ?>
                    </div>
                </div>
            <?php }
        }
        echo $args['after_widget'];
    }

    function update($new_instance, $old_instance) {
        return $new_instance;
    }

    function form($instance) {
        $instance = wp_parse_args((array) $instance, array('icl_language' => 'multilingual', 'title' => '', 'post-id' => 0, 'layout' => ''));
        $post_id = $instance['post-id'];
        if ($post_id) {
			$post = get_post($post_id); 
        } ?>
        <div class="widget-with-layouts">
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'widget-with-layouts')?></label>
                <input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>"/>
            </p>
            <div class="autocomplete-input">
                <label for="<?php echo $this->get_field_id('post'); ?>"><?php _e('Post', 'widget-with-layouts')?></label>
                <input type="text" id="<?php echo $this->get_field_id('post'); ?>" value="<?php echo $post_id ? $post->post_title : ''; ?>" class="widget-with-layouts-autocomplete"/>
                <input type="hidden" name="<?php echo $this->get_field_name('post-id'); ?>" value="<?php echo $post_id; ?>"/>
                <ul class="widget-with-layouts-autocomplete-result"></ul>
            </div>
            <div class="widget-layouts">
	            <h3><?php _e('Layouts', 'widget-with-layouts')?></h3>
	            <p>
	                <label for="<?php echo $this->get_field_id('widget-layout-1'); ?>"><?php _e('Image with title and description', 'widget-with-layouts')?></label>
	                <input type="radio" name="<?php echo $this->get_field_name('layout'); ?>" value="1" id="<?php echo $this->get_field_id('widget-layout-1'); ?>" <?php if ($instance['layout'] == "1") { echo 'checked="checked"'; } ?>/>
				</p>
				<p>
	                <label for="<?php echo $this->get_field_id('widget-layout-2'); ?>"><?php _e('Block with image floated to the left', 'widget-with-layouts')?></label>
	                <input type="radio" name="<?php echo $this->get_field_name('layout'); ?>" value="2" id="<?php echo $this->get_field_id('widget-layout-2'); ?>" <?php if ($instance['layout'] == "2") { echo 'checked="checked"'; } ?>/>
				</p>
				<p>
	                <label for="<?php echo $this->get_field_id('widget-layout-3'); ?>"><?php _e('Block with image floated to the right', 'widget-with-layouts')?></label>
	                <input type="radio" name="<?php echo $this->get_field_name('layout'); ?>" value="3" id="<?php echo $this->get_field_id('widget-layout-3'); ?>">  <?php if ($instance['layout'] == "3") { echo 'checked="checked"'; } ?>
	            </p>
            </div>
        </div>
        <?php
        icl_widget_text_language_selectbox($instance['icl_language'], $this->get_field_name('icl_language'));
    }
}
function add_script_widget_with_layouts_admin_head() {
    $args = array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => -1
    );
    $posts = get_posts($args);
    $posts_for_json = array();
    foreach ($posts as $post) {
        $post_id = $post->ID;
        $posts_for_json[$post_id] = array('post_title' => get_the_title($post_id), 'post_url' => get_the_permalink($post_id));
    }
    $posts_for_json = json_encode($posts_for_json); ?>
    <script type="text/javascript">
        var $document = jQuery(document);
        var posts_for_widget = '<?php echo $posts_for_json; ?>';
        $document.on('keyup', '.widget-with-layouts-autocomplete', function() {
            var results = '';
            var $this = jQuery(this);
            $this.parents('.autocomplete-input').find('input[type="hidden"]').val('');
            var inputVal = $this.val();
            var posts = jQuery.parseJSON(posts_for_widget); 
            if (inputVal) {
                jQuery.each(posts, function(index, el) {
                    if (el.post_title.indexOf(inputVal) !== -1) {
                        results = results + '<li><a class="selected-widget-post" href="#" data-post-id="' + index + '">' + el.post_title + '</a></li>';

                    }
                    if (el.post_url.indexOf(inputVal) > -1) {
                        results = results + '<li><a class="selected-widget-post" href="#" data-post-id="' + index + '">' + el.post_title + '</a></li>';
                    }
                });
                if (results) {
                    $this.parent().find('.widget-with-layouts-autocomplete-result').html(results).css('display', 'block');
                }
            }
        });
        $document.on('click', '.selected-widget-post', function(el) {
            el.preventDefault();
            var $this = jQuery(this);
            var autocompleteInput = $this.parents('.autocomplete-input');
            autocompleteInput.find('[type="text"]').val($this.html());
            autocompleteInput.find('[type="hidden"]').val($this.data('post-id'));
            autocompleteInput.find('.widget-with-layouts-autocomplete-result').css('display', 'none');
        });
    </script>
<?php }
add_action('admin_head', 'add_script_widget_with_layouts_admin_head');
