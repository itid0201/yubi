<?php
/* list skin */
$lock_img = '<span class="icon_lock">비공개글</span>';
$reply_img = '<span class="icon_reply">답변글</span>';
$new_img = '<span class="icon_new">새로운글</span>';
echo $data['debug']; // only test..

$colspan = 5;

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
else if ($data['writer_display'] == 'other_organ') $writer_name = '타기관';
else if ($data['writer_display'] == 'processing') $writer_name = '처리부서';
else if ($data['writer_display'] == 'charge_organ') $writer_name = '담당부서';
else $writer_name = '등록자';


## ================================================================================================
## 목록 출력..
## ================================================================================================
$buffer = array();
$data['parameter']['mode'] = 'view';
$view_parameter = make_GET_parameter($data['parameter'], '&amp;', true);
$print_table_tr = '';
$original_cnt = 0;


for ($iter = 0; $iter < $data['count']; $iter++) {
  $buffer = $data['list'][$iter];
  $print_modify = '';
  ##---- class 선처리 -- start
  ## 작성자명 설정.
  if (empty($data['writer_display']) || in_array($data['writer_display'], array('person', 'other_organ'))) {
    $writer = $buffer['reg_name'];
  } else {
    $writer = empty($buffer['depart_name']) ? $buffer['reg_name'] : $buffer['depart_name'];
  }

  ## 2023.03.27 이진주 : 이사장과의 소통 익명 추가
  if ($_SYSTEM['module_config']['board_id'] == 'www_hope' && $buffer['anonymous'] == 'y') $writer = '익명';

  $link_class = 'basic_cont ';

  if ($buffer['is_lock'] == 'true') {  //비공개글
    $link_class .= 'list_lock';
    $href_view = '#none';
  } else {
    $href_view = $_SERVER['PHP_SELF'] . '?idx=' . $buffer['idx'] . (empty($view_parameter) ? '' : '&amp;' . $view_parameter);
  }

  ## 삭제글표시
  if ($buffer['is_delete'] == 'true') $link_class .= empty($link_class) ? 'del' : ' del';

  ## 2023.03.08 이진주 : 수정한 글 표시
  if ($_SYSTEM['module_config']['board_id'] == 'www_notice' && $buffer['modify_date'] !== null) {
    $print_modify = '[수정] ';
  }

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


//  if($buffer['comment_cnt'] > 0) $print_img .= '<span class="comment_cnt">'.$buffer['comment_cnt'].'</span>'; //댓글개수 수정 : 오경우(20120125)
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

  if ($data['use_category_1'] == 'true') $print_table_tr .= '<td class="list_cate">' . $buffer['category_1'] . '</td>';
  $print_table_tr .= '								<td class="align_left">';
  // tag 사용시 tag_name 출력
  if ($_SYSTEM['module_config']['use_tag'] == 'true') $print_table_tr .= board_tag($buffer['tag'], $_SYSTEM['menu_info']['_board_id'], $_SYSTEM['module_config']['source_id']);
  $print_table_tr .= '									<a href="' . $href_view . '" ' . $link_class . ' title="' . $buffer['title'] . ' 에 대한 글내용 보기." >' . $print_modify . $buffer['title'];
  $print_table_tr .= $print_img;
  $print_table_tr .= '									</a>';
  $print_table_tr .= '								</td>';
  $print_table_tr .= '								<td>' . $writer . '</td>';
  if ($data['use_process_1'] == 'true') {
    $print_table_tr .= '<td>';
    if ($_SYSTEM['menu_info']['_board_id'] == 'www_smart_manage') {

      if ($buffer['process_1'] == '신청') {
        $print_table_tr .= '<div class="proc_wrap"><span class="request">' . $buffer['process_1'] . '</span></div>';
      } elseif ($buffer['process_1'] == '해지') {
        $print_table_tr .= '<div class="proc_wrap"><span class="receipt">' . $buffer['process_1'] . '</span></div>';
      }
    } else {
      if ($buffer['process_1'] == '신청') {
        $print_table_tr .= '<div class="proc_wrap"><span class="request">' . $buffer['process_1'] . '</span></div>';
      } elseif ($buffer['process_1'] == '접수') {
        $print_table_tr .= '<div class="proc_wrap"><span class="receipt">' . $buffer['process_1'] . '</span></div>';
      } elseif ($buffer['process_1'] == '보류') {
        $print_table_tr .= '<div class="proc_wrap"><span class="defer">' . $buffer['process_1'] . '</span></div>';
      } elseif ($buffer['process_1'] == '완료') {
        $print_table_tr .= '<div class="proc_wrap"><span class="complete">' . $buffer['process_1'] . '</span></div>';
      }
    }
    $print_table_tr .= '</td>';

  }
  if ($data['use_allow'] == 'true' && $_SYSTEM['permission']['admin'] == 'true')
    $print_table_tr .= '<td>' . ($buffer['allow'] == 'y' ? '승인' : '비승인') . '</td>';
  //20210114 김다정 나주도시재생 보도자료 출력 수정
  if ($_SYSTEM['module_config']['board_id'] == 'njursc_newsrelease') $release_date = date("Y-m-d", strtotime($buffer['varchar_1']));
  $print_table_tr .= '								<td>' . ($_SYSTEM['module_config']['board_id'] == 'njursc_newsrelease' ? $release_date : $buffer['reg_date']) . '</td>';
  ## mode setting에서 목록 파일 다운로드 기능 true일때만 나타난다. --
  if ($data['use_list_attach'] == 'true') {
    $print_table_tr .= '							<td>';
    //if($buffer['file_exist'] == 'true' || $buffer['photo_exist'] == 'true' || $buffer['movie_exist'] == 'true') {
    $print_table_tr .= download_box_new("list", $buffer['idx'], $buffer['file_list_file']);
    //}
    $print_table_tr .= '							</td>';
  }
  ## ---------------------------------------------------------------
  $print_table_tr .= '								<td>' . $buffer['visit_cnt'] . '</td>';
  $print_table_tr .= '							</tr>';
  $original_cnt++;
  if ($buffer['is_top'] == 'true') $original_cnt--;
}


