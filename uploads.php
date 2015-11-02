<div class="content user_account panel-body">
			<div id="sidebar" class="col-md-3">
                
				
				<div class="user-dashboard-widget"><?php osc_show_widgets('user-dashboard'); ?></div>
			</div>
			<div class="col-md-9">
				<div class="row form-group">


<div class="content user_account">

  
    <div id="main">
            <h2><?php _e('Cover Photo Uploader', 'cover_photo'); ?></h2> <br /><br />
            
            
            <?php cover_photo_upload(); ?> 
            
            
            
    </div>
</div>
</div></div></div>

<style type="text/css">

.listing-basicinfo a{text-decoration:none; color:#007fb2; font-size:13px;font-size: 15px; letter-spacing:normal; font-weight:bold;}
.listing-basicinfo a:hover{text-decoration:underline; color:#000;}
/*#fav_item{ border-bottom:1px solid #EAEAEA; width:100%; clear:both; margin-left:10px;}
.listing-thumb a{ text-decoration:none; color:#007FB2; font-size:15px; font-weight:bold;}
#fav_sidebar{ float:left; border-right:1px solid #EAEAEA; width:210px; }
#fav_sidebar a{ text-decoration:none;}
#fav_main{ width:750px; float:right; }
#checkall{ margin:5px 5px 5px 15px;}*/
.delete{ background:none repeat scroll 0 0 transparent;
    border: medium none;
    cursor: pointer;
    
    margin-left: 10px;
    padding-left: 5px;}
.close{ background:url(images/Cross-icon.png);}
#fav_listings td {
    border-bottom: 1px solid #DDDDDD;
    padding: 5px;
    vertical-align: top;
}
td.check {
    text-align: center;
    width: 5%;
}
#fav_listings td.favimg {
    text-align: center;
    width: 10%;
}
td.iteminfo {
    width: 45%;
}
td.price {
    text-align: center;
    width: 10%;
}

td.iteminfo a {
    font-size: 1.3em;
	/*color: #0066DD;*/
    text-decoration: none;
}

td.iteminfo h3 {
    font-size: 14px;;
	color: #0066DD;
    text-decoration: none;
}
td.iteminfo a:hover { text-decoration:underline;}

</style>
