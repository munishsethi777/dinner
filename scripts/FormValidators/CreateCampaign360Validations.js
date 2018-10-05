$('#createCampaignForm').jqxValidator({
    hintType: 'label',
    animationDuration: 0,
    rules: [
       { input: '#name', message: 'Name is required!', action: 'keyup, blur', rule: 'required' },
       { input: '#endDate', message: 'End Date is required!', action: 'keyup, blur', rule: 'required' },
       { input: '#number', message: 'Number is required!', action: 'keyup, blur', rule: function () {
           var value = $("#number").val();
           var result = !isNaN(value);
           return result; }
       },
       { input: '#startDate', message: 'Start Date is required!', action: 'keyup, blur', rule: 'required' },
       { input: '#description', message: 'Description Name is required!', action: 'keyup, blur', rule: 'required' },  
       { input: '#profiles', message: 'Select atleast one profile!', action: 'keyup, blur', rule: function (input, commit) {
            return requiredProfile(input);}
       },
       { input: '#userSelect', message: 'Select Populated By!', action: 'keyup, blur', rule: function (input, commit) {
           return requiredPopulatedBy(input);}
       }
      ]
});



function requiredProfile(input){
    var vals = [];
   $( '#profiles :selected' ).each( function( i, selected ) {
       vals[i] = $( selected ).val();
   });
   if(vals.length > 0){
       $("#profileError").text("");
       $(".hilight").removeClass("hilight");
       return true;           
   }    
   $("#profilesSelect_chosen").addClass("hilight");
   $("#profileError").text("Select atleast one profile!");
   return false;
}

function requiredPopulatedBy(input){
   var vals = input[0].selectedOptions;  
   if(vals.length > 0){       
       return true;           
   }    
   return false;
}