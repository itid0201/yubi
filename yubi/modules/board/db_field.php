<?php
## 이 파일은 setup.php 파일과 config 파일과 더불어 모듈이 생성될 때 모듈매니저에서 자동 생성되는 파일이다.
## 우선 프로그램 테스트에서 사용될 필드만 정의해놓는다.


$db_field[] = array('fields_name'=>'depart_name', 'label_name'=>'등록부서', 'list_view'=>'1');
$db_field[] = array('fields_name'=>'depart_name_real', 'label_name'=>'실부서', 'list_view'=>'0');
$db_field[] = array('fields_name'=>'reg_pin', 'label_name'=>'고유키', 'list_view'=>'0');
$db_field[] = array('fields_name'=>'reg_id', 'label_name'=>'아이디', 'list_view'=>'1');
$db_field[] = array('fields_name'=>'reg_name', 'label_name'=>'등록자', 'list_view'=>'1');
$db_field[] = array('fields_name'=>'reg_name_real', 'label_name'=>'실명', 'list_view'=>'0');
$db_field[] = array('fields_name'=>'search_tag', 'label_name'=>'검색태그', 'list_view'=>'1');
$db_field[] = array('fields_name'=>'category_1', 'label_name'=>'분류', 'list_view'=>'1');
$db_field[] = array('fields_name'=>'process_1', 'label_name'=>'처리상태', 'list_view'=>'1');
$db_field[] = array('fields_name'=>'title', 'label_name'=>'제목', 'list_view'=>'1');
$db_field[] = array('fields_name'=>'contents', 'label_name'=>'내용', 'list_view'=>'1');
$db_field[] = array('fields_name'=>'master', 'label_name'=>'민원처리담당자', 'list_view'=>'1');

$db_field[] = array('fields_name'=>'reg_date', 'label_name'=>'등록일', 'list_view'=>'1');
$db_field[] = array('fields_name'=>'zipcode', 'label_name'=>'우편번호', 'list_view'=>'1');
$db_field[] = array('fields_name'=>'address_1', 'label_name'=>'주소', 'list_view'=>'1');
$db_field[] = array('fields_name'=>'address_2', 'label_name'=>'상세주소', 'list_view'=>'1');
$db_field[] = array('fields_name'=>'phone', 'label_name'=>'연락처', 'list_view'=>'1');
$db_field[] = array('fields_name'=>'phone1', 'label_name'=>'연락처 첫번쨰자리', 'list_view'=>'1');
$db_field[] = array('fields_name'=>'phone2', 'label_name'=>'연락처 중간자리', 'list_view'=>'1');
$db_field[] = array('fields_name'=>'phone3', 'label_name'=>'연락처 마지막자리', 'list_view'=>'1');

$db_field_list = serialize($db_field);
?>