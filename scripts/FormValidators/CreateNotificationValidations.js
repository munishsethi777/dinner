$('#createNotificationForm').jqxValidator({
    hintType: 'label',
    animationDuration: 0,
    rules: [
       { input: '#name', message: 'Name is required!', action: 'keyup, blur', rule: 'required' },  
       { input: '#subject', message: 'Subject Name is required!', action: 'keyup, blur', rule: 'required' },        
      ]
});