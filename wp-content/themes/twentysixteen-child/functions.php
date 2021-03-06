<?php

function enqueue_parent_styles() {wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );}
function electives_js() { wp_enqueue_script( 'electives_js', get_stylesheet_directory_uri() . '/js/electives.js', array( 'jquery' ), '1.0', true );}

add_action('wp_enqueue_scripts', 'enqueue_parent_styles' );
add_action('wp_enqueue_scripts', 'electives_js');

//function test_js() {
//    wp_enqueue_script( 'custom_js', get_stylesheet_directory_uri() . '/js/test.js', array( 'jquery' ), '1.0', true );/
//}
//add_action('wp_enqueue_scripts', 'test_js');
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
            "elective_selection_edit_view" => 213,
            "vocal_evaluation_form" => 13,
            "vocal_evaluation_edit_view" => 256,
            "string_evaluation_form" => 14,
            "string_evaluation_edit_view" => 258,
            "wind_evaluation_form" => 15,
            "wind_evaluation_edit_view" => 260
            );
    return $ids[$which];
}

function find_field($form, $field_id) {
    foreach($form['fields'] as $field) if ($field->id == $field_id) return $field;
    return null;
}

/***************************************************/
/*  Registration form helpers -- allow other forms need to get instrument and total */

function registration_form() { return entry("registration_form"); }

function registration_form_field_id($field_name) {
    $ids = array(
        "participation" => 63,
        "instrument" => 3,
        "total" => 36);
    return $ids[$field_name];
}

function registration_form_value($field_name) { return registration_form()[registration_form_field_id($field_name)];}
// Get total by name, not by ID.  Why?
function registration_form_total() { return registration_form()[registration_form_field_id("total")]; }
function primary_instrument() { return registration_form_value("instrument"); }
add_shortcode('primary_instrument', 'primary_instrument');

/***************************************************/
/*  Contact form helpers -- allow other forms need to get user's name */

function contact_form() { return entry("contact_form"); }
function contact_form_field_id($field_name) {
    $ids = array(
        "first_name" => "1.3",
        "last_name" => "1.6");
    return $ids[$field_name];
}

function contact_form_value($field_name) { return contact_form()[contact_form_field_id($field_name)];}
function user_name() { return contact_form_value("first_name") . " " . contact_form_value("last_name");}

function user_role($user_id) {
    $user_meta=get_userdata($user_id);
    return $user_meta->roles[0]; 
}

function current_user_role() { return user_role(get_current_user_id()); }

/********************************/
/* For example, get a link to the page with title "Contact Information Form" */

function get_page_link_by_title($page_title) { return get_page_link(get_page_by_title($page_title)->ID); }

/******************************/

function entries($form_name) {
    $form_id = id_for($form_name);
    $user_id = get_current_user_id();
    if ($user_id <= 0) return array();
    $entries = array();
    foreach(GFAPI::get_entries($form_id, array(), array(), array('offset' => 0, 'page_size' => 200)) as $entry)
        if ($entry["created_by"] == $user_id) $entries[] = $entry;
    return $entries;
}

function entry($form_name) {
    $entries = entries($form_name);
    if ($entries == null) return null;
    if (count($entries) == 0) return null;
    if (count($entries) > 1) throw new Exception("Too many entries for form $form_id");
    return $entries[0];
}

function has_entry($form_name) { return entry($form_name) != null; }

