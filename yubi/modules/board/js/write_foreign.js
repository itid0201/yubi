$(function() { 
	
	$('.datetime').datetimepicker({
		lang:'ko',
		prevText: '〈전달',
		nextText: '다음달〉',
		ampm: false,
		timepicker:false,
		format:'Ymd',
		formatDate:'Y-m-d'		
	});	
	
	$('.datetime_gongik').datetimepicker({
		lang:'ko',
		prevText: '〈전달',
		nextText: '다음달〉',
		ampm: false,
		timepicker:false,
		format:'Y-m-d',
		formatDate:'Y-m-d'		
	});
	
	/**************  *******************/
	/*게시기간 설정 및 top설정 체크 action */
	$("input[type='checkbox'][name='period'], input[type='checkbox'][name='top'] ").on("click",function(){
		var now = new Date();
		var yearStart= now.getFullYear();
		var monStart = (now.getMonth()+1)>9 ? ''+(now.getMonth()+1) : '0'+(now.getMonth()+1);
		var dayStart = now.getDate()>9 ? ''+now.getDate() : '0'+now.getDate();		
		
		now.setDate(now.getDate() + 7); /* 기본값 일주일 설정 */
		var yearEnd= now.getFullYear();
		var monEnd = (now.getMonth()+1)>9 ? ''+(now.getMonth()+1) : '0'+(now.getMonth()+1);
		var dayEnd = now.getDate()>9 ? ''+now.getDate() : '0'+now.getDate();
		
		
		var module_w = $(this).parent(".module_t").siblings(".module_w"); 
		var name = $(this).attr("name"); /* 이름이 중요함. 체크박스 이름과 input box이름 시작이 동일해야함. */
		
		if( $(this).is(":checked") ){
			module_w.find("input[name='"+name+"_start']").val( module_w.find("input[name='"+name+"_start_hidden']").val().replace(/-/g,"") );
			module_w.find("input[name='"+name+"_end']").val( module_w.find("input[name='"+name+"_end_hidden']").val().replace(/-/g,"") );
			module_w.find("input").attr("disabled",false);
		}else{
			module_w.find("input[name='"+name+"_start']").val('').attr("placeholder",yearStart+monStart+dayStart);
			module_w.find("input[name='"+name+"_end']").val('').attr("placeholder",yearEnd+monEnd+dayEnd);
			module_w.find("input").attr("disabled",true);
		}
		
	});	
	/**************  *******************/
	
	var $link_temp = [];	
	$.each($(".toggle_label"),function(){
		if( $(this).val() != '' ){
			$(this).siblings("label[for="+$(this).attr("id")+"]").hide();
		}
	});
	
	/* 링크입력 라벨 처리 */
	$(document).on("focus", ".toggle_label", function(){
		$(this).siblings("label[for="+$(this).attr("id")+"]").hide();
	}).on("focusout", ".toggle_label", function(){
		if( $(this).val().length === 0 ){
			$(this).siblings("label[for="+$(this).attr("id")+"]").show();
		}else{
			
			/* 링크 주소 창일떄 http 체크 */
			if( $(this).attr("id") == "link_text" ) {
				if( !checkUrlForm($(this).val()) ){
					$(this).val('http://'+$(this).val()) ;
				}				
			}			
		}
	});
	
	$(document).on("keydown","#link_text", function(key){
		 if (key.keyCode == 13) {
			 $(this).siblings("a.link_btn").focus().click();
			 return false;
		 }
		
	});
	
	
	
	/*********************** 닫기 버튼 *************************/
	$(document).on("click","a.date_icon",function(){
		$(this).siblings("input.datetime").focusin();
	});
	
	$(document).on("click","a.date_icon",function(){
		$(this).siblings("input.datetime_gongik").focusin();
	});
	
	/* 링크 모달창 닫기 */
	$(document).on("click", "a.modal_close", function(){
		var obj = $(this).data("obj");
		$("."+obj).slideUp().remove();
	});
	
	/* 아이템 삭제 */
	$(document).on("click","a.item_del",function(){
		console.log( $(this).parents(".write_cont") );
		$(this).parents(".write_cont").remove();
	});	
	

	/*********************** 내용 버튼 **************************/
	/* 본문 내용 쓰기 */
	$(".text_append > a").on("click",function(){
		var objData = [];
		var obj_wrap = $("#cont_zone");
		var html_item = get_cont_item('text', objData);
		
		obj_wrap.append(html_item);
	});		
	
	/* 이미지 입력 버튼 클릭 */
	$(".image_append > a").on("click",function(){
		$(this).siblings("div.cont_image_add").find('input#cont_image').click();
	});		
	
	$("input#cont_image").change(function(){
	   readURL(this);
	});

	function readURL(input) {
	 if (input.files && input.files[0]) {
	  var reader = new FileReader();
		 /*console.log( input.files );*/

		if( input.files[0].type != "image/jpeg" &&  input.files[0].type != "image/png" ){
			modal({
				type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
				text: "Select Image File(jpg/png)",
				callback: function(result) {	

				}
			});					
			return false;
		}
		 
		 var fileSize = input.files[0].size;
		 var maxSize = 1024 * 1024 ;
		 console.log( fileSize );
		 if( fileSize > maxSize ){
			modal({
				type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
				text: "Excess File Size(1MB)",
				callback: function(result) {	

				}
			});					
			return false; 
		 }
		 
		 var _URL = window.URL || window.webkitURL;
		 var img = new Image();
		 var max_width = $("input#cont_image").data("width");
		 var fixImgWidth = (max_width == '' )?980:parseInt(max_width);
			  
		 //alert(max_width);
		 img.src = _URL.createObjectURL(input.files[0]);
		 img.onload = function(){
			 /*console.log( max_width +' / '+ parseInt(img.width)  );*/
			 var strImg = '';
			 if( max_width > parseInt(img.width) ){
				 fixImgWidth = img.width;				 
			 }else{
				 strImg = " Width("+fixImgWidth+"px) Set. ";
			 }	
			 modal({
				type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
				text: "Image size is "+img.width+"*"+img.height+"."+strImg,
				callback: function(result) {	
			 			
				}
			});			
		 }
		 
		
		 
		 
	  reader.onload = function (e) {		
			var objData = [];
			var obj_wrap = $("#cont_zone");

			$("#cont_zone .write_cont module_imagebox .img_align_box img_align_box").each(function(){
				objData.push($(this));
			});
			var html_item = get_cont_item('image', objData);
			 obj_wrap.append(html_item);	
		  	 /*console.log( $(' .img_align_box > img') );*/
		  	setTimeout(function(){
				var imageboxIdx = $(".module_imagebox").length;
				$('#img_align_box'+(imageboxIdx-1)+'  > img').attr('src', e.target.result).attr('width', fixImgWidth+'px').addClass("align_height");
			},1000);
	   		
	  }

	  reader.readAsDataURL(input.files[0]);
	  }
	}

	/* 링크입력 버튼 클릭 */
	$(".link_append > a").on("click",function(){
		var objData = [];
		var obj_wrap = $("#append_btn_zone");		
		var html_item = get_cont_item('link_modal', objData);
		
		obj_wrap.append(html_item);
		$(".link_modal").slideDown();
	});	
	
	/* 링크 체크 */
	$(document).on("click",".link_btn",function(){
		if( $("input#link_text").val() == "" ){
			/*alert('링크 URL을 입력하세요.');*/
			modal({
				type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
				text: "Insert Link URL.",
				callback: function(result) {	
					$('input#link_text').focus();										
				}
			});		

			return false;
		}
		
		var getUrl = $('input#link_text').val();
/*		console.log( getUrl );	*/

		$.ajax({
		  url: "/ybscript.io/module/_ajax_get_og"
		   , type : "POST" /*request type(POST, GET)*/
            , dataType:"json" /*return type(xml, html, json, jsonp, script, text)*/
            , async : false /*동기화유무(true, false)*/
            , cache: false /*캐쉬사용여부(true, false)*/
            , data:{url : getUrl}
            , success: function(data) {
                console.log( data );
				var obj = $("#popup_link_zone");				
				
				if( data.title != '' ){
						var html = '';
						html += 		'<div class="link_test'+(( data.image == undefined )?' noImg':'')+'">';
						html += 			'<div class="cont_box">';
						html += 			'<strong>'+data.title+'</strong>';
						/*if( data.description != undefined ){
						html += 			'<p>'+data.description+'</p>';
						}*/
						html += 			'</div>';
						if( data.image != undefined ){					
						html += 			'<div class="img_box"><img src="'+data.image+'" alt="'+data.title+' Preview Link" /></div>';
						}
						html += 		'</div>';				


						if( obj.parent("div").has(".link_test") ){
							$(".link_test").remove();
							obj.after(html);
						}else{
							obj.after(html);
						}

						$("input#link_check").val("true");
				}else{
					$(".link_test").remove();					
					obj.after('<div class="link_test noData"><div class="cont_box">None Header</div></div>');
				}			
            }
            , error: function(data) {
                console.log(data);
            }
            , complete : function(response){                    
                /*console.log( response );*/
            }
		});
		
		
	});
	
	
	
	/* 링크 추가 */
	$(document).on("click",".btn_add > a",function(){
		if( $("input#link_check").val() != "true" ){
			/*alert('링크 URL유효한 링크인지 확인하세요.');*/
			modal({
				type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
				text: "Link URL Make sure it is a valid link.",
				callback: function(result) {	
					$('a.link_btn').focus();
				}
			});		
			return false;
		}
		
		if( $("input#link_text").val() == "" ){
			/*alert('링크 URL을 입력하세요.');*/
			modal({
				type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
				text: "Insert Link URL",
				callback: function(result) {	
					$('input#link_text').focus();
				}
			});					
			return false;
		}
		var obj_wrap = $("#cont_zone");
		var objData = [];
		var obj = $("."+$(this).data("obj"));
		
		var url = $("input#link_text").val();
		var img = obj.find('.img_box').html();
		var img_src = obj.find('.img_box').find("img").attr("src");
		var title = obj.find('.cont_box > strong').text();
		var disc = obj.find('.cont_box > p').text();
		
		objData.push('link');
		objData.push(url);
		/*objData.push(img);*/
		objData.push(title);
		objData.push(disc);
		objData.push(img_src);	
		
		var html_item = get_cont_item('link_cont',objData);
		obj_wrap.append(html_item );		
		
		
		obj.slideUp().remove();
		
	});
	
	/************************************************************ 이미지 ******************************************************/
	/* 꽉찬 화면 */
	$(document).on("click","ul.img_util > li > a",function(){
		var obj = $(this).parents(".img_align_box");
		$("ul.img_util").find("li").removeClass("on");		
		obj.find("img").removeClass("align_width").removeClass("align_height");
		obj.find("img").addClass( $(this).parent("li").attr("class") );
		$(this).parent("li").addClass("on");		
	});
	
	/************************************************************* 키워드 *****************************************************/	
	/* 인기 키워드 등록 클릭 */
	$(document).on("click",".tag_add", function(){
		var input_word = $("input#write_tag").val();

		if( input_word == "" ){
			/*alert("입력된 키워드가 없습니다.");*/			
			modal({
				type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
				text: "None Keyword",
				callback: function(result) {	
				
				}
			});					
			return false;
		}else{
			input_keyword(input_word);
		}
	});
			
	/* 키워드 삭제 */
	$(document).on("click","div.tag_list a.tag_del",function(){
		var keyword = $(this).data("keyword");
		var keyword_del = $("input#keyword_del").val();
		
		$("input#keyword_del").val(keyword_del+'|'+keyword);		
		
		$("input#keyword").val( $("input#keyword").val().replace(","+keyword,"").replace(keyword+",","") );
		$(this).remove();
	});

	/* 인기 키워드 엔터  */
	$(document).on("keypress",'input#write_tag', function(e){
		if(e.which === 13){
			$(".tag_add").click();			
			return false;
		}
	});
	
	/* 본문내의 이미지 삽입시 alt값 안보이게 하기. */
	$('input[name="imagebox_alt"]').each(function(){	
        

		
		if( $(this).val() != "" ){
			$('label[for="'+$(this).attr("id")+'"]').hide();
		}
		
	});
	
	
	/*********************** 폼서브밋 **************************/	
	$("#v_btn_confirm").bind("click", function (event) {
		var board_id = $("input#board_id").val();
        
        if($('#title').val() == '') {
			modal({
				type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
				text: "제목 입력은 필수사항입니다.",
				callback: function(result) {	
					$('#title').focus();
					loading( $('#wrap') ,"end","fixed");
				}
			});					
			return false;
		}
		
		if($('#reg_name').val() == '') {		
			modal({
				type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
				text: "등록자 입력은 필수사항입니다.",
				callback: function(result) {	
					$('#reg_name').focus();
					loading( $('#wrap') ,"end","fixed");
				}
			});				
			
			return false;
		}
		
    if( board_id == 'najuyouth_request' || board_id == 'najuyouth_freeboard' ) {
        
        if($('#phone_1').val() == '') {
			modal({
				type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
				text: "연락처 첫자리 입력은 필수사항입니다.",
				callback: function(result) {	
					$('#phone_1').focus();
					loading( $('#wrap') ,"end","fixed");
				}
			});					
			return false;
		}
        if($('#phone_2').val() == '') {
			modal({
				type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
				text: "연락처 중간자리 입력은 필수사항입니다.",
				callback: function(result) {	
					$('#phone_2').focus();
					loading( $('#wrap') ,"end","fixed");
				}
			});					
			return false;
		}
        if($('#phone_3').val() == '') {
			modal({
				type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
				text: "연락처 끝자리 입력은 필수사항입니다.",
				callback: function(result) {	
					$('#phone_3').focus();
					loading( $('#wrap') ,"end","fixed");
				}
			});					
			return false;
		}
        
        if($('#varchar_2').val() == '') {
			modal({
				type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
				text: "생년월일 입력은 필수사항입니다.",
				callback: function(result) {	
					$('#phone_1').focus();
					loading( $('#varchar_2') ,"end","fixed");
				}
			});					
			return false;
		}
        
        if($('#agree_privacy').is(":checked") != true) {
			modal({
				type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
				text: "개인정보처리방침 동의는 필수사항입니다.",
				callback: function(result) {	
					$('#agree_privacy').focus();
					loading( $('#wrap') ,"end","fixed");
				}
			});					
			return false;
		}    
    }
     
		
	if( board_id == 'www_newsletter_req' ) {
        
        if($('#phone_1').val() == '') {
			modal({
				type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
				text: "연락처 첫자리 입력은 필수사항입니다.",
				callback: function(result) {	
					$('#phone_1').focus();
					loading( $('#wrap') ,"end","fixed");
				}
			});					
			return false;
		}
        if($('#phone_2').val() == '') {
			modal({
				type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
				text: "연락처 중간자리 입력은 필수사항입니다.",
				callback: function(result) {	
					$('#phone_2').focus();
					loading( $('#wrap') ,"end","fixed");
				}
			});					
			return false;
		}
        if($('#phone_3').val() == '') {
			modal({
				type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
				text: "연락처 끝자리 입력은 필수사항입니다.",
				callback: function(result) {	
					$('#phone_3').focus();
					loading( $('#wrap') ,"end","fixed");
				}
			});					
			return false;
		}
        
        if($('#zipcode').val() == '') {
			modal({
				type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
				text: "우편번호 입력은 필수사항입니다.",
				callback: function(result) {	
					$('#zipcode').focus();
					loading( $('#zipcode') ,"end","fixed");
				}
			});					
			return false;
		}
		
		if($('#address_1').val() == '') {
			modal({
				type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
				text: "기본주소 입력은 필수사항입니다.",
				callback: function(result) {	
					$('#address_1').focus();
					loading( $('#address_1') ,"end","fixed");
				}
			});					
			return false;
		}
        
        if($('#agree_privacy').is(":checked") != true) {
			modal({
				type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
				text: "개인정보처리방침 동의는 필수사항입니다.",
				callback: function(result) {	
					$('#agree_privacy').focus();
					loading( $('#wrap') ,"end","fixed");
				}
			});					
			return false;
		}    
    }	
        
		
	 //여수경비보조 , 여수사업승인현황  _ 내용선택사항
	if(board_id !== "www_assist_service" && board_id !== "www_public_house" && board_id !== "www_approval_status" ){
		
		if( $('textarea[name="contents[]"]').val() != undefined ){
			
			if( $('textarea[name="contents[]"]').val() == undefined || $('textarea[name="contents[]"]').val() == "" ){
				modal({
					type: "alert", 
					text: "내용 입력은 필수사항입니다.",
					callback: function(result) {	
						if( $('textarea[name="contents[]"]').length == 0 ){
							$("ul.append_btn > li.text_append > a").click();	
						}					
						$('textarea[name="contents[]"]').focus();
						loading( $('#wrap') ,"end","fixed");
					}
				});						
				return false;
			}
			
		}
		
		
	} //여수경비보조 _ 내용선택사항
		
		if( $("input[name='imagebox_alt']").val() != undefined ){
			$(".module_imagebox > .item").find("textarea").remove();
			var imgAlt = "";
			
			$("input[name='imagebox_alt']").each(function(){
				if( $(this).val().length == 0 ){
					imgAlt = $(this).attr("id");
					
					return false;
				}else{
					$(this).parents(".item").append('<textarea style="display:none;" name="contents[]">'+$(this).parents(".item").find("img").parent('div').html()+'</textarea>');
				}
			});
			
            

            
            
			if( imgAlt.length > 0 ){
				modal({
					type: "alert", 
					text: "Insert Subtitle Image.",
					callback: function(result) {	
									
						$("input[id='"+imgAlt+"']").focus();
						loading( $('#wrap') ,"end","fixed");
					}
				});		
				
				return false;
			}
		}
		
		
					
		//$('input[name="contents"]').val( $('textarea[name="contents"]').serializeArray() );
		/*console.log( $("form").serializeArray() ); 
		console.log( $('textarea[name="contents[]"]').serializeArray() );
		console.log( $('input[name="contents[]"]').val() );*/
		
		loading( $('#wrap') ,"start","fixed");
/*		return false;*/
		return true;
	});	
	
	
	/*포커스*/
	$(document).on("focus", ".module_write_box .write_contbox .module_w .module_textbox textarea, .module_write_box .write_contbox .module_w .module_imagebox .img_altbox input", function(){
		$(".module_w .write_cont, .module_w .write_cont .item, .module_w .write_cont .right_util .item_del").removeAttr("style");
		$(this).parents(".write_cont").css({"border":"1px solid #000","box-shadow":"0 4px 2px rgba(34,34,34,0.25)"}).find(".item_del").css("border-top-color","#000");
		$(this).parents(".item").css("border-right-color", "#000");
	}).on("focusout", ".module_write_box .write_contbox .module_w .module_textbox textarea, .module_write_box .write_contbox .module_w .module_imagebox .img_altbox input", function(){
		$(".module_w .write_cont, .module_w .write_cont .item, .module_w .write_cont .right_util .item_del").removeAttr("style");
	});
	
	
	/*드래그박스*/
	$(".module_write_box .write_contbox .module_w .module_textbox textarea").on({
		keydown: function(e){
			if (e.keyCode == 65 && e.ctrlKey) {
				e.target.select()
			}
		}
	});
	$(".write_contbox .module_w").sortable({
		cursor: "move",
		axis: "y",
		start: function(event, ui){
			$(".module_write_box .write_contbox .module_w .write_cont").css("transition","none");
		},
		stop: function(event, ui){
			$(".module_write_box .write_contbox .module_w .write_cont").css("transition","ease-in-out 0.3s");
		},
	});
	$(".write_contbox .module_w").disableSelection();
	
	$(document).on("focusout","input[name='imagebox_alt']",function(){
		$(this).parents(".item").find("img").attr("alt", $(this).val() );
	});
	
	
	$(document).on("click","input.file_check",function(){
		var thisCheck = $(this);
		modal({
					type: 'confirm',  /* option : alert/confirm/prompt/success/warning/info/error/primary */
					text: '선택 파일을 삭제하시겠습니까?',
					callback: function(result) {
						if(result){
							console.log( thisCheck.data('idx') );
							thisCheck.parents("li").remove();
							if( $("div.MultiFile-label > ul > li").length == 0 ){
								$("div.MultiFile-label").remove();
								$("div#write_file_list").hide();
								$("#write_file_wrap > input[type='file']").not(":last-child").remove(); 
							}			
							
							console.log( $("input[name='filename[]']")[0].files[0] );
							/*$("input[name='filename[]']")[0].files[0].remove();		*/					
							
						}else{
							thisCheck.prop("checked",false);
						}
					}
		});
	});
	
	$(document).on("click",".file_del",function(){
		if( $(this).hasClass("modify") ){															

			if( !$("input.chk_remove_file").is(":checked") ){
				/*alert("삭제하실 파일을 선택해주세요.");*/
				modal({
					type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
					text: "Please select the file to be deleted.",
					callback: function(result) {	

					}
				});					
				return false;
			}else{			
				modal({
					type: 'confirm',  /* option : alert/confirm/prompt/success/warning/info/error/primary */
					text: 'Do you want to delete?',
					callback: function(result) {
						if(result){
				
								var formData = new FormData();		

								$("input[name='remove_file[]']:checked").each(function(){
									console.log( $(this).data('idx') );
									formData.append("idx",$(this).data('idx') );
									formData.append("fileIdx",$(this).data('fidx'));			
									formData.append("mode", "ajax_file_Delete");			
									formData.append("return", "json");					
									console.log( formData );
									 $.ajax({
										url:selfUrl //request 보낼 서버의 경로
										, type:'POST' // 메소드(get, post, put 등)
										, data:formData //보낼 데이터
										, processData: false
										, contentType: false
										, success: function(data) {
											console.log( data );
											if( data == "true" ){
												modal({
													type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
													text: "Deleted",
													callback: function(result) {	
														window.location.reload(true);
													}
												});					
												return false;
											}

										}
										, error: function(data) {
											console.log(data);
										}
										, complete : function(response){                    
											/*console.log( response );*/
										}
									});    

								});/* ajax end */
				
						}				
					}/* callback end */
				});/* modal end */
				

			}

		}
		return false;
	});
	
	

	
});

