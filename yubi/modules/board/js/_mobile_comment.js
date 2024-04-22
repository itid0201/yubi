	$(function() { 
		$("#comment_input").focus(function(){
			$(this).siblings("label[for="+$(this).attr("id")+"]").hide();
		}).focusout(function(){
			console.log($(this).val());
			if( $(this).val() == '' ){
				$(this).siblings("label[for="+$(this).attr("id")+"]").show();
			}else{
				$(this).siblings("label[for="+$(this).attr("id")+"]").hide();
			}
		});
		
		$('.icon_cm_modify').click(function() {
                
			
			var $this = $(this).parent().parent().children('.usr_comm');
			if($this.attr('mode')=='edit') return false;
			console.log( $this.html() );
			var $html = $this.html().replace(/<br>/g,'\n');
			
			console.log( $html );
			var $idx =  $(this).attr('id').replace('comment_','');	
			$this.html('<textarea class="w90" cols="15" rows="5">'+$html+'</textarea>');
			
			$this.attr('mode','edit');
			$this.find('textarea').focus();
			
			$this.find('textarea').blur(function() {
				$(this).parent().attr('mode','');
				param = {contents:$(this).val(), idx:$idx };
				$.post("ybscript.io/module/comment/modify_comment",param).done(function(data){
					//alert(data);
				});
			  $(this).parent().html($(this).val().replace(/\n/g,'<br>'));
				
			});
			
		});
		
		/* 댓글 등록 */
		$('#comment_submit').click(function() {
			if( $("textarea#comment_input").val() == "" ){
				modal({
					type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
					text: "댓글을 입력새주세요.",
					callback: function(result) {	
						$('textarea#comment_input').focus();
					}
				});					
				return false;
			}else{
				$("form#comment_form").submit();
			}
		});
		
		/* 댓글(실명인증) 등록 */
		$('#comment_submit_chk').click(function() {			
			modal({
				type: "confirm", /* option : alert/confirm/prompt/success/warning/info/error/primary */
				text: "로그인 후 댓글등록이 가능합니다. 로그인 페이지로 이동하시겠습니까?",
				callback: function(result) {	
					location.href = "<?php echo $_SYSTEM['rep_login']; ?>";
				}
			});					
			return false;			
		});	
	
	});

	$(document).on("click",".icon_cm_delete",function() {
		var link_url = $(this).data("link");
		modal({
			type: "confirm", /* option : alert/confirm/prompt/success/warning/info/error/primary */
			text: "삭제하시겠습니까?",
			callback: function(result) {	
				if( result ){
					location.replace(link_url);	
				}
				
			}
		});		
		return false;	
	});
	
	function login_page_go() {
		modal({
			type: "confirm", /* option : alert/confirm/prompt/success/warning/info/error/primary */
			text: "로그인 후 댓글등록이 가능합니다. 로그인 페이지로 이동하시겠습니까?",
			callback: function(result) {	
				if( result ){
					location.href = "<?php echo $_SYSTEM['rep_login']; ?>";	
				}				
			}
		});		
		return false;			
	}