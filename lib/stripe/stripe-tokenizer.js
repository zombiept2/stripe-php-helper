(function ( $ ) {
    $.fn.stripetokenizer = function( options ) {
 
        // This is the easiest way to have default options.
        var settings = $.extend({
            // These are the defaults.
            key: '',
            cc_field: '',
            cc_exp_month_field: '',
            cc_exp_year_field: '',
            cc_cvc_field: ''
        }, options );

        var form = $(this);
        if (form.attr('stripe-tokenized') == 'true') {
            return;
        }
        if (!form.is('form')) {
            throw 'stripetokenizer - Initialized element is not a form.';
        }
        
        //check for stripe.js script
        if (typeof Stripe == 'function' || $('#stripetokenizer_stripeloading').val() == 'true') { 
            //stripe.js script is already loaded
        }
        else {
            $('body').append('<input type="hidden" id="stripetokenizer_stripeloading" value="true" />');
            //stripe.js do not exist
            var path = 'https://js.stripe.com/v2/';
            var preventCache = false;
            (function(d, script) {
                script = d.createElement('script');
                script.type = 'text/javascript';
                script.async = true;
                script.onload = function(){
                    // remote script has loaded
                    Stripe.setPublishableKey(settings.key);
                };
                if (preventCache) {
                    script.src = path + '?r=' + Math.floor(Math.random()*10000);
                }
                else {
                    script.src = path;
                }
                d.getElementsByTagName('head')[0].appendChild(script);
            }(document));
        }
        
        //setup form
        if (settings.cc_field !== '') {
            var cc_field = $('#'+settings.cc_field);
            if (!cc_field.is('[data-stripe]') && cc_field.attr('data-stripe') != 'number') {
                cc_field.attr('data-stripe', 'number');
            }
            var cc_exp_month_field = $('#'+settings.cc_exp_month_field);
            if (!cc_exp_month_field.is('[data-stripe]') && cc_exp_month_field.attr('data-stripe') != 'exp-month') {
                cc_exp_month_field.attr('data-stripe', 'exp-month');
            }
            var cc_exp_year_field = $('#'+settings.cc_exp_year_field);
            if (!cc_exp_year_field.is('[data-stripe]') && cc_exp_year_field.attr('data-stripe') != 'exp-year') {
                cc_exp_year_field.attr('data-stripe', 'exp-year');
            }
            var cc_cvc_field = $('#'+settings.cc_cvc_field);
            if (!cc_cvc_field.is('[data-stripe]') && cc_cvc_field.attr('data-stripe') != 'cvc') {
                cc_cvc_field.attr('data-stripe', 'cvc');
            }
        }
        //replace form action
        var form_action = form.attr('action');
        console.log(form_action);
        form.submit(function(event) {
            event.preventDefault();
            Tokenizer();
        });
        
        //prevent duplicate setups
        form.attr('stripe-tokenized', 'true');
        
        var stripeResponseHandler = function(status, response) {
            if (response.error) {
                // Show the errors on the form
                form.find('.payment-errors').text(response.error.message);
                alert(response.error.message);
            } 
            else {
                // token contains id, last4, and card type
                var token = response.id;
                // Insert the token into the form so it gets submitted to the server
                form.append($('<input type="hidden" name="stripe_token" class="stripe_token" />').val(token));
                // and re-submit
                eval(form_action);
            }
        };
        function Tokenizer() {
            Stripe.card.createToken(form, stripeResponseHandler);
        }
        
    };
}( jQuery ));