/* http  체크  */
function checkUrlForm(strUrl) {
    var expUrl = /^http[s]?\:\/\//i;
    return expUrl.test(strUrl);
}
	
/* 키워드 등록 초기화 */
function reset_keyword(){
	var listObj = $("div.tag_list");

	$("input#keyword").val('');
	listObj.html('');	

}

/* 키워드 등록 */
function input_keyword(word){
	var listObj = $("div.tag_list");
	var keyword = $("input#keyword").val();
	var cnt_keyored = keyword.length;

	if( cnt_keyored > 0 ){
		/* 중복체크 */
		var keywordIdx = keyword.search(','+word);
		if( keywordIdx >= 0 ){
			/*alert("이미 keyword가 추가되었습니다.");*/			
			modal({
				type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
				text: "이미 keyword가 추가되었습니다.",
				callback: function(result) {	
					$("input#write_tag").val('');
				}
			});					
			return false;
		}else{
			$("input#keyword_del").val( $("input#keyword_del").val().replace("|"+word,"") );
			$("input#keyword").val( keyword+','+word);
			listObj.append('<a href="#none" class="tag_del" data-keyword="'+word+'" >'+word+'<span class="icon "></span></a>');			
			$("input#write_tag").val('');
		}
	}else{
		$("input#keyword_del").val( $("input#keyword_del").val().replace("|"+word,"") );
		$("input#keyword").val(','+word);
		listObj.append('<a href="#none" class="tag_del" data-keyword="'+word+'" >'+word+'<span class="icon "></span></a>');							
		$("input#write_tag").val('');
	}
}
	
