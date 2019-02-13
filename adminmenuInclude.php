<?php 

$isBookings="";
$isMenus = "";
$isTimeSlots = "";
$isDiscountCoupons = "";
$isChangePassword="";
$isPackages = "";
$isOccassions = "";
$isSettings = "";
$parts = Explode('/', $_SERVER["PHP_SELF"]);
$file =  $parts[count($parts) - 1];


//echo  $file;
if($file == "dashboard.php" || $file == "adminEditBooking.php"){
	$isBookings = "active";
}elseif($file == "adminShowMenus.php" || $file == "adminAddMenu.php"){
	$isMenus = "active";
}elseif($file == "adminShowTimeSlots.php" || $file == "adminAddTimeSlot.php"){
	$isTimeSlots = "active";
}elseif($file == "adminShowDiscountCoupons.php" || $file == "adminAddDiscountCoupon.php"){
	$isDiscountCoupons = "active";
}elseif($file == "adminChangePassword.php"){
	$isChangePassword = "active";
}elseif($file == "adminShowPackages.php" || $file == "adminCreatePackage.php"){
	$isPackages = "active";
}elseif($file == "adminShowOccasions.php" || $file == "adminCreateOccasion.php"){
	$isOccassions = "active";
}elseif($file == "adminSettings.php"){
	$isSettings = "active";
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
                    <a href="dashboard.php"><i class="fa fa-list-alt"></i> 
                    	<span class="nav-label ">Bookings</span>  
                    </a>
                </li>
                <li class="<?php echo $isMenus;?>">
                    <a href="adminShowMenus.php"><i class="fa fa-coffee"></i> 
                    	<span class="nav-label">Menus</span>  
                    </a>
                </li>
                <li class="<?php echo $isTimeSlots;?>">
                    <a href="adminShowTimeSlots.php"><i class="fa fa-clock-o"></i> 
                    	<span class="nav-label">Time Slots</span>  
                    </a>
                </li>
                <li class="<?php echo $isDiscountCoupons;?>">
                    <a href="adminShowDiscountCoupons.php"><i class="fa fa-gift"></i> 
                    	<span class="nav-label">Discount Coupons</span>  
                    </a>
                </li>
                <li class="<?php echo $isChangePassword;?>">
                    <a href="adminChangePassword.php"><i class="fa fa-key"></i> 
                    	<span class="nav-label">Change Password</span>  
                    </a>
                </li>
                <li class="<?php echo $isPackages;?>">
                    <a href="adminShowPackages.php"><i class="fa fa-angellist"></i> 
                    	<span class="nav-label">Packages</span>  
                    </a>
                </li>
                 <li class="<?php echo $isOccassions;?>">
                    <a href="adminShowOccasions.php"><i class="fa fa-calendar"></i> 
                    	<span class="nav-label">Occasions</span>  
                    </a>
                </li>
                <li class="<?php echo $isSettings;?>">
                    <a href="adminSettings.php"><i class="fa fa-cog"></i> 
                    	<span class="nav-label">Settings</span>  
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
