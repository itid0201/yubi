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
	$data['contents_en'] = unserialize(base64_decode($data['contents_en']) );	
	$contents_en = get_html_contents( $data['contents_en'], $data['file_list'] );	
}else{
	$temp[] = $data['contents'];
	$contents = get_html_contents( $temp, $data['file_list'], NULL, $data);
	$temp2[] = $data['contents_en'];
	$contents_en = get_html_contents( $temp2, $data['file_list'], NULL, $data);
}
############################# contetns #############################

?>


<div class="board_wrapper">
	<div class="module_view_box">
		<div class="view_titlebox">
			<dl>
				<dt>등록일자 : </dt><dd><?php
				//20210114 김다정 나주도시재생 보도자료 reg_date 출력 수정
				echo ($_SYSTEM['module_config']['board_id'] == 'njursc_newsrelease'?date("Y-m-d", strtotime($data['varchar_1'])):date('Y.m.d ',strtotime($data['reg_date']))); ?></dd>
				<dt>조회수</dt><dd><?php echo $data['visit_cnt']; ?></dd>
				<dt><?php echo ($data['writer_display'] == 'department' ? '등록부서' : '등록자')?> : </dt><dd><?php echo($data['writer_display'] == 'department' ? $data['depart_name'] : $data['reg_name']); ?></dd>
				<dt><?php echo '담당부서'; ?> : </dt><dd><?php echo $data['depart_name']; ?></dd>
			</dl>
		</div>
		<div class="contbox">
			
			
			<p class="view_board_tit01">의회발언</p>
			<?php /* 의회발언 */ echo str_replace('< ','&lt;&nbsp;',$contents); ?>
				
		<?php
				if(!empty($contents_en) ) {					
						echo '<div class="view_city">';						
						//echo '<div class="view_titlebox">';						
						echo '<p class="view_board_tit02">시입장</p>';													
						//echo '</div>';						
						echo '<div class="board_cont">';
						echo '	<p>'.nl2br(str_replace('< ','&lt;&nbsp;',$contents_en)).'</p>';
						echo '</div>';		
						echo '</div>';												
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