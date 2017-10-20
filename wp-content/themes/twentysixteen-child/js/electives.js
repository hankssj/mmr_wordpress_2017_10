

function options_for(text) {
    var options = {
        "Flute Choir": ["Flute"],
        "Drum Circle": ["Percussion"],
        "Clarinet Choir": ["Clarinet", "Clarinet-Bass"],
        "Brass Ensemble": ["Trumpet", "Trombone", "Tuba", "Horm"],
        "Beginning Voice Class": ["Voice-Soprano", "Voice-Alto", "Voice-Tenor",	"Voice-Bass"],
        "Baroque Orchestra for Strings": ["Violin", "Viola", "Cello", "Bass"]
    };
    var list = '';
    if (!options.hasOwnProperty(text)) {
        list += '<option value=\"No selection required\" isSelected=\"selected\">No selection required</option>';
    } else {
        var opts = options[text];
        for (var i = 0; i < opts.length; i++) {
            list += '<option value="' + opts[i] + '">' + opts[i] + '</option>';
        }
    }
    return list;
}

/*************************************************************************************/

jQuery("#input_12_7").change(function() {
        var elective_text = jQuery("#input_12_7").val();
        var opts = options_for(elective_text);
        alert("change " + elective_text + " " + opts)
        jQuery("#input_12_8").html(options_for(elective_text));
    });

jQuery("#input_12_12").change(function() {
        var elective_text = jQuery("#input_12_12").val();
        var opts = options_for(elective_text);
        jQuery("#input_12_3").html(options_for(elective_text));
    });

jQuery("#input_12_17").change(function() {
        var elective_text = jQuery("#input_12_17").val();
        var opts = options_for(elective_text);
        jQuery("#input_12_15").html(options_for(elective_text));
    });

jQuery("#input_12_14").change(function() {
        var elective_text = jQuery("#input_12_14").val();
        var opts = options_for(elective_text);
        jQuery("#input_12_18").html(options_for(elective_text));
    });

jQuery("#input_12_20").change(function() {
        var elective_text = jQuery("#input_12_20").val();
        var opts = options_for(elective_text);
        jQuery("#input_12_21").html(options_for(elective_text));
    });

/*******************************************************/
$(document).load(function(){
    alert("ready");
    jQuery("#input_12_7").trigger("change");
    jQuery("#input_12_12").trigger("change");
    jQuery("#input_12_17").trigger("change");
    jQuery("#input_12_14").trigger("change");
    jQuery("#input_12_20").trigger("change");
});

