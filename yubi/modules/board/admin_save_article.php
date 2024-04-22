<?php
$save_action_url = str_replace('&amp;','&',$_SERVER['REQUEST_URI']);
$save_action_url = str_replace('&','&amp;',$move_action_url);
?>
<form name="admin_save_article" id="admin_save_article" method="post" action="<?php echo $save_action_url;?>">
<input type="hidden" name="se_mode" value="article_save" />
<table class="boardlist" summary="게시물 설정하기">
	<caption><?php echo htmlentities($data['title'], ENT_QUOTES | ENT_IGNORE, "UTF-8");?></caption>
	<tr>
		<th scope="row" class="th_150px">제목</th>
		<td><?php echo htmlentities($data['title'], ENT_QUOTES | ENT_IGNORE, "UTF-8");?></td>
	</tr>
	<tr>
		<th scope="row" class="th_150px">승인처리</th>
			<td>
            <input type="radio" name="allow" value="y" id="save_allow_y" <?php echo $data['allow'] == 'y' ? 'checked="checked"' : '' ?> />
			<label for="save_allow_y">승인</label>
			<input type="radio" name="allow" value="n" id="save_allow_n" <?php echo $data['allow'] != 'y' ? 'checked="checked"' : '' ?> />
			<label for="save_allow_n">비승인</label>
		</td>
	</tr>
    <?php 
	if($data['use_process_1'] == 'true' && isset($data['process_1_list'])) {
		$process_1_list = unserialize($data['process_1_list']);
		for($iter=0;$iter<count($process_1_list);$iter++) {
			$process_1_list_all .= '<option value="'.$process_1_list[$iter].'" '.($process_1_list[$iter] == $data['process_1'] ? 'selected="selected"' : '').'>'.$process_1_list[$iter].'</option>';
		}
	?>
	<tr>
		<th scope="row" class="th_150px">처리상태</th>
		<td>
        <select name="process_1" id="process_1">
          <?php echo $process_1_list_all; ?>
        </select>
		</td>
	</tr>
    <?php }?>
    <?php if($data['use_top'] == 'true') {?>
	<tr>
		<th scope="row" class="th_150px">머릿글 설정</th>
        <td>
            <input type="radio" name="top" value="y" id="top_y" <?php echo $data['top'] == 'y' ? 'checked="checked"' : '' ?> />
            <label for="top_y">설정</label>
            <input type="radio" name="top" value="n" id="top_n" <?php echo $data['top'] != 'y' ? 'checked="checked"' : '' ?> />
            <label for="top_n">해제</label>
            기간
            <input type="text" class="js_calendar datetime" name="top_start" value="<?php echo date("Y-m-d",strtotime($data['top_start']==""?date("Y-m-d"):$data['top_start']));?>" size="10" title="top 시작일" /> ~ 
            <input type="text" class="js_calendar datetime" name="top_end" value="<?php echo date("Y-m-d",strtotime($data['top_end']==""?date("Y-m-d"):$data['top_end']));?>" size="10" title="top 종료일" />
		</td>
	</tr>
    <?php }?>
	<tr>
		<th scope="row" class="th_150px">안내문 삽입</th>
		<td>
            <input type="radio" name="admin_comment_to" id="admin_comment_to_all" value="all" <?php echo $data['admin_comment_to'] == 'all' ? 'checked="checked"' : '' ?> />
            <label for="admin_comment_to_all">모두 보이도록</label>
            <input type="radio" name="admin_comment_to" id="admin_comment_to_writer" value="writer" <?php echo $data['admin_comment_to'] != 'all' ? 'checked="checked"' : '' ?> />
            <label for="admin_comment_to_writer">작성자만 보이도록</label>
            <textarea id="admin_comment" name="admin_comment"  rows="4" cols="47"><?php echo preg_replace('/(<br[^>]+>)/',"\n",$data['admin_comment']);?></textarea>
		</td>
	</tr>
    <?php if($data['del'] == 'y') {?>
	<tr>
		<th scope="row" class="th_150px">게시물복구</th>
        <td>
            <input type="radio" name="del" value="n" id="del_n" <?php echo $data['del'] != 'y' ? 'checked="checked"' : '' ?> />
            <label for="del_n">복구</label>
            <input type="radio" name="del" value="y" id="del_y" <?php echo $data['del'] == 'y' ? 'checked="checked"' : '' ?> />
            <label for="del_y">삭제</label>
		</td>
	</tr>
    <?php }?>
	<tr>
		<td colspan="2">
            <input type="submit" id="btn_save_article" value="설정하기" title="설정하기"/>
            <input type="button" name="btn_erase_article" id="btn_erase_article" value="영구삭제" title="게시물 영구삭제" />
		</td>
	</tr>
