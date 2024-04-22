<?php
echo $data['debug']; // only test..

//2012-10-10 황재복 : 휴대폰 번호 자동 차단 방지
if($_SYSTEM['module_config']['use_phone_filter'] == 'false') {  
	$data['title']=preg_replace('/01([016789])[-~.[:space:]]([0-9]{3,4})[-~.[:space:]]([0-9]{4})/','01\1－\2－\3',$data['title']);
	$data['contents']=preg_replace('/01([016789])[-~.[:space:]]([0-9]{3,4})[-~.[:space:]]([0-9]{4})/','01\1－\2－\3',$data['contents']);
}
?>

<?php if(!empty($data['null_check'])) echo '<ul class="type03">'.$data['null_check'].'</ul>';?>

<?php
	//2012-10-09 황재복 : 글쓰기씨 상단문구가 빠져 있어 수정
	## 상단문구 출력
	if(!empty($data['write_msg'])) {
		if($data['device']=='mobile') echo '<p>'.stripslashes($data['write_msg']).'</p>';
		else echo '<div class="sub-tit-top"></div><div class="content_top_alert"><div class="alert_content">'.stripslashes($data['write_msg']).'</div></div><div class="sub-tit-bottom"></div>';
	}
?>
<form id="write_form" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" name="selectForm" enctype="multipart/form-data">
<fieldset>
	<legend class="write_label">필수입력사항</legend>
   

		<?php
		if($data['use_lock'] == 'true'){
			$open_allow = $data['open'] == 'y' ? 'checked="checked"' : '';
			$open_deny  = $data['open'] == 'y' ? '' : 'checked="checked"';
		?>
		<div class="mob">
            <label class="title">공개여부<?php check_required_field_wirte_mode($_SYSTEM['module_config']['required_value'], 'open'); ?></label>
            <input type="radio" name="open" id="open_allow" value="y" <?php echo $open_allow; ?> /><label for="open_allow">공개</label>
            <input type="radio" name="open" id="open_deny" value="n" <?php echo $open_deny; ?> /><label for="open_deny">비공개</label>
		</div>
		<?php } //if end ?>    
		<div class="write_box">
		<div class="module_t">제목<em>(*)</em><?php check_required_field_wirte_mode($_SYSTEM['module_config']['required_value'], 'title'); ?></div>
		<div class="module_w">	
			<?php 
			if($data['use_title_style'] == 'true' && !empty($data['title_style_list'])) {
				foreach($data['title_style_list'] as $value) {
					$title_style .= '<option value="'.$value.'"'.($value==$data['title_style']?' selected="selected"':'').'>'.$value.'</option>';
				}
				echo '<select name="title_style" id="title_style">'.$title_style.'</select>';
			}
			?>
			<input type="text" name="title" id="title" value="<?php echo $data['title'];?>" placeholder="제목을 입력하세요" data-clear-btn="true" />
		</div>
		</div>	
		<div class="mob">
        		<label for="reg_name" class="title">등록자<em>(*)</em><?php check_required_field_wirte_mode($_SYSTEM['module_config']['required_value'], 'open'); ?></label>

				<?php if($data['writer_display'] == 'department') { ?>
                <input type="text"  name="depart_name" id="reg_name" value="<?php echo $data['depart_name']; ?>" placeholder="부서명을 입력하세요" />
                <?php } elseif($data['writer_display'] == 'other_organ') { ?>
                <input type="text"  name="reg_name" id="reg_name" value="<?php echo $data['reg_name']; ?>" placeholder="이름을 입력하세요" />
                <?php } else { ?>
                <input type="text"  name="reg_name" id="reg_name" value="<?php echo $data['reg_name']; ?>" <?php echo ($data['read_only_reg_name'] == 'true' ? 'readonly="readonly"' : '')?> placeholder="이름을 입력하세요" />
                <?php } ?>

         </div>
	<?php
		if($data['use_category_1'] == 'true') {
			$category_1_all = unserialize($data['category_1_all']);
			$category_1 = '';

			foreach($category_1_all as $key=>$value) {
				$category_1 .= '<option value="'.$value.'"'.($value==$data['category_1']?' selected="selected"':'').'>'.$value.'</option>';
			}
		?>
        <div class="mob"><label for="category_1" class="title">분류<?php check_required_field_wirte_mode($_SYSTEM['module_config']['required_value'], 'category_1'); ?></label>
	            <select name="category_1" id="category_1"><?php echo $category_1;?></select>
			</div>
        <?php
        } //if end

		if($data['use_tag']=='true' && !empty($data['tag_list_all'])) {
			foreach($data['tag_list_all'] as $value){
				$tag_list .= '<label><input type="checkbox" name="tag_list[]" value="'.$value.'" '.(in_array($value, $data['tag_list']) ? 'checked="checked"' : '').' />'.$value.'</label>';
			}
		?>
        <div class="mob">
        <label>tag 등록</label>
            <?php echo $tag_list;?>
        </div>
		<?php
		}
		
		?>
		<div class="mob">
        <label class="title">회원주소<span>(<strong style="color: #ff4444">*</strong>)</span></label>
        		<div class="mob1">
				<?php
				if(!empty($data['zipcode_1']) || !empty($data['zipcode_2'])) $data['zipcode'] = $data['zipcode_1'].$data['zipcode_2'];
				$address = array();
				$address['zipcode']   = $data['zipcode'];
				$address['address_1'] = $data['address_1'];
				$address['address_2'] = $data['address_2'];
				echo address_box($address);
				?>
                </div>
			</div>
            
		<div class="mob">
        	<label class="title">연락처<span>(<strong style="color: #ff4444">*</strong>)</span></label>
				<?php
				if(!empty($data['phone_1']) || !empty($data['phone_2']) || !empty($data['phone_3'])) {
					$phone_1 = $data['phone_1'];
					$phone_2 = $data['phone_2'];
					$phone_3 = $data['phone_3'];
				} else {
					if(!empty($data['phone'])) list($phone_1, $phone_2, $phone_3) = explode('-',$data['phone']);
				}
				?>
				<input type="text" id="phone_1" class="w20" maxlength="5" size="5" name="phone_1" value="<?php echo $phone_1;?>" title="회원 연락처 첫자리"/> - 
				<input type="text" id="phone_2" class="w20" maxlength="4" size="5" name="phone_2" value="<?php echo $phone_2;?>" title="회원 연락처 중간자리"/> - 
				<input type="text" id="phone_3" class="w20" maxlength="4" size="5" name="phone_3" value="<?php echo $phone_3;?>" title="회원 연락처 마지막자리" />

			</div>
		


		<?php if($data['mode'] != 'reply' && ($data['use_reply_sms'] == 'all' || $data['use_reply_sms'] == 'admin')) { ?>
		<div class="mob">
        	<label for="reply_sms_recv_number" class="title">답변 수신번호</label>
			<input type="text" id="reply_sms_recv_number" maxlength="14" size="14" name="reply_sms_recv_number" value="<?php echo $data['reply_sms_recv_number'];?>" title="답변글 SMS 수신번호" />

			</div>
		<?php }?>
        
		<div class="mob">
        <label for="contents" class="title">내용<em>(*)</em><?php check_required_field_wirte_mode($_SYSTEM['module_config']['required_value'], 'contents'); ?></label>
			<textarea name="contents" id="contents" rows="25" cols="70" placeholder="내용을 입력하세요"><?php echo $data['contents'];?></textarea>
		</div>
        
        </fieldset>

