
$('#createMessageForm').jqxValidator({
    hintType: 'label',
    animationDuration: 0,
    rules: [
       { input: '#name', message: 'Name is required!', action: 'keyup, blur', rule: 'required' }, 
       { input: '#subject', message: 'subject is required!', action: 'keyup, blur', rule: 'required' },
//       { input: '#learningPlanDD', message: 'Select atleast one learning plan!', action: 'keyup, blur', rule: function (input, commit) {
//                return requiredLearningPlan(input);}
//       },       
       { input: '#sendDate', message: 'Particuler date is required!', action: 'keyup, blur', rule: function (input, commit) {
                var val = $("input:radio[name='actOption']:checked").val();
                return requiredIf(input,"onParticulerDate",val);}
       },
       { input: '#percent', message: 'Percent is required!', action: 'keyup, blur', rule: function (input, commit) {
                var val = $("input:radio[name='actOption']:checked").val();
                return requiredIf(input,"onMarks",val);}
       
       },
       { input: '#moduleDD', message: 'Select atleast one module!', action: 'keyup, blur', rule: function (input, commit) {
                return requiredModule(input);}
       },       
    ]
});
$("#createMessageForm").on('validationSuccess', function () {
    $("#createMessageForm-iframe").fadeIn('fast');
});

function requiredLearningPlan(input){
    if(input[0].selectedOptions.length > 0){
        $("#lpError").text("");
        $(".hilight").removeClass("hilight");
        $("#lpError").text("");
        return true;           
    }  
    var val = $("input:radio[name='actOption']:checked").val();
    if(val != "onEnrollment"){
        $("#learningPlanDD_chosen").addClass("hilight");
        $("#lpError").text("Select atleast one learning plan!");
        return false;    
    }else{
         $("#lpError").text("");
        $(".hilight").removeClass("hilight");
        $("#lpError").text("");
         return true;
    }
   
}

function requiredModule(input){
    var val = $("input:radio[name='actOption']:checked").val();
    if(val != "onParticulerDate"){
        if(input[0].selectedOptions.length > 0){
            $("#moduleError").text("");
            $(".hilight").removeClass("hilight");
            $("#moduleError").text("");
            return true;           
        }    
        $("#moduleDD_chosen").addClass("hilight");
        $("#moduleError").text("Select atleast one Module!");
        return false;
    }
    return true;
}

function requiredIf(input,condition,val){
    if (val == condition){  
        if(input.val().length > 0){
            return true;    
        }else{
            return false;
        }
    }
    return true;
}
