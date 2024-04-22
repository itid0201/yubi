<?php
echo $data['debug']; // only test..

//print_r($data);
//2012-10-10 황재복 : 휴대폰 번호 자동 차단 방지
if($_SYSTEM['module_config']['use_phone_filter'] == 'false') {  
	$data['title']=preg_replace('/01([016789])[-~.[:space:]]([0-9]{3,4})[-~.[:space:]]([0-9]{4})/','01\1－\2－\3',$data['title']);
	$data['contents']=preg_replace('/01([016789])[-~.[:space:]]([0-9]{3,4})[-~.[:space:]]([0-9]{4})/','01\1－\2－\3',$data['contents']);
}
?>

<?php if(!empty($data['null_check'])) echo '<ul class="type03">'.$data['null_check'].'</ul>';?>


<?php
	//2012-10-09 황재복 : 글쓰기씨 상단문구가 빠져 있어 수정
	## 상단문구 출력
	if(!empty($data['write_msg'])) {
		if($data['device']=='mobile') echo '<p>'.stripslashes($data['write_msg']).'</p>';
		else echo '<div class="sub-tit-top"></div><div class="content_top_alert"><div class="alert_content">'.stripslashes($data['write_msg']).'</div></div><div class="sub-tit-bottom"></div>';
	}
?>
<div class="board_wrap">
<div class="form_write">
<p class="help_txt"><span class="icon_help2">도움말</span>(*)항목은 필수사항 입니다.</p>
<form id="write_form" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" name="selectForm" onkeydown="return captureReturnKey(event)" enctype="multipart/form-data">
<fieldset>
	<legend>필수입력사항</legend>
	<input type="hidden" name="noti_list" />
	<input type="hidden" name="dept_name" />	
