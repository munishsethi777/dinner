 $('#learningProfileForm').jqxValidator({
            hintType: 'label',
            animationDuration: 0,
            rules: [
               { input: '#name', message: 'Profile Name is required!', action: 'keyup, blur', rule: 'required' }
               ]
        });