/**
 For display elements on the user home page 
  Generic way to do create or edit links given a form ID and text for page.
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

add_shortcode('balance_string', 'balance_string');

function balance() { return charge_total() - payment_total(); }
function charge_total() { return has_registration() ? registration_form_total() : 0; }
function payment_total() { return online_payment_total() + check_payment_total(); }

function currency_string($amount) { setlocale(LC_MONETARY, en_US.UTF-8); return money_format('$%.2n', $amount);  }
function balance_string() { return currency_string(balance()); }
function charge_total_string() { return currency_string(charge_total()); }
function payment_total_string() { return currency_string(payment_total()); }

add_shortcode('balance_string', 'balance_string');
add_shortcode('charge_total_string', 'charge_total_string');
add_shortcode('payment_total_string', 'payment_total_string');


function online_payment_total() {
    $total = 0;
    $user_id = get_current_user_id();
    $payment_amount_id = 9;
    foreach(GFAPI::get_entries(id_for("online_payment_form"), array(), array(), array( 'offset' => 0, 'page_size' => 30 )) 
                as $entry)
        if ($entry["created_by"] == $user_id) {
            if ($entry["is_fulfilled"] == 1) {
                $payment_amount = substr($entry[$payment_amount_id], 1);
                $total = $total + $payment_amount;
            }
        }
    return $total;
}

function check_payment_total() {
    $user_email = wp_get_current_user()->user_email;
    $total = 0;
    foreach(GFAPI::get_entries(id_for("check_payment_form"), array(), array(), array( 'offset' => 0, 'page_size' => 30 )) as $entry)
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
    return do_shortcode("[otw_shortcode_button href=$page_link size='medium' icon_position='left' shape='square' style='width: 300px']" . $pagename . '[/otw_shortcode_button]');
}

function payment_options() {
    $ret = "";
    if (payment_total() > 0) $ret .= button_for_page("Charge and Payment Summary");
    if (balance() > 0)  $ret .= "</br>" . button_for_page("Pay Online") . "</br>" . button_for_page("Pay by Check");
    return $ret;
}

add_shortcode('payment_status_text', 'payment_status_text');
add_shortcode('payment_options',     'payment_options');

/**********************************************************************/
/*   Online payment options   */

function online_payment_options() {
    $str = "";
    $str .= "Your payment options";
    $str .= "<ul>";
    if (balance() > 150 && payment_total() < 150) $str .= "<li>Pay a deposit of $150.00</li>";
    $str .= "<li>Pay your balance of " . balance_string(). "</li>";
    $str .= "<li>Pay some other amount, up to " . balance_string() . "</li>";
    $str .= "</ul>";
    return $str;
}

add_shortcode('online_payment_options', 'online_payment_options');

/****************************************************************************/
/*  Prepopulate primary instrument and morning ensemble choice    */

add_filter("gform_field_value_morning_ensemble_instrument", 'populate_morning_ensemble_instrument');
add_filter("gform_field_value_morning_ensemble_selection_group", 'populate_morning_ensemble_selection_group');

function populate_morning_ensemble_instrument($value) { return registration_form_value("instrument"); }
function populate_morning_ensemble_selection_group($value) { return morning_ensemble_selection_group(registration_form_value("instrument")); }

/****************************************************************/
/*  Generic stuff for instruments    */

function instrument_form_field_id($field_name) {
    $ids = array("name" => 1, "morning_ensemble_selection_group" => 2, "evaluation_group" => 5);
    return $ids[$field_name];
}

function instruments() { return GFAPI::get_entries(id_for("instrument_form"), array(), array(), array('offset' => 0, 'page_size' => 200)); }

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

function morning_ensemble_selection_group($instrument_name) { return instrument_attribute($instrument_name, "morning_ensemble_selection_group");}
function evaluation_selection_group($instrument_name) { return instrument_attribute($instrument_name, "evaluation_group"); }

/**********/
/*  Enables invisible labels in Gravity Forms */

add_filter( 'gform_enable_field_label_visibility_settings', '__return_true' );

/**********/
/*  Login redirect to userhome -- TODO: change for admin-like role */

function login_redirect( $redirect_to, $request, $user ){ 
    if (user_role($user->ID) == 'faculty') 
        return home_url('facultyhome');
        else return home_url('userhome');
}

add_filter( 'login_redirect', 'login_redirect', 10, 3 );

/********************/
/*  Top menu customization for logged in / logged out */

function my_wp_nav_menu_args( $args = '' ) {
    $args['menu'] = is_user_logged_in() ? 'top_menu_logged_in' :'top_menu_logged_out';
    return $args;
}
add_filter( 'wp_nav_menu_args', 'my_wp_nav_menu_args' );

