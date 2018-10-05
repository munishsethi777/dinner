$('#contactUsForm').jqxValidator({
    hintType: 'label',
    animationDuration: 0,
    rules: [
        { input: '#name', message: 'Name is required!', action: 'keyup, blur', rule: 'required'
        },
         { input: '#employeeId', message: 'Employee Id is required!', action: 'keyup, blur', rule: 'required'
        },
         { input: '#workLocation', message: 'Work Location is required!', action: 'keyup, blur', rule: 'required'
        },
        { input: '#internetSpeed', message: 'Internet Speed is required!', action: 'keyup, blur', rule: 'required'
        },
         { input: '#yourLocation', message: 'Location is required!', action: 'keyup, blur', rule: 'required'
        },
         { input: '#phoneNo', message: 'Phone No is required!', action: 'keyup, blur', rule: 'required'
        },
        { input: '#emailId', message: 'Email Id is required!', action: 'keyup, blur', rule: 'required'
        },
         { input: '#emailId', message: 'Invalid Email Id!', action: 'keyup, blur', rule: 'email' }, 
         { input: '#problemDetails', message: 'Detail is required!', action: 'keyup, blur', rule: 'required'
        },
    ]
});
$("#contactUsForm").on('validationSuccess', function () {
    $("#contactUsForm-iframe").fadeIn('fast');
});
