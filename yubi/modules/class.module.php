<?php
/********************************************************************
*  module의 최상위 클레스
*  module에서 공통적으로 사용하는 기능을 정의한다.
********************************************************************/

//*********constant definition used in module************************
define('GUEST_PIN', 'guest');
//*******************************************************************


class module extends smartourpack {

	public $self_url;
	public $hostname;
	public $module_root;
	//public $system_root;
	public $permission;
	public $myinfo;
	public $module_config;
	public $device;
	public $referer;
	public $config;
	
	//첨부파일기능 수정 : 오경우 (20120120)
	private $ext_allow_photo = array('jpg','gif','png','bmp', 'jpeg');
	private $ext_allow_movie = array('wmv','avi','mpeg','mpg','mp4','mov','flv');
	private $ext_allow_ebook = array('doc','docx','ppt','pptx','xls','xlsx','pdf','hwp');
	private $max_size_file   = 10;
	//private $max_size_photo  = 2;
	private $max_size_photo  = 10;
	private $max_size_movie  = 50;
	private $max_size_upload = 50;


	function __construct($system=array()) {
		$this->self_url      = $system['index_url'];
		$this->hostname      = $system['hostname'];
		$this->module_root   = $system['module_root'];
		//$this->system_root   = $system['system_root'];
		$this->permission    = $system['permission'];
		$this->permission['credit_check'] = $system['rep_login']; 
		$this->myinfo        = $system['myinfo'];
		$this->module_config = $system['module_config'];
		$this->device        = $system['device'];
		$this->referer       = empty($_SERVER['HTTP_REFERER']) ? '/' : $_SERVER['HTTP_REFERER'];
	}



	
	private function print_xml($arr_data, $header=NULL) {
		if($header == NULL) $result = ArrayToXML::toXml($arr_data);
		else $result = ArrayToXML::toXml($arr_data,$header);
		echo $result;
	}

	public function setup() {
		$module_config = $this->module_config;		## setup.php 에서 사용된다.
		$setup_file = $this->module_config['module_path'].'setup.php';
		$db_field_file = $this->module_config['module_path'].'db_field.php';
		if(is_file($db_field_file)) {
			include_once $db_field_file;
			$db_field = unserialize($db_field_list);
		}
		

		//print_r($this->module_config['module_path']);			exit;			
			
		if(!is_file($setup_file)) {
			echo call::xml_error('101','모듈설정파일 오류',$module->referer);	
		}else {							
			include_once $setup_file;			
		}
	}
	
	## 2017.03.13 서희진 추가 : 이사님 지시로 jpg사진 이미지 gps 가져오는 로직
	public function gps($coordinate, $hemisphere) {
	  for ($i = 0; $i < 3; $i++) {
		$part = explode('/', $coordinate[$i]);
		if (count($part) == 1) {
		  $coordinate[$i] = $part[0];
		} else if (count($part) == 2) {
		  $coordinate[$i] = floatval($part[0])/floatval($part[1]);
		} else {
		  $coordinate[$i] = 0;
		}
	  }
	  list($degrees, $minutes, $seconds) = $coordinate;
	  $sign = ($hemisphere == 'W' || $hemisphere == 'S') ? -1 : 1;
	  return $sign * ($degrees + $minutes/60 + $seconds/3600);
	}	

	public function board_category($cate_field, $where) {
		global $mysql;		
		$return = array();

		$query  = sprintf('SELECT %s, COUNT(idx) AS cnt FROM %s WHERE %s GROUP BY %s', $cate_field, $this->module_config['table_name'], $where, $cate_field);
if( $_SESSION['user_id'] == "djkim" ){
	//echo $query;exit;		
}
		$result = $mysql->query($query);
		while($data = $mysql->fetch_array($result)) $return[$data[$cate_field]] = $data['cnt'];

		return $return;
	}
	
	public function board_category2($cate_field, $where) {
		global $mysql;
		
		$return = array();

		$query  = sprintf('SELECT %s FROM %s WHERE %s GROUP BY %s', $cate_field, $this->module_config['table_name'], $where, $cate_field);
if( $_SESSION['user_id'] == "jini0808" ){
//echo $query;exit;		
}		

		$result = $mysql->query($query);
		while($data = $mysql->fetch_array($result)) $return[$data[$cate_field]] = $data['category_1'];

		return $return;
	}
	
	
	public function board_category_voucher($mysql2, $cate_field) {
			
		$return = array();

		$query = 'SELECT cate_value, COUNT(idx) AS cnt FROM md_board_wsboard WHERE cate = "www_couponshop" AND del = "n" GROUP BY cate_value';
if( $_SESSION['user_id'] == "djkim" ){
	//echo $query;exit;		
}
		$result = $mysql2->query($query);
		while($data = $mysql2->fetch_array($result)) $return[$data[$cate_field]] = $data['cnt'];

	
		return $return;
		
	}
	
	public function board_category_trash($where) {
		global $mysql;		
		$return = array();
		$query  = ' SELECT pay_status, COUNT(*) AS cnt FROM (';
		$query .= '	SELECT a.idx, a.reg_name, a.reg_date, a.title, a.del, a.category_1, IFNULL(b.LGD_PAY_STATUS,"대기중") AS pay_status ';
		$query .= '	FROM '.$this->module_config['table_name'].' AS a LEFT OUTER JOIN _pg_lgdacom AS b ON a.pg_code = b.LGD_OID AND b.LGD_RESPCODE="0000" ';
		$query .= ' WHERE '.$where;
		$query .= ' ) AS T GROUP BY pay_status ';
if( $_SESSION['user_id'] == "direct24" ){
	//echo $query;exit;		
}
		$result = $mysql->query($query);
		while($data = $mysql->fetch_array($result)) $return[$data['pay_status']] = $data['cnt'];

		return $return;
	}
	
	public function board_interpreter_type($where,$list_field) {
		global $mysql;
		
		$return = array();
		for($i=0;$i<count($list_field);$i++){
			$query  = 'SELECT "'.$list_field[$i].'" AS str, COUNT(idx) AS cnt FROM '.$this->module_config['table_name'].' WHERE del="n" AND workplace like "%'.$list_field[$i].'%" '; 
			$data   = $mysql->query_fetch($query);			
			$return[$data['str']] = $data['cnt'];
		}
		return $return;
	}	
	

	public function list_total_count($where, $table_name=NULL) {
		global $mysql;
		//2013-08-05 황재복 : 아래 문구 중 count(idx) 일 경우 join 했을 경우 두 테이블 다 idx 값 존해할 때 오류 생김. count(idx) => count(a.idx)

		$table_name = empty($table_name) ? $this->module_config['table_name'] : $table_name;
		
		$query  = sprintf('SELECT count(a.idx) as cnt FROM %s as a %s', $table_name, $where);

if( $_SESSION['user_id'] == "djkim" ){
//	echo $query;	exit;
}
		$data   = $mysql->query_fetch($query);
		return empty($data['cnt']) ? 0 : $data['cnt'];
	}
	
	## 강진교육정보 카테고리 포함 count
	public function list_total_count_category($where, $table_name=NULL, $category) {
		global $mysql;
		//2013-08-05 황재복 : 아래 문구 중 count(idx) 일 경우 join 했을 경우 두 테이블 다 idx 값 존해할 때 오류 생김. count(idx) => count(a.idx)

		$table_name = empty($table_name) ? $this->module_config['table_name'] : $table_name;
		$category = empty($category) ? '' : ' AND a.category_1="'.$category.'"';
		
		$query  = sprintf('SELECT count(a.idx) as cnt FROM %s as a %s %s', $table_name, $where, $category);

if( $_SESSION['user_id'] == "jgy7789" ){
//	echo $query;	exit;
}
		$data   = $mysql->query_fetch($query);
		return empty($data['cnt']) ? 0 : $data['cnt'];
	}	
	
	public function cate_total_count($table_name=NULL, $board_id) {
		global $mysql;
		//2013-08-05 황재복 : 아래 문구 중 count(idx) 일 경우 join 했을 경우 두 테이블 다 idx 값 존해할 때 오류 생김. count(idx) => count(a.idx)
		$return = array();
		$table_name = empty($table_name) ? $this->module_config['table_name'] : $table_name;
		
		$query  = sprintf('SELECT * FROM %s WHERE board_id="%s" AND del="n" AND category_1 IS NOT NULL AND category_1 <> "" GROUP BY category_1', $table_name, $board_id);
		
		$result = $mysql->query($query);
		while($data = $mysql->fetch_array($result)) $return[$data['category_1']] = $data['category_1'];

		return $return;
	}
	
	public function cate_count($table_name=NULL, $board_id, $category_1) {
		global $mysql;

		$table_name = empty($table_name) ? $this->module_config['table_name'] : $table_name;
		
		$query  = sprintf('SELECT count(idx) as cnt FROM %s WHERE board_id="%s" AND del="n"', $table_name, $board_id);
		
		$data   = $mysql->query_fetch($query);
		return empty($data['cnt']) ? 0 : $data['cnt'];
	}
	
	
	## 서희진 추가 카테고리 목록 가져오기.
	function list_cate($table_name=NULL, $board_id) {
		global $mysql;

		$return = array();
		$table_name = empty($table_name) ? $this->module_config['table_name'] : $table_name;
		
		$query  = sprintf('SELECT * FROM %s WHERE board_id="%s" AND del="n" AND category_1 IS NOT NULL AND category_1 <> "" GROUP BY category_1', $table_name, $board_id);
		
		$result = $mysql->query($query);
		while($data = $mysql->fetch_array($result)) $return[] = $data['category_1'];

		return $return;		
	}
	
	
	## 서희진 추가 카테고리 목록 갯수 
	function list_cate_count($table_name=NULL, $board_id, $where_query ) {
		global $mysql;
		
		$return = array();
		$table_name = empty($table_name) ? $this->module_config['table_name'] : $table_name;
		
		
		if( !empty($where_query) ){
			$where_query = str_replace('WHERE','AND', $where_query);
		}	

		
		$query  = sprintf('SELECT category_1, count(idx) as cnt FROM %s WHERE category_1<>"" AND board_id="%s" AND del="n" %s GROUP BY category_1', $table_name, $board_id, $where_query);
		//echo $query;		exit;
		$result = $mysql->query($query);		
		while($data = $mysql->fetch_array($result)) {
			$return[$data['category_1']] = $data['cnt'];	
		}
		
		return $return;
	}
	
	
	public function list_total_count_foodViewList($where, $table_name=NULL) {
		global $mysql;
		//2013-08-05 황재복 : 아래 문구 중 count(idx) 일 경우 join 했을 경우 두 테이블 다 idx 값 존해할 때 오류 생김. count(idx) => count(a.idx)

		$table_name = empty($table_name) ? $this->module_config['table_name'] : $table_name;
		
		$query  = ' SELECT count(A.idx) AS cnt ';
		$query .= ' FROM `_smartourpack` AS A  ';
		$query .= ' LEFT OUTER JOIN  ';
		$query .= ' 	(  ';
		$query .= ' 	SELECT idx AS img_idx, file_type AS img_type, original_name AS ori_name, re_name, file_path AS img_path ';
		$query .= ' 				, file_size AS img_size ,seq AS img_seq, download AS img_download, title AS img_title FROM _file_info  ';
		$query .= ' 	) AS C  ';
		$query .= ' ON A.mainimage_idx = C.img_idx  ';
		$query .= $where;		

		$data   = $mysql->query_fetch($query);
		return empty($data['cnt']) ? 0 : $data['cnt'];
	}	
	
	/* 좋아요 기능 */
	public  function increment_recomm($data) {
		global $mysql;
		 $table_name = $data['table_name'];
		 $table_idx = $data['table_idx'];
		
//		if(empty($_SESSION['user_pin'])) return;
//		if(empty($_SESSION['user_pin']) || empty($table_idx) || empty($table_name)) return;
		$query = sprintf('SELECT session_info FROM _recommend_info WHERE table_name = "%s" AND table_idx = "%s"', $table_name, $table_idx);
		$data   = $mysql->query_fetch($query);

		$current_time = time();
		if(empty($data)) { 
//			$session_info[] = $_SESSION['user_pin'];
//			$query = sprintf('INSERT INTO _recommend_info SET table_name="%s", table_idx="%s", visit_cnt=1, session_info="%s"', $table_name, $table_idx, addslashes(serialize($session_info)));
			$query = sprintf('INSERT INTO _recommend_info SET table_name="%s", table_idx="%s", visit_cnt=1', $table_name, $table_idx);
		} else { 
//			$query = sprintf('SELECT count(*) as cnt FROM _recommend_info WHERE table_name = "%s" AND table_idx = "%s" AND session_info REGEXP "%s"', $table_name, $table_idx, $_SESSION['user_pin']);
//			$query = sprintf('SELECT count(*) as cnt FROM _recommend_info WHERE table_name = "%s" AND table_idx = "%s" ', $table_name, $table_idx);
//			$check   = $mysql->query_fetch($query);
//			if($check['cnt']==0||$check['cnt']=='') {
//				$session_info = unserialize($data['session_info']);
//				$session_info[] = $_SESSION['user_pin'];
//				$query = sprintf('UPDATE _recommend_info SET visit_cnt=visit_cnt+1, session_info="%s" WHERE table_name="%s" AND table_idx="%s"', addslashes(serialize($session_info)), $table_name, $table_idx);
				$query = sprintf('UPDATE _recommend_info SET visit_cnt=visit_cnt+1 WHERE table_name="%s" AND table_idx="%s"', $table_name, $table_idx);
//			} else {
//				return;	
//			}
		}

		$mysql->query($query);
		return;
	}
	
	## 급하게 만들어서 코드가 지저분하다. 나중에 정리하자.
	public function increment_visit($table_idx, $table_name, $expire=60) {
	//public function increment_visit($table_idx, $table_name, $expire=60,$memo='') {
		global $mysql;
		global $_SYSTEM;		

		// 2012.08.29 조회수 증가로직 변경 관련 : url로 바로 들어왔을경우 쿠키가 없을때 조회수 증가가 안되는 현상 해결.(강성수)
		//if(empty($_COOKIE['PHPSESSID']) || empty($table_idx) || empty($table_name)) return;
		$session_id = session_id();	// session_id() 의 리턴값이 value가 아닌 주소값 참조형식으로 넘어오는것으로 추정, 바로 empty()로 검사가 안되는듯.
		if(empty($session_id) || empty($table_idx) || empty($table_name)) return;

		$session_info = array();
		$expire *= 60;

		$query = sprintf('SELECT session_info FROM _visit_info WHERE table_name = "%s" AND table_idx = "%s" AND device = "%s"', $table_name, $table_idx, $_SYSTEM['device']);
	
		$data   = $mysql->query_fetch($query);
		
		## 게시판 열람자 기록...
		//$query = sprintf('INSERT INTO _visit_list SET table_name="%s", table_idx="%s", user_id="%s", user_name="%s", user_pin="%s", remote_ip="%s", memo="%s", visit_date="%s"', $table_name, $table_idx, $_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_pin'], $_SERVER['REMOTE_ADDR'],$memo, date('Y-m-d H:i:s'));
		//$mysql->query($query);

		## cookie id 값을 index로 하고 글을 본 시간을 value로 갖는 배열을 생성해서 serialize하여 디비에 저장한다.
		$current_time = time();
		if(empty($data)) { ## 값이 아예 없을 경우 - 배열을 생성해서 디비에 insert
			$session_info[$_COOKIE['PHPSESSID']] = $current_time;
			$query = sprintf('INSERT INTO _visit_info SET table_name="%s", table_idx="%s", visit_cnt=1, session_info="%s", device="%s"', $table_name, $table_idx, addslashes(serialize($session_info)), $_SYSTEM['device']);

		} else { ## 값이 있을경우
			$session_info = unserialize($data['session_info']);
			## 만료시간 전일 경우 skip
			if(array_key_exists($_COOKIE['PHPSESSID'], $session_info) && $session_info[$_COOKIE['PHPSESSID']] > ($current_time - $expire)) return;

			if($expire > 0) { ## 만료시간이 지났을 경우.
				foreach($session_info as $key=>$value) if($value < ($current_time - $expire)) unset($session_info[$key]); ## 시간이 지난것은 삭제한다.
				$session_info[$_COOKIE['PHPSESSID']] = $current_time;
				$query = sprintf('UPDATE _visit_info SET visit_cnt=visit_cnt+1, session_info="%s" WHERE table_name="%s" AND table_idx="%s" AND device = "%s"', addslashes(serialize($session_info)), $table_name, $table_idx, $_SYSTEM['device']);
			} else {          ## 만료시간 설정이 없을 경우 visit_cnt만 증가시킨다.
				$query = sprintf('UPDATE _visit_info SET visit_cnt=visit_cnt+1 WHERE table_name="%s" AND table_idx="%s" AND device = "%s"', $table_name, $table_idx, $_SYSTEM['device']);
			}
		}	
		
		$mysql->query($query);

		$this->article_log_cms40('view', $table_name, $table_idx);

		return;
	}
	
	public function increment_visit_faq($table_idx, $table_name, $expire=60) {
	//public function increment_visit($table_idx, $table_name, $expire=60,$memo='') {
		global $mysql;
		global $_SYSTEM;		
		
		// 2012.08.29 조회수 증가로직 변경 관련 : url로 바로 들어왔을경우 쿠키가 없을때 조회수 증가가 안되는 현상 해결.(강성수)
		//if(empty($_COOKIE['PHPSESSID']) || empty($table_idx) || empty($table_name)) return;
		$session_id = session_id();	// session_id() 의 리턴값이 value가 아닌 주소값 참조형식으로 넘어오는것으로 추정, 바로 empty()로 검사가 안되는듯.
		if(empty($session_id) || empty($table_idx) || empty($table_name)) return;

		$session_info = array();
		$expire *= 60;

		$query = sprintf('SELECT session_info FROM _visit_info WHERE table_name = "%s" AND table_idx = "%s" AND device = "%s"', $table_name, $table_idx, $_SYSTEM['device']);
		$data   = $mysql->query_fetch($query);
		
		## 게시판 열람자 기록...
		//$query = sprintf('INSERT INTO _visit_list SET table_name="%s", table_idx="%s", user_id="%s", user_name="%s", user_pin="%s", remote_ip="%s", memo="%s", visit_date="%s"', $table_name, $table_idx, $_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_pin'], $_SERVER['REMOTE_ADDR'],$memo, date('Y-m-d H:i:s'));
		//$mysql->query($query);

		## cookie id 값을 index로 하고 글을 본 시간을 value로 갖는 배열을 생성해서 serialize하여 디비에 저장한다.
		$current_time = time();
		if(empty($data)) { ## 값이 아예 없을 경우 - 배열을 생성해서 디비에 insert
			$session_info[$_COOKIE['PHPSESSID']] = $current_time;
			$query = sprintf('INSERT INTO _visit_info SET table_name="%s", table_idx="%s", visit_cnt=1, session_info="%s"', $table_name, $table_idx, addslashes(serialize($session_info)));
		} else { ## 값이 있을경우
			$session_info = unserialize($data['session_info']);
			## 만료시간 전일 경우 skip
			if(array_key_exists($_COOKIE['PHPSESSID'], $session_info) && $session_info[$_COOKIE['PHPSESSID']] > ($current_time - $expire)) return;

			if($expire > 0) { ## 만료시간이 지났을 경우.
				foreach($session_info as $key=>$value) if($value < ($current_time - $expire)) unset($session_info[$key]); ## 시간이 지난것은 삭제한다.
				$session_info[$_COOKIE['PHPSESSID']] = $current_time;
				$query = sprintf('UPDATE _visit_info SET visit_cnt=visit_cnt+1, session_info="%s" WHERE table_name="%s" AND table_idx="%s"', addslashes(serialize($session_info)), $table_name, $table_idx);
			} else {          ## 만료시간 설정이 없을 경우 visit_cnt만 증가시킨다.
				$query = sprintf('UPDATE _visit_info SET visit_cnt=visit_cnt+1 WHERE table_name="%s" AND table_idx="%s"', $table_name, $table_idx);
			}
		}
		$mysql->query($query);

		$this->article_log_cms40('list', $table_name, $table_idx);

		return;
	}



	## 파일정보 추출.
	public function attached_photo_list($idx, $table_name=NULL) {
		global $mysql;
		$data_file = array();
		
		$table_name = is_null($table_name) ? $this->module_config['table_name'] : $table_name;
		$query  = 'SELECT idx, file_type, original_name, re_name, file_path, file_size, seq, download, title, description, tag, ebook_code, movie_duration, title_image, pwd ';
		$query .= 'FROM _file_info ';
		$query .= 'WHERE table_name = "'.$table_name.'" AND table_idx = "'.$idx.'" AND file_type = "photo" AND movie_capture <> "y" ';
		$query .= 'ORDER BY file_type ASC, seq ASC, idx ASC';

		$result = $mysql->query($query);
		while($data_file[] = $mysql->fetch_array($result)) ;
		array_pop($data_file);

		return $data_file;
	}
	
	
	## 이미지파일정보추출 (ajax)업로드.
	public function attached_photo_ajax_list($idx, $table_name=NULL) {
		global $mysql;
		$data_file = array();
		
		$table_name = is_null($table_name) ? $this->module_config['table_name'] : $table_name;
		$query = ' SELECT * FROM ';
		$query  .= ' ( SELECT idx, file_type, original_name, re_name, file_path, file_size, seq, download, title, description, tag, ebook_code, movie_duration, title_image, pwd ';
		$query .= ' FROM _file_info ';
		$query .= ' WHERE table_name = "'.$table_name.'" AND table_idx = "'.$idx.'" AND file_type = "photo" AND movie_capture <> "y" ) AS A ';
		$query .= ' LEFT OUTER JOIN ';
		$query .= ' ( SELECT mainimage_idx, "0" AS sort FROM '.$table_name.' WHERE idx = "'.$idx.'" ) AS B ON A.idx = B.mainimage_idx ';
		$query .= ' ORDER BY file_type ASC, sort DESC, seq ASC, idx ASC ';
		

		$result = $mysql->query($query);
		while($data_file[] = $mysql->fetch_array($result)) ;
		array_pop($data_file);
		//2016-10-05 황재복 : 대표사진 제외 나오도록
//		array_shift($data_file);
		return $data_file;
	}
	
	## 이미지파일정보추출 (ajax)업로드.
	public function attached_photo_ajax_view($idx, $table_name=NULL, $mainimg=true) {
		global $mysql;
		$data_file = array();
		
		$table_name = is_null($table_name) ? $this->module_config['table_name'] : $table_name;
		$query = ' SELECT * FROM ';
		$query  .= ' ( SELECT idx, file_type, original_name, re_name, file_path, file_size, seq, download, title, description, tag, ebook_code, movie_duration, title_image, open, pwd ';
		$query .= ' FROM _file_info ';
		$query .= ' WHERE table_name = "'.$table_name.'" AND table_idx = "'.$idx.'" AND file_type = "photo" AND movie_capture <> "y" ) AS A ';
		$query .= ' LEFT OUTER JOIN ';
		$query .= ' ( SELECT mainimage_idx, "0" AS sort FROM '.$table_name.' WHERE idx = "'.$idx.'" ) AS B ON A.idx = B.mainimage_idx ';
		$query .= ' ORDER BY file_type ASC, sort DESC, seq ASC, idx ASC ';

		$result = $mysql->query($query);
		while($data_file[] = $mysql->fetch_array($result)) ;
		array_pop($data_file);
		
		if( $mainimg ){
			//2016-10-05 황재복 : 대표사진 제외 나오도록
			array_shift($data_file);
		}
		
		return $data_file;
	}	
	
