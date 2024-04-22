<?php
/********************************************************************
 *  모든 board(게시판) 클레스
 ** lineup
 ** view
 ** save
 ** remove
 ** restore
 ** erase
 ** null_check
 ** admin_tools_article_save 관리자기능 : 승인/비승인 설정, 처리상태 설정, top 설정, 관리자 코멘트등록, 게시물 이동, 삭제글 복구설정 추가해야 한다.
 ********************************************************************/

class board extends module
{
  private $plugins = array();

  public function load_plugin(&$object)
  {
    $plugin_name = $object->identifier;

    $object->loadBaseObject($this);
    $this->plugins[$plugin_name] = &$object;
  }

  public function &getPlugin($name)
  {
    return $this->plugins[$name];
  }

  ### 클린신고센터 동의페이지 ###
  public function agreeForm()
  {
    global $mysql;
    $ARTICLES = array();
    $data = array();
    $data_file = array();

    ## skin 설정
    $ARTICLES['device'] = $this->device;
    $ARTICLES['module_root'] = $this->module_root;                  ## 근본적으로 스킨쪽에서 세팅되어야 한다.
    $ARTICLES['module_path'] = $this->module_config['module_path']; ## 근본적으로 스킨쪽에서 세팅되어야 한다.
    $ARTICLES['skin_style'] = empty($this->module_config['skin_style']) ? $this->module_config['board_id'] : $this->module_config['skin_style'];

    //if($this->module_config['skin_style'] == 'regulation' && $this->module_config['board_id'] == 'mayor_proposal_hope'){
    if ($this->module_config['skin_style'] == 'regulation') {
      //$ARTICLES['skin_name']   = 'agreeForm_'.$this->module_config['board_id'];
      $ARTICLES['skin_name'] = 'agreeForm_reg';
    } else {
      $ARTICLES['skin_name'] = 'agreeForm';
    }

    ob_clean();

    echo serialize($ARTICLES);

    return true;
  }


  public function agree()
  {
    global $mysql;

    ## pin이 있으면
    ## pin이 없으면 로그인 '/support/member_login?/welfare/woman/talentbank?mode=agree'
    if ((!($this->myinfo['is_login'] === true) || $this->myinfo['my_pin'] == null || $this->myinfo['my_pin'] == '') && !($this->permission['admin'] == true)) {
      ob_clean();
      //header("Location: /support/member_login?return_url=/welfare/woman/talentbank?mode=agree");
      //header("Location: /operation_guide/member_login?return_url=".$_SERVER['PHP_SELF']."?mode=agree"); //20150716 윤지미 return_url 수정
      header("Location: /support/member_login?return_url=" . $_SERVER['PHP_SELF'] . "?mode=agree"); //20150716 윤지미 return_url 수정
      return false;
    } else {
      $_SESSION["agree_st_" . $this->module_config['board_id']] = 'agree';
      $this->write();
      return false;
    }

    return true;
  }

