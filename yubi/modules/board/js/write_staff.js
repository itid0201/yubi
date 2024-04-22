$(function() { 
	var objInputDept = $(document).find("input[name='reg_name']");
	var objInputDiv = objInputDept.parents("div.module_w");
	objInputDept.addClass("b_staff");	
	objInputDiv.append('<p class="mat5"><span class="star_a"></span>이름으로 직원 검색이 가능합니다.</p>');	
	
	$(document).on("keydown","input[name='reg_name']",function(event){
		if(event.keyCode==13){
			event.preventDefault();
		}
	})
	
	$(document).on("keyup","input[name='reg_name']",function(event){
		/*console.log(event.keyCode);*/
		if(event.keyCode!=27){			
			if( event.keyCode ==13 ){
				$(this).siblings("input[name='varchar_2']").focus();
				return false;
			}		
			
			if( $(document).find("div#text_staff").length == 0 ){
				objInputDiv.append('<div id="text_staff"><div id="search_text"></div></div><div class="scroll_wrap"></div>');					
			}			
			search_script( $(this).val() );
		}
	});	
	

	function search_script(keyword) {
/*		console.log(keyword);	*/
		$.post("/ybscript.io/staff/department_manage_proc",{
				keyword : keyword,
				search_key : 'app_staff',
				operation:'search_item'
		},function(data){
			if(data.length > 0){
			
			$('#text_staff').fadeIn();
			$('#search_text').html('<ul><\/ul>');
			$('#search_text ul').html(data);
			$('.stext').on("click", function(){

				$('#text_staff').fadeOut().find('li.stext').remove();
				$('.no_search').remove();

				var idx = $(this).children('span.ico_name').attr('id').replace('user_','');
				$.ajax({
					async : false,
					type: "POST",
					url: "/phonebook/notice_manage/distribution",
					dataType: "json",
					data : { 
						operation:"get_member_info",
						idx : idx,
						type : "member",
						returnType : "json"
					}, 
					success : function (data) {
						$(document).find("input[name='reg_name']").val(data.user_name);
						var dept_part = data.dept_title.split(" ");
						var regexp = /과|관$/;
						if(regexp.test(dept_part[1]))	$(document).find("input[name='depart_name']").val(dept_part[1]);	
						else									$(document).find("input[name='depart_name']").val(dept_part[0]);	
						$(document).find("input[name='varchar_2']").val(data.dept_position);						
						if( data.dept_tel.length > 0 ){
							$(document).find("input[name='phone_1']").val(data.dept_tel.split("-")[0]);
							$(document).find("input[name='phone_2']").val(data.dept_tel.split("-")[1]);
							$(document).find("input[name='phone_3']").val(data.dept_tel.split("-")[2]);	
						}	
					}
				});	
			});
			 
			}else{
				$('#text_staff').fadeOut();
			}
		});
	}	

});
