
<?php

/* list skin */
	$lock_img  = '<span class="icon_lock">Private Posts</span>';
	$reply_img = '<span class="icon_reply">Answer</span>';
	$file_img  = '<span class="icon_attach">Attached File</span>';
	$photo_img = '<span class="icon_image">Attached Picture</span>';
	$movie_img = '<span class="icon_movie">Attached Movie</span>';
	$new_img   = '<span class="icon_new">New Posts</span>';
	$hidden_img = '<span class="icon_hidden">비승인글</span>'; //********************************************************************************* 여기 추가
	
	
	echo $data['debug']; // only test..

	## 상단문구 출력
	if(!empty($data['list_msg'])) {
		if($data['device']=='mobile') echo '<p>'.stripslashes($data['list_msg']).'</p>';
		else echo '<div class="sub-tit-top"></div><div class="content_top_alert"><div class="alert_content">'.stripslashes($data['list_msg']).'</div></div><div class="sub-tit-bottom"></div>';
	}
	
	## 상단문구 출력
	if(!empty($data['list_msg_no_css'])) {
		if($data['device']=='mobile') echo '<p>'.stripslashes($data['list_msg_no_css']).'</p>';
		else echo '<div class="content_top_alert2">'.stripslashes($data['list_msg_no_css']).'</div>';
	}

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
			if($value == $data['category_1']) $category_1 .= '<li class="on">'.$value.'<span>('.(empty($category_1_list[$value]) ? 0 : $category_1_list[$value]).')'.'</span></li>';
			else $category_1 .= '<li><a href="'.$_SERVER['PHP_SELF'].'?category_1='.rawurlencode($value).(empty($category_parameter) ? '' : '&amp;'.$category_parameter).'">'.$value.'<span>('.(empty($category_1_list[$value]) ? 0 : $category_1_list[$value]).')'.'</span></a></li>';
			$category_1_total += empty($category_1_list[$value]) ? 0 : $category_1_list[$value];
		}
		$category_1_total += $category_1_list[''];
		if(empty($data['category_1'])) $category_1 = '<li  class="first on">전체<span>('.$category_1_total.')'.'</span></li>'.$category_1;
		else $category_1 = '<li class="first"><a href="'.$_SERVER['PHP_SELF'].(empty($category_parameter) ? '' : '?'.$category_parameter).'">전체<span>('.$category_1_total.')'.'</span></a></li>'.$category_1;
		$category_1 = '<ul class="cate_list">'.$category_1.'</ul>';
		echo $category_1;
	}

	$colspan = 5;
	
	if($data['use_category_1'] == 'true') {
		$colspan++;
	}
	if($data['use_process_1'] == 'true') {
		$colspan++;
	}
	if($data['use_allow'] == 'true') {
		$colspan++;
	}
	
	## 등록자 표시 부분 열라 급조한것이다. 오픈이 없어서.. 나중에 다른 방식으로 수정해야한다.
	if($data['writer_display'] == 'department') $writer_name = '등록부서';
	else if($data['writer_display'] == 'other_organ') $writer_name = '기관/부서명';
	else if($data['writer_display'] == 'processing') $writer_name = '처리부서';
	else $writer_name = '등록자';

//	echo '<form action="" method="post" name="form_del" id="form_del">';
//	echo '<input type="hidden" name="mode" value="delete_all" />';

	echo '<table class="board_t1">';
	echo '  <caption>'.$_SYSTEM['menu_info']['title'].' 게시물. 총 '.$data['total_count'].'건, '.$data['total_page'].'페이지 중 '.$data['page'].'페이지 '.$data['count'].'건 입니다. 본 데이터표는 '.$colspan.'컬럼, '.$data['count'].'로우로 구성되어 있습니다. 각 로우는 번호, '.($data['use_category_1'] == 'true'?'분류, ':'').' 제목, '.($data['use_process_1'] == 'true'?'처리상태, ':'').' '.($data['use_allow'] == 'true'?'승인상태, ':'').' '.$writer_name.', 등록일, 조회로 구성되어 있습니다.</caption>';
	
	
	
