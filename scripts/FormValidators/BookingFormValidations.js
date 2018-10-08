$('#bookingForm').jqxValidator({
    hintType: 'label',
    animationDuration: 0,
    rules: [
        { input: '.menuCount', message: 'field is required!', action: 'keyup, blur', rule: 'required'
        }
    ]
});
$("#bookingForm").on('validationSuccess', function () {
    $("#bookingForm-iframe").fadeIn('fast');
});
