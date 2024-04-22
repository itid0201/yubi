<?php	
		

	/* list skin */
	$lock_img  = '<span class="icon_lock">Private Posts</span>';
	$reply_img = '<span class="icon_reply">Answer</span>';
	$new_img   = '<span class="icon_new">New Posts</span>';
	echo $data['debug']; // only test..

	$colspan = 5;
	
	## 카테고리 사용시 필드 추가
	if($data['use_category_1'] == 'true') {		$colspan++;	}

	## 프로세서 사용시 필드 추가	
	if($data['use_process_1'] == 'true') {		$colspan++;	}
	
	## 공개비공개 사용시 필드 추가	
	if($data['use_allow'] == 'true' && $_SYSTEM['permission']['admin'] == 'true') {
		$colspan++;
	}

	## 목록 파일 다운로드
	if($data['use_list_attach'] == 'true') {		$colspan++;	}
	
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
		$print_img = !empty($print_img)?'<div class="icon_box">'.$print_img.'</div>':'';
		$link_class .= $print_img_cnt>0?' icon_0'.$print_img_cnt:'';
		
		
		## 제목 a 태그 클래스 선처리 맨 마지막에 와야함. 
		$link_class = empty($link_class) ? '' : 'class="'.$link_class.'"';						
		##---- class 선처리 -- end		
		
		$print_table_tr  .=  '    						<tr'.($buffer['is_top']=='true' ? ' class="notice"' : '').'>';
		$print_table_tr  .=  '      							<td'.($buffer['is_top']=='true' ? ' class="notice_icon"' : '').'>';
		if($_SYSTEM['permission']['admin'] == true && $data['multi_delete'] == 'true') 
		$print_table_tr  .=  '<div class="list_checkbox"><input type="checkbox" name="check_idx[]" id="check_idx_'.$buffer['idx'].'" value="'.$buffer['idx'].'" /><label for="check_idx_'.$buffer['idx'].'">Select Posts</label></div>';
		
		$print_table_tr  .= ($buffer['is_top']=='true' ? '<span class="notice_icon">Notice</span>' : $buffer['list_num']);
		$print_table_tr  .= '</td>';
		
		if($data['use_category_1'] == 'true') $print_table_tr  .=  '<td class="list_cate">'.$buffer['category_1'].'</td>';				
		$print_table_tr  .=  '								<td class="align_left">';		
		$print_table_tr  .=  '									<a href="'.$href_view.'" '.$link_class.' title="View to post on '.$buffer['title'].'." >'.$buffer['title'];
		$print_table_tr  .= $print_img;
		$print_table_tr  .=  '									</a>';
		$print_table_tr  .=  '								</td>';
		$print_table_tr  .=  '								<td>'.$writer.'</td>';
		if($data['use_process_1'] == 'true') {
			$print_table_tr  .=  '<td>';
			if($_SYSTEM['menu_info']['_board_id'] == 'www_smart_manage'){			
			
				if($buffer['process_1'] == '신청') {
					$print_table_tr  .=  '<div class="proc_wrap"><span class="request">'.$buffer['process_1'].'</span></div>';
				} elseif($buffer['process_1'] == '해지') {
					$print_table_tr  .=  '<div class="proc_wrap"><span class="receipt">'.$buffer['process_1'].'</span></div>';
				}
			}else{
				if($buffer['process_1'] == '신청') {
					$print_table_tr  .=  '<div class="proc_wrap"><span class="request">'.$buffer['process_1'].'</span></div>';
				} elseif($buffer['process_1'] == '접수') {
					$print_table_tr  .=  '<div class="proc_wrap"><span class="receipt">'.$buffer['process_1'].'</span></div>';
				} elseif($buffer['process_1'] == '보류') {
					$print_table_tr  .=  '<div class="proc_wrap"><span class="defer">'.$buffer['process_1'].'</span></div>';
				} elseif($buffer['process_1'] == '완료') {
					$print_table_tr  .=  '<div class="proc_wrap"><span class="complete">'.$buffer['process_1'].'</span></div>';
				}
			}
			$print_table_tr  .=  '</td>';
			
		}		
		if($data['use_allow'] == 'true' && $_SYSTEM['permission']['admin'] == 'true')
			$print_table_tr  .=  '<td>'.($buffer['allow']=='y' ? '승인' : '비승인').'</td>';
		
		$print_table_tr  .=  '								<td>'.$buffer['reg_date'].'</td>';		
		## mode setting에서 목록 파일 다운로드 기능 true일때만 나타난다. --
		if($data['use_list_attach'] == 'true') {
			$print_table_tr  .=  '							<td>';		
			//if($buffer['file_exist'] == 'true' || $buffer['photo_exist'] == 'true' || $buffer['movie_exist'] == 'true') {
			$print_table_tr  .= download_box_new("list", $buffer['idx'], $buffer['file_list_file']);			
			//}
			$print_table_tr  .=  '							</td>';			
		}
		## ---------------------------------------------------------------
		$print_table_tr  .=  '								<td>'.$buffer['visit_cnt'].'</td>';
		$print_table_tr  .=  '							</tr>';
		$original_cnt++;
		if($buffer['is_top']=='true') $original_cnt--;
	}	

	
	if( empty($print_table_tr) || $original_cnt < 1 ){
		
		$print_table_tr .= '<tr><td class="data_none" colspan="'.$colspan.'"><div class="no_result"><span class="icon"></span>No search result.</div></td></tr>';
	}	


	## ================================================================================================	
	## table 출력 
	## ================================================================================================	

	$print_table  = '';	
	$print_table .=  '						<table class="board_basic">';
	$print_table .=  '  							<caption>'.$_SYSTEM['menu_info']['title'].'. Total '.$data['total_count'].', Out of '.$data['total_page'].' pages, page '.$data['page'].' has '.$data['count'].' results. This data table consists of '.$colspan.' columns and '.$data['count'].' rows. Each row consists of a number, '.($data['use_category_1'] == 'true'?'category,':'').' a title, '.($data['use_process_1'] == 'true'?'처리상태, ':'').' '.($data['use_allow'] == 'true' && $_SYSTEM['permission']['admin'] == 'true'?'an approval state, ':'').' an attachment, '.$writer_name.', a registration date, and number of views.</caption>';
	$print_table .=  '							<thead>';
	$print_table .=  '								<tr>';
	$print_table .=  '									<th scope="col" class="th_100px" >No</th>';
	//if($_SYSTEM['permission']['admin'] == true && $data['multi_delete'] == 'true') 			$print_table .=  '	<th scope="col"><input type="checkbox" name="check_all" id="check_all" /></th>';
	if($data['use_category_1'] == 'true') 			$print_table .=  '	<th scope="col" class="th_100px">Categories</th>';
	$print_table .=  '									<th scope="col">Title</th>';
	$print_table .=  '									<th scope="col" class="th_100px">'.$writer_name.'</th>';	
	if($data['use_process_1'] == 'true') 			$print_table .=  '	<th scope="col" class="th_100px">Processing</th>';
	if($data['use_allow'] == 'true' && $_SYSTEM['permission']['admin'] == 'true') 			$print_table .=  '	<th scope="col">Status</th>';
	$print_table .=  '									<th scope="col" class="th_120px">Posted at</th>';	
	if($data['use_list_attach'] == 'true') $print_table .=  '			<th scope="col" class="th_100px">Attachment</th>';
	$print_table .=  '									<th scope="col" class="th_70px">Hits</th>';	
	$print_table .=  '								</tr>';
	$print_table .=  '							</thead>';
	$print_table .=  '							<tbody>';
	$print_table .= $print_table_tr;
	$print_table .= '							</tbody>';
	$print_table .= '						</table>';	
	## ================================================================================================	
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

		## -----------  카테고리.: start
		if($data['use_category_1'] == 'true' && $mode != "all" ) {
			$category_1_list    = unserialize($data['category_1_list']);
			$category_1_all     = unserialize($data['category_1_all']);
			$arr_temp           = $data['navi_parameter'];
			$arr_temp['category_1'] = NULL;
		//	$category_parameter = make_GET_parameter($arr_temp, '&amp;', true);					
			echo print_category($data['category_1'], $category_1_list , $category_1_all, $arr_temp, $_SERVER['PHP_SELF'] );			
		}
		## -----------  카테고리 : end
		
		
		## -----------  인기글 : start
		if( $data['user_list_hot'] == 'true' ){
			echo print_hot_new($data['hot_articles_data']); 	
		}		
		## -----------  인기글 : end
		?>
		<div class="board_list_box">
			<?php 
			## -----------  검색 영역 : start
			//		echo search_box_new($data['total_count'], $data['search_type'], $data['search_word'], $data['search_list'], $data['page_scale_search'], $data['page_scale'], $data['start_date'], $data['finish_date'], $data['search_parameter']); 
			
			
			//echo search_box_tour_eng($data['search_type'], $data['search_word'], $data['search_list'], $data['search_parameter']); 
			
			/*20210507 검색박스 수정*/
			echo search_box_new_foreign("ch", $data['total_count'], $data['search_type'], $data['search_word'], $data['search_list'], $data['page_scale_search'], $data['page_scale'], $data['start_date'], $data['finish_date'], $data['search_parameter'],$data); 
			## -----------  검색 영역 : end
			
			echo   '		<div class="board_list">';	
			echo '<div class="board_photo">'.tag_box($data['keyword']).'</div>';
			## 폼 써브밋을  ajax으로 변경하고 싶다~~~~ ㅡㅜ 
			if(  $_SYSTEM['permission']['admin'] == true &&  $data['multi_delete'] == 'true') {
				echo  '		<form action="" method="post" name="form_del" id="form_del">';
				echo  '			<input type="hidden" name="mode" value="delete_all" />';			
				echo  '			<div class="board_manager_btn">
									<a href="#none" class="all_check"><span class="icon"></span>Select All</a>
									<a href="#none" class="check_delete"><span class="icon"></span>Delete Selection</a>
								</div>';
			}
			
			## ----------- 목록 : start
			echo $print_table; 
			## ----------- 목록 : end
			
			## ----------- 페이징 : start
			## 페이지 네비게이션 영역			
		//	$navi_parameter = make_GET_parameter($data['navi_parameter'], '&amp;', true);
			$page_navi      = page_navigation_new($data['total_count'], $data['page_scale'], $data['block_scale'], $data['page'], $data['navi_parameter'],'list','en');
			echo '<!-- page navigation START -->'.$page_navi.'<!-- page navigation END -->';
			## ----------- 페이징 : end
			
			if(  $_SYSTEM['permission']['admin'] == true &&  $data['multi_delete'] == 'true') {
			echo   '			</form>';
			}	
			echo   '		</div>';			
			?>																											
		</div>
	</div>
	
	<?php
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
	?>	
</div>


<script src="/js/jquery/datetimepicker/jquery.datetimepicker.js"></script>
<link rel="stylesheet" type="text/css" href="/js/jquery/datetimepicker/jquery.datetimepicker.css">
<script src="<?php echo $_SERVER['SELF']; ?>js/list.js" defer ></script>
<?php 	
## 관리자 삭제일떄만 출력되는 javascript----- start
## /modules/보드이름/js/ 로 이동.( 모듈 복사시 모든 스크립를 같이 옮기기 위해.)
if( $_SYSTEM['permission']['admin'] == true && $data['multi_delete'] == 'true') { 
echo '<script src="'.$_SERVER['SELF'].'js/list_admin.js"  ></script>';
} 
## 관리자 삭제일떄만 출력되는 javascript----- end
?>						
