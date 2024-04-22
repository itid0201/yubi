$(function(){
		/*체크박스 컨트롤*/
		$(".module_list_box .board_list_box .board_list .board_manager_btn > a.all_check").on("click", function(){
			if($(this).hasClass("check") == false){
				$(this).addClass("check");
				$("table.board_basic td .list_checkbox input").prop("checked",true);
			}else{
				$(this).removeClass("check");
				$("table.board_basic td .list_checkbox input").prop("checked",false);
			}
		});
		$("table.board_basic td .list_checkbox label").on("click", function(){
			setTimeout(function(){
				var list_checkbox = $("table.board_basic td .list_checkbox");
				if(list_checkbox.find("input[type=\"checkbox\"]").length == list_checkbox.find("input[type=\"checkbox\"]:checked").length){
					$(".module_list_box .board_list_box .board_list .board_manager_btn > a.all_check").addClass("check");
				}else{
					$(".module_list_box .board_list_box .board_list .board_manager_btn > a.all_check").removeClass("check");
				}
			},10);
		});
		/* 선택글 삭제 */
		$(".module_list_box .board_list_box .board_list .board_manager_btn > a.check_delete").on("click", function(){

			if(!isLogin) {
				modal({
						type: 'alert',  /* option : alert/confirm/prompt/success/warning/info/error/primary */
						text: '로그인 후 이용 가능합니다.',
						callback: function(result) {
							self.location.href="/www/operation_guide/member_login?return_url="+selfUrl;
						}
					});
				return false;
			}		

			if($("table.board_basic  input:checkbox").is(":checked") != true){
				modal({
					type: 'alert',  /* option : alert/confirm/prompt/success/warning/info/error/primary */
					text: '삭제할 글을 선택해주세요.',
					callback: function(result) {					
					}
				});
				return false;		

			}else{
					var check_idx = new Array();  
					$("input[name='check_idx[]']:checked").each(function(i){
						check_idx.push($(this).val());
					});
					console.log(check_idx);
					modal({
						type: 'confirm',  /* option : alert/confirm/prompt/success/warning/info/error/primary */
						text: '삭제하시겠습니까?',
						callback: function(result) {
							if(result){

								$("form#form_del").submit();

							}				
						}
					});		

			}

		});
});