  ## 목록
  public function lineup()
  {
    global $_GET;
    global $mysql;
    global $_SYSTEM;
    $ARTICLES = array();

    ## parameter setting.
    $page = $this->get_parameter('page');
    $page = empty($page) ? 1 : $page;
    $mode = $this->get_parameter('mode');
    $search_type = $this->get_parameter('search_type');
    $search_word = $this->get_parameter('search_word');
    $search_option = $this->get_parameter('search_option');
    $search_option_1 = $this->get_parameter('search_option_1');
    $category_1 = $this->get_parameter('category_1');
    $keyword = $this->get_parameter('keyword');
    if (!empty($keyword)) {
      $search_type = "title";
      $search_word = $keyword;
    }
    $category_1 = str_replace("amp;", "", $category_1);

    $page_scale = $this->get_parameter('page_scale');
    $search_start_date = $this->get_parameter('start_date');
    $search_finish_date = $this->get_parameter('finish_date');
    $search_date = $this->get_parameter('search_date');
    $start_date = empty($search_start_date) ? date('Ymd', strtotime('-1 years')) : $search_start_date;
    $finish_date = empty($search_finish_date) ? date('Ymd') : $search_finish_date;
    $year = $this->get_parameter('year');
    $device = $this->get_parameter('device');  // 앱에서 메인에 부르는 보도자료에서 파일을 못 가져 가도록 하기 위해서 GET값을 하나 더 받음. 앱에서는 app값을 던짐.


    ## social형 게시판 목록쪽에 여러이미지 불러오기 위해서 필요. ( 스킨 추가시 배열 목록 추가.)
    $multi_gallery = array("gallery_photo", "gallery_photo_foreign", "share_photo", "new_gallery_en", "new_gallery_ch", "new_gallery_jp", "board_list_down", "book_search");
    $sub_mode = $this->get_parameter('sub_mode');
    if ($this->module_config['skin_style'] == "popup" || $this->module_config['skin_style'] == "banner") {
      $sub_mode = empty($sub_mode) ? 'ing' : $sub_mode;
    }

    $sort = $this->get_parameter('sort');


    if (isset($_GET['use_tag']) && $_GET['use_tag'] == 'false') {
      $this->module_config['use_tag'] = 'false';
    }

    ##========= 조건절 세팅 start
    $query_where = '';

    ## tag 적용
    if ($this->module_config['use_tag'] == 'true') {
      if (!empty($this->module_config['source_id'])) $this->module_config['board_id'] = $this->module_config['source_id'];
    }

    ## 모듈 카테고리
    $query_where .= (!empty($query_where) ? ' AND ' : '') . ' board_id="' . $this->module_config['board_id'] . '" ';

    ## 2012-08-29 황재복 : 관리자 삭제 사유 사용 시 삭제 게시물 출력
    if ($this->module_config['use_delete_reason'] == 'true') {
      if ($this->permission['admin'] !== true) $query_where .= (!empty($query_where) ? ' AND ' : '') . ' IF(`admin_comment` IS NULL AND del="y", 0, 1) = 1 ';
    } else {
      ## 삭제게시물 출력
      if ($this->permission['admin'] !== true || $this->module_config['show_delete'] == 'false') $query_where .= (!empty($query_where) ? ' AND ' : '') . ' del="n" ';
    }

    ## 검색테그 설정 : 2014.07.24 오경우 추가 : 완도군청 실과/읍면 공지사항에 사용됨. $_POST['search_tag'] 는 메뉴세팅에서 넘어오는 값이다.
    if ($this->module_config['use_search_tag'] == 'true') {
      if (empty($_POST['search_tag'])) $query_where .= (!empty($query_where) ? ' AND ' : '') . ' search_tag IS NULL ';
      else $query_where .= (!empty($query_where) ? ' AND ' : '') . ' ( search_tag="' . $_POST['search_tag'] . '" OR search_tag IS NULL ) ';
    }

    ## 승인/비승인 사용
    if ($this->module_config['use_allow'] == 'true') {
      $ARTICLES['use_allow'] = $this->module_config['use_allow'];
      if ($this->permission['admin'] !== true) $query_where .= (!empty($query_where) ? ' AND ' : '') . ' (allow="y" OR reg_pin="' . $this->myinfo['my_pin'] . '") ';
    }

    ## 비공개글
    if ($this->module_config['use_lock'] == 'true') {
      $ARTICLES['use_lock'] = $this->module_config['use_lock'];
      $ARTICLES['open'] = $data['open'];
    }

    ## 작성자가 쓴 글만 보기
    if ($this->module_config['view_only_my_articles'] == 'true' && $_SYSTEM['config']['site_name'] != '개인업무') {

      if ($this->module_config['skin_style'] == 'regulation') {
        if ($this->permission['admin'] !== true && !empty($this->myinfo['user_id'])) {
          $query_where .= (!empty($query_where) ? ' AND ' : '') . ' (reg_pin = "' . $this->myinfo['my_pin'] . '" OR approval_id = "' . $this->myinfo['user_id'] . '" ) ';
        } elseif ($this->permission['admin'] !== true && empty($this->myinfo['user_id'])) {
          $query_where .= (!empty($query_where) ? ' AND ' : '') . ' (reg_pin = "' . $this->myinfo['my_pin'] . '") ';
        }
      } else {
        if ($this->permission['admin'] !== true) $query_where .= (!empty($query_where) ? ' AND ' : '') . ' reg_pin = "' . $this->myinfo['my_pin'] . '" ';
      }
    }

    ## 비로그인 회원 글쓰기 기능
    if ($this->module_config['use_logoff_write'] == 'true') $ARTICLES['use_logoff_write'] = $this->module_config['use_logoff_write'];

    ## 목록 파일 다운로드 설정
    $ARTICLES['use_list_attach'] = $this->module_config['use_list_attach'];
    $ARTICLES['use_internet_banner'] = !empty($this->module_config['use_internet_banner']) ? $this->module_config['use_internet_banner'] : "";

    ## tag 설정
    $query_tag = '';
    if ($this->module_config['use_tag'] == 'true' && !empty($this->module_config['tag_list'])) {
      $query_tag = implode('|', $this->module_config['tag_list']);
      $query_tag = ' tag REGEXP "' . $query_tag . '" ';
      $query_where .= (!empty($query_where) ? ' AND ' : '') . $query_tag;
    }

    ######################################################################
    ## 멀티호스트 설정
    if (!empty($this->module_config['use_multihost']) && $this->module_config['use_multihost'] == 'true') {
      $ARTICLES['use_multihost'] = $this->module_config['use_multihost'];
      $ARTICLES['host_list_all'] = $this->get_site_host_list();

    }
    ######################################################################

    ## 페이지 검색
    $arr_search_type = $this->make_search_list($this->module_config['search_list']);

    $search_type_count = count($arr_search_type);
    #검색 조건 : 제목, 내용, 이름, 아이디
    $query_search = '';
    for ($iter = 0; $iter < $search_type_count; $iter++) {
      if ($search_type == $arr_search_type[$iter]['value']) {
        $query_where .= (!empty($query_where) ? ' AND ' : '') . ' ' . $arr_search_type[$iter]['value'] . ' like "%' . $search_word . '%" ';
      } else {
        if (!empty($search_word)) {
          $query_search .= (!empty($query_search) ? ' OR ' : '') . ' ' . $arr_search_type[$iter]['value'] . ' like "%' . $search_word . '%" ';
        }
      }
    }


    $ARTICLES['search_list'] = $arr_search_type;
    $ARTICLES['search_type'] = $search_type;
    $ARTICLES['search_word'] = $search_word;

    ## 페이징검색 설정 ------------ 2020신규 기능 추가
    ## 설정값이 없으면 자동으로 6개 ,12개 , 30개 적용.
    if (empty($this->module_config['page_scale_search_list'])) {
      $this->module_config['page_scale_search_list'] = array("6", "12", "30");
    }
    $arr_page_scale_search = $this->module_config['page_scale_search_list'];
    $ARTICLES['page_scale_search'] = $arr_page_scale_search;

    ## 기간검색  ------------ 2020신규 기능 추가
    $ARTICLES['start_date'] = date('Ymd', strtotime($start_date));
    $ARTICLES['finish_date'] = date('Ymd', strtotime($finish_date));
    if ($search_date == "y" && ($this->module_config['skin_style'] != 'board_business' && $this->module_config['skin_style'] != 'board_busview')) {
      $query_where .= (!empty($query_where) ? ' AND ' : '') . ' reg_date <= "' . $finish_date . ' 23:59:59" and "' . $start_date . ' 00:00:00" <= reg_date ';
      $ARTICLES['search_date'] = $search_date;
    } elseif ($search_date == "y" && ($this->module_config['skin_style'] == 'board_business' || $this->module_config['skin_style'] == 'board_busview')) {
      $query_where .= (!empty($query_where) ? ' AND ' : '') . ' (( period_start <= "' . $finish_date . '" and "' . $finish_date . '" <= period_end ) OR (period_start <= "' . $start_date . '" and "' . $start_date . '" <= period_end))';
      $ARTICLES['search_date'] = $search_date;
    }


    ## 2015.09.24 서희진 추가
    ## 후기 스킨일떄 검색어 검색
    if ($this->module_config['skin_style'] == 'epilogue') {
      if (!empty($search_word)) {
        $query_where .= (!empty($query_where) ? ' AND ' : '') . ' ( title REGEXP "' . $search_word . '" OR contents REGEXP "' . $search_word . '" OR varchar_1 REGEXP "' . $search_word . '")';
      }
      if (!empty($search_option)) {
        $query_where .= (!empty($query_where) ? ' AND ' : '') . ' ( ';
        foreach ($search_option as $key => $value) {
          if ($key > 0) $query_where .= ' OR ';
          $query_where .= ' varchar_2 REGEXP "' . $value . '" ';
        }
        if (!empty($search_option_1)) {
          $query_where .= '';
        } else {
          $query_where .= ' ) ';
        }
      }

      if (!empty($search_option_1)) {
        $query_where .= (!empty($search_option) ? ' OR ' : ' AND ( ');
        foreach ($search_option_1 as $key => $value) {
          if ($key > 0) $query_where .= ' OR ';
          $query_where .= ' varchar_7 REGEXP "' . $value . '" ';
        }
        $query_where .= ' ) ';
      }

      $ARTICLES['search_option'] = $search_option;
      $ARTICLES['search_option_1'] = $search_option_1;
    }

    if ($this->module_config['board_id'] == 'www_occasion' && $this->hostname != 'www') { ## 20191108 공연행사 게시판 본청 외에 서브사이트 각각 등록한 행사만 보이도록
      $query_where .= (!empty($query_where) ? ' AND ' : '') . ' category_2="' . $_POST['category_2'] . '"';
    }


    ## 분류 : 무조건 조건의 가장 마지막에 와야한다.
    if ($this->module_config['use_category_1'] == 'true' && is_array($this->module_config['category_1'])) {
      $ARTICLES['use_category_1'] = $this->module_config['use_category_1'];
      $ARTICLES['category_1'] = $category_1;
      $category_1_list = $this->board_category('category_1', $query_where);
      $ARTICLES['category_1_list'] = serialize($category_1_list);
      $ARTICLES['category_1_all'] = serialize($this->module_config['category_1']);
      if (!empty($category_1)) $query_where .= (!empty($query_where) ? ' AND ' : '') . ' category_1="' . $category_1 . '" ';
    }

    if (!empty($this->module_config['use_category_2']) && $this->module_config['use_category_2'] == 'true' && is_array($this->module_config['category_2'])) {
      $ARTICLES['use_category_2'] = $this->module_config['use_category_2'];
      $ARTICLES['category_2'] = $category_2;
      $category_2_list = $this->board_category('category_2', $query_where);
      $ARTICLES['category_2_list'] = serialize($category_2_list);
      $ARTICLES['category_2_all'] = serialize($this->module_config['category_2']);
      if (!empty($category_2)) $query_where .= (!empty($query_where) ? ' AND ' : '') . ' category_2="' . $category_2 . '" ';
    }

    ## API에서 오늘 날짜의 값을 가져오기 위함...
    if (!empty($_GET['list_size']) && $_GET['list_size'] == 'now') {
      $query_tag = ' (top_start <= "' . date('Y-m-d') . '" AND top_end >= "' . date('Y-m-d') . '") ';
      $query_where .= (!empty($query_where) ? ' AND ' : '') . $query_tag;
      $this->module_config['page_scale'] = 200;
    }

    ## API에서 PAGE SCALE 를 조절할수 있게 하기위함...  : 정운영 추가 2015-10-20
    if (isset($_GET['page_scale'])) {  //다른 class.php에도 추가해야함.
      $this->module_config['page_scale'] = $_GET['page_scale'];
    }

    if (isset($page_scale)) {  //다른 class.php에도 추가해야함.
      $this->module_config['page_scale'] = $page_scale;
    }

    ## 처리상태
    if ($this->module_config['use_process_1'] == 'true' && is_array($this->module_config['process_1'])) $ARTICLES['use_process_1'] = $this->module_config['use_process_1'];

    ## 묻고 답하기 일때 답변글 빼고 쿼리
    if ($this->module_config['skin_style'] == 'qna' || $this->module_config['use_reply'] != "none") {
      $query_where .= (!empty($query_where) ? ' AND ' : '') . ' level="0"';
    }

    ## open_measure 스킨에서만 사용.
    ## 일반 사용자들에게는 게시기간이 아닌 건 보이지 않도록 적용.
    if ($this->module_config['use_period'] && $_SESSION['user_level'] != 1 && $_SESSION['user_level'] != 6 && in_array($this->module_config['skin_style'], array("open_measure"))) {
      $query_where .= (!empty($query_where) ? ' AND ' : '') . ' (use_period = "y" AND period_start <= "' . date('Y-m-d') . '" and "' . date('Y-m-d') . '" <= period_end) ';
    }

    ## open_measure 카드뉴스에만 사용.
    ## 2021.07.21  서희진 수정: 모듈셋팅에서 게시글 사용, 게시기간 체크했을떄만
    if ($this->module_config['use_period'] && $_SESSION['user_level'] != 1 && $_SESSION['user_level'] != 6 && in_array($this->module_config['skin_style'], array("card_news"))) {
      $query_where .= (!empty($query_where) ? ' AND ' : '') . ' ( use_period = "n" OR ( use_period = "y" AND period_start <= "' . date('Y-m-d') . '" and "' . date('Y-m-d') . '" <= period_end) ) ';
    }

    ## top설정되어 있는 항목은 쿼리에서 뺀다 : 서희진 추가 2017.07.20
    $chk_top_date = (!empty($data['top_start']) && !empty($data['top_start'])) ? 'true' : 'false';
    if ($this->get_top_count($chk_top_date) > 0 && $this->module_config['use_top'] == 'true') {
      $top_idx_array = $this->get_top_idx_array($chk_top_date);
      $top_idx = implode(",", $top_idx_array);
      $query_where .= (!empty($query_where) ? ' AND ' : '') . ' idx NOT IN (' . $top_idx . ') ';
    }

    ## 2020  신규 기능. ( 진행중 / 종료/ 대기 /전체 쿼리 )
    if ($this->module_config['skin_style'] == 'popup' || $this->module_config['skin_style'] == 'youth_program') {
      switch ($sub_mode) {
        case "end":
          $query_where .= (!empty($query_where) ? ' AND ' : '') . ' top_end < "' . date('Y-m-d') . '" ';
          break;
        case "wait":
          $query_where .= (!empty($query_where) ? ' AND ' : '') . ' top_start > "' . date('Y-m-d') . '" ';
          break;
        case "ing":
        case "sort":
        case "list":
          $query_where .= (!empty($query_where) ? ' AND ' : '') . ' top_start <= "' . date('Y-m-d') . '" and "' . date('Y-m-d') . '" <= top_end ';
          break;
        case "all":
        default :
          $sub_mode;
          break;
      }
    }
    ##---------------------------------------------------------

    $query_where = empty($query_where) ? '' : ' WHERE ' . $query_where;
    ##========= 조건절 세팅 end


    #페이징 쿼리추가
    $this->module_config['page_scale'] = empty($this->module_config['page_scale']) ? '10' : $this->module_config['page_scale'];
    //	$limit = ' LIMIT '.$this->module_config['page_scale']*($page-1).', '.$this->module_config['page_scale'];
    //# 엑셀 다운로드일 경우 전체 목록 출력될 수 있도록! 210623
    $limit = ($sub_mode == 'excel') ? '' : ' LIMIT ' . $this->module_config['page_scale'] * ($page - 1) . ', ' . $this->module_config['page_scale'];

    #전체갯수 쿼리
    $total_count = $this->list_total_count($query_where);
    $total_page = ceil($total_count / $this->module_config['page_scale']);

    $query = 'SELECT a.*, b.visit_cnt, c.visit_cnt as recommend_cnt FROM ';
    $query .= '  ( ';
    $query .= '	   SELECT * ';
    if ($this->module_config['skin_style'] == 'culture_info2') {
      $query .= '	 , if( DATEDIFF(varchar_1,now()) < -30 ,9999, ABS(DATEDIFF(varchar_1,now()))) as date_diff  '; // 오늘 날짜에서 시작일이 -7일 전 오늘 이후 시작일 순으로 정렬되기 위해서 날짜 차이 값.
    }
    $query .= '	   FROM ' . $this->module_config['table_name'] . ' ';
    $query .= $query_where;
    if ($this->module_config['skin_style'] == 'popup' || $this->module_config['skin_style'] == 'youth_program') {
      $query .= '	   ORDER BY ' . ($sub_mode == "sort" || $sub_mode == "ing" ? ' seq, ' : '') . ' reg_date DESC ';  //varchar_1 은 노출 sorting값
    } elseif ($this->module_config['skin_style'] == "gallery_photo" || $this->module_config['skin_style'] == "regulation" || $this->module_config['skin_style'] == "ys_bodo") {
      ## 핫이슈, 보도자료 예전데이터가 idx 큰데 reg_date가 더 이전인 데이터가 있음.. 20210528 강성수
      $query .= '	   ORDER BY reg_date DESC ';
    } elseif ($sub_mode == 'excel') {
      $query .= '	   ORDER BY idx ASC ';
    } else {
      //$query .= '	   ORDER BY reg_date DESC ';
      //$query .= '	   ORDER BY idx, reg_date DESC '; ##20210528 강성수 reg_date 가 없는 데이터이전이 있습니다;
      $query .= '	   ORDER BY idx DESC '; ##20210528 강성수 reg_date 가 없는 데이터이전이 있습니다;
    }
    if ($this->module_config['skin_style'] != "finance_form" && $sub_mode != "sort") {
      $query .= $limit;
    }
    $query .= '  ) AS a ';
    $query .= '  LEFT JOIN ( ';                ## 보기횟수
    //$query .= '	   SELECT table_idx, SUM(visit_cnt) as visit_cnt 	';
    //$query .= '	   FROM _visit_info ';
    //$query .= '	   WHERE table_name="'.$this->module_config['table_name'].'" GROUP BY table_idx ';
    $query .= '	   SELECT table_idx, visit_cnt ';
    $query .= '	   FROM _visit_info ';
    $query .= '	   WHERE table_name="' . $this->module_config['table_name'] . '" ';
    $query .= '  ) AS b ON a.idx=b.table_idx';
    $query .= '  LEFT JOIN _recommend_info AS c';
    $query .= '  ON a.idx = c.table_idx AND c.table_name = "yb_board"';
    if ($sort == 'recommend') {
      $query .= '	   ORDER BY recommend_cnt DESC';
    } elseif ($sub_mode == 'excel') {
      $query .= '	   ORDER BY idx ASC ';
    } elseif ($this->module_config['skin_style'] == "gallery_photo" || $this->module_config['skin_style'] == "regulation" || $this->module_config['skin_style'] == "ys_bodo") {
      ## 정렬이 안 맞아서 임시로 붙여둠.. 다시 확인해야함.221212.a.
      $query .= '	   ORDER BY a.reg_date DESC ';
    } elseif ($this->module_config['skin_style'] == "board") {
      $query .= '	   ORDER BY a.idx DESC ';
    }


    $data_list = array();
    $result = $mysql->query($query);

    $idx_list = '';
    for ($iter = 0; $data_tmp = $mysql->fetch_array($result); $iter++) {
      $data_list[] = $data_tmp;
      $idx_list .= (empty($idx_list) ? '' : ',') . $data_tmp['idx'];
    }

    ## 첨부파일 쿼리를 따로 분리 시작.
    $query = 'SELECT ';
    $query .= '    idx, ';
    $query .= '    table_idx, ';

    //첨부파일기능 수정 : 오경우 (20120125)
    $query .= '    file_type, ';
    $query .= '    original_name, ';
    $query .= '    original_name AS origin_name, ';
    $query .= '    re_name , ';
    $query .= '    re_name AS file_name, ';
    $query .= '    file_path, ';
    $query .= '    file_size, ';
    $query .= '    title AS photo_alt, ';
    $query .= '    download, title, description, tag, ebook_code, movie_duration, title_image, pwd ';

    $query .= 'FROM _file_info ';
    $query .= 'WHERE ';
    $query .= '    table_name="' . $this->module_config['table_name'] . '" ';
    $query .= '    AND table_idx IN (' . $idx_list . ') ';
    ## 특정 스킨에만 있던 기능을 전부다 넣음.
    if ($this->module_config['main_image_use'] == 'true' || $this->module_config['skin_style'] == 'mush_farmer') {
      //if( $this->module_config['skin_style'] == 'thumb_photo' || $this->module_config['skin_style'] == 'thumb_photo_gongik' ){
      $query .= '    AND title_image = "y" ';
    }
    ## function 상단에 $multi_gallery 배열 선언중.
    if (in_array($this->module_config['skin_style'], $multi_gallery)) {
      $query .= 'GROUP BY table_idx, file_type, seq';
    }
    $query .= ' ORDER BY idx ';


    $file_list = array();
    $data_file = array();

    $result = $mysql->query($query);
    while ($data_file = $mysql->fetch_array($result)) $file_list[$data_file['table_idx']][] = $data_file;
    ## 첨부파일 쿼리를 따로 분리 끝.

    //댓글개수 수정 : 오경우(20120125)
    ## 댓글갯수
    if ($this->module_config['use_comment'] == 'true') {
      $query = sprintf('SELECT table_idx, count(idx) AS cnt FROM yb_comment WHERE table_name = "%s" AND del="n" AND table_idx IN (%s) group by table_idx', $this->module_config['table_name'], $idx_list);
      $comment_list = array();
      $data_comment = array();
      $result = $mysql->query($query);
      while ($data_comment = $mysql->fetch_array($result)) $comment_list[$data_comment['table_idx']] = $data_comment;
    }


    # top 설정 처리 -main 추출시 use_top=false 설정이면 top list 추가하지 않도록 조건 추가. 210621
    if ($this->module_config['use_top'] == 'true' && !(isset($_GET['use_top']) && $_GET['use_top'] == 'false')) {

      $query_tag = empty($query_tag) ? '' : ' AND ' . $query_tag;

      $query = ' SELECT ';
      $query .= ' 	a.*, b.visit_cnt ';
      $query .= ' FROM ' . $this->module_config['table_name'] . ' AS a ';
      $query .= ' LEFT JOIN _visit_info AS b ';
      $query .= ' ON b.table_name="' . $this->module_config['table_name'] . '" AND a.idx=b.table_idx ';
      $query .= ' WHERE a.board_id="' . $this->module_config['board_id'] . '" AND a.del="n" AND a.open="y" AND a.top ="y" ';
//			if( $this->module_config['use_top_limit'] == 'ture')
      //if( !empty($data['top_start']) )
      $query .= ' AND a.top_start <= "' . date('Y-m-d') . '" AND a.top_end >= "' . date('Y-m-d') . '" ';
      $query .= $query_tag;
      $query .= ' ORDER BY a.idx DESC';

      $data_top = array();
      $result = $mysql->query($query);
      while ($data_top[] = $mysql->fetch_array($result)) ;
      array_pop($data_top);
      $count_top = count($data_top);
      if ($count_top > 0) $data_list = array_merge($data_top, $data_list);
    } else {
      $count_top = 0;
    }

    $count_list = count($data_list);


    $data = array();
    $new_term = strtotime(($this->module_config['new_article_term'] * (-1)) . 'day');

    if ($page == 1) {
      $board_new_list = '';
    }

    for ($iter = 0; $iter < $count_list; $iter++) {
      $data = $data_list[$iter];


      //$data['contents'] = htmlentities($data['contents'], ENT_QUOTES | ENT_IGNORE, "UTF-8");
      $data['contents'] = str_replace("&nbsp;", " ", $data['contents']);


      ## 공지사항 설정
      $chk_top_date = (!empty($data['top_start']) && !empty($data['top_start'])) ? 'true' : 'false';
      if ($this->module_config['use_top'] == "true" && in_array($data['idx'], $this->get_top_idx_array($chk_top_date))) {
        $ARTICLES['list'][$iter]['is_top'] = 'true';
      }

      ## 글번호
      $ARTICLES['list'][$iter]['list_num'] = $total_count - ($this->module_config['page_scale'] * ($page - 1)) - $iter + $count_top;


      $data['title'] = stripslashes($data['title']);  // 정운영추가 2013-11-01 홑따움표에 \\\\\ 가 붙어서 나오는것을 처리
      $data['orignal_title'] = preg_replace("(\<(/?[^\>]+)\>)", "", $data['title']);  // 정운영추가 2013-11-01 홑따움표에 \\\\\ 가 붙어서 나오는것을 처리


      ## 제목 길이설정
      if ($this->module_config['subject_cut'] > 0) {
        $subject_cut = $this->module_config['subject_cut'] - ($data['seq'] * 2);
        $data['title'] = call::strcut($data['title'], $subject_cut, '..');
      }


      ##20200617 김다정 권부장님요청 나주 모바일일때 더 자릅니다.
      if ($_SYSTEM['device'] == 'mobile' && $_SYSTEM['menu_info']['_board_id'] == 'www_different_organ') {
        $data['title'] = call::strcut(str_replace('&', '&amp;', $data['title']), 30, '..');
      }


      $data['contents'] = preg_replace('/<style(.*)<\/style>/', '', $data['contents']);

      ## 본문글 길이설정변수(포토뉴스, 텍스트뉴스에서 사용). 추후 테그제거 기능을 넣어야 한다.
      ############################# contetns #############################
      //echo $buffer['html_tag'];
      if ($data['html_tag'] == "a") {
        $arr_contents = unserialize(base64_decode($data['contents_original']));
        foreach ($arr_contents as $val) {
          preg_match_all("/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i", $val, $matches);
          if (count($matches[0]) > 0) {
          } else {
            $data['contents'] = $val;
            break;
          }
        }
      } else {

      }
      ############################# contetns #############################
      if ($this->module_config['skin_style'] == 'blog' || $this->module_config['skin_style'] == 'faq') { // 모바일에서 내용 자르기 안됨.ㅡㅜ
        if ($this->module_config['board_id'] == 'www_notice') {
          $ARTICLES['list'][$iter]['contents'] = empty($ARTICLES['list'][$iter]['contents']) ? '' : htmlspecialchars(str_replace('&', '&amp;', $ARTICLES['list'][$iter]['contents']));
          if ($this->module_config['contents_cut'] > 0) {
            $data['contents'] = stripslashes($data['contents']);
            $ARTICLES['list'][$iter]['contents'] = call::strcut($this->strip_contents($data['contents']), $this->module_config['contents_cut'], '..');
          }
        } else {
          $ARTICLES['list'][$iter]['contents'] = $data['contents'];
        }

        ## 신규 모듈은 contents_original의 내용을 출력해야하고. 신규 모듈로 변경되기전(데이타이전) 으느 contents  내용을 출력해야한다
        $ARTICLES['list'][$iter]['contents'] = empty($data['contents_original']) ? $data['contents'] : $data['contents_original'];

      } else {
        $ARTICLES['list'][$iter]['contents'] = empty($ARTICLES['list'][$iter]['contents']) ? '' : htmlspecialchars(str_replace('&', '&amp;', $ARTICLES['list'][$iter]['contents']));
        if ($this->module_config['contents_cut'] > 0) {
          $data['contents'] = stripslashes($data['contents']);
          $ARTICLES['list'][$iter]['contents'] = call::strcut($this->strip_contents($data['contents']), $this->module_config['contents_cut'], '..');
        }
      }
      $data['view_permission'] = 'false';
      if (!empty($this->myinfo['user_id']) && isset($data['view_id'])) {
        if (preg_match('/' . $this->myinfo['user_id'] . ';/', $data['view_id'])) {
          $data['view_permission'] = 'true';
        }
      }
      ## 비공개글 설정 - 비공개글은 로그인 한 사람만 작성할 수 있다.
      $ARTICLES['list'][$iter]['is_lock'] = 'false';
      $ARTICLES['list'][$iter]['lock_img'] = 'false';
      $ARTICLES['list'][$iter]['open'] = $data['open'];


      if ($this->module_config['use_lock'] == 'true') {        ## 비공개글을 사용함
        if ($data['open'] != 'y') {                          ## 비공개글일 경우
          $ARTICLES['list'][$iter]['lock_img'] = 'true';
          if ($this->permission['admin'] != true && $this->myinfo['my_pin'] != $data['reg_pin']) { ## 관리자가 아니거나 자신이 작성한 글이 아닐때

            $ARTICLES['list'][$iter]['is_lock'] = $data['reg_pin'] == GUEST_PIN ? 'false' : 'true'; // 비공개글 추가 수정 : 오경우
            if ($this->module_config['use_hide_name'] == 'true') $data['reg_name'] = call::strcut($data['reg_name'], 1, 'OO');
            if ($this->module_config['use_hide_title'] == 'true' && $data['view_permission'] == 'false') $data['title'] = '본인 요청에 의한 비공개 상담글입니다.'; // 추후 messgae에 정의해서 사용한다.
          }
        }
      }

      //2012-08-30 황재복 : 관리자 삭제 사유 사용 시 해당 게시글 제목 변형
      if ($this->module_config['use_delete_reason'] == 'true') {
        //$data['reg_pin'] == $this->myinfo['my_pin'] ==> 쿼리문에서 이미 걸려줬지만 혹시나 해서 다시 설정
        //2013-05-09 황재복 : $this->module_config['use_delete_reason'] == true
        if ($this->module_config['show_delete_normal'] != true) {
          if ($data['del'] == 'y' && $this->permission['admin'] != true && $data['reg_pin'] == $this->myinfo['my_pin']) {
            $data['title'] = '관리자에 의해 삭제된 글입니다.';
          }
        } else {
          if ($data['del'] == 'y' && $this->permission['admin'] != true) {
            //preg_match('/([^:]+)글삭제:([^:]+):([^:]+)[:]?([^:]*)/',$data['log'],$deleter);
            //if($data['reg_pin'] == $deleter[3]) {
            $data['title'] = '글쓴이에 의해 삭제된 게시물입니다.';
            //} else {
            //	$data['title'] = ($data['delete_reason']==''?'관리자에 의해 삭제된 글입니다.':$data['delete_reason']);
            //}
          }
        }
      }
      //board_munhak_visit_application
      ## 2012.03.22 오경우 추가
      if ($this->permission['admin'] != true && $this->module_config['use_hide_name_all'] == 'true') $data['reg_name'] = call::strcut($data['reg_name'], 1, 'OO');


      $ARTICLES['list'][$iter]['reg_name'] = $data['reg_name'];
      $ARTICLES['list'][$iter]['reg_pin'] = $data['reg_pin'];
      $ARTICLES['list'][$iter]['depart_name'] = $data['depart_name'];
      //$ARTICLES['list'][$iter]['title'] = htmlspecialchars(str_replace('&','&amp;',$data['title']));
      //$ARTICLES['list'][$iter]['title'] = htmlspecialchars($data['title']);
      $ARTICLES['list'][$iter]['title'] = $data['title'];
      $ARTICLES['list'][$iter]['orignal_title'] = htmlspecialchars($data['orignal_title']);
      ## 삭제글 설정
      $ARTICLES['list'][$iter]['is_delete'] = $data['del'] == 'y' ? 'true' : 'false';
      if ($data['html_tag'] == "y") {
        $ARTICLES['list'][$iter]['contents'] = $data['contents'];
      } elseif ($data['html_tag'] == "a") {
        $ARTICLES['list'][$iter]['contents'] = $data['contents_original'];
      } else {
        $ARTICLES['list'][$iter]['contents'] = $this->module_config['use_editor'] == 'true' ? $data['contents'] : nl2br(strip_tags($data['contents']));
      }

      ## 새로운글 설정
      $ARTICLES['list'][$iter]['new_img'] = $new_term < strtotime($data['reg_date']) ? 'true' : 'false';

      $ARTICLES['list'][$iter]['top_start'] = empty($data['top_start']) ? NULL : date("Y-m-d", strtotime($data['top_start']));
      $ARTICLES['list'][$iter]['top_end'] = empty($data['top_end']) ? NULL : date("Y-m-d", strtotime($data['top_end']));
      $ARTICLES['list'][$iter]['top_end_time'] = empty($data['top_end']) ? NULL : date("H:i:s", strtotime($data['top_end']));

      $ARTICLES['list'][$iter]['linkbox'] = unserialize(base64_decode($data['linkbox']));

      //첨부파일기능 수정 : 오경우 (20120222)
      ## 첨부파일 이미지 설정
      $ARTICLES['list'][$iter]['file_exist'] = 'false';
      $ARTICLES['list'][$iter]['photo_exist'] = 'false';
      $ARTICLES['list'][$iter]['movie_exist'] = 'false';
      if (!isset($file_list[$data['idx']])) $file_list[$data['idx']] = array();
      $file_count = count($file_list[$data['idx']]);


      if ($_SERVER['REMOTE_ADDR'] == '49.254.140.140' && $_SESSION['user_id'] == 'wlswn4630') {
        //echo '<pre>';
        //print_r($file_list);
        //exit;
      }


      ## 이미지 파일 갯수
      ## 2017.11.13 함수로 개시물마다 불러오는 쿼리를 뺴고 file_list의 count값으로 설정.
//			$ARTICLES['list'][$iter]['photo_cnt'] = $this->get_photo_count($this->module_config['table_name'], $data['idx']);
      $ARTICLES['list'][$iter]['photo_cnt'] = $file_count;

      ## 2023.03.08 이진주 수정 날짜 추가
      $ARTICLES['list'][$iter]['modify_date'] = $data['modify_date'];

      //2018-03-30 황재복 : 목포 앱 오류 때문에 임시로 막아놈
      //	if($this->module_config['skin_style']!='board') {
      if (!empty($data['idx'])) {
        if (in_array($this->module_config['skin_style'], $multi_gallery)) {
          $ARTICLES['list'][$iter]['file_list2'] = $this->attached_file_list($data['idx']);
          $ARTICLES['list'][$iter]['photo_cnt'] = count($ARTICLES['list'][$iter]['file_list2']);
        }
      }
      

      $main_img = true;
      for ($jter = 0; $jter < $file_count; $jter++) {
        if ($file_list[$data['idx']][$jter]['file_type'] == 'photo') {
          $ARTICLES['list'][$iter]['photo_exist'] = 'true';
          //$img_path = $_SERVER['PHP_SELF'].'/ybmodule.file'.$this->module_config['path'].'/'.$this->module_config['board_id'].'/';
          if ($main_img) {
            ## 대표이미지 -----------------------------------------------
            $img_path = '/ybmodule.file' . $this->module_config['path'] . '/' . $this->module_config['board_id'] . '/';
            $ARTICLES['list'][$iter]['photo_origin_name'] = $file_list[$data['idx']][$jter]['origin_name'];
            $ARTICLES['list'][$iter]['photo_idx'] = $file_list[$data['idx']][$jter]['idx'];
            $ARTICLES['list'][$iter]['photo_path'] = $img_path;
            $ARTICLES['list'][$iter]['photo_name'] = $file_list[$data['idx']][$jter]['file_name'];
            $ARTICLES['list'][$iter]['photo_alt'] = empty($file_list[$data['idx']][$jter]['photo_alt']) ? $data['title'] : $file_list[$data['idx']][$jter]['photo_alt'];
            $ARTICLES['list'][$iter]['photo_down'] = $file_list[$data['idx']][$jter]['download'];
            $main_img = false;
            ## 대표이미지 -----------------------------------------------
          }


          ## function 상단에 $multi_gallery 배열 선언중.
          if (in_array($this->module_config['skin_style'], $multi_gallery)) {
            for ($ii = 0; $ii < $ARTICLES['list'][$iter]['photo_cnt']; $ii++) {
              $tmp_photo = array();
              $tmp_photo = $ARTICLES['list'][$iter]['file_list2'][$ii];
              $ARTICLES['list'][$iter]['photo_list'][] = array(
                'photo_origin_name' => $tmp_photo['original_name'],
                'photo_path' => $img_path,
                'photo_name' => $tmp_photo['re_name'],
                'photo_alt' => $tmp_photo['title'],
                'photo_idx' => $tmp_photo['idx']
              );
            }
          }

        } else if ($file_list[$data['idx']][$jter]['file_type'] == 'movie') {
          $img_path = $_SERVER['PHP_SELF'] . '/ybmodule.file' . $this->module_config['path'] . '/' . $this->module_config['board_id'] . '/';
          $ARTICLES['list'][$iter]['photo_path'] = $img_path;
          $ARTICLES['list'][$iter]['movie_exist'] = 'true';
          $ARTICLES['list'][$iter]['gif_exist'] = file_exists($_SYSTEM['module_root'] . $file_list[$data['idx']][$jter]['file_path'] . '.gif');

          ## ------------------------  이사님 지시하에 동영상 정보 추출 및 info json파일 생성. ---------------------------------------
          ## 2020.05.24 서희진
          /*Array(
							[width] => 1280
							[height] => 720
							[r_frame_rate] => 24/1
							[avg_frame_rate] => 24/1
							[bit_rate] => 3329544
							[nb_read_frames] => 1281
							[duration] => 00:00:00.00000
						)
					*/
          $filename = $_SYSTEM['module_root'] . '/_data' . $this->module_config['path'] . '/' . $this->module_config['board_id'] . '/' . $file_list[$data['idx']][$jter]['file_name'];
          $movie_info = call::getMoveInfo($filename);  ## 시스템 함수 yubi_cms에 선언되어 있어야함.
          //print_r( $movie_info );exit;


          /*$ARTICLES['list'][$iter]['photo_exist'] = 'true';*/
          $ARTICLES['list'][$iter]['movie_name'] = $file_list[$data['idx']][$jter]['file_name'];
          $ARTICLES['list'][$iter]['movie_file_size'] = round($file_list[$data['idx']][$jter]['file_size'] / 1000000, 1) . 'MB';
          $ARTICLES['list'][$iter]['movie_duration'] = empty($movie_info['duration']) ? '00:00:00' : date('H:i:s', strtotime($movie_info['duration']));
          $ARTICLES['list'][$iter]['movie_bitrate'] = empty($movie_info['bit_rate']) ? '0' : round($movie_info['bit_rate'] * 0.000125, 1) . 'KB';
          $ARTICLES['list'][$iter]['movie_p'] = empty($movie_info['height']) ? '0' : $movie_info['height'];
          ## ------------------------  이사님 지시하에 동영상 정보 추출 및 info json파일 생성. ---------------------------------------

        } else if ($file_list[$data['idx']][$jter]['file_type'] == 'file') {
          $img_path = $_SERVER['PHP_SELF'] . '/ybmodule.file' . $this->module_config['path'] . '/' . $this->module_config['board_id'] . '/';
          $ARTICLES['list'][$iter]['photo_path'] = $img_path;

          //$ARTICLES['list'][$iter]['file_list_file'] = $this->attached_file_list_none($data['idx']);
          $ARTICLES['list'][$iter]['file_list_file'] = $file_list[$data['idx']];
          foreach ($file_list[$data['idx']] as $file_buffer) {
            $ARTICLES['list'][$iter]['file_idxs'][] = $file_buffer['idx'];
          }

          $ARTICLES['list'][$iter]['file_exist'] = 'true';
          //2012-06-14 황재복 : 민원사무편람 게시판을 예전과 같이 리스트 상에서 확인 가능토록 해달라는 요청으로 아래 문구 삽입
          if ($this->module_config['skin_style'] == 'minwon_form' || $this->module_config['skin_style'] == 'ebook') {
            if (!empty($data['idx'])) {
              $ARTICLES['list'][$iter]['file_list'] = $this->attached_file_list($data['idx']);
            }
          }
        }


        ##----------------------------------------------------
        ## open_measure 카드뉴스에만 사용.
        ## 2021.07.21  서희진 수정: 모듈셋팅에서 게시글 사용, 게시기간 체크했을떄만
        if ($this->module_config['use_period'] && $this->permission['admin'] == 'ture' && in_array($this->module_config['skin_style'], array("card_news"))) {
          if ($data['use_period'] == 'y') {
            $ARTICLES['list'][$iter]['period_type'] = "y";
            $ARTICLES['list'][$iter]['period_type_check'] = ($data['period_start'] <= date("Y-m-d H:i:s") && date("Y-m-d H:i:s") <= $data['period_end']) ? 'y' : 'n';
          } else {
            $ARTICLES['list'][$iter]['period_type'] = "n";
          }
        } else {
          $ARTICLES['list'][$iter]['period_type'] = "";
        }
        ##----------------------------------------------------


      }

      //	}

      //댓글개수 수정 : 오경우(20120125)
      ## 댓글 설정
      if ($this->module_config['use_comment'] == 'true') $ARTICLES['list'][$iter]['comment_cnt'] = empty($comment_list[$data['idx']]['cnt']) ? 0 : $comment_list[$data['idx']]['cnt'];
      else $ARTICLES['list'][$iter]['comment_cnt'] = 0;

      if (isset($this->module_config['use_banner']) && $this->module_config['use_banner'] == 'true') $ARTICLES['list'][$iter]['link_url'] = $data['link_url'];

      ## banner와 유튜브 스킨에서 사용.
      $ARTICLES['list'][$iter]['link_url'] = $data['link_url'];


      ## 기타설정
      $ARTICLES['list'][$iter]['category_1'] = $data['category_1'];
      if (!empty($data['category_2'])) {
        $ARTICLES['list'][$iter]['category_2'] = $data['category_2'];
      }
      if (!empty($data['category_3'])) {
        $ARTICLES['list'][$iter]['category_3'] = $data['category_3'];
      }
      $ARTICLES['list'][$iter]['process_1'] = $data['process_1'];
      if (!empty($data['process_2'])) {
        $ARTICLES['list'][$iter]['process_2'] = $data['process_2'];
      }
      if (!empty($data['process_3'])) {
        $ARTICLES['list'][$iter]['process_3'] = $data['process_3'];
      }
      $ARTICLES['list'][$iter]['board_id'] = $data['board_id'];
      $ARTICLES['list'][$iter]['level'] = $data['level'];
      $ARTICLES['list'][$iter]['seq'] = $data['seq'];
      $ARTICLES['list'][$iter]['idx'] = $data['idx'];
      $ARTICLES['list'][$iter]['reg_date'] = (date('Y-m-d') == date('Y-m-d', strtotime($data['reg_date'])) ? substr($data['reg_date'], -8) : substr($data['reg_date'], 0, 10)); // substr($data['reg_date'],0,10);
      $ARTICLES['list'][$iter]['reg_datetime'] = $data['reg_date'];
      if (isset($data['mod_date'])) $ARTICLES['list'][$iter]['mod_date'] = substr($data['mod_date'], 0, 10);
      $ARTICLES['list'][$iter]['visit_cnt'] = empty($data['visit_cnt']) ? 0 : $data['visit_cnt'];
      $ARTICLES['list'][$iter]['seq'] = empty($data['seq']) ? 0 : $data['seq'];
      $ARTICLES['list'][$iter]['allow'] = $data['allow'];
      $ARTICLES['list'][$iter]['title_style'] = $data['title_style'];
      $ARTICLES['list'][$iter]['approval_id'] = empty($data['approval_id']) ? '' : $data['approval_id'];
      //2012.09.20 강성수 phone, email 추가.

      //$ARTICLES['list'][$iter]['phone'] = $data['phone'];
      //20200421 조지영 스마트마을방송 작업 중 수정, 연락처는 암호화 처리
      $encryption = new yb_crypt();
      $ARTICLES['list'][$iter]['phone'] = $encryption->decrypt($data['phone']);


      $ARTICLES['list'][$iter]['email'] = $data['email'];

      if (isset($data['organ'])) $ARTICLES['list'][$iter]['organ'] = $data['organ'];
      if (isset($data['sex'])) $ARTICLES['list'][$iter]['sex'] = $data['sex'];
      if (isset($data['age'])) $ARTICLES['list'][$iter]['age'] = $data['age'];
      if (isset($data['hphone'])) $ARTICLES['list'][$iter]['hphone'] = $data['hphone'];


      $ARTICLES['list'][$iter]['contents_en'] = $data['contents_en'];
      $ARTICLES['list'][$iter]['contents_jp'] = $data['contents_jp'];
      $ARTICLES['list'][$iter]['contents_cn'] = $data['contents_cn'];

      $ARTICLES['list'][$iter]['view_permission'] = $data['view_permission'];

      ## 새로운공연전시 기능 추가 : 2012.04.17 오경우
      if (isset($this->module_config['use_event_skin']) && $this->module_config['use_event_skin'] == 'true') {
        if (!empty($data['contents_en'])) {
          $arr_week_korean = array('Sun' => '일', 'Mon' => '월', 'Tue' => '화', 'Wed' => '수', 'Thu' => '목', 'Fri' => '금', 'Sat' => '토');
          $ARTICLES['list'][$iter]['contents_en'] = substr($data['contents_en'], 0, 10) . ' (' . $arr_week_korean[date('D', strtotime($data['contents_en']))] . '요일)';
        }
        if (!empty($data['contents_cn'])) {
          $concert_list = unserialize(base64_decode($data['contents_cn']));
          $ARTICLES['list'][$iter]['contents_cn'] = NULL;
          for ($jter = 1; $jter <= 10; $jter++) {
            if (!empty($concert_list[$jter]['start']) && !empty($concert_list[$jter]['end'])) {
              $ARTICLES['list'][$iter]['contents_cn'] .= $jter . '회 ';
              $ARTICLES['list'][$iter]['contents_cn'] .= $concert_list[$jter]['start'];
              $ARTICLES['list'][$iter]['contents_cn'] .= ' ~ ';
              $ARTICLES['list'][$iter]['contents_cn'] .= $concert_list[$jter]['end'];
              $ARTICLES['list'][$iter]['contents_cn'] .= ($jter % 2 == 0 ? '<br />' : ',&nbsp;');
            }
          }
        }
      }


      ## 2014-08-11 황재복 : qna 답변 갯수
      if ($this->module_config['skin_style'] == 'qna' || $this->module_config['use_reply'] != "none") {
        $re_count_sql = 'SELECT COUNT(idx) AS cnt FROM ' . $this->module_config['table_name'] . ' WHERE pidx="' . $data['idx'] . '" AND level<>"0" AND del = "n" ';
        $re_count_val = $mysql->query_fetch($re_count_sql);
        $ARTICLES['list'][$iter]['reply_cnt'] = $re_count_val['cnt'];
      }

      ## 2014.08.04 오경우 추가 : 확장필드
      for ($vi = 1; $vi < 11; $vi++) {
        $ARTICLES['list'][$iter]['varchar_' . $vi] = isset($data['varchar_' . $vi]) ? $data['varchar_' . $vi] : '';
      }

      ## 등록현황
      if ($this->module_config['skin_style'] == 'reg_status') {
        for ($vi = 1; $vi < 16; $vi++) {
          $ARTICLES['list'][$iter]['varchar_' . $vi] = isset($data['varchar_' . $vi]) ? $data['varchar_' . $vi] : '';
        }

        $ARTICLES['list'][$iter]['contents'] = isset($data['contents']) ? $data['contents'] : '';
        $ARTICLES['list'][$iter]['reg_date'] = isset($data['reg_date']) ? $data['reg_date'] : '';
      }


      $ARTICLES['list'][$iter]['period_start'] = isset($data['period_start']) ? $data['period_start'] : NULL;      // 여행기간 시작일
      $ARTICLES['list'][$iter]['period_end'] = isset($data['period_end']) ? $data['period_end'] : NULL;          // 여행기간 종료일


      ######################################################################
      ## 20180628 상단팝업 host설정
      if ($this->module_config['skin_style'] == 'popup_color_multihost') {
        $ARTICLES['list'][$iter]['host_list'] = explode("|", $data['varchar_10']);
      }

      ## 최근글
      if ($iter < 3 && $page == 1) {
        $board_new_list .= '<li><a href="?idx=' . $data['idx'] . '&mode=view">' . $data['orignal_title'] . '</a></li>';
      }


      ## 2015.09.24 서희진 추가
      ## 후기 스킨일떄 여분필들에 값을 가져온다. 단체견학신청일 때
      if ($this->module_config['skin_style'] == 'epilogue' || $this->module_config['skin_style'] == 'request_event') {
        $ARTICLES['list'][$iter]['recommend_cnt'] = $data['recommend_cnt'];


        $ARTICLES['list'][$iter]['tourlist'] = explode("|", $data['varchar_1']);                      // 여행코스
        $ARTICLES['list'][$iter]['tour_object'] = isset($data['varchar_2']) ? explode("|", $data['varchar_2']) : '기타';  // 여행목적
        ## 통계를 위한 필드
        $ARTICLES['list'][$iter]['tour_object_etc'] = isset($data['varchar_3']) ? $data['varchar_3'] : NULL;        // 기타여행목적
        $ARTICLES['list'][$iter]['tour_age'] = isset($data['varchar_4']) ? $data['varchar_4'] : NULL;            // 연령
        $ARTICLES['list'][$iter]['tour_person'] = isset($data['varchar_5']) ? $data['varchar_5'] : NULL;          // 인원
        $ARTICLES['list'][$iter]['tour_method'] = isset($data['varchar_7']) ? explode("|", $data['varchar_7']) : '기타';  // 여행수단
        $ARTICLES['list'][$iter]['tour_method_etc'] = isset($data['varchar_8']) ? $data['varchar_8'] : NULL;        // 기타여행수단
        $ARTICLES['list'][$iter]['tour_return'] = isset($data['varchar_9']) ? $data['varchar_9'] : NULL;          // 재방문의사
        $ARTICLES['list'][$iter]['tour_return_term'] = isset($data['varchar_10']) ? $data['varchar_10'] : NULL;      // 재방문기간
        $ARTICLES['list'][$iter]['period_start'] = isset($data['period_start']) ? $data['period_start'] : NULL;      // 여행기간 시작일
        $ARTICLES['list'][$iter]['period_end'] = isset($data['period_end']) ? $data['period_end'] : NULL;          // 여행기간 종료일
      }


      ## 서희진 추가 : 외국어 모드 일떄 외국어제목 및 외국어내용 노출.
      if (isset($this->module_config['foreign_en_yn']) && $this->module_config['foreign_en_yn'] == 'y' && $this->hostname == 'toureng') {
        if (!empty($data['title_en'])) $ARTICLES['list'][$iter]['title'] = htmlspecialchars(str_replace('&', '&amp;', $data['title_en']));
      }


      ## 서희진 추가 : 외국어 모드 일떄 외국어제목 및 외국어내용 노출.
      if (isset($this->module_config['foreign_ch_yn']) && $this->module_config['foreign_ch_yn'] == 'y' && $this->hostname == 'tourch') {
        if (!empty($data['title_cn'])) $ARTICLES['list'][$iter]['title'] = htmlspecialchars(str_replace('&', '&amp;', $data['title_cn']));
      }


      ## 서희진 추가 : 외국어 모드 일떄 외국어제목 및 외국어내용 노출.
      if (isset($this->module_config['foreign_jp_yn']) && $this->module_config['foreign_jp_yn'] == 'y' && $this->hostname == 'tourjp') {
        if (!empty($data['title_jp'])) $ARTICLES['list'][$iter]['title'] = htmlspecialchars(str_replace('&', '&amp;', $data['title_jp']));
      }


      if ($this->module_config['use_tag'] == 'true') $ARTICLES['list'][$iter]['tag'] = $data['tag'];
      $ARTICLES['list'][$iter]['html_tag'] = isset($data['html_tag']) ? $data['html_tag'] : NULL;          // 여행기간 종료일

      ## 황재복 추가 : 리스트상에 log 안나오도록 수정
      $ARTICLES['list'][$iter]['log'] = '';
      if ($_SERVER['REMOTE_ADDR'] == '49.254.140.140' && $_SESSION['user_id'] == 'siha1997' && $data['idx'] == '1766') {
#    echo '<pre>'; print_r(unserialize(base64_decode($ARTICLES['list'][$iter]['contents']) )); exit;
      }

    } ## end for loop

    $ARTICLES['count'] = $count_list;


    $ARTICLES['keyword'] = $keyword;
    $ARTICLES['page_scale'] = $this->module_config['page_scale'];
    $ARTICLES['block_scale'] = $this->module_config['block_scale'];
    $ARTICLES['total_count'] = $total_count;
    $ARTICLES['page'] = $page;
    $ARTICLES['total_page'] = $total_page;
    $ARTICLES['list_msg'] = $this->module_config['list_msg'];
    $ARTICLES['old_link'] = $this->module_config['old_link'];
    $ARTICLES['list_msg_no_css'] = $this->module_config['list_msg_no_css'];
    $ARTICLES['board_name'] = $this->module_config['board_name'];
    $ARTICLES['permission'] = $this->permission;
    $ARTICLES['writer_display'] = $this->module_config['writer_display'];
    $ARTICLES['multi_delete'] = $this->module_config['multi_delete'];
    $ARTICLES['file_encrypt'] = $this->module_config['file_encrypt'];


    $ARTICLES['year'] = $year;

    if ($ARTICLES['writer_display'] == 'user_self') $ARTICLES['writer_display_user_self'] = $this->module_config['writer_display_user_self'];
    $ARTICLES['use_reg_date'] = $this->module_config['use_reg_date'];

    ## 이미지 사이즈 사용 하기 싶을
    $ARTICLES['img_size_width'] = isset($this->module_config['img_size_width']) ? $this->module_config['img_size_width'] : null;
    $ARTICLES['img_size_height'] = isset($this->module_config['img_size_height']) ? $this->module_config['img_size_height'] : null;


    if (isset($this->module_config['use_banner']) && $this->module_config['use_banner'] == 'true') {
      $ARTICLES['banner_size_width'] = $this->module_config['banner_size_width'];
      $ARTICLES['banner_size_height'] = $this->module_config['banner_size_height'];
      $ARTICLES['banner_allow_schedule'] = $this->module_config['banner_allow_schedule'];
    }

    if (!empty($this->module_config['use_list_size']) && $this->module_config['use_list_size'] == 'true') {
      $ARTICLES['list_size_width'] = $this->module_config['list_size_width'];
      $ARTICLES['list_size_height'] = $this->module_config['list_size_height'];
    }

    if ($this->module_config['use_view_comment'] == 'true') {
      $ARTICLES['table_name'] = $this->module_config['table_name'];
      $ARTICLES['board_id'] = $this->module_config['board_id'];
      $ARTICLES['board_name'] = $this->module_config['board_name'];
    }

    ## 검색 파라메터
    $arr_parameter = array();
    $arr_parameter['mode'] = $mode;

    if (!empty($category_1)) $arr_parameter['category_1'] = $category_1;
    if (!empty($sub_mode)) $arr_parameter['sub_mode'] = $sub_mode;
    if (!empty($arr_parameter)) $ARTICLES['search_parameter'] = $arr_parameter;

    ## 페이지네비 파라메터 추가
    if (!empty($search_type)) $arr_parameter['search_type'] = $search_type;
    if (!empty($search_word)) $arr_parameter['search_word'] = $search_word;
    if (!empty($page_scale)) $arr_parameter['page_scale'] = $page_scale;
    if (!empty($search_start_date)) $arr_parameter['start_date'] = $search_start_date;
    if (!empty($search_finish_date)) $arr_parameter['finish_date'] = $search_finish_date;
    if (!empty($search_date) && $search_date == 'y') $arr_parameter['search_date'] = $search_date;

    if (!empty($arr_parameter)) $ARTICLES['navi_parameter'] = $arr_parameter;

    ## 보기, 버튼 파라미터 추가
    if ($page > 1) $arr_parameter['page'] = $page;
    if (!empty($arr_parameter)) $ARTICLES['parameter'] = $arr_parameter;


    ## 인기글 --------------
    if ($this->module_config['user_list_hot'] == "true") {
      $apcu_key_hot = 'hot_' . $this->module_config['board_id'];
      apcu_delete($apcu_key_hot);
      $ARTICLES['user_list_hot'] = $this->module_config['user_list_hot'];
      $ARTICLES['hot_articles_data'] = apcu_fetch($apcu_key_hot);
      if (empty($ARTICLES['hot_articles_data'])) {
        $ARTICLES['hot_articles_data'] = $this->hot_articles($this->module_config['table_name'], $this->module_config['board_id'], $this->module_config['list_hot_count']);
        apcu_add($apcu_key_hot, $ARTICLES['hot_articles_data'], 86400);
      }
    }

    ##----------------------

    ## skin 설정
    $ARTICLES['sub_mode'] = $sub_mode;
    $ARTICLES['device'] = $this->device;
    $ARTICLES['module_root'] = $this->module_root;                  ## 근본적으로 스킨쪽에서 세팅되어야 한다.
    $ARTICLES['module_path'] = $this->module_config['module_path']; ## 근본적으로 스킨쪽에서 세팅되어야 한다.
    $ARTICLES['skin_style'] = empty($this->module_config['skin_style']) ? 'board' : $this->module_config['skin_style'];
    $ARTICLES['skin_name'] = ($sub_mode == 'excel') ? 'excel' : 'list';


    if ($this->module_config['skin_style'] == "book_search" && ($mode == "photo" || empty($mode))) {
      $ARTICLES['skin_name'] = "photo";
    }

    $ARTICLES['use_comment'] = $this->module_config['use_comment'];

    ## 데이타 ajax으로 가져가기 위해서 추가.
    $return = $this->get_parameter('return');

    if ($return == "json") {

      $ARTICLES['json_navi_parameter'] = $this->make_GET_parameter($arr_parameter, '&amp;', true);
      ob_clean();
      $ARTICLES = call::SafeFilterSTR($ARTICLES);
      echo json_encode($ARTICLES);
      exit;
    }



    ## ========================================================
    echo serialize($ARTICLES);

    return true;
  }

