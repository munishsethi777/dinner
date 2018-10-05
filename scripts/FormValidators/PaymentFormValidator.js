$('#payuForm').jqxValidator({
    hintType: 'label',
    animationDuration: 0,
    rules: [
        { input: '#amount', message: 'Amount is required!', action: 'keyup, blur', rule: 'required'
        },
        { input: '#email', message: 'Email is required!', action: 'keyup, blur', rule: 'required'
        },
        { input: '#email', message: 'Invalid Email', action: 'keyup, blur', rule: 'email'
        },
        { input: '#productinfo', message: 'Product Info is required', action: 'keyup, blur', rule: 'required'
        },
        { input: '#firstname', message: 'FirstName is required!', action: 'keyup, blur', rule: 'required'
        },
        { input: '#phone', message: 'FirstName is required!', action: 'keyup, blur', rule: 'required'
        },
    ]
});
$("#payuForm").on('validationSuccess', function () {
    $("#customFieldForm-iframe").fadeIn('fast');
});