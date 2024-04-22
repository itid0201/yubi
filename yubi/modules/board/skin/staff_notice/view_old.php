<?php echo $data['debug']; // only test.. ?>
<?php
//2012-10-10 황재복 : 휴대폰 번호 자동 차단 방지
if($_SYSTEM['module_config']['use_phone_filter'] == 'false') {  
	$data['title']=preg_replace('/01([016789])[-~.[:space:]]([0-9]{3,4})[-~.[:space:]]([0-9]{4})/','01\1－\2－\3',$data['title']);
	$data['contents']=preg_replace('/01([016789])[-~.[:space:]]([0-9]{3,4})[-~.[:space:]]([0-9]{4})/','01\1－\2－\3',$data['contents']);
}

?>
<div id="board_basic_view">
<div class="news_tit">
  <h3><?php echo $data['title']; ?></h3>
  <dl>
  	<dt>작성일</dt>
    <dd><?php echo date('Y.m.d H:i',strtotime($data['reg_date'])); ?></dd>
    <dt><?php echo ($data['writer_display'] == 'department' ? '등록부서' : '등록자')?></dt>
    <dd><?php echo($data['writer_display'] == 'department' ? $data['depart_name'] : $data['reg_name']) ?></dd> 
    <dt>조회수</dt>
    <dd><?php echo $data['visit_cnt']; ?></dd>
  </dl>
</div>

<?php
if(!empty($data['file_list'])) { //첨부파일기능 수정 : 오경우 (20120125)?>
<div class="file_attach ">
	<h5>첨부파일<span>(<strong><?php echo count($data['file_list']); ?></strong>)</span></h5>
	
		  <ul>
		    <?php download_box_tour($data['idx'], $data['file_list']);?>
		  </ul>
		
</div>
<?php 
} // if end 첨부파일
?>
<div class="board_cont">
  <?php 
	## 2017.03.23 서희진 추가 : 태그형 게시판일떄는 board_id 값을 source_id 값으로 변경해야한다.
	if( $_SYSTEM['module_config']['use_tag'] == 'true' ){
		$board_id = $_SYSTEM['module_config']['source_id'];
	}else{
		$board_id = $_SYSTEM['module_config']['board_id'];
	}
	
	## 이미지 출력 부분 
	if(!empty($data['file_list'])) { 
		foreach($data['file_list'] as $file) {
			if($file['file_type']=='photo') {
				$photo_file = $_SYSTEM['module_root'].'/_data'.$_SYSTEM['module_config']['path'].'/'.$_SYSTEM['module_config']['board_id'].'/'.$file['re_name'];
				$photo_size = getimagesize($photo_file);
				if($photo_size[0]<=$_SYSTEM['module_config']['contents_img_width_size']) $_SYSTEM['module_config']['contents_img_width_size']='';
				echo '<div class="photo_view"><img src="./ybmodule.file'.$_SYSTEM['module_config']['path'].'/'.$_SYSTEM['module_config']['board_id'].(!empty($_SYSTEM['module_config']['contents_img_width_size'])?'/'.$_SYSTEM['module_config']['contents_img_width_size'].'x1':'').'/'.$file['re_name'].'" alt="'.(!empty($file['title'])?$file['title']:$file['original_name']).'" /></div>';				
			}
		}
	} 
  ?>
  <p>
  <?php  echo $data['contents']; ?>
  </p>
  <?php  
  ## 담당자 안내문
  if(!empty($data['admin_comment'])) {
	echo '<dl class="admin_comment"><dt><img src="/images/common/img_notice.gif" alt="담당자 안내문" /> </dt>';
	echo '<dd> '.$data['admin_comment'].' </dd>';
	echo '</dl>';
  }
  ?>

</div>
</div>

<?php
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
echo print_button('view', $data['permission'], $data['parameter'], $url, $img_url, $user_info, $arr_data);

if($data['use_comment'] == 'true') include_once($data['module_root'].'/_plugin/skin/comment.php');

if($data['permission']['admin'] == true) include_once($data['module_path'].'/admin.php');
?>
