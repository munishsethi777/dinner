$('#importReportForm').jqxValidator({
        hintType: 'label',
        animationDuration: 0,
        rules: [
           { input: '#name', message: 'DataSet Name is required!', action: 'keyup, blur', rule: 'required' }
           ]
 });