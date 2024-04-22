<link rel="stylesheet" href="<?php echo $_SERVER['SELF'] ?>css/thumb_photo.css" type="text/css" />
<?php
echo $data['debug']; // only test..

//print_r($data);
//2012-10-10 황재복 : 휴대폰 번호 자동 차단 방지
if($_SYSTEM['module_config']['use_phone_filter'] == 'false') {  
	$data['title']=preg_replace('/01([016789])[-~.[:space:]]([0-9]{3,4})[-~.[:space:]]([0-9]{4})/','01\1－\2－\3',$data['title']);
	$data['contents']=preg_replace('/01([016789])[-~.[:space:]]([0-9]{3,4})[-~.[:space:]]([0-9]{4})/','01\1－\2－\3',$data['contents']);
}

############################# contetns #############################
if( $data['html_tag'] == "a" ){
	$data['contents'] = unserialize(base64_decode($data['contents']) );
	$contents = get_form_contents( $data['contents'] );																									
}else{
	$temp[] = strip_tags($data['contents']) ;
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
							<div class="module_write_box">
								<form id="write_form" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" name="selectForm" enctype="multipart/form-data">
									<input type="hidden" name="keyword_del" id="keyword_del" />				
									<input type="hidden" name="keyword" id="keyword" value="" />
									<div class="title_write">										
										<label for="title">게시글 제목</label>
										<input type="text" id="title" name="title" value="<?php echo $data['title']?>" placeholder="제목" />
									</div>
									<div class="cont_write">
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
                                        
                                        <?php 
                                        ##나주청년센터
                                        if($_SYSTEM['module_config']['board_id'] == 'najuyouth_review') {
                                        ?>
                                        <div class="write_box">
											<div class="module_t"><label for="phone">연락처<em>(*)</em></label></div>
											<div class="module_w">
												<input type="text" class="transparent_box" name="phone_1" id="phone_1" value="<?php if(empty($data['phone_1'])) echo $data['user_phone_1']; else echo $data['phone_1'] ?>" /> - <input type="text" class="transparent_box" name="phone_2" id="phone_2" value="<?php if(empty($data['phone_2'])) echo $data['user_phone_2']; else echo $data['phone_2'] ?>" /> - <input type="text" class="transparent_box" name="phone_3" id="phone_3" value="<?php if(empty($data['phone_3'])) echo $data['user_phone_3']; else echo $data['phone_3'] ?>" />
											</div>
										</div>
                                        
                                        <?php }
                                        ?>
                                        
										<?php
										## 서희진 작업 : 공지로 등록하기를 글쓰기/수정하기에서 할수 있도록.
										## 2018-09-04 황재복 : 박옥이 주무관 요청. 자유게시판 관리자만 공지글 사용 가능하게
										if($data['use_top'] == 'true' && $_SESSION['user_level'] <= 6 && in_array($data['mode'],array("write","modify") ) ){	
											$top_checked = $data['top'] == 'y' ? 'checked="checked"' : '';
										?>
										<div class="write_box notice_box">
											<div class="module_t">
												<input type="checkbox" name="top" id="notice_check" value="y" <?php echo $top_checked; ?> />
												<label for="notice_check">공지로 등록<span class="icon"></span></label></div>
											<div class="module_w">	
												<div class="base_box date_box">
													<input type="hidden" name="notice_start_date" class="data_hidden" />
													<label for="notice_start" class="box_hidden">공지 시작시간</label>
													<input type="text" id="notice_start" class="datetime datetime_notice" placeholder="<?php echo date('Ymd'); ?>" />
													<a href="#none" class="date_icon"></a> 
												</div>
												<span class="icon_text">~</span>
												<div class="base_box date_box">
													<input type="hidden" name="notice_finish_date" class="data_hidden" />
													<label for="notice_end" class="box_hidden">공지 종료시간</label>
													<input type="text" id="notice_end" class="datetime datetime_notice" placeholder="<?php echo date('Ymd',strtotime("+7 day")); ?>" />
													<a href="#none" class="date_icon"></a>
												</div>
											</div>
										</div>
										<?php } //if end 										
										
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
											foreach($data['tag_list_all'] as $value){
												$tag_list .= '<label><input type="checkbox" name="tag_list[]" value="'.$value.'" '.(in_array($value, $data['tag_list']) ? 'checked="checked"' : '').' />'.$value.'</label>';
											}
										?>
										<div class="write_box module_theme">
											<div class="module_t"><label for="category_1">tag 등록</label></div>
											<div class="module_w">
												<?php echo $tag_list;?>
											</div>
										</div>
										<?php
										}
										
										?>	
										<div class="write_box">
											<div class="module_t"><label for="reg_name">작성자</label></div>
											<div class="module_w">
												<?php if($data['writer_display'] == 'department') { ?>
												<input type="text" class="transparent_box" name="depart_name" id="reg_name" value="<?php echo $data['depart_name']; ?>" />
												<?php } elseif($data['writer_display'] == 'other_organ') { ?>
												<input type="text" class="transparent_box" name="reg_name" id="reg_name" value="<?php echo $data['reg_name']; ?>" />
												<?php } else { ?>
												<input type="text" class="transparent_box" name="reg_name" id="reg_name" value="<?php echo $data['reg_name']; ?>" <?php echo ($data['read_only_reg_name'] == 'true' ? 'readonly="readonly"' : '')?> />
												<?php } ?>
											</div>
										</div>
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
												<input type="checkbox" name="use_period" id="period_check" value="y" <?php echo $period_checked; ?> />
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
										<div class="write_box">
											<div class="module_t"><label for="link_url">URL</label></div>
											<div class="module_w">
												<input type="text" id="link_url" name="link_url" class="module_text w100" value="<?php echo $data['link_url'] ?>" />
											</div>
										</div>
										
										<!--내용부분-->
										<div class="write_contbox">
											<div class="module_t" id="append_btn_zone">
												<strong>내용</strong><span>입력버튼을 클릭하면 아래에 입력박스가 생성됩니다.</span>
												<ul class="append_btn">
													<li class="text_append"><a href="#none"><span class="icon"></span>텍스트 입력</a></li>
													<li class="image_append">
														<div class="cont_image_add" style="display:none;">
															<input type="file" id="cont_image" name="cont_image[]" />
														</div>														
														<a href="#none" class="_write_file_icon"><span class="icon"></span>이미지 입력</a>
													</li>
													<li class="link_append"><a href="#none"><span class="icon"></span>링크 입력</a></li>
												</ul>												
											</div>
											<div class="module_w" id="cont_zone">												
												<?php 
												## -----------  콘텐츠 수정.: start
												echo $contents; 
												## ------------ 콘텐츠 수정 : end
												?>
											</div>
										</div>
										<?php
										if($_SYSTEM['hostname'] == 'gongik') { ## 공익에서는 입력버튼 하단에도 추가 요청
										?>
										<div class="append_btn_bottom">
											<ul class="append_btn">
												<li class="text_append"><a href="#none"><span class="icon"></span>텍스트 입력</a></li>
												<li class="image_append">
													<div class="cont_image_add" style="display:none;">
														<input type="file" id="cont_image" name="cont_image[]" data-width="<?php echo $_SYSTEM['module_config']['contents_img_width_size']; ?>" />
													</div>														
													<a href="#none" class="_write_file_icon"><span class="icon"></span>이미지 입력</a>
												</li>
												<li class="link_append"><a href="#none"><span class="icon"></span>링크 입력</a></li>
											</ul>
										</div>	
										<?php
										}
										?>
										<!--내용부분-->
										<?php
										## ----------- 키워드 : start
										echo keyword_input($data);
										## -----------  키워드 : end
										?>	
										
										<?php
										## ----------- 테마 설정 : start
										echo theme_input();
										## -----------  테마 설정 : end
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
										<?php
										if( $_SYSTEM['module_config']['skin_style'] == 'thumb_photo' ){
										?>
										<div class="write_box module_file">
											<div class="module_t">
												<strong>메인 이미지</strong>
												<?php echo ( $_SYSTEM['module_config']['use_gallery_img'] == 'true')?'<em>(*)</em>':''?>												
											</div>
											<div class="module_w">												
												<p class="file_size_cont"> 1016x620 또는 같은비율 최소 323x197 </p>
												<?php 
												//print_r( $_SYSTEM['module_config']['ext_photo_file'] );
												 ## 갤러리 필수
												 $ext_file_img = $_SYSTEM['module_config']['ext_photo_file'];
												 $use_gallery_img = true; 												 
												 if($data['file_upload_count'] > 0) echo upload_box_main($data['file_list_main'], $data['idx'], 0, $use_gallery_img, $ext_file_img , 'true', 1 );												
												?>
											</div>											
										</div>	
										<?php 
										}
										?>
										<div class="write_box module_file">
											<div class="module_t"><strong>파일첨부</strong></div>
											<div class="module_w">												
												<?php 
												//print_r( $_SYSTEM['module_config']['ext_photo_file'] );
												 ## 갤러리 필수
												/* $use_gallery_img = false;
												 if( $_SYSTEM['module_config']['use_gallery_img'] == 'true'){
													 $ext_file = $_SYSTEM['module_config']['ext_photo_file'];
													 $use_gallery_img = true; 
												 }*/
												 
												 if($data['file_upload_count'] > 0) echo upload_box_alt_tour($data['file_list'], $data['idx'], 0, $use_gallery_img, $ext_file , $data['file_upload_count']);												
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
<script src="/js/jquery/datetimepicker/jquery.datetimepicker.js"></script>
<link rel="stylesheet" href="/js/jquery/datetimepicker/jquery.datetimepicker.css" type="text/css"  />
<script src="<?php echo $_SERVER['SELF']; ?>js/write.js" ></script>
<script>
	$(".main_images").on("click",function(){
		
		
	});
</script>