	## 이미지파일정보추출 (ajax)업로드.
	public function attached_photo_ajax_view2($idx, $table_name=NULL, $mainimg=true) {
		global $mysql;
		$data_file = array();
		
		$table_name = is_null($table_name) ? $this->module_config['table_name'] : $table_name;
		$query = ' SELECT * FROM ';
		$query  .= ' ( SELECT idx, file_type, original_name, re_name, file_path, file_size, seq, download, title, description, tag, ebook_code, movie_duration, title_image, open, pwd ';
		$query .= ' FROM _file_info ';
		$query .= ' WHERE table_name = "'.$table_name.'" AND table_idx = "'.$idx.'" AND file_type = "photo" AND movie_capture <> "y" ) AS A ';
		$query .= ' LEFT OUTER JOIN ';
		$query .= ' ( SELECT mainimage_idx, "0" AS sort FROM '.$table_name.' WHERE idx = "'.$idx.'" ) AS B ON A.idx = B.mainimage_idx ';
		$query .= ' ORDER BY file_type ASC, sort DESC, seq ASC, idx ASC ';

		$result = $mysql->query($query);
		while($data_file[] = $mysql->fetch_array($result)) ;
		array_pop($data_file);
		
		if( $mainimg ){
			//2016-10-05 황재복 : 대표사진 제외 나오도록
			//array_shift($data_file);
		}
		
		return $data_file;
	}
	##230717 장비DB 파일 권재영
	public function equipment_remove_upload_file_all($table_name, $manageCode) {
		global $mysql;

		$data   = array();

		if(empty($table_name) || empty($manageCode)) return false;

		$query  = sprintf('SELECT idx, file_path FROM _file_info WHERE table_name = "%s" AND manageCode = "%s"', $table_name, $manageCode);
		$result = $mysql->query($query);

		## unlink file.
		while($data = $mysql->fetch_array($result)) {
			$file_path  = $this->module_root.$data['file_path'];
			if(is_file($file_path)) unlink($file_path);
		}

		## delete db.
		$query = sprintf('DELETE FROM _file_info WHERE table_name = "%s" AND manageCode = "%s"', $table_name, $manageCode);
		$result = $mysql->query($query);
		if($result == false) return false;
		else return true;
	}
	##230717 장비DB 파일 권재영
	public function nearby_articles_equipment($table_name,$manageCode,$sort) {
		global $mysql;
		$query = '';
		$query_view_permission = array();
		
		
		if($this->permission['manage'] === true || $this->permission['admin'] === true){
		}else{
		}
			$query_view_permission[] = 'allow <> "n"';
			$query_view_permission[] = '`open` <> "n"';
			$query_view_permission[] = 'del <> "y"';
		$query .= '
			SELECT a.* FROM (
			(
				SELECT
					NULL AS "manageCode",
					NULL AS "title",
					NULL AS "contents",
					NULL AS "reg_date",
					NULL AS "del",
					NULL AS "open",
					NULL AS "allow",
					NULL AS "type"
			)
			UNION ALL
			(SELECT manageCode,title,contents,reg_date,del,open,allow,"prev" FROM '.$table_name.' WHERE sort > "'.$sort.'"'.(!empty($query_view_permission) ? 'AND '.implode(' AND ',$query_view_permission) : '').' ORDER BY sort ASC LIMIT 1)
			UNION ALL
			(SELECT manageCode,title,contents,reg_date,del,open,allow,"next" FROM '.$table_name.' WHERE sort < "'.$sort.'" '.(!empty($query_view_permission) ? 'AND '.implode(' AND ',$query_view_permission) : '').' ORDER BY sort DESC LIMIT 1)
			) AS a  LIMIT 3';
		
		#echo $query;		exit;
		$data_list = array();
		$result = $mysql->query($query);
		while($self = $mysql->fetch_array($result)){
			if(!$self)continue;
			$data_list[] =$self; 
		}
		return $data_list;
	}
	##230717 장비DB 파일 권재영
	public function equipment_file_list($manageCode,$type="") {
		global $mysql;
		global $_SYSTEM;
		$data_file = $data = array();
		
		$query  = 'SELECT idx, file_type, original_name, re_name, file_path, file_size, download, title, description, tag, ebook_code, movie_duration, title_image, pwd, original_name as board_title ';
		$query .= 'FROM equipment_file_info ';
		$query .= 'WHERE table_name = "'.$this->module_config['table_name'].'" AND manageCode = "'.$manageCode.'" AND movie_capture <> "y" ';
		
		if( $_SERVER['REMOTE_ADDR'] == "49.254.140.140" ){
		//echo $query;		exit;
		}
		
		$query .= 'ORDER BY';
		$query .= ' file_type ASC, seq DESC, idx ASC';
		
		
		$result       = $mysql->query($query);
		while($data = $mysql->fetch_array($result)){
			$data['file_ext'] = strtolower($this->get_file_extension($data['re_name']));
			
			## 파일만 목록화 하고 싶을떄 20221005 서희진 추가 
			/*if( $type == "file" ){
				if( $data['file_type'] != "photo" )	{
					$data_file[] = $data;
				}
			}else{
				$data_file[] = $data;
			}*/
			
			$data_file[] = $data;
		}	

		return $data_file;
	}
	## 230717 장비DB 파일 권재영
	## 반환되는값(0/1)
	public function equipment_title_image($table_name,$board_id,$manageCode,$file_list,$mod_idx) {
		global $mysql;
		$query = '';
		/*
		if(empty($table_name) || empty($board_id) || empty($table_idx) || empty($file_list) || empty($mod_idx)){
			return false;
		}
		*/
		$file_idxs = array();
		$query .= sprintf('UPDATE `%s` SET title_image = ','equipment_file_info');
		$query .= 'CASE ';
		foreach($file_list as $file_key=>$file_data){
			$file_idxs[] = $file_data['idx'];
			if(!$file_data)continue;
			//현재는_한개만_바꿈
			//다중으로_할_필요가있을경우_$mod_idx를배열로바꾸고_in_array함수로_비교하면됨.
			if($file_key == $mod_idx){
				$query .= sprintf('WHEN idx = %s THEN "y" ',$file_data['idx']);
			}else{
				$query .= sprintf('WHEN idx = %s THEN "n" ',$file_data['idx']);
			}
		}
		$query .= 'END ';
		$query .= 'WHERE idx IN('.implode(',',$file_idxs).') ';
		$query .= sprintf('AND table_name = "%s" ',$table_name);
		$query .= sprintf('AND manageCode = "%s" ',$manageCode);
		//echo $query;exit;
		$mysql->query($query);
		unset($query,$file_idxs);
		return true;
	}
	## 230717 장비DB 파일 권재영
	public function remove_equipment_file($idx, $table_name, $manageCode) {
		global $mysql;
		$data = array();

		if(empty($idx)) return false;

		$query = sprintf('SELECT idx, file_path FROM equiptment_file_info WHERE table_name = "%s" AND manageCode = "%s" AND (idx = "%s" OR (movie_idx = "%s" AND movie_capture = "y"))', $table_name, $manageCode, $idx, $idx);

		$file_path = array();
		$result = $mysql->query($query);
		for($iter=0 ; $buffer = $mysql->fetch_array($result) ; $iter++) $file_path[$iter] = $buffer['file_path'];

		$query = sprintf('DELETE FROM `%s` WHERE idx = "%s" OR (movie_idx = "%s" AND movie_capture = "y")', "equipment_file_info", $idx, $idx);
		$result = $mysql->query($query);
		if($result == false) return false;

		foreach($file_path as $value) {
			$remove_path  = $this->module_root.$value;
			if(is_file($remove_path)) unlink($remove_path);
		}

		return true;
	}
	## 230717 장비DB 파일 권재영
	public function equipment_upload_file($file_info, $file, $ext_list, $dir_option='', $resize=array('w'=>false, 'h'=>false), $watermark=NULL, $board_id=NULL) {
		$board_id = empty($board_id) ? $this->module_config['board_id'] : $board_id;
		
		
		global $mysql;
	
		set_time_limit(0);		

		## 업로드가 금지된 확장자 인지 체크한다. class.php의 null_check에서 선행처리하는데 다른곳에서 사용될 경우 누락(해킹)의 위험때문에 한번 더 넣었음.
		if($this->check_value_in_list($file['name'], $ext_list) == true) return false;

		## 디렉토리 검사해서 없으면 만들고
		$directory  = $this->module_root.'/_data/'.$this->module_config['package_id'];

		$this->make_folder($directory);
		
		
		if( !empty($dir_option) ){
			$directory .= '/'.$dir_option;
		}else{
			//$directory .= '/'.$this->module_config['board_id'];
			$directory .= '/'.$board_id;
		}



			
	
		$test = $this->make_folder($directory);
	
		## 파일이름 설정
		$file_ext      = strtolower($this->get_file_extension($file['name']));
		$file_pre_name = time();
		
		
		$iter = 0;
		do {
			$file_name = $file_pre_name.($iter > 0 ? '_'.$iter : '').'.'.$file_ext;
			$file_path = $directory.'/'.$file_name;
			$iter++;
		} while(file_exists($file_path) || file_exists($file_path.'.encrypt'));
		//} while(is_file($file_path));

			
					
		## file copy and db update
		if(move_uploaded_file($file['tmp_name'],$file_path) == false) return false;



		$ext_allow_photo = array();
		$ext_allow_movie = array();
		$ext_allow_ebook = array();
		$ext_allow_photo = empty($this->module_config['ext_photo_file'])  ? $this->ext_allow_photo : explode(',',$this->module_config['ext_photo_file']);
		$ext_allow_movie = empty($this->module_config['ext_movie_file'])  ? $this->ext_allow_movie : explode(',',$this->module_config['ext_movie_file']);
		$ext_allow_ebook = empty($this->module_config['ext_ebook_file'])  ? $this->ext_allow_ebook : explode(',',$this->module_config['ext_ebook_file']);
		$file_list       = array();



		
		
		if(in_array($file_ext, $ext_allow_photo)) { ## 이미지 파일일 경우
			if($resize['w']!=false && $resize['h']!=false) {				
				$temp_file_path = $directory.'/temp_'.$file_name;
		
				
			
				rename($file_path, $temp_file_path);					
				$size = getimagesize($temp_file_path);				
				
				if($size[0]>$resize['w']) { //사이즈를 변경후 비율에 맞춰서 Crop (일그러짐 방지)
					exec('/usr/bin/convert  -size '.$size[0].'x'.$size[1].' '.$temp_file_path.' -thumbnail '.$resize['w'].'x'.$resize['h'].'^ -gravity center -extent '.$resize['w'].'x'.$resize['h'].'  '.$file_path);
	
				} else {
					exec('cp -rf '.$temp_file_path.' '.$file_path);
				}				
				unlink($temp_file_path);			

			} else if($file['size'] > 10485760) {
				$temp_file_path = $directory.'/temp_'.$file_name;
				rename($file_path, $temp_file_path);
				$resize = floor((9000000 / $file['size']) * 200);
				exec('/usr/bin/convert  -resize '.$resize.'%x'.$resize.'% -quality 100 '.$temp_file_path.' '.$file_path);
				unlink($temp_file_path);
			}
				

		
			if(!is_null($watermark) && is_file($watermark)) exec('/usr/bin/composite -watermark 30% -gravity SouthEast '.$watermark.' '.$file_path.' '.$file_path);

			$file_list[0]['file_type'] = 'photo';
			$file_list[0]['original_name'] = $file['name'];
			$file_list[0]['re_name'] = $file_name;
			$file_list[0]['file_size'] = $file['size'];
			$file_list[0]['file_path'] = str_replace($this->module_root, '', $file_path);
			$file_list[0]['open'] = $file['open'];
			$file_list[0]['main'] = $file['main'];
			## 2017.03.13 서희진 : 이사님 지시로 position 값 넣기 ---- start
			## 7.2 이상 버전용으로 변경되어야함. 잠시 막아둠.
			//$exif_data = exif_read_data($file_path, 'IFD0');			
			//$fp = fopen($file_path, 'rb');
			//$headers = exif_read_data($fp);print_r( $headers )	;exit;			
			
			
			if( $exif_data["GPSLongitude"] && $exif_data["GPSLatitude"] ){
				$file_list[0]['GPS_lon'] = $this->gps($exif_data["GPSLongitude"], $exif_data['GPSLongitudeRef']);				
				$file_list[0]['GPS_lat'] = $this->gps($exif_data["GPSLatitude"], $exif_data['GPSLatitudeRef']);								
				
						
			## 2017.03.13 서희진 : 이사님 지시로 position 값 넣기 ---- end
			}

			$file_list[0]['open'] = $file['open'];				
		} elseif(in_array($file_ext, $ext_allow_movie)) { ## 동영상 파일일 경우			
			## flv convert
			$ffmpegCommand 		= '/usr/bin/ffmpeg'; // x264, xbix, ora, gsm, lame, faac, swscale, 0.5
			$qt_faststart 		= '/usr/bin/qt-faststart';
			/*
			#yum install svn
			#svn checkout svn://svn.ffmpeg.org/ffmpeg/trunk ffmpeg
			#cd ffmpeg
			#./configure
			#make
			#make tools/qt-faststart
			#cp -a tools/qt-faststart /usr/bin/
			
			*/
			
			
			$new_ext = '.mp4';
			if(extension_loaded('ffmpeg') == true) {
				set_time_limit(0); ## php excution time increase;


				$path_info = pathinfo($file_path);
				$movie = new ffmpeg_movie($file_path);
				$capture_count = 5;
				$total_frame = $movie->getFrameCount();
				$movie_duration = $movie->getDuration();


				$frame_interval = $capture_count > 0 ? floor($total_frame / $capture_count) : 0;

				## capture
				for($iter = 0; $iter <= $capture_count ; $iter++ ) {
					$frame = $movie->getFrame($iter * $frame_interval + 1);
					if($frame) {
						$gd_image = $frame->toGDImage();
						$captuer_image_tmp_path = $path_info['dirname'].'/'.$path_info['filename'].'_temp_'.$iter.'.png';
						$captuer_image_path = $path_info['dirname'].'/'.$path_info['filename'].'_thumb_'.$iter.'.png';
						
						header("Content-Type: image/png");
						imagepng($gd_image, $captuer_image_tmp_path);
						imagedestroy($gd_image);
						//$thumb_size = ($iter == 0) ? '640x480' : '80x60';
						$thumb_size =  '640x480';
						
						exec('/usr/bin/convert -resize '.$thumb_size.' '.$captuer_image_tmp_path.' '.$captuer_image_path);						
//						unlink($captuer_image_tmp_path);

						$file_list[$iter+1]['file_type'] = 'photo';
						$file_list[$iter+1]['original_name'] = $path_info['filename'].'_thumb_'.$iter.'.png';
						$file_list[$iter+1]['re_name'] = $path_info['filename'].'_thumb_'.$iter.'.png';
						$file_list[$iter+1]['file_size'] = filesize($captuer_image_path);
						$file_list[$iter+1]['file_path'] = str_replace($this->module_root, '', $captuer_image_path);
						$file_list[$iter+1]['movie_capture'] = 'y';
					}
				}
			} else {
				$path_info = pathinfo($file_path);
				$captuer_image_tmp_path = $path_info['dirname'].'/'.$path_info['filename'].'_temp_'.$iter.'.png';
				$captuer_image_path = $path_info['dirname'].'/'.$path_info['filename'].'_thumb_'.$iter.'.png';
				exec($ffmpegCommand.' -ss 00:00:03.01 -i '.$file_path.' -y -f image2 -vcodec mjpeg -vframes 1 '.$captuer_image_tmp_path);
			    
				$thumb_size =  '640x480';
				exec('/usr/bin/convert -resize '.$thumb_size.' '.$captuer_image_tmp_path.' '.$captuer_image_path);
				
				$file_list[1]['file_type'] = 'photo';
				$file_list[1]['original_name'] = $path_info['filename'].'_thumb_'.$iter.'.png';
				$file_list[1]['re_name'] = $path_info['filename'].'_thumb_'.$iter.'.png';
				$file_list[1]['file_size'] = filesize($captuer_image_path);
				$file_list[1]['file_path'] = str_replace($this->module_root, '', $captuer_image_path);
				$file_list[1]['movie_capture'] = 'y';
				
			}
				
				if($file_info['use_streaming'] == 'true') { ## mp4 변환
					$new_ext = '.mp4';
					$allowedMimeTypes = array('video/avi','video/mp4','video/mpeg','video/quicktime','video/x-msvideo','video/msvideo','video/x-ms-wmv');
				
				
					// Video
					$videoSize 			= isset($_POST['video_size']) 					? $_POST['video_size'] 		: '640x360';
					$videoBitrate 		= isset($_POST['video_bitrate'])				? (int)$_POST['video_bitrate'] 	: '700';
					$videoFramerate		= isset($_POST['video_framerate'])				? (int)$_POST['video_framerate'] : '30';
					$videoDeinterlace	= isset($_POST['encoding_video_deinterlace'])	? 1 : 0 ;
					$videoAspect 		= isset($_POST['video_aspect']) 				? $_POST['video_aspect'] 		: 0; //-aspect $videoAspect
					
				
if( $_SERVER['REMOTE_ADDR'] == "49.254.140.140" ){
 //print_r($_POST);exit;
}					
					// Adudio
					$audioEnabled		= isset($_POST['encoding_enable_audio'])		? 0 : 1 ;
					$audioSamplerate	= isset($_POST['encoding_audio_sampling_rate'])	? (int)$_POST['encoding_audio_sampling_rate'] : '44100';
					$audioBitrate		= isset($_POST['encoding_audio_bitrate'])		? (int)$_POST['encoding_audio_bitrate'] : '128';
					$audioChannels		= (isset($_POST['encoding_audio_channels']) && $_POST['encoding_audio_channels']	== 'stereo')	? 2 : 1 ;
				
					// Build up the ffmpeg params from the values posted from the html form
					$customParams  = ' -s '.$videoSize; 				// Format the video size
					$customParams .= ' -b '.$videoBitrate.'k'; 		// Format the video bit rate  -- -vb
					$customParams .= ' -r '.$videoFramerate;			// Format the video frame rate
					if ($videoAspect) {
						$customParams .= ' -aspect '.$videoAspect;		// aspect ratio 
					}
					if ($videoDeinterlace) {
						$customParams .= ' -deinterlace ';				// Deinterlace the video
					}
					if ($audioEnabled) {
						$customParams .= ' -ar '.$audioSamplerate;		// Audio sample rate
						$customParams .= ' -ab '.$audioBitrate.'k';		// Audio bit rate
						$customParams .= ' -ac '.$audioChannels;		// Audio Channels
					} else {
						$customParams .= ' -an '; 						// Disable audio
					}
					$customParams .= ' -y ';							// Overwrite existing file
					
					
					$file_path_mp4 = $path_info['dirname'].'/'.$path_info['filename'].'_new.mp4';
				
					$command = $ffmpegCommand.' -i '.$file_path.' -vcodec libx264  -vpre superfast -vpre baseline -vsync 1  '; //  -bt 50k
					if ($audioEnabled) {
						$command = $command.' -acodec libfaac ';
					}
					$command = $command.$customParams.' -threads 12 '.$file_path_mp4.' 2>&1';
					///ffmpeg -i /home/encode/data/file.avi -threads 0 -vcodec libx264 -b 800k -r 24 -s 640x360 -vpre superfast -vpre baseline -acodec libfaac -ac 2 -ab 192k -ar 48000 -y /home/encode/mp4/file.mp4 	
					
					exec($command);
					exec($qt_faststart.' '.$file_path_mp4.' '.$file_path_mp4.' 2>&1');
				
					//exec($ffmpegCommand.' -i '.$file_path.' -threads 16 -b 604k -ac 1 -ar 44100 -coder 1 -flags +loop -cmp +chroma -partitions +parti4x4+partp8x8+partb8x8 -subq 5 -g 250 -keyint_min 25 -sc_threshold 40 -i_qfactor 0.71 '.$file_path_mp4);
					
					//if($file_path_mp4!=$file_path) 
					//unlink($file_path);
					exec('mv '.$file_path_mp4.' '.$file_path);
					//$file_path = $file_path_mp4;
					$file['size'] = filesize($file_path);
				} else {
					/*
					if($file_ext != 'flv') { ## flv 변환
						$new_ext = '.flv';
						$m_bitrate = $movie->getBitRate();
						$m_bitrate = ceil( $m_bitrate / 1000 );
						$file_path_flv = $path_info['dirname'].'/'.$path_info['filename'].'.flv';
						exec('/usr/local/bin/ffmpeg -i '.$file_path.' -ar 44100 -f flv -ab '.$m_bitrate.' -s '.$movie->getFrameWidth().'x'.$movie->getFrameHeight().' '.$file_path_flv);
						unlink($file_path);
						$file_path = $file_path_flv;
						$file['size'] = filesize($file_path);
					}
					*/
					
				}





				$file_list[0]['file_type'] = 'movie';
				//$file_list[0]['original_name'] = str_replace($this->get_file_extension($file['name']), 'flv', $file['name']);
				$file_list[0]['original_name'] = $file['name'];
				$file_list[0]['re_name'] = $path_info['filename'].$new_ext;
				$file_list[0]['file_size'] = $file['size'];
				$file_list[0]['file_path'] = str_replace($this->module_root, '', $file_path);
				
				//2016-11-22 황재복 : 동영상 업로드 후 gif 파일 추출
				
				//실행을안해서_주석처리
				exec('sh /home/jhmush_cms/cms/scripts/cron/mp42ani.sh');
				//exit;
			
		}elseif( in_array($file_ext, array("pdf")) ){	## pdf 파일일경우
			##표지이미지 추출
	
				
			$path_info = pathinfo($file_path);
			//$captuer_image_path = $path_info['dirname'].'/'.$path_info['filename'].'_cover.png';
			$captuer_image_path = $path_info['dirname'].'/'.$file_name.'_cover.png';						
			exec('/usr/bin/convert "'.$file_path.'[0]" "'.$captuer_image_path.'" ');					
			
			$file_list[0]['file_type'] = 'file';
			$file_list[0]['original_name'] = $file['name'];
			$file_list[0]['re_name'] = $file_name;
			$file_list[0]['file_size'] = $file['size'];
			$file_list[0]['file_path'] = str_replace($this->module_root, '', $file_path);			
			
		}else { ## 일반파일일 경우
			
			$file_list[0]['file_type'] = 'file';
			$file_list[0]['original_name'] = $file['name'];
			$file_list[0]['re_name'] = $file_name;
			$file_list[0]['file_size'] = $file['size'];
			$file_list[0]['file_path'] = str_replace($this->module_root, '', $file_path);
		}
		
	if( $_SERVER['REMOTE_ADDR'] == "49.254.140.140" && $_SESSION['user_id'] == "jini0808"  ){
		//print_r($file_list);		exit;
	}			

		
		$db_data['board_id']   = $file_info['board_id'];
		$db_data['table_name'] = $file_info['table_name'];
		$db_data['manageCode']  = $file_info['manageCode'];
		$db_data['open']  	   = $file_info['open'];			
		$movie_idx = 0;
		$file_count = count($file_list);
		for($iter = 0 ; $iter < $file_count ; $iter++) {
			$buffer = $file_list[$iter];
			$db_data['file_type']     = $buffer['file_type'];
			$db_data['original_name'] = $buffer['original_name'];
			$db_data['re_name']       = $buffer['re_name'];
			$db_data['file_size']     = $buffer['file_size'];
			$db_data['file_path']     = $buffer['file_path'];
			$db_data['title_image']   = ($file['main'] == "true")?'y':'n';
			
			
			## 2017.03.13 서희진 : 이사님 지시로 position 값 넣기 ---- start
			$db_data['GPS_lon']       = $buffer['GPS_lon'];			
			$db_data['GPS_lat']       = $buffer['GPS_lat'];						
			//2014-12-16 황재복 : alt 넣도록
			$db_data['title']         = $file_info['title'];
			if(!empty($buffer['movie_capture'])) $db_data['movie_capture'] = $buffer['movie_capture'];
			if(!empty($movie_idx) && $buffer['movie_capture'] == 'y') $db_data['movie_idx'] = $movie_idx;
			if($buffer['file_type'] == 'movie') $db_data['movie_duration'] = $movie_duration;

// 2020-04-21 정운영 : 파일 암호화 시작 ==============================================================

		// file_encrypt		
			if($this->module_config['file_encrypt']=='true') {
				$db_data['pwd'] = call::generateStrongPassword('9');
				$source_file = $this->module_root.$db_data['file_path'];
				$target_file = $this->module_root.$db_data['file_path'].'.encrypt';
				$ret=shell_exec('/usr/bin/zip -P yubi'.$db_data['pwd'].'itid '.$target_file.' '.$source_file);

				$ret=shell_exec('/usr/bin/unzip -P yubi'.$db_data['pwd'].'itid -t '.$target_file);
				if(preg_match('/.*?(OK[\n]+No errors detected).*?/',$ret)) {
					shell_exec('rm -rf '.$source_file);
				} else {
					unset($db_data['pwd']);
				}
			}			
			
			/*원본 시작부분  */
			$query_fields = '';
			foreach($db_data as $field => $value) if(!empty($value)) $query_fields .= (empty($query_fields) ? '' : ', ').$field.' = "'.$value.'" ';
			$query = sprintf('INSERT INTO equipment_file_info SET %s', $query_fields);	
			$result = $mysql->query($query);



			$file_idx = $mysql->insert_id();
			if($buffer['file_type'] == 'movie') $movie_idx = $file_idx;
			/*원본 끝부분 */			
			
			##메인 이미지 처리 부분.
			if(  $file['main'] == "true" ){
				$query = sprintf('UPDATE %s SET mainimage_idx = "%s"  WHERE manageCode = "%s"', $this->module_config['table_name'], $file_idx, $file_info['manageCode']);	

				$result = $mysql->query($query);
			}
			

// 2020-04-21 정운영 : 파일 암호화 끝. ==============================================================			
		
			/*if(in_array($file_ext, $ext_allow_ebook)) {
				if($this->module_config['ebook_use']=='true') {
					$this->getPlugin('digitomi')->upload_before($db_data['table_idx'], $file_idx, $file['name']);
				}
			}*/
		}
		
		$this->clip_geocode('equipment_file_info', $file_idx);
		
		return true;
	}
	## 230717 장비DB 파일 권재영
	public function equipment_update_alt($file_info, $title) {
		global $mysql;

		$query = sprintf('UPDATE equipment_file_info SET `title` = "%s" WHERE `idx`="%s"', $title, $file_info['idx']);				
		
		$result = $mysql->query($query);
			
		return true;
	}
	
