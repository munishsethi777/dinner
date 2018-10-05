
$('#createLearningPlanForm').jqxValidator({
    hintType: 'label',
    animationDuration: 0,
    rules: [
       { input: '#name', message: 'Name is required!', action: 'keyup, blur', rule: 'required' },       
       { input: '#activationDate', message: 'Activation date is required!', action: 'keyup, blur', rule: function (input, commit) {
                return reuiredActiveDateIf(input);               
       }
       
       },
//       { input: '#modulesSelect', message: 'Select atleast one Module!', action: 'keyup, blur', rule: function (input, commit) {
//                return requiredSelect(input,"modulesSelect");}
//       },
       { input: '#profiles', message: 'Select atleast one Profile!', action: 'keyup, blur', rule: function (input, commit) {
           return requiredProfile(input,"profile");}
       },
       { input: '#userSelect', message: 'Select atleast one User!', action: 'keyup, blur', rule: function (input, commit) {
           return requiredSelect(input,"userSelect");}
       },
       { input: '#deactiveDate', message: 'Deactivation date is required!', action: 'keyup, blur', rule: function (input, commit) {
                return reuiredDeactiveDateIf(input);               
       }
       }
       ]
});
$("#createLearningPlanForm").on('validationSuccess', function () {
    $("#createLearningPlanForm-iframe").fadeIn('fast');
});
function reuiredActiveDateIf(input){
    var val = $("input:radio[name='actOption']:checked").val()
    if (val == "futureActive"){  
        if(input.val().length > 0){
            return true;    
        }else{
            return false;
        }
    }
    return true;
}
function reuiredDeactiveDateIf(input){
    if ($("#deactivateChk").is(":checked")){  
        if(input.val().length > 0){
            return true;    
        }else{
            return false;
        }
    }
    return true;
} 


function requiredSelect(input,id){
	if(id == "userSelect"){
		var option = $("input[name='assOption']:checked").val();
		if(option != "user"){
			return true;
		}
	}
	var vals = getMultiSelectSelectedVals(id);
    if(vals.length > 0){            
    	return true;           
    }
    return false;
}

function requiredProfile(input,id){
	var option = $("input[name='assOption']:checked").val();
	if(option != "profile"){
		return true;
	}
    var vals = [];
   var profileIds = getMultiSelectSelectedVals("profiles");
   if(profileIds.length > 0){
       $("#profileError").text("");
       $(".hilight").removeClass("hilight");
       return true;           
   }    
   $("#profiles").addClass("hilight");
   $("#profileError").text("Select atleast one Profile!");
   return false;
}

  