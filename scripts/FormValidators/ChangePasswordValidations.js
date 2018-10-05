$('#changePasswordForm').jqxValidator({
    hintType: 'label',
    animationDuration: 0,
    rules: [
        { input: '#earlierPassword', message: 'Current password is required!', action: 'keyup, blur', rule: 'required'
        },
        //{ input: '#earlierPassword', message: 'Incorrect Current Password', action: 'keyup, focus', 
//            rule: function (input, commit) {
//               $.getJSON("Actions/CompanyAction.php?getCurrentPassword",function(data){
//                    if (input.val() === $('#newPassword').val()) {
//                    return true;
//                   }
//                   return false;    
//               })
//            }       
//        },
        { input: '#newPassword', message: 'New password is required!', action: 'keyup, blur', rule: 'required'
        },
        { input: '#confirmPassword', message: 'Confirm password is required!', action: 'keyup, blur', rule: 'required'
        },
        { input: '#confirmPassword', message: 'Confirm Password doesn\'t match!', action: 'keyup, focus', 
                rule: function (input, commit) {
                   if (input.val() === $('#newPassword').val()) {
                        return true;
                   }
                   return false;
                }       
        }
    ]
});
$("#customFieldForm").on('validationSuccess', function () {
    $("#customFieldForm-iframe").fadeIn('fast');
});
