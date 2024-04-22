<?php
echo $data['debug']; // only test..
?>
<?php if(!empty($data['null_check'])) echo '<ul class="type03">'.$data['null_check'].'</ul>';?>


<form id="write_form" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" name="selectForm" enctype="multipart/form-data">
<fieldset>
	<legend>필수입력사항</legend>

<?php
	//2012-10-09 황재복 : 글쓰기씨 상단문구가 빠져 있어 수정
	## 상단문구 출력
	if($_GET['mode'] != 'reply' && ($_GET['mode'] != 'modify' && $_SYSTEM['site_info']['level'] <= 1)){ //write 모드일 때만 출력 됨
		if(!empty($data['write_msg'])) {
			if($data['device']=='mobile') echo '<p>'.stripslashes($data['write_msg']).'</p>';
			else echo '<div class="board_guide2 guide_img5 new_agreebox_wrap">'.stripslashes($data['write_msg']);
?>
						 <div class="privacy_checks"> <!--20150921 정재윤 : 홍보물 신청 개인정보 동의(상단문구에 적용) -->
							<input type="checkbox" name="agree_privacy" class="custom-check" id="agree_privacy" value="y" <?php echo ($data['agree_privacy'] == 'y') ? 'checked="checked"' : '' ?>/>
							<label for="agree_privacy">홍보물 신청을 위한 개인정보 이용에 동의합니다.</label>        
						 </div>
					   </div>
<?php		
		}
	}
?>

		<?php
		if($data['use_lock'] == 'true'){
//			$open_allow = $data['open'] == 'y' ? 'checked="checked"' : '';
//			$open_deny  = $data['open'] == 'y' ? '' : 'checked="checked"';
		?>

		<input type="hidden" name="open" value="n" />

		<?php } //if end ?>
		
