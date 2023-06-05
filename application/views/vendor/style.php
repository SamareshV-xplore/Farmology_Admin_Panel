<style type="text/css">
	body{
		overflow-x: clip;
		font-family: 'Montserrat', sans-serif;
	}
	#container{
		margin: 200px auto;
	    width: 400px;
	    padding: 20px;
	    border: 1px solid #e1e1e1;
	    border-radius: 5px;
    	box-shadow: 1px 2px 4px #e1e1e1;
	}
	.inputbox{
		margin-top: 30px;
		transition: all .3s;
	}
	.inputbox:hover, .inputbox:focus{
		border: 1px solid #3c93ff;
	}

	.welcome-text{
		font-family: 'Playfair Display SC', serif;
		color: #3c93ff;
    	font-size: 20px;
	}
	.login-btn{
		background-color: #3c93ff;
	    color: white;
	    margin-top: 20px;
	    transition: all .3s;
	}
	.login-btn:hover{
		background-color: white;
		color: #3c93ff;
		border: 1px solid #3c93ff;
	}

	.login-btn:disabled{
		background-color: #565656;
		color: white;
	}
	*:disabled{
		cursor: no-drop;
	}
	.shadow-none{
		box-shadow: none !important;
	}
	.login-link:hover{
		color: orange;
	}

	/* The side navigation menu */
.sidebar {
  margin: 0;
  padding: 0;
  width: 232px;
  background-color: #f1f1f1;
  position: fixed;
  height: 100%;
  overflow: auto;
}

/* Sidebar links */
.sidebar a {
  display: block;
  color: black;
  padding: 16px;
  text-decoration: none;
}

/* Active/current link */
.sidebar a.active {
  background-color: #3c93ff;
  color: white;
}

/* Links on mouse-over */
.sidebar a:hover:not(.active) {
  background-color: #9bc2f1;
  /*color: white;*/
}

/* Page content. The value of the margin-left property should match the value of the sidebar's width property */
div.content {
  margin-left: 200px;
  padding: 1px 16px;
  height: 1000px;
}

/* On screens that are less than 700px wide, make the sidebar into a topbar */
@media screen and (max-width: 700px) {
  .sidebar {
    width: 100%;
    height: auto;
    position: relative;
  }
  .sidebar a {float: left;}
  div.content {margin-left: 0;}
}

/* On screens that are less than 400px, display the bar vertically, instead of horizontally */
@media screen and (max-width: 400px) {
  .sidebar a {
    text-align: center;
    float: none;
  }
}

.dashboard-welcome-text{
	font-family: 'Montserrat', sans-serif;
	color: white;
	font-size: 15px;
	padding: 5px;
	line-height: 35px;
}

.header-label{
		padding: 20px 0px 0px 10px;
    font-size: 25px;
}

.dashboard-card{
		width: 300px;
    border: 1px solid #e1e1e1;
    box-shadow: 1px 2px 4px #c1c1c1;
    border-radius: 5px;
    font-family: 'Montserrat';
    margin: 30px;    
}

.dashboard-card-label{
	    /*color: #3c93ff;*/
	    font-weight: 600;
	    font-size: 20px;
	    padding: 10px 0px 0px 10px;
}
.counter{
		color: #3c93ff;
    font-size: 48px;
    font-weight: 700;
}

.main-container{
	padding: 10px;
}

.fab {
   width: 60px;
   height: 60px;
   background-color: #3c93ff;
   border-radius: 50%;
   box-shadow: 0 6px 10px 0 #666;
   transition: all 0.1s ease-in-out;
 
   font-size: 45px;
   color: white !important;
   text-align: center;
   line-height: 60px;
 
   position: fixed;
   right: 20px;
   bottom: 20px;

   cursor: pointer;
}
 
.fab:hover {
   box-shadow: 0 6px 14px 0 #666;
   transform: scale(1.05);
   text-decoration: none;
}

.right{
	margin-left: 96%;
	cursor: pointer;
}

#dashboard-content{
	/*padding-left: 10px;*/
}
.log-out-butt{
    margin: 15px 15px 0px 0px;
    /* line-height: 23px; */
    background: white;
    height: 25px;
    width: 25px;
    border-radius: 25px;
    text-align: center;
    font-weight: 700;
    color: #3c93ff;
    border: 1px solid #003576;
    cursor: pointer;
    transition: all 0.3s;
}
	.header_nav{
		background-color: #3c93ff;
    position: fixed;
    width: 100%;
    z-index: 1;
	}

	.log-out-butt:hover{
		background-color: #3c93ff;
		color: white;
	}	

	.box-body{
		margin-left: 10px;
	}

	.profile-lines{
		background-color: black;
    width: 230px;
	}

	.profile-image{
		height: 170px;
		max-height: 170px;
		width: 170px;
		max-width: 170px;
    border-radius: 500px;
    border: 1px solid #9d9d9d;
    cursor: pointer;
	}

	.hide-input-file{
		    margin-top: -10px;
		    padding: 5px;
		    border-radius: 5px;
		    cursor: pointer;
	}

	.form-group{
		text-align: left;
	}

	.profile-inputbox{
		transition: all .3s;
	}
	.profile-inputbox:hover, .profile-inputbox:focus{
		border: 1px solid #3c93ff;
	}

	.profile-container{
		width: 500px;
	}

	.profile-update-button{
    background: #3c93ff;
    border-radius: 3px;
    border: none;
    padding: 10px;
    color: white;
    width: 100%;
    margin-top: 40px;
	}

	.dropdown-toggle::after {
	    display:none;
	}
	.dropdown-toggle:hover{
			text-decoration: none;
	}
	
	


/*.outer {
  display: table;
  position: absolute;
  top: 0;
  left: 0;
  height: 100%;
  width: 100%;
}

.middle {
  display: table-cell;
  vertical-align: middle;
}

.inner {
  margin-left: auto;
  margin-right: auto;
  width: 400px;
  /* Whatever width you want */
}*/

</style>