<table class="board_write t_setting">
	<caption>이 표는 <?php echo $_SYSTEM['menu_info']['title']; ?>내용 입력란을 제공하며 <?php if($data['use_lock'] == 'true') echo '공개여부, ';?><?php if($data['use_top'] == 'true') echo '공지등록, ';?>제목, 등록자, <?php if($data['use_category_1'] == 'true') echo '분류, ';?><?php if($data['use_tag']=='true' && !empty($data['tag_list_all']))  echo 'tag 등록, ';?><?php if($data['get_guest_info'] == 'true' && ($_SYSTEM['myinfo']['user_level'] == 11 || $_SYSTEM['myinfo']['user_level'] == 99)) echo '회원주소, 회원연락처, ';?><?php if($data['mode'] != 'reply' && ($data['use_reply_sms'] == 'all' || $data['use_reply_sms'] == 'admin')) echo '답변 수신번호, ';?>내용으로 구성되어 있습니다.</caption>
	<tbody>
		<?php
		if($data['use_lock'] == 'true'){	
			$open_allow = $data['open'] == 'y' ? 'checked="checked"' : '';
			$open_deny  = $data['open'] == 'y' ? '' : 'checked="checked"';
		?>
		<tr>
			<th scope="row"><label>공개여부<?php check_required_field_wirte_mode($_SYSTEM['module_config']['required_value'], 'open'); ?></label></th>
			<td>
            	<div class="item">
				<input type="radio" name="open" id="open_allow" value="y" <?php echo $open_allow; ?> /><label for="open_allow">공개</label>&nbsp;&nbsp;&nbsp;
				<input type="radio" name="open" id="open_deny" value="n" <?php echo $open_deny; ?> /><label for="open_deny">비공개</label>
                </div>
			</td>
		</tr>
		<?php } //if end ?>                
		<?php
		## 서희진 작업 : 공지로 등록하기를 글쓰기/수정하기에서 할수 있도록.
		## 2018-09-04 황재복 : 박옥이 주무관 요청. 자유게시판 관리자만 공지글 사용 가능하게
		if($data['use_top'] == 'true' && $_SESSION['user_level'] <= 6){	
			$top_checked = $data['top'] == 'y' ? 'checked="checked"' : '';
		?>
		<tr>
			<th scope="row">공지등록</th>
			<td>
				<input type="checkbox" name="top" id="top" value="y" <?php echo $top_checked; ?> /><label for="top">공지로 등록</label>&nbsp;&nbsp;&nbsp;
			</td>
		</tr>
		<?php } //if end ?>
		<tr>
			<th scope="row"><label>발송여부</label></th>
			<td> 
            	<div class="item">
				<input type="checkbox" name="send_sms_push" id="send_sms_push" value="y"/><label for="send_sms_push">&nbsp;&nbsp;&nbsp;PUSH보내기</label>
                </div>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="title">제목<em>(*)</em></label></th>
			<td>
                <?php 
                if($data['use_title_style'] == 'true' && !empty($data['title_style_list'])) {
                    foreach($data['title_style_list'] as $value) {
                        $title_style .= '<option value="'.$value.'"'.($value==$data['title_style']?' selected="selected"':'').'>'.$value.'</option>';
                    }
                    echo '<select name="title_style" id="title_style">'.$title_style.'</select>';
                }
                ?>
                <input type="text" class="w95" name="title" id="title" value="<?php echo $data['title'];?>" />
            </td>
		</tr>
		<tr>
			<th scope="row"><div class="title"><label for="reg_name">등록자<em>(*)</em><?php check_required_field_wirte_mode($_SYSTEM['module_config']['required_value'], 'open'); ?></label></div></th>
			<td>

				<?php if($data['writer_display'] == 'department') { ?>
                <input type="text" class="i_text w200" name="depart_name" id="reg_name" value="<?php echo $data['depart_name']; ?>" />
                <?php } elseif($data['writer_display'] == 'other_organ') { ?>
                <input type="text" class="i_text w200" name="reg_name" id="reg_name" value="<?php echo $data['reg_name']; ?>" />
                <?php } else { ?>
                <input type="text" class="i_text w200" name="reg_name" id="reg_name" value="<?php echo $data['reg_name']; ?>" <?php echo ($data['read_only_reg_name'] == 'true' ? 'readonly="readonly"' : '')?> />
                <?php } ?>

            </td>
		</tr>
		<?php
			$dept_1_all = unserialize($data['dept_1_all']);
			$dept_1 = '';
			$dept_1 .= '<option value="all">전체</option>';
			$dept_1 .= '<option value="dept">부서선택</option>';		
			$dept_1 .= '<option value="group">직원선택</option>';
			foreach($dept_1_all as $key=>$value) {
//				$dept_1 .= '<option value="'.$value.'"'.($value==$data['dept_1']?' selected="selected"':'').'>'.$value.'</option>';
			}
		?>
        <tr class="dept" style="display: none">
            <th scope="row"><label for="dept_1">수신 직원 선택</label></th>
			<td class="member_setting">
	            <select name="dept_1" id="dept_1"><?php echo $dept_1;?></select>
				<?php
				$office_option = '';
				if( count($data['dept_office']) > 0  ){
					foreach( $data['dept_office'] as $value){
						$office_option .= '<option value="'.$value['id'].'">'.$value['title'].'</option>';
					}
				}				
				?>
				<div class="wrap_dept">
					<select name="dept1"><option value="">선택</option><?php echo $office_option ?></select>
					<div class="scroll_wrap">
						<!--<div class="member_list"></div>-->
					</div>
				</div>
				<div class="wrap_member">
					<div class="search_box2">
						<div class="input_box">
							<label for="item_search">검색어를 입력해주세요</label>
							<input type="text" id="item_search" name="item_search" autocomplete="off" /><!--<a href="#none"  class="apply"><span>적용</span></a><a href="#none" class="close">닫기</a>-->
						</div>
						<div id="text_div2">
							<div id="search_text">
							</div>
						</div>
					</div>
					<div class="scroll_wrap"></div>
					<!--<div class="member_list"></div>-->
				</div>
			</td>
        </tr>        

		<?php if($data['mode'] != 'reply' && ($data['use_reply_sms'] == 'all' || $data['use_reply_sms'] == 'admin')) { ?>
		<tr>
			<th scope="row"><label for="reply_sms_recv_number">답변 수신번호</label></th>
			<td>
				<input type="text" class="i_text" id="reply_sms_recv_number" maxlength="14" size="14" name="reply_sms_recv_number" value="<?php echo $data['reply_sms_recv_number'];?>" title="답변글 SMS 수신번호" />

			</td>
		</tr>
		<?php }?>

		<tr>
			<th scope="row"><label for="contents">내용<em>(*)</em></label></th>
			<td><textarea name="contents" id="contents" rows="3" cols="70"><?php echo $data['contents'];?></textarea></td>
		</tr>
		<?php
		## 공공누리 입력창 2017.12.12 서희진 추가
		if( $data['use_open_type'] == 'true' ){ 		 
		?>
		<tr>
			<th scope="row"><label for="contents">공공누리 적용<em>(*)</em></label></th>
			<td>
				<?php echo upload_koglType_kogl($data);?>
			</td>
		</tr>
			
		<?php
		}//공공누리 입력창 끝
		?>
		<?php
		if($data['use_search_tag'] == 'true'){ ?>
		<tr>
			<th scope="row"><label for="search_tag">검색태그</label></th>
			<td><input type="text" class="i_text" style="width:350px;" size="50" name="search_tag" id="search_tag" value="<?php echo $data['search_tag']?>" /></td>
		</tr>
		<?php
		}//if end 
		?>
		<?php
		if($data['use_logoff_write'] == 'true' && $_SYSTEM['myinfo']['is_login'] != true){ 
		?>
		<tr>
			<th scope="row"><label for="passwd">패스워드<?php check_required_field_wirte_mode($_SYSTEM['module_config']['required_value'], 'passwd'); ?></label></th>
			<td><input type="password" class="i_text" style="width:350px;" size="50" name="passwd" id="passwd" value="" /></td>
		</tr>
		<tr>
			<th scope="row"><label for="search_tag">키코드입력<?php check_required_field_wirte_mode($_SYSTEM['module_config']['required_value'], 'text_keycode'); ?></label></th>
			<td>
                <p style="color:#00F"><?php echo $data['text_keycode'];?></p>
                <input type="text" class="i_text" style="width:350px;" size="50" name="text_keycode" id="text_keycode" value="" />
            </td>
		</tr>
		<?php
		}//if end 
		?>
        <?php
		 //if($data['file_upload_count'] > 0) echo upload_box_alt_tour($data['file_list']);
		 ## 갤러리 필수
		 $use_gallery_img = false;
