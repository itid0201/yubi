
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



<?php
	echo '<p class="hidden">'.$_SYSTEM['menu_info']['title'].' 게시물. 총 '.$data['total_count'].'건, '.$data['total_page'].'페이지 중 '.$data['page'].'페이지 '.$data['count'].'건 입니다.</p>';
	$buffer = array();
	$data['parameter']['mode'] = 'view';
	$view_parameter = make_GET_parameter($data['parameter'], '&amp;', true);

	echo '<div class="photonews_wrap">';
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
					



		
		
					if( $iter < 2 && $data['page']== 1){
						if($buffer['photo_exist'] == 'true') {
							$img =  '<img src="'.$buffer['photo_path'].'539x310/'.$buffer['photo_name'].'" alt="'.htmlspecialchars($buffer['photo_alt']).'" />';
						} else {
							$img =  '<img src="/images/board/photonews_noimage.jpg" alt="이미지가 없습니다." width="539" height="310" />';
							//$img =  '<p class="big_emp_img"></p>';
							//$img =  '';
						}					
						
						if( $iter == 0 ){
							echo ' <ul class="photonews_top group">
								   <li>
										<a href="'.$href_view.'" '.$link_class.'>
											'.$img.'
											<div class="photonews_oppacity">
												<div class="title">
													<span>'.$buffer['reg_date'].'</span><p>'.htmlspecialchars($buffer['title']).'</p>
												</div>
											</div>
									   </a>
									</li> ';
						}else{
							echo '     <li>
											<a href="'.$href_view.'" '.$link_class.'>	
											'.$img.'
												<div class="photonews_oppacity">
													<div class="title">
													   <span>'.$buffer['reg_date'].'</span><p>'.htmlspecialchars($buffer['title']).'</p>
													</div>
												</div>
										   </a>
										</li>
									</ul>	';				
						}
					}else{
						if($buffer['photo_exist'] == 'true') {
							$img =  '<img src="'.$buffer['photo_path'].'240x180/'.$buffer['photo_name'].'" alt="'.htmlspecialchars($buffer['photo_alt']).'" />';
						//	$img =  '<img src="'.$buffer['photo_path'].$data['list_size_width'].'x'.$data['list_size_height'].'/'.$buffer['photo_name'].'" alt="'.htmlspecialchars($buffer['photo_alt']).'" />';
						} else {
							$img =  '<img src="/images/board/photonews_small_noimage.jpg" alt="이미지가 없습니다." />';
							//$img = '<p class="emp_img"></p>';
						}		
						echo '	<div class="photonews_cont"><p class="img"><a href="'.$href_view.'" '.$link_class.'>'.$img.'</a></p>';
						
						echo '	<dl><dt class="title"><span class="span_tit"><a href="'.$href_view.'" '.$link_class.' title="'.htmlspecialchars($buffer['title']).' 에 대한 글내용 보기.">';
				
						echo htmlspecialchars($buffer['title']);
						if($buffer['new_img'] == 'true') echo $new_img;
						echo '</a></span><span class="span_date">'.$buffer['reg_date'].'</span></dt>';
						
						echo '	<dd class="con">'.htmlspecialchars($buffer['contents']).'</dd></dl></div>';
					}

	}
	if($iter == 0) echo '<p class="list_empty">검색내역이 없습니다.</p>';
	echo '</div>';
?>

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