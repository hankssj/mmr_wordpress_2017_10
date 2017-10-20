<?php
/*
 *   Setup the settings page for configuring the options
 */
if ( class_exists( 'GFForms' ) ) {
	GFForms::include_addon_framework();
	class ITSG_GF_Infobox_Settings extends GFAddOn {
		protected $_version = '1.5.3';
		protected $_min_gravityforms_version = "1.7.9999";
		protected $_slug = "itsg_gf_infobox";
		protected $_full_path = __FILE__;
		protected $_title = "Infobox field for Gravity Forms";
		protected $_short_title = "Infobox field";

		public function init(){
			parent::init();
			add_filter( 'gform_submit_button', array( $this, 'form_submit_button' ), 10, 2);
        } // END init

		// Add the text in the plugin settings to the bottom of the form if enabled for this form
		function form_submit_button( $button, $form ){
			$settings = $this->get_form_settings( $form );
			if ( true == rgar( $settings, 'enabled' ) ){
				$text = $this->get_plugin_setting('mytextbox');
				$button = "<div>{$text}</div>" . $button;
			}
			return $button;
		} // END form_submit_button

		// add the options
		public function plugin_settings_fields() {
            return array(
				array(
                    'title'  => __( 'Formatting and Styles', 'gravity-forms-infobox-field' ),
                    'fields' => array(
						array(
                            'label'   => __( 'Include CSS styles', 'gravity-forms-infobox-field' ),
                            'type'    => 'checkbox',
                            'name'    => 'includecss',
                            'tooltip' => __( 'This option allows you to control whether to use the CSS styles provided in the plugin. If this is not enabled you can apply styles through your theme.', 'gravity-forms-infobox-field' ),
                            'choices' => array(
                                array(
                                    'label' => __( 'Yes', 'gravity-forms-infobox-field' ),
                                    'name'  => 'includecss',
									'default_value' => true
                                )
                            )
                        )
					)
				)
			);
        } // END plugin_settings_fields

		public function scripts() {
			$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';
			$version = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? mt_rand() : $this->_version;

			$scripts = array(
				array(
					'handle'    => 'itsp_infobox_script',
					'src'       => $this->get_base_url() . "/js/itsp_infobox_script{$min}.js",
					'version'   => $version,
					'deps'      => array( 'jquery' ),
					'enqueue'   => array( array( $this, 'requires_script' ) ),
					'in_footer' => true,
				),
			);

			return array_merge( parent::scripts(), $scripts );
		} // END scripts

		public function requires_script( $form, $is_ajax ) {
			if ( ! $this->is_form_editor() && is_array( $form ) ) {
				foreach ( $form['fields'] as $field ) {
					if ( 'Infobox' == $field->type ) {
						return true;
					}
				}
			}

			return false;
		} // END requires_script

		public function styles() {
			$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';
			$version = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? mt_rand() : $this->_version;

			$styles = array(
				array(
					'handle'  => 'itsp_infobox_css',
					'src'     => $this->get_base_url() . "/css/itsp_infobox_css{$min}.css",
					'version' => $version,
					'media'   => 'screen',
					'enqueue' => array( array( $this, 'requires_stylesheet' ) ),
				),
			);

			return array_merge( parent::styles(), $styles );
		} // END styles

		public function requires_stylesheet( $form, $is_ajax ) {
			$infobox_options = ITSP_GF_Infobox::get_options();
			if ( rgar( $infobox_options, 'includecss' ) && $this->requires_script( $form, $is_ajax ) ) {
				return true;
			}

			return false;
		} // END requires_stylesheet
    }
    new ITSG_GF_Infobox_Settings();
}