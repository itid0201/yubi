<?php

/* list skin */
	## 임시 정의
	$lock_img  = '<span class="icon_lock">비공개글</span>';
	$reply_img = '<span class="icon_reply">[답변글]</span>';
	$file_img  = '<span class="icon_attach">일반파일 첨부</span>';
	$photo_img = '';
	$movie_img = '';
	$new_img   = '<span class="icon_new">새로운글</span>';
	$hidden_img = '<span class="icon_hidden">비승인글</span>'; //********************************************************************************* 여기 추가

echo $data['debug']; // only test..
?>
<?php

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
			if($value == $data['category_1']) $category_1 .= '<li class="on"><span>'.$value.'('.(empty($category_1_list[$value]) ? 0 : $category_1_list[$value]).')'.'</span></li>';
			else $category_1 .= '<li><a href="'.$_SERVER['PHP_SELF'].'?category_1='.rawurlencode($value).(empty($category_parameter) ? '' : '&amp;'.$category_parameter).'"><span>'.$value.'('.(empty($category_1_list[$value]) ? 0 : $category_1_list[$value]).')'.'</span></a></li>';
			$category_1_total += empty($category_1_list[$value]) ? 0 : $category_1_list[$value];
		}
		$category_1_total += $category_1_list[''];
		if(empty($data['category_1'])) $category_1 = '<li  class="on"><span>전체('.$category_1_total.')'.'</span></li>'.$category_1;
		else $category_1 = '<li><a href="'.$_SERVER['PHP_SELF'].(empty($category_parameter) ? '' : '?'.$category_parameter).'"><span>전체('.$category_1_total.')'.'</span></a></li>'.$category_1;
		$category_1 = '<ul class="cate_list">'.$category_1.'</ul>';
		echo $category_1;
	}

?>

<div id="banner">


<?php


	$buffer = array();
	$data['parameter']['mode'] = 'view';
	$view_parameter = make_GET_parameter($data['parameter']);
	for($iter=0 ; $iter<$data['count'] ; $iter++) {
		$buffer = $data['list'][$iter];
		
		$link_class = '';
		if($buffer['is_lock'] == 'true') {
			$link_class .= 'list_lock';
			$href_view = '#';
		} else {
			$href_view = $_SERVER['PHP_SELF'].'?idx='.$buffer['idx'].(empty($view_parameter) ? '' : '&amp;'.$view_parameter);
		}
		if($buffer['is_delete'] == 'true') $link_class .= empty($link_class) ? 'title_delete' : ' title_delete';
		$link_class = empty($link_class) ? '' : 'class="'.$link_class.'"';
		echo '  '; //id="photonews_list_table"
				echo '	<div class="popupzone_type"><p class="img"><a href="'.str_replace('&','&amp;',str_replace('&amp;','&',$buffer['link_url'])).'" target="'.$buffer['varchar_2'].'" title="'.$buffer['contents'].'">';
		if($buffer['photo_exist'] == 'true') {
			echo '<img src="'.$buffer['photo_path'].$data['banner_size_width'].'x'.$data['banner_size_height'].'/'.$buffer['photo_name'].'" alt="'.$buffer['photo_alt'].'" />';
		} else {
			//echo '<img src="/images/noimage.gif" alt="이미지가 없습니다." />';
			echo '<p class="emp_img"></p>';
		}
		echo '</a></p>';
		echo '	<dl><dt><a href="'.$href_view.'" '.$link_class.'>';

		echo $buffer['title'];
		if($buffer['new_img'] == 'true') echo $new_img;
		//echo '</a></span><span class="span_date">'.$buffer['reg_date'].'</span></dt>';
		echo '</a></dt>';

		
		echo '	<dd>'.$buffer['contents'];
//		if($data['banner_allow_schedule']=='true') echo '<br /><span class="term"> 노출기간'.substr($buffer['top_start'],0,17).' ~ '.substr($buffer['top_end'],0,17).'</span>';
		if($data['banner_allow_schedule']=='true') echo '<br /><span class="term"> 노출기간 : '.(($buffer['top_start']>0)?date('Y-m-d',strtotime($buffer['top_start'])):'0000-00-00').' ~ '.(($buffer['top_end']>0)?date('Y-m-d',strtotime($buffer['top_end'])):'0000-00-00').'</span>';
		echo '</dd>';
		
		echo '	<dd>URL  : '.str_replace('&','&amp;',$buffer['link_url']);
		if($data['banner_allow_schedule']=='true');
		echo '</dd>';
		
		echo '</dl></div>';
	}
	if($iter == 0) echo '<dt class="list_empty">검색내역이 없습니다.</dt>';


?>

</div>
<?php	
	## 페이지 네비게이션 영역
	$navi_parameter = make_GET_parameter($data['navi_parameter']);
	$page_navi      = page_navigation($data['total_count'], $data['page_scale'], $data['block_scale'], $data['page'], $navi_parameter);
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
	echo print_button('list', $data['permission'], $data['parameter'], $url, $img_url, $user_info, $data['use_logoff_write']);
?>