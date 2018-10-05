$('#createManagerForm').jqxValidator({
    hintType: 'label',
    animationDuration: 0,
    rules: [
       { input: '#username', message: 'User Name is required!', action: 'keyup, blur', rule: 'required' }, 
       { input: '#password', message: 'Password is required!', action: 'keyup, blur', rule: 'required' },
       { input: '#fullname', message: 'Full Name is required!', action: 'keyup, blur', rule: 'required' }, 
       { input: '#email', message: 'Email is required!', action: 'keyup, blur', rule: 'required' },
       { input: '#mobile', message: 'Mobile is required!', action: 'keyup, blur', rule: 'required' },
       { input: '#userSelect', message: 'Select User!', action: 'keyup, blur', rule: function (input, commit) {
                return requiredUser(input);}
       }
//       { input: '#profilesDD', message: 'Select atleast one module!', action: 'keyup, blur', rule: function (input, commit) {
//                return requiredProfile(input);}
//       },
//       { input: '#cusFieldNameDD', message: 'Select CustomField Name !', action: 'keyup, blur', rule: function (input, commit) {
//                return requiredCustomField(input);}
//       },
//       { input: '#cusFieldValueDD', message: 'Select CustomField Name !', action: 'keyup, blur', rule: function (input, commit) {
//                return requiredCustomFieldValue(input,"");}
//       },
       
              
    ]
});
$("#createManagerForm").on('validationSuccess', function (){
    $("#createManagerForm-iframe").fadeIn('fast');
});

function requiredLearningPlan(input){
    var val = $("input:radio[name='actOption']:checked").val();
    if(val == "learningPlan"){
        if(input[0].selectedOptions.length > 0){
            $("#lPlanError").text("");
            $(".hilight").removeClass("hilight");             
            return true;           
        }    
        $("#learningPlanDD_chosen").addClass("hilight");
        $("#lPlanError").text("Select atleast one learning Plan!");
        return false;
    }
    return true;
}

function requiredUser(input){
	var isChecked = $("#ismanagerlearner").is(':checked');
    if(isChecked){
        if(input[0].selectedOptions.length > 0){             
            return true;           
        } 
        return false;
    }
    return true;
}

function requiredProfile(input){
    var val = $("input:radio[name='actOption']:checked").val();
    if(val == "learningProfile"){
        if(input[0].selectedOptions.length > 0){
            $("#lprofileError").text("");
            $(".hilight").removeClass("hilight");
            return true;           
        }    
        $("#profilesDD_chosen").addClass("hilight");
        $("#lprofileError").text("Select atleast one learner profile!");
        return false;
    }
    return true;
}
function requiredCustomFieldValue(input,counter){
    var val = $("input:radio[name='actOption']:checked").val();
    if(val == "customField"){
        if(input[0].selectedOptions.length > 0){
            $("#cusFieldValueError" + counter).text("");
            $(".hilight").removeClass("hilight");
            return true;           
        }    
        $("#cusFieldValueDD_chosen" + counter).addClass("hilight");
        $("#cusFieldValueError" + counter).text("Select Custom Field Value!");
        return false;
    }
    return true;
}

function requiredCustomField(input){
    var val = $("input:radio[name='actOption']:checked").val();
    if(val == "customField"){
        if(input[0].selectedOptions[0].label != "Select Option"){
            return true;           
        }
        return false;
    }
    return true
} 
