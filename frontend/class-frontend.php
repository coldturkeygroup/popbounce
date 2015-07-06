<?php

/**
 * Class Popbounce_Frontend
 *
 * Class for rendering popBounce popups
 */
class Popbounce_Frontend
{

    /**
     * Constructor for the frontend class
     */
    public function __construct()
    {
        add_action('wp_head', [$this, 'custom_css']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('wp_footer', [$this, 'wp_footer'], 0, POPBOUNCE_OPTION_KEY . '-functions');
        add_action('wp_enqueue_scripts', [$this, 'enqueue_style']);
    }

    /**
     * Render the content for the popup
     * if content has been provided by
     * the WordPress admin user.
     */
    public function create_modal_content()
    {
        if (stripslashes(get_option(POPBOUNCE_OPTION_KEY . '_content')) != '') {
            if (get_option(POPBOUNCE_OPTION_KEY . '_full_page')) {
                ?>
                <div id="popbounce-modal" class="popbounce-modal underlay" style="display:none">
                    <div id="popbounce-modal-sub" class="popbounce-modal-sub full modal">
                        <div class="modal-body">
                            <div class="close-btn modal-close" aria-hidden="true">×</div>
                            <?= do_shortcode(stripslashes(get_option(POPBOUNCE_OPTION_KEY . '_content'))); ?>
                        </div>
                    </div>
                </div>
                <?php
            } else {
                ?>
                <div id="popbounce-modal" class="popbounce-modal underlay" style="display:none">
                    <div id="popbounce-modal-sub" class="popbounce-modal-sub modal">
                        <?php if (stripslashes(get_option(POPBOUNCE_OPTION_KEY . '_title')) != '') { ?>
                            <div class="modal-title">
                                <h3><?= stripslashes(get_option(POPBOUNCE_OPTION_KEY . '_title')); ?></h3>
                            </div>
                        <?php } ?>
                        <div class="modal-body">
                            <?= do_shortcode(stripslashes(get_option(POPBOUNCE_OPTION_KEY . '_content'))); ?>
                        </div>
                        <?php if (stripslashes(get_option(POPBOUNCE_OPTION_KEY . '_footer')) != '') { ?>
                            <div class="modal-footer">
                                <p><?= stripslashes(get_option(POPBOUNCE_OPTION_KEY . '_footer')); ?></p>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <?php
            }
        }
    }

    /**
     * Add our required code to
     * the WordPress footer.
     */
    public function wp_footer()
    {
        if ($this->test_if_status_is_off())
            return;

        $this->create_modal_content();
        $this->load_footer_script();
    }

    /**
     * Load required JavaScript into
     * the website footer.
     */
    protected function load_footer_script()
    {
        if (stripslashes(get_option(POPBOUNCE_OPTION_KEY . '_content')) != '') {
            ?>
            <script>
                (function ($) {
                    var fired = false;
                    var cookieName = 'popBounce';
                    var aggressive = '<?= $this->test_if_aggressive(); ?>';

                    $(document).ready(function () {
                        if (typeof ouibounce !== 'undefined' && $.isFunction(ouibounce)) {
                            var _ouibounce = ouibounce(document.getElementById('popbounce-modal'), {
                                <?php
                                if ( $this->test_if_aggressive() )
                                    echo 'aggressive:true,';

                                if ( $this->test_if_given_str('hesitation') )
                                    echo 'delay:'.$this->get_option('hesitation').',';

                                echo "cookieName:cookieName,";

                                echo
                                "callback:function(){".
                                    "fired = true;".
                                "}"
                                ?>
                            });
                        }

                        var popbounce = $('#popbounce-modal');

                        $('body').on('click', function () {
                            $('#popbounce-modal').hide();
                        });

                        popbounce.find('.modal-close').on('click', function () {
                            $('#popbounce-modal').hide();
                        });

                        popbounce.find('.modal-footer').on('click', function () {
                            $('#popbounce-modal').hide();
                        });

                        $('#popbounce-modal-sub').on('click', function (e) {
                            e.stopPropagation();
                        });

                        /*
                         * AUTOFIRE JS
                         */
                        var autoFire = null;
                        <?php
                        if ( $this->test_if_given_str('autoFire') )
                            echo 'autoFire = '.$this->get_option('autoFire').';';
                        ?>

                        function isInteger(x) {
                            return (typeof x === 'number') && (x % 1 === 0);
                        }

                        function handleAutoFire(delay) {
                            if ((_ouibounce.checkCookieValue(cookieName, 'true') && !aggressive ) || fired === true) return;
                            setTimeout(_ouibounce._fireAndCallback, delay);
                        }

                        if (isInteger(autoFire) && autoFire !== null) {
                            handleAutoFire(autoFire);
                        }
                    });
                })(jQuery);
            </script>
            <?php
        }
    }

    /**
     * Get the requested WordPress option
     *
     * @param $option_name
     *
     * @return mixed
     */
    protected function get_option($option_name)
    {
        return get_option(POPBOUNCE_OPTION_KEY . '_' . $option_name);
    }

    /**
     * See if the given option is empty
     *
     * @param $option_name
     *
     * @return bool
     */
    protected function test_if_given_str($option_name)
    {
        return (get_option(POPBOUNCE_OPTION_KEY . '_' . $option_name) != "") ? true : false;
    }

    /**
     * See if "aggressive mode" is enabled.
     * Aggressive mode allows the popup to keep
     * appearing, regardless if the user has
     * seen it before.
     *
     * @return bool
     */
    protected function test_if_aggressive()
    {
        if ($this->get_option('aggressive_mode') == '1' || (current_user_can('manage_options') && ($this->get_option('test_mode') == '1')))
            return true;

        return false;
    }

    /**
     * Enqueue the required JS for the plugin
     */
    public function enqueue_scripts()
    {
        if ($this->test_if_status_is_off())
            return;

        wp_enqueue_script('jquery');  // Enable jQuery (comes with WordPress)
        if (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) {
            wp_enqueue_script(POPBOUNCE_OPTION_KEY . '-function', plugins_url('js/' . POPBOUNCE_OPTION_KEY . '.js', plugin_dir_path(__FILE__)), 'jquery', POPBOUNCE_VERSION_NUM, true);
        } else {
            wp_enqueue_script(POPBOUNCE_OPTION_KEY . '-function', plugins_url('js/min/' . POPBOUNCE_OPTION_KEY . '-ck.js', plugin_dir_path(__FILE__)), 'jquery', POPBOUNCE_VERSION_NUM, true);
        }
    }

    /**
     * Enqueue the required CSS for the plugin
     */
    public function enqueue_style()
    {
        if ($this->test_if_status_is_off())
            return;

        wp_register_style(POPBOUNCE_OPTION_KEY . '-style', plugins_url('css/min/' . POPBOUNCE_OPTION_KEY . '.css', plugin_dir_path(__FILE__)));
        wp_enqueue_style(POPBOUNCE_OPTION_KEY . '-style');
    }

    /**
     * Return custom CSS supplied by the user
     */
    public function custom_css()
    {
        if ($this->test_if_status_is_off())
            return;

        if (stripslashes(get_option(POPBOUNCE_OPTION_KEY . '_custom_css')) != '') {
            echo '<style type="text/css">';
            echo stripslashes(get_option(POPBOUNCE_OPTION_KEY . '_custom_css'));
            echo '</style>';
        }
    }

    /**
     * Test if status is "off" for specific post/page
     */
    protected function test_if_status_is_off()
    {
        global $post;
        $id = null;

        if (isset($post->ID))
            $id = $post->ID;

        // When the individual status for a page/post is 'off', all the other setting don't matter. So this has to be tested at first.
        if (get_post_meta($id, 'popbounce_status', true) && get_post_meta($id, 'popbounce_status', true) === 'off')
            return true;

        if ((!get_option(POPBOUNCE_OPTION_KEY . '_status_default')) || (get_post_meta($id, 'popbounce_status', true) === 'on') || (get_option(POPBOUNCE_OPTION_KEY . '_status_default') === 'on') || (get_option(POPBOUNCE_OPTION_KEY . '_status_default') === 'on_posts' && is_single()) || (get_option(POPBOUNCE_OPTION_KEY . '_status_default') === 'on_pages' && is_page()) || (get_option(POPBOUNCE_OPTION_KEY . '_status_default') === 'on_posts_pages' && (is_single() || is_page())))
            return false;

        return true;
    }

}

new Popbounce_Frontend();