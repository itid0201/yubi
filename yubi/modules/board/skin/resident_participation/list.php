<?php
/* list skin */
$lock_img = '<span class="icon_lock">비공개글</span>';
$reply_img = '<span class="icon_reply">답변글</span>';
$new_img = '<span class="icon_new">새로운글</span>';
//	$file_img  = '<span class="icon_attach">일반파일 첨부</span>';
//	$photo_img = '<span class="icon_attach">이미지 파일 첨부</span>';
//	$movie_img = '<span class="icon_attach">동영상파일 첨부</span>';
//	$hidden_img = '<span class="icon_hidden">비승인글</span>'; 	
echo $data['debug']; // only test..

$colspan = 6;

$arr_boardname = array();
$arr_boardname['www_proposal'] = '건의질의민원';
$arr_boardname['www_distress'] = '서비스불편민원';
$arr_boardname['www_autonomy'] = '공단자율운영신고창구';
$arr_boardname['www_subcontract'] = '하도급부조리신고';
$arr_boardname['www_human_rights'] = '성희롱등인권침해고충상담';

$arr_url = array();
$arr_url['www_proposal'] = '/www/customer/opinion/proposal';
$arr_url['www_distress'] = '/www/customer/opinion/distress';
$arr_url['www_autonomy'] = '/www/complaint/autonomy';
$arr_url['www_subcontract'] = '/www/complaint/subcontract';
$arr_url['www_human_rights'] = '/www/complaint/human_rights';

$stat_str = array(
  '1' => '민원신청',
  '2' => '민원접수',
  '3' => '부서지정',
  '4' => '담당지정',
  '5' => '결재진행',
  '6' => '처리완료',
  '7' => '처리완료');
$stat_class = array(
  '1' => 'btn_round_green',
  '2' => 'btn_round_blue',
  '3' => 'btn_round_blue',
  '4' => 'btn_round_red',
  '5' => 'btn_round_blue',
  '6' => 'btn_round_black',
  '7' => 'btn_round_black');

## 카테고리 사용시 필드 추가
if ($data['use_category_1'] == 'true') {
  $colspan++;
}

## 프로세서 사용시 필드 추가
if ($data['use_process_1'] == 'true') {
  $colspan++;
}

## 공개비공개 사용시 필드 추가
if ($data['use_allow'] == 'true' && $_SYSTEM['permission']['admin'] == 'true') {
  $colspan++;
}

## 목록 파일 다운로드
if ($data['use_list_attach'] == 'true') {
  $colspan++;
}

## 등록자 표시 부분 열라 급조한것이다. 오픈이 없어서.. 나중에 다른 방식으로 수정해야한다.
if ($data['writer_display'] == 'department') $writer_name = '등록부서';
else if ($data['writer_display'] == 'other_organ') $writer_name = '기관/부서명';
else if ($data['writer_display'] == 'processing') $writer_name = '처리부서';
else $writer_name = '등록자';

## ================================================================================================
## 목록 출력..
## ================================================================================================
$buffer = array();
$data['parameter']['mode'] = 'view';
$view_parameter = make_GET_parameter($data['parameter'], '&amp;', true);
$print_table_tr = '';

