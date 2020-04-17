"use strict";

// Class Definition
var KTLoginGeneral = function() {

    var login = $('#kt_login');

    var showErrorMsg = function(form, type, msg) {
        var alert = $('<div class="kt-alert kt-alert--outline alert alert-' + type + ' alert-dismissible" role="alert">\
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\
			<span></span>\
		</div>');

        form.find('.alert').remove();

        alert.prependTo(form);
        //alert.animateClass('fadeIn animated');
        KTUtil.animateClass(alert[0], 'fadeIn animated');
        alert.find('span').html(msg);
    };

    // Private Functions
    var displaySignInForm = function() {
        login.removeClass('kj-passwordforgetwrapper');
        //login.removeClass('kt-login--signup');

        $('.kj-loginwrapper').show();
        $('.kj-passwordforgetwrapper').hide();

        login.addClass('kj-loginwrapper');
        KTUtil.animateClass(login.find('.kj-loginwrapper')[0], 'flipInX animated');
    };

    var displayForgotForm = function() {
        login.removeClass('kj-loginwrapper');
        //login.removeClass('kt-login--signup');
        $('.kj-loginwrapper').hide();
        $('.kj-passwordforgetwrapper').show();

        login.addClass('kj-passwordforgetwrapper');
        KTUtil.animateClass(login.find('.kj-passwordforgetwrapper')[0], 'flipInX animated');
    };

    var handleFormSwitch = function() {
        $('#kt_login_forgot').click(function(e) {
            e.preventDefault();
            displayForgotForm();
        });

        $('#kt_login_forgot_cancel').click(function(e) {
            e.preventDefault();
            displaySignInForm();
        });
    };

    var handleSignInFormSubmit = function() {
        $('#kt_login_signin_submit').click(function(e) {
            e.preventDefault();
            var btn = $(this);
            var form = $(this).closest('form');

            form.validate();

            if (!form.valid()) {
                return;
            }

            btn.addClass('kt-spinner kt-spinner--right kt-spinner--sm kt-spinner--light').attr('disabled', true);

            form.submit();
        });
    };

    var handleForgotFormSubmit = function() {
        $('#kt_login_forgot_submit').click(function(e) {
            e.preventDefault();

            var btn = $(this);
            var form = $('.form-forget');

            form.validate();

            if (!form.valid()) {
                return;
            }

            btn.addClass('kt-spinner kt-spinner--right kt-spinner--sm kt-spinner--light').attr('disabled', true);
            form.ajaxSubmit({
                url: form.attr('action'),
                type: "POST",
                data:{
                    _token: $("kt-login__forgot input[name='_token']").val()
                },

                success: function(response, status, xhr, $form) {
                    if( response.success ) {
                        // similate 2s delay
                        setTimeout(function () {
                            btn.removeClass('kt-spinner kt-spinner--right kt-spinner--sm kt-spinner--light').attr('disabled', false); // remove
                            form.clearForm(); // clear form
                            form.validate().resetForm(); // reset validation states

                            // display signup form
                            displaySignInForm();
                            var signInForm = $('.form-login');
                            signInForm.clearForm();
                            signInForm.validate().resetForm();

                            showErrorMsg(signInForm, 'success', response.status);
                        }, 2000);
                    } else {
                        // Error weergeven
                        log(btn);
                        setTimeout(function() {
                            btn.removeClass('kt-spinner--sm kt-spinner--light').attr('disabled', false); // remove
                            showErrorMsg(form, 'danger', response.msg);
                        }, 500);
                    }
                }
            });
        });
    };

    // Public Functions
    return {
        // public functions
        init: function() {
            handleFormSwitch();
            handleSignInFormSubmit();
            handleForgotFormSubmit();
        }
    };
}();

// Class Initialization
$(document).ready(function() {
    KTLoginGeneral.init();
});