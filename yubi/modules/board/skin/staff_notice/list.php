<?php	
	/* list skin */
	$lock_img  = '<span class="icon_lock">비공개글</span>';
	$reply_img = '<span class="icon_reply">답변글</span>';
	$file_img  = '<span class="icon_attach">일반파일 첨부</span>';
	$photo_img = '<span class="icon_attach">이미지 파일 첨부</span>';
	$movie_img = '<span class="icon_attach">동영상파일 첨부</span>';
	$new_img   = '<span class="icon_new1">새로운글</span>';
	$hidden_img = '<span class="icon_hidden">비승인글</span>'; 	
	echo $data['debug']; // only test..

	## 대형폐기물 공지사항에 신고게시판으로 링크 버튼 요청
	if($_SYSTEM['module_config']['board_id'] == 'www_large_waste_notice') {
		echo '<p class="btn_p align_right mab30"><a class="p4 btn_waste" href="/www/civil_complaint/field_complaint/large_waste/large_waste_report">대형폐기물배출신고</a></p>';
	}
	
?>
<div class="board_wrap">
<?php
	## 상단문구 출력
	if(!empty($data['list_msg'])) {
		if($data['device']=='mobile') echo '<p>'.stripslashes($data['list_msg']).'</p>';
		else echo '<div class="sub-tit-top"></div><div class="content_top_alert"><div class="alert_content">'.stripslashes($data['list_msg']).'</div></div><div class="sub-tit-bottom"></div>';
	}		

	## 카테고리.
	if($data['use_category_1'] == 'true') {
		$category_1_list    = unserialize($data['category_1_list']);
		$category_1_all     = unserialize($data['category_1_all']);
		$category_1         = '';
		$category_1_total   = 0;
		$arr_temp           = $data['navi_parameter'];
		$arr_temp['category_1'] = NULL;
		$category_parameter = make_GET_parameter($arr_temp, '&amp;', true);

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

	$colspan = 6;
	
	if($data['use_category_1'] == 'true') {
		$colspan++;
	}
	if($data['use_process_1'] == 'true') {
		$colspan++;
	}
	if($data['use_allow'] == 'true' && $_SYSTEM['permission']['admin'] == 'true') {
		$colspan++;
	}
	
	
	if($data['multi_delete'] == 'true') {
	echo '<form action="" method="post" name="form_del" id="form_del">';
	echo '<input type="hidden" name="mode" value="delete_all" />';
	}
	
	echo '<table class="board_t1">';
	echo '  <caption>'.$_SYSTEM['menu_info']['title'].' 게시물. 
		총 '.$data['total_count'].'건, '.$data['total_page'].'페이지 중 '.$data['page'].'페이지 '.$data['count'].'건 입니다. 
		본 데이터표는 '.$colspan.'컬럼, '.$data['count'].'로우로 구성되어 있습니다. 
		각 로우는 번호, '.($data['use_category_1'] == 'true'?'분류, ':'').' 제목, '.($data['use_process_1'] == 'true'?'처리상태, ':'').' '.($data['use_allow'] == 'true' && $_SYSTEM['permission']['admin'] == 'true'?'승인상태, ':'').' 첨부파일, '.$writer_name.', 등록일, 조회수로 구성되어 있습니다.</caption>';	
	##--- thead -- start	
	echo '  <thead>';
	echo '    <tr class="bg-color">';
	echo '      <th scope="col">번호</th>';
	if($_SYSTEM['permission']['admin'] == true && $data['multi_delete'] == 'true') echo '<th scope="col"><input type="checkbox" name="check_all" id="check_all" /></th>';
	if($data['use_category_1'] == 'true') echo '<th scope="col">분류</th>';
	echo '      <th scope="col" class="w50">제목</th>';
	if($data['use_process_1'] == 'true') echo '<th scope="col">처리상태</th>';
	if($data['use_allow'] == 'true' && $_SYSTEM['permission']['admin'] == 'true') echo '<th scope="col">승인상태</th>';
	echo '      <th scope="col" class="w10">발송자</th>';
	echo '      <th scope="col">발송일</th>';
	echo '      <th scope="col">발송그룹</th>';			
	echo '      <th scope="col" class="bg_none">발송건수</th>';
	echo '    </tr>';
	echo '  </thead>';
	##--- thead -- end

	##--- tbody -- start		
	echo '  <tbody>';
	$buffer = array();
	$data['parameter']['mode'] = 'view';
	$view_parameter = make_GET_parameter($data['parameter'], '&amp;', true);
	for($iter=0 ; $iter<$data['count'] ; $iter++) {
		$buffer = $data['list'][$iter];
		##---- class 선처리 -- start
		$link_class = '';
		if($buffer['is_lock'] == 'true') {	//비공개글
			$link_class .= 'list_lock';
			$href_view = '#none';
		} else {
			$href_view = $_SERVER['PHP_SELF'].'?idx='.$buffer['idx'].(empty($view_parameter) ? '' : '&amp;'.$view_parameter);
		}
		if($buffer['is_delete'] == 'true') $link_class .= empty($link_class) ? 'title_delete' : ' title_delete';	//삭제글표시
		
		$link_class = empty($link_class) ? '' : 'class="'.$link_class.'"';		
		##---- class 선처리 -- end
		
		echo '    <tr'.($buffer['is_top']=='true' ? ' class="tr_notice"' : '').'>';
		echo '      <td'.($buffer['is_top']=='true' ? ' class="list_idx"' : '').'>'.($buffer['is_top']=='true' ? '공지' : $buffer['list_num']).'</td>';
		if($_SYSTEM['permission']['admin'] == true && $data['multi_delete'] == 'true') 
		echo '		<td><input type="checkbox" name="check_idx[]" id="check_idx_'.$buffer['idx'].'" value="'.$buffer['idx'].'" /></td>';
		if($data['use_category_1'] == 'true') echo '<td class="list_cate">'.$buffer['category_1'].'</td>';		
		echo '      <td class= "align_l title_wrap">';	
		/* 답글 처리 */
		if($buffer['is_top'] != 'true' && $buffer['level'] > 0) {
			for($i=0;$i<$buffer['level'];$i++) echo "&nbsp;&nbsp;&nbsp;";
			echo $reply_img;
		}        
		echo '			<a href="'.$href_view.'" '.$link_class.' title="'.$buffer['title'].' 에 대한 발송내용 보기." class="title_cont">'.$buffer['title'].'</a>';
		if($buffer['open'] == 'n') echo $lock_img;
		if($buffer['allow'] == 'n' && $_SYSTEM['permission']['admin'] == 'true') echo $hidden_img; 
		if($buffer['new_img'] == 'true') echo $new_img;
		if($buffer['comment_cnt'] > 0) echo '<span class="comment_cnt">'.$buffer['comment_cnt'].'</span>'; //댓글개수 수정 : 오경우(20120125)
		echo '      </td>';
		if($data['use_process_1'] == 'true') {
			if($buffer['process_1'] == '신청') {
				echo '<td><div class="proc_wrap"><span class="request">'.$buffer['process_1'].'</span></div></td>';
			} elseif($buffer['process_1'] == '접수') {
				echo '<td><div class="proc_wrap"><span class="receipt">'.$buffer['process_1'].'</span></div></td>';
			} elseif($buffer['process_1'] == '보류') {
				echo '<td><div class="proc_wrap"><span class="defer">'.$buffer['process_1'].'</span></div></td>';
			} elseif($buffer['process_1'] == '완료') {
				echo '<td><div class="proc_wrap"><span class="complete">'.$buffer['process_1'].'</span></div></td>';
			}
		}
		if($data['use_allow'] == 'true' && $_SYSTEM['permission']['admin'] == 'true') echo '<td>'.($buffer['allow']=='y' ? '승인' : '비승인').'</td>';
		
		if(empty($data['writer_display']) || in_array($data['writer_display'],array('person', 'other_organ'))) {
			$writer = $buffer['reg_name'];
		} else {
			$writer = empty($buffer['depart_name']) ? $buffer['reg_name'] : $buffer['depart_name'];
		}		
		echo '      <td class="center">'.$writer.'</td>';
		if($data['use_reg_date'] != 'false') echo '      <td class="date center">'.$buffer['reg_date'].'</td>';		
		$group_str = '';
		if( $buffer['varchar_1'] == "ALL" ){
			$group_str = '전체';
		}elseif( $buffer['varchar_1'] == "GROUP"  ){
			if( !empty($buffer['varchar_4']) ){
				$group_str = $buffer['varchar_4'];
			}else{
				$group_str = "개인발송";	
			}
			
		}
		echo '      <td class="center">'.$group_str.'</td>';						
		echo '      <td class="visit center">'.(!empty($buffer['varchar_3'])?$buffer['varchar_3'].'건':'').'</td>';
		echo '    </tr>';
	}

	if($iter == 0) echo '<tr><td colspan="'.$colspan.'" class="list_empty">검색내역이 없습니다.</td></tr>';

	echo '  </tbody>';
	echo '</table>';

	if($data['multi_delete'] == 'true') {
	echo '</form>';
	}
	
	if($_SYSTEM['permission']['admin'] == true && $data['multi_delete'] == 'true') echo '<p><a href="#none" id="delete_all"><span class="btn_pack btn_down">선택삭제</span></a></p>';	

	## 페이지 네비게이션 영역
//echo $data['navi_parameter'].'/'.$data['total_count'].'/'.$data['page_scale'].'/'.$data['block_scale'].'/'.$data['page'];
//	$navi_parameter = make_GET_parameter($data['navi_parameter']);
	$page_navi      = page_navigation($data['total_count'], $data['page_scale'], $data['block_scale'], $data['page'], $data['navi_parameter']);
	echo '<!-- page navigation START --><div class="paging">'.$page_navi.'</div><!-- page navigation END -->';

	## 검색 영역
	echo search_box($data['search_type'], $data['search_word'], $data['search_list'], $data['search_parameter']); //검색키가 다르거나 별도로 추가해야할경우..????

	## 버튼 영역.
	$img_url = '/images/common/board/temp';
	$url = $_SERVER['PHP_SELF'];
	$user_info = array();
	$user_info['is_login'] = empty($_SYSTEM['myinfo']['is_login']) ? NULL : $_SYSTEM['myinfo']['is_login'];
	$user_info['user_pin'] = empty($_SYSTEM['myinfo']['my_pin']) ? NULL : $_SYSTEM['myinfo']['my_pin'];
	$data['parameter']['mode'] = NULL;
	
	if($_SYSTEM['module_config']['board_id'] == 'home_error') {
	echo '<div class="board_button"><ul><li><a href="'.$url.'?mode.=write'.(empty($parameter) ? '' : '&amp;'.$parameter).'" id="btn_write">글쓰기</a></li></ul></div>';
	} else {
	$arr_data['use_logoff_write'] = empty($data['use_logoff_write']) ? NULL : $data['use_logoff_write'];
	
	echo print_button('list', $data['permission'], $data['parameter'], $url, $img_url, $user_info, $arr_data);		
	}

	$add_script = NULL;
	if($_SYSTEM['permission']['admin'] == true && $data['multi_delete'] == 'true') {
		$add_script .= '<script name="import_jquery.ready">'.CR;
		$add_script .= '	$("#check_all").click(function(e) {'.CR;
		$add_script .= '		$(".list01 input:checkbox").attr("checked",this.checked);'.CR;
		$add_script .= '	});'.CR;
		$add_script .= '	$("#delete_all").click(function(){'.CR;
		$add_script .= '		$("#form_del").submit();'.CR;
		$add_script .= '	});'.CR;
		$add_script .= '</script>'.CR;
	}
	echo $add_script;
?>
</div>