	## 230717 장비DB 파일 권재영
	public function equipment_update_alt_new($fileIdx, $title) {
		global $mysql;

		$query = sprintf('UPDATE equipment_file_info SET `title` = "%s" WHERE `idx`="%s"', $title, $fileIdx);		
		$result = $mysql->query($query);
			
		return true;
	}
	## 230717 장비DB 파일 권재영
	public function equipment_total_count($where, $table_name=NULL) {
		global $mysql;
		//2013-08-05 황재복 : 아래 문구 중 count(idx) 일 경우 join 했을 경우 두 테이블 다 idx 값 존해할 때 오류 생김. count(idx) => count(a.idx)

		$table_name = empty($table_name) ? $this->module_config['table_name'] : $table_name;
		
		$query  = sprintf('SELECT count(a.manageCode) as cnt FROM %s as a %s', $table_name, $where);

if( $_SESSION['user_id'] == "djkim" ){
//	echo $query;	exit;
}
		$data   = $mysql->query_fetch($query);
		return empty($data['cnt']) ? 0 : $data['cnt'];
	}
	
	## 파일정보 추출.
	public function attached_file_list($idx,$type="") {
		global $mysql;
		global $_SYSTEM;
		$data_file = $data = array();
		
		$query  = 'SELECT idx, file_type, original_name, re_name, file_path, file_size, download, title, description, tag, ebook_code, movie_duration, title_image, pwd, original_name as board_title ';
		$query .= 'FROM _file_info ';
		//$query .= 'WHERE table_name = "'.$this->module_config['table_name'].'" AND table_idx = "'.$idx.'" AND movie_capture <> "y" ';
		$query .= 'WHERE table_name = "'.$this->module_config['table_name'].'" AND table_idx = "'.$idx.'"';
		## 군수에게 바란다일떄는 file_type 이 approval 이 아닌애들만 가져온다.
		if( $this->module_config['skin_style'] == 'approval' || $this->module_config['skin_style'] ==='approval_ex') {
			$query .= '   AND  `file_type` <> "approval" ';
		}
		$query .= 'ORDER BY';
		if( $this->module_config['skin_style'] == 'thumb_photo' && $_SYSTEM['hostname'] == 'gongik' ){ //20200723 조지영 썸네일게시판 스킨에서 썸네일메인이미지 가장 하단 위치하도록 함(공익요청)
			$query .= ' title_image ASC, ';
		}
		$query .= ' file_type ASC, seq DESC, idx ASC';

		$result       = $mysql->query($query);
		while($data = $mysql->fetch_array($result)){
			$data['file_ext'] = strtolower($this->get_file_extension($data['re_name']));
			
			## 파일만 목록화 하고 싶을떄 20221005 서희진 추가 
			/*if( $type == "file" ){
				if( $data['file_type'] != "photo" )	{
					$data_file[] = $data;
				}
			}else{
				$data_file[] = $data;
			}*/
			
			$data_file[] = $data;
		}	

		return $data_file;
	}
	
	## 교육모듈 파일정보 추출
	public function attached_file_list_edu($idx) {
		global $mysql;
		$data_file = array();
		
		$query  = 'SELECT idx, file_type, original_name, re_name, file_path, file_size, download, title, description, tag, ebook_code, movie_duration, title_image, pwd ';
		$query .= 'FROM _file_info ';
		$query .= 'WHERE table_name = "'.$this->module_config['table_manager'].'" AND table_idx = "'.$idx.'" AND movie_capture <> "y" ';
		$query .= 'ORDER BY file_type ASC, seq DESC, idx ASC';
		
		$result       = $mysql->query($query);
		while($data_file[] = $mysql->fetch_array($result)) ;
		array_pop($data_file);

		return $data_file;
	}	
#################	
	

	public function attached_file_list_none($idx) {
		global $mysql;
		$data_file = array();
		
		$query  = 'SELECT idx, file_type, original_name, re_name, file_path, file_size, download, title, description, tag, ebook_code, movie_duration, title_image, pwd ';
		$query .= 'FROM _file_info ';
		$query .= 'WHERE table_name = "'.$this->module_config['table_name'].'" AND table_idx = "'.$idx.'" AND movie_capture <> "y" AND file_type <> "photo" ';
		$query .= 'ORDER BY file_type ASC, seq DESC, idx ASC';

		$result       = $mysql->query($query);
		while($data_file[] = $mysql->fetch_array($result)) ;
		array_pop($data_file);

		return $data_file;
	}
	
	## 파일정보 추출. - 사진
	public function attached_file_list_photo($idx, $table_name="yb_board") {
		global $mysql;
		$data_file = array();
		
		$query  = 'SELECT idx, file_type, original_name, re_name, file_path, file_size, download, title, description, tag, ebook_code, movie_duration, title_image, pwd ';
		$query .= 'FROM _file_info ';
		$query .= 'WHERE table_name = "'.$table_name.'" AND table_idx = "'.$idx.'" AND movie_capture <> "y" ';
		$query .= ' AND file_type = "photo"';		
		$query .= 'ORDER BY file_type ASC, seq DESC, idx ASC';
		
		$result       = $mysql->query($query);
		while($data_file[] = $mysql->fetch_array($result)) ;
		array_pop($data_file);

		return $data_file;
	}
	
	

	## 파일정보 추출. - 도장
	public function attached_file_list_stemp($idx) {
		global $mysql;
		$data_file = array();
		
		$query  = 'SELECT idx, file_type, original_name, re_name, file_path, file_size, download, title, description, tag, ebook_code, movie_duration, title_image, pwd ';
		$query .= 'FROM _file_info ';
		$query .= 'WHERE table_name = "'.$this->module_config['table_name'].'" AND table_idx = "'.$idx.'" AND movie_capture <> "y" ';
		$query .= ' AND title = "stemp"';				
		$query .= 'ORDER BY file_type ASC, seq DESC, idx ASC';

		$result       = $mysql->query($query);
		while($data_file[] = $mysql->fetch_array($result)) ;
		array_pop($data_file);

		return $data_file;
	}
		
	## 파일정보 추출. - 도장(아이디로 추출)
	public function attached_file_list_stemp_id($user_id) {
		global $mysql;
		$data_file = array();
		
		$query  = ' SELECT a.idx, a.table_name, a.table_idx, a.file_type, a.original_name, a.re_name, a.file_path, a.file_size, a.download, a.title, a.description, a.tag, a.ebook_code,a.movie_duration, a.title_image, a.pwd ';
		$query  .= ' FROM _file_info AS a  ';
		$query  .= ' RIGHT JOIN ';
		$query  .= ' ( ';
		$query  .= ' 	SELECT idx from _tour_interpreter_member ';
		$query  .= ' 	Where del = "n" AND member_id = "'.$user_id.'" ';
		$query  .= ' ) AS b ';
		$query  .= ' ON b.idx = a.table_idx ';
		$query  .= ' WHERE a.table_name="_tour_interpreter_member" and a.title = "stemp"  ';
		$query  .= ' ORDER BY a.file_type ASC, a.seq DESC, a.idx ASC ';

//		echo $query;		exit;

		$result       = $mysql->query($query);
		while($data_file[] = $mysql->fetch_array($result)) ;
		array_pop($data_file);

		return $data_file;
	}	
	
#################
	## 파일정보 추출.
	public function attached_file_list_approval($idx) {
		global $mysql;
		$data_file = array();
		
		$query  = 'SELECT idx, file_type, original_name, re_name, file_path, file_size ';
		$query .= 'FROM _file_info ';
		$query .= 'WHERE table_name = "'.$this->module_config['table_name'].'" AND table_idx = "'.$idx.'" AND file_type = "approval" ';
		$query .= 'ORDER BY seq DESC, idx ASC';

		$result       = $mysql->query($query);
		while($data_file[] = $mysql->fetch_array($result)) ;
		array_pop($data_file);

		return $data_file;
	}

	public function attached_file_list_in_table($idx, $table) {
		global $mysql;
		$data_file = array();
		
		$query  = 'SELECT idx, file_type, original_name, re_name, file_path, file_size, download, title, description, tag, ebook_code, movie_duration ';
		$query .= 'FROM _file_info ';
		$query .= 'WHERE table_name = "'.$table.'" AND table_idx = "'.$idx.'" AND movie_capture <> "y" ';
		$query .= 'ORDER BY file_type ASC, seq DESC, idx ASC';

		$result       = $mysql->query($query);
		while($data_file[] = $mysql->fetch_array($result)) ;
		array_pop($data_file);

		return $data_file;
	}
	
	## 파일정보 추출. - 사용안함
	public function attached_file_list_NOTUSE($idx) {
		global $mysql;
		$return = array();
		
		$query  = 'SELECT idx, file_type, original_name, re_name, file_path, file_size, download, title, description, tag ';
		$query .= 'FROM _file_info ';
		$query .= 'WHERE table_name = "'.$this->module_config['table_name'].'" AND table_idx = "'.$idx.'" ';
		$query .= 'ORDER BY file_type ASC, seq DESC, idx ASC';

		$result       = $mysql->query($query);
		while($data_file[] = $mysql->fetch_array($result)) ;
		array_pop($data_file);

		$file_list  = array();
		$photo_list = array();
		$movie_list = array();
		$link_list  = array();
		foreach($data_file as $key=>$value) ${$value['file_type'].'_list'}[] = $value;
		$return['file']  = $file_list;
		$return['photo'] = $photo_list;
		$return['movie'] = $movie_list;
		$return['link']  = $link_list;

		return $return;
	}
	
	
	
	## 임시적으로 만든것이다.
	## user management class가 완성되면 수정해야 한다.
	public static function get_user_info($user_pin) {
		global $mysql;
		$binary = new yb_crypt();
		$return = array();

		$query  = sprintf('SELECT phone, email, zipcode, address1, address2 FROM _member WHERE user_pin="%s"', $user_pin);
		$return   = $mysql->query_fetch($query);
		
		// 2012.05.21 오경우 추가
		foreach($return as $key=>$value) $return[$key] = $binary->decrypt($value);

		return $return;
	}
	
	public static function get_crm_info($myinfo) {
		## 2013.04.17 장재욱 추가 : crm 기능을 위해 추가
		global $mysql;

		if( !empty($myinfo['user_id']) && $myinfo['crm_member_type'] != 'none' ){
			$info = array();
			if( $myinfo['crm_member_type'] == 'company' ){
				$query  = 'SELECT idx, name, name2, kind FROM _crmpack_member_company WHERE login_ID="'.$myinfo['user_id'].'" ';
				$data = $mysql->query_fetch($query);
				$info['name']	= $data['name'].(!empty($data['name2'])?' '.$data['name2']:'');
				$info['kind']	= $data['kind'];
			}elseif( $myinfo['crm_member_type'] == 'customer' ){
				$query  = 'SELECT a.idx, a.name, a.department, a.position, b.name AS cName, b.name2 AS cName2, b.kind AS cKind ';
				$query .= 'FROM _crmpack_member_customer AS a ';
				$query .= 'LEFT JOIN _crmpack_member_company AS b ON b.idx = a.company_idx ';
				$query .= 'WHERE a.login_ID="'.$myinfo['user_id'].'" ';
				$data = $mysql->query_fetch($query);
				$info['name']		= $data['name'];
				$info['department']	= $data['department'];
				$info['position']	= $data['position'];
				$info['cname']		= $data['cName'].(!empty($data['cName2'])?' '.$data['cName2']:'');
				$info['ckind']		= $data['cKind'];
			}elseif( $myinfo['crm_member_type'] == 'staff' ){
				$query  = 'SELECT idx, name, department, position ';
				$query .= 'FROM _crmpack_member_staff ';
				$query .= 'WHERE login_ID="'.$myinfo['user_id'].'" ';
				echo $query;
				$data = $mysql->query_fetch($query);
				$info['name']		= $data['name'];
				$info['department']	= $data['department'];
				$info['position']	= $data['position'];
			}
			$info['type'] = $myinfo['crm_member_type'];
		}
		return $info;
	}
	
	public function check_value_in_list($file_name, $ext_list) {
		$arr_ext_list = is_array($ext_list) ? $ext_list : explode(',', $ext_list);

		$file_ext = strtolower($this->get_file_extension($file_name));
		if(in_array($file_ext, $arr_ext_list)) return true;
		else return false;
	}

	public static function get_file_extension($file) {
		$arr_temp = array();
		$arr_temp = explode(".",$file);
		return array_pop($arr_temp);
	}

	private static function make_folder($folder) {
		//if(!file_exists($folder)) return @mkdir($folder,0755);
		if(!file_exists($folder)) return mkdir($folder,0755);		
	}






	## ************************************************************************
	## $file_info = array('table_name' => 모듈테이블명, 
	##                    'table_idx'=> 게시물 idx,
	##                    'title' => 파일설명 (이미지 파일의 경우 alt테그에 사용));
	## $file = $_FILES;
	## ************************************************************************
	public function upload_photo($order, $file_info, $file, $resize=array('w'=>false, 'h'=>false)) {
		global $mysql;

		$file_ext = strtolower($this->get_file_extension($file['name']));
		$ext_allow_photo = empty($this->module_config['ext_photo_file'])  ? $this->ext_allow_photo : explode(',',$this->module_config['ext_photo_file']);
		if(!in_array($file_ext, $ext_allow_photo)) return false;


		## 디렉토리 검사해서 없으면 만들고
		$directory  = $this->module_root.'/_data/'.$this->module_config['package_id'];
		$this->make_folder($directory);
		if( !empty($dir_option) ){
			$directory .= '/'.$dir_option;
		}else{
			$directory .= '/'.$this->module_config['board_id'];
		}
		$this->make_folder($directory);

		## 파일이름 설정
		$file_pre_name = time();

		$iter = 0;
		do {
			$file_name = $file_pre_name.($iter > 0 ? '_'.$iter : '').'.'.$file_ext;
			$file_path = $directory.'/'.$file_name;
			$iter++;
		} while(file_exists($file_path) || file_exists($file_path.'.encrypt'));

		## file copy and db update
		if(move_uploaded_file($file['tmp_name'],$file_path) == false) return false;

		$file_list       = array();

		if($resize['w']!=false && $resize['h']!=false) {
			$temp_file_path = $directory.'/temp_'.$file_name;
			rename($file_path, $temp_file_path);
			$size = getimagesize($temp_file_path);
			if($size[0]>$resize['w']) { //사이즈를 변경후 비율에 맞춰서 Crop (일그러짐 방지)
				exec('/usr/bin/convert  -size '.$size[0].'x'.$size[1].' '.$temp_file_path.' -thumbnail '.$resize['w'].'x'.$resize['h'].'^ -gravity center -extent '.$resize['w'].'x'.$resize['h'].'  '.$file_path);
			} else {
				exec('cp -rf '.$temp_file_path.' '.$file_path);
			}
			unlink($temp_file_path);
		//} else if($file['size'] > 2000000) { 20160727 이미지화질향상 2M>10M 로 변경
		} else if($file['size'] > 10485760){
			$temp_file_path = $directory.'/temp_'.$file_name;
			rename($file_path, $temp_file_path);
			$resize = floor((1800000 / $file['size']) * 1000);
			exec('/usr/bin/convert  -resize '.$resize.'%x'.$resize.'% -quality 100 '.$temp_file_path.' '.$file_path);
			unlink($temp_file_path);
		}


		$db_data['file_type']     = 'photo';
		$db_data['original_name'] = $file['name'];
		$db_data['re_name']       = $file_name;
		$db_data['file_size']     = $file['size'];
		$db_data['file_path']     = str_replace($this->module_root, '', $file_path);
		$db_data['board_id']      = $file_info['board_id'];
		$db_data['table_name']    = $file_info['table_name'];
		$db_data['table_idx']     = $file_info['table_idx'];
		$db_data['seq']           = $order;

		$query_fields = '';
		foreach($db_data as $field => $value) if(!is_null($value)) $query_fields .= (empty($query_fields) ? '' : ', ').$field.' = "'.$value.'" ';
		$query = sprintf('INSERT INTO _file_info SET %s', $query_fields);
		$result = $mysql->query($query);


		if(in_array($file_ext, $this->ext_allow_ebook)) {
			if($this->module_config['ebook_use']=='true') {
				$this->getPlugin('digitomi')->upload_before($db_data['table_idx'], $file_idx, $file['name']);
			}
		}

		$this->clip_geocode('_file_info', $file_idx);
		
		return true;
	}





