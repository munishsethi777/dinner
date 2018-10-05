function loadValidator(quesFormId){
	$(quesFormId).jqxValidator({
	    hintType: 'label',
	    animationDuration: 0,
	    rules: [
	       { input: quesFormId + ' #questionname', message: 'Title is required!', action: 'keyup, blur', rule: 'required' },  
	       
	       { input: quesFormId + ' #vembedCode', message: 'Embed Code is required!', action: 'keyup, blur', rule: function (input, commit) {
	           return requiredEmbedCode(input,quesFormId);}
	       },
	       { input: quesFormId + ' #option1', message: 'Option1 is required!', action: 'keyup, blur', rule: function (input, commit) {
	           return requiredOption(input,quesFormId);}
	       },     
	      
	       //{ input: '#totalMarks', message: 'Marks should be greater than 0!', action: 'keyup, blur', rule: 'required' },
	      { input: quesFormId + ' #totalMarks', message: 'Marks should be greater than 0!', action: 'keyup', rule: function (input, commit) {
	              return totalMarksValidate(input,quesFormId);}
	      },
	       ]
	});
	$(quesFormId).on('validationSuccess', function () {
	    $(quesFormId + "-iframe").fadeIn('fast');
	});
}
function totalMarksValidate(input,quesFormId){
	var moduleType = $("input[name='quesModuleType']:checked").val();
	if(moduleType == "quiz"){
		var quesType = $(quesFormId + " input[name='questiontype']:checked").val();
		if(questionType != "single" && questionType != "multi"){
			if(input.val().length > 0){
		        if(input.val() > 0){
		           return true;   
		        }
		    }	
		}else{
			return true;
		}
	}else{
		return true;
	}
	return false;
}



function requiredEssay(quesFormId){
	val = $(quesFormId + " input[name='questiontype']:checked").val();
    if(val == "doc"){
         var editorData = $(quesFormId + ' #summernote').summernote('code');         
         if(editorData != "" && editorData != "<br>"){
            $(quesFormId + " #essayError").text("");
            return false;           
        }else{
        	$(quesFormId + " #essayError").text("Description is required!");
        	return true;
        }
    }
    return false;
}
function requiredDocFile(quesFormId){
	val = $(quesFormId + " input[name='questiontype']:checked").val();
    if(val == "doc"){
         var file = $(quesFormId + " #fileUpload")[0].value;
         alert(file);
         if(file != ""){
            $(quesFormId + " #documentError").text("");
            return false;           
        }
        $(quesFormId + " #documentError").text("File is required!");
        return true;
    }
}
function requiredEmbedCode(input,quesFormId){
	val = $(quesFormId + " input[name='questiontype']:checked").val();
    if(val == "media"){
        code = $(quesFormId + " #vembedcode").val()
        if(code == null || code == ""){
        	return false;
        }
    }
    return true;
}
function requiredOption(input,quesFormId){
	val = $(quesFormId + " input[name='questiontype']:checked").val();
    if(val == "single" || val == "multi" || val == "sequencing"){
        option = input.val();
        if(option == null || option == ""){
        	return false;
        }
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

function validateForm(){
	$('form').each(function(){
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
	//if(/^[;%.?_$@/"<,':#a-zA-Z0-9- ]*$/.test(str) == false){
	   // return true;
	//}
	return false;
}