<?php

add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );

function enqueue_parent_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
}

function test_js() {
    wp_enqueue_script( 'custom_js', get_stylesheet_directory_uri() . '/js/test.js', array( 'jquery' ), '1.0', true );
}

function electives_js() {
     wp_enqueue_script( 'electives_js', get_stylesheet_directory_uri() . '/js/electives.js', array( 'jquery' ), '1.0', true );
}

add_action('wp_enqueue_scripts', 'test_js');
add_action('wp_enqueue_scripts', 'electives_js');

/*************************************************************************/
/*  Gets rid of the offensive Howdy bar at the top of each page! */

//show_admin_bar(false);
//add_filter('show_admin_bar', '__return_false');

/********************************/
// IDs for all forms and views

function id_for($which) {
    $ids = array(
            "contact_form" => 1,
            "contact_edit_view" => 62,
            "registration_form" => 2,
            "registration_edit_view" => 106,
            "online_payment_form" => 8,
            "check_payment_form"  => 9,
            "instrument_form" => 3,
            "elective_form" => 11,
            "morning_ensemble_form" => 6,
            "morning_ensemble_edit_view" => 147,
            "afternoon_ensemble_form" => 10,
            "afternoon_ensemble_edit_view" => 189,
            "elective_selection_form" => 12,
            "elective_selection_edit_view" => 213);
    return $ids[$which];
}

function find_field($form, $field_id) {
    foreach($form['fields'] as $field) if ($field->id == $field_id) return $field;
    return null;
}

/***************************************************/
/*  Registration form helpers -- allow other forms need to get instrument
    and total */

function registration_form() { return entry("registration_form"); }

function registration_form_field_id($field_name) {
    $ids = array(
        "participation" => 41,
        "instrument" => 3,
        "total" => 36);
    return $ids[$field_name];
}

function registration_form_value($field_name) {
    return registration_form()[registration_form_field_id($field_name)];
}

//  Apparently total isn't really a field.  Go figure
function registration_form_total() {
    return registration_form()[registration_form_field_id("total")];
}

function primary_instrument() { return registration_form_value("instrument"); }
add_shortcode('primary_instrument', 'primary_instrument');

/********************************/
/* For example, get a link to the page for the "Contact Information Form" page */

function get_page_link_by_title($page_title) {
    $page = get_page_by_title($page_title);
    return get_page_link($page->ID);
}

/******************************/

function entries($form_name) {
    $form_id = id_for($form_name);
    $user_id = get_current_user_id();
    if ($user_id <= 0) return array();
    $entries = array();
    foreach(GFAPI::get_entries($form_id) as $entry)
        if ($entry["created_by"] == $user_id) $entries[] = $entry;
    return $entries;
}

function entry($form_name) {
    $entries = entries($form_name);
    $cnt = count($entries);
    if ($entries == null) return null;
    if (count($entries) == 0) return null;
    if (count($entries) > 1) throw new Exception("Too many entries for form $form_id");
    return $entries[0];
}

function has_entry($form_name) { return entry($form_name) != null; }

/**
 For user home page 
  Generic way to do create or edit given form ID and text for page.
  Used for registration, contact info, morning ensemble, afternoon ensemble, 
  elective.
  For example if user has filled out contact information, return home 
  page text and an edit button.  If the user has not filled out registration,
  return text and a create button.
  
**/

function create_or_edit_info($form_name, $view_name, $slug) {
    $entry = entry($form_name);  
    if ($entry == null) {
        $page_link = get_page_link_by_title($slug . " Form");
        return do_shortcode("[otw_shortcode_button href=$page_link size='medium' icon_position='left' shape='square']New $slug [/otw_shortcode_button]");
    } else {
        $view_id = id_for($view_name);
        $entry_id = $entry["id"];
        return do_shortcode("[gv_entry_link action='edit' entry_id=$entry_id view_id=$view_id link_atts='class=medium square otw-button']" . 
                            "Edit $slug" . "[/gv_entry_link]");
    }
}

