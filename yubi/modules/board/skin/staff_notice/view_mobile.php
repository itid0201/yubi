<?php echo $data['debug']; // only test.. ?>
<?php
//모달창 sns공유기능
$sns_url = 'http://www.gokseong.go.kr/'.$_SYSTEM['hostname'].$_SERVER['REQUEST_URI'];
$title = $_SYSTEM['menu_info']['title'];

//2012-10-10 황재복 : 휴대폰 번호 자동 차단 방지
if($_SYSTEM['module_config']['use_phone_filter'] == 'false') {  
	$data['title']=preg_replace('/01([016789])[-~.[:space:]]([0-9]{3,4})[-~.[:space:]]([0-9]{4})/','01\1－\2－\3',$data['title']);
	$data['contents']=preg_replace('/01([016789])[-~.[:space:]]([0-9]{3,4})[-~.[:space:]]([0-9]{4})/','01\1－\2－\3',$data['contents']);
}
?>
<!--div class="layor_view"-->
<div class="module_wrap">	<!-- =======컨텐츠는 content_wrap로 감싸고 모듈은 module_wrap 으로 감싸야합니다.=====  -->
 <div class="btn_close_layor btn_close_1 <?php if(empty($data['btoff'])){ echo "hidden";} ?>"><a href="">상세페이지 닫기</a></div>
 <!--div class="btn_close_layor btn_close_1"><a href="">상세페이지 닫기</a></div-->
  <div class="title_board">
  	<h3 class="c0"><?php echo $data['title']; ?></h3>
    <p><span class="date"><?php echo date('Y.m.d ',strtotime($data['reg_date'])); ?></span><span class="name"><?php echo($data['writer_display'] == 'department' ? $data['depart_name'] : $data['reg_name']) ?></span></p>
	<?php if( count($data['file_list']) > 0 ){ ?>
    <div class="file_attach_board">
		<h5>첨부파일</h5>
		<ul class="file_attach_type2">
		  <?php echo download_box_mobile($data['idx'], $data['file_list']);?>
		</ul>
  	</div>
	<?php }?>
    <div class="sns_board">
		<h5>SNS공유</h5>
		<div class="sns_type2">
            <ul>
                <li><a id="kakao-link-btn" href="javascript:sendLink('<?php echo $sns_url;?>')" class="kakaotalk">카카오톡</a></li>
                <li><a href="http://www.facebook.com/sharer.php?u=<?php echo $sns_url; ?>&amp;t=<?php echo $title; ?>" target="_blank" title="Facebook으로 내보내기" class="facebook">페이스북</a></li>
                <li><a href="http://twitter.com/home?status=<?php echo $title; ?>%20<?php echo $sns_url; ?>" target="_blank" title="Twitter로 내보내기" class="twitter">트위터</a></li>
                <li><a href="http://www.band.us/plugin/share?body=<?php echo $title; ?>%0A<?php echo urlencode($sns_url); ?>" target="_blank" title="밴드로 내보내기" class="band">네이버밴드</a></li>
                <li><a href="https://story.kakao.com/share?url=<?php echo urlencode($sns_url); ?>" target="_blank" title="카카오스토리로 내보내기" class="kakaostory">카카오스토리</a></li>
            </ul>
		</div>
  	</div>
  </div>
  <div class="content_board">
    <div class="body">
  <?php 
	## 이미지 출력 부분 
	if(!empty($data['file_list'])) { 
		foreach($data['file_list'] as $file) {
			if($file['file_type']=='photo') {
				$photo_file = $_SYSTEM['module_root'].'/_data'.$_SYSTEM['module_config']['path'].'/'.$_SYSTEM['module_config']['board_id'].'/'.$file['re_name'];
				$photo_size = getimagesize($photo_file);
				if($photo_size[0]<=$_SYSTEM['module_config']['contents_img_width_size_mobile']) $_SYSTEM['module_config']['contents_img_width_size_mobile']='';
				echo '<div class="photo_view"><img src="./ybmodule.file'.$_SYSTEM['module_config']['path'].'/'.$_SYSTEM['module_config']['board_id'].(!empty($_SYSTEM['module_config']['contents_img_width_size_mobile'])?'/'.$_SYSTEM['module_config']['contents_img_width_size_mobile'].'x1':'').'/'.$file['re_name'].'" alt="'.(!empty($file['title'])?$file['title']:$file['original_name']).'" /></div>';				
			}
		}
	} 
  	echo $data['contents']; 

		
	$prt_open = "";
	$open_text = array(
	array("type"=>"","text"=>"")
	, array("type"=>"제1유형","text"=>"출처표시")
	, array("type"=>"제2유형","text"=>"출처표시+상업적이용금지")
	, array("type"=>"제3유형","text"=>"출처표시+변경금지")
	, array("type"=>"제4유형","text"=>"출처표시+상업적이용금지+변경금지") 
	, array("type"=>"제5유형","text"=>"공공저작물 자유이용 허락표시 적용 안함") 
	);		
	
	$kogl_type = $data['kogl_type'];
	$open_kogl_url = 'http://www.kogl.or.kr/info/licenseType'.$kogl_type.'.do';
	$open_kogl_text = $open_text[$kogl_type]['type'].':'.$open_text[$kogl_type]['text'];
	$open_kogl_img = 'http://www.kogl.or.kr/open/web/images/images_2014/codetype/new_img_opentype0'.$kogl_type.'.png';
		
	if( $kogl_type == "5" ){
		$prt_open = "<div class='open_type'>
						<img src='http://www.kogl.or.kr/open/web/images/images_2014/codetype/img_opencode0_1.jpg'  alt='공공저작물 자유이용 허락표시 적용 안함'/>
						<a href='http://www.law.go.kr/lsInfoP.do?lsiSeq=148848&efYd=20140701#0000'target='_blank'> 
						공공저작물 자유이용 허락표시 적용 안함</a>
					</div>";
	}elseif( $kogl_type == "" ){
		$prt_open = "";
	}else{
		$prt_open = "<div class='open_type'>
						<div class='type_1 box_img'>
							<a href='".$open_kogl_url."' target='_blank'>
							<img alt='".$open_text[$kogl_type]['type']."' src='".$open_kogl_img."' />
							</a>
						</div>
						<div class='type_1_text box_text'> 본 저작물은 \"공공누리\" <a href='".$open_kogl_url."' target='_blank'>".$open_kogl_text."</a> 조건에 따라 이용 할 수 있습니다.</div>
					</div>";
	}
	echo $prt_open;	

		
		
	 ## 담당자 안내문
	  if(!empty($data['admin_comment'])) {
		echo '<div class="admin_notice"> ';
		echo '	<p class="title"><span class="icon"></span>담당자 안내글</p> ';
		echo '    <p>'.$data['admin_comment'].'</p> ';
		echo '</div> ';	

	  }   
?>    		
    </div>		
  </div>
 
<?php 	
## 댓글
if($data['use_comment'] == 'true') 
include_once($data['module_root'].'/_plugin/skin/comment_mobile.php');

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
echo print_button_mobile('view', $data['permission'], $data['parameter'], $url, $img_url, $user_info, $arr_data);

## 최근글
if(!empty($data['board_new_list'])) { 
echo 	'<div class="recent_writing"><p>최근글</p><ul>'.$data['board_new_list'].'</ul></div>';
}
	
?>
<!--	
<div class="btn_close_layor btn_close_2 <?php if(empty($data['btoff'])){ echo "hidden";} ?>"><a href="">상세페이지 닫기</a></div>
-->
<!--div class="btn_close_layor btn_close_2"><a href="">상세페이지 닫기</a></div-->
<!--	
</div>
-->
<!--/div-->
