<?include("SessionCheck.php");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Time Slots</title>
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
	                        <h5>Time Slots</h5>
	                    </div>
	                    <div class="ibox-content">
	                        <div id="menuGrid" style="margin-top:8px"></div>
	                    </div>
	                </div>
	            </div>
        	</div>
       </div>
    </div>
    <form id="form1" name="form1" method="post" action="adminAddTimeSlot.php">
     	<input type="hidden" id="seq" name="seq"/>
   	</form>
   </body>
</html>

	<script type="text/javascript">
	 isSelectAll = false;
        $(document).ready(function(){
           loadGrid()
           $('.i-checks').iCheck({
	        	checkboxClass: 'icheckbox_square-green',
	        	radioClass: 'iradio_square-green',
	    	});
           
        });
        
        function deleteTimeSlot(gridId,deleteURL){
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
                                imagenames.push(dataRow.imageName);
                            }
                        });
                        $.get( deleteURL + "&ids=" + ids+"&imagenames="+imagenames,function( data ){
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
        	 			  { text: 'Title', datafield: 'title', width:"20%"}, 			
        	 			  { text: 'id', datafield: 'seq' , hidden:true},
        	              { text: 'Description', datafield: 'description',width:"25%"},
        	              { text: 'Available Till', datafield: 'availabletill' ,filterable:false,sortable:false,width:"15%"},
        	              { text: 'Seats', datafield: 'seats',width:"10%"},
        	              { text: 'Menu', datafield: 'menus',filterable:false,sortable:false ,width:"26%"}
        	             ]
        	            
        	             var source =
        	             {
        	                 datatype: "json",
        	                 id: 'id',
        	                 pagesize: 20,
        	                 sortcolumn: 'seq',
        	                 sortdirection: 'desc',
        	                 datafields: [{ name: 'seq', type: 'integer' },
        	                             { name: 'title', type: 'string' },
        	                             { name: 'availabletill', type: 'string' },
        	                             { name: 'description', type: 'string'},
        	                             { name: 'seats', type: 'string' },
        	                             { name: 'menus', type: 'string' }
        	                             ],                          
        	                 url: 'Actions/TimeSlotAction.php?call=getAllTimeSlots',
        	                 root: 'Rows',
        	                 cache: false,
        	                 beforeprocessing: function(data)
        	                 {        
        	                     source.totalrecords = data.TotalRows;
        	                 },
        	                 filter: function()
        	                 {
        	                     // update the grid and send a request to the server.
        	                     $("#menuGrid").jqxGrid('updatebounddata', 'filter');
        	                 },
        	                 sort: function()
        	                 {
        	                     // update the grid and send a request to the server.
        	                     $("#menuGrid").jqxGrid('updatebounddata', 'sort');
        	                 }
        	             };
        	             
        	             var dataAdapter = new $.jqx.dataAdapter(source);
        	             // initialize jqxGrid
        	             $("#menuGrid").jqxGrid(
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
        	                         location.href = ("adminAddTimeSlot.php");
        	                     });
        	                     // update row.
        	                     editButton.click(function (event){
        	                     	var selectedrowindex = $("#menuGrid").jqxGrid('selectedrowindexes');
        	                         var value = -1;
        	                         indexes = selectedrowindex.filter(function(item) { 
        	                             return item !== value
        	                         })
        	                         if(indexes.length != 1){
        	                             bootbox.alert("Please Select single row for edit.", function() {});
        	                             return;    
        	                         }
        	                         var row = $('#menuGrid').jqxGrid('getrowdata', indexes);
        	                         $("#seq").val(row.seq);                        
        	                         $("#form1").submit();    
        	                     });
        	                     // delete row.
        	                     deleteButton.click(function (event) {
        	                         gridId = "menuGrid";
        	                         deleteUrl = "Actions/TimeSlotAction.php?call=deleteTimeSlots";
        	                         deleteTimeSlot(gridId,deleteUrl);
        	                     });
        	                 }
        	             });
        }
		
        
</script>
