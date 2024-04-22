<?php echo $data['debug']; // only test.. ?>
<?php
//2012-10-10 황재복 : 휴대폰 번호 자동 차단 방지
if($_SYSTEM['module_config']['use_phone_filter'] == 'false') {  
	$data['title']=preg_replace('/01([016789])[-~.[:space:]]([0-9]{3,4})[-~.[:space:]]([0-9]{4})/','01\1－\2－\3',$data['title']);
	$data['contents']=preg_replace('/01([016789])[-~.[:space:]]([0-9]{3,4})[-~.[:space:]]([0-9]{4})/','01\1－\2－\3',$data['contents']);
}

//print_r( $data['linkbox'] );
############################# contetns #############################
if( $data['html_tag'] == "a" ){
	$data['contents'] = unserialize(base64_decode($data['contents']) );
	$contents = get_html_contents( $data['contents'], $data['file_list'] );																									
}else{
	$temp[] = $data['contents'];
	//ob_clean();	print_r($temp);	exit;
	$contents = get_html_contents( $temp, $data['file_list'], NULL, $data);
	
	$temp[] = $data['longtext_1'];
	$longtext_1 = get_html_contents( $temp, $data['longtext_1'], NULL, $data);
}
############################# contetns #############################
?>
<div class="board_wrapper">
	<div class="module_view_box">
		<div class="view_titlebox">
			<h3><?php echo $data['title']; ?></h3>
			<dl>
				<dt class="text_hidden">날짜</dt><dd><?php echo date('Y.m.d H:i',strtotime($data['reg_date'])); ?></dd>
				<dt>조회수</dt><dd><?php echo $data['visit_cnt']; ?></dd>
				<dt><?php echo ($data['writer_display'] == 'department' ? '등록부서' : '등록자')?></dt><dd><?php echo($data['writer_display'] == 'department' ? $data['depart_name'] : $data['reg_name']) ?></dd>
			</dl>
		</div>
		<div class="contbox">
			
			<?php  
	
			## 이미지 출력 부분 (코로나 일일상황보고 게시판에서는 첨부된 이미지도 본문에 썸네일로 나오도록 수정 요청)
			if($_SYSTEM['module_config']['board_id'] == 'COVID_19_today') {
				if(!empty($data['file_list'])) { 
					foreach($data['file_list'] as $file) {
						$file_board_id = $_SYSTEM['module_config']['use_tag'] == 'true' ? $_SYSTEM['module_config']['source_id'] : $_SYSTEM['module_config']['board_id'];
						if($file['file_type']=='photo') {
							$photo_file = $_SYSTEM['module_root'].'/_data'.$_SYSTEM['module_config']['path'].'/'.$file_board_id.'/'.$file['re_name'];
							$photo_size = getimagesize($photo_file);
							if($photo_size[0]<=$_SYSTEM['module_config']['contents_img_width_size']) $_SYSTEM['module_config']['contents_img_width_size']='';
							echo '<div class="photo_view"><img src="./ybmodule.file'.$_SYSTEM['module_config']['path'].'/'.$file_board_id.'/980x1/'.$file['re_name'].'" alt="'.(!empty($file['title'])?$file['title']:$file['original_name']).'" /></div>';				
						}
					}
				} 
			}
			?>
			
			
			<!--여기부터 수정 s-->
			<div class="viewbox">
				<div class="item_viewbox">
					<div class="item_list"><span class="tit">민원사무명</span><span class="cont"><?php echo $data['title']; ?></span></div>
					<div class="item_list"><span class="tit">민원내용</span><span class="cont"><?php echo nl2br($data['longtext_1']); ?></span></div>
					<div class="item_list"><span class="tit">관계법령</span><span class="cont"><?php echo nl2br($data['longtext_2']); ?></span></div>
					<div class="item_list"><span class="tit">구비서류</span><span class="cont"><?php echo nl2br($data['longtext_3']); ?></span></div>
					<div class="item_list"><span class="tit">처리부서</span><span class="cont"><?php echo $data['category_1']; ?></span></div>
					<div class="item_list"><span class="tit">협의부서</span><span class="cont"><?php echo nl2br($data['longtext_4']); ?></span></div>
					<div class="item_list"><span class="tit">접수</span><span class="cont"><?php echo $data['varchar_1']; ?></span></div>
					<div class="item_list"><span class="tit">수수료</span><span class="cont"><?php echo $data['varchar_2']; ?></span></div>
					<div class="item_list"><span class="tit">처리기간</span><span class="cont"><?php echo $data['varchar_3']; ?></span></div>
					<div class="item_list"><span class="tit">기타사항</span><span class="cont"><?php echo $data['varchar_4']; ?></span></div>
					<div class="item_list"><span class="tit">흐름도</span><span class="cont"><?php echo nl2br($data['longtext_5']); ?></span></div>
					<div class="item_list"><span class="tit">기타참고사항</span><span class="cont"><?php echo nl2br($data['longtext_6']); ?></span></div>
				</div>
			</div>
			<!--여기부터 수정 e-->

			<?php			
			//print_r( $data['file_list'] );
			## --------------------- 첨부파일 
			if( count($data['file_list']) > 0 ){
				 $group_file = ( count($data['file_list']) > 0 )?'<a href="/ybscript.io/common/file_download?pkey='.$_SESSION['private_key'].'&file_type=all&idx='.$data['idx'].'&file='.urlencode(implode("|",$data['file_idxs'])).'" class="all_down" title="파일전체('.count($data['file_list']).'개) 압축파일(zip)으로 다운로드(새창)">전체(Zip)다운로드</a>':'<a href="#none" class="all_down none" title="전체다운로드 없음">전체(Zip)다운로드</a>';
			?>
			<div class="file_viewbox">
				<div class="left_box">
					<strong>첨부파일</strong>
					<?php echo $group_file; ?>					
				</div>
				<div class="right_box">
					<?php echo download_box_new("view", $data['idx'], $data['file_list']); ?>
				</div>
			</div>
			<?php 	
			}
			## ---------------------			
			
			## ---------------------공공누리 출력 
			## 테스트로  포항아이피에서  $data['kogl_type'] 값을 임시로 넣어둠.(1~5)
			//if( $_SERVER['REMOTE_ADDR'] == "49.254.140.140" ){ $data['kogl_type'] = "4"; }			
			if( $data['use_open_type'] == 'true'  &&  !empty($data['kogl_type']) ){ 		
				echo '<div class="open_viewbox">';
				echo print_koglType_kogl($data);	
				echo '</div>';
			}
			## ---------------------

			## --------------------- view_tag 
			if( $data['use_view_tag'] == 'true' ){			
				echo print_view_tag();
			}
			## ---------------------
			?>			
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



		## --------------------- 버튼 : start
		$print_button =  print_button_new('view', $data['permission'], $data['parameter'], $url, $img_url, $user_info, $arr_data);
		if( !empty($print_button ) ){
		echo '<!-- 버튼 START --><div class="board_btn_box align_right">'.$print_button.'</div><!-- 버튼 END -->';	
		}
		## --------------------- 
		

		## ---------------------  댓글
		if($data['use_comment'] == 'true') include_once($data['module_root'].'/_plugin/skin/comment_2020.php');
		## --------------------- 

		## --------------------- 이전글 / 다음글
		//print_r( $data['nearby_articles'] );
		$data['use_pager'] = 'true';
		if( $data['use_pager'] == 'true' ){			
			echo print_pager($data['nearby_articles'],$data['parameter'],$_SERVER['PHP_SELF']);			
		}
		## ---------------------

		## --------------------- 인기 게시물 
		if( $data['user_list_hot'] == 'true' ){			
			echo print_view_hot($data['hot_articles_data']);
		}
		## ---------------------
		
		?>		
	</div>
</div>
<?php


## 관리자 탭 적용.
if($data['permission']['admin'] == true) include_once($data['module_path'].'/admin.php');		
?>