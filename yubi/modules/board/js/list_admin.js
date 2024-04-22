$(function(){
	 	var obj_table = $("table.board_basic");
		/*체크박스 컨트롤*/
		$(".board_manager_btn > a.all_check").on("click", function(){			
			if($(this).hasClass("check") == false){
				$(this).addClass("check");
				obj_table.find("td > .list_checkbox input[type=checkbox]").prop("checked",true);
			}else{
				$(this).removeClass("check");
				obj_table.find("td > .list_checkbox input[type=checkbox]").prop("checked",false);
			}
		});
	
		obj_table.find(".list_checkbox label").on("click", function(){
			var obj_table = $(this).parent("div.board_manager_btn").siblings("table");			
			
			setTimeout(function(){
				var list_checkbox = obj_table.find("td .list_checkbox");
				if(list_checkbox.find("input[type=\"checkbox\"]").length == list_checkbox.find("input[type=\"checkbox\"]:checked").length){
					$(".board_manager_btn > a.all_check").addClass("check");
				}else{
					$(".board_manager_btn > a.all_check").removeClass("check");
				}
			},10);
		});
		/* 선택글 삭제 */
		$(".board_manager_btn > a.check_delete").on("click", function(){

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

			if( obj_table.find("input[type=checkbox]").is(":checked") != true){
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
