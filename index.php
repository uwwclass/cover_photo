<?php
/*
Plugin Name: Cover Photo 
Plugin URI: http://www.osclass.org
Description: Allows users to upload a Cover  picture
Version: 3.0.2.1
Author: Customized by Henry(uwwclass.com)
Author URI: http://www.osclass.org/
Short Name: cover_photo
*/

function cover_photo_install() {
    $conn = getConnection();
    $conn->autocommit(false);
    try {
        $path = osc_plugin_resource('cover_photo/struct.sql');
        $sql = file_get_contents($path);
        $conn->osc_dbImportSQL($sql);
        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        echo $e->getMessage();
    }
    $conn->autocommit(true);
}



function cover_photo_uninstall() {
    $conn = getConnection();
    $conn->autocommit(false);
    try {
	$conn->osc_dbExec('DROP TABLE %st_cover_photo', DB_TABLE_PREFIX);
	$conn->commit();
	} catch (Exception $e) {
	    $conn->rollback();
	    echo $e->getMessage();
	}
    $conn->autocommit(true);
}



function cover_photo_upload(){


   // Configuration - Your Options ///////////////////////////////////////////////////////

    $width = '1200';
    $height = '140'; // height is optional. If you use a height, *MAKE SURE* it's proportional to the WIDTH

    $allowed_filetypes = array('.jpg','.gif','.bmp','.png'); // These will be the types of file that will pass the validation.
    $max_filesize = 524288; // Maximum filesize in BYTES (currently 0.5MB).
    $upload_path = osc_plugins_path().'cover_photo/images/';

    $button_text = 'Upload Cover Photo';

    ////// ***** No modifications below here should be needed ***** /////////////////////

    // First, check to see if user has existing profile picture...
	$user_id = osc_logged_user_id(); // the user id of the user profile we're at
	$conn = getConnection();
	$result=$conn->osc_dbFetchResult("SELECT user_id, pic_ext FROM %st_cover_photo WHERE user_id = '%d' ", DB_TABLE_PREFIX, $user_id);

	if($result>0) //if picture exists
	{
	    echo '<script language="javascript">function ShowDiv(){document.getElementById("HiddenDiv").style.display = \'\';}</script>';
	    echo '<script language="javascript">function deletePhoto(){document.forms["deleteForm"].submit();}</script>';

	    $modtime = filemtime($upload_path.'profile'.$user_id.$result['pic_ext']); //ensures browser cache is refreshed if newer version of picture exists
	    echo '<img class="img-thumbnail" src="'.osc_base_url() . 'oc-content/plugins/cover_photo/images/profile'.$user_id.$result['pic_ext'].'?'.$modtime.'" width="'.$width.'" height="'.$height.'">'; // display picture
	}
	else { // show default photo since they haven't uploaded one
	    echo '<img class="img-thumbnail" src="'.osc_base_url() . 'oc-content/plugins/cover_photo/no_picture.jpg" width="'.$width.'" height="'.$height.'">';
	} 

    if( osc_is_web_user_logged_in()){
	if($result>0){
	    echo '<br><a href="javascript:ShowDiv();">Upload Cover Photo</a> - <a href="javascript:deletePhoto();">Delete Photo</a>';
	    echo '<div id="HiddenDiv" style="display:none;">'; // hides form if user already has a profile picture and displays a link to form instead
	}
	$url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	echo '
	    <form name="newpic" method="post" enctype="multipart/form-data"  action="'.$url.'">
	    <input type="file" name="userfile" id="file"><br>
	    <input name="Submit" type="submit" value="'.$button_text.'">
	    </form>
	    <form name="deleteForm" method="POST" action="'.$url.'"><input type="hidden" name="deletePhoto"></form>
	'; //echo
    	if($result>0) echo '</div>';
    } //if logged-in


    if(isset($_POST['Submit'])) // Upload photo
    {
	$filename = $_FILES['userfile']['name']; // Get the name of the file (including file extension).
	$ext = substr($filename, strpos($filename,'.'), strlen($filename)-1); // Get the extension from the filename.
 
	// Check if the filetype is allowed, if not DIE and inform the user.
	if(!in_array($ext,$allowed_filetypes))
	    die('The file you attempted to upload is not allowed.');
 
	// Now check the filesize, if it is too large then DIE and inform the user.
	if(filesize($_FILES['userfile']['tmp_name']) > $max_filesize)
	    die('The file you attempted to upload is too large.');
 
	// Check if we can upload to the specified path, if not DIE and inform the user.
	if(!is_writable($upload_path))
	{
	    die('You cannot upload to the specified directory, please CHMOD it to 777.');
	}
	$today = date('Y-m-d');
	// Upload the file to your specified path.
	if(move_uploaded_file($_FILES['userfile']['tmp_name'],$upload_path . 'profile'.$user_id.$ext)){
	    if($result==0){
		$conn->osc_dbExec("INSERT INTO %st_cover_photo (user_id, pic_ext , on_date , status) VALUES ('%d', '%s' , '%s' , 'pending')", DB_TABLE_PREFIX, $user_id, $ext , $today);
	    }
	    else {
		$conn->osc_dbExec("UPDATE %st_cover_photo SET pic_ext = '%s' ,status = 'pending' , on_date = '%s' WHERE user_id = '%d' ", DB_TABLE_PREFIX, $ext , $today , $user_id);
	    }
		osc_add_flash_ok_message(' Your profile picture will appear in public profile after admin approval  ');
	    echo '<script type="text/javascript">window.location = document.URL;</script>';
	}

	else{
	    echo 'There was an error during the file upload.  Please try again.'; // It failed :(.
	}
     }

    if(isset($_POST['deletePhoto'])) // Delete the photo
    {
	$conn->osc_dbExec("DELETE FROM %st_cover_photo WHERE user_id = '%d' ", DB_TABLE_PREFIX, $user_id);
	echo '<script type="text/javascript">window.location = document.URL;</script>';
    }

} // end cover_photo_upload()





