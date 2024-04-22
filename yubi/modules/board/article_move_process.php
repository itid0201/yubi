<?php
/******************************************
* 기능설명 : 게시물 이동시 각 모듈별로 필수로 세팅되어야 할 값들을 설정 해준다.
  class.module.php 의 admin_tools_article_move() 에서 include 되어진다.

* parameter 정의
  - idx : 이동된 게시물의 idx
  
  - $module_config : 이동된 모듈의 설정값 (cfg_module.xml) 값이 저장되어 있다.

  - $data : 원본글에 대한 데이터 값들이 저장되어 있다.

  - $files : 원본글에 대한 첨부파일 정보들이 저장되어 있다.

* return 정의
  $return['result'] = true / false;
  $return['mesg'] = '실패 메시지';
******************************************/

function article_move_update($idx, $module_config, $data, $file_list) {
	global $mysql;
	
	$return = array('result' => true, 'mesg' => '');
	$return['result'] = true;
	$update_data = array();

	## source_id 연계 확인
	if($module_config['use_tag'] == 'true') {
		if(!empty($module_config['source_id'])) $update_data['board_id'] = $module_config['source_id'];
	}

	## 갤러리등 대표이미지 필수 체크
	if($module_config['use_gallery_img'] == 'true') {
		$is_photo = false;
		foreach($file_list as $file) if($file['file_type'] == 'photo') $is_photo = true;
		if($is_photo == false) {
			$return['result'] = false;
			$return['mesg'] = '사진이 없습니다. 이동할 게시판에는 반드시 사진이 필요합니다.';
		}
	}

	## flv 게시판등 동영상 필수 체크
	if($module_config['use_upload_movie'] == 'true') {
		$is_movie = false;
		foreach($file_list as $file) if($file['file_type'] == 'movie') $is_movie = true;
		if($is_movie == false) {
			$return['result'] = false;
			$return['mesg'] = '동영상이 없습니다. 이동할 게시판에는 반드시 동영상이 필요합니다.';
		}
	}


	## db update.
	if(!empty($update_data)) {
		$query_fields = '';
		foreach($update_data as $field => $value) if(!is_null($value)) $query_fields .= (empty($query_fields) ? '' : ', ').$field.' = "'.$value.'" ';

		$query = sprintf('UPDATE %s SET %s WHERE idx = "%s"', $module_config['table_name'], $query_fields, $idx);
		$result = $mysql->query($query);
	}

	return $return;
}
?>