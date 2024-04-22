
<?php

	## 목록 리스트 ==========================================================
	/***********
	** list_data : 실제 데이타 출력되는 목록
	** list_data_paging : 모바일에서만 사용 페이지별 목록	
	** list_buffer : 데이타별 임시 저장
	***********/	
	$buffer = array();
	$data['parameter']['mode'] = 'view';
	$view_parameter = make_GET_parameter($data['parameter']);
	

	
	$img_path = str_replace('/modules/','/modules/_data/',$data['module_path']).$_SYSTEM['module_config']['board_id'].'/';
		
	$str_list = '';
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

		## 이미지 불러오기
		$img_list = array();
		foreach($buffer['photo_list'] as $item) {
			$img_list[] = array('file_name'=>$item['photo_name'],'alt'=>$item['photo_alt'],'idx'=>$buffer['idx']);
		}

//print_r($buffer['photo_list']);exit;
		$img = $module->get_munti_thumb($width,$height,$margin,$img_list,$buffer['photo_path'],$href_view,$buffer['photo_cnt']);

		$img_cnt = count($img_list);
		unset($img_list);
		if( $img_cnt < 1 ){
			$img  = '<div class="grid_img thumb1">';
			$img .= '	<div class="item">';
			$img .= '		<a href="#none" title="등록된 이미지 없음">';
			$img .= '		<img src="/images/board/social_noimage.jpg" title="등록된 이미지 없음" width="367" height="182" />';
			$img .= '		</a>';
			$img .= '	</div>';
			$img .= '</div>';
		}

		##키워드 가져오기 
		$keyword_list = $buffer['keyword'];			

		$str_list = '			<div class="grid_item'.($buffer['is_lock']=='true'?' closed':'').'">';
		$str_list .= '				<div class="title">';
		$str_list .= '					<a href="'.$href_view.'" '.$link_class.' title="'.$buffer['title'].' 에 대한 글내용 보기." class="title_cont">';
		$str_list .= '					<h3>'.($buffer['is_lock']=='true'?'<span class="icon_lock">비공개</span>':'').$buffer['title'].'</h3>';
		$str_list .= ($buffer['new_img']=='true'?'<span class="new">새글</span>':'');
		$str_list .= '					</a>';
		//$str_list .= ($buffer['is_lock']=='true'?'<span class="icon_lock">비공개</span>':'');
		$str_list .= '				</div>';
		$str_list .= $img;		
		$str_list .= '				<div class="grid_cont">';
		$str_list .= '					<p class="name"><span class="icon"></span>'.$buffer['reg_name'].'</p>';
		$str_list .= '					<p class="date"><span class="icon"></span>'.date("Y.m.d",strtotime($buffer['reg_date'])).'</p>';
		$str_list .= '					<div class="contents_wrap">';
		$str_list .= '					<span class="close">'.$buffer['contents'].'</span>';
		$str_list .= '					</div>';
		$str_list .= '				</div>';
		$str_list .= '			</div>	';

		$list_data .= $str_list;			
		$list_data_paging .= $str_list;				
	
	}
	if($iter == 0) echo '<li class="no_result">검색내역이 없습니다.</li>';
	## 목록 리스트 ==========================================================	




/* list skin */
	## 임시 정의
	$new_img   = '<span class="icon_new1">새로운글</span>';//********************************************************************************* 여기 추가
?>

<?php
	## 상단문구 출력
	if(!empty($data['list_msg'])) {
		if($data['device']=='mobile') echo '<div class="top_alert">	<h3 class="c0">알립니다 !</h3><div class="alert_content">'.stripslashes($data['list_msg']).'</div></div>';
		else echo '	<div class="board_guide guide_img1">'.stripslashes($data['list_msg']).'</div>';
	}	

	## 버튼 영역.
	$img_url = '/images/common/board/temp';
	$url = $_SERVER['PHP_SELF'];
	$user_info = array();
	$user_info['is_login'] = empty($_SYSTEM['myinfo']['is_login']) ? NULL : $_SYSTEM['myinfo']['is_login'];
	$user_info['user_pin'] = empty($_SYSTEM['myinfo']['my_pin']) ? NULL : $_SYSTEM['myinfo']['my_pin'];
	$data['parameter']['mode'] = NULL;
	echo print_button_mobile('list', $data['permission'], $data['parameter'], $url, $img_url, $user_info);
	
	## 검색 영역
	echo search_box_mobile($data['search_type'], $data['search_word'], $data['search_list'], $data['search_parameter'], $data['total_count'] ); 

	## 카테고리 표시 	
	if($data['use_category_1'] == 'true') {
		$category_1_list    = unserialize($data['category_1_list']);
		$category_1_all     = unserialize($data['category_1_all']);		
		echo category_box_mobile($category_1_list, $category_1_all, $data['navi_parameter'],$data['category_1']);		
	}	


?>
<div class="social_gallery<?php echo ($_SYSTEM['module_config']['contents_cut'] == 0 || empty($_SYSTEM['module_config']['contents_cut']) )?" one_title":"";?>">
<?php
	echo '<p class="hidden">'.$_SYSTEM['menu_info']['title'].' 게시물. 총 '.$data['total_count'].'건, '.$data['total_page'].'페이지 중 '.$data['page'].'페이지 '.$data['count'].'건 입니다.</p>';
	## 목록 출력 =========================================================================
	## 2017-09-21 서희진 : list_mobile 클래스를 꼭 포함하고 있어야함.
	echo ' <div class="list_mobile">'.$list_data.'</div>';		
	## 모바일 페이징을 위해서 꼭 필요함 지우지말것.
	if( $sub_mode == 'paging' ){
		ob_clean();		ob_start();
		echo $list_data_paging;
		exit;
	}
	## 목록 출력 =========================================================================	

?>
</div>
<?php
	## 페이지 네비게이션 영역
	$navi_parameter = make_GET_parameter($data['navi_parameter']);
	$page_navi      = page_navigation($data['total_count'], $data['page_scale'], $data['block_scale'], $data['page'], $navi_parameter);
	echo '<!-- page navigation START --><div class="btn_more">'.$page_navi.'</div><!-- page navigation END -->';	
?>	



