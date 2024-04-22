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
	
	## ----------- 페이징 : start
	## 페이지 네비게이션 영역
//	$navi_parameter = make_GET_parameter($data['navi_parameter'], '&amp;', true);
	$page_navi      = page_navigation_new($data['total_count'], $data['page_scale'], $data['block_scale'], $data['page'], $data['navi_parameter']);
	$print_table_page 	= '<!-- page navigation START -->'.$page_navi.'<!-- page navigation END -->';
	if( $data['count'] == 0 ){	$print_table_page = ''; }
	## ----------- 페이징 : end

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
		$target = '';
		if($buffer['is_lock'] == 'true') {	//비공개글
			$link_class .= 'list_lock';
			$href_view = '#none';
		} else {
			
			if( !empty($buffer['link_url'] ) ){
				$href_view = $buffer['link_url'];		
				$target = ' target="_blank"';
			}else{
				$href_view = $_SERVER['PHP_SELF'].'?idx='.$buffer['idx'].(empty($view_parameter) ? '' : '&amp;'.$view_parameter);		
				$target = ' target="_self"';
			}
			
			if( $_SYSTEM['permission']['admin'] == 'true'  ){
				$href_view = $_SERVER['PHP_SELF'].'?idx='.$buffer['idx'].(empty($view_parameter) ? '' : '&amp;'.$view_parameter);				
				$target = ' target="_self"';
			}
			
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
		$print_img = !empty($print_img)? $print_img :'';
		$link_class .= $print_img_cnt>0?' icon_0'.$print_img_cnt:'';
		

		if( $buffer['photo_exist'] == 'true') {
			##2022.08.03 최무성 여수 접근성 작업, a 안에 제목, 내용 있으면 이미지 알트는 ""
			//$img =  '<div class="thumb_box"><img src="'.$buffer['photo_path'].$data['banner_size_width'].'x'.$data['banner_size_height'].'/'.$buffer['photo_name'].'" alt="'.htmlspecialchars($buffer['photo_alt']).'" /></div>';
			$img =  '<div class="thumb_box"><img src="'.$buffer['photo_path'].$data['banner_size_width'].'x'.$data['banner_size_height'].'/'.$buffer['photo_name'].'" alt="" /></div>';
			$link_class .= " hasthumb";
		} else {
			$img =  '';
		}			
		
		## 제목 a 태그 클래스 선처리 맨 마지막에 와야함. 
		$link_class = empty($link_class) ? '' : 'class="'.$link_class.'"';						
		##---- class 선처리 -- end		
		
		############################# contetns #############################
		if( $buffer['html_tag'] == "a" ){
			$buffer['contents'] = unserialize(base64_decode($buffer['contents']) );
			#print_r( $buffer['contents'] );

			$contents = $buffer['contents'][0];
		}else{
			$contents = $buffer['contents'] ;
		}
		############################# contetns #############################				
		
		$print_table_tr  .=  '<div class="item '.( $buffer['photo_exist'] == 'false'?'item_img_none':'').'">';
		$print_table_tr  .=  	'<a href="'.$href_view.'" '.$link_class.' title="'.$buffer['title'].' 에 대한 글내용 보기." '.$target.'>';
		$print_table_tr  .=  		'<div class="hover_box"><div class="lt"></div><div class="lr"></div><div class="lb"></div><div class="ll"></div></div>';
		$print_table_tr  .= $img;
		$print_table_tr  .=  		'<div class="cont_box">';
		if($data['banner_allow_schedule']=='true') {
		$print_table_tr  .=  			'<span class="date">'.(($buffer['top_start']>0)?date('Y-m-d',strtotime($buffer['top_start'])):'0000-00-00').' ~ '.(($buffer['top_end']>0)?date('Y-m-d',strtotime($buffer['top_end'])):'0000-00-00').'</span>';	
		}		
		$print_table_tr  .=  			'<h3>'.$buffer['title'].$print_img.'</h3>';
		$print_table_tr  .=  		'</div>';
		$print_table_tr  .=  	'</a>';
		$print_table_tr  .=  '</div>';
		
	}	

	if( empty($print_table_tr) ){
		$print_table_tr .= '<div class="item no_data"><div class="no_result"><span class="icon"></span>검색내역이 없습니다.</div></div>';
	}


	## ================================================================================================	
	## table 출력 
	## ================================================================================================	

	$print_table  = '';	
	$print_table .=  '<div class="board_list">';
	$print_table .=  	'<div class="board_popup">';
	$print_table .=  		'<div class="ing'.($data['sub_mode']=="ing"?' on':'').'">';	
	$print_table .=  			'<a href="#none" class="category_tab_btn" data-type="ing" >진행중'.($data['sub_mode']=="ing"?'<span class="icon">선택됨</span>':'').'</a>';
	$print_table .=  			'<div class="tab_cont_wrap'.($_SYSTEM['permission']['admin'] == "true"?' change_popup_box':'').'">';
	$print_table .=  				'<div class="tab_list popupbox'.($data['sub_mode']=="ing"?' on':'').'">';
	if($_SYSTEM['permission']['admin'] == 'true') {
	$print_table .=  					'<a href="#none" class="tab_btn"  data-type="ing"><span class="icon"></span>목록보기<span class="text_hidden">선택됨</span></a>';
	}	
	$print_table .=  					'<div class="list_wrap popup_list">';
	$print_table .= $print_table_tr;
	$print_table .=  					'</div>';
	$print_table .= $print_table_page;	
	$print_table .=  				'</div>';
	if($_SYSTEM['permission']['admin'] == 'true') {
	$print_table .=  				'<div class="tab_list changebox'.($data['sub_mode']=="sort"?' on':'').'">';
	$print_table .=  					'<a href="#none" class="tab_btn"  data-type="sort" ><span class="icon"></span>순서변경</a>';
	$print_table .=  					'<div class="list_wrap change_list">';
	$print_table .=  					'</div>';
	$print_table .=  				'</div>';
	}
	$print_table .=  			'</div>';
	$print_table .=  		'</div>';

	if( $_SYSTEM['permission']['admin'] == 'true' ){
	$print_table .=  		'<div class="end'.($data['sub_mode']=="end"?' on':'').'">';
	$print_table .=  			'<a href="#none" class="category_tab_btn"  data-type="end" >종료'.($data['sub_mode']=="end"?'<span class="icon">선택됨</span>':'').'</a>	';
	$print_table .=  			'<div class="tab_cont_wrap">';
	$print_table .=  				'<div class="list_wrap popup_list">';
	$print_table .= $print_table_tr;
	$print_table .=  				'</div>';
	$print_table .= $print_table_page;	
	$print_table .=  			'</div>';
	$print_table .=  		'</div>';
	$print_table .=  		'<div class="wait'.($data['sub_mode']=="wait"?' on':'').'">';
	$print_table .=  			'<a href="#none" class="category_tab_btn"  data-type="wait" >대기'.($data['sub_mode']=="end"?'<span class="icon">선택됨</span>':'').'</a>	';
	$print_table .=  			'<div class="tab_cont_wrap">';
	$print_table .=  				'<div class="list_wrap popup_list">';
	$print_table .= $print_table_tr;
	$print_table .=  				'</div>';
	$print_table .= $print_table_page;	
	$print_table .=  			'</div>';
	$print_table .=  		'</div>';
	$print_table .=  		'<div class="all'.($data['sub_mode']=="all"?' on':'').'">';
	$print_table .=  			'<a href="#none" class="category_tab_btn"  data-type="all" >전체'.($data['sub_mode']=="all"?'<span class="icon">선택됨</span>':'').'</a>	';
	$print_table .=  			'<div class="tab_cont_wrap">';
	$print_table .=  				'<div class="list_wrap popup_list">';
	$print_table .= $print_table_tr;
	$print_table .=  				'</div>';
	$print_table .= $print_table_page;	
	$print_table .=  			'</div>';
	$print_table .= 		'</div>	';
	}

	$print_table .= 	'</div>';
	$print_table .= '</div>';	
	## ================================================================================================	
