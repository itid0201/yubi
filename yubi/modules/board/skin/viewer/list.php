<?php
/* list skin */
	## 임시 정의
	$lock_img  = '<span class="icon_lock">비공개글</span>';
	$reply_img = '<span class="icon_reply">답변글</span>';
	$file_img  = '<span class="icon_attach">일반파일 첨부</span>';
	$photo_img = '<span class="icon_image">이미지 파일 첨부</span>';
	$movie_img = '<span class="icon_movie">동영상파일 첨부</span>';
	$new_img   = '<span class="icon_new">새로운글</span>';
	$hidden_img = '<span class="icon_hidden">비승인글</span>'; //********************************************************************************* 여기 추가

echo $data['debug']; // only test..

?>

<?php

	## 상단문구 출력
	## 상단문구 출력
	if(!empty($data['list_msg'])) {
		if($data['device']=='mobile') echo '<p>'.stripslashes($data['list_msg']).'</p>';
		else echo '<div class="sub-tit-top"></div><div class="content_top_alert"><div class="alert_content">'.stripslashes($data['list_msg']).'</div></div><div class="sub-tit-bottom"></div>';
	}

	## 카테고리 표시 => 함수로 뺄까? 스킨때문에 고민이 된다.
	if($data['use_category_1'] == 'true') {
		$category_1_list    = unserialize($data['category_1_list']);
		$category_1_all     = unserialize($data['category_1_all']);
		$category_1         = '';
		$category_1_total   = 0;
		$arr_temp           = $data['navi_parameter'];
		$arr_temp['category_1'] = NULL;
		$category_parameter = make_GET_parameter($arr_temp, '&amp;', true);

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

<div class="newspaper_wrap">
<?php

	$buffer = array();
	$data['parameter']['mode'] = 'view';
	$view_parameter = make_GET_parameter($data['parameter'], '&amp;', true);
	for($iter=0 ; $iter<$data['count'] ; $iter++) {
		$buffer = $data['list'][$iter];
		
		$link_class = '';
		if($buffer['is_lock'] == 'true') {
			$link_class .= 'list_lock';
			$href_view = '#none';
		} else {
			$href_view = $_SERVER['PHP_SELF'].'?idx='.$buffer['idx'].(empty($view_parameter) ? '' : '&amp;'.$view_parameter);
		}
		
		if($buffer['is_delete'] == 'true') $link_class .= empty($link_class) ? 'title_delete' : ' title_delete';
		$link_class = empty($link_class) ? '' : 'class="'.$link_class.'"';
		
		
#2018-06-29	황재복 : 변환 안된 것들 확인하고 변환 작업
/*
		$test = file_get_contents('http://bs.mx.co.kr/Viewer/output/'.$buffer['file_list2'][0]['re_name'].'.files/1.png');
		
		if(empty($test)) {
			file_get_contents('http://bs.mx.co.kr/Viewer/'.$buffer['file_list2'][0]['idx']);
		}
*/
#2018-06-29	: 황재복		
		if($buffer['file_exist'] == 'true' && !empty($buffer['file_list2'][0]['re_name']) ) {			
			$img =  '<img src="/Viewer/output/'.$buffer['file_list2'][0]['re_name'].'.files/1.png" alt="'.str_replace('"','',$buffer['file_list2'][0]['original_name']).'" />';
		} else {
			$img =  '<img src="/images/board/no_img_s.jpg" alt="이미지가 없습니다." />';
			$href_ebook = $href_view;
		}
		
		$ex_temp =  explode(".",$buffer['file_list'][0]['original_name']);
		$btn_class = empty($ex_temp[1])?$ex_temp[1].'_down':'';
if( $_SESSION['user_id'] == "jini0808"  ){
//	print_r($ex_temp[1]);	exit;
}
//		$href_ebook = '/ybscript.io/ebook/preview?bookcode='.$buffer['file_list'][0]['ebook_code'];
		$href_down = '/ybscript.io/common/file_download/'.$buffer['idx'].'/'.$buffer['file_list2'][0]['idx'].'/'.rawurlencode($buffer['file_list'][0]['original_name']);
		$href_ebook = "#none";

		//echo '<div class="newspaper'.(($iter%2 == 0)?' mal0':'').'">';
		echo '<div class="newspaper">';
		echo '	  <div class="newspaper_inner">';
		if($data['permission']['admin'] == true) {
		echo '		  <div class="newspaper_img"><a href="/Viewer/'.$buffer['file_list2'][0]['idx'].'"target="_blank"  title="'.htmlspecialchars($buffer['title']).' 문서뷰어 보기(새창)" >'.$img.'</a></div>';
		} else {
		echo '		  <div class="newspaper_img">'.$img.'</div>';				  
		}
		echo '		  <div class="newspaper_cont">';
		if($data['permission']['admin'] == true) {
		echo '			  <div class="newspaper_title"><a href="'.$href_view.'"  title="'.htmlspecialchars($buffer['title']).' 문서뷰어 보기(새창)" ><strong>'.$buffer['title'].'</strong>'.($buffer['new_img']=='true'?'<span class="new">'.$new_img.'</span>':'').'</a></div>';
		} else {
		echo '			  <div class="newspaper_title"><strong>'.$buffer['title'].'</strong>'.($buffer['new_img']=='true'?'<span class="new">'.$new_img.'</span>':'').'</div>';					  
		}
		echo '			  <div class="newspaper_btn">';
		echo '				  <ul> ';
		echo '					  <li class="newsview_btn"><a href="/Viewer/'.$buffer['file_list2'][0]['idx'].'" target="_blank" title="'.htmlspecialchars($buffer['title']).' 문서뷰어 보기(새창)" >미리보기</a></li>';
		if ( !empty($ex_temp[1]) ){
		echo '					  <li class="newsdown_btn '.$btn_class.'"><a href="'.$href_down.'" title="'.htmlspecialchars($buffer['title']).$ex_temp[1].' 다운로드" >'.$ex_temp[1].' 다운로드</a></li>';
		}
		echo '				  </ul>';
		echo '			  </div>';
		echo '		  </div>';
		echo '	  </div>';
		echo '</div>';
	}



	if($iter == 0) echo '<div class="newspaper mal0">검색내역이 없습니다.</div>';

?>
</div>

<?php	
	## 페이지 네비게이션 영역
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
	echo print_button('list', $data['permission'], $data['parameter'], $url, $img_url, $user_info, $data['use_logoff_write']);
?>