	## ************************************************************************
	## $file_info = array('table_name' => 모듈테이블명, 
	##                    'table_idx'=> 게시물 idx,
	##                    'title' => 파일설명 (이미지 파일의 경우 alt테그에 사용));
	## $file = $_FILES;
	## ************************************************************************
	//public function upload_file($file_info, $file, $ext_list, $dir_option='', $resize=array('w'=>false, 'h'=>false), $watermark=NULL) {
	public function upload_file($file_info, $file, $ext_list, $dir_option='', $resize=array('w'=>false, 'h'=>false), $watermark=NULL, $board_id=NULL) {
		$board_id = empty($board_id) ? $this->module_config['board_id'] : $board_id;
		
		
		global $mysql;
	
		set_time_limit(0);		

		## 업로드가 금지된 확장자 인지 체크한다. class.php의 null_check에서 선행처리하는데 다른곳에서 사용될 경우 누락(해킹)의 위험때문에 한번 더 넣었음.
		if($this->check_value_in_list($file['name'], $ext_list) == true) return false;

		## 디렉토리 검사해서 없으면 만들고
		$directory  = $this->module_root.'/_data/'.$this->module_config['package_id'];

		$this->make_folder($directory);
		
		
		if( !empty($dir_option) ){
			$directory .= '/'.$dir_option;
		}else{
			//$directory .= '/'.$this->module_config['board_id'];
			$directory .= '/'.$board_id;
		}



			
	
		$test = $this->make_folder($directory);
	
		## 파일이름 설정
		$file_ext      = strtolower($this->get_file_extension($file['name']));
		$file_pre_name = time();
		
		
		$iter = 0;
		do {
			$file_name = $file_pre_name.($iter > 0 ? '_'.$iter : '').'.'.$file_ext;
			$file_path = $directory.'/'.$file_name;
			$iter++;
		} while(file_exists($file_path) || file_exists($file_path.'.encrypt'));
		//} while(is_file($file_path));

			
					
		## file copy and db update
		if(move_uploaded_file($file['tmp_name'],$file_path) == false) return false;



		$ext_allow_photo = array();
		$ext_allow_movie = array();
		$ext_allow_ebook = array();
		$ext_allow_photo = empty($this->module_config['ext_photo_file'])  ? $this->ext_allow_photo : explode(',',$this->module_config['ext_photo_file']);
		$ext_allow_movie = empty($this->module_config['ext_movie_file'])  ? $this->ext_allow_movie : explode(',',$this->module_config['ext_movie_file']);
		$ext_allow_ebook = empty($this->module_config['ext_ebook_file'])  ? $this->ext_allow_ebook : explode(',',$this->module_config['ext_ebook_file']);
		$file_list       = array();



		
		
		if(in_array($file_ext, $ext_allow_photo)) { ## 이미지 파일일 경우
			if($resize['w']!=false && $resize['h']!=false) {				
				$temp_file_path = $directory.'/temp_'.$file_name;
		
				
			
				rename($file_path, $temp_file_path);					
				$size = getimagesize($temp_file_path);				
				
				if($size[0]>$resize['w']) { //사이즈를 변경후 비율에 맞춰서 Crop (일그러짐 방지)
					exec('/usr/bin/convert  -size '.$size[0].'x'.$size[1].' '.$temp_file_path.' -thumbnail '.$resize['w'].'x'.$resize['h'].'^ -gravity center -extent '.$resize['w'].'x'.$resize['h'].'  '.$file_path);
	
				} else {
					exec('cp -rf '.$temp_file_path.' '.$file_path);
				}				
				unlink($temp_file_path);			
if($_SERVER['REMOTE_ADDR'] == '49.254.140.140'  && $_SESSION['user_id'] == 'siha1997'){
 #   echo '<pre>'; print_r($temp_file_path); exit;
}
			} else if($file['size'] > 10485760) {
				$temp_file_path = $directory.'/temp_'.$file_name;
				rename($file_path, $temp_file_path);
				$resize = floor((9000000 / $file['size']) * 200);
				exec('/usr/bin/convert  -resize '.$resize.'%x'.$resize.'% -quality 100 '.$temp_file_path.' '.$file_path);
				unlink($temp_file_path);
			}
				

		
			if(!is_null($watermark) && is_file($watermark)) exec('/usr/bin/composite -watermark 30% -gravity SouthEast '.$watermark.' '.$file_path.' '.$file_path);

			$file_list[0]['file_type'] = 'photo';
			$file_list[0]['original_name'] = $file['name'];
			$file_list[0]['re_name'] = $file_name;
			$file_list[0]['file_size'] = $file['size'];
			$file_list[0]['file_path'] = str_replace($this->module_root, '', $file_path);
			$file_list[0]['open'] = $file['open'];
			$file_list[0]['main'] = $file['main'];
			## 2017.03.13 서희진 : 이사님 지시로 position 값 넣기 ---- start
			## 7.2 이상 버전용으로 변경되어야함. 잠시 막아둠.
			//$exif_data = exif_read_data($file_path, 'IFD0');			
			//$fp = fopen($file_path, 'rb');
			//$headers = exif_read_data($fp);print_r( $headers )	;exit;			
			
			
			if( $exif_data["GPSLongitude"] && $exif_data["GPSLatitude"] ){
				$file_list[0]['GPS_lon'] = $this->gps($exif_data["GPSLongitude"], $exif_data['GPSLongitudeRef']);				
				$file_list[0]['GPS_lat'] = $this->gps($exif_data["GPSLatitude"], $exif_data['GPSLatitudeRef']);								
				
						
			## 2017.03.13 서희진 : 이사님 지시로 position 값 넣기 ---- end
			}

			$file_list[0]['open'] = $file['open'];				
		} elseif(in_array($file_ext, $ext_allow_movie)) { ## 동영상 파일일 경우			
			## flv convert
			$ffmpegCommand 		= '/usr/bin/ffmpeg'; // x264, xbix, ora, gsm, lame, faac, swscale, 0.5
			$qt_faststart 		= '/usr/bin/qt-faststart';
			/*
			#yum install svn
			#svn checkout svn://svn.ffmpeg.org/ffmpeg/trunk ffmpeg
			#cd ffmpeg
			#./configure
			#make
			#make tools/qt-faststart
			#cp -a tools/qt-faststart /usr/bin/
			
			*/
			
			
			$new_ext = '.mp4';
			if(extension_loaded('ffmpeg') == true) {
				set_time_limit(0); ## php excution time increase;


				$path_info = pathinfo($file_path);
				$movie = new ffmpeg_movie($file_path);
				$capture_count = 5;
				$total_frame = $movie->getFrameCount();
				$movie_duration = $movie->getDuration();


				$frame_interval = $capture_count > 0 ? floor($total_frame / $capture_count) : 0;

				## capture
				for($iter = 0; $iter <= $capture_count ; $iter++ ) {
					$frame = $movie->getFrame($iter * $frame_interval + 1);
					if($frame) {
						$gd_image = $frame->toGDImage();
						$captuer_image_tmp_path = $path_info['dirname'].'/'.$path_info['filename'].'_temp_'.$iter.'.png';
						$captuer_image_path = $path_info['dirname'].'/'.$path_info['filename'].'_thumb_'.$iter.'.png';
						
						header("Content-Type: image/png");
						imagepng($gd_image, $captuer_image_tmp_path);
						imagedestroy($gd_image);
						//$thumb_size = ($iter == 0) ? '640x480' : '80x60';
						$thumb_size =  '640x480';
						
						exec('/usr/bin/convert -resize '.$thumb_size.' '.$captuer_image_tmp_path.' '.$captuer_image_path);						
//						unlink($captuer_image_tmp_path);

						$file_list[$iter+1]['file_type'] = 'photo';
						$file_list[$iter+1]['original_name'] = $path_info['filename'].'_thumb_'.$iter.'.png';
						$file_list[$iter+1]['re_name'] = $path_info['filename'].'_thumb_'.$iter.'.png';
						$file_list[$iter+1]['file_size'] = filesize($captuer_image_path);
						$file_list[$iter+1]['file_path'] = str_replace($this->module_root, '', $captuer_image_path);
						$file_list[$iter+1]['movie_capture'] = 'y';
					}
				}
			} else {
				$path_info = pathinfo($file_path);
				$captuer_image_tmp_path = $path_info['dirname'].'/'.$path_info['filename'].'_temp_'.$iter.'.png';
				$captuer_image_path = $path_info['dirname'].'/'.$path_info['filename'].'_thumb_'.$iter.'.png';
				exec($ffmpegCommand.' -ss 00:00:03.01 -i '.$file_path.' -y -f image2 -vcodec mjpeg -vframes 1 '.$captuer_image_tmp_path);
			    
				$thumb_size =  '640x480';
				exec('/usr/bin/convert -resize '.$thumb_size.' '.$captuer_image_tmp_path.' '.$captuer_image_path);
				
				$file_list[1]['file_type'] = 'photo';
				$file_list[1]['original_name'] = $path_info['filename'].'_thumb_'.$iter.'.png';
				$file_list[1]['re_name'] = $path_info['filename'].'_thumb_'.$iter.'.png';
				$file_list[1]['file_size'] = filesize($captuer_image_path);
				$file_list[1]['file_path'] = str_replace($this->module_root, '', $captuer_image_path);
				$file_list[1]['movie_capture'] = 'y';
				
			}
				
				if($file_info['use_streaming'] == 'true') { ## mp4 변환
					$new_ext = '.mp4';
					$allowedMimeTypes = array('video/avi','video/mp4','video/mpeg','video/quicktime','video/x-msvideo','video/msvideo','video/x-ms-wmv');
				
				
					// Video
					$videoSize 			= isset($_POST['video_size']) 					? $_POST['video_size'] 		: '640x360';
					$videoBitrate 		= isset($_POST['video_bitrate'])				? (int)$_POST['video_bitrate'] 	: '700';
					$videoFramerate		= isset($_POST['video_framerate'])				? (int)$_POST['video_framerate'] : '30';
					$videoDeinterlace	= isset($_POST['encoding_video_deinterlace'])	? 1 : 0 ;
					$videoAspect 		= isset($_POST['video_aspect']) 				? $_POST['video_aspect'] 		: 0; //-aspect $videoAspect
					
				
if( $_SERVER['REMOTE_ADDR'] == "49.254.140.140" ){
 //print_r($_POST);exit;
}					
					// Adudio
					$audioEnabled		= isset($_POST['encoding_enable_audio'])		? 0 : 1 ;
					$audioSamplerate	= isset($_POST['encoding_audio_sampling_rate'])	? (int)$_POST['encoding_audio_sampling_rate'] : '44100';
					$audioBitrate		= isset($_POST['encoding_audio_bitrate'])		? (int)$_POST['encoding_audio_bitrate'] : '128';
					$audioChannels		= (isset($_POST['encoding_audio_channels']) && $_POST['encoding_audio_channels']	== 'stereo')	? 2 : 1 ;
				
					// Build up the ffmpeg params from the values posted from the html form
					$customParams  = ' -s '.$videoSize; 				// Format the video size
					$customParams .= ' -b '.$videoBitrate.'k'; 		// Format the video bit rate  -- -vb
					$customParams .= ' -r '.$videoFramerate;			// Format the video frame rate
					if ($videoAspect) {
						$customParams .= ' -aspect '.$videoAspect;		// aspect ratio 
					}
					if ($videoDeinterlace) {
						$customParams .= ' -deinterlace ';				// Deinterlace the video
					}
					if ($audioEnabled) {
						$customParams .= ' -ar '.$audioSamplerate;		// Audio sample rate
						$customParams .= ' -ab '.$audioBitrate.'k';		// Audio bit rate
						$customParams .= ' -ac '.$audioChannels;		// Audio Channels
					} else {
						$customParams .= ' -an '; 						// Disable audio
					}
					$customParams .= ' -y ';							// Overwrite existing file
					
					
					$file_path_mp4 = $path_info['dirname'].'/'.$path_info['filename'].'_new.mp4';
				
					$command = $ffmpegCommand.' -i '.$file_path.' -vcodec libx264  -vpre superfast -vpre baseline -vsync 1  '; //  -bt 50k
					if ($audioEnabled) {
						$command = $command.' -acodec libfaac ';
					}
					$command = $command.$customParams.' -threads 12 '.$file_path_mp4.' 2>&1';
					///ffmpeg -i /home/encode/data/file.avi -threads 0 -vcodec libx264 -b 800k -r 24 -s 640x360 -vpre superfast -vpre baseline -acodec libfaac -ac 2 -ab 192k -ar 48000 -y /home/encode/mp4/file.mp4 	
					
					exec($command);
					exec($qt_faststart.' '.$file_path_mp4.' '.$file_path_mp4.' 2>&1');
				
					//exec($ffmpegCommand.' -i '.$file_path.' -threads 16 -b 604k -ac 1 -ar 44100 -coder 1 -flags +loop -cmp +chroma -partitions +parti4x4+partp8x8+partb8x8 -subq 5 -g 250 -keyint_min 25 -sc_threshold 40 -i_qfactor 0.71 '.$file_path_mp4);
					
					//if($file_path_mp4!=$file_path) 
					//unlink($file_path);
					exec('mv '.$file_path_mp4.' '.$file_path);
					//$file_path = $file_path_mp4;
					$file['size'] = filesize($file_path);
				} else {
					/*
					if($file_ext != 'flv') { ## flv 변환
						$new_ext = '.flv';
						$m_bitrate = $movie->getBitRate();
						$m_bitrate = ceil( $m_bitrate / 1000 );
						$file_path_flv = $path_info['dirname'].'/'.$path_info['filename'].'.flv';
						exec('/usr/local/bin/ffmpeg -i '.$file_path.' -ar 44100 -f flv -ab '.$m_bitrate.' -s '.$movie->getFrameWidth().'x'.$movie->getFrameHeight().' '.$file_path_flv);
						unlink($file_path);
						$file_path = $file_path_flv;
						$file['size'] = filesize($file_path);
					}
					*/
					
				}





				$file_list[0]['file_type'] = 'movie';
				//$file_list[0]['original_name'] = str_replace($this->get_file_extension($file['name']), 'flv', $file['name']);
				$file_list[0]['original_name'] = $file['name'];
				$file_list[0]['re_name'] = $path_info['filename'].$new_ext;
				$file_list[0]['file_size'] = $file['size'];
				$file_list[0]['file_path'] = str_replace($this->module_root, '', $file_path);
				
				//2016-11-22 황재복 : 동영상 업로드 후 gif 파일 추출
				
				//실행을안해서_주석처리
				exec('sh /data/ys_cms/cms/scripts/cron/mp42ani.sh');
				//exit;
			
		}elseif( in_array($file_ext, array("pdf")) ){	## pdf 파일일경우
			##표지이미지 추출
	
				
			$path_info = pathinfo($file_path);
			//$captuer_image_path = $path_info['dirname'].'/'.$path_info['filename'].'_cover.png';
			$captuer_image_path = $path_info['dirname'].'/'.$file_name.'_cover.png';						
			exec('/usr/bin/convert "'.$file_path.'[0]" "'.$captuer_image_path.'" ');					
			
			$file_list[0]['file_type'] = 'file';
			$file_list[0]['original_name'] = $file['name'];
			$file_list[0]['re_name'] = $file_name;
			$file_list[0]['file_size'] = $file['size'];
			$file_list[0]['file_path'] = str_replace($this->module_root, '', $file_path);			
			
		}else { ## 일반파일일 경우
			
			$file_list[0]['file_type'] = 'file';
			$file_list[0]['original_name'] = $file['name'];
			$file_list[0]['re_name'] = $file_name;
			$file_list[0]['file_size'] = $file['size'];
			$file_list[0]['file_path'] = str_replace($this->module_root, '', $file_path);
		}
		
	if( $_SERVER['REMOTE_ADDR'] == "49.254.140.140" && $_SESSION['user_id'] == "jini0808"  ){
		//print_r($file_list);		exit;
	}			

		
		$db_data['board_id']   = $file_info['board_id'];
		$db_data['table_name'] = $file_info['table_name'];
		$db_data['table_idx']  = $file_info['table_idx'];
		$db_data['open']  	   = $file_info['open'];			
		$movie_idx = 0;
		$file_count = count($file_list);
		for($iter = 0 ; $iter < $file_count ; $iter++) {
			$buffer = $file_list[$iter];
			$db_data['file_type']     = $buffer['file_type'];
			$db_data['original_name'] = $buffer['original_name'];
			$db_data['re_name']       = $buffer['re_name'];
			$db_data['file_size']     = $buffer['file_size'];
			$db_data['file_path']     = $buffer['file_path'];
			$db_data['title_image']   = ($file['main'] == "true")?'y':'n';
			
			
			## 2017.03.13 서희진 : 이사님 지시로 position 값 넣기 ---- start
			$db_data['GPS_lon']       = $buffer['GPS_lon'];			
			$db_data['GPS_lat']       = $buffer['GPS_lat'];						
			//2014-12-16 황재복 : alt 넣도록
			$db_data['title']         = $file_info['title'];
			if(!empty($buffer['movie_capture'])) $db_data['movie_capture'] = $buffer['movie_capture'];
			if(!empty($movie_idx) && $buffer['movie_capture'] == 'y') $db_data['movie_idx'] = $movie_idx;
			if($buffer['file_type'] == 'movie') $db_data['movie_duration'] = $movie_duration;

// 2020-04-21 정운영 : 파일 암호화 시작 ==============================================================

		// file_encrypt		
			if($this->module_config['file_encrypt']=='true') {
				$db_data['pwd'] = call::generateStrongPassword('9');
				$source_file = $this->module_root.$db_data['file_path'];
				$target_file = $this->module_root.$db_data['file_path'].'.encrypt';
				$ret=shell_exec('/usr/bin/zip -P yubi'.$db_data['pwd'].'itid '.$target_file.' '.$source_file);

				$ret=shell_exec('/usr/bin/unzip -P yubi'.$db_data['pwd'].'itid -t '.$target_file);
				if(preg_match('/.*?(OK[\n]+No errors detected).*?/',$ret)) {
					shell_exec('rm -rf '.$source_file);
				} else {
					unset($db_data['pwd']);
				}
			}			
			
			/*원본 시작부분  */
			$query_fields = '';
			foreach($db_data as $field => $value) if(!empty($value)) $query_fields .= (empty($query_fields) ? '' : ', ').$field.' = "'.$value.'" ';
			$query = sprintf('INSERT INTO _file_info SET %s', $query_fields);	
			$result = $mysql->query($query);



			$file_idx = $mysql->insert_id();
			if($buffer['file_type'] == 'movie') $movie_idx = $file_idx;
			/*원본 끝부분 */			
			
			##메인 이미지 처리 부분.
			if(  $file['main'] == "true" ){
				$query = sprintf('UPDATE %s SET mainimage_idx = "%s"  WHERE idx = %s', $this->module_config['table_name'], $file_idx, $file_info['table_idx']);	
				$result = $mysql->query($query);
			}
			
			if($this->module_config['file_encrypt']=='true') {
				$mysql2 = new mysql_class_user(0,0,0,0);
				$mysql2->connect('FILE_ENC_KEY', __HOST, __USER, __PASSWORD);	
				$db_data['idx'] = $file_idx;
				$query_fields = '';
				foreach($db_data as $field => $value) if(!empty($value)) $query_fields .= (empty($query_fields) ? '' : ', ').$field.' = "'.$value.'" ';
				$query = sprintf('INSERT INTO _file_info SET %s', $query_fields);
				$result = $mysql2->query($query);
				unset($db_data['idx']);
				$mysql = new mysql_class_user(0,0,0,0);
				$mysql->connect(__DATABASE, __HOST, __USER, __PASSWORD);
			}
// 2020-04-21 정운영 : 파일 암호화 끝. ==============================================================			
		
			/*if(in_array($file_ext, $ext_allow_ebook)) {
				if($this->module_config['ebook_use']=='true') {
					$this->getPlugin('digitomi')->upload_before($db_data['table_idx'], $file_idx, $file['name']);
				}
			}*/
		}
		
		$this->clip_geocode('_file_info', $file_idx);
		
		return true;
	}

	//2014-12-16 황재복 : alt 넣도록
	public function update_alt($file_info, $title) {
		global $mysql;

		$query = sprintf('UPDATE _file_info SET `title` = "%s" WHERE `idx`="%s"', $title, $file_info['idx']);				
		
		$result = $mysql->query($query);
			
		return true;
	}
	
	//2014-12-16 황재복 : alt 넣도록
	public function update_alt_new($fileIdx, $title) {
		global $mysql;

		$query = sprintf('UPDATE _file_info SET `title` = "%s" WHERE `idx`="%s"', $title, $fileIdx);		
		$result = $mysql->query($query);
			
		return true;
	}

	public function upload_file_NOTUSE($file_info, $file, $ext_list) {
		global $mysql;

		## 업로드가 금지된 확장자 인지 체크한다. class.php의 null_check에서 선행처리하는데 다른곳에서 사용될 경우 누락(해킹)의 위험때문에 한번 더 넣었음.
		if($this->check_value_in_list($file['name'], $ext_list) == true) return false;

		## 디렉토리 검사해서 없으면 만들고
		$directory  = $this->module_root.'/_data/'.$this->module_config['package_id'];
		$this->make_folder($directory);
		$directory  = $directory.'/'.$this->module_config['board_id'];
		$this->make_folder($directory);

		## 파일이름 설정
		$file_ext      = strtolower($this->get_file_extension($file['name']));
		$file_pre_name = time();

		for($iter=0 ; $iter<100 ; $iter++) {
			$file_name = $file_pre_name.($iter > 0 ? '_'.$iter : '').'.'.$file_ext;
			$file_path = $directory.'/'.$file_name;
			if(!is_file($file_path)) break;
		}

		## file copy and db update
		if(move_uploaded_file($file['tmp_name'],$file_path)) {
			$file_path = str_replace($this->module_root, '', $file_path);

			$db_data = array();
			$db_data['file_type']     = $file_info['file_type'];
			$db_data['table_name']    = $file_info['table_name'];
			$db_data['table_idx']     = $file_info['table_idx'];
			$db_data['original_name'] = $file['name'];
			$db_data['re_name']       = $file_name;
			$db_data['file_size']     = $file['size'];
			$db_data['file_path']     = $file_path;
			$db_data['title']         = $file_info['title'];

			$query_fields = '';
			foreach($db_data as $field => $value) if(!empty($value)) $query_fields .= (empty($query_fields) ? '' : ', ').$field.' = "'.$value.'" ';
			$query = sprintf('INSERT INTO _file_info SET %s', $query_fields);
			$result = $mysql->query($query);
			return $result;
		}
		return false;
	}


	## ************************************************************************
	## _file_info에 종속되지 않고 단순히 파일을 업로드 시킨다.
	## 유의사항
	##    1. 업로드가 금지된 확장자 체크를 하지 않는다. 
	##       simple_file_upload를 call하는 부분에서 확장자 체크를 반드시 해야한다.
	## 파라미터
	##    $file : $_FILES
	##    $path : 파일업로드 위치
	## ************************************************************************

	public function simple_file_upload($file=array(), $path) {
	
		## 디렉토리 검사해서 없으면 만든다.
		$this->make_folder($path);
		
		## 파일이름 설정
		$file_ext      = strtolower($this->get_file_extension($file['name']));
		$file_pre_name = time();
	
		for($iter=0 ; $iter<100 ; $iter++) {
			$file_name = $file_pre_name.($iter > 0 ? '_'.$iter : '').'.'.$file_ext;
			$file_path = $path.'/'.$file_name;
			if(!is_file($file_path)) break;
		}

		## file copy
		if(move_uploaded_file($file['tmp_name'],$file_path)) return $file_path;
		else return false;
	}

	public function remove_upload_file($idx, $table_name, $table_idx) {
		global $mysql;
		$data = array();

		if(empty($idx)) return false;

		$query = sprintf('SELECT idx, file_path FROM _file_info WHERE table_name = "%s" AND table_idx = "%s" AND (idx = "%s" OR (movie_idx = "%s" AND movie_capture = "y"))', $table_name, $table_idx, $idx, $idx);

		$file_path = array();
		$result = $mysql->query($query);
		for($iter=0 ; $buffer = $mysql->fetch_array($result) ; $iter++) $file_path[$iter] = $buffer['file_path'];

		$query = sprintf('DELETE FROM _file_info WHERE idx = "%s" OR (movie_idx = "%s" AND movie_capture = "y")', $idx, $idx);
		$result = $mysql->query($query);
		if($result == false) return false;

		foreach($file_path as $value) {
			$remove_path  = $this->module_root.$value;
			if(is_file($remove_path)) unlink($remove_path);
		}

		return true;
	}

	public function remove_upload_file_NOTUSE($idx, $table_name, $table_idx) {
		global $mysql;
		$data = array();

		$query = sprintf('SELECT idx, file_path FROM _file_info WHERE table_name = "%s" AND table_idx = "%s" AND idx = "%s"', $table_name, $table_idx, $idx);
		$data  = $mysql->query_fetch($query);

		if(!empty($data['idx'])) {
			$query = sprintf('DELETE FROM _file_info WHERE idx = "%s" ', $data['idx']);
			$result = $mysql->query($query);
			if($result == false) return false;

			$file_path  = $this->module_root.$data['file_path'];
			if(is_file($file_path)) unlink($file_path);
		}
		return true;
	}

	public function remove_upload_file_all($table_name, $table_idx) {
		global $mysql;

		$data   = array();

		if(empty($table_name) || empty($table_idx)) return false;

		$query  = sprintf('SELECT idx, file_path FROM _file_info WHERE table_name = "%s" AND table_idx = "%s"', $table_name, $table_idx);
		$result = $mysql->query($query);

		## unlink file.
		while($data = $mysql->fetch_array($result)) {
			$file_path  = $this->module_root.$data['file_path'];
			if(is_file($file_path)) unlink($file_path);
		}

		## delete db.
		$query = sprintf('DELETE FROM _file_info WHERE table_name = "%s" AND table_idx = "%s"', $table_name, $table_idx);
		$result = $mysql->query($query);
		if($result == false) return false;
		else return true;
	}

	public function make_GET_parameter($parameter=array(), $amp='&amp;', $xss=false) {
		$return = '';
		if(!empty($parameter)) {
			foreach($parameter as $name => $value) {
				if($xss == true)	$value = call::SafeFilterSTR($value);
				if(!empty($value)) $return .= (empty($return) ? '' : $amp).$name.'='.rawurlencode($value);
			}
		}
		return $return;
	}

	## 유의사항
	##     $_GET과 $_POST에 섞여서 들어오는 파라미터에만 사용할 것.
	##     무조건 모든 파라메타럴 get_parameter를 사용하면 안된다. : 해킹에 취약하게 된다.
	##     get_parameter를 사용하는것과 사용하지 않는것을 확실하게 구분해서 쓴다.
	public function get_parameter($str) {
		if(!isset($_POST[$str])) $_POST[$str]='';
		
										
		$return = empty($_GET[$str]) ? $_POST[$str] : rawurldecode($_GET[$str]);
		return empty($return) ? NULL : $return;
	}

