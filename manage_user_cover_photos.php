<?php
/*
 *      OSCLass – software for creating and publishing online classified
 *                           advertising platforms
 *        Created by Henry Fernandez Hernandez. uwwclass.com / uwwclass.net
 *                        Copyright (C) 2010 OSCLASS
 *
 *       This program is free software: you can redistribute it and/or
 *     modify it under the terms of the GNU Affero General Public License
 *     as published by the Free Software Foundation, either version 3 of
 *            the License, or (at your option) any later version.
 *
 *     This program is distributed in the hope that it will be useful, but
 *         WITHOUT ANY WARRANTY; without even the implied warranty of
 *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *             GNU Affero General Public License for more details.
 *
 *      You should have received a copy of the GNU Affero General Public
 * License along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
 
$flg = Params::getParam('flg');
$file = 'cover_photo/manage_user_profile_pics.php';
if($flg!='')
{
	$file = 'cover_photo/manage_user_profile_pics.php?flg='.$flg;
}
$section = Params::getParam('section');
switch($section)
{
	case 'bulk_actions':
		$action = Params::getParam('bulk_action');
		switch($action)
		{
			case 'approve': 
				 $user_ids = Params::getParam('chk');
				 $count = count($user_ids );
				if ($count > 0)
			   {
				  $item_count = 0;
				 foreach ($user_ids as $user_id)
				 {
				 $conn = getConnection();
				
					$qry = 'UPDATE '.DB_TABLE_PREFIX.'t_cover_photo SET status = "approve" WHERE user_id = '.$user_id;
				
				 $conn->osc_dbExec($qry);
				 $item_count++;
				   }
				   
				   osc_add_flash_ok_message($item_count.' Profile Picture(s) Approved  Successfully','admin');
				   header('Location:'.osc_admin_render_plugin_url($file));
				   
				  
				}
			break;
			case 'deny': 
				 $user_ids = Params::getParam('chk');
				 $count = count($user_ids );
				if ($count > 0)
			   {
				  $item_count = 0;
				 foreach ($user_ids as $user_id)
				 {
				 $conn = getConnection();
				
					$qry = 'UPDATE '.DB_TABLE_PREFIX.'t_cover_photo SET status = "deny" WHERE user_id = '.$user_id;
				
				 $conn->osc_dbExec($qry);
				 $item_count++;
				   }
				   
				   osc_add_flash_ok_message($item_count.' Cover Photo(s) Denied  ','admin');
				   header('Location:'.osc_admin_render_plugin_url($file));
				   
				  
				}
			break;
			case 'delete': 
				 $user_ids = Params::getParam('chk');
				 $count = count($user_ids );
				if ($count > 0)
			   {
				  $item_count = 0;
				 foreach ($user_ids as $user_id)
				 {
				 	$conn = getConnection();
				
					$qry = 'DELETE FROM  '.DB_TABLE_PREFIX.'t_cover_photo  WHERE user_id = '.$user_id;
					$img_path = osc_plugins_path().'cover_photo/images/profile'.$user_id.get_image_extension($user_id);
					unlink($img_path);
					$conn->osc_dbExec($qry);
					 $item_count++;
				   }
				  
				   osc_add_flash_ok_message($item_count.' Cover Photo(s) Deleted  ','admin');
				   header('Location:'.osc_admin_render_plugin_url($file));
				   
				  
				}
			break;
		}
	break;
}
 
if(Params::getParam('start')==''){
 $start = 0;
}else{
  $start = Params::getParam('start');
}
$len=10;
$total = profile_pic_total_count($flg);
$page = osc_admin_render_plugin_url("cover_photo/manage_user_profile_pics.php");
   ?>
  <?php
$con = getConnection();
$qry = 'SELECT * FROM '.DB_TABLE_PREFIX.'t_cover_photo ';
if($flg!='')
{
	$qry.= ' WHERE status="'.$flg.'"';
}
$qry.='ORDER BY on_date DESC LIMIT '.$start.' , '.$len.' ';
$result = $con->osc_dbFetchResults($qry);
/*$result = $con->osc_dbFetchResults('SELECT * FROM %st_cover_photo ORDER BY on_date DESC LIMIT %s , %s ',DB_TABLE_PREFIX,$start,$len);*/
?>
<script type="text/javascript">
			function selectAll(checkname,root)
			{
			for(x=0;x<checkname.length;x++)
			{
			
			checkname[x].checked=root.checked?true:false;
			}
			
			}
</script>
 <form  name="profile_pic_actions"  method="post" data-dialog-open="false">
 <input type="hidden" name="section" value="bulk_actions" />
        <div id="bulk-actions">
            <label>
                <?php //osc_print_bulk_actions('bulk_actions', 'bulk_actions', __get('bulk_options'), 'select-box-extra'); ?>
				<select name="bulk_action">
                <option value="">Bulk Actions</option>
                <option value="approve">Approve</option>
                <option value="deny">Deny</option>
                <option value="delete">Delete</option>
                </select>
				
            </label>
			
                <input type="submit" style="margin:-5px 0px 10px 30px;"  class="btn" name="submit"value="<?php echo osc_esc_html( __('Apply') ); ?>" />
			
        </div>
<table class="table" cellspacing="0" cellpadding="0">
<tr>
<th>S.No</th>
<th><input type="checkbox" name="checkall" id="checkall" onClick="selectAll(document.profile_pic_actions['chk[]'],this)"/></th>
<th align="left">Cover Photo</th>
<th align="left">Name</th>
<th align="left">Ondate</th>
<th align="left">Status</th>
</tr>
<?php 
$n=1;
if($result)
{
foreach($result as $prof_pic)
{
?>
	<tr>
	<td><?php echo $n;?></td>
    <td><input type="checkbox"  name="chk[]" id="chk[]" value="<?php echo $prof_pic['user_id'];?>"/></td>
    <td><?php echo '<img src="'.osc_base_url() . 'oc-content/plugins/cover_photo/images/profile'.$prof_pic['user_id'].$prof_pic['pic_ext'].'" style="height:60px; width:60px;">';?></td>
    <td>
	<?php 
	$user = get_user_by_id($prof_pic['user_id']);
	echo $user['s_name'];?>
    </td>
    <td><?php echo $prof_pic['on_date'];?></td>
    <td><?php echo $prof_pic['status'];?></td>
    </tr>
<?php
$n++; 			
}
}
else
{
	echo '<tr><td colspan="6" align="center" height="50" valign="center"> No Profiles </td></tr>';
}

?>
</table>
</form>
 <?php echo cover_photo_drawNavigation($start,$total,$page,$len)?>
