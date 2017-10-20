// #input_7_4 is first text box, #input_7_3 is second text box
// #input_7_1 is first dropdown, #input_7_2 is second dropdown

//jQuery("#input_7_4").change(function() {
//    jQuery("#input_7_3").val(jQuery("#input_7_4").val());
//});

// Send the selected value of first dropdown into text of first text box

jQuery("#input_7_1").change(function() {
    
/*    function changeA() {
    alert("Change A");
    var selectbox = jQuery('#input_7_2');
    selectbox.empty();
    var list = '';
    list += '<option value="A1"> A1</option>';
    list += '<option value="A2"> A2</option>';
    list += '<option value="A3"> A3</option>';
    selectbox.html(list);
}
*/
/*
    function changeB() {
    jQuery("#input_7_2").empty();
    var list = '';
    list += '<option value="B1"> B1</option>';
    list += '<option value="B2"> B2</option>';
    jQuery("#input_7_2").html(list);
    }
*/
    var text = jQuery("#input_7_1 option:selected" ).text();
    jQuery("#input_7_2").html(options_for(text));
    jQuery("#input_7_4").val(text);
});

function options_for(text) {
    var options = {
        "A": ["A1", "A2", "A3"], 
        "B": ["B1", "B2"]
    };
    var list = '';
    var opts = options[text];
    for (var i = 0; i < opts.length; i++) {
        list += '<option value="' + opts[i] + '">' + opts[i] + '</option>';
    }
    return(list);
}



