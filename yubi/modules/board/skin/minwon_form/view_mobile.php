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
    <span><?php echo date('Y.m.d ',strtotime($data['reg_date'])); ?> / <?php echo($data['writer_display'] == 'department' ? $data['depart_name'] : $data['reg_name']) ?></span></div>
  <div class="contbox">
		<?php	
		/*echo '<div class="viewinfo">';
		echo '<ul>';
		echo '<li><span class="subt">민원사무명</span>'.$data['title'].'</li>';
		echo '<li><span class="subt">민원내용</span>'.stripslashes($data['longtext_1']).'</li>';
		echo '<li><span class="subt">관계법령</span>'.stripslashes($data['longtext_2']).'</li>';
		echo '<li><span class="subt">구비서류</span>'.stripslashes($data['longtext_3']).'</li>';
		echo '<li><span class="subt">처리부서</span>'.$data['category_1'].'</li>';
		echo '<li><span class="subt">협의부서</span>'.$data['longtext_4'].'</li>';
		echo '<li><span class="subt">접수</span>'.$data['varchar_1'].'</li>';
		echo '<li><span class="subt">수수료</span>'.$data['varchar_2'].'</li>';
		echo '<li><span class="subt">처리기간</span>'.$data['varchar_3'].'</li>';
		echo '<li><span class="subt">기타사항</span>'.$data['varchar_4'].'</li>';
		echo '<li><span class="subt">흐름도</span>'.$data['longtext_5'].'</li>';
		echo '<li><span class="subt">기타참고사항</span>'.stripslashes($data['longtext_6']).'</li>';

		echo '</ul>';		
		echo '</div>';*/
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
			## -------------- 콘텐츠
			//echo $contents; 	
		
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
  <!--<div class="board_btn_box align_right">
    <ul>
      <li class="board_btn_reply"><a href="#none">답변하기</a></li>
      <li class="board_btn_delete"><a href="#none">삭제</a></li>
      <li class="board_btn_modify"><a href="#none">수정</a></li>
      <li class="board_btn_list"><a href="#none">목록</a></li>
    </ul>
  </div>-->
  <?php /*
  <div class="comment_box">
    <form>
      <div class="comment_input_box"><strong class="title_box"><span class="icon"></span>댓글(12)</strong><!--숫자는 댓글 카운트수로-->
        <div class="comment_input_inner">
          <label for="comment_input">댓글은 실명인증후 작성하실 수 있습니다.</label>
          <textarea id="comment_input" name="comment_input"></textarea>
          <input type="button" id="comment_submit" name="comment_submit" value="댓글입력">
          <!--로그인전이면 댓글입력 글자대신 실명인증 글자나오게--></div>
      </div>
    </form>
    <div class="comment_list">
      <div class="comment_item">
        <div class="title_box"><span class="name">홍○○</span><span class="date">2019-11-18 15:36</span><a href="#none" class="comment_modify">수정</a></div>
        <p>대방 건강댄스 프로그램이 아파트 안에서 할수 있어 낮에 아이를 돌보는 엄마들 직장인들 운동이 하고 싶지만 멀어서 못 가시는 분들을 위해 운동할 수 있는 좋은 기회입니다. 임경자 선생님도 다양한 프로그램으로 수업을 진행해주셔서 즐겁게 운동을 하고 있고 참여도가 높습니다. 2020년에도 나주시에서 지우너을 해주시기를 희망합니다.</p>
      </div>
      <div class="comment_item">
        <div class="title_box"><span class="name">홍○○</span><span class="date">2019-11-18 15:36</span></div>
        <p>대방 건강댄스 프로그램이 아파트 안에서 할수 있어 낮에 아이를 돌보는 엄마들 직장인들 운동이 하고 싶지만 멀어서 못 가시는 분들을 위해 운동할 수 있는 좋은 기회입니다. 임경자 선생님도 다양한 프로그램으로 수업을 진행해주셔서 즐겁게 운동을 하고 있고 참여도가 높습니다. 2020년에도 나주시에서 지우너을 해주시기를 희망합니다.</p>
      </div>
      <div class="comment_item">
        <div class="title_box"><span class="name">홍○○</span><span class="date">2019-11-18 15:36</span></div>
        <p>대방 건강댄스 프로그램이 아파트 안에서 할수 있어 낮에 아이를 돌보는 엄마들 직장인들 운동이 하고 싶지만 멀어서 못 가시는 분들을 위해 운동할 수 있는 좋은 기회입니다. 임경자 선생님도 다양한 프로그램으로 수업을 진행해주셔서 즐겁게 운동을 하고 있고 참여도가 높습니다. 2020년에도 나주시에서 지우너을 해주시기를 희망합니다.</p>
      </div>
    </div>
    <div class="board_btn_box align_center">
      <ul>
        <li class="board_btn_more"><a href="#none">더보기</a></li>
      </ul>
    </div>
  </div>

  <div class="board_view_pager"><a href="#none" class="prev_box"><span class="icon"></span>
    <div class="cont_box"><span>이전글</span><span class="title">지역 주도형 청년일자 LP가스 안전점검 사업체험형 청년</span></div>
    </a><a href="#none" class="next_box"><span class="icon"></span>
    <div class="cont_box"><span>다음글</span><span class="title">작성된 글이 없습니다.</span></div>
    </a></div>
  <div class="board_view_hot">
    <h3>인기 게시물</h3>
    <ul>
      <li><a href="#none">소규모 업체 HACCP 인증 설명회 개최 알림</a></li>
      <li><a href="#none">2019 청소년수련관 가을학기 프로그램 참가자 모집 안내</a></li>
      <li><a href="#none">2020년 밭작물공동경영체육성지원 사업 대상자 공모</a></li>
      <li><a href="#none">무안군 제3기 도시재생대학 수강생 모집</a></li>
    </ul>
  </div>
</div>

	
	
<!--div class="layor_view"-->
<div class="module_wrap"> <!-- =======컨텐츠는 content_wrap로 감싸고 모듈은 module_wrap 으로 감싸야합니다.=====  -->
  <div class="btn_close_layor btn_close_1 <?php if(empty($data['btoff'])){ echo "hidden";} ?>"><a href="">상세페이지 닫기</a></div>
  <!--div class="btn_close_layor btn_close_1"><a href="">상세페이지 닫기</a></div-->
  <div class="title_board">
    <h3 class="c0"><?php echo $data['title']; ?></h3>
    <?php
    ## 게시기간 표시
    if ( $data[ 'use_display_date' ] == 'true' ) {
      $str_display_date = '';
      if ( !empty( $data[ 'period_start' ] ) && !empty( $data[ 'period_end' ] ) ) {
        if ( $buffer[ 'period_end' ] < date( 'Y-m-d' ) ) {
          $str_display_date = '<span class="period_finish">마감</span>';
        } else {
          $str_display_date = '<span class="period_ing">진행</span>';
        }
      }
      $str_display_date = '<span class="period_date">' . $str_display_date . '<span class="period_date">(' . $data[ 'period_start' ] . ' ~ ' . $data[ 'period_end' ] . ')</spna> </span>';
    }
    ?>
    <p><span class="date"><?php echo date('Y.m.d ',strtotime($data['reg_date'])); ?></span><span class="name"><?php echo($data['writer_display'] == 'department' ? $data['depart_name'] : $data['reg_name']) ?></span><?php echo $str_display_date; ?></p>
    <?php if( count($data['file_list']) > 0 ){ ?>
    <div class="file_attach_board">
      <h5>첨부파일</h5>
      <ul class="file_attach_type2">
        <?php echo download_box_mobile($data['idx'], $data['file_list']);?>
      </ul>
    </div>
    <?php }?>
  </div>
  <div class="content_board">
    <div class="body">
      <?php
      ## 이미지 출력 부분 
      if ( !empty( $data[ 'file_list' ] ) ) {
        foreach ( $data[ 'file_list' ] as $file ) {
          $file_board_id = $_SYSTEM[ 'module_config' ][ 'use_tag' ] == 'true' ? $_SYSTEM[ 'module_config' ][ 'source_id' ] : $_SYSTEM[ 'module_config' ][ 'board_id' ];
          if ( $file[ 'file_type' ] == 'photo' ) {
            $photo_file = $_SYSTEM[ 'module_root' ] . '/_data' . $_SYSTEM[ 'module_config' ][ 'path' ] . '/' . $file_board_id . '/' . $file[ 're_name' ];
            $photo_size = getimagesize( $photo_file );
            if ( $photo_size[ 0 ] <= $_SYSTEM[ 'module_config' ][ 'contents_img_width_size' ] )$_SYSTEM[ 'module_config' ][ 'contents_img_width_size' ] = '';
            echo '<div class="photo_view"><img src="./ybmodule.file' . $_SYSTEM[ 'module_config' ][ 'path' ] . '/' . $file_board_id . ( !empty( $_SYSTEM[ 'module_config' ][ 'contents_img_width_size' ] ) ? '/' . $_SYSTEM[ 'module_config' ][ 'contents_img_width_size' ] . 'x1' : '' ) . '/' . $file[ 're_name' ] . '" alt="' . ( !empty( $file[ 'title' ] ) ? $file[ 'title' ] : $file[ 'original_name' ] ) . '" /></div>';
          }
        }
      }
      echo $data[ 'contents' ];

      ## 담당자 안내문
      if ( !empty( $data[ 'admin_comment' ] ) ) {
        echo '<div class="admin_notice"> ';
        echo '	<p class="title"><span class="icon"></span>담당자 안내글</p> ';
        echo '    <p>' . $data[ 'admin_comment' ] . '</p> ';
        echo '</div> ';

      }
      ## 버튼 영역.
      $img_url = '/images/common/board/temp';
      $url = $_SERVER[ 'PHP_SELF' ];
      $user_info = array();
      $user_info[ 'is_login' ] = empty( $_SYSTEM[ 'myinfo' ][ 'is_login' ] ) ? NULL : $_SYSTEM[ 'myinfo' ][ 'is_login' ];
      $user_info[ 'user_pin' ] = empty( $_SYSTEM[ 'myinfo' ][ 'my_pin' ] ) ? NULL : $_SYSTEM[ 'myinfo' ][ 'my_pin' ];
      $arr_data[ 'reg_pin' ] = empty( $data[ 'reg_pin' ] ) ? NULL : $data[ 'reg_pin' ];
      $arr_data[ 'del' ] = empty( $data[ 'del' ] ) ? NULL : $data[ 'del' ];
      $arr_data[ 'use_logoff_write' ] = empty( $data[ 'use_logoff_write' ] ) ? NULL : $data[ 'use_logoff_write' ];
      $arr_data[ 'use_reply' ] = empty( $data[ 'use_reply' ] ) ? 'none' : $data[ 'use_reply' ];
      echo print_button_mobile( 'view', $data[ 'permission' ], $data[ 'parameter' ], $url, $img_url, $user_info, $arr_data );
      ?>
    </div>
  </div>
*/ ?>  
  <?php
  ## 댓글
