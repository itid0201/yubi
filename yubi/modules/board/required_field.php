<?php
## 필수입력값을 확인하기 위한 파일 
## 모들 필드를 추가하려고 했으나, 굳이 자동 입력 필드는 나타내지 않았음. 추가할 필드가 있으면 추가 바람.

$required_field[] = array('fields_name'=>'title', 'label_name'=>'제목', 'required'=>'1');
$required_field[] = array('fields_name'=>'contents', 'label_name'=>'내용', 'required'=>'1');
$required_field[] = array('fields_name'=>'depart_name', 'label_name'=>'담당부서', 'required'=>'0');  // 위회발언에서 사용 
$required_field[] = array('fields_name'=>'zipcode', 'label_name'=>'우편번호', 'required'=>'0');
$required_field[] = array('fields_name'=>'address_1', 'label_name'=>'주소', 'required'=>'0');
$required_field[] = array('fields_name'=>'address_2', 'label_name'=>'상세주소', 'required'=>'0');
$required_field[] = array('fields_name'=>'phone', 'label_name'=>'연락처', 'required'=>'0');
$required_field[] = array('fields_name'=>'phone_1', 'label_name'=>'연락처 첫번째', 'required'=>'0');
$required_field[] = array('fields_name'=>'phone_2', 'label_name'=>'연락처 두번쨰', 'required'=>'0');
$required_field[] = array('fields_name'=>'phone_3', 'label_name'=>'연락처 세번째', 'required'=>'0');
$required_field[] = array('fields_name'=>'varcahr_1', 'label_name'=>'임시필드1', 'required'=>'0');
$required_field[] = array('fields_name'=>'varcahr_2', 'label_name'=>'임시필드2', 'required'=>'0');
$required_field[] = array('fields_name'=>'varcahr_3', 'label_name'=>'임시필드3', 'required'=>'0');
$required_field[] = array('fields_name'=>'varcahr_4', 'label_name'=>'임시필드4', 'required'=>'0');
$required_field[] = array('fields_name'=>'varcahr_5', 'label_name'=>'임시필드5', 'required'=>'0');
$required_field[] = array('fields_name'=>'varcahr_6', 'label_name'=>'임시필드6', 'required'=>'0');
$required_field[] = array('fields_name'=>'varcahr_7', 'label_name'=>'임시필드7', 'required'=>'0');
$required_field[] = array('fields_name'=>'varcahr_8', 'label_name'=>'임시필드8', 'required'=>'0');
$required_field[] = array('fields_name'=>'varcahr_9', 'label_name'=>'임시필드9', 'required'=>'0');
$required_field[] = array('fields_name'=>'varcahr_10', 'label_name'=>'임시필드10', 'required'=>'0');
$required_field[] = array('fields_name'=>'title_en', 'label_name'=>'제목(영문)', 'required'=>'0');
$required_field[] = array('fields_name'=>'title_jp', 'label_name'=>'제목(일문)', 'required'=>'0');
$required_field[] = array('fields_name'=>'title_cn', 'label_name'=>'제목(중문)', 'required'=>'0');
$required_field[] = array('fields_name'=>'contents_en', 'label_name'=>'내용(영문)', 'required'=>'0');
$required_field[] = array('fields_name'=>'contents_jp', 'label_name'=>'내용(일문)', 'required'=>'0');
$required_field[] = array('fields_name'=>'contents_cn', 'label_name'=>'내용(중문)', 'required'=>'0');
$required_field[] = array('fields_name'=>'link_url', 'label_name'=>'링크', 'required'=>'0');

//$required_field_list = serialize($required_field);
?>