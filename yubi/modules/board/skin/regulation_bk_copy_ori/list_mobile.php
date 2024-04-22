<link rel="stylesheet" type="text/css" href="/style/common/approval_mobile.css" />
<?php	
	## 목록 리스트 ==========================================================
	/***********
	** list_data : 실제 데이타 출력되는 목록
	** list_data_paging : 모바일에서만 사용 페이지별 목록	
	** list_buffer : 데이타별 임시 저장
	***********/	
	/* list skin */
	$lock_img  = '<span class="icon_lock">비공개글</span>';
	$reply_img = '<span class="icon_reply">답변글</span>';
	$new_img   = '<span class="icon_new">새로운글</span>';
	$top_img   = '<span class="icon_notice">공지글</span>';


	$stat_str = array(
	'1'=>'민원신청',
	'2'=>'민원접수',
	'3'=>'부서지정',
	'4'=>'담당지정',
	'5'=>'결재진행',
	'6'=>'일부완료',
	'7'=>'처리완료');

	$stat_class = array(
	'1'=>'btn_round_green',
	'2'=>'btn_round_blue',
	'3'=>'btn_round_blue',
	'4'=>'btn_round_blue',
	'5'=>'btn_round_blue',
	'6'=>'btn_round_black',
	'7'=>'btn_round_black');


	echo $data['debug']; // only test..
	
	## 등록자 표시 부분 열라 급조한것이다. 오픈이 없어서.. 나중에 다른 방식으로 수정해야한다.
	if($data['writer_display'] == 'department') $writer_name = '등록부서';
	else if($data['writer_display'] == 'other_organ') $writer_name = '기관/부서명';
	else if($data['writer_display'] == 'processing') $writer_name = '처리부서';
	else $writer_name = '등록자';	
	
	## ================================================================================================	
	## 목록 출력..
	## ================================================================================================		
	$buffer = array();
	$data['parameter']['mode'] = 'view';
	$view_parameter = make_GET_parameter($data['parameter']);
	$print_table_tr ='';		
	$original_cnt = 0;

	for($iter=0 ; $iter<$data['count'] ; $iter++) {
		$buffer = $data['list'][$iter];			
		
		##---- class 선처리 -- start
		## 작성자명 설정.
		if(empty($data['writer_display']) || in_array($data['writer_display'],array('person', 'other_organ'))) {
			$writer = $buffer['reg_name'];
		} else {
			$writer = empty($buffer['depart_name']) ? $buffer['reg_name'] : $buffer['depart_name'];
		}						
		
		$link_class = 'basic_cont ';
		
		if($buffer['is_lock'] == 'true') {	//비공개글
			$link_class .= 'list_lock';
			$href_view = '#none';
		} else {
			$href_view = $_SERVER['PHP_SELF'].'?idx='.$buffer['idx'].(empty($view_parameter) ? '' : '&amp;'.$view_parameter);
		}
		
		## 삭제글표시 
		if($buffer['is_delete'] == 'true') $link_class .= empty($link_class) ? 'del' : ' del';	
		
		## 제목 옆에 붙는 아이콘 
		$print_img = '';
		$print_img_cnt = 0;
		
		if($buffer['lock_img'] == 'true') {		## 공개/비공개
			$print_img .= $lock_img;	
			$print_img_cnt++;
		}		
		if($buffer['allow'] == 'n' && $_SYSTEM['permission']['admin'] == 'true') {		## 승인/비승인
			$print_img .= $hidden_img; 
			$print_img_cnt++;
		}
		if($buffer['new_img'] == 'true') {		##새글
			$print_img .= $new_img;
			$print_img_cnt++;
		}
		//if($buffer['comment_cnt'] > 0) print_img .= '<span class="comment_cnt">'.$buffer['comment_cnt'].'</span>'; //댓글개수 수정 : 오경우(20120125)				
		if($buffer['reply_cnt'] > 0) {		## 답글 처리 	
			$print_img .=  $reply_img ;
			$print_img_cnt++;
		}
		$print_img = !empty($print_img)?$print_img:'';
		$link_class .= $print_img_cnt>0?' icon_0'.$print_img_cnt:'';
		## 제목 a 태그 클래스 선처리 맨 마지막에 와야함. 
		$link_class = empty($link_class) ? '' : 'class="'.$link_class.'"';						
		##---- class 선처리 -- end		
		
		## ================================================================================================	
		##  출력 
		## ================================================================================================			
		$print_table_tr = '<div class="item">
									<a href="'.$href_view.'" title="'.$buffer['title'].'에 대한 글내용 보기."  '.$link_class.'>
										<span class="proc '.$stat_class[$buffer['process_2']].'">'.$stat_str[$buffer['process_2']].'</span>
										<strong class="title">'.($buffer['is_top']=='true' ? $top_img:'').$buffer['title'].$print_img.'</strong>
										<div class="approval_info_box"><span class="approval_date">'.$buffer['reg_date'].'</span>	
										<span class="visit_cnt">조회 '.$buffer['visit_cnt'].'</span>
										<span class="depart">'.$buffer['process_3'].'</span></div>							
									</a>
							</div>';
		$original_cnt++;
		if($buffer['is_top']=='true') $original_cnt--;
		else $list_data_paging .= $print_table_tr;
		
		$print_table .= $print_table_tr;
		## ================================================================================================			
	}	

	
	if( empty($print_table) || $original_cnt < 1 ){		
		$print_table .= '<div class="no_result"><span class="icon"></span>검색내역이 없습니다.</div>';
	}	