<div class="board_wrap">
<div class="form_write">
<p class="help_txt"><span class="icon_help2">도움말</span>(*)항목은 필수사항 입니다.</p>
<table class="board_write">
	<caption>이 표는 <?php echo $_SYSTEM['menu_info']['title']; ?> 내용 입력란을 제공하며 <?php if($data['use_lock'] == 'true') echo '공개여부, ';?> 제목, 등록자, <?php if($data['use_category_1'] == 'true') echo '분류, ';?> <?php if($data['use_tag']=='true' && !empty($data['tag_list_all']))  echo 'tag 등록, ';?> <?php if($data['get_guest_info'] == 'true' && ($_SYSTEM['myinfo']['user_level'] == 11 || $_SYSTEM['myinfo']['user_level'] == 99)) echo '회원주소, 회원연락처, ';?><?php if($data['mode'] != 'reply' && ($data['use_reply_sms'] == 'all' || $data['use_reply_sms'] == 'admin')) echo '답변 수신번호, ';?> 내용으로 구성되어 있습니다.</caption>
	<tbody>
		<!--tr>
            <th scope="row"><label for="title" >제목<em>(*)</em></label></th>
			<td>
                <?php 
				/*
                if($data['use_title_style'] == 'true' && !empty($data['title_style_list'])) {
                    foreach($data['title_style_list'] as $value) {
                        $title_style .= '<option value="'.$value.'"'.($value==$data['title_style']?' selected="selected"':'').'>'.$value.'</option>';
                    }
                    echo '<select name="title_style" id="title_style">'.$title_style.'</select>';
                }
				*/
                ?>
                <input type="text" class="w95" name="title" id="title" value="<?php// echo $data['title'];?>" />
            </td>
		</tr-->
		
		
		<?php
		if($_GET['mode'] == 'reply' || ($_GET['mode'] == 'modify' && $_SYSTEM['site_info']['level'] >= 1)){ 	// reply, 답변글일 때 조건문
		?>				
        <tr>
            <th scope="row"><label for="title" >제목<em>(*)</em></label></th>
			<td>
                <?php 
				
                if($data['use_title_style'] == 'true' && !empty($data['title_style_list'])) {
                    foreach($data['title_style_list'] as $value) {
                        $title_style .= '<option value="'.$value.'"'.($value==$data['title_style']?' selected="selected"':'').'>'.$value.'</option>';
                    }
                    echo '<select name="title_style" id="title_style">'.$title_style.'</select>';
                }
				
                ?>
                <input type="text" class="w95" name="title" id="title" value="<?php echo $data['title'];?>" />
            </td>
		</tr>
		<tr>
			<th scope="row"><label for="reg_name">작성자<em>(*)</em></label></th>
			<td>
                <input type="text" class="w20" name="reg_name" id="reg_name" value="<?php echo $data['reg_name']; ?>" <?php echo ($data['read_only_reg_name'] == 'true' ? 'readonly="readonly"' : '')?> />
            </td>
		</tr>
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
			<td>
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
            <td><div class="item"><?php echo $tag_list;?></div></td>
        </tr>
		<?php
		}
		?>
		<tr>
			<th scope="row"><label for="contents">답변 내용</label></th>
			<td><div class="item"><textarea name="contents" id="contents"><?php echo $data['contents'];?></textarea></div></td>
		</tr>
		
		<?php
		}else{
		?>
		
		<input type="hidden" name="title" id="title" value="관광책자신청" />
		<tr>
			<th scope="row"><label for="reg_name">신청자<em>(*)</em></label></th>
			<td>
                <input type="text" class="w20" name="reg_name" id="reg_name" value="<?php echo $data['reg_name']; ?>" <?php echo ($data['read_only_reg_name'] == 'true' ? 'readonly="readonly"' : '')?> />
            </td>
		</tr>
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
			<td>
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
            <td><div class="item"><?php echo $tag_list;?></div></td>
        </tr>
		<?php
		}
		?>
        <tr>
			<th scope="row"><label>연락처<em>(*)</em></label></th>
			<td>
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
				<input type="text" class="w20" id="phone_1" maxlength="5" size="5" name="phone_1" value="<?php echo $phone_1;?>" title="회원 연락처 첫자리"/> - 
				<input type="text" class="w20" id="phone_2" maxlength="4" size="5" name="phone_2" value="<?php echo $phone_2;?>" title="회원 연락처 중간자리"/> - 
				<input type="text" class="w20" id="phone_3" maxlength="4" size="5" name="phone_3" value="<?php echo $phone_3;?>" title="회원 연락처 마지막자리" />
                </div>
			</td>
		</tr>
		<tr>
			<th scope="row"><label>주소<em>(*)</em></label></th>
			<td><div class="item">
				<?php
				if(!empty($data['zipcode_1']) || !empty($data['zipcode_2'])) $data['zipcode'] = $data['zipcode_1'].$data['zipcode_2'];
				$address = array();
				$address['zipcode']   = $data['zipcode'];
				$address['address_1'] = $data['address_1'];
				$address['address_2'] = $data['address_2'];
				echo address_box_tour($address);
				echo '<p>※ 상세주소(아파트 동·호수 등)를 꼭 입력해주시기 바랍니다.</p>';
				?>
                </div>
			</td>
		</tr>		


		<?php if($data['mode'] != 'reply' && ($data['use_reply_sms'] == 'all' || $data['use_reply_sms'] == 'admin')) { ?>
		<tr>
			<th scope="row"><label for="reply_sms_recv_number">답변글 SMS 수신번호</label></th>
			<td><div class="item">
				<input type="text" class="i_text" id="reply_sms_recv_number" maxlength="14" size="14" name="reply_sms_recv_number" value="<?php echo $data['reply_sms_recv_number'];?>" title="답변글 SMS 수신번호" />
                </div>
			</td>
		</tr>
		<?php }?>

		<tr>
			<th scope="row"><label for="contents">요청글</label></th>
			<td><div class="item"><textarea name="contents" id="contents"><?php echo $data['contents'];?></textarea></div></td>
		</tr>
		
		<?php
		}
		
		if($data['use_search_tag'] == 'true'){ ?>
		<tr>
			<th scope="row"><label for="search_tag">검색태그</label></th>
			<td><div class="item"><input type="text" class="i_text" style="width:350px;" size="50" name="search_tag" id="search_tag" value="<?php echo $data['search_tag']?>" /></div></td>
		</tr>
		<?php
		}//if end 
		?>
        
		<?php
		if($data['use_logoff_write'] == 'true' && $_SYSTEM['myinfo']['is_login'] != true){ ?>
		<tr>
			<th scope="row"><label for="passwd">패스워드</label></th>
			<td><div class="item"><input type="password" class="i_text" style="width:350px;" size="50" name="passwd" id="passwd" value="" /></div></td>
		</tr>
		<tr>
			<th scope="row"><label for="search_tag">키코드입력</label></th>
			<td><div class="item">
                <p style="color:#00F"><?php echo $data['text_keycode'];?></p>
                <input type="text" class="i_text" style="width:350px;" size="50" name="text_keycode" id="text_keycode" value="" />
                </div>
            </td>
		</tr>
		<?php
		}//if end 
		?>
        <?php // 파일 업로드
		//첨부파일기능 수정 : 오경우 (20120120)
		for($iter=0; $iter<$data['file_upload_count']; $iter++){
		?>
		<tr>
			<th scope="row"><div class="title"><label for="file_<?php echo $iter;?>">파일<?php echo($iter+1);?></label></div></th>
			<td><div class="item"><?php echo upload_box($data['file_list'], $iter);?></div></td>
		</tr>
		<?php
		} //for end
		?>
		</tbody>
