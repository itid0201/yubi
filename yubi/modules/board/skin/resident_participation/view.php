<?php echo $data['debug']; // only test.. ?>
<?php

//if( $_SERVER['REMOTE_ADDR'] == "49.254.140.140" && $_SESSION['user_id'] == "wlswn4630" ){
//	echo '<pre>';
//	print_r($data);
//	exit;
//}


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
	$contents = get_html_contents( $temp, $data['file_list'], NULL, $data);
}
############################# contetns #############################
?>

<script type="text/javascript" src="/js/smart_editor/js/HuskyEZCreator.js"></script>
<link rel="stylesheet" type="text/css" href="/style/common/approval.css" />
<?php
	$stat_type_str = array(
	'reg'=>'신청',
	'accept'=>'접수',
	'pass'=>'전달',
	'next'=>'지정',
	'approval'=>'결재진행',
	'complete'=>'완료');

global $member;

if( $_SYSTEM['permission']['admin'] != true ) {
	if($_SESSION['user_id'] != $data['reg_id']) {
		$data['reg_name'] = preg_replace('/(.{1})(.*?)$/su','$1○○',$data['reg_name']);
	}
}


if($_SESSION['user_level']==1) {
	$_SYSTEM['permission']['manage']=true;
}

$damdang=false;

foreach($data['approval'] as $list) {
	foreach($list as $ar) {
		if($ar['user_id'] == $_SYSTEM['myinfo']['user_id'] && !empty($ar['user_id'])) {
			$damdang=true;
		}
	}
}
if($_SYSTEM['hostname']!='business' && $_SYSTEM['permission']['admin']==true) $damdang=true;

