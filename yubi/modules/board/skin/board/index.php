<?php
## 스킨에서는 system 변수를 사용해서는 안된다.
## 모든 설정은 모듈에서 해주어야 한다.
## 모듈에서 $ARTICLES에 세팅한 값이 스킨에서 $data로 넘어온다.
## ** 실제 서버와 분리될 경우 아래의 include 파일 경로 정보는 module에서 넘어와서는 안되고 스킨쪽에서 자체 처리(세팅)되어야한다.
## ** 이와 관련하여 module에서는 skin 정보(skin_style)만 넘어오면 된다.

//echo $data['mode'] ; exit;
## 2012.08.29 게시물 조회수 증가 skin으로 이동.(강성수)
if($data['mode'] == 'view') {
	## include class.smartourpack.php
	$path_module_class = $_SYSTEM['module_root'].'/_plugin/module/smartourpack/class.smartourpack.php';
	if(file_exists($path_module_class)) include_once $path_module_class;
	
	## include class.module.php
	$path_module_class = $_SYSTEM['module_root'].'/class.module.php';
	if(file_exists($path_module_class)) include_once $path_module_class;		
	## 객체생성
	$class_module = new module();	
	## increment_visit() 실행
	$class_module->increment_visit($data['idx'], $data['table_name'], $data['visit_expire_term']);
	
}
$path_tour_function_file = $data['module_root'].'/_plugin/skin/user_function_smartour.php';
if(file_exists($path_tour_function_file)) include_once $path_tour_function_file;

//$path_function_file = $data['module_root'].'/_plugin/skin/user_function.php';
$path_function_file = $data['module_root'].'/_plugin/skin/user_function_2020.php';
$path_skin_file     = $data['module_path'].'/skin/'.$data['skin_style'].'/'.$data['skin_name'].($data['device']=='default' ? '' : '_'.$data['device']).'.php';
if(!file_exists($path_skin_file)) $path_skin_file = $data['module_path'].'/skin/'.$data['skin_style'].'/'.$data['skin_name'].'.php';
if(file_exists($path_function_file)) include_once $path_function_file;
if(file_exists($path_skin_file)) include_once $path_skin_file;
?>