for ($iter = 0; $iter < $data['count']; $iter++) {
  $buffer = $data['list'][$iter];

  ##---- class 선처리 -- start
  ## 작성자명 설정.
  if (empty($data['writer_display']) || in_array($data['writer_display'], array('person', 'other_organ'))) {
    $writer = $buffer['reg_name'];
  } else {
    $writer = empty($buffer['depart_name']) ? $buffer['reg_name'] : $buffer['depart_name'];
  }

  $link_class = 'basic_cont ';

  if ($buffer['is_lock'] == 'true' && ($_SESSION['user_id'] != $buffer['approval_id'])) {  //비공개글
    $link_class .= 'list_lock';
    $href_view = '#none';
  } else {
    if ($_SYSTEM['module_config']['board_id'] == "www_singo") {
      $href_view = $arr_url[$buffer['board_id']] . '?idx=' . $buffer['idx'] . (empty($view_parameter) ? '' : '&amp;' . $view_parameter);
    } else {
      $href_view = $_SERVER['PHP_SELF'] . '?idx=' . $buffer['idx'] . (empty($view_parameter) ? '' : '&amp;' . $view_parameter);
    }
  }

  ## 삭제글표시
  if ($buffer['is_delete'] == 'true') $link_class .= empty($link_class) ? 'del' : ' del';

  ## 제목 옆에 붙는 아이콘
  $print_img = '';
  $print_img_cnt = 0;

  if ($buffer['lock_img'] == 'true') {    ## 공개/비공개
    $print_img .= $lock_img;
    $print_img_cnt++;
  }
  if ($buffer['allow'] == 'n' && $_SYSTEM['permission']['admin'] == 'true') {    ## 승인/비승인
    $print_img .= $hidden_img;
    $print_img_cnt++;
  }
  if ($buffer['new_img'] == 'true') {    ##새글
    $print_img .= $new_img;
    $print_img_cnt++;
  }
  //if($buffer['comment_cnt'] > 0) print_img .= '<span class="comment_cnt">'.$buffer['comment_cnt'].'</span>'; //댓글개수 수정 : 오경우(20120125)
  if ($buffer['reply_cnt'] > 0) {    ## 답글 처리
    $print_img .= $reply_img;
    $print_img_cnt++;
  }
  $print_img = !empty($print_img) ? '<div class="icon_box">' . $print_img . '</div>' : '';
  $link_class .= $print_img_cnt > 0 ? ' icon_0' . $print_img_cnt : '';


  ## 제목 a 태그 클래스 선처리 맨 마지막에 와야함.
  $link_class = empty($link_class) ? '' : 'class="' . $link_class . '"';
  ##---- class 선처리 -- end

  $print_table_tr .= '    						<tr' . ($buffer['is_top'] == 'true' ? ' class="notice"' : '') . '>';
  $print_table_tr .= '      							<td' . ($buffer['is_top'] == 'true' ? ' class="notice_icon"' : '') . '>';
  if ($_SYSTEM['permission']['admin'] == true && $data['multi_delete'] == 'true')
    $print_table_tr .= '<div class="list_checkbox"><input type="checkbox" name="check_idx[]" id="check_idx_' . $buffer['idx'] . '" value="' . $buffer['idx'] . '" /><label for="check_idx_' . $buffer['idx'] . '">게시글 선택</label></div>';
  $print_table_tr .= ($buffer['is_top'] == 'true' ? '<span class="notice_icon">공지글</span>' : $buffer['list_num']);
  $print_table_tr .= '</td>';
  if ($_SYSTEM['module_config']['board_id'] == "www_singo") $print_table_tr .= '<td class="board_name">' . $arr_boardname[$buffer['board_id']] . '</td>';
  if ($data['use_category_1'] == 'true') $print_table_tr .= '<td class="list_cate">' . $buffer['category_1'] . '</td>';
  $print_table_tr .= '								<td class="align_left">';
  $print_table_tr .= '									<a href="' . $href_view . '" ' . $link_class . ' title="' . $buffer['title'] . ' 에 대한 글내용 보기." >' . $buffer['title'];
  $print_table_tr .= $print_img;
  $print_table_tr .= '									</a>';
  $print_table_tr .= '								</td>';
  $print_table_tr .= '								<td>' . $writer . '</td>';
  /*if($data['use_process_1'] == 'true') {
    $print_table_tr  .=  '<td>';
    if($buffer['process_1'] == '신청') {
      $print_table_tr  .=  '<div class="proc_wrap"><span class="request">'.$buffer['process_1'].'</span></div>';
    } elseif($buffer['process_1'] == '접수') {
      $print_table_tr  .=  '<div class="proc_wrap"><span class="receipt">'.$buffer['process_1'].'</span></div>';
    } elseif($buffer['process_1'] == '보류') {
      $print_table_tr  .=  '<div class="proc_wrap"><span class="defer">'.$buffer['process_1'].'</span></div>';
    } elseif($buffer['process_1'] == '완료') {
      $print_table_tr  .=  '<div class="proc_wrap"><span class="complete">'.$buffer['process_1'].'</span></div>';
    }
    $print_table_tr  .=  '</td>';
  }*/
  if ($data['use_allow'] == 'true' && $_SYSTEM['permission']['admin'] == 'true')
    $print_table_tr .= '<td>' . ($buffer['allow'] == 'y' ? '승인' : '비승인') . '</td>';

  $print_table_tr .= '								<td>' . $buffer['reg_date'] . '</td>';
  ## mode setting에서 목록 파일 다운로드 기능 true일때만 나타난다. --
  if ($data['use_list_attach'] == 'true') {
    $print_table_tr .= '							<td>';
    //if($buffer['file_exist'] == 'true' || $buffer['photo_exist'] == 'true' || $buffer['movie_exist'] == 'true') {
    $print_table_tr .= download_box_new("list", $buffer['idx'], $buffer['file_list2']);
    //}
    $print_table_tr .= '							</td>';
  }
  ## ---------------------------------------------------------------
//		$print_table_tr  .=  '								<td>'.$buffer['visit_cnt'].'</td>';
  $print_table_tr .= '								<td class="proc"><span class="' . $stat_class[$buffer['process_2']] . '"><em>' . $stat_str[$buffer['process_2']] . '</em></span></td>';
  $depart = explode('|', $buffer['process_3']);
  $depart = end($depart);
  $print_table_tr .= '								<td class="depart">' . $depart . '</td>';
  $print_table_tr .= '							</tr>';
}