<fieldset>
	<legend class="write_label">추가입력사항</legend>

		<?php
		if($data['use_search_tag'] == 'true'){ ?>
		<div class="mob">
            <label for="search_tag">검색태그</label>
            <input type="text" size="50" name="search_tag" id="search_tag" value="<?php echo $data['search_tag']?>" />
        </div>
		<?php
		}//if end 
		?>
		<?php
		if($data['use_logoff_write'] == 'true' && $_SYSTEM['myinfo']['is_login'] != true){ ?>
		<div class="mob">
            <label for="passwd">패스워드<?php check_required_field_wirte_mode($_SYSTEM['module_config']['required_value'], 'passwd'); ?></label>
            <input type="password"  size="50" name="passwd" id="passwd" value="" />
         </div>
		<div class="mob">
            <label for="search_tag">키코드입력<?php check_required_field_wirte_mode($_SYSTEM['module_config']['required_value'], 'text_keycode'); ?></label>
            <p style="color:#00F"><?php echo $data['text_keycode'];?></p>
            <input type="text"  size="50" name="text_keycode" id="text_keycode" value="" />
           
		</div>
		<?php
		}//if end 
		?>
        <?php // 파일 업로드
		//첨부파일기능 수정 : 오경우 (20120120)
		for($iter=0; $iter<$data['file_upload_count']; $iter++){
		?>
		<div class="mob">
            <label for="file_<?php echo $iter;?>">파일<?php echo($iter+1);?></label>
            <?php echo upload_box($data['file_list'], $iter);?>
		</div>
		<?php
		} //for end
		?>
