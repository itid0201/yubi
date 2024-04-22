<?php	
	## 목록 리스트 ==========================================================
	/***********
	** list_data : 실제 데이타 출력되는 목록
	** list_data_paging : 모바일에서만 사용 페이지별 목록	
	** list_buffer : 데이타별 임시 저장
	***********/	
	/* list skin */
	$lock_img  = '<span class="icon_lock">Private Posts</span>';
	$reply_img = '<span class="icon_reply">Answer</span>';
	$new_img   = '<span class="icon_new">New Posts</span>';
	$top_img   = '<span class="icon_notice">Notice</span>';

	echo $data['debug']; // only test..
	
	## 등록자 표시 부분 열라 급조한것이다. 오픈이 없어서.. 나중에 다른 방식으로 수정해야한다.
	if($data['writer_display'] == 'department') $writer_name = 'Registration Dept.';
	else if($data['writer_display'] == 'other_organ') $writer_name = 'Organization';
	else if($data['writer_display'] == 'processing') $writer_name = 'Processing Dept.';
	else $writer_name = 'Registrar';	
	
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
									<a href="#none" data-idx="'.$buffer['idx'].'" data-link="'.$href_view.'"  title="View to post on '.$buffer['title'].'."  '.$link_class.'>
										<strong>'.($buffer['is_top']=='true' ? $top_img:'').$buffer['title'].$print_img.'</strong>
										<span class="date">'.$buffer['reg_date'].'</span>
									</a>
							</div>';
		$original_cnt++;
		if($buffer['is_top']=='true') $original_cnt--;
		else $list_data_paging .= $print_table_tr;
		
		$print_table .= $print_table_tr;
		## ================================================================================================			
	}	

	
	if( empty($print_table) || $original_cnt < 1 ){		
		$print_table .= '<div class="no_result"><span class="icon"></span>No search result.</div>';
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
		$print_button =  print_button_new_foreign('ch','list', $data['permission'], $data['parameter'], $url, $img_url, $user_info, $arr_data);
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
		if($data['use_category_1'] == 'true') {
			$category_1_list    = unserialize($data['category_1_list']);
			$category_1_all     = unserialize($data['category_1_all']);		
			echo category_box_mobile($category_1_list, $category_1_all, $data['navi_parameter'],$data['category_1']);		
		}	
		?>
		<div class="board_list_box">
			<?php 
			## -----------  검색 영역 : start
			//echo search_box_new($data['total_count'], $data['search_type'], $data['search_word'], $data['search_list'], $data['page_scale_search'], $data['page_scale'], $data['start_date'], $data['finish_date'], $data['search_parameter']);
			
			/*20210507 검색박스 수정*/
			echo search_box_new_foreign("ch", $data['total_count'], $data['search_type'], $data['search_word'], $data['search_list'], $data['page_scale_search'], $data['page_scale'], $data['start_date'], $data['finish_date'], $data['search_parameter'],$data); 

			## -----------  검색 영역 : end
			
			echo '<p class="hidden">'.$_SYSTEM['menu_info']['title'].'. Total '.$data['total_count'].',  Out of '.$data['total_page'].' pages, page '.$data['page'].' has  '.$data['count'].' results.</p>';
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
		//	$navi_parameter = make_GET_parameter($data['navi_parameter'], '&amp;', true);
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