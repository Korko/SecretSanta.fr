$('.participant-add').on('click', function() {

    var el = newParticipant(0);

    document.getElementById("participants").getElementsByTagName("tbody")[0].appendChild(el);

});

$(document.body).on('click', '.participant-remove', function(event) {

    var participant = $(event.target).parents('.participant')[0];

    var index = Array.prototype.slice.call(document.getElementsByClassName("participant")).indexOf(participant);
    participant.parentNode.removeChild(participant);

    var parts = document.getElementsByClassName("participant"), select;
    for (var i = 0; i < parts.length; i++) {
        select = parts[i].getElementsByClassName("participant-partner")[0];
        if (parseInt(select.options[select.selectedIndex].value, 10) === index) {
            select.selectedIndex = 0;
        }

        for (var j = 0; j < select.options.length; j++) {
            if (parseInt(select.options[j].value, 10) === index) {
                select.removeChild(select.options[j]);
                break;
            }
        }
    }

});

$(document.body).on('blur', '.participant-name', function(event) {

    var participant = $(event.target).parents('.participant')[0];

    // Update for all participants the "participant-partner" so that the name is corrected or added
    var index = Array.prototype.slice.call(document.getElementsByClassName("participant")).indexOf(participant);
    var parts = document.getElementsByClassName("participant"), select, option, founded;
    for (var i = 0; i < parts.length; i++) {
        if (i === index) continue;

        select = parts[i].getElementsByClassName("participant-partner")[0];
        founded = false;

        for (var j = 0; j < select.options.length; j++) {
            if (parseInt(select.options[j].value, 10) === index) {
                select.options[j].text = (j+1)+'. '+participant.getElementsByClassName("participant-name")[0].value;
                founded = true;
            }
        }

        if (!founded) {
            option = document.createElement("option");
            option.value = index;
            option.text = (index+1)+'. '+participant.getElementsByClassName("participant-name")[0].value;
            select.appendChild(option);
        }
    }

});

$('#form-mail-group').prop('disabled', 'disabled');
$(document.body).on('blur', '.participant-email', function(event) {

    $('#form-mail-group').prop('disabled', 'disabled');

    $('.participant-email').each(function() {
        if($(this).val()){
           $('#form-mail-group').prop('disabled', false);
           return false;
        }
    });

});

$('#form-sms-group').prop('disabled', 'disabled');
$(document.body).on('blur', '.participant-phone', function(event) {

    $('#form-sms-group').prop('disabled', 'disabled');

    $('.participant-phone').each(function() {
        if($(this).val()){
           $('#form-sms-group').prop('disabled', false);
           return false;
        }
    });

});

// Validate a phone number field
var locked = false;
$("#form form").submit(function(e) {

    var postData = $(this).serializeArray();
    var formURL = $(this).attr("action");
    if(!locked) {
        locked = true;
        $.ajax({
            url : formURL,
            type: "POST",
            data : postData,
            success:function(data, textStatus, jqXHR) {
                alertify.alert(jqXHR.responseJSON[0]);
                $('#form form fieldset').prop('disabled', 'disabled');
                $('#errors-wrapper').hide();
                $('#success-wrapper').show();
                $.scrollTo($('#success-wrapper'), 800, {offset: -120});
            },
            error: function(jqXHR, textStatus, errorThrown) {
                grecaptcha.reset();
                $('#errors').html('');
                $(jqXHR.responseJSON).each(function(field, errors) {
                    $.each(errors, function(k, err) {
                        $('#errors').append('<li>'+err+'</li>');
                    });
                });
                $('#errors-wrapper').show();
                $.scrollTo($('#errors-wrapper'), 800, {offset: -120});
                locked = false;
            }
        });
    }
    e.preventDefault(); //STOP default action

    return false;

});

function newParticipant() {

    // Clone the first participant
    var el = document.getElementsByClassName("participant")[0].cloneNode(true);

    // Reset inputs
    var inputs = el.getElementsByTagName("input");
    for (i in inputs) {
        inputs[i].value = "";
    }

    // Get the max counter and refill the select "participant-partner" with correct values
    var max = 0;
    var select = el.getElementsByClassName("participant-partner")[0];
    var parts = document.getElementsByClassName("participant");
    for (var i = 0; i < parts.length; i++) {
        max = Math.max(max, parts[i].getElementsByClassName('counter')[0].innerHTML);
        if (parts[i].getElementsByClassName("participant-name")[0].value === '') continue;

        founded = false;

        for (var j = 0; j < select.options.length; j++) {
            if (parseInt(select.options[j].value, 10) === i) {
                select.options[j].text = (j+1)+'. '+parts[i].getElementsByClassName("participant-name")[0].value;
                founded = true;
            }
        }

        if (!founded) {
            option = document.createElement("option");
            option.value = i;
            option.text = (i+1)+'. '+parts[i].getElementsByClassName("participant-name")[0].value;
            select.appendChild(option);
        }
    }

    // Sort the "participant-partner" select so that values are well ordered
    var options = $(select.options);
    options.sort(function(a,b) {
        if (a.value > b.value) return 1;
        else if (a.value < b.value) return -1;
        else return 0;
    });
    $(select).empty().append(options);

    // Set the counter
    el.getElementsByClassName('counter')[0].innerHTML = max + 1;

    return el;

}
