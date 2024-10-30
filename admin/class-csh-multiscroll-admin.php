<?php

class Csh_Multiscroll_Admin
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;
    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;
    protected $fields = array(); // attribute of all fields.
    protected $sections = array(); // attribute of all sections.

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $plugin_name The name of this plugin.
     * @param      string $version The version of this plugin.
     */

    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        add_action('admin_enqueue_scripts', array($this, 'cshms_admin_register_style'));
        add_action('admin_enqueue_scripts', array($this, 'cshms_admin_register_script'));

        //add_filter('admin_init', array($this, 'create_section_and_fields'));
        //for ks_post
        add_filter( 'safe_style_css', function( $styles ) {
            $styles[] = 'display';
            return $styles;
        } );
        //Create some menus at admin dashboard.
        //add_action('admin_menu', array($this, 'create_settings_menu'));
    }


    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */

    public function cshms_admin_register_style()
    {
        wp_enqueue_style('cshms_select2', CSHMS_PLUGIN_ASSETS_URL . 'css/select2.min.css');
        wp_enqueue_style('cshms_jquery_ui', CSHMS_PLUGIN_ASSETS_URL . 'css/jquery-ui.min.css');
        wp_enqueue_style('cshms_admin_style', CSHMS_PLUGIN_ASSETS_URL . 'css/csh-multiscroll-admin.css');
        wp_enqueue_style('wp-color-picker');
    }

    public function cshms_admin_register_script()
    {
        wp_enqueue_script('jquery');
        wp_enqueue_script('cshms_jquery_ui', CSHMS_PLUGIN_ASSETS_URL . 'js/jquery-ui.min.js');//for drag
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_script('cshms_select2', CSHMS_PLUGIN_ASSETS_URL . 'js/select2.min.js');
        wp_enqueue_script('cshms_admin_script', CSHMS_PLUGIN_ASSETS_URL . 'js/csh-multiscroll-admin.js');
        wp_localize_script('cshms_admin_script', 'cshms_new_section', array('new_html' => $this->add_new_slide_section()));
    }

    /*-------------------------------------
    One page -> one Form -> one Submit Button -> one Group setting -> display some sections and it's fields:
        do_settings_sections($sectionId);
        settings_fields( 'groupSetting' );
        submit_button();
    -------------------------------------*/
    public function add_new_slide_section()
    {
        ob_start();
        ?>
        <div class="cshms-meta-{{slide-count}} cshms-meta">
            <div class="cshms-heading">
                <p class="cshms-bar-title"><span class="cshms-section-name">Section {{slide-count}}</span> <span
                            class="dashicons  dashicons-arrow-up"></span></p>
                <span class="cshms-close dashicons dashicons-no" title="Delete section"></span>
            </div>

            <div class="cshms-toggle-action">
                <div class="cshms-content">
                    <!-- Left content -->
                    <div class="content-wrap left-content-wrap">
                        <div class="left-content-image content-image">
                            <input type="hidden" class="hide-image-url"
                                   id="cshms_left_image_{{slide-count}}"
                                   name="cshms_left_image_{{slide-count}}"
                                   value=""/>
                            <div class="cshms-show-image">
                            </div>
                            <a href="#" class="select-image">Left Image</a>
                            <a href="#" class="remove-image" style="display: none;">Remove
                                image</a>
                        </div>

                        <div class="left-content-text content-text">
                            <textarea rows="10" placeholder="Left text or html content..."
                                  id="cshms_left_text_{{slide-count}}"
                                  name="cshms_left_text_{{slide-count}}"></textarea>
                        </div>

                    </div>

                    <!-- Right content -->
                    <div class="content-wrap right-content-wrap">
                        <div class="right-content-image content-image">
                            <input type="hidden" class="hide-image-url"
                                   id="cshms_right_image_{{slide-count}}"
                                   name="cshms_right_image_{{slide-count}}"
                                   value=""/>
                            <div class="cshms-show-image">
                            </div>
                            <a href="#" class="select-image">Right image</a>
                            <a href="#" class="remove-image" style="display: none;">Remove
                                image</a>
                        </div>

                        <div class="right-content-text content-text">
                            <textarea id="cshms_right_text_{{slide-count}}" rows="10"
                                  placeholder="Right text or html content..."
                                  name="cshms_right_text_{{slide-count}}"></textarea>
                        </div>

                    </div>
                    <input type="hidden" class="cshms_hide_content"
                           id="cshms_hide_content_{{slide-count}}"
                           name="cshms_hide_content_{{slide-count}}"
                           value=""/>
                </div>
            </div>

        </div>
        <?php
        return ob_get_clean();
    }

    public function add_section($sectionId, $title)
    {
        $input = array('sectionId' => $sectionId,
            'title' => $title);
        array_push($this->sections, $input);
    }

    //Fields of Sections.
    public function add_field_of_section($sectionId, $fieldId, $title, $typeInput, $xData = array())
    {
        $input = array('sectionId' => $sectionId,
            'fieldId' => $fieldId,
            'title' => $title,
            'typeInput' => $typeInput,
            'xData' => $xData);
        array_push($this->fields, $input);
    }


    public function create_section_and_fields()
    {

        foreach ($this->sections as $key => $value) {
            add_settings_section(
                $this->sections[$key]['sectionId'], // ID
                $this->sections[$key]['title'], // Title
                '', // Section can no need callback function.
                $this->sections[$key]['sectionId'] // Let page same sectionId to unique.
            );
        }

        // Render fields loop.
        foreach ($this->fields as $key => $value) {
            $callback = array($this, 'fields_callback');
            add_settings_field(
                $this->fields[$key]['fieldId'], // ID
                $this->fields[$key]['title'], // Title
                $callback, // Callback
                $this->fields[$key]['sectionId'], // Same Page
                $this->fields[$key]['sectionId'], // Belong to Section id
                array('fieldId' => $this->fields[$key]['fieldId'],
                    'typeInput' => $this->fields[$key]['typeInput'],
                    'xData' => $this->fields[$key]['xData']
                )
            );
        }
    }

    public function register_postypes()
    {
        add_action('init', 'cshms_register_posttye');
        function cshms_register_posttye()
        {
            $labels = array(
                'name' => _x('Multiscroll', 'post type general name', 'cshmultiscroll'),
                'singular_name' => _x('Multiscroll', 'post type singular name', 'cshmultiscroll'),
                'menu_name' => _x('Csh Multiscroll', 'admin menu', 'cshmultiscroll'),
                'name_admin_bar' => _x('Multiscroll', 'add new on admin bar', 'cshmultiscroll'),
                'add_new' => _x('Add New', 'Multiscroll', 'cshmultiscroll'),
                'add_new_item' => __('Add New Multiscroll', 'cshmultiscroll'),
                'new_item' => __('New Multiscroll', 'cshmultiscroll'),
                'edit_item' => __('Edit Multiscroll', 'cshmultiscroll'),
                'view_item' => __('View Multiscroll', 'cshmultiscroll'),
                'all_items' => __('All Multiscrolls', 'cshmultiscroll'),
                'search_items' => __('Search Multiscroll', 'cshmultiscroll'),
                'parent_item_colon' => __('Parent Multiscroll:', 'cshmultiscroll'),
                'not_found' => __('No Multiscroll found.', 'cshmultiscroll'),
                'not_found_in_trash' => __('No Multiscroll found in Trash.', 'cshmultiscroll')
            );

            $args = array(
                'labels' => $labels,
                'description' => __('Description.', 'cshmultiscroll'),
                'supports' => array('title'),
                'menu_icon' => 'dashicons-format-gallery',
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'capability_type' => 'page',
                'has_archive' => true,
                'hierarchical' => false,
                'menu_position' => null
            );

            register_post_type('pt-multiscroll', $args);
        }
    }

    public function add_meta_box()
    {
        add_action('add_meta_boxes', 'cshms_add_cshmss_metaboxes');
        function cshms_add_cshmss_metaboxes()
        {
            // Cause Information.
            add_meta_box('meta-cshms-infomation', 'Multiscroll Content', 'cshms_metabox_show_info', 'pt-multiscroll', 'normal', 'low');
            function cshms_metabox_show_info($post)
            {
                wp_enqueue_media();

                $cshms_section_data = get_post_meta($post->ID, '_cshms-sections-data', true);
                if (!empty($cshms_section_data)) {
                    $cshms_section_count = count($cshms_section_data);
                } else {
                    $cshms_section_count = 0;
                }
                ?>
                <div id="cshms-meta-wrap">
                    <?php
                    if ($cshms_section_count >= 1) {
                        for ($i = 1; $i <= $cshms_section_count; $i++) {
                            $left_text = $cshms_section_data['section_' . $i]['left_text'];
                            $right_text = $cshms_section_data['section_' . $i]['right_text'];
                            $left_image = $cshms_section_data['section_' . $i]['left_image'];
                            $right_image = $cshms_section_data['section_' . $i]['right_image'];
                            $hide_content = !empty($cshms_section_data['section_' . $i]['hide_content']) ? $cshms_section_data['section_' . $i]['hide_content'] : '';
                            ?>
                            <div class="<?php echo 'cshms-meta-' . $i . ' ' . $hide_content ?> cshms-meta">
                                <div class="cshms-heading">
                                    <?php
                                    if ($hide_content == 'hide-content') {
                                        ?><p class="cshms-bar-title"><span
                                                class="cshms-section-name"><?php echo 'Section ' . $i; ?></span> <span
                                                class="dashicons  dashicons-arrow-down"></span></p><?php
                                    } else {
                                        ?><p class="cshms-bar-title"><span
                                                class="cshms-section-name"><?php echo 'Section ' . $i; ?></span> <span
                                                class="dashicons  dashicons-arrow-up"></span></p><?php
                                    }
                                    ?>
                                    <span class="cshms-close dashicons dashicons-no"
                                          title="Delete section"></span>
                                </div>

                                <div class="cshms-toggle-action">
                                    <div class="cshms-content">
                                        <!-- Left content -->
                                        <div class="content-wrap left-content-wrap">
                                            <div class="left-content-image content-image">
                                                <input type="hidden" class="hide-image-url"
                                                       id="<?php echo 'cshms_left_image_' . $i ?>"
                                                       name="<?php echo 'cshms_left_image_' . $i ?>"
                                                       value="<?php echo esc_attr($left_image) ?>"/>
                                                <div class="cshms-show-image">
                                                    <?php
                                                    if ($left_image != "") {
                                                        ?>
                                                        <img src="<?php echo esc_url($left_image) ?>">
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                                <?php
                                                if ($left_image != "") {
                                                    ?>
                                                    <a href="#" class="select-image" style="display: none;">Left
                                                        Image</a>
                                                    <a href="#" class="remove-image">Remove Image</a>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <a href="#" class="select-image">Left Image</a>
                                                    <a href="#" class="remove-image" style="display: none;">Remove
                                                        Image</a>
                                                    <?php
                                                }
                                                ?>
                                            </div>

                                            <div class="left-content-text content-text">
                                                <textarea id="<?php echo 'cshms_left_text_' . $i ?>" rows="10"
                                                      placeholder="Left text or html content..."
                                                      name="<?php echo 'cshms_left_text_' . $i ?>"><?php echo esc_textarea($left_text) ?></textarea>
                                            </div>
                                        </div>

                                        <!-- Right content -->
                                        <div class="content-wrap right-content-wrap">
                                            <div class="right-content-image content-image">
                                                <input type="hidden" class="hide-image-url"
                                                       id="<?php echo 'cshms_right_image_' . $i ?>"
                                                       name="<?php echo 'cshms_right_image_' . $i ?>"
                                                       value="<?php echo esc_attr($right_image) ?>"/>
                                                <div class="cshms-show-image">
                                                    <?php
                                                    if ($right_image != "") {
                                                        ?>
                                                        <img src="<?php echo esc_url($right_image) ?>">
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                                <?php
                                                if ($right_image != "") {
                                                    ?>
                                                    <a href="#" class="select-image" style="display: none;">Right
                                                        Image</a>
                                                    <a href="#" class="remove-image">Remove image</a>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <a href="#" class="select-image">Right Image</a>
                                                    <a href="#" class="remove-image" style="display: none;">Remove
                                                        image</a>
                                                    <?php
                                                }
                                                ?>
                                            </div>

                                            <div class="right-content-text content-text">
                                                <textarea id="<?php echo 'cshms_right_text_' . $i ?>" rows="10"
                                                      placeholder="Right text or html content..."
                                                      name="<?php echo 'cshms_right_text_' . $i ?>"><?php echo esc_attr($right_text) ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" class="cshms_hide_content"
                                       id="<?php echo 'cshms_hide_content_' . $i ?>"
                                       name="<?php echo 'cshms_hide_content_' . $i ?>"
                                       value="<?php echo esc_attr($hide_content) ?>"/>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
                <?php
                ?>
                <input type="hidden" id="cshms_section_count" name="cshms_section_count"
                       value="<?php echo esc_attr($cshms_section_count) ?>"/>
                <div class="cshms-add-new-wrap">
                    <a href="javascript:void(0)" class="cshms-add-new">Add new section</a>
                </div>
                <?php
            }

            add_meta_box('meta-cshms-select-page', 'Show Multiscroll at Page', 'cshms_metabox_show_page', 'pt-multiscroll', 'side', 'low');
            function cshms_metabox_show_page($post)
            {
                $pages_active = cshms_get_pages_active_slide();
                $page_selected = get_post_meta($post->ID, '_cshms-page-active', true);
                $q = new WP_Query(array(
                    'post_type' => 'page'
                ));
                ?>
                <select name="cshms-select-page" id="cshms-select-page">
                    <option value=""></option>
                    <?php
                    foreach ($q->posts as $p) {
                        if ( ($p->ID == $page_selected) || !in_array($p->ID,$pages_active)){
                            ?>
                            <option value="<?php echo $p->ID ?>" <?php selected($p->ID, $page_selected) ?> ><?php echo $p->post_title ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
                <?php
            }

            add_meta_box('meta-cshms-premium', 'CSH Multiscroll Premium <span class="dashicons dashicons-star-filled"></span>', 'cshms_metabox_premium', 'pt-multiscroll', 'side', 'low');
            function cshms_metabox_premium($post)
            {
                ?>
                    <div class="cshms-premium-inner">
                        <p>- Custom more <span>Styles</span></p>
                        <p>- Support content contains <span>Shortcode</span></p>
                        <p>- <span>24/7 support</span></p>
                    </div>
                    <div class="link-buy">
                        <a id="cshms-premium-button" class="button button-primary" href="http://demo.cmssuperheroes.com/csh-plugins/csh-multiscroll" target="_blank">Get Premium now!</a>
                        <p><small>Price is lower than 15$, Extend support to 12 months</small></p>
                    </div>
                <?php
            }
        }

        add_action('save_post', 'cshms_metabox_save');
        function cshms_metabox_save($post_id)
        {
            $post_type = get_post_type($post_id);
            // If this isn't a 'book' post, don't update it.
            if ("pt-multiscroll" != $post_type) return;

            if ($_POST) {
                $cshms_section_count = 0;
                $data = $_POST;
                foreach ($data as $key => $value) {
                    $check = strpos($key, 'cshms_left_text_');
                    if ($check !== false) {
                        $cshms_section_count++;
                    }
                }
                //Section meta
                $data_update = array();
                if ($cshms_section_count >= 1) {
                    for ($i = 1; $i <= $cshms_section_count; $i++) {
                        $section_data = array(
                            'hide_content' => sanitize_text_field($_POST['cshms_hide_content_' . $i]),
                            'left_text' => wp_kses_post($_POST['cshms_left_text_' . $i]),
                            'left_image' => esc_url_raw($_POST['cshms_left_image_' . $i]),
                            'right_text' =>wp_kses_post($_POST['cshms_right_text_' . $i]),
                            'right_image' => esc_url_raw($_POST['cshms_right_image_' . $i])
                        );
                        $data_update['section_' . $i] = $section_data;
                    }
                }

                update_post_meta($post_id, '_cshms-sections-data', $data_update);
                //Page active meta
                if (isset($_POST['cshms-select-page'])) {
                    $page_selected = sanitize_text_field($_POST['cshms-select-page']);
                    update_post_meta($post_id, '_cshms-page-active', $page_selected);
                }

            }
        }
    }

    public
    function create_settings_menu()
    {
        //Main setting menu.
        register_setting(
            $this->plugin_name, //group of setting.
            $this->plugin_name //name of setting.
        );

        $menu_callback = array($this, 'main_settings_menu_show');
        add_submenu_page('edit.php?post_type=pt-multiscroll',
            'Settings',
            'Settings',
            'manage_options',
            'cshms-settings',
            $menu_callback);

    }

    public
    function main_settings_menu_show()
    {
        wp_enqueue_media();
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_style('wp-color-picker');

        ?>
        <div id="cshms-setting-wrap">

            <h1 style="margin-bottom: 50px;">Plugin Setting</h1>

            <form method="post" action="options.php">
                <?php
                foreach ($this->sections as $key => $value) {
                    if ($this->sections[$key]['sectionId'] == 'cshms-tab1') {
                        do_settings_sections($this->sections[$key]['sectionId']);
                        settings_fields($this->plugin_name);
                        submit_button();
                    }
                }
                ?>
            </form>

        </div>
        <?php

    }

    public
    function fields_callback($args)
    {
        $arrGlobalData = get_option($this->plugin_name);
        switch ($args['typeInput']) {
            case 'radio':
                $name = $this->plugin_name . '[' . $args['fieldId'] . ']';

                foreach ($args['xData']['options'] as $key => $value) {
                    $checked_default = '';
                    $enable_checked = '';
                    // check default.
                    if ($args['xData']['default'][$key] == '1') {
                        $checked_default = 'checked';
                        $enable_checked = $checked_default;
                    } else {
                        $checked_default = '';
                        $enable_checked = $checked_default;
                    }

                    //not default.
                    if ((isset($arrGlobalData[$args['fieldId']]) && $arrGlobalData[$args['fieldId']] != '')) {
                        $value_data = esc_attr($arrGlobalData[$args['fieldId']]);
                        if ($value == $value_data) {
                            $enable_checked = 'checked';
                        } else {
                            $enable_checked = '';
                        }
                    }

                    ?>
                    <div style="display: inline-table; margin-right: 15px;">
                        <input type="radio" id="<?php echo esc_attr($args['fieldId']); ?>"
                               name="<?php echo esc_attr($name); ?>"
                               value="<?php echo esc_attr($value); ?>" <?php echo esc_html($enable_checked) ?>> <?php echo esc_html($value); ?>
                    </div>
                    <?php
                }
                break;
            case 'select':
                $name = $this->plugin_name . '[' . $args['fieldId'] . ']';

                $selected = '';
                $desc = $args['xData']['desc'];
                ?>
                <select id="<?php echo esc_attr($args['fieldId']); ?>" name="<?php echo esc_attr($name); ?>">
                    <?php
                    foreach ($args['xData']['options'] as $key => $value) {
                        if ((isset($arrGlobalData[$args['fieldId']]) && $arrGlobalData[$args['fieldId']] != '')) {
                            $value_data = esc_attr($arrGlobalData[$args['fieldId']]);
                            if ($value == $value_data) {
                                $selected = 'selected';
                            } else {
                                $selected = '';
                            }
                        }

                        ?>
                        <option value="<?php echo esc_attr($value) ?>" <?php echo esc_html($selected) ?>><?php echo esc_html($value); ?> </option>
                        <?php
                    }
                    ?>
                </select>
                <p><?php echo esc_html($desc); ?></p>
                <?php
                break;
            case 'text':
                $name = $this->plugin_name . '[' . $args['fieldId'] . ']';
                $value = "";

                if (isset($args['xData']['default'])) {
                    $value = $args['xData']['default'];
                }

                $desc = "";
                if (isset($args['xData']['desc'])) {
                    $desc = $args['xData']['desc'];
                }

                if ((isset($arrGlobalData[$args['fieldId']]) && $arrGlobalData[$args['fieldId']] != '')) {
                    $value = esc_attr($arrGlobalData[$args['fieldId']]);
                }

                ?>
                <input
                        type="text"
                        class="regular-text"
                        id="<?php echo esc_attr($args['fieldId']); ?>"
                        name="<?php echo esc_attr($name); ?>"
                        value="<?php echo esc_attr($value) ?>"/>

                <p><?php echo esc_html($desc); ?></p>
                <?php
                break;
            case 'color':
                $value = $args['xData']['default'];
                $name = $this->plugin_name . '[' . $args['fieldId'] . ']';
                $desc = $args['xData']['desc'];

                if ((isset($arrGlobalData[$args['fieldId']]) && $arrGlobalData[$args['fieldId']] != '')) {
                    $value = esc_attr($arrGlobalData[$args['fieldId']]);
                }

                ?>
                <input
                        name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($args['fieldId']); ?>"
                        type="text"
                        value="<?php echo esc_attr($value) ?>"
                        class="csh_color_picker"/>
                <p> <?php echo esc_html($desc); ?></p>

                <script type="text/javascript">
                    jQuery(document).ready(function ($) {
                        // color type.
                        $(".csh_color_picker").wpColorPicker();
                    });
                </script>

                <?php
                break;
            case 'upload':
                $name = $this->plugin_name . '[' . $args['fieldId'] . ']';
                $value = $args['xData']['default'];

                if ((isset($arrGlobalData[$args['fieldId']]) && $arrGlobalData[$args['fieldId']] != '')) {
                    $value = esc_attr($arrGlobalData[$args['fieldId']]);
                }

                $inputId = $args['fieldId'] . '_input';
                $buttonId = $args['fieldId'] . '_button';
                global $j_input_var;
                global $j_button_var;
                $j_input_var = '#' . $inputId;
                $j_button_var = '#' . $buttonId;
                ?>
                <div>

                    <input type="text"
                           name="<?php echo esc_attr($name); ?>"
                           id="<?php echo esc_attr($inputId) ?>" class="regular-text"
                           value="<?php echo esc_attr($value) ?>">
                    <input type="button" name="upload-btn" id="<?php echo esc_attr($buttonId) ?>"
                           class="button-secondary" value="Upload Image">

                </div>
                <script type="text/javascript">
                    jQuery(document).ready(function ($) {
                        var j_button = "<?php echo esc_html($j_button_var); ?>";
                        $(j_button).click(function (e) {
                            e.preventDefault();
                            var image = wp.media({
                                title: 'Upload Image',
                                // mutiple: true if you want to upload multiple files at once
                                multiple: false
                            }).open()
                                .on('select', function (e) {
                                    // This will return the selected image from the Media Uploader, the result is an object
                                    var uploaded_image = image.state().get('selection').first();
                                    // We convert uploaded_image to a JSON object to make accessing it easier
                                    // Output to the console uploaded_image
                                    console.log(uploaded_image);
                                    var image_url = uploaded_image.toJSON().url;
                                    // Let's assign the url value to the input field
                                    var j_input = "<?php echo esc_html($j_input_var); ?>";
                                    $(j_input).val(image_url);
                                });
                        });
                    });
                </script>
                <?php
                echo esc_html($args['xData']['desc']);
                break;
            case 'number':
                $name = $this->plugin_name . '[' . $args['fieldId'] . ']';
                $value = $args['xData']['default'];

                if ((isset($arrGlobalData[$args['fieldId']]) && $arrGlobalData[$args['fieldId']] != '')) {
                    $value = esc_attr($arrGlobalData[$args['fieldId']]);
                }

                ?>
                <input
                        style="width: 8%;"
                        type="number"
                        id="<?php echo esc_attr($args['fieldId']); ?>"
                        name="<?php echo esc_attr($name); ?>"
                        value="<?php echo esc_attr($value) ?>"/>
                <?php
                echo esc_html($args['xData']['desc']);
                break;
            case 'description':
                echo htmlspecialchars($args['xData']['desc']);
                break;
            case 'checkbox':
                $name = $this->plugin_name . '[' . $args['fieldId'] . ']';
                $checked = '';
                $desc = $args['xData']['desc'];

                if ((isset($arrGlobalData[$args['fieldId']]) && $arrGlobalData[$args['fieldId']] != '')) {
                    $checked = 'checked';
                } else {
                    $checked = '';
                }

                ?>
                <input type="checkbox" id="<?php echo esc_attr($args['fieldId']); ?>"
                       name="<?php echo esc_attr($name); ?>"<?php echo esc_html($checked) ?>> <?php echo esc_html($desc); ?>
                <?php
                break;
            case 'textarea':
                $name = $this->plugin_name . '[' . $args['fieldId'] . ']';
                $value = "";

                $label = "";
                if (isset($args['xData']['label'])) {
                    $label = $args['xData']['label'];
                }

                $width = "";
                if (isset($args['xData']['width'])) {
                    $width = $args['xData']['width'];
                }

                $height = "";
                if (isset($args['xData']['height'])) {
                    $height = $args['xData']['height'];
                }

                if ((isset($arrGlobalData[$args['fieldId']]) && $arrGlobalData[$args['fieldId']] != '')) {
                    $value = esc_attr($arrGlobalData[$args['fieldId']]);
                }
                ?>
                <p><?php echo esc_html($label); ?></p>
                <textarea
                        style="width: <?php echo esc_html($width) ?>; height: <?php echo esc_html($height) ?>;"
                        id="<?php echo esc_attr($args['fieldId']); ?>"
                        name="<?php echo esc_attr($name); ?>"><?php echo esc_html($value) ?></textarea>
                <?php
                break;
            default:
        }
    }// end of call back.


}// End of AdminSettings.

?>