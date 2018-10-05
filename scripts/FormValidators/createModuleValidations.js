$('#createModuleForm').jqxValidator({
    hintType: 'label',
    animationDuration: 0,
    rules: [
       { input: '#name', message: 'Module Name is required!', action: 'keyup, blur', rule: 'required' },
      // { input: '#vembedCode', message: 'Video embed code!', action: 'keyup, blur', rule: function (input, commit) {
       //         return reuiredIf(input);}
     //  },
    //   { input: '#aembedCode', message: 'Audio embed code!', action: 'keyup, blur', rule: function (input, commit) {
     //           return reuiredIf(input);}
    //   },
//       { input: '#questionsSelect2', message: 'Select atleast one question!', action: 'keyup, blur', rule: function (input, commit) {
//                return requiredQuestion(input);}
//       },
       //{input: '#fileToUpload', message: 'Select Document!', action: 'keyup, blur',rule: function (input, commit) {
         //           return validateFile(input);
        //        }
     //  }
       ]
       
});
$("#createModuleForm").on('validationSuccess', function () {
    $("#createQuestionForm-iframe").fadeIn('fast');
});
function validateFile(input){
     var val = $("#moduleType").val(); 
      $("#documentError").text("");
      if(val == "document" && isDocumentLoaded == 0){
        var fileinput = input[0];
        if(fileinput == undefined){
            fileinput =input         
        }
        val = fileinput.value;
        if(val != ""){
            return true;
        }
        $('#lblFileUpload').html("<a>Select Document</a>"); 
        $("#documentError").text("Please Select Document!");    
        return false;
       }return true;
}
function reuiredVideoIf(input){
    var val = $("#moduleType").val(); 
    if(val == "video"){
        if(input.val().length > 0){
            return true;    
        }return false   
    }
    return true;
}
function reuiredIf(input){
    var val = $("#moduleType").val(); 
    if(val == "audio"){
        if($("#aembedCode").val() != ""){                          
            return true;    
        }return false   
    }
    if(val == "video"){
        if($("#vembedCode").val() != ""){
            return true;    
        }return false   
    }
    return true;
}

function requiredQuestion(input){
    var val = $("#moduleType").val();    
    if(val == "quiz" || val == "survey"){
    	
    	var formid = val == "quiz" ? "#createQuestionForm" : "#createSurveyQuestionForm"
    	var vals = [];
        $(formid + ' #questionsSelect :selected' ).each( function( i, selected ) {
            vals[i] = $( selected ).val();
        });
        if(vals.length > 0){
            $(formid + " #questionError").text("");
            $(formid + " .hilight").removeClass("hilight");
            return true;           
        }    
        $(formid + " #questionsSelect_chosen").addClass("hilight");
        $(formid + " #questionError").text("Select atleast one question!");
        return false;
    }
    return true;
}
function requiredEssay(){
    var val = $("#moduleType").val();
    if(val == "essay"){
         var editorData = CKEDITOR.instances.editor.getData();
         if(editorData != ""){
            $("#essayError").text("");
            return false;           
        }
        $("#essayError").text("Please create essay!");
        return true;
    }
}