if (empty($print_table_tr) && $original_cnt < 1) {

  $print_table_tr .= '<tr><td class="data_none" colspan="' . $colspan . '"><div class="no_result"><span class="icon"></span>검색내역이 없습니다.</div></td></tr>';
}


## ================================================================================================
## table 출력
## ================================================================================================

$print_table = '';
$print_table .= '						<table class="board_basic">';
$print_table .= '  							<caption>' . $_SYSTEM['menu_info']['title'] . ' 게시물. 총 ' . $data['total_count'] . '건, ' . $data['total_page'] . '페이지 중 ' . $data['page'] . '페이지 ' . $data['count'] . '건 입니다. 본 데이터표는 ' . $colspan . '컬럼, ' . $data['count'] . '로우로 구성되어 있습니다. 각 로우는 번호, ' . ($data['use_category_1'] == 'true' ? '분류, ' : '') . ' 제목, ' . ($data['use_process_1'] == 'true' ? '처리상태, ' : '') . ' ' . ($data['use_allow'] == 'true' && $_SYSTEM['permission']['admin'] == 'true' ? '승인상태, ' : '') . ($data['use_list_attach'] == 'true' ? '첨부파일,' : '') . $writer_name . ', 등록일, 조회수로 구성되어 있습니다.</caption>';
$print_table .= '							<thead>';
$print_table .= '								<tr>';
$print_table .= '									<th scope="col" class="th_100px" >번호</th>';
//if($_SYSTEM['permission']['admin'] == true && $data['multi_delete'] == 'true') 			$print_table .=  '	<th scope="col"><input type="checkbox" name="check_all" id="check_all" /></th>';
if ($data['use_category_1'] == 'true') $print_table .= '	<th scope="col" class="th_15">분류</th>';
$print_table .= '									<th scope="col">제목</th>';
$print_table .= '									<th scope="col" class="th_200px">' . $writer_name . '</th>';
if ($data['use_process_1'] == 'true') $print_table .= '	<th scope="col" class="th_100px">처리상태</th>';
if ($data['use_allow'] == 'true' && $_SYSTEM['permission']['admin'] == 'true') $print_table .= '	<th scope="col">승인상태</th>';
//20210114 김다정 나주도시재생 보도자료 출력수정
$print_table .= '									<th scope="col" class="th_120px">' . ($_SYSTEM['module_config']['board_id'] == 'njursc_newsrelease' ? '보도일' : '등록일') . '</th>';
if ($data['use_list_attach'] == 'true') $print_table .= '			<th scope="col" class="th_100px">첨부파일</th>';
$print_table .= '									<th scope="col" class="th_70px">조회수</th>';
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
    <input type="hidden" name="board_id" id="board_id" value="<?php echo $_SYSTEM['module_config']['board_id']; ?>"/>
    <?php
    if ($_SYSTEM['module_config']['board_id'] != 'www_hope') {
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
    }
    ## -----------  카테고리.: start
    if ($data['use_category_1'] == 'true' && $mode != "all" && $_SYSTEM['module_config']['use_category_tab'] == "true") {
      $category_1_list = unserialize($data['category_1_list']);
      $category_1_all = unserialize($data['category_1_all']);
      $arr_temp = $data['navi_parameter'];
      $arr_temp['category_1'] = NULL;
      //	$category_parameter = make_GET_parameter($arr_temp, '&amp;', true);
      // 카테고리에 '전체'가 보이지 않도록 하려면 allType 값 수정
      $allType = true;
      echo print_category($data['category_1'], $category_1_list, $category_1_all, $arr_temp, $_SERVER['PHP_SELF'], $allType);
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
      if ($_SERVER['REMOTE_ADDR'] == "49.254.140.140" && $_SESSION['user_id'] == "jini0808") {
//			print_r( $data['search_date'] );
      }

      ## -----------  검색 영역 : start
      echo search_box_new($data['total_count'], $data['search_type'], $data['search_word'], $data['search_list'], $data['page_scale_search'], $data['page_scale'], $data['start_date'], $data['finish_date'], $data['search_parameter'], $data);
      ## -----------  검색 영역 : end

      echo '		<div class="board_list" id="board_list">';
      if (!empty($data['keyword'])) {
        echo '<div class="board_photo">' . tag_box($data['keyword']) . '</div>';
      }

      ## 2021.06.22 서희진 추가 : 엑셀 출력 기능
      if ($_SYSTEM['permission']['admin'] == true && $_SYSTEM['module_config']['use_list_excel'] == 'true') {
        $cate_param = ($data['category_1']) ? '&category_1=' . $data['category_1'] : '';
        echo '<div class="btn_p align_right"><a href="?mode=list&sub_mode=excel' . $cate_param . '" target="_blank" class="p1 mar5">엑셀 내려받기</a></div>';
      }

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
      //	$navi_parameter = make_GET_parameter($data['navi_parameter'], '&amp;', true);
      $page_navi = page_navigation_new($data['total_count'], $data['page_scale'], $data['block_scale'], $data['page'], $data['navi_parameter']);
      echo '<!-- page navigation START -->' . $page_navi . '<!-- page navigation END -->';
      ## ----------- 페이징 : end

      if ($_SYSTEM['permission']['admin'] == true && $data['multi_delete'] == 'true') {
        echo '						</form>';
      }
      echo '		</div>';
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

  $arr_data['use_logoff_write'] = empty($data['use_logoff_write']) ? NULL : $data['use_logoff_write'];

  ## --------------------- 버튼 : start
  //$print_button =  print_button_new('list', $data['permission'], $data['parameter'], $url, $img_url, $user_info, $arr_data['use_logoff_write']);
  if ($_SERVER['REMOTE_ADDR'] == "49.254.140.140") {
    //print_r($data['permission']);exit;
  }

  $print_button = print_button_new('list', $data['permission'], $data['parameter'], $url, $img_url, $user_info, $arr_data);
  if (!empty($print_button)) {
    echo '<!-- 버튼 START --><div class="board_btn_box align_center">' . $print_button . '</div><!-- 버튼 END -->';
  }
  ## --------------------- 버튼 : end
  ?>
</div>
<?php
############## 아래 날짜 부분 나중에는 모듈 설정값으로 뺴내야함 ##########################
?>
<script>
  $(function () {
    let board_id = $(document).find("input#board_id").val();
    if (board_id == "www_situation") {

      //let html = '';
      //let grade_arr = ["","좋음","보통","나쁨","매우나쁨"];
      //let grade_class = ["","good","ord","bad","verybad"];

      $.ajax({
        url: "/contents/cleansys/cleansys.json",
        type: "POST",
        dataType: "json",
        success: function (data) {

          let res = data.response.body.items[0];
          let date = res.mesure_dt;
          let year = date.split('-')[0];
          let month = date.split('-')[1];
          let day = date.split('-')[2];
          let d = day.substring(0, 2);
          let hour = day.slice(-5);
          let hr = hour.substring(0, 2);

          let tsp = res.tsp_mesure_value;
          let sox = res.sox_mesure_value;
          let nox = res.nox_mesure_value;
          let hcl = res.hcl_mesure_value;
          let co = res.co_mesure_value;

          let bad = 'bad';
          let bad_html = '나쁨';
          let tsp_grade, sox_grade, nox_grade, hcl_grade, co_grade;
          tsp_grade = sox_grade = nox_grade = hcl_grade = co_grade = 'good';
          let tsp_html, sox_html, nox_html, hcl_html, co_html;
          tsp_html = sox_html = nox_html = hcl_html = co_html = '좋음';

          if (tsp > 15) {
            tsp_grade = bad;
            tsp_html = bad_html;
          }
          if (sox > 20) {
            sox_grade = bad;
            sox_html = bad_html;
          }
          if (nox > 50) {
            nox_grade = bad;
            nox_html = bad_html;
          }
          if (hcl > 12) {
            hcl_grade = bad;
            hcl_html = bad_html;
          }
          if (co > 50) {
            co_grade = bad;
            co_html = bad_html;
          }

          $(document).find("div.wrap_pollutant_date").append('' + year + ' 년 ' + month + ' 월 ' + d + ' 일 ' + hr + ' 시');

          $(document).find("li.tsp > span.cont").html(tsp + 'mg/Sm³');
          $(document).find("li.tsp > span.stat").addClass(tsp_grade).html(tsp_html);
          $(document).find("li.sox > span.cont").html(sox + 'ppm');
          $(document).find("li.sox > span.stat").addClass(sox_grade).html(sox_html);
          $(document).find("li.nox > span.cont").html(nox + 'ppm');
          $(document).find("li.nox > span.stat").addClass(nox_grade).html(nox_html);
          $(document).find("li.hcl > span.cont").html(hcl + 'ppm');
          $(document).find("li.hcl > span.stat").addClass(hcl_grade).html(hcl_html);
          $(document).find("li.co > span.cont").html(co + 'ppm');
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
<link type="text/css" rel="stylesheet" href="/js/jquery/datetimepicker/jquery.datetimepicker.css">
<script src="<?php echo $_SERVER['SELF']; ?>js/list.js" defer></script>
<?php
## 관리자 삭제일떄만 출력되는 javascript----- start
## /modules/보드이름/js/ 로 이동.( 모듈 복사시 모든 스크립를 같이 옮기기 위해.)
if ($_SYSTEM['permission']['admin'] == true && $data['multi_delete'] == 'true') {
  echo '<script src="' . $_SERVER['SELF'] . 'js/list_admin.js"  ></script>';
}
## 관리자 삭제일떄만 출력되는 javascript----- end
?>						
