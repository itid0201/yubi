<div class="module_wrap">	<!-- =======컨텐츠는 content_wrap로 감싸고 모듈은 module_wrap 으로 감싸야합니다.=====  -->
<?php
	## 상단문구 출력
	if(!empty($data['list_msg'])) {
		if($data['device']=='mobile') echo '<div class="top_alert">	<h3>알립니다 !</h3><div class="alert_content">'.stripslashes($data['list_msg']).'</div></div>';
		else echo '	<div class="board_guide guide_img1">'.stripslashes($data['list_msg']).'</div>';
	}
	## 검색 영역
	echo search_box_mobile_eng($data['search_type'], $data['search_word'], $data['search_list'], $data['search_parameter'], $data['total_count'] ); 
?>
<?php
	## 카테고리 표시 => 함수로 뺄까? 스킨때문에 고민이 된다.
	if($data['use_category_1'] == 'true') {
		$category_1_list    = unserialize($data['category_1_list']);
		$category_1_all     = unserialize($data['category_1_all']);
		$category_1         = '';
		$category_1_total   = 0;
		$arr_temp           = $data['navi_parameter'];
		$arr_temp['category_1'] = NULL;
		$category_parameter = make_GET_parameter($arr_temp);

		foreach($category_1_all as $key=>$value) {
			if($value == $data['category_1']) $category_1 .= '<li class="ui-btn-active"><a href="#none"><span>'.$value.'('.(empty($category_1_list[$value]) ? 0 : $category_1_list[$value]).')'.'</span></a></li>';
			else $category_1 .= '<li><a href="'.$_SERVER['PHP_SELF'].'?category_1='.rawurlencode($value).(empty($category_parameter) ? '' : '&amp;'.$category_parameter).'"><span>'.$value.'('.(empty($category_1_list[$value]) ? 0 : $category_1_list[$value]).')'.'</span></a></li>';
			$category_1_total += empty($category_1_list[$value]) ? 0 : $category_1_list[$value];
		}
		$category_1_total += $category_1_list[''];
		if(empty($data['category_1'])) $category_1 = '<li  class="ui-btn-active"><a href="#none"><span>전체('.$category_1_total.')'.'</span></a></li>'.$category_1;
		else $category_1 = '<li><a href="'.$_SERVER['PHP_SELF'].(empty($category_parameter) ? '' : '?'.$category_parameter).'"><span>전체('.$category_1_total.')'.'</span></a></li>'.$category_1;
		$category_1 = '<div data-role="navbar" class="category_list"><ul class="group">'.$category_1.'</ul></div>';

		echo $category_1;
	}

	## 목록 출력 리스트
	$buffer = array();
	$data['parameter']['mode'] = 'view';
	$view_parameter = make_GET_parameter($data['parameter']);
	$str_list  = '';	
	for($iter=0 ; $iter<$data['count'] ; $iter++) {
		$buffer = $data['list'][$iter];
		
				
		
		##  댓글
		$reply_str='';
		if($buffer['is_top'] != 'true' && $buffer['level'] > 0) {
			 $reply_str = '<span class="icon_reply">'.$reply_img.'</span>';
		}

		## 
		$link_class = '';
		if($buffer['is_lock'] == 'true') {
			$link_class .= 'list_lock';
			$href_view = '#';
		} else {
			$href_view = $_SERVER['PHP_SELF'].'?idx='.$buffer['idx'].(empty($view_parameter) ? '' : '&amp;'.$view_parameter);
		}
		if($buffer['is_delete'] == 'true') $link_class .= empty($link_class) ? 'title_delete' : ' title_delete';
		$link_class = empty($link_class) ? '' : ' class="'.$link_class.'"';			
		
		$str_list .= '        <li'.$link_class.'> ';
		$str_list .= '               	<a href="'.$href_view.'"> ';
		$str_list .= '                 	<div class="title_type1"> ';
		$str_list .= '                    	<strong>'.$buffer['title'].'</strong> ';
		$str_list .= '                    </div> ';
		$str_list .= '                    <p>'.call::strcut($buffer['contents'], 150, '...').'</p> ';
		$str_list .= '                  </a> ';		
		$str_list .= '                    <ul class="file_attach_type1"> '.download_box_mobile($buffer['idx'], $buffer['file_list2']).'</ul> ';
		//$str_list .= '                        <li class="excel"><a href=""><span class="icon">엑셀아이콘</span><span>2017년 3월분 학교급식 친환경농산물.xls(13.5kb)</span></a></li> ';
		//$str_list .= '                        <li class="hangul"><a href=""><span class="icon">한글아이콘</span><span>2017년 3월분 학교급식 친환경농산물 .hwp(10.2kb)</span></a></li> ';
		//$str_list .= '                        <li class="pdf"><a href=""><span class="icon">pdf아이콘</span><span>2017년 3월분 학교급식 친환경농산물 .hwp(10.2kb)</span></a></li> ';
		//$str_list .= '                        <li class="image"><a href=""><span class="icon">이미지아이콘</span><span>2017년 3월분 학교급식 친환경농산물 .hwp(10.2kb)</span></a></li> ';
		//$str_list .= '                    </ul> ';
		
		$str_list .= '                    <span>'.$buffer['reg_date'];
		if($buffer['new_img'] == 'true')		$str_list .= '                    	<span class="icon">새글</span>';
		$str_list .= '                    </span>';
		$str_list .= '                </li> ';
	}
	
	
