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
									<input type="hidden" name="keyword_del" id="keyword_del" />		 <input type="hidden" name="board_id" id="board_id" value="<?php echo $_SYSTEM['module_config']['board_id']; ?>" />		
									<input type="hidden" name="keyword" id="keyword" value="" />
									
                                    <div class="title_write">										
										<label for="title">게시글 제목<em>(*)</em></label>
										<input type="text" id="title" name="title" value="<?php echo $data['title']?>" placeholder="제목" />
									</div>
									<div class="cont_write">
										<strong class="import_txt">*(별표)는 필수항목입니다.</strong>
										<?php if($_SYSTEM['module_config']['board_id'] == 'www_newsletter_req' ) { ?>
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
										## 서희진 작업 : 공지로 등록하기를 글쓰기/수정하기에서 할수 있도록.
										## 2018-09-04 황재복 : 박옥이 주무관 요청. 자유게시판 관리자만 공지글 사용 가능하게
										if($data['use_top'] == 'true' && ($_SYSTEM['permission']['admin'] == 'true' || $_SYSTEM['permission']['manage'] == 'true') && in_array($data['mode'],array("write","modify") ) ){	
											if( $data['top'] == 'y' ){
												$top_checked =  ' checked="checked"';	
												$top_disable = '';	
											}else{
												$top_checked = '';	
												$top_disable = ' disabled="disabled" '; 				
											}
											
										?>
										<div class="write_box date_select_box">
											<div class="module_t">												
												<input type="checkbox" name="top" id="notice_check" value="y" <?php echo $top_checked; ?> />
												<label for="notice_check">공지로 등록<span class="icon"></span></label></div>
											<div class="module_w">	
												<div class="base_box date_box">
													<input type="hidden" id="top_start_hidden" name="top_start_hidden" value="<?php echo $data['top_start']?>" />
													<label for="top_start" class="box_hidden">공지 시작시간</label>
													<input type="text" id="top_start" name="top_start" class="datetime datetime_notice" value="<?php echo str_replace("-","",date('Y-m-d',strtotime($data['top_start']))); ?>" <?php echo empty($data['top_start'])?' placeholder="'.date('Ymd').'"':''; ?>  <?php echo $top_disable;?> />
													<a href="#none" class="date_icon"></a> 
												</div>
												<span class="icon_text">~</span>
												<div class="base_box date_box">
													<input type="hidden" id="top_end_hidden" name="top_end_hidden" value="<?php echo $data['top_end']?>" />
													<label for="top_end" class="box_hidden">공지 종료시간</label>
													<input type="text" id="top_end" name="top_end" class="datetime datetime_notice" value="<?php echo str_replace("-","",date('Y-m-d',strtotime($data['top_end']))); ?>"  <?php echo empty($data['top_end'])?' placeholder="'.date('Ymd',strtotime("+7 day")).'"':''; ?>  <?php echo $top_disable;?> />
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
										<?php if($_SYSTEM['module_config']['board_id'] != 'www_newsletter_req') { ?>
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
											<div class="module_t"><label for="reg_name"><?php echo $data['writer_display'] == 'department' ? '등록부서' : '등록자'; ?><em>(*)</em></label></div>
											<div class="module_w">
												<?php if($data['writer_display'] == 'department') { ?>
												<input type="text" class="module_text _chk_input" name="depart_name" id="reg_name" value="<?php echo $data['depart_name']; ?>" />
												<?php } elseif($data['writer_display'] == 'other_organ') { ?>
												<input type="text" class="module_text _chk_input" name="reg_name" id="reg_name" value="<?php echo $data['reg_name']; ?>" />
												<?php } else { ?>
												<input type="text" class="module_text _chk_input" name="reg_name" id="reg_name" value="<?php echo $data['reg_name']; ?>" <?php echo ($data['read_only_reg_name'] == 'true' ? 'readonly="readonly"' : '')?> />
												<?php } ?>
											</div>
										</div>
										<?php 
                                             ## 여수 교육경비 보조사업 추가
                                            if($_SYSTEM['module_config']['board_id'] == 'www_assist_service') {
                                                echo '
												<div class="write_box">
													<div class="module_t">	
														<label for="varchar_1">보조사업자</label>
													</div>
													<div class="module_w">	
														<input type="text" name="varchar_1" id="varchar_1" class="module_text _chk_input" value="'.$data['varchar_1'].'" />
													</div>
											    </div>';
												echo '
												<div class="write_box">
													<div class="module_t">	
														<label for="varchar_2">2018년지원액</label>
													</div>
													<div class="module_w">	
														<input type="text" name="varchar_2" id="varchar_2" class="module_text _chk_input" value="'.$data['varchar_2'].'" />
													</div>
											    </div>';
                                            }
										
										?>             
										
										<div class="write_box">
											<div class="module_t"><label for="reg_name">주소<em>(*)</em></label></div>
											<div class="module_w">
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
										<div class="write_box write_inputbox">
											<div class="module_t"><label for="reg_name">연락처<em>(*)</em></label></div>
											<div class="module_w">
												<?php
												if(!empty($data['phone_1']) || !empty($data['phone_2']) || !empty($data['phone_3'])) {
													$phone_1 = $data['phone_1'];
													$phone_2 = $data['phone_2'];
													$phone_3 = $data['phone_3'];
												} else {
													if(!empty($data['phone'])) list($phone_1, $phone_2, $phone_3) = explode('-',$data['phone']);
												}
												?>		
												<div class="split_box r_none">
													<input type="text" name="phone_1" id="phone_1" value="<?php echo $phone_1;?>" class="text_phone module_text _chk_input"  maxlength="3"  />
													-</div>
												<div class="split_box r_none"><input type="text" name="phone_2" id="phone_2" value="<?php echo $phone_2;?>" class="text_phone module_text _chk_input"  maxlength="4" />-</div>
												<div class="split_box r_none"><input type="text" name="phone_3" id="phone_3" value="<?php echo $phone_3;?>" class="text_phone module_text _chk_input"  maxlength="4" /></div>			
											</div>
										</div>
										<!--내용부분-->
										<div class="write_contbox">
											<div class="module_t" id="append_btn_zone">
												<strong>내용<em>(*)</em></strong><span>입력버튼을 클릭하면 아래에 입력박스가 생성됩니다.</span>
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
											<div class="module_w" id="cont_zone">												
												<?php 
												## -----------  콘텐츠 수정.: start
												echo $contents; 
												## ------------ 콘텐츠 수정 : end												
												?>
											</div>
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
										
										<?php
										if( $_SYSTEM['module_config']['use_gallery_img'] == 'true' ){
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
										
										<p class="basic mab20"><span class="star_c"></span><span class="mar20">서식을 내려받아 작성후 파일 첨부해주세요.</span> <a class="btn_dw hwp" href="/download/citizen/reserve_counseling.hwp">서식다운로드</a></p>
										<div class="write_box module_file">
											<div class="module_t"><strong>파일첨부<em>(*)</em></strong></div>
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
                                        //    if($_SYSTEM['module_config']['use_privacy_write'] == 'true' && $_SYSTEM['module_config']['use_logoff_write'] == 'true' ){ 
											if($_SYSTEM['module_config']['use_privacy_write'] == 'true' ){	
										?>
                                           
											<div class="write_box module_privacy">
												<div class="module_t"><strong>개인정보처리방침</strong></div>
												<div class="module_w">
													<p class="joinPoint">아래 개인정보처리방침을 반드시 읽어보시고 '동의'에 체크하신 후 '확인'버튼을 눌러주세요</p>        
													<p class="mat10">
														<textarea name="privacy_html" id="privacy_html" readonly class="minwon_privacy_txt"><?php echo stripslashes($_SYSTEM['module_config']['privacy_msg']) ?></textarea>
													</p>
													<p class="basic">
														<label for="agree_privacy">개인정보처리방침에 동의</label> 
														<input class="_chk_input" type="checkbox" name = "agree_privacy" id="agree_privacy" value="y" <?php echo ($data['agree_privacy'] == 'y') ? 'checked="checked"' : '' ?>/>
													</p>
													<p class="basic"><span class="star_a"></span>민원 내용이 부패·공익신고에 해당 될 경우 『부패방지 권익위법』및 『공익신고자 보호법』에 따라 신고자 보호를 받을 수 있음을 안내해 드립니다.</p>
													<p class="basic"><span class="star_a"></span>국민신문고를 사칭하여 금융정보를 요구하는 사기전화(보이스 피싱) 사례가 발생하고 있습니다. 행정기관에서 전화로 금융정보 등을 요구하는 경우는 없으니 피해가 발생하지 않도록 주의하시기 바랍니다.</p>
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
<script src="<?php echo $_SERVER['SELF']; ?>js/write.js?build=<?php echo SERIAL;?>" ></script>