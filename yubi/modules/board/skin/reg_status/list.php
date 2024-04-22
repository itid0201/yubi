<?php	
		

	/* list skin */
	$lock_img  = '<span class="icon_lock">비공개글</span>';
	$reply_img = '<span class="icon_reply">답변글</span>';
	$new_img   = '<span class="icon_new">새로운글</span>';
	echo $data['debug']; // only test..

	$colspan = 7;

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
	if($data['writer_display'] == 'department') $writer_name = '등록부서';
	else if($data['writer_display'] == 'other_organ') $writer_name = '타기관';
	else if($data['writer_display'] == 'processing') $writer_name = '처리부서';
	else if($data['writer_display'] == 'charge_organ') $writer_name = '담당부서';
	else $writer_name = '등록자';	
	

	
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
		$print_table_tr  .=  '<div class="list_checkbox"><input type="checkbox" name="check_idx[]" id="check_idx_'.$buffer['idx'].'" value="'.$buffer['idx'].'" /><label for="check_idx_'.$buffer['idx'].'">게시글 선택</label></div>';
		
		$print_table_tr  .= ($buffer['is_top']=='true' ? '<span class="notice_icon">공지글</span>' : $buffer['list_num']);
		$print_table_tr  .= '</td>';
		
		$print_table_tr  .=  '								<td class="list_cate">'.$buffer['title'].'</td>';
		$print_table_tr  .=  '								<td class="reg_date">'.date('Y-m-d',strtotime($buffer['reg_date'])).'</td>';
		$print_table_tr  .=  '								<td class="align_left">';		
		$print_table_tr  .=  '									<a href="'.$href_view.'" '.$link_class.' title="'.$buffer['contents'].' 에 대한 글내용 보기." >'.$buffer['contents'];
		$print_table_tr  .= $print_img;
		$print_table_tr  .=  '									</a>';
		$print_table_tr  .=  '								</td>';
		$print_table_tr  .=  '								<td class="varchar_12">'.$buffer['varchar_12'].'</td>';
		$print_table_tr  .=  '								<td class="varchar_7">'.$buffer['varchar_7'].'</td>';
		$print_table_tr  .=  '								<td class="varchar_8">'.$buffer['varchar_8'].'</td>';
		$print_table_tr  .=  '							</tr>';
		$original_cnt++;
		if($buffer['is_top']=='true') $original_cnt--;
	}	

	
	if( empty($print_table_tr) || $original_cnt < 1 ){
		
		$print_table_tr .= '<tr><td class="data_none" colspan="'.$colspan.'"><div class="no_result"><span class="icon"></span>검색내역이 없습니다.</div></td></tr>';
	}	


	## ================================================================================================	
	## table 출력 
	## ================================================================================================	

	$print_table  = '';	
	$print_table .=  '						<table class="board_basic">';
	$print_table .=  '  							<caption>'.$_SYSTEM['menu_info']['title'].' 게시물. 총 '.$data['total_count'].'건, '.$data['total_page'].'페이지 중 '.$data['page'].'페이지 '.$data['count'].'건 입니다. 본 데이터표는 '.$colspan.'컬럼, '.$data['count'].'로우로 구성되어 있습니다. 각 로우는 번호, 관리번호, 등록일자, 규제사무명, 규제시행/폐지일, 유형, 등록사유로 구성되어 있습니다.</caption>';
	$print_table .=  '							<thead>';
	$print_table .=  '								<tr>';
	$print_table .=  '									<th scope="col" class="th_50px" >번호</th>';
	$print_table .=  '									<th scope="col" class="th_70px" >관리번호</th>';
	$print_table .=  '									<th scope="col" class="th_80px" >등록일자</th>';
	$print_table .=  '									<th scope="col" class="th_100px" >규제사무명</th>';
	$print_table .=  '									<th scope="col" class="th_80px" >규제시행/폐지일</th>';
	$print_table .=  '									<th scope="col" class="th_90px" >유형</th>';
	$print_table .=  '									<th scope="col" class="th_60px" >등록사유</th>';

	/*$print_table .=  '									<th scope="col">제목</th>';
	$print_table .=  '									<th scope="col" class="th_120px">'.$writer_name.'</th>';	
	$print_table .=  '									<th scope="col" class="th_120px">'.($_SYSTEM['module_config']['board_id'] == 'njursc_newsrelease'?'보도일':'등록일').'</th>';	
	$print_table .=  '									<th scope="col" class="th_70px">조회수</th>';*/	

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
			else echo '<div class="content_top_alert"><div class="alert_content">'.stripslashes($data['list_msg']).'</div></div>';
		}
		
		## 상단문구 출력(스타일 없음))
		if(!empty($data['list_msg_no_css'])) {
			echo '<div class="alert_content_none">'.stripslashes($data['list_msg_no_css']).'</div>';
		}			

		## -----------  카테고리.: start
		if( $data['use_category_1'] == 'true' && $mode != "all" && $_SYSTEM['module_config']['use_category_tab'] == "true" ) {
			$category_1_list    = unserialize($data['category_1_list']);
			$category_1_all     = unserialize($data['category_1_all']);
			$arr_temp           = $data['navi_parameter'];
			$arr_temp['category_1'] = NULL;
		//	$category_parameter = make_GET_parameter($arr_temp);
			// 카테고리에 '전체'가 보이지 않도록 하려면 allType 값 수정
			$allType = true;
			echo print_category($data['category_1'], $category_1_list , $category_1_all, $arr_temp, $_SERVER['PHP_SELF'], $allType);			
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
if( $_SERVER['REMOTE_ADDR'] == "49.254.140.140" && $_SESSION['user_id'] == "jini0808" ){
//			print_r( $data['search_date'] );
}		

			## -----------  검색 영역 : start
			$type = array(
				array( "type"=>"규제사무명", "value"=>"contents"),
				array( "type"=>"관리번호", "value"=>"title")
				);
			echo search_box_new($data['total_count'], $data['search_type'], $data['search_word'], $type, $data['page_scale_search'], $data['page_scale'], $data['start_date'], $data['finish_date'], $data['search_parameter'], $data, false); 
			## -----------  검색 영역 : end
			
			echo   '		<div class="board_list">';	
			echo '<div class="board_photo">'.tag_box($data['keyword']).'</div>';
			## 폼 써브밋을  ajax으로 변경하고 싶다~~~~ ㅡㅜ 
			if(  $_SYSTEM['permission']['admin'] == true &&  $data['multi_delete'] == 'true') {
				echo  '		<form action="" method="post" name="form_del" id="form_del">';
				echo  '			<input type="hidden" name="mode" value="delete_all" />';			
				echo  '			<div class="board_manager_btn">
									<a href="#none" class="all_check"><span class="icon"></span>전체선택</a>
									<a href="#none" class="check_delete"><span class="icon"></span>선택삭제</a>
								</div>';
			}
			
			## ----------- 목록 : start
			echo $print_table; 
			## ----------- 목록 : end
			
			## ----------- 페이징 : start
			## 페이지 네비게이션 영역			
		//	$navi_parameter = make_GET_parameter($data['navi_parameter']);
			$page_navi      = page_navigation_new($data['total_count'], $data['page_scale'], $data['block_scale'], $data['page'], $data['navi_parameter']);
			echo '<!-- page navigation START -->'.$page_navi.'<!-- page navigation END -->';
			## ----------- 페이징 : end
			
			if(  $_SYSTEM['permission']['admin'] == true &&  $data['multi_delete'] == 'true') {
			echo   '						</form>';
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
	$print_button =  print_button_new('list', $data['permission'], $data['parameter'], $url, $img_url, $user_info, $arr_data);
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