	public function get_board_keycode($length=5) {
		return substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, $length);
	}
	
	public function make_search_list($arr_list = array()) {
		$return   = array();

		if(!empty($arr_list) && is_array($arr_list)) {
			$temp = array();
			foreach($arr_list as $value) {
				if(!empty($value)) {
					$temp = explode('|', $value);
					$return[] = array('type'=>$temp[1], 'value'=>$temp[0]);
				}
			}
		} else { ## 모듈의 config 생성시 아래 내용이 자동으로 생성되면 필요없게 된다. 그때는 삭제해도 된다.
			$return[] = array('type' => '제목'  , 'value' => 'title');
			$return[] = array('type' => '내용'  , 'value' => 'contents');
			$return[] = array('type' => '등록자'  , 'value' => 'reg_name');
			$return[] = array('type' => '아이디', 'value' => 'reg_id');
		}
		return $return;
	}
	
	public function get_board_config($board_id, $module_path = NULL) {
		global $_SYSTEM;
		
		if(empty($module_path)) $config_file = $this->module_config['module_path'].'/config/cfg_'.$board_id.'.txt';
		
		
		else $config_file = $this->module_root.$module_path.'/config/cfg_'.$board_id.'.txt';

		if(!is_file($config_file)) return false;
		else return unserialize(file_get_contents($config_file));
		//else return ArrayToXML::toArray(file_get_contents($config_file));
	}
	

	public function sms_send($recv_list, $sender, $mesg, $send_time = NULL, $safe_time = false) {
		/* recv_list : 수신번호, 010-1111-1111, 또는 01011111111 형식을 반드시 지켜야 한다.
					   하나일때 : 010-1111-1111, 또는 01011111111
					   복수일때 : ###-####-####,###-####-####,###-####-####,###-####-#### 또는 ###########,##########,##########,##########
		   sender    : 발신번호, 010-1111-1111, 또는 01011111111 형식을 반드시 지켜야 한다.
		   mesg      : sms 메시지.
		   send_time : 예약발송 시간, YmdHis 형식을 반드시 지켜야함. NULL 일 경우 즉시 발송
		   safe_time : true / false
		               true  - 오후 6시 이후일 경우 다음날 9시에 sms 발송.
					   false - 즉시 발송.
		*/

		$sender = '0542325630';
		$arr_param = array('receiver'     => $recv_list,
						   'sender'       => $sender,
						   'sms_mesg'     => $mesg,
						   'reserve_time' => $send_time,
						   'safe_time'    => $safe_time
						   );

		// print_r($arr_param);
		// exit;

		call::get_api('sms',$arr_param);
	}
	
	## 서희진 2017.07.04 추가 : 인기글
	public function hit_article($data, $where, $limit=NULL) {
		global $mysql;
		
		$return = array();
		
		$query  = '';
		$query .= '	SELECT tbl1.* , tbl2.* FROM ';		
		$query .= '  ( ';		
		$query .= ' 	SELECT a.*, b.hit_cnt, c.commend_cnt FROM ';
		$query .= '  	( ';
		$query .= '	   		SELECT * ';
		$query .= '	   		FROM '.$data['table_name'].' ';
		$query .= '	   		WHERE del = "n" /* AND board_id = "'.$data['board_id'].'" */ '.$where;		
		$query .= '  	) AS a ';
		$query .= ' 	LEFT OUTER JOIN ';
		$query .= '		( SELECT table_idx AS hit_idx, visit_cnt AS hit_cnt FROM _recommend_info WHERE table_name="'.$data['table_name'].'" ) AS b';
		$query .= ' 	ON a.idx = b.hit_idx ';
		$query .= ' 	LEFT OUTER JOIN ';
		$query .= '		( SELECT table_idx AS commend_idx, COUNT(idx) AS commend_cnt FROM yb_comment WHERE table_name="'.$data['table_name'].'" GROUP BY commend_idx ) AS c';
		$query .= ' 	ON a.idx = c.commend_idx ';		
		$query .= '  	ORDER BY  b.hit_cnt DESC ';		
		$query .= '  	limit 0, '.$limit;
		$query .= '  ) AS tbl1 ';		
		$query .= ' LEFT JOIN ';
		$query .= '	( SELECT table_idx AS file_idx, file_type, original_name, re_name, file_path, file_size, board_id AS file_board_id  FROM _file_info WHERE table_name="'.$data['table_name'].'" /* AND  board_id = "'.$data['board_id'].'" */ GROUP BY file_idx ) AS tbl2';
		$query .= ' ON tbl1.idx = tbl2.file_idx ';		

		$result = $mysql->query($query);
		for($iter=0 ; $buffer = $mysql->fetch_array($result) ; $iter++) {
			$return[$iter]       = $buffer;
		}		
		return $return;
	}	
	
	## 비즈 리뷰 
	## 댓글사용	
	public function replay($data, $limit=NULL) {
		if($data['se_mode'] == 'replay_save') $this->replay_save($data);
		if($data['se_mode'] == 'replay_remove') $this->replay_remove($data);
		if($data['se_mode'] == 'replay_report') $this->replay_report($data);
		
		return $this->replay_lineup($data, $limit);
	}		
	
	

	## 댓글사용
	## 서희진 2015.10.07 추가 : 인자값 $limit=NULL을 댓글 페이징을 위해서 추가	
	public function comment($data, $limit=NULL, $type=NULL) {
		
		if( $type == "petition"){
			if($data['se_mode'] == 'comment_save') $this->comment_save_petition($data);
			if($data['se_mode'] == 'comment_remove') $this->comment_remove_petition($data);
			if($data['se_mode'] == 'comment_report') $this->comment_report_petition($data);
			return $this->comment_lineup_petition($data, $limit);	
		}else{
			if($data['se_mode'] == 'comment_save') $this->comment_save($data);
			if($data['se_mode'] == 'comment_remove') $this->comment_remove($data);
			if($data['se_mode'] == 'comment_report') $this->comment_report($data);
			return $this->comment_lineup($data, $limit);
		}
		
	}

	## 댓글 목록  - 청원게시판
	## 서희진 2015.10.07 추가 : 인자값 $limit=NULL을 댓글 페이징을 위해서 추가
	public function comment_lineup_petition($data, $limit=NULL) {
		global $mysql;
		
		$return = array();
		$return['comment_type'] = $data['comment_type'];

		
		if(empty($limit))	## 페이징일떄
		$query = sprintf('SELECT idx, contents, point, reg_pin, reg_name, reg_date, sns_type, profile_img FROM yb_comment_petition WHERE table_name = "%s" AND table_idx = "%s" ORDER BY reg_date DESC', $data['table_name'], $data['table_idx']);
		else
		$query = sprintf('SELECT idx, contents, point, reg_pin, reg_name, reg_date, sns_type, profile_img FROM yb_comment_petition WHERE table_name = "%s" AND table_idx = "%s" ORDER BY reg_date DESC '.$limit, $data['table_name'], $data['table_idx']);

		$result = $mysql->query($query);
		$point_negative = 0;
		$point_positive = 0;
		$point_question = 0;
		$point = 0;
		for($iter=0 ; $buffer = $mysql->fetch_array($result) ; $iter++) {
			$return['list'][$iter]['idx']         = $buffer['idx'];
			$return['list'][$iter]['contents']    = $buffer['contents'];
			$return['list'][$iter]['reg_name']    = $buffer['reg_name'];
			## 이름 숨김 설정 및 내글 수정 삭제.----------- 20190807 서희진.		
			if($this->permission['admin'] != true && $this->module_config['use_hide_name_all'] == true) $return['list'][$iter]['reg_name'] = call::strcut($buffer['reg_name'], 1, 'OO');
			else $return['list'][$iter]['reg_name']    = $buffer['reg_name'];
			if( $buffer['reg_pin'] == $this->myinfo['my_pin'] && !empty($this->myinfo['my_pin'])) $return['list'][$iter]['reg_name'] = $buffer['reg_name'];
			## 이름 숨김 설정 및 내글 수정 삭제. end
			
			$return['list'][$iter]['reg_pin']     = $buffer['reg_pin'];
			$return['list'][$iter]['reg_date']    = $buffer['reg_date'];
			$return['list'][$iter]['sns_type']    = $buffer['sns_type'];
			$return['list'][$iter]['profile_img'] = $buffer['profile_img'];
			//if($data['comment_type'] == 'recommend') {
			//	if($buffer['point'] > 0) $point_positive++;
			//	if($buffer['point'] < 0) $point_negative++;
			//	if($buffer['point'] == 0) $point_question++;
			//} else if($data['comment_type'] == 'point') {
			//	$point += $buffer['point'];
			//} else if($data['comment_type'] == 'biz') {
			//	$return['list'][$iter]['point'] = $buffer['point'];
			//}
			$return['list'][$iter]['point'] = $buffer['point'];		
		}
		$return['point_positive'] = $this->list_total_count(' WHERE table_idx = "'.$data['table_idx'].'" AND point > 0 ','yb_comment_petition');
		$return['point_negative'] = $this->list_total_count(' WHERE table_idx = "'.$data['table_idx'].'" AND point < 0','yb_comment_petition');
		$return['point_question'] = $this->list_total_count(' WHERE table_idx = "'.$data['table_idx'].'" AND point = 0','yb_comment_petition');
		$return['point'] = $point;
		$return['count'] = $iter;
		$return['total_count'] = $this->list_total_count(' WHERE table_idx = "'.$data['table_idx'].'"','yb_comment_petition');
		
		return $return;
	}	
	
	## 댓글 쓰기 - 청원게시판
	## sns 와 연계가 되어야 한다.
	public function comment_save_petition($data) {
		global $mysql;
		global $_SYSTEM;

		
		## 로그인 확인
//		if($this->myinfo['is_login'] !== true) call::xml_error('206','',$this->referer);

		$query = sprintf('SELECT COUNT(idx) AS cnt FROM yb_comment_petition WHERE table_name = "%s" AND table_idx = "%s" AND reg_pin = "%s" ', $data['table_name'], $data['table_idx'], $this->myinfo['my_pin']);
		$buffer = array();
		$buffer = $mysql->query_fetch($query);

		## 댓글은 한번만 쓸 수 있다.
		if(in_array($data['comment_type'], array('recommend','point')) && $buffer['cnt'] > 0) call::xml_error('155','',$this->referer);


		$db_data = array();
		$db_data['table_name']   = $data['table_name'];
		$db_data['table_idx']    = $data['table_idx'];
		$db_data['contents']     = addslashes(trim($data['comment']));
		$db_data['point']        = $data['point'];
		$db_data['reg_pin']      = $this->myinfo['my_pin'];
		$db_data['reg_id']       = $this->myinfo['user_id'];
		$db_data['reg_name']     = empty($data['reg_name'])?$this->myinfo['user_name']:$data['reg_name'];
		$db_data['reg_date']     = date('Y-m-d H:i:s');
		$db_data['reg_ip']       = $_SERVER['REMOTE_ADDR'];
		$db_data['sns_type']     = $this->myinfo['login_method'];
		$db_data['profile_img']  = $this->myinfo['profile_img'];
		$db_data['session_info'] = addslashes(serialize($this->myinfo));


		foreach($db_data as $field => $value) if(!empty($value)) $query_fields .= (empty($query_fields) ? '' : ', ').$field.' = "'.$value.'" ';
		if(!empty($db_data['contents'])) {
			$query = sprintf('INSERT INTO yb_comment_petition SET %s', $query_fields);
			$result = $mysql->query($query);
		}		
		
		/*
		## sns 포스팅.
		require_once($_SYSTEM['system_root'].'/tools/sns/class.use_sns.php');
		$my_sns = new use_sns();

		$message = $data['comment']; // 포스팅은 110자까지 제한된다.
		$url     = empty($_SERVER['HTTP_REFERER']) ? 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : $_SERVER['HTTP_REFERER'];
		$title   = $_SYSTEM['config']['site_name'];

		$result  = $my_sns->posting($message, $url, $title);
		*/
		if( $result ){
			call::xml_error(55,'',$this->referer);	
		}else{
			call::xml_error('154','',$this->referer);
		}
		
	}
	
	## 댓글 삭제
	public function comment_remove_petition($data) {
		global $mysql;
		
		## 로그인 확인
		if($this->myinfo['is_login'] !== true) call::xml_error('206','',$this->referer);

		$query = sprintf('SELECT reg_pin FROM yb_comment_petition WHERE idx = "%s" ', $data['comment_idx']);
		$buffer = array();
		$buffer = $mysql->query_fetch($query);

		## 관리자가 아니거나 자기 글이 아닐경우 삭제할 수 없다.
		if($this->permission['admin'] !== true && $this->myinfo['my_pin'] != $buffer['reg_pin']) call::xml_error('205','',$this->referer);

		$query = sprintf('DELETE FROM yb_comment_petition WHERE idx = "%s" ', $data['comment_idx']);
		$result = $mysql->query($query);

		call::xml_error(56,'',$this->referer);
	}

	## 댓글 신고
	public function comment_report_petition($data) {
		global $mysql;
		
		## 로그인 확인
		if($this->myinfo['is_login'] !== true) call::xml_error('206','',$this->referer);

		$buffer = array();
		$session_info = array();
		$query  = sprintf('SELECT session_info FROM yb_comment_petition WHERE idx = "%s"', $data['comment_idx']);
		$buffer = $mysql->query_fetch($query);

		if(!empty($buffer['session_info'])) $session_info = unserialize($buffer['session_info']);

		if(in_array($this->myinfo['my_pin'], $session_info)) call::xml_error('154','해당 댓글을 이미 신고하셨습니다.',$this->referer);

		$session_info[] = $this->myinfo['my_pin'];
		$query = sprintf('UPDATE yb_comment_petition SET report=report+1, session_info="%s" WHERE idx="%s"', addslashes(serialize($session_info)), $data['comment_idx']);
		$mysql->query($query);

		call::xml_error(62,'',$this->referer);
	}	
	
	## 댓글 목록
	## 서희진 2015.10.07 추가 : 인자값 $limit=NULL을 댓글 페이징을 위해서 추가
	public function comment_lineup($data, $limit=NULL) {
		global $mysql;
		
		if( $data['comment_type'] == "signature"){
			$table_name = "yb_signature";
		}else{
			$table_name = "yb_comment";
		}		
		
		$return = array();
		$return['comment_type'] = $data['comment_type'];

		if(empty($limit))	## 페이징일떄
		$query = sprintf('SELECT idx, contents, point, reg_pin, reg_name, reg_date, sns_type, profile_img FROM %s WHERE table_name = "%s" AND table_idx = "%s" ORDER BY reg_date DESC', $table_name, $data['table_name'], $data['table_idx']);
		else
		$query = sprintf('SELECT idx, contents, point, reg_pin, reg_name, reg_date, sns_type, profile_img FROM %s WHERE table_name = "%s" AND table_idx = "%s" ORDER BY reg_date DESC '.$limit, $table_name, $data['table_name'], $data['table_idx']);


		$result = $mysql->query($query);
		$point_negative = 0;
		$point_positive = 0;
		$point = 0;
		for($iter=0 ; $buffer = $mysql->fetch_array($result) ; $iter++) {
			$return['list'][$iter]['idx']         = $buffer['idx'];
			$return['list'][$iter]['contents']    = $buffer['contents'];
			$return['list'][$iter]['reg_name']    = $buffer['reg_name'];
			## 이름 숨김 설정 및 내글 수정 삭제.----------- 20190807 서희진.		
			//if($this->permission['admin'] != true && $this->module_config['use_hide_name_all'] == true) $return['list'][$iter]['reg_name'] = call::strcut($buffer['reg_name'], 1, 'OO');
			//else $return['list'][$iter]['reg_name']    = $buffer['reg_name'];
			$return['list'][$iter]['reg_name']    = $buffer['reg_name'];
			if( $buffer['reg_pin'] == $this->myinfo['my_pin'] && !empty($this->myinfo['my_pin'])) $return['list'][$iter]['reg_name'] = $buffer['reg_name'];
			## 이름 숨김 설정 및 내글 수정 삭제. end			
			$return['list'][$iter]['reg_pin']     = $buffer['reg_pin'];
			$return['list'][$iter]['reg_date']    = $buffer['reg_date'];
			$return['list'][$iter]['sns_type']    = $buffer['sns_type'];
			$return['list'][$iter]['profile_img'] = $buffer['profile_img'];
			if($data['comment_type'] == 'recommend') {
				if($buffer['point'] > 0) $point_positive++;
				if($buffer['point'] < 0) $point_negative++;
			} else if($data['comment_type'] == 'point') {
				$point += $buffer['point'];
			} else if($data['comment_type'] == 'biz') {
				$return['list'][$iter]['point'] = $buffer['point'];
			}
		}
		$return['point_positive'] = $point_positive;
		$return['point_negative'] = $point_negative;
		$return['point'] = $point;
		$return['count'] = $iter;
		$return['total_count'] = $this->list_total_count(' WHERE table_idx = "'.$data['table_idx'].'"',$table_name );
		
		return $return;
	}


	## 댓글 쓰기
	## sns 와 연계가 되어야 한다.
	public function comment_save($data) {
		global $mysql;
		global $_SYSTEM;		
				
		if( $data['comment_type'] == "signature"){
			$table_name = "yb_signature";
		}else{
			$table_name = "yb_comment";
		}
		
		## 로그인 확인
//		if($this->myinfo['is_login'] !== true) call::xml_error('206','',$this->referer);

		//$query = sprintf('SELECT COUNT(idx) AS cnt FROM yb_comment WHERE table_name = "%s" AND table_idx = "%s" AND reg_pin = "%s" ', $data['table_name'], $data['table_idx'], $this->myinfo['my_pin']);
		$query = sprintf('SELECT COUNT(idx) AS cnt FROM %s WHERE table_name = "%s" AND table_idx = "%s" AND reg_pin = "%s" ', $table_name, $data['table_name'], $data['table_idx'], $this->myinfo['my_pin']);
		$buffer = array();
		$buffer = $mysql->query_fetch($query);

	
		
		## 댓글은 한번만 쓸 수 있다.
		if(in_array($data['comment_type'], array('recommend','point')) && $buffer['cnt'] > 0) call::xml_error('155','',$this->referer);


		
		$db_data = array();
		$db_data['table_name']   = $data['table_name'];
		$db_data['table_idx']    = $data['table_idx'];
		$db_data['contents']     = addslashes(trim($data['comment']));		
		$db_data['point']        = $data['point'];
		$db_data['reg_pin']      = $this->myinfo['my_pin'];
		$db_data['reg_id']       = $this->myinfo['user_id'];
		$db_data['reg_name']     = empty($data['reg_name'])?$this->myinfo['user_name']:$data['reg_name'];
		$db_data['reg_date']     = date('Y-m-d H:i:s');
		$db_data['reg_ip']       = $_SERVER['REMOTE_ADDR'];
		$db_data['sns_type']     = $this->myinfo['login_method'];
		$db_data['profile_img']  = $this->myinfo['profile_img'];
		$db_data['session_info'] = addslashes(serialize($this->myinfo));

//print_r( $data['comment'] );exit;		
		foreach($db_data as $field => $value) if(!empty($value)) $query_fields .= (empty($query_fields) ? '' : ', ').$field.' = "'.$value.'" ';
		if(!empty($db_data['contents'])) {
			//$query = sprintf('INSERT INTO yb_comment SET %s', $query_fields);
			$query = sprintf('INSERT INTO %s SET %s', $table_name, $query_fields);
			$result = $mysql->query($query);
		}		
		
		/*
		## sns 포스팅.
		require_once($_SYSTEM['system_root'].'/tools/sns/class.use_sns.php');
		$my_sns = new use_sns();

		$message = $data['comment']; // 포스팅은 110자까지 제한된다.
		$url     = empty($_SERVER['HTTP_REFERER']) ? 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : $_SERVER['HTTP_REFERER'];
		$title   = $_SYSTEM['config']['site_name'];

		$result  = $my_sns->posting($message, $url, $title);
		*/
		if( $result ){
			if( $data['comment_type'] == "signature"){
				call::xml_error(81,'',$this->referer);	
			}else{
				call::xml_error(55,'',$this->referer);		
			}
			
		}else{
			call::xml_error('154','',$this->referer);
		}
		
	}
	
	## 댓글 삭제
	public function comment_remove($data) {
		global $mysql;
		
		## 로그인 확인
		if($this->myinfo['is_login'] !== true) call::xml_error('206','',$this->referer);
		
		if( $data['comment_type'] == "signature"){
			$table_name = "yb_signature";
		}else{
			$table_name = "yb_comment";
		}

		//$query = sprintf('SELECT reg_pin FROM yb_comment WHERE idx = "%s" ', $data['comment_idx']);
		$query = sprintf('SELECT reg_pin FROM %s WHERE idx = "%s" ', $table_name, $data['comment_idx']);
		$buffer = array();
		$buffer = $mysql->query_fetch($query);

		## 관리자가 아니거나 자기 글이 아닐경우 삭제할 수 없다.
		if($this->permission['admin'] !== true && $this->myinfo['my_pin'] != $buffer['reg_pin']) call::xml_error('205','',$this->referer);

		//$query = sprintf('DELETE FROM yb_comment WHERE idx = "%s" ', $data['comment_idx']);
		$query = sprintf('DELETE FROM %s WHERE idx = "%s" ', $table_name, $data['comment_idx']);
		$result = $mysql->query($query);
		
		if( $data['comment_type'] == "signature"){
			call::xml_error(82,'',$this->referer);
		}else{
			call::xml_error(56,'',$this->referer);
		}
	}

	## 댓글 신고
	public function comment_report($data) {
		global $mysql;
		
		if( $data['comment_type'] == "signature"){
			$table_name = "yb_signature";
		}else{
			$table_name = "yb_comment";
		}
		
		## 로그인 확인
		if($this->myinfo['is_login'] !== true) call::xml_error('206','',$this->referer);

		$buffer = array();
		$session_info = array();
		$query  = sprintf('SELECT session_info FROM %s WHERE idx = "%s"', $table_name, $data['comment_idx']);
		$buffer = $mysql->query_fetch($query);

		if(!empty($buffer['session_info'])) $session_info = unserialize($buffer['session_info']);

		if(in_array($this->myinfo['my_pin'], $session_info)) call::xml_error('154','해당 댓글을 이미 신고하셨습니다.',$this->referer);

		$session_info[] = $this->myinfo['my_pin'];
		$query = sprintf('UPDATE %s SET report=report+1, session_info="%s" WHERE idx="%s"', $table_name, addslashes(serialize($session_info)), $data['comment_idx']);
		$mysql->query($query);

		call::xml_error(62,'',$this->referer);
	}

	// 2012.09.06 파라미터추가 $mode 강성수.각 모듈별 클래스의 upload_file_check()호출부분 수정.
	public function upload_file_check($mode, $idx, $files = array()) {
		global $mysql;

		$ext_allow_photo = array();
		$ext_allow_movie = array();
		$ext_allow_photo = empty($this->module_config['ext_photo_file'])  ? $this->ext_allow_photo : explode(',',$this->module_config['ext_photo_file']);
		$ext_allow_movie = empty($this->module_config['ext_movie_file'])  ? $this->ext_allow_movie : explode(',',$this->module_config['ext_movie_file']);
		$ext_ebook_file  = empty($this->module_config['ext_ebook_file'])  ? $this->ext_allow_ebook : explode(',',$this->module_config['ext_ebook_file']);
		$max_size_upload = empty($this->module_config['max_size_upload']) ? $this->max_size_upload : $this->module_config['max_size_upload'];
		$max_size_file   = empty($this->module_config['max_size_file'])   ? $this->max_size_file   : $this->module_config['max_size_file'];
		$max_size_photo  = empty($this->module_config['max_size_photo'])  ? $this->max_size_photo  : $this->module_config['max_size_photo'];
		$max_size_movie  = empty($this->module_config['max_size_movie'])  ? $this->max_size_movie  : $this->module_config['max_size_movie'];

		/*
		$max_size_upload *= 1000000;
		$max_size_file   *= 1000000;
		$max_size_photo  *= 1000000;
		$max_size_movie  *= 1000000;
		*/
		
		$max_size_upload *= 20000000;
		$max_size_file   *= 20000000;
		$max_size_photo  *= 20000000;
		$max_size_movie  *= 20000000;
		
		$sum_file_size = 0;
		$count_photo   = 0;
		$count_movie   = 0;

		$return        = '';

		for($iter=0 ; $iter < $this->module_config['file_upload_count'] ; $iter++) {
			$field_name = 'file_'.$iter;
			if(!empty($files[$field_name]['name'])) { ## 업로드 파일이 있을 경우
				$sum_file_size += $files[$field_name]['size'];
				if($files[$field_name]['error'] > 0) $return .= '<li>업로드 도중 오류가 발생했습니다.</li>'.CR;
				if($this->check_value_in_list($files[$field_name]['name'], $this->module_config['ext_deny_upload']) == true) $return .= '<li>업로드가 허용되지 않은 확장자 입니다.</li>'.CR;
				
				if($this->check_value_in_list($files[$field_name]['name'], $ext_allow_movie) == true) { ## 동영상 파일일 경우
					$count_movie++;
					if($files[$field_name]['size'] > $max_size_movie) $return .= '<li>업로드 가능한 동영상 파일 크기('.round($max_size_movie/1000000,1).'MB)를 초과하였습니다.</li>'.CR;
				} else if($this->check_value_in_list($files[$field_name]['name'], $ext_allow_photo) == true) { ## 이미지 파일일 경우
					$count_photo++;
					if($files[$field_name]['size'] > $max_size_photo) $return .= '<li>업로드 가능한 사진 파일 크기('.round($max_size_photo/1000000,1).'MB)를 초과하였습니다.</li>'.CR;
				} else {
					if($files[$field_name]['size'] > $max_size_file) $return .= '<li>업로드 가능한 파일 크기('.round($max_size_file/1000000,1).'MB)를 초과하였습니다.</li>'.CR;
				}
			}
		}

		if($this->module_config['use_gallery_img'] == 'true' || $this->module_config['use_upload_movie'] == 'true') {
			if($mode == 'change') { ## 이미 올라가 있는 이미지가 있는지 확인, 동영상도 확인한다.
				$query  = sprintf('SELECT file_type FROM _file_info WHERE table_name="%s" AND table_idx="%s" AND file_type IN ("photo","movie") ', $this->module_config['table_name'], $idx);
				$result = $mysql->query($query);

				for($iter=0 ; $buffer = $mysql->fetch_array($result) ; $iter++) {
					if($buffer['file_type'] == 'movie') $count_movie += count($buffer);
					if($buffer['file_type'] == 'photo') $count_photo += count($buffer);
				}
			}


			if($this->module_config['use_gallery_img'] == 'true' && $count_photo == 0) $return .= '<li>1개 이상의 사진을 올려주시기 바랍니다.</li>'.CR;
			if($this->module_config['use_upload_movie'] == 'true' && $count_movie == 0) $return .= '<li>1개 이상의 동영상을 올려주시기 바랍니다.</li>'.CR;
		}

		if($sum_file_size > $max_size_upload) $return .= '<li>전체파일크기는 '.round($sum_file_size/1000000,1).'MB입니다. 업로드 가능한 전체 파일 크기 ('.round($max_size_upload/1000000,1).'MB)를 초과하였습니다.</li>'.CR;
		if($count_movie > 2) $return .= '<li>동영상파일은 하나만 올릴 수 있습니다.</li>'.CR;

		return $return;
	}



	public function clip_geocode($table_name, $table_idx) {
		global $mysql;

		$db_data = array();
		$query_field = '';
		$geo_data = $_SESSION['mylocation'];
		
		if(empty($table_name) || empty($table_idx)) return false;
		if(empty($geo_data)) return false;
		
		$db_data['k_latitude']  = $geo_data['k_latitude'];
		$db_data['k_longitude'] = $geo_data['k_longitude'];
		$db_data['g_latitude']  = $geo_data['g_latitude'];
		$db_data['g_longitude'] = $geo_data['g_longitude'];
		$db_data['address']     = $geo_data['addr'];
		$db_data['table_name']  = $table_name;
		$db_data['table_idx']   = $table_idx;
		$db_data['reg_date']    = date('Y-m-d H:i:s');
		
		foreach($db_data as $field => $value) if(!is_null($value)) $query_fields .= (empty($query_fields) ? '' : ', ').$field.' = "'.$value.'" ';
		$query = sprintf('INSERT INTO _geo_info SET %s', $query_fields);
		return $mysql->query($query);
	}

	public function admin_tools($idx) {
		$se_mode = $this->get_parameter('se_mode');

		switch ($se_mode) {
			case 'article_move':	// 게시물 이동
				$this->admin_tools_article_move($idx);
				break;
			case 'article_save':	// 게시물 설정
				if(method_exists($this, 'admin_tools_article_save')) $this->admin_tools_article_save($idx);
				break;
		}
	}

	public function admin_tools_article_delete($idx) {
	}

	public function admin_tools_article_move($idx) {
		global $mysql;
		
		$path         = $this->get_parameter('path'); ## target module path
		$board_id     = $this->get_parameter('board_id'); ## target board_id
		$menu_idx     = $this->get_parameter('menu_idx'); ## target board_id
		$menu_text    = $this->get_parameter('menu_text'); ## target board_id
		$move_type    = $this->get_parameter('move_type'); ## 원본글 처리
		$move_ment    = $this->get_parameter('move_ment'); ## 원본글 처리
		$sms_allow    = $this->get_parameter('sms_allow'); ## 등록자에게 sms 발송 여부
		$sms_phone    = $this->get_parameter('sms_phone'); ## 등록자 핸드폰 번호
		$sms_ment     = $this->get_parameter('sms_ment'); ## sms 맨트
		$remove_field = $this->get_parameter('remove_field'); ## 필드 호환성
		$allow        = $this->get_parameter('allow');

		if(empty($idx) || empty($board_id) || empty($path)) call::xml_error('154', '이동할 게시판을 선택해 주세요.', $this->referer);

		$source_table = $this->module_config['table_name'];
		$source_board_id = $this->module_config['board_id'];
		$source_idx = $idx;


		$target_module_info = $this->get_board_config($board_id, $path);
		$target_module = $target_module_info['package_id'];
		$target_table = $target_module_info['table_name'];
		$target_board_id = $board_id;

		if(empty($target_module_info)) call::xml_error('154', '이동 게시판의 설정값을 찾지 못하였습니다.', $this->referer);

		if($this->module_config['article_move_out'] == 'false') call::xml_error('154', '게시물 이동(보내기)이 금지된 게시판 입니다.', $this->referer);
		if($target_module_info['article_move_in'] == 'false') call::xml_error('154', '게시물 이동(받기)이 금지된 게시판 입니다.', $this->referer);

		## 글 이동 시작 ////////
		$buffer = array();
		$data = array();
		$overlap_data = array(); ## 중복되는 필드
		$garbage_data = array(); ## 중복되지 않는 필드
		$source_table_fields = array();
		$target_table_fields = array();

		## source table fields info.
		$result = $mysql->query('SHOW FIELDS FROM '.$source_table);
		while($buffer = $mysql->fetch_array($result)) $source_table_fields[] = $buffer['Field'];

		## target table fields info.
		$result = $mysql->query('SHOW FIELDS FROM '.$target_table);
		while($buffer = $mysql->fetch_array($result)) $target_table_fields[] = $buffer['Field'];

		## source article info
		$query = sprintf('SELECT * FROM %s WHERE idx = %s', $source_table, $source_idx);
		$data = $mysql->query_fetch($query);

		$skip_field = array('idx','pidx','level','seq','sort');
		foreach($source_table_fields as $field) {
			if(in_array($field, $skip_field)) continue;
			if(is_null($data[$field])) continue;

			if(in_array($field, $target_table_fields)) {
				$overlap_data[$field] = $data[$field];
			} else {
				$garbage_data[$field] = $data[$field];
			}
		}


		$overlap_data['board_id'] = $target_board_id;
		if(in_array('allow', $target_table_fields)) $overlap_data['allow'] = empty($allow) ? 'n' : $allow;
		
		$log = ' || 글이동:'.$this->myinfo['user_name'].':'.$this->myinfo['my_pin'].':'.$this->myinfo['user_level'].':'.date('Y-m-d H:i:s').':'.$_SERVER['REMOTE_ADDR'].':'.$source_table.':'.$source_board_id.':'.$source_idx;

		$target_contents = '이 게시물은 관리자에 의해서 '.date('Y-m-d').'에 이동된 글입니다.<br /><br />';
		$target_contents .= isset($data['contents']) ? $data['contents'] : '';

		if(in_array('log', $target_table_fields)) $overlap_data['log'] .= $log;
		else $target_contents .= '<br />'.$log;

		if($remove_field == 'append' && !empty($garbage_data)) $target_contents .= '<br />'.serialize($garbage_data);
		if(in_array('contents', $target_table_fields)) $overlap_data['contents'] = $target_contents;
		
		$query_fields = '';
		foreach($overlap_data as $field => $value) if(!is_null($value)) $query_fields .= (empty($query_fields) ? '' : ', ').$field.' = "'.$value.'" ';
		$query = sprintf('INSERT INTO %s SET %s', $target_table, $query_fields);
		$result = $mysql->query($query);

		$target_idx = $mysql->insert_id();
		if($target_board_id == 'mayor_proposal_reply') {
			$query = sprintf('UPDATE %s SET pidx = "%s", sort = %s, reg_date = "%s" WHERE idx = "%s"', $target_table, $target_idx, $target_idx, date('Y-m-d H:i:s'), $target_idx);
		} else {
			$query = sprintf('UPDATE %s SET pidx = "%s", sort = %s WHERE idx = "%s"', $target_table, $target_idx, $target_idx, $target_idx);
		}
		$mysql->query($query);

		## 대상 모듈에서 필수 세팅값 udpate : 모듈별로 존재한다.
		$article_move_update_file = $this->module_root.$target_module_info['path'].'/article_move_process.php';
		if(is_file($article_move_update_file)) {
			$module_info = $target_module_info;
			$file_info = $this->attached_file_list($source_idx);
			include_once $article_move_update_file;
			$move_result = article_move_update($target_idx, $module_info, $data, $file_info);
			if($move_result['result'] == false) { ## 글 이동이 실패할 경우 이동한 글을 삭제하고 되돌아간다.
				$query = sprintf('DELETE FROM %s WHERE idx = %s', $target_table, $target_idx);
				$mysql->query($query);
				call::xml_error('154',$move_result['mesg'],$this->referer);
				
			}
		}

		## 글 이동 끝 ////////

		## 파일처리 시작 ////////
		$target_data_path = '/_data/'.$target_module.'/'.$target_board_id.'/';

		$file_list = array();
		$query = sprintf('SELECT file_path AS source_file, CONCAT("%s",re_name) AS target_file FROM _file_info WHERE table_name = "%s" AND table_idx = "%s"', $target_data_path, $source_table, $source_idx);
		$result = $mysql->query($query);
		while($file_list = $mysql->fetch_array($result)) {
			if(is_file($this->module_root.$file_list['source_file'])) rename($this->module_root.$file_list['source_file'], $this->module_root.$file_list['target_file']);
		}
		
		$query = sprintf('UPDATE _file_info SET table_name = "%s", table_idx = "%s", file_path = CONCAT("%s",re_name) WHERE table_name = "%s" AND table_idx = "%s"', $target_table, $target_idx, $target_data_path, $source_table, $source_idx);
		$mysql->query($query);

		## 파일처리 끝 ////////

		## 원본글 처리 시작 ////////
		$db_data = array();
		if(in_array('move_pin', $source_table_fields)) $db_data['move_pin'] = $this->myinfo['my_pin'];
		if(in_array('move_name', $source_table_fields)) $db_data['move_name'] = $this->myinfo['user_name'];
		if(in_array('move_date', $source_table_fields)) $db_data['move_date'] = date('Y-m-d H:i:s');
		//if(in_array('move_type', $source_table_fields)) $db_data['move_type'] = $move_type;
		if(in_array('move_position', $source_table_fields)) $db_data['move_position'] = $target_module.'|'.$target_table.'|'.$target_idx;
		if(in_array('contents', $source_table_fields)) {
			## 추후 이동된 게시물로 바로가기 링크가 추가되어야 한다.
			$menuQuery = 'SELECT _path_url FROM _cms_menu WHERE id="'.$menu_idx.'" ';
			$menuInfo = $mysql->query_fetch($menuQuery);
			$temp = explode('|', $menuInfo['_path_url']);
			$hosts_url = 'http://'.substr($temp[0], 0, -3);
			$link = '<a href="'.$hosts_url;
			for($i=1;$i<count($temp);$i++) $link .= '/'.$temp[$i];
			$link .= '?idx='.$target_idx.'&mode=view" >[바로가기]</a>';
			$comment = '본 게시물은 관리자에 의해 '.$menu_text.'(으)로 이동되었습니다.<br />'.($this->module_config['use_editor'] == 'true' ? htmlspecialchars($link) : $link).'<br />';
			
			$db_data['contents'] = $comment.($move_type == 'cp'?$move_ment.'<br /><br /> '.$data['contents']:$move_ment);
		}

		$query_fields = '';
		if($move_type == 'rm') $query_fields = 'del = "y"';
		else foreach($db_data as $field => $value) if(!is_null($value)) $query_fields .= (empty($query_fields) ? '' : ', ').$field.' = "'.$value.'" ';

		$query = sprintf('UPDATE %s SET %s WHERE idx = "%s"', $source_table, $query_fields, $source_idx);
		$result = $mysql->query($query);
		## 원본글 처리 끝 ////////
		
		## sns 알림 - 추후 추가한다.
		
		## 이동완료
		call::xml_error('63', '', $this->referer);
	}

	public function strip_contents($data) {
		$data = str_replace('&','&amp;',$data);
		$data = strip_tags($data);
		
		return $data;
	}
	
	//2015-10-23 황재복 " 해당 글의 이미지 파일 갯수 쿼리
	public function get_photo_count($table_name, $idx) {
		global $mysql;
		
		$sql = sprintf('SELECT COUNT(idx) AS cnt FROM _file_info WHERE file_type="photo" AND table_name="%s" AND table_idx="%s"', $table_name, $idx);
		$cnt = $mysql->query_fetch($sql);
		
		return $cnt['cnt'];
	}
	
	## 참여인원 2017-06-07 서희
	public function get_poll_count($table_name,$idx) {
		global $mysql;
		$sql = sprintf('SELECT COUNT(idx) AS cnt FROM %s WHERE poll_idx="%s"', $table_name, $idx);

		$cnt = $mysql->query_fetch($sql);
		
		return $cnt['cnt'];
	}	

	## 참여인원 2017-06-07 서희
	public function get_poll_list($table_name,$idx,$field) {
		global $mysql;
		$data_list = array();
		
		$sql = sprintf('SELECT %s FROM %s WHERE poll_idx="%s"',$field, $table_name, $idx);

		$result       = $mysql->query($sql);
		
		while($data = $mysql->fetch_array($result)){
			$data_list[] = $data[$field];
		}

		return $data_list;
	}	
	
	//2012-09-26 황재복 : bit.ly 사이트 api를 이용 short url 제공
	public function get_short_url($long_url) {
		$long_url = urlencode(htmlspecialchars($long_url));
		$bitly_Username = 'o_f2hvdsf0i';
		$bitly_API_Key = 'R_e5577a9f095e7e697cabb604d4199965';
		$req = 'http://api.bit.ly/v3/shorten?login='.$bitly_Username.'&apiKey='.$bitly_API_Key.'&longUrl='.$long_url; 
		
		$contents = file_get_contents($req); 
	
		if(isset($contents)) { 
			$url = json_decode($contents, true); 
		} 
		return $url['data']['url'];
	}


