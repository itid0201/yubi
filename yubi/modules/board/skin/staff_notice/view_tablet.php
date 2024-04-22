<?php echo $data['debug']; // only test.. ?>
<?php
//2012-10-10 황재복 : 휴대폰 번호 자동 차단 방지
if($_SYSTEM['module_config']['use_phone_filter'] == 'false') {  
	$data['title']=preg_replace('/01([016789])[-~.[:space:]]([0-9]{3,4})[-~.[:space:]]([0-9]{4})/','01\1－\2－\3',$data['title']);
	$data['contents']=preg_replace('/01([016789])[-~.[:space:]]([0-9]{3,4})[-~.[:space:]]([0-9]{4})/','01\1－\2－\3',$data['contents']);
}
?>
<div class="tit-box">
  <h3><?php echo $data['title']; ?></h3>
  <dl>
    <dt>작성일</dt>
    <dd><?php echo date('Y.m.d H:i',strtotime($data['reg_date'])); ?></dd>
    
    <dt><?php echo ($data['writer_display'] == 'department' ? '등록부서' : '등록자')?></dt>
    <dd><?php echo($data['writer_display'] == 'department' ? $data['depart_name'] : $data['reg_name']) ?></dd>
    
    <dt>조회수</dt>
    <dd><?php echo $data['visit_cnt'] ?> </dd>
    
  </dl>
</div>

<div class="set-box">
  <dl class="left">
    <?php if($data['use_category_1'] == 'true') { ?>
    <dt>분류</dt>
    <dd><?php echo $data['category_1']; ?></dd>
    <?php } 
	if($data['use_allow'] == 'true') {
	?>
    <dt>승인요청</dt>
    <dd><span class="btn_round_red"><em><?php echo ($data['allow'] == 'y' ? '승인' : '승인요청중'); ?></em></span></dd>
    <?php }
	if($data['use_lock'] == 'true') { 
	?>
    <dt>공개여부</dt>
    <dd><span class="btn_round_green"><em><?php echo ($data['open'] == 'y' ? '공개' : '비공개'); ?></em></span></dd>
    <?php } ?>
  </dl>
</div>
<div class="board_content">
  <div class="body"> 
  <?php // 20120214 오경우 : 관리자 코멘트 추가
  
  if(!empty($data['file_list'])) { /* 사진 보기 */
	 foreach($data['file_list'] as $file) {
		 if($file['file_type']=='photo') {
			echo '<div class="photo_view"><img src="./ybmodule.file'.$_SYSTEM['module_config']['path'].'/'.$_SYSTEM['module_config']['board_id'].'/300x1/'.$file['re_name'].'" alt="'.(!empty($file['description'])?$file['description']:$file['original_name']).'" />'.(!empty($file['description'])?'<span class="caption">'.$file['description'].'</span>':'').'</div>';	
		 }
	}
  }  
  
  echo $data['contents'];
  if(!empty($data['admin_comment'])) {
	echo '<dl class="admin_comment"><dt><img src="/images/common/img_notice.gif" alt="담당자 안내문" /> </dt>';
	echo '<dd> '.$data['admin_comment'].' </dd>';
	echo '</dl>';
	echo '<p class="boxbottom"><img src="/images/common/admin_noticebox02.gif" alt="" /></p>';
	
	/* 
	echo '<br /><br /><fieldset class="admin_comment"><legend> <img src="/images/common/img_notice.gif" alt="담당자 안내문" /> </legend>';
	echo $data['admin_comment'];
	echo '</fieldset>';
	*/
  }
  ?>
  </div>
</div>
<?php
if(!empty($data['file_list'])) { //첨부파일기능 수정 : 오경우 (20120125)?>
<div class="file_attach">
	<h5>첨부파일<span>(<strong><?php echo count($data['file_list']); ?></strong>)</span></h5>
		<div class="attach_thum">
		  <ul>
		    <?php download_box($data['idx'], $data['file_list']);?>
		  </ul>
		</div>
</div>
<?php 
} // if end 첨부파일

## 버튼 영역.
$img_url   = '/images/common/board/temp';
$url       = $_SERVER['PHP_SELF'];
$user_info = array();
$user_info['is_login'] = empty($_SYSTEM['myinfo']['is_login']) ? NULL : $_SYSTEM['myinfo']['is_login'];
$user_info['user_pin'] = empty($_SYSTEM['myinfo']['my_pin']) ? NULL : $_SYSTEM['myinfo']['my_pin'];
$arr_data['reg_pin']   = empty($data['reg_pin']) ? NULL : $data['reg_pin'];
$arr_data['del']       = empty($data['del']) ? NULL : $data['del'];
$arr_data['use_logoff_write'] = empty($data['use_logoff_write']) ? NULL : $data['use_logoff_write'];
$arr_data['use_reply'] = empty($data['use_reply']) ? 'none' : $data['use_reply'];
echo print_button_tour('view', $data['permission'], $data['parameter'], $url, $img_url, $user_info, $arr_data);

if($data['use_comment'] == 'true') include_once($data['module_root'].'/_plugin/skin/comment.php');

## 20140704 신윤복 페이지담당자 게시글 관리기능 제한.
if($data['permission']['admin'] == true && $_SYSTEM['permission']['manage'] != true) include_once($data['module_path'].'/admin.php');
//if($data['permission']['admin'] == true) include_once($data['module_path'].'/admin.php');
?>