<script type="text/javascript" >
	var selfUrl = "<?php echo $_SERVER['PHP_SELF'];?>";
	var mainimage_idx = "<?php echo $data['mainimage_idx'];?>";;
</script>
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
		else echo '<div class="board_guide2 guide_img10">'.stripslashes($data['write_msg']).'</div>';
	}
?>
<div class="board_wrap">
<div class="form_write mat30">
<p class="help_txt"><span class="icon_help2">도움말</span><span class="point_t">(*)</span>항목은 필수사항 입니다.</p>
<form id="write_form" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" name="selectForm" enctype="multipart/form-data">
<fieldset>
	<legend>필수입력사항</legend>
   
<table class="board_write" summary="이 표는 <?php echo $_SYSTEM['menu_info']['title']; ?> 내용 입력란을 제공하며 <?php if($data['use_lock'] == 'true') echo '공개여부, ';?> 신청자, 업소명, 전화번호, 유형, 콘텐츠현황, 사업자등록증, 콘텐츠 수집표 첨부, 이미지첨부 <?php if($data['use_category_1'] == 'true') echo '분류, ';?> <?php if($data['use_tag']=='true' && !empty($data['tag_list_all']))  echo 'tag 등록, ';?> <?php if($data['get_guest_info'] == 'true' && ($_SYSTEM['myinfo']['user_level'] == 11 || $_SYSTEM['myinfo']['user_level'] == 99)) echo '회원주소, 회원연락처, ';?><?php if($data['mode'] != 'reply' && ($data['use_reply_sms'] == 'all' || $data['use_reply_sms'] == 'admin')) echo '답변 수신번호, ';?> 로 구성되어 있습니다.">
	<caption><?php echo $_SYSTEM['menu_info']['title']; ?> 입력</caption>
    <colgroup>
        <col width="15%" />
        <col />
    </colgroup>
	<tbody> 
    <?php if($_SYSTEM['permission']['admin'] === true && $_SYSTEM['hostname'] == 'tourbiz'){ ?>
    	<tr>
        	<th scope="row"><label for="process_1">처리 상태</label></th>
            <td>
            	<select id="process_1" title="처리 상태" name="process_1">
            	<option <?php echo $data['process_1']=='신청' ? 'selected="selected"':''; if(empty($data['process_1'])) echo 'selected"selected"'; ?> value="신청">신청</option>
                <option <?php echo $data['process_1']=='접수' ? 'selected="selected"':''?> value="접수">접수</option>
                <option <?php echo $data['process_1']=='반려' ? 'selected="selected"':''?> value="반려">반려</option>
                <option <?php echo $data['process_1']=='완료' ? 'selected="selected"':''?> value="완료">완료</option>
                </select>
            </td>
        </tr>    
        <tr>
        	<th scope="row"><label for="admin_comment">반려사유</label>
            <td>
            	<input type="text" name="admin_comment" title="반려사유" id="admin_comment" class="w80" value="<?php echo $data['admin_comment'] ?>" />
            </td>
        </tr>   
	<?php } ?>  
		<tr>
			<th scope="row"><div class="title"><label for="reg_name"><?php echo ($data['writer_display'] == 'department' ? '등록부서' : '신청자')?><?php check_required_field_wirte_mode($_SYSTEM['module_config']['required_value'], 'open'); ?></label></div></th>
			<td>

				<?php if($data['writer_display'] == 'department') { ?>
                <input type="text" class="w20" name="depart_name" id="reg_name" value="<?php echo $data['depart_name']; ?>"  />
                <?php } elseif($data['writer_display'] == 'other_organ') { ?>
                <input type="text" class="w20" name="reg_name" id="reg_name" value="<?php echo $data['reg_name']; ?>" />
                <?php } else { ?>
                <input type="text" class="w20" name="reg_name" id="reg_name" value="<?php echo $data['reg_name']; ?>" <?php echo ($data['read_only_reg_name'] == 'true' ? 'readonly="readonly"' : '')?> />
                <?php } ?>

            </td>
		</tr>
		<tr>
			<th scope="row"><label for="title">업소명<em>(*)</em></label></th>
			<td>
                <?php 
                if($data['use_title_style'] == 'true' && !empty($data['title_style_list'])) {
                    foreach($data['title_style_list'] as $value) {
                        $title_style .= '<option value="'.$value.'"'.($value==$data['title_style']?' selected="selected"':'').'>'.$value.'</option>';
                    }
                    echo '<select name="title_style" id="title_style">'.$title_style.'</select>';
                }
                ?>
                <input type="text" class="w80" name="title" id="title" value="<?php echo $data['title'];?>" />
            </td>
		</tr>
		<tr>
			<th scope="row"><label>전화번호</label></th>
			<td>

				<?php
				if(!empty($data['phone_1']) || !empty($data['phone_2']) || !empty($data['phone_3'])) {
					$phone_1 = $data['phone_1'];
					$phone_2 = $data['phone_2'];
					$phone_3 = $data['phone_3'];
				} else {
					if(!empty($data['phone'])) list($phone_1, $phone_2, $phone_3) = explode('-',$data['phone']);
				}
				?>
				<input type="text" class="i_text w10" id="phone_1" maxlength="5" size="5" name="phone_1" value="<?php echo $phone_1;?>" title="회원 연락처 첫자리"/> - 
				<input type="text" class="i_text w10" id="phone_2" maxlength="4" size="5" name="phone_2" value="<?php echo $phone_2;?>" title="회원 연락처 중간자리"/> - 
				<input type="text" class="i_text w10" id="phone_3" maxlength="4" size="5" name="phone_3" value="<?php echo $phone_3;?>" title="회원 연락처 마지막자리" />

			</td>
		</tr>
		<tr>
			<th scope="row"><label>유형<?php check_required_field_wirte_mode($_SYSTEM['module_config']['required_value'], 'open'); ?></label></th>
			<td>
            	<div class="item">
				<input type="radio" name="varchar_1" id="food" value="음식점" <?php if(empty($data['varchar_1'])) echo 'checked="checked"'; echo $data['varchar_1'] == '음식점' ? 'checked="checked"' : ''; ?> /><label for="food">음식점</label>&nbsp;&nbsp;&nbsp;
				<input type="radio" name="varchar_1" id="lodge" value="숙박업소" <?php echo $data['varchar_1'] == '숙박업소' ? 'checked="checked"' : ''; ?> /><label for="lodge">숙박업소</label>
                </div>
			</td>
		</tr>
		<tr>
			<th scope="row"><label>콘텐츠현황<?php check_required_field_wirte_mode($_SYSTEM['module_config']['required_value'], 'open'); ?></label></th>
			<td>
            	<div class="item">
				<input type="radio" name="varchar_2" id="new" value="신규" <?php echo $data['varchar_2'] == '신규' ? 'checked="checked"' : ''; if(empty($data['varchar_2'])) echo 'checked="checked"'?> /><label for="new">신규</label>&nbsp;&nbsp;&nbsp;
				<input type="radio" name="varchar_2" id="modify" value="기존콘텐츠수정" <?php echo $data['varchar_2'] == '기존콘텐츠수정' ? 'checked="checked"' : ''; ?> /><label for="modify">기존 콘텐츠 수정</label>
                <input class="w70" type="text" name="varchar_3" value="<?php echo empty($data['varchar_3'])? '기존 콘텐츠 URL' : $data['varchar_3'] ?>"  /> 
                </div>
                
			</td>
		</tr>
        <tr>
          <th scope="row"><label>사업자등록증</label></th>
          <td>
          <?php
		  		$file_list_shop1 = $data['file_list_shop1'] ;
		  		$file_list_shop2 = $data['file_list_shop2'] ;
				$file_list_shop3 = array_merge($file_list_shop1,$file_list_shop2);
		   ?>
          
          <?php if((empty($file_list_shop1) && $data['process_1'] != '반려') || $_SYSTEM['permission']['admin'] === true) { 
		  for($i=0; $i<count($file_list_shop1); $i++ ){ ?>        
              <input class="chk_remove_file" type="checkbox" name="remove_shop_file_<?php echo $i ?>" id="remove_shop_file_<?php echo $i ?>" value="<?php echo $file_list_shop1[$i]['idx'] ?>" />
              <label for="remove_shop_file_<?php echo $i ?>" class="img_name1"> <?php echo $file_list_shop1[$i]['original_name']?> 삭제</label>
          <?php 
		  if(preg_match('/(photo)/u',$file_list_shop1[$i]['file_type'])) {
		  $path_info = explode('/', $file_list_shop1[$i]["file_path"]);
		  $img_path1 = $_SERVER['PHP_SELF'].'/ybmodule.file/'.$path_info[2].'/'.$path_info[3].'/400x1/'.$path_info[4];
		  $img_path1 = call::filePathFilter($img_path1);
		  }
		  ?>
          <?php 
		  		}
			} ?>
          	<div class="fileUpload fileUpload0">
			<span class="file_name"></span>
				<div class="fileUpload_button">
				<span>첨부파일</span> 
          		<input type="file" class="file_input" title="사업자등록증" name="shop_file_0" value="<?php echo $file_list_shop1[$idx]['title'] ?>" />
                </div>
            </div>
          </td>
        </tr>
        <tr>
          <th scope="row"><label>콘텐츠 수집표 첨부</label></th>
          <td>
          <?php if((empty($file_list_shop2) && $data['process_1'] != '반려') || $_SYSTEM['permission']['admin'] === true) {  ?>
          <?php 
		  for($j=0; $j<count($file_list_shop2); $j++ ){ ?>  
          <input class="chk_remove_file" type="checkbox" name="remove_shop_file_<?php echo $j+$i ?>" id="remove_shop_file_<?php echo $j+$i ?>" value="<?php echo $file_list_shop2[$j]['idx'] ?>" />
		  <label for="remove_shop_file_<?php echo $j+$i ?>" class="img_name2"> <?php echo $file_list_shop2[$j]['original_name']?> 삭제</label> 
          <?php 
		  if(preg_match('/(photo)/u',$file_list_shop2[$j]['file_type'])) {
		  $path_info = explode('/', $file_list_shop2[$j]["file_path"]);
		  $img_path2 = $_SERVER['PHP_SELF'].'/ybmodule.file/'.$path_info[2].'/'.$path_info[3].'/'.$path_info[4];
		  $img_path2 = call::filePathFilter($img_path2);
		  }
		  ?>             
          <?php 

		  		}
		 	} ;?>
          	<div class="fileUpload fileUpload1">
			<span class="file_name"></span>
				<div class="fileUpload_button">
				<span>첨부파일</span> 
          		<input type="file" class="file_input" title="콘텐츠 수집표 첨부" name="shop_file_1" value="<?php echo $file_list_shop2[$idx]['title'] ?>" />
                </div>
            </div>
          </td>
        </tr>
		<?php
		if($data['use_category_1'] == 'true') {
			$category_1_all = unserialize($data['category_1_all']);
			$category_1 = '';

			foreach($category_1_all as $key=>$value) {
				$category_1 .= '<option value="'.$value.'"'.($value==$data['category_1']?' selected="selected"':'').'>'.$value.'</option>';
			}
		?>
        <tr>
            <th scope="row"><label for="category_1">분류<?php check_required_field_wirte_mode($_SYSTEM['module_config']['required_value'], 'category_1'); ?></label></th>
			<td>
	            <select name="category_1" id="category_1"><?php echo $category_1;?></select>
			</td>
        </tr>
        <?php
        } //if end

		if($data['use_tag']=='true' && !empty($data['tag_list_all'])) {
			foreach($data['tag_list_all'] as $value){
				$tag_list .= '<label><input type="checkbox" name="tag_list[]" value="'.$value.'" '.(in_array($value, $data['tag_list']) ? 'checked="checked"' : '').' />'.$value.'</label>';
			}
		?>
        <tr>
            <th scope="row"><label>tag 등록</label></th>
            <td><?php echo $tag_list;?></td>
        </tr>
		<?php
		}

		if($data['get_guest_info'] == 'true' && ($_SYSTEM['myinfo']['user_level'] == 11 || $_SYSTEM['myinfo']['user_level'] == 99)) {
		?>
		<tr>
			<th scope="row"><label>회원주소</label></th>
			<td>
				<?php
				if(!empty($data['zipcode_1']) || !empty($data['zipcode_2'])) $data['zipcode'] = $data['zipcode_1'].$data['zipcode_2'];
				$address = array();
				$address['zipcode']   = $data['zipcode'];
				$address['address_1'] = $data['address_1'];
				$address['address_2'] = $data['address_2'];
				echo address_box($address);
				?>
			</td>
		</tr>

		<?php } //if end ?>


		<?php if($data['mode'] != 'reply' && ($data['use_reply_sms'] == 'all' || $data['use_reply_sms'] == 'admin')) { ?>
		<tr>
			<th scope="row"><label for="reply_sms_recv_number">답변 수신번호</label></th>
			<td>
				<input type="text" class="i_text" id="reply_sms_recv_number" maxlength="14" size="14" name="reply_sms_recv_number" value="<?php echo $data['reply_sms_recv_number'];?>" title="답변글 SMS 수신번호" />

			</td>
		</tr>
		<?php }?>

		<!--tr>
			<th scope="row"><label for="contents">내용<em>(*)</em></label></th>
			<td><textarea name="contents" id="contents" rows="15" cols="70"><?php echo $data['contents'];?></textarea></td>
		</tr-->

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
		if($data['use_logoff_write'] == 'true' && $_SYSTEM['myinfo']['is_login'] != true){ ?>
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
        
        <?php if($data['file_upload_count'] > 0 && count($data['img_file_list']) > 0) { ?>
        <tr>
        	<th scope="row"><label for="img_file">사진첨부</label></th>
            <td>
            	<p>목록페이지에 보여질 대표사진 1개를 체크해주세요. </p>
                <div class="photo_checks">
            	<?php echo upload_box_alt_ajax_image_shop($data['img_file_list'], $data['mainimage_idx']); ?>    
                </div>  
            </td>
        </tr>
        <?php } ?>
		<?php if($data['file_upload_count'] > 0 && count($data['img_file_list']) < 1) { ?>
                <?php echo upload_box_image_alt_smartour($data['file_list']); ?>
        <?php } ?>
		</tbody>
</table>



<?php     
	/*2014-04-22 서희진 : 개인정보처리방침 필수 체크 기능 구현.
	* 설정값 use_privacy_write 가 사용(True)일때 / use_logoff_write 가 설정(True) 일때 / 비회원일때 적용됩니다.
	* $_SYSTEM['module_config']['privacy_msg'] : 내용 
	* 테스트를 위해서 use_privacy_write 가 사용(True)일때만 보이도록 셋팅함. 개발후 if문 주석과 변경.
	*/
//	if($_SYSTEM['module_config']['use_privacy_write'] == 'true' && $_SYSTEM['module_config']['use_logoff_write'] == 'true' && $_SYSTEM['myinfo']['is_login'] != true ){
	if($_SYSTEM['module_config']['use_privacy_write'] == 'true' ){	
?>

    <div>
    	<p><label for="privacy_html">개인정보처리방침</label></p>
	    <p class="joinPoint">아래 개인정보처리방침을 반드시 읽어보시고 '동의'에 체크하신 후 '확인'버튼을 눌러주세요</p>        
		<p><textarea name="privacy_html" id="privacy_html" rows="5" cols="100" readonly="readonly"><?php echo stripslashes($_SYSTEM['module_config']['privacy_msg']) ?>
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
	echo '<input type="hidden" name="open" id="open_deny" value="n" />';
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
<div class="img_box"></div>
<?php 
	## 네이버 스마트 웹에디터 설정
	if($data['use_editor'] == 'true') $editor_info = naver_smart_editor('btn_form_submit', 'contents');
?>
<script type="text/javascript">
	$("form#write_form").bind("submit", function () {
		if($('#title').val() == '') {
			alert('업소명 입력은 필수사항입니다.');
			$('#title').focus();
			return false;
		}
		return true;
	});	
  <?php if(!empty($file_list_shop1)){ ?>  $('.fileUpload0').hide();	<?php } ?>
  <?php if(!empty($file_list_shop2)){ ?>  $('.fileUpload1').hide();	<?php }?>
	
	
	$('#remove_shop_file_0').click(function(){
		if($('#remove_shop_file_0').is(':checked')==true) {
			$(this).next().next().show();			
		}else{
			$(this).next().next().hide();
		}
	});
		
	$('#remove_shop_file_1').click(function(){
		if($('#remove_shop_file_1').is(':checked')==true) {
			$(this).next().next().show();
		} else {
			$(this).next().next().hide();
		}
	});	
	
	if($('#new').is(':checked')==true)		$('.w70').hide();
	else if($('#modify').is(':checked')==true)	 $('.w70').show();
	
	$('#modify').click(function(){
		$('.w70').show();
		$('.w70').click(function(){
			$(this).val('');
			});
		})
	$('#new').click(function(){
		$('.w70').hide();
		$('.w70').val('');
		})
	
	if($('#process_1').val() != '반려')	$('#admin_comment').parent().parent().hide();
	$('#process_1').change(function(){
		if($(this).val()=='반려')	$('#admin_comment').parent().parent().show();
		else $('#admin_comment').parent().parent().hide();
	});
	// layer 중간 찾기.
	jQuery.fn.center = function () {
		this.css("top", Math.max(0, (($(window).height() - $(this).outerHeight()) / 2) + $(window).scrollTop() - 236) + "px");
		this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) + $(window).scrollLeft() + ($(this).width /2)) + "px");
		return this;
	}
		
   $("div#img_box").css("display","none");
<?php for($i=1; $i<=count($file_list_shop3); $i++){	?>
	$('.img_name<?php echo $i ?>').mouseover(function(){
		var img_path1 = "<?php echo $img_path1  ;?>";
		var img_path2 = "<?php echo $img_path2  ;?>";
		var obj = $('div.img_box');
		var html = '';
			html += '<div class="img_view" style="top: 300px; position: absolute; left: 40%" >';
			html += '<img src = "'+img_path<?php echo $i ?>+'" />';
			html += '</div>';
		$('.img_view').remove();	
		$(obj).append(html);
		$(obj).show();
		$(obj).center();
		})
		
		$('.img_name<?php echo $i ?>').mouseout(function(){
			$('.img_view').remove();	
			$('.img_view').hide();	
			})	
<?php } ?>
</script>

