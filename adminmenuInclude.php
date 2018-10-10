<?php 

$isProduct = "";
$isProductGroup = "";
$isReport = "";
$isChangePassword="";

$parts = Explode('/', $_SERVER["PHP_SELF"]);
$file =  $parts[count($parts) - 1];


//echo  $file;
if($file == "dashboard.php"){
	$isBookings = "active";
}

?>

<nav class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav metismenu" id="side-menu">
                <li class="nav-header">
                    <div class="dropdown profile-element"> 
                    	<a data-toggle="dropdown" class="dropdown-toggle" href="#"> 
	                    	<span class="clear"> 
	                    		<span class="block m-t-xs"> 
	                    			<strong class="font-bold">FLY DINING BOOKINGS</strong>
	                    		</span>
							</span>
						</a>
                    </div>
					
                </li>
                <li class="<?php echo $isBookings;?>">
                    <a href="dashboard.php"><i class="fa fa-cube"></i> 
                    	<span class="nav-label ">Bookings</span>  
                    </a>
                </li>
                <li class="<?php echo $isChangePassword;?>">
                    <a href="changePassword.php"><i class="fa fa-key"></i> 
                    	<span class="nav-label">Change Password</span>  
                    </a>
                </li>
                <li>
                    <a href="logout.php"><i class="fa fa-sign-out"></i> 
                    	<span class="nav-label">Logout</span>  
                    </a>
                </li>
            </ul>
            <ul class="dropdown-menu animated fadeInRight m-t-xs">
				
			</ul>

        </div>
    </nav>
