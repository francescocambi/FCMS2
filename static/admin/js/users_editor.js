/**
 * Created by Francesco on 31/03/15.
 */

var usernameField,
    passwordField,
    repeatPasswordField,
    emailField,
    expirationFields,
    enabledCheckbox,
    complexityBar;

var passwordComplexity = false;

$(function () {
   //Set up datetimepicker
    $('.datetimepicker').datetimepicker({
        formatTime: 'H:i',
        formatDate: 'd/m/Y',
        defaultDate: '+1970/02/01',
        defaultTime: '00:00',
        mask: '9999/19/39 29:59',
        lang: 'it',
        minDate: '-1970/01/01'
    });

    //Display enabled status
    enabledCheckbox = $('input[name="enabled"]');
    displayEnabledCheckboxStatus(enabledCheckbox);

    //Display expiration alert
    expirationFields = $('.expirationField');
    expirationFields.each(function (index, field) {
        displayExpirationAlert($(field), $(field).siblings('.expiration-alert'))
    });

    var form = $('form:first');

    usernameField = form.find('input[name="username"]');
    usernameField.blur(function (event) {
        if (usernameFieldCheck(usernameField))
            checkUsernameUnique(usernameField.val(), $('#id').val(),
                function (result) {
                    if (result) {
                        usernameField.css('border-color', '');
                    }
                    else
                        usernameField.css('border-color', 'red');
            });
    });

    passwordField = form.find('input[name="password"]');
    repeatPasswordField = form.find('input[name="repeatpassword"]');
    passwordField.blur(function () {
        passwordFieldsCheck(passwordField, repeatPasswordField);
    });
    repeatPasswordField.blur(function () {
        passwordFieldsCheck(passwordField, repeatPasswordField);
    });

    emailField = form.find('input[name="email"]');
    emailField.blur(function (event) {
        validateEmail($(event.target));
    });

    enabledCheckbox.change(function (event) {
        displayEnabledCheckboxStatus($(event.target));
    });

    expirationFields.blur(function (event) {
        var textField = $(event.target);
        var alert = textField.siblings('.expiration-alert');
        displayExpirationAlert(textField, alert);
    });

    complexityBar = $('#complexity-bar');

    //Set up password complexity bar
    var progress = $('#complexity-progress');
    progress.css('width', passwordField.width()+5);
    //progress.show();
    passwordField.complexify({
        strengthScaleFactor: 0.70
    }, function (valid, complexity) {
        complexityBar.css('width', complexity+'%');
        if (complexity >= 17) {
            complexityBar.text(Math.ceil(complexity)+'%');
        } else {
            complexityBar.text('');
        }
        passwordComplexity = valid;
        if (valid) {
            complexityBar.removeClass('progress-bar-danger');
            complexityBar.addClass('progress-bar-success');
        } else {
            complexityBar.removeClass('progress-bar-success');
            complexityBar.addClass('progress-bar-danger');
        }
    });

    //Set up form submit handler
    $('#save-all').click(function () {
        var id = $('#id').val();
        if (id == undefined) id = -1;

        checkUsernameUnique(usernameField.val(), id,
            function (result) {
                if (result && fieldValidation())
                    form.submit();
        });
    });


});

function displayEnabledCheckboxStatus(checkbox) {
    if (checkbox.prop('checked')) {
        $('#enabled-caption').text('ABILITATO').css('color', 'green');
    } else {
        $('#enabled-caption').text('DISABILITATO').css('color', 'red');
    }
}

function displayExpirationAlert(expirationField, alert) {
    var datetimeString = expirationField.val();
    var date = new Date(datetimeString);
    var now = new Date();

    if (date <= now) {
        alert.show();
    } else {
        alert.hide();
    }
}

function passwordFieldsCheck(field, repeatField) {
    //If password field is empty, password is valid
    //because server will ignore it
    if (field.val() == "") return true;

    //Checks that password is at least 8 characters long
    if (!passwordComplexity) {
        field.css('border-color', 'red');
        return false;
    } else {
        field.css('border-color', '');
    }

    //If repeat password field is empty skip this check
    //if (repeatField.val().length == 0) return false;

    //Checks that password fields have the same value
    if (field.val() != repeatField.val()) {
        field.css('border-color', 'red');
        repeatField.css('border-color', 'red');
        return false;
    } else {
        field.css('border-color', '');
        repeatField.css('border-color', '');
        return true;
    }
}

function usernameFieldCheck(field) {
    var username = field.val();

    if (username.length >= 6) {
        field.css('border-color', '');
        return true;
    } else {
        field.css('border-color', 'red');
        return false;
    }
}

function validateEmail(field) {
    var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    if (re.test(field.val())) {
        //Valid email
        field.css('border-color', '');
        return true;
    } else {
        //Not valid email
        field.css('border-color', 'red');
        return false;
    }
}

function checkExpirationFields(fields) {
    var result = true;

    fields.each(function (i, element) {
        var field = $(element);
        var dt = new Date(field.val());

        if (dt.getTime() > 0) {
            field.css('border-color', '');
        } else {
            field.css('border-color', 'red');
            result = false;
        }
    });

    return result;

}

function fieldValidation() {
    var result = true;

    //Checks username field
    result = result && usernameFieldCheck(usernameField);

    //Checks that password is valid
    result = result && passwordFieldsCheck(passwordField, repeatPasswordField);

    //Checks email field is valid
    result = result && validateEmail(emailField);

    //Checks that expiration field(s) are valid timestamps
    result = result && checkExpirationFields(expirationFields);

    return result;
}

function checkUsernameUnique(username, id, callback) {
    if (username.length == 0) return false;

    $.getJSON('/admin/users/checkUsername', {
        username: username,
        id: id
    }, function (data) {
        if (data.status) {
            callback(data.data.unique);
        }
    });

}