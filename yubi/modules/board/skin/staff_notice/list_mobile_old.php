<div class="module_wrap">	<!-- =======컨텐츠는 content_wrap로 감싸고 모듈은 module_wrap 으로 감싸야합니다.=====  -->
<?php
	
	## 목록 리스트 ==========================================================
	$buffer = array();
	$data['parameter']['mode'] = 'view';
	$view_parameter = make_GET_parameter($data['parameter']);
	
	/***********
	** list_data : 실제 데이타 출력되는 목록
	** list_data_paging : 모바일에서만 사용 페이지별 목록	
	** str_list : 데이타별 임시 저장
	***********/
	$list_data = $list_data_paging = $str_list = "";
	for($iter=0 ; $iter<$data['count'] ; $iter++) {
		$buffer = $data['list'][$iter];
		$reply_str='';
		if($buffer['is_top'] != 'true' && $buffer['level'] > 0) {
			 $reply_str = '<span class="icon_reply">'.$reply_img.'</span>';
		}

		$link_class = '';
		if($buffer['is_lock'] == 'true') {
			$link_class .= 'list_lock';
			$href_view = '#';
		} else {
			$href_view = $_SERVER['PHP_SELF'].'?idx='.$buffer['idx'].(empty($view_parameter) ? '' : '&amp;'.$view_parameter)."&amp;btoff=y";
		}
		if(!empty($buffer['link_url']))	$href_view = $buffer['link_url'].'?idx='.$buffer['idx'].(empty($view_parameter) ? '' : '&amp;'.$view_parameter);		
		if($buffer['is_delete'] == 'true') $link_class .= empty($link_class) ? 'title_delete' : ' title_delete';
		$link_class = empty($link_class) ? '' : 'class="'.$link_class.'"';
		
		$class = '';
		$class .= $buffer['level']>0?' re'.$buffer['level']:'';
		##공지일떄와 공지가 아닐때로 구분
		if( $buffer['is_top'] == 'true' ){	// top 설정
			$class .= " notice";
			$class  = (empty($class)?'':'class="'.$class.'"');
			$str_list = '         <li '.$class.'> ';
			$str_list .= '             <a href="'.$href_view.'" '.$link_class.'> '.$reply_str;
			$str_list .= '                 <span>공지</span> ';
			$str_list .= '                 <div class="list_top"> ';
			$str_list .= '                     <p class="title">'.($buffer['new_img'] == 'true' ? '<span class="icon_new">새글</span>' : '').($data['use_category_1']=='true'&&!empty($buffer['category_1'])?'['.$buffer['category_1'].'] ':'').$buffer['title'].($buffer['open'] == 'n' ? '<span class="icon_lock">비공개글</span>' : '').'</p> ';
			$str_list .= '                     <p><span class="date"> '.$buffer['reg_date'].' </span>'.($buffer['lock_img']=='true'?$lock_img:'').'</p> ';
			$str_list .= '                 </div> ';
			##-- 승인 비승인 / process ------------ S
			$str_list .= '			<div class="approval_tit_box"> ';
			if($data['use_allow'] == 'true' && $_SYSTEM['permission']['admin'] == 'true') {
			$str_list .= '				<span class="proc"><span class="'.$allow_class[$buffer['allow']].'"><em>'.($buffer['allow']=='y' ? '승인' : '비승인').'</em></span></span> ';
			}
			if($data['use_process_1'] == 'true') {
			$str_list .= '				<span class="proc"><span class="'.$stat_class[$buffer['process_1']].'"><em>'.$buffer['process_1'].'</em></span></span> ';
			}
			$str_list .= '			</div>				 ';
			##-- 승인 비승인 / process ------------ E
			$str_list .= '                 <div class="list_btm"><p>'.call::strcut( strip_tags($buffer['contents']), 150, '...').'</p></div> ';
			$str_list .= '             </a> ';
			$str_list .= '         </li> ';	
			$list_data .= $str_list;
		}else{
			$class  = (empty($class)?'':'class="'.$class.'"');			
			$str_list = '         <li '.$class.'> ';
			$str_list .= '             <a href="'.$href_view.'" '.$link_class.'> '.$reply_str;
			$str_list .= '                 <div class="list_top"> ';
			$str_list .= '                     <p class="title">'.($buffer['new_img'] == 'true' ? '<span class="icon_new">새글</span>' : '').($data['use_category_1']=='true'&&!empty($buffer['category_1'])?'['.$buffer['category_1'].'] ':'').$buffer['title'].($buffer['open'] == 'n' ? '<span class="icon_lock">비공개글</span>' : '').'</p> ';
			$str_list .= '                 </div> ';
			##-- 승인 비승인 / process ------------ S
			$str_list .= '			<div class="approval_tit_box"> ';
			if($data['use_allow'] == 'true' && $_SYSTEM['permission']['admin'] == 'true') {
			$str_list .= '				<span class="proc"><span class="'.$allow_class[$buffer['allow']].'"><em>'.($buffer['allow']=='y' ? '승인' : '비승인').'</em></span></span> ';
			}
			if($data['use_process_1'] == 'true') {
			$str_list .= '				<span class="proc"><span class="'.$stat_class[$buffer['process_1']].'"><em>'.$buffer['process_1'].'</em></span></span> ';
			}
			$str_list .= '			</div>				 ';
			##-- 승인 비승인 / process ------------ E
			$str_list .= '                 <div class="list_btm"><p>'.call::strcut( strip_tags($buffer['contents']), 150, '...').'</p></div> ';
			$str_list .= '                 <p><span class="date"> '.$buffer['reg_date'].' </span>';
			$str_list .= ($buffer['is_lock'] =='true'?$lock_img:'');
			$str_list .= '					</p> ';
			$str_list .= '             </a> ';
			$str_list .= '         </li> ';
			$list_data .= $str_list;			
			$list_data_paging .= $str_list;
		}
	}
	if($iter == 0) $list_data .= '<li class="no_result">검색결과가 없습니다</li>';	
	## 목록 리스트 ==========================================================	
	
	
	
	
	
	
	## 상단문구 출력
	if(!empty($data['list_msg'])) {
		if($data['device']=='mobile') echo '<div class="top_alert"><div class="alert_content">'.stripslashes($data['list_msg']).'</div></div>';
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
	
	## 목록 출력 =========================================================================
	## 2017-09-21 서희진 : list_mobile 클래스를 꼭 포함하고 있어야함.
	echo ' <div class="list_type_3"><ul class="list_mobile">'.$list_data.'</ul></div>';		
	## 모바일 페이징을 위해서 꼭 필요함 지우지말것.
	if( $sub_mode == 'paging' ){
		ob_clean();		ob_start();
		echo $list_data_paging;
		exit;
	}
	## 목록 출력 =========================================================================
?>  
<?php
	## 페이지 네비게이션 영역
	$navi_parameter = make_GET_parameter($data['navi_parameter']);
	$page_navi      = page_navigation($data['total_count'], $data['page_scale'], $data['block_scale'], $data['page'], $navi_parameter);
	echo '<!-- page navigation START --><div class="btn_more">'.$page_navi.'</div><!-- page navigation END -->';					 
?>	

</div>
<input type="hidden" name="_viewIdx" id="_viewIdx" />
<input type="hidden" name="_viewUrl" id="_viewUrl" />
<div class="layor_view"></div>
<script name="import_jquery.ready">
	$(".layor_view").hide();

	$("#module_content .layor_view").on("click",".btn_close_layor a",function(){
		$(".layor_view").hide();
		$("#wrap_hide").remove();
		$(".layor_view").html('');
		$('.list_mobile li:eq('+$("#_viewIdx").val()+') a ').attr("href",$("#_viewUrl").val());
		return false;
	});
		
		
	// wrap control
	$('.list_mobile').on("click","li",function(){
		if( $(this).hasClass("no_result") ){
		}else{
			var $url = $(this).children("a").attr("href");
			$("#_viewIdx").val($(this).index());
			$("#_viewUrl").val($url);
			
			//console.log(idx);
			if($('#wrap_hide').length==0) {
					$('body').append('<div id="wrap_hide"></div>');
			}
			
			$('#wrap_hide').show(function(){
				$(this).width($(document).width());
				$(this).height($(document).height());
			});
			

			$(".layor_view").load($url + " div.module_wrap");
			$(".layor_view").show();
			$(".layor_view").center();
		}
		return false;		
	});

</script>

<script type="text/javascript">
	/* layer 중간 찾기.*/
	jQuery.fn.center = function () {
		this.css("top", Math.max(0, (($(window).height() - $(this).outerHeight()) / 2) + $(window).scrollTop() - 236) + "px");
		this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) + $(window).scrollLeft() + ($(this).width /2)) + "px");
		return this;
	};	
	
	
	$(window).resize(function(){
		if($('#wrap_hide').is(':visible')) {
			$('#wrap_hide').width($(document).width());
			$('#wrap_hide').height($(document).height());
		}
	});
	
</script>