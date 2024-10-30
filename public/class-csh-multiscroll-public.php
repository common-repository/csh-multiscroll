<?php
/**
 * Class to handle all custom post type definitions for Restaurant Reservations
 */
if (!defined('ABSPATH'))
    exit;

class Csh_Multiscroll_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;


	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
        //--------------------Action------------------//
        add_action('wp_enqueue_scripts', array($this, 'cshms_public_register_style'));
        add_action('wp_enqueue_scripts', array($this, 'cshms_public_register_script'));
        //replace content page
        add_filter('template_include', array($this, 'cshms_replace_template'));

	}

    public function cshms_replace_template($template){
        if ( is_single() && is_singular('pt-multiscroll') ) {
            $file   = 'cshms-content.php';
            $find[] = $file;
            $find[] = 'csh-multiscroll/' . $file;

            if ( isset( $file ) ) {
                $template = locate_template( array_unique( $find ) );
                if ( ! $template ) {
                    $template = CSHMS_PLUGIN_TEMPLATES_DIR . $file;
                }
            }

            return  $template;
        }else{
            $page_id = get_the_ID();
            $page_active = cshms_get_pages_active_slide();
            global $sc_id;
            if (in_array($page_id, $page_active)) {
                $q = new WP_Query(array(
                    'post_type' => 'pt-multiscroll',
                    'meta_query' => array(
                        array(
                            'key' => '_cshms-page-active',
                            'value' => $page_id,
                            'compare' => '=',
                        ),
                    )
                ));

                if (count($q->posts) > 0) {
                    $sc_id = $q->posts[0]->ID;
                    $file = 'cshms-content.php';
                    $find[] = $file;
                    $find[] = 'csh-multiscroll/' . $file;
                    $template = locate_template(array_unique($find));
                    if (!$template) {
                        $template = CSHMS_PLUGIN_TEMPLATES_DIR . $file;
                    }
                    return $template;
                }
            }
            return $template;
        }
    }

    public function cshms_public_register_style(){
        wp_register_style('jquery_multiscroll', CSHMS_PLUGIN_ASSETS_URL . 'css/jquery.multiscroll.css');
        wp_register_style('cshms_public_style', CSHMS_PLUGIN_ASSETS_URL . 'css/csh-multiscroll-public.css');
    }

    public function cshms_public_register_script(){
        wp_enqueue_script('jquery');
        wp_enqueue_media();
        wp_register_script('jquery_multiscroll', CSHMS_PLUGIN_ASSETS_URL . 'js/jquery.multiscroll.js');
        wp_register_script('jquery_multiscroll_easings', CSHMS_PLUGIN_ASSETS_URL . 'js/jquery.easings.min.js');
        wp_register_script('cshms_public_script', CSHMS_PLUGIN_ASSETS_URL . 'js/csh-multiscroll-public.js');
    }
}