function info_status_text($form_name, $slug) {
    if (has_entry($form_name)) return '<div style="color: green">Done</div>';
    else return '<div style="color: red">' . "No $slug" . '</div>';
}

/****************************************************************************/
/*  Contact status on user home  */

function has_contact_info() { return has_entry("contact_form");}

function create_or_edit_contact() { return create_or_edit_info("contact_form", "contact_edit_view", "Contact Information"); }
function contact_status_text() {    return info_status_text("contact_form", "Contact Information"); }

add_shortcode('create_or_edit_contact', 'create_or_edit_contact');
add_shortcode('contact_status_text', 'contact_status_text');

/****************************************************************************/
/*  Registration status on user home                                        */       

function has_registration() { return has_entry("registration_form");}
function create_or_edit_registration() { return create_or_edit_info("registration_form", "registration_edit_view", "Registration"); }
function registration_status_text() { return info_status_text("registration_form", "Registration"); }

add_shortcode('create_or_edit_registration', 'create_or_edit_registration');
add_shortcode('registration_status_text', 'registration_status_text');

/****************************************************************************/
/*  Morning ensemble status on user home                                        */       

function has_morning_ensemble() { return has_entry("morning_ensemble_form");}
function create_or_edit_morning_ensemble() { return create_or_edit_info("morning_ensemble_form", "morning_ensemble_edit_view", "Morning Ensemble"); }
function morning_ensemble_status_text() { return info_status_text("morning_ensemble_form", "Morning Ensemble"); }

add_shortcode('create_or_edit_morning_ensemble', 'create_or_edit_morning_ensemble');
add_shortcode('morning_ensemble_status_text', 'morning_ensemble_status_text');

/****************************************************************************/
/*  Afternoon ensemble status on user home                                        */       

function has_afternoon_ensemble() { return has_entry("afternoon_ensemble_form");}
function create_or_edit_afternoon_ensemble() { return create_or_edit_info("afternoon_ensemble_form", "afternoon_ensemble_edit_view", "Afternoon Ensemble"); }
function afternoon_ensemble_status_text() { return info_status_text("afternoon_ensemble_form", "Afternoon Ensemble"); }

add_shortcode('create_or_edit_afternoon_ensemble', 'create_or_edit_afternoon_ensemble');
add_shortcode('afternoon_ensemble_status_text', 'afternoon_ensemble_status_text');

/****************************************************************************/
/*  Elective selection status on user home                                        */       

function has_elective_selection() { return has_entry("elective_selection_form");}
function create_or_edit_elective_selection() { return create_or_edit_info("elective_selection_form", "elective_selection_edit_view", "Elective Selection"); }
function elective_selection_status_text() { return info_status_text("elective_selection_form", "Elective Selection"); }

add_shortcode('create_or_edit_elective_selection', 'create_or_edit_elective_selection');
add_shortcode('elective_selection_status_text', 'elective_selection_status_text');

/*****************************************************************/
/*  Balance                                                      */

function balance() { return charge_total() - payment_total(); }

add_shortcode('balance_string', 'balance_string');
function balance_string() { setlocale(LC_MONETARY, en_US.UTF-8); return money_format('$%.2n', balance()); }

function charge_total() {
    if(has_registration()) return registration_form_total(); else return 0;
}

function payment_total() {
    return online_payment_total() + check_payment_total(); 
}

function online_payment_total() {
    $total = 0;
    $user_id = get_current_user_id();
    $payment_amount_id = 9;
    foreach(GFAPI::get_entries(id_for("online_payment_form")) as $entry)
        if ($entry["created_by"] == $user_id) {
            if ($entry["is_fulfilled"] == 1) {
                $payment_amount = substr($entry[$payment_amount_id], 1);
                $total = $total + $payment_amount;
            }
        }
    return $total;
}

// Field 1 is the email;  field 2 is the amount

