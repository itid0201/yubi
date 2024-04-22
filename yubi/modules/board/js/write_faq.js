$(function() { 
	$(document).find(".move_icon").remove(); // 이동하기 삭제 
	
	$('.datetime').datetimepicker({
		lang:'ko',
		prevText: '〈전달',
		nextText: '다음달〉',
		ampm: false,
		timepicker:false,
		format:'Ymd',
		formatDate:'Y-m-d'		
	});	
	
	
	var $link_temp = [];
	
	$.each($(".text_box textarea"),function(){
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
			/*alert('링크 URL을 입력하세요.');*/
			alert("링크 URL을 입력하세요.")
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
		console.log( getUrl );	

		$.ajax({
		  url: "/ybscript.io/module/_ajax_get_og"
		   , type : "POST" /*request type(POST, GET)*/
            , dataType:"json" /*return type(xml, html, json, jsonp, script, text)*/
            , async : false /*동기화유무(true, false)*/
            , cache: false /*캐쉬사용여부(true, false)*/
            , data:{url : getUrl}
            , success: function(data) {
       //  console.log( data );
				var obj = $("#popup_link_zone");	
				var title_str	= ( data.title == null || data.title == undefined ) ? '': data.title;
				var desc_str	= ( data.description == null || data.description == undefined ) ? '': data.description; 
				if( data.title != '' ){
						var html = '';
						html += 		'<div class="link_test'+(( data.image == undefined )?' noImg':'')+'">';
						html += 			'<div class="cont_box">';
						html += 			'<strong>'+title_str+'</strong>';
						html += 			'<p>'+desc_str+'</p>';
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
				text: "입력된 키워드가 없습니다.",
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
		if($('#title').val() == '') {
			alert("질문 입력은 필수사항입니다.")
			$('#title').focus();
			// modal({
			// 	type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
			// 	text: "질문 입력은 필수사항입니다.",
			// 	callback: function(result) {
			// 		$('#title').focus();
			// 	}
			// });
			return false;
		}
		
		if($('#reg_name').val() == '') {
			alert("등록자 입력은 필수사항입니다.")
			$('#reg_name').focus();
			// modal({
			// 	type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
			// 	text: "등록자 입력은 필수사항입니다.",
			// 	callback: function(result) {
			// 		$('#reg_name').focus();
			// 	}
			// });
			return false;
		}
		
		
		if( $('textarea[name="contents[]"]').val() != undefined ){
			
			if( $('textarea[name="contents[]"]').val() == undefined || $('textarea[name="contents[]"]').val() == "" ){
				alert("답변 입력은 필수사항입니다.")
				$('textarea[name="contents[]"]').focus();
				// modal({
				// 	type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
				// 	text: "답변 입력은 필수사항입니다.",
				// 	callback: function(result) {
				// 		if( $('textarea[name="contents[]"]').length == 0 ){
				// 			$("ul.append_btn > li.text_append > a").click();
				// 		}
				// 		$('textarea[name="contents[]"]').focus();
				// 	}
				// });
				return false;
			}
			
		}
					
		
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
				alert("이미지 alt 입력은 필수사항입니다.")
				$("input[id='"+imgAlt+"']").focus();
				// modal({
				// 	type: "alert",
				// 	text: "이미지 alt 입력은 필수사항입니다.",
				// 	callback: function(result) {
				//
				// 		$("input[id='"+imgAlt+"']").focus();
				// 	}
				// });
				return false;
			}
		}
		
		//$('input[name="contents"]').val( $('textarea[name="contents"]').serializeArray() );
		
		
		console.log( $("form").serializeArray() ); 
		console.log( $('textarea[name="contents[]"]').serializeArray() );
		console.log( $('input[name="contents[]"]').val() );
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
			alert("이미 keyword가 추가되었습니다.");
			$("input#write_tag").val('');
			// modal({
			// 	type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
			// 	text: "이미 keyword가 추가되었습니다.",
			// 	callback: function(result) {
			// 		$("input#write_tag").val('');
			// 	}
			// });
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
			html += 			'<label for="link_text">링크 url 입력 ex)http://www.yeosu.go.kr/</label>';
			html += 			'<input type="text" id="link_text" class="toggle_label" />';
			html += 			'<input type="hidden" id="link_check" value="false"/>';
			html += 			'<a href="#none" class="link_btn">링크테스트</a>';
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
			var title_str	= ( data[2] == null || data[2] == undefined ) ? '': data[2];
			var desc_str	= ( data[3] == null || data[3] == undefined ) ? '': data[3]; 

			html += '<div class="write_cont module_linkbox'+(( data[2] == undefined )?' noImg':'')+'">';
			html += 	'<input type="hidden" name="contents[]" value="'+objDataStr+'" />';
			
			html += 	'<div class="item">';
			html += 		'<div class="cont_box">';
			html += 			'<strong><span class="icon"></span><a href="'+data[1]+'" class="_link_url" target="_blank">'+data[1]+'</a></strong>';
			html += 			'<span class="title">'+title_str+'</span>';
			html += 			'<span class="cont">'+desc_str+'</span>';
			html += 		'</div>';
			if( data[4] != undefined ){
			html += 		'<div class="img_box"><img src="'+data[4]+'" alt="'+title_str+' 링크 미리보기" /></div>';	
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