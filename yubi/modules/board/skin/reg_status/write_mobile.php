<?php
echo $data['debug']; // only test..

//print_r($data);
//2012-10-10 황재복 : 휴대폰 번호 자동 차단 방지
if($_SYSTEM['module_config']['use_phone_filter'] == 'false') {  
	$data['title']=preg_replace('/01([016789])[-~.[:space:]]([0-9]{3,4})[-~.[:space:]]([0-9]{4})/','01\1－\2－\3',$data['title']);
	$data['contents']=preg_replace('/01([016789])[-~.[:space:]]([0-9]{3,4})[-~.[:space:]]([0-9]{4})/','01\1－\2－\3',$data['contents']);
}
//echo $data['mode'];
############################# contetns #############################
if( $data['html_tag'] == "a" || $data['mode'] == "write" ){
	$data['contents'] = unserialize(base64_decode($data['contents']) );	
	$contents = get_form_contents( $data['contents'] );																									
}else{	
	$temp[] = $data['contents'];
	/*print_r( $temp );	exit;*/
	$contents = get_form_contents( $temp );
}
############################# contetns #############################
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
	<div class="board_wrapper">
		<div class="module_write_box m_reg_status">
			<form id="write_form" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" name="selectForm" enctype="multipart/form-data">
				<input type="hidden" name="keyword_del" id="keyword_del" /> <input type="hidden" name="board_id" id="board_id" value="<?php echo $_SYSTEM['module_config']['board_id']; ?>" />				
				<input type="hidden" name="keyword" id="keyword" value="" />
				<div class="cont_write">
					<strong class="import_txt">*(별표)는 필수항목입니다.</strong>
					<input type="hidden" class="module_text" name="reg_name" id="reg_name" value="<?php echo ($data['reg_name']==""?"user":$data['reg_name']); ?>" />
					<div class="write_box">
						<div class="module_t"><label for="title">관리번호<em>(*)</em></label></div>
						<div class="module_w">
							<input type="text" class="module_text" name="title" id="title" value="<?php echo $data['title']; ?>" />
						</div>
					</div>
					<div class="write_box">
						<div class="module_t"><label for="contents">규제사무명<em>(*)</em></label></div>
						<div class="module_w">
							<input type="text" class="module_text" name="contents" id="contents" value="<?php echo $data['contents']; ?>" />
						</div>
					</div>
					<div class="write_box">
						<div class="module_t"><label for="depart_name">담당부서</label></div>
						<div class="module_w">
							<input type="text" class="module_text" name="depart_name" id="depart_name" value="<?php echo $data['depart_name']; ?>" />
						</div>
					</div>
					<div class="write_box">
						<div class="module_t"><label for="varchar_1">상위법령</label></div>
						<div class="module_w">
							<input type="text" class="module_text" name="varchar_1" id="varchar_1" value="<?php echo $data['varchar_1']; ?>" />
						</div>
					</div>
					<div class="write_box">
						<div class="module_t"><label for="varchar_2">조례</label></div>
						<div class="module_w">
							<input type="text" class="module_text" name="varchar_2" id="varchar_2" value="<?php echo $data['varchar_2']; ?>" />
						</div>
					</div>
					<div class="write_box">
						<div class="module_t"><label for="varchar_3">규칙</label></div>
						<div class="module_w">
							<input type="text" class="module_text" name="varchar_3" id="varchar_3" value="<?php echo $data['varchar_3']; ?>" />
						</div>
					</div>
					<div class="write_box">
						<div class="module_t"><label for="varchar_4">훈령</label></div>
						<div class="module_w">
							<input type="text" class="module_text" name="varchar_4" id="varchar_4" value="<?php echo $data['varchar_4']; ?>" />
						</div>
					</div>
					<div class="write_box">
						<div class="module_t"><label for="varchar_5">예규</label></div>
						<div class="module_w">
							<input type="text" class="module_text" name="varchar_5" id="varchar_5" value="<?php echo $data['varchar_5']; ?>" />
						</div>
					</div>
					<div class="write_box">
						<div class="module_t"><label for="varchar_6">고시등</label></div>
						<div class="module_w">
							<input type="text" class="module_text" name="varchar_6" id="varchar_6" value="<?php echo $data['varchar_6']; ?>" />
						</div>
					</div>
					<div class="write_box">
						<div class="module_t"><label for="varchar_7">유형</label></div>
						<div class="module_w">
							<input type="text" class="module_text" name="varchar_7" id="varchar_7" value="<?php echo $data['varchar_7']; ?>" />
						</div>
					</div>
					<div class="write_box">
						<div class="module_t"><label for="varchar_8">등록사유</label></div>
						<div class="module_w">
							<input type="text" class="module_text" name="varchar_8" id="varchar_8" value="<?php echo $data['varchar_8']; ?>" />
						</div>
					</div>
					<div class="write_box">
						<div class="module_t"><label for="varchar_9">법령공표일</label></div>
						<div class="module_w">
							<input type="text" class="module_text" name="varchar_9" id="varchar_9" value="<?php echo $data['varchar_9']; ?>" />
						</div>
					</div>
					<div class="write_box">
						<div class="module_t"><label for="varchar_10">부문</label></div>
						<div class="module_w">
							<input type="text" class="module_text" name="varchar_10" id="varchar_10" value="<?php echo $data['varchar_10']; ?>" />
						</div>
					</div>
					<div class="write_box">
						<div class="module_t"><label for="varchar_11">존속기한</label></div>
						<div class="module_w">
							<input type="text" class="module_text" name="varchar_11" id="varchar_11" value="<?php echo $data['varchar_11']; ?>" />
						</div>
					</div>
					<div class="write_box">
						<div class="module_t"><label for="varchar_12">규제시행/폐지일</label></div>
						<div class="module_w">
							<input type="text" class="module_text" name="varchar_12" id="varchar_12" value="<?php echo $data['varchar_12']; ?>" />
						</div>
					</div>
					<div class="write_box">
						<div class="module_t"><label for="contents_en">규제목적</label></div>
						<div class="module_w">
							<textarea name="contents_en" id="contents_en" /><?php echo $data['contents_en']; ?></textarea>
						</div>
					</div>
					<div class="write_box">
						<div class="module_t"><label for="contents_jp">규제내용</label></div>
						<div class="module_w">
							<textarea name="contents_jp" id="contents_jp" /><?php echo $data['contents_jp']; ?></textarea>
						</div>
					</div>
					<div class="write_box">
						<div class="module_t"><label for="contents_cn">등록후변동결과</label></div>
						<div class="module_w">
							<textarea name="contents_cn" id="contents_cn" /><?php echo $data['contents_cn']; ?></textarea>
						</div>
					</div>										

					<!--내용부분-->
					<?php
					## ----------- 키워드 : start
					echo keyword_input();
					## -----------  키워드 : end
					?>	

					<?php
					## ----------- 테마 설정 : start
					if( $data['use_theme'] == 'true' ){ 	
					echo theme_input($data);
					## -----------  테마 설정 : end
					}
					?>											
					<?php
					## 공공누리 입력창 2017.12.12 서희진 추가
					if( $data['use_open_type'] == 'true' ){ 		 
					?>
					<div class="write_box module_open">
						<div class="module_t"><strong>공공누리적용</strong><em>(*)</em></div>
						<div class="module_w">
							<?php echo upload_koglType_kogl($data);?>
						</div>
					</div>
					<?php
					}//공공누리 입력창 끝
					?>																													
					<?php     
						/*2014-04-22 서희진 : 개인정보처리방침 필수 체크 기능 구현.
						* 설정값 use_privacy_write 가 사용(True)일때 / use_logoff_write 가 설정(True) 일때 / 비회원일때 적용됩니다.
						* $_SYSTEM['module_config']['privacy_msg'] : 내용 
						* 테스트를 위해서 use_privacy_write 가 사용(True)일때만 보이도록 셋팅함. 개발후 if문 주석과 변경.
						*/
						//if($_SYSTEM['module_config']['use_privacy_write'] == 'true' && $_SYSTEM['module_config']['use_logoff_write'] == 'true' && $_SYSTEM['myinfo']['is_login'] != true ){ // 실제 사용 되어야할 if문
						if($_SYSTEM['module_config']['use_privacy_write'] == 'true' && $_SYSTEM['module_config']['use_logoff_write'] == 'true' ){ 
						//if($_SYSTEM['module_config']['use_privacy_write'] == 'true' ){	
					?>

						<div class="write_box module_privacy">
							<div class="module_t"><strong>개인정보처리방침</strong></div>
							<div class="module_w">
								<p class="joinPoint">아래 개인정보처리방침을 반드시 읽어보시고 '동의'에 체크하신 후 '확인'버튼을 눌러주세요</p>        
								<p>
									<textarea name="privacy_html" id="privacy_html" class="privacy_box" readonly><?php echo stripslashes($_SYSTEM['module_config']['privacy_msg']) ?></textarea>
								</p>
								<p>
									<label for="agree_privacy">개인정보처리방침에 동의합니다</label> 
									<input type="checkbox" name = "agree_privacy" id="agree_privacy" value="y" <?php echo ($data['agree_privacy'] == 'y') ? 'checked="checked"' : '' ?>/>
								</p>        
							</div>
						</div>
					<?php 
						} // 개인정보 보호 방침 끝.
					?>											
				</div>
									