<?php     
	/*2014-04-22 서희진 : 개인정보처리방침 필수 체크 기능 구현.
	* 설정값 use_privacy_write 가 사용(True)일때 / use_logoff_write 가 설정(True) 일때 / 비회원일때 적용됩니다.
	* $_SYSTEM['module_config']['privacy_msg'] : 내용 
	* 테스트를 위해서 use_privacy_write 가 사용(True)일때만 보이도록 셋팅함. 개발후 if문 주석과 변경.
	*/
//	if($_SYSTEM['module_config']['use_privacy_write'] == 'true' && $_SYSTEM['module_config']['use_logoff_write'] == 'true' && $_SYSTEM['myinfo']['is_login'] != true ){
	if($_SYSTEM['module_config']['use_privacy_write'] == 'true' ){	
?>

    <div>
    	<p><label for="privacy_html">개인정보처리방침</label></p>
	    <p class="joinPoint">아래 개인정보처리방침을 반드시 읽어보시고 '동의'에 체크하신 후 '확인'버튼을 눌러주세요</p>        
		<p><textarea name="privacy_html" id="privacy_html" rows="5" cols="100" readonly><?php echo stripslashes($_SYSTEM['module_config']['privacy_msg']) ?>
        </textarea>
        </p>
		<p><label for="agree_privacy">개인정보처리방침에 동의합니다</label> <input type="checkbox" name = "agree_privacy" id="agree_privacy" value="y" <?php echo ($data['agree_privacy'] == 'y') ? 'checked="checked"' : '' ?>/></p>        
    </div>
<?php 
	}
	// 개인정보 보호 방침 끝.
?>
</fieldset>

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
		echo print_button_private_write('write', $data['permission'], $data['parameter'], $url, $img_url, $user_info, $arr_data);
	} else {
		echo print_button_mobile('write', $data['permission'], $data['parameter'], $url, $img_url, $user_info, $arr_data);
	}
?>
</form>
<?php 
	## 네이버 스마트 웹에디터 설정
	//if($data['use_editor'] == 'true') $editor_info = naver_smart_editor('btn_form_submit', 'contents');
?>

<script name="import_jquery.ready">

	$("form#write_form").bind("submit", function () {
		if($('#title').val() == '') {
			alert('제목 입력은 필수사항입니다.');
			$('#title').focus();
			return false;
		}
		
		if($('#reg_name').val() == '') {
			alert('등록자 입력은 필수사항입니다.');
			$('#reg_name').focus();
			return false;
		}
		
		if($('#zipcode').val() == '') {
			alert('우편번호 입력은 필수사항입니다.');
			$('#phone_1').focus();
			return false;
		}
		if($('#address_1').val() == '') {
			alert('주소입력은 필수사항입니다.');
			$('#phone_1').focus();
			return false;
		}
		
		if($('#phone_1').val() == ''&& $('#phone_1').length != 0) {
			alert('연락처 첫자리는 필수사항입니다.');
			$('#phone_1').focus();
			return false;
		}
		
		if($('#phone_2').val() == ''&& $('#phone_2').length != 0) {
			alert('연락처 중간자리는 필수사항입니다.');
			$('#phone_2').focus();
			return false;
		}
		
		if($('#phone_3').val() == ''&& $('#phone_3').length != 0) {
			alert('연락처 마지막자리는 필수사항입니다.');
			$('#phone_3').focus();
			return false;
		}		
		
		if($('#contents').val() == '') {
			alert('내용 입력은 필수사항입니다.');
			$('#contents').focus();
			return false;
		}
		return true;
	});

</script>