/*	echo '  <colgroup>';
	echo '  	<col width="10%" />';
	if($_SYSTEM['permission']['admin'] == true && $data['multi_delete'] == 'true') echo '  	<col width="5%" />';
	if($data['use_category_1'] == 'true') echo '  	<col width="15%" />';
	echo '  	<col />';
	if($data['use_process_1'] == 'true') echo '  	<col width="10%" />';
	if($data['use_allow'] == 'true') echo '  	<col width="10%" />';
	echo '  	<col width="'.((empty($data['writer_display']) || $data['writer_display'] == 'person') ? '15' : '20').'%" />';
	if($data['use_reg_date'] != 'false') echo '  	<col width="12%" />';
	echo '  	<col width="10%" />';
	echo '  </colgroup>';*/
	
	
	
	echo '  <thead>';
	echo '    <tr class="bg-color">';
	echo '      <th scope="col">号码</th>';
	if($_SYSTEM['permission']['admin'] == true && $data['multi_delete'] == 'true') echo '<th scope="col"><input type="checkbox" name="check_all" id="check_all" /></th>';
	if($data['use_category_1'] == 'true') echo '<th scope="col">Categories</th>';
	echo '      <th scope="col" class="w45">题目</th>';
	if($data['use_process_1'] == 'true') echo '<th scope="col">Processing</th>';
	if($data['use_allow'] == 'true') echo '<th scope="col">Status</th>';
	echo '      <th scope="col" class="w15">登录者姓名</th>';
	if($data['use_reg_date'] != 'false') echo '      <th scope="col">浏览次数</th>';
	echo '      <th scope="col" class="bg_none">瀏覽次數</th>';
	echo '    </tr>';
	echo '  </thead>';
	echo '  <tbody>';


	$buffer = array();
	$data['parameter']['mode'] = 'view';
	$view_parameter = make_GET_parameter($data['parameter']);
	for($iter=0 ; $iter<$data['count'] ; $iter++) {
		$buffer = $data['list'][$iter];
		echo '    <tr'.($buffer['is_top']=='true' ? ' class="tr_notice"' : '').'>';
		echo '      <td class="center">'.number_format($buffer['list_num'],0).'</td>';
		if($_SYSTEM['permission']['admin'] == true && $data['multi_delete'] == 'true') echo '<td><input type="checkbox" name="check_idx[]" id="check_idx_'.$buffer['idx'].'" value="'.$buffer['idx'].'" /></td>';
		if($data['use_category_1'] == 'true') echo '<td class="list_cate">'.$buffer['category_1'].'</td>';
		echo '      <td class="title" style="padding-left:'.($buffer['level']*15).'px;">';
		if($buffer['is_top'] != 'true' && $buffer['level'] > 0) {
			for($i=0;$i<$buffer['level'];$i++) echo "&nbsp;&nbsp;&nbsp;";
			echo $reply_img;
		}

		$link_class = '';
		if($buffer['is_lock'] == 'true') {
			$link_class .= 'list_lock';
			$href_view = '#';
		} else {
			$href_view = $_SERVER['PHP_SELF'].'?idx='.$buffer['idx'].(empty($view_parameter) ? '' : '&amp;'.$view_parameter);
		}
		if($buffer['is_delete'] == 'true') $link_class .= empty($link_class) ? 'title_delete' : ' title_delete';
		$link_class = empty($link_class) ? '' : 'class="'.$link_class.'"';

        //echo '<a href="'.$href_view.'" '.$link_class.' title="'.htmlspecialchars($buffer['title']).' 에 대한 글내용 보기.">'.$buffer['title'].'</a>';
		echo '<a href="'.$href_view.'" '.$link_class.' title="'.$buffer['title'].'">'.$buffer['title'].'</a>';
		if($buffer['lock_img'] == 'true') echo $lock_img;
		if($buffer['allow'] == 'n') echo $hidden_img; //****************************************************************************************  여기추가
		//if($buffer['file_exist'] == 'true') echo $file_img;   // 2012.02.22 오경우 수정
		//if($buffer['photo_exist'] == 'true') echo $photo_img; // 2012.02.22 오경우 수정
		//if($buffer['movie_exist'] == 'true') echo $movie_img; // 2012.02.22 오경우 수정
		if($buffer['file_exist'] == 'true' || $buffer['photo_exist'] == 'true' || $buffer['movie_exist'] == 'true') echo $file_img;
		if($buffer['new_img'] == 'true') echo $new_img;
		if($buffer['comment_cnt'] > 0) echo '<span class="comment_cnt">'.$buffer['comment_cnt'].'</span>'; //댓글개수 수정 : 오경우(20120125)
		echo '      </td>';
		if($data['use_process_1'] == 'true') echo '<td>'.$buffer['process_1'].'</td>';
		if($data['use_allow'] == 'true') echo '<td>'.($buffer['allow']=='y' ? 'Approval' : 'Disapproval').'</td>';
		
		if(empty($data['writer_display']) || in_array($data['writer_display'],array('person', 'other_organ'))) {
			$writer = $buffer['reg_name'];
		} else {
			$writer = empty($buffer['depart_name']) ? $buffer['reg_name'] : $buffer['depart_name'];
		}
		echo '      <td class="center">'.$writer.'</td>';
		if($data['use_reg_date'] != 'false') echo '      <td class="date center">'.$buffer['reg_date'].'</td>';
		echo '      <td class="visit center">'.$buffer['visit_cnt'].'</td>';
		echo '    </tr>';
	}

	if($iter == 0) echo '<tr><td colspan="'.$colspan.'" class="list_empty">没有登记的数据</td></tr>';

	echo '  </tbody>';
	echo '</table>';