/*************************/
/*  Get rid of form title for Gravity View Edit Entry forms */

add_filter( 'gravityview_edit_entry_title', function($p) {return '';}, 10, 1);

/**************************/
/*  Make edit entry redirect to userhome on submit */

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
     $form = $validation_result['form'];  $participation_field = null;  $instrument_field = null;
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

/****************************************/
/*  Validation for online payment -- payment amount  */

add_filter( 'gform_field_validation_8_9', 'custom_validation', 10, 4 );
function custom_validation( $result, $value, $form, $field ) {
    $number = GFCommon::to_number( $value, '' );
    $balance = balance();
    if ( $result['is_valid'] && ($number <= 0 || $number > $balance)) {
        $result['is_valid'] = false;
        $result['message'] = 'Payment value must be between $1 and ' . balance_string();
    }
    return $result;
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
/*   Evaluation forms   */


// Names of instruments for which eval required, whether or not complete
function required_evaluations() {
    $required = array();
    if (has_registration()) $required[] = registration_form_value("instrument");
    $aft = entry("afternoon_ensemble_form");
    if ($aft) foreach(array(2, 37,35, 36) as $i) $required[] = $aft[$i];
    $elec = entry("elective_selection_form");
    if ($elec) foreach(array(8,3,15,18,21) as $i) $required[] = $elec[$i];
    $instrument_names = instrument_names();
    $returned = array();
    foreach ($required as $inst) {
        if (in_array($inst, $instrument_names) && !in_array($inst, $returned)) $returned[] = $inst;
    }
    return $returned;
}

// Names of instruments for which evaluations have been filled out
//  Instrument is in field 1 every time.  For now.

// Assumes no duplicates
function completed_evaluations() {
    $completed = array();
    foreach(array("vocal_evaluation_form", "string_evaluation_form", "wind_evaluation_form") as $form_name) {
        foreach(entries($form_name) as $entry) {
            $completed[] = $entry[1];
        }
    }
    return $completed;
}

function incomplete_evaluations() {
    $required = required_evaluations();  $completed = completed_evaluations();
    $incomplete = array();
    foreach($required as $r) if (!in_array($r, $completed)) $incomplete[] = $r;
    return $incomplete;
}
/*****/
function evaluation_entry($instrument) {
    foreach(array("vocal_evaluation_form", "string_evaluation_form", "wind_evaluation_form") as $form_name)
        foreach(entries($form_name) as $entry) if($entry[1] == $instrument) return $entry;
    return null;
}

function evaluation_form_link($instrument) {
    $eval_group = evaluation_selection_group($instrument);
    $user_name = user_name();
    return site_url("$eval_group-evaluation-form") . "/?username=$user_name&instrument=$instrument";
}

function evaluation_edit_link($instrument) {
    $eval_group = evaluation_selection_group($instrument);
    if ($eval_group == "wind")
        return eval_edit_link_for($instrument, "wind_evaluation_edit_view");
    else if ($eval_group == "string") 
        return eval_edit_link_for($instrument, "string_evaluation_edit_view");
    else if ($eval_group == "vocal")
        return eval_edit_link_for($instrument, "vocal_evaluation_edit_view");
    else throw new Exception("Bad evaluation group $eval_group for $instrument");
}

function eval_edit_link_for($instrument, $view_name) {
    $view_id = id_for($view_name);
    $entry_id = evaluation_entry($instrument)["id"];
    return do_shortcode("[gv_entry_link action='edit' entry_id=$entry_id view_id=$view_id]" . 
                            "Edit $instrument Evaluation" . "[/gv_entry_link]");
}

function evals_to_complete() {
    $copy = '<ul style="list-style-position: inside;">';
    foreach(incomplete_evaluations() as $inst) 
        $copy .= "<li><a href=\"" . evaluation_form_link($inst) . "\">" . "Complete $inst Evaluation" . "</a></li>";
    $copy .= "</ul></div>";
    return $copy;
}

function evals_to_edit() {
    $copy = '<ul style="list-style-position: inside;">';
    foreach(completed_evaluations() as $inst) 
        $copy .= "<li> " . evaluation_edit_link($inst) . "</li>";
    $copy .= "</ul></div>";
    return $copy;
}

add_shortcode('evals_to_complete', 'evals_to_complete');
add_shortcode('evals_to_edit', 'evals_to_edit');

/*********************************************************/
/*  Quantity and price fields on the registration form     */

$registration_form_id = id_for("registration_form");

// Eventually key on user role.  "regular" "faculty" "staff" "bmv"
function charge_type() { 
    $role = current_user_role();
    if ($role=="faculty") return "faculty";
    if ($role=="staff") return "staff";
    if ($role=="board" || $role=="major_volunteer") return "bmv";
    return "regular";
}

function populate_charge_type($value) { return charge_type();}
add_filter("gform_field_value_charge_type", 'populate_charge_type');

function populate_participation($value) { return (charge_type() == 'faculty') ? "Non Participant" : "Participant";}
add_filter("gform_field_value_participation", 'populate_participation');

// Registration fee / flat fee -- Faculty Staff 0 BMV 150 Regular 200
function populate_registration_fee_price($value) {
    $charge_type = charge_type();
    if ($charge_type == "regular") return "$200.00";
    if ($charge_type == "bmv") return "$150.00";
    if ($charge_type == "faculty" || $charge_type == "staff") return "$0.00";
    throw new Exception("Unknown charge type $charge_type");
}

function populate_registration_fee_quantity($value) {
    $charge_type = charge_type();
    return ($charge_type == "regular" || $charge_type == "bmv") ? 1 : 0;
}

add_filter("gform_field_value_registration_fee_price", 'populate_registration_fee_price');
add_filter("gform_field_value_registration_fee_quantity", 'populate_registration_fee_quantity');

add_filter( "gform_pre_render_$registration_form_id", 'set_registration_form_charges' );

function set_registration_form_charges($form) {
    $charge_type = charge_type();
    set_tuition($form, $charge_type);
    set_lodging($form, $charge_type);
    set_meals($form, $charge_type);
    return $form;
}

// For tuition / participant

function set_tuition($form, $charge_type) {
    $field = find_field($form, 63);
    $field->choices[0]["price"] = ($charge_type == "regular") ? "$400.00" : "$0.00";
}

//  For lodging double/single/none
//     faculty, staff => 0/100/0
//     regular, bmv => 240/340/0

function set_lodging($form, $charge_type) {
    $field = find_field($form, 30);
    if ($charge_type == "faculty" || $charge_type == "staff") {
       $field->choices[0]["price"] = "$0.00"; 
       $field->choices[1]["price"] = "$100.00";
       $field->choices[2]["price"] = "$0.00";
    } else {
        $field->choices[0]["price"] = "$240.00"; 
       $field->choices[1]["price"] = "$340.00";
       $field->choices[2]["price"] = "$0.00";
    }
}
  
 //  For meals three/two/none
//     faculty, staff => 0/0/0
//      regular, bmv => 260/200/0
 
function set_meals($form, $charge_type) {
    $field = find_field($form, 31);
    if ($charge_type == "faculty" || $charge_type == "staff") {
       $field->choices[0]["price"] = "$0.00"; 
       $field->choices[1]["price"] = "$0.00";
       $field->choices[2]["price"] = "$0.00";
    } else {
        $field->choices[0]["price"] = "$260.00"; 
       $field->choices[1]["price"] = "$200.00";
       $field->choices[2]["price"] = "$0.00";
    }
}

/*******************************************************/
/** Change text for no entries in multiple entry view -- 
 * used only for payments I hope
 */
 
function modify_gravitview_no_entries_text( $existing_text, $is_search=false) {return 'No payments recorded';}
add_filter( 'gravitview_no_entries_text', 'modify_gravitview_no_entries_text', 10, 2 );