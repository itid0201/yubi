<link rel="stylesheet" type="text/css" href="/style/common/board.css" />
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data">
<table summary="삭제사유" class="boardwrite">
	<caption>삭제사유</caption>
	<tbody>
		<tr>
			<th scope="row"><label>삭제사유</label></th>
			<td>
				<textarea name="admin_comment" id="admin_comment" ></textarea>
			</td>
		</tr>
	</tbody>
</table>
<?php
	$hidden = '';
	foreach($data['hidden'] as $key=>$value) if(!is_null($value)) $hidden .= '<input type="hidden" name="'.$key.'" id="'.$key.'" value="'.$value.'" />';
	echo $hidden;
	## 버튼 영역.
	$img_url   = '/images/common/board/temp';
	echo '<div class="board_button"><ul><li><input type="image" src="'.$img_url.'/board_ok.gif" alt="확인" id="btn_form_submit" /></li></ul></div>';
?>
</form>