//	echo '</form>';

	if($_SYSTEM['permission']['admin'] == true && $data['multi_delete'] == 'true') echo '<p><a href="#none" id="delete_all"><span class="btn_pack btn_down">선택삭제</span></a></p>';	

	## 페이지 네비게이션 영역

	$navi_parameter = make_GET_parameter($data['navi_parameter']);
	$page_navi      = page_navigation_tour_eng($data['total_count'], $data['page_scale'], $data['block_scale'], $data['page'], $navi_parameter);
	echo '<!-- page navigation START --><div class="paging">'.$page_navi.'</div><!-- page navigation END -->';

	## 검색 영역
	//echo search_box_tour_eng($data['search_type'], $data['search_word'], $data['search_list'], $data['search_parameter']); //검색키가 다르거나 별도로 추가해야할경우..????

	/*20210507 검색박스 수정*/
	echo search_box_new_foreign("ch", $data['total_count'], $data['search_type'], $data['search_word'], $data['search_list'], $data['page_scale_search'], $data['page_scale'], $data['start_date'], $data['finish_date'], $data['search_parameter'],$data); 

	## 버튼 영역.
	$img_url = '/images/common/board/temp';
	$url = $_SERVER['PHP_SELF'];
	$user_info = array();
	$user_info['is_login'] = empty($_SYSTEM['myinfo']['is_login']) ? NULL : $_SYSTEM['myinfo']['is_login'];
	$user_info['user_pin'] = empty($_SYSTEM['myinfo']['my_pin']) ? NULL : $_SYSTEM['myinfo']['my_pin'];
	$data['parameter']['mode'] = NULL;
	echo print_button_tour_ch('list', $data['permission'], $data['parameter'], $url, $img_url, $user_info, $data['use_logoff_write']);

	$add_script = NULL;
	if($_SYSTEM['permission']['admin'] == true && $data['multi_delete'] == 'true') {
		$add_script .= '<script name="import_jquery.ready">'.CR;
		$add_script .= '	$("#check_all").click(function(e) {'.CR;
		$add_script .= '		$(".board_t1 input:checkbox").attr("checked",this.checked);'.CR;
		$add_script .= '	});'.CR;
		$add_script .= '	$("#delete_all").click(function(){'.CR;
		$add_script .= '		$("#form_del").submit();'.CR;
		$add_script .= '	});'.CR;
		$add_script .= '</script>'.CR;
	}
	echo $add_script;
?>