?>

<div class="board_wrapper">
	<div class="module_list_box">
		<?php 
		## 상단문구 출력
		if(!empty($data['list_msg'])) {
			if($data['device']=='mobile') echo '<p>'.stripslashes($data['list_msg']).'</p>';
			elseif($_SYSTEM['module_config']['board_id'] == 'leader_prop_status') echo stripslashes($data['list_msg']); ## 군수실 공약추진현황 게시판 상단문구 전용
			else echo '<div class="sub-tit-top"></div><div class="content_top_alert"><div class="alert_content">'.stripslashes($data['list_msg']).'</div></div><div class="sub-tit-bottom"></div>';
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
			echo print_hot_new($data['hot_articles_data']); 	
		}		
		## -----------  인기글 : end
		?>
		<div class="board_list_box">
			<?php 
			
			## -----------  검색 영역 : start
			echo search_box_new($data['total_count'], $data['search_type'], $data['search_word'], $data['search_list'], $data['page_scale_search'], $data['page_scale'], $data['start_date'], $data['finish_date'], $data['search_parameter']); 
			## -----------  검색 영역 : end
			## ----------- 목록 : start
			echo $print_table; 
			## ----------- 목록 : end
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
	$data['parameter']['sub_mode'] = NULL;	

	$arr_data['use_logoff_write'] = empty($data['use_logoff_write']) ? NULL : $data['use_logoff_write'];
	
	## --------------------- 버튼 : start
	$print_button =  print_button_new('list', $data['permission'], $data['parameter'], $url, $img_url, $user_info, $arr_data);
	if( !empty($print_button ) ){
	echo '<!-- 버튼 START --><div class="board_btn_box align_center">'.$print_button.'</div><!-- 버튼 END -->';	
	}
	## --------------------- 버튼 : end
	?>	
