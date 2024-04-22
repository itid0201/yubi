<?php echo $data['debug']; // only test.. ?>
<?php
//2012-10-10 황재복 : 휴대폰 번호 자동 차단 방지
if($_SYSTEM['module_config']['use_phone_filter'] == 'false') {  
	$data['title']=preg_replace('/01([016789])[-~.[:space:]]([0-9]{3,4})[-~.[:space:]]([0-9]{4})/','01\1－\2－\3',$data['title']);
	$data['contents']=preg_replace('/01([016789])[-~.[:space:]]([0-9]{3,4})[-~.[:space:]]([0-9]{4})/','01\1－\2－\3',$data['contents']);
}
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
if($_SYSTEM['permission']['admin']!=true) {
	$data['reg_name'] = preg_replace('/(.{1})(.*?)$/su','$1○○',$data['reg_name']);
}

?>
<?php
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
if($_SYSTEM['hostname']!='business'&&$_SYSTEM['permission']['admin']==true) $damdang=true;
?>
<input type="hidden" id="board_idx" value="<?php echo $data['idx']; ?>"  />
<div class="module_view_box m_approval_wrap">	<!-- =======컨텐츠는 content_wrap로 감싸고 모듈은 module_wrap 으로 감싸야합니다.=====  -->
  <div class="view_titlebox">
  	<h3><?php echo $data['title']; ?></h3>
    <p><span class="date"><?php echo date('Y.m.d ',strtotime($data['reg_date'])); ?></span><span class="name"><?php echo($data['writer_display'] == 'department' ? $data['depart_name'] : $data['reg_name']) ?></span></p>
	<?php 	if(!empty($data['file_list'])) {  ?>    
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
		
  	echo $data['contents']; 
  
	## 담당자 안내문
	if(!empty($data['admin_comment'])) {
		echo '<div class="admin_notice"> ';
		echo '	<p class="title"><span class="icon"></span>담당자 안내글</p> ';
		echo '    <p>'.$data['admin_comment'].'</p> ';
		echo '</div> ';	
	}    
?>    		
    </div>		
  </div>

<?php if(($_SESSION['user_level']<=6&&$data['open']=='y')||($data['open']=='n'&&($_SYSTEM['permission']['manage']==true||$damdang==true))) { ?>
<div class="comment">
	<h3 class="approval_title">등록자 정보</h3>
    <div class="comment_input">
		<ul class="member_info">
			<li><span class="label">접수자</span><span class="txt"><?php echo $data['reg_name']; ?>(<?php echo $data['reg_pin']; ?>)</span></li>
		    <li><span class="label">연락처</span><span class="txt"><?php echo $data['phone']; ?></span></li>
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
		<p><input type="checkbox" value="true" name="sms_send" id="sms_send" checked="checked" /> <label for="sms_send">접수처리되었음을 신청자(민원인)에게 SMS로 알림</label></p>
        <p>※ 접수가 완료되면 신청자는 더이상 내용을 수정하거나 삭제할 수 없습니다.</p>
		<p class="btn_right"><a href="#none" class="btn_big" id="accept_item"><span>접수처리</span></a></p>
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
		<p><input type="checkbox" value="true" name="sms_send" id="sms_send" checked="checked" /> <label for="sms_send">담당지정이 되었음을 신청자(민원인)에게 SMS로 알림</label></p>
        <p><input type="checkbox" value="true" name="sms_send2" id="sms_send2" checked="checked" /> <label for="sms_send2">배정된 담당자에게 SMS로 알림</label></p>
        <p><label for="deadline">답변 처리기한</label> <input type="text" value="<?php echo date('Y-m-d',strtotime('+3 days'));?>" name="deadline" id="deadline" size="10" autocomplete="off" /></p>
        <p class="staff_list"><?php
		global $member;
        $myinfo = $member->get_member_info($_SYSTEM['myinfo']['user_id']);
		$dept_info = $member->get_department_info($myinfo['dept_id']);
		echo '<span class="blank">'.str_replace('|',' ',$dept_info['dept_path_title']).' '. $myinfo['user_name'].'님의 즉시답변 : </span>';
		
		?> </p>
        <div class="answer_write"><textarea id="answer_contents"></textarea></div>
        <p class="btn_right"><a href="#none" class="btn_big" id="answer" title="<?php echo $data['approval']['list'][0]['idx']; ?>"><span>즉시답변완료</span></a></p> 
        <div class="search_box"> 
			<div class="bg"></div>
			<fieldset>
				<legend>검색</legend>
				<select name="search_key">
				    <option value="staff">직원</option>
				    <option value="department">부서</option>
                    <option value="work">업무</option>
				</select>
				<input type="text" name="staff_id" class="autocomplete" autocomplete="off" />
				<div id="text_div"><div id="search_text"></div></div>
				<a href="#none" class="btn_sml" id="append_staff"><span>담당추가</span></a> <a href="#none" class="btn_sml" id="sel_complete"><span>배정완료</span></a> <a href="#none" class="btn_sml"><span>초기화</span></a>
			</fieldset>
		</div>
        <div class="staff_list" id="select_staff_list"><span class="blank">&quot;담당추가&quot;를 클릭하여 담당자를 배정해주세요.</span></div>
    </form>
    <script type="text/javascript">
		var oEditors = [];
		nhn.husky.EZCreator.createInIFrame({
			oAppRef: oEditors,
			elPlaceHolder: "answer_contents",
			sSkinURI: "/js/smart_editor/SEditorSkin.html",
			fCreator: "createSEditorInIFrame"
		});
		document.getElementById("answer").onclick = function () {
			oEditors.getById["answer_contents"].exec("UPDATE_IR_FIELD", []);
				$.post("/ybscript.io/approval/approval_proc",{
					idx : $("#answer").attr('title'),
					content : $("#answer_contents").val(),
					approval_line: '1',
					operation:'reply_complete'
				}, function(req) {
					if(req=='OK') {
						$('body').fadeOut('fast', 0,function() {
							alert('답변완료');
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
		<p><input type="checkbox" value="true" name="sms_send" id="sms_send" checked="checked" /> <label for="sms_send">답변저장 후 완료시 신청자(민원인)에게 <strong>완료</strong>되었음을 SMS로 알림</label></p>
        <!--p><input type="checkbox" value="true" name="sms_send" id="sms_send" checked="checked" /> <label for="sms_send">답변저장 후 결재요청시 결재자에게 SMS로 알림</label></p-->
        <?php ?>
		<?php if($_SYSTEM['permission']['manage']==true) { ?>
	        <input type="hidden" name="board_id" value="<?php echo $_SYSTEM['menu_info']['value']['board_id']; ?>" />
			<p><label for="deadline">답변 처리기한 변경</label> <input type="text" value="<?php echo $data['approval']['deadline'];?>" name="deadline" id="deadline" size="10" autocomplete="off" /></p>
			<?php } else { ?>
	        <p><label for="deadline">답변 처리기한</label> : <?php $limit = !empty($data['approval']['deadline'])?$data['approval']['deadline']:date('Y-m-d',strtotime($data['reg_date'].' + 3day'));
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
			}
			?></p>
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
					$html .= (!empty($html)?'<span class="arrow_right"></span>':'').'<span class="mini_box" title="'.$proc['idx'].'">
					<em>'.preg_replace('/.*?\|([^\|]+[실|과|소|읍|면|동]+)\|.*$/i','$1',$proc['dept_path_title']).' '.$proc['user_name'].' '.$str_proc.' ('.date('Y.m.d H:i',strtotime($proc['processing_date'])).')'.($str_proc!='접수111'?'<a href="#none"><img alt="제외" src="/images/common/icon_delete_9.gif" /></a>':'').'</em>
					</span>';
					if($proc['process']=='complete') $reply = nl2br(htmlspecialchars_decode($proc['reply']));
				}
				//$reply = preg_replace('/<(p)([^>]*)>/i',"\n\n",$reply);
				//$reply = preg_replace('/<[^>]*>/i','',$reply);
				echo $html;
		        echo '</div>';
				if($_SYSTEM['permission']['manage']==true || $proc['user_id']==$_SYSTEM['myinfo']['user_id']) {
					echo '<div class="answer_write"><textarea cols="70" rows="10" id="answer_contents_'.$list['approval_line'].'">'.htmlspecialchars($reply).'</textarea></div>';
					echo '<p class="btn_right"><a href="#none" class="btn_sml" id="answer_submit_'.$list['approval_line'].'" title="'.$list['idx'].'"><span>답변완료</span></a> <!--a href="#none" class="btn_sml return_approve" title="'.$list['idx'].'"><span>서무담당에게 반송</span></a-->';
					echo ($_SYSTEM['permission']['manage']==true?'<a href="#none" class="btn_sml urging"  title="'.$list['idx'].'"><span>답변촉구요청</span></a> <a href="#none" class="btn_sml reset_select"  title="'.$list['idx'].'"><span>접수상태로변경</span></a>':'');
					echo '</p>';
					if($list['type']=='department') {
						echo '
					        <div class="search_box">
								<div class="bg"></div>

								<fieldset>
									<legend>검색</legend>
									<select name="search_key">
									    <option value="staff">직원</option>
									    <option value="department">부서</option>
					                    <option value="work">업무</option>
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
			<?php 
			foreach($answer_id as $id) {  ?>
			var oEditors<?php echo $id; ?> = [];
			nhn.husky.EZCreator.createInIFrame({
				oAppRef: oEditors<?php echo $id; ?>,
				elPlaceHolder: "answer_contents_<?php echo $id; ?>",
				sSkinURI: "/js/smart_editor/SEditorSkin.html",
				fCreator: "createSEditorInIFrame"
			});
			document.getElementById("answer_submit_<?php echo $id; ?>").onclick = function () {
				oEditors<?php echo $id; ?>.getById["answer_contents_<?php echo $id; ?>"].exec("UPDATE_IR_FIELD", []);
				$.post("/ybscript.io/approval/approval_proc",{
					idx : $("#answer_submit_<?php echo $id; ?>").attr('title'),
					content : $("#answer_contents_<?php echo $id; ?>").val(),
					operation:'reply_complete'
				}, function(req) {
					if(req=='OK') {
						$('body').fadeOut('fast', 0,function() {
							alert('답변완료');
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
		if($list['approval_line']!='0' ||($list['approval_line']=='0'&&$data['process_2']=='2') ) {
		//	$list['reply'] = preg_replace('/<(p|span|font)([^>]*)>/i','<\1>',$list['reply']);
			//$list['reply'] = preg_replace('/<[\/]?font[^>]*>/i','',$list['reply']);
			echo '<div class="progress_m">
					<strong class="tit">진행상태</strong>
					<ol>
						<li'.($list['process']=='reg'?' class="on"':'').'><span>신청'.($list['process']=='reg'?'<span class="now"></span>':'').'</span></li>
						<li'.($list['process']=='accept'?' class="on"':'').'><span>접수'.($list['process']=='accept'?'<span class="now"></span>':'').'</span></li>
						<li'.(($list['process']=='next'||$list['process']=='pass')&&$list['step']>=1?' class="on"':'').'><span>담당자<br />지정'.(($list['process']=='next'||$list['process']=='pass')&&$list['step']>=1?'<span class="now"></span>':'').'</span></li>
						<li'.($list['process']=='complete'?' class="on"':'').'><span>답변완료'.($list['process']=='complete'?'<span class="now"></span>':'').'</span></li>
					</ol>	
				</div>
				<div class="answer m_approval_default_answer">
					'.nl2br(strip_tags(htmlspecialchars_decode($list['reply']))).'
					<div class="staff"><span class="date">'.date('Y년m월d일 H시m분',strtotime($list['processing_date'])).'</span><span class="dept">'.str_replace('|',' ',$list['dept_path_title']).'</span><span class="name">'.$list['user_name'].(!empty($list['dept_tel'])?'('.$list['dept_tel'].')':'').'</span></div>
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
</div>
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
				board_idx : $("#board_idx").text(),
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
//]]>
</script>
<script type="text/javascript" src="/js/jquery/datetimepicker/jquery.datetimepicker.js"></script>
<link rel="stylesheet" type="text/css" href="/js/jquery/datetimepicker/jquery.datetimepicker.css" />
<script name="import_jquery.ready">
//<![CDATA[
	/*$('#deadline').datetimepicker({
		prevText: '<전달',
		nextText: '다음달>',
		ampm: false,
		monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
		monthNamesShort: ['1','2','3','4','5','6','7','8','9','10','11','12'],
		dayNames: ['일요일','월요일','화요일','수요일','목요일','금요일','토요일'],
		dayNamesShort: ['일','월','화','수','목','금','토'],
		timeText: '시간설정',
		hourText: '시(H)',
		YearText: '년',
		currentText: '오늘날짜',
		closeText: '선택',
		showTime: false,
		showHour: false,
		showMinute: false,
		dateFormat: 'yy-mm-dd',
		timeFormat: '',
		showTimepicker:false,
		onClose : function() {
			$.post("/ybscript.io/approval/approval_proc",{
				menu_id : $("#target").attr('value'),
				board_idx : $("#board_idx").text(),
				next_step : $('input[name="next_step"]').val(),
				board_id : $('input[name="board_id"]').val(),
				deadline : $(this).val(),
				operation:'deadline'
			},function(data){
				if(data=='OK') {
					alert('설정완료');
				} else {
					alert('ERROR'+data);	
				}
		
			});
				
		}
	});*/
	
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
				board_idx : $("#board_idx").text(),
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
				board_idx : $("#board_idx").text(),
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
				board_idx : $("#board_idx").text(),
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
			board_idx : $("#board_idx").text(),
			next_step : '3',
			board_id : $('input[name="board_id"]').val(),
			deadline : $('#deadline').val(),
			operation:'select_complete'
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
				board_idx : $("#board_idx").text(),
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
				board_idx : $("#board_idx").text(),
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
echo print_button_mobile('view', $data['permission'], $data['parameter'], $url, $img_url, $user_info, $arr_data);

if($data['use_comment'] == 'true') include_once($data['module_root'].'/_plugin/skin/comment.php');

## 20140704 신윤복 페이지담당자 게시글 관리기능 제한.
//if($data['permission']['admin'] == true && $_SYSTEM['permission']['manage'] != true) include_once($data['module_path'].'/admin.php');
//if($data['permission']['admin'] == true) include_once($data['module_path'].'/admin.php');
?>