//		 if( $_SYSTEM['module_config']['use_gallery_img'] == 'true')	$use_gallery_img = true;
//		 if($data['file_upload_count'] > 0) echo upload_box_alt_tour($data['file_list'], $iter, 0, $use_gallery_img, false, $data['file_upload_count']);
		 ?>
		</tbody>
</table>

<?php     
	/*2014-04-22 서희진 : 개인정보처리방침 필수 체크 기능 구현.
	* 설정값 use_privacy_write 가 사용(True)일때 / use_logoff_write 가 설정(True) 일때 / 비회원일때 적용됩니다.
	* $_SYSTEM['module_config']['privacy_msg'] : 내용 
	* 테스트를 위해서 use_privacy_write 가 사용(True)일때만 보이도록 셋팅함. 개발후 if문 주석과 변경.
	*/
//	if($_SYSTEM['module_config']['use_privacy_write'] == 'true' && $_SYSTEM['module_config']['use_logoff_write'] == 'true' && $_SYSTEM['myinfo']['is_login'] != true ){ // 실제 사용 되어야할 if문
	if($_SYSTEM['module_config']['use_privacy_write'] == 'true' ){	
?>

    <div>
    	<p><label for="privacy_html">개인정보처리방침</label></p>
	    <p class="joinPoint">아래 개인정보처리방침을 반드시 읽어보시고 '동의'에 체크하신 후 '확인'버튼을 눌러주세요</p>        
		<p><textarea name="privacy_html" id="privacy_html" rows="5" cols="100" readonly><?php echo stripslashes($_SYSTEM['module_config']['privacy_msg']) ?>
        </textarea>
        </p>
		<p><label for="agree_privacy">개인정보처리방침에 동의합니다</label> <input type="checkbox" name = "agree_privacy" id="agree_privacy" value="y" <?php echo ($data['agree_privacy'] == 'y') ? 'checked="checked"' : '' ?>/></p>        
    </div>
<?php 
	}
	// 개인정보 보호 방침 끝.
?>
</fieldset>

<?php
	$hidden = '';
	foreach($data['hidden'] as $key=>$value) if(!is_null($value)) $hidden .= '<input type="hidden" name="'.$key.'" id="'.$key.'" value="'.$value.'" />';
	echo $hidden;
?>
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
	
	if($_SYSTEM['module_config']['use_privacy_write'] == 'true' ){ ## 개인정보이용동의체크시
		echo print_button_private_write('write', $data['permission'], $data['parameter'], $url, $img_url, $user_info, $arr_data);
	} else {
		echo print_button('write', $data['permission'], $data['parameter'], $url, $img_url, $user_info, $arr_data);
	}