</div>

<script src="/js/jquery/jquery-oLoader-v0.1/js/jquery.oLoader.js"></script>		
<script src="/js/jquery/datetimepicker/jquery.datetimepicker.js"></script>
<link rel="stylesheet" type="text/css" href="/js/jquery/datetimepicker/jquery.datetimepicker.css">
<script src="<?php echo $_SERVER['SELF']; ?>js/list_popup.js" ></script>
<script>
$(function(){
	var img_size = "<?php echo $data['banner_size_width'].'x'.$data['banner_size_height'] ?>";
	var img_path = "<?php echo $_SERVER['PHP_SELF'].'/ybmodule.file'.$_SYSTEM['module_config']['path'].'/'.$_SYSTEM['module_config']['board_id'].'/' ;?>";		
	

	$(document).find(".on > .tab_cont_wrap").show("slide",{direction: "left"},500, function(){ $(document).find("div.popup_list").css({"display":"inline-block"}); });
	
	$(".module_list_box .board_popup > div > .tab_cont_wrap:has('.changebox')").addClass("change_popup_box");
	
	/*if($(".module_list_box .board_popup > div.on").find(".tab_cont_wrap .tab_list").hasClass("changebox")){
		$(".module_list_box .board_popup > div").addClass("change_popup_box");
	}*/
	
	
	/* 진행중 상태 탭버튼*/
	$(".module_list_box .board_popup > div > a.category_tab_btn").on("click", function(event){
		var type = $(this).data("type");
		var $wrapObj = $("div.board_popup");		
		var $obj = $(this).siblings("div.tab_cont_wrap").find("div.popup_list");
		var $this = $(this);
		
		if( type == "ing" ){
			$(".module_list_box .board_popup > div > .tab_cont_wrap .tab_list").removeClass("on").children(".list_wrap").hide();
			$(".module_list_box .board_popup > div > .tab_cont_wrap .tab_list.popupbox").addClass("on").children(".list_wrap").show();
		}
		
		if($(".module_list_box .board_popup > div.on").find(".tab_cont_wrap .tab_list").hasClass("changebox")){
			$(".module_list_box .board_popup > div").addClass("change_popup_box");
		}
		
		$("#board_sch1 input[name='sub_mode']").val(type);
		
		$wrapObj.oLoader();	
		get_list_data(type, $(this), $obj, $wrapObj, img_path, img_size, function(e){			
			
			
			$(".module_list_box .board_popup > div > a.category_tab_btn span.icon").remove();			
			$this.append('<span class="icon">선택됨</span>');
			if($this.parent().hasClass("on") == false){
				$(".module_list_box .board_popup > div").removeClass("on").children(".tab_cont_wrap").hide();
				/* 페이징 숨김*/
				if( $this.find("div.tab_list").hasClass("popupbox") == true){					
					$this.find("div.tab_list").addClass("on").children(".list_wrap").show("slide",{direction: "left"},500, function(){ $(this).find("div.popup_list").css({"display":"inline-block"})});
				}else if( $this.find().hasClass("changebox") == true){
					$this.find("div.tab_list").addClass("on").children(".list_wrap").show("slide",{direction: "right"},500, function(){ $(this).find("div.popup_list").css({"display":"inline-block"})});
				}
				
				$this.parent().addClass("on").children(".tab_cont_wrap").show("slide",{direction: "left"},500, function(){ $(this).find("div.popup_list").css({"display":"inline-block"})});
			}				

			$wrapObj.oLoader('hide');			
		} );
		
		
		
	});
	
	/* 순서 설정 탭버튼 */
	$(document).on("click", ".module_list_box .board_popup > div > .tab_cont_wrap .tab_list > .tab_btn", function(){
		var type = $(this).data("type");
		var $wrapObj = $("div.board_popup");	/* 로딩 표시구역 */
		var $obj = $(this).siblings("div.list_wrap");	/* 실제 item이 들어갈 구역 */
		var $this = $(this);
		
		
		$wrapObj.oLoader();	
		get_list_data(type, $(this), $obj, $wrapObj, img_path, img_size, function(e){				
			if(e){
				if( $this.parent().hasClass("on") == false){
					$(".module_list_box .board_popup > div > .tab_cont_wrap .tab_list").removeClass("on").children(".list_wrap").hide();									
					if( $this.parent("div.tab_list").hasClass("popupbox") == true){
						//$this.parent("div.tab_list").addClass("on").children(".list_wrap").show("slide",{direction: "left"},500).css({"display":"inline-block"});
						$this.parent("div.tab_list").addClass("on").children(".list_wrap").show("slide",{direction: "left"},500, function(){ $(this).find("div.popup_list").css({"display":"inline-block"})});
					}else if( $this.parent().hasClass("changebox") == true){
						$this.parent("div.tab_list").addClass("on").children(".list_wrap").show("slide",{direction: "right"},500, function(){ $(this).find("div.popup_list").css({"display":"inline-block"})});
					}
				}						
			}
			
				
		});
		$wrapObj.oLoader('hide');		
		
	});
	
	/*박스 호버*/
	$(document).on("mouseenter", ".module_list_box .board_popup .popup_list .item a", function(){
		$(this).parent().addClass("hover");
	}).on("mouseleave", ".module_list_box .board_popup .popup_list .item a", function(){
		$(this).parent().removeClass("hover");
	});

	$(document).on("mouseover", ".module_list_box .board_popup .change_list .item", function(){
		$(this).addClass("hover");
	}).on("mouseleave", ".module_list_box .board_popup .change_list .item", function(){
		$(this).removeClass("hover");
	});
	
	$(document).on("focus", ".module_list_box .board_popup .change_list .item .cont_box .left_box .check_box input", function(){
		$(this).parents(".item").addClass("hover");
	}).on("focusout", ".module_list_box .board_popup .change_list .item .cont_box .left_box .check_box input", function(){
		$(this).parents(".item").removeClass("hover");
	});	

});
</script>

