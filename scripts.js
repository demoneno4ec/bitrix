/*Поиск первого родителя по классу*/
function closest(el, cl) {
    while(!el.hasClass(cl)) {
        if(el[0].tagName.toLowerCase() == 'html') return false;
        el = el.parent();
    }
    return el;
}

/*Обработка клика по примерочной на мобильных устройствах*/
$(function(){
	let mq = window.matchMedia("(min-width: 1150px)");
    mq.addListener(addClickForFittingRoom);
    addClickForFittingRoom(mq);
});

let clickFunc = function() {
	let domEl = $(this);
	if(domEl.hasClass('open')){
		domEl.removeClass('open');
	}else{
		domEl.addClass('open');
	}
};
function addClickForFittingRoom(mq){
	let fittingRoom = $('.fitting-room');
	if(!mq.matches){
		fittingRoom.bind('click', clickFunc);
	}else{
		if(fittingRoom.hasClass('open')){
			fittingRoom.removeClass('open');
		}
		fittingRoom.unbind('click', clickFunc);
	}
}


/*Якорный переход через дата атрибут*/
$(function(){
	$('.anchor').on('click', function(e){
		let targetID = $(this).data('anchor');
		let targetElement = $(targetID);
		let needScroll = targetElement.offset().top - 150;
		if(targetElement.length > 0){
			$('body,html').animate({
	            scrollTop: needScroll,
	        }, 400);
	        return false;
		}
	});
});