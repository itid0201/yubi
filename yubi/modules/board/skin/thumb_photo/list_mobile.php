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
	echo $data['debug']; // only test..

	## 등록자 표시 부분 열라 급조한것이다. 오픈이 없어서.. 나중에 다른 방식으로 수정해야한다.
	if($data['writer_display'] == 'department') $writer_name = '등록부서';
	else if($data['writer_display'] == 'other_organ') $writer_name = '기관/부서명';
	else if($data['writer_display'] == 'processing') $writer_name = '처리부서';
	else $writer_name = '등록자';	


	  ## 공익에서만 사용하는 process_1 값 ( 정말 알수가 없음.ㅡㅜ )-------------
	  $PROC_1 = array();
	  $PROC_1['예정'] = 'wait';
	  $PROC_1['진행중'] = 'ing';
	  $PROC_1['마감'] = 'end';
	  ## 공익에서만 사용하는 ( 정말 알수가 없음.ㅡㅜ )-------------
	
	## ================================================================================================	
	## 목록 출력..
	## ================================================================================================		
	$buffer = array();
	$data['parameter']['mode'] = 'view';
	$view_parameter = make_GET_parameter($data['parameter'], '&amp;', true);
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
		$print_img = $print_h3_class = '';
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
			$print_h3_class = 'new_list';
			$print_img_cnt++;
		}
		//if($buffer['comment_cnt'] > 0) print_img .= '<span class="comment_cnt">'.$buffer['comment_cnt'].'</span>'; //댓글개수 수정 : 오경우(20120125)				
		if($buffer['reply_cnt'] > 0) {		## 답글 처리 	
			$print_img .=  $reply_img ;
			$print_img_cnt++;
		}
		

		$link_class .= $print_img_cnt>0?' icon_0'.$print_img_cnt:'';
		## 제목 a 태그 클래스 선처리 맨 마지막에 와야함. 
		$link_class = empty($link_class) ? '' : 'class="'.$link_class.'"';						
		$print_h3_class = empty($print_h3_class) ? '' : 'class="'.$print_h3_class.'"';						
		##---- class 선처리 -- end		
		
		## ================================================================================================	
		##  출력 
		## ================================================================================================			

		/*
		## 이미지 불러오기
		$img_list = array();
		foreach($buffer['photo_list'] as $item) {		
			$img_list[] = array('file_name'=>$item['photo_name'],'alt'=>$item['photo_alt'],'idx'=>$buffer['idx']);
		}
		
		$img_count = ( count($buffer['photo_list']) > 5 )? 6: count($buffer['photo_list']);
		
		if( $img_count > 0 ){
			$width = $height = $margin = '';
			$img = $module->get_munti_thumb_mobile_new($width,$height,$margin,$img_list,$buffer['photo_path'],$href_view,$img_count);	
		}else{
			$img = "noimg";
		}
			*/	
		
		if($buffer['photo_exist'] == 'true') {
			$img =  '<div class="thumb_box"><img src="'.$buffer['photo_path'].'324x198/'.$buffer['photo_name'].'" alt="'.htmlspecialchars($buffer['photo_alt']).'" /></div>';
			$link_class .= " hasthumb";
		} else {
			$img = '<div class="img_box"><img src="/images/board/noimage_book_list.jpg" alt="이미지가 없습니다" width="324" height="198" /></div>';
		}	
		
		$print_table_tr  =  '    			<div class="item">';
		if(($_SERVER['PHP_SELF'] == '/www/community/portfolio/portfolio_web') && ($_SYSTEM['permission']['admin'] == true)){
			$print_table_tr .= '    			<a href="' . $href_view . '" class="item_click item_cont">상세보기</a></p>';
		}
//		$print_table_tr  .=  '    					<div class="hover_box"><div class="lt"></div><div class="lr"></div><div class="lb"></div><div class="ll"></div></div>';
		$print_table_tr  .=  '    					<div class="thumb_box item0'.$img_count.'">';
		if($_SERVER['PHP_SELF'] == '/www/community/portfolio/portfolio_web'){
			$print_table_tr  .=  '    						<a href="' . $buffer['varchar_2'] . '" data-link="'.$href_view.'" title="'.$buffer['title'].'" class="item_click item_cont" target="_blank">';
		}else{
			$print_table_tr  .=  '    						<a href="#none" data-idx="'.$buffer['idx'].'" data-link="'.$href_view.'" title="'.$buffer['title'].'" class="item_click item_cont">';
		}
		$print_table_tr  .= $img;
