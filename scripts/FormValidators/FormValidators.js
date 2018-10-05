validateForm();
function validateForm(){
	$('form').each(function() {
	     formId = "#"+$(this).attr('id');
	     var validationRules = $(formId).jqxValidator('rules');
	     if(typeof validationRules == 'undefined'){
	    	 validationRules = [];
	     }
	     $("form"+formId+" :input[type=text]").each(function(){
	    	 var inputId = $(this).attr('id');
	    	 if(typeof inputId != 'undefined' && inputId != null && inputId != ""){
		    	 validationRules.push({ input: '#'+inputId, message: 'Remove Special Charactar !', action: 'keyup, blur', rule: function (input, commit) {
		    		 					return checkIsContainSpecialCharactar(input);               
				  					  }}
		    	  
	    	     )
	    	 }
	     });
	     if(typeof validationRules != 'undefined' && validationRules.length > 0){
	    	 $(formId).jqxValidator('rules', validationRules);
	     }
	});
}
function checkIsContainSpecialCharactar(input){
    value = input.val();
    var hasSpecialChar = hasSpecialCharactar(value);
	if(!hasSpecialChar){
        return true;    
    }else{
        return false;
    }
}

function hasSpecialCharactar(str){
	if(/^[;%.?_$@/:#a-zA-Z0-9- ]*$/.test(str) == false){
	    return true;
	}
	return false
}