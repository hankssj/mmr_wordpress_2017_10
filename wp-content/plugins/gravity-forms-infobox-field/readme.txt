=== Infobox field for Gravity Forms ===
Contributors: ovann86
Donate link: https://www.itsupportguides.com/donate/
Tags: gravity forms, wcag, accessibility, forms
Requires at least: 4.7
Tested up to: 4.8
Stable tag: 1.5.3
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Extends the Gravity Forms plugin - adding an infobox field that can be used to display information throughout the form.

== Description ==

> This plugin is an add-on for the <a href="https://www.e-junkie.com/ecom/gb.php?cl=54585&c=ib&aff=299380" target="_blank">Gravity Forms</a> (affiliate link) plugin. If you don't yet own a license for Gravity Forms - <a href="https://www.e-junkie.com/ecom/gb.php?cl=54585&c=ib&aff=299380" target="_blank">buy one now</a>! (affiliate link)

This plugin extends the Gravity Forms plugin - adding an infobox field that can be used to display information throughout the form.

Infoboxes can be placed anywhere in a form, like you would any other form field.

Each infobox can be styled using the 'Infobox type' field, options include:

* help
* note
* critical
* warning
* information
* highlight

> See a demo of this plugin at [http://demo.itsupportguides.com/gravity-forms-infobox-field/](http://demo.itsupportguides.com/gravity-forms-infobox-field/ "demo website")

**Disclaimer**

*Gravity Forms is a trademark of Rocketgenius, Inc.*

*This plugins is provided “as is” without warranty of any kind, expressed or implied. The author shall not be liable for any damages, including but not limited to, direct, indirect, special, incidental or consequential damages or losses that occur out of the use or inability to use the plugin.*

== Installation ==

1. This plugin requires the Gravity Forms plugin, installed and activated
2. Install plugin from WordPress administration or upload folder to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in the WordPress administration
4. Open the form you want to add an infobox to
5. In the 'Standard fields' section you will find a new field option - 'Infobox'
6. Add the field to the location required
7. Use the 'Field Label' field for the infobox title
8. Use the 'Infobox type' field to select a style
9. Use the 'Description' field for the infobox text
10. Use the 'More information' field for additional infobox text - this text will be hidden by default and expanded when the user clicks on the 'More information' link

== Frequently Asked Questions ==

= How do I apply my own styles? =

You can override infobox styles by applying your own CSS class name to the field then add desired CSS code to your theme's CSS file.

For example, if you apply the CSS class name of custombox - you would add the following CSS to your theme's CSS file.

.custombox.gform_infobox {
    background: white;
}

This will give this infobox a white background.

= How to use with Gravity PDF (previously Gravity Forms PDF extended) =

To exclude the infoboxes from PDF's created using Gravity PDF ensure that the Infobox has a Custom CSS Class of 'exclude'.

This can be added in the form editor in the 'Appearance' tab.

== Screenshots ==

1. This screen shot shows several infoboxes in a form. There are six different styles that can selected. More information can be displayed in a text area that is hidden until the user clicks on the 'More information' link.
2. This screen shot shows the infobox options in the form editor.
3. This screen shot shows the infobox button, in the 'Standard Fields' section.

== Changelog ==

= 1.5.3 =
* Maintenance: tidy php (use object notation for $field)

= 1.5.2 =
* Maintenance: Improve CSS that handles dot points and numbered lists inside an infobox

= 1.5.1 =
* Maintenance: Tweak CSS that handles dot points inside an infobox
* Maintenance: Add ability to run infoboxes in forms embedded into the WordPress dashboard through the Gravity_Form() function

= 1.5.0 =
* Fix: Resolve JavaScript error when adding new infobox in the form editor
* Fix: Fix issue with Infobox printing at top of entry print view
* Maintenance: Add minified JavaScript and CSS
* Maintenance: Confirm working with WordPress 4.6.0 RC1

= 1.4.0 =
* FEATURE: Added menu option to disable infobox styles for all forms (Gravity Forms -> Settings -> Infobox)
* MAINTENANCE: Moved JavaScript to external file.
* MAINTENANCE: Changed CSS and JavaScript to be enqueued using the Gravity Forms addon framework
* MAINTENANCE: Tested against Gravity Forms 2.0 RC1
* MAINTENANCE: Tested against Gravity PDF 4.0 RC4
* MAINTENANCE: Added blank index.php file to plugin directory to ensure directory browsing does not occur. This is a security precaution.

= 1.3.2 =
* FIX: Add support for listed items (numbered lists, dot point lists) inside infobox text.

= 1.3.1 =
* MAINTENANCE: Tidy up code, working towards WordPress standards
* MAINTENANCE: Improve translation support
* FIX: Make 'Information' the default infobox type - if no type is chosen the infobox will be an 'Information' type infobox

= 1.3.0 =
* FIX: Fix issue with CSS and images not loading - in update 1.2.5 the new directories did not go out with the update.
* FEATURE: Add support for multisite WordPress installations.

= 1.2.5 =
* FIX: Add 'data-type' property to Infobox button in form editor - provides support for old and new versions of Gravity Forms.
* MAINTENANCE: Place CSS and images into their own directories.
* MAINTENANCE: Change CSS to load using wp_enqueue_style
* MAINTENANCE: Change name from 'Gravity Forms - Infobox field' to 'Infobox field for Gravity Forms'
* MAINTENANCE: Resolve various PHP errors that were appearing in debug mode, but did not affect functionality.
* MAINTENANCE: Change constructor so plugin load is delayed using the 'plugins_loaded' action - this ensures the plugin loads after Gravity Forms has loaded and functions correctly.

= 1.2.4 =
* FIX: 'More information' link was not keyboard accessible.

= 1.2.3 =
* FIX: Modified JavaScript for 'More Information' click so that it will work in older versions of Internet Explorer (e.g. IE9).

= 1.2.2 =
* IMPROVEMENT: Added default 'Custom CSS Class' of 'exclude' so that the Infobox field does not appear in PDF's created using the Gravity PDF plugin (previously Gravity Forms PDF extended).

= 1.2.1 =
* FIX: 'Infobox type' and 'More information' fields not displaying saved data on reload.
* IMPROVEMENT: Changed More information text area to display with inline-block, rather than block - this allows for more complicated HTML to be included in the More information text area, such as tables.
* IMPROVEMENT: Added CSS styles to give tables inside of the More information text area a white background and slight padding on th and td.

= 1.0 =
* First public release.

== Upgrade Notice ==

= 1.2.1 =
Fixes 'Infobox type' and 'More information' fields not displaying saved data on reload.