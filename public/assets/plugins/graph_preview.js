var show_graph = function(id){
    axios.get(get_json_url.replace('__id__', id))
        .then(function(response){
            popup_graph(response.data);
        });
}

var popup_graph = function(site_info, mode, el){
    mode = mode || 'popup';

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
    var graph_nodes = new vis.DataSet(getNodes(site_info));
    var graph_edges = new vis.DataSet(getEdges(site_info));
    var graph_data = {
        nodes : graph_nodes,
        edges : graph_edges
    };
    if(mode == 'popup'){
        var message = "<div id='site_graph_preview_" + site_info.site_id + "' style='height: 400px; width: 100%;'></div>";
        bootbox.alert({
            size: 'large',
            title: "Graph site :: " + site_info.start_url,
            message: message
        }).on('shown.bs.modal',function(){
            var container = document.getElementById('site_graph_preview_' + site_info.site_id);
            console.log(container, graph_data, graph_options);
            var graph = new vis.Network(container, graph_data, graph_options);
        });
    }else if(mode == 'container'){
        var graph = new vis.Network(el, graph_data, graph_options);
    }
}

function getNodes(site_info) {
    var nodes = [
        {
            id : 'root',
            label: "Start URL \n" + site_info.start_url,
            color: 'red',
            font: {color: "#fff", align: "left"},
            shape: "box",
            widthConstraint: {
                maximum: 200
            }
        }
    ];
    for(var i = 0; i < site_info.selectors.length; i++){
        var step = site_info.selectors[i];
        var node = {
            id : step.id,
            font: {align:"left"},
            label : step.title ? step.title : step.id,
            shape: "box",
            widthConstraint: {
                maximum: 200
            }
        };
        if(step.type == 'data' || step.type == 'get_link')
            node.color = 'lime';
        nodes.push(node);
    }
    return nodes;
}
function getEdges(site_info) {
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