?>
</form>
</div>
</div>
<?php 
	## 네이버 스마트 웹에디터 설정
	if($data['use_editor'] == 'true') $editor_info = naver_smart_editor('v_btn_confirm', 'contents');
?>
<script type="text/javascript">

	$("#v_btn_confirm").bind("click", function (event) {
		if($('#title').val() == '') {
			alert('제목 입력은 필수사항입니다.');
			$('#title').focus();
			return false;
		}
		
		if($('#reg_name').val() == '') {
			alert('등록자 입력은 필수사항입니다.');
			$('#reg_name').focus();
			return false;
		}
		
		if($('#contents').val() == '') {
			alert('내용 입력은 필수사항입니다.');
			$('#contents').focus();
			return false;
		}
		return true;
	});	
</script>
<script>
$(function(){	
	showDiv('all');
	
	/* 수신 직원 선택 타입 */
	$("select#dept_1").on("change",function(){
		var type = $(this).val();
		showDiv(type);
	});
			
	/* 부서 선택 */
	$("div.wrap_dept").on("change","select",function(){
		var code = $(this).val();
		var $obj_this = $(this);
		var obj_idx = $(this).attr("name").replace("dept","");
		var this_idx = 0;
		
		if( code == "" ){
			/*console.log( $(".wrap_dept").find("select[name='dept"+(eval(obj_idx)+1)+"']") );*/
			$(".wrap_dept").find("select[name='dept"+(eval(obj_idx)+1)+"']").remove();
			if( obj_idx == 1 ){
				$(".wrap_dept").find("a.dept_apply").remove();
			}	
			/* select 초기화 */
			$(".wrap_dept").find("select").each(function(){
				this_idx = $(this).attr("name").replace("dept","")								
				if( eval(this_idx) > eval(obj_idx) ){
					$(this).remove();
				}
			});			
			
		}else{
			/* select 초기화 */			
			$(".wrap_dept").find("select").each(function(){
				this_idx = $(this).attr("name").replace("dept","")								
				if( eval(this_idx) > eval(obj_idx) ){
					$(this).remove();
				}
			});	
				$(".wrap_dept").find("select[name='dept"+(eval(obj_idx)+1)+"']").remove();
				$.ajax({
					async : false,
					type: 'POST',
					url: "/ybscript.io/staff/department_manage_proc",
					dataType: 'json',
					data : { 
						operation:'depart_sub'
						, code : code
					}, 
					success : function (data) {

						if( data.length > 0 ){
							var str = '';
							str = '<option value="">선택</option>';
							for(var i=0; i < data.length; i++){
								str += '<option value="'+data[i]['id']+'">'+data[i]['title']+'</option>';
							}
							
							if( $(".wrap_dept").find("a.dept_apply").length < 1){
								$obj_this.after('<a href="#none"  class="dept_apply"><span>적용</span></a>');															
							}
							$(".wrap_dept").find("a.dept_apply").before('<select name="dept'+(eval(obj_idx)+1)+'">'+str+'</select>');

						}
					}
				});				
			
			
		}	
		
	});
	
	
	/* 부서 선택 적용*/
	$("div.wrap_dept").on("click","a.dept_apply",function(){
		var lastObj = $(this).prev();		
		
		if( $(this).prev().val() == "" ){
			lastObj = $(this).prev().prev();
		}
		
		
		var dept_code = lastObj.val();
		var dept_name = lastObj.find("option[value='"+dept_code+"']").text();
		
		console.log('>> '+dept_name);
		
		 $.post("/ybscript.io/staff/department_manage_proc",{
			dept_code : dept_code,
			search_key : 'app_dept',
			operation:'search_item'
		  },function(data){
			 /*console.log(data);*/
			 	$('div.member_list').remove();
				$('.wrap_dept .scroll_wrap').show().fadeIn().append('<div class="member_list"><div class="wrap_list_count"><span class="member_list_count">0</span>명 선택</div><ul class="member_item"></ul></div>');			 
				$('ul.member_item').append(data);
				set_member();
			 	$('input[name="dept_name"]').val(dept_name);
		  });	
		
		
	});
	
	/* 직원검색 */
	if($.browser.opera==true || $.browser.mozilla==true ) {
		$('input[name="item_search"]').keypress(function(event){
			trick();
		});
	} else {
		$('input[name="item_search"]').keyup(function(event){
			if(event.keyCode!=27){
				if( event.keyCode ==13 ){
					return false;
				}
				search_script( $(this).val() );
			}
		});
	}
	/* 직원검색 */
	$('input[name="item_search"]').focus(function() {
		$(this).keypress();
	});
	$('input[name="item_search"]').click(function() {
		$(this).focus();
	});
	
	/* 선택 직원 삭제 */
	$(document).on("click","ul.member_item li a.itme_del",function(){
		$(this).parent("li").remove();	
		set_member();
		
		if( $("div.member_list ul li").length == 0  ){
			$("div.member_list").remove();	
		}		
		
	});
	
	$('.search_box2 .close').click(function() {  $('input[name="item_search"]').val(''); $('#text_div2').hide('fast'); });
		
});
	