/*	
		echo '<li>
				<a href="'.$href_view.'" '.$link_class.'>					
					'.($buffer['photo_exist']=='true'?'<div class="thmb"><img height="50" width="50" alt="" src="'.$buffer['photo_path'].'50x50/'.$buffer['photo_name'].'"><span class="mask"></span></div>':'').'
					<div class="cnt">
					<span class='.($buffer['is_top']=='true' ? '"list_top"' : '"list_num"').'>'.($buffer['is_top']=='true' ? '공지':'NO. '.number_format($buffer['list_num'],0)).'</span>			
						<p class="notice_tit" title="'.$buffer['title'].' 에 대한 글내용 보기.">'.$buffer['title'].'</p>					
						<span class="reg_name">'.$buffer['reg_name'].' |</span>
						<span class="list_date">'.$buffer['reg_date'].' |</span> 
						<span class="visit_cnt">조회 '.$buffer['visit_cnt'].'</span> 
					</div>	
					<p class="notice_txt">'.call::strcut($buffer['contents'], 90, '...').'</p>											
				</a>						
			</li>';
*/

	if($iter == 0) $str_list .= '<li class="no_result_eng">There is no search posts.</li>';	


?>
  <div class="list_type1">
  	<ul>    	
    <?php echo $str_list;?>
  	</ul>
  </div>
  
  <!--<p>스크롤시 쭉쭉나오게 부탁드립니다</p>-->
  
<?php 
	
	## 페이지 네비게이션 영역
	$navi_parameter = make_GET_parameter($data['navi_parameter']);
	$page_navi      = page_navigation_mobile_eng($data['total_count'], $data['page_scale'], $data['block_scale'], $data['page'], $navi_parameter);
	echo '<!-- page navigation START --><div class="pagenum">'.$page_navi.'</div><!-- page navigation END -->';

	## 검색 영역
//	echo search_box_tour($data['search_type'], $data['search_word'], $data['search_list'], $data['search_parameter']); //검색키가 다르거나 별도로 추가해야할경우..????
	
	
	## 버튼 영역.
	$img_url = '/images/common/board/temp';
	$url = $_SERVER['PHP_SELF'];
	$user_info = array();
	$user_info['is_login'] = empty($_SYSTEM['myinfo']['is_login']) ? NULL : $_SYSTEM['myinfo']['is_login'];
	$user_info['user_pin'] = empty($_SYSTEM['myinfo']['my_pin']) ? NULL : $_SYSTEM['myinfo']['my_pin'];
	$data['parameter']['mode'] = NULL;
	echo print_button_mobile_eng('list', $data['permission'], $data['parameter'], $url, $img_url, $user_info);

?>  
</div>
