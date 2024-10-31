(function ($, root, undefined) {
	
	$(function () {
		
		'use strict';

		$('#draw_type').on("change",function(){
			if($(this).val()=="none"){
				$('.question-div').removeClass("show");
				$('.single-answer-div').removeClass("show");
				$('.multiple-answer-div').removeClass("show");
				$('.multiple-answer-ans-div').removeClass("show");
				$('.answer-correct-check-div').removeClass("show");
				$('#multi-answer').val('');
				$('#multi-answers').val('');
				$('#single-answer').val('');
				$('#question').val("");
				$('.single-answer-has-correct').removeClass("show");

			}else if($(this).val()=="multi"){
				$('.question-div').addClass("show");
				$('.single-answer-div').removeClass("show");
				$('.multiple-answer-div').addClass("show");
				$('.multiple-answer-ans-div').removeClass("show");
				$('#multi-answer').val('');
				$('#multi-answers').val('');
				$('#single-answer').val('');
				$('.single-answer-has-correct').removeClass("show");
				$('.answer-correct-check-div').addClass("show");
				$('#single-answer-has-correct').val("0");
				$('#single-answer-has-correct').unbind("change");
				$('#single-answer-has-correct').bind("change",function(){
					if($(this).val()==1){
						$('.multiple-answer-ans-div').addClass("show");
						if($('#multi-answer').val() == ""){$('#multi-answer').val('1');}
					}else{
						$('.multiple-answer-ans-div').removeClass("show");
					}
				});

			}else if($(this).val()=="single"){
				$('.question-div').addClass("show");
				$('.multiple-answer-div').removeClass("show");
				$('.multiple-answer-ans-div').removeClass("show");
				$('.single-answer-has-correct').removeClass("show");
				$('.single-answer-div').removeClass("show");
				$('.answer-correct-check-div').addClass("show");
				$('#multi-answer').val('');
				$('#multi-answers').val('');
				$('#single-answer').val('');
				$('#single-answer-has-correct').val("0");
				$('#single-answer-has-correct').unbind("change");
				$('#single-answer-has-correct').bind("change",function(){
					if($(this).val()==1){
						$('.single-answer-div').addClass("show");
					}else{
						$('.single-answer-div').removeClass("show");
					}
				});
			}
		});



		if($('#single-answer-has-correct').length>0){
			if($('#draw_type').val()=="multi"){
						$('#single-answer-has-correct').unbind("change");
						$('#single-answer-has-correct').on("change",function(){
							if($(this).val()==1){
								$('.multiple-answer-ans-div').addClass("show");
								if($('#multi-answer').val() == ""){$('#multi-answer').val('1');}
							}else{
								$('.multiple-answer-ans-div').removeClass("show");
							}
						});
			}else if($('#draw_type').val()=="single"){
						$('#single-answer-has-correct').unbind("change");
						$('#single-answer-has-correct').on("change",function(){							
							if($(this).val()==1){
								$('.single-answer-div').addClass("show");
							}else{
								$('.single-answer-div').removeClass("show");
							}
						});
			}
		}


			
	});


	
})(jQuery, this);

function validate(){

	var ok = true;
	var err = "";

	$ = jQuery;

	if($('.formerrors').length>0){
		$('.formerrors').remove();
	}

	if($('#draw_type').val()=="none"){
			$('#multi-answer').val('');
			$('#multi-answers').val('');
			$('#single-answer').val('');		
			$('#question').val('');
	}else if($('#draw_type').val()=="single"){
			$('#multi-answer').val('');
			$('#multi-answers').val('');		
			if($('#question').val()=="" || $('#question').val()==" "){
				ok = false;
				err+="<li>Missing Question</li>";
			}	
			if($('#single-answer-has-correct').val()=="1" && ($('#single-answer').val()=='' || $('#single-answer').val()==' ')){
				ok = false;
				err+="<li>Correct text answer is required</li>";				
			}
	}else if($('#draw_type').val()=="multi"){
			$('#single-answer').val('');		
			if($('#question').val()=="" || $('#question').val()==" "){
				ok = false;
				err+="<li>Missing Question</li>";
			}	
			if($('#multi-answers').val()=='' || $('#multi-answers').val()==' '){
				ok = false;
				err+="<li>Multiple choices must be given</li>";				
			}	
			if($('#single-answer-has-correct').val()=="1" && ($('#multi-answer').val()=='' || $('#multi-answer').val()==' ')){
				ok = false;
				err+="<li>Multiple choice answer must be given</li>";				
			}
			if(ok){
				if(!createMultipleChoiceAnswers()){
					ok = false;
				}
			}
	}
	if(!ok){
		$('<ul class="formerrors">'+err+'</ul>').insertBefore('p.submit');
	}
	return ok;

}

function createMultipleChoiceAnswers(){

	var choices = jQuery('#multi-answers').val();
	var parsedchoices = choices.replace(/\n/g , "|")+"|";
	jQuery('#answers').val(parsedchoices);
	return true;
}