$('#customFieldForm').jqxValidator({
            hintType: 'label',
            animationDuration: 0,
            rules: [
               { input: '#fieldName', message: 'Field Name is required!', action: 'keyup, blur', rule: 'required' }
               ]
        });