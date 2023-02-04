 $(document).ready(function() {
    $('#contact_form').bootstrapValidator({
        // To use feedback icons, ensure that you use Bootstrap v3.1.0 or later
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            first_name: {
                validators: {
                        stringLength: {
                        min: 2,
						message: 'Bitte geben Sie mehr als 2 Buchstaben ein.'
                    },
                        notEmpty: {
                        message: 'Bitte geben Sie Ihren Vornamen an.'
                    }
                }
            },
             last_name: {
                validators: {
                     stringLength: {
                        min: 2,
						message: 'Bitte geben Sie mehr als 2 Buchstaben ein.'
                    },
                    notEmpty: {
                        message: 'Bitte geben Sie Ihren Nachnamen an.'
                    }
                }
            },
            email: {
                validators: {
                    notEmpty: {
                        message: 'Bitte geben Sie Ihre E-Mail Adresse an.'
                    },
                    emailAddress: {
                        message: 'Bitte geben Sie eine gültige E-Mail Adresse an.'
                    }
                }
            },
            comment: {
                validators: {
                      stringLength: {
                        min: 10,
                        max: 500,
                        message:'Bitte geben Sie mindestens 10 und maximal 500 Zeichen ein'
                    },
                    notEmpty: {
                        message: 'Ihre Anfrage muss einen Text enthalten'
                    }
                    }
                }
            }
        })
        .on('success.form.bv', function(e) {
            $('#success_message').slideDown({ opacity: "show" }, "slow") // Do something ...
                $('#contact_form').data('bootstrapValidator').resetForm();

            // Prevent form submission
            e.preventDefault();

            // Get the form instance
            var $form = $(e.target);

            // Get the BootstrapValidator instance
            var bv = $form.data('bootstrapValidator');

            // Use Ajax to submit form data
            $.post($form.attr('action'), $form.serialize(), function(result) {
                console.log(result);
            }, 'json');
        });
});

$('body').scrollspy({ 
target: '#navbar',
offset:50
});

$("#navbar a[href^='#']").on('click', function(e) {

   // prevent default anchor click behavior
   e.preventDefault();

   // store hash
   var hash = this.hash;

   // animate
   $('html, body').animate({
       scrollTop: $(hash).offset().top - 49
     }, 300, function(){

       // when done, add hash to url
       // (default click behaviour)
       window.location.hash = hash;
     });

});
