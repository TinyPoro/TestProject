ACjQuery(document).ready(function($){
    ACjQuery('li').unbind('click');
    ACjQuery(document).on('click', 'a', function(e){
        return false;
    }).on('dblclick', function(e){
        $(e.target).remove();
        return false;
    });
});

function startInspector(options) {
    resetPreview();
    ACjQuery('body').startInspect(options);
}
function stopInspector() {
    resetPreview();
    ACjQuery('body').stopInspect();
}
function isInspecting() {
    ACjQuery('body').isInspecting();
}

function getDocumentHeight() {
    return ACjQuery(document).height();
}

function resetPreview() {
    ACjQuery('.ac_inspected').removeClass('ac_inspected');
}

function previewCssSelector(selector, cb, multiple) {
    resetPreview();
    var els = ACjQuery(selector);
    var result = [];
    els.each(function(i, el){
        var _this = ACjQuery(this);
        _this.addClass('ac_inspected');
        var _e = {
            text: _this.text()
        };
        if(_this.is('a')){
            _e.link = _this.attr('href');
        }
        result.push(_e);
        return multiple;
    });
    scrollTo(els);
    if(cb){
        cb(result);
    }
}
function previewXpathSelector(selector, cb, multiple) {
    resetPreview();
    var els = document.evaluate(selector, document, null, XPathResult.UNORDERED_NODE_SNAPSHOT_TYPE);
    var result = [];
    for(var i = 0; i < els.snapshotLength; i++){
        var el = els.snapshotItem(i);
        if(i==0){
            scrollTo(el);
        }
        ACjQuery(el).addClass('ac_inspected');
        var _e = {
            text: el.textContent
        };
        if(el.href){
            _e.link = el.href;
        }
        result.push(_e);
        if(!multiple){
            break;
        }
    }
    if(cb){
        cb(result);
    }
}
function scrollTo(el) {
    el = el instanceof ACjQuery ? el : ACjQuery(el);
    var to = el.offset().top;
    ACjQuery('html,body').animate({scrollTop: to});
}
function makeDoubleRightClickHandler( handler ) {
    var timeout = 0, clicked = false;
    return function(e) {

        e.preventDefault();

        if( clicked ) {
            clearTimeout(timeout);
            clicked = false;
            return handler.apply( this, arguments );
        }
        else {
            clicked = true;
            timeout = setTimeout( function() {
                clicked = false;
            }, 300 );
        }
    };
}
var up = 0;
function showSibling(selector, _type){
    if(!selector){
        ACjQuery('.crwl_show').removeClass('crwl_show');
        up = 0;
        return;
    }
    var el = null;
    if(_type == 'css'){
        el = ACjQuery(selector);
    }else if(_type == 'xpath'){
        var els = document.evaluate(selector, document, null, XPathResult.UNORDERED_NODE_SNAPSHOT_TYPE);
        for(var i = 0; i < els.snapshotLength; i++) {
            el = ACjQuery(els.snapshotItem(i));
            break;
        }
    }
    // console.log(el.siblings());
    if(up == 0){// show sibling
        showInner(el);
        showInner(el.siblings());
    }else{ // show more sibling
        var parent = el.parent();
        for(var i = 1; i < up; i++){
            parent = parent.parent();
        }
        showInner(parent);
    }
    up++;
}

function showInner(el) {
    el.each(function(){
        if(ACjQuery(this).css('display') == 'none'){
            ACjQuery(this).addClass('crwl_show');
        }
        if(ACjQuery(this).css('visibility') == 'hidden'){
            ACjQuery(this).addClass('crwl_show');
        }
        if(ACjQuery(this).css('opacity') == '0'){
            ACjQuery(this).addClass('crwl_show');
        }
    });
    el.find('*').each(function(){
        if(ACjQuery(this).css('display') == 'none'){
            ACjQuery(this).addClass('crwl_show');
        }
        if(ACjQuery(this).css('visibility') == 'hidden'){
            ACjQuery(this).addClass('crwl_show');
        }
        if(ACjQuery(this).css('opacity') == '0'){
            ACjQuery(this).addClass('crwl_show');
        }
    });
}