function check_payment_total() {
    $user_email = wp_get_current_user()->user_email;
    $total = 0;
    foreach(GFAPI::get_entries(id_for("check_payment_form")) as $entry)
        if ($entry[1] == $user_email) 
             $total += $entry[2];
    return $total;
}

/****************************************************************/
/*  Charges and payment section on user home page               */

function payment_status_text() {
    if (!has_registration()) return '<div style="color: green">No charges yet</div>';
    $balance = balance();
    if ($balance == 0) return '<div style="color: green">You have a zero balance</div>';
    setlocale(LC_MONETARY, en_US.UTF-8);
    $f = money_format('%.2n', $balance);
    return  '<div style="color: red">' . "You have a balance of $f" . '</div>';
}

function button_for_page($pagename) {
    $page_link = get_page_link_by_title($pagename);
    return do_shortcode("[otw_shortcode_button href=$page_link size='medium' icon_position='left' shape='square']" . $pagename . '[/otw_shortcode_button]');
}

function payment_options() {
    $ret = "";
    if (payment_total() > 0) $ret .= button_for_page("Payment Summary");
    if (balance() > 0)  $ret .= "</br>" . button_for_page("Pay Online") . "</br>" . button_for_page("Pay by Check");
    return $ret;
}

add_shortcode('payment_status_text', 'payment_status_text');
add_shortcode('payment_options',     'payment_options');

/****************************************************************************/
/*  Prepopulate primary instrument and morning ensemble choice    */

$morning_ensemble_form_index = id_for("morning_ensemble_form");
$instrument_field_id = 13;
$selection_group_field_id = 16;

add_filter("gform_field_value_morning_ensemble_instrument", 'populate_morning_ensemble_instrument');
add_filter("gform_field_value_morning_ensemble_selection_group", 'populate_morning_ensemble_selection_group');

function populate_morning_ensemble_instrument($value) { return registration_form_value("instrument"); }

function populate_morning_ensemble_selection_group($value) { 
    return morning_ensemble_selection_group(registration_form_value("instrument"));
}

/****************************************************************/
/*  Generic stuff for instruments    */

// These are positions of fields in the actual Instrument form
function instrument_form_field_id($field_name) {
    $ids = array("name" => 1, "morning_ensemble_selection_group" => 2, "evaluation_group" => 5);
    return $ids[$field_name];
}

function instruments() { return GFAPI::get_entries(id_for("instrument_form")); }

function find_instrument($instrument_name) {
    foreach(instruments() as $instrument)
        if ($instrument[instrument_form_field_id("name")] == $instrument_name)
            return $instrument;
    return null;
}

function instrument_names() {
    $names = array();
    foreach(instruments() as $instrument) $names[] =  $instrument[instrument_form_field_id("name")];
    return $names;
}

function instrument_attribute($instrument_name, $attribute_name) {
    $instrument = find_instrument($instrument_name);
    return $instrument[instrument_form_field_id($attribute_name)];
}

function morning_ensemble_selection_group($instrument_name) {
    return instrument_attribute($instrument_name, "morning_ensemble_selection_group");
}

function evaluation_selection_group($instrument_name) {
    return instrument_attribute($instrument_name, "evaluation_group");
}


/**********/
/*  Enables invisible labels in Gravity Forms */

add_filter( 'gform_enable_field_label_visibility_settings', '__return_true' );

/**********/
/*  Login redirect to userhome -- TODO: change for admin-like role */

function my_login_redirect( $redirect_to, $request, $user ) {
       return home_url("userhome");
}

/********************/
/*  Top menu customization for logged in / logged out */

function my_wp_nav_menu_args( $args = '' ) {
    if( is_user_logged_in() )
        $args['menu'] = 'top_menu_logged_in';
    else
        $args['menu'] = 'top_menu_logged_out';
    return $args;
}
add_filter( 'wp_nav_menu_args', 'my_wp_nav_menu_args' );

/*************************/
/*  Get rid of form title for Gravity View Edit Entry forms */

