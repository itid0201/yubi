<script type="text/javascript" src="/js/smart_editor/js/HuskyEZCreator.js"></script>
<link rel="stylesheet" type="text/css" href="/style/common/approval_mobile.css" />
<?php echo $data['debug']; // only test.. ?>
<?php
//2012-10-10 황재복 : 휴대폰 번호 자동 차단 방지
if($_SYSTEM['module_config']['use_phone_filter'] == 'false') {  
	$data['title']=preg_replace('/01([016789])[-~.[:space:]]([0-9]{3,4})[-~.[:space:]]([0-9]{4})/','01\1－\2－\3',$data['title']);
	$data['contents']=preg_replace('/01([016789])[-~.[:space:]]([0-9]{3,4})[-~.[:space:]]([0-9]{4})/','01\1－\2－\3',$data['contents']);
}

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

## 23.04.12 이진주 :: 팀장의 경우 각 팀원의 민원 사항을 다 볼 수 있도록 -----------------
$team_leader_arr = ["1836","1762","1870","1759","1756","1865"];
$is_team_leader = false;
if(in_array($_SESSION['dept_posid'], $team_leader_arr)) $is_team_leader = true;


if($data['open']=='y' || ($data['reg_pin'] == $_SYSTEM['myinfo']['my_pin']) || ($data['open'] == 'n' && ($_SYSTEM['permission']['manage'] == true || $damdang == true)) || $data['view_permission']=='true' ||$is_team_leader) {
	if( $data['html_tag'] == "a" ){
		$data['contents'] = unserialize(base64_decode($data['contents']) );
		$contents = get_html_contents( $data['contents'], $data['file_list'] );																									
	}else{
		$temp[] = nl2br($data['contents']);
		$contents = get_html_contents( $temp, $data['file_list'], NULL, $data);
	}
}else{
	echo '- 비공개글입니다(조회 권한이 없음) - ';
}

$reg_num = $data['varchar_1'];
?>

<input type="hidden" id="board_idx" value="<?php echo $data['idx']; ?>" />
<div class="module_view_box noPopup">
  <div class="view_titlebox">
<?php if($data['open']=='y'||($data['reg_pin']==$_SYSTEM['myinfo']['my_pin'])||($data['open']=='n'&&($_SYSTEM['permission']['manage']==true||$damdang==true))||$data['view_permission']=='true') {	?>  
    <h3><?php echo $data['title']; ?></h3>
<?php }else{ ?>
	<h3> - 비공개글입니다 - </h3>
<?php } ?>
    <span><?php echo date('Y.m.d ',strtotime($data['reg_date'])); ?> / <?php echo($data['writer_display'] == 'department' ? $data['depart_name'] : $data['reg_name']) ?></span></div>
  <div class="contbox">

	<div class="set-box">
	  <dl class="left">
		<?php if($data['use_category_1'] == 'true') { ?>
		<dt>분류</dt>
		<dd><?php echo $data['category_1']; ?></dd>
		<?php } 
		if($data['use_allow'] == 'true') {
		?>
		<dt>승인요청</dt>
		<dd><span class="btn_round_red"><em><?php echo ($data['allow'] == 'y' ? '승인' : '승인요청중'); ?></em></span></dd>
		<?php }
		if($data['use_lock'] == 'true') { 
		?>
		<!--<dt>공개여부</dt>
		<dd><span class="btn_round_green"><em><?php /*echo ($data['open'] == 'y' ? '공개' : '비공개');*/ ?></em></span></dd>-->
		<?php } ?>
	  </dl>
	</div>
	<div class="contbox">
    	<?php
	
			## -------------- 콘텐츠
			echo $contents; 	
		
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
	?>
