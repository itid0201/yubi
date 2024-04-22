$(function(){

	
    $("div.board_btn_box > ul > li.board_btn_reply > a#v_btn_reply, .reply_btn_box > a.rebtn_modify").on("click", function(){		

		var ori_title = $(this).data
		var html ='';
		
		
		if( $(this).hasClass("rebtn_modify") ){
			var idx = $(this).data("idx");		
			var contents ='';
			$.get(selfUrl+'?mode=view&return=json&idx='+idx,function(data){
				console.log(data.contents );
				setTimeout(function(){
					$("#reply_input").html(data.contents);					
				},300);				
			},'json');
			
			html += '<div class="reply_modal">';
			html += 	'<div class="modal_inner">';
			html += 		'<h3>답변수정</h3>';
			html += 		'<div id="popup_reply" class="base_box">';
			html += 			'<label for="reply_input" class="text_hidden">답변내용 입력</label>';
			html += 			'<textarea id="reply_input" name="reply_input"></textarea>';
			html += 			'<input type="hidden" name="idx" value="'+idx+'" />';		
			html += 		'</div>';
			html += 		'<div class="modal_btnbox">';
			html += 			'<ul>';
			html += 				'<li class="btn_add"><a href="#none" id="add_reply"  class="reply_modify" >답변수정</a></li>';
			html += 				'<li class="btn_cancel"><a href="#none" class="modal_close">취소</a></li>';
			html += 			'</ul>';
			html += 		'</div>';
			html += 	'</div>';
			html += '</div>';			
			

		}else{
			var pidx = $(this).data("pidx");		
			$.get(selfUrl+'?mode=view&return=json&idx='+pidx,function(data){				
				setTimeout(function(){
					$("input[name=title]").val('['+data.title+']글에 대한 답변');
				},300);				
			},'json');
			
			html += '<div class="reply_modal">';
			html += 	'<div class="modal_inner">';
			html += 		'<h3>답변하기</h3>';
			html += 		'<div id="popup_reply" class="base_box">';
			html += 			'<label for="reply_input" class="text_hidden">답변내용 입력</label>';
			html += 			'<textarea id="reply_input" name="reply_input"></textarea>';
			html += 			'<input type="hidden" name="pidx" value="'+pidx+'" />';		
			html += 			'<input type="hidden" name="title" value="글에 대한 답변" />';		
			html += 		'</div>';
			html += 		'<div class="modal_btnbox">';
			html += 			'<ul>';
			html += 				'<li class="btn_add"><a href="#none" id="add_reply" class="reply_save" >답변입력</a></li>';
			html += 				'<li class="btn_cancel"><a href="#none" class="modal_close">취소</a></li>';
			html += 			'</ul>';
			html += 		'</div>';
			html += 	'</div>';
			html += '</div>';	
		}
		
		
		$(this).after(html);
		$(".reply_modal").stop().fadeIn();
    });
	  
	/* 답변입력 */
	$(document).on("click","a#add_reply",function(){
		if( $(".reply_modal").find("textarea[name=reply_input]").val() == '' ){
			modal({
				type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
				text: "답변을 입력하세요.",
				callback: function(result) {	
					 $(".reply_modal").find("textarea[name=reply_input]").focus();										
				}
			});		

			return false;
		}
		
		
		var formData = new FormData();	
		var message = '';
		if( $(this).hasClass("reply_modify") ){
			formData.append("idx",  $("input[name=idx]").val());
			formData.append("mode", "reply_change");
			formData.append("contents", $("textarea#reply_input").val());
			formData.append("return", "json");		
			
			message = "답변이 수정되었습니다.";
		}else{
			formData.append("pidx",  $("input[name=pidx]").val());
			formData.append("mode", "reply_save");

			formData.append("title", $("input[name=title]").val());
			formData.append("contents", $("textarea#reply_input").val());		
			formData.append("return", "json");			
			message = "답변이 등록되었습니다.";
		}
		
		 $.ajax({
			url:selfUrl //request 보낼 서버의 경로
			, type:'POST' // 메소드(get, post, put 등)
			, data:formData //보낼 데이터
			, processData: false
			, contentType: false
			, success: function(data) {
				console.log( data );
				if( data == "ok" ){
					modal({
						type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
						text: message,
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
		
	});
	
	/* */
    $(document).on("click",".reply_modal .modal_inner .modal_btnbox ul li.btn_cancel a", function(){
    	$(this).parents(".reply_modal").stop().fadeOut(function(){
    		$(this).remove();
    	});
    });
    
	
	
	
});