add_filter( 'gravityview_edit_entry_title', function($p) {return '';}, 10, 1);

/**************************/
/*  Make edit entry redirect to userhome */

add_action( 'gravityview/edit_entry/after_update', 'gravityview_redirect_after_update', 10, 2 );
        
function gravityview_redirect_after_update( $form, $entry_id ) {
    $userhome_page_id = 42;
    $permalink = get_permalink( $userhome_page_id );
?>
    <script>
        jQuery(document).ready( function() {
            window.location.replace( "<?php echo $permalink; ?>" );
        });
    </script>
    <?php
}

/****************************************/
/*  Validation for registration form -- make participant select an instrument */

$reg_form_id = id_for("registration_form");

add_filter( "gform_validation_$reg_form_id", 'validate_participant_has_instrument' );

function validate_participant_has_instrument( $validation_result ) {
    print("Validation</br>");
     $form = $validation_result['form'];
     $participation_field = null;
     $instrument_field = null;
     $participation_field_id = registration_form_field_id("participation");
     $instrument_field_id = registration_form_field_id("instrument");
     foreach( $form['fields'] as &$field ) {
         if ( $field->id == registration_form_field_id("participation")) $participation_field = $field;
         if ( $field->id == registration_form_field_id("instrument")) $instrument_field = $field;
     }
     
    $participation_field_value = rgpost( "input_{$participation_field->id}" );
    $instrument_field_value    = rgpost( "input_{$instrument_field->id}" );
 
 //  CAREFUL!  Negative Regexp match on Non Participant to determine Participant
 //     will break if registration form field values change.  Exact string matching
 //     not working because Gravity Forms putting price information into the field value
 
    if (!preg_match('/Non Participant/', $participation_field_value) && empty($instrument_field_value)) {
        $instrument_field->failed_validation = true;
        $instrument_field->validation_message = 'Participants must select a primary instrument';
        $validation_result['is_valid'] = false;
        $validation_result['form'] = $form;
     }
    return $validation_result;
}

/**********************************************************/
/*  Cancel on gravityforms edit goes back to userhome  */
function gv_my_edit_cancel_link( $back_link, $form, $entry, $view_id ) { return home_url("userhome"); }
add_filter( 'gravityview/edit_entry/cancel_link', 'gv_my_edit_cancel_link', 10, 4 );

/****************************************************************/
/*  Prepopulate instrument dropdowns (4) in afternoon ensemble selection  */

$afternoon_ensemble_form_id = id_for("afternoon_ensemble_form");

add_filter( "gform_pre_render_$afternoon_ensemble_form_id", 'populate_afternoon_ensemble_default_instrument' );
//GFCommon::log_debug("added hook for {$afternoon_ensemble_form_id}");

function populate_afternoon_ensemble_default_instrument($form) {
    //GFCommon::log_debug( __METHOD__ . "(): Called hook" );
    return populate_default_instrument($form, array(2, 35, 36, 37));
}

function populate_default_instrument($form, $field_ids ) {
    $primary_instrument = primary_instrument();
    //GFCommon::log_debug( __METHOD__ . "(): Populating with {$primary_instrument} event." );
    foreach( $form['fields'] as &$field )
        if( in_array($field->id, $field_ids))
            foreach( $field->choices as &$choice )
                if($choice['value'] == $primary_instrument) $choice['isSelected'] = true;
    return $form;
}

/********************************************************************/
/*   Self evaluation forms   */
// Forms, string eval 14, wind eval 13, 15 

function self_eval_url_for($instrument) {
    $eval_group = evaluation_selection_group($instrument);
    return site_url("$eval_group-self-evaluation-form") . "/?instrument=$instrument";
}

function self_eval($atts = [], $content=null, $tag='') {
    $instrument = $atts['instrument'] ? $atts['instrument'] : primary_instrument();
    $url = self_eval_url_for($instrument);
    return "<a href=\"$url\">Self Eval for $instrument</a>";
}
add_shortcode('self_eval', 'self_eval');