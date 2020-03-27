window.homeP = {};
homeP.el = {};
homeP.fn = {};
toastr.options.timeOut = 15000; 
toastr.options.extendedTimeOut = 15000; 

$(function(){
    homeP.fn.init();
    homeP.fn.initSelfInittedAjaxButtons();
    homeP.fn.initReloadTableBtn();
});


homeP.fn.initReloadTableBtn = function(){
    homeP.el.reloadTableBtn.addEventListener('click',(ev) => {
        if(ev instanceof Event){
            ev.preventDefault();
        }
        $.ajax({
            url: 'getUsers',
            data: {test:'test'},
            complete: function (data, status) {
                let res = data.responseJSON;

            }
        });
    });
};

homeP.fn.init= function(){
    homeP.el.reloadTableBtn = document.querySelector('.reload_table_btn');
}

homeP.fn.initSelfInittedAjaxButtons = function(){
    let btns = Array.from(document.querySelectorAll('button[data-btn-self-init-ajax]'));
    btns.forEach(b => {
        b.addEventListener('click',(ev) => {
            let method = b.getAttribute('data-btn-self-init-ajax');
            ev.preventDefault();
            $.ajax({url: method});
        });
    });
};



$(document).ajaxComplete((event, xhr, settings) => {
    let res = xhr.responseJSON;
    if(!res) return;
    toastr.clear();
    if(res.errors && res.errors.length > 0){
        res.errors.forEach(e => toastr.error(e));
    }
    if(res.confirms && res.confirms.length > 0){
        res.confirms.forEach(c => toastr.success(c));
    }
});

$(document).ajaxSend((event, xhr, settings)=>{
    settings.url = getAjaxUrl() + settings.url;
});

$.ajaxSetup({
    type: "POST",
    dataType: "json",
});

function getAjaxUrl(){
    let pn = location.pathname.split('/');
    pn = pn.slice(0,pn.length-1).join('/');

    return location.origin + pn + '/ajax.php/';
}

