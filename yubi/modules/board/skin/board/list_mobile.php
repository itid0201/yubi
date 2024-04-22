<?php	

	## 목록 리스트 ==========================================================
	/***********
	** list_data : 실제 데이타 출력되는 목록
	** list_data_paging : 모바일에서만 사용 페이지별 목록	
	** list_buffer : 데이타별 임시 저장
	***********/	
	/* list skin */
	$lock_img  = '<span class="icon_lock">비공개글</span>';
	$reply_img = '<span class="icon_reply">답변글</span>';
	$new_img   = '<span class="icon_new">새로운글</span>';
	$top_img   = '<span class="icon_notice">공지글</span>';

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
	$original_cnt = 0;

	for($iter=0 ; $iter<$data['count'] ; $iter++) {
		$buffer = $data['list'][$iter];
        $print_modify = '';
		##---- class 선처리 -- start
		## 작성자명 설정.
		if(empty($data['writer_display']) || in_array($data['writer_display'],array('person', 'other_organ'))) {
			$writer = $buffer['reg_name'];
		} else {
			$writer = empty($buffer['depart_name']) ? $buffer['reg_name'] : $buffer['depart_name'];
		}

        ## 2023.03.27 이진주 : 이사장과의 소통 익명 추가
        if($_SYSTEM['module_config']['board_id'] == 'www_hope' && $buffer['anonymous'] == 'y') $writer = '익명';


		
		$link_class = 'basic_cont ';
		
		if($buffer['is_lock'] == 'true') {	//비공개글
			$link_class .= 'list_lock';
			$href_view = '#none';
		} else {
			$href_view = $_SERVER['PHP_SELF'].'?idx='.$buffer['idx'].(empty($view_parameter) ? '' : '&amp;'.$view_parameter);
		}
		
		## 삭제글표시 
		if($buffer['is_delete'] == 'true') $link_class .= empty($link_class) ? 'del' : ' del';

        ## 2023.03.08 이진주 : 수정한 글 표시
        if($_SYSTEM['module_config']['board_id'] == 'www_notice' && $buffer['modify_date'] !== null) {
            $print_modify = '[수정] ';
        }
		
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
		$print_img = !empty($print_img)?$print_img:'';
		$link_class .= $print_img_cnt>0?' icon_0'.$print_img_cnt:'';
		## 제목 a 태그 클래스 선처리 맨 마지막에 와야함. 
		$link_class = empty($link_class) ? '' : 'class="'.$link_class.'"';						
		##---- class 선처리 -- end		
		
		## ================================================================================================	
		##  출력 
		## ================================================================================================		

		//20210114 김다정 나주도시재생 보도자료 reg_date 출력 수정
		
		// tag 사용시 tag_name 출력 
		if($_SYSTEM['module_config']['use_tag'] == 'true')	$tag_span = board_tag($buffer['tag'],$_SYSTEM['menu_info']['_board_id'],$_SYSTEM['module_config']['source_id']);
		$html_process = '';
		if($_SYSTEM['menu_info']['_board_id'] == 'www_smart_manage'){						
			if($buffer['process_1'] == '신청') {
				$html_process  .=  '<div class="proc_wrap"><span class="request">'.$buffer['process_1'].'</span></div>';
			} elseif($buffer['process_1'] == '해지') {
				$html_process  .=  '<div class="proc_wrap"><span class="receipt">'.$buffer['process_1'].'</span></div>';
			}
		}else{
			if($buffer['process_1'] == '신청') {
				$html_process  .=  '<div class="proc_wrap"><span class="request">'.$buffer['process_1'].'</span></div>';
			} elseif($buffer['process_1'] == '접수') {
				$html_process  .=  '<div class="proc_wrap"><span class="receipt">'.$buffer['process_1'].'</span></div>';
			} elseif($buffer['process_1'] == '보류') {
				$html_process  .=  '<div class="proc_wrap"><span class="defer">'.$buffer['process_1'].'</span></div>';
			} elseif($buffer['process_1'] == '완료') {
				$html_process  .=  '<div class="proc_wrap"><span class="complete">'.$buffer['process_1'].'</span></div>';
			}
		}
		
		$print_table_tr = '<div class="item">
									<a href="#none" data-idx="'.$buffer['idx'].'" data-link="'.$href_view.'"  title="'.$buffer['title'].'에 대한 글내용 보기."  '.$link_class.'>
										<strong>'.($buffer['is_top']=='true' ? $top_img:'').$tag_span.(($buffer['board_id']=='www_notice' && !empty($buffer['depart_name']))?'['.$buffer['depart_name'].'] ':'').$print_modify.$buffer['title'].$print_img.'</strong>
										<span class="date">'.$buffer['reg_date'].'</span>
										<span class="reg_name">'.$writer.'</span>
										'.$html_process.'
									</a>
							</div>';
		$original_cnt++;
		if($buffer['is_top']=='true') $original_cnt--;
		else $list_data_paging .= $print_table_tr;
		
		$print_table .= $print_table_tr;
		## ================================================================================================			
	}	

	
	if( empty($print_table) && $original_cnt < 1 ){		
		$print_table .= '<div class="no_result"><span class="icon"></span>검색내역이 없습니다.</div>';
	}	
