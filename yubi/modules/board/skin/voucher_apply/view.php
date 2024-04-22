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
				<dt class="text_hidden">날짜</dt><dd><?php
				//20210114 김다정 나주도시재생 보도자료 reg_date 출력 수정
				echo ($_SYSTEM['module_config']['board_id'] == 'njursc_newsrelease'?date("Y-m-d", strtotime($data['varchar_1'])):date('Y.m.d ',strtotime($data['reg_date']))); ?></dd>
				<dt>조회수</dt><dd><?php echo $data['visit_cnt']; ?></dd>
				<dt><?php echo ($data['writer_display'] == 'department' ? '등록부서' : '등록자')?></dt><dd><?php echo($data['writer_display'] == 'department' ? $data['depart_name'] : $data['reg_name']) ?></dd>
			</dl>
		</div>
		<div class="contbox">
			<?php  
			## -------------- 콘텐츠            
			//echo $contents; 
			//echo str_replace('< ','&lt;&nbsp;',$contents);
			
			/*## -------------- 링크 연결 
			if( count( $data['linkbox'] ) > 0 ){
				foreach($data['linkbox'] as $buffer_link ){
					echo print_linkbox($buffer_link);
				}
			}*/
					
			echo '<div class="viewinfo">';
			echo '<ul>';
			echo $data['title'] == '' ? '' : '<li><span class="subt">상호명</span>'.$data['title'].'</li>';
			echo $data['reg_name'] == '' ? '' : '<li><span class="subt">등록자명</span>'.$data['reg_name'].'</li>';
			echo $data['reg_date'] == '' ? '' : '<li><span class="subt">등록일</span>'.$data['reg_date'].'</li>';
			/*echo $data['phone_1'] == '' ? '' : '<li><span class="subt">휴대전화</span>'.$data['phone_1'].'-'.$data['phone_2'].'-'.$data['phone_3'].'</li>';*/
			echo $data['phone_1'] == '' ? '' : '<li><span class="subt">휴대전화</span>'.$data['phone_1'].'-'.$data['phone_2'].'-'.$data['phone_3'].'</li>';
			echo $data['varchar_6'] == '' ? '' : '<li><span class="subt">신청상태 구분</span>'.$data['varchar_6'].'</li>';
			echo $data['varchar_7'] == '' ? '' : '<li><span class="subt">사업자등록번호</span>'.$data['varchar_7'].'</li>';
			echo $data['varchar_8'] == '' ? '' : '<li><span class="subt">업태</span>'.$data['varchar_8'].'</li>';
					
			echo $data['zipcode'] == '' ? '' : '<li><span class="subt">사업장 소재지</span>['.$data['zipcode'].'] '.$data['address_1'].' '.$data['address_2'].'</li>';
			echo $data['varchar_1'] == '' ? '' : '<li><span class="subt">본점소재지</span>['.$data['varchar_1'].'] '.$data['varchar_2'].' '.$data['varchar_3'].'</li>';
					
			echo $data['varchar_4'] == '' ? '' : '<li><span class="subt">생년월일</span>'.$data['varchar_4'].'</li>';
			echo $data['varchar_5'] == '' ? '' : '<li><span class="subt">매장 전화번호</span>'.$data['varchar_5'].'</li>';	
			echo '</ul>';		
			echo '</div>';
					
			?>			
			<?php
			## -------------- 답변글
			if(count($data['reply']) > 0) {
				echo print_reply($data['reply']);
			}
			
		//	print_r( $data['file_list'] );exit;
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
		echo '<div class="comment_box">';
		if($data['use_comment'] == 'true') include_once($data['module_root'].'/_plugin/skin/comment.php');
		echo '</div>';
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