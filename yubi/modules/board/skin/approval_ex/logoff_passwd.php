<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data">
<table summary="글쓰기" class="write_form" id="write_form">
	<caption>글쓰기</caption>
	<tbody>
		<tr>
			<th scope="row"><label>패스워드</label></th>
			<td>
            	<input type="password" class="text_input" style="width:350px;" size="50" name="input_passwd" id="input_passwd" value="" />
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