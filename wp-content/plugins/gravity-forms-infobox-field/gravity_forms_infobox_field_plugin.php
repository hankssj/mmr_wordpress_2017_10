<?php
/*
Plugin Name: Infobox field for Gravity Forms
Version: 1.5.3
Description: Extends the Gravity Forms plugin, adding an infobox field that can be used to display information throughout the form.
Author: Adrian Gordon
Author URI: http://www.itsupportguides.com
License: GPL2
Text Domain: gravity-forms-infobox-field

------------------------------------------------------------------------
Copyright 2015 Adrian Gordon

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

if ( ! defined(  'ABSPATH' ) ) {
	die();
}

load_plugin_textdomain( 'gravity-forms-infobox-field', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );

add_action( 'admin_notices', array( 'ITSP_GF_Infobox', 'admin_warnings' ), 20 );

if ( !class_exists('ITSP_GF_Infobox') ) {
    class ITSP_GF_Infobox {

		private static $name = 'Infobox field for Gravity Forms';
		private static $slug = 'gravity-forms-infobox-field';

        /**
         * Construct the plugin object
         */
        public function __construct() {
			// register plugin functions through 'gform_loaded' -
			// this delays the registration until Gravity Forms has loaded, ensuring it does not run before Gravity Forms is available.
            add_action( 'gform_loaded', array( $this, 'register_actions' ) );

        } // END __construct

		/*
         * Register plugin functions
         */
		function register_actions() {
		// register actions
            if ( self::is_gravityforms_installed() ) {

				// addon framework
				require_once( plugin_dir_path( __FILE__ ).'gravity_forms_infobox_field_addon.php' );

				//start plug in
				add_filter( 'gform_add_field_buttons', array( $this, 'infobox_add_field' ) );
				add_filter( 'gform_field_type_title' , array( $this, 'infobox_title' ), 10, 2 );
				add_action( 'gform_editor_js', array( $this, 'infobox_editor_js' ) );
				add_action( 'gform_field_standard_settings', array( $this, 'infobox_settings_type' ) , 10, 2 );
				add_action( 'gform_field_standard_settings', array( $this, 'infobox_settings_more_info' ), 10, 2 );
				add_filter( 'gform_tooltips', array( $this, 'infobox_tooltip_more_info' ) );
				add_action( 'gform_field_css_class', array( $this, 'infobox_custom_class' ), 10, 3 );
				add_filter( 'gform_field_content', array( $this, 'infobox_display_field' ), 10, 5 );

				// patch to allow JS and CSS to load when loading forms through wp-ajax requests
				add_action( 'gform_enqueue_scripts', array( $this, 'enqueue_scripts' ), 90, 2 );
			}
		} // END register_actions

	/**
	 * BEGIN: patch to allow JS and CSS to load when loading forms through wp-ajax requests
	 *
	 */

		/*
         * Enqueue JavaScript to footer
         */
		public function enqueue_scripts( $form, $is_ajax ) {
			if ( $this->requires_scripts( $form, $is_ajax ) ) {
				$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';

				wp_enqueue_script( 'itsp_infobox_script', plugins_url( "/js/itsp_infobox_script{$min}.js", __FILE__ ), array( 'jquery' ) );

			}

			if ( $this->requires_scripts( $form, $is_ajax ) ) {
				$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';

				wp_enqueue_style( 'itsp_infobox_css', plugins_url( "/css/itsp_infobox_css{$min}.css", __FILE__ ) );

			}
		} // END datepicker_js

		public function requires_scripts( $form, $is_ajax ) {
			if ( is_admin() && ! GFCommon::is_form_editor() && is_array( $form ) ) {
				foreach ( $form['fields'] as $field ) {
					if ( 'Infobox' == $field->type ) {
						return true;
					}
				}
			}

			return false;
		} // END requires_scripts

		public function requires_stylesheet( $form, $is_ajax ) {
			$infobox_options = ITSP_GF_Infobox::get_options();
			if ( true == rgar( $infobox_options, 'includecss' ) && $this->requires_script( $form, $is_ajax ) ) {
				return true;
			}

			return false;
		} // END requires_stylesheet

	/**
	 * END: patch to allow JS and CSS to load when loading forms through wp-ajax requests
	 *
	 */

        /**
         * Add infobox field to 'standard fields' group in Gravity Forms forms editor
         */
        function infobox_add_field( $field_groups ) {
            foreach ( $field_groups as &$group ) {
                if ( 'standard_fields' == $group['name'] ) {
                    $group['fields'][] = array(
                        'class'		=> 'button',
                        'value'		=> __( 'Infobox', 'gravity-forms-infobox-field' ),
						'data-type'	=> 'Infobox',
                    );
                    break;
                }
            }
            return $field_groups;
        } // END infobox_add_field

        /**
         * Add title to infobox field, displayed in Gravity Forms forms editor
         */
        function infobox_title( $title, $field_type ) {
            if ( 'Infobox' == $field_type ) {
                return 'Infobox';
			} else {
				return $title;
			}
        } // END infobox_title

        /**
         * JavaSript to add field options to infoxbox field in the Gravity forms editor
         */
        function infobox_editor_js() {
		?>
		<script>
			jQuery( document ).ready( function($) {
				// standard field options
				fieldSettings['Infobox'] = '.label_setting, .description_setting, .css_class_setting, .infobox_type_field_setting, .infobox_more_info_field_setting, .conditional_logic_field_setting';

				//custom field options
				jQuery( document ).bind( 'gform_load_field_settings', function( event, field, form ){
					if ( 'Infobox' == field['type'] ) {
						var infobox_type_value = field['itsg_list_field_character_limit_format'] ? field['itsg_list_field_character_limit_format'] : 'info';
						jQuery( '#infobox_type_field_setting' ).val( infobox_type_value );
						jQuery( '#infobox_more_info_field' ).val( field['infobox_more_info_field'] == undefined ? '' : field['infobox_more_info_field'] );
							if ( '' == jQuery( '#field_css_class' ).val()  ) {
								jQuery( '#field_css_class' ).val( 'exclude' );
							}
					}
				});
			});

		</script>
		<?php
        } // END infobox_editor_js

        /**
         * Add infobox 'Type' field, displayed in Gravity Forms forms editor
         */
        function infobox_settings_type( $position, $form_id ) {
            // Create settings on position 50 (right after Field Label)
            if ( 50 == $position ) {
?>
			<li class="infobox_type_field_setting field_setting">
				<label for="infobox_type_field">
				<?php esc_attr_e( 'Infobox type', 'gravity-forms-infobox-field' ); ?>
				</label>
				<select id="infobox_type_field_setting" onBlur="SetFieldProperty('infobox_type_field_setting', this.value);">
					<option value="help"><?php esc_attr_e( 'Help', 'gravity-forms-infobox-field' ); ?></option>
					<option value="note"><?php esc_attr_e( 'Note', 'gravity-forms-infobox-field' ); ?></option>
					<option value="critical"><?php esc_attr_e( 'Critical', 'gravity-forms-infobox-field' ); ?></option>
					<option value="warning"><?php esc_attr_e( 'Warning', 'gravity-forms-infobox-field' ); ?></option>
					<option value="info"><?php esc_attr_e( 'Information', 'gravity-forms-infobox-field' ); ?></option>
					<option value="highlight"><?php esc_attr_e( 'Highlight', 'gravity-forms-infobox-field' );?></option>
				</select>
				</li>
			<?php
            }
        } // END infobox_settings_type

		/**
         * Add infobox 'More information' field, displayed in Gravity Forms forms editor
         */
        function infobox_settings_more_info( $position, $form_id ) {
            // Create settings on position 50 (right after Field Label)
            if ( 1430 == $position ) {
?>
			<li class="infobox_more_info_field_setting field_setting">
				<label for="infobox_more_info_field">
				<?php _e( 'More information', 'gravity-forms-infobox-field' ); ?>
				<?php gform_tooltip( 'infobox_more_info_field_tooltip' );?>
				</label>
				<textarea id="infobox_more_info_field" class="fieldwidth-3 fieldheight-2" onchange="SetFieldProperty('infobox_more_info_field', this.value);"></textarea>
			</li>


			<?php

            }
        } // END infobox_settings_more_info

		/**
         * Add tooltip for 'More information' field
         */
		function infobox_tooltip_more_info( $tooltips ) {
			$tooltips['infobox_more_info_field_tooltip'] = '<h6>' . esc_attr__( 'More information', 'gravity-forms-infobox-field' ) .'</h6>' . esc_attr__( 'Text will be display in an hidden but expandable section below the main description', 'gravity-forms-infobox-field' );
			return $tooltips;
		}

        /**
         * Add custom classes to infobox fields, controls CSS applied to field
         */
        public static function infobox_custom_class( $classes, $field, $form ) {
			if ( 'Infobox' == $field->type ) {
                $classes .= ' gform_infobox exclude';
				if ( 'help' == $field->infobox_type_field_setting ) {
					$classes .= ' gform_infobox_help';
				} elseif ( 'note' == $field->infobox_type_field_setting ) {
					$classes .= ' gform_infobox_note';
				} elseif ( 'critical' == $field->infobox_type_field_setting ) {
					$classes .= ' gform_infobox_critical';
				} elseif ( 'info' == $field->infobox_type_field_setting ) {
					$classes .= ' gform_infobox_information';
				} elseif ( 'warning' == $field->infobox_type_field_setting ) {
					$classes .= ' gform_infobox_warning';
				} elseif ( 'highlight' == $field->infobox_type_field_setting ) {
					$classes .= ' gform_infobox_highlight';
				} else {
					$classes .= ' gform_infobox_information';
				}
			}
			return $classes;
        } // END infobox_custom_class

        /**
         * Displays infobox field
         */
        public function infobox_display_field ( $content, $field, $value, $lead_id, $form_id ) {
			if ( ( GFCommon::is_entry_detail() || 'print-entry' == rgget( 'gf_page' ) ) && 'Infobox' == $field->type ) {
				return;
			}
			if ( ! GFCommon::is_form_editor() && ! GFCommon::is_entry_detail() && 'Infobox' == $field->type ) {
				$content = '';
				if ( isset( $field->label ) && '' <> $field->label ) {
					$content .= "<div class='gfield_label'>" . $field->label . "</div>";
				}
				if ( isset( $field->description ) && '' <> $field->description ) {
					$content .= "<div class='gfield_description'>" . $field->description . "</div>";
                }
                if ( isset( $field->infobox_more_info_field ) && '' <> $field->infobox_more_info_field ) {
                    if ( isset( $field->label ) && '' <> $field->label ) {
						$content .= "<div class='gfield_description gfield_infobox_more_info_" . $field->id . " gfield_infobox_more_info_button'><a class='target-self' href='javascript:void(0)' title='More information - ".$field->label."' aria-label='" . esc_attr__( 'More information', 'gravity-forms-infobox-field' ) . " - ".$field->label."'>" . esc_attr__( 'More information', 'gravity-forms-infobox-field') . "</a></div>";
					} else {
						$content .= "<div class='gfield_description gfield_infobox_more_info_" . $field->id . " gfield_infobox_more_info_button'><a class='target-self' href='javascript:void(0)'>" . esc_attr__( 'More information', 'gravity-forms-infobox-field' ) . "</a></div>";
					}
                    $content .= "<div style='display: none;' class='gfield_description gfield_infobox_more_info_" . $field->id . "_box'>" . $field->infobox_more_info_field . "</div>";

                }
            }
            return $content;
        } // END infobox_display_field

		/*
		 *   Handles the plugin options.
		 *   Default values are stored in an array.
		 */
		public static function get_options() {
			$defaults = array(
				'includecss' => true,
			);
			$options = wp_parse_args( get_option( 'gravityformsaddon_itsg_gf_infobox_settings' ), $defaults );
			return $options;
		} // END get_options

		/*
         * Warning message if Gravity Forms is installed and enabled
         */
		public static function admin_warnings() {
			if ( !self::is_gravityforms_installed() ) {
				printf(
					'<div class="error"><h3>%s</h3><p>%s</p><p>%s</p></div>',
						__( 'Warning', 'gravity-forms-infobox-field' ),
						sprintf ( __( 'The plugin %s requires Gravity Forms to be installed.', 'gravity-forms-infobox-field' ), '<strong>'.self::$name.'</strong>' ),
						sprintf ( esc_html__( 'Please %sdownload the latest version of Gravity Forms%s and try again.', 'gravity-forms-infobox-field' ), '<a href="https://www.e-junkie.com/ecom/gb.php?cl=54585&c=ib&aff=299380" target="_blank">', '</a>' )
					);
			}
		} // END admin_warnings

		/*
         * Check if GF is installed
         */
        private static function is_gravityforms_installed() {
			if ( !function_exists( 'is_plugin_active' ) || !function_exists( 'is_plugin_active_for_network' ) ) {
				require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
			}
			if ( is_multisite() ) {
				return ( is_plugin_active_for_network( 'gravityforms/gravityforms.php' ) || is_plugin_active( 'gravityforms/gravityforms.php' ) );
			} else {
				return is_plugin_active( 'gravityforms/gravityforms.php' );
			}
        } // END is_gravityforms_installed
    }
    $ITSP_GF_Infobox = new ITSP_GF_Infobox();
}