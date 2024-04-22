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
	$temp[] = strip_tags($data['contents']);
	/*print_r( $temp );	exit;*/
	$contents = get_form_contents( $temp );
}
############################# contetns #############################
## 등록자 표시 부분 열라 급조한것이다. 오픈이 없어서.. 나중에 다른 방식으로 수정해야한다.
## write, view, list 모두 중복 코드, 수정 필요!!
if($data['writer_display'] == 'department') $writer_name = '등록부서';
else if($data['writer_display'] == 'other_organ') $writer_name = '타기관';
else if($data['writer_display'] == 'processing') $writer_name = '처리부서';
else if($data['writer_display'] == 'charge_organ') $writer_name = '담당부서';
else $writer_name = '등록자';	
?>

<?php if(!empty($data['null_check'])) echo '<ul class="type03">'.$data['null_check'].'</ul>'; ?>

<?php	
//2012-10-09 황재복 : 글쓰기씨 상단문구가 빠져 있어 수정
## 상단문구 출력
if(!empty($data['write_msg'])) {
	if($data['device']=='mobile') echo '<p>'.stripslashes($data['write_msg']).'</p>';
	else echo '<div class="sub-tit-top"></div><div class="content_top_alert"><div class="alert_content">'.stripslashes($data['write_msg']).'</div></div><div class="sub-tit-bottom"></div>';
}
?>

						<div class="board_wrapper">
							<div class="module_write_box">
								<form id="write_form" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" name="selectForm" enctype="multipart/form-data">
									<input type="hidden" name="keyword_del" id="keyword_del" />		 <input type="hidden" name="board_id" id="board_id" value="<?php echo $_SYSTEM['module_config']['board_id']; ?>" />		
									<input type="hidden" name="keyword" id="keyword" value="" />									
									
									<div class="title_write">										
										<label for="title">게시글 제목<em>(*)</em></label>
										<input type="text" id="title" name="title" value="<?php echo $data['title']?>" placeholder="제목" />
									</div>
									<div class="cont_write">
										<strong class="import_txt">*(별표)는 필수항목입니다.</strong>
										<?php if($_SYSTEM['module_config']['board_id'] == 'www_newsletter_req') { ?>
										<input type="hidden" name="open" value="n" />
										<?php } else { ?>
										<?php
										if($data['use_lock'] == 'true'){	
											$open_allow = $data['open'] == 'y' ? 'checked="checked"' : '';
											$open_deny  = $data['open'] == 'y' ? '' : 'checked="checked"';
										?>
										<div class="write_box notice_box">
											<div class="module_t">												
												<label for="notice_check">공개여부<?php check_required_field_wirte_mode($_SYSTEM['module_config']['required_value'], 'open'); ?></label></div>
											<div class="module_w">	
												<input type="radio" name="open" id="open_allow" value="y" <?php echo $open_allow; ?> /><label for="open_allow">공개</label>&nbsp;&nbsp;&nbsp;
												<input type="radio" name="open" id="open_deny" value="n" <?php echo $open_deny; ?> /><label for="open_deny">비공개</label>
											</div>
										</div>										
										<?php } //if end ?>     
                                        <?php } ?>
													 
										<?php		
										## 공지글 등록 부분 삭제
										##----------------------
										
										##카테고리
										if($data['use_category_1'] == 'true' && in_array($data['mode'],array("write","modify") ) ){	
											$category_1_all = unserialize($data['category_1_all']);
											$category_1 = '';

											foreach($category_1_all as $key=>$value) {
												$category_1 .= '<option value="'.$value.'"'.($value==$data['category_1']?' selected="selected"':'').'>'.$value.'</option>';
											}
										?>
										<div class="write_box module_theme">
											<div class="module_t"><label for="category_1">카테고리 선택</label></div>
											<div class="module_w">
												<select id="category_1" name="category_1" class="custom_select">
													<?php echo $category_1;?>
												</select>
											</div>
										</div>
										<?php
										} //if end

										if($data['use_tag']=='true' && !empty($data['tag_list_all'])) {
											foreach($data['tag_list_all'] as $idx=>$value){
												$tag_list .= '<span class="tag_check"><input type="checkbox" name="tag_list[]" id="tag_list_'.$idx.'" value="'.$value.'" '.(in_array($value, $data['tag_list']) ? 'checked="checked"' : '').' /><label for="tag_list_'.$idx.'">'.$value.'<span class="icon"></span></label></span>';
											}
										?>
										<div class="write_box module_tegs">
											<div class="module_t"><label for="category_1">tag 등록</label></div>
											<div class="module_w">
												<?php echo $tag_list;?>
											</div>
										</div>
										<?php
										}
										
										?>				
										<?php if($_SYSTEM['module_config']['use_period'] == 'true') { ?>
										<?php
										## 기본은 게시기간 사용 안함. 사용했을떄만 설정 가능.
										if( empty($data['period_start']) && empty($data['period_end']) ){
											$period_checked = '';	
											$period_disable = ' disabled="disabled" '; 											
										}else{
											$period_checked = ' checked="checked"';	
											$period_disable = '';	
										}
										
										?>
										<div class="write_box date_select_box">
											<div class="module_t">
												<!--<strong>게시기간</strong>-->
												<input type="checkbox" name="period" id="period_check" <?php echo $period_checked; ?> />
												<label for="period_check">게시기간<span class="icon"></span></label>
											</div>
											<div class="module_w">
												<div class="base_box date_box">
													<input type="hidden" id="period_start_hidden" name="period_start_hidden" value="<?php echo $data['period_start']?>" />
													<label for="period_start" class="box_hidden">게시 시작시간</label>
													<input type="text" id="period_start" name="period_start" class="datetime datetime_normal" value="<?php echo str_replace("-","",$data['period_start']); ?>"  <?php echo empty($data['period_start'])?' placeholder="'.date('Ymd').'"':''; ?> <?php echo $period_disable;?> />
													<a href="#none" class="date_icon"></a> 
												</div>
												<span class="icon_text">~</span>
												<div class="base_box date_box">
													<input type="hidden" id="period_end_hidden" name="period_end_hidden" value="<?php echo $data['period_end']?>" />													
													<label for="period_end" class="box_hidden">게시 종료시간</label>
													<input type="text" id="period_end" name="period_end" class="datetime datetime_normal" value="<?php echo str_replace("-","",$data['period_end']); ?>" <?php echo empty($data['period_end'])?' placeholder="'.date('Ymd',strtotime("+7 day")).'"':''; ?> <?php echo $period_disable;?> />
													<a href="#none" class="date_icon"></a>
												</div>
											</div>
										</div>
										<?php } ?>
										<div class="write_box">
											<div class="module_t"><label for="reg_name"><?php
											//echo $data['writer_display'] == 'department' ? '등록부서' : '등록자'; ##등록자 옵션이 여러개라 위에서 배열로 생성해서 받아옴, 추가수정필요!
											echo $writer_name; ?><em>(*)</em></label></div>
											<div class="module_w">
												<?php if($data['writer_display'] == 'department') { ?>
												<input type="text" class="transparent_box _chk_input" name="depart_name" id="reg_name" value="<?php echo $data['depart_name']; ?>" />
												<?php } elseif($data['writer_display'] == 'other_organ') { ?>
												<input type="text" class="transparent_box _chk_input" name="reg_name" id="reg_name" value="<?php echo $data['reg_name']; ?>" />
												<?php } else { ?>
												<input type="text" class="transparent_box _chk_input" name="reg_name" id="reg_name" value="<?php echo $data['reg_name']; ?>" <?php echo ($data['read_only_reg_name'] == 'true' ? 'readonly="readonly"' : '')?> />
												<?php } ?>
											</div>
										</div>										

										<div class="write_box">
											<div class="module_t"><label for="depart_name">담당부서<em>(*)</em></label></div>
											<div class="module_w">
												<input type="text" class="module_text _chk_input" name="depart_name" id="depart_name" value="<?php echo $data['depart_name']; ?>" />
											</div>
										</div>																				

										<div class="write_box module_privacy" >
											<div class="module_t"><label for="contents"><strong>의회발언<em>(*)</em></strong></label></div>	
											<div class="privacy_textarea">
												<!--<input type=hidden name=title value="의회발언/시입장">-->
												<textarea id="contents" name="contents" class="_chk_input" rows=10><?php echo $data['contents']; ?></textarea>
											</div>
										</div>
										<div class="write_box module_privacy" >
											<div class="module_t"><label for="contents_en"><strong>시입장<em>(*)</em></strong></label></div>	
											<div class="privacy_textarea"><textarea id="contents_en" class="_chk_input" name="contents_en" rows=10><?php echo $data['contents_en']; ?></textarea></div>
										</div>										
										<!--내용부분-->
										<?php		
										## ----------- 키워드 : start
										echo keyword_input($data);
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
										<div class="write_box">
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
												<?php if($_GET['mode'] == 'modify' && $_SYSTEM['hostname'] == 'gongik') { ?>
												<p class="modify_cont rd"> ※ 글 수정 시 기존 등록된 첨부파일 변경 및 삭제를 하시려면 먼저 선택삭제 후 수정, 등록 바랍니다. </p>
												<?php } ?>
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
											//if($_SYSTEM['module_config']['use_privacy_write'] == 'true' && $_SYSTEM['module_config']['use_logoff_write'] == 'true' && $_SYSTEM['myinfo']['is_login'] != true ){ // 실제 사용 되어야할 if문
                                            if($_SYSTEM['module_config']['use_privacy_write'] == 'true' && $_SYSTEM['module_config']['use_logoff_write'] == 'true' ){ 
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
<script src="<?php echo $_SERVER['SELF']; ?>js/write_other.js?build=<?php echo SERIAL;?>" ></script>