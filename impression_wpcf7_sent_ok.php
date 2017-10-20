<?php
/*
Plugin Name: WPCF7 Sent OK
Description: Event Listener plugin to replace sent_ok functionality
Plugin URI: https://www.impression.co.uk
Author: Impression
Version: 1.0
Author URI: https://www.impression.co.uk
*/


Class Impression_CF7_Sent_OK{

    public $form_id;

    public function __construct(){
        add_filter('wpcf7_editor_panels', array($this, 'create_settings_panel'));
        // Hook this in more specificly ?get_plugin_page_hookname?
        add_action('init', array($this, 'save_panel_sent_ok'));
        add_action('wpcf7_contact_form', array($this, 'add_scripts'));
    }

    public function create_settings_panel($panels){
        $panels['sent_ok-panel'] = array(
            'title' => __( 'Sent OK', 'contact-form-7' ),
            'callback' => array($this, 'editor_panel_sent_ok')
        );
        return $panels;
    }

    public function editor_panel_sent_ok(){
        global $post;
        $settings = '';
        $settings = get_post_meta($_GET['post'], '_impression_sent_ok', true);
        ?>
        <h2><?php echo esc_html( __( 'Sent OK Scripts', 'contact-form-7' ) ); ?></h2>
        <fieldset>
            <legend><?php _e( 'Add javascript below to be added to the DOM Event "wpcf7mailsent", use like old on_sent_ok flag from additional Settings.', 'contact-form-7' ); ?></legend>
            <textarea id="wpcf7-sent-ok-settings" name="wpcf7-impression-sent-ok-settings" cols="100" rows="8" class="large-text" data-config-field="impression_sent_ok.body"><?php echo $settings ?></textarea>
        </fieldset>
        <?php
    }

    public function save_panel_sent_ok(){
        $id = isset( $_POST['post_ID'] ) ? $_POST['post_ID'] : '-1';
        if($id > 0){
            if(isset($_POST['wpcf7-impression-sent-ok-settings'])){
                if(!empty($_POST['wpcf7-impression-sent-ok-settings'])){
                    if(update_post_meta($id, '_impression_sent_ok', $_POST['wpcf7-impression-sent-ok-settings'])){
                        return true;
                    }
                }
            }
        }
    }

    public function add_scripts($contact_form){
        if ( !strpos($_SERVER['REQUEST_URI'], 'wp-json') ) {
            $js = get_post_meta( $contact_form->id, '_impression_sent_ok', true );
            if ( $js ) {
                $script = '<script>document.addEventListener( \'wpcf7mailsent\', function(event) {if(event.detail.contactFormId == '.$contact_form->id.'){' . $js . '}}, false );</script>';
                echo $script;
            }
        }
    }
}

new Impression_CF7_Sent_OK();