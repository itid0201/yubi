<?php
echo $data['debug']; // only test..
?>
<?php if(!empty($data['null_check'])) echo '<ul class="type03">'.$data['null_check'].'</ul>';?>

<div class="form_table">
<form id="write_form" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data">
<table class="board_write">
	<caption>이 표는 <?php echo $_SYSTEM['menu_info']['title']; ?> 내용 입력란을 제공하며 <?php if($data['use_lock'] == 'true') echo '공개여부, ';?> 제목, 등록자, <?php if($data['use_category_1'] == 'true') echo '분류, ';?> <?php if($data['use_tag']=='true' && !empty($data['tag_list_all']))  echo 'tag 등록, ';?> <?php if($data['get_guest_info'] == 'true' && ($_SYSTEM['myinfo']['user_level'] == 11 || $_SYSTEM['myinfo']['user_level'] == 99)) echo '회원주소, 회원연락처, ';?><?php if($data['mode'] != 'reply' && ($data['use_reply_sms'] == 'all' || $data['use_reply_sms'] == 'admin')) echo '답변 수신번호, ';?> 내용으로 구성되어 있습니다.</caption>
	<tbody>
		<?php
		if($data['use_lock'] == 'true'){
			$open_allow = $data['open'] == 'y' ? 'checked="checked"' : '';
			$open_deny  = $data['open'] == 'y' ? '' : 'checked="checked"';
		?>
		<tr>
			<th scope="row"><label>공개여부</label></th>
			<td colspan="3">
            	<div class="item">
				<input type="radio" name="open" id="open_allow" value="y" <?php echo $open_allow; ?> /><label for="open_allow">공개</label>&nbsp;&nbsp;&nbsp;
				<input type="radio" name="open" id="open_deny" value="n" <?php echo $open_deny; ?> /><label for="open_deny">비공개</label>
                </div>
			</td>
		</tr>
		<?php } //if end ?>
		<tr>
			<th scope="row"><label for="title">제목<span>(<strong style="color: #ff4444">*</strong>)</span></label></th>
			<td colspan="3">
                <div class="item">
                <?php 
                if($data['use_title_style'] == 'true' && !empty($data['title_style_list'])) {
                    foreach($data['title_style_list'] as $value) {
                        $title_style .= '<option value="'.$value.'"'.($value==$data['title_style']?' selected="selected"':'').'>'.$value.'</option>';
                    }
                    echo '<select name="title_style" id="title_style">'.$title_style.'</select>';
                }
                ?>
                <input type="text" class="i_text w300" size="50" name="title" id="title" value="<?php echo $data['title'];?>" />
                </div>
            </td>
		</tr>
		<tr>
			<th scope="row"><label for="reg_name"><?php echo ($data['writer_display'] == 'department' ? '등록부서' : '등록자')?><span>(<strong style="color: #ff4444">*</strong>)</span></label></th>
			<td colspan="3">
                <div class="item">
				<?php if($data['writer_display'] == 'department') { ?>
                <input type="text" class="i_text w200" name="depart_name" id="reg_name" value="<?php echo $data['depart_name']; ?>" />
                <?php } else { ?>
                <input type="text" class="i_text w200" name="reg_name" id="reg_name" value="<?php echo $data['reg_name']; ?>" <?php echo ($data['read_only_reg_name'] == 'true' ? 'readonly="readonly"' : '')?> />
                <?php } ?>
                </div>
            </td>
		</tr>
        <?php if($_SYSTEM['module_config']['board_id'] == 'expo_press_release') { ?>
		<tr>
			<th scope="row"><label for="link_url">링크주소</label></th>
			<td colspan="3">
                <div class="item">
                <input type="text" class="i_text w300" size="120" name="link_url" id="link_url" value="<?php echo $data['link_url'];?>" />
                </div>
            </td>
		</tr>
        <?php } ?>        
		<?php
		if($data['use_category_1'] == 'true') {
			$category_1_all = unserialize($data['category_1_all']);
			$category_1 = '';

			foreach($category_1_all as $key=>$value) {
				$category_1 .= '<option value="'.$value.'"'.($value==$data['category_1']?' selected="selected"':'').'>'.$value.'</option>';
			}
		?>
        <tr>
            <th scope="row"><label for="category_1">분류<span>(<strong style="color: #ff4444">*</strong>)</span></label></th>
			<td colspan="3">
                <div class="item">
	            <select name="category_1" id="category_1"><?php echo $category_1;?></select>
                </div>
			</td>
        </tr>
        <?php
        } //if end
		
		if($data['use_tag']=='true' && !empty($data['tag_list_all'])) {
			foreach($data['tag_list_all'] as $value){
				$tag_list .= '<label><input type="checkbox" name="tag_list[]" value="'.$value.'" '.(in_array($value, $data['tag_list']) ? 'checked="checked"' : '').' />'.$value.'</label>';
			}
		?>
        <tr>
            <th scope="row"><label>tag 등록<span>(<strong style="color: #ff4444">*</strong>)</span></label></th>
            <td colspan="3"><div class="item"><?php echo $tag_list;?></div></td>
        </tr>
		<?php
		}

		if($data['get_guest_info'] == 'true') {
		?>
		<tr>
			<th scope="row"><label>회원주소<span>(<strong style="color: #ff4444">*</strong>)</span></label></th>
			<td colspan="3"><div class="item">
				<?php
				if(!empty($data['zipcode_1']) || !empty($data['zipcode_2'])) $data['zipcode'] = $data['zipcode_1'].$data['zipcode_2'];
				$address = array();
				$address['zipcode']   = $data['zipcode'];
				$address['address_1'] = $data['address_1'];
				$address['address_2'] = $data['address_2'];
				echo address_box($address);
				?>
                </div>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="phone_1">회원연락처<span>(<strong style="color: #ff4444">*</strong>)</span></label></th>
			<td colspan="3">
            	<div class="item">
				<?php
				if(!empty($data['phone_1']) || !empty($data['phone_2']) || !empty($data['phone_3'])) {
					$phone_1 = $data['phone_1'];
					$phone_2 = $data['phone_2'];
					$phone_3 = $data['phone_3'];
				} else {
					if(!empty($data['phone'])) list($phone_1, $phone_2, $phone_3) = explode('-',$data['phone']);
				}
				?>
				<input type="text" class="i_text" id="phone_1" maxlength="5" size="5" name="phone_1" value="<?php echo $phone_1;?>" title="회원 연락처 첫자리"/> - 
				<input type="text" class="i_text" id="phone_2" maxlength="4" size="5" name="phone_2" value="<?php echo $phone_2;?>" title="회원 연락처 중간자리"/> - 
				<input type="text" class="i_text" id="phone_3" maxlength="4" size="5" name="phone_3" value="<?php echo $phone_3;?>" title="회원 연락처 마지막자리" />
                </div>
			</td>
		</tr>
		<?php } //if end ?>


		<?php if($data['mode'] != 'reply' && ($data['use_reply_sms'] == 'all' || $data['use_reply_sms'] == 'admin')) { ?>
		<tr>
			<th scope="row"><label for="reply_sms_recv_number">답변글 SMS 수신번호</label></th>
			<td colspan="3"><div class="item">
				<input type="text" class="i_text" id="reply_sms_recv_number" maxlength="14" size="14" name="reply_sms_recv_number" value="<?php echo $data['reply_sms_recv_number'];?>" title="답변글 SMS 수신번호" />
                </div>
			</td>
		</tr>
		<?php }?>

		<tr>
			<th scope="row"><label for="contents">내용</label></th>
			<td colspan="3"><div class="item"><textarea name="contents" id="contents" rows="15" cols="53"><?php echo $data['contents'];?></textarea></div></td>
		</tr>
		<?php
		## 공공누리 입력창 2017.12.12 서희진 추가
		if( $data['use_open_type'] == 'true' ){ 		 
		?>
		<tr>
			<th scope="row"><label for="contents">공공누리 적용<em>(*)</em></label></th>
			<td>
				<?php echo upload_koglType_kogl($data);?>
			</td>
		</tr>
			
		<?php
		}//공공누리 입력창 끝
		?>
		<?php
		if($data['use_search_tag'] == 'true'){ ?>
		<tr>
			<th scope="row"><label for="search_tag">검색태그</label></th>
			<td><input type="text" class="i_text" style="width:350px;" size="50" name="search_tag" id="search_tag" value="<?php echo $data['search_tag']?>" /></td>
		</tr>
		<?php
		}//if end 
		?>
		<?php
		if($data['use_logoff_write'] == 'true' && $_SYSTEM['myinfo']['is_login'] != true){ ?>
		<tr>
			<th scope="row"><label for="passwd">패스워드<?php check_required_field_wirte_mode($_SYSTEM['module_config']['required_value'], 'passwd'); ?></label></th>
			<td><input type="password" class="i_text" style="width:350px;" size="50" name="passwd" id="passwd" value="" /></td>
		</tr>
		<tr>
			<th scope="row"><label for="text_keycode">키코드입력<?php check_required_field_wirte_mode($_SYSTEM['module_config']['required_value'], 'text_keycode'); ?></label></th>
			<td>
                <p style="color:#00F"><?php echo $data['text_keycode'];?></p>
                <input type="text" class="i_text" style="width:350px;" size="50" name="text_keycode" id="text_keycode" value="" />
            </td>
		</tr>
		<?php
		}//if end 
		?>
        <?php
		$use_gallery_img = false;
		if( $_SYSTEM['module_config']['use_gallery_img'] == 'true')	$use_gallery_img = true;
		if($data['file_upload_count'] > 0) echo upload_box_alt_tour($data['file_list'], $iter, 0, $use_gallery_img);					
		//if($data['file_upload_count'] > 0) echo upload_box_alt_tour($data['file_list']);
		?>
		</tbody>