if (empty($print_table_tr)) {
  $print_table_tr .= '<tr><td class="data_none" colspan="6"><div class="no_result"><span class="icon"></span>검색 내역이 없습니다.</div></td></tr>';
}


## ================================================================================================
## table 출력
## ================================================================================================

$print_table = '';
$print_table .= '						<table class="board_basic">';
$print_table .= '  							<caption>' . $_SYSTEM['menu_info']['title'] . ' 게시물. 총 ' . $data['total_count'] . '건, ' . $data['total_page'] . '페이지 중 ' . $data['page'] . '페이지 ' . $data['count'] . '건 입니다. 본 데이터표는 ' . $colspan . '컬럼, ' . $data['count'] . '로우로 구성되어 있습니다. 각 로우는 번호, 게시판,' . ($data['use_category_1'] == 'true' ? '분류, ' : '') . ' 제목, ' . $writer_name . ', 등록일, 상태, 처리부서로 구성되어 있습니다.</caption>';
$print_table .= '							<thead>';
$print_table .= '								<tr>';
$print_table .= '									<th scope="col" class="th_100px" >번호</th>';
if ($_SYSTEM['module_config']['board_id'] == "www_singo") $print_table .= '<th scope="col" class="th_150px" >게시판</th>';
//if($_SYSTEM['permission']['admin'] == true && $data['multi_delete'] == 'true') 			$print_table .=  '	<th scope="col"><input type="checkbox" name="check_all" id="check_all" /></th>';
if ($data['use_category_1'] == 'true') $print_table .= '	<th scope="col" class="th_100px">분류</th>';
$print_table .= '									<th scope="col">제목</th>';
$print_table .= '									<th scope="col" class="th_100px">' . $writer_name . '</th>';
//if($data['use_process_1'] == 'true') 			$print_table .=  '	<th scope="col" class="th_100px">처리상태</th>';
if ($data['use_allow'] == 'true' && $_SYSTEM['permission']['admin'] == 'true') $print_table .= '	<th scope="col">승인상태</th>';
$print_table .= '									<th scope="col" class="th_120px">등록일</th>';
if ($data['use_list_attach'] == 'true') $print_table .= '			<th scope="col" class="th_100px">첨부파일</th>';
//	$print_table .=  '									<th scope="col" class="th_70px">조회수</th>';	
$print_table .= '									<th scope="col" class="th_100px">상태</th>';
$print_table .= '									<th scope="col" class="th_140px">처리부서</th>';
$print_table .= '								</tr>';
$print_table .= '							</thead>';
$print_table .= '							<tbody>';
$print_table .= $print_table_tr;
$print_table .= '							</tbody>';
$print_table .= '						</table>';
## ================================================================================================
?>

