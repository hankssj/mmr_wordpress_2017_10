
// <script type="text/javascript">
// jQuery(document).bind('gform_post_render', function(){

//     function options_for(text) {
//         var options = {
//         "They're Not Dead Yet: Choral Music of Living Composers": ["Voice-Alto","Voice-Bass","Voice-Soprano","Voice-Tenor"],
//         "Madrigal Madness": ["Voice-Alto","Voice-Bass","Voice-Soprano","Voice-Tenor"],
//         "Womens Anything But Classical Vocal Ensemble": ["Voice-Alto","Voice-Soprano"],
//         "Fingerboard Geography for Violinists": ["Violin"],     			
//         "I Hate to Read Music, But I Love to Sing!": ["Voice-Alto","Voice-Bass","Voice-Soprano","Voice-Tenor"],
//         "Great American Songbook (All Voices)": ["Voice-Alto","Voice-Bass","Voice-Soprano","Voice-Tenor"],
//         "Clarinet Choir": ["Clarinet","Clarinet-Bass"],
//         "Musicianship for Singers": ["Voice-Alto","Voice-Bass","Voice-Soprano","Voice-Tenor"],
//         "Saxophone Ensemble": ["Saxophone-Alto","Saxophone-Baritone","Saxophone-Soprano","Saxophone-Tenor"],
//         "Jazz Big Band": ["Double Bass","Percussion","Piano","Saxophone-Alto","Saxophone-Baritone","Saxophone-Soprano","Saxophone-Tenor","Trombone","Trumpet"],
//         "Brass Ensemble": ["Euphonium","Horn","Trombone","Trumpet","Tuba"],
//         "Afternoon Elective Orchestra": ["Bassoon","Cello","Clarinet","Clarinet-Bass","Double Bass","Flute","Harp","Horn","Oboe","Percussion","Trombone","Trumpet","Tuba","Viola","Violin"],
//         "Percussion Ensemble": ["Percussion"],
//         "Flute Choir": ["Flute"],
//         "Baroque Orchestra for Strings": ["Cello","Double Bass","Viola","Violin"],
//         "Beginning Voice Class": ["Voice-Alto","Voice-Bass","Voice-Soprano","Voice-Tenor"]
//         };
//         var list = '';
//         if (!options.hasOwnProperty(text)) {
//             list += '<option value=\"No selection required\" isSelected=\"selected\">No selection required</option>';
//         } else {
//             var opts = options[text];
//             for (var i = 0; i < opts.length; i++) {
//                 list += '<option value="' + opts[i] + '">' + opts[i] + '</option>';
//             }
//         }
//         return list;
//     }

// jQuery("#input_12_7").change(function() {
//         var elective_text = jQuery("#input_12_7").val();
//         jQuery("#input_12_8").empty();
//         jQuery("#input_12_8").html(options_for(elective_text));
//     });

// jQuery("#input_12_12").change(function() {
//         var elective_text = jQuery("#input_12_12").val();
//         jQuery("#input_12_3").empty();
//         jQuery("#input_12_3").html(options_for(elective_text));
//     });

// jQuery("#input_12_17").change(function() {
//         var elective_text = jQuery("#input_12_17").val();
//         jQuery("#input_12_15").empty();
//         jQuery("#input_12_15").html(options_for(elective_text));
//     });

// jQuery("#input_12_14").change(function() {
//         var elective_text = jQuery("#input_12_14").val();
//         jQuery("#input_12_18").empty();
//         jQuery("#input_12_18").html(options_for(elective_text));
//     });

// jQuery("#input_12_20").change(function() {
//         var elective_text = jQuery("#input_12_20").val();
//         jQuery("#input_12_21").empty();
//         jQuery("#input_12_21").html(options_for(elective_text));
//     });

//     function set_selection(elective_id, instrument_id) {
//         var elective_text = jQuery(elective_id).val();
//         var selected = jQuery(instrument_id + ' option:selected').text();
//         jQuery(instrument_id).html(options_for(elective_text));
//         jQuery(instrument_id).val(selected);
//     }
    
//     set_selection("#input_12_7",  "#input_12_8");
//     set_selection("#input_12_12", "#input_12_3");
//     set_selection("#input_12_17", "#input_12_15");
//     set_selection("#input_12_14", "#input_12_18");
//     set_selection("#input_12_20", "#input_12_21");

// });
// </script>