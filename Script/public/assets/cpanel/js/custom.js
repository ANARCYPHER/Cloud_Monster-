


function Button(obj) {
    this.node = obj;
    this.text = obj.text();
    this.loader = '<div class="bounce">\n' +
        '    <div class="bounce1"></div>\n' +
        '    <div class="bounce2"></div>\n' +
        '    <div class="bounce3"></div>\n' +
        '</div>';
    this.loading = function(){
        this.disable(true);
        this.node.html(this.loader);
    }
    this.loaded = function(){
        this.disable(false);
        this.node.html(this.text);
    }
    this.disable = function(t = true){
        if(t){
            if(!this.node.hasClass('disabled')){
                this.node.addClass('disabled');
            }
        }else{
            this.node.removeClass('disabled');
        }
    }
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text)
}


function formatBytes(a,b=2){if(0===a)return"0 Bytes";const c=0>b?0:b,d=Math.floor(Math.log(a)/Math.log(1024));return parseFloat((a/Math.pow(1024,d)).toFixed(c))+" "+["Bytes","KB","MB","GB","TB","PB","EB","ZB","YB"][d]}

function isEmpty(str) {
    str = str.trim();
    return (!str || str.length === 0 );
}
function timeFormat(duration)
{
    // Hours, minutes and seconds
    var hrs = Math.floor(duration / 3600);
    var mins = Math.floor((duration % 3600) / 60);
    var secs = Math.floor(duration % 60);

    var ret = "";

    if (hrs > 0) {
        ret += "" + hrs + " hours " + (mins < 10 ? "0" : "");
    }

    ret += "" + mins + " min " + (secs < 10 ? "0" : "");
    ret += "" + secs + ' sec';
    return ret;
}

// $('#create-bucket').on('click', function(){
//
//
//     let bucketName = $('input.bucket-name').val();
//
//     $.ajax({
//
//         url : ROOT + '/admin/buckets/new',
//         type : 'POST',
//         data : {
//             'name' : bucketName
//         },
//         dataType:'json',
//         success : function(data) {
//             console.log(data);
//             displayAlerts(data.alerts);
//         },
//         error : function(request,error)
//         {
//             alert(JSON.stringify(request));
//         }
//     });
//
//
//
// });
//
//


function method_exists (obj, method) {

    if (typeof obj === 'string') {
        return window[obj] && typeof window[obj][method] === 'function'
    }

    return typeof obj[method] === 'function';
}

function isValidURL(str) {
    var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
        '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
        '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
        '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
        '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
        '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
    return !!pattern.test(str);
}

$(document).on('click', '.clickable-row', function(){
    let target = $(this).data('target');
    let url = $(this).data("href");
    if(target === '_blank'){
        window.open(url, '_blank').focus();
    }else{
        window.location = url;
    }
} );


$.fn.hasAttr = function(name) {
    return this.attr(name) !== undefined;
};


function createCookie(name, value, days) {
    let expires;

    if (days) {
        let date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
    } else {
        expires = "";
    }
    document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + expires + "; path=/";
}

function readCookie(name) {
    let nameEQ = encodeURIComponent(name) + "=";
    let ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ')
            c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0)
            return decodeURIComponent(c.substring(nameEQ.length, c.length));
    }
    return null;
}

function eraseCookie(name) {
    createCookie(name, "", -1);
}

































function getHtmlAlert(msg, type){

    return '<div class="alert alert-'+ type +' alert-dismissible" role="alert">' + msg + '</div>';

}

function addAlert(msg , type, alertWrapId = ''){

    if(isEmpty(alertWrapId)) alertWrapId = 'alert-wrap';

    let elementId = '#' + alertWrapId;
    let alertWrap = $(elementId);

    if(alertWrap.length > 0){

        //clear old alerts
        alertWrap.html(getHtmlAlert(msg, type));

    }

}

function cleanAlerts(alertWrapId = ''){
    if(isEmpty(alertWrapId)) alertWrapId = 'alert-wrap';
    let elementId = '#' + alertWrapId;
    let alertWrap = $(elementId);

    if(alertWrap.length > 0){
        alertWrap.html('');
    }
}

function displayAlerts(alerts, alertWrapId = 'alert-wrap'){

    if(typeof alerts === 'object' && alerts !== null){

        let elementId = '#' + alertWrapId;
        let alertWrap = $(elementId);

        if(alertWrap.length > 0){

            //clear old alerts
            alertWrap.html('');

            for (let key in alerts) {

                let msg = '';
                let value = alerts[key];

                if(value.length === 1){

                    alertWrap.append(getHtmlAlert(value[0], key));

                }else{

                    value.forEach(function (item, index) {

                        alertWrap.append(getHtmlAlert(item, index));

                    });

                }

            }

        }


    }



}


function redirect(path = '', isFull = false){
    window.location.href = !isFull ? ROOT + '/cpanel' + path : path;
}


function insertParam(key, value) {
    key = encodeURIComponent(key);
    value = encodeURIComponent(value);

    let kvp = document.location.search.substr(1).split('&');
    let i=0;

    for(; i<kvp.length; i++){
        if (kvp[i].startsWith(key + '=')) {
            let pair = kvp[i].split('=');
            pair[1] = value;
            kvp[i] = pair.join('=');
            break;
        }
    }

    if(i >= kvp.length){
        kvp[kvp.length] = [key,value].join('=');
    }

    // can return this or...
    let params = kvp.join('&');

    // reload page with new params
    document.location.search = params;
}