
$('#createBadgeForm').jqxValidator({
    hintType: 'label',
    animationDuration: 0,
    rules: [
       { input: '#badgeName', message: 'Name is required!', action: 'keyup, blur', rule: 'required' },       
       //{ input: '#learningPlanDD', message: 'Select atleast one option!', action: 'keyup, blur', rule: function (input, commit) {
          // return requiredModule(input);}
      // }, 
       { input: '#score', message: 'Required!', action: 'keyup, blur', rule: function (input, commit) {
           return requiredScore(input);}
       },
       { input: '#scoreto', message: 'Required!', action: 'keyup, blur', rule: function (input, commit) {
           return requiredScoreTo(input);}
       }
       //{ input: '#badgeImage', message: 'Required!', action: 'keyup, blur', rule: function (input, commit) {
       //    return requiredImage(input);}
      // },
       ]
});


function requiredModule(input){
    if(input[0].selectedOptions.length > 0){
        $("#lpError").text("");
        $(".hilight").removeClass("hilight");
        $("#lpError").text("");
        return true;           
    }    
    $("#learningPlanDD_chosen").addClass("hilight");
    $("#lpError").text("Select atleast one option!");
    return false;
}



function requiredScore(input){
	var flag = $("#isAllotOnCompletion").is(':checked');
    if (!flag){  
        if(input.val().length > 0){
            return true;    
        }else{
            return false;
        }
    }
    return true;
}
function requiredScoreTo(input){
	var flag = $("#isAllotOnCompletion").is(':checked');
    if (!flag){
    	if($("#allotCriteria").val()=="between"){
	        if(input.val().length > 0){
	            return true;    
	        }else{
	            return false;
	        }
    	}
    }
    return true;
}

function requiredImage(input){
	var val = $("#seq").val();
    if (val == 0 || val == null){  
        if(input.val().length > 0){
        	$("#imageError").text("");
            $("#moduleImg").removeClass("hilight");            
            return true;             
        }else{
        	$("#moduleImg").addClass("hilight");
            $("#imageError").text("Select Image");
            return false;
        }
    }
    return true;
}