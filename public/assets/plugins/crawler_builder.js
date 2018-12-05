/**
 * Created by hocvt on 6/12/17.
 */
(function ( $ ) {

    $.fn.crawler_builder = function(options) {
        var site_info = options.site_info || {
            start_url : "http://google.com",
            title: "Google",
            browser_engine: "curl",
            delay : 0,
            selectors: [
                {
                    id: 'list_page',
                    title: 'Trang danh sach',
                    test_url: 'http://google.com',
                    parent_selectors : ['root'],
                    type : 'link',
                    selector : 'xpath:',
                    multiple : false,
                    delay : 0
                }
            ]
        };
        var graph_viewer = document.getElementById('graph_viewer');
        var $graph_viewer = $('#graph_viewer');
        var node_info = this.find('#step_info');
        var node_editor = this.find('#step_editor');
        var site_preview = this.find('#site_preview');
        var graph, graph_nodes, graph_edges;
        console.log(site_preview);
        var graph = null;

        function init() {
            console.log(getParents());

            // init graph
            updateGraph();

            // init form
            initForm({});

            // float form
            // new Tether({
            //     element: '#step_info_form',
            //     target: '#site_preview',
            //     attachment: 'middle center',
            //     targetAttachment: 'middle center',
            //     targetModifier: 'visible'
            // });
            $('#change_status_check').click(function(){
                return changeStatus(10);
            });
            $('#change_status_ready').click(function(){
                return changeStatus(1);
            });
            $('#change_status_edit').click(function(){
                return changeStatus(100);
            });
            $('#disable_image').change(function (e) {
                site_preview.data('parsed', false);
            })

        }

        function updateGraph() {
            var graph_options = {
                layout : {
                    // hierarchical: {
                    //     direction : 'UD',
                    //     sortMethod: 'hubsize'
                    // }
                    randomSeed: 2
                },
                edges: {
                    smooth: true,
                    arrows: {to : true }
                }
            };
            if(!graph){
                graph_nodes = new vis.DataSet(getNodes());
                graph_edges = new vis.DataSet(getEdges());
                var graph_data = {
                    nodes : graph_nodes,
                    edges : graph_edges
                };
                graph = new vis.Network(graph_viewer, graph_data, graph_options);
                $('#redraw_graph').click(function (e) {
                    console.log("Fitting...");
                    graph.fit();
                });
                graph.on('selectNode', selectNode);
                graph.on('deselectNode', deselectNode);
            }else{
                graph_nodes.clear();
                graph_edges.clear();
                graph_nodes.add(getNodes());
                graph_edges.add(getEdges());
            }
        }

        function getNodes() {
            var _root = {
                id : 'root',
                label: "Start URL",//site_info.title +
                color: 'red',shape: "box",
                font: {color: "#fff"}
            };
            if(site_info.delay){
                _root.label += "(" + site_info.delay + ")";
            }
            var nodes = [
                _root
            ];
            for(var i = 0; i < site_info.selectors.length; i++){
                var step = site_info.selectors[i];
                var node = {
                    id : step.id,shape: "box",
                    label : step.title ? step.title : step.id
                };
                if(step.type == 'data' || step.type == 'get_link'|| step.type == 'get_remote_title'){
                    node.color = 'lime';
                }
                if(step.type == 'keywords' || step.type == 'name' || step.type == 'get_links'){
                    node.color = 'gray';
                }
                if(step.type == 'click' || step.type == 'stop_click'){
                    node.color = 'blue';
                    node.font = {color : '#ffffff'};
                }
                if(step.delay != undefined && step.delay != site_info.delay && step.delay !== ''){
                    node.label += "(" + step.delay + ")";
                }
                nodes.push(node);
            }
            return nodes;
        }
        function getEdges() {
            var edges = [];
            for(var i = 0; i < site_info.selectors.length; i++){
                var step = site_info.selectors[i];
                for(var j = 0; j < step.parent_selectors.length; j++){
                    var edge = {
                        arrows : 'to',
                        from : step.parent_selectors[j],
                        to : step.id
                    };
                    if(step.type == 'data'){
                        edge.dashes = true;
                    }
                    edges.push(edge);
                }
            }
            return edges;
        }

        /**
         *
         * @param params
         * {
  nodes: [Array of selected nodeIds],
  edges: [Array of selected edgeIds],
  event: [Object] original click event,
  pointer: {
    DOM: {x:pointer_x, y:pointer_y},
    canvas: {x:canvas_x, y:canvas_y}
  }
}
         */
        function selectNode(params) {console.log(params);
            var selected_node = params.nodes[0];
            var step_info = {};
            var buttons = $("<div style='background: transparent;' id='context_buttons' class='btn-group-vertical'></div>");
            if(selected_node == 'root'){
                step_info = {'test_url':site_info.start_url, 'id' : 'root', parent_selectors: []};
                buttons.append("<button class='btn btn-sm btn-primary btn_add_child'><i class='fa fa-plus'></i> Add child</button>");
            }else{
                for(var i = 0; i < site_info.selectors.length; i++){
                    if(site_info.selectors[i].id == selected_node){
                        step_info = site_info.selectors[i];
                        break;
                    }
                }
                buttons.append("<button class='btn btn-sm btn-info btn_edit_clone'><i class='fa fa-pencil'></i> Edit</button>");
                buttons.append("<button class='btn btn-sm btn-primary btn_add_child'><i class='fa fa-plus'></i> Add child</button>");
                buttons.append("<button class='btn btn-sm btn-danger btn_delete'><i class='fa fa-trash'></i> Delete</button>");
            }
            buttons.offset({top: params.pointer.DOM.y, left: params.pointer.DOM.x});
            buttons.on('click', '.btn_add_child', function(e){
                initForm({
                    parent_selectors: [step_info.id],
                    test_url: step_info.test_url,
                    multiple: false
                });
                changeTab(2);
                buttons.remove();
            });
            buttons.on('click', '.btn_edit_clone', function (e) {
                initForm(step_info);
                changeTab(2);
                buttons.remove();
            });
            buttons.on('click', '.btn_delete', function (e) {
                initForm({multiple: false});
                changeTab(2);
                deleteNode(step_info.id);
                buttons.remove();
                return saveSite();
            });
            $graph_viewer.append(buttons);
            showStepInfo(step_info);
        }

        function deleteNode(node_id) {
            for(var i = 0; i < site_info.selectors.length; i++){
                if(site_info.selectors[i].id == node_id){
                    site_info.selectors.splice(i,1);
                    break;
                }
            }
            updateGraph();
        }


        /**
         * {
  nodes: [Array of selected nodeIds],
  edges: [Array of selected edgeIds],
  event: [Object] original click event,
  pointer: {
    DOM: {x:pointer_x, y:pointer_y},
    canvas: {x:canvas_x, y:canvas_y}
    }
  },
         previousSelection: {
    nodes: [Array of previously selected nodeIds],
    edges: [Array of previously selected edgeIds]
  }
         }
         * @param params
         */
        function deselectNode(params) {
            $('#context_buttons').remove();
        }

        function showStepInfo(info) {
            var html = $("<div>");
            html.append("<h4 class='mb1 mt1'>Step info</h4>");
            html.append("<div class=''><label class='bold'>ID</label> "+ info.id +"</div>");
            html.append("<div class=''><label class='bold'>Test URL</label> " + info.test_url + "</div>");
            html.append("<div class=''><label class='bold'>Parent</label> " + info.parent_selectors.join(",") + "</div>");
            html.append("<div class=''><label class='bold'>Type</label> " + info.type + "</div>");
            html.append("<div class=''><label class='bold'>Selector</label> " + info.selector + "</div>");
            html.append("<div class=''><label class='bold'>Multiple</label> " + (info.multiple ? "true" : "false") + "</div>");
            var _delay = 0;
            if(info.delay != undefined && info.delay !== ''){
                _delay = info.delay + "s";
            }else if(!info.delay){
                _delay = site_info.delay + "s";
            }
            html.append("<div class=''><label class='bold'>Delay</label> " + _delay + "</div>");
            /*
             <div class="btn-group" role="group" aria-label="Basic example">
             <button type="button" class="btn btn-secondary">Left</button>
             <button type="button" class="btn btn-secondary">Middle</button>
             <button type="button" class="btn btn-secondary">Right</button>
             </div>
            * */
            // var buttons = $("<div class='btn-group btn-group-sm' role='group' aria-label='Actions'>" +
            //     "<button class='btn btn-danger btn_delete'><b>x</b> Del</button>" +
            //     "<button class='btn btn-info btn_edit'><b>/</b> Edit/Clone</button>" +
            //     "<button class='btn btn-warning btn_test'><b>*</b> Test</button>" +
            //     "<button class='btn btn-info btn_add_child'><b>+</b> Add child</button></div>");
            // html.append(buttons);
            node_info.html(html).removeClass('hide');
            node_info.find('.btn_edit').click(function(event){
                initForm(info);
            });
            node_info.find('.btn_test').click(function(event){
                testStep(info);
            });

            changeTab(1);
        }

        /**
         *
         * @param exclude []
         */
        function getParents(exclude) {
            exclude = exclude || [];
            var parents = ['root'];
            for(var i = 0; i < site_info.selectors.length; i++){
                if(exclude.indexOf(site_info.selectors[i].id) !== false){
                    parents.push(site_info.selectors[i].id);
                }
            }
            return parents;
        }

        var form = this.find('#step_info_form');
        form.on('submit', function(){return false;});

        function initForm(step_info) {
            form.find('#step_id').val(step_info.id || '');
            var options = "";
            var _options = getParents(step_info.parent_selectors);
            for(var i = 0; i < _options.length; i++){
                options += "<option value='" + _options[i] + "'>" + _options[i] + "</option>";
            }
            form.find('#step_type').val(step_info.type);
            form.find('#step_test_url').val(step_info.test_url);
            form.find('#step_selector').val(step_info.selector);
            form.find('#step_title').unbind().on('change', function(e){
                if(!step_info.id){
                    form.find('#step_id').val(buildId(this.value));
                }
            }).val(step_info.title);
            form.find('#step_multiple').prop('checked', step_info.multiple);
            console.log(options);
            form.find('#step_parent_selectors').html(options).val(step_info.parent_selectors);
            if(step_info.test_url){
                getPreview(step_info.test_url);
            }
            if(step_info.delay != undefined){
                form.find('#step_delay').val(step_info.delay);
            }
            form.find('#step_selector_inspector').unbind('click').on('click', function(e){
                unShowSiblings();
                if(site_preview.find('#site_preview_iframe').length){
                    var iframe = site_preview.find('#site_preview_iframe')[0];
                    if(iframe.contentWindow.isInspecting()){
                        iframe.contentWindow.stopInspector();
                        $.notify({"message" : "Stop inspector"});
                        return false;
                    }
                }
                var current_url = form.find('#step_test_url').val();
                getPreview(current_url, function(){
                    $.notify({"message" : "Start inspector"});
                    site_preview.find('#site_preview_iframe')[0].contentWindow.startInspector({
                        root: '',
                        onClick: function(e, paths, xpaths){
                            selectPath(paths, xpaths, form.find('#step_selector'));
                        }
                    });
                    scrollTo(site_preview);
                });
                return false;
            });
            form.find('#step_selector_test').unbind('click').on('click', function(e){
                var current_url = form.find('#step_test_url').val();
                if(site_preview.find('#site_preview_iframe').length){
                    site_preview.find('#site_preview_iframe')[0].contentWindow.stopInspector();
                }
                /////////
                getPreview(current_url, function(){
                    var selector = form.find('#step_selector').val();
                    var multiple = form.find('#step_multiple').prop('checked');
                    var selector_info = selector.split(/:\s/);
                    if(selector_info.length != 2){
                        $.notify({
                            type: 'danger',
                            message: 'Wrong selector'
                        });
                        return false;
                    }
                    scrollTo(site_preview);
                    if(selector_info[0] == 'css'){
                        site_preview.find('#site_preview_iframe')[0]
                            .contentWindow
                            .previewCssSelector(selector_info[1], showInspected, multiple);
                    }else if(selector_info[0] == 'xpath'){
                        site_preview.find('#site_preview_iframe')[0]
                            .contentWindow
                            .previewXpathSelector(selector_info[1], showInspected, multiple);
                    }
                });
                // site_preview.startInspect({
                //     root: '#site_preview',
                //     onClick: function(e, path, xpath){
                //         selectPath(path, xpath, form.find('#step_selector'))
                //     }
                // });
                return false;
            });
            form.find('#btn_form_save').unbind('click').on('click', function(e){
                console.log("Saving...");
                saveStep();
            });
        }

        function showInspected(result) {return false;
            var message = '<ul class="list-unstyled">';
            message += "<li class='font-weight-bold'>Have " + result.length + " element(s)</li>";
            for(var i = 0; i < result.length; i++){
                message += '<li style="border-bottom: 1px solid #ccc;">';
                message += '<div>' + result[i].text + '</div>';
                if(result[i].link){
                    message += '<i class="text-primary">' + result[i].link + '</i>';
                }
                message += '</li>';
            }
            message += "</ul>";
            console.log(message);
            bootbox.alert({
                size: 'large',
                title: "Match by selector",
                message: message
            });
        }

        function saveStep() {
            var step_info = {
                id : form.find('#step_id').val(),
                title : form.find('#step_title').val(),
                type : form.find('#step_type').val(),
                test_url : form.find('#step_test_url').val(),
                parent_selectors : form.find('#step_parent_selectors').val(),
                selector : form.find('#step_selector').val(),
                multiple : form.find('#step_multiple').prop('checked'),
                delay : form.find('#step_delay').val()
            }
            if(step_info.id == 'root'){
                alert('invalid step id :: ' + root);
                return false;
            }
            // validate
            if(!step_info.id){
                alert('Step id can not be null');
                return false;//saveSite();
            }
            if(!step_info.type){
                alert('Step type can not be null');
                return false;//saveSite();
            }

            for(var i = 0; i < site_info.selectors.length; i++){
                if(site_info.selectors[i].id == step_info.id){
                    site_info.selectors[i] = step_info;
                    return saveSite();// update
                }
            }
            site_info.selectors.push(step_info);
            return saveSite();// add
        }

        function saveSite() {
            axios.post(save_selector_link, {
                id: site_info.site_id,
                selectors: site_info.selectors
            }).then(function(response){
                if(response.data.success){
                    $.notify({
                        type: "success",
                        message: "saved"
                    });
                }else{
                    $.notify({
                        message: response.data.message ? response.data.message : "Can not save"
                    }, {type: "danger",
                        delay: 5000,
                        timer: 5000});
                }
            }).catch(function(error){
                $.notify({
                    message: "Post error"
                }, {type: "danger"});
            });
            updateGraph();
        }

        function getPreview(url, ready_callback) {
            if(url == site_preview.data('url') && site_preview.data('parsed')){
                if(ready_callback){
                    ready_callback();
                }
                return;
            }
            $.notify({
                message: "Parsing " + url + " ..."
            });
            var renderer = site_info.browser_engine;
            if(renderer == 'phantomjs' && site_info.pre_render){
                renderer = 'prerender';
            }
            var _url = html_render_url + "?url=" + encodeURIComponent(url) + "&engine=" + renderer;
            if($('#disable_image').prop('checked')){
                _url += '&disable_image=true';
            }
            site_preview.data('parsed', false);
            site_preview.html("<iframe id='site_preview_iframe' width='100%' height='1000px' src='" + _url + "'></iframe>");
            $('#site_preview_iframe').on('load', function(){
                site_preview.data('url', url);
                site_preview.data('parsed', true);
                console.log(this);
                var e_body_height = $(window).height();//this.contentWindow.getDocumentHeight();
                $(this).height(e_body_height);
                if(ready_callback){
                    ready_callback();
                }
            });
            return;
        }

        function testStep(step_info) {
            var url = step_info.test_url;
            getPreview(url);
        }

        function unShowSiblings() {
            site_preview.find('#site_preview_iframe')[0]
                .contentWindow
                .showSibling();
        }

        function selectPath(csspaths, xpaths, input) {
            $('.selected_path_view').remove();
            var paths = [];
            for(var j = 0; j < csspaths.length; j++){
                paths.push(["css: " + csspaths[j]]);
            }
            for(var k = 0; k < xpaths.length; k++){
                paths.push("xpath: " + xpaths[k]);
            }
            var selector = paths[0];
            var modal = $("<div class='selected_path_view' style=''></div>");
            var ul = $("<ul>");
            modal.append(
                "<div>" +
                "<b><i class='fa fa-close'></i> Select a selector</b> or " +
                "<button class='btn btn-sm btn-danger show_sibling'><i class='fa fa-eye'></i> Show sibling </button> " +
                "</div>"
            );
            for(var i = 0; i < paths.length; i++){
                ul.append("<li style='cursor: pointer;' class='border mb1'>" + paths[i] + "</li>");
            }
            modal.append(ul);
            $('body').append(modal);
            modal.on('click', 'li', function(e){
                var text = e.target.innerText;
                input.val(text);
                if(site_preview.find('#site_preview_iframe').length){
                    site_preview.find('#site_preview_iframe')[0].contentWindow.stopInspector();
                }
                modal.remove();
                scrollTo('#step_test_url');
            }).on('click', '.fa-close', function(){
                site_preview.find('#site_preview_iframe')[0]
                    .contentWindow
                    .showSibling();
                modal.remove();
            }).on('click', '.show_sibling', function(){
                var selector_info = selector[0].split(/:\s/);
                if(selector_info.length != 2){
                    $.notify({
                        type: 'danger',
                        message: 'Wrong selector'
                    });
                    return false;
                }
                scrollTo(site_preview);
                if(selector_info[0] == 'css'){
                    site_preview.find('#site_preview_iframe')[0]
                        .contentWindow
                        .showSibling(selector_info[1], 'css');
                }else if(selector_info[0] == 'xpath'){
                    site_preview.find('#site_preview_iframe')[0]
                        .contentWindow
                        .showSibling(selector_info[1], 'xpath');
                }
            });
        }

        function buildId(title) {
            //Đổi chữ hoa thành chữ thường
            slug = title.toLowerCase();

            //Đổi ký tự có dấu thành không dấu
            slug = slug.replace(/á|à|ả|ạ|ã|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ/gi, 'a');
            slug = slug.replace(/é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ/gi, 'e');
            slug = slug.replace(/i|í|ì|ỉ|ĩ|ị/gi, 'i');
            slug = slug.replace(/ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ/gi, 'o');
            slug = slug.replace(/ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự/gi, 'u');
            slug = slug.replace(/ý|ỳ|ỷ|ỹ|ỵ/gi, 'y');
            slug = slug.replace(/đ/gi, 'd');
            //Xóa các ký tự đặt biệt
            slug = slug.replace(/\`|\~|\!|\@|\#|\||\$|\%|\^|\&|\*|\(|\)|\+|\=|\,|\.|\/|\?|\>|\<|\'|\"|\:|\;|_/gi, '');
            //Đổi khoảng trắng thành ký tự gạch ngang
            slug = slug.replace(/ /gi, "-");
            //Đổi nhiều ký tự gạch ngang liên tiếp thành 1 ký tự gạch ngang
            //Phòng trường hợp người nhập vào quá nhiều ký tự trắng
            slug = slug.replace(/\-{2,}/gi, '-');
            //Xóa các ký tự gạch ngang ở đầu và cuối
            slug = slug.replace(/^\-+|\-+$/gi, '');
            return slug;
        }

        function changeTab(index) {
            $('#info-and-editor .nav li:eq(' + index + ') a').tab('show');
        }

        function changeStatus(new_status) {
            axios.post(change_status_url, {
                status : new_status
            })
                .then(function(response){
                    if(response.data.success){
                        $.notify({
                            message: "Saved site status " + response.data.new_status + " !!!"
                        });
                        if(new_status == 1){
                            window.location = home_url;
                        }
                    }else{
                        $.notify({
                            message: "Error :: " . response.data.message
                        }, {type: 'danger'});
                    }

                })
                .catch(function(error){
                    $.notify({
                        message: "Can not change site status !!!"
                    }, {type: 'danger'});
                });
        }

        init();
    };

}( jQuery ));

$(document).ready(function () {
    $('#crawler_builder').crawler_builder({
        site_info : site_info
    });
    // $.notify({
    //     message: "HAVE FUN !!!"
    // });
});

function scrollTo(el) {
    el = el instanceof jQuery ? el : $(el);
    var to = el.offset().top;
    $('html,body').animate({scrollTop: to});
}