?>
<div class="board_wrapper">
	<div class="module_list_box<?php echo ($data['use_comment'] == 'true'?' use_comment':'')?>">
		<input type="hidden" name="board_id" id="board_id" value="<?php echo $_SYSTEM['module_config']['board_id']; ?>" />
		<?php if($_SYSTEM['module_config']['board_id'] == 'www_situation'){ ?>
		<div class="wrap_pollutant_date"><span class="star_b"></span></div>
		<div class="top_alert">
			<div class="alert_content">
			<ul class="ul_br pollutant">
				<li class="tsp">
					<span class="tit">먼지</span>
					<span class="cont"></span>
					<span class="std">(기준 : 15mg/Sm³)</span>
					<span class="stat"></span>
				</li>
				<li class="sox">
					<span class="tit">황산화물</span>
					<span class="cont"></span>
					<span class="std">(기준 : 20ppm)</span>
					<span class="stat"></span>
				</li>
				<li class="nox">
					<span class="tit">질소산화물</span>
					<span class="cont"></span>
					<span class="std">(기준 : 50ppm)</span>
					<span class="stat"></span>
				</li>
				<li class="hcl">
					<span class="tit">염화수소</span>
					<span class="cont"></span>
					<span class="std">(기준 : 12ppm)</span>
					<span class="stat"></span>
				</li>
				<li class="co">
					<span class="tit">일산화탄소</span>
					<span class="cont"></span>
					<span class="std">(기준 : 50ppm)</span>
					<span class="stat"></span>
				</li>
			</ul>	
		</div>
		</div>
		
		<?php } ?>
		<?php 
		## 상단문구 출력
		if(!empty($data['list_msg'])) {
			//if($data['device']=='mobile') echo '<p>'.stripslashes($data['list_msg']).'</p>';
			if($data['device']=='mobile') echo '<div class="top_alert"><div class="alert_content">'.stripslashes($data['list_msg']).'</div></div>';
			elseif($_SYSTEM['module_config']['board_id'] == 'leader_prop_status') echo stripslashes($data['list_msg']); ## 군수실 공약추진현황 게시판 상단문구 전용
			else echo '<div class="sub-tit-top"></div><div class="content_top_alert"><div class="alert_content">'.stripslashes($data['list_msg']).'</div></div><div class="sub-tit-bottom"></div>';
		}	
			
		## 상단문구 출력(스타일 없음))
		if(!empty($data['list_msg_no_css'])) {
			echo '<div class="alert_content_none">'.stripslashes($data['list_msg_no_css']).'</div>';
		}
		
		## -----------  인기글 : start
		if( $data['user_list_hot'] == 'true' ){
			echo print_hot_new($data['hot_articles_data']); 	
		}		
		## -----------  인기글 : end
		
		## 카테고리 표시 	
		if($data['use_category_1'] == 'true') {
			$category_1_list    = unserialize($data['category_1_list']);
			$category_1_all     = unserialize($data['category_1_all']);		
			echo category_box_mobile($category_1_list, $category_1_all, $data['navi_parameter'],$data['category_1']);		
		}	
		
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
		<div class="board_list_box">
			<?php 
			## -----------  검색 영역 : start
			//echo search_box_new($data['total_count'], $data['search_type'], $data['search_word'], $data['search_list'], $data['page_scale_search'], $data['page_scale'], $data['start_date'], $data['finish_date'], $data['search_parameter']); 
			## -----------  검색 영역 : end
			
			echo '<p class="hidden">'.$_SYSTEM['menu_info']['title'].' 게시물. 총 '.$data['total_count'].'건, '.$data['total_page'].'페이지 중 '.$data['page'].'페이지 '.$data['count'].'건 입니다.</p>';
			## 목록 출력 =========================================================================
			## board_list 클래스를 꼭 포함하고 있어야함.
			echo   '<div class="board_list">';				
			echo $print_table; 
			echo   '</div>';						
			## 모바일 페이징을 위해서 꼭 필요함 지우지말것.
			if( $sub_mode == 'paging' ){
				ob_clean();		ob_start();
				echo $list_data_paging;
				exit;
			}
			## 목록 출력 =========================================================================
			
			## ----------- 페이징 : start
			## 페이지 네비게이션 영역						
		//	$navi_parameter = make_GET_parameter($data['navi_parameter'], '&amp;', true);
			$page_navi      = page_navigation_new($data['total_count'], $data['page_scale'], $data['block_scale'], $data['page'], $data['navi_parameter']);				
			if($data['total_count'] > 0) {
				echo '<!-- page navigation START --><div class="board_btn_box align_center">'.$page_navi.'</div><!-- page navigation END -->';
			}
			## ----------- 페이징 : end			
			?>																											
		</div>
	</div>
