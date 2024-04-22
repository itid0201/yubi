<?php
/****** 클레스 인터페이스 ****************************************
 * index.php에서 권한체크 부분은 필수 사항이다.
 * class method를 call 하기전에 반드시 사용자의 권한을 체크해야 한다.
 ******************************************************************/


## include class.smartourpack.php
$path_module_class = $_SYSTEM['module_root'] . '/_plugin/module/smartourpack/class.smartourpack.php';
if (file_exists($path_module_class)) include_once $path_module_class;

## include class.module.php
$path_module_class = $_SYSTEM['module_root'] . '/class.module.php';
if (file_exists($path_module_class)) include_once $path_module_class;

##230711권재영 알리고 문자 클래스 적용
$path_aligo_class = $_SYSTEM['system_root'] . '/system/api/aligo.php';
if (file_exists($path_aligo_class)) include_once $path_aligo_class;
$aligo = new aligo();

## include class.php
$path_class = $_SYSTEM['module_config']['module_path'] . '/class.php';
if (file_exists($path_class)) include_once $path_class;
else echo call::xml_error('101', '', $module->referer);

## module class declare.
if (empty($_SYSTEM['module_config']['board_id'])) call::xml_error('103', 'board_id가 없습니다.', $module->referer);
if (!isset($module)) $module = new board($_SYSTEM);

if ($module->module_config['ebook_use'] == 'true') {
  require_once($_SYSTEM['module_root'] . '/_plugin/setup/digitomi/class.digitomi.php');
  class_parents('digitomi');
  $module->load_plugin(new digitomi);
}

## mode setting.

$mode = $module->get_parameter('mode');
$mode = empty($mode) ? 'list' : $mode;

## sub_mode setting.
$sub_mode = $module->get_parameter('sub_mode');
if ($mode == 'write') {
  if (!(empty($sub_mode) || $sub_mode == 'write')) {
    echo call::xml_error('90', '', $module->referer);
  }
}


## action.
switch ($mode) {
  case 'agreeForm':  // 동의폼
    if ($module->permission['access'] == false) echo call::xml_error('201', '', $module->referer);
    else $module->agreeForm();
    break;

  case 'agree':  // 동의
    if ($module->permission['access'] == false) echo call::xml_error('201', '', $module->referer);
    else $module->agree();
    break;
  case 'search':
    if ($module->permission['access'] == false) echo call::xml_error('201', '', $module->referer);
    else $module->lineup_search();
    break;
  case 'intro':
    if ($module->permission['access'] == false) echo call::xml_error('201', '', $module->referer);
    else $module->intro();
    break;

  case 'all':
  case 'list_www':
  case 'list_mayor':
  case 'photo':  // 목록, 접근
  case 'list':  // 목록, 접근
    if ($module->permission['access'] == false) echo call::xml_error('201', '', $module->referer);
    else $module->lineup();
    break;
  case 'view':  // 보기
    if ($module->permission['view'] !== true) echo call::xml_error('201', '', $module->referer);
    else $module->view();
    break;

  case 'write':  // 쓰기폼
    if ($module->permission['write'] !== true) echo call::xml_error('201', '', $module->referer);
    else $module->write();
    break;

  case 'modify':  // 수정폼
    if ($module->permission['modify'] !== true) echo call::xml_error('201', '', $module->referer);
    else $module->write();
    break;

  case 'reply':  // 답변폼
    if ($_SYSTEM['module_config']['skin_style'] == 'story_accept' || $_SYSTEM['module_config']['skin_style'] == 'request_event') {
      if ($module->permission['reply'] !== true) echo call::xml_error('201', '', $module->referer);
      else $module->write_reply();
    } else {
      if ($module->permission['reply'] !== true) echo call::xml_error('201', '', $module->referer);
      else $module->write();
    }
    break;
  case 'reply_save':  // 답변저장
  case 'save':  // 저장
    if ($module->permission['write'] !== true) echo call::xml_error('201', '', $module->referer);
    else $module->save();

    break;
  case 'reply_change':  // 답변저장
  case 'change':  // 수정
    if ($module->permission['modify'] !== true) echo call::xml_error('201', '', $module->referer);
    else $module->save();
    break;

  case 'remove':  // 삭제
    if ($module->permission['remove'] !== true) echo call::xml_error('201', '', $module->referer);
    else $module->remove();
    break;


  //2012-04-10 황재복 : 복원기능 추가
  case 'restore':  // 복원
    if ($module->permission['remove'] !== true) echo call::xml_error('201', '', $module->referer);
    else $module->restore();
    break;

  case 'erase':  // 영구삭제 : 사이트 관리자만 가능
    if ($module->permission['admin'] !== true) echo call::xml_error('201', '', $module->referer);
    else $module->erase();
    break;

  case 'excel':  // 엑셀출력
    if ($module->permission['admin'] !== true) echo call::xml_error('201', '', $module->referer);
    else $module->print_excel();
    break;

  case 'yeosun_excel':  // 엑셀출력
    if ($module->permission['admin'] !== true) echo call::xml_error('201', '', $module->referer);
    else $module->print_yeosun_excel();
    break;

  case 'setting':  // 모듈관리 : 허용된 아이피에서 접속한 사이트 관리자만 가능
    if ($module->permission['admin'] !== true) {
      echo call::xml_error('201', '', $module->referer);
    } else {
      $module->setup();
    }
    break;
  case 'admin_remove':  // 관리자삭제
    if ($module->permission['remove'] !== true) echo call::xml_error('201', '', $module->referer);
    else $module->admin_remove();
    break;

  case 'delete_all':  // 관리자삭제
    if ($module->permission['remove'] !== true) echo call::xml_error('201', '', $module->referer);
    else $module->delete_all();
    break;
  /* ajax 이미지업로드 */
  case 'ajax_img_upload': //이미지업로드(ajax)
    if ($module->permission['write'] !== true) echo call::xml_error('201', '', $module->referer);
    else $module->ajaxImageUpload();
    break;

  /* ajax 이미지리스트 */
  case 'ajax_img_list': //이미지리스트(ajax)
    if ($module->permission['view'] !== true) echo call::xml_error('201', '', $module->referer);
    else $module->ajaxImageList();
    break;

  /* 파일 삭제 */
  case 'ajax_file_Delete':  //이미지리스트(ajax)
    if ($module->permission['write'] !== true) echo call::xml_error('201', '', $module->referer);
    else $module->ajaxfileDelete();
    break;

  case 'ajax_sorting':
    if ($module->permission['write'] !== true) echo call::xml_error('201', '', $module->referer);
    else $module->ajaxSorting();
    break;

  case 'private':
    if ($module->permission['access'] != true) echo call::xml_error('201', '', $module->referer);
    else $module->lineup_private();
    break;

  case 'charge_insert': ## ys_singo 담당지정
    if ($module->permission['admin'] != true) echo call::xml_error('201', '', $module->referer);
    else $module->charge_insert();
    break;

  case 'charge_process':
    if ($module->permission['admin'] != true) echo call::xml_error('201', '', $module->referer);
    else $module->charge_process();
    break;
  default:
    echo call::xml_error('202', '', $module->referer);
    break;


}

//exit;

?>