function cover_photo_show(){

   // Configuration - Your Options ///////////////////////////////////////////////////////

    $width = '1200';
    $height = '140'; // height is optional. If you use a height, *MAKE SURE* it's proportional to the WIDTH


    ////// ***** No modifications below here should be needed ***** /////////////////////

    // First, check to see if user has existing profile picture...
    $user_id = osc_user_id(); // the user id of the user profile we're at

    $conn = getConnection();
    $result=$conn->osc_dbFetchResult("SELECT user_id, pic_ext FROM %st_cover_photo WHERE user_id = '%d' AND status='approve' ", DB_TABLE_PREFIX, $user_id);

    if($result>0) //if picture exists
    {
	$upload_path = osc_plugins_path().'cover_photo/images/';
	$modtime = filemtime($upload_path.'profile'.$user_id.$result['pic_ext']); //ensures browser cache is refreshed if newer version of picture exists
	// This is the picture HTML code displayed on page
	echo '<img class="img-thumbnail" src="'.osc_base_url() . 'oc-content/plugins/cover_photo/images/profile'.$user_id.$result['pic_ext'].'?'.$modtime.'" width="'.$width.'" height="'.$height.'"/>'; // display picture
    }
    else{
	echo '<img class="img-thumbnail" src="'.osc_base_url() . 'oc-content/plugins/cover_photo/no_picture.jpg" width="'.$width.'" height="'.$height.'"/>';
    }
} //end cover_photo_show()


 function cover_photo_admin_menu() {
      
        echo '<h3><a href="#" style="font-weight:bold">Cover Photo</a></h3>
<ul> 
  <li><a style="color:blue;font-weight:bold" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/help.php') . '">&raquo; ' . __('Help', 'cover_photo') . '</a></li>
  <li><a style="color:orange;font-weight:bold" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/manage_user_cover_photos.php?flg=pending') . '">&raquo; ' . __('Pending', 'cover_photo') . '</a></li>
  <li><a style="color:darkturquoise;font-weight:bold" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/manage_user_cover_photos.php?flg=approve') . '">&raquo; ' . __('Approved', 'cover_photo') . '</a></li>
  <li><a style="color:green;font-weight:bold" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/manage_user_cover_photos.php?flg=deny') . '">&raquo; ' . __('Denied', 'cover_photo') . '</a></li>
  <li><a style="color:goldenrod;font-weight:bold" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/manage_user_cover_photos.php') . '">&raquo; ' . __('All Photos', 'cover_photo') . '</a></li>
</ul>';
}

 function get_user_by_id1($userId)
 {
	 $con = getConnection();
	 $result = $con->osc_dbFetchResult('SELECT * FROM %st_user WHERE pk_i_id=%d',DB_TABLE_PREFIX,$userId);
	 return $result;
 }
 
 function get_image_extension1($userId)
 {
	 $con = getConnection();
	 $result = $con->osc_dbFetchResult('SELECT * FROM %st_cover_photo WHERE user_id="%s"',DB_TABLE_PREFIX,$userId);
	 
	 return $result['pic_ext'];
 }

