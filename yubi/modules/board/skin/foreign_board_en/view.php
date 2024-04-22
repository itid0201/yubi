<?php echo $data['debug']; // only test.. ?>
<?php

//2012-10-10 황재복 : 휴대폰 번호 자동 차단 방지
if($_SYSTEM['module_config']['use_phone_filter'] == 'false') {  
	$data['title']=preg_replace('/01([016789])[-~.[:space:]]([0-9]{3,4})[-~.[:space:]]([0-9]{4})/','01\1－\2－\3',$data['title']);
	$data['contents']=preg_replace('/01([016789])[-~.[:space:]]([0-9]{3,4})[-~.[:space:]]([0-9]{4})/','01\1－\2－\3',$data['contents']);
}


############################# contetns #############################
if( $data['html_tag'] == "a" ){
	$data['contents'] = unserialize(base64_decode($data['contents']) );
	$contents = get_html_contents( $data['contents'], $data['file_list'] );																									
}else{
	$temp[] = $data['contents'];
	//ob_clean();	print_r($temp);	exit;
	$contents = get_html_contents( $temp, $data['file_list'], NULL, $data);
}
############################# contetns #############################

?>


<div class="board_wrapper">
	<div class="module_view_box">
		<div class="view_titlebox">
			<h3><?php echo $data['title']; ?></h3>
			<dl>
				<dt class="text_hidden">Date</dt><dd><?php echo date('Y.m.d H:i',strtotime($data['reg_date'])); ?></dd>
				<dt>Views</dt><dd><?php echo $data['visit_cnt']; ?></dd>
				<dt><?php echo ($data['writer_display'] == 'department' ? 'Department' : 'Registrar')?></dt><dd><?php echo($data['writer_display'] == 'department' ? $data['depart_name'] : $data['reg_name']) ?></dd>
			</dl>
		</div>
		<div class="contbox">
			<?php  
			## -------------- 콘텐츠
			echo $contents; 					
			
			/*## -------------- 링크 연결 
			if( count( $data['linkbox'] ) > 0 ){
				foreach($data['linkbox'] as $buffer_link ){
					echo print_linkbox($buffer_link);
				}
			}*/
			?>			
			<?php
				if(count($data['reply']) > 0) {
					for($i=0;$i<count($data['reply']);$i++) {
						$reply = $data['reply'][$i];						
						
						echo '<div class="item_reply">';						
						echo '<div class="view_titlebox">';						
						echo '<h3 style="background: url(\'/images/common/board/contract_h4.gif\') no-repeat scroll 0 6px;">RE] '.$reply['title'].'</h3>';
						echo '<dl>';
						echo '	<dt>Writer</dt>';
						echo '	<dd>'.$reply['reg_name'].'</dd>';
						echo '	<dt>Date </dt>';
						echo '	<dd>'.date('Y.m.d H:i',strtotime($reply['reg_date'])).'</dd>';
						echo '</dl>';
						if($reply['reg_id'] == $_SYSTEM['myinfo']['user_id'] || $_SYSTEM['permission']['admin'] === true || $_SYSTEM['permission']['manage'] === true) {
							echo '<div class="reply_button_list ">
									<ul class="reply_btnbox">
										<li><a id="rebtn_modify" href="'.$_SERVER['PHP_SELF'].'?mode=modify&amp;idx='.$reply['idx'].'">Modify</a></li>
										<li><a id="rebtn_remove" href="'.$_SERVER['PHP_SELF'].'?mode=remove&amp;idx='.$reply['idx'].'">Delete</a></li>
									</ul>
								</div>';
						}						
						echo '</div>';
						
						echo '<div class="board_cont" style="min-height:100px;">';
						  if(!empty($reply['file_list'])) { /* 사진 보기 */
							 foreach($reply['file_list'] as $file1) {
								 if($file1['file_type']=='photo') {									
									echo '<div class="photo_view"><img src="./ybmodule.file'.$_SYSTEM['module_config']['path'].'/'.$_SYSTEM['module_config']['board_id'].'/600x1/'.$file1['re_name'].'" alt="'.(!empty($file['title'])?$file1['title']:$file1['original_name']).'" /></div>';	
								 }
							}
						  }

						echo '	<p>'.nl2br($reply['contents']).'</p>';
						echo '</div>';		
						echo '</div>';												
					}
					
				}
			
			//print_r( $data['file_list'] );
			## --------------------- 첨부파일 
			if( count($data['file_list']) > 0 ){
				
				$group_file = ( count($data['file_list']) > 0 )?'<a href="/ybscript.io/common/file_download?pkey='.$_SESSION['private_key'].'&file_type=all&idx='.$data['idx'].'&file='.implode("|",$data['file_idxs']).'" class="all_down">Full download(Zip)</a>':'<a href="#none" class="all_down none">Full download(Zip)</a>';
			?>
			<div class="file_viewbox">
				<div class="left_box">
					<strong>Attached File</strong>
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
		$print_button =  print_button_new_foreign('en','view', $data['permission'], $data['parameter'], $url, $img_url, $user_info, $arr_data);
		if( !empty($print_button ) ){
		echo '<!-- 버튼 START --><div class="board_btn_box align_right">'.$print_button.'</div><!-- 버튼 END -->';	
		}
		## --------------------- 
		

		## ---------------------  댓글
		if($data['use_comment'] == 'true') include_once($data['module_root'].'/_plugin/skin/comment.php');
		## --------------------- 

		## --------------------- 이전글 / 다음글
		//print_r( $data['nearby_articles'] );
		$data['use_pager'] = 'true';
		if( $data['use_pager'] == 'true' ){			
			//echo print_pager($data['nearby_articles'],$data['parameter'],$_SERVER['PHP_SELF']);	
			
			echo print_pager_foreign("en", $data['nearby_articles'],$data['parameter'],$_SERVER['PHP_SELF']);
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