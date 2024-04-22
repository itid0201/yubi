<?php	

if( $_SERVER['REMOTE_ADDR'] == "49.254.140.140" && $_SESSION['user_id'] == "hya9196" ){
//echo '<pre>';print_r( $data );exit;
}

	/* list skin */
	$lock_img  = '<span class="icon_lock">비공개글</span>';
	$reply_img = '<span class="icon_reply">답변글</span>';
	$new_img   = '<span class="icon_new">새로운글</span>';
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
	for($iter=0 ; $iter < $data['count'] ; $iter++) {
		$buffer = $data['list'][$iter];			
		
		##---- class 선처리 -- start
		## 작성자명 설정.
		if(empty($data['writer_display']) || in_array($data['writer_display'],array('person', 'other_organ'))) {
			$writer = $buffer['reg_name'];
		} else {
			$writer = empty($buffer['depart_name']) ? $buffer['reg_name'] : $buffer['depart_name'];
		}						
		
		$link_class = 'item_cont ';
		
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
		if( isset($buffer['reply_cnt']) && $buffer['reply_cnt'] > 0) {		## 답글 처리 	
			$print_img .=  $reply_img ;
			$print_img_cnt++;
		}
		$print_img = !empty($print_img)?'<span class="icon_box">'.$print_img.'</span>':'';
		$link_class .= $print_img_cnt>0?' icon_0'.$print_img_cnt:'';
		
		if($buffer['photo_exist'] == 'true') {
			$img =  '<div class="thumb_box"><img src="'.$buffer['photo_path'].'324x198/'.$buffer['photo_name'].'" alt="'.htmlspecialchars($buffer['photo_alt']).'" /></div>';
			$link_class .= " hasthumb";
		} else {
			$img =  '<img src="/images/board/noimage_thumb_photo.png" alt="이미지가 없습니다" />';
		}			
		
		## 제목 a 태그 클래스 선처리 맨 마지막에 와야함. 
		$link_class = empty($link_class) ? '' : 'class="'.$link_class.'"';						
		##---- class 선처리 -- end			
		
		## 이미지 불러오기
		$img_list = array();
		foreach($buffer['photo_list'] as $item) {		
			$img_list[] = array('file_name'=>$item['photo_name'],'alt'=>$item['photo_alt'],'idx'=>$buffer['idx']);
		}
	
		$img_count = ( $buffer['photo_cnt'] > 5 )? 6: $buffer['photo_cnt'];
		
		if( $img_count > 0 ){
			$width = $height = $margin = '';
			//$img = $module->get_munti_thumb_new($width,$height,$margin,$img_list,$buffer['photo_path'],$href_view,$img_count);
			
			## 2022.08.03 최무성 여수 접근성 작업 , 알트에 글 제목
			//$img = '<div class="thumb_box"><img src="'.$buffer['photo_path'].'324x324/'.$buffer['photo_name'].'" alt="'.htmlspecialchars($buffer['photo_alt']).'" /></div>';	
			$img = '<div class="thumb_box"><img src="'.$buffer['photo_path'].'324x324/'.$buffer['photo_name'].'" alt="'.htmlspecialchars($buffer['title']).'" /></div>';	
			if($buffer['photo_exist'] != 'true' && $_SYSTEM['permission']['admin'] == true) $img =  '<img src="/images/board/noimage_thumb_photo.png" height="347" alt="이미지가 없습니다" />';	
		}else{
			$img = '<img src="/images/board/noimage_thumb_photo.png" alt="이미지가 없습니다" />';
		} 
				
		$print_table_tr  .=  '    			<div class="item">';
		$print_table_tr  .=  '    					<div class="hover_box"><div class="lt"></div><div class="lr"></div><div class="lb"></div><div class="ll"></div></div>';
		$print_table_tr  .=  '    					<div class="thumb_box" >';
		$print_table_tr  .=  '    						<a href="'.(empty($buffer['link_url'])?'#none':$buffer['link_url']).'" '.(empty($buffer['link_url'])?'':' target="_blank" ').'class="item_click">';
		$print_table_tr  .= $img;
//if( $_SERVER['REMOTE_ADDR'] == "49.254.140.140" || $_SERVER['REMOTE_ADDR'] == "168.131.244.65" ){
	if( $module->module_config['board_id'] == 'gongik_education_ing' && $module->module_config['use_category_1'] == "true" && !empty($buffer['category_1']) ){
		$print_table_tr  .=  '    						<span class="'.$PROC_1[$buffer['category_1']].'">'.$buffer['category_1'].'</span>';		
	}
//}		
		$print_table_tr  .=  '    						</a>';
		$print_table_tr  .=  '    					</div>';
		$print_table_tr  .=  '    					<div class="cont_box">';
		$print_table_tr  .=  '    						<div class="top_util">';
		$print_table_tr  .=  '    							<ul>';
		$print_table_tr  .=  '    								<li class="view"><span class="icon">조회수</span>'.$buffer['visit_cnt'].'</li>';
		if( $module->module_config['use_comment'] == 'true' ) {		
		$print_table_tr  .=  '    								<li class="reply"><span class="icon">댓글수</span>'.$buffer['comment_cnt'].'</li>';
		}
		$print_table_tr  .=  '    								<li class="share_wrap">';
		$print_table_tr  .=  '    									<a href="#none" class="board_btn_share share_btn"><span class="icon"></span>공유</a>';
		$print_table_tr  .=  '    									<div class="board_share_box">';
		$print_table_tr  .=  '    										<ul class="share_btn">';
		$print_table_tr  .=  '    											<li class="facebook share_btn"><a href="#none" class="share_btn" data-type="facebook" data-url="'.$href_view.'" data-title="'.$buffer['title'].'"><span class="icon share_btn">페이스북</span></a></li>';
		$print_table_tr  .=  '    											<li class="twitter share_btn"><a href="#none" class="share_btn" data-type="twitter" data-url="'.$href_view.'" data-title="'.$buffer['title'].'"><span class="icon  share_btn">트위터</span></a></li>';
		$print_table_tr  .=  '    											<li class="kakaostory share_btn"><a href="#none" class="share_btn" data-type="kakaostory" data-url="'.$href_view.'" data-title="'.$buffer['title'].'"><span class="icon  share_btn">카카오스토리</span></a></li>';
		$print_table_tr  .=  '    											<li class="band share_btn"><a href="#none" class="share_btn" data-type="band" data-url="'.$href_view.'" data-title="'.$buffer['title'].'"><span class="icon  share_btn">네이버밴드</span></a></li>';
		$print_table_tr  .=  '    										</ul>';
		$print_table_tr  .=  '    									</div>';
		$print_table_tr  .=  '    								</li>';
		$print_table_tr  .=  '    							</ul>';
		$print_table_tr  .=  '    						</div>';
		$print_table_tr  .=  '    						<h3>'.$buffer['title'].$print_img.'</h3>';
		if($buffer['contents'])	$print_table_tr  .=  '<p>'.$buffer['contents'].'</p>';		
		$print_table_tr  .=  '							<dl>';
		$print_table_tr  .=  '								<dt class="text_hidden">작성날짜</dt><dd>'.$buffer['reg_date'].'</dd>';
		$print_table_tr  .=  '								<dt class="text_hidden">작성자</dt><dd>'.$writer.'</dd>';
		$print_table_tr  .=  '							</dl>';
		$print_table_tr  .=  '						</div>';
		if(  $_SYSTEM['permission']['admin'] == true ){
		$print_table_tr  .=  '						<div class="bottom_btn"><a href="'.$href_view.'" class="modify">수정</a>'.($buffer['period_type']=='y'?'<span class="admin_period_info">기간설정 : '.($buffer['period_type_check']=='y'?'보임':'숨김').'</span>':'').'</div>';	
		}		
		$print_table_tr  .=  '    				</div>';	

		
	}	

	if( empty($print_table_tr) ){
		$print_table_tr = '<div class="no_data"><div class="no_result"><span class="icon"></span>검색내역이 없습니다.</div></div>';
	}	


	## ================================================================================================	
	## table 출력 
	## ================================================================================================	

	$print_table  = '';	
	$print_table .= '<div class="board_list">
						<div class="board_photo">';
	$print_table .= tag_box($data['keyword']);
	$print_table .= '		<div class="item_wrap">';
	$print_table .= $print_table_tr;
	$print_table .= '		</div>';
	$print_table .= '	</div>';
	$print_table .= '</div>';	
	## ================================================================================================	
