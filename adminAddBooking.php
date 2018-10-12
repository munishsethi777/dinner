<?//include("SessionCheck.php");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Bookings</title>
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
	                        <h5>Create Bookings</h5>
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
			var columns = [
			  { text: 'Payment ID', datafield: 'transactionid', width:"10%"}, 			
			  { text: 'id', datafield: 'seq' , hidden:true},
              { text: 'Booked On', datafield: 'bookedon',cellsformat: 'd-M-yyyy hh:mm tt',width:"15%"},
              { text: 'Booking Date', datafield: 'bookingdate',cellsformat: 'd-M-yyyy',width:"10%"},
              { text: 'Slot', datafield: 'timeslot',width:"15%"},
              { text: 'Menu', datafield: 'menu', width:"15%" ,sortable:false},
              { text: 'Customer Name', datafield: 'fullname',width:"12%"},
              { text: 'Email', datafield: 'email',width:"20%"},
              { text: 'Mobile', datafield: 'mobile',width:"10%"}
            ]
           
            var source =
            {
                datatype: "json",
                id: 'id',
                pagesize: 20,
                sortcolumn: 'bookedon',
                sortdirection: 'desc',
                datafields: [{ name: 'seq', type: 'integer' },
                            { name: 'bookedon', type: 'date' },
                            { name: 'bookingdate', type: 'date' },
                            { name: 'transactionid', type: 'string'},
                            { name: 'timeslot', type: 'string'},
                            { name: 'email', type: 'string'},
                            { name: 'fullname', type: 'string'},
                            { name: 'mobile', type: 'string'},
                            { name: 'menu', type: 'string' }
                            ],                          
                url: 'Actions/BookingAction.php?call=getBookings',
                root: 'Rows',
                cache: false,
                beforeprocessing: function(data)
                {        
                    source.totalrecords = data.TotalRows;
                },
                filter: function()
                {
                    // update the grid and send a request to the server.
                    $("#bookingsgrid").jqxGrid('updatebounddata', 'filter');
                },
                sort: function()
                {
                    // update the grid and send a request to the server.
                    $("#bookingsgrid").jqxGrid('updatebounddata', 'sort');
                }
            };
            
            var dataAdapter = new $.jqx.dataAdapter(source);
            // initialize jqxGrid
            $("#bookingsgrid").jqxGrid(
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
    			rendergridrows: function (toolbar) {
                  return dataAdapter.records;     
           		 },
                renderstatusbar: function (statusbar) {
                    // appends buttons to the status bar.
                    var container = $("<div style='overflow: hidden; position: relative; margin: 5px;height:30px'></div>");
                    var reloadButton = $("<div style='float: left; margin-left: 5px;'><i class='fa fa-refresh'></i><span style='margin-left: 4px; position: relative;'>Reload</span></div>");
                    
                    container.append(reloadButton);
                    statusbar.append(container);
                    reloadButton.jqxButton({  width: 70, height: 18 });
                    reloadButton.click(function (event) {
                        $("#bookingsgrid").jqxGrid({ source: dataAdapter });
                    });
                }
            });
        }

        
</script>