
$('#customFieldForm').jqxValidator({
    hintType: 'label',
    animationDuration: 0,
    rules: [
       { input: '#username', message: 'User Name is required!', action: 'keyup, blur', rule: 'required' },
       { input: '#username', message: 'Invalid Username!', action: 'keyup, blur', rule: function (input, commit) {
      			return checkValidation(input);               
  			} 
       },
       { input: '#password', message: 'Password is required!', action: 'keyup, blur', rule: 'required' },
       { input: '#password', message: 'Invalid Password!', action: 'keyup, blur', rule: function (input, commit) {
                return checkValidation(input);               
           } 
       },
       //{ input: '#confirmPassword', message: 'Confirm Password is required!', action: 'keyup, blur', rule: function (input, commit) {
//                return reuiredIf(input);               
//           }
//       },
//       { input: '#confirmPassword', message: 'Confirm Password doesn\'t match!', action: 'keyup, focus', rule: function (input, commit) {
//               if(isCheckValidtion()){
//                   if (input.val() === $('#password').val()) {
//                        return true;
//                   }
//                   return false;       
//               }
//               return true;
//               
//           }
//       },
       
       { input: '#emailid', message: 'Invalid e-mail!', action: 'keyup', rule: 'email' },
       ]
});
$("#customFieldForm").on('validationSuccess', function () {
    $("#customFieldForm-iframe").fadeIn('fast');
});
function isCheckValidtion(){
    if ($("#isChangePassword").is(":checked") || $("#id").val() == "0"){
        return true;
    }return false;
}
function reuiredIf(input){
   // if(isCheckValidtion()){
        if(input.val().length > 0){
            return true;    
        }return false   
   // }
}

function checkValidation(input){
	 if(input.val().length > 0){
		 if(input.val().includes(" ")){
			 return false
		 }
         return true;    
     }return false   
}

$('#setProfileForm').jqxValidator({
    hintType: 'label',
    animationDuration: 0,
    rules: [
         { 
         input: '#profileSelect', message: 'Select Profile!', action: 'keyup, blur', 
            rule: function (input, commit) {
                    return requiredProfile(input);}
         }
            
    ]
});
$("#setProfileForm").on('validationSuccess', function () {
    $("#setProfileForm-iframe").fadeIn('fast');
});
function validate(input){
    index = document.getElementById(input).selectedIndex;
    if(index > 0){
        return true;
    }
    alert("Please Select Profile!")
   return false;
}

function requiredProfile(input){
    if(input[0].selectedOptions.length > 0){
        $(".hilight").removeClass("hilight");
        $("#profileSelectError").text("");
        return true;           
    }    
    $("#profileSelect_chosen").addClass("hilight");
    $("#profileSelectError").text("Select atleast one profile!");
    return false;
}