<?php if(($_SESSION['user_level'] <= 6 && $data['open']=='y')||($data['open']=='n' && ($_SYSTEM['permission']['manage']==true||$damdang==true))) { ?>
<div class="comment">
	<h5>등록자 정보<span></span></h5>
    <div class="comment_input">
		<ul class="member_info">
			<li><span class="label">접수자</span><span class="txt"><?php echo $data['reg_name']; ?><!--(<?php /*echo $data['reg_pin'];*/ ?>)--></span></li>
			<?php
				if(!empty($data['phone_1']) || !empty($data['phone_2']) || !empty($data['phone_3'])) {
					$phone = $data['phone_1'].'-'.$data['phone_2'].'-'.$data['phone_3'];
				} else {
					if(!empty($data['phone'])) list($phone_1, $phone_2, $phone_3) = explode('-',$data['phone']);
				}
			?>
		    <li><span class="label">연락처</span><span class="txt"><?php echo $phone; ?></span></li>
		    <li><span class="label">주소</span><span class="txt"><?php echo $data['address_1']; ?>  <?php echo $data['address_2']; ?></span></li>
		</ul>
    </div>
</div>
<?php } 

$status=empty($data['process_2'])?'1':$data['process_2'];
if($_SESSION['user_level']>6)  $status='8';

?>

