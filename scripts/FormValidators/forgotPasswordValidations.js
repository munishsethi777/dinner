$('#forgotPasswordForm').jqxValidator({
    hintType: 'label',
    animationDuration: 0,
    rules: [
        { input: '#username', message: 'User Name is required!', action: 'keyup, blur', rule: 'required'
        }
    ]
});
$("#forgotPasswordForm").on('validationSuccess', function () {
    $("#forgotPasswordForm-iframe").fadeIn('fast');
});