var db = "";
function trick() {
  db = $('input[name="item_search"]').val();
  search_script(db);
}	
	
function showDiv(type){	
	$('div.member_list').remove();
	set_member();
	if( type == 'all' ){
		$("div.wrap_dept, div.wrap_member, div#text_div2").hide();		
		$("div.wrap_dept").find('select[name="dept1"]').children("option:eq(0)").click();
		
	}
	
	if( type == 'dept'){
		$("div.wrap_dept, div.wrap_member").hide();
		$("div.wrap_dept").show();
	}
	
	if( type == 'group' ){
		$("div.wrap_dept, div.wrap_member").hide();
		$("div.wrap_member").show();
	}
}
	
function search_script(keyword) {
	  $.post("/ybscript.io/staff/department_manage_proc",{
		keyword : keyword,
		search_key : 'app_staff',
		operation:'search_item'
	  },function(data){
		  $('#text_div2').fadeIn();
		  $('#search_text').html('<ul><\/ul>');
		  $('#search_text ul').html(data);
		  $('.stext').click(function(){
			
			  $('#text_div2').fadeOut().find('li.stext').remove();

			 if( $('div.member_list').length > 0 ){
				 $('.wrap_member .scroll_wrap').show().fadeIn();
			 }else{
				 $('div.member_list').remove();
				$('.wrap_member .scroll_wrap').show().fadeIn().append('<div class="member_list"><div class="wrap_list_count"><span class="member_list_count">0</span>명 선택</div><ul class="member_item"></ul></div>');			
			 }

			 $('div.member_list ul.member_item').append('<li id="'+$(this).children('span').attr('id')+'"><a hef="#none" class="itme_del">'+$(this).children('span').attr('value')+' <span class="icon">삭제</span></a></li>');
			 $('input[name="item_search"]').val('');
			 set_member();

			  
		  });
	  });
	}	
function set_member(){
	var member = [];
	$("input[name='noti_list'], input[name='dept_name']").val('');
	$("div.member_list ul.member_item li").each(function(){
		member.push($(this).attr("id").replace("user_",""));
		/*console.log($(this).attr("id").replace("user_",""));*/
	});
	/*console.log( member );*/
	$("input[name='noti_list']").val(member);
	
	/* 스크롤 over시 스타일 적용하기*/
	if( $("div.member_list").height() <  $("ul.member_item").height()){
		$(".scroll_wrap").addClass("over");
	}else{
		$(".scroll_wrap").removeClass("over");
	}
	
	
	$("div.member_list").find("span.member_list_count").html($("div.member_list").find("ul li").length);
	/*console.log( $("div.member_list").height()+' / '+$("ul.member_item").height() );*/
	
}
	
$('input[type="text"]').each(function(){
	var labelID = $(this).attr("id");
	if( $(this).val().length === 0 ){
		$(this).siblings("label[for="+labelID+"]").show();
	}else{
		$(this).siblings("label[for="+labelID+"]").hide();
	}
}).focus(function(){
	var labelID = $(this).attr("id");
	$(this).siblings("label[for="+labelID+"]").hide();
}).focusout(function(){
	var labelID = $(this).attr("id");
	if( $(this).val().length === 0 ){
		$(this).siblings("label[for="+labelID+"]").show();
	}else{
		$(this).siblings("label[for="+labelID+"]").hide();
	}
})
	
function captureReturnKey(e) {
 if(e.keyCode==13 && e.srcElement.type != 'textarea')
 return false;
}
	
$('#send_sms_push').on("click",function(){
	if($(this).is(':checked')){
		$('.dept').show();
	}else{
		$('.dept').hide();
	}
})
</script>