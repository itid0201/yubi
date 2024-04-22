$(function() { 
	var $arr = ['','www_budget','www_suggestion','www_question','www_compliment','www_freeboard','www_proposal','www_distress','www_subcontract','www_autonomy','www_human_rights'];
	console.log( $(document).find("input[name=board_id]").val() );
	console.log( $.inArray( $(document).find("input[name=board_id]").val(),$arr ) );
	if( $.inArray( $(document).find("input[name=board_id]").val(),$arr ) ){
		$(document).find(".move_icon").remove(); // 이동하기 삭제 
	}
	$(".text_append a").append('<span class="hid">텍스트 입력 버튼을 선택하면 내용입력창이 추가됩니다.</span>');
	
	
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
			$(this).siblings("label[for="+$(this).attr("id")+"]").attr("style","position:absolute;text-indent:-99999px;width:0px;height:0px;z-index:-1;");
		}
	});
	
	/* 링크입력 라벨 처리 */
	$(document).on("focus", ".toggle_label", function(){
		$(this).siblings("label[for="+$(this).attr("id")+"]").attr("style","position:absolute;text-indent:-99999px;width:0px;height:0px;z-index:-1;");
	}).on("focusout", ".toggle_label", function(){
		if( $(this).val().length === 0 ){
			$(this).siblings("label[for="+$(this).attr("id")+"]").attr("style","");
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
	
	/*********************** 숫자만 입력 *************************/
	$(document).on("keyup","input.isnumber", function(){
		$(this).val($(this).val().replace(/[^0-9]/g,""));
	});
	/*********************** 숫자만 입력 *************************/
	
	$(document).on("keyup","input.input_birth", function(){
		var $input = $(this);
		$(this).val($(this).val().replace(/[^0-9\-]/g,""));
		
		if($(this).val().length >= 10){
			var tmp = $(this).val().replace(/(19[0-9][0-9]|20\d{2})-(0[0-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])/g,"");
			console.log(tmp);
			if (tmp != ""){
				alert('생년월일 입력이 올바르지 않습니다.')
				$input.focus();
				$input.val("");
				// modal({
				// 	type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
				// 	text: '생년월일 입력이 올바르지 않습니다.',
				// 	callback: function(result) {
				// 		$input.focus();
				// 		$input.val("");
				// 	}
				// });
			}
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
			alert("이미지 파일(jpg/png)을 선택해주세요.")
			// modal({
			// 	type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
			// 	text: "이미지 파일(jpg/png)을 선택해주세요.",
			// 	callback: function(result) {
			//
			// 	}
			// });
			return false;
		}
		 
		 var fileSize = input.files[0].size;
		 var maxSize = 1024 * 1024 ;
		 console.log( fileSize );
		 if( fileSize > maxSize ){
			 alert("파일 용량(1MB)를 초과했습니다.")
			// modal({
			// 	type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
			// 	text: "파일 용량(1MB)를 초과했습니다.",
			// 	callback: function(result) {
			//
			// 	}
			// });
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
				 strImg = " 기본가로("+fixImgWidth+"px) 사이즈로 설정합니다. ";
			 }
			 alert("이미지의 사이즈는 "+img.width+"*"+img.height+" 입니다."+strImg)
			//  modal({
			// 	type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
			// 	text: "이미지의 사이즈는 "+img.width+"*"+img.height+" 입니다."+strImg,
			// 	callback: function(result) {
			//
			// 	}
			// });
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
			alert('링크 URL을 입력하세요.');
			$('input#link_text').focus();
			// modal({
			// 	type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
			// 	text: "링크 URL을 입력하세요.",
			// 	callback: function(result) {
			// 		$('input#link_text').focus();
			// 	}
			// });

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
						html += 			'<div class="img_box"><img src="'+data.image+'" alt="'+data.title+' 링크 미리보기" /></div>';
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
					obj.after('<div class="link_test noData"><div class="cont_box">헤더값이 없습니다.</div></div>');
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
			alert('링크 URL유효한 링크인지 확인하세요.');
			$('a.link_btn').focus();
			// modal({
			// 	type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
			// 	text: "링크 URL유효한 링크인지 확인하세요.",
			// 	callback: function(result) {
			// 		$('a.link_btn').focus();
			// 	}
			// });
			return false;
		}
		
		if( $("input#link_text").val() == "" ){
			alert('링크 URL을 입력하세요.')
			$('input#link_text').focus();
			// modal({
			// 	type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
			// 	text: "링크 URL을 입력하세요.",
			// 	callback: function(result) {
			// 		$('input#link_text').focus();
			// 	}
			// });
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
			alert("입력된 키워드가 없습니다.");
			// modal({
			// 	type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
			// 	text: "입력된 키워드가 없습니다.",
			// 	callback: function(result) {
			//
			// 	}
			// });
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
		var submit_check = true;
        
				
        if($('#title').val() == '') {
			alert('제목 입력은 필수사항입니다.');
			$('#title').focus();
			// modal({
			// 	type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
			// 	text: "제목 입력은 필수사항입니다.",
			// 	callback: function(result) {
			// 		$('#title').focus();
			// 		loading( $('#wrap') ,"end","fixed");
			// 	}
			// });
			submit_check = false;
			return false;
		}
		
		if($('#reg_name').val() == '') {
			alert('등록자 입력은 필수사항입니다');
			$('#reg_name').focus();
			// modal({
			// 	type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
			// 	text: "등록자 입력은 필수사항입니다.",
			// 	callback: function(result) {
			// 		$('#reg_name').focus();
			// 		loading( $('#wrap') ,"end","fixed");
			// 	}
			// });
			submit_check = false;
			return false;
		}
		
		if( $("#file_upload_chk").val() == 'true' && ( ( $("#mode").val() == 'save' && $("input[name='file[]']").length == 0 ) || ( $("#mode").val() == 'change' && $("input[name='file_m[]']").length == 0 && $("input[name='file[]']").length == 0  ) ) ){
			alert('파일 업로드는 필수사항입니다.');
			$('a.write_file_icon').focus();
			// modal({
			// 	type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
			// 	text: "파일 업로드는 필수사항입니다..",
			// 	callback: function(result) {
			// 		$('a.write_file_icon').focus();
			// 		loading( $('#wrap') ,"end","fixed");
			// 	}
			// });
			submit_check = false;
			return false;
		}
		
		
		//학습동아리
		if(board_id !== "edu_club" && board_id !== "www_yeosoon_info") {
			
			
				if($('#zipcode').val() == '') {
					alert("우편번호 입력은 필수사항입니다.")
					$('#zipcode').focus();
					// modal({
					// 	type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
					// 	text: "우편번호 입력은 필수사항입니다.",
					// 	callback: function(result) {
					// 		$('#zipcode').focus();
					//
					// 	}
					// });
					// submit_check = false;
					return false;
				}
			
				if($('#address_1').val() == '') {
					alert("주소 입력은 필수사항입니다.")
					$('#address_1').focus();
					// modal({
					// 	type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
					// 	text: "주소 입력은 필수사항입니다.",
					// 	callback: function(result) {
					// 		$('#address_1').focus();
					//
					// 	}
					// });
					// submit_check = false;
					return false;
				}
			
			

				/*if($('#address_2').val() == '') {		
					modal({
						type: "alert", 
						text: "상세주소 입력은 필수사항입니다.",
						callback: function(result) {	
							$('#address_2').focus();
						}
					});				
					submit_check = false;
					return false;
				}*/

		}
		
		
		
		/* input 에 _chk_input 클레스가 있으면 빈값 체크 */
		$(document).find("._chk_input").each(function(){
			var $input = $(this);		
			
			
			if( $input.attr("type") == "text" ||  $input.prop("tagName") == "SELECT"  ){
			/* 일반 INPUT TEXT TYPE */
				if($input.val() == '') {
					alert($("label[for='"+$input.attr("id")+"']").text()+' 입력은 필수사항입니다.')
					$input.focus();
					// modal({
					// 	type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
					// 	text: $("label[for='"+$input.attr("id")+"']").text()+' 입력은 필수사항입니다.',
					// 	callback: function(result) {
					// 		$input.focus();
					// 		loading( $('#wrap') ,"end","fixed");
					// 	}
					// });
					submit_check = false;
					return false;
				}	
			}else if( $input.attr("type") == "radio" ){
			/* INPUT radio TYPE */				
				if($(':radio[name="'+$input.attr("name")+'"]:checked').length < 1){
					alert($("label[for='"+$input.attr("name")+"']").text()+' 선택은 필수사항입니다.')
					$input.focus();
					// modal({
					// 	type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
					// 	text: $("label[for='"+$input.attr("name")+"']").text()+' 선택은 필수사항입니다.',
					// 	callback: function(result) {
					// 		$input.focus();
					// 		loading( $('#wrap') ,"end","fixed");
					// 	}
					// });
					submit_check = false;
					return false;					
				}
			}else if( $input.attr("type") == "checkbox" ){
			/* INPUT radio TYPE */				
				if($(':checkbox[name="'+$input.attr("name")+'"]:checked').length < 1){
					alert($("label[for='"+$input.attr("name")+"']").text()+' 선택은 필수사항입니다.')
					$input.focus();
					// modal({
					// 	type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
					// 	//text: $("label[for='"+$input.attr("name")+"']").text()+' 선택은 필수사항입니다.',
					// 	text: $("label[for='"+$input.attr("name")+"']").text()+' 선택은 필수사항입니다.',
					// 	callback: function(result) {
					// 		$input.focus();
					// 		loading( $('#wrap') ,"end","fixed");
					// 	}
					// });
					submit_check = false;
					return false;					
				}
			}
					
		});	
     
		
	
	
		
		 //여수경비보조 , 여수사업승인현황, 여수평생교육  _ 내용선택사항
		if( submit_check &&  board_id !== "www_assist_service" && board_id !== "www_public_house" && board_id !== "www_approval_status" && board_id !== "www_lecturer" ){

			if( $('textarea[name="contents[]"]').val() != undefined ){

				if( $('textarea[name="contents[]"]').val() == undefined || $('textarea[name="contents[]"]').val() == "" ){
					alert('내용 입력은 필수사항입니다.');
					$('textarea[name="contents[]"]').focus();
					// modal({
					// 	type: "alert",
					// 	text: "내용 입력은 필수사항입니다.",
					// 	callback: function(result) {
					// 		if( $('textarea[name="contents[]"]').length == 0 ){
					// 			$("ul.append_btn > li.text_append > a").click();
					// 		}
					// 		$('textarea[name="contents[]"]').focus();
					// 		loading( $('#wrap') ,"end","fixed");
					// 	}
					// });
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
					submit_check = false;
					return false;
				}else{
					$(this).parents(".item").append('<textarea style="display:none;" name="contents[]">'+$(this).parents(".item").find("img").parent('div').html()+'</textarea>');
				}
			});
			
            

            
            
			if( imgAlt.length > 0 ){
				alert("이미지 alt 입력은 필수사항입니다.")
				$("input[id='"+imgAlt+"']").focus();
				// modal({
				// 	type: "alert",
				// 	text: "이미지 alt 입력은 필수사항입니다.",
				// 	callback: function(result) {
				//
				// 		$("input[id='"+imgAlt+"']").focus();
				// 		loading( $('#wrap') ,"end","fixed");
				// 	}
				// });
				// submit_check = false;
				return false;
			}
		}
		
		
					
		//$('input[name="contents"]').val( $('textarea[name="contents"]').serializeArray() );
		/*console.log( $("form").serializeArray() ); 
		console.log( $('textarea[name="contents[]"]').serializeArray() );
		console.log( $('input[name="contents[]"]').val() );*/
		
		loading( $('#wrap') ,"start","fixed");
/*		return false;*/
		if( submit_check ){
			return true;	
		}else{
			return false;	
		}
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
	
	$(document).on("click","a.write_file_icon",function(){
//			console.log($("#write_file_wrap input[type=file]")[0].files);
	});
	
	$(document).on("click","input.file_check",function(){
		var thisCheck = $(this);
		modal({
					type: 'confirm',  /* option : alert/confirm/prompt/success/warning/info/error/primary */
					text: '선택 파일을 삭제하시겠습니까?',
					callback: function(result) {
						if(result){
							//# 선택된 파일 목록 & 추가되는 멀티파일 & 실제 업로드된 파일 삭제. 220404.a.
							var df = new DataTransfer();
							var fileArr = Array.from($("div.file_add #write_file_wrap input[type='file']")[0].files);
							fileArr.splice(thisCheck.parents("li").index(),1);
							//fileArr.forEach(file => { df.items.add(file); }); 220406권재영 수정
							fileArr.forEach(function(file){  df.items.add(file); });
							$("div.file_add #write_file_wrap input[name='filename[]']:last").files = df.files;
							$("div.file_add #write_file_wrap .MultiFile")[0].files = df.files;
					
							thisCheck.parents("li").remove();
							$("div.file_add #write_file_wrap input[type='file']").not(":last-child").remove();
		///		console.log($("div.file_add #write_file_wrap .MultiFile"));
							if( $("div.MultiFile-label > ul > li").length == 0 ){
								$("div.MultiFile-label").remove();
								$("div#write_file_list").hide();
							}
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
				alert("삭제하실 파일을 선택해주세요.");
				// modal({
				// 	type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
				// 	text: "삭제하실 파일을 선택해주세요.",
				// 	callback: function(result) {
				//
				// 	}
				// });
				return false;
			}else{			
				modal({
					type: 'confirm',  /* option : alert/confirm/prompt/success/warning/info/error/primary */
					text: '삭제하시겠습니까?',
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
													text: "삭제되었습니다.",
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
			html += 		'<div class="text_box"><label for="textbox_'+item_idx+'">내용입력</label>';
			html += 			'<textarea id="textbox_'+item_idx+'" name="contents[]" class="toggle_label"></textarea>';
			html += 		'</div>';
			html += 	'</div>';
			html += 	'<div class="right_util">';
			html += 		'<div class="move_icon"><span class="icon"></span>이동</div>';
			html += 		'<a href="#none" class="item_del"><span class="icon">선택된 입력박스 삭제</span></a>';
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
			html += 			'<label for="imagebox_img_'+item_idx+'">이미지 설명을 입력해주세요.</label>';
			html += 			'<input type="text" id="imagebox_img_'+item_idx+'" name="imagebox_alt" class="toggle_label" />';
			html += 		'</div>';
			html += 	'</div>';
			html += 	'<div class="right_util">';
			html += 		'<div class="move_icon"><span class="icon"></span>이동</div>';
			html += 		'<a href="#none" class="item_del"><span class="icon">선택된 입력박스 삭제</span></a>';
			html += 	'</div>';
			html += '</div>';
			break;		
		case "link_modal" :	
			html += '<div class="link_modal">';
			html += 	'<div class="modal_inner">';
			html += 		'<h3>링크입력</h3>';
			html += 		'<div class="base_box" id="popup_link_zone">';
			html += 			'<label for="link_text">링크 url 입력 ex)http://www.jmi.re.kr/</label>';
			html += 			'<input type="text" id="link_text" class="toggle_label" />';
			html += 			'<input type="hidden" id="link_check" value="false"/>';
			html += 			'<a href="#none" class="link_btn">입력한 링크가 유효한지 확인</a>';
			html += 		'</div>';			
			html += 		'<div class="modal_btnbox">';
			html += 		'<ul>';
			html += 			'<li class="btn_add"><a href="#none" data-obj="link_modal">링크 추가</a></li>';
			html += 			'<li class="btn_cancel"><a href="#none" class="modal_close" data-obj="link_modal">취소</a></li>';
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
			console.log(objDataStr);

			html += '<div class="write_cont module_linkbox'+(( data[2] == undefined )?' noImg':'')+'">';
			html += 	'<input type="hidden" name="contents[]" value="'+objDataStr+'" />';
			
			html += 	'<div class="item">';
			html += 		'<div class="cont_box">';
			html += 			'<strong><span class="icon"></span><a href="'+data[1]+'" class="_link_url" target="_blank">'+data[1]+'</a></strong>';
			html += 			'<span class="title">'+data[2]+'</span>';
			if( data[3] != undefined ){
			html += 			'<span class="cont">'+data[3]+'</span>';
			} 
			html += 		'</div>';
			if( data[4] != undefined ){
			html += 		'<div class="img_box"><img src="'+data[4]+'" alt="'+data[2]+' 링크 미리보기" /></div>';	
			}			
			html += 	'</div>';
			html += 	'<div class="right_util">';
			html += 		'<div class="move_icon"><span class="icon"></span>이동</div>';
			html += 		'<a href="#none" class="item_del"><span class="icon">선택된 입력박스 삭제</span></a>';
			html += 	'</div>';
			html += '</div>';
			break;
		default:
			break;
	}
	
	return html;
}	