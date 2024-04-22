<?php echo $data['debug']; // only test.. ?>
<?php

//2012-10-10 황재복 : 휴대폰 번호 자동 차단 방지
if ( $_SYSTEM[ 'module_config' ][ 'use_phone_filter' ] == 'false' ) {
  $data[ 'title' ] = preg_replace( '/01([016789])[-~.[:space:]]([0-9]{3,4})[-~.[:space:]]([0-9]{4})/', '01\1－\2－\3', $data[ 'title' ] );
  $data[ 'contents' ] = preg_replace( '/01([016789])[-~.[:space:]]([0-9]{3,4})[-~.[:space:]]([0-9]{4})/', '01\1－\2－\3', $data[ 'contents' ] );
}



############################# contetns #############################
if( $data['html_tag'] == "a" ){
	$data['contents'] = unserialize(base64_decode($data['contents']) );
	$contents = get_html_contents( $data['contents'], $data['file_list'] );																									
}else{
	$temp[] = nl2br($data['contents']);
	//ob_clean();	print_r($temp);	exit;
	$contents = get_html_contents( $temp, $data['file_list'], NULL, $data);
}
############################# contetns #############################

?>
<div class="module_view_box">
  <div class="view_titlebox">
    <h3><?php echo $data['title']; ?></h3>
    <span><?php //20210114 김다정 나주도시재생 보도자료 reg_date 수정
		echo ($_SYSTEM['module_config']['board_id'] == 'njursc_newsrelease'?date("Y-m-d", strtotime($data['varchar_1'])):date('Y.m.d ',strtotime($data['reg_date']))) ; ?> / <?php echo($data['writer_display'] == 'department' ? $data['depart_name'] : $data['reg_name']); ?></span></div>
	<input type="hidden" name="<?php echo $_SERVER['PHP_SELF']; ?>">
	
<?php		
// -------------------- 관리자 만 보기	
if($data['permission']['admin'] == true || $data['permission']['manage'] == true) {
	$phone = $data['phone'];
	$address = '['.$data['zipcode'].'] '.$data['address_1'].' '.$data['address_2'];
	$email = $data['email'];
?>		
<div class="admin_list">
	<ul class="basic">
		<li><span>전화번호</span> : <?php echo $phone; ?> </li>
		<li><span>주소</span> : <?php echo $address; ?> </li>
		<?php if(!empty($email)) { ?>
		<li><span>이메일</span> : <?php echo $email; ?> </li>
		<?php } ?>
	</ul>
</div>
<?php	
} // -------------------- 관리자 만 보기	
?>
	
  <div class="contbox">

      
    	<?php 
	
			## -------------- 콘텐츠
			echo $contents; 	
	  
	  		## -------------- 답변글
			if(count($data['reply']) > 0) {
				echo print_reply($data['reply']);
			}	
		
			## --------------------- 첨부파일 
			if( count($data['file_list']) > 0 ){
				
				$group_file = ( count($data['file_list']) > 0 )?'<a href="/ybscript.io/common/file_download?pkey='.$_SESSION['private_key'].'&file_type=all&idx='.$data['idx'].'&file='.implode("|",$data['file_idxs']).'" class="all_down">전체(Zip)다운로드</a>':'<a href="#none" class="all_down none">전체(Zip)다운로드</a>';
			?>
			<div class="file_viewbox">
			  <div class="top_box">
				<?php echo download_box_new("view", $data['idx'], $data['file_list']); ?>
			  </div>
			  <div class="bottom_box"><?php echo $group_file; ?></div>
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