<div class="board_wrapper">
  <div class="module_list_box">
    <?php

    ## 상단문구 출력
    if (!empty($data['list_msg'])) {
      if ($data['device'] == 'mobile') echo '<p>' . stripslashes($data['list_msg']) . '</p>';
      elseif ($_SYSTEM['module_config']['board_id'] == 'leader_prop_status') echo stripslashes($data['list_msg']); ## 군수실 공약추진현황 게시판 상단문구 전용
      else echo '<div class="content_top_alert"><div class="alert_content">' . stripslashes($data['list_msg']) . '</div></div>';
    }

    ## 상단문구 출력(스타일 없음))
    if (!empty($data['list_msg_no_css'])) {
      echo '<div class="alert_content_none">' . stripslashes($data['list_msg_no_css']) . '</div>';
    }


    ## 상단문구 출력
    /*		if(!empty($data['list_msg'])) {
          if($data['device']=='mobile') echo '<p>'.stripslashes($data['list_msg']).'</p>';
          elseif($_SYSTEM['module_config']['board_id'] == 'leader_prop_status') echo stripslashes($data['list_msg']); ## 군수실 공약추진현황 게시판 상단문구 전용
          else echo '<div class="sub-tit-top"></div><div class="content_top_alert"><div class="alert_content">'.stripslashes($data['list_msg']).'</div></div><div class="sub-tit-bottom"></div>';
        }*/
    if ($_SYSTEM['hostname'] == 'business') {
      $_GET['mode'] = 'list';
    } else {
      if ($_SYSTEM['module_config']['board_id'] == 'www_singo') {
        $_GET['mode'] = 'list';
      }
      if ($_SYSTEM['module_config']['board_id'] == 'mayor_proposal_hope') {
        $login_text = '<ul class="basic">
			<li class="f_red"><strong>[신고하기] 클릭</strong> -> "휴대폰 본인확인 서비스 또는 소셜로그인" 인증 -> 민원등록</li>
			<li>처리 내용을 확인하기 위해서는 신고하기 시 인증한 방법으로 로그인 한 후 해당 민원창구로 이동하여 
				"<strong>내가쓴글보기</strong>"를 클릭하시면 등록하신 목록을 보실수 있습니다.</li>
			</ul>';
      } elseif ($_SYSTEM['module_config']['board_id'] != 'www_inconvenience' && $_SYSTEM['module_config']['board_id'] != 'www_reaulatory_reform' && $_SYSTEM['module_config']['board_id'] != 'www_poor' && $_SYSTEM['module_config']['board_id'] != 'www_newspaper' && $_SYSTEM['module_config']['board_id'] != 'www_pollution' && $_SYSTEM['module_config']['board_id'] != 'www_cyber_disaster') {
        $login_text = '<ul class="basic">
			<li class="f_red"><strong>[신고하기] 클릭</strong> -> "휴대폰 본인확인 서비스 또는 소셜로그인" 인증 -> 민원등록</li>
			<li>처리 내용을 확인하기 위해서는 신고하기 시 인증한 방법으로 로그인 한 후 해당 민원창구로 이동하여 
				"<strong>내가쓴글보기</strong>"를 클릭하시면 신고하신 목록을 보실수 있습니다.</li>
			</ul>';
      }

      ## 상단문구 출력
      if ($data['device'] == 'mobile') echo '<p>' . stripslashes($data['list_msg']) . '</p>';
      else {
        echo '<div class="btn_p align_right mab20">';
        if ($_SYSTEM['myinfo']['is_login'] == true) {
          echo '<a href="?mode=write" class="p2 mar5 btn_reportform"><span>제안하기</span></a>';
          echo '<a href="?mode=list" class="p3 btn_mywrite"><span>' . ($_SYSTEM['permission']['admin'] == true ? '제안글 보기' : '내가쓴글보기') . '</span></a>';
        } else {
          echo '<a href="' . $_SYSTEM['rep_login'] . $_SERVER['PHP_SELF'] . '?mode=write" class="p2 mar5 btn_reportform"><span class="btn1hover1"></span><span>제안하기</span></a>';
          echo '<a href="' . $_SYSTEM['rep_login'] . $_SERVER['PHP_SELF'] . '?mode=list" class="p3 btn_mywrite"><span class="btn1hover1"></span><span>' . ($_SYSTEM['permission']['admin'] == true ? '제안글 보기' : '내가쓴글보기') . '</span></a>';
        }
        echo '</div>';
      }
    }
    $mode = $_GET['mode'];
    if (!empty($data['search_type']) || !empty($date['search_word'])) {
      $mode = "list";
    }

    //-- if list
    if ($mode == 'list') {
      ## -----------  카테고리.: start
      if ($data['use_category_1'] == 'true') {
        $category_1_list = unserialize($data['category_1_list']);
        $category_1_all = unserialize($data['category_1_all']);
        $arr_temp = $data['navi_parameter'];
        $arr_temp['category_1'] = NULL;
        //	$category_parameter = make_GET_parameter($arr_temp);
        echo print_category($data['category_1'], $category_1_list, $category_1_all, $arr_temp, $_SERVER['PHP_SELF']);
      }
      ## -----------  카테고리 : end


      ## -----------  인기글 : start
      if ($data['user_list_hot'] == 'true') {
        echo print_hot_new($data['hot_articles_data']);
      }
      ## -----------  인기글 : end
      ?>
      <div class="board_list_box">
        <?php
        ## -----------  검색 영역 : start
        echo search_box_new($data['total_count'], $data['search_type'], $data['search_word'], $data['search_list'], $data['page_scale_search'], $data['page_scale'], $data['start_date'], $data['finish_date'], $data['search_parameter']);
        ## -----------  검색 영역 : end

        echo '		<div class="board_list">';
        ## 폼 써브밋을  ajax으로 변경하고 싶다~~~~ ㅡㅜ
        if ($_SYSTEM['permission']['admin'] == true && $data['multi_delete'] == 'true') {
          echo '		<form action="" method="post" name="form_del" id="form_del">';
          echo '			<input type="hidden" name="mode" value="delete_all" />';
          echo '			<div class="board_manager_btn">
									<a href="#none" class="all_check"><span class="icon"></span>전체선택</a>
									<a href="#none" class="check_delete"><span class="icon"></span>선택삭제</a>
								</div>';
        }

        ## ----------- 목록 : start
        echo $print_table;
        ## ----------- 목록 : end

        ## ----------- 페이징 : start
        ## 페이지 네비게이션 영역
        //echo $data['navi_parameter'].'/'.$data['total_count'].'/'.$data['page_scale'].'/'.$data['block_scale'].'/'.$data['page'];

        //		$navi_parameter = make_GET_parameter($data['navi_parameter']);
        $page_navi = page_navigation_new($data['total_count'], $data['page_scale'], $data['block_scale'], $data['page'], $data['navi_parameter']);
        echo '<!-- page navigation START -->' . $page_navi . '<!-- page navigation END -->';
        ## ----------- 페이징 : end

        if ($_SYSTEM['permission']['admin'] == true && $data['multi_delete'] == 'true') {
          echo '						</form>';
        }
        echo '		</div>';
        ?>
      </div>
      <?php
    }//-- if list
    ?>
    <?php
    //-- if list
    if ($mode == 'list') {
      ## 버튼 영역.
      $img_url = '/images/common/board/temp';
      $url = $_SERVER['PHP_SELF'];
      $user_info = array();
      $user_info['is_login'] = empty($_SYSTEM['myinfo']['is_login']) ? NULL : $_SYSTEM['myinfo']['is_login'];
      $user_info['user_pin'] = empty($_SYSTEM['myinfo']['my_pin']) ? NULL : $_SYSTEM['myinfo']['my_pin'];
      $data['parameter']['mode'] = NULL;

      $arr_data['use_logoff_write'] = empty($data['use_logoff_write']) ? NULL : $data['use_logoff_write'];

      ## --------------------- 버튼 : start
      $print_button = print_button_new('list', $data['permission'], $data['parameter'], $url, $img_url, $user_info, $arr_data);
      if (!empty($print_button)) {
        //echo '<!-- 버튼 START --><div class="board_btn_box align_center">'.$print_button.'</div><!-- 버튼 END -->';
      }
      ## --------------------- 버튼 : end
      ?>

      <script src="/js/jquery/datetimepicker/jquery.datetimepicker.js"></script>
      <link rel="stylesheet" type="text/css" href="/js/jquery/datetimepicker/jquery.datetimepicker.css">
      <script src="<?php echo $_SERVER['SELF']; ?>js/list.js"></script>
      <?php
## 관리자 삭제일떄만 출력되는 javascript----- start
## /modules/보드이름/js/ 로 이동.( 모듈 복사시 모든 스크립를 같이 옮기기 위해.)
      if ($_SYSTEM['permission']['admin'] == true && $data['multi_delete'] == 'true') {
        echo '<script src="' . $_SERVER['SELF'] . 'js/list_admin.js" ></script>';
      }
## 관리자 삭제일떄만 출력되는 javascript----- end
      ?>

      <?php
    } //-- if list
    ?>
  </div>
</div>