</table>
</form>
<?php
$erase_action = $_SERVER['PHP_SELF'].'?mode=erase&idx='.$data['idx'];
?>
    <?php if($data['use_top'] == 'true') {?>	
	<script type="text/javascript" src="/js/jquery/datetimepicker/jquery.datetimepicker.js"></script>
	<link rel="stylesheet" type="text/css" href="/js/jquery/datetimepicker/jquery.datetimepicker.css" />    
    <?php }?>
<script name="import_jquery.ready">
	$("#btn_erase_article").click(function() {
		var is_delete = confirm("영구삭제하면 복구하지 못합니다. 삭제하시겠습니까?");
		if(is_delete == true) {
			$("#admin_save_article").attr({
				action:"<?php echo $erase_action;?>",
				method:"post"
			}).submit();
		}
	});
	
	<?php if( $data['use_process_1'] == 'true' && isset($data['process_1_list']) &&  $_SYSTEM['module_config']['skin_style'] == "petition" /* $_SYSTEM['module_config']['skin_style'] == "discussion"   */ ) {  ?>
	$("input[name='allow']").on("click",function(){
		if( $(this).attr("id") == "save_allow_y" ){
			$("select[name='process_1']").val("진행중").prop("selected", true);
		}else{
			$("select[name='process_1']").val("대기중").prop("selected", true);
		}
	});
	
	$("form[name='admin_save_article']").submit(function(){
		console.log( $("input[id='save_allow_y']").prop("checked") );
		if( $("input[id='save_allow_y']").prop("checked") ){
			alert("승인시에는 처리상태를 진행중으로 변경하셔야합니다.\n청원 일정이 설정됩니다.");
			/*$("select[name='process_1']").val("진행중").prop("selected", true);*/
		}else{
			alert("비승인시에는 처리상태를 대기중으로 변경하셔야합니다.\n청원 일정이 초기화 됩니다.");
			/*$("select[name='process_1']").val("대기중").prop("selected", true);*/
		}
		
	});
	<?php } ?>
	
	<?php if( $data['use_process_1'] == 'true' && isset($data['process_1_list']) &&  $_SYSTEM['module_config']['skin_style'] == "discussion" ) {  ?>
	$("input[name='allow']").on("click",function(){
		if( $(this).attr("id") == "save_allow_y" ){
			$("select[name='process_1']").val("토론중").prop("selected", true);
		}else{
			$("select[name='process_1']").val("대기중").prop("selected", true);
		}
	});
	/*
	$("form[name='admin_save_article']").submit(function(){
		console.log( $("input[id='save_allow_y']").prop("checked") );
		if( $("input[id='save_allow_y']").prop("checked") ){
			alert("승인시에는 처리상태를 진행중으로 변경하셔야합니다.\토론 일정이 설정됩니다.");	
		}else{
			alert("비승인시에는 처리상태를 대기중으로 변경하셔야합니다.\n청원 일정이 초기화 됩니다.");	
		}		
	});*/
	<?php } ?>
	
/*	$('.js_calendar').datetimepicker({
		prevText: '<전달',
		nextText: '다음달>',
		ampm: false,
		monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
		monthNamesShort: ['1','2','3','4','5','6','7','8','9','10','11','12'],
		dayNames: ['일요일','월요일','화요일','수요일','목요일','금요일','토요일'],
		dayNamesShort: ['일','월','화','수','목','금','토'],
		timeText: '시간설정',
		hourText: '시(H)',
		currentText: '오늘날짜',
		closeText: '선택',
		showTime: false,
		showHour: false,
		showMinute: false,
		dateFormat: 'yy-mm-dd',
		timeFormat: ''
	});*/
    <?php if($data['use_top'] == 'true') {?>	
	
		$('.datetime').datetimepicker({
			lang:'ko',
			prevText: '〈전달',
			nextText: '다음달〉',
			ampm: false,
			timepicker:false,
			format:'Y-m-d',
			formatDate:'Y-m-d'		
		});	
	<?php }?>
</script>
