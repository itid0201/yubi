<?php	
	/* list skin */
	$lock_img  = '<span class="icon_lock">비공개글</span>';
	$reply_img = '<span class="icon_reply">답변글</span>';
	$new_img   = '<span class="icon_new">새로운글</span>';
//	$file_img  = '<span class="icon_attach">일반파일 첨부</span>';
//	$photo_img = '<span class="icon_attach">이미지 파일 첨부</span>';
//	$movie_img = '<span class="icon_attach">동영상파일 첨부</span>';
//	$hidden_img = '<span class="icon_hidden">비승인글</span>'; 	
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
	$view_parameter = make_GET_parameter($data['parameter'], '&amp;', true);
	$print_table_tr ='';		
	

	for($iter=0 ; $iter<$data['count'] ; $iter++) {
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
		//if($buffer['comment_cnt'] > 0) print_img .= '<span class="comment_cnt">'.$buffer['comment_cnt'].'</span>'; //댓글개수 수정 : 오경우(20120125)				
		if($buffer['reply_cnt'] > 0) {		## 답글 처리 	
			$print_img .=  $reply_img ;
			$print_img_cnt++;
		}
		$print_img = !empty($print_img)?'<div class="icon_box">'.$print_img.'</div>':'';
		$link_class .= $print_img_cnt>0?' icon_0'.$print_img_cnt:'';
		
		if($buffer['photo_exist'] == 'true') {
			##2022.08.03 최무성 여수 접근성 작업, a 안에 제목, 내용 있으면 이미지 알트는 ""
			//$img =  '<div class="thumb_box"><img src="'.$buffer['photo_path'].'282x159/'.$buffer['photo_name'].'" alt="'.htmlspecialchars($buffer['photo_alt']).'" /></div>';
			$img =  '<div class="thumb_box"><img src="'.$buffer['photo_path'].'282x159/'.$buffer['photo_name'].'" alt="" /></div>';
			$link_class .= " hasthumb";
		} else {
			$img =  '';
		}			
		
		## 제목 a 태그 클래스 선처리 맨 마지막에 와야함. 
		$link_class = empty($link_class) ? '' : 'class="'.$link_class.'"';						
		##---- class 선처리 -- end		
					
		
		
		$print_table_tr  .=  '    			<div class="item">';
		$print_table_tr  .=  '    				<a href="'.$href_view.'" '.$link_class.' title="'.$buffer['title'].' 에 대한 글내용 보기.">';
		$print_table_tr  .=  $img;
		$print_table_tr  .=  '    					<div class="cont_box">';
		$print_table_tr  .=  '    						<div class="title_box"><h3>'.$buffer['title'].$print_img.'</h3></div>';
        /* 20200811 김다정 나주청년센터 프로그램 안내에서 사용 */
        if($_SYSTEM['menu_info']['_board_id'] == 'najuyouth_guide') {
            $print_table_tr  .=  '    						<p>'.$buffer['varchar_1'].'</p>';
            $print_table_tr  .=  '    						<dl>';
            $print_table_tr  .=  '    							<dt class="text_hidden">담당정보 : </dt><dd>'.$buffer['varchar_2'].'</dd>';
        } else {
            $print_table_tr  .=  '    						<p>'.$buffer['contents'].'</p>';
            $print_table_tr  .=  '    						<dl>';
            $print_table_tr  .=  '    							<dt class="text_hidden">작성자 : </dt><dd>'.$writer.'</dd>';
        }
		$print_table_tr  .=  '    							<dt class="text_hidden">작성날짜 : </dt><dd class="date">'.$buffer['reg_date'].'</dd>';
		$print_table_tr  .=  '    							<dt>조회수</dt><dd>'.$buffer['visit_cnt'].'</dd>';
		$print_table_tr  .=  '    						</dl>';
		$print_table_tr  .=  '    					</div>';
		$print_table_tr  .=  '    				</a>';
		$print_table_tr  .=  			'<a href="#none" class="board_btn_share share_btn"><span class="icon"></span>공유</a>';
		$print_table_tr  .=  			'<div class="board_share_box">';
		$print_table_tr  .=  				'<ul class="share_btn">';
		$print_table_tr  .=  '    				<li class="facebook share_btn"><a href="#none" class="share_btn" data-type="facebook" data-url="'.$href_view.'" data-title="'.$buffer['title'].'"><span class="icon share_btn">페이스북</span></a></li>';
		$print_table_tr  .=  '    				<li class="twitter share_btn"><a href="#none" class="share_btn" data-type="twitter" data-url="'.$href_view.'" data-title="'.$buffer['title'].'"><span class="icon  share_btn">트위터</span></a></li>';
		$print_table_tr  .=  '    				<li class="kakaostory share_btn"><a href="#none" class="share_btn" data-type="kakaostory" data-url="'.$href_view.'" data-title="'.$buffer['title'].'"><span class="icon  share_btn">카카오스토리</span></a></li>';
		$print_table_tr  .=  '    				<li class="band share_btn"><a href="#none" class="share_btn" data-type="band" data-url="'.$href_view.'" data-title="'.$buffer['title'].'"><span class="icon  share_btn">네이버밴드</span></a></li>';
		$print_table_tr  .=  				'</ul>';
		$print_table_tr  .=  			'</div>';		
		$print_table_tr  .=  '    			</div>';	
		
	}	

	if( empty($print_table_tr) ){
		$print_table_tr .= '<tr><td class="data_none" colspan="'.$colspan.'"><div class="no_result"><span class="icon"></span>검색내역이 없습니다.</div></td></tr>';
	}	


	## ================================================================================================	
	## table 출력 
	## ================================================================================================	

	$print_table  = '';	
	$print_table .=  '				<div class="board_list">';
	$print_table .=  '					<div class="board_thumb">';
	$print_table .= $print_table_tr;
	$print_table .= '					</div>';
	$print_table .= '				</div>';	
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
		if($data['use_category_1'] == 'true') {
			$category_1_list    = unserialize($data['category_1_list']);
			$category_1_all     = unserialize($data['category_1_all']);
			$arr_temp           = $data['navi_parameter'];
			$arr_temp['category_1'] = NULL;
		//	$category_parameter = make_GET_parameter($arr_temp);			
			echo print_category($data['category_1'], $category_1_list , $category_1_all, $arr_temp, $_SERVER['PHP_SELF'] );			
		}
		## -----------  카테고리 : end
		
		
		## -----------  인기글 : start
		if( $data['user_list_hot'] == 'true' ){
if( $_SERVER['REMOTE_ADDR'] == "49.254.140.140" && $_SESSION['user_id'] == "jini0808" ){
//	echo '<pre>';print_r( $data['hot_articles_data'] );	exit;
}
			echo print_hot_new($data['hot_articles_data']); 	
		}		
		## -----------  인기글 : end
		?>
		<div class="board_list_box">
			<?php 
			
	
			
			## -----------  검색 영역 : start
			echo search_box_new($data['total_count'], $data['search_type'], $data['search_word'], $data['search_list'], $data['page_scale_search'], $data['page_scale'], $data['start_date'], $data['finish_date'], $data['search_parameter']); 
			## -----------  검색 영역 : end
			
//			echo   '		<div class="board_list">';	
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
//			echo   '		</div>';			
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
<script src="<?php echo $_SERVER['SELF']; ?>js/list.js" ></script>
<?php 	
## 관리자 삭제일떄만 출력되는 javascript----- start
if( $_SYSTEM['permission']['admin'] == true && $data['multi_delete'] == 'true') { 
	echo '<script src="/js/module/list_admin.js"></script>';
} 
## 관리자 삭제일떄만 출력되는 javascript----- end
?>						
