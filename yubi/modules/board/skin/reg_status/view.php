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
			<h3><?php echo $data['contents']; ?></h3>
			<dl>
				<dt class="text_hidden">날짜</dt><dd><?php
				//20210114 김다정 나주도시재생 보도자료 reg_date 출력 수정
				echo ($_SYSTEM['module_config']['board_id'] == 'njursc_newsrelease'?date("Y-m-d", strtotime($data['varchar_1'])):date('Y.m.d ',strtotime($data['reg_date']))); ?></dd>
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
	
            
            ##나주청년센터 금융상담 게시판
            if($_SYSTEM['module_config']['board_id'] == 'najuyouth_request') {
                if($data['varchar_1'] == 'y') echo '<p class="counsel">상담예약</p>';    
            }
            if(($_SYSTEM['module_config']['board_id'] == 'najuyouth_request' || $_SYSTEM['module_config']['board_id'] == 'najuyouth_freeboard') && $_SESSION['admin'] == true  ) {
                if($_SESSION['admin'] == true ) echo '<p class="phone">'.$data['phone'].'</p>';
                if($_SESSION['admin'] == true ) echo '<p class="birth">'.$data['varchar_2'].'</p>';
                    
            }
			if($_SYSTEM['module_config']['board_id'] == 'www_newsletter_req') {
				if($_SYSTEM['permission']['manage'] == true || $_SYSTEM['permission']['admin'] == true) echo '<p class="phone">연락처 : '.$data['phone'].'</p>';
                if($_SYSTEM['permission']['manage'] == true || $_SYSTEM['permission']['admin'] == true) echo '<p class="birth">주소 : ('.$data['zipcode'].') '.$data['address_1'].' '.$data['address_2'].'</p>';
			}
			## -------------- 콘텐츠
            
			//echo $contents; 
			//echo str_replace('< ','&lt;&nbsp;',$contents);
					
			echo '<div class="viewinfo">';
			echo '<ul>';
			echo $data['title'] == '' ? '' : '<li><span class="subt">관리번호</span>'.$data['title'].'</li>';
			echo $data['depart_name'] == '' ? '' : '<li><span class="subt">담당부서</span>'.$data['depart_name'].'</li>';
			echo $data['varchar_1'] == '' ? '' : '<li><span class="subt">상위법령</span>'.$data['varchar_1'].'</li>';
			echo $data['varchar_2'] == '' ? '' : '<li><span class="subt">조례</span>'.$data['varchar_2'].'</li>';
			echo $data['varchar_3'] == '' ? '' : '<li><span class="subt">규칙</span>'.$data['varchar_3'].'</li>';
			echo $data['varchar_4'] == '' ? '' : '<li><span class="subt">훈령</span>'.$data['varchar_4'].'</li>';
			echo $data['varchar_5'] == '' ? '' : '<li><span class="subt">예규</span>'.$data['varchar_5'].'</li>';
			echo $data['varchar_6'] == '' ? '' : '<li><span class="subt">고시등</span>'.$data['varchar_6'].'</li>';
			echo $data['varchar_7'] == '' ? '' : '<li><span class="subt">유형</span>'.$data['varchar_7'].'</li>';
			echo $data['varchar_8'] == '' ? '' : '<li><span class="subt">등록사유</span>'.$data['varchar_8'].'</li>';
			echo $data['varchar_9'] == '' ? '' : '<li><span class="subt">법령공표일</span>'.$data['varchar_9'].'</li>';
			echo $data['varchar_10'] == '' ? '' : '<li><span class="subt">규칙</span>'.$data['varchar_10'].'</li>';
			echo $data['varchar_11'] == '' ? '' : '<li><span class="subt">존속기한</span>'.$data['varchar_11'].'</li>';
			echo $data['varchar_12'] == '' ? '' : '<li><span class="subt">규제시행/폐지일</span>'.$data['varchar_12'].'</li>';
			echo $data['contents_en'] == '' ? '' : '<li><span class="subt">규제목적</span>'.$data['contents_en'].'</li>';
			echo $data['contents_jp'] == '' ? '' : '<li><span class="subt">규제내용</span>'.$data['contents_jp'].'</li>';
			echo $data['contents_cn'] == '' ? '' : '<li><span class="subt">등록후변동결과</span>'.$data['contents_cn'].'</li>';
					
			echo '</ul>';		
			echo '</div>';

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
						
						echo '<div class="item_reply view_reply">';						
						echo '<div class="view_titlebox">';						
						echo '<h3>RE] '.$reply['title'].'</h3>';
						echo '<dl>';
						echo '	<dt>작성자</dt>';
						echo '	<dd>'.$reply['reg_name'].'</dd>';
						echo '	<dt>작성일</dt>';
						echo '	<dd>'.date('Y.m.d H:i',strtotime($reply['reg_date'])).'</dd>';
						echo '</dl>';
						if($reply['reg_id'] == $_SYSTEM['myinfo']['user_id'] || $_SYSTEM['permission']['admin'] === true || $_SYSTEM['permission']['manage'] === true) {
							echo '<div class="reply_btn_box ">
									<ul>
										<li><a id="rebtn_modify_'.$reply['idx'].'" class="rebtn_modify" href="#none" data-idx="'.$reply['idx'].'">수정</a></li>
										<li><a id="rebtn_remove_'.$reply['idx'].'" class="rebtn_remove" href="'.$_SERVER['PHP_SELF'].'?mode=remove&amp;idx='.$reply['idx'].'">삭제</a></li>
									</ul>
								</div>';
						}						
						echo '</div>';
						
						echo '<div class="board_cont">';
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
		if($data['use_comment'] == 'true') include_once($data['module_root'].'/_plugin/skin/comment.php');
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
<script src="<?php echo $_SERVER['SELF']; ?>js/view.js?build=<?php echo SERIAL;?>" ></script>
<?php
## 관리자 탭 적용.
if($data['permission']['admin'] == true) include_once($data['module_path'].'/admin.php');		
?>