?>
<div class="board_wrapper">
	<div class="module_list_box">
		<?php 
		## 상단문구 출력

		if($_SYSTEM['config']['site_name']=='개인업무' || $_SYSTEM['config']['site_name']=='완도군청관리자' ) {
			if($_SYSTEM['myinfo']['user_level'] == 1) echo '<span><a href="/ybscript.io/business/excel_view_comment?table_name='.$data['table_name'].'&amp;board_id='.$data['board_id'].'&amp;board_name='.urlencode($data['board_name']).'" target="_blank">열람사유 출력</a></span>';
			$_GET['mode']='list';
		} else if($_SYSTEM['module_config']['board_id'] == 'immovables') { // 20160808 부동산불법중개행위신고 문구 다르게 수정
			$login_text = '[신고대상]	<ul>
		<li>부동산 거래가격 허위신고 등 : 중개업소의 다운계약 강요행위, 다운·업계약서<br /> 작성 및 신고행위</li>
		<li>청약통장 불법거래 : 청약통장 불법 양도·양수 알선 및 그 광고행위</li>
		<li>분양권 불법전매 :  전매금지 기간 내 분양권 전매 및 알선, 중개행위</li>
		<li>임시중개시설물 설치 : 떴다방 등 불법시설물 설치행위</li>
		<li>토지거래허가 위반 : 토지거래허가구역내 허가없이 계약체결, 이용의무 위반 등</li>
		<li><strong>각 시·군 홈페이지에 허가 사항이 게제 됨으로 확인 가능</li></strong>
		<br/>
		<li>신고접수 : 민원봉사과 부동산관리담당 <span class="btn_pack btn_down"><a href="/contents/18/re_list.hwp" title="새창" target="_blank">신고접수처 다운로드</a></span></li>
		<li>신고방법 : 도 및 시·군홈페이지 신고양식에 의거 인터넷 신고, 우편, Fax 또는 방문 접수 <br/>
		</li>
				<li>우리군 전화번호 : 061-550-5372, 팩스 : 061-550-5347<br /><br/>
				<span class="btn_pack btn_down"><a href="/contents/18/li_form.hwp" title="새창" target="_blank">불법거래 신고양식서 다운로드</a></span></li>

				<br/>
			<li class="alert_point"><strong>[비회원 글쓰기] 신고하기 클릭.</strong> -> "비회원 로그인" -> "휴대폰인증 또는 공공아이핀" 인증 	<br/> -> 민원등록</li>
			<li>처리 내용을 확인하기 위해서는 비회원 로그인을 한 후 해당 민원창구로 이동하여 
				"내가 신고한 글목록 보기"를 클릭하시면 신고하신 목록을 보실수 있습니다.</li>
			<li>회원은 "마이페이지"를 통하여 확인하실수 있습니다.</li>
			</ul>';

			## 상단문구 출력
			//if(!empty($data['list_msg'])) {
				if($data['device']=='mobile') echo '<div class="top_alert">'.stripslashes($data['list_msg']).'</div>';
				else { 
					echo '<div class="sub-tit-top"></div><div class="content_top_alert"><div class="alert_content">'.stripslashes($data['list_msg']).'<hr />'.$login_text.'</div>
					<div class="button">';
					if($_SYSTEM['myinfo']['is_login'] == true) {
						if($_SYSTEM['module_config']['board_id'] != 'www_inconvenience' && $_SYSTEM['module_config']['board_id'] != 'www_reaulatory_reform' && $_SYSTEM['module_config']['board_id'] != 'www_poor' && $_SYSTEM['module_config']['board_id'] != 'www_newspaper' && $_SYSTEM['module_config']['board_id'] != 'www_pollution' && $_SYSTEM['module_config']['board_id'] != 'www_cyber_disaster') {
						echo '<a href="?mode=write" class="btn_big"><span>신고하기</span></a>';
						}
						if($_SYSTEM['module_config']['board_id'] != 'www_inconvenience' && $_SYSTEM['module_config']['board_id'] != 'www_reaulatory_reform' && $_SYSTEM['module_config']['board_id'] != 'www_poor' && $_SYSTEM['module_config']['board_id'] != 'www_newspaper' && $_SYSTEM['module_config']['board_id'] != 'www_pollution' && $_SYSTEM['module_config']['board_id'] != 'www_cyber_disaster') {
						echo '<a href="?mode=list" class="btn_big"><span>'.($_SYSTEM['permission']['admin']==true?'신고글보기':'내가쓴글보기').'</span></a>';
						}
						if(!empty($data['old_link'])) echo '<a href="'.$data['old_link'].'" class="btn_big" target="_blank" title="새창"><span>이전게시판보기</span></a>';
					} else {
						if($_SYSTEM['module_config']['board_id'] != 'www_inconvenience' && $_SYSTEM['module_config']['board_id'] != 'www_reaulatory_reform' && $_SYSTEM['module_config']['board_id'] != 'www_poor' && $_SYSTEM['module_config']['board_id'] != 'www_newspaper' && $_SYSTEM['module_config']['board_id'] != 'www_pollution' && $_SYSTEM['module_config']['board_id'] != 'www_cyber_disaster') {
						echo '<a href="'.$_SYSTEM['rep_login'].'?mode=write" class="btn_big"><span>신고하기</span></a>';
						}
						if($_SYSTEM['module_config']['board_id'] != 'www_inconvenience' && $_SYSTEM['module_config']['board_id'] != 'www_reaulatory_reform' && $_SYSTEM['module_config']['board_id'] != 'www_poor' && $_SYSTEM['module_config']['board_id'] != 'www_newspaper' && $_SYSTEM['module_config']['board_id'] != 'www_pollution' && $_SYSTEM['module_config']['board_id'] != 'www_cyber_disaster') {
						echo '<a href="'.$_SYSTEM['rep_login'].'?mode=list" class="btn_big"><span>'.($_SYSTEM['permission']['admin']==true?'신고글보기':'내가쓴글보기').'</span></a>';
						}
						if(!empty($data['old_link'])) echo '<a href="'.$data['old_link'].'" class="btn_big" target="_blank" title="새창"><span>이전게시판보기</span></a>';
					}
					echo '<a href="/download/exemption/20140515_hw.hwp" class="btn_big"><span>신청서 다운로드</span></a>';
					echo '</div></div><div class="sub-tit-bottom"></div>';
				}
			//}		
		} else {

			if($_SYSTEM['module_config']['board_id'] == 'mayor_proposal_hope') {
				$login_text = '<div class="top_alert"><ul class="basic mal170">
				<li class="f_red"><strong>[비회원 글쓰기] 글쓰기 클릭</strong> -> "비회원 로그인" -> "휴대폰인증 또는 공공아이핀" 인증 -> 민원등록</li>
				<li>처리 내용을 확인하기 위해서는 비회원 로그인을 한 후 해당 민원창구로 이동하여 
					"<strong>내가 등록한 글목록 보기</strong>"를 클릭하시면 등록하신 목록을 보실수 있습니다.</li>
				<li>회원은 "<strong>마이페이지</strong>"를 통하여 확인하실수 있습니다.</li>
				</ul></div>';
			} elseif($_SYSTEM['module_config']['board_id'] != 'www_inconvenience' && $_SYSTEM['module_config']['board_id'] != 'www_reaulatory_reform' && $_SYSTEM['module_config']['board_id'] != 'www_poor' && $_SYSTEM['module_config']['board_id'] != 'www_newspaper' && $_SYSTEM['module_config']['board_id'] != 'www_pollution' && $_SYSTEM['module_config']['board_id'] != 'www_cyber_disaster') {
				$login_text = '<div class="top_alert"><ul class="basic mal170">
				<li class="f_red"><strong>[비회원 글쓰기] 신고하기 클릭</strong> -> "비회원 로그인" -> "휴대폰인증 또는 공공아이핀" 인증 -> 민원등록</li>
				<li>처리 내용을 확인하기 위해서는 비회원 로그인을 한 후 해당 민원창구로 이동하여 
					"<strong>내가 신고한 글목록 보기</strong>"를 클릭하시면 신고하신 목록을 보실수 있습니다.</li>
				<li>회원은 "<strong>마이페이지</strong>"를 통하여 확인하실수 있습니다.</li>
				</ul></div>';
			}
			## 상단문구 출력
			//if(!empty($data['list_msg'])) {			
				if($data['device']=='mobile')	{
	//				echo '<p>'.stripslashes($data['list_msg']).'</p>';
					echo '<div class="content_top_alert2 bor_box"><div class="c_box iconbox mat0 clear"><div class="alert_content info">'.stripslashes($data['list_msg']).''.$login_text.'</div></div>';
					echo '<div class="btn_p align_center bt_line regulation_btn">';
					if($_SYSTEM['module_config']['board_id'] == 'mayor_proposal_hope') {
						if($_SYSTEM['myinfo']['is_login'] == true) {
							echo '<a href="?mode=write" class="btst btn1 singo p2"><span class="btn1hover1"></span><span>글쓰기</span></a>';
							echo '<a href="?mode=list" class="btst btn1 mal10 p2 nae"><span class="btn1hover1"></span><span>'.($_SYSTEM['permission']['admin']==true?'내가쓴글보기':'내가쓴글보기').'</span></a>';
							if(!empty($data['old_link'])) echo '<a href="'.$data['old_link'].'" class="btst btn1" target="_blank" title="새창"><span class="btn1hover1"></span><span>이전게시판보기</span></a>';
						} else {
							echo '<a href="'.$_SYSTEM['rep_login'].$_SERVER['PHP_SELF'].'?mode=write" class="btst btn1 singo"><span class="btn1hover1"></span><span>글쓰기</span></a>';
							echo '<a href="'.$_SYSTEM['rep_login'].$_SERVER['PHP_SELF'].'?mode=list" class="btst btn1 mal10 nae"><span class="btn1hover1"></span><span>'.($_SYSTEM['permission']['admin']==true?'내가쓴글보기':'내가쓴글보기').'</span></a>';
							if(!empty($data['old_link'])) echo '<a href="'.$data['old_link'].'" class="btst btn1" target="_blank" title="새창"><span class="btn1hover1"></span><span>이전게시판보기</span></a>';
						}
					} else {
						if($_SYSTEM['myinfo']['is_login'] == true) {
							echo '<a href="?mode=write" class="btst btn1 singo p2"><span class="btn1hover1"></span><span>신고하기</span></a>';
							echo '<a href="?mode=list" class="btst btn1 mal10 p2 nae"><span class="btn1hover1"></span><span>'.($_SYSTEM['permission']['admin']==true?'신고글보기':'내가쓴글보기').'</span></a>';
							if(!empty($data['old_link'])) echo '<a href="'.$data['old_link'].'" class="btst btn1" target="_blank" title="새창"><span class="btn1hover1"></span><span>이전게시판보기</span></a>';
						} else {
							echo '<a href="'.$_SYSTEM['rep_login'].$_SERVER['PHP_SELF'].'?mode=write" class="btst btn1 singo p2"><span class="btn1hover1"></span><span>신고하기</span></a>';
							echo '<a href="'.$_SYSTEM['rep_login'].$_SERVER['PHP_SELF'].'?mode=list" class="btst btn1 mal10 p2 nae"><span class="btn1hover1"></span><span>'.($_SYSTEM['permission']['admin']==true?'신고글보기':'내가쓴글보기').'</span></a>';
							if(!empty($data['old_link'])) echo '<a href="'.$data['old_link'].'" class="btst btn1" target="_blank" title="새창"><span class="btn1hover1"></span><span>이전게시판보기</span></a>';
						}
					}
				//	echo '<a href="/download/exemption/20140515_hw.hwp" class="btst btn1 mal10"><span class="btn1hover1"></span><span>신청서 다운로드</span></a>';
					echo '</div>';
					echo '</div>';			
				}else { 
					echo '<div class="sub-tit-top"></div><div class="content_top_alert2 bor_box"><div class="alert_content">'.stripslashes($data['list_msg']).''.$login_text.'</div>
					<div class="button">';
					if($_SYSTEM['myinfo']['is_login'] == true) {
						echo '<a href="?mode=write" class="btst btn1 singo"><span class="btn1hover1"></span><span>신고하기</span></a>';
						echo '<a href="?mode=list" class="btst btn1 mal10 nae"><span class="btn1hover1"></span><span>'.($_SYSTEM['permission']['admin']==true?'신고글보기':'내가쓴글보기').'</span></a>';
						if(!empty($data['old_link'])) echo '<a href="'.$data['old_link'].'" class="btst btn1" target="_blank" title="새창"><span class="btn1hover1"></span><span>이전게시판보기</span></a>';
					} else {
						echo '<a href="'.$_SYSTEM['rep_login'].'?mode=write" class="btst btn1 singo"><span class="btn1hover1"></span><span>신고하기</span></a>';
						echo '<a href="'.$_SYSTEM['rep_login'].'?mode=list" class="btst btn1 mal10 nae"><span class="btn1hover1"></span><span>'.($_SYSTEM['permission']['admin']==true?'신고글보기':'내가쓴글보기').'</span></a>';
						if(!empty($data['old_link'])) echo '<a href="'.$data['old_link'].'" class="btst btn1" target="_blank" title="새창"><span class="btn1hover1"></span><span>이전게시판보기</span></a>';
					}
				//	echo '<a href="/download/exemption/20140515_hw.hwp" class="btst btn1 mal10"><span class="btn1hover1"></span><span>신청서 다운로드</span></a>';
					echo '</div></div><div class="sub-tit-bottom"></div>';
				}
			//}
		}

		
		
		
		
		## 버튼 영역.
		$img_url = '/images/common/board/temp';
		$url = $_SERVER['PHP_SELF'];
		$user_info = array();
		$user_info['is_login'] = empty($_SYSTEM['myinfo']['is_login']) ? NULL : $_SYSTEM['myinfo']['is_login'];
		$user_info['user_pin'] = empty($_SYSTEM['myinfo']['my_pin']) ? NULL : $_SYSTEM['myinfo']['my_pin'];
		$data['parameter']['mode'] = NULL;

		$arr_data['use_logoff_write'] = empty($data['use_logoff_write']) ? NULL : $data['use_logoff_write'];

		## --------------------- 버튼 : start
		$print_button =  print_button_new('list', $data['permission'], $data['parameter'], $url, $img_url, $user_info, $arr_data);
		if( !empty($print_button ) ){
		//echo '<!-- 버튼 START --><div class="board_btn_box align_center">'.$print_button.'</div><!-- 버튼 END -->';	
		}
		## --------------------- 버튼 : end		
		
		## -----------  인기글 : start
		if( $data['user_list_hot'] == 'true' ){
			echo print_hot_new($data['hot_articles_data']); 	
		}		
		## -----------  인기글 : end
		
		## 카테고리 표시 	
		if($data['use_category_1'] == 'true') {
			$category_1_list    = unserialize($data['category_1_list']);
			$category_1_all     = unserialize($data['category_1_all']);		
			echo category_box_mobile($category_1_list, $category_1_all, $data['navi_parameter'],$data['category_1']);		
		}	
		
		$mode = $_GET['mode'];	
		if( !empty($data['search_type']) || !empty($date['search_word']) ){
			$mode = "list";
		}
		
		if($mode =='list' ) {		
		?>
		<div class="board_list_box approval_list_wrap">
			<?php 
			## -----------  검색 영역 : start
			echo search_box_new($data['total_count'], $data['search_type'], $data['search_word'], $data['search_list'], $data['page_scale_search'], $data['page_scale'], $data['start_date'], $data['finish_date'], $data['search_parameter']); 
			## -----------  검색 영역 : end
			
			echo '<p class="hidden">'.$_SYSTEM['menu_info']['title'].' 게시물. 총 '.$data['total_count'].'건, '.$data['total_page'].'페이지 중 '.$data['page'].'페이지 '.$data['count'].'건 입니다.</p>';
			## 목록 출력 =========================================================================
			## board_list 클래스를 꼭 포함하고 있어야함.
			echo   '<div class="board_list">';				
			echo $print_table; 
			echo   '</div>';						
			## 모바일 페이징을 위해서 꼭 필요함 지우지말것.
			if( $sub_mode == 'paging' ){
				ob_clean();		ob_start();
				echo $list_data_paging;
				exit;
			}
			## 목록 출력 =========================================================================
			
			## ----------- 페이징 : start
			## 페이지 네비게이션 영역			
			$navi_parameter = make_GET_parameter($data['navi_parameter']);
			$page_navi      = page_navigation_new($data['total_count'], $data['page_scale'], $data['block_scale'], $data['page'], $navi_parameter);
			echo '<!-- page navigation START --><div class="board_btn_box align_center">'.$page_navi.'</div><!-- page navigation END -->';
			## ----------- 페이징 : end			

			?>																											
		</div>
		<?php
		} //- 리스트 출력 
		
		?>
	</div>
</div>


<script src="/js/jquery/datetimepicker/jquery.datetimepicker.js"></script>
<link rel="stylesheet" type="text/css" href="/js/jquery/datetimepicker/jquery.datetimepicker.css">
<script src="<?php echo $_SERVER['SELF']; ?>js/list_mobile.js?build=<?php echo SERIAL;?>" defer ></script>