/* contents item  생성 */	
function get_cont_item(type, data){
	var html = '';	

	switch(type){
		case "text" :
			var item_idx = $(".module_textbox").length;

			html += '<div class="write_cont module_textbox">';
			html += 	'<div class="item">';
			html += 		'<div class="text_box"><label for="textbox_'+item_idx+'">Enter contents</label>';
			html += 			'<textarea id="textbox_'+item_idx+'" name="contents[]" class="toggle_label"></textarea>';
			html += 		'</div>';
			html += 	'</div>';
			html += 	'<div class="right_util">';
			html += 		'<div class="move_icon"><span class="icon"></span>Move</div>';
			html += 		'<a href="#none" class="item_del"><span class="icon">Delete</span></a>';
			html += 	'</div>';
			html += '</div>';
			break;
		case "image" :			
			
			var item_idx = $(".module_imagebox").length;			

			html += '<div class="write_cont module_imagebox">';
			html += 	'<div class="item">';
			html += 		'<div class="img_align_box" id="img_align_box'+item_idx+'">';
			html += 			'<img src="#" />';
/*			html += 			'<ul class="img_util">';
			html += 				'<li class="align_width"><a href="#none"><span class="icon">가로기준 꽉채움</span></a></li>';
			html += 				'<li class="align_height on"><a href="#none"><span class="icon">세로기준 꽉채움</span></a></li>';
			html += 			'</ul>';*/
			html += 		'</div>';
			html += 		'<div class="img_altbox">';
			html += 			'<span class="icon"></span>';
			html += 			'<label for="imagebox_img_'+item_idx+'">Insert Subtitle Image</label>';
			html += 			'<input type="text" id="imagebox_img_'+item_idx+'" name="imagebox_alt" class="toggle_label" />';
			html += 		'</div>';
			html += 	'</div>';
			html += 	'<div class="right_util">';
			html += 		'<div class="move_icon"><span class="icon"></span>Move</div>';
			html += 		'<a href="#none" class="item_del"><span class="icon">Delete</span></a>';
			html += 	'</div>';
			html += '</div>';
			break;		
		case "link_modal" :	
			html += '<div class="link_modal">';
			html += 	'<div class="modal_inner">';
			html += 		'<h3>Insert URL</h3>';
			html += 		'<div class="base_box" id="popup_link_zone">';
			html += 			'<label for="link_text">Insert URL ex)http://www.yeosu.go.kr/</label>';
			html += 			'<input type="text" id="link_text" class="toggle_label" />';
			html += 			'<input type="hidden" id="link_check" value="false"/>';
			html += 			'<a href="#none" class="link_btn">Link Test</a>';
			html += 		'</div>';			
			html += 		'<div class="modal_btnbox">';
			html += 		'<ul>';
			html += 			'<li class="btn_add"><a href="#none" data-obj="link_modal">Add URL</a></li>';
			html += 			'<li class="btn_cancel"><a href="#none" class="modal_close" data-obj="link_modal">Cancel</a></li>';
			html += 		'</ul>';
			html += 		'</div>';
			html += 	'</div>';
			html += '</div>';
			break;					
		case "link_cont":
			var item_idx = $(".write_cont").length;									
			var objDataStr = '';
			$.each(data,function(e){
				objDataStr += data[e]+'|';
			});

			html += '<div class="write_cont module_linkbox'+(( data[2] == undefined )?' noImg':'')+'">';
			html += 	'<input type="hidden" name="contents[]" value="'+objDataStr+'" />';
			
			html += 	'<div class="item">';
			html += 		'<div class="cont_box">';
			html += 			'<strong><span class="icon"></span><a href="'+data[1]+'" class="_link_url" target="_blank">'+data[1]+'</a></strong>';
			html += 			'<span class="title">'+data[2]+'</span>';
			html += 			'<span class="cont">'+data[4]+'</span>';
			html += 		'</div>';
			if( data[4] != undefined ){
			html += 		'<div class="img_box"><img src="'+data[4]+'" alt="'+data[2]+' Preview URL" /></div>';	
			}			
			html += 	'</div>';
			html += 	'<div class="right_util">';
			html += 		'<div class="move_icon"><span class="icon"></span>Move</div>';
			html += 		'<a href="#none" class="item_del"><span class="icon">Delete</span></a>';
			html += 	'</div>';
			html += '</div>';
			break;
		default:
			break;
	}
	
	return html;
}	