</div>
<?php
############## 아래 날짜 부분 나중에는 모듈 설정값으로 뺴내야함 ##########################
?>
<script>
	$(function(){
		var board_id = $(document).find("input#board_id").val();
		if( board_id == "www_situation"){			
			
			let html = '';
			let grade_arr = ["","좋음","보통","나쁨","매우나쁨"];
			let grade_class = ["","good","ord","bad","verybad"];
			
			$.ajax({
				url: "/contents/cleansys/cleansys.json",
				type: "POST",
				dataType: "json",
				success: function(data){
					
					let res = data.response.body.items[0];
					let date = res.mesure_dt;
					let year = date.split('-')[0];
					let month = date.split('-')[1];
					let day = date.split('-')[2];
					let d = day.substring(0,2);
					let hour = day.slice(-5);
					let hr = hour.substring(0,2);
					
					let tsp = res.tsp_mesure_value;
					let sox = res.sox_mesure_value;
					let nox = res.nox_mesure_value;
					let hcl = res.hcl_mesure_value;
					let co = res.co_mesure_value;
					
					let bad = 'bad';
					let bad_html = '나쁨';
					let tsp_grade , sox_grade , nox_grade , hcl_grade , co_grade;
					tsp_grade = sox_grade = nox_grade = hcl_grade = co_grade = 'good';
					let tsp_html , sox_html , nox_html , hcl_html , co_html;
					tsp_html = sox_html = nox_html = hcl_html = co_html = '좋음';
				
					if(tsp > 15) {tsp_grade = bad; tsp_html = bad_html;}
					if(sox > 20) {sox_grade = bad; sox_html = bad_html;}
					if(nox > 50) {nox_grade = bad; nox_html = bad_html;}
					if(hcl > 12) {hcl_grade = bad; hcl_html = bad_html;}
					if(co > 50) {co_grade = bad; co_html = bad_html;}
					
					$(document).find("div.wrap_pollutant_date").append(''+year+' 년 '+month+' 월 '+d+' 일 '+hr+' 시');
					
					$(document).find("li.tsp > span.cont").html(tsp+'mg/Sm³');
					$(document).find("li.tsp > span.stat").addClass(tsp_grade).html(tsp_html);
					$(document).find("li.sox > span.cont").html(sox+'ppm');
					$(document).find("li.sox > span.stat").addClass(sox_grade).html(sox_html);
					$(document).find("li.nox > span.cont").html(nox+'ppm');
					$(document).find("li.nox > span.stat").addClass(nox_grade).html(nox_html);
					$(document).find("li.hcl > span.cont").html(hcl+'ppm');
					$(document).find("li.hcl > span.stat").addClass(hcl_grade).html(hcl_html);
					$(document).find("li.co > span.cont").html(co+'ppm');
					$(document).find("li.co > span.stat").addClass(co_grade).html(co_html);
				}
			});
		}
	});
</script>
<?php
####################################################################################
?>
<script src="/js/jquery/datetimepicker/jquery.datetimepicker.js"></script>
<link rel="stylesheet" type="text/css" href="/js/jquery/datetimepicker/jquery.datetimepicker.css">
<script src="<?php echo $_SERVER['SELF']; ?>js/list_mobile.js?build=<?php echo SERIAL;?>" defer ></script>