<?php
	$hidden = '';
	foreach($data['hidden'] as $key=>$value) if(!is_null($value)) $hidden .= '<input type="hidden" name="'.$key.'" id="'.$key.'" value="'.$value.'" />';
	echo $hidden;

	## 버튼 영역.
	$img_url   = '/images/common/board/temp';
	$url       = $_SERVER['PHP_SELF'];
	$user_info = array();
	$user_info['is_login'] = empty($_SYSTEM['myinfo']['is_login']) ? NULL : $_SYSTEM['myinfo']['is_login'];
	$user_info['user_pin'] = empty($_SYSTEM['myinfo']['my_pin']) ? NULL : $_SYSTEM['myinfo']['my_pin'];
	$arr_data['reg_pin']   = empty($data['reg_pin']) ? NULL : $data['reg_pin'];
	$arr_data['del']       = empty($data['del']) ? NULL : $data['del'];
	$arr_data['use_logoff_write'] = empty($data['use_logoff_write']) ? NULL : $data['use_logoff_write'];
	
/*	if($_SYSTEM['module_config']['use_privacy_write'] == 'true' ){ ## 개인정보이용동의체크시
		echo print_button_private_write('write', $data['permission'], $data['parameter'], $url, $img_url, $user_info, $arr_data);
	} else {
		echo print_button_new('write', $data['permission'], $data['parameter'], $url, $img_url, $user_info, $arr_data);
	}*/

	## --------------------- 버튼 : start
	$print_button =  print_button_new('write', $data['permission'], $data['parameter'], $url, $img_url, $user_info, $arr_data);
	if( !empty($print_button ) ){
	echo '<!-- 버튼 START --><div class="board_btn_box align_right">'.$print_button.'</div><!-- 버튼 END -->';	
	}
	## --------------------- 버튼 : end

?>									
								</form>
							</div>
						</div>

<?php 
	## 네이버 스마트 웹에디터 설정
//	if($data['use_editor'] == 'true') $editor_info = naver_smart_editor('v_btn_confirm', 'contents');
?>
<script>
	var use_theme = "<?php echo $data['use_theme']; ?>";
</script>
<script src="/js/jquery/datetimepicker/jquery.datetimepicker.js"></script>
<link rel="stylesheet" href="/js/jquery/datetimepicker/jquery.datetimepicker.css" type="text/css"  />
<script src="<?php echo $_SERVER['SELF']; ?>js/write_other_mobile.js" ></script>