/*
  if ( $data[ 'use_comment' ] == 'true' )
    include_once( $data[ 'module_root' ] . '/_plugin/skin/comment_mobile.php' );
*/

  ## 버튼 영역.
  /*$img_url   = '/images/common/board/temp';
  $url       = $_SERVER['PHP_SELF'];
  $user_info = array();
  $user_info['is_login'] = empty($_SYSTEM['myinfo']['is_login']) ? NULL : $_SYSTEM['myinfo']['is_login'];
  $user_info['user_pin'] = empty($_SYSTEM['myinfo']['my_pin']) ? NULL : $_SYSTEM['myinfo']['my_pin'];
  $arr_data['reg_pin']   = empty($data['reg_pin']) ? NULL : $data['reg_pin'];
  $arr_data['del']       = empty($data['del']) ? NULL : $data['del'];
  $arr_data['use_logoff_write'] = empty($data['use_logoff_write']) ? NULL : $data['use_logoff_write'];
  $arr_data['use_reply'] = empty($data['use_reply']) ? 'none' : $data['use_reply'];
  echo print_button_mobile('view', $data['permission'], $data['parameter'], $url, $img_url, $user_info, $arr_data);*/

  ## 최근글
/*  if ( !empty( $data[ 'board_new_list' ] ) ) {
    echo '<div class="recent_writing"><p>최근글</p><ul>' . $data[ 'board_new_list' ] . '</ul></div>';
  }*/
/*
  ?>
  <div class="btn_close_layor btn_close_2 <?php if(empty($data['btoff'])){ echo "hidden";} ?>"><a href="">상세페이지 닫기</a></div>
  <!--div class="btn_close_layor btn_close_2"><a href="">상세페이지 닫기</a></div--> 
</div>
<!--/div--> 

<script name="import_jquery.ready">

	$("#module_content .layor_view .btn_close_layor a").on("click",function(){
		$(".layor_view").hide();
		return false;
	});		

</script>
 */ ?>