$('#createCTForm').jqxValidator({
	hintType: 'label',
	animationDuration: 0,
	rules: [
	{ input: '#title', message: 'Title is required!', action: 'keyup, blur', rule: 'required' },
	{ input: '#fromDate', message: 'From Date is required!', action: 'keyup, blur', rule: 'required' },
	{ input: '#toDate', message: 'To Date is required!', action: 'keyup, blur', rule: 'required' },
	]
});