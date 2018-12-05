(function( $ ){
    var getStringForElement = function (el) {
        var string = el.tagName.toLowerCase();

        if (el.id) {
            string += "#" + el.id;
        }
        if (el.className) {
            string += "." + el.className.replace(/ /g, '.');
        }

        return string;
    };

    var default_options = {
        xpath_view : '',
        path_view : '',
        content_view : '',
        border_color : 'blue',
        selector : '',
        onClick : function (e, path, xpath) {
            // alert(e.target.tagName);
            console.log(path);
            console.log(xpath);
        },
        root : 'body',
    };
    var elements = {};
    var pub = {};
    var borderWidth = 2;
    var is_active = false;
    var initialized = false;

    $.fn.startInspect = function(options){
        default_options.selector = this;
        options = options || {};
        options = jQuery.extend(default_options, options);
        initStylesheet();
        if (is_active !== true) {
            is_active = true;
            createOutlineElements();
            // aply outline
            jQuery(options.selector).on('mousemove.dom_outline', updateOutlinePosition);
            // remove all default click
            jQuery(options.selector).unbind('click').on('click', '*', function(e){
                if(!is_active){return false};
                var _path = getPath(e.target, options.root);
                var paths = [
                    _path
                ];
                if(_path.match(/\>\s[^\s]+#[^\s]+$/)){
                    paths.push(_path.replace(/#[^>\.\s]+([^\s]*)$/, '$1'));
                }
                var xpath1 = getXPath(e.target, options.root),
                    xpath2 = getXPath(e.target, options.root, null, true);
                var xpaths = [xpath1];
                if(xpath2 != xpath1){
                    xpaths.push(xpath2);
                }
                console.log(paths);
                options.onClick(e, paths, xpaths);
                return false;
            });

            // jQuery(options.selector).on('keyup.dom_outline', stopOnEscape);
            // if (self.opts.onClick) {
            //     setTimeout(function () {
            //         jQuery('body').on('click.' + self.opts.namespace, function(e){
            //             if (self.opts.filter) {
            //                 if (!jQuery(e.target).is(self.opts.filter)) {
            //                     return false;
            //                 }
            //             }
            //             clickHandler.call(this, e);
            //         });
            //     }, 50);
            // }
        }
    }

    function getPath(element, root, prefix, string) {
        prefix = prefix || 'body';
        if (typeof(string) == "undefined") {
            string = true;
        }

        var p = [];
        $(element).parents().each(function() {
            if($(this).is(root)){
                return false;
            }
            var el_present = getStringForElement(this)
            p.push(el_present);
            if(el_present.match(/#/)){
                return false;
            }
        });
        if(p.length == 0 || !p[p.length - 1].match(/#/)){
            p.push(prefix);
        }
        p.reverse();
        p.push(getStringForElement(element));
        return string ? p.join(" > ") : p;
    }

    function getXPath(element, root, prefix, ignore_id) {
        ignore_id = ignore_id || false;
        prefix = prefix || '/html/body';
        console.log('prefix ' + prefix);
        if($(element).is(root)) {
            return prefix;
        }
        console.log(ignore_id);
        if (element.id !== '' && !ignore_id)
            return "//*[@id='" + element.id + "']";

        if (element===document.body)
            return element.tagName.toLowerCase();

        var ix= 0;
        var siblings= element.parentNode.childNodes;
        for (var i= 0; i<siblings.length; i++) {
            var sibling= siblings[i];

            if (sibling===element)
                return getXPath(element.parentNode,
                    root,
                    prefix) + '/' + element.tagName.toLowerCase() + '[' + (ix + 1) + ']';

            if (sibling.nodeType===1 && sibling.tagName === element.tagName) {
                ix++;
            }
        }
    }

    function writeStylesheet(css) {
        if(initialized == false){
            initialized = true;
            var element = document.createElement('style');
            element.type = 'text/css';
            document.getElementsByTagName('head')[0].appendChild(element);

            if (element.styleSheet) {
                element.styleSheet.cssText = css; // IE
            } else {
                element.innerHTML = css; // Non-IE
            }
        }
    }

    function initStylesheet() {
        var css = '' +
            '.' + 'dom_outline' + ' {' +
            '    background: #09c;' +
            '    position: absolute;' +
            '    z-index: 90;' +
            '}' +
            '.' + 'dom_outline' + '_label {' +
            '    background: #09c;' +
            '    border-radius: 2px;' +
            '    color: #fff;' +
            '    font: bold 12px/12px Helvetica, sans-serif;' +
            '    padding: 4px 6px;' +
            '    position: absolute;' +
            '    text-shadow: 0 1px 1px rgba(0, 0, 0, 0.25);' +
            '    z-index: 90;' +
            '}';

        writeStylesheet(css);
    }


    function createOutlineElements() {
        elements.label = jQuery('<div></div>').addClass('dom_outline_label').appendTo('body');
        elements.top = jQuery('<div></div>').addClass('dom_outline').appendTo('body');
        elements.bottom = jQuery('<div></div>').addClass('dom_outline').appendTo('body');
        elements.left = jQuery('<div></div>').addClass('dom_outline').appendTo('body');
        elements.right = jQuery('<div></div>').addClass('dom_outline').appendTo('body');
    }

    function removeOutlineElements(element) {
        jQuery.each(element, function(name, value) {
            if (name != "target") {
                value.remove();
            }
        });
    }

    function compileLabelText(element, width, height) {
        var label = element.tagName.toLowerCase();
        if (element.id) {
            label += '#' + element.id;
        }
        if (element.className) {
            label += ('.' + jQuery.trim(element.className).replace(/ /g, '.')).replace(/\.\.+/g, '.');
        }
        return label + ' (' + Math.round(width) + 'x' + Math.round(height) + ')';
    }

    function getScrollTop() {
        if (!elements.window) {
            elements.window = jQuery(window);
        }
        return elements.window.scrollTop();
    }

    function updateOutlinePosition(e) {
        if (e.target.className.indexOf('dom_outline') !== -1) {
            return;
        }
        // if (self.opts.filter) {
        //     if (!jQuery(e.target).is(self.opts.filter)) {
        //         return;
        //     }
        // }
        pub.element = e.target;

        var b = borderWidth;
        var scroll_top = getScrollTop();
        var pos = pub.element.getBoundingClientRect();
        var top = pos.top + scroll_top;

        var label_text = compileLabelText(pub.element, pos.width, pos.height);
        var label_top = Math.max(0, top - 20 - b, scroll_top);
        var label_left = Math.max(0, pos.left - b);

        elements.label.css({ top: label_top, left: label_left }).text(label_text);
        elements.top.css({ top: Math.max(0, top - b), left: pos.left - b, width: pos.width + b, height: b });
        elements.bottom.css({ top: top + pos.height, left: pos.left - b, width: pos.width + b, height: b });
        elements.left.css({ top: top - b, left: Math.max(0, pos.left - b), width: b, height: pos.height + b });
        elements.right.css({ top: top - b, left: pos.left + pos.width, width: b, height: pos.height + (b * 2) });
    }

    $.fn.isInspecting = function () {
        return is_active;
    }

    $.fn.stopInspect = function() {
        is_active = false;
        removeOutlineElements(elements);
        // jQuery('body').off('mousemove.' + self.opts.namespace)
        //     .off('keyup.' + self.opts.namespace)
        //     .off('click.' + self.opts.namespace);
        // jQuery(window).off('resize.' + self.opts.namespace);
    }

})( jQuery );