</table>
<?php
	$hidden = '';
	foreach($data['hidden'] as $key=>$value) if(!is_null($value)) $hidden .= '<input type="hidden" name="'.$key.'" id="'.$key.'" value="'.$value.'" />';
	echo $hidden;
?>
<?php 
	## 버튼 영역.
	$img_url   = '/images/common/board/temp';
	$url       = $_SERVER['PHP_SELF'];
	$user_info = array();
	$user_info['is_login'] = empty($_SYSTEM['myinfo']['is_login']) ? NULL : $_SYSTEM['myinfo']['is_login'];
	$user_info['user_pin'] = empty($_SYSTEM['myinfo']['my_pin']) ? NULL : $_SYSTEM['myinfo']['my_pin'];
	$arr_data['reg_pin']   = empty($data['reg_pin']) ? NULL : $data['reg_pin'];
	$arr_data['del']       = empty($data['del']) ? NULL : $data['del'];

	$arr_data['use_logoff_write'] = empty($data['use_logoff_write']) ? NULL : $data['use_logoff_write'];
	echo print_button('write', $data['permission'], $data['parameter'], $url, $img_url, $user_info, $arr_data);
?>
</form>
<?php 
	## 네이버 스마트 웹에디터 설정
	if($data['use_editor'] == 'true') $editor_info = naver_smart_editor('btn_form_submit', 'contents');
?>
</div>
<script name="import_jquery.ready">
/*	$('input:.chk_remove_file').click(function() {
		var input_box = $(this).parent().parent().children('p').children('input:.i_text');
		var file_box = $(this).parent().parent().children('input:.file_input');

		if($(this).is(':checked') == true) {
			input_box.show();
			file_box.show();
		} else {
			input_box.hide();
			file_box.hide();
		}
	});*/
	$("#v_btn_confirm").bind("click", function (event) {
		if($('#title').val() == '' && $('#title').length != 0) {
			alert('제목 입력은 필수사항입니다.');
			$('#title').focus();
			return false;
		}
		if($('#reg_name').val() == '' && $('#reg_name').length != 0) {
			alert('등록자 입력은 필수사항입니다.');
			$('#reg_name').focus();
			return false;
		}				
		return true;
	});
</script>