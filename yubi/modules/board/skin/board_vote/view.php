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
				
				
				
				<?php
				##2021.7.5 김다정 여수시청요청 - 타기관소식일때 등록자 표시안되게 요청
				if($_SYSTEM['module_config']['board_id'] != 'www_other_news') { ?>
				
				<dt><?php echo ($data['writer_display'] == 'department' ? '등록부서' : '등록자')?></dt><dd><?php echo($data['writer_display'] == 'department' ? $data['depart_name'] : $data['reg_name']) ?></dd>
				
				<?php } ?>
			</dl>
		</div>
		<div class="contbox">
			<div class="module_write_box">		
				<div class="cont_write">
					<div class="write_box">
						<div class="module_t">생년월일</div>
						<div class="module_w">
							<?php echo $data['varchar_2'];?>
						</div>
					</div>
					<div class="write_box">
						<div class="module_t">이메일</div>
						<div class="module_w">
							<?php echo $data['varchar_3'];?>
						</div>
					</div>
					<div class="write_box module_theme">
						<div class="module_t">접수상태</div>
						<div class="module_w">							
							<select class="custom_select" name="set_process" data-idx="<?php echo $data['idx']; ?>">
								<option value="신청" <?php echo $data['process_1'] == '신청'?' selected="selected"':'';?>>신청</option>
								<option value="처리완료"<?php echo $data['process_1'] == '처리완료'?' selected="selected"':'';?>>처리완료</option>
							</select>
							
						</div>
					</div>
					<?php if( !empty($data['phone']) ){ ?>
					<div class="write_box">
						<div class="module_t">연락처</div>
						<div class="module_w">
							<?php echo $data['phone'];?>
						</div>
					</div>
					<?php } ?>
					<?php if( !empty($data['zipcode']) ){ ?>
					<div class="write_box">
						<div class="module_t">주소</div>
						<div class="module_w">
							<?php echo '('.$data['zipcode'].')'.$data['address_1'].' '.$data['address_2'];?>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
			
			<?php  
			
			## -------------- 콘텐츠            
			//echo $contents; 
			echo str_replace('< ','&lt;&nbsp;',$contents);
			
			/*## -------------- 링크 연결 
			if( count( $data['linkbox'] ) > 0 ){
				foreach($data['linkbox'] as $buffer_link ){
					echo print_linkbox($buffer_link);
				}
			}*/
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
<script>
	$(function(){
		$(document).on('change','select[name=set_process]',function(){
			console.log( '<?php echo $_SERVER['PHP_SELF']; ?>' );
			var idx = $(this).data("idx");
			var field = 'process_1';
			var process = $(this).val();
			
			var cfm = confirm("접수상태를 변경하시겠습니까?");
			if(cfm){
				// ajax 통신
				$.ajax({
					type : "POST",           
					url : '<?php echo $_SERVER['PHP_SELF']; ?>',      
					data : {mode:'charge_process',idx:idx, field:field, process:process}, 
					cache: false, 
					dataType: "json",				
					success : function(res){                     
						console.log(res);							
						alert(res.msg);
						if( res.code == "00" ){
							window.location.reload();
						}
					},
					error : function(XMLHttpRequest, textStatus, errorThrown){ 
						console.log( XMLHttpRequest );
					}
				});
			}
			
		});
	});
</script>
<script src="<?php echo $_SERVER['SELF']; ?>js/view.js?build=<?php echo SERIAL;?>" ></script>

<?php
## 관리자 탭 적용.
if($data['permission']['admin'] == true) include_once($data['module_path'].'/admin.php');		
?>