$reg_num = $data['varchar_1'];
?>
<!--
1. 결제라인 갯수 만큼
2. order by step 로 마지막 상태값을 가져옴.
3. 처리로그는 별도의 function 으로 처리함.
4. $status = array('신청','접수','부서지정','담당자지정','결재진행','완료'); //process2 필드를 이용함.
5. 부서지정일 경우 ->> 부서명 서무담당연락처
6. 담당지정일 경우 ->> 부서명 담당자연락처
-->
<div class="board_wrapper approval_box">
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

			## 23.04.12 이진주 :: 팀장의 경우 각 팀원의 민원 사항을 다 볼 수 있도록 -----------------
			$team_leader_arr = ["1836","1762","1870","1759","1756","1865"];
			$is_team_leader = false;
			if(in_array($_SESSION['dept_posid'], $team_leader_arr)) $is_team_leader = true;

			if($data['open']=='y'||($data['reg_pin']==$_SYSTEM['myinfo']['my_pin'])||($data['open']=='n'&&($_SYSTEM['permission']['manage']==true||$damdang==true))||$data['view_permission']=='true' || $is_team_leader) {
				echo $contents;
			} else {
				echo '- 비공개글입니다(조회 권한이 없음) - ';}


		  ## 담당자 안내문
		  if(!empty($data['admin_comment'])) {
			echo '<dl class="admin_comment"><dt></dt>';
			echo '<dd> '.$data['admin_comment'].' </dd>';
			echo '</dl>';
		  }

			## --------------------- 첨부파일
			if( count($data['file_list']) > 0 ){

				$group_file = ( count($data['file_list']) > 0 )?'<a href="/ybscript.io/common/file_download?pkey='.$_SESSION['private_key'].'&file_type=all&idx='.$data['idx'].'&file='.implode("|",$data['file_idxs']).'" class="all_down">전체(Zip)다운로드</a>':'<a href="#none" class="all_down none">전체(Zip)다운로드</a>';
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

		  ?>
		</div>
<!---------------------------------  내용 ---------------------------->
<input type="hidden" id="board_idx" value="<?php echo $data['idx']; ?>"  />
<!---------------------------------  내용 ---------------------------->

<?php if(($_SESSION['user_level']<=6 && $data['open']=='y')||($data['open']=='n'&&($_SYSTEM['permission']['manage']==true||$damdang==true))) { ?>
<div class="comment">
	<h3 class="approval_title">등록자 정보</h3>
    <div class="comment_input">
		<ul class="member_info">
			<li><span class="label">접수자</span><span class="txt"><?php echo $data['reg_name']; ?></span></li>
		    <?php
				if(!empty($data['phone_1']) || !empty($data['phone_2']) || !empty($data['phone_3'])) {
					$phone = $data['phone_1'].'-'.$data['phone_2'].'-'.$data['phone_3'];
				} else {
					if(!empty($data['phone'])) list($phone_1, $phone_2, $phone_3) = explode('-',$data['phone']);
				}
			?>
			<li><span class="label">연락처</span><span class="txt"><?php echo $phone; ?></span></li>
		    <li  class="la_add"><span class="label">주소</span><span class="txt"><?php echo $data['address_1']; ?>  <?php echo $data['address_2']; ?></span></li>
		</ul>
    </div>
</div>
<?php }
$status=empty($data['process_2'])?'1':$data['process_2'];
if($_SESSION['user_level']>6)  $status='8';
?>

<h3 class="approval_title">진행 상태</h3>
<div class="approval_board_top"></div>
<div class="approval_board <?php echo ($_SYSTEM['menu_info']['wide'] == true)?' w1200':'';?>">
	<?php
	if($status=='1' && $_SYSTEM['permission']['manage']==true) { ?>
    <form id="step_0">
		<div class="progress_info_list">
    	<input type="hidden" name="next_step" value="1" />
        <input type="hidden" name="board_id" value="<?php echo $_SYSTEM['menu_info']['value']['board_id']; ?>" />
        <input type="hidden" name="search_key" value="staff" />
        <input type="hidden" name="staff_myid" value="<?php echo $_SYSTEM['myinfo']['user_name'].'('.$_SYSTEM['myinfo']['user_id'].')'; ?>"  />
		<!--<p><input type="checkbox" value="true" name="sms_send" id="sms_send" checked="checked" /> <label for="sms_send">접수 처리되었음을 신청자(민원인)에게 SMS로 알림</label></p>
        <p>※ 접수가 완료되면 신청자는 더 이상 내용을 수정하거나 삭제 할 수 없습니다.</p>-->
		<p class="mat20"><a href="#none" class="btn_pro_a" id="accept_item"><span>접수처리</span></a></p>
		</div>
    </form>
	<?php }
	/*
	if(($status=='1' ||$status=='2')&& $_SYSTEM['permission']['manage']==false) {
		echo '<ul>';
		foreach($_SYSTEM['page_manager'] as $manager) {
			echo '<li>'.($status=='2'?'담당자 지정중':'접수처리 중').' : <span class="dept">'.$manager['dept'].'</span> <span class="name">'.$manager['name'].'</span> <span class="tel">(문의 : '.$manager['tel'].')</span></li>';
		}
		echo '</ul>';
	}
	*/
	?>
    <?php if($status=='2'&& $_SYSTEM['permission']['manage']==true) {
	?>
    <form id="step_1">
		<div class="progress_info_list">
			<input type="hidden" name="next_step" value="2" />
			<input type="hidden" name="board_id" value="<?php echo $_SYSTEM['menu_info']['value']['board_id']; ?>" />
			<!--<p><input type="checkbox" value="true" name="sms_send" id="sms_send" checked="checked" /> <label for="sms_send">담당 지정이 되었음을 신청자(민원인)에게 SMS로 알림</label></p>
			<p><input type="checkbox" value="true" name="sms_send2" id="sms_send2" checked="checked" /> <label for="sms_send2">배정된 담당자에게 SMS로 알림</label></p>
			<p><label for="deadline">답변 처리기한</label> <input type="text" value="<?php /*echo ($data['approval']['deadline']==''?date('Y-m-d',strtotime('+5 days')):$data['approval']['deadline']);*/?>" name="deadline" id="deadline" size="10" autocomplete="off" /><input type="button" id="deadline_change" value="변경" /></p>-->
			<p class="staff_list"><?php

			$myinfo = $member->get_member_info($_SYSTEM['myinfo']['user_id']);
			$dept_info = $member->get_department_info($myinfo['dept_id']);
			echo '<span class="blank">'.str_replace('|',' ',$dept_info['dept_path_title']).' '. $myinfo['user_name'].'님의 즉시 답변 : </span>';

			?> </p>
			<div class="answer_write"><textarea id="answer_contents"></textarea></div>
			<p class="btn_right"><a href="#none" class="btn_big" id="answer" title="<?php echo $data['approval']['list'][0]['idx']; ?>"><span>즉시 답변 완료</span></a></p>
			<div>
				<div class="search_box ori">
					<div class="bg"></div>
					<fieldset>
						<legend>검색</legend>
						<select class="search_key" name="search_key_staff_id">
							<option value="staff">직원</option>
							<!--<option value="department">부서</option>
							<option value="work">업무</option>-->
						</select>
						<input type="text" id="staff_id" name="staff_id" class="autocomplete" autocomplete="off" />
						<div id="text_div_staff_id" class="text_div"><div id="search_text_staff_id"  class="search_text search_staff"></div></div>
						<a href="#none" class="btn_sml append_staff"><span>담당 추가</span></a> <!--<a href="#none" class="btn_sml append_view"><span>보기권한추가</span></a>--> <a href="#none" class="btn_sml" id="sel_complete"><span>배정 완료</span></a><!--<a href="#none" class="btn_sml"><span>초기화</span></a>-->
					</fieldset>
				</div>
				<h4>답변 담당자</h4>
				<div class="staff_list" id="select_staff_list"><span class="blank">&quot;담당추가&quot;를 클릭하여 담당자를 배정해주세요.</span></div>
				<!--<h4>보기 권한자</h4>
				<div class="view_staff_list"><?php

					/*foreach(explode(';',$data['view_id']) as $view_id) {
						if(!empty($view_id)) {
							$user = $mysql->query_fetch('SELECT dept_id, user_name, user_id FROM `_member` WHERE user_id="'.$view_id.'"');
							$dept_info = $member->get_department_info($user['dept_id']);

							$dept_info['dept_path_title'] = str_replace('|',' ',$dept_info['dept_path_title']);
							$ret .= '<li><span class="dept" value="'.$user['dept_id'].'">'.$dept_info['dept_path_title'].'</span><span class="pos">'.$dept_info['dept_posname'].'</span><span class="name">'.$user['user_name'].'</span><a href="#none" class="remove view_id_remove" remove_id="'.$user['user_id'].'"><img src="/images/common/icon_delete_9.gif" alt="제외" /></a></li>';
						}
					}
						if(!empty($ret)) {
							echo '<ul class="list">'.$ret.'</ul>';
						} else {
							echo '<span class="blank">&quot;보기권한추가&quot;를 클릭하여 볼수있는 권한을 배정해주세요.</span>';
						}*/

				?></div>-->
			</div>
		</div>
    </form>
    <script type="text/javascript">
		var oEditors = [];
		var reg_num = <?php echo $reg_num; ?>;
		nhn.husky.EZCreator.createInIFrame({
			oAppRef: oEditors,
			elPlaceHolder: "answer_contents",
			sSkinURI: "/js/smart_editor/SEditorSkin.html",
			fCreator: "createSEditorInIFrame"
		});
		document.getElementById("answer").onclick = function () {
			oEditors.getById["answer_contents"].exec("UPDATE_IR_FIELD", []);
				$.post("/ybscript.io/approval/approval_proc",{
					board_idx : $("#board_idx").val(),
					idx : $("#answer").attr('title'),
					content : $("#answer_contents").val(),
					approval_line: '1',
					ret_url: $(location).attr('href').split("?")[0],
					reg_num: reg_num,
					operation:'reply_complete'
				}, function(req) {
					if(req=='OK') {
						$('body').fadeOut('fast', 0,function() {
							alert('답변 완료');
							location.reload();
						});
					} else {
						alert(req);
					}
				});
		}

	</script>
	<?php } ?>
    <?php if( $status == '3' || $status == '4' || (( $status == '8'|| $status == '7'|| $status == '6') && $_SESSION['user_level'] <= 6)) { ?>
    <form id="step_2">
		<div class="progress_info_list">
		<!--<p><input type="checkbox" value="true" name="sms_send" id="sms_send" checked="checked" /><label for="sms_send"> 답변 저장 후 완료 시 신청자(민원인)에게 <em>완료</em>되었음을 SMS로 알림</label></p>-->
		<?php if($_SYSTEM['permission']['manage']==true) { ?>
	        <input type="hidden" name="board_id" value="<?php echo $_SYSTEM['menu_info']['value']['board_id']; ?>" />
				<?php if($data['process_2'] != '7' || $data['process_2'] != '8') { ?>
				<!--<p><label for="deadline">답변 처리기한 변경</label><input type="text" value="<?php /*echo $data['approval']['deadline'];*/ ?>" name="deadline" id="deadline" size="10" autocomplete="off" /><input type="button" id="deadline_change" value="변경" /></p>-->
				<?php } ?>
			<?php } else { ?>
	        <!--<p><label for="deadline">답변 처리기한</label> : --><?php /*$limit = !empty($data['approval']['deadline'])?$data['approval']['deadline']:date('Y-m-d',strtotime($data['reg_date'].' + 5day'));
			echo '<span>'.$limit.'</span>'.(empty($data['approval']['deadline'])?' <span class="btn_round_red"><em>자동 지정</em></span>':'');
			if( $status != '8' && $status !='7' && $status!='6' ) {
				if(date('Y-m-d') > $limit) $gap = ceil((strtotime(date('Y-m-d')) - strtotime($limit)) / 86400); 
				else if(date('Y-m-d') < $limit) $gap = ceil((strtotime($limit) - strtotime(date('Y-m-d'))) / 86400);
				else $gap =  '0';
				
				if( $gap > 0) {
					echo ' ('.$gap.'일 남음)';
				} else if($gap < 0) {
					echo ' ('.$gap.'일 지남)';
				}
			}*/
			?>
			<!--</p>-->
	     <?php } ?>
         <?php

		 $script_id = 0;
		 foreach($data['approval']['list'] as $list) {
		 	$html='';
			$reply='';
		 	if($list['approval_line']=='0') {
				$proc = $data['approval']['approval_0'][0];
				$html0 ='<span class="mini_box" idx="'.$proc['idx'].'"><em>'.preg_replace('/^([^\|]+)\|(.*?)$/','$1',$proc['dept_path_title']).' '.$proc['user_name'].' '.$stat_type_str[$proc['process']].' ('.date('Y.m.d H:i',strtotime($proc['processing_date'])).')</em>
					</span>';
			} else {
		 		echo '<div class="process_list">';
				$html=$html0;
				foreach($data['approval']['approval_'.$list['approval_line']] as $proc) {
					if($proc['step']=='1'&&$proc['process']=='pass') {
						$str_proc='접수';
					} else {
						$str_proc=$stat_type_str[$proc['process']];
					}

					if($proc['modi'] == 'y') $str_proc = '수정';

					## 20141120 결제라인 제거
					/*
					$html .= (!empty($html)?'<span class="arrow_right"></span>':'').'<span class="mini_box" idx="'.$proc['idx'].'">
					<em>'.preg_replace('/.*?\|([^\|]+[실|과|소|읍|면|동]+)\|.*$/i','$1',$proc['dept_path_title']).' '.$proc['user_name'].' '.$str_proc.' ('.date('Y.m.d H:i',strtotime($proc['processing_date'])).')'.($str_proc!='접수'?'<a href="#none" title="'.$proc['idx'].'" class="remove_step"><img alt="제외" src="/images/common/icon_delete_9.gif" /></a>':'').'</em>
					</span>';
					*/
					$depart = explode('|',$proc['dept_path_title']);
					$depart = end($depart);

					//$html .= (!empty($html)?'<span class="arrow_right"></span>':'').'<span class="mini_box" idx="'.$proc['idx'].'"><em>'.preg_replace('/.*?\|([^\|]+[실|과|소|읍|면|동]+)\|.*$/i','$1',$proc['dept_path_title']).' '.$proc['user_name'].' '.$str_proc.' ('.date('Y.m.d H:i',strtotime($proc['processing_date'])).')'.'<a href="#none" title="'.$proc['idx'].'" class="remove_step"><img alt="제외" src="/images/common/icon_delete_9.gif" /></a></em></span>';
					$html .= (!empty($html)?'<span class="arrow_right"></span>':'').'<span class="mini_box" idx="'.$proc['idx'].'"><em>'.preg_replace('/.*?\|([^\|]+[실|과|소|읍|면|동]+)\|.*$/i','$1',$depart).' '.$proc['user_name'].' '.$str_proc.' ('.date('Y.m.d H:i',strtotime($proc['processing_date'])).')'.'<a href="#none" title="'.$proc['idx'].'" class="remove_step"><img alt="제외" src="/images/common/icon_delete_9.gif" /></a></em></span>';

					if($proc['process']=='complete') $reply = $proc['reply'];
				}
				//$reply = preg_replace('/<(p)([^>]*)>/i',"\n\n",$reply);
				//$reply = preg_replace('/<[^>]*>/i','',$reply);
				echo $html;
		        echo '</div>';
				if($_SYSTEM['permission']['manage']== true || $proc['user_id'] == $_SYSTEM['myinfo']['user_id']) {
					echo '<div class="answer_write manage_answer_write" answer_process="'.$proc['process'].'"><textarea cols="70" rows="10" id="answer_contents_'.$list['approval_line'].'">'.$reply.'</textarea></div>';
					echo '<div class="answer_write_text answer_complete" style="display: none">'.nl2br(htmlspecialchars_decode($reply)).'<div class="answer_edit"><span class="edit_btn">편집창 열기</span></div></div>';
					echo '<p class="btn_right"><a href="#none" class="btn_sml" id="answer_submit_'.$list['approval_line'].'" title="'.$list['idx'].'">';
					if($data['process_2'] == '6' || $data['process_2'] == '7' || $data['process_2'] == '8'){
						echo '<span class="'.$proc['modi'].'">답변 수정</span></a>';
					}else{
						echo '<span class="'.$proc['modi'].'">답변 완료</span></a>';
					}
					echo ($_SYSTEM['permission']['manage']==true?'<a href="#none" class="btn_sml reset_select"  title="'.$list['idx'].'"><span>접수 상태로 변경</span></a> <!--<a href="#none" class="btn_sml add_item"  title="'.$list['idx'].'"><span>담당자추가지정</span></a>-->':'');
					echo '</p>';

					if($list['type']=='department'|| $proc['step']<=1) {
						if($data['process_2'] != 6 && $data['process_2'] != 7 && $data['process_2'] != 8){
						echo '<div class="add"><div class="search_box">
								<div class="bg"></div>
								<fieldset>
									<legend>검색</legend>
									<select class="search_key" name="search_key_staff_id'.$script_id.'">
									    <option value="staff">직원</option>
									    <!--<option value="department">부서</option>
					                    <option value="work">업무</option>-->
									</select>
									<input type="text" id="staff_id'.$script_id.'" name="staff_id" class="autocomplete" autocomplete="off" />
									<div id="text_div_staff_id'.$script_id.'" class="text_div"><div id="search_text_staff_id'.$script_id.'" class="search_text" ></div></div>
									<a href="#none" class="btn_sml next_staff"><span>담당지정</span></a> <a href="#none" class="btn_sml sel_pass" title="'.$list['idx'].'"><span>배정완료</span></a><!--<a href="#none" class="btn_sml"><span>초기화</span></a>-->
								</fieldset>
							</div>
							<h4>답변 담당자</h4>
					        <div class="staff_list" id="select_staff_list"><span class="blank">&quot;담당추가&quot;를 클릭하여 담당자를 배정해주세요.(1명만 지정가능)</span></div></div>';
						}
					} else {
						/*echo '<div><div class="search_box">
								<div class="bg"></div>
								<fieldset>
									<legend>검색</legend>
									<select class="search_key" name="search_key_staff_id'.$script_id.'">
									    <option value="staff">직원</option>
									    <option value="department">부서</option>
					                    <option value="work">업무</option>
									</select>
									<input type="text" id="staff_id'.$script_id.'" name="staff_id" class="autocomplete" autocomplete="off" />
									<div id="text_div_staff_id'.$script_id.'" class="text_div"><div id="search_text_staff_id'.$script_id.'"  class="search_text" ></div></div>
									<!--<a href="#none" class="btn_sml append_view"><span>보기권한추가</span></a>-->
								</fieldset>
							</div></div>';*/
					}



					$answer_id[] = $list['approval_line'];
				} else {
					echo '<div class="answer_write'.(empty($reply)?'_standby':'_text').'">'.(empty($reply)?'<span>답변준비중</span>':nl2br(strip_tags(htmlspecialchars_decode($reply)))).'</div>';
					/*echo '<div class="search_box">
								<div class="bg"></div>
								<fieldset>
									<legend>검색</legend>
									<select class="search_key" name="search_key_staff_id'.$script_id.'">
									    <option value="staff">직원</option>
									    <option value="department">부서</option>
					                    <option value="work">업무</option>
									</select>
									<input type="text" id="staff_id'.$script_id.'" name="staff_id" class="autocomplete" autocomplete="off" />444
									<div id="text_div_staff_id'.$script_id.'" class="text_div"><div id="search_text_staff_id'.$script_id.'"  class="search_text" ></div></div>
									<!--<a href="#none" class="btn_sml append_view"><span>보기권한추가</span></a>-->
								</fieldset>
							</div>';*/
				}
				/*if(empty($view_staff_list)) {
				$view_staff_list = '<h4>보기 권한자</h4>
						 <div class="view_staff_list">';

						foreach(explode(';',$data['view_id']) as $view_id) {
							if(!empty($view_id)) {
								$user = $mysql->query_fetch('SELECT dept_id, user_name, user_id FROM `_member` WHERE user_id="'.$view_id.'"');
								$dept_info = $member->get_department_info($user['dept_id']);

								$dept_info['dept_path_title'] = str_replace('|',' ',$dept_info['dept_path_title']);
								$ret .= '<li><span class="dept" value="'.$user['dept_id'].'">'.$dept_info['dept_path_title'].'</span><span class="pos">'.$dept_info['dept_posname'].'</span><span class="name">'.$user['user_name'].'</span>';
								//if($list['type']=='department'||$proc['step']<=1) {
								$ret .= '<a href="#none" class="remove view_id_remove" remove_id="'.$user['user_id'].'"><img src="/images/common/icon_delete_9.gif" alt="제외" /></a></li>'; //}
							}
						}
							if(!empty($ret)) {
								$view_staff_list .= '<ul class="list">'.$ret.'</ul>';
							} else {
								$view_staff_list .= '<span class="blank">&quot;보기권한추가&quot;를 클릭하여 볼수있는 권한을 배정해주세요.</span>';
							}

						$view_staff_list .= '</div>';
						echo $view_staff_list;
				}*/
			}
			$script_id++;
		 ?>
        <?php } // end foreach


		?>
        <script type="text/javascript">

			const manageAnswerWrite = $(".manage_answer_write");
			const answerComplete = $(".answer_complete");
			const manageAnswerTextarea = manageAnswerWrite.find("textarea");
			const editBtn = $(".edit_btn");

			const manager_answer_process = manageAnswerWrite.attr("answer_process");
			if (manager_answer_process == 'complete') {
				manageAnswerWrite.css('display', 'none');
				manageAnswerTextarea.css('height', '600px');
				answerComplete.css('display', 'block');
			} else {
				manageAnswerWrite.css('display', 'block');
				answerComplete.css('display', 'none');
			}

			editBtn.click(function () {
				manageAnswerTextarea.css('height', '600px');
				manageAnswerWrite.css('display', 'block');
				answerComplete.css('display', 'none');
			})



			<?php
			foreach($answer_id as $id) {  ?>
			var oEditors = [];
			<?php if($data['process_2'] == '6' || $data['process_2'] == '7' || $data['process_2'] == '8') {
					$modi = 'y';
				  }else{
					$modi = 'n';
				  }
			?>
			var modi = "<?php echo $modi; ?>";
			nhn.husky.EZCreator.createInIFrame({
				oAppRef: oEditors,
				elPlaceHolder: "answer_contents_<?php echo $id; ?>",
				sSkinURI: "/js/smart_editor/SEditorSkin.html",
				fCreator: "createSEditorInIFrame"
			});
			document.getElementById("answer_submit_<?php echo $id; ?>").onclick = function () {
				oEditors.getById["answer_contents_<?php echo $id; ?>"].exec("UPDATE_IR_FIELD", []);
				$.post("/ybscript.io/approval/approval_proc",{
					board_idx : $("#board_idx").val(),
                    idx : $("#answer_submit_<?php echo $id; ?>").attr('title'),
					content : $("#answer_contents_<?php echo $id; ?>").val(),
					operation:'reply_complete',
					modi: modi
				}, function(req) {
					if(req=='OK') {
						$('body').fadeOut('fast', 0,function() {
							if(modi == 'y') alert('답변 수정 완료');
							if(modi ==  'n') alert('답변 완료');
							location.reload();
						});
					} else {
						alert(req);
					}
				});
			}
			<?php } ?>
		</script>
		</div>
    </form>
	<?php } ?>
    <?php if($status=='8'&&$_SESSION['user_level']>6) { ?>
    <form id="step_3" name="step_3" action="#">
		<div class="progress_info_list">
    <?php

	if(count($data['approval']['list']) == 0 || $data['process_2'] == '1') {
		$data['approval']['list'][0] = array('process'=>'reg','processing_date'=>$data['reg_date'],'user_name'=>$data['reg_name'],'dept_path_title'=>'신청');
 	}

	//echo '<pre>'; print_r($data['approval']); exit;
	foreach($data['approval']['list'] as $list) {
		if(!empty($data['approval']['last_process'])){
			$list['process'] = $data['approval']['last_process'];
			$list['reply'] = $data['approval']['last_reply'];
			$list['processing_date'] = $data['approval']['last_date'];
			$list['dept_path_title'] = $data['approval']['last_dept'];
			$list['user_name'] = $data['approval']['last_name'];
		}

		if($list['approval_line']!='0' || ($list['approval_line']=='0'&&$data['process_2']=='2')) {
			//	$list['reply'] = preg_replace('/<(p|span|font)([^>]*)>/i','<\1>',$list['reply']);
			// $list['reply'] = preg_replace('/<[\/]?font[^>]*>/i','',$list['reply']);
			echo '<div class="progress">
					<!--<strong class="tit">진행 상태</strong>-->
					<ol>
						<li'.($list['process']=='reg'?' class="on"':'').'><span>신청</span></li>
						<li'.($list['process']=='accept'?' class="on"':'').'><span>접수</span></li>
						<li'.(($list['process']=='next'||$list['process']=='pass')&&$list['step']>=1?' class="on"':'').'><span>담당자지정</span></li>
						<li'.($list['process']=='complete'?' class="on"':'').'><span>답변완료</span></li>
					</ol>	
				</div>
				<div class="answer">';
			//echo '<pre>'; print_r($data['approval']); exit;
			//echo $list['reply'];  20150306 오경우
			//echo nl2br(strip_tags($list['reply']));
			echo nl2br(htmlspecialchars_decode($list['reply']));
			echo 	'<div class="staff"><span class="date">'.date('Y년 m월 d일 H시 i분',strtotime($list['processing_date'])).'</span><span class="dept">'.str_replace('|',' ',$list['dept_path_title']).'</span><span class="name">'.$list['user_name'].(!empty($list['dept_tel'])?'('.$list['dept_tel'].')':'').'</span></div>
				</div>';
		 }
	}?>
		</div>
    </form>
    <?php
	}

	## 답변 첨부파일 : 민원인이 볼때
	if($_SESSION['user_level']>6 && !empty($data['approval']['file_list'])) {
		$file_list = NULL;
		$iter = 1;
		foreach($data['approval']['file_list'] as $file) {
			$download_url = '/ybscript.io/common/file_download/'.$data['idx'].'/'.$file['idx'].'/'.rawurlencode($file['original_name']);
			$file_list .= '<div class="approval_uploaded">';
			$file_list .= '    <label for="file_'.$iter.'">파일'.$iter.'</label><span>'.$file['original_name'].'</span>';
			$file_list .= '    <div class="file_load"><span><a href="'.$download_url.'">내려받기</a></span></div>';
			$file_list .= '</div>';
			$iter++;
		}

		if(!empty($file_list)) echo '<div id="approval_attach">'.$file_list.'</div>';
	}


	## 답변 첨부파일 : 공무원들이 볼때
	if($_SESSION['user_level'] <= 6) {
		echo '<form id="approval_file" action="/ybscript.io/approval/approval_file_control" method="post" name="approval_file" enctype="multipart/form-data">';
		echo '	<input type="hidden" name="process" value="upload" />';
		echo '	<input type="hidden" name="board_id" value="'.$data['approval']['board_id'].'" />';
		echo '	<input type="hidden" name="board_idx" value="'.$data['idx'].'" />';
		echo '	<div id="approval_attach">';

		for($iter=0 ; $iter < 5 ; $iter++) {
			if(!empty($data['approval']['file_list'][$iter])) {
				$file = $data['approval']['file_list'][$iter];
				$download_url = '/ybscript.io/common/file_download/'.$data['idx'].'/'.$file['idx'].'/'.rawurlencode($file['original_name']);
				$remove_url   = '/ybscript.io/approval/approval_file_control?process=remove&amp;board_id='.$data['approval']['board_id'].'&amp;board_idx='.$data['idx'].'&amp;file_idx='.$file['idx'];
				echo '<div class="approval_uploaded">';
				echo '<label for="file_'.($iter+1).'">파일'.($iter+1).'</label><span>'.$file['original_name'].'</span>';
				echo '<div class="file_load"><span><a href="'.$download_url.'">내려받기</a></span><span><a href="'.$remove_url.'">파일삭제</a></span></div>';
				echo '</div>';
			} else {
				echo '<div class="approval_uploaded"><label for="file_'.($iter+1).'">파일'.($iter+1).'</label><input type="file" name="file_'.($iter+1).'" id="file_'.($iter+1).'" value="" /></div>';
			}
		}

		echo '	</div>';
		echo '    <p class="align_right mar35 mab5"><input type="image" src="/images/common/board/report_ok.gif" alt="파일올리기" id="btn_form_submit" /></p>';
		echo '</form>';
	}

	?>
</div>

<div class="approval_board_bot"></div>
<?php if($_SESSION['user_level'] != 1) { ?>
<script>
	$(document).find(".remove_step").hide();
</script>
<?php } ?>
<?php if($_SESSION['user_level']<=6) { ?>

<script>
	var db = "";
/*
	var trick = function ($) {
		if ( ($('input[name="staff_id"]').val()!='' && db != $('input[name="staff_id"]').val())) {
			db = $('input[name="staff_id"]').val()
			search_script(jQuery);
		}
		window.setTimeout("trick(jQuery)", 100);
	};
*/
	var trick_id = function ($,id) {
		if ( ($('#'+id).val()!='' && db != $('#'+id).val())) {
			db = $('#'+id).val();
			search_script_id(jQuery, id);
		}
		window.setTimeout("trick_id(jQuery, '"+id+"')", 100);
	};
/*
	var search_script = function ($) {
		  $.post("/ybscript.io/approval/approval_proc",{
		    keyword : $('input[name="staff_id"]').val(),
			search_key : $('select[class="search_key"] option:selected').val(),
			operation:'search_item'
		  },function(data){
			  $(this).parent().parent().children('fieldset').children('#text_div').fadeIn();
		      $('#search_text').html("<ul><\/ul>");
		      $('#search_text > ul').html(data);
		      $('.stext').mouseover(function(){
		        	$(this).css({'background-color':'#eee'});
		      }).mouseout(function(){
		        	$(this).css({'background-color':'#fff'});
		      }).mouseup(function(){
			        $(this).parent().parent().children('fieldset').children('#text_div').fadeOut().find('li.stext').remove();
					$('.bg').hide();
			        $('input[name="staff_id"]').val($(this).children('span').attr('value'));
			        db = $(this).text();
		      });
		  });
		};
*/
		var search_script_id = function ($,id) {
		  $.post("/ybscript.io/approval/approval_proc",{
		    keyword : $('input[id="'+id+'"]').val(),
			search_key : $('select[name="search_key_'+id+'"] option:selected').val(),
			operation:'search_item'
		  },function(data){
			  /*$(this).parent().parent().children('fieldset').children('#text_div').fadeIn();*/
		      $('#search_text_'+id).html("<ul><\/ul>");
		      $('#search_text_'+id+' > ul').html(data);
		      $('.stext').mouseover(function(){
		        	$(this).css({'background-color':'#eee'});
		      }).mouseout(function(){
		        	$(this).css({'background-color':'#fff'});
		      }).mouseup(function(){
			        $('#text_div_'+id).fadeOut().find('li.stext').remove();
					$('.bg').hide();
			        $('input[id="'+id+'"]').val($(this).children('span').attr('value'));
			        db = $(this).text();
		      });
		  });
		};

		var reload_staff_list = function ($) {
			$.post("/ybscript.io/approval/approval_proc",{
				menu_id : $("#target").attr('value'),
				board_idx : $("#board_idx").val(),
				next_step : $('input[name="next_step"]').val(),
				board_id : $('input[name="board_id"]').val(),
				operation:'select_staff_list'
			}, function(req) {
				$('#select_staff_list').html(req);
				$('#select_staff_list .remove').click(function() {
					$idx = $(this).parent().find('.dept').attr('value');
					if(confirm('정말로 제외하시겠습니까?')) {
						$.post("/ybscript.io/approval/approval_proc",{
							idx : $idx,
							operation:'remove_staff_list'
						}, function(req) {
							if(req=='OK') {
								reload_staff_list(jQuery);
								alert('삭제하였습니다');
							} else {
								alert(req);
							}
						});
					}

				});
			});
		};
</script>
<script type="text/javascript" src="/js/jquery/datetimepicker/jquery.datetimepicker.js"></script>
<link rel="stylesheet" type="text/css" href="/js/jquery/datetimepicker/jquery.datetimepicker.css" />
<script type="text/javascript" name="import_jquery.ready">
//<![CDATA[
	var reg_num_2 = <?php echo $reg_num ?>;

	$('#deadline').datetimepicker({
		lang:'ko',
		prevText: '〈전달',
		nextText: '다음달〉',
		ampm: false,
		timepicker:false,
		format:'Y-m-d',
		formatDate:'Y-m-d'
	});
	$('#deadline_change').click(function() {
		$.post("/ybscript.io/approval/approval_proc",{
			menu_id : $("#target").attr('value'),
			board_idx : $("#board_idx").val(),
			next_step : $('input[name="next_step"]').val(),
			board_id : $('input[name="board_id"]').val(),
			deadline : $('#deadline').val(),
			operation:'deadline'
		},function(data){
			if(data=='OK') {
				alert('설정완료');
			} else {
				alert('ERROR'+data);
			}

		});
	});

	var matched, browser;

    jQuery.uaMatch = function (ua) {
        ua = ua.toLowerCase();

        var match = /(chrome)[ \/]([\w.]+)/.exec(ua) ||
            /(webkit)[ \/]([\w.]+)/.exec(ua) ||
            /(opera)(?:.*version|)[ \/]([\w.]+)/.exec(ua) ||
            /(msie) ([\w.]+)/.exec(ua) ||
            ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec(ua) ||
            [];

        return {
            browser: match[1] || "",
            version: match[2] || "0"
        };
    };

    matched = jQuery.uaMatch(navigator.userAgent);
    browser = {};

    if (matched.browser) {
        browser[matched.browser] = true;
        browser.version = matched.version;
    }

    if (browser.chrome) {
        browser.webkit = true;
    } else if (browser.webkit) {
        browser.safari = true;
    }

    jQuery.browser = browser;

	if($.browser.opera==true || $.browser.mozilla==true ) {
		$('input[name="staff_id"]').keypress(function(event){
			/*trick(jQuery);*/
			trick_id(jQuery, this.id);
		});
	} else {
		$('input[name="staff_id"]').keyup(function(event){
			if(event.keyCode!=27){
				/*search_script(jQuery);*/
				search_script_id(jQuery, this.id);
			} else {
				/*$(this).parent().parent().children('fieldset').children('#text_div').fadeOut().find('li.stext').remove();*/
				$('#text_div_'+this.id).fadeOut().find('li.stext').remove();
			}
		});
	};

	$('select[name="search_key"]').change(function() {
		/*$('input[name="staff_id"]').val('');
		$(this).parent().parent().children('fieldset').children('#text_div').fadeOut().find('li.stext').remove();*/
		$($('#'+this.id)).val('');
		$('#text_div_'+this.id).fadeOut().find('li.stext').remove();
		$('.bg').hide();
	});

	$('input[name="staff_id"]').focus(function() {
		/*
		$(this).keypress();
		$('#text_div').fadeIn();
		$('.bg').show().click(function() {
			$('#text_div').fadeOut().find('li.stext').remove();
			$(this).hide();
		});
		*/

		// 20140528 강성수
		/*
		$(this).keypress();
		$(this).parent().parent().children('fieldset').children('#text_div').fadeIn();
		$(this).parent().parent().children('.bg').show().click(function() {
			$(this).parent().parent().children('fieldset').children('#text_div').fadeOut().find('li.stext').remove();
			$(this).hide();
		});
		*/
		$(this).keypress();
		$('#text_div_'+this.id).fadeIn();
		$(this).parent().parent().children('.bg').show().click(function() {
			$('#text_div_'+this.id).fadeOut().find('li.stext').remove();
			$(this).hide();
		});

	});

	$('#accept_item').click(function() {
		if($('input[name="staff_id"]').val()!='') {
			$.post("/ybscript.io/approval/approval_proc",{
				items : $('input[name="staff_myid"]').val(),
				type : $('input[name="search_key"]').val(),
				process : 'accept',
				menu_id : $("#target").attr('value'),
				board_idx : $("#board_idx").val(),
				next_step : $('input[name="next_step"]').val(),
				board_id : $('input[name="board_id"]').val(),
				operation:'append_item'
			},function(data){
				if(data=='OK') {
					$('body').fadeOut('fast', 0,function() {
						alert('접수처리 완료');
						location.reload();
					});
				} else {
					alert('ERROR'+data);
				}

			});
		}
	});
	$('.remove_step').click(function() {
		var set_text = $(this).parent().text();
		var next_check = $(this).parent().parent().nextAll('.mini_box').attr('idx');
		var $remove_obj = $(this).parents('.mini_box');
		if(next_check!=undefined) {
			alert('다음단계가 존재합니다. 다음단계부터 먼저 삭제후 다시 시도하세요.');
			return false;
		} else {
			if(confirm(set_text+'된 건을 정말로 삭제하시겠습니까?')) {
				$.post("/ybscript.io/approval/approval_proc",{
					board_idx : $(this).attr('title'),
					operation:'remove_item'
				},function(data){
					if(data=='OK') {
						//$remove_obj.prev('.arrow_right').remove();
						//$remove_obj.remove();
						alert('삭제 완료');
						location.reload();
					} else {
						alert('ERROR'+data);
					}

				});
			}
		}
	});

	$('.add_item').click(function() {

		$board_idx = $(this).attr('title');
		if(confirm('답변 담당자를 추가로 지정하시겠습니까?')) {
			$.post("/ybscript.io/approval/approval_proc",{
				board_idx : $board_idx,
				operation:'add_item'
			},function(data){
				if(data=='OK') {
					$('body').fadeOut('fast', 0,function() {
						alert('담당자를 추가완료');
						location.reload();
					});
				} else {
					alert('에러 : '+data);
				}
			});
		}

	});

	$('.reset_select').click(function() {

		$idx = $(this).attr('title');
		if(confirm('정말로 접수상태로 되돌리시겠습니까?\n\n되돌리시면 담당자를 처음부터 다시 지정해야합니다.')) {
			$.post("/ybscript.io/approval/approval_proc",{
				idx : $idx,
				operation:'reset_select'
			},function(data){
				if(data=='OK') {
					$('body').fadeOut('fast', 0,function() {
						alert('접수상태로 변경완료');
						location.reload();
					});
				} else {
					alert('에러 : '+data);
				}
			});
		}

	});
	$('.urging').click(function() {
		$idx = $(this).attr('title');
		if(confirm('해당 담당자에게 촉구요청 문자를 발송하시겠습니까?')) {
			$.post("/ybscript.io/approval/approval_proc",{
				idx : $idx,
				operation:'urging'
			},function(data){
				if(data=='OK') {
					alert('답변 촉구요청(SMS 전송) 완료');
				} else {
					alert('에러 : '+data);
				}
			});
		}
	});
	function view_id_remove() {
		$('.view_id_remove').unbind();
		$('.view_id_remove').click(function() {
			var $this = $(this);
			if(confirm('삭제하시면 즉시 적용됩니다.\n\n그래도 삭제하시겠습니까?')) {
				$.post("/ybscript.io/approval/approval_proc",{
					board_idx : $("#board_idx").val(),
					board_id : $('input[name="board_id"]').val(),
					remove_id : $(this).attr('remove_id'),
					operation:'view_id_remove'
				},function(data){
					$this.parent().remove();
				});
			}
		});
	}

	view_id_remove();
	$('.append_view').click(function() {
		var $staff_id_obj = $(this).parents('fieldset').find('input[name="staff_id"]');
		if($(this).parents('.search_box').nextAll('.view_staff_list').children('span').length>0) {
			$(this).parents('.search_box').nextAll('.view_staff_list').html('<ul class="list"><\/ul>');
		}
		var $staff_list = $(this).parents('.search_box').nextAll('.view_staff_list').find('.list');
		if($staff_id_obj.val()!='') {
			$(this).parent().parent().children('fieldset').children('#text_div').fadeOut();
			$('.bg').hide();
			$search_key=$(this).parents('fieldset').find('select[class="search_key"] option:selected').val();
			$.post("/ybscript.io/approval/approval_proc",{
				board_idx : $("#board_idx").val(),
				board_id : $('input[name="board_id"]').val(),
				view_id : $staff_id_obj.val(),
				operation:'view_id_append'
			},function(data){
				var $data = data.split(':');
				if($data[0]=='OK') {
					$staff_list.append('<li><span class="'+$search_key+'">'+$staff_id_obj.val()+'</span><a href="#none" class="remove view_id_remove" remove_id="'+$data[1]+'"><img src="/images/common/icon_delete_9.gif" alt="제외" /></a></li>');
					view_id_remove();
					$staff_id_obj.val('');
				} else {
					alert(data);
				}

			});


		} else {
			alert('직원/부서 검색부터 먼저하세요');
			$staff_id_obj.focus();
		}

	});

	$('.append_staff').click(function() {
		var $staff_id_obj = $(this).parents('fieldset').find('input[name="staff_id"]');
		var $staff_list = $(this).parents('.search_box').nextAll('.staff_list').find('.list');
		if($staff_id_obj.val()!='') {
			$(this).parent().parent().children('fieldset').children('#text_div').fadeOut();
			$('.bg').hide();
			$search_key=$(this).parents('fieldset').find('select[class="search_key"] option:selected').val();
			$staff_list.append('<li><span class="'+$search_key+'">'+$staff_id_obj.val()+'</span><a href="#none" class="remove"><img src="/images/common/icon_delete_9.gif" alt="제외" /></a></li>')
			.find('.remove').click(function() {
				if(confirm('삭제하시겠습니까?')) {
					$(this).parent().remove();
				}
			});
			$staff_id_obj.val('');
		} else {
			alert('직원/부서 검색부터 먼저하세요');
			$staff_id_obj.focus();
		}
	});


	$('.next_staff').click(function() {
		/*alert($(this).parents('fieldset').find('input[name="staff_id"]').attr('id') + this.id);
		alert($('#'+staff_id).val());*/
		var staff_id = $(this).parents('fieldset').find('input[name="staff_id"]').attr('id');
		/*if($('input[name="staff_id"]').val()!='') {*/
		if($('#'+staff_id).val()!='') {
			/*$(this).parent().parent().children('fieldset').children('#text_div').fadeOut();*/
			$('#text_div_'+staff_id).fadeOut();
			$('.bg').hide();
			/*$search_key=$('select[class="search_key"] option:selected').val();*/
			$search_key=$('select[name="search_key_'+staff_id+'"] option:selected').val();
			/*alert($search_key);
			$('#select_staff_list > .list').html('<li><span class="'+$search_key+'">'+$('input[name="staff_id"]').val()+'</span><a href="#none" class="remove"><img src="/images/common/icon_delete_9.gif" alt="제외" /></a></li>')

			$('#select_staff_list > .list').html('<li><span class="'+$search_key+'">'+$('#'+staff_id).val()+'</span><a href="#none" class="remove"><img src="/images/common/icon_delete_9.gif" alt="제외" /></a></li>')
			alert($(this).parents().parents().parentchildren('.staff_list').html
			$(this).parents().parents().parents().children('.staff_list').html('<ul class="list"><li><span class="'+$search_key+'">'+$('#'+staff_id).val()+'</span><a href="#none" class="remove"><img src="/images/common/icon_delete_9.gif" alt="제외" /></a></li></ul>')*/
			$(this).parents().parents().parents().children('.staff_list').html('<li><span class="'+$search_key+'">'+$('#'+staff_id).val()+'</span><a href="#none" class="remove"><img src="/images/common/icon_delete_9.gif" alt="제외" /></a></li></ul>')
			.find('.remove').click(function() {
				if(confirm('삭제하시겠습니까?')) {
					$(this).parent().remove();
				}
			});
			/*$('input[name="staff_id"]').val('');*/
			$('#'+staff_id).val('');
		} else {
			alert('직원/부서 검색부터 먼저하세요');
			/*$('input[name="staff_id"]').focus();
			$(this).parents('fieldset').find('input[name="staff_id"]').focus();*/
			$('#'+staff_id).focus();
		}
	});


	$('#return_staff_admin').click(function() {
		if($('input[name="staff_id"]').val()!='') {
			$(this).parent().parent().children('fieldset').children('#text_div').fadeOut();
			$('.bg').hide();
			$.post("/ybscript.io/approval/approval_proc",{
				items : $('input[name="staff_id"]').val(),
				type : $('select[class="search_key"] option:selected').val(),
				process : 'next',
				menu_id : $("#target").attr('value'),
				board_idx : $("#board_idx").val(),
				next_step : $('input[name="next_step"]').val(),
				board_id : $('input[name="board_id"]').val(),
				operation:'append_item'
			},function(data){
				$('input[name="staff_id"]').val('');
				$('#select_staff_list > *').remove();
				reload_staff_list(jQuery);
			});
		} else {
			alert('직원/부서 검색부터 먼저하세요');
			$('input[name="staff_id"]').focus();
		}
	});


	$('#sel_complete').click(function() {
		var $list = '';
		var $list2 = '';

		if( $('#select_staff_list > .list > li').length<1) { alert('지정부터 해주세요'); return false;}
		 $('#select_staff_list > .list > li').each(function(index, element) {
           $list += ($list!=''?'|':'') + $(this).text()+'['+$(this).find('span:first-child').attr('class')+']';
        });

		if( $('.view_staff_list > .list > li').length>0) {
			$('.view_staff_list > .list > li').each(function(index, element) {
			   $list2 += ($list!=''?'|':'') + $(this).text()+'['+$(this).find('span:first-child').attr('class')+']';
			});
		}
		/*alert($(this).parents('fieldset').find('select[class="search_key"] option:selected').val());return;*/
		$.post("/ybscript.io/approval/approval_proc",{
			items : $list,
			items2 : $list2,
			/*type : $('input[name="search_key"]').val(),*/
			type : $(this).parents('fieldset').find('select[class="search_key"] option:selected').val(),
			process : 'accept',
			menu_id : $("#target").attr('value'),
			board_idx : $("#board_idx").val(),
			next_step : '3',
			board_id : $('input[name="board_id"]').val(),
			//deadline : $('#deadline').val(),
			reg_num : reg_num_2,
			operation:'select_complete'
		}, function(req) {
			//console.log("req="+req);
			if(req=='OK') {
				$('body').fadeOut('fast', 0,function() {
					alert('배정 완료');
					location.reload();
				});
			} else {
				alert('ERROR : '+req);
			}
		});

	});
	$('.sel_pass').click(function() {
		/*alert($(this).parents().parents().parents().children('.staff_list').html());
		alert($(this).parents().parents().parents().children('.staff_list').children('li').html());
		alert($(this).parents().parents().parents().children('.staff_list').children('li').length);*/
		if($(this).parents().parents().parents().children('.staff_list').children('li').length<1) { alert('지정부터 해주세요'); return false;}
		/*if( $('#select_staff_list > .list > li').length<1) { alert('지정부터 해주세요'); return false;}*/
		if(confirm('배정을 완료하시겠습니까?')) {
			var $list = '';
			/* $('#select_staff_list > .list > li').each(function(index, element) {*/
			$(this).parents().parents().parents().children('.staff_list').children('li').each(function(index, element) {
	           $list += ($list!=''?'|':'') + $(this).text()+'['+$(this).find('span:first-child').attr('class')+']';
	        });
				var $idx = $(this).attr('title');
			/*alert($idx);
			alert($list);alert($search_key=$(this).parents('fieldset').find('select[class="search_key"] option:selected').val());
			alert($("#target").attr('value'));
			alert($("#board_idx").val());
			alert($('input[name="board_id"]').text());
			alert($(this).parents('fieldset').find('select[class="search_key"] option:selected').val());	*/
			$.post("/ybscript.io/approval/approval_proc",{
				items : $list,
				idx : $idx,
				/*type : $('input[name="search_key"]').val(),*/
				type : $(this).parents('fieldset').find('select[class="search_key"] option:selected').val(),
				process : 'pass',
				menu_id : $("#target").attr('value'),
				board_idx : $("#board_idx").val(),
				board_id : $('input[name="board_id"]').val(),
				operation:'select_pass'
			}, function(req) {
				if(req=='OK') {
						$('body').fadeOut('fast', 0,function() {
							alert('배정 완료');
							location.reload();
						});
					} else {
						alert('ERROR : '+req);
					}
			});
		}

	});
	$('#answer').click(function() {
		if($('#answer_contents').val()!='') {
			$.post("/ybscript.io/approval/approval_proc",{
				process : 'answer',
				menu_id : $("#target").attr('value'),
				board_idx : $("#board_idx").val(),
				next_step : $('input[name="next_step"]').val(),
				board_id : $('input[name="board_id"]').val(),
				operation:'answer'
			},function(data){
				if(data=='OK') {
					$('body').fadeOut('fast', 0,function() {
						alert('답변 완료');
						location.reload();
					});
				} else {
					alert('ERROR : '+data);
				}

			});
		}
	});
	$('.return_approve').click(function() {
		if(confirm('서무담당자에게 반송하시겠습니까?')) {
			var $idx = $(this).attr('title');
			$.post("/ybscript.io/approval/approval_proc",{
				idx : $idx,
				operation:'return_approve'
			},function(data){
				if(data=='OK') {
					$('body').fadeOut('fast', 0,function() {
						alert('반송 완료');
						location.reload();
					});
				} else {
					alert('ERROR : '+data);
				}

			});
		}
	});
	setTimeout("reload_staff_list(jQuery)", 2000);
	//]]>
</script>
<?php
}
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
if($_SESSION['user_pin']!=$data['reg_pin'] && $_SYSTEM['permission']['admin']!='true') {
	$data['permission']['write']='';
	$data['permission']['modify']='';
	$data['permission']['remove']='';
	$data['permission']['reply']='';
}
if($_SYSTEM['module_config']['board_id'] == 'www_inconvenience' || $_SYSTEM['module_config']['board_id'] == 'www_reaulatory_reform' || $_SYSTEM['module_config']['board_id'] == 'www_poor' || $_SYSTEM['module_config']['board_id'] == 'www_newspaper' || $_SYSTEM['module_config']['board_id'] == 'www_pollution' || $_SYSTEM['module_config']['board_id'] == 'www_cyber_disaster') {
	$data['permission']['write']='';
	$data['permission']['modify']='';
	$data['permission']['remove']='';
	$data['permission']['reply']='';
}
if( $data['process_2'] == '6' || $data['process_2'] == '7' || $data['process_2'] == '8') {
	$data['permission']['modify'] = '';
	$data['permission']['remove'] = '';
	$data['permission']['reply'] = '';
}
//echo print_button('view', $data['permission'], $data['parameter'], $url, $img_url, $user_info, $arr_data);
## --------------------- 버튼 : start
		$print_button =  print_button_new('view', $data['permission'], $data['parameter'], $url, $img_url, $user_info, $arr_data);
		if( !empty($print_button ) ){
		echo '<!-- 버튼 START --><div class="board_btn_box align_right">'.$print_button.'</div><!-- 버튼 END -->';
		}
		## ---------------------

if(isset($data['use_comment'])&&$data['use_comment'] == 'true') include_once($data['module_root'].'/_plugin/skin/comment.php');
?>
	</div>
</div>
<?php

if($data['permission']['admin'] == true) include_once($data['module_path'].'/admin.php');
?>