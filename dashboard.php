<?//include("SessionCheck.php");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bookings</title>
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
	                        <h5>Bookings</h5>
	                    </div>
	                    <div class="ibox-content">
	                        <div id="bookingsgrid" style="margin-top:8px"></div>
	                    </div>
	                </div>
	            </div>
        	</div>
       </div>
    </div>
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
        
        
        function loadGrid(){
			$("#productsGrid").bind('bindingcomplete', function (event) {
        		
        	});
        	var actions = function (row, columnfield, value, defaulthtml, columnproperties) {
                data = $('#productsGrid').jqxGrid('getrowdata', row);
                var html = "<div style='text-align: center; margin-top:1px;font-size:18px'><a href='javascript:viewDetail("+ data['seq'] + ")' ><i class='fa fa-server' title='View Detail'></i></a>";
                    html += "</div>";
                
                return html;
            }
            var columns = [
              { text: 'id', datafield: 'seq' , hidden:true},
              { text: 'Code', datafield: 'code', width:"16%"},
              { text: 'Title', datafield: 'title', width:"50%"},
              { text: 'Modified', datafield: 'lastmodifiedon',cellsformat: 'd-M-yyyy hh:mm tt',width:"15%"},
              { text: 'Created', datafield: 'createdon',cellsformat: 'd-M-yyyy hh:mm tt',width:"15%"}              
            ]
           
            var source =
            {
                datatype: "json",
                id: 'id',
                pagesize: 20,
                sortcolumn: 'lastmodifiedon',
                sortdirection: 'desc',
                datafields: [{ name: 'seq', type: 'integer' }, 
                            { name: 'code', type: 'string' }, 
                            { name: 'title', type: 'string' },
                            { name: 'lastmodifiedon', type: 'date'},
                            { name: 'createdon', type: 'date'},
                            { name: 'action', type: 'string' } 
                            ],                          
                url: 'Actions/ProductAction.php?call=getProducts',
                root: 'Rows',
                cache: false,
                beforeprocessing: function(data)
                {        
                    source.totalrecords = data.TotalRows;
                },
                filter: function()
                {
                    // update the grid and send a request to the server.
                    $("#productsGrid").jqxGrid('updatebounddata', 'filter');
                },
                sort: function()
                {
                    // update the grid and send a request to the server.
                    $("#productsGrid").jqxGrid('updatebounddata', 'sort');
                },
                addrow: function (rowid, rowdata, position, commit) {
                    commit(true);
                },
                deleterow: function (rowid, commit) {
                    commit(true);
                },
                updaterow: function (rowid, newdata, commit) {
                    commit(true);
                }
            };
            
            var dataAdapter = new $.jqx.dataAdapter(source);
            // initialize jqxGrid
            $("#productsGrid").jqxGrid(
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
    			selectionmode: 'checkbox',
    			showstatusbar: true,
    			virtualmode: true,
    			rendergridrows: function (toolbar) {
                  return dataAdapter.records;     
           		 },
                renderstatusbar: function (statusbar) {
                    // appends buttons to the status bar.
                    var container = $("<div style='overflow: hidden; position: relative; margin: 5px;height:30px'></div>");
                    var addButton = $("<div style='float: left; margin-left: 5px;'><i class='fa fa-plus-square'></i><span style='margin-left: 4px; position: relative;'>Add</span></div>");
                    var deleteButton = $("<div style='float: left; margin-left: 5px;'><i class='fa fa-times-circle'></i><span style='margin-left: 4px; position: relative;'>Delete</span></div>");
                    var editButton = $("<div style='float: left; margin-left: 5px;'><i class='fa fa-edit'></i><span style='margin-left: 4px; position: relative;'>Edit</span></div>");
                    var reloadButton = $("<div style='float: left; margin-left: 5px;'><i class='fa fa-refresh'></i><span style='margin-left: 4px; position: relative;'>Reload</span></div>");

                    container.append(addButton);
                    container.append(editButton);
                    container.append(deleteButton);
                    container.append(reloadButton);
                    statusbar.append(container);
                    addButton.jqxButton({  width: 65, height: 18 });
                    deleteButton.jqxButton({  width: 65, height: 18 });
                    editButton.jqxButton({  width: 65, height: 18 });
                    reloadButton.jqxButton({  width: 70, height: 18 });
                    // create new row.
                    addButton.click(function (event) {
                        location.href = ("createProduct.php");
                    });
                    editButton.click(function (event){
                        var selectedrowindex = $("#productsGrid").jqxGrid('selectedrowindexes');
                        var value = -1;
                        indexes = selectedrowindex.filter(function(item) { 
                            return item !== value
                        })
                        if(indexes.length != 1){
                            bootbox.alert("Please Select single row for edit.", function() {});
                            return;    
                        }
                        var row = $('#productsGrid').jqxGrid('getrowdata', indexes);
                        $("#id").val(row.seq);                        
                        $("#form1").submit();                   
                        });
                     deleteButton.click(function (event) {
                    	 deleteRows("productsGrid","Actions/ProductAction.php?call=deleteProduct");
                     });
                     $("#productsGrid").bind('rowselect', function (event) {
                         var selectedRowIndex = event.args.rowindex;
                          var pageSize = event.args.owner.rows.records.length - 1;                       
                         if($.isArray(selectedRowIndex)){           
                             if(isSelectAll){
                                 isSelectAll = false;    
                             } else{
                                 isSelectAll = true;
                             }                                                                     
                             $('#productsGrid').jqxGrid('clearselection');
                             if(isSelectAll){
                                 for (i = 0; i <= pageSize; i++) {
                                     var index = $('#productsGrid').jqxGrid('getrowboundindex', i);
                                     $('#productsGrid').jqxGrid('selectrow', index);
                                 }    
                             }
                         }                        
                    });
                    // reload grid data.
                    reloadButton.click(function (event) {
                        $("#productsGrid").jqxGrid({ source: dataAdapter });
                    });
                }
            });
        }

        
</script>