</table>
<?php     
	/*2014-04-22 서희진 : 개인정보처리방침 필수 체크 기능 구현.
	* 설정값 use_privacy_write 가 사용(True)일때 / use_logoff_write 가 설정(True) 일때 / 비회원일때 적용됩니다.
	* $_SYSTEM['module_config']['privacy_msg'] : 내용 
	* 테스트를 위해서 use_privacy_write 가 사용(True)일때만 보이도록 셋팅함. 개발후 if문 주석과 변경.
	*/
//	if($_SYSTEM['module_config']['use_privacy_write'] == 'true' && $_SYSTEM['module_config']['use_logoff_write'] == 'true' && $_SYSTEM['myinfo']['is_login'] != true ){
/*	if($_SYSTEM['module_config']['use_privacy_write'] == 'true' ){	
?>  
    <div class="privacy_checks"> 
    	<input type="checkbox" name="agree_privacy" class="custom-check" id="agree_privacy" value="y" <?php echo ($data['agree_privacy'] == 'y') ? 'checked="checked"' : '' ?>/>
		<label for="agree_privacy">홍보물 신청을 위한 개인정보 이용에 동의합니다.</label>        
    </div>
    
    <div>
    	<p><label for="privacy_html">개인정보처리방침</label></p>
	    <p class="joinPoint">아래 개인정보처리방침을 반드시 읽어보시고 '동의'에 체크하신 후 '확인'버튼을 눌러주세요</p>        
		<p><textarea name="privacy_html" id="privacy_html" rows="5" cols="100" readonly="readonly"><?php echo stripslashes($_SYSTEM['module_config']['privacy_msg']) ?>
        </textarea>
        </p>
		<p><label for="agree_privacy">개인정보처리방침에 동의합니다</label> <input type="checkbox" name = "agree_privacy" id="agree_privacy" value="y" <?php echo ($data['agree_privacy'] == 'y') ? 'checked="checked"' : '' ?>/></p>        
    </div>
<?php 
	} */
	// 개인정보 보호 방침 끝.
?>
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
	
	if($_SYSTEM['module_config']['use_privacy_write'] == 'true' ){ ## 개인정보이용동의체크시
		echo print_button('write', $data['permission'], $data['parameter'], $url, $img_url, $user_info, $arr_data);
	//	echo print_button_private_write_tour('write', $data['permission'], $data['parameter'], $url, $img_url, $user_info, $arr_data);
	} else {
		echo print_button('write', $data['permission'], $data['parameter'], $url, $img_url, $user_info, $arr_data);
	}
?>
</div>
</div>

</fieldset>
</form>
<?php 
	## 네이버 스마트 웹에디터 설정
//	if($data['use_editor'] == 'true') $editor_info = naver_smart_editor('btn_form_submit', 'contents');
?>

<script name="import_jquery.ready">
	/*$('input:.chk_remove_file').click(function() {
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
		var mode = '<?php echo $_GET['mode'] ?>';
		
			if( mode == 'reply'){
				
				if($('#title').val() == '') {
					alert('제목 입력은 필수사항입니다.');
					$('#title').focus();
					return false;
				}

				if($('#reg_name').val() == '') {
					alert('작성 입력은 필수사항입니다.');
					$('#reg_name').focus();
					return false;
				}
				return true;
				
			}else{
			
				if($("input:checkbox[id='agree_privacy']").is(":checked") == false ) {
					alert('개인정보취급위탁동의에 동의하셔야만 예약하실 수 있습니다.');
					$('#agree_privacy').focus();
					return false;
				}

				if($('#title').val() == '') {
					alert('제목 입력은 필수사항입니다.');
					$('#title').focus();
					return false;
				}

				if($('#reg_name').val() == '') {
					alert('신청자 입력은 필수사항입니다.');
					$('#reg_name').focus();
					return false;
				}

				if($('#phone_1').val() == '' && $('#phone_1').length != 0) {
					alert('연락처 첫번째 자리를 입력해 주시기 바랍니다.');
					$('#phone_1').focus();
					return false;
				}

				if($('#phone_2').val() == '' && $('#phone_2').length != 0) {
					alert('연락처 중간자리를 입력해 주시기 바랍니다.');
					$('#phone_2').focus();
					return false;
				}

				if($('#phone_3').val() == '' && $('#phone_3').length != 0) {
					alert('연락처 마지막자리를 입력해 주시기 바랍니다.');
					$('#phone_3').focus();
					return false;
				}	

				if($('#zipcode').val() == '' && $('#zipcode').length != 0) {
					alert('우편번호를 입력해 주시기 바랍니다.');
					$('#zipcode_1').focus();
					return false;
				}

				if($('#address_1').val() == '' && $('#address_1').length != 0) {
					alert('기본주소를 입력해 주시기 바랍니다.');
					$('#address_1').focus();
					return false;
				}
				return true;
			}
	});

</script>