<h3 class="approval_title">민원업무처리</h3>
<div class="approval_board">
	<?php 
	if($status=='1' && $_SYSTEM['permission']['manage']==true) { ?>
    <form id="step_0" action="#">
    	<input type="hidden" name="next_step" value="1" />
        <input type="hidden" name="board_id" value="<?php echo $_SYSTEM['menu_info']['value']['board_id']; ?>" />
        <input type="hidden" name="search_key" value="staff" />
        <input type="hidden" name="staff_myid" value="<?php echo $_SYSTEM['myinfo']['user_name'].'('.$_SYSTEM['myinfo']['user_id'].')'; ?>"  />
		<!--<p><input type="checkbox" value="true" name="sms_send" id="sms_send" checked="checked" /> <label for="sms_send">접수처리되었음을 신청자(민원인)에게 SMS로 알림</label></p>
        <p>※ 접수가 완료되면 신청자는 더이상 내용을 수정하거나 삭제할 수 없습니다.</p>-->
		<p class="btn_right"><a href="#none" class="btn_big" id="accept_item"><span>접수 처리</span></a></p>
    </form>
	<?php } 
	if(($status=='1' ||$status=='2')&& $_SYSTEM['permission']['manage']==false) {
		echo '<ul>';
		foreach($_SYSTEM['page_manager'] as $manager) {
			echo '<li>'.($status=='2'?'담당자 지정중':'접수처리 중').' : <span class="dept">'.$manager['dept'].'</span> <span class="name">'.$manager['name'].'</span> <span class="tel">(문의 : '.$manager['tel'].')</span></li>';	
		}
		echo '</ul>';
	}?>
    <?php if($status=='2'&& $_SYSTEM['permission']['manage']==true) { 
	?>
    <form id="step_1" action="#">
        <input type="hidden" name="next_step" value="2" />
        <input type="hidden" name="board_id" value="<?php echo $_SYSTEM['menu_info']['value']['board_id']; ?>" />
		<!--<p><input type="checkbox" value="true" name="sms_send" id="sms_send" checked="checked" /> <label for="sms_send">담당지정이 되었음을 신청자(민원인)에게 SMS로 알림</label></p>
        <p><input type="checkbox" value="true" name="sms_send2" id="sms_send2" checked="checked" /> <label for="sms_send2">배정된 담당자에게 SMS로 알림</label></p>
        <p><label for="deadline">답변 처리기한</label> <input type="text" value="<?php /*echo date('Y-m-d',strtotime('+3 days'));*/?>" name="deadline" id="deadline" size="10" autocomplete="off" /></p>-->
        <p class="staff_list"><?php
		global $member;
        $myinfo = $member->get_member_info($_SYSTEM['myinfo']['user_id']);
		$dept_info = $member->get_department_info($myinfo['dept_id']);
		echo '<span class="blank">'.str_replace('|',' ',$dept_info['dept_path_title']).' '. $myinfo['user_name'].'님의 즉시 답변 : </span>';
		
		?> </p>
        <div class="answer_write"><textarea id="answer_contents"></textarea></div>
        <p class="btn_right"><a href="#none" class="btn_big" id="answer" title="<?php echo $data['approval']['list'][0]['idx']; ?>"><span>즉시 답변 완료</span></a></p>
        <div class="search_box">
			<div class="bg"></div>
			<fieldset>
				<div class="top_box">
					<legend>검색</legend>
					<select name="search_key">
						<option value="staff">직원</option>
						<!--<option value="department">부서</option>
						<option value="work">업무</option>-->
					</select>
					<input type="text" name="staff_id" class="autocomplete" autocomplete="off" />
				</div>
				<div id="text_div"><div id="search_text"></div></div>
				<div class="bottom_box">
					<a href="#none" class="btn_sml" id="append_staff"><span>담당추가</span></a><a href="#none" class="btn_sml" id="sel_complete"><span>배정완료</span></a><!--<a href="#none" class="btn_sml"><span>초기화</span></a>-->
				</div>
			</fieldset>
		</div>
        <div class="staff_list" id="select_staff_list"><span class="blank">&quot;담당추가&quot;를 클릭하여 담당자를 배정해주세요.</span></div>
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
			if($("#answer_contents").val() == ""){
				alert("답변을 입력해주세요");
				return;
			}
			$.post("/ybscript.io/approval/approval_proc", {
				  board_idx: $("#board_idx").val(),
				  idx: $("#answer").attr('title'),
				  content: $("#answer_contents").val(),
				  approval_line: '1',
				  ret_url: $(location).attr('href').split("?")[0],
				  reg_num: reg_num,
				  operation: 'reply_complete'
			  }, function (req) {
				  if (req == 'OK') {
					  $('body').fadeOut('fast', 0, function () {
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
    <?php if($status=='3' || $status=='4' || (($status=='8'||$status=='7'||$status=='6')&&$_SESSION['user_level']<=6)) { ?>
    <form id="step_2" action="#">
		<!--<p><input type="checkbox" value="true" name="sms_send" id="sms_send" checked="checked" /> <label for="sms_send">답변저장 후 완료시 신청자(민원인)에게 <strong>완료</strong>되었음을 SMS로 알림</label></p>-->
        <!--p><input type="checkbox" value="true" name="sms_send" id="sms_send" checked="checked" /> <label for="sms_send">답변저장 후 결재요청시 결재자에게 SMS로 알림</label></p-->
        <?php ?>
		<?php if($_SYSTEM['permission']['manage']==true) { ?>
	        <input type="hidden" name="board_id" value="<?php echo $_SYSTEM['menu_info']['value']['board_id']; ?>" />
			<!--<p><label for="deadline">답변 처리기한 변경</label> <input type="text" value="<?php /*echo $data['approval']['deadline'];*/?>" name="deadline" id="deadline" size="10" autocomplete="off" /></p>-->
			<?php } else { ?>
	        <!--<p><label for="deadline">답변 처리기한</label> : --><?php /*$limit = !empty($data['approval']['deadline'])?$data['approval']['deadline']:date('Y-m-d',strtotime($data['reg_date'].' + 3day'));
			echo '<span>'.$limit.'</span>'.(empty($data['approval']['deadline'])?' <span class="btn_round_red"><em>자동지정</em></span>':'');
			if($status!='8'&&$status!='7'&&$status!='6') { 
				if(date('Y-m-d')>$limit) $gap =  ceil((strtotime(date('Y-m-d')) - strtotime($limit)) / 86400); 
				else if(date('Y-m-d')<$limit)  $gap = ceil((strtotime($limit) - strtotime(date('Y-m-d'))) / 86400);
					else $gap =  '0';
				if($gap>0) {
					echo ' ('.$gap.'일 남음)';
				} else if($gap<0) {
					echo ' ('.$gap.'일 지남)';
				}
			}*/
			?><!--</p>-->
	     <?php } ?>
         <?php 
		 foreach($data['approval']['list'] as $list) { 
		 	$html='';
			$reply='';
		 	if($list['approval_line']=='0') {
				$proc = $data['approval']['approval_0'][0];
				$html0 ='<span class="mini_box" title="'.$proc['idx'].'"><em>'.preg_replace('/^([^\|]+)\|(.*?)$/','$1',$proc['dept_path_title']).' '.$proc['user_name'].' '.$stat_type_str[$proc['process']].' ('.date('Y.m.d H:i',strtotime($proc['processing_date'])).')</em>
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
					
					$depart = explode('|',$proc['dept_path_title']);
					$depart = end($depart);
					
					$html .= (!empty($html)?'<span class="arrow_right"></span>':'').'<span class="mini_box" title="'.$proc['idx'].'">
					<em>'.preg_replace('/.*?\|([^\|]+[실|과|소|읍|면|동]+)\|.*$/i','$1',$depart).' '.$proc['user_name'].' '.$str_proc.' ('.date('Y.m.d H:i',strtotime($proc['processing_date'])).')'.($str_proc!='접수111'?'<a href="#none" class="remove"><img alt="제외" src="/images/common/icon_delete_9.gif" /></a>':'').'</em>
					</span>';
					if($proc['process']=='complete') $reply = $proc['reply'];
				}
				
				//$reply = preg_replace('/<(p)([^>]*)>/i',"\n\n",$reply);
				//$reply = preg_replace('/<[^>]*>/i','',$reply);
				echo $html;
		        echo '</div>';
				if($_SYSTEM['permission']['manage']==true || $proc['user_id']==$_SYSTEM['myinfo']['user_id']) {
					echo '<div class="answer_write manage_answer_write" answer_process="'.$proc['process'].'"><textarea cols="70" rows="10" id="answer_contents_'.$list['approval_line'].'">'.$reply.'</textarea></div>';
					echo '<div class="answer_write_text answer_complete" style="display: none">'.nl2br(htmlspecialchars_decode($reply)).'<div class="answer_edit"><span class="edit_btn">편집창 열기</span></div></div>';
					echo '<p class="btn_right"><a href="#none" class="btn_sml" id="answer_submit_'.$list['approval_line'].'" title="'.$list['idx'].'">';
					if($data['process_2'] == '6' || $data['process_2'] == '7' || $data['process_2'] == '8'){
						echo '<span class="'.$proc['modi'].'">답변 수정</span></a>';	
					}else{
						echo '<span class="'.$proc['modi'].'">답변 완료</span></a>';	
					}
					//echo ' <!--a href="#none" class="btn_sml return_approve" title="'.$list['idx'].'"><span>서무담당에게 반송</span></a-->';
					echo ($_SYSTEM['permission']['manage']==true?'<!--<a href="#none" class="btn_sml urging"  title="'.$list['idx'].'"><span>답변촉구요청</span></a>--><a href="#none" class="btn_sml reset_select"  title="'.$list['idx'].'"><span>접수 상태로 변경</span></a>':'');
					echo '</p>';
					if($list['type']=='department') {
						echo '<p>
					        <div class="search_box">
								<div class="bg"></div>
								<fieldset>
									<legend>검색</legend>
									<select name="search_key">
									    <option value="staff">직원</option>
									    <!--<option value="department">부서</option>
					                    <option value="work">업무</option>-->
									</select>
									<input type="text" name="staff_id" class="autocomplete" autocomplete="off" />
									<div id="text_div"><div id="search_text"></div></div>
									<a href="#none" class="btn_sml" id="next_staff"><span>담당지정</span></a> <a href="#none" class="btn_sml" id="sel_pass" title="'.$list['idx'].'"><span>배정완료</span></a> <a href="#none" class="btn_sml"><span>초기화</span></a>
								</fieldset>
							</div>
					        <div class="staff_list" id="select_staff_list"><span class="blank">&quot;담당추가&quot;를 클릭하여 담당자를 배정해주세요.(1명만 지정가능)</span></div>';
						
					}
					$answer_id[] = $list['approval_line'];
				} else {
					echo '<div class="answer_write'.(empty($reply)?'_standby':'_text').'">'.(empty($reply)?'<span>답변준비중</span>':$reply).'</div>';
					echo (empty($reply)?'<p class="btn_right"><a href="#none" class="btn_sml"><span>답변촉구요청</span></a></p>':'');
				}
				
			}
		 ?>
        <?php } ?>
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
			var oEditors<?php echo $id; ?> = [];
			var reg_num = <?php echo $reg_num; ?>;
			<?php if($data['process_2'] == '6' || $data['process_2'] == '7' || $data['process_2'] == '8') {
					$modi = 'y';
				  }else{
					$modi = 'n';
				  }
			?> 
			var modi = "<?php echo $modi; ?>";
			nhn.husky.EZCreator.createInIFrame({
				oAppRef: oEditors<?php echo $id; ?>,
				elPlaceHolder: "answer_contents_<?php echo $id; ?>",
				sSkinURI: "/js/smart_editor/SEditorSkin.html",
				fCreator: "createSEditorInIFrame"
			});
			document.getElementById("answer_submit_<?php echo $id; ?>").onclick = function () {
				if($("#answer_contents_<?php echo $id; ?>").val() == ""){
					alert("답변을 입력해주세요");
					return;
				}
				oEditors<?php echo $id; ?>.getById["answer_contents_<?php echo $id; ?>"].exec("UPDATE_IR_FIELD", []);
				$.post("/ybscript.io/approval/approval_proc",{
					board_idx: $("#board_idx").val(),
					idx : $("#answer_submit_<?php echo $id; ?>").attr('title'),
					content : $("#answer_contents_<?php echo $id; ?>").val(),
					approval_line: '1',
                    ret_url: $(location).attr('href').split("?")[0],
				    reg_num: reg_num,
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
    </form>
	<?php } ?>
    <?php if($status=='8'&&$_SESSION['user_level']>6) { ?>
    <form id="step_3" action="#">
    <?php
	
	if(count($data['approval']['list'])==0|| $data['process_2']=='1') {
		$data['approval']['list'][0] = array('process'=>'reg','processing_date'=>$data['reg_date'],'user_name'=>$data['reg_name'],'dept_path_title'=>'신청');	
 	}
	
	 foreach($data['approval']['list'] as $list) {
		 
		if(!empty($data['approval']['last_process'])){
			$list['process'] = $data['approval']['last_process'];
			$list['reply'] = $data['approval']['last_reply'];
			$list['processing_date'] = $data['approval']['last_date'];
			$list['dept_path_title'] = $data['approval']['last_dept'];
			$list['user_name'] = $data['approval']['last_name'];	
		}
		 
		$list_title = explode('|',$list['dept_path_title']);
		$list_title = end($list_title);
		 
		if($list['approval_line']!='0' ||($list['approval_line']=='0'&&$data['process_2']=='2') ) {
		//	$list['reply'] = preg_replace('/<(p|span|font)([^>]*)>/i','<\1>',$list['reply']);
			//$list['reply'] = preg_replace('/<[\/]?font[^>]*>/i','',$list['reply']);
			echo '<div class="progress">
					<strong class="tit">진행상태</strong>
					<ol>
						<li'.($list['process']=='reg'?' class="on"':'').'><span>신청</span></li>
						<li'.($list['process']=='accept'?' class="on"':'').'><span>접수</span></li>
						<!--<li'.(($list['process']=='next'||$list['process']=='pass')&&$list['step']==2?' class="on"':'').'><span>부서지정'.(($list['process']=='next'||$list['process']=='pass')&&$list['step']==2?'(현재단계)':'').'</span></li>-->
						<li'.(($list['process']=='next'||$list['process']=='pass')&&$list['step']>=1?' class="on"':'').'><span>담당지정</span></li>
						<!--<li'.($list['process']=='next'&&$list['step']>=3?' class="on"':'').'><span>담당자지정'.($list['process']=='next'&&$list['step']>=3?' class="on"':'').'</span></li>-->
						<li'.($list['process']=='complete'?' class="on"':'').'><span>완료</span></li>
					</ol>	
				</div>
				<div class="answer">
					'.nl2br(htmlspecialchars_decode($list['reply'])).'
					<div class="staff"><span class="date">'.date('Y년 m월 d일 H시 i분',strtotime($list['processing_date'])).'</span><span class="dept">'.$list_title.'</span><span class="name">'.$list['user_name'].(!empty($list['dept_tel'])?'('.$list['dept_tel'].')':'').'</span></div>
				</div>';
		 }
	}?>
   
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
		echo '    <p class="align_right mar15 mab5"><input type="image" src="/images/common/board/report_ok.gif" alt="파일올리기" id="btn_form_submit" /></p>';
		echo '</form>';
	}

?>
  </div> 
<?php if($_SESSION['user_level'] != 1) { ?>
<script>
	$(document).find(".remove").hide();
</script>		
<?php } ?>	
<?php if($_SESSION['user_level']<=6) { ?>
<script type="text/javascript">
//<![CDATA[
	var db = "";

	var trick = function ($) {
		if ( ($('input[name="staff_id"]').val()!='' && db != $('input[name="staff_id"]').val())) {
			db = $('input[name="staff_id"]').val()
			search_script(jQuery);
		}
		window.setTimeout("trick(jQuery)", 100);
	};

	var search_script = function ($) {
		  $.post("/ybscript.io/approval/approval_proc",{
		    keyword : $('input[name="staff_id"]').val(),
			search_key : $('select[name="search_key"] option:selected').val(),
			operation:'search_item'
		  },function(data){
			  //$('#text_div').fadeIn();
			  console.log(data);
		      $('#search_text').html('<ul><\/ul>');
		      $('#search_text > ul').html(data);
		      $('.stext').mouseover(function(){
		        	$(this).css({'background-color':'#eee'});
		      }).mouseout(function(){
		        	$(this).css({'background-color':'#fff'});
		      }).mouseup(function(){
			        $('#text_div').fadeOut().find('li.stext').remove();
					$('.bg').hide();
			        $('input[name="staff_id"]').val($(this).children('span').attr('value'));
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
<script name="import_jquery.ready">	
	
	var reg_num_2 = <?php echo $reg_num ?>;
	
	$('#deadline').datetimepicker({
		lang:'ko',
		prevText: '〈전달',
		nextText: '다음달〉',
		ampm: false,
		timepicker:false,
		format:'Y-m-d',
		formatDate:'Y-m-d',
		onChangeDateTime: function() {
			$.post("/ybscript.io/approval/approval_proc",{
				menu_id : $("#target").attr('value'),
				board_idx : $("#board_idx").val(),
				next_step : $('input[name="next_step"]').val(),
				board_id : $('input[name="board_id"]').val(),
				deadline : $('#deadline').val(),
				operation:'deadline'
			},function(data){
				if(data=='OK') {
				
				} else {
					alert('ERROR'+data);	
				}
		
			});
		}	
	});
	
	$('#deadline').change(function() {
			alert('설정완료');
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
			trick(jQuery);
		});
	} else {
		$('input[name="staff_id"]').keyup(function(event){
			if(event.keyCode!=27){
				search_script(jQuery);
			} else {
				$('#text_div').fadeOut().find('li.stext').remove();
			}
		});
	};

	$('select[name="search_key"]').change(function() {
		$('input[name="staff_id"]').val('');
		$('#text_div').fadeOut().find('li.stext').remove();
		$('.bg').hide();
	});
	
	$('input[name="staff_id"]').focus(function() {
		$(this).keypress();
		$('#text_div').fadeIn();
		$('.bg').show().click(function() {
			$('#text_div').fadeOut().find('li.stext').remove();
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

	$('#append_staff').click(function() { 
		if($('input[name="staff_id"]').val()!='') {
			$('#text_div').fadeOut();
			$('.bg').hide();
			$search_key=$('select[name="search_key"] option:selected').val();
			$('#select_staff_list > .list').append('<li><span class="'+$search_key+'">'+$('input[name="staff_id"]').val()+'</span><a href="#none" class="remove"><img src="/images/common/icon_delete_9.gif" alt="제외" /></a></li>')
			.find('.remove').click(function() {
				if(confirm('삭제하시겠습니까?')) {
					$(this).parent().remove();	
				}
			});
			$('input[name="staff_id"]').val('');
		} else {
			alert('직원/부서 검색부터 먼저하세요');
			$('input[name="staff_id"]').focus();
		}
	});
	$('#next_staff').click(function() { 
		if($('input[name="staff_id"]').val()!='') {
			$('#text_div').fadeOut();
			$('.bg').hide();
			$search_key=$('select[name="search_key"] option:selected').val();
			$('#select_staff_list > .list').html('<li><span class="'+$search_key+'">'+$('input[name="staff_id"]').val()+'</span><a href="#none" class="remove"><img src="/images/common/icon_delete_9.gif" alt="제외" /></a></li>')
			.find('.remove').click(function() {
				if(confirm('삭제하시겠습니까?')) {
					$(this).parent().remove();	
				}
			});
			$('input[name="staff_id"]').val('');
		} else {
			alert('직원/부서 검색부터 먼저하세요');
			$('input[name="staff_id"]').focus();
		}
	});
	
	$('#return_staff_admin').click(function() { 
		if($('input[name="staff_id"]').val()!='') {
			$('#text_div').fadeOut();
			$('.bg').hide();
			$.post("/ybscript.io/approval/approval_proc",{
				items : $('input[name="staff_id"]').val(),
				type : $('select[name="search_key"] option:selected').val(),
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
		if( $('#select_staff_list > .list > li').length<1) { alert('지정부터 해주세요'); return false;}
		 $('#select_staff_list > .list > li').each(function(index, element) {
           $list += ($list!=''?'|':'') + $(this).text()+'['+$(this).find('span:first-child').attr('class')+']';
        });
		$.post("/ybscript.io/approval/approval_proc",{
			items : $list,
			type : $('input[name="search_key"]').val(),
			process : 'accept',
			menu_id : $("#target").attr('value'),
			board_idx : $("#board_idx").val(),
			next_step : '3',
			board_id : $('input[name="board_id"]').val(),
			//deadline : $('#deadline').val(),
			reg_num : reg_num_2,
			operation:'select_complete'
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
	
	});
	$('#sel_pass').click(function() {
		if( $('#select_staff_list > .list > li').length<1) { alert('지정부터 해주세요'); return false;}
		if(confirm('배정을 완료하시겠습니까?')) {
			var $list = '';
			 $('#select_staff_list > .list > li').each(function(index, element) {
	           $list += ($list!=''?'|':'') + $(this).text()+'['+$(this).find('span:first-child').attr('class')+']';
	        });
			
			var $idx = $(this).attr('title');
			$.post("/ybscript.io/approval/approval_proc",{
				items : $list,
				idx : $idx,
				type : $('input[name="search_key"]').val(),
				process : 'pass',
				menu_id : $("#target").attr('value'),
				board_idx : $("#board_idx").val(),
				board_id : $('input[name="board_id"]').val(),
				operation:'select_pass'
			}, function(req) {
				if(req=='OK') {
						$('body').fadeOut('fast', 0,function() {
							alert('배정완료');
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
		

		## --------------------- 버튼 : start
		$print_button =  print_button_new('view', $data['permission'], $data['parameter'], $url, $img_url, $user_info, $arr_data);
		if( !empty($print_button ) ){
		echo '<!-- 버튼 START --><div class="board_btn_box align_right">'.$print_button.'</div><!-- 버튼 END -->';	
		}
		## --------------------- 
	
		## ---------------------  댓글
		if($data['use_comment'] == 'true') include_once($data['module_root'].'/_plugin/skin/comment.php');
		## --------------------- 

## 20140704 신윤복 페이지담당자 게시글 관리기능 제한.
//if($data['permission']['admin'] == true && $_SYSTEM['permission']['manage'] != true) include_once($data['module_path'].'/admin.php');
//if($data['permission']['admin'] == true) include_once($data['module_path'].'/admin.php');
?>