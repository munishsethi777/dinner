<?include("SessionCheck.php");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Packages</title>
    <?include "ScriptsInclude.php"?>
</head>
<body>
    <div id="wrapper">
    <?php include("adminmenuInclude.php")?>  
        <div id="page-wrapper" class="gray-bg">
	        <div class="row border-bottom">
	        </div>
        	<div class="row">
	            <div class="col-lg-12">
	                <div class="ibox">
	                    <div class="ibox-title">
	                    	 <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
								<a class="navbar-minimalize minimalize-styl-2 btn btn-primary "
									href="#"><i class="fa fa-bars"></i> </a>
							</nav>
	                        <h5>Packages</h5>
	                    </div>
	                    <div class="ibox-content">
	                        <div id="packageGrid" style="margin-top:8px"></div>
	                    </div>
	                </div>
	            </div>
        	</div>
       </div>
    </div>
    <form id="form1" name="form1" method="post" action="adminCreatePackage.php">
     	<input type="hidden" id="seq" name="seq"/>
   	</form>
   </body>
</html>
<script type="text/javascript">
	$(document).ready(function(){
	    loadGrid()
	});
	function deleteMenus(gridId,deleteURL){
        var selectedRowIndexes = $("#" + gridId).jqxGrid('selectedrowindexes');
        if(selectedRowIndexes.length > 0){
            bootbox.confirm("Are you sure you want to delete selected row(s)?", function(result) {
                if(result){
                    var ids = [];
                    var imagenames = [];
                    $.each(selectedRowIndexes, function(index , value){
                        if(value != -1){
                            var dataRow = $("#" + gridId).jqxGrid('getrowdata', value);
                            ids.push(dataRow.seq);
                        }
                    });
                    $.get( deleteURL + "&ids=" + ids,function( data ){
                        if(data != ""){
                            var obj = $.parseJSON(data);
                            var message = obj.message;
                            if(obj.success == 1){

                                toastr.success(message,'Success');
                               //$.each(selectedRowIndexes, function(index , value){
                                  //  var id = $("#"  + gridId).jqxGrid('getrowid', value);
                                    var commit = $("#"  + gridId).jqxGrid('deleterow', ids);
                                    $("#"+gridId).jqxGrid('updatebounddata');
                                    $("#"+gridId).jqxGrid('clearselection');
                                //});
                            }else{
                                toastr.error(message,'Failed');
                            }
                        }

                    });

                }
            });
        }else{
             bootbox.alert("No row selected.Please select row to delete!", function() {});
        }
    } 
	function loadGrid(){
		
		var columns = [
		  { text: 'id', datafield: 'seq' , hidden:true},
		  { text: 'Occasion', datafield: 'occasions.title', width:"10%"}, 			
		  { text: 'Title', datafield: 'packages.title', width:"15%"}, 			
		  { text: 'Description', datafield: 'packages.description',width:"30%"},
	      { text: 'Price', datafield: 'price',width:"8%"},
	      { text: 'LastModified', datafield: 'packages.lastmodifiedon', filtertype: 'date',cellsformat: 'd-M-yyyy h:m tt',width:"14%"},
	      { text: 'Created On', datafield: 'packages.createdon', filtertype: 'date',cellsformat: 'd-M-yyyy h:m tt',width:"14%"},
	      { text: 'Enabled', datafield: 'packages.isenabled',columntype: 'checkbox',width:"6%",cellsalign: 'center'},
	    ]
	   
	    var source =
	    {
	        datatype: "json",
	        id: 'id',
	        pagesize: 20,
	        sortcolumn: 'packages.createdon',
	        sortdirection: 'desc',
	        datafields: [{ name: 'seq', type: 'integer' },
	                     { name: 'occasions.title', type: 'string' },
	                    { name: 'packages.title', type: 'string' },
	                    { name: 'packages.description', type: 'string'},
	                    { name: 'price', type: 'string' },
	                    { name: 'packages.isenabled', type: 'boolean' },
	                    { name: 'packages.lastmodifiedon', type: 'date' },
	                    { name: 'packages.createdon', type: 'date' }
	                    ],                          
	        url: 'Actions/PackageAction.php?call=getAllPackages',
	        root: 'Rows',
	        cache: false,
	        beforeprocessing: function(data)
	        {        
	            source.totalrecords = data.TotalRows;
	        },
	        filter: function()
	        {
	            // update the grid and send a request to the server.
	            $("#packageGrid").jqxGrid('updatebounddata', 'filter');
	        },
	        sort: function()
	        {
	            // update the grid and send a request to the server.
	            $("#packageGrid").jqxGrid('updatebounddata', 'sort');
	        }
	    };
	    
	    var dataAdapter = new $.jqx.dataAdapter(source);
	    // initialize jqxGrid
	    $("#packageGrid").jqxGrid(
	    {
	    	width: '100%',
			height: '75%',
			source: dataAdapter,
			filterable: true,
			sortable: true,
			autoshowfiltericon: true,
			columns: columns,
			pageable: true,
			altrows: true,
			enabletooltips: true,
			columnsresize: true,
			columnsreorder: true,
			showstatusbar: true,
			virtualmode: true,
			selectionmode: 'checkbox',
			rendergridrows: function (toolbar) {
	          return dataAdapter.records;     
	   		 },
	        renderstatusbar: function (statusbar) {
	            // appends buttons to the status bar.
	            var container = $("<div style='overflow: hidden; position: relative; margin: 5px;height:30px'></div>");
	            var addButton = $("<div style='float: left; margin-left: 5px;'><i class='fa fa-plus-square'></i><span style='margin-left: 4px; position: relative;'>    Add</span></div>");
	            var deleteButton = $("<div style='float: left; margin-left: 5px;'><i class='fa fa-times-circle'></i><span style='margin-left: 4px; position: relative;'>Delete</span></div>");
	            var editButton = $("<div style='float: left; margin-left: 5px;'><i class='fa fa-edit'></i><span style='margin-left: 4px; position: relative;'>Edit</span></div>");
	
	
	            container.append(addButton);
	            container.append(editButton);
	            container.append(deleteButton);
	
	            statusbar.append(container);
	            addButton.jqxButton({  width: 65, height: 18 });
	            deleteButton.jqxButton({  width: 70, height: 18 });
	            editButton.jqxButton({  width: 65, height: 18 });
	
	            // create new row.
	            addButton.click(function (event) {
	                location.href = ("adminCreatePackage.php");
	            });
	            // update row.
	            editButton.click(function (event){
	            	var selectedrowindex = $("#packageGrid").jqxGrid('selectedrowindexes');
	                var value = -1;
	                indexes = selectedrowindex.filter(function(item) { 
	                    return item !== value
	                })
	                if(indexes.length != 1){
	                    bootbox.alert("Please Select single row for edit.", function() {});
	                    return;    
	                }
	                var row = $('#packageGrid').jqxGrid('getrowdata', indexes);
	                $("#seq").val(row.seq);                        
	                $("#form1").submit();    
	            });
	            // delete row.
	            deleteButton.click(function (event) {
	                gridId = "packageGrid";
	                deleteUrl = "Actions/PackageAction.php?call=deletePackages";
	                deleteMenus(gridId,deleteUrl);
	            });
	        }
	    });
	}
</script>