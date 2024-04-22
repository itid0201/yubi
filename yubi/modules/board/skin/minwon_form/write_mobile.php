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
	## 상단문구 출력
	if(!empty($data['write_msg'])) {
		if($data['device']=='mobile') echo '<p>'.stripslashes($data['write_msg']).'</p>';
		else echo '<div class="sub-tit-top"></div><div class="content_top_alert"><div class="alert_content">'.stripslashes($data['write_msg']).'</div></div><div class="sub-tit-bottom"></div>';
	}
?>
						<div class="board_wrapper">
							<div class="module_write_box">
								<form id="write_form" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" name="selectForm" enctype="multipart/form-data">
									<input type="hidden" name="keyword_del" id="keyword_del" />	<input type="hidden" name="board_id" id="board_id" value="<?php echo $_SYSTEM['module_config']['board_id']; ?>" />	
									<input type="hidden" name="keyword_del" id="keyword_del" />				
									<input type="hidden" name="keyword" id="keyword" value="" />
									<div class="cont_write">
										<input type="hidden" class="module_text" name="reg_name" id="reg_name" value="<?php echo ($data['reg_name']==""?"user":$data['reg_name']); ?>" />
										
										<!--내용부분-->
										<div class="write_box">
											<div class="module_t"><label for="title">민원사무명<em>(*)</em></label></div>
											<div class="module_w">
												<input type="text" class="module_text" name="title" id="title" value="<?php echo $data['title']; ?>" />
											</div>
										</div>
										<div class="write_box module_theme">
											<div class="module_t"><label for="varchar_5">민원분야</label></div>
											<div class="module_w">
												<select name="varchar_5" id="varchar_5" class="custom_select"> 
													<option value="공통">공통</option>
												</select>
											</div>
										</div>
										<div class="write_box">
											<div class="module_t"><label for="longtext_1">민원내용</label></div>
											<div class="module_w">
												<textarea name="longtext_1" id="longtext_1" value=""><?php echo $data['longtext_1'];?></textarea>
											</div>
										</div>
										<div class="write_box">
											<div class="module_t"><label for="longtext_2">관계법령</label></div>
											<div class="module_w">
												<textarea name="longtext_2" id="longtext_2" value=""><?php echo $data['longtext_2'];?></textarea>
											</div>
										</div>
										<div class="write_box">
											<div class="module_t"><label for="longtext_3">구비서류 </label></div>
											<div class="module_w">
												<textarea name="longtext_3" id="longtext_3" value=""><?php echo $data['longtext_3'];?> </textarea>
											</div>
										</div>
										
										<?php
										##카테고리
										if($data['use_category_1'] == 'true' && in_array($data['mode'],array("write","modify") ) ){	
											$category_1_all = unserialize($data['category_1_all']);
											$category_1 = '';

											foreach($category_1_all as $key=>$value) {
												$category_1 .= '<option value="'.$value.'"'.($value==$data['category_1']?' selected="selected"':'').'>'.$value.'</option>';
											}
										?>
										<div class="write_box module_theme">
											<div class="module_t"><label for="category_1">주무부서<em>(*)</em></label></div>
											<div class="module_w">
												<select id="category_1" name="category_1" class="custom_select">
													<?php echo $category_1;?>
												</select>
											</div>
										</div>
										<?php } ?>
										
										<div class="write_box">
											<div class="module_t"><label for="longtext_4">협의부서</label></div>
											<div class="module_w">
												<textarea name="longtext_4" id="longtext_4" value=""><?php echo $data['longtext_4'];?></textarea>
											</div>
										</div>
										<div class="write_box">
											<div class="module_t"><label for="varchar_1">접수</label></div>
											<div class="module_w">
												<input type="text" class="module_text" name="varchar_1" id="varchar_1" value="<?php echo $data['varchar_1']; ?>" />
											</div>
										</div>	
										<div class="write_box">
											<div class="module_t"><label for="varchar_2">수수료</label></div>
											<div class="module_w">
												<input type="text" class="module_text" name="varchar_2" id="varchar_2" value="<?php echo $data['varchar_2']; ?>" />
											</div>
										</div>
										<div class="write_box">
											<div class="module_t"><label for="varchar_3">처리기한</label></div>
											<div class="module_w">
												<input type="text" class="module_text" name="varchar_3" id="varchar_3" value="<?php echo $data['varchar_3']; ?>" />
											</div>
										</div>
										<div class="write_box">
											<div class="module_t"><label for="varchar_4">기타사항</label></div>
											<div class="module_w">
												<input type="text" class="module_text" name="varchar_4" id="varchar_4" value="<?php echo $data['varchar_4']; ?>" />
											</div>
										</div>
										<div class="write_box">
											<div class="module_t"><label for="longtext_5">흐름도</label></div>
											<div class="module_w">
												<textarea name="longtext_5" id="longtext_5" value=""><?php echo $data['longtext_5'];?> </textarea>
											</div>
										</div>
										
										<div class="write_box">
											<div class="module_t"><label for="longtext_6">기타참고사항</label></div>
											<div class="module_w">
												<textarea name="longtext_6" id="longtext_6" value=""><?php echo $data['longtext_6'];?> </textarea>
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
										<div class="write_box module_file">
											<div class="module_t"><strong>파일첨부</strong></div>
											<div class="module_w">												
												<?php 
												//print_r($data['file_list']);
												 ## 갤러리 필수
												 $use_gallery_img = false;
												 if( $_SYSTEM['module_config']['use_gallery_img'] == 'true')	$use_gallery_img = true;
												 if($data['file_upload_count'] > 0) echo upload_box_alt_tour($data['file_list'], $data['idx'], 0, $use_gallery_img, false, $data['file_upload_count']);												
												?>
											</div>											
										</div>	
										<?php     
											/*2014-04-22 서희진 : 개인정보처리방침 필수 체크 기능 구현.
											* 설정값 use_privacy_write 가 사용(True)일때 / use_logoff_write 가 설정(True) 일때 / 비회원일때 적용됩니다.
											* $_SYSTEM['module_config']['privacy_msg'] : 내용 
											* 테스트를 위해서 use_privacy_write 가 사용(True)일때만 보이도록 셋팅함. 개발후 if문 주석과 변경.
											*/
											if($_SYSTEM['module_config']['use_privacy_write'] == 'true' && $_SYSTEM['module_config']['use_logoff_write'] == 'true' && $_SYSTEM['myinfo']['is_login'] != true ){ // 실제 사용 되어야할 if문
										//	if($_SYSTEM['module_config']['use_privacy_write'] == 'true' ){	
										?>

											<div class="write_box module_privacy">
												<div class="module_t"><strong>개인정보처리방침</strong></div>
												<div class="module_w">
													<p class="joinPoint">아래 개인정보처리방침을 반드시 읽어보시고 '동의'에 체크하신 후 '확인'버튼을 눌러주세요</p>        
													<p>
														<textarea name="privacy_html" id="privacy_html" rows="5" cols="100" readonly><?php echo stripslashes($_SYSTEM['module_config']['privacy_msg']) ?></textarea>
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