  ## 보기
  public function view()
  {
    global $mysql;
    global $_SYSTEM;
    $ARTICLES = array();
    $data = array();
    $data_file = array();
    $encryption = new yb_crypt();

    ## parameter setting.
    $idx = $this->get_parameter('idx');
    $page = $this->get_parameter('page');
    $page = empty($page) ? 1 : $page;
    ## 댓글 페이징 parameter - 20170919 서희진 추가
    $cpage = $this->get_parameter('cpage');
    $cpage = empty($cpage) ? 1 : $cpage;

    $mode = $this->get_parameter('mode');
    $search_date = $this->get_parameter('search_date');
    $search_type = $this->get_parameter('search_type');
    $search_word = $this->get_parameter('search_word');
    $start_date = $this->get_parameter('start_date');
    $finish_date = $this->get_parameter('finish_date');
    $category_1 = $this->get_parameter('category_1');
    ## 모바일 버전사용 일반스킨 레이아웃 상세보기 닫기 버튼 hidden or show 처리를 위한 변수 김경남 17.12.14
    $btoff = $this->get_parameter('btoff');

    ## ====================================================
    ##               validation check start
    ## =====================f===============================

    if (empty($idx)) call::xml_error('152', '', $this->referer);


    /*      보기페이지에서 관리자 설정 저장을 위한 부분. 관리자 설정 부분은 추후 추가/수정될 부분이다.

		## 관리자 설정 적용일 경우 넘어온 설정값을 update한다.
		## 이부분을 jquery ajax로 변경한다.
		if($this->permission['admin'] === true && $_GET['admin_save'] == 'true') {
			if($this->admin_save($idx, $_GET) !== true) call::xml_error('154', '관리자 설정을 저장하는중에 오류가 발생하였습니다.', $this->referer);
		}
*/


    ## 게시물정보 추출.
    $query = 'SELECT a.*, b.visit_cnt ';
    $query .= 'FROM ' . $this->module_config['table_name'] . ' AS a ';
    $query .= 'LEFT JOIN _visit_info AS b ';
    $query .= 'ON b.table_name = "' . $this->module_config['table_name'] . '" AND a.idx = b.table_idx ';
    if ($this->module_config['skin_style'] == 'schedule') {
      $query .= '  INNER JOIN _schedule AS c';
      $query .= '    ON c.board_id=a.board_id AND c.board_idx=a.idx ';
    }
    $query .= 'WHERE a.idx=' . $idx;



    $data = $mysql->query_fetch($query);

    $data['title'] = stripslashes($data['title']);  // 2013.11.01 정운영추가 '에 \\\\가 붙어나오는것을 방지
    $data['contents'] = stripslashes($data['contents']);

    ## 디비에 값이 없을 경우 에러처리
    if (empty($data)) call::xml_error('152', '', $this->referer);

    ## tag 적용
    if ($this->module_config['use_tag'] == 'true') {
      if (!empty($this->module_config['source_id'])) $this->module_config['board_id'] = $this->module_config['source_id'];
    }

    ## 모듈 카테고리와 디비의 모듈카테고리가 같지 않는경우 에러처리.
    ## 2018-10-01 황재복 : 하드코딩이다 ㅠㅠ 한국어에서 영문, 일문 중문 다 입력된 상태, 각 모듈별로 보이도록
    if ($this->module_config['board_id'] != $data['board_id']) call::xml_error('152', '', $this->referer);

    ## 삭제글 체크 : remove 된 글이면 사이트 관리자가 아닌경우 블록한다.
    //2012-08-30 황재복 : 관리자 삭제 사유 사용시 자신이 등록한 글일 경우 삭제되더라도 확인 가능해야함.
    if ($this->module_config['use_delete_reason'] == 'true') {
      if ($data['del'] == 'y' && $this->permission['admin'] !== true && $data['reg_pin'] != $this->myinfo['my_pin']) call::xml_error('153', '', $this->referer);
    } else {
      if ($data['del'] == 'y' && $this->permission['admin'] !== true) call::xml_error('153', '', $this->referer);
    }

    ## 비공개글 체크 : 비공개글일 경우 사이트 관리자, 게시판 관리자, 글작성자가 아닐경우 블록한다.
    ## 비회원이 쓴 글일 경우 패스워드 확인 과정을 거친다. 비공개글 추가 수정 : 오경우
    if ($this->module_config['use_lock'] == 'true' && $data['open'] != 'y' && $this->permission['admin'] !== true && $this->myinfo['my_pin'] != $data['reg_pin'] && $this->module_config['board_id'] == 'atec_counsel_old') { ##200713 귀농귀촌상담(구) 게시판관련 특수처리
      $passwd = $this->get_parameter('passwd');
      if (empty($idx)) call::xml_error('152', '', $this->referer);
      if (empty($passwd)) call::xml_error('152', '', $this->referer);
      $query = sprintf('SELECT count(*) as cnt FROM %s WHERE idx = "%s" AND passwd = OLD_PASSWORD("%s") ', $this->module_config['table_name'], $idx, $passwd);
      $check_data = $mysql->query_fetch($query);
      $check_cnt = empty($check_data['cnt']) ? 0 : $check_data['cnt'];
      if ($check_cnt < 1) call::xml_error('98', '비밀번호를 확인하세요!', $this->referer);
    } else {
      if ($this->module_config['use_lock'] == 'true' && $data['open'] != 'y' && $this->permission['admin'] !== true && $this->myinfo['my_pin'] != $data['reg_pin'] && $this->module_config['skin_style'] != 'approval' && $this->module_config['skin_style'] != 'regulation' && $this->module_config['skin_style'] != 'solicit' && $this->module_config['skin_style'] != 'approval_ex' && $this->module_config['skin_style'] != 'approval_health' && $this->module_config['skin_style'] != 'solicit_cleen' && $this->module_config['skin_style'] != 'address_regulation' && $this->module_config['skin_style'] != 'monitor') {

        if ($this->module_config['board_id'] == 'www_singo' && $this->module_config['skin_style'] == 'ys_singo' && $data['charge_id'] == $this->myinfo['user_id']) {
          ## business 에서 신고게시판 처리 - 담당자처리할수있도록
        } else {
          if ($data['reg_pin'] != GUEST_PIN) call::xml_error('203', '본인 요청에 의한 비공개 상담글 입니다..', $this->referer);
          if ($this->confirm_logoff_passwd($data['idx'], $data['passwd']) !== true) return false;
        }
      }
    }

    ## 비로그인 회원 글쓰기에 사용된 세션값 삭제
    if (isset($_SESSION['logoff_confirm'])) unset($_SESSION['logoff_confirm']);

    ## 승인/비승인 체크
    //if($this->module_config['use_allow'] == 'true' && $data['allow'] != 'y' && $this->permission['admin'] !== true && $this->myinfo['my_pin'] != $data['reg_pin']) call::xml_error('203','비승인글입니다.',$this->referer);
    if ($data['allow'] != 'y' && $this->permission['admin'] !== true && $this->myinfo['my_pin'] != $data['reg_pin']) call::xml_error('203', '비승인글입니다.', $this->referer);


    ## 멀티호스트 : 사용하고 있지 않아서 주석처리 - 20170919 서희진
    //if($this->module_config['use_multihost'] == 'true' && $this->hostname != $data['hostname']) call::xml_error('202','',$this->referer);

    ## ====================================================
    ##               validation check end
    ## ====================================================

    ## 댓글설정
    if ($this->module_config['use_comment'] == 'true') {
      $ARTICLES['use_comment'] = $this->module_config['use_comment'];
      $ARTICLES['comment_type'] = $this->module_config['comment_type'];
      $data_comment = array();
      $data_comment['se_mode'] = $this->get_parameter('se_mode');
      $data_comment['comment_type'] = $this->module_config['comment_type'];
      $data_comment['table_name'] = $this->module_config['table_name'];
      $data_comment['table_idx'] = $idx;
      $data_comment['comment_idx'] = $_GET['comment_idx'];
      $data_comment['comment'] = $_POST['comment'];
      $data_comment['point'] = $_POST['point'];
      $data_comment['reg_name'] = $_POST['reg_name'];

      /*$ARTICLES['comment_list'] = $this->comment($data_comment);
			$ARTICLES['comment_list']['count'] = count($ARTICLES['comment_list']);*/
      ## 댓글 페이징 쿼리 - 20170919 서희진 추가
      $ARTICLES['comment_page_scale'] = 5;
      $climit = ' LIMIT ' . $ARTICLES['comment_page_scale'] * ($cpage - 1) . ', ' . $ARTICLES['comment_page_scale'];
      $ARTICLES['comment_list'] = $this->comment($data_comment, $climit);
    }

    ## 분류 설정
    if ($this->module_config['use_category_1'] == 'true' && is_array($this->module_config['category_1'])) {
      $ARTICLES['use_category_1'] = $this->module_config['use_category_1'];
      $ARTICLES['category_1'] = $data['category_1'];
    }

    ## 처리상태
    if ($this->module_config['use_process_1'] == 'true' && is_array($this->module_config['process_1'])) {
      $ARTICLES['use_process_1'] = $this->module_config['use_process_1'];
      $ARTICLES['process_1'] = $data['process_1'];
    }
    $ARTICLES['process_3'] = $data['process_3'];

    ## 승인/비승인
    //if($this->module_config['use_allow'] == 'true') {
    $ARTICLES['use_allow'] = $this->module_config['use_allow'];
    $ARTICLES['allow'] = $data['allow'];
    //}

    ## 비공개글
    if ($this->module_config['use_lock'] == 'true') {
      $ARTICLES['use_lock'] = $this->module_config['use_lock'];
      $ARTICLES['open'] = $data['open'];
    }

    ## 관리자일 경우 보여주는 정보. : 비회원 정보 (주소, 전화번호) 등등
    if ($this->permission['admin'] === true || $data['reg_pin'] == $this->myinfo['my_pin']) {
      $ARTICLES['reg_ip'] = $data['reg_ip'];
      $ARTICLES['zipcode'] = $data['zipcode'];
      $ARTICLES['address_1'] = $data['address_1'];
      $ARTICLES['address_2'] = $data['address_2'];
      $ARTICLES['phone'] = $encryption->decrypt($data['phone']);

      $this->permission['modify'] = true;
      $this->permission['remove'] = true;
    }

    ## 20181119 업소현황에서는 연락처 및 주소지가 보여야해서 추가
    if ($this->module_config['skin_style'] == 'announce' || $this->module_config['skin_style'] == 'hygiene' || $this->module_config['skin_style'] == 'educational_status' || $this->module_config['board_id'] == 'www_newsletter_req' || $this->module_config['skin_style'] == 'voucher_apply' || $this->module_config['skin_style'] == 'reserve_counseling' || $this->module_config['skin_style'] == 'yeosun_incident' || $this->module_config['skin_style'] == 'mush_farmer') {
      $ARTICLES['address_1'] = $data['address_1'];
      $ARTICLES['address_2'] = $data['address_2'];
      $ARTICLES['phone'] = $data['phone'];
    }


    ## 20140624 회원글쓰기일경우 등록자정보추출
    if (!empty($data['reg_id']) && empty($data['phone'])) {
      $encryption = new yb_crypt();
      $query = sprintf('SELECT * FROM _member WHERE user_id = "%s"', $data['reg_id']);
      $m_data = array();
      $m_data = $mysql->query_fetch($query);

      $ARTICLES['register_phone'] = $encryption->decrypt($m_data['phone']);
    }


    ######################################################################
    ## 멀티호스트 설정
    if ($this->module_config['use_multihost'] == 'true') {
      $ARTICLES['use_multihost'] = $this->module_config['use_multihost'];
      $ARTICLES['host_list_all'] = $this->get_site_host_list();
      $ARTICLES['host_list'] = explode("|", $data['varchar_10']);

    }
    ######################################################################

    ## 2012.03.22 오경우 추가
    if ($this->permission['admin'] != true && $this->module_config['use_hide_name_all'] == 'true') $data['reg_name'] = call::strcut($data['reg_name'], 1, 'OO');

    ## 비로그인 회원 글쓰기 기능
    if ($this->module_config['use_logoff_write'] == 'true') $ARTICLES['use_logoff_write'] = $this->module_config['use_logoff_write'];

    $ARTICLES['idx'] = $idx;
    $ARTICLES['visit_cnt'] = empty($data['visit_cnt']) ? 0 : $data['visit_cnt'];
    $ARTICLES['reg_name'] = $data['reg_name'];
    $ARTICLES['depart_name'] = $data['depart_name'];
    $ARTICLES['reg_date'] = $data['reg_date'];
    $ARTICLES['modify_date'] = $data['modify_date'];
    $ARTICLES['mod_date'] = $data['mod_date'];
    $ARTICLES['del'] = $data['del'];
    $ARTICLES['open'] = $data['open'];
    $ARTICLES['title'] = $data['title'];
    $ARTICLES['reg_pin'] = $data['reg_pin'];
    $ARTICLES['reg_id'] = $data['reg_id'];
    $ARTICLES['category_3'] = $data['category_3'];
    $ARTICLES['phone'] = $data['phone'];
    $ARTICLES['tel'] = $data['tel'];
    $ARTICLES['number'] = $data['number'];
    $ARTICLES['phone_1'] = substr($ARTICLES['phone'], 0, 3);
    $ARTICLES['phone_2'] = substr($ARTICLES['phone'], 4, 4);
    $ARTICLES['phone_3'] = substr($ARTICLES['phone'], 9, 4);


    if ($this->module_config['board_id'] == 'www_newsletter_req'
      || $this->module_config['board_id'] == 'www_study_circle' || $this->module_config['board_id'] == 'www_press'
      || $this->module_config['board_id'] == 'www_clarify' || $this->module_config['skin_style'] == 'ys_bodo' || $this->module_config['skin_style'] == 'ys_land_check' || $this->module_config['skin_style'] == 'ys_request_insentive') {
      $ARTICLES['phone'] = $data['phone'];
    } else {
      $ARTICLES['phone'] = $encryption->decrypt($data['phone']);
    }

    $ARTICLES['email'] = $data['email'];
    $ARTICLES['organ'] = $data['organ'];
    $ARTICLES['sex'] = $data['sex'];
    $ARTICLES['age'] = $data['age'];
    $ARTICLES['hphone'] = $data['hphone'];
    $ARTICLES['level'] = $data['level'];
    $ARTICLES['top_start'] = $data['top_start'];
    $ARTICLES['top_end'] = $data['top_end'];
    $ARTICLES['nearby_articles'] = array(0 => NULL, 1 => NULL);

    $referCate = array('category_1' => $category_1);
    if (empty($referCate['category_1']) && parse_url($_SERVER['PHP_SELF'])['path'] == parse_url($_SERVER['HTTP_REFERER'])['path']) {
      parse_str(parse_url($_SERVER['HTTP_REFERER'])['query'], $referCate);
    }

    $ARTICLES['nearby_articles_data'] = $this->nearby_articles($this->module_config['table_name'], $this->module_config['board_id'], $data['idx'], $data['top'], $referCate['category_1']);
    foreach ($ARTICLES['nearby_articles_data'] as $nearby_key => $nearby_data) {
      if ($nearby_data['type'] == 'prev') {
        $ARTICLES['nearby_articles'][1] = $nearby_data;
      }
      if ($nearby_data['type'] == 'next') {
        $ARTICLES['nearby_articles'][0] = $nearby_data;
      }
    }

    if ($this->module_config['skin_style'] == 'reserve') {
      $ARTICLES['phone'] = $data['phone'];
    }
    $ARTICLES['permission'] = $this->permission;
    $ARTICLES['writer_display'] = $this->module_config['writer_display'];

    if (isset($this->module_config['use_banner']) && $this->module_config['use_banner'] == 'true') $ARTICLES['link_url'] = $data['link_url'];

    ## 최근글(3개)
    #$ARTICLES['board_new_list'] = file_get_contents($this->module_config['new_list_filename']);


    ## 2015.09.24 서희진 추가
    ## 후기 스킨일떄 여분필들에 값을 가져온다.
    if ($this->module_config['skin_style'] == 'epilogue') {
//			$ARTICLES['tour_list'] = unserialize(base64_decode($data['varchar_1']));
      $ARTICLES['tourlist'] = explode("|", $data['varchar_1']);                      // 여행코스
      $ARTICLES['tour_object'] = isset($data['varchar_2']) ? explode("|", $data['varchar_2']) : '기타';  // 여행목적
      ## 통계를 위한 필드
      $ARTICLES['tour_object_etc'] = isset($data['varchar_3']) ? $data['varchar_3'] : NULL;        // 기타여행목적
      $ARTICLES['tour_age'] = isset($data['varchar_4']) ? $data['varchar_4'] : NULL;            // 연령
      $ARTICLES['tour_person'] = isset($data['varchar_5']) ? $data['varchar_5'] : NULL;          // 인원
      $ARTICLES['tour_method'] = isset($data['varchar_7']) ? explode("|", $data['varchar_7']) : '기타';  // 여행수단
      $ARTICLES['tour_method_etc'] = isset($data['varchar_8']) ? $data['varchar_8'] : NULL;        // 기타여행수단
      $ARTICLES['tour_return'] = isset($data['varchar_9']) ? $data['varchar_9'] : NULL;          // 재방문의사
      $ARTICLES['tour_return_term'] = isset($data['varchar_10']) ? $data['varchar_10'] : NULL;      // 재방문기간
      $ARTICLES['period_start'] = isset($data['period_start']) ? $data['period_start'] : NULL;      // 여행기간 시작일
      $ARTICLES['period_end'] = isset($data['period_end']) ? $data['period_end'] : NULL;          // 여행기간 종료일
    } else if ($this->module_config['skin_style'] == 'open_measure') {
      $ARTICLES['period_start'] = isset($data['period_start']) ? $data['period_start'] : NULL;      // 게시기간 설정 시작일
      $ARTICLES['period_end'] = isset($data['period_end']) ? $data['period_end'] : NULL;          // 게시기간 설정 종료일
    }


    // 민원처리용 추가 : 정운영 (20120219)
    $ARTICLES['view_permission'] = 'false';
    if (!empty($this->myinfo['user_id'])) {
      if (preg_match('/' . $this->myinfo['user_id'] . ';/', $data['view_id'])) {
        $ARTICLES['view_permission'] = 'true';
      }
    }
    $ARTICLES['view_id'] = $data['view_id'];

    if ($this->module_config['skin_style'] == 'approval' || $this->module_config['skin_style'] == 'regulation' || $this->module_config['skin_style'] == 'solicit' || $this->module_config['skin_style'] == 'approval_ex' || $this->module_config['skin_style'] == 'approval_health' || $this->module_config['skin_style'] == 'solicit_cleen' || $this->module_config['skin_style'] == 'address_regulation' || $this->module_config['skin_style'] == 'monitor') {

      // 2023.01.10 최무성 추가
      $ARTICLES['address_1'] = $data['address_1'];
      $ARTICLES['address_2'] = $data['address_2'];

      $ARTICLES['process_2'] = $data['process_2'];
      $ARTICLES['process_3'] = $data['process_3'];
      $ARTICLES['approval']['status'] = '0';

      $a_result = $mysql->query('SELECT b.* FROM 
					(SELECT DISTINCT approval_line FROM `_approval` WHERE `board_id`="' . $this->module_config['board_id'] . '" AND `board_idx`=' . $data['idx'] . ' and step >0 ORDER BY approval_line ASC) AS a LEFT JOIN 
					(SELECT * FROM `_approval` WHERE `board_id`="' . $this->module_config['board_id'] . '" AND `board_idx`=' . $data['idx'] . ' ORDER BY step DESC ) AS b ON a.approval_line= b.approval_line
					GROUP BY a.approval_line');

      while ($a_data = $mysql->fetch_array($a_result)) {

        if (!empty($a_data['user_id'])) {
          global $member;
          $a_staff = $member->get_member_info($a_data['user_id']);
          $a_data['dept_master'] = $a_staff['dept_master'];
          $a_data['dept_tel'] = $a_staff['dept_tel'];
        }
        $ARTICLES['approval']['list'][] = $a_data;

        $b_result = $mysql->query('SELECT idx, approval_line, step, user_name,user_id, dept_path_title, comment, reply, process, processing_date, modi, dept_posname FROM  `_approval` WHERE `board_id`="' . $this->module_config['board_id'] . '" AND `board_idx`=' . $data['idx'] . ' AND approval_line="' . $a_data['approval_line'] . '" ORDER BY step ASC');
        while ($b_data = $mysql->fetch_array($b_result)) {
          if (!empty($b_data['user_id'])) {
            global $member;
            $b_staff = $member->get_member_info($b_data['user_id']);
            $b_data['dept_master'] = $b_staff['dept_master'];
            $b_data['dept_tel'] = $b_staff['dept_tel'];
          }
          $ARTICLES['approval']['approval_' . $a_data['approval_line']][] = $b_data;

          if ($b_data['process'] == 'complete' || $b_data['process'] == 'approval') {
            $ARTICLES['approval']['status'] = '8';
          }
          $ARTICLES['approval']['last_process'] = $b_data['process'];
          $ARTICLES['approval']['last_reply'] = $b_data['reply'];
          $ARTICLES['approval']['last_date'] = $b_data['processing_date'];
          $ARTICLES['approval']['last_dept'] = $b_data['dept_path_title'];
          $ARTICLES['approval']['last_name'] = $b_data['user_name'];
          $ARTICLES['approval']['dept_posname'] = $b_data['dept_posname']; ## 2023.04.11 이진주 추가
        }
      }

      // 민원처리용 추가 : 정운영 (20120219)
      if ($ARTICLES['approval']['status'] == '0') {
        $approval_status = $mysql->query_fetch('SELECT max(step) as max_step FROM  `_approval` WHERE  `board_id`="' . $this->module_config['board_id'] . '" AND `board_idx`=' . $data['idx'] . '');
        $ARTICLES['approval']['status'] = $approval_status['max_step'];
      }

      $deadline = $mysql->query_fetch('SELECT deadline FROM `_approval` WHERE board_idx="' . $data['idx'] . '" AND board_id="' . $this->module_config['board_id'] . '" AND approval_line="0" AND step="0"');
      $ARTICLES['approval']['deadline'] = $deadline['deadline'];

      ## 2014.12.12 오경우 추가 : 신고게시판 답변 첨부파일 기능 추가
      $ARTICLES['approval']['file_list'] = $this->attached_file_list_approval($idx);
      $ARTICLES['approval']['board_id'] = $this->module_config['board_id'];
    }

    $damdang = 'false';
    foreach ($ARTICLES['approval'] as $list) {
      foreach ($list as $ar) {
        if ($ar['user_id'] == $this->myinfo['user_id'] && !empty($ar['user_id'])) {
          $damdang = 'true';
        }
      }
    }

    $view_id = explode(';', $ARTICLES['view_id']);
    if (!empty($this->myinfo['user_id']) && in_array($this->myinfo['user_id'], $view_id)) $damdang = 'true';

    if ($damdang == 'true') $ARTICLES['reg_name'] = $data['reg_name'];


    if ($this->module_config['use_banner'] == 'true') {
      $ARTICLES['banner_size_width'] = $this->module_config['banner_size_width'];
      $ARTICLES['banner_size_height'] = $this->module_config['banner_size_height'];
      $ARTICLES['banner_allow_schedule'] = $this->module_config['banner_allow_schedule'];
    }

    if (!empty($this->module_config['use_list_size']) && $this->module_config['use_list_size'] == 'true') {
      $ARTICLES['list_size_width'] = $this->module_config['list_size_width'];
      $ARTICLES['list_size_height'] = $this->module_config['list_size_height'];
    }


    $ARTICLES['linkbox'] = unserialize(base64_decode($data['linkbox']));

    ## 관리자 메모
    if ($data['admin_comment']) {
      if ($data['admin_comment_to'] == 'all') $ARTICLES['admin_comment'] = $data['admin_comment'];
      if ($this->permission['admin'] === true || $this->myinfo['my_pin'] == $data['reg_pin']) {
        $ARTICLES['admin_comment'] = $data['admin_comment'];
        $ARTICLES['admin_comment_to'] = $data['admin_comment_to'];
      }
      if (!empty($ARTICLES['admin_comment'])) $ARTICLES['admin_comment'] = str_replace("\n", '<br />', $data['admin_comment']);
    }


    ## *************** 필요할 경우 컨텐츠 내용 뽑는 부분에 가공하는 부분을 더 추가한다. (ex : xss 등)
    ## 2012.07.25 오경우 수정 : 웹편집기를 사용할 경우 nl2br을 사용하면 안된다.
    //$data['contents'] = htmlentities($data['contents'], ENT_QUOTES | ENT_IGNORE, "UTF-8");;
    //$ARTICLES['contents'] = str_replace("\n", '<br />',$data['contents']);

    ## 2018.06.29 서희진 수정 : 이전 데이타에 html태그가 포함되어 잇는 글은. DB field에 html_tag값을 y로 설정후 nl2br안하고 뿌린다.
    if ($data['html_tag'] == "y") {
      $ARTICLES['contents'] = $data['contents'];
    } elseif ($data['html_tag'] == "a") {
      $ARTICLES['contents'] = $data['contents_original'];
    } else {
      $ARTICLES['contents'] = $this->module_config['use_editor'] == 'true' ? $data['contents'] : nl2br(strip_tags($data['contents']));
    }

    $ARTICLES['html_tag'] = $data['html_tag'];
    //$ARTICLES['contents'] = $this->module_config['use_editor'] == 'true' ? $data['contents'] : nl2br($data['contents']);

    ##******************************************************************************************
    //첨부파일기능 수정 : 오경우 (20120125)
    ## 첨부파일 리스트

    $ARTICLES['file_upload_count'] = $this->module_config['file_upload_count'];
    if (!empty($idx)) {
      $file_list = $this->attached_file_list($idx);
      $ARTICLES['file_list'] = $file_list;
      $ARTICLES['img_path'] = $_SERVER['PHP_SELF'] . '/ybmodule.file' . $this->module_config['path'] . '/' . $this->module_config['board_id'] . '/';
    }

    foreach ($ARTICLES['file_list'] as $file_buffer) {
      $ARTICLES['file_idxs'][] = $file_buffer['idx'];
    }

    //2016-04-07 황재복 : ebook 제작 안되었을 경우 제작
    if ($this->module_config['ebook_use'] == 'true') {
      for ($fi = 0; $fi < count($ARTICLES['file_list']); $fi++) {
        if (empty($ARTICLES['file_list'][$fi]['ebook_code']) && $ARTICLES['file_list'][$fi]['file_type'] == 'file') {
          $this->getPlugin('digitomi')->upload_before($ARTICLES['idx'], $ARTICLES['file_list'][$fi]['idx'], $ARTICLES['file_list'][$fi]['original_name']);
          $this->getPlugin('digitomi')->write_before();
        }
      }
    }


    //첨부파일기능 수정 : 오경우 (20120125)
    $ARTICLES['use_reply'] = empty($this->module_config['use_reply']) ? 'none' : $this->module_config['use_reply'];

    // 섬네일 목록 숨기기 : 오경우 (20120628) - 갤러리 스킨에서 사용되고 있다. 목포문학관 소장유품에서 다운로드를 막기 위해서 추가함.
    $ARTICLES['hidden_attach'] = $this->module_config['hidden_attach'] == 'true' ? $this->module_config['hidden_attach'] : 'none';


    //관리자 기능 추가 : 오경우 (20120214)
    if ($this->permission['admin'] === true) {
      $this->admin_tools($idx);
      $ARTICLES['article_move_out'] = empty($this->module_config['article_move_out']) ? 'true' : $this->module_config['article_move_out'];
      $ARTICLES['admin_comment_to'] = empty($data['admin_comment_to']) ? 'writer' : $data['admin_comment_to'];
      if ($this->module_config['use_top'] == 'true') {
        $ARTICLES['use_top'] = $this->module_config['use_top'];
        $ARTICLES['top'] = empty($data['top']) ? 'n' : $data['top'];
        $ARTICLES['top_start'] = $data['top_start'];
        $ARTICLES['top_end'] = $data['top_end'];
      }
      if ($this->module_config['use_process_1'] == 'true' && is_array($this->module_config['process_1'])) $ARTICLES['process_1_list'] = serialize($this->module_config['process_1']);
    }

    $ARTICLES['top_start'] = empty($data['top_start']) ? date('Y-m-d') : date("Y-m-d", strtotime($data['top_start']));
    $ARTICLES['top_end'] = empty($data['top_end']) ? date('Y-m-d', strtotime('+7 day')) : date("Y-m-d", strtotime($data['top_end']));

    $ARTICLES['contents_en'] = $data['contents_en'];
    $ARTICLES['contents_jp'] = $data['contents_jp'];
    $ARTICLES['contents_cn'] = $data['contents_cn'];
    ## 새로운공연전시 기능 추가 : 2012.04.17 오경우
    if ($this->module_config['use_event_skin'] == 'true' && !empty($data['contents_cn'])) {
      $arr_week_korean = array('Sun' => '일', 'Mon' => '월', 'Tue' => '화', 'Wed' => '수', 'Thu' => '목', 'Fri' => '금', 'Sat' => '토');
      $ARTICLES['concert_week'] = $arr_week_korean[date('D', strtotime($data['contents_en']))] . '요일';
      $concert_list = unserialize(base64_decode($data['contents_cn']));
      for ($iter = 1; $iter <= 10; $iter++) {
        $ARTICLES['concert_' . $iter . '_start'] = $concert_list[$iter]['start'];
        $ARTICLES['concert_' . $iter . '_end'] = $concert_list[$iter]['end'];
      }
    }

    ## QNA 또는 답변기능이 있을떄
    if ($this->module_config['skin_style'] == 'qna' || $this->module_config['use_reply'] != "none") {
      $re_query = 'SELECT * FROM ' . $this->module_config['table_name'] . ' WHERE pidx="' . $data['idx'] . '" AND level<>"0" AND del="n"';
      $re_result = $mysql->query($re_query);

      $itr = 0;
      while ($reply = $mysql->fetch_array($re_result)) {
        $ARTICLES['reply'][$itr]['idx'] = $reply['idx'];
        $ARTICLES['reply'][$itr]['title'] = $reply['title'];
        $ARTICLES['reply'][$itr]['contents'] = $reply['contents'];
        $ARTICLES['reply'][$itr]['reg_name'] = $reply['reg_name'];
        $ARTICLES['reply'][$itr]['reg_date'] = $reply['reg_date'];
        $ARTICLES['reply'][$itr]['reg_id'] = $reply['reg_id'];
        $ARTICLES['reply'][$itr]['file_list'] = $this->attached_file_list($reply['idx']);
        $itr++;
      }
    }


    ## banner와 유튜브 게시판에서 사용
    if (!empty($data['link_url'])) $ARTICLES['link_url'] = stripslashes($data['link_url']);

    $ARTICLES['youtibe_movie_width'] = $this->module_config['youtibe_movie_width'];
    $ARTICLES['youtibe_movie_height'] = $this->module_config['youtibe_movie_height'];

    ## 2014.08.04 오경우 확장필드 추가
    $ARTICLES['varchar_1'] = $data['varchar_1'];
    $ARTICLES['varchar_2'] = $data['varchar_2'];
    $ARTICLES['varchar_3'] = $data['varchar_3'];
    $ARTICLES['varchar_4'] = $data['varchar_4'];
    $ARTICLES['varchar_5'] = $data['varchar_5'];
    $ARTICLES['varchar_6'] = $data['varchar_6'];
    $ARTICLES['varchar_7'] = $data['varchar_7'];
    $ARTICLES['varchar_8'] = $data['varchar_8'];
    $ARTICLES['varchar_9'] = $data['varchar_9'];
    $ARTICLES['varchar_10'] = $data['varchar_10'];
    $ARTICLES['varchar_11'] = $data['varchar_11'];
    $ARTICLES['varchar_12'] = $data['varchar_12'];
    $ARTICLES['varchar_13'] = $data['varchar_13'];
    $ARTICLES['varchar_14'] = $data['varchar_14'];
    $ARTICLES['varchar_15'] = $data['varchar_15'];
    $ARTICLES['period_start'] = isset($data['period_start']) ? $data['period_start'] : NULL;      // 게시기간 설정 시작일
    $ARTICLES['period_end'] = isset($data['period_end']) ? $data['period_end'] : NULL;          // 게시기간 설정 종료일
    if ($this->module_config['skin_style'] == 'minwon_form') {
      $ARTICLES['longtext_1'] = $data['longtext_1'];
      $ARTICLES['longtext_2'] = $data['longtext_2'];
      $ARTICLES['longtext_3'] = $data['longtext_3'];
      $ARTICLES['longtext_4'] = $data['longtext_4'];
      $ARTICLES['longtext_5'] = $data['longtext_5'];
      $ARTICLES['longtext_6'] = $data['longtext_6'];
    }


    if ($this->module_config['skin_style'] == 'ys_singo' || $this->module_config['skin_style'] == 'ys_singo_coast' || $this->module_config['skin_style'] == 'ys_reserve_minwon') {
      $ARTICLES['petition'] = isset($data['petition']) ? $data['petition'] : NULL;
      $ARTICLES['c_phone'] = addslashes($encryption->decrypt($data['c_phone']));
      $ARTICLES['c_tel'] = addslashes($encryption->decrypt($data['c_tel']));
      $ARTICLES['c_email'] = isset($data['c_email']) ? addslashes($encryption->decrypt($data['c_email'])) : NULL;
      $ARTICLES['c_zipcode'] = addslashes($encryption->decrypt($data['c_zipcode']));
      $ARTICLES['c_address_1'] = isset($data['c_address_1']) ? addslashes($encryption->decrypt($data['c_address_1'])) : NULL;
      $ARTICLES['c_address_2'] = isset($data['c_address_2']) ? addslashes($encryption->decrypt($data['c_address_2'])) : NULL;
      $ARTICLES['c_varchar_1'] = isset($data['c_varchar_1']) ? addslashes($encryption->decrypt($data['c_varchar_1'])) : NULL;
      $ARTICLES['c_varchar_2'] = isset($data['c_varchar_2']) ? addslashes($encryption->decrypt($data['c_varchar_2'])) : NULL;
      $ARTICLES['c_varchar_3'] = isset($data['c_varchar_3']) ? addslashes($encryption->decrypt($data['c_varchar_3'])) : NULL;

    }

    ## 2015.10.13 윤지미 처리상태 표시를 위해 필드 추가
    if ($this->module_config['skin_style'] == 'shop_registration') {
      $ARTICLES['admin_comment'] = isset($data['admin_comment']) ? str_replace("\n", '<br />', $data['admin_comment']) : NULL;
      $ARTICLES['process_1'] = isset($data['process_1']) ? $data['process_1'] : NULL;
    }


    if ($this->module_config['skin_style'] == 'sewol') { // 20171025 세월호 유류품 목록
      $ARTICLES['manage_number'] = $data['manage_number'];
      $ARTICLES['amount'] = $data['amount'];
      $ARTICLES['place'] = $data['place'];
      $ARTICLES['note'] = $data['note'];
      $ARTICLES['seq'] = $data['seq'];
    }


    ## 모바일 버전사용 일반스킨 레이아웃 상세보기 닫기 버튼 hidden or show 처리를 위한 변수 김경남 17.12.14
    $ARTICLES['btoff'] = $btoff;


    ## 버튼설정
    $arr_parameter = array();
    if ($page > 1) $arr_parameter['page'] = $page;
    if (!empty($search_date) && $search_date == 'y') $arr_parameter['search_date'] = $search_date;
    if (!empty($search_type)) $arr_parameter['search_type'] = $search_type;
    if (!empty($search_word)) $arr_parameter['search_word'] = $search_word;
    if (!empty($start_date)) $arr_parameter['start_date'] = $start_date;
    if (!empty($finish_date)) $arr_parameter['finish_date'] = $finish_date;
    if (!empty($category_1)) $arr_parameter['category_1'] = $category_1;
    if (!empty($idx)) $arr_parameter['idx'] = $idx;

    if (!empty($arr_parameter)) $ARTICLES['parameter'] = $arr_parameter;

    ## 페이지네비 파라메터 추가 - 댓글 페이징을 위해 필요
    if (!empty($arr_parameter)) $ARTICLES['navi_parameter'] = $arr_parameter;
    $ARTICLES['cpage'] = $cpage;

    ##테마 적용 - 20200423 서희진
    $ARTICLES['use_theme'] = $this->module_config['use_theme'];
    if ($this->module_config['use_theme'] == "true") {
      $ARTICLES['theme_list'] = $this->module_config['theme_list'];
    }

    ##모듈 공공누리 설정 적용 - 20171212 서희진
    $ARTICLES['use_open_type'] = ($_SYSTEM['menu_info']['open_type'] == "1" ? "true" : "false");

    ## 2012.08.29 VIEW 페이지의 스킨쪽에서 조회수 증가로직 추가.(강성수)
    $ARTICLES['mode'] = $mode;
    $ARTICLES['table_name'] = $this->module_config['table_name'];
    $ARTICLES['visit_expire_term'] = $this->module_config['visit_expire_term'];

    ## 2012.08.29 게시물보기 횟수증가 주석처리 -> 스킨에서 처리(강성수)
    //## 게시물 보기 횟수증가
    $this->increment_visit($idx, $this->module_config['table_name'], $this->module_config['visit_expire_term']);


    ## 2014.04.19 보기사유 작성 - 일반인이 아닐경우 보기사유를 작성한다.
    if ($this->module_config['use_view_comment'] == 'true' && $this->myinfo['user_level'] > 0 && $this->myinfo['user_level'] < 9 && $data['reg_pin'] != $this->myinfo['my_pin']) {
      if ($this->view_comment($this->module_config['table_name'], $idx, $this->module_config['board_id']) == false) return false;;
    }

    ## 20130827 강성수 이북사용여부에따른 첨부파일표시방법을 변경하기위한 사용여부값설정
    $ARTICLES['ebook_use'] = $this->module_config['ebook_use']; //true-이북사용 else 미사용

    ##모듈 공공누리 설정 적용 - 20171212 서희진
    $ARTICLES['kogl_type'] = $data['kogl_type'];

    ## 인기글 --------------
    ## 최근 일주일치만 조회
    if ($this->module_config['user_list_hot'] == "true") {
      $ARTICLES['user_list_hot'] = $this->module_config['user_list_hot'];
      $ARTICLES['hot_articles_data'] = $this->hot_articles($this->module_config['table_name'], $this->module_config['board_id'], $this->module_config['list_hot_count']);
    }
    ##----------------------

    ## skin 설정
    $ARTICLES['device'] = $this->device;
    $ARTICLES['board_id'] = $this->module_config['board_id'];                  ## 근본적으로 스킨쪽에서 세팅되어야 한다.
    $ARTICLES['self_url'] = $_SERVER['PHP_SELF'];                  ## 근본적으로 스킨쪽에서 세팅되어야 한다.
    $ARTICLES['module_root'] = $this->module_root;                  ## 근본적으로 스킨쪽에서 세팅되어야 한다.
    $ARTICLES['module_path'] = $this->module_config['module_path']; ## 근본적으로 스킨쪽에서 세팅되어야 한다.
    //$ARTICLES['skin_style']  = empty($this->module_config['skin_style']) ? 'default' : $this->module_config['skin_style'];


    /*if($ARTICLES['file_list'][0]['file_type'] == 'movie' && !($this->module_config['skin_style']=='approval'||$this->module_config['skin_style']=='regulation'||$this->module_config['skin_style']=='regulation_ex'||$this->module_config['skin_style']=='approval_ex')) {
			if($this->module_config['skin_style'] == 'stream') {
				$ARTICLES['skin_style']  = 'stream';
			} else {
				$ARTICLES['skin_style']  = 'flv';
			}
		} else {
			$ARTICLES['skin_style']  = empty($this->module_config['skin_style']) ? 'default' : $this->module_config['skin_style'];
		}
		*/


    $ARTICLES['skin_style'] = empty($this->module_config['skin_style']) ? 'default' : $this->module_config['skin_style'];
    $ARTICLES['skin_name'] = 'view';
    $ARTICLES['list_at_view'] = $this->module_config['list_at_view'];


    if ($_SYSTEM['hostname'] == "mayor") {
      $ARTICLES['skin_name'] = "view_mayor";
    }

    /* 좋아요 기능 */
    if ($this->get_parameter('se_mode') == 'recomm') $this->increment_recomm(array('table_name' => $ARTICLES['table_name'], 'table_idx' => $idx));
    $query = sprintf('SELECT visit_cnt FROM _recommend_info WHERE table_name = "%s" AND table_idx = "%s"', $ARTICLES['table_name'], $idx);
    $data = $mysql->query_fetch($query);
    $ARTICLES['recommend_cnt'] = $data['visit_cnt'];
    /* 좋아요 기능 */

    ## 데이타 ajax으로 가져가기 위해서 추가.
    $return = $this->get_parameter('return');
    if ($return == "json") {
      $ARTICLES['contents'] = strip_tags($ARTICLES['contents']);
      ob_clean();
      echo json_encode($ARTICLES);
      exit;
    }
    ## ---------------------------------------------

    ob_clean();
    echo serialize($ARTICLES);



    ## 이부분에 보기페이지 아래에 목록을 표기하고자 할때 line_up() 을 call 한다.
    ## skin 쪽에서도 같이 수정해줘야 한다.
    if ($this->module_config['list_at_view'] == 'true') $this->lineup();
    return true;
  }

  ## 글쓰기/수정
  public function write()
  {

    global $mysql;
    global $_SYSTEM;
    $ARTICLES = array();
    $data = array();
    $data_file = array();


    ## parameter setting.
    $idx = $this->get_parameter('idx');
    $page = $this->get_parameter('page');
    $page = empty($page) ? 1 : $page;
    $mode = $this->get_parameter('mode');
    $search_type = $this->get_parameter('search_type');
    $search_word = $this->get_parameter('search_word');
    $category_1 = $this->get_parameter('category_1');
    $category_2 = $this->get_parameter('category_2');
    $title = $this->get_parameter('title');
    $null_check = $this->get_parameter('null_check'); ## 필수값 체크 메시지
    $sub_mode = $this->get_parameter('sub_mode');

    ## ====================================================
    ##               모드별 선행처리 start
    ## ====================================================
    // 관리자만 글을 쓸수 있는 옵션일 경우 : faq 등의 게시판등에서 사용됨.
    if ($this->module_config['write_admin_only'] == 'true' && $this->permission['admin'] !== true) call::xml_error('204', '', $this->referer);


    if ($mode == 'modify') {
      if (empty($idx)) call::xml_error('152', '', $this->referer);
      if ($this->module_config['skin_style'] == 'schedule') {  // 스케줄러 게시판
        $query = sprintf('SELECT * FROM %s WHERE board_idx = "%s"', '_schedule', $idx);
      } else {
        $query = sprintf('SELECT * FROM %s WHERE idx = "%s"', $this->module_config['table_name'], $idx);
      }


      $data = $mysql->query_fetch($query);

      if (empty($data)) call::xml_error('152', '', $this->referer);

      if ($this->myinfo['is_login'] === true) {
        ## 게시판 관리자가 아니거나 자기글이 아닐경우 수정하지 못한다.
        if ($this->permission['admin'] !== true && $this->myinfo['my_pin'] != $data['reg_pin']) call::xml_error('204', '', $this->referer);
      } else {
        ## 비로그인 회원 글쓰기가 허용일 경우 패스워드 확인을 거친다.
        ## 수정일때 $this->module_config['use_logoff_write'] == 'true' 조건을 체크하지 않는다. 이미 등록되어있는 글에 대해서 수정을 할수 있어야 하기 때문에.
        if ($data['reg_pin'] != GUEST_PIN) call::xml_error('204', '', $this->referer);
        if ($this->confirm_logoff_passwd($idx, $data['passwd']) !== true) return false;
      }
      if ($this->module_config['skin_style'] == 'approval' || $this->module_config['skin_style'] == 'regulation' || $this->module_config['skin_style'] == 'solicit' || $this->module_config['skin_style'] == 'approval_ex' || $this->module_config['skin_style'] == 'approval_health' || $this->module_config['skin_style'] == 'solicit_cleen' || $this->module_config['skin_style'] == 'address_regulation' || $this->module_config['skin_style'] == 'monitor') {

        if ($data['process_2'] != '1' && $this->permission['manage'] == false) {
          call::xml_error('209', '', $this->referer); //진행중인 글은 수정 못한다.11
        }
      } else if ($this->module_config['skin_style'] == 'ys_singo' || $this->module_config['skin_style'] == 'ys_singo_coast' || $this->module_config['skin_style'] == 'ys_reserve_minwon') {
        $query2 = sprintf('SELECT idx, reg_pin, process_1, charge_id FROM %s WHERE idx = "%s"', $this->module_config['table_name'], $data['pidx']);
        $data2 = $mysql->query_fetch($query2);

        if (!($data2['process_2'] == '신청')) { ## 신청이 아니면서
          if (!($this->permission['manage'] == true || $this->permission['admin'] == true)) { ## 자기자신이거나 담당이거나 관리자가 아닐때
            call::xml_error('209', '', $this->referer); //진행중인 글은 삭제 못한다.
          }
        }
      }
      $ARTICLES = $data;
      //$ARTICLES['contents'] = strip_tags(str_replace("<p ","\n<p ",$data['contents']));
      //print_r( $ARTICLES['contents'] );			exit;
    } else if ($mode == 'reply') {
      if (empty($idx)) call::xml_error('152', '', $this->referer);
      if ($this->module_config['use_reply'] == 'none') call::xml_error('201', '답변글쓰기가 금지되어 있습니다.', $this->referer);
      if ($this->module_config['use_reply'] == 'admin' && $this->permission['admin'] !== true) call::xml_error('201', '답변글쓰기가 금지되어 있습니다.', $this->referer);

      // sort 수정 추가 - 기존의 pidx, seq를 사용한 쿼리가 너무 느려서 sort 필드 하나만 이용하도록 수정함.
      $query = sprintf('SELECT contents, title, pidx, level, seq, sort, reg_name, depart_name, reg_date FROM %s WHERE idx = "%s"', $this->module_config['table_name'], $idx);
      $data = $mysql->query_fetch($query);

      $ARTICLES['contents'] = '[원본글]' . "\n";
      $ARTICLES['contents'] .= ($this->module_config['writer_display'] == 'department' ? $data['depart_name'] : $data['reg_name']) . '님이 ' . date("Y-m-d", strtotime($data['reg_date'])) . '에 작성한 글입니다.' . "\n";
      $ARTICLES['contents'] .= '제목 : ' . $data['title'] . "\n";
      $ARTICLES['contents'] .= $data['contents'] . "\n\n";
      $ARTICLES['contents'] .= '[답변내용]';
    }

    if (empty($ARTICLES['contents']) && !empty($this->module_config['default_contents'])) $ARTICLES['contents'] = $this->module_config['default_contents'];


    ## ====================================================
    ##               모드별 선행처리 end
    ## ====================================================

    ## 필수값 체크
    if (!empty($null_check)) {
      $null_data = array();
      foreach ($_POST as $key => $val) if (!empty($val)) $null_data[$key] = $val;
      $null_data['null_check'] = $null_check;
      $data = count($data) > 0 ? array_merge($data, $null_data) : $null_data;
      $ARTICLES = $data;
    }

    ## 비로그인 회원 글쓰기 설정
    if ($this->myinfo['is_login'] !== true && $mode != 'modify') {
      if ($this->module_config['use_logoff_write'] == 'true') {
        ## 최초 글 쓸 경우 여기에서 text_code를 세션으로 구운 다음에 save에서 그 값을 form값과 비교한다. 그런 다음에 save를 마치고나서 session을 distory 한다.
        if ($mode != 'modify') {
          $_SESSION['text_keycode'] = $this->get_board_keycode();
          $ARTICLES['use_logoff_write'] = $this->module_config['use_logoff_write'];
          $ARTICLES['text_keycode'] = $_SESSION['text_keycode'];
        }
        ## 수정, 삭제 시에는 password_idx를 세션으로 구운다. 여기에서는 그값이 없을 경우 password를 입력하라는 form을 띄운다.
      } else {
        ## 게시판의 권한이 비로그인 회원이 글을 쓸수 있을지라도 설정에서 비로그인 회원의 글쓰기를 허용 해주지 않으면 글을 쓸 수 없다.
        call::xml_error('202', '', $this->referer);
      }
    }

    ## 이름입력 설정
    // 글 등록시 작성자명 입력하는 부분 : 현재는 관리자만 입력할(바꿀)수 있게 되어있는데 이부분 수정되어야 한다.
    //  => sns 또는 비회원 글 작성시 이름이 없을 경우 이름을 입력할 수 있게 되어야 한다.
    //  => 수정시에는 이름을 변경할 수 없도록 한다. ??? => 이부분은 정운영 소장 다시 문의.
    //  => 부서등록 부분과 같이 연계되어야 한다.

    ##2018.01.30 김경남 수정 문화예술회관DB자료, 시민문화체육센터DB자료만 적용 (글쓰기 등록시 등록자 미입력시 등록자 명에 관리자로 보이게)
    if ($this->module_config['board_id'] == "art_culture_data" || $this->module_config['board_id'] == "art_data") {
      if (empty($ARTICLES['reg_name'])) $ARTICLES['reg_name'] = $this->module_config['use_nick_name'] == 'true' ? $this->myinfo['user_nick'] : "관리자";
    } elseif ($this->hostname == 'seafountain' && $this->myinfo['user_id'] == 'jgy1237') { ## 바다분수축제 제갈연 주무관 등록자명 바로 관리자로 나오게 수정
      if (empty($ARTICLES['reg_name'])) $ARTICLES['reg_name'] = "관리자";
    } else {
      ## 2012.03.29 오경우 수정 : 타기관소식에서 타기관명을 user_nick 필드에 넣어서 사용하도록 변경함.
      //$ARTICLES['reg_name'] = empty($ARTICLES['reg_name']) ? $this->myinfo['user_name'] : $ARTICLES['reg_name'];
      if (empty($ARTICLES['reg_name'])) $ARTICLES['reg_name'] = $this->module_config['use_nick_name'] == 'true' ? $this->myinfo['user_nick'] : $this->myinfo['user_name'];

    }

    $ARTICLES['user_phone'] = $_SESSION['user_phone'];
    $ARTICLES['user_birth'] = $_SESSION['user_birth'];

    //$ARTICLES['depart_name'] = empty($ARTICLES['depart_name']) ? $this->myinfo['dept']['name'] : $ARTICLES['depart_name'];
    $ARTICLES['depart_name'] = empty($ARTICLES['depart_name']) ? $_SESSION['dept_name'] : $ARTICLES['depart_name'];

    $ARTICLES['writer_display'] = $this->module_config['writer_display'];


    //2018-08-24 황재복 : 박옥이 주무관 요청으로 게시판에 공무원은 작성자 명 바꿀 수 있게
    //$ARTICLES['read_only_reg_name'] = ($this->permission['admin'] === true || empty($ARTICLES['reg_name'])) ? 'false' : 'true';
    $ARTICLES['read_only_reg_name'] = ($this->permission['admin'] === true || empty($ARTICLES['reg_name']) || $this->myinfo['user_level'] <= 6) ? 'false' : 'true';

    ## 비공개글 설정
    if ($this->module_config['use_lock'] == 'true') {
      $ARTICLES['use_lock'] = $this->module_config['use_lock'];
      if (empty($ARTICLES['open'])) $ARTICLES['open'] = $this->module_config['lock_default'] == 'open' ? 'y' : 'n';
    }


    ## TOP설정
    if ($this->module_config['use_top'] == 'true') {
      $ARTICLES['use_top'] = $this->module_config['use_top'];
      $ARTICLES['top_start'] = empty($data['top_start']) ? date('Y-m-d') : date("Y-m-d", strtotime($data['top_start']));
      $ARTICLES['top_end'] = empty($data['top_end']) ? date('Y-m-d', strtotime('+7 day')) : date("Y-m-d", strtotime($data['top_end']));
    }


    ## 기본분류 설정.
    if ($this->module_config['use_category_1'] == 'true' && is_array($this->module_config['category_1'])) {
      $ARTICLES['use_category_1'] = $this->module_config['use_category_1'];
      $ARTICLES['category_1'] = $data['category_1'];
      $ARTICLES['category_1_all'] = serialize($this->module_config['category_1']);
    }

    ## 기본분류 설정.
    if ($this->module_config['use_category_2'] == 'true' && is_array($this->module_config['category_2'])) {
      $ARTICLES['use_category_2'] = $this->module_config['use_category_2'];
      $ARTICLES['category_2'] = $data['category_2'];
      $ARTICLES['category_2_all'] = serialize($this->module_config['category_2']);
    }

    /*
		## 재정정보에서 사용.
		if( in_array($this->module_config['skin_style'],array('finance_form','finance_form_settlement') ) ) {
			$ARTICLES['category_1'] = empty($category_1)?$data['category_1']:$category_1;
			$ARTICLES['category_2'] = empty($category_2)?$data['category_2']:$category_2;
			$ARTICLES['title'] = empty($title)?$data['title']:$title;
			$ARTICLES['contents'] = empty($title)?$data['contents']:$title;
		}
		*/
    ## 베너존일 경우 이미지 사이즈
    if ($this->module_config['use_banner'] == 'true') {
      $ARTICLES['banner_size_width'] = $this->module_config['banner_size_width'];
      $ARTICLES['banner_size_height'] = $this->module_config['banner_size_height'];
      $ARTICLES['banner_allow_schedule'] = $this->module_config['banner_allow_schedule'];
    }

    if (!empty($this->module_config['use_list_size']) && $this->module_config['use_list_size'] == 'true') {
      $ARTICLES['list_size_width'] = $this->module_config['list_size_width'];
      $ARTICLES['list_size_height'] = $this->module_config['list_size_height'];
    }
    ## tag 설정
    if ($this->module_config['use_tag'] == 'true') {
      $ARTICLES['use_tag'] = $this->module_config['use_tag'];

      ## tag 적용
      if (!empty($this->module_config['source_id'])) {
        $original_board_id = $this->module_config['board_id'];
        $this->module_config['board_id'] = $this->module_config['source_id'];
      }

      ## tag를 사용할 경우 source_id의 config에 있는 tag_list_all 값을 가져와야 한다.
      $source_config = $this->get_board_config($this->module_config['board_id']);
      $ARTICLES['tag_list_all'] = $source_config['tag_list_all'];

      if (empty($data['tag_list'])) {
        $data['tag_list'] = empty($data['tag']) ? $this->module_config['tag_list'] : explode('|', $data['tag']);
      }

      // 2012.03.22 오경우 수정 : 아래로 대체
      //if($original_board_id != $this->module_config['source_id']) $ARTICLES['tag_list'] = $data['tag_list'];
      $ARTICLES['tag_list'] = $data['tag_list'];
    }

    ## 게시기간 설정
    //if($this->module_config['use_display_date'] == 'true' ) {
    //setting 게시기간 체크 없음 -- 게시기간은 기본 사용. use_display_date 사용 확인하고 삭제!!
    $ARTICLES['use_display_date'] = $this->module_config['use_display_date'];
    $ARTICLES['period_start'] = $data['period_start'];
    $ARTICLES['period_end'] = $data['period_end'];


    //}

    ## 2021.06 서희진
    if (strpos($ARTICLES['phone'], "-") > 0) {
      list($ARTICLES['phone_1'], $ARTICLES['phone_2'], $ARTICLES['phone_3']) = explode("-", $ARTICLES['phone']);
      list($ARTICLES['user_phone_1'], $ARTICLES['user_phone_2'], $ARTICLES['user_phone_3']) = explode("-", $ARTICLES['phone']);
    } else {
      $ARTICLES['phone_1'] = substr($ARTICLES['phone'], 0, 3);
      $ARTICLES['phone_2'] = substr($ARTICLES['phone'], 4, 4);
      $ARTICLES['phone_3'] = substr($ARTICLES['phone'], 9, 4);


      $ARTICLES['user_phone_1'] = substr($ARTICLES['user_phone'], 0, 3);
      $ARTICLES['user_phone_2'] = substr($ARTICLES['user_phone'], 3, 4);
      $ARTICLES['user_phone_3'] = substr($ARTICLES['user_phone'], 7, 4);
    }

    ######################################################################
    ## 멀티호스트 설정
    if ($this->module_config['use_multihost'] == 'true') {
      $ARTICLES['use_multihost'] = $this->module_config['use_multihost'];
      $ARTICLES['host_list_all'] = $this->get_site_host_list();
      $ARTICLES['host_list'] = explode("|", $data['varchar_10']);

    }
    ######################################################################
    //첨부파일기능 수정 : 오경우 (20120120)
    ## 첨부파일
    $ARTICLES['file_upload_count'] = $this->module_config['file_upload_count'];
    if ($mode != 'reply' && !empty($idx)) {
      $file_list = $this->attached_file_list($idx);
      $ARTICLES['file_list'] = $file_list;
      ## 메인 이미지 구분 ====== 2020 새로운 기능
      ## 2021.05.12 서희진 : 특정 스킨에만 있던 기능을 전부다 넣음.
      if ($this->module_config['main_image_use'] == 'true' || $this->module_config['skin_style'] == 'mush_farmer') {
        //if(  $this->module_config['skin_style'] == 'thumb_photo' || $this->module_config['skin_style'] == 'thumb_photo_gongik' || $this->module_config['skin_style'] == 'youth_program' ){
        $ARTICLES['file_list'] = array();
        foreach ($file_list as $val) {
          if ($val['idx'] == $data['mainimage_idx']) {
            $ARTICLES['file_list_main'][] = $val;
          } else {
            $ARTICLES['file_list'][] = $val;
          }
        }

      }

    }
    //첨부파일기능 수정 : 오경우 (20120120)

    ## 첨부파일 리스트(업소등록 - 사업자등록증, 컨텐츠수집표)
    if (!empty($idx)) {
      $file_list_shop = $this->attached_file_list_shop1($idx);
      $ARTICLES['file_list_shop1'] = $file_list_shop;

      $file_list_shop = $this->attached_file_list_shop2($idx);
      $ARTICLES['file_list_shop2'] = $file_list_shop;
    }

    ## 이미지 리스트
    if (!empty($idx)) {
      $img_file_list = $this->attached_photo_ajax_list($idx, $this->module_config['table_name']);
      $ARTICLES['img_file_list'] = $img_file_list;
    }


    ## 비회원정보 수집 (주소, 연락처)
    ## 회원구분이 복잡하기 때문에(sns등) 관리자가 아닐경우 모든 회원에 대해서 정보를 수집한다.
    if ($this->module_config['get_guest_info'] == 'true' && ($this->myinfo['user_level'] == 11 || $this->myinfo['user_level'] == 99)) {
      $ARTICLES['get_guest_info'] = 'true';
      if ($mode == 'write') {
        ## get_user_info() : 임시적으로 만든것이다. user management class가 완성되면 수정해야 한다.
        $user_info = $this->get_user_info($data['reg_pin']);
        $ARTICLES['phone'] = $user_info['phone'];
        $ARTICLES['zipcode'] = $user_info['zipcode'];
        $ARTICLES['address1'] = $user_info['address1'];
        $ARTICLES['address2'] = $user_info['address2'];
      }
    }

    ## 제목 스타일 적용
    if ($this->module_config['use_title_style'] == 'true') {
      $ARTICLES['use_title_style'] = $this->module_config['use_title_style'];
      $ARTICLES['title_style_list'] = $this->module_config['title_style'];
      $ARTICLES['title_style'] = $data['title_style'];
    }

    $ARTICLES['use_editor'] = $this->module_config['use_editor'] == 'true' ? 'true' : 'false'; ## 웹에디터 사용유무
    $ARTICLES['use_map'] = $this->module_config['use_map'] == 'true' ? 'true' : 'false'; ## 지도 사용유무
    $ARTICLES['use_search_tag'] = $this->module_config['use_search_tag'] == 'true' ? 'true' : 'false'; ## 검색단어 등록 사용유무
    if ($this->module_config['use_search_tag'] == 'true' && !empty($_POST['search_tag'])) $ARTICLES['search_tag'] = $_POST['search_tag'];
    $ARTICLES['permission'] = $this->permission;

    $ARTICLES['contents_en'] = $data['contents_en'];
    $ARTICLES['contents_jp'] = $data['contents_jp'];
    $ARTICLES['contents_cn'] = $data['contents_cn'];
    ## 새로운공연전시 기능 추가 : 2012.04.17 오경우
    if ($this->module_config['use_event_skin'] == 'true' && !empty($data['contents_cn'])) {
      $concert_list = unserialize(base64_decode($data['contents_cn']));
      for ($iter = 1; $iter <= 10; $iter++) {
        $ARTICLES['concert_' . $iter . '_start'] = $concert_list[$iter]['start'];
        $ARTICLES['concert_' . $iter . '_end'] = $concert_list[$iter]['end'];
      }
    }


    ## 2014.08.04 오경우 확장필드 추가
    $ARTICLES['varchar_1'] = $data['varchar_1'];
    $ARTICLES['varchar_2'] = $data['varchar_2'];
    $ARTICLES['varchar_3'] = $data['varchar_3'];
    $ARTICLES['varchar_4'] = $data['varchar_4'];
    $ARTICLES['varchar_5'] = $data['varchar_5'];
    $ARTICLES['varchar_6'] = $data['varchar_6'];
    $ARTICLES['varchar_7'] = $data['varchar_7'];
    $ARTICLES['varchar_8'] = $data['varchar_8'];
    $ARTICLES['varchar_9'] = $data['varchar_9'];
    $ARTICLES['varchar_10'] = $data['varchar_10'];
    $ARTICLES['varchar_11'] = $data['varchar_11'];
    $ARTICLES['varchar_12'] = $data['varchar_12'];
    $ARTICLES['varchar_13'] = $data['varchar_13'];
    $ARTICLES['varchar_14'] = $data['varchar_14'];
    $ARTICLES['varchar_15'] = $data['varchar_15'];


    if ($this->module_config['skin_style'] == 'minwon_form') {
      $ARTICLES['longtext_1'] = $data['longtext_1'];
      $ARTICLES['longtext_2'] = $data['longtext_2'];
      $ARTICLES['longtext_3'] = $data['longtext_3'];
      $ARTICLES['longtext_4'] = $data['longtext_4'];
      $ARTICLES['longtext_5'] = $data['longtext_5'];
      $ARTICLES['longtext_6'] = $data['longtext_6'];
    }

    if ($this->module_config['skin_style'] == 'ys_singo' || $this->module_config['skin_style'] == 'ys_singo_coast' || $this->module_config['skin_style'] == 'ys_reserve_minwon') {
      $encryption = new yb_crypt();
      $ARTICLES['petition'] = $data['petition'];
      $ARTICLES['c_phone'] = $encryption->decrypt($data['c_phone']);
      $ARTICLES['c_tel'] = $encryption->decrypt($data['c_tel']);
      $ARTICLES['c_email'] = $encryption->decrypt($data['c_email']);
      $ARTICLES['c_zipcode'] = $encryption->decrypt($data['c_zipcode']);
      $ARTICLES['c_address_1'] = $encryption->decrypt($data['c_address_1']);
      $ARTICLES['c_address_2'] = $encryption->decrypt($data['c_address_2']);
      $ARTICLES['c_varchar_1'] = $encryption->decrypt($data['c_varchar_1']);
      $ARTICLES['c_varchar_2'] = $encryption->decrypt($data['c_varchar_2']);
      $ARTICLES['c_varchar_3'] = $encryption->decrypt($data['c_varchar_3']);
    }

    ## 2015.10.13 윤지미 처리상태 표시를 위해 필드 추가
    if ($this->module_config['skin_style'] == 'shop_registration') {
      $ARTICLES['admin_comment'] = isset($data['admin_comment']) ? str_replace("\n", '<br />', $data['admin_comment']) : NULL;
      $ARTICLES['process_1'] = isset($data['process_1']) ? $data['process_1'] : NULL;
    }

    ## 2015.09.24 서희진 추가
    ## 후기 스킨일떄 여분필들에 값을 가져온다.
    if ($this->module_config['skin_style'] == 'epilogue') {
//			$ARTICLES['option'] = unserialize(base64_decode($data['varchar_1']));
      $ARTICLES['tourlist'] = explode("|", $data['varchar_1']);                      // 여행코스
      $ARTICLES['tour_object'] = isset($data['varchar_2']) ? explode("|", $data['varchar_2']) : '기타';  // 여행목적
      ## 통계를 위한 필드
      $ARTICLES['tour_object_etc'] = isset($data['varchar_3']) ? $data['varchar_3'] : NULL;        // 기타여행목적
      $ARTICLES['tour_age'] = isset($data['varchar_4']) ? $data['varchar_4'] : NULL;            // 연령
      $ARTICLES['tour_person'] = isset($data['varchar_5']) ? $data['varchar_5'] : NULL;          // 인원
      $ARTICLES['tour_method'] = isset($data['varchar_7']) ? explode("|", $data['varchar_7']) : '기타';  // 여행수단
      $ARTICLES['tour_method_etc'] = isset($data['varchar_8']) ? $data['varchar_8'] : NULL;        // 기타여행수단
      $ARTICLES['tour_return'] = isset($data['varchar_9']) ? $data['varchar_9'] : NULL;          // 재방문의사
      $ARTICLES['tour_return_term'] = isset($data['varchar_10']) ? $data['varchar_10'] : NULL;      // 재방문기간
      $ARTICLES['period_start'] = isset($data['period_start']) ? $data['period_start'] : NULL;      // 여행기간 시작일
      $ARTICLES['period_end'] = isset($data['period_end']) ? $data['period_end'] : NULL;          // 여행기간 종료일
    }

    ## 답변글 sms 발송여부
    $ARTICLES['mode'] = $mode;
    if ($this->module_config['use_reply_sms'] == 'all' || $this->module_config['use_reply_sms'] == 'admin') {
      $ARTICLES['use_reply_sms'] = $this->module_config['use_reply_sms'];
      ## 원본글에서 작성자의 핸드폰 번호가 있을 경우 그것을 사용하며,
      ## 핸드폰 번호가 없을 경우 글 작성자의 아이디로 핸드폰 번호를 뽑아오는 로직을 추가해야 한다.
      $ARTICLES['reply_sms_recv_number'] = $data['reply_sms_recv_number'];
      $ARTICLES['reply_sms_recv_number'] = empty($ARTICLES['reply_sms_recv_number']) ? $data['phone'] : $ARTICLES['reply_sms_recv_number'];
      //$ARTICLES['reply_sms_recv_number'] = empty($ARTICLES['reply_sms_recv_number']) ? $user_info['phone'] : $ARTICLES['reply_sms_recv_number'];
    } else {
      $this->module_config['use_reply_sms'] = 'none';
    }





    $ARTICLES['linkbox'] = unserialize(base64_decode($data['linkbox']));
    ## 2018.06.29 서희진 수정 : 이전 데이타에 html태그가 포함되어 잇는 글은. DB field에 html_tag값을 y로 설정후 nl2br안하고 뿌린다.
    if ($data['html_tag'] == "y") {
      $ARTICLES['contents'] = nl2br(strip_tags($data['contents']));
    } elseif ($data['html_tag'] == "a") {
      $ARTICLES['contents'] = $data['contents_original'];
    } else {
      //$ARTICLES['contents'] = $this->module_config['use_editor'] == 'true' ? $data['contents'] : nl2br(strip_tags($data['contents']));
      //$ARTICLES['contents'] =  str_replace("\n", '<br />',$data['contents']);
      $ARTICLES['contents'] = strip_tags(stripslashes(nl2br($data['contents'])), '<p>');
      $ARTICLES['contents'] = str_replace('</p>', chr(13), $ARTICLES['contents']);
      $ARTICLES['contents'] = strip_tags($ARTICLES['contents']);
      $ARTICLES['contents'] = str_replace("&nbsp;", " ", $ARTICLES['contents']);

    }

    ## 행정톡 공지사항 최초 부서가져오기. ----- start
    if ($this->module_config['board_id'] == 'staff_notice') {
      $ret = $mysql->query(' SELECT id, title FROM _staff_department WHERE  `level` = "2" AND `type` = "office" ');
      while ($dept_data[] = $mysql->fetch_array($ret))
        $ARTICLES['dept_office'] = $dept_data;
    }
    ## 행정톡 공지사항 최초 부서가져오기. ----- end


    ## hidden 값 설정
    $hidden = array();
    $hidden['mode'] = $mode == 'modify' ? 'change' : 'save';
    if ($mode == 'reply') {
      $hidden['pidx'] = $data['pidx'];
      $hidden['seq'] = $data['seq'];
      $hidden['level'] = $data['level'];
      // sort 수정 추가 - 기존의 pidx, seq를 사용한 쿼리가 너무 느려서 sort 필드 하나만 이용하도록 수정함.
      $hidden['sort'] = $data['sort'];
    }
    if ($mode == 'modify') $hidden['idx'] = $data['idx'];
    if ($page > 1) $hidden['page'] = $page;
    if (!empty($search_type)) $hidden['search_type'] = $search_type;
    if (!empty($search_word)) $hidden['search_word'] = $search_word;
    //if(!empty($category_1)) $hidden['category_1'] = $category_1;

    $ARTICLES['hidden'] = $hidden;


    ## 파라미터 설정 : 버튼
    $arr_parameter = array();
    if ($page > 1) $arr_parameter['page'] = $page;
    if (!empty($search_type)) $arr_parameter['search_type'] = $search_type;
    if (!empty($search_word)) $arr_parameter['search_word'] = $search_word;
    if (!empty($category_1)) $arr_parameter['category_1'] = $category_1;
    $ARTICLES['parameter'] = $arr_parameter;

    //2012-10-09 황재복 : 상단문구 출력안되어 추가
    $ARTICLES['write_msg'] = $this->module_config['write_msg'];

    ##테마 적용 - 20200423 서희진
    $ARTICLES['use_theme'] = $this->module_config['use_theme'];
    if ($this->module_config['use_theme'] == "true") {
      $ARTICLES['theme_list'] = $this->module_config['theme_list'];
    }

    ##모듈 공공누리 설정 적용 - 20171212 서희진
    $ARTICLES['use_open_type'] = ($_SYSTEM['menu_info']['open_type'] == "1" ? "true" : "false");

    ## skin 설정
    $ARTICLES['device'] = $this->device;
    $ARTICLES['module_root'] = $this->module_root;                  ## 근본적으로 스킨쪽에서 세팅되어야 한다.
    $ARTICLES['module_path'] = $this->module_config['module_path']; ## 근본적으로 스킨쪽에서 세팅되어야 한다.
    $ARTICLES['skin_style'] = empty($this->module_config['skin_style']) ? 'default' : $this->module_config['skin_style'];
    $ARTICLES['skin_name'] = 'write';


    if ($_SYSTEM['hostname'] == "mayor") {
      $ARTICLES['skin_name'] = "write_mayor";
    }
    if ($_SERVER['REMOTE_ADDR'] == "49.254.140.140") {
      //	echo $sub_mode; exit;
    }
    if ($sub_mode == "privacy_print") {
      $ARTICLES['skin_name'] = "privacy_print";
    }

    echo serialize($ARTICLES);
    return true;
  }


  ## 바다분수 사연신청 답변 폼
  public function write_reply()
  {

    global $mysql;
    global $_SYSTEM;
    $ARTICLES = array();
    $data = array();
    $data_file = array();

    if ($this->module_config['skin_style'] == 'solicit' || $this->module_config['skin_style'] == 'approval_ex' || $this->module_config['skin_style'] == 'apply_photo' || $this->module_config['board_id'] == 'www_ordinanace_rule' || $this->module_config['skin_style'] == 'solicit_cleen' || $this->module_config['skin_style'] == 'address_regulation' || ($this->module_config['skin_style'] == 'regulation' && ($this->module_config['board_id'] == 'www_sexually_harass' || $this->module_config['board_id'] == 'www_regulation_inspection' || $this->module_config['board_id'] == 'www_regulation_land' || $this->module_config['board_id'] == 'www_welfare_councel'))) {
      $agree_st = $_SESSION["agree_st_" . $this->module_config['board_id']];
      if ($agree_st != 'agree') {
        ob_clean();
        header("Location: " . $_SERVER['PHP_SELF'] . "?mode=agreeForm");

        return false;
      }
    }

    ## parameter setting.
    $idx = $this->get_parameter('idx');
    $page = $this->get_parameter('page');
    $page = empty($page) ? 1 : $page;
    $mode = $this->get_parameter('mode');
    $search_type = $this->get_parameter('search_type');
    $search_word = $this->get_parameter('search_word');
    //$category_1  = $this->get_parameter('category_1');
    $null_check = $this->get_parameter('null_check'); ## 필수값 체크 메시지


    ## ====================================================
    ##               모드별 선행처리 start
    ## ====================================================
    // 관리자만 글을 쓸수 있는 옵션일 경우 : faq 등의 게시판등에서 사용됨.
    if ($this->module_config['write_admin_only'] == 'true' && $this->permission['admin'] !== true) call::xml_error('204', '', $this->referer);


    if ($mode == 'modify') {
      if (empty($idx)) call::xml_error('152', '', $this->referer);
      if ($this->module_config['skin_style'] == 'schedule') {  // 스케줄러 게시판
        $query = sprintf('SELECT * FROM %s WHERE board_idx = "%s"', '_schedule', $idx);
      } else {
        $query = sprintf('SELECT * FROM %s WHERE idx = "%s"', $this->module_config['table_name'], $idx);
      }

      $data = $mysql->query_fetch($query);
      if (empty($data)) call::xml_error('152', '', $this->referer);

      if ($this->myinfo['is_login'] === true) {
        ## 게시판 관리자가 아니거나 자기글이 아닐경우 수정하지 못한다.
        if ($this->permission['admin'] !== true && $this->myinfo['my_pin'] != $data['reg_pin']) call::xml_error('204', '', $this->referer);
      } else {
        ## 비로그인 회원 글쓰기가 허용일 경우 패스워드 확인을 거친다.
        ## 수정일때 $this->module_config['use_logoff_write'] == 'true' 조건을 체크하지 않는다. 이미 등록되어있는 글에 대해서 수정을 할수 있어야 하기 때문에.
        if ($data['reg_pin'] != GUEST_PIN) call::xml_error('204', '', $this->referer);
        if ($this->confirm_logoff_passwd($idx, $data['passwd']) !== true) return false;
      }
      if ($this->module_config['skin_style'] == 'approval' || $this->module_config['skin_style'] == 'regulation' || $this->module_config['skin_style'] == 'solicit' || $this->module_config['skin_style'] == 'approval_ex' || $this->module_config['skin_style'] == 'approval_health' || $this->module_config['skin_style'] == 'solicit_cleen' || $this->module_config['skin_style'] == 'address_regulation' || $this->module_config['skin_style'] == 'monitor') {

        if ($data['process_2'] != '1' && $this->permission['manage'] == false) {
          call::xml_error('209', '', $this->referer); //진행중인 글은 수정 못한다.11
        }
      } else if ($this->module_config['skin_style'] == 'ys_singo' || $this->module_config['skin_style'] == 'ys_singo_coast' || $this->module_config['skin_style'] == 'ys_reserve_minwon') {
        $query2 = sprintf('SELECT idx, reg_pin, process_1, charge_id FROM %s WHERE idx = "%s"', $this->module_config['table_name'], $data['pidx']);
        $data2 = $mysql->query_fetch($query2);

        if (!($data2['process_2'] == '신청')) { ## 신청이 아니면서
          if (!($this->permission['manage'] == true || $this->permission['admin'] == true)) { ## 자기자신이거나 담당이거나 관리자가 아닐때
            call::xml_error('209', '', $this->referer); //진행중인 글은 수정 못한다.
          }
        }
      }

      $data['modify_date'] = date('Y-m-d H:i:s');
      $ARTICLES = $data;


    } else if ($mode == 'reply') {
      if (empty($idx)) call::xml_error('152', '', $this->referer);
      if ($this->module_config['use_reply'] == 'none') call::xml_error('201', '답변글쓰기가 금지되어 있습니다.', $this->referer);
      if ($this->module_config['use_reply'] == 'admin' && $this->permission['admin'] !== true) call::xml_error('201', '답변글쓰기가 금지되어 있습니다.', $this->referer);

      // sort 수정 추가 - 기존의 pidx, seq를 사용한 쿼리가 너무 느려서 sort 필드 하나만 이용하도록 수정함.
      $query = sprintf('SELECT contents, title, pidx, level, seq, sort, reg_name, depart_name, reg_date FROM %s WHERE idx = "%s"', $this->module_config['table_name'], $idx);
      $data = $mysql->query_fetch($query);

      $ARTICLES['contents'] = '[원본글]' . "\n";
      $ARTICLES['contents'] .= ($this->module_config['writer_display'] == 'department' ? $data['depart_name'] : $data['reg_name']) . '님이 ' . date("Y-m-d", strtotime($data['reg_date'])) . '에 작성한 글입니다.' . "\n";
      $ARTICLES['contents'] .= '제목 : ' . $data['title'] . "\n";
      $ARTICLES['contents'] .= $data['contents'] . "\n\n";
      $ARTICLES['contents'] .= '[답변내용]';
    }

    if (empty($ARTICLES['contents']) && !empty($this->module_config['default_contents'])) $ARTICLES['contents'] = $this->module_config['default_contents'];


    ## ====================================================
    ##               모드별 선행처리 end
    ## ====================================================


    ## 필수값 체크
    if (!empty($null_check)) {
      $null_data = array();
      foreach ($_POST as $key => $val) if (!empty($val)) $null_data[$key] = $val;
      $null_data['null_check'] = $null_check;
      $data = count($data) > 0 ? array_merge($data, $null_data) : $null_data;
      $ARTICLES = $data;
    }

    ## 비로그인 회원 글쓰기 설정
    if ($this->myinfo['is_login'] !== true && $mode != 'modify') {
      if ($this->module_config['use_logoff_write'] == 'true') {
        ## 최초 글 쓸 경우 여기에서 text_code를 세션으로 구운 다음에 save에서 그 값을 form값과 비교한다. 그런 다음에 save를 마치고나서 session을 distory 한다.
        if ($mode != 'modify') {
          $_SESSION['text_keycode'] = $this->get_board_keycode();
          $ARTICLES['use_logoff_write'] = $this->module_config['use_logoff_write'];
          $ARTICLES['text_keycode'] = $_SESSION['text_keycode'];
        }
        ## 수정, 삭제 시에는 password_idx를 세션으로 구운다. 여기에서는 그값이 없을 경우 password를 입력하라는 form을 띄운다.
      } else {
        ## 게시판의 권한이 비로그인 회원이 글을 쓸수 있을지라도 설정에서 비로그인 회원의 글쓰기를 허용 해주지 않으면 글을 쓸 수 없다.
        call::xml_error('202', '', $this->referer);
      }
    }

    ## 이름입력 설정
    // 글 등록시 작성자명 입력하는 부분 : 현재는 관리자만 입력할(바꿀)수 있게 되어있는데 이부분 수정되어야 한다.
    //  => sns 또는 비회원 글 작성시 이름이 없을 경우 이름을 입력할 수 있게 되어야 한다.
    //  => 수정시에는 이름을 변경할 수 없도록 한다. ??? => 이부분은 정운영 소장 다시 문의.
    //  => 부서등록 부분과 같이 연계되어야 한다.

    ##2018.01.30 김경남 수정 문화예술회관DB자료, 시민문화체육센터DB자료만 적용 (글쓰기 등록시 등록자 미입력시 등록자 명에 관리자로 보이게)
    if ($this->module_config['board_id'] == "art_culture_data" || $this->module_config['board_id'] == "art_data") {
      if (empty($ARTICLES['reg_name'])) $ARTICLES['reg_name'] = $this->module_config['use_nick_name'] == 'true' ? $this->myinfo['user_nick'] : "관리자";
    } elseif ($this->hostname == 'seafountain' && $this->myinfo['user_id'] == 'jgy1237') { ## 바다분수축제 제갈연 주무관 등록자명 바로 관리자로 나오게 수정
      if (empty($ARTICLES['reg_name'])) $ARTICLES['reg_name'] = "관리자";
    } else {
      ## 2012.03.29 오경우 수정 : 타기관소식에서 타기관명을 user_nick 필드에 넣어서 사용하도록 변경함.
      //$ARTICLES['reg_name'] = empty($ARTICLES['reg_name']) ? $this->myinfo['user_name'] : $ARTICLES['reg_name'];
      if (empty($ARTICLES['reg_name'])) $ARTICLES['reg_name'] = $this->module_config['use_nick_name'] == 'true' ? $this->myinfo['user_nick'] : $this->myinfo['user_name'];

    }

    //$ARTICLES['depart_name'] = empty($ARTICLES['depart_name']) ? $this->myinfo['dept']['name'] : $ARTICLES['depart_name'];
    $ARTICLES['depart_name'] = empty($ARTICLES['depart_name']) ? $_SESSION['dept_name'] : $ARTICLES['depart_name'];

    $ARTICLES['writer_display'] = $this->module_config['writer_display'];


    //$ARTICLES['read_only_reg_name'] = ($this->permission['admin'] === true || empty($ARTICLES['reg_name'])) ? 'false' : 'true';
    $ARTICLES['read_only_reg_name'] = ($this->permission['admin'] === true || empty($ARTICLES['reg_name']) || $this->myinfo['user_level'] <= 6) ? 'false' : 'true';

    ## 비공개글 설정
    if ($this->module_config['use_lock'] == 'true') {
      $ARTICLES['use_lock'] = $this->module_config['use_lock'];
      if (empty($ARTICLES['open'])) $ARTICLES['open'] = $this->module_config['lock_default'] == 'open' ? 'y' : 'n';
    }

    ## TOP설정
    if ($this->module_config['use_top'] == 'true') {
      $ARTICLES['use_top'] = $this->module_config['use_top'];
    }

    ## 기본분류 설정.
    if ($this->module_config['use_category_1'] == 'true' && is_array($this->module_config['category_1'])) {
      $ARTICLES['use_category_1'] = $this->module_config['use_category_1'];
      $ARTICLES['category_1'] = $data['category_1'];
      $ARTICLES['category_1_all'] = serialize($this->module_config['category_1']);
    }

    ## 기본분류 설정.
    if ($this->module_config['use_category_2'] == 'true' && is_array($this->module_config['category_2'])) {
      $ARTICLES['use_category_2'] = $this->module_config['use_category_2'];
      $ARTICLES['category_2'] = $data['category_2'];
      $ARTICLES['category_2_all'] = serialize($this->module_config['category_2']);
    }

    ## 베너존일 경우 이미지 사이즈
    if ($this->module_config['use_banner'] == 'true') {
      $ARTICLES['banner_size_width'] = $this->module_config['banner_size_width'];
      $ARTICLES['banner_size_height'] = $this->module_config['banner_size_height'];
      $ARTICLES['banner_allow_schedule'] = $this->module_config['banner_allow_schedule'];
    }

    if (!empty($this->module_config['use_list_size']) && $this->module_config['use_list_size'] == 'true') {
      $ARTICLES['list_size_width'] = $this->module_config['list_size_width'];
      $ARTICLES['list_size_height'] = $this->module_config['list_size_height'];
    }
    ## tag 설정
    if ($this->module_config['use_tag'] == 'true') {
      $ARTICLES['use_tag'] = $this->module_config['use_tag'];

      ## tag 적용
      if (!empty($this->module_config['source_id'])) {
        $original_board_id = $this->module_config['board_id'];
        $this->module_config['board_id'] = $this->module_config['source_id'];
      }

      ## tag를 사용할 경우 source_id의 config에 있는 tag_list_all 값을 가져와야 한다.
      $source_config = $this->get_board_config($this->module_config['board_id']);
      $ARTICLES['tag_list_all'] = $source_config['tag_list_all'];

      if (empty($data['tag_list'])) {
        $data['tag_list'] = empty($data['tag']) ? $this->module_config['tag_list'] : explode('|', $data['tag']);
      }

      // 2012.03.22 오경우 수정 : 아래로 대체
      //if($original_board_id != $this->module_config['source_id']) $ARTICLES['tag_list'] = $data['tag_list'];
      $ARTICLES['tag_list'] = $data['tag_list'];
    }

    //첨부파일기능 수정 : 오경우 (20120120)
    ## 첨부파일
    $ARTICLES['file_upload_count'] = $this->module_config['file_upload_count'];
    if ($mode != 'reply' && !empty($idx)) {
      $file_list = $this->attached_file_list($idx);
      $ARTICLES['file_list'] = $file_list;
    }
    //첨부파일기능 수정 : 오경우 (20120120)

    ## 첨부파일 리스트(업소등록 - 사업자등록증, 컨텐츠수집표)
    if (!empty($idx)) {
      $file_list_shop = $this->attached_file_list_shop1($idx);
      $ARTICLES['file_list_shop1'] = $file_list_shop;

      $file_list_shop = $this->attached_file_list_shop2($idx);
      $ARTICLES['file_list_shop2'] = $file_list_shop;
    }

    ## 이미지 리스트
    if (!empty($idx)) {
      $img_file_list = $this->attached_photo_ajax_list($idx, $this->module_config['table_name']);
      $ARTICLES['img_file_list'] = $img_file_list;
    }

    ## 비회원정보 수집 (주소, 연락처)
    ## 회원구분이 복잡하기 때문에(sns등) 관리자가 아닐경우 모든 회원에 대해서 정보를 수집한다.
    if ($this->module_config['get_guest_info'] == 'true' && ($this->myinfo['user_level'] == 11 || $this->myinfo['user_level'] == 99)) {
      $ARTICLES['get_guest_info'] = 'true';
      if ($mode == 'write') {
        ## get_user_info() : 임시적으로 만든것이다. user management class가 완성되면 수정해야 한다.
        $user_info = $this->get_user_info($data['reg_pin']);
        $ARTICLES['phone'] = $user_info['phone'];
        $ARTICLES['zipcode'] = $user_info['zipcode'];
        $ARTICLES['address1'] = $user_info['address1'];
        $ARTICLES['address2'] = $user_info['address2'];
      }
    }

    ## 제목 스타일 적용
    if ($this->module_config['use_title_style'] == 'true') {
      $ARTICLES['use_title_style'] = $this->module_config['use_title_style'];
      $ARTICLES['title_style_list'] = $this->module_config['title_style'];
      $ARTICLES['title_style'] = $data['title_style'];
    }

    $ARTICLES['use_editor'] = $this->module_config['use_editor'] == 'true' ? 'true' : 'false'; ## 웹에디터 사용유무
    $ARTICLES['use_map'] = $this->module_config['use_map'] == 'true' ? 'true' : 'false'; ## 지도 사용유무
    $ARTICLES['use_search_tag'] = $this->module_config['use_search_tag'] == 'true' ? 'true' : 'false'; ## 검색단어 등록 사용유무
    if ($this->module_config['use_search_tag'] == 'true' && !empty($_POST['search_tag'])) $ARTICLES['search_tag'] = $_POST['search_tag'];
    $ARTICLES['permission'] = $this->permission;

    $ARTICLES['contents_en'] = $data['contents_en'];
    $ARTICLES['contents_jp'] = $data['contents_jp'];
    $ARTICLES['contents_cn'] = $data['contents_cn'];
    ## 새로운공연전시 기능 추가 : 2012.04.17 오경우
    if ($this->module_config['use_event_skin'] == 'true' && !empty($data['contents_cn'])) {
      $concert_list = unserialize(base64_decode($data['contents_cn']));
      for ($iter = 1; $iter <= 10; $iter++) {
        $ARTICLES['concert_' . $iter . '_start'] = $concert_list[$iter]['start'];
        $ARTICLES['concert_' . $iter . '_end'] = $concert_list[$iter]['end'];
      }
    }


    ## 2014.08.04 오경우 확장필드 추가
    $ARTICLES['varchar_1'] = $data['varchar_1'];
    $ARTICLES['varchar_2'] = $data['varchar_2'];
    $ARTICLES['varchar_3'] = $data['varchar_3'];
    $ARTICLES['varchar_4'] = $data['varchar_4'];
    $ARTICLES['varchar_5'] = $data['varchar_5'];
    $ARTICLES['varchar_6'] = $data['varchar_6'];
    $ARTICLES['varchar_7'] = $data['varchar_7'];
    $ARTICLES['varchar_8'] = $data['varchar_8'];
    $ARTICLES['varchar_9'] = $data['varchar_9'];
    $ARTICLES['varchar_10'] = $data['varchar_10'];

    ## 2015.10.13 윤지미 처리상태 표시를 위해 필드 추가
    if ($this->module_config['skin_style'] == 'shop_registration') {
      $ARTICLES['admin_comment'] = isset($data['admin_comment']) ? str_replace("\n", '<br />', $data['admin_comment']) : NULL;
      $ARTICLES['process_1'] = isset($data['process_1']) ? $data['process_1'] : NULL;
    }

    ## 2015.09.24 서희진 추가
    ## 후기 스킨일떄 여분필들에 값을 가져온다.
    if ($this->module_config['skin_style'] == 'epilogue') {
//			$ARTICLES['option'] = unserialize(base64_decode($data['varchar_1']));
      $ARTICLES['tourlist'] = explode("|", $data['varchar_1']);                      // 여행코스
      $ARTICLES['tour_object'] = isset($data['varchar_2']) ? explode("|", $data['varchar_2']) : '기타';  // 여행목적
      ## 통계를 위한 필드
      $ARTICLES['tour_object_etc'] = isset($data['varchar_3']) ? $data['varchar_3'] : NULL;        // 기타여행목적
      $ARTICLES['tour_age'] = isset($data['varchar_4']) ? $data['varchar_4'] : NULL;            // 연령
      $ARTICLES['tour_person'] = isset($data['varchar_5']) ? $data['varchar_5'] : NULL;          // 인원
      $ARTICLES['tour_method'] = isset($data['varchar_7']) ? explode("|", $data['varchar_7']) : '기타';  // 여행수단
      $ARTICLES['tour_method_etc'] = isset($data['varchar_8']) ? $data['varchar_8'] : NULL;        // 기타여행수단
      $ARTICLES['tour_return'] = isset($data['varchar_9']) ? $data['varchar_9'] : NULL;          // 재방문의사
      $ARTICLES['tour_return_term'] = isset($data['varchar_10']) ? $data['varchar_10'] : NULL;      // 재방문기간
      $ARTICLES['period_start'] = isset($data['period_start']) ? $data['period_start'] : NULL;      // 여행기간 시작일
      $ARTICLES['period_end'] = isset($data['period_end']) ? $data['period_end'] : NULL;          // 여행기간 종료일
    }

    ## 답변글 sms 발송여부
    $ARTICLES['mode'] = $mode;
    if ($this->module_config['use_reply_sms'] == 'all' || $this->module_config['use_reply_sms'] == 'admin') {
      $ARTICLES['use_reply_sms'] = $this->module_config['use_reply_sms'];
      ## 원본글에서 작성자의 핸드폰 번호가 있을 경우 그것을 사용하며,
      ## 핸드폰 번호가 없을 경우 글 작성자의 아이디로 핸드폰 번호를 뽑아오는 로직을 추가해야 한다.
      $ARTICLES['reply_sms_recv_number'] = $data['reply_sms_recv_number'];
      $ARTICLES['reply_sms_recv_number'] = empty($ARTICLES['reply_sms_recv_number']) ? $data['phone'] : $ARTICLES['reply_sms_recv_number'];
      //$ARTICLES['reply_sms_recv_number'] = empty($ARTICLES['reply_sms_recv_number']) ? $user_info['phone'] : $ARTICLES['reply_sms_recv_number'];
    } else {
      $this->module_config['use_reply_sms'] = 'none';
    }

    ## hidden 값 설정
    $hidden = array();
    $hidden['mode'] = $mode == 'modify' ? 'change' : 'save';
    if ($mode == 'reply') {
      $hidden['pidx'] = $data['pidx'];
      $hidden['seq'] = $data['seq'];
      $hidden['level'] = $data['level'];
      // sort 수정 추가 - 기존의 pidx, seq를 사용한 쿼리가 너무 느려서 sort 필드 하나만 이용하도록 수정함.
      $hidden['sort'] = $data['sort'];
    }
    if ($mode == 'modify') $hidden['idx'] = $data['idx'];
    if ($page > 1) $hidden['page'] = $page;
    if (!empty($search_type)) $hidden['search_type'] = $search_type;
    if (!empty($search_word)) $hidden['search_word'] = $search_word;
    //if(!empty($category_1)) $hidden['category_1'] = $category_1;

    $ARTICLES['hidden'] = $hidden;


    ## 파라미터 설정 : 버튼
    $arr_parameter = array();
    if ($page > 1) $arr_parameter['page'] = $page;
    if (!empty($search_type)) $arr_parameter['search_type'] = $search_type;
    if (!empty($search_word)) $arr_parameter['search_word'] = $search_word;
    //if(!empty($category_1))  $arr_parameter['category_1']  = $category_1;
    $ARTICLES['parameter'] = $arr_parameter;

    //2012-10-09 황재복 : 상단문구 출력안되어 추가
    $ARTICLES['write_msg'] = $this->module_config['write_msg'];

    ##테마 적용 - 20200423 서희진
    $ARTICLES['use_theme'] = $this->module_config['use_theme'];
    if ($this->module_config['use_theme'] == "true") {
      $ARTICLES['theme_list'] = $this->module_config['theme_list'];
    }

    ##모듈 공공누리 설정 적용 - 20171212 서희진
    $ARTICLES['use_open_type'] = ($_SYSTEM['menu_info']['open_type'] == "1" ? "true" : "false");

    ## skin 설정
    $ARTICLES['device'] = $this->device;
    $ARTICLES['module_root'] = $this->module_root;                  ## 근본적으로 스킨쪽에서 세팅되어야 한다.
    $ARTICLES['module_path'] = $this->module_config['module_path']; ## 근본적으로 스킨쪽에서 세팅되어야 한다.
    $ARTICLES['skin_style'] = empty($this->module_config['skin_style']) ? 'default' : $this->module_config['skin_style'];
    $ARTICLES['skin_name'] = 'write_reply';


    echo serialize($ARTICLES);
    return true;
  }

  ## 저장
  public function save()
  {

    global $mysql;
    global $_SYSTEM;
    $data = array();
    $db_data = array();
    $files = array();

    $data = $_POST;
    $files = $_FILES;
    $file = array();

    $mainImg = 0;
    if (count($files['filenameImg']['name']) > 0) {
      for ($iter = 0; $iter < count($files['filenameImg']['name']); $iter++) {
        $tmp = array();
        if (!empty($files['filenameImg']['name'][$iter])) {
          $tmp['name'] = $files['filenameImg']['name'][$iter];
          $tmp['type'] = $files['filenameImg']['type'][$iter];
          $tmp['tmp_name'] = $files['filenameImg']['tmp_name'][$iter];
          $tmp['error'] = $files['filenameImg']['error'][$iter];
          $tmp['size'] = $files['filenameImg']['size'][$iter];
          $tmp['main'] = 'true';

          $file['file_' . $mainImg] = $tmp;
          $mainImg++;
          //$mainImg = $mainImg + count($files['filenameImg']['name']) ;
        }
      }
    }
    if (count($files['filename']['name']) > 0) {
      //20230111::이광배::배열명이 다른데 0부터 시작안하면 우얍니꺼?
      //for( $iter=$mainImg; $iter<count($files['filename']['name'])+$mainImg; $iter++ ){
      for ($iter = 0; $iter < count($files['filename']['name']); $iter++) {

        $tmp = array();
        if (!empty($files['filename']['name'][$iter])) {
          $tmp['name'] = $files['filename']['name'][$iter];
          $tmp['type'] = $files['filename']['type'][$iter];
          $tmp['tmp_name'] = $files['filename']['tmp_name'][$iter];
          $tmp['error'] = $files['filename']['error'][$iter];
          $tmp['size'] = $files['filename']['size'][$iter];
          $tmp['main'] = 'false';

          $file['file_' . $mainImg] = $tmp;
          $mainImg++;
        }
      }
    }


    ## parameter setting.
    $page = empty($data['page']) ? 1 : $data['page'];
    $mode = empty($data['mode']) ? 'save' : $data['mode'];
    $search_type = empty($data['search_type']) ? NULL : $data['search_type'];
    $search_word = empty($data['search_word']) ? NULL : $data['search_word'];
    //$category_1  = empty($data['category_1']) ? NULL : $data['category_1'];


    ## 모드별 선행처리. - 권한 확인
    // 관리자만 글을 쓸수 있는 옵션일 경우 : faq 등의 게시판등에서 사용됨.
    if ($this->module_config['write_admin_only'] == 'true' && $this->permission['admin'] !== true) call::xml_error('204', '', $this->referer);


    if ($mode == 'change') {

      $sub_data = array();
      if (empty($data['idx'])) call::xml_error('152', '', $this->referer);
      $query = sprintf('SELECT idx, pidx, reg_pin, passwd, process_2 FROM %s WHERE idx = "%s"', $this->module_config['table_name'], $data['idx']);
      $sub_data = $mysql->query_fetch($query);

      if ($this->myinfo['is_login'] === true) { ## 로그인 회원의 경우 관리자 또는 등록자만 수정이 허용된다.
        if ($this->permission['admin'] !== true && $this->myinfo['my_pin'] != $sub_data['reg_pin']) call::xml_error('204', '', $this->referer);
      } else { ## 비로그인 회원 글쓰기 허용 확인
        if ($_SESSION['logoff_confirm'] != md5($data['idx'] . $sub_data['reg_pin'] . $sub_data['passwd'])) call::xml_error('204', '', $this->referer);
      }
      if ($this->module_config['skin_style'] == 'approval' || $this->module_config['skin_style'] == 'regulation' || $this->module_config['skin_style'] == 'solicit' || $this->module_config['skin_style'] == 'approval_ex' || $this->module_config['skin_style'] == 'approval_health' || $this->module_config['skin_style'] == 'solicit_cleen' || $this->module_config['skin_style'] == 'address_regulation' || $this->module_config['skin_style'] == 'monitor') {
        if ($sub_data['process_2'] != '1' && $this->permission['manage'] == false) {
          call::xml_error('209', '', $this->referer); //진행중인 글은 수정 못한다.
        }
      } else if ($this->module_config['skin_style'] == 'ys_singo' || $this->module_config['skin_style'] == 'ys_singo_coast' || $this->module_config['skin_style'] == 'ys_reserve_minwon') {
        $query2 = sprintf('SELECT idx, reg_pin, process_1, charge_id FROM %s WHERE idx = "%s"', $this->module_config['table_name'], $sub_data['pidx']);
        $data2 = $mysql->query_fetch($query2);

        if (!($data2['process_2'] == '신청')) { ## 신청이 아니면서
          if (!($this->permission['manage'] == true || $this->permission['admin'] == true)) { ## 자기자신이거나 담당이거나 관리자가 아닐때
            call::xml_error('209', '', $this->referer); //진행중인 글은 수정 못한다.
          }
        }
      }

      $data['modify_date'] = date('Y-m-d H:i:s');

    } else {
      if ($mode == "reply_save") {
        $query = sprintf('SELECT idx, phone, c_phone FROM %s WHERE idx = "%s"', $this->module_config['table_name'], $data['pidx']);
        $sub_data = $mysql->query_fetch($query);
      }
      if ($_SERVER['REMOTE_ADDR'] == '49.254.140.140') {
#    echo '<pre>'; print_r($this->myinfo['is_login']); exit;
#    echo '<pre>'; print_r(getimagesize($files['filename']['tmp_name'][0])); exit;
      }
      if ($this->myinfo['is_login'] === true) { ## 로그인 회원.
        if (empty($this->myinfo['my_pin'])) call::xml_error('202', '', $this->referer);
      } else { ## 비로그인 회원 글쓰기 허용 확인
        if ($this->module_config['use_logoff_write'] != 'true') call::xml_error('202', '', $this->referer);
        else $this->myinfo['my_pin'] = GUEST_PIN;
      }
    }

    ## 필수입력값 확인, 사진업로드 확장자 확인, 업로드 허용확장자 확인
    if ($mode != "reply_save" && $mode != "reply_change" && $this->module_config['skin_style'] != "ys_request_insentive") { ##답변등록시 필수 체크 없음. ##221006 권재영 write에서 체크
      $null_check_result = $this->null_check($mode, $data, $file);
    }


    if (!empty($null_check_result)) {
      $_POST['null_check'] = $null_check_result;
      if ($mode == 'change') $_POST['mode'] = 'modify';
      else                     $_POST['mode'] = empty($data['pidx']) ? 'write' : 'reply';

      $this->write();
      return false;
    }

    ## 비로그인 회원 글쓰기에 사용된 세션값 삭제
    if (isset($_SESSION['text_keycode'])) unset($_SESSION['text_keycode']);
    if (isset($_SESSION['logoff_confirm'])) unset($_SESSION['logoff_confirm']);

    ## 디비값 세팅
    $db_data['reg_name'] = empty($data['reg_name']) ? $this->myinfo['user_name'] : $data['reg_name'];
    $db_data['depart_name'] = empty($data['depart_name']) ? $this->myinfo['dept']['parent_name'] : $data['depart_name'];
    $db_data['search_tag'] = isset($data['search_tag']) ? $data['search_tag'] : NULL; ## 검색단어 등록 사용유무
    ## 멀티호스트 : 사용하고 있지 않아서 주석처리 - 20170919 서희진
    //$db_data['hostname']   = $this->module_config['use_multihost'] == 'true' ? $this->hostname : NULL;


    $db_data['hostname'] = $this->hostname;
    //	$db_data['html_tag']   = isset($data['html_tag']) ? $data['html_tag'] : 'n';
    $db_data['html_tag'] = ($mode == 'change' && $this->permission['admin'] == true) ? 'y' : 'n';
    $db_data['open'] = isset($data['open']) ? $data['open'] : NULL;
    if ($this->module_config['skin_style'] != 'dictionary') {
      $db_data['category_1'] = isset($data['category_1']) ? $data['category_1'] : NULL;
    }
    if ($this->module_config['skin_style'] == 'dictionary') {
      $db_data['title'] = substr($data['title'], 0, 3);
      //		$spelling = '';
      if (preg_match('/[가-낗]+/u', $db_data['title'])) $db_data['category_1'] = '가';
      if (preg_match('/[나-닣]+/u', $db_data['title'])) $db_data['category_1'] = '나';
      if (preg_match('/[다-띻]+/u', $db_data['title'])) $db_data['category_1'] = '다';
      if (preg_match('/[라-맇]+/u', $db_data['title'])) $db_data['category_1'] = '라';
      if (preg_match('/[마-밓]+/u', $db_data['title'])) $db_data['category_1'] = '마';
      if (preg_match('/[바-삫]+/u', $db_data['title'])) $db_data['category_1'] = '바';
      if (preg_match('/[사-앃]+/u', $db_data['title'])) $db_data['category_1'] = '사';
      if (preg_match('/[아-잏]+/u', $db_data['title'])) $db_data['category_1'] = '아';
      if (preg_match('/[자-찧]+/u', $db_data['title'])) $db_data['category_1'] = '자';
      if (preg_match('/[차-칳]+/u', $db_data['title'])) $db_data['category_1'] = '차';
      if (preg_match('/[카-킿]+/u', $db_data['title'])) $db_data['category_1'] = '카';
      if (preg_match('/[타-팋]+/u', $db_data['title'])) $db_data['category_1'] = '타';
      if (preg_match('/[파-핗]+/u', $db_data['title'])) $db_data['category_1'] = '파';
      if (preg_match('/[하-힣]+/u', $db_data['title'])) $db_data['category_1'] = '하';
    }

    $db_data['title'] = $data['title'];
    //2015-09-23 황재복 :
    //$db_data['contents']   = $data['contents'];

    ############################################ 2020 신규 모듈 콘텐츠 등록 폼. ###################################
    if (is_array($data['contents'])) {
      $db_data['contents_original'] = base64_encode(serialize($data['contents']));
      $db_data['html_tag'] = "a";
      $contets_marge = '';

      foreach ($data['contents'] as $con_buffer) {
        preg_match_all("/<img[^>]*src=[\"']?([^>\"']+)[\"']?[^>]*>/i", $con_buffer, $matches);
        if (!is_array($con_buffer) && count($matches[0]) == 0) {
          $contets_marge .= $con_buffer;
        }
      }
      $db_data['contents'] = preg_replace('/(<\?xml[^>]+>)/', '', $contets_marge);;
    } else {
      $db_data['contents'] = preg_replace('/(<\?xml[^>]+>)/', '', $data['contents']);
    }
    #################################################################################################################

    if ($mode == "reply_save" || $mode == "reply_change") {
      $db_data['contents'] = addslashes($data['contents']);
    }


    $db_data['linkbox'] = isset($data['linkbox']) ? base64_encode(serialize($data['linkbox'])) : '';

    $db_data['zipcode'] = (isset($data['zipcode'])) ? $data['zipcode'] : NULL;
    $db_data['address_1'] = isset($data['address_1']) ? $data['address_1'] : NULL;
    $db_data['address_2'] = isset($data['address_2']) ? $data['address_2'] : NULL;
    $db_data['phone'] = (isset($data['phone_1']) && isset($data['phone_2']) && isset($data['phone_3'])) ? $data['phone_1'] . '-' . $data['phone_2'] . '-' . $data['phone_3'] : NULL;
    $db_data['passwd'] = isset($data['passwd']) ? $data['passwd'] : NULL;
    if (is_array($data['tag_list'])) $db_data['tag'] = implode('|', $data['tag_list']);
    $db_data['reply_sms_recv_number'] = isset($data['reply_sms_recv_number']) ? $data['reply_sms_recv_number'] : NULL; ## 답변글 SMS사용시 수신번호 저장
    $db_data['title_style'] = isset($data['title_style']) ? $data['title_style'] : NULL;
    $db_data['link_url'] = isset($data['link_url']) ? $data['link_url'] : NULL;
    $db_data['top'] = isset($data['top']) ? $data['top'] : "n";
    $db_data['top_start'] = (isset($data['top_start']) && !empty($data['top_start'])) ? $data['top_start'] : NULL;
    $db_data['top_end'] = (isset($data['top_end']) && !empty($data['top_end'])) ? $data['top_end'] : NULL;
    //2012.09.20 강성수 email 칼럼추가, 입력, 수정로직추가
    $db_data['email'] = isset($data['email']) ? $data['email'] : NULL;
    if ($mode == 'save' && ($this->module_config['skin_style'] == 'approval' || $this->module_config['skin_style'] == 'regulation' || $this->module_config['skin_style'] == 'solicit' || $this->module_config['skin_style'] == 'approval_ex' || $this->module_config['skin_style'] == 'approval_health' || $this->module_config['skin_style'] == 'solicit_cleen' || $this->module_config['skin_style'] == 'address_regulation' || $this->module_config['skin_style'] == 'monitor')) {
      $db_data['process_2'] = isset($data['process_2']) ? $data['process_2'] : '1'; //신청상태로 등록함...
    }

    $db_data['category_2'] = isset($data['category_2']) ? $data['category_2'] : NULL;

    $db_data['organ'] = isset($data['organ']) ? $data['organ'] : NULL;
    $db_data['sex'] = $data['sex'];
    $db_data['age'] = isset($data['age']) ? $data['age'] : NULL;
    $db_data['hphone'] = (isset($data['hphone_1']) && isset($data['hphone_2']) && isset($data['hphone_3'])) ? $data['hphone_1'] . '-' . $data['hphone_2'] . '-' . $data['hphone_3'] : NULL;

    $db_data['contents_en'] = isset($data['contents_en']) ? $data['contents_en'] : NULL;
    $db_data['contents_jp'] = isset($data['contents_jp']) ? $data['contents_jp'] : NULL;
    $db_data['contents_cn'] = isset($data['contents_cn']) ? $data['contents_cn'] : NULL;

    $db_data['process_3'] = isset($data['process_3']) ? $data['process_3'] : NULL;

    if ($mode == 'save' && $this->module_config['skin_style'] == 'regulation') {
      $data['varchar_1'] = date("ymdHis");
    }
    ## 2014.08.04 오경우 확장필드 추가
    $db_data['varchar_1'] = isset($data['varchar_1']) ? $data['varchar_1'] : NULL;
    $db_data['varchar_2'] = isset($data['varchar_2']) ? $data['varchar_2'] : NULL;
    $db_data['varchar_3'] = isset($data['varchar_3']) ? $data['varchar_3'] : NULL;
    $db_data['varchar_4'] = isset($data['varchar_4']) ? $data['varchar_4'] : NULL;
    $db_data['varchar_5'] = isset($data['varchar_5']) ? $data['varchar_5'] : NULL;
    $db_data['varchar_6'] = isset($data['varchar_6']) ? $data['varchar_6'] : NULL;
    $db_data['varchar_7'] = isset($data['varchar_7']) ? $data['varchar_7'] : NULL;
    $db_data['varchar_8'] = isset($data['varchar_8']) ? $data['varchar_8'] : NULL;
    $db_data['varchar_9'] = isset($data['varchar_9']) ? $data['varchar_9'] : NULL;
    $db_data['varchar_10'] = isset($data['varchar_10']) ? $data['varchar_10'] : NULL;
    $db_data['varchar_11'] = isset($data['varchar_11']) ? $data['varchar_11'] : NULL;
    $db_data['varchar_12'] = isset($data['varchar_12']) ? $data['varchar_12'] : NULL;
    $db_data['varchar_13'] = isset($data['varchar_13']) ? $data['varchar_13'] : NULL;
    $db_data['varchar_14'] = isset($data['varchar_14']) ? $data['varchar_14'] : NULL;
    $db_data['varchar_15'] = isset($data['varchar_15']) ? $data['varchar_15'] : NULL;
    $db_data['process_1'] = isset($data['process_1']) ? $data['process_1'] : NULL;
    $db_data['modify_date'] = isset($data['modify_date']) ? $data['modify_date'] : NULL;
    $db_data['tel'] = !empty($data['tel_1']) ? $data['tel_1'] . '-' . $data['tel_2'] . '-' . $data['tel_3'] : "";

    ## --- 2020 신규 기능. 키워드 및 테마
    $db_data['search_tag'] = isset($data['keyword']) ? $data['keyword'] : NULL;
    $db_data['theme'] = isset($data['theme']) ? $data['theme'] : NULL;

    /* 나주부터 필수 체크 */
    //if($this->module_config['use_display_date'] == 'true' ) {
    //$db_data['use_period'] = isset($data['use_period']) ? $data['use_period'] : 'n';
    $db_data['use_period'] = isset($data['use_period']) ? $data['use_period'] : 'n';
    $db_data['use_period'] = isset($data['use_period']) ? $data['use_period'] : 'n';
    $db_data['period_start'] = isset($data['period_start']) ? date('Y-m-d', strtotime($data['period_start'])) : NULL;      // 게시물 시작일
    $db_data['period_end'] = isset($data['period_end']) ? date('Y-m-d', strtotime($data['period_end'])) : NULL;          // 게시품 종료일
    //}

    ##모듈 공공누리 설정 적용 - 20171212 서희진
    $db_data['kogl_type'] = empty($data['kogl_type']) ? "0" : $data['kogl_type'];

    ## 새로운공연전시 기능 추가 : 2012.04.17 오경우
    if ($this->module_config['use_event_skin'] == 'true') {
      for ($iter = 1; $iter <= 10; $iter++) {
        $concert_list[$iter]['start'] = isset($data['concert_' . $iter . '_start']) ? $data['concert_' . $iter . '_start'] : NULL;
        $concert_list[$iter]['end'] = isset($data['concert_' . $iter . '_end']) ? $data['concert_' . $iter . '_end'] : NULL;
      }
      $db_data['contents_cn'] = base64_encode(serialize($concert_list));
    }

    ## 2015.09.24 서희진 추가
    ## 후기 스킨일떄 여분필들에 값을 넣는다.
    ######################################################################
    ## 20180628 상단팝업 host설정
    if ($this->module_config['skin_style'] == 'popup_color_multihost') {
      $db_data['varchar_10'] = '|' . implode("|", $data['host_list']) . '|';
    }
    ######################################################################

    if ($mode == 'save' || $mode == 'reply_save') { ## insert
      if (empty($data['pidx'])) {
        $data['pidx'] = 0;
        $data['level'] = 0;
        $data['seq'] = 0;
        // sort 수정 추가 - 기존의 pidx, seq를 사용한 쿼리가 너무 느려서 sort 필드 하나만 이용하도록 수정함.
        $data['sort'] = 0;
      } else { ## 답변글일 경우
        $query = sprintf('UPDATE %s SET seq = seq+1 WHERE pidx = "%s" AND seq > "%s"', $this->module_config['table_name'], $data['pidx'], $data['seq']);
        $mysql->query($query);
        $data['level']++;
        $data['seq']++;

        // sort 수정 추가 - 기존의 pidx, seq를 사용한 쿼리가 너무 느려서 sort 필드 하나만 이용하도록 수정함.
        $query = sprintf('UPDATE %s SET sort = sort-0.01 WHERE pidx = "%s" AND sort < %s', $this->module_config['table_name'], $data['pidx'], floatval($data['sort']));
        $mysql->query($query);
        $data['sort'] -= 0.01;
      }

      $db_data['pidx'] = $data['pidx'];
      $db_data['level'] = $data['level'];
      $db_data['seq'] = $data['seq'];
      // sort 수정 추가 - 기존의 pidx, seq를 사용한 쿼리가 너무 느려서 sort 필드 하나만 이용하도록 수정함.
      $db_data['sort'] = $data['sort'];


      ## tag 적용
      if ($this->module_config['use_tag'] == 'true') {
        if (!empty($this->module_config['source_id'])) $this->module_config['board_id'] = $this->module_config['source_id'];
      }

      $db_data['board_id'] = $this->module_config['board_id'];
      $db_data['reg_pin'] = $this->myinfo['my_pin'];
      $db_data['reg_id'] = $this->myinfo['user_id'];
      $db_data['reg_name_real'] = $this->myinfo['user_name'];

      ## 2012.09.26 오경우 추가
      if ($this->module_config['use_allow'] == 'true') {
        $db_data['allow'] = isset($data['allow']) ? $data['allow'] : 'n';
      }


      $db_data['depart_code_real'] = $this->myinfo['dept']['layer_code'];
      $db_data['depart_name_real'] = $this->myinfo['dept']['layer_name'];

      $db_data['reg_date'] = date('Y-m-d H:i:s');
      $db_data['reg_ip'] = $_SERVER['REMOTE_ADDR'];

      if ($this->module_config['board_id'] != 'culture_guide_book' || $this->permission['admin'] != true) { //조지영 2019.02.11 - 안내책자투어일 때 관리자 답변글에 process_1 값 넣지 않음
        $db_data['process_1'] = $this->module_config['use_process_1'] == 'true' ? $this->module_config['process_1'][0] : NULL;
      }
      $log_db_data = $db_data;
      if ($this->module_config['board_id'] == 'urc_news') {
        unset($log_db_data['contents_original']);
      }
      $db_data['log'] = '글등록:' . $this->myinfo['user_name'] . ':' . $this->myinfo['my_pin'] . ':' . $this->myinfo['user_level'] . ':' . date('Y-m-d H:i:s') . ':' . $_SERVER['REMOTE_ADDR'] . ':' . base64_encode(serialize($log_db_data));

      foreach ($db_data as $field => $value) if (!is_null($value)) $query_fields .= (empty($query_fields) ? '' : ', ') . $field . ' = "' . $value . '" ';
      $query = sprintf('INSERT INTO %s SET %s', $this->module_config['table_name'], $query_fields);
    } else { ## update
      foreach ($db_data as $field => $value) if (!is_null($value)) $query_fields .= (empty($query_fields) ? '' : ', ') . $field . ' = "' . $value . '" ';
      // 게시글 체크 해제되면 날짜값 초기화 // period value 설정 안 된 스킨 있어서 전체값으로 설정 안 함. 차후 확인하고 다른 방법으로 모두 적용되도록 수정!!
      //	if($data['period'] == 'n')	$query_fields .= ', period_start = NULL, period_end = NULL ';
      $log_db_data = $db_data;
      unset($log_db_data['contents_original']);
      $query_fields .= ',log = CONCAT(IFNULL(log,""), " || 글수정:' . $this->myinfo['user_name'] . ':' . $this->myinfo['my_pin'] . ':' . $this->myinfo['user_level'] . ':' . date('Y-m-d H:i:s') . ':' . $_SERVER['REMOTE_ADDR'] . ':' . base64_encode(serialize($log_db_data)) . '") ';
      $query = sprintf('UPDATE %s SET %s WHERE idx = "%s"', $this->module_config['table_name'], $query_fields, $data['idx']);
    }

    $result = $mysql->query($query);

    if ($result) {
      if ($mode == 'save') {
        $data['idx'] = $mysql->insert_id();
        if ($data['pidx'] == 0) {
          // sort 수정 추가 - 기존의 pidx, seq를 사용한 쿼리가 너무 느려서 sort 필드 하나만 이용하도록 수정함.
          $query = sprintf('UPDATE %s SET pidx = "%s", sort = %s WHERE idx = "%s"', $this->module_config['table_name'], $data['idx'], $data['idx'], $data['idx']);
          $mysql->query($query);
        }
        $this->clip_geocode($this->module_config['table_name'], $data['idx']);
      }


      ##베너스킨일 경우 이미지 리사이즈.. 요청
      $resize = array('w' => false, 'h' => false);
      if ($this->module_config['use_banner'] == 'true') {
        $resize = array('w' => $this->module_config['banner_size_width'], 'h' => $this->module_config['banner_size_height']);
        $this->module_config['file_upload_count'] = 1;
      }

      if (!empty($this->module_config['use_list_size']) && $this->module_config['use_list_size'] == 'true' && $this->module_config['skin_style'] != "photonews" && $this->module_config['skin_style'] != "lecture_guide") {
        $resize = array('w' => $this->module_config['list_size_width'], 'h' => $this->module_config['list_size_height']);
        $this->module_config['file_upload_count'] = 1;
      }

      ## 배주원_20171026_대표_썸네일이미지_출력
      ## 모듈셋팅설정이되있을경우_실행
      ## @테이블명,@보드명,@해당게시물인덱스,@해당게시물_@변경값
      if (($_SYSTEM['module_config']['use_main_image'] == 'true' || $this->module_config['skin_style'] == 'culture_info' || $this->module_config['skin_style'] == 'gallery_comic' || $this->module_config['skin_style'] == 'gallery_resipe') && isset($data['chk_main'])) {

        $file_info = $this->attached_file_list($data['idx']);
        if ($file_info) $this->update_title_image($this->module_config['table_name'], $this->module_config['board_id'], $data['idx'], $file_info, $data['chk_main']);
      }

      ## 파일업로드.
      for ($iter = 0; $iter < $this->module_config['file_upload_count']; $iter++) {

        $field_name = 'file_' . $iter;

        ## 파일 삭제
//				if(!empty($data['remove_'.$field_name])) $this->remove_upload_file($data['remove_'.$field_name], $this->module_config['table_name'], $data['idx']);
        if (!empty($data['remove_' . $field_name])) {
          $this->remove_upload_file($data['remove_' . $field_name], $this->module_config['table_name'], $data['idx']);
          ## file_upload_count 가 정해져 있을떄 ex) 업로드 파일 1나만 된다면 삭제만 하고 끝나므로 $this->module_config['file_upload_count']를 1증가해서 다른 파일을 업로드 가능하도록 수정.
          ## 작업자 : 서희진 2017-02-28
          $this->module_config['file_upload_count']++;
        }

        ## 파일 업로드
        if (!empty($file[$field_name]['name'])) {

          $file_info = array();
          $file_info['board_id'] = $this->module_config['board_id'];
          $file_info['table_name'] = $this->module_config['table_name'];
          $file_info['table_idx'] = $data['idx'];
          $file_info['use_streaming'] = $this->module_config['use_streaming'];
          //2014-12-16 황재복 : alt 넣도록
          $file_info['title'] = $data[$field_name . '_alt'];
          //2017-10-26_썸네일이미지_선택가능하도록
          if ($this->module_config['use_watermark'] == 'true' && is_file($_SYSTEM['root'] . $this->module_config['watermark_path'])) {
            $watermark_path = $_SYSTEM['root'] . $this->module_config['watermark_path'];
          }

          $this->upload_file($file_info, $file[$field_name], $this->module_config['ext_deny_upload'], '', $resize, $watermark_path);
        } elseif (!empty($data[$field_name . '_alt'])) {
          $file_info = $this->attached_file_list($data['idx']);
          $title = $data[$field_name . '_alt'];

          $this->update_alt($file_info[$iter], $title);
        } elseif (count($data['file_m']) > 0) {

          foreach ($data['file_m_alt'] as $findex => $fvalue) {
            if (!empty($fvalue)) {
              $this->update_alt_new($data['file_m_idx'][$findex], $data['file_m_alt'][$findex]);
            }
          }
        }

      }

      //첨부파일기능 수정 : 오경우 (20120120)
      if ($this->module_config['ebook_use'] == 'true') {
        $this->getPlugin('digitomi')->write_before();
      }

      if ($mode == 'save' && ($this->module_config['skin_style'] == 'approval' || $this->module_config['skin_style'] == 'regulation' || $this->module_config['skin_style'] == 'solicit' || $this->module_config['skin_style'] == 'approval_ex' || $this->module_config['skin_style'] == 'approval_health' || $this->module_config['skin_style'] == 'solicit_cleen' || $this->module_config['skin_style'] == 'address_regulation' || $this->module_config['skin_style'] == 'monitor')) {  // 민원처리용(정운영)
        $query = 'INSERT INTO `_approval` (`board_id`, `board_idx`, `approval_line`, `step`, `user_id`, `user_name`,  `process`, `processing_date`) 
				VALUES ("' . $db_data['board_id'] . '", "' . $data['idx'] . '", "0", "0", "' . $db_data['reg_id'] . '", "' . $db_data['reg_name'] . '",  "reg", "' . date('Y-m-d H:i:s') . '")';
        $mysql->query($query);
      }

      ## SMS 설정
      if ($mode == 'save') {
        $ums_phone = '0542325630';
        $safe_time = false;
        if ($this->module_config['staff_push'] == 'true') {
          $short_url = call::shorturl('http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'] . '?idx=' . $data['idx'] . '&mode=view');
          $short_url = 'http://' . $_SERVER['SERVER_NAME'] . '/' . $short_url . '@';

          $ret = file_get_contents('http://' . $_SERVER['SERVER_NAME'] . '/index.api?collection=mobile_push&apimode=send_push&message=' . rawurlencode(call::strcut(preg_replace('/(<[^>]+>)/', '', $db_data['contents']), 40, '..')) . '&title=' . rawurlencode('[' . $db_data['category_1'] . '] ' . call::strcut($db_data['title'], 20, '..')) . '&url=' . $short_url . '&category=' . $db_data['category_1']);
          $ret = call::filePathFilter($ret);
        }

        if ($data['pidx'] == 0) { ## 글쓰기
          if ($this->module_config['use_write_sms'] == 'true') {
            $sms_recv_list = $this->module_config['write_sms_recv_number'];
            $sms_mesg = $this->module_config['write_sms_mesg'];
            $sms_mesg = str_replace('[등록자]', $this->myinfo['user_name'], $sms_mesg);
            $sms_mesg = str_replace('[등록일시]', date('Y년m월d일'), $sms_mesg);
            $sms_mesg = str_replace('[제목]', call::strcut($db_data['title'], 20, '..'), $sms_mesg);
            $sms_mesg = str_replace('[아이피]', $_SERVER['REMOTE_ADDR'], $sms_mesg);
            $sms_mesg = str_replace('[메뉴]', $_SYSTEM['menu_info']['title'], $sms_mesg);
            $sms_mesg = str_replace('[사이트]', $_SYSTEM['config']['site_name'], $sms_mesg);
            $sms_mesg .= " 관리자용 메세지입니다.";
            $sms_mesg = str_replace('[URL]', $this->get_short_url('http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'] . '?idx=' . $data['idx'] . '&mode=view'), $sms_mesg);
            $sms_sender = empty($this->module_config['write_sms_send_number']) ? $ums_phone : $this->module_config['write_sms_send_number'];
            if (!isset($aligo)) $aligo = new aligo();
            $smsRec[] = array("Company" => $_SYSTEM['site_name'], "name" => $db_data['reg_name'], "phone" => $sms_recv_list, "sender" => $sms_sender);

            // $res = $aligo->aligo_send($smsRec[0], $sms_mesg, "");
            $this->sms_send($sms_recv_list, $sms_sender, $sms_mesg, NULL, $safe_time);
          }
        } else { ## 답변글
          if ($this->module_config['use_reply_sms'] == 'all' || ($this->module_config['use_reply_sms'] == 'admin' && $this->permission['admin'] === true)) {

            $sms_recv_list = $data['reply_sms_recv_number'];

            $sms_mesg = $this->module_config['board_name'] . ' 게시판에 ' . $data['reg_name'] . '님의 글에 대한 답변글이 기재되었습니다.';
            ## 추후 모바일 게시판 기능이 완료되면 바로가기에 모바일 게시판 링크를 붙여준다.
            $sms_mesg = '[<a href="' . $_SERVER['PHP_SELF'] . '?mode=view&idx=' . $data['idx'] . '"' . '바로가기' . '</a>]';
            $sms_sender = empty($this->module_config['write_sms_send_number']) ? $_SYSTEM['config']['tel'] : $this->module_config['write_sms_send_number'];
            if (!isset($aligo)) $aligo = new aligo();
            $smsRec[] = array("Company" => $_SYSTEM['site_name'], "name" => $db_data['reg_name'], "phone" => $sms_recv_list, "sender" => $sms_sender);
            $res = $aligo->aligo_send($smsRec[0], $smsMsg, "");
          }
        }
      }

		## 20240403 서희진 : 문의 게시판 문자 발송 
		if ($this->module_config['board_id'] == 'qna' ) {
			//$this->sms_send('01062708118', $sender, '(주)유비 QNA 문의:'.CR.$data['title'] . ' -' . $data['reg_name'] . ' ');
		}
		if ($this->module_config['board_id'] == 'request') {
			$this->sms_send('01035357714','01032789095, 01033825530, 01043952351', $sender, '(주)유비 상담 및 견적:'.CR . $data['title'] . ' -' . $data['reg_name'] . ' ');
			//$this->sms_send('01094956485, 01035357714', $sender, '(주)유비 상담 및 견적:'.CR . $data['title'] . ' -' . $data['reg_name'] . ' ');

		}
		if ($this->module_config['board_id'] == 'inquiry') {
			//$this->sms_send('01062708118', $sender, '(주) 유비 해외문의:'.CR . $data['title'] . ' -' . $data['reg_name'] . ' ');
		}



      ##202106 ys_singo 게시판에서 답변시 민원인에서 문자발송
      if (($this->module_config['skin_style'] == 'ys_singo' || $this->module_config['skin_style'] == 'ys_singo_coast' || $this->module_config['skin_style'] == 'ys_reserve_minwon') && $mode == 'reply_save') {
        if ($this->module_config['use_reply_sms'] == 'all' || ($this->module_config['use_reply_sms'] == 'admin' && $this->permission['admin'] === true)) {
          $sms_recv_list = $encryption->decrypt($sub_data['c_phone']);
          $sms_mesg = $this->module_config['board_name'] . ' 게시판에 ' . $data['reg_name'] . '님의 글에 대한 답변글이 기재되었습니다.';
          ## 추후 모바일 게시판 기능이 완료되면 바로가기에 모바일 게시판 링크를 붙여준다.
          //$sms_mesg = '[<a href="'.$_SERVER['PHP_SELF'].'?mode=view&idx='.$data['idx'].'"'.'바로가기'.'</a>]';
          $sms_sender = empty($this->module_config['write_sms_send_number']) ? '000-000-0000' : $this->module_config['write_sms_send_number'];
          $this->sms_send($sms_recv_list, $sms_sender, $sms_mesg, NULL, $safe_time);
        }
      }

      $log_mode = $mode == 'save' ? 'write' : 'modify';
      $this->article_log_cms40($log_mode, $this->module_config['table_name'], $data['idx']);

      ## 파라미터 설정
      $arr_parameter = array();
      if ($_SYSTEM['device'] == 'mobile') {
        $arr_parameter['mode'] = 'list';
      } else {
        $arr_parameter['mode'] = 'view';
      }

      ## 문고답하기 스킨에서는 글등록후 list로 가기
      if ($this->module_config['skin_style'] == 'qna' || $this->module_config['skin_style'] == 'faq' || $this->module_config['skin_style'] == 'share_photo' || $this->module_config['skin_style'] == 'staff_notice' || $this->module_config['skin_style'] == 'remembrance' || $this->module_config['use_reply'] != "none") $arr_parameter['mode'] = 'list';
      $arr_parameter['idx'] = $data['idx'];
      if ($page > 1) $arr_parameter['page'] = $page;
      if (!empty($search_type)) $arr_parameter['search_type'] = $search_type;
      if (!empty($search_word)) $arr_parameter['search_word'] = $search_word;
      //if(!empty($category_1))  $arr_parameter['category_1']  = $category_1;


      $parameter = $this->make_GET_parameter($arr_parameter, '&');
      $parameter = empty($parameter) ? '' : '?' . $parameter;

      ## 글이 저장 되면 메인 추출 한번 실행.
      if ($_SYSTEM['hostname'] == "www") {
        //shell_exec('sh '.$_SYSTEM['script_root'] .'/cron/gosi.sh');
      }

      $msg_id = $mode == 'save' ? 51 : 52;

      ## 데이타 ajax으로 가져가기 위해서 추가.
      $return = $this->get_parameter('return');
      if ($return == "json") {
        ob_clean();
        if ($result) {
          echo 'ok';
        }
        exit;
      }

      call::xml_error($msg_id, '', $_SERVER['PHP_SELF'] . $parameter);
    } else {
      //		$debug_log = $this->permission['admin'] === true ? $query: '';
      $debug_log = '';
      call::xml_error('103', $debug_log, $this->referer);
    }