/*	public function article_log_cms40($type, $table_name, $table_idx, $board_id=NULL, $view_comment=NULL) {
		global $mysql;

		$user_pin = $_SESSION['user_pin'];
		$user_name = $_SESSION['user_name'];
		$user_level = $_SESSION['user_level'];
		$user_id = $_SESSION['user_id'];

		$query = 'INSERT INTO `_article_log` SET table_name = "'.$table_name.'", table_idx = "'.$table_idx.'", '.(empty($board_id) ? NULL : 'board_id = "'.$board_id.'",').' type = "'.$type.'", user_pin = "'.$_SESSION['user_pin'].'", user_id = "'.$_SESSION['user_id'].'", user_name = "'.$_SESSION['user_name'].'", user_level = "'.$_SESSION['user_level'].'", access_date = "'.date('Y-m-d H:i:s').'", access_ip = "'.$_SERVER['REMOTE_ADDR'].'", access_host = "'.$_SERVER['HTTP_HOST'].'", access_url = "'.$_SERVER['PHP_SELF'].'"'.(empty($view_comment) ? NULL : ', view_comment = "'.$view_comment.'"');

		$mysql->query($query);
	}*/


	## 2014.07.08 오경우 : 신고센터 게시물 접근 로그 기록 : 쓰기, 보기, 수정, 삭제
	public function article_log_cms40($type, $table_name, $table_idx) {
		global $mysql;

		//if(empty($this->myinfo['my_pin'])) return false;

		if(empty($this->myinfo)) {
			global $_SYSTEM;
			$this->myinfo = $_SYSTEM['myinfo'];
		}

		$query = 'INSERT INTO `_article_log` SET table_name = "'.$table_name.'", table_idx = "'.$table_idx.'", type = "'.$type.'", user_pin = "'.$this->myinfo['my_pin'].'", user_name = "'.$this->myinfo['user_name'].'", user_level = "'.$this->myinfo['user_level'].'", access_date = "'.date('Y-m-d H:i:s').'", access_ip = "'.$_SERVER['REMOTE_ADDR'].'", access_host = "'.$_SERVER['HTTP_HOST'].'", access_url = "'.$_SERVER['PHP_SELF'].'"';

		$mysql->query($query);
	}
	
	public function view_comment($table_name, $table_idx, $board_id) {
		if($_POST['confirm_view_comment'] == 'true' && !empty($_POST['view_comment'])) {
		//	$this->article_log_cms40('view', $table_name, $table_idx, $board_id, $_POST['view_comment']);
		//	return true;
		} else {
			$ARTICLES['idx']  = $this->get_parameter('idx');
			$ARTICLES['mode'] = $this->get_parameter('mode');

			$ARTICLES['device']      = $this->device;
			$ARTICLES['module_root'] = $this->module_root;                  ## 근본적으로 스킨쪽에서 세팅되어야 한다.
			$ARTICLES['module_path'] = $this->module_config['module_path']; ## 근본적으로 스킨쪽에서 세팅되어야 한다.
			$ARTICLES['skin_style']  = 'common';
			$ARTICLES['skin_name']   = 'view_comment';

			//$xml_data =  ArrayToXML::toXml($ARTICLES, 'view');
			//echo $xml_data;
			echo serialize($ARTICLES);
			return false;
		}
	}

	## ************************************************************************
	## $file_info = array('table_name' => 모듈테이블명, 
	##                    'table_idx'=> 게시물 idx,
	##                    'title' => 파일설명 (이미지 파일의 경우 alt테그에 사용));
	## $file = $_FILES;
	## ************************************************************************
	public function upload_file_shop($file_info, $file, $ext_list, $dir_option='', $resize=array('w'=>false, 'h'=>false), $watermark=NULL) {
		global $mysql;
	
		set_time_limit(0);		
		//exit;
		## 업로드가 금지된 확장자 인지 체크한다. class.php의 null_check에서 선행처리하는데 다른곳에서 사용될 경우 누락(해킹)의 위험때문에 한번 더 넣었음.
		if($this->check_value_in_list($file['name'], $ext_list) == true) return false;

		## 디렉토리 검사해서 없으면 만들고
		$directory  = $this->module_root.'/_data/'.$this->module_config['package_id'];
		$this->make_folder($directory);
		if( !empty($dir_option) ){
			$directory .= '/'.$dir_option;
		}else{
			$directory .= '/'.$this->module_config['board_id'];
		}
		$this->make_folder($directory);

		## 파일이름 설정
		$file_ext      = strtolower($this->get_file_extension($file['name']));
		$file_pre_name = time();

		$iter = 0;
		do {
			$file_name = $file_pre_name.($iter > 0 ? '_'.$iter : '').'.'.$file_ext;
			$file_path = $directory.'/'.$file_name;
			$iter++;
		} while(file_exists($file_path) || file_exists($file_path.'.encrypt'));
		//} while(is_file($file_path));

		## file copy and db update
		if(move_uploaded_file($file['tmp_name'],$file_path) == false) return false;

		$ext_allow_photo = array();
		$ext_allow_movie = array();
		$ext_allow_photo = empty($this->module_config['ext_photo_file'])  ? $this->ext_allow_photo : explode(',',$this->module_config['ext_photo_file']);
		$ext_allow_movie = empty($this->module_config['ext_movie_file'])  ? $this->ext_allow_movie : explode(',',$this->module_config['ext_movie_file']);
		$ext_allow_ebook = empty($this->module_config['ext_ebook_file'])  ? $this->ext_allow_ebook : explode(',',$this->module_config['ext_ebook_file']);
		$file_list       = array();


		if(in_array($file_ext, $ext_allow_photo)) { ## 이미지 파일일 경우
			if($resize['w']!=false && $resize['h']!=false) {
				$temp_file_path = $directory.'/temp_'.$file_name;
				rename($file_path, $temp_file_path);
				$size = getimagesize($temp_file_path);
				if($size[0]>$resize['w']) { //사이즈를 변경후 비율에 맞춰서 Crop (일그러짐 방지)
					exec('/usr/bin/convert  -size '.$size[0].'x'.$size[1].' '.$temp_file_path.' -thumbnail '.$resize['w'].'x'.$resize['h'].'^ -gravity center -extent '.$resize['w'].'x'.$resize['h'].'  '.$file_path);
				} else {
					exec('cp -rf '.$temp_file_path.' '.$file_path);
				}
				unlink($temp_file_path);
			/* 사진용량의 최대크기를 2MB에서 10MB로 변경함.
			} else if($file['size'] > 2000000) {
				$temp_file_path = $directory.'/temp_'.$file_name;
				rename($file_path, $temp_file_path);
				$resize = floor((1800000 / $file['size']) * 100);
				exec('/usr/bin/convert  -resize '.$resize.'%x'.$resize.'% -quality 100 '.$temp_file_path.' '.$file_path);
				unlink($temp_file_path);
			*/
			} else if($file['size'] > 10000000) {
				$temp_file_path = $directory.'/temp_'.$file_name;
				rename($file_path, $temp_file_path);
				$resize = floor((9000000 / $file['size']) * 1000);
				exec('/usr/bin/convert  -resize '.$resize.'%x'.$resize.'% -quality 100 '.$temp_file_path.' '.$file_path);
				unlink($temp_file_path);
			}
			
			if(!is_null($watermark) && is_file($watermark)) exec('/usr/bin/composite -watermark 30% -gravity SouthEast '.$watermark.' '.$file_path.' '.$file_path);

			$file_list[0]['file_type'] = 'photo';
			$file_list[0]['original_name'] = $file['name'];
			$file_list[0]['re_name'] = $file_name;
			$file_list[0]['file_size'] = $file['size'];
			$file_list[0]['file_path'] = str_replace($this->module_root, '', $file_path);
		} else if(in_array($file_ext, $ext_allow_movie)) { ## 동영상 파일일 경우
			## flv convert
			$ffmpegCommand 		= '/usr/bin/ffmpeg'; // x264, xbix, ora, gsm, lame, faac, swscale, 0.5
					$qt_faststart 		= '/usr/local/bin/qt-faststart';
			$new_ext = '.mp4';
			if(extension_loaded('ffmpeg') == true) {
				set_time_limit(0); ## php excution time increase;


				$path_info = pathinfo($file_path);
				$movie = new ffmpeg_movie($file_path);
				$capture_count = 5;
				$total_frame = $movie->getFrameCount();
				$movie_duration = $movie->getDuration();


				$frame_interval = $capture_count > 0 ? floor($total_frame / $capture_count) : 0;

				## capture
				for($iter = 0; $iter <= $capture_count ; $iter++ ) {
					$frame = $movie->getFrame($iter * $frame_interval + 1);
					if($frame) {
						$gd_image = $frame->toGDImage();
						$captuer_image_tmp_path = $path_info['dirname'].'/'.$path_info['filename'].'_temp_'.$iter.'.png';
						$captuer_image_path = $path_info['dirname'].'/'.$path_info['filename'].'_thumb_'.$iter.'.png';
						imagepng($gd_image, $captuer_image_tmp_path);
						imagedestroy($gd_image);
						//$thumb_size = ($iter == 0) ? '640x480' : '80x60';
						$thumb_size =  '640x480';
						exec('/usr/bin/convert -resize '.$thumb_size.' '.$captuer_image_tmp_path.' '.$captuer_image_path);
						unlink($captuer_image_tmp_path);

						$file_list[$iter+1]['file_type'] = 'photo';
						$file_list[$iter+1]['original_name'] = $path_info['filename'].'_thumb_'.$iter.'.png';
						$file_list[$iter+1]['re_name'] = $path_info['filename'].'_thumb_'.$iter.'.png';
						$file_list[$iter+1]['file_size'] = filesize($captuer_image_path);
						$file_list[$iter+1]['file_path'] = str_replace($this->module_root, '', $captuer_image_path);
						$file_list[$iter+1]['movie_capture'] = 'y';
					}
				}
			} else {
				$path_info = pathinfo($file_path);
				$captuer_image_tmp_path = $path_info['dirname'].'/'.$path_info['filename'].'_temp_'.$iter.'.png';
				$captuer_image_path = $path_info['dirname'].'/'.$path_info['filename'].'_thumb_'.$iter.'.png';
				exec($ffmpegCommand.' -ss 00:00:03.01 -i '.$file_path.' -y -f image2 -vcodec mjpeg -vframes 1 '.$captuer_image_tmp_path);
			    
				$thumb_size =  '640x480';
				exec('/usr/bin/convert -resize '.$thumb_size.' '.$captuer_image_tmp_path.' '.$captuer_image_path);
				
				$file_list[1]['file_type'] = 'photo';
				$file_list[1]['original_name'] = $path_info['filename'].'_thumb_'.$iter.'.png';
				$file_list[1]['re_name'] = $path_info['filename'].'_thumb_'.$iter.'.png';
				$file_list[1]['file_size'] = filesize($captuer_image_path);
				$file_list[1]['file_path'] = str_replace($this->module_root, '', $captuer_image_path);
				$file_list[1]['movie_capture'] = 'y';
				
			}
				
				if($file_info['use_streaming'] == 'true') { ## mp4 변환
					$new_ext = '.mp4';
					$allowedMimeTypes = array('video/avi','video/mp4','video/mpeg','video/quicktime','video/x-msvideo','video/msvideo','video/x-ms-wmv');
				
				
					// Video
					$videoSize 			= isset($_POST['video_size']) 					? $_POST['video_size'] 		: '640x360';
					$videoBitrate 		= isset($_POST['video_bitrate'])				? (int)$_POST['video_bitrate'] 	: '700';
					$videoFramerate		= isset($_POST['video_framerate'])				? (int)$_POST['video_framerate'] : '30';
					$videoDeinterlace	= isset($_POST['encoding_video_deinterlace'])	? 1 : 0 ;
					$videoAspect 		= isset($_POST['video_aspect']) 				? $_POST['video_aspect'] 		: 0; //-aspect $videoAspect
					
				
					// Adudio
					$audioEnabled		= isset($_POST['encoding_enable_audio'])		? 0 : 1 ;
					$audioSamplerate	= isset($_POST['encoding_audio_sampling_rate'])	? (int)$_POST['encoding_audio_sampling_rate'] : '44100';
					$audioBitrate		= isset($_POST['encoding_audio_bitrate'])		? (int)$_POST['encoding_audio_bitrate'] : '128';
					$audioChannels		= (isset($_POST['encoding_audio_channels']) && $_POST['encoding_audio_channels']	== 'stereo')	? 2 : 1 ;
				
					// Build up the ffmpeg params from the values posted from the html form
					$customParams  = ' -s '.$videoSize; 				// Format the video size
					$customParams .= ' -b '.$videoBitrate.'k'; 		// Format the video bit rate  -- -vb
					$customParams .= ' -r '.$videoFramerate;			// Format the video frame rate
					if ($videoAspect) {
						$customParams .= ' -aspect '.$videoAspect;		// aspect ratio 
					}
					if ($videoDeinterlace) {
						$customParams .= ' -deinterlace ';				// Deinterlace the video
					}
					if ($audioEnabled) {
						$customParams .= ' -ar '.$audioSamplerate;		// Audio sample rate
						$customParams .= ' -ab '.$audioBitrate.'k';		// Audio bit rate
						$customParams .= ' -ac '.$audioChannels;		// Audio Channels
					} else {
						$customParams .= ' -an '; 						// Disable audio
					}
					$customParams .= ' -y ';							// Overwrite existing file
					
					
					$file_path_mp4 = $path_info['dirname'].'/'.$path_info['filename'].'_new.mp4';
				
					$command = $ffmpegCommand.' -i '.$file_path.' -vcodec libx264  -vpre superfast -vpre baseline -vsync 1  '; //  -bt 50k
					if ($audioEnabled) {
						$command = $command.' -acodec libfaac ';
					}
					$command = $command.$customParams.' -threads 12 '.$file_path_mp4.' 2>&1';
					///ffmpeg -i /home/encode/data/file.avi -threads 0 -vcodec libx264 -b 800k -r 24 -s 640x360 -vpre superfast -vpre baseline -acodec libfaac -ac 2 -ab 192k -ar 48000 -y /home/encode/mp4/file.mp4 	
					
					exec($command);
					exec($qt_faststart.' '.$file_path_mp4.' '.$file_path_mp4.' 2>&1');
				
					//exec($ffmpegCommand.' -i '.$file_path.' -threads 16 -b 604k -ac 1 -ar 44100 -coder 1 -flags +loop -cmp +chroma -partitions +parti4x4+partp8x8+partb8x8 -subq 5 -g 250 -keyint_min 25 -sc_threshold 40 -i_qfactor 0.71 '.$file_path_mp4);
					
					//if($file_path_mp4!=$file_path) 
					unlink($file_path);
					exec('mv '.$file_path_mp4.' '.$file_path);
					//$file_path = $file_path_mp4;
					$file['size'] = filesize($file_path);
				} else {
					/*
					if($file_ext != 'flv') { ## flv 변환
						$new_ext = '.flv';
						$m_bitrate = $movie->getBitRate();
						$m_bitrate = ceil( $m_bitrate / 1000 );
						$file_path_flv = $path_info['dirname'].'/'.$path_info['filename'].'.flv';
						exec('/usr/local/bin/ffmpeg -i '.$file_path.' -ar 44100 -f flv -ab '.$m_bitrate.' -s '.$movie->getFrameWidth().'x'.$movie->getFrameHeight().' '.$file_path_flv);
						unlink($file_path);
						$file_path = $file_path_flv;
						$file['size'] = filesize($file_path);
					}
					*/
					exit;
				}





				$file_list[0]['file_type'] = 'movie';
				$file_list[0]['original_name'] = str_replace($this->get_file_extension($file['name']), 'flv', $file['name']);
				$file_list[0]['re_name'] = $path_info['filename'].$new_ext;
				$file_list[0]['file_size'] = $file['size'];
				$file_list[0]['file_path'] = str_replace($this->module_root, '', $file_path);
		
			
		} else { ## 일반파일일 경우
			
			$file_list[0]['file_type'] = 'file';
			$file_list[0]['original_name'] = $file['name'];
			$file_list[0]['re_name'] = $file_name;
			$file_list[0]['file_size'] = $file['size'];
			$file_list[0]['file_path'] = str_replace($this->module_root, '', $file_path);
		}

		$db_data['board_id']   = $file_info['board_id'];
		$db_data['table_name'] = $file_info['table_name'];
		$db_data['table_idx']  = $file_info['table_idx'];
		$db_data['seq']		   = $file_info['seq'];
		$movie_idx = 0;
		$file_count = count($file_list);
		for($iter = 0 ; $iter < $file_count ; $iter++) {
			$buffer = $file_list[$iter];
			$db_data['file_type']     = $buffer['file_type'];
			$db_data['original_name'] = $buffer['original_name'];
			$db_data['re_name']       = $buffer['re_name'];
			$db_data['file_size']     = $buffer['file_size'];
			$db_data['file_path']     = $buffer['file_path'];		
			//2014-12-16 황재복 : alt 넣도록
			$db_data['title']         = $file_info['title'];
			if(!empty($buffer['movie_capture'])) $db_data['movie_capture'] = $buffer['movie_capture'];
			if(!empty($movie_idx) && $buffer['movie_capture'] == 'y') $db_data['movie_idx'] = $movie_idx;
			if($buffer['file_type'] == 'movie') $db_data['movie_duration'] = $movie_duration;
			
			$query_fields = '';
			foreach($db_data as $field => $value) if(!empty($value)) $query_fields .= (empty($query_fields) ? '' : ', ').$field.' = "'.$value.'" ';
			$query = sprintf('INSERT INTO _file_info_shop SET %s', $query_fields);	
			$result = $mysql->query($query);

			$file_idx = $mysql->insert_id();
			if($buffer['file_type'] == 'movie') $movie_idx = $file_idx;
			
			if(in_array($file_ext, $ext_allow_ebook)) {
				if($this->module_config['ebook_use']=='true') {
					$this->getPlugin('digitomi')->upload_before($db_data['table_idx'], $file_idx, $file['name']);
				}
			}
		}
		
		$this->clip_geocode('_file_info', $file_idx);
		
		return true;
	}
	
	//2014-12-16 황재복 : alt 넣도록
	public function update_alt_shop($file_info, $title) {
		global $mysql;

		$query = sprintf('UPDATE _file_info_shop SET `title` = "%s" WHERE `idx`="%s"', $title, $file_info['idx']);				
		$result = $mysql->query($query);
			
		return true;
	}
	
	## 등록업소 사업자등록증 파일정보 추출.
	public function attached_file_list_shop1($idx) {
		global $mysql;
		$data_file = array();
		
		$query  = 'SELECT idx, file_type, original_name, re_name, file_path, file_size, download, title, description, tag, ebook_code, seq, movie_duration, title_image ';
		$query .= 'FROM _file_info_shop ';
		$query .= 'WHERE table_name = "'.$this->module_config['table_name'].'" AND table_idx = "'.$idx.'" AND movie_capture <> "y" AND seq = "1" ';
		$query .= 'ORDER BY file_type ASC, seq DESC, idx ASC';

		$result       = $mysql->query($query);
		while($data_file[] = $mysql->fetch_array($result)) ;
		array_pop($data_file);
		return $data_file;
	}
	
	## 등록업소 사업자등록증 파일정보 추출.
	public function attached_file_list_shop2($idx) {
		global $mysql;
		$data_file = array();
		
		$query  = 'SELECT idx, file_type, original_name, re_name, file_path, file_size, download, title, description, tag, ebook_code, seq, movie_duration, title_image ';
		$query .= 'FROM _file_info_shop ';
		$query .= 'WHERE table_name = "'.$this->module_config['table_name'].'" AND table_idx = "'.$idx.'" AND movie_capture <> "y" AND seq = "2" ';
		$query .= 'ORDER BY file_type ASC, seq DESC, idx ASC';

		$result       = $mysql->query($query);
		while($data_file[] = $mysql->fetch_array($result)) ;
		array_pop($data_file);
		return $data_file;
	}
	
	public function remove_upload_file_shop($idx, $table_name, $table_idx) {
		global $mysql;
		$data = array();

		if(empty($idx)) return false;

		$query = sprintf('SELECT idx, file_path FROM _file_info_shop WHERE table_name = "%s" AND table_idx = "%s" AND (idx = "%s" OR (movie_idx = "%s" AND movie_capture = "y"))', $table_name, $table_idx, $idx, $idx);

		$file_path = array();
		$result = $mysql->query($query);
		for($iter=0 ; $buffer = $mysql->fetch_array($result) ; $iter++) $file_path[$iter] = $buffer['file_path'];

		$query = sprintf('DELETE FROM _file_info_shop WHERE idx = "%s" OR (movie_idx = "%s" AND movie_capture = "y")', $idx, $idx);
		$result = $mysql->query($query);
		if($result == false) return false;

		foreach($file_path as $value) {
			$remove_path  = $this->module_root.$value;
			if(is_file($remove_path)) unlink($remove_path);
		}

		return true;
	}
	
	## 2017-10-26_배주원
	## 게시판_대표썸네일_업데이트위해_만듬
	## title_image만_수정하기위해_작성됨.
	## 반환되는값(0/1)
	public function update_title_image($table_name,$board_id,$table_idx,$file_list,$mod_idx) {
		global $mysql;
		$query = '';
		/*
		if(empty($table_name) || empty($board_id) || empty($table_idx) || empty($file_list) || empty($mod_idx)){
			return false;
		}
		*/
		$file_idxs = array();
		$query .= sprintf('UPDATE `%s` SET title_image = ','_file_info');
		$query .= 'CASE ';
		foreach($file_list as $file_key=>$file_data){
			$file_idxs[] = $file_data['idx'];
			if(!$file_data)continue;
			//현재는_한개만_바꿈
			//다중으로_할_필요가있을경우_$mod_idx를배열로바꾸고_in_array함수로_비교하면됨.
			if($file_key == $mod_idx){
				$query .= sprintf('WHEN idx = %s THEN "y" ',$file_data['idx']);
			}else{
				$query .= sprintf('WHEN idx = %s THEN "n" ',$file_data['idx']);
			}
		}
		$query .= 'END ';
		$query .= 'WHERE idx IN('.implode(',',$file_idxs).') ';
		$query .= sprintf('AND board_id = "%s" ',$board_id);
		$query .= sprintf('AND table_name = "%s" ',$table_name);
		$query .= sprintf('AND table_idx = "%s" ',$table_idx);
		//echo $query;exit;
		$mysql->query($query);
		unset($query,$file_idxs);
		return true;
	}
	
	## view.php
	## 앞,뒤글_불러오기
	## 권한구별해서_글,불러오기
	public function nearby_articles($table_name,$board_id,$this_idx,$use_top="n",$category_1="") {
		global $mysql;
		$query = '';
		$query_view_permission = array();
	
		
		if($this->permission['manage'] === true || $this->permission['admin'] === true){
		}else{ 
		}

			if($use_top=="y"){
				$query_view_permission[] = 'top = "y"';
			}else{
				$query_view_permission[] = 'top <> "y"';
			}
			if(!empty($category_1)) $query_view_permission[] = 'category_1 = "'.$category_1.'"';
			$query_view_permission[] = 'allow <> "n"';
			$query_view_permission[] = '`open` <> "n"';
			$query_view_permission[] = 'del <> "y"';
			// $query_view_permission[] = '`level` = 0';
		$query .= '
			SELECT a.* FROM (
			(
				SELECT
					NULL AS "idx",
					NULL AS "title",
					NULL AS "contents",
					NULL AS "reg_date",
					NULL AS "del",
					NULL AS "open",
					NULL AS "allow",
					NULL AS "type"
			)
			UNION ALL
			(SELECT idx,title,contents,reg_date,del,open,allow,"prev" FROM '.$table_name.' WHERE idx > "'.$this_idx.'" AND '.($this->module_config['skin_style']=='board_busview'?' board_id in ("www_contest","own_business") AND `varchar_3`="y" ':'board_id = "'.$board_id.'" ').(!empty($query_view_permission) ? 'AND '.implode(' AND ',$query_view_permission) : '').' ORDER BY idx ASC LIMIT 1)
			UNION ALL
			(SELECT idx,title,contents,reg_date,del,open,allow,"next" FROM '.$table_name.' WHERE idx < "'.$this_idx.'" AND '.($this->module_config['skin_style']=='board_busview'?' board_id in ("www_contest","own_business") AND `varchar_3`="y" ':'board_id = "'.$board_id.'" ').(!empty($query_view_permission) ? 'AND '.implode(' AND ',$query_view_permission) : '').' ORDER BY idx DESC LIMIT 1)
			) AS a  LIMIT 3';

		//echo $query;		exit;
		$data_list = array();
		$result = $mysql->query($query);
		while($self = $mysql->fetch_array($result)){
			if(!$self)continue;
			$data_list[] =$self; 
		}
		return $data_list;
	}
	
	########################################################################
	## biz에서만 사용
	########################################################################
	/* code1값을 카테고리 배열생성 */
	public static function get_cate_list($cate) {
		global $mysql;
		$return = array();
		
		$len = strlen($cate);		
		$loof =  (int)($len/2);
		$table_name = '_smartourpack_code';
		$value = '';
		for($iter=0;$iter<=$loof;$iter++){			
			if( $iter == 0 ){
				$temp[$iter]['code'] = substr($cate,$iter,1);
			}else{
				$temp[$iter]['code'] = substr($cate,$iter,2);
			}
			$value .= $temp[$iter]['code'];
				
			$query = sprintf('SELECT title FROM %s WHERE kind="BIZ_DEPTH_%s_CATE" AND value="%s";', $table_name, ($iter+1), $value);
			$data   = $mysql->query_fetch($query);
			$temp[$iter]['name'] = $data['title'];
		}
		$return = $temp;

		
		return $return;
	}	
	

	########################################################################
	## 그리드형 갤러리에서 사용하는 이미지 목록.(사진 DB에서 사용하던 스킨 가져오면서 같이 옮김)
	########################################################################
	public function get_munti_thumb_system($sizew=480,$sizeh=480,$margin=0,$img=array(),$img_url='',$img_path='',$url='#none',$class='') {
		$j=0;
		$w = $h = '522';
		$ratio_type =  array();
		for($i=0;$i<count($img);$i++) {
			if(preg_match('/.*?\.(jpg|png|bmp|gif|jpeg)$/',$img[$i]['file_name'])) {
				$img_item[] = $img[$i];
				$img_size = getimagesize($img_path.$img[$i]['file_name']);
				$img_scale = number_format($img_size[0]/$img_size[1],1);
				if($img_scale=='1') { $ratio_type['s']++;} //정사각형
					else if($img_scale<1) { $ratio_type['h']++; } //세로형
						else { $ratio_type['w']++; } //가로형
			}
		}
		unset($img);
		switch(count($img_item)) {
			case 1:
				$w = '522';
				$h = '258';
				break;
			case 2:
				if($ratio_type['w']>=2) { 
					$ratio_style='w';
					$w = '480';//'580';
					$h = '476';//'280';
				} else {
					$w = '480';
					$h = '476'; //'480';
				}
				break;
			case 3:
				$w = '318';
				$h = '461'; //'480';
				break;
			case 4:
				if($ratio_type['w']>2) { 
					$ratio_style='w';
					$w = '690';
					$h = '344';
				} else {
					$w = '237';
					$h = '480';
				}
				break;
			case 5:
				break;
			case (count($img_item) >= 6):
				$w = '588';
				$h = '440'; //'440';
				break;
				
		}
		$iter=0;
		$li='';
		//주석시작
		foreach($img_item as $item) {
			$li .= '<li class="_photo_item'.(($iter==5 && count($img_item)>6)?" img_more":"").'" data-mediaindex="'.$iter.'">';
			$li .= '<img src="'.$img_url.$w.'x'.$h.'/'.$item['file_name'].'" alt="'.$item['alt'].'">';
			if($iter==5 && count($img_item)>6) { $li .='<a class="_more_item iframe" href="'.$url.'"><span>더보기</span></a></li>'; $iter++;break; } 
			$li .= '</li>';
			$iter++;
		}
		//주석 끝
		$link_class = empty($class)?'':' class="'.$class.'"';
		foreach($img_item as $item) {
			$li .= '<li class="_photo_item" data-mediaindex="'.$iter.'">';
			$li .= '<a href="'.$url.'" '.$link_class.' ><img src="'.$img_url.$w.'x'.$h.'/'.$item['file_name'].'" alt="'.$item['alt'].'" /></a>';			

			if($iter==5 && count($img_item)>=6) { 
				$li .='<a class="_more_item '.$class.'" href="'.$url.'" '.$link_class.' ><span>더보기</span></a>';
				$li .= '</li>'; 
				$iter++;
				break; 
			} else {
//			if($iter<6) {
				$li .= '</li>';
				$iter++;
			}
		}

		$html = '<div class="_attachment_photos_region">
		<div class="collage_widget">
		<ul class="collage" data-collage="'.$iter.'"  '.(!empty($ratio_style)?'data-ratio="'.$ratio_style.'"':'').'>'.CR.$li.CR.'</ul>
		</div>
		</div>';
		

			
		return $html;
		
	}

	########################################################################
	## 그리드형 갤러리에서 사용하는 이미지 목록.(사진 DB에서 사용하던 스킨 가져오면서 같이 옮김)
	########################################################################
	public function get_munti_thumb($sizew=480,$sizeh=480,$margin=0,$img=array(),$img_path='',$url='#none', $photo_count=0, $class='') {
		global $_SYSTEM;
		
		$img_item = $img;
		unset($img);
		switch(count($img_item)) {
			case 1:
				$w_ori = '418'; //'480';
				$h_ori = '184'; //'238';
				break;
			case 2:
				$w_ori = '207'; //'480';
				$h_ori = '184'; //'238';				
				break;
			case 3:
				$w_ori = '138';
				$h_ori = '184'; //'480';
				break;
			case 4:
				$w_ori = '207';
				$h_ori = '92';
				break;
			case 5:
				$w_ori = '138';
				$h_ori = '92';
				break;
			default:
				$w_ori = '138';
				$h_ori = '92';
				break;
				
		}


		$img_cnt = 4; //롤오버시 나타낼수 잇는 사진수
		$iter=0;
		$li='';
		foreach($img_item as $item) {			
			if( count($img_item) == 5 && $iter == 0 ){
				$w = '138';
				$h = '184';
			}else{
				$w = $w_ori;
				$h = $h_ori;
			}
			if( count($img_item)  > 6 ){
//				$src = call::image_check($img_path.substr($img_item[$iter]['file_name'],0,1).'/w'.$w.',h'.$h.',q85/'.$img_item[$iter]['file_name'],$_SYSTEM['proxy_name']);
//				$src = $img_path.substr($img_item[$iter]['file_name'],0,1).'/w'.$w.',h'.$h.',q85/'.$img_item[$iter]['file_name'];
				$src = $img_path.'/'.$w.'x'.$h.'/'.$img_item[$iter]['file_name'];

				$li .= '<div class="item">';
				$li .= '	<a href="'.$url.'" '.(!empty($class)? $class:'').'>';
				$li .= '		<p class="pic">';
				$li .= '			<span>';
				$li .= '				<img src="'.$src.'" alt="'.$img_item[$iter]['alt'].'" />';
				/*
				for($jter=0; $jter<$img_cnt; $jter++){
					$tmp_rand = mt_rand(0,count($img_item)-1);
					//$li .= '		<img src="'.call::image_check($img_path.substr($img_item[$tmp_rand]['file_name'],0,1).'/w'.$w.',h'.$h.',q85/'.$img_item[$tmp_rand]['file_name'],$_SYSTEM['proxy_name']).'" alt="'.$img_item[$tmp_rand]['alt'].'" />';
					$li .= '		<img src="'.$img_path.substr($img_item[$tmp_rand]['file_name'],0,1).'/w'.$w.',h'.$h.',q85/'.$img_item[$tmp_rand]['file_name'].'" alt="'.$img_item[$tmp_rand]['alt'].'" />';
				}
				*/
				$li .= '			</span>';
				$li .= '		</p>';
				$li .= '	</a>';
				$li .= '</div>';
			}else{
//				$src = call::image_check($img_path.substr($img_item[$iter]['file_name'],0,1).'/w'.$w.',h'.$h.',q85/'.$img_item[$iter]['file_name'],$_SYSTEM['proxy_name']);
//				$src = $img_path.substr($img_item[$iter]['file_name'],0,1).'/w'.$w.',h'.$h.',q85/'.$img_item[$iter]['file_name'];
				$src = $img_path.'/'.$w.'x'.$h.'/'.$img_item[$iter]['file_name'];
				$li .= '<div class="item">';
				$li .= '	<a href="'.$url.'" '.(!empty($class)? $class:'').'>';
				$li .= '				<img src="'.$src.'" alt="'.$img_item[$iter]['alt'].'" />';
				$li .= '	</a>';
				$li .= '</div>';
			}
			$arr_img[] = $img_item[$iter]['file_name'];
			if($iter == 4 && count($img_item)>6) { 
//				$src = call::image_check($img_path.substr($img_item[$iter]['file_name'],0,1).'/w'.$w.',h'.$h.',q85/'.$img_item[$iter]['file_name'],$_SYSTEM['proxy_name']);
//				$src = $img_path.substr($img_item[$iter]['file_name'],0,1).'/w'.$w.',h'.$h.',q85/'.$img_item[$iter]['file_name'];
				$src = $img_path.'/'.$w.'x'.$h.'/'.$img_item[$iter+1]['file_name'];
				$iter++;
				$li .= '<div class="item">
							<a href="#none"><img src="'.$src.'" alt="'.$img_item[$iter+1]['alt'].'" /></a>
							<a href="'.$url.'" class="more"><span><span class="icon"></span>더보기('.($photo_count-6).')</span></a>
						</div>';
				$iter++; // 더보기를 카운팅하여 6을 만들기 위해서 하나더 증가.
				$addClass = ' more slide_thumb';
				break; 
			}else{ 
				$addClass = '';
				$iter++;
			}
		}

		

		$html = '<div class="grid_img thumb'.$iter.$addClass.'">'.CR.$li.CR.'</div>';
		
		return $html;
		
	}
	
	########################################################################
	## 그리드형 갤러리에서 사용하는 이미지 목록.(사진 DB에서 사용하던 스킨 가져오면서 같이 옮김)
	########################################################################
	public function get_munti_thumb_new($sizew=480,$sizeh=480,$margin=0,$img=array(),$img_path='',$url='#none', $photo_count=0) {
		global $_SYSTEM;

		$img_item = $img;
		unset($img);
		
		$img_count = ( count($img_item) > 5 )?6:count($img_item);
		$li='';
		switch( $img_count ) {
			case 1:
				$size[] = array("w"=>'324', "h"=>'198' );		
				foreach($img_item as $index=>$item) {								
					$li .= '<div class="img_box">';
					$li .= '<img src="'.$img_path.'/'.$size[$index]['w'].'x'.$size[$index]['h'].'/'.$item['file_name'].'" alt="'.$item['alt'].'" />';				
					$li .= '</div>';	
				}
				
				break;
			case 2:
				$size[] = array("w"=>'162', "h"=>'198' );
				$size[] = array("w"=>'162', "h"=>'198' );
				
				foreach($img_item as $index=>$item) {				
					$li .= '<div class="img_box">';
					$li .= '<img src="'.$img_path.'/'.$size[$index]['w'].'x'.$size[$index]['h'].'/'.$item['file_name'].'" alt="'.$item['alt'].'" />';				
					$li .= '</div>';						
				}	
				
				break;
			case 3:
				$size[] = array("w"=>'162', "h"=>'198' );
				$size[] = array("w"=>'162', "h"=>'66' );
				$size[] = array("w"=>'162', "h"=>'132' );
				
				
				$li .= '<div class="left_box">';
				$li .= '	<div class="img_box">';
				$li .= 			'<img src="'.$img_path.'/'.$size[0]['w'].'x'.$size[0]['h'].'/'.$img_item[0]['file_name'].'" alt="'.$img_item[0]['alt'].'" />';				
				$li .= '	</div>';
				$li .= '</div>';
				$li .= '<div class="right_box">';
				$li .= '	<div class="img_box">';
				$li .= 			'<img src="'.$img_path.'/'.$size[1]['w'].'x'.$size[1]['h'].'/'.$img_item[1]['file_name'].'" alt="'.$img_item[1]['alt'].'" />';
				$li .= '	</div>';
				$li .= '	<div class="img_box">';
				$li .= 			'<img src="'.$img_path.'/'.$size[2]['w'].'x'.$size[2]['h'].'/'.$img_item[2]['file_name'].'" alt="'.$img_item[2]['alt'].'" />';
				$li .= '	</div>';
				$li .= '</div>';				
				
				break;
			case 4:
				$size[] = array("w"=>'324', "h"=>'99' );
				$size[] = array("w"=>'108', "h"=>'99' );
				$size[] = array("w"=>'108', "h"=>'99' );
				$size[] = array("w"=>'108', "h"=>'99' );				
				
				$li .= '<div class="top_box">';
				$li .= '	<div class="img_box">';
				$li .= 			'<img src="'.$img_path.'/'.$size[0]['w'].'x'.$size[0]['h'].'/'.$img_item[0]['file_name'].'" alt="'.$img_item[0]['alt'].'" />';				
				$li .= '	</div>';
				$li .= '</div>';
				$li .= '<div class="bottom_box">';
				$li .= '	<div class="img_box">';
				$li .= 			'<img src="'.$img_path.'/'.$size[1]['w'].'x'.$size[1]['h'].'/'.$img_item[1]['file_name'].'" alt="'.$img_item[1]['alt'].'" />';
				$li .= '	</div>';
				$li .= '	<div class="img_box">';
				$li .= 			'<img src="'.$img_path.'/'.$size[2]['w'].'x'.$size[2]['h'].'/'.$img_item[2]['file_name'].'" alt="'.$img_item[2]['alt'].'" />';
				$li .= '	</div>';
				$li .= '	<div class="img_box">';
				$li .= 			'<img src="'.$img_path.'/'.$size[3]['w'].'x'.$size[3]['h'].'/'.$img_item[3]['file_name'].'" alt="'.$img_item[3]['alt'].'" />';
				$li .= '	</div>';				
				$li .= '</div>';					
				
				
				break;
			case 5:
				$size[] = array("w"=>'108', "h"=>'198' );
				$size[] = array("w"=>'108', "h"=>'99' );				
				$size[] = array("w"=>'108', "h"=>'99' );				
				$size[] = array("w"=>'108', "h"=>'99' );				
				$size[] = array("w"=>'108', "h"=>'99' );								
				
				$li .= '<div class="left_box">';
				$li .= '	<div class="img_box">';
				$li .= 			'<img src="'.$img_path.'/'.$size[0]['w'].'x'.$size[0]['h'].'/'.$img_item[0]['file_name'].'" alt="'.$img_item[0]['alt'].'" />';				
				$li .= '	</div>';
				$li .= '</div>';
				$li .= '<div class="right_box">';
				$li .= '	<div class="img_box">';
				$li .= 			'<img src="'.$img_path.'/'.$size[1]['w'].'x'.$size[1]['h'].'/'.$img_item[1]['file_name'].'" alt="'.$img_item[1]['alt'].'" />';
				$li .= '	</div>';
				$li .= '	<div class="img_box">';
				$li .= 			'<img src="'.$img_path.'/'.$size[2]['w'].'x'.$size[2]['h'].'/'.$img_item[2]['file_name'].'" alt="'.$img_item[2]['alt'].'" />';
				$li .= '	</div>';
				$li .= '	<div class="img_box">';
				$li .= 			'<img src="'.$img_path.'/'.$size[3]['w'].'x'.$size[3]['h'].'/'.$img_item[3]['file_name'].'" alt="'.$img_item[3]['alt'].'" />';
				$li .= '	</div>';
				$li .= '	<div class="img_box">';
				$li .= 			'<img src="'.$img_path.'/'.$size[4]['w'].'x'.$size[4]['h'].'/'.$img_item[4]['file_name'].'" alt="'.$img_item[4]['alt'].'" />';
				$li .= '	</div>';				
				$li .= '</div>';	
				
				break;
			case 6:	
				$size[] = array("w"=>'108', "h"=>'99' );
				$size[] = array("w"=>'108', "h"=>'99' );				
				$size[] = array("w"=>'108', "h"=>'99' );				
				$size[] = array("w"=>'108', "h"=>'99' );				
				$size[] = array("w"=>'108', "h"=>'99' );				
				$size[] = array("w"=>'108', "h"=>'99' );								
				
				foreach($img_item as $index=>$item) {	
					if( $index == 6 ) break;
					$li .= '<div class="img_box">';
					$li .= '<img src="'.$img_path.'/'.$size[$index]['w'].'x'.$size[$index]['h'].'/'.$item['file_name'].'" alt="'.$item['alt'].'" />';				
					$li .= '</div>';						
				}
				
				break;				
			default:
				$size[] = array("w"=>'324', "h"=>'198' );				
				break;
				
		}
		$html = $li;
			
		return $html;
		
	}
	
	public function get_munti_thumb_new_alt($sizew=480,$sizeh=480,$margin=0,$img=array(),$img_path='',$url='#none', $photo_count=0, $title) {
		global $_SYSTEM;

		$img_item = $img;
		unset($img);
		
		$img_count = ( count($img_item) > 5 )?6:count($img_item);
		$li='';
		switch( $img_count ) {
			case 1:
				$size[] = array("w"=>'324', "h"=>'198' );		
				foreach($img_item as $index=>$item) {								
					$li .= '<div class="img_box">';
					$li .= '<img src="'.$img_path.'/'.$size[$index]['w'].'x'.$size[$index]['h'].'/'.$item['file_name'].'" alt="'.$title.'" />';				
					$li .= '</div>';	
				}
				
				break;
			case 2:
				$size[] = array("w"=>'162', "h"=>'198' );
				$size[] = array("w"=>'162', "h"=>'198' );
				
				foreach($img_item as $index=>$item) {				
					$li .= '<div class="img_box">';
					$li .= '<img src="'.$img_path.'/'.$size[$index]['w'].'x'.$size[$index]['h'].'/'.$item['file_name'].'" alt="'.$item['alt'].'" />';				
					$li .= '</div>';						
				}	
				
				break;
			case 3:
				$size[] = array("w"=>'162', "h"=>'198' );
				$size[] = array("w"=>'162', "h"=>'66' );
				$size[] = array("w"=>'162', "h"=>'132' );
				
				
				$li .= '<div class="left_box">';
				$li .= '	<div class="img_box">';
				$li .= 			'<img src="'.$img_path.'/'.$size[0]['w'].'x'.$size[0]['h'].'/'.$img_item[0]['file_name'].'" alt="'.$img_item[0]['alt'].'" />';				
				$li .= '	</div>';
				$li .= '</div>';
				$li .= '<div class="right_box">';
				$li .= '	<div class="img_box">';
				$li .= 			'<img src="'.$img_path.'/'.$size[1]['w'].'x'.$size[1]['h'].'/'.$img_item[1]['file_name'].'" alt="'.$img_item[1]['alt'].'" />';
				$li .= '	</div>';
				$li .= '	<div class="img_box">';
				$li .= 			'<img src="'.$img_path.'/'.$size[2]['w'].'x'.$size[2]['h'].'/'.$img_item[2]['file_name'].'" alt="'.$img_item[2]['alt'].'" />';
				$li .= '	</div>';
				$li .= '</div>';				
				
				break;
			case 4:
				$size[] = array("w"=>'324', "h"=>'99' );
				$size[] = array("w"=>'108', "h"=>'99' );
				$size[] = array("w"=>'108', "h"=>'99' );
				$size[] = array("w"=>'108', "h"=>'99' );				
				
				$li .= '<div class="top_box">';
				$li .= '	<div class="img_box">';
				$li .= 			'<img src="'.$img_path.'/'.$size[0]['w'].'x'.$size[0]['h'].'/'.$img_item[0]['file_name'].'" alt="'.$img_item[0]['alt'].'" />';				
				$li .= '	</div>';
				$li .= '</div>';
				$li .= '<div class="bottom_box">';
				$li .= '	<div class="img_box">';
				$li .= 			'<img src="'.$img_path.'/'.$size[1]['w'].'x'.$size[1]['h'].'/'.$img_item[1]['file_name'].'" alt="'.$img_item[1]['alt'].'" />';
				$li .= '	</div>';
				$li .= '	<div class="img_box">';
				$li .= 			'<img src="'.$img_path.'/'.$size[2]['w'].'x'.$size[2]['h'].'/'.$img_item[2]['file_name'].'" alt="'.$img_item[2]['alt'].'" />';
				$li .= '	</div>';
				$li .= '	<div class="img_box">';
				$li .= 			'<img src="'.$img_path.'/'.$size[3]['w'].'x'.$size[3]['h'].'/'.$img_item[3]['file_name'].'" alt="'.$img_item[3]['alt'].'" />';
				$li .= '	</div>';				
				$li .= '</div>';					
				
				
				break;
			case 5:
				$size[] = array("w"=>'108', "h"=>'198' );
				$size[] = array("w"=>'108', "h"=>'99' );				
				$size[] = array("w"=>'108', "h"=>'99' );				
				$size[] = array("w"=>'108', "h"=>'99' );				
				$size[] = array("w"=>'108', "h"=>'99' );								
				
				$li .= '<div class="left_box">';
				$li .= '	<div class="img_box">';
				$li .= 			'<img src="'.$img_path.'/'.$size[0]['w'].'x'.$size[0]['h'].'/'.$img_item[0]['file_name'].'" alt="'.$img_item[0]['alt'].'" />';				
				$li .= '	</div>';
				$li .= '</div>';
				$li .= '<div class="right_box">';
				$li .= '	<div class="img_box">';
				$li .= 			'<img src="'.$img_path.'/'.$size[1]['w'].'x'.$size[1]['h'].'/'.$img_item[1]['file_name'].'" alt="'.$img_item[1]['alt'].'" />';
				$li .= '	</div>';
				$li .= '	<div class="img_box">';
				$li .= 			'<img src="'.$img_path.'/'.$size[2]['w'].'x'.$size[2]['h'].'/'.$img_item[2]['file_name'].'" alt="'.$img_item[2]['alt'].'" />';
				$li .= '	</div>';
				$li .= '	<div class="img_box">';
				$li .= 			'<img src="'.$img_path.'/'.$size[3]['w'].'x'.$size[3]['h'].'/'.$img_item[3]['file_name'].'" alt="'.$img_item[3]['alt'].'" />';
				$li .= '	</div>';
				$li .= '	<div class="img_box">';
				$li .= 			'<img src="'.$img_path.'/'.$size[4]['w'].'x'.$size[4]['h'].'/'.$img_item[4]['file_name'].'" alt="'.$img_item[4]['alt'].'" />';
				$li .= '	</div>';				
				$li .= '</div>';	
				
				break;
			case 6:	
				$size[] = array("w"=>'108', "h"=>'99' );
				$size[] = array("w"=>'108', "h"=>'99' );				
				$size[] = array("w"=>'108', "h"=>'99' );				
				$size[] = array("w"=>'108', "h"=>'99' );				
				$size[] = array("w"=>'108', "h"=>'99' );				
				$size[] = array("w"=>'108', "h"=>'99' );								
				
				foreach($img_item as $index=>$item) {	
					if( $index == 6 ) break;
					$li .= '<div class="img_box">';
					$li .= '<img src="'.$img_path.'/'.$size[$index]['w'].'x'.$size[$index]['h'].'/'.$item['file_name'].'" alt="'.$item['alt'].'" />';				
					$li .= '</div>';						
				}
				
				break;				
			default:
				$size[] = array("w"=>'324', "h"=>'198' );				
				break;
				
		}
		$html = str_replace("//","/",$li);
if($_SERVER['REMOTE_ADDR'] == '49.254.140.140'  && $_SESSION['user_id'] == 'siha1997'){
#    echo '<pre>'; print_r($html); exit;
}			
		return $html;
		
	}
	
	
	########################################################################
	## 그리드형 갤러리에서 사용하는 이미지 목록.(사진 DB에서 사용하던 스킨 가져오면서 같이 옮김)
	########################################################################
	public function get_munti_thumb_mobile_new($sizew=480,$sizeh=480,$margin=0,$img_item=array(),$img_path='',$url='#none', $photo_count=0) {
		global $_SYSTEM;

		//$img_item = $img;
		//unset($img);
		
		$img_count = ( count($img_item) > 5 )?6:count($img_item);
		$li='';
		switch( $img_count ) {
			case 1:
				$size[] = array("w"=>'324', "h"=>'198' );		
				foreach($img_item as $index=>$item) {								
					$li .= '<div class="img_box">';
					$li .= '<img src="'.$img_path.$size[$index]['w'].'x'.$size[$index]['h'].'/'.$item['file_name'].'" alt="'.$item['alt'].'" />';				
					$li .= '</div>';	
				}
				
				break;
			case 2:
				$size[] = array("w"=>'162', "h"=>'198' );
				$size[] = array("w"=>'162', "h"=>'198' );
				
				foreach($img_item as $index=>$item) {				
					$li .= '<div class="img_box">';
					$li .= '<img src="'.$img_path.$size[$index]['w'].'x'.$size[$index]['h'].'/'.$item['file_name'].'" alt="'.$item['alt'].'" />';				
					$li .= '</div>';						
				}	
				
				break;
			case "3":
				$size[] = array("w"=>'162', "h"=>'108' );
				$size[] = array("w"=>'162', "h"=>'108' );
				$size[] = array("w"=>'324', "h"=>'189' );

				$li .= '<div class="top_box">';
				$li .= '	<div class="img_box">';				
				$li .= 			'<img src="'.$img_path.$size[0]['w'].'x'.$size[0]['h'].'/'.$img_item[0]['file_name'].'" alt="'.$img_item[0]['alt'].'" />';
				$li .= '	</div>';				
				$li .= '	<div class="img_box">';				
				$li .= 			'<img src="'.$img_path.$size[1]['w'].'x'.$size[1]['h'].'/'.$img_item[1]['file_name'].'" alt="'.$img_item[1]['alt'].'" />';
				$li .= '	</div>';				
				$li .= '</div>';
				$li .= '<div class="bottom_box">';
				$li .= '	<div class="img_box">';				
				$li .= 			'<img src="'.$img_path.$size[2]['w'].'x'.$size[2]['h'].'/'.$img_item[2]['file_name'].'" alt="'.$img_item[2]['alt'].'" />';
				$li .= '	</div>';				
				$li .= '</div>';
				break;
				
			case 4:
				$size[] = array("w"=>'324', "h"=>'99' );
				$size[] = array("w"=>'108', "h"=>'99' );
				$size[] = array("w"=>'108', "h"=>'99' );
				$size[] = array("w"=>'108', "h"=>'99' );				
				
				$li .= '<div class="top_box">';
				$li .= '	<div class="img_box">';
				$li .= 			'<img src="'.$img_path.$size[0]['w'].'x'.$size[0]['h'].'/'.$img_item[0]['file_name'].'" alt="'.$img_item[0]['alt'].'" />';				
				$li .= '	</div>';
				$li .= '</div>';
				$li .= '<div class="bottom_box">';
				$li .= '	<div class="img_box">';
				$li .= 			'<img src="'.$img_path.$size[1]['w'].'x'.$size[1]['h'].'/'.$img_item[1]['file_name'].'" alt="'.$img_item[1]['alt'].'" />';
				$li .= '	</div>';
				$li .= '	<div class="img_box">';
				$li .= 			'<img src="'.$img_path.$size[2]['w'].'x'.$size[2]['h'].'/'.$img_item[2]['file_name'].'" alt="'.$img_item[2]['alt'].'" />';
				$li .= '	</div>';
				$li .= '	<div class="img_box">';
				$li .= 			'<img src="'.$img_path.$size[3]['w'].'x'.$size[3]['h'].'/'.$img_item[3]['file_name'].'" alt="'.$img_item[3]['alt'].'" />';
				$li .= '	</div>';				
				$li .= '</div>';					
				
				
				break;
			case 5:
				$size[] = array("w"=>'108', "h"=>'198' );
				$size[] = array("w"=>'108', "h"=>'99' );				
				$size[] = array("w"=>'108', "h"=>'99' );				
				$size[] = array("w"=>'108', "h"=>'99' );				
				$size[] = array("w"=>'108', "h"=>'99' );								
				
				$li .= '<div class="left_box">';
				$li .= '	<div class="img_box">';
				$li .= 			'<img src="'.$img_path.$size[0]['w'].'x'.$size[0]['h'].'/'.$img_item[0]['file_name'].'" alt="'.$img_item[0]['alt'].'" />';				
				$li .= '	</div>';
				$li .= '</div>';
				$li .= '<div class="right_box">';
				$li .= '	<div class="img_box">';
				$li .= 			'<img src="'.$img_path.$size[1]['w'].'x'.$size[1]['h'].'/'.$img_item[1]['file_name'].'" alt="'.$img_item[1]['alt'].'" />';
				$li .= '	</div>';
				$li .= '	<div class="img_box">';
				$li .= 			'<img src="'.$img_path.$size[2]['w'].'x'.$size[2]['h'].'/'.$img_item[2]['file_name'].'" alt="'.$img_item[2]['alt'].'" />';
				$li .= '	</div>';
				$li .= '	<div class="img_box">';
				$li .= 			'<img src="'.$img_path.$size[3]['w'].'x'.$size[3]['h'].'/'.$img_item[3]['file_name'].'" alt="'.$img_item[3]['alt'].'" />';
				$li .= '	</div>';
				$li .= '	<div class="img_box">';
				$li .= 			'<img src="'.$img_path.$size[4]['w'].'x'.$size[4]['h'].'/'.$img_item[4]['file_name'].'" alt="'.$img_item[4]['alt'].'" />';
				$li .= '	</div>';				
				$li .= '</div>';	
				
				break;
			case 6:	
				$size[] = array("w"=>'108', "h"=>'99' );
				$size[] = array("w"=>'108', "h"=>'99' );				
				$size[] = array("w"=>'108', "h"=>'99' );				
				$size[] = array("w"=>'108', "h"=>'99' );				
				$size[] = array("w"=>'108', "h"=>'99' );				
				$size[] = array("w"=>'108', "h"=>'99' );								
				
				foreach($img_item as $index=>$item) {	
					if( $index == 6 ) break;
					$li .= '<div class="img_box">';
					$li .= '<img src="'.$img_path.$size[$index]['w'].'x'.$size[$index]['h'].'/'.$item['file_name'].'" alt="'.$item['alt'].'" />';				
					$li .= '</div>';						
				}
				
				break;				
			default:
				$size[] = array("w"=>'324', "h"=>'198' );				
				break;
				
		}
		$html = $li;
		
		return $html;
		
	}

	## 호스트별 사이트명 목록 가져오기
	public function get_site_host_list(){
		global $mysql;
		$return = array();
		
		$query  = ' SELECT site_name
						,  left(`site_domain`,INSTR(`site_domain`,".g")-1) as site_code  
					FROM _site WHERE site_option = "open" ';
		
		$result = $mysql->query($query);		
		while($data = $mysql->fetch_array($result) ){
			$return[$data['site_code']] = $data['site_name'];
		}

		return $return;		
	}
	
	public function hot_articles($table_name, $board_id, $limit = '3', $order='hot' ) {
		global $mysql;
		##최근 한달.
		$limit_date = date('Y-m-d 00:00:00', strtotime("-1 month") );
		
		$query = $where = '';
		$query_view_permission[] = ' allow <> "n" ';
		$query_view_permission[] = ' `open` <> "n" ';
		$query_view_permission[] = ' del = "n" ';
		$query_view_permission[] = ' `level` = 0 ';
		$query_view_permission[] = ' `reg_date` > "'.$limit_date.'" ' ;
		
		$query_order = ' ';			

		
		$query  = ' SELECT b.visit_cnt, c.*, a.title, a.* FROM ';
		$query .= ' ( '; 
		$query .= ' SELECT title, contents, reg_id, reg_date, reg_name, idx, board_id FROM '.$table_name.' WHERE board_id = "'.$board_id.'" '.(!empty($query_view_permission) ? 'AND '.implode(' AND ',$query_view_permission) : '').' ';
		$query .= ' ) as a ';
		$query .= ' LEFT JOIN ( SELECT * FROM _visit_info WHERE `table_name` = "'.$table_name.'" )  AS b ';
		$query .= ' ON a.idx = b.table_idx ';
		$query .= ' LEFT JOIN  ';
		$query .= ' 	(  ';
		$query .= ' 	SELECT idx AS img_idx, file_type AS img_type, original_name AS ori_name, re_name, file_path AS img_path, table_idx ';
		$query .= ' 			, file_size AS img_size ,seq AS img_seq, download AS img_download, title AS img_title ';
		$query .= ' 			FROM _file_info ';
		$query .= ' 			WHERE  file_type = "photo" AND table_name = "'.$table_name.'" AND board_id = "'.$board_id.'" GROUP BY table_idx';
		$query .= ' 	) AS c ';
		$query .= ' ON a.idx = c.table_idx  ';		
		$query .= ' ORDER BY b.visit_cnt DESC ';
		$query .= ' limit 0, '.$limit;

if( $_SERVER['REMOTE_ADDR'] == "49.254.140.140" && $_SESSION['user_id'] == "jini0808" ){
//echo $query;exit;
}			
		$data_list = array();
		$result = $mysql->query($query);
		while($self = $mysql->fetch_array($result)){
			if(!$self)continue;
			//$self['module_root'] = $this->module_root;
			$self['module_path'] = $this->module_config['module_path'];
			$self['img_module_path'] = str_replace($this->module_root,"",$this->module_config['module_path']);
			$self['log'] = '';
			$data_list[] = $self; 
		}
		return $data_list;
	}
	
	public function list_sorting($board_id, $table_name, $idxs) {
		global $mysql;

		$result  = false;

		if(empty($board_id)) return false;
	
		$list_idxs = explode(",",$idxs);
	
		if( count($list_idxs) > 0 ){
			## 이전 정렬값 초기화
			$query = sprintf('UPDATE %s SET seq = 0  WHERE board_id = "%s" ', $table_name, $board_id);
			$mysql->query($query);


			foreach( $list_idxs as $index=>$buffer ){
				$query = sprintf('UPDATE %s SET seq = %s  WHERE board_id = "%s" AND idx = "%s" ', $table_name, ($index+1), $board_id, $buffer);		
				$result = $mysql->query($query);	
			}		
		}
		
		
 		return $result ;
	}
	
	
	function week_time($table_name=NULL, $board_id) {
		global $mysql;

		$return = array();
		$table_name = empty($table_name) ? $this->module_config['table_name'] : $table_name;
		
		$query  = sprintf('SELECT week_time FROM %s WHERE board_id="%s" AND del="n" ', $table_name, $board_id);
		
		$result = $mysql->query($query);
		while($data = $mysql->fetch_array($result)) $return[] = $data['week_time'];

		return $return;		
	}
}
?>