//if( $_SERVER['REMOTE_ADDR'] == "49.254.140.140" || $_SERVER['REMOTE_ADDR'] == "168.131.244.65" ){
	if( $module->module_config['board_id'] == 'gongik_education_ing' && $module->module_config['use_process_1'] == "true" && !empty($buffer['process_1']) ){
		$print_table_tr  .=  '    						<span class="'.$PROC_1[$buffer['process_1']].'">'.$buffer['process_1'].'</span>';		
	}
//}				
		$print_table_tr  .=  '    						</a>';
		$print_table_tr  .=  '    						<div class="share_wrap">';
		$print_table_tr  .=  '  							<a href="#none" class="board_btn_share share_btn"><span class="icon"></span>공유</a>';
		$print_table_tr  .=  '    							<div class="board_share_box" style="display: none;">';
		$print_table_tr  .=  '    								<ul class="share_btn">';
		$print_table_tr  .=  '    									<li class="facebook share_btn"><a href="#none" class="share_btn" data-type="facebook" data-url="'.$href_view.'" data-title="'.$buffer['title'].'"><span class="icon share_btn">페이스북</span></a></li>';
		$print_table_tr  .=  '    									<li class="twitter share_btn"><a href="#none" class="share_btn" data-type="twitter" data-url="'.$href_view.'" data-title="'.$buffer['title'].'"><span class="icon  share_btn">트위터</span></a></li>';
		// $print_table_tr  .=  '    									<li class="kakaostory share_btn"><a href="#none" class="share_btn" data-type="kakaostory" data-url="'.$href_view.'" data-title="'.$buffer['title'].'"><span class="icon  share_btn">카카오스토리</span></a></li>';
		$print_table_tr  .=  '    									<li class="band share_btn"><a href="#none" class="share_btn" data-type="band" data-url="'.$href_view.'" data-title="'.$buffer['title'].'"><span class="icon  share_btn">네이버밴드</span></a></li>';
		$print_table_tr  .=  '    								</ul>';
		$print_table_tr  .=  '    							</div>';
		$print_table_tr  .=  '    						</div>';
		$print_table_tr  .=  '    					</div>';
		$print_table_tr  .=  '    					<div class="cont_box">';
		$print_table_tr  .=  '    						<div class="top_util">';
		$print_table_tr  .=  '    							<ul>';
		$print_table_tr  .=  '    								<li class="view"><span class="icon">조회수</span>'.$buffer['visit_cnt'].'</li>';
		$print_table_tr  .=  '    								<li class="reply"><span class="icon">댓글수</span>'.$buffer['comment_cnt'].'</li>';
		/*$print_table_tr  .=  '    								<li class="share_wrap">';
		$print_table_tr  .=  '    									<a href="#none" class="board_btn_share share_btn"><span class="icon"></span>공유</a>';
		$print_table_tr  .=  '    									<div class="board_share_box">';
		$print_table_tr  .=  '    										<ul class="share_btn">';
		$print_table_tr  .=  '    											<li class="facebook share_btn"><a href="#none" class="share_btn"><span class="icon share_btn">페이스북</span></a></li>';
		$print_table_tr  .=  '    											<li class="twitter share_btn"><a href="#none" class="share_btn"><span class="icon  share_btn">트위터</span></a></li>';
		$print_table_tr  .=  '    											<li class="kakaostory share_btn"><a href="#none" class="share_btn"><span class="icon  share_btn">카카오스토리</span></a></li>';
		$print_table_tr  .=  '    											<li class="band share_btn"><a href="#none" class="share_btn"><span class="icon  share_btn">네이버밴드</span></a></li>';
		$print_table_tr  .=  '    										</ul>';
		$print_table_tr  .=  '    									</div>';
		$print_table_tr  .=  '    								</li>';*/
		$print_table_tr  .=  '    							</ul>';
		$print_table_tr  .=  '    						</div>';		
		$print_table_tr  .=  '    						<h3 '.$print_h3_class.'>'.$print_img.$buffer['title'].'</h3>';
		$print_table_tr  .=  '							<dl>';
		$print_table_tr  .=  '								<dt class="text_hidden">작성날짜</dt><dd>'.$buffer['reg_date'].'</dd>';
		$print_table_tr  .=  '								<dt class="text_hidden">작성자</dt><dd>'.$writer.'</dd>';
		$print_table_tr  .=  '							</dl>';
		$print_table_tr  .=  '						</div>';
		$print_table_tr  .=  '    				</div>';		
		
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
		if(!empty($data['list_msg'])) {
			if($data['device']=='mobile') echo '<p>'.stripslashes($data['list_msg']).'</p>';
			elseif($_SYSTEM['module_config']['board_id'] == 'leader_prop_status') echo stripslashes($data['list_msg']); ## 군수실 공약추진현황 게시판 상단문구 전용
			else echo '<div class="sub-tit-top"></div><div class="content_top_alert"><div class="alert_content">'.stripslashes($data['list_msg']).'</div></div><div class="sub-tit-bottom"></div>';
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
		echo '<!-- 버튼 START --><div class="board_btn_box align_center">'.$print_button.'</div><!-- 버튼 END -->';	
		}
		## --------------------- 버튼 : end		
		
		## -----------  인기글 : start
		if( $data['user_list_hot'] == 'true' ){
			echo print_hot_new($data['hot_articles_data']); 	
		}		
		## -----------  인기글 : end
		
		## 카테고리 표시 	
		//if($data['use_category_1'] == 'true') {
		if($data['use_category_1'] == 'true' && $mode != "all" && $_SYSTEM['module_config']['use_category_tab'] == "true" ) {
			$category_1_list    = unserialize($data['category_1_list']);
			$category_1_all     = unserialize($data['category_1_all']);		
			echo category_box_mobile($category_1_list, $category_1_all, $data['navi_parameter'],$data['category_1']);		
		}	
		?>
		<div class="board_list_box">
			<?php 
			## -----------  검색 영역 : start
			echo search_box_new($data['total_count'], $data['search_type'], $data['search_word'], $data['search_list'], $data['page_scale_search'], $data['page_scale'], $data['start_date'], $data['finish_date'], $data['search_parameter']); 
			## -----------  검색 영역 : end

			//echo '<p class="hidden">'.$_SYSTEM['menu_info']['title'].' 게시물. 총 '.$data['total_count'].'건, '.$data['total_page'].'페이지 중 '.$data['page'].'페이지 '.$data['count'].'건 입니다.</p>';
			## 목록 출력 =========================================================================
			## board_list 클래스를 꼭 포함하고 있어야함.
			echo 		'<div class="board_photo board_list">';
			echo $print_table; 
			echo   		'</div>';
			
			## 모바일 페이징을 위해서 꼭 필요함 지우지말것.
			if( $sub_mode == 'paging' ){
				ob_clean();		ob_start();
				echo $list_data_paging;
				exit;
			}
			## 목록 출력 =========================================================================
			
			## ----------- 페이징 : start
			## 페이지 네비게이션 영역			
		//	$navi_parameter = make_GET_parameter($data['navi_parameter']);

			$page_navi      = page_navigation_new($data['total_count'], $data['page_scale'], $data['block_scale'], $data['page'], $data['navi_parameter']);
			echo '<!-- page navigation START --><div class="board_btn_box align_center">'.$page_navi.'</div><!-- page navigation END -->';
			## ----------- 페이징 : end			

			?>																											
		</div>
	</div>
</div>


<script src="/js/jquery/datetimepicker/jquery.datetimepicker.js"></script>
<link rel="stylesheet" type="text/css" href="/js/jquery/datetimepicker/jquery.datetimepicker.css">
<script src="<?php echo $_SERVER['SELF']; ?>js/list_mobile.js?build=<?php echo SERIAL;?>" defer ></script>