?>

<div class="board_wrapper">
	<div class="module_list_box">
		<?php 
		## 상단문구 출력
		if(!empty($data['list_msg'])) {
			if($data['device']=='mobile') echo '<p>'.stripslashes($data['list_msg']).'</p>';
			else echo stripslashes($data['list_msg']); ## 상단문구 전용
			//else echo '<div class="sub-tit-top"></div><div class="content_top_alert"><div class="alert_content">'.stripslashes($data['list_msg']).'</div></div><div class="sub-tit-bottom"></div>';
		}	
		
		
		## 상단문구 출력(스타일 없음))
		if(!empty($data['list_msg_no_css'])) {
			echo '<div class="alert_content_none">'.stripslashes($data['list_msg_no_css']).'</div>';
		}

		## -----------  카테고리.: start
		if($data['use_category_1'] == 'true') {
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
		<div class="board_list_box thumb_photo_box">
			<?php 			
			## -----------  검색 영역 : start
			echo search_box_new($data['total_count'], $data['search_type'], $data['search_word'], $data['search_list'], $data['page_scale_search'], $data['page_scale'], $data['start_date'], $data['finish_date'], $data['search_parameter']); 
			## -----------  검색 영역 : end
			
			echo   '		<div class="board_list">';	
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
		//	$navi_parameter = make_GET_parameter($data['navi_parameter'], '&amp;', true);
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
<script>
	var isLogin = <?php echo ($_SYSTEM['myinfo']['is_login'] == true) ? 'true':'false'; ?>;
	var selfUrl = "<?php echo $_SERVER['PHP_SELF'];?>";		
</script>
<script src="<?php echo $_SERVER['SELF']; ?>js/list.js" defer ></script>
<?php 	
## 관리자 삭제일떄만 출력되는 javascript----- start
if( $_SYSTEM['permission']['admin'] == true && $data['multi_delete'] == 'true') { 
//echo '<script src="'.$_SERVER['SELF'].'js/list_admin.js" ></script>';
} 
## 관리자 삭제일떄만 출력되는 javascript----- end
?>	