function cover_photo_drawNavigation($start,$total,$link,$len=10)
{

   $en = $start +$len;
	if($start==0)
	{
		if($total>0)
		{
			$start1=1;
		}
		else 
		{
			$start1=0;
		}
	}
	else 
	{
		$start1=$start+1;
	}
    if($en>$total)
		$en = $total;
	if($total!=0)
			$pagecount=($total % $len)?(intval($total / $len)+1):($total / $len);
	else
	{
		print("<span align=center class='bodytext' ><br>No Results found<br></span>");
		$pagecount=0;
		return;
	}
	print "<table  cellpading=0 cellspacing=0 align=left width=100% ><tr>";
	print "<td width=20% class=bodytext valign=bottom height=25>Showing $start1 - $en of $total</td>";
	print "<td width=71% align=right valign=bottom class='page_nums'>";
        if($en>$len)
        {
		$en1=$start-$len;
		print "<a href='$link&start=$en1' class='link2'> Previous </a><span class='bodytext'> | </span>" ;
        }
		else
			print "<span class='bodytext'> Previous </span><span class='bodytext'> | </span>" ;
		
		// Caliculating Page Values
		$numstr="";
		$curpage=intval(($start+1)/$len)+1;
		if($pagecount > 10)
		{
			
			$istart=(intval($curpage/10) * 10)+1;
			if($istart + 10 > $pagecount)
				$istart=$pagecount - 9;
			$pagecount=10;
		}
		else
			$istart=1;
		for($i=$istart;$i<$pagecount+$istart;$i++)
		{
			$ed=($i-1)*$len;
			if($start!=$ed)
			{
				$numstr.= " <a href='$link&start=$ed' class='link2'> $i </a><span class='bodytext'> | </span>";
				
			}
			else { 
				//if($i >1 )
					$numstr.= "<span class='selectedpage'> $i </span><span class='bodytext'> | </span>";
				//else
					//$numstr.= "<span class='bodytext'> | </span>";
			}
		}
		print $numstr;
        if($en<$total)
		{
			$en2=$start+$len;
			print "<a href='$link&start=$en2' class='link2'> Next </a>" ;
	  	}
		else
			print "<span class='bodytext'> Next </span>" ;
	print "</td></tr></table>";
        
}


function cover_photo_user_menu() {
        echo '<li class="" ><a href="' . osc_render_file_url(osc_plugin_folder(__FILE__) . 'uploads.php') . '" >' . __('Cover Photo Uploader', 'cover_photo') . '</a></li>';
    } 


function cover_photo_total_count($flg)
{
	$con = getConnection();
	$qry = 'SELECT count(*) AS total FROM '.DB_TABLE_PREFIX.'t_cover_photo  ';
	if($flg!='')
	{
		$qry.= ' WHERE status="'.$flg.'"';
	}
	$result = $con->osc_dbFetchResult($qry);
	return $result['total'];
}
    // This is needed in order to be able to activate the plugin
    osc_register_plugin(osc_plugin_path(__FILE__), 'cover_photo_install') ;
    // This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
    osc_add_hook(osc_plugin_path(__FILE__) . '_uninstall', 'cover_photo_uninstall') ;
	
	
	
	//configuring admin menu
	
    osc_add_hook('admin_menu', 'cover_photo_admin_menu');
	
	
	//configuring user menu
	
	// Add link in user menu page
    osc_add_hook('user_menu', 'cover_photo_user_menu');
	
	
	

?>
