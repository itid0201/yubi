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
							<div class="module_write_box">
								<form id="write_form" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" name="selectForm" enctype="multipart/form-data">
									<input type="hidden" name="keyword_del" id="keyword_del" />		<input type="hidden" name="board_id" id="board_id" value="<?php echo $_SYSTEM['module_config']['board_id']; ?>" />				
									<input type="hidden" name="keyword" id="keyword" value="" />
									<div class="title_write">										
										<label for="title">프로그램<em>(*)</em></label>
										<input type="text" id="title" name="title" value="<?php echo $data['title']?>" placeholder="프로그램" />
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
										## 서희진 작업 : 공지로 등록하기를 글쓰기/수정하기에서 할수 있도록.
										## 2018-09-04 황재복 : 박옥이 주무관 요청. 자유게시판 관리자만 공지글 사용 가능하게
										if($data['use_top'] == 'true' && $_SESSION['user_level'] <= 6 && in_array($data['mode'],array("write","modify") ) ){	
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
										
										if($_SYSTEM['module_config']['board_id'] != 'www_newsletter_req') {
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
												<input type="text" class="transparent_box" name="depart_name" id="reg_name" value="<?php echo $data['depart_name']; ?>" />
												<?php } elseif($data['writer_display'] == 'other_organ') { ?>
												<input type="text" class="transparent_box" name="reg_name" id="reg_name" value="<?php echo $data['reg_name']; ?>" />
												<?php } else { ?>
												<input type="text" class="transparent_box" name="reg_name" id="reg_name" value="<?php echo $data['reg_name']; ?>" <?php echo ($data['read_only_reg_name'] == 'true' ? 'readonly="readonly"' : '')?> />
												<?php } ?>
											</div>
										</div>
										<div class="write_box notice_box">
											<div class="module_t">	
												<label for="varchar_1">대표자명<em>(*)</em>
												</label>
                                            </div>
											<div class="module_w">	
												<input type="text" name="varchar_1" id="varchar_1" class="module_text _chk_input" value="<?php echo $data['varchar_1']; ?>" />
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
												<input type="text" name="phone_1" id="phone_1" value="<?php echo $phone_1;?>" class="text_phone module_text _chk_input" maxlength="3" numberonly />
												- <input type="text" name="phone_2" id="phone_2" value="<?php echo $phone_2;?>" class="text_phone module_text _chk_input" maxlength="4" numberonly />
												- <input type="text" name="phone_3" id="phone_3" value="<?php echo $phone_3;?>" class="text_phone module_text _chk_input" maxlength="4" numberonly />
											</div>
												<span class="bigo_txt"> * 입력예) 010-1234-1234</span>
											</div>
										</div>
										<div class="write_box notice_box">
											<div class="module_t">	
												<label for="email">이메일<em>(*)</em>
												</label>
                                            </div>
											<div class="module_w">	
												<input type="text" name="email" id="email" class="module_text _chk_input" value="<?php echo $data['email']; ?>" />
											</div>
										</div>
										<div class="write_box write_inputbox">
											<div class="module_t"><label for="zipcode">주소<em>(*)</em></label></div>
											<div class="module_w">
												<tr>
													<div class="item">
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
										</div>
										<div class="write_box module_theme">
											<div class="module_t">	
												<label for="varchar_2">지역구분<em>(*)</em>
												</label>
                                            </div>
											<div class="module_w">	
											<div class="selectric-wrapper selectric-custom_select">
												<select name="varchar_2" id="varchar_2" class="custom_select _chk_input">
													<option value="">지역선택</option>
													<option value="경호동" <?php if($data['varchar_2']=="경호동") echo 'selected'; ?> >경호동</option>
													<option value="고소동" <?php if($data['varchar_2']=="고소동") echo 'selected'; ?> >고소동</option>
													<option value="공화동" <?php if($data['varchar_2']=="공화동") echo 'selected'; ?>>공화동</option>
													<option value="관문동" <?php if($data['varchar_2']=="관문동") echo 'selected'; ?>>관문동</option>
													<option value="광무동" <?php if($data['varchar_2']=="광무동") echo 'selected'; ?>>광무동</option>
													<option value="교동" <?php if($data['varchar_2']=="교동") echo 'selected'; ?>>교동</option>
													<option value="국동" <?php if($data['varchar_2']=="국동") echo 'selected'; ?>>국동</option>
													<option value="군자동" <?php if($data['varchar_2']=="군자동") echo 'selected'; ?>>군자동</option>
													<option value="낙포동" <?php if($data['varchar_2']=="낙포동") echo 'selected'; ?>>낙포동</option>
													<option value="남면" <?php if($data['varchar_2']=="남면") echo 'selected'; ?>>남면</option>
													<option value="남산동" <?php if($data['varchar_2']=="남산동") echo 'selected'; ?>>남산동</option>
													<option value="덕충동" <?php if($data['varchar_2']=="덕충동") echo 'selected'; ?>>덕충동</option>
													<option value="돌산읍" <?php if($data['varchar_2']=="돌산읍") echo 'selected'; ?>>돌산읍</option>
													<option value="동산동" <?php if($data['varchar_2']=="동산동") echo 'selected'; ?>>동산동</option>
													<option value="둔덕동" <?php if($data['varchar_2']=="둔덕동") echo 'selected'; ?>>둔덕동</option>
													<option value="만흥동" <?php if($data['varchar_2']=="만흥동") echo 'selected'; ?>>만흥동</option>
													<option value="묘도동" <?php if($data['varchar_2']=="묘도동") echo 'selected'; ?>>묘도동</option>
													<option value="문수동" <?php if($data['varchar_2']=="문수동") echo 'selected'; ?>>문수동</option>
													<option value="미평동" <?php if($data['varchar_2']=="미평동") echo 'selected'; ?>>미평동</option>
													<option value="봉강동" <?php if($data['varchar_2']=="봉강동") echo 'selected'; ?>>봉강동</option>
													<option value="봉계동" <?php if($data['varchar_2']=="봉계동") echo 'selected'; ?>>봉계동</option>
													<option value="봉산동" <?php if($data['varchar_2']=="봉산동") echo 'selected'; ?>>봉산동</option>
													<option value="삼산면" <?php if($data['varchar_2']=="삼산면") echo 'selected'; ?>>삼산면</option>
													<option value="상암동" <?php if($data['varchar_2']=="상암동") echo 'selected'; ?>>상암동</option>
													<option value="서교동" <?php if($data['varchar_2']=="서교동") echo 'selected'; ?>>서교동</option>
													<option value="선원동" <?php if($data['varchar_2']=="선원동") echo 'selected'; ?>>선원동</option>
													<option value="소라면" <?php if($data['varchar_2']=="소라면") echo 'selected'; ?>>소라면</option>
													<option value="소호동" <?php if($data['varchar_2']=="소호동") echo 'selected'; ?>>소호동</option>
													<option value="수정동" <?php if($data['varchar_2']=="수정동") echo 'selected'; ?>>수정동</option>
													<option value="시전동" <?php if($data['varchar_2']=="시전동") echo 'selected'; ?>>시전동</option>
													<option value="신기동" <?php if($data['varchar_2']=="신기동") echo 'selected'; ?>>신기동</option>
													<option value="신덕동" <?php if($data['varchar_2']=="신덕동") echo 'selected'; ?>>신덕동</option>
													<option value="신월동" <?php if($data['varchar_2']=="신월동") echo 'selected'; ?>>신월동</option>
													<option value="안산동" <?php if($data['varchar_2']=="안산동") echo 'selected'; ?>>안산동</option>
													<option value="여서동" <?php if($data['varchar_2']=="여서동") echo 'selected'; ?>>여서동</option>
													<option value="여천동" <?php if($data['varchar_2']=="여천동") echo 'selected'; ?>>여천동</option>
													<option value="연등동" <?php if($data['varchar_2']=="연등동") echo 'selected'; ?>>연등동</option>
													<option value="오림동" <?php if($data['varchar_2']=="오림동") echo 'selected'; ?>>오림동</option>
													<option value="오천동" <?php if($data['varchar_2']=="오천동") echo 'selected'; ?>>오천동</option>
													<option value="웅천동" <?php if($data['varchar_2']=="웅천동") echo 'selected'; ?>>웅천동</option>
													<option value="월내동" <?php if($data['varchar_2']=="월내동") echo 'selected'; ?>>월내동</option>
													<option value="월하동" <?php if($data['varchar_2']=="월하동") echo 'selected'; ?>>월하동</option>
													<option value="율촌면" <?php if($data['varchar_2']=="율촌면") echo 'selected'; ?>>율촌면</option>
													<option value="적량동" <?php if($data['varchar_2']=="적량동") echo 'selected'; ?>>적량동</option>
													<option value="종화동" <?php if($data['varchar_2']=="종화동") echo 'selected'; ?>>종화동</option>
													<option value="주삼동" <?php if($data['varchar_2']=="주삼동") echo 'selected'; ?>>주삼동</option>
													<option value="중앙동" <?php if($data['varchar_2']=="중앙동") echo 'selected'; ?>>중앙동</option>
													<option value="중흥동" <?php if($data['varchar_2']=="중흥동") echo 'selected'; ?>>중흥동</option>
													<option value="충무동" <?php if($data['varchar_2']=="충무동") echo 'selected'; ?>>충무동</option>
													<option value="평여동" <?php if($data['varchar_2']=="평여동") echo 'selected'; ?>>평여동</option>
													<option value="학동" <?php if($data['varchar_2']=="학동") echo 'selected'; ?>>학동</option>
													<option value="학용동" <?php if($data['varchar_2']=="학용동") echo 'selected'; ?>>학용동</option>
													<option value="해산동" <?php if($data['varchar_2']=="해산동") echo 'selected'; ?>>해산동</option>
													<option value="호명동" <?php if($data['varchar_2']=="호명동") echo 'selected'; ?>>호명동</option>
													<option value="화양면" <?php if($data['varchar_2']=="화양면") echo 'selected'; ?>>화양면</option>
													<option value="화장동" <?php if($data['varchar_2']=="화장동") echo 'selected'; ?>>화장동</option>
													<option value="화정면" <?php if($data['varchar_2']=="화정면") echo 'selected'; ?>>화정면</option>
													<option value="화치동" <?php if($data['varchar_2']=="화치동") echo 'selected'; ?>>화치동</option>
												</select>
												</div>
											</div>
										</div>
										<div class="write_box module_theme">
											<div class="module_t">	
												<label for="varchar_3">분류<em>(*)</em>
												</label>
                                            </div>
											<div class="module_w">	
												<div class="selectric-wrapper selectric-custom_select">
													<select name="varchar_3" id="varchar_3" class="custom_select _chk_input">
													<option value="">분류 선택</option>
													<option value="생활체육" <?php if($data['varchar_3']=="생활체육") echo 'selected'; ?>>생활체육</option>
													<option value="컴퓨터" <?php if($data['varchar_3']=="컴퓨터") echo 'selected'; ?>>컴퓨터</option>
													<option value="영어" <?php if($data['varchar_3']=="영어") echo 'selected'; ?>>영어</option>
													<option value="일본어" <?php if($data['varchar_3']=="일본어") echo 'selected'; ?>>일본어</option>
													<option value="중국어" <?php if($data['varchar_3']=="중국어") echo 'selected'; ?> >중국어</option>
													<option value="건강" <?php if($data['varchar_3']=="건강") echo 'selected'; ?> >건강</option>
													<option value="요리&amp;인테리어" <?php if($data['varchar_3']=="요리&amp;인테리어") echo 'selected'; ?>>요리&amp;인테리어</option>
													<option value="취미와여가" <?php if($data['varchar_3']=="취미와여가") echo 'selected'; ?>>취미와여가</option>
													<option value="자녀교육" <?php if($data['varchar_3']=="자녀교육") echo 'selected'; ?>>자녀교육</option>
													<option value="교양" <?php if($data['varchar_3']=="교양") echo 'selected'; ?>>교양</option>
													<option value="공예/인테리어" <?php if($data['varchar_3']=="공예/인테리어") echo 'selected'; ?>>공예/인테리어</option>
													<option value="마케팅/영업" <?php if($data['varchar_3']=="마케팅/영업") echo 'selected'; ?>>마케팅/영업</option>
													<option value="재테크/부동산" <?php if($data['varchar_3']=="재테크/부동산") echo 'selected'; ?>>재테크/부동산</option>
													<option value="평생학습" <?php if($data['varchar_3']=="평생학습") echo 'selected'; ?>>평생학습</option>
													</select>
												</div>
											</div>
										</div>
										<div class="write_box notice_box">
											<div class="module_t">	
												<label for="varchar_4">회원가입조건</label>
                                            </div>
											<div class="module_w">
												<input type="text" name="varchar_4" id="varchar_4" class="module_text" value="<?php echo $data['varchar_4']; ?>" />
											</div>
										</div>
										<div class="write_box notice_box">
											<div class="module_t">	
												<label for="varchar_5">정기모임</label>
                                            </div>
											<div class="module_w">
												<input type="text" name="varchar_5" id="varchar_5" class="module_text" value="<?php echo $data['varchar_5']; ?>" />
											</div>
										</div>
										<div class="write_box notice_box">
											<div class="module_t">	
												<label for="varchar_6">정기모임장소</label>
                                            </div>
											<div class="module_w">
												<input type="text" name="varchar_6" id="varchar_6" class="module_text" value="<?php echo $data['varchar_6']; ?>" />
											</div>
										</div>
										<!--div class="write_box notice_box">
											<div class="module_t">	
												<label for="varchar_7">창립일</label>
                                            </div>
											<div class="module_w">
												<input type="text" name="varchar_7" id="varchar_7" class="module_text datetime" value="<?php echo $data['varchar_7']; ?>" />
											</div>
										</div-->
										
										
										<!--내용부분-->
										<div class="write_contbox">
											<div class="module_t" id="append_btn_zone">
												<strong>동아리설명<em>(*)</em></strong><span>입력버튼을 클릭하면 아래에 입력박스가 생성됩니다.</span>
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
<script src="<?php echo $_SERVER['SELF']; ?>js/write_mobile.js" ></script>