    return true;
  }


  ## 글삭제
  public function remove()
  {
    global $mysql;

    ## parameter setting.
    $idx = $this->get_parameter('idx');
    $page = $this->get_parameter('page');
    $page = empty($page) ? 1 : $page;
    $search_type = $this->get_parameter('search_type');
    $search_word = $this->get_parameter('search_word');
    $category_1 = $this->get_parameter('category_1');

    ## ====================================================
    ##               validation check start
    ## ====================================================
    if (empty($idx)) call::xml_error('152', '', $this->referer);

    // charge_id 로 삭제시 에러메시지 출력, board 이외의 board 모듈에는 charge_id  없을수 있어서 삭제함. 20220323.a.
    $query = sprintf('SELECT idx, pidx, reg_pin, passwd,process_2 FROM %s WHERE idx = "%s"', $this->module_config['table_name'], $idx);
    $data = $mysql->query_fetch($query);
    if (empty($data['idx'])) call::xml_error('152', '', $this->referer);

    if ($this->module_config['skin_style'] == 'approval' || $this->module_config['skin_style'] == 'regulation' || $this->module_config['skin_style'] == 'solicit' || $this->module_config['skin_style'] == 'approval_ex' || $this->module_config['skin_style'] == 'approval_health' || $this->module_config['skin_style'] == 'solicit_cleen' || $this->module_config['skin_style'] == 'address_regulation' || $this->module_config['skin_style'] == 'monitor') {
      if ($data['process_2'] != '1' && $this->permission['manage'] == false) {
        call::xml_error('212', '', $this->referer); //진행중인 글은 삭제 못한다.
      }
    } else if ($this->module_config['skin_style'] == 'ys_singo' || $this->module_config['skin_style'] == 'ys_singo_coast' || $this->module_config['skin_style'] == 'ys_reserve_minwon') {
      $query2 = sprintf('SELECT idx, reg_pin, process_1, charge_id FROM %s WHERE idx = "%s"', $this->module_config['table_name'], $data['pidx']);
      $data2 = $mysql->query_fetch($query2);

      if (!($data2['process_2'] == '신청')) { ## 신청이 아니면서
        if (!($this->permission['manage'] == true || $this->permission['admin'] == true)) { ## 자기자신이거나 담당이거나 관리자가 아닐때
          call::xml_error('212', '', $this->referer); //진행중인 글은 삭제 못한다.
        }
      }
    }

    if ($this->myinfo['is_login'] === true) {
      if ($this->permission['admin'] !== true && $this->myinfo['my_pin'] != $data['reg_pin']) call::xml_error('205', '', $this->referer);
    } else {
      ## 비로그인 회원 글쓰기가 허용일 경우 패스워드 확인을 거친다.
      if ($data['reg_pin'] != GUEST_PIN) call::xml_error('205', '', $this->referer);
      if ($this->confirm_logoff_passwd($data['idx'], $data['passwd']) !== true) return false;
    }
    //2012-08-30 황재복 : 관리자 삭제 사유 사용 시 관리자만 등록할 수 있도록 한다.
    if ($this->module_config['use_delete_reason'] == 'true') {
      if ($this->permission['admin'] === true) {
        if ($this->admin_delete_reason_form($data['idx']) !== true) return false;
      }
    }


    //if($_SESSION['logoff_confirm'] != md5($data['idx'].$data['reg_pin'].$data['passwd'])) call::xml_error('205','',$this->referer);

    ## 비로그인 회원 글쓰기에 사용된 세션값 삭제
    if (isset($_SESSION['logoff_confirm'])) unset($_SESSION['logoff_confirm']);


    ## ====================================================
    ##               validation check end
    ## ====================================================

    $query = ' UPDATE ' . $this->module_config['table_name'];
    $query .= ' SET ';
    $query .= '     del = "y", ';
    $query .= '     log = CONCAT(IFNULL(log,""), " || 글삭제:' . $this->myinfo['user_name'] . ':' . $this->myinfo['my_pin'] . ':' . $this->myinfo['user_level'] . ':' . date('Y-m-d H:i:s') . ':' . $_SERVER['REMOTE_ADDR'] . '") ';
    $query .= ' WHERE idx = "' . $idx . '"';
    $mysql->query($query);

    $this->article_log_cms40('delete', $this->module_config['table_name'], $idx);

    ## 파라미터 설정
    $arr_parameter = array();
    if ($page > 1) $arr_parameter['page'] = $page;
    if (!empty($search_type)) $arr_parameter['search_type'] = $search_type;
    if (!empty($search_word)) $arr_parameter['search_word'] = $search_word;
    if (!empty($category_1)) $arr_parameter['category_1'] = $category_1;

    $parameter = $this->make_GET_parameter($arr_parameter, '&');
    $parameter = empty($parameter) ? '' : '?' . $parameter;

    ## 인기글 apc삭제 해야함.
    $apcu_key_hot = 'hot_' . $this->module_config['board_id'];
    apcu_delete($apcu_key_hot);


    call::xml_error('53', '', $_SERVER['PHP_SELF'] . $parameter);
    return true;
  }

  ## 글삭제
  ## 사용안함. 헷갈림 지워도 될까나???
  public function remove_voucher()
  {

    ##상품권 start
    $mysql2 = new mysql_class_user(0, 0, 0, 0);
    $mysql2->connect('gov_yeosu', '152.99.136.74', 'root', 'ehdqor!!##%%74');

    ## parameter setting.
    $idx = $this->get_parameter('idx');
    $page = $this->get_parameter('page');
    $page = empty($page) ? 1 : $page;
    $search_type = $this->get_parameter('search_type');
    $search_word = $this->get_parameter('search_word');
    $category_1 = $this->get_parameter('category_1');

    $table_name = 'md_board_wsboard';

    if (empty($idx)) call::xml_error('152', '', $this->referer);
    if ($this->permission['admin'] !== true) call::xml_error('205', '', $this->referer);

    $query = ' UPDATE ' . $table_name;
    $query .= ' SET ';
    $query .= '     del = "y", ';
    $query .= '     admin_comment = "' . $admin_comment . '", ';
    $query .= '     log = CONCAT(IFNULL(log,""), " || 글삭제:' . $this->myinfo['user_name'] . ':' . $this->myinfo['my_pin'] . ':' . $this->myinfo['user_level'] . ':' . date('Y-m-d H:i:s') . ':' . $_SERVER['REMOTE_ADDR'] . '") ';
    $query .= ' WHERE idx = "' . $idx . '"';
    $mysql2->query($query);

    $this->article_log_cms40('delete', $table_name, $idx);

    ## 파라미터 설정
    $arr_parameter = array();
    if ($page > 1) $arr_parameter['page'] = $page;
    if (!empty($search_type)) $arr_parameter['search_type'] = $search_type;
    if (!empty($search_word)) $arr_parameter['search_word'] = $search_word;
    if (!empty($category_1)) $arr_parameter['category_1'] = $category_1;

    $parameter = $this->make_GET_parameter($arr_parameter, '&');
    $parameter = empty($parameter) ? '' : '?' . $parameter;

    call::xml_error('53', '', $_SERVER['PHP_SELF']);
    return true;
  }

  ## ********************************************************************************************************************
  ## 관리자기능 : 삭제글 복구.
  ## 	//2012-04-10 황재복 : 복원기능 추가
  ## ********************************************************************************************************************
  public function restore()
  {
    global $mysql;

    ## parameter setting.
    $idx = $this->get_parameter('idx');
    $page = $this->get_parameter('page');
    $page = empty($page) ? 1 : $page;
    $search_type = $this->get_parameter('search_type');
    $search_word = $this->get_parameter('search_word');
    $category_1 = $this->get_parameter('category_1');


    ## ====================================================
    ##               validation check start
    ## ====================================================
    if (empty($idx)) call::xml_error('152', '', $this->referer);

    $query = sprintf('SELECT idx, pidx, reg_pin, passwd FROM %s WHERE idx = "%s"', $this->module_config['table_name'], $idx);
    $data = $mysql->query_fetch($query);
    if (empty($data['idx'])) call::xml_error('152', '', $this->referer);

    if ($this->module_config['skin_style'] == 'approval' || $this->module_config['skin_style'] == 'regulation' || $this->module_config['skin_style'] == 'solicit' || $this->module_config['skin_style'] == 'approval_ex' || $this->module_config['skin_style'] == 'approval_health' || $this->module_config['skin_style'] == 'solicit_cleen' || $this->module_config['skin_style'] == 'address_regulation' || $this->module_config['skin_style'] == 'monitor') {
      if ($data['process_2'] != '1' && $this->permission['manage'] == false) {
        call::xml_error('212', '', $this->referer); //진행중인 글은 삭제 못한다.
      }
    } else if ($this->module_config['skin_style'] == 'ys_singo' || $this->module_config['skin_style'] == 'ys_singo_coast' || $this->module_config['skin_style'] == 'ys_reserve_minwon') {
      $query2 = sprintf('SELECT idx, reg_pin, process_1, charge_id FROM %s WHERE idx = "%s"', $this->module_config['table_name'], $data['pidx']);
      $data2 = $mysql->query_fetch($query2);

      if (!($data2['process_2'] == '신청')) { ## 신청이 아니면서
        if (!($this->permission['manage'] == true || $this->permission['admin'] == true)) { ## 자기자신이거나 담당이거나 관리자가 아닐때
          call::xml_error('212', '', $this->referer); //진행중인 글은 삭제 못한다.
        }
      }
    }


    if ($this->myinfo['is_login'] === true) {
      if ($this->permission['admin'] !== true && $this->myinfo['my_pin'] != $data['reg_pin']) call::xml_error('205', '', $this->referer);
    } else {
      ## 비로그인 회원 글쓰기가 허용일 경우 패스워드 확인을 거친다.
      if ($data['reg_pin'] != GUEST_PIN) call::xml_error('205', '', $this->referer);
      if ($this->confirm_logoff_passwd($data['idx'], $data['passwd']) !== true) return false;
    }

    //if($_SESSION['logoff_confirm'] != md5($data['idx'].$data['reg_pin'].$data['passwd'])) call::xml_error('205','',$this->referer);

    ## 비로그인 회원 글쓰기에 사용된 세션값 삭제
    if (isset($_SESSION['logoff_confirm'])) unset($_SESSION['logoff_confirm']);

    ## ====================================================
    ##               validation check end
    ## ====================================================

    $query = ' UPDATE ' . $this->module_config['table_name'];
    $query .= ' SET ';
    $query .= '     del = "n", ';
    $query .= '     log = CONCAT(IFNULL(log,""), " || 글복원:' . $this->myinfo['user_name'] . ':' . $this->myinfo['my_pin'] . ':' . $this->myinfo['user_level'] . ':' . date('Y-m-d H:i:s') . ':' . $_SERVER['REMOTE_ADDR'] . '") ';
    $query .= ' WHERE idx = "' . $idx . '"';
    $mysql->query($query);

    $this->article_log_cms40('restore', $this->module_config['table_name'], $idx);

    ## 파라미터 설정
    $arr_parameter = array();
    if ($page > 1) $arr_parameter['page'] = $page;
    if (!empty($search_type)) $arr_parameter['search_type'] = $search_type;
    if (!empty($search_word)) $arr_parameter['search_word'] = $search_word;
    if (!empty($category_1)) $arr_parameter['category_1'] = $category_1;

    $parameter = $this->make_GET_parameter($arr_parameter, '&');
    $parameter = empty($parameter) ? '' : '?' . $parameter;

    call::xml_error('53', '', $_SERVER['PHP_SELF'] . $parameter);
    return true;
  }

  ## ********************************************************************************************************************
  ## 영구삭제 : 사이트 관리자만 가능
  ## ********************************************************************************************************************
  public function erase()
  {

    global $mysql;

    ## parameter setting.
    $idx = $this->get_parameter('idx');
    $page = $this->get_parameter('page');
    $page = empty($page) ? 1 : $page;
    $search_type = $this->get_parameter('search_type');
    $search_word = $this->get_parameter('search_word');
    $category_1 = $this->get_parameter('category_1');

    ## ====================================================
    ##               validation check start
    ## ====================================================
    if (empty($idx)) call::xml_error('152', '', $this->referer);

    $query = sprintf('SELECT idx, reg_pin FROM %s WHERE idx = "%s"', $this->module_config['table_name'], $idx);
    $data = $mysql->query_fetch($query);
    if (empty($data['idx'])) call::xml_error('152', '', $this->referer);

    if ($this->permission['admin'] !== true && $this->myinfo['my_pin'] != $data['reg_pin']) call::xml_error('205', '', $this->referer);
    ## ====================================================
    ##               validation check end
    ## ====================================================


    ## 파일 삭제
    $this->remove_upload_file_all($this->module_config['table_name'], $idx);

    ## 디비삭제
    $query = sprintf('DELETE FROM %s WHERE idx = "%s"', $this->module_config['table_name'], $idx);
    $mysql->query($query);

    $this->article_log_cms40('remove', $this->module_config['table_name'], $idx);

    ## 파라미터 설정
    $arr_parameter = array();
    if ($page > 1) $arr_parameter['page'] = $page;
    if (!empty($search_type)) $arr_parameter['search_type'] = $search_type;
    if (!empty($search_word)) $arr_parameter['search_word'] = $search_word;
    if (!empty($category_1)) $arr_parameter['category_1'] = $category_1;

    $parameter = $this->make_GET_parameter($arr_parameter, '&');
    $parameter = empty($parameter) ? '' : '?' . $parameter;

    call::xml_error('53', '', $_SERVER['PHP_SELF'] . $parameter);
    return true;
  }

  ## ********************************************************************************************************************
  ## 글작성시 입력값 체크
  ## ********************************************************************************************************************
  private function null_check($mode, $data, $files = array())
  {

    global $mysql;

    ## 글내용 필수가 아닌 스킨 설정값. 해당 스킨에서는 글내용을 필수 처리 하지 않는다.
    $contents_null_no_check_skin = array("rental_dome", "share_photo", "legal_info");

    $return = '';
    //2012-09-05 황재복 : 필수입력필드를 설정하였을 경우 필수입력필드 처리를 한 것을 우선적으로 검사하여 빈값 체크
    if ($this->module_config['use_write_field'] == 'true') {
      include_once $this->module_config['module_path'] . '/required_field.php';
      $required_value = $this->module_config['required_value'];
      if ($_SERVER['REMOTE_ADDR'] != "49.254.140.140") {
        for ($i = 0; $i < count($required_value); $i++) {
          $required_key = array_search($required_value[$i], array_column($required_field, 'fields_name'));
          //echo '<pre>';						print_r( $required_key );			exit;
          if ($required_value[$i] != 'contents')
            if (empty($data[$required_value[$i]])) {
              //echo '<pre>';						print_r( $required_value[$i] );			exit;
              $return .= '<li>' . $required_field[$required_key]['label_name'] . ' 입력은 필수사항입니다.</li>';
            }
        }
      }
    } else {
      /*2014-04-22 서희진 : 개인정보처리방침 필수 체크 기능 구현.
			* 설정값 use_privacy_write 가 사용(True)일때 / use_logoff_write 가 설정(True) 일때 / 비회원일때 적용됩니다.
			* 테스트를 위해서 use_privacy_write 가 사용(True)일때만 보이도록 셋팅함. 개발후 if문 주석과 변경.
			*/

      if ($this->module_config['use_privacy_write'] == 'true' && $this->module_config['use_logoff_write'] == 'true' && $this->myinfo['is_login'] != true) {
//			if($this->module_config['use_privacy_write'] == 'true'){
        if (empty($data['agree_privacy']) || $data['agree_privacy'] != 'y') $return .= '<li>개인정보처리방침 동의는 필수사항입니다.</li>';  ## 개인정보처리방침 동의
      }
      ## common
      if ($this->module_config['skin_style'] != 'apply_cockle' && $this->module_config['skin_style'] != 'apply_sopyonje' && $this->module_config['skin_style'] != 'county_request' && $this->module_config['skin_style'] != 'county_suggest' && $this->module_config['skin_style'] != 'flea_market_atec' && $this->module_config['skin_style'] != 'remembrance') {
        if (empty($data['title'])) $return .= '<li>제목 입력은 필수사항입니다.</li>';  ## 제목
      }
    }

    if ($this->module_config['skin_style'] != 'request_manage' && $this->module_config['skin_style'] != 'sewol' && $this->module_config['skin_style'] != 'dictionary' && $this->module_config['skin_style'] != 'county_suggest' && $this->module_config['skin_style'] != 'hygiene' && $this->module_config['skin_style'] != 'educational_status' && $this->module_config['skin_style'] != 'library' && $this->module_config['skin_style'] != 'job_search' && $this->module_config['skin_style'] != 'bodo') { //20151025 세월호 유류품 필수 제외
      if ($this->module_config['writer_display'] == 'department') {
        if (empty($data['depart_name'])) $return .= '<li>등록부서 입력은 필수사항입니다.</li>';  ## 등록자
      } else {
        if (empty($data['reg_name'])) $return .= '<li>등록자 입력은 필수사항입니다.</li>';  ## 등록자
      }
    }
    if ($this->module_config['skin_style'] == 'dictionary' || $this->module_config['board_id'] == 'art_screening' || $this->module_config['board_id'] == 'art_event') {
      $return;
    } elseif ($this->module_config['use_category_1'] == 'true' && is_array($this->module_config['category_1']) && empty($data['category_1']) && $this->module_config['skin_style'] != 'search') $return .= '<li>분류 입력은 필수사항입니다.</li>';
    if ($this->module_config['get_guest_info'] == 'true' && ($this->myinfo['user_level'] == 11 || $this->myinfo['user_level'] == 99)) {

      if ($this->module_config['skin_style'] == 'ys_singo' || $this->module_config['skin_style'] == 'ys_singo_coast' || $this->module_config['skin_style'] == 'ys_reserve_minwon') {

        if (empty($data['c_phone_1']) || empty($data['c_phone_2']) || empty($data['c_phone_3'])) $return .= '<li>회원연락처 입력은 필수사항입니다.</li>';
        if (empty($data['c_zipcode'])) $return .= '<li>우편번호 입력은 필수사항입니다.</li>';
        if (empty($data['c_address_1']) || empty($data['c_address_1'])) $return .= '<li>주소 입력은 필수사항입니다.</li>';


      } else {

        if (empty($data['phone_1']) || empty($data['phone_2']) || empty($data['phone_3'])) $return .= '<li>회원연락처 입력은 필수사항입니다.</li>';
        if (empty($data['zipcode'])) $return .= '<li>우편번호 입력은 필수사항입니다.</li>';
        if (empty($data['address_1']) || empty($data['address_1'])) $return .= '<li>주소 입력은 필수사항입니다.</li>';
      }
    }


    if ($this->module_config['use_tag'] == 'true' && !empty($this->module_config['tag_list_all'])) {
      if (empty($data['tag_list'])) $return .= '<li>tag 입력은 필수사항입니다.</li>';
    }

    ## 비로그인 회원 글쓰기 설정 - 패스워드 및 키코드 확인
    if ($this->myinfo['is_login'] !== true && $this->module_config['use_logoff_write'] == 'true') {
      $data['passwd'] = trim($data['passwd']);
      if ($mode != 'change' && $this->module_config['skin_style'] != 'county_suggest') {
        if (empty($data['passwd'])) $return .= '<li>패스워드 입력은 필수사항입니다.</li>';
        if (empty($data['text_keycode']) || $_SESSION['text_keycode'] != $data['text_keycode']) $return .= '<li>키코드 입력은 필수사항입니다.</li>';
      }
    }

    ## 2012.09.20 강성수 email 패턴검사 로직추가.
    /*if(!empty($data['email'])) {
			if(ereg('[[:space:]]', trim($data['email'])) || !preg_match('/^[_0-9a-zA-Z-]+(\.[_0-9a-zA-Z-]+)*@[0-9a-zA-Z-]+(\.[0-9a-zA-Z-]+)*$/', trim($data['email']))) {
				$return .= '<li>e-mail 형식이 올바르지 않습니다.</li>';
			}
		}*/

    ## 업로드 파일 체크 //첨부파일기능 수정 : 오경우 (20120120)
    //2012.09.06 파라미터 $mode추가. 강성수
    if ($this->module_config['use_upload_movie'] == 'true') $return .= $this->upload_file_check($mode, $data['idx'], $files);
    if ($this->module_config['use_gallery_img'] == 'true') $return .= $this->upload_file_check($mode, $data['idx'], $files);

    return $return;
  }

  ## ********************************************************************************************************************
  ## 관리자기능 : 승인/비승인 설정, 처리상태 설정, top 설정, 관리자 코멘트등록, 게시물 이동, 삭제글 복구설정 추가해야 한다.
  ## ********************************************************************************************************************
  public function admin_tools_article_save($idx)
  {
    global $mysql;
    $db_data = array();

    if (empty($idx)) call::xml_error('154', '게시물을 선택해 주세요.', $this->referer);

    $allow = $this->get_parameter('allow'); ## 승인처리.
    $process_1 = $this->get_parameter('process_1'); ## 처리상태.
    $top = $this->get_parameter('top'); ## 머릿글 설정
    $top_start = $this->get_parameter('top_start'); ## 머릿글 설정
    $top_end = $this->get_parameter('top_end'); ## 머릿글 설정
    $admin_comment_to = $this->get_parameter('admin_comment_to'); ## 안내문 삽입.
    $admin_comment = $this->get_parameter('admin_comment'); ## 안내문 삽입.
    $del = $this->get_parameter('del'); ## 게시물복구.


    ## 승인/비승인 설정
    $db_data['allow'] = empty($allow) ? NULL : $allow;

    ## 처리상태 설정
    $db_data['process_1'] = empty($process_1) ? NULL : $process_1;

    ## 관리자 코멘트 등록
    $db_data['admin_comment'] = empty($admin_comment) ? NULL : $admin_comment;
//		if(!is_null($db_data['admin_comment'])) $db_data['admin_comment_to'] = empty($admin_comment_to) ? NULL : $admin_comment_to;
    $db_data['admin_comment_to'] = empty($admin_comment_to) ? NULL : $admin_comment_to;
    if ($admin_comment_to == 'none') {
      $db_data['admin_comment'] = '';
    }

    ## top 설정
    $db_data['top'] = empty($top) ? NULL : $top;
    if ($db_data['top'] == 'y') {
      //2012-08-27 황재복 : top 설정 시 갯수 제한 기능 추가
      if ($this->module_config['use_top_limit'] === true) {
        if ($this->module_config['use_top_limit_value'] > 0) {
          if ($this->module_config['use_top_limit_value'] <= $this->get_top_count()) {
            call::xml_error('154', '머리글 설정 갯수를 초과하였습니다. 최대 머리글 갯수는 ' . $this->module_config['use_top_limit_value'] . '입니다.', $this->referer);
          } else {
            $db_data['top_start'] = empty($top_start) ? NULL : $top_start;
            $db_data['top_end'] = empty($top_end) ? NULL : $top_end;
            $db_data['open'] = 'y'; ## 공지사항이 될 경우 자동으로 공개글이 된다.
          }
        }
      } else {
        $db_data['top_start'] = empty($top_start) ? NULL : $top_start;
        $db_data['top_end'] = empty($top_end) ? NULL : $top_end;
        $db_data['open'] = 'y'; ## 공지사항이 될 경우 자동으로 공개글이 된다.
      }
    }

    ## 삭제/복구
    $db_data['del'] = empty($del) ? NULL : $del;
    if (!is_null($db_data['del'])) {
      $status = $db_data['del'] == 'y' ? '글삭제' : '글복구';
      $log .= ',log = CONCAT(IFNULL(log,""), " || ' . $status . ':' . $this->myinfo['user_name'] . ':' . $this->myinfo['user_id'] . ':' . $this->myinfo['user_level'] . ':' . $this->myinfo['my_pin'] . ':' . date('Y-m-d H:i:s') . ':' . $_SERVER['REMOTE_ADDR'] . '") ';
    }

    //배주원_20170425_mod_date업데이트(세월호 유류품 게시판)
    if ($this->module_config['skin_style'] == 'sewol') {
      $db_data['mod_date'] = date('Y-m-d H:i:s');
    }

    ## make query
    $query_fields = '';
    foreach ($db_data as $field => $value) if (!is_null($value)) $query_fields .= (empty($query_fields) ? '' : ', ') . $field . ' = "' . $value . '" ';
    $query_fields .= $log;


    ## data update
    $query = sprintf('UPDATE %s SET %s WHERE idx = "%s"', $this->module_config['table_name'], $query_fields, $idx);
    $result = $mysql->query($query);

    if (!is_null($db_data['del'])) {
      $log_mode = $db_data['del'] == 'y' ? 'delete' : 'restore';
      $this->article_log_cms40($log_mode, $this->module_config['table_name'], $idx);
    }

    ## 이동완료
    call::xml_error('64', '', $this->referer);
  }

  ## ********************************************************************************************************************
  ## 비로그인 글쓰기
  ## ********************************************************************************************************************
  public function confirm_logoff_passwd($idx, $original_passwd)
  {
    $input_passwd = trim($_POST['input_passwd']);

    if (!isset($_SESSION['logoff_confirm'])) { ## 세션이 없을때 패스워드 입력값이 있을 경우 게시물 패스워드와 같으면 세션을 생성한다.
      if (!empty($input_passwd) && $input_passwd == $original_passwd) {
        $_SESSION['logoff_confirm'] = md5($idx . GUEST_PIN . $input_passwd);
        return true;
      }
    } else { ## 세션이 있을때 세션값이 올바를 경우.
      if ($_SESSION['logoff_confirm'] == md5($idx . GUEST_PIN . $original_passwd)) return true;
    }

    ## 위의 두가지 경우가 아닐 경우 패스워드 입력 페이지로 간다.
    if (isset($_SESSION['logoff_confirm'])) unset($_SESSION['logoff_confirm']);

    ## parameter setting.
    $idx = $this->get_parameter('idx');
    $page = $this->get_parameter('page');
    $page = empty($page) ? 1 : $page;
    $mode = $this->get_parameter('mode');
    $search_type = $this->get_parameter('search_type');
    $search_word = $this->get_parameter('search_word');

    ## hidden 값 설정
    $hidden = array();
    if ($page > 1) $hidden['page'] = $page;
    if (!empty($mode)) $hidden['mode'] = $mode;
    if (!empty($idx)) $hidden['idx'] = $idx;
    if (!empty($search_type)) $hidden['search_type'] = $search_type;
    if (!empty($search_word)) $hidden['search_word'] = $search_word;
    //if(!empty($category_1)) $hidden['category_1'] = $category_1;

    $ARTICLES['hidden'] = $hidden;


    ## skin 설정
    $ARTICLES['device'] = $this->device;
    $ARTICLES['module_root'] = $this->module_root;                  ## 근본적으로 스킨쪽에서 세팅되어야 한다.
    $ARTICLES['module_path'] = $this->module_config['module_path']; ## 근본적으로 스킨쪽에서 세팅되어야 한다.
    //	$ARTICLES['skin_style']  = 'common';
    //	$ARTICLES['skin_style']	 = empty($this->module_config['skin_style']) ? 'board' : $this->module_config['skin_style'];
    $ARTICLES['skin_style'] = 'apply';
    $ARTICLES['skin_name'] = 'logoff_passwd';


//		$xml_data =  ArrayToXML::toXml($ARTICLES, 'write');
    echo serialize($ARTICLES);
    //echo $xml_data;
    return false;
  }
  ## ********************************************************************************************************************
  ##  관리자기능 : 관리자 삭제 사유
  ## ********************************************************************************************************************
  public function admin_delete_reason_form($idx)
  {
    ## parameter setting.
    $idx = $this->get_parameter('idx');
    $page = $this->get_parameter('page');
    $page = empty($page) ? 1 : $page;
    $mode = $this->get_parameter('mode');
    $search_type = $this->get_parameter('search_type');
    $search_word = $this->get_parameter('search_word');

    ## hidden 값 설정
    $hidden = array();
    if ($page > 1) $hidden['page'] = $page;
    //if(!empty($mode))        $hidden['mode'] = $mode;
    if (!empty($idx)) $hidden['idx'] = $idx;
    if (!empty($search_type)) $hidden['search_type'] = $search_type;
    if (!empty($search_word)) $hidden['search_word'] = $search_word;
    //if(!empty($category_1)) $hidden['category_1'] = $category_1;

    $hidden['mode'] = 'admin_remove';

    $ARTICLES['hidden'] = $hidden;


    ## skin 설정
    $ARTICLES['device'] = $this->device;
    $ARTICLES['module_root'] = $this->module_root;                  ## 근본적으로 스킨쪽에서 세팅되어야 한다.
    $ARTICLES['module_path'] = $this->module_config['module_path']; ## 근본적으로 스킨쪽에서 세팅되어야 한다.
    $ARTICLES['skin_style'] = empty($this->module_config['skin_style']) ? 'board' : $this->module_config['skin_style'];
    $ARTICLES['skin_name'] = 'admin_delete_reason';


    echo serialize($ARTICLES);
    return false;
  }


  ## ********************************************************************************************************************
  ##  관리자 삭제
  ## ********************************************************************************************************************
  public function admin_remove()
  {
    global $mysql;

    ## parameter setting.
    $idx = $this->get_parameter('idx');
    $page = $this->get_parameter('page');
    $page = empty($page) ? 1 : $page;
    $search_type = $this->get_parameter('search_type');
    $search_word = $this->get_parameter('search_word');
    $category_1 = $this->get_parameter('category_1');
    $admin_comment = $this->get_parameter('admin_comment');
    $delete_reason = $this->get_parameter('delete_reason');


    if (empty($idx)) call::xml_error('152', '', $this->referer);

    $query = sprintf('SELECT idx, pidx, reg_pin, passwd,process_2, charge_id FROM %s WHERE idx = "%s"', $this->module_config['table_name'], $idx);
    $data = $mysql->query_fetch($query);
    if (empty($data['idx'])) call::xml_error('152', '', $this->referer);

    if ($this->module_config['skin_style'] == 'approval' || $this->module_config['skin_style'] == 'regulation' || $this->module_config['skin_style'] == 'solicit' || $this->module_config['skin_style'] == 'approval_ex' || $this->module_config['skin_style'] == 'approval_health' || $this->module_config['skin_style'] == 'solicit_cleen' || $this->module_config['skin_style'] == 'address_regulation' || $this->module_config['skin_style'] == 'monitor') {
      if ($data['process_2'] != '1' && $this->permission['manage'] == false) {
        call::xml_error('212', '', $this->referer); //진행중인 글은 삭제 못한다.
      }
    } else if ($this->module_config['skin_style'] == 'ys_singo' || $this->module_config['skin_style'] == 'ys_singo_coast' || $this->module_config['skin_style'] == 'ys_reserve_minwon') {
      $query2 = sprintf('SELECT idx, reg_pin, process_1, charge_id FROM %s WHERE idx = "%s"', $this->module_config['table_name'], $data['pidx']);
      $data2 = $mysql->query_fetch($query2);

      if (!($data2['process_2'] == '신청')) { ## 신청이 아니면서
        if (!($this->permission['manage'] == true || $this->permission['admin'] == true)) { ## 자기자신이거나 담당이거나 관리자가 아닐때
          call::xml_error('212', '', $this->referer); //진행중인 글은 삭제 못한다.
        }
      }
    }


    if ($this->myinfo['is_login'] === true) {
      if ($this->permission['admin'] !== true && $this->myinfo['my_pin'] != $data['reg_pin']) call::xml_error('205', '', $this->referer);
    }

    ## 비로그인 회원 글쓰기에 사용된 세션값 삭제
    if (isset($_SESSION['logoff_confirm'])) unset($_SESSION['logoff_confirm']);

    $query = ' UPDATE ' . $this->module_config['table_name'];
    $query .= ' SET ';
    $query .= '     del = "y", ';
    $query .= '     admin_comment = "' . $admin_comment . '", ';
    $query .= '     log = CONCAT(IFNULL(log,""), " || 글삭제:' . $this->myinfo['user_name'] . ':' . $this->myinfo['my_pin'] . ':' . $this->myinfo['user_level'] . ':' . date('Y-m-d H:i:s') . ':' . $_SERVER['REMOTE_ADDR'] . '"), ';
    $query .= '     delete_reason = "' . $delete_reason . '" ';
    $query .= ' WHERE idx = "' . $idx . '"';
    $mysql->query($query);

    $this->article_log_cms40('delete', $this->module_config['table_name'], $idx);

    ## 파라미터 설정
    $arr_parameter = array();
    if ($page > 1) $arr_parameter['page'] = $page;
    if (!empty($search_type)) $arr_parameter['search_type'] = $search_type;
    if (!empty($search_word)) $arr_parameter['search_word'] = $search_word;
    if (!empty($category_1)) $arr_parameter['category_1'] = $category_1;

    $parameter = $this->make_GET_parameter($arr_parameter, '&');
    $parameter = empty($parameter) ? '' : '?' . $parameter;

    call::xml_error('53', '', $_SERVER['PHP_SELF']);
    return true;
  }

  ## ********************************************************************************************************************
  ##  관리자 목록 선택삭제
  ## ********************************************************************************************************************
  public function delete_all()
  {
    global $mysql;

    ## parameter setting.
    $idx = $this->get_parameter('idx');
    $page = $this->get_parameter('page');
    $page = empty($page) ? 1 : $page;
    $search_type = $this->get_parameter('search_type');
    $search_word = $this->get_parameter('search_word');
    $category_1 = $this->get_parameter('category_1');
    $check_idx = $this->get_parameter('check_idx');
    $return = $this->get_parameter('return');

    for ($i = 0; $i < count($check_idx); $i++) {
      $query = ' UPDATE ' . $this->module_config['table_name'];
      $query .= ' SET ';
      $query .= '     del = "y", ';
      $query .= '     log = CONCAT(IFNULL(log,""), " || 글삭제:' . $this->myinfo['user_name'] . ':' . $this->myinfo['my_pin'] . ':' . $this->myinfo['user_level'] . ':' . date('Y-m-d H:i:s') . ':' . $_SERVER['REMOTE_ADDR'] . '") ';
      $query .= ' WHERE idx = "' . $check_idx[$i] . '"';
      $result = $mysql->query($query);
    }
    ## 파라미터 설정
    $arr_parameter = array();
    if ($page > 1) $arr_parameter['page'] = $page;
    if (!empty($search_type)) $arr_parameter['search_type'] = $search_type;
    if (!empty($search_word)) $arr_parameter['search_word'] = $search_word;
    if (!empty($category_1)) $arr_parameter['category_1'] = $category_1;

    $parameter = $this->make_GET_parameter($arr_parameter, '&');
    $parameter = empty($parameter) ? '' : '?' . $parameter;

    if ($return == "json") {
      ob_clean();
      if ($result) {
        echo 'ok';
      }
      exit;
    }

    call::xml_error('53', '', $_SERVER['PHP_SELF']);
    return true;
  }

  ## ********************************************************************************************************************
  ##  modify  이미지 삭제 - 2020 신규 모듈
  ## ********************************************************************************************************************
  public function ajaxfileDelete()
  {
    $mode = $this->get_parameter('mode'); ## 모드.
    $idx = $this->get_parameter('idx'); ##  글번호 idx.
    $fileIdx = $this->get_parameter('fileIdx'); ## fileidx.
    $return = $this->get_parameter('return');

    //$this->getImageList($idx, $this->module_config['table_name']);
    global $mysql;
    $data_file = array();

    $table_name = is_null($table_name) ? $this->module_config['table_name'] : $table_name;
    $result = $this->remove_upload_file($fileIdx, $table_name, $idx);

    if ($return == "json") {
      ob_clean();
      echo json_encode($result);
      exit;
    }
    exit;
    return true;
  }

  ## ********************************************************************************************************************
  ##  modify  이미지 삭제 - 2020 신규 모듈
  ## ********************************************************************************************************************
  public function ajaxSorting()
  {
    $mode = $this->get_parameter('mode'); ## 모드.
    $idxs = $this->get_parameter('idxs'); ##  글번호 idxs.
    $return = $this->get_parameter('return');


    //$this->getImageList($idx, $this->module_config['table_name']);
    global $mysql;
    $data_file = array();

    $table_name = is_null($table_name) ? $this->module_config['table_name'] : $table_name;

    $result = $this->list_sorting($this->module_config['board_id'], $table_name, $idxs);

    if ($return == "json") {
      ob_clean();
      if ($result) {
        echo 'ok';
      } else {
        echo 'fail';
      }

      exit;
    }
    exit;
    return true;
  }


  public function print_excel()
  {
    global $mysql;

    $file_name = $this->module_config['board_id'] . '_excel_' . date('Y.m.d');

    header('Content-type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $file_name . '.xls');
    header('Content-Description: PHP4 Generated Data');
    print('<meta http-equiv="Content-Type" content="application/vnd.ms-excel; charset=utf-8">');


    $idx = $this->get_parameter('idx');
    if (empty($idx)) call::xml_error('152', '', $this->referer);
    $query = 'SELECT * FROM ' . $this->module_config['table_name'] . ' WHERE board_id = "' . $this->module_config['board_id'] . '" AND del = "n"';

    $result = $mysql->query($query);
    $data = $mysql->fetch_array($result);


    echo '<table class="boardlist" cellspacing="0" cellpadding="0" border="1">';
    echo '  <colgroup>';
    echo '  	<col />';
    echo '  	<col />';
    echo '  	<col />';
    //	echo '  	<col />';
    echo '  	<col />';
    echo '  	<col />';
    echo '  	<col />';
    //	echo '  	<col />';
    echo '  </colgroup>';
    echo '  <thead>';
    echo '    <tr class="bg-color">';
    echo '        <th colspan="6">' . $data['title'] . '</th>';
    echo '    </tr>';
    echo '  </thead>';
    echo '  <tbody>';
    echo '    <tr class="bg-color">';
    echo '        <td class="center">모집인원</th>';
    echo '        <td>' . $data['quota'] . ' 명</th>';
    //	echo '        <td class="center">강사</th>';
    //	echo '        <td>'.$data['lecturer'].'</th>';
    echo '        <td class="center">교육기간</th>';
    echo '        <td>' . substr($data['lecture_start'], 0, 10) . ' ~ ' . substr($data['lecture_end'], 0, 10) . '</th>';
    echo '    </tr>';
    echo '  </tbody>';
    echo '</table>';


    echo '<br />';


    echo '<table class="boardlist" cellspacing="0" cellpadding="0" border="1">';
    echo '  <colgroup>';
    echo '  	<col />';
    echo '  	<col />';
    echo '  	<col />';
    //	echo '  	<col />';
    echo '  	<col />';
    echo '  	<col />';
    echo '  	<col />';
    //	echo '  	<col />';
    echo '  </colgroup>';
    echo '  <thead>';
    echo '    <tr class="bg-color">';
    echo '        <th>신청자</th>';
    echo '        <th>연락처</th>';
    echo '        <th>주소</th>';
    //	echo '        <th>이수여부</th>';
    echo '        <th>신청확인</th>';
    echo '        <th>접수상태</th>';
    echo '        <th>신청일</th>';
    //	echo '        <th>문의사항</th>';
    echo '    </tr>';
    echo '  </thead>';
    echo '  <tbody>';

    //$query = 'SELECT name, tel, email, zipcode, address_1, address_2, reg_date, birth FROM '.$this->module_config['table_name'].' WHERE manager_idx = "'.$idx.'" AND del = "n" ORDER BY reg_date ASC';
    global $mysql;

    $page = $this->get_parameter('page');
    $page = empty($page) ? 1 : $page;
    $mode = $this->get_parameter('mode');
    $idx = $this->get_parameter('idx');
    if (empty($idx)) call::xml_error('152', '', $this->referer);

    #전체갯수 쿼리
    $query = 'SELECT a.student_idx, a.`name`, a.complete, a.request, a.request_tel, a.request_reg_date, a.standby_yn, a.zipcode, a.address_1, a.address_2, a.contents, b.* FROM ( ';
    $query .= '    SELECT idx as student_idx, manager_idx, `name`, complete, request, tel as request_tel, reg_date as request_reg_date, standby_yn, zipcode, address_1, address_2, contents FROM ' . $this->module_config['table_name'] . ' WHERE board_id = "' . $this->module_config['board_id'] . '" AND del = "n" ';
    $query .= ') AS a ';
    $query .= 'LEFT JOIN ( ';
    $query .= '    SELECT * FROM ' . $this->module_config['table_manager'] . ' WHERE del = "n" ';
    $query .= ') AS b ON a.manager_idx = b.idx WHERE b.idx = "' . $idx . '" ORDER BY b.reg_date DESC ';
    $result = $mysql->query($query);
    $data = $mysql->fetch_array($result);

    $result = $mysql->query($query);
    for ($iter = 0; $data = $mysql->fetch_array($result); $iter++) {
      echo '    <tr>';

      echo '      <td class="center">' . $data['name'] . '</td>';
      echo '      <td class="center">' . $data['request_tel'] . '</td>';
      //		echo '      <td class="center">'.$data['email'].'</td>';
      //	echo '      <td class="center">'.$data['birth'].'</td>';
      //	echo '      <td class="center">'.$data['email'].'</td>';
      echo '      <td class="center">[' . $data['zipcode'] . ']' . $data['address_1'] . ' ' . $data['address_2'] . '</td>';
      //	echo '      <td class="center">'.($data['complete']=='y'?'완료':'미완료').'</td>';
      echo '      <td class="center">' . ($data['request'] == 'y' ? '신청완료' : '입금대기') . '</td>';
      echo '      <td class="center">' . ($data['standby_yn'] == 'y' ? '대기' : '접수') . '</td>';
      echo '      <td class="center">' . $data['request_reg_date'] . '</td>';
      //	echo '      <td class="center">'.$data['contents'].'</td>';
      echo '    </tr>';
    }

    if ($iter == 0) echo '<tr><td colspan="6" class="list_empty">검색내역이 없습니다.</td></tr>';

    echo '  </tbody>';
    echo '</table>';

    exit;
  }

  public function print_yeosun_excel()
  {
    global $mysql;

    $file_name = $this->module_config['board_id'] . '_excel_' . date('Y.m.d');

    header('Content-type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $file_name . '.xls');
    header('Content-Description: PHP4 Generated Data');
    print('<meta http-equiv="Content-Type" content="application/vnd.ms-excel; charset=utf-8">');


    /*$idx = $this->get_parameter('idx');
		if(empty($idx)) call::xml_error('152','',$this->referer);
		$query = 'SELECT * FROM '.$this->module_config['table_name'].' WHERE board_id = "'.$this->module_config['board_id'].'" AND del = "n"';

		$result = $mysql->query($query);
		$data = $mysql->fetch_array($result);*/

    echo '<table class="boardlist" cellspacing="0" cellpadding="0" border="1">';
    echo '  <colgroup>';
    echo '  	<col />';
    echo '  	<col />';
    echo '  	<col />';
    echo '  	<col />';
    echo '  	<col />';
    echo '  	<col />';
    echo '  	<col />';
    echo '  	<col />';
    echo '  	<col />';
    echo '  	<col />';
    echo '  </colgroup>';
    echo '  <thead>';
    echo '    <tr class="bg-color">';
    echo '        <th>제목</th>';
    echo '        <th>등록자명</th>';
    echo '        <th>생년월일</th>';
    echo '        <th>주소</th>';
    echo '        <th>휴대전화</th>';
    echo '        <th>피해사실(또는 피해자)과의 관계</th>';
    echo '        <th>피해유형</th>';
    echo '        <th>발생일시</th>';
    echo '        <th>발생장소</th>';
    echo '        <th>피해경위</th>';
    echo '    </tr>';
    echo '  </thead>';
    echo '  <tbody>';

    //$query = 'SELECT name, tel, email, zipcode, address_1, address_2, reg_date, birth FROM '.$this->module_config['table_name'].' WHERE manager_idx = "'.$idx.'" AND del = "n" ORDER BY reg_date ASC';
    global $mysql;

    $page = $this->get_parameter('page');
    $page = empty($page) ? 1 : $page;
    $mode = $this->get_parameter('mode');
    //$idx  = $this->get_parameter('idx');
    //if(empty($idx)) call::xml_error('152','',$this->referer);

    #전체갯수 쿼리
    $query = 'SELECT idx, title, reg_name, varchar_1, zipcode, address_1, address_2, phone, varchar_2, varchar_3, varchar_4, varchar_5, varchar_6, varchar_7, varchar_8, contents FROM `yb_board` WHERE board_id = "yeosun_incident" AND del = "n" ORDER BY idx ';
    $result = $mysql->query($query);
    $data = $mysql->fetch_array($result);

    $result = $mysql->query($query);
    for ($iter = 0; $data = $mysql->fetch_array($result); $iter++) {
      echo '    <tr>';

      echo '      <td class="center">' . $data['title'] . '</td>';
      echo '      <td class="center">' . $data['reg_name'] . '</td>';
      echo '      <td class="center">' . $data['varchar_1'] . '</td>';
      echo '      <td class="center">[' . $data['zipcode'] . ']' . $data['address_1'] . ' ' . $data['address_2'] . '</td>';
      echo '      <td class="center">' . $data['phone'] . '</td>';
      echo '      <td class="center">' . ($data['varchar_2'] == 'etc' ? '기타(' . $data['varchar_4'] . ')' : '경험·목격자') . '</td>';

      if ($data['varchar_5'] != "") {
        if ($data['varchar_5'] == "group") $varchar_5 = "양민 집단학살(암매장)";
        if ($data['varchar_5'] == "type_family") $varchar_5 = "일가족 살상";
        if ($data['varchar_5'] == "embroider") $varchar_5 = "자수자 집단 살상";
        if ($data['varchar_5'] == "type_etc") $varchar_5 = "기타";
      }

      echo '      <td class="center">' . $varchar_5 . '</td>';
      echo '      <td class="center">' . date('Y년 m월 d일', strtotime($data['varchar_6'])) . " " . ($data['varchar_7'] == "" ? "" : $data['varchar_7'] . " 시") . '</td>';
      echo '      <td class="center">' . $data['varchar_8'] . '</td>';
      echo '      <td class="center">' . $data['contents'] . '</td>';
      echo '    </tr>';
    }

    if ($iter == 0) echo '<tr><td colspan="10" class="list_empty">검색내역이 없습니다.</td></tr>';

    echo '  </tbody>';
    echo '</table>';

    exit;


  }


  function __autoload($class_name)
  {
    global $_SYSTEM;
    $class = $_SYSTEM['module_root'] . '/_plugin/setup/' . $class_name . '/class.' . $class_name . '.php';

    if (file_exists($class)) {
      require_once $class;
    } else {
      echo 'plugin autoload error : ' . $class_name;
      exit;
    }
  }

  // 2012-08-27 황재복 : 탑으로 설정된 글 갯수 쿼리
  function get_top_count($term_chk)
  {
    global $mysql;

    //$query  = 'SELECT COUNT(idx) FROM `'.$this->module_config['table_name'].'` WHERE `board_id` = "'.$this->module_config['board_id'].'" AND `top` = "y" AND `del` = "n"';
    $query = 'SELECT COUNT(idx) as cnt FROM `' . $this->module_config['table_name'] . '` WHERE `board_id` = "' . $this->module_config['board_id'] . '" AND `top` = "y" ';
    if ($this->module_config['board_id'] == 'gongik_announce') {
      $query .= ' AND (top_start <= "' . date('Y-m-d') . '" AND top_end >= "' . date('Y-m-d') . '") ';
    } else {
      if ($term_chk == 'true')
        $query .= ' AND (top_start <= "' . date('Y-m-d') . '" AND top_end >= "' . date('Y-m-d') . '") ';
    }
    $query .= ' AND `del` = "n"';

    $data = $mysql->query_fetch($query);

    return $data['cnt'];
  }

  // 2017-09-11 서희진 : 탑으로 설정된 글 제외 하기 위해서 idx번호 필요.
  function get_top_idx_array($term_chk)
  {
    global $mysql;

    $return_list = array();

    $query = ' SELECT idx FROM `' . $this->module_config['table_name'] . '` WHERE `board_id` = "' . $this->module_config['board_id'] . '" AND `top` = "y" ';
    if ($this->module_config['board_id'] == 'gongik_announce') {
      $query .= ' AND (top_start <= "' . date('Y-m-d') . '" AND top_end >= "' . date('Y-m-d') . '") ';
    } else {
      if ($term_chk == 'true')
        $query .= ' AND (top_start <= "' . date('Y-m-d') . '" AND top_end >= "' . date('Y-m-d') . '") ';
    }
    $query .= ' AND `del` = "n"';

    $result = $mysql->query($query);
    while ($data = $mysql->fetch_array($result)) $return_list[] = $data['idx'];


    return $return_list;
  }


  //2012-08-28 황재복 : 삭제 사유
  function admin_delete_reason()
  {
    global $mysql;

    ## parameter setting.
    $idx = $this->get_parameter('idx');
    $page = $this->get_parameter('page');
    $page = empty($page) ? 1 : $page;
    $search_type = $this->get_parameter('search_type');
    $search_word = $this->get_parameter('search_word');
    $category_1 = $this->get_parameter('category_1');

    $reason = addslashes($reason);
    $query = 'UPDATE `' . $this->module_config['table_name'] . '` SET `admin_comment` = "' . $reason . '" WHERE `idx` = ' . $idx;
    $result = $mysql->query($query);
  }


  function list_total_count_voucher($mysql2, $query_where)
  {

    $query_total = 'SELECT count(idx) AS cnt FROM md_board_wsboard ';
    $query_total .= $query_where;

    $data = $mysql2->query_fetch($query_total);
    return empty($data['cnt']) ? 0 : $data['cnt'];


  }

  public function intro()
  {

    $ARTICLES = array();

    $ARTICLES['myinfo'] = $this->myinfo;
    $ARTICLES['module_config'] = $this->module_config;
    $ARTICLES['permission'] = $this->permission;

    $ARTICLES['list_msg'] = $this->module_config['list_msg'];
    $ARTICLES['old_link'] = $this->module_config['old_link'];
    $ARTICLES['list_msg_no_css'] = $this->module_config['list_msg_no_css'];
    $ARTICLES['board_name'] = $this->module_config['board_name'];

    ## skin 설정
    $ARTICLES['device'] = $this->device;
    $ARTICLES['module_root'] = $this->module_root;                  ## 근본적으로 스킨쪽에서 세팅되어야 한다.
    $ARTICLES['module_path'] = $this->module_config['module_path']; ## 근본적으로 스킨쪽에서 세팅되어야 한다.
    $ARTICLES['skin_style'] = empty($this->module_config['skin_style']) ? $this->module_config['board_id'] : $this->module_config['skin_style'];
    $ARTICLES['skin_name'] = 'intro';

    ob_clean();

    echo serialize($ARTICLES);

    return true;
  }


  ## ********************************************************************************************************************
  ##  ys_singo 모듈 담당지정
  ## ********************************************************************************************************************
  public function charge_insert()
  {
    $mode = $this->get_parameter('mode'); ## 모드.
    $idx = $this->get_parameter('idx'); ##  글번호 idx.
    $charge_id = $this->get_parameter('charge_id');
    $charge_name = $this->get_parameter('charge_name');
    $charge_postname = $this->get_parameter('charge_postname');

    global $mysql;
    $query = sprintf('UPDATE %s SET charge_id = "%s", charge_name = "%s", charge_postname = "%s" WHERE idx = "%s"', $this->module_config['table_name'], $charge_id, $charge_name, $charge_postname, $idx);
    $result = $mysql->query($query);

    ob_clean();
    if ($result) {
      echo "SUCESS";
      exit;
    } else {
      echo "FAIL";
      exit;
    }
  }


  ## ********************************************************************************************************************
  ##  ys_상태 변경
  ## ********************************************************************************************************************
  public function charge_process()
  {
    $mode = $this->get_parameter('mode'); ## 모드.
    $idx = $this->get_parameter('idx'); ##  글번호 idx.
    $field = $this->get_parameter('field');
    $process = $this->get_parameter('process');
    $return = array();

    global $mysql;

    $query = sprintf('UPDATE %s SET %s = "%s" WHERE idx = "%s"', $this->module_config['table_name'], $field, $process, $idx);
    //echo $query; exit;
    $result = $mysql->query($query);


    if ($result) {
      $return['code'] = '00';
      $return['msg'] = '변경 되었습니다.';
    } else {
      $return['code'] = '01';
      $return['msg'] = '변경 실패입니다.';
    }
    ob_clean();
    echo json_encode($return);
    exit;
  }

  function send_notification($tokens, $message, $body)
  {
    $url = 'https://fcm.googleapis.com/fcm/send';

    $key = "AAAA1auw_VA:APA91bFpFUu9mGOQJd3FopBhy7zLGMB5BjtyHkorHq8tkqop2200a-XRPd3Zic4J0cHBP-iJT57OEmtrRco3Kr6_1eYRX8arqc1gFGmOgt7qoB-SN_RY_lZ1FZWDJJ9RjgEV2kOkJT2Z";
    $headers = array(
      'Authorization: key=' . $key,
      'Content-Type: application/json'
    );

    $fields = array(
      'notification' => array("body" => $body, "title" => $message, "click_action" => "noti_intent"),
      'data' => array("body" => $body, "title" => $message)
    );

    if (is_array($tokens)) {
      $fields['registration_ids'] = $tokens;
    } else {
      $fields['to'] = $tokens;
    }

    $fields['priority'] = "high";

    $fields = json_encode($fields);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

    $result = curl_exec($ch);
    if ($result === FALSE) {
      die('Curl failed: ' . curl_error($ch));
    }
    curl_close($ch);
    return $result;
  }

  function send_notification_new($tokens, $message, $body, $device)
  {
    $url = 'https://fcm.googleapis.com/fcm/send';

    $key = "AAAA1auw_VA:APA91bFpFUu9mGOQJd3FopBhy7zLGMB5BjtyHkorHq8tkqop2200a-XRPd3Zic4J0cHBP-iJT57OEmtrRco3Kr6_1eYRX8arqc1gFGmOgt7qoB-SN_RY_lZ1FZWDJJ9RjgEV2kOkJT2Z";
    $headers = array(
      'Authorization: key=' . $key,
      'Content-Type: application/json'
    );

    $fields = array(
      'notification' => array("body" => $body, "title" => $message, "click_action" => "noti_intent"),
      'data' => array("body" => $body, "title" => $message)
    );

    if (is_array($tokens)) {
      $fields['registration_ids'] = $tokens;
    } else {
      $fields['to'] = $tokens;
    }

    $fields['priority'] = "high";

    $fields = json_encode($fields);
    if ($_SERVER['REMOTE_ADDR'] == "49.254.140.140") {
//		echo "<pre>"; print_r($fields); exit;
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

    $result = curl_exec($ch);
    if ($result === FALSE) {
      die('Curl failed: ' . curl_error($ch));
    }
    curl_close($ch);
    return $result;
  }

  function get_noti_to_token($noti_list)
  {
    global $mysql;

    $return = array();

    if ($noti_list == 'all') {
      $query = 'SELECT u_fcm FROM `_member` WHERE u_fcm IS NOT NULL';
    } else {
      $query = 'SELECT u_fcm FROM `_member` WHERE idx IN (' . $noti_list . ') AND u_fcm IS NOT NULL';
    }
    $result = $mysql->query($query);
    while ($data = $mysql->fetch_array($result)) {
      $return[] = $data['u_fcm'];
    }

    return $return;
  }

}

?>