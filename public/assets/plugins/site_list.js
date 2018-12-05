





/** ASSIGN BUTTONS **/
$(document).ready(function(){
    $("#sites_list").on('click', '.task_crawl_controls i.fa-play', function(e){
        var id = $(this).parents('tr').attr('id').replace('row_site_', '');
        console.log(id);
        return control_task(id, 'crawl');
    }).on('click', '.task_crawl_controls i.fa-stop', function(e){
        var id = $(this).parents('tr').attr('id').replace('row_site_', '');
        return control_task(id, '-crawl');
    }).on('click', '.task_crawl_controls i.fa-refresh', function(e){
        var id = $(this).parents('tr').attr('id').replace('row_site_', '');
        return refresh_site(id);
    }).on('click', '.task_download_link_controls i.fa-play', function(e){
        var id = $(this).parents('tr').attr('id').replace('row_site_', '');
        return control_task(id, 'download_link');
    }).on('click', '.task_download_link_controls i.fa-stop', function(e){
        var id = $(this).parents('tr').attr('id').replace('row_site_', '');
        return control_task(id, '-download_link');
    }).on('click', '.site_id', function(){
        show_graph($(this).data('id'));
    }).on('click', '.btn_delete', function(){
        var id = $(this).parents('tr').attr('id').replace('row_site_', '');
        return delete_site(id);
    }).on('click', '.btn_restore', function(){
        var id = $(this).parents('tr').attr('id').replace('row_site_', '');
        return restore_site(id);
    });
    setInterval(refresh_check, 1000);
});

var refresh_count_down = 0;

function refresh_check() {console.log(refresh_count_down);
    if($('#auto_refresh_enabled').prop('checked') == false){return;}
    refresh_count_down++;
    var _time = $('#auto_refresh_time').val();
    if(refresh_count_down > _time){
        refresh_count_down = 0;
        refresh_site_info();
    }
}

/** FUNCTIONS **/

function control_task(id, task_name) {
    var action = task_name.match(/^\-/) ? "disable" : "enable";
    var y = confirm("Are you sure to " + action + " task " + task_name + " on this site ?");
    if(!y){
        return false;
    }
    axios.post(update_tasks_url, {
        id: id,
        task: task_name,
        quick: true
    }).then(function(response){
        if(response.data.success){
            refresh_site_info([id]);
        }else{
            alert("Error");
        }
    });
    return false;
}

function refresh_site(id) {
    var y = confirm("Are you sure to reset crawl process for this site ?");
    if(!y){
        return false;
    }

    axios.post(refresh_crawl_url, {
        id: id
    }).then(function(response){
        if(response.data.success){
            refresh_site_info([id]);
        }else{
            alert("Error");
        }
    });

    return false;
}

function delete_site(id) {
    var y = confirm("Are you sure to move this site to trash?");
    if(!y){
        return false;
    }
    axios.post(delete_site_url, {
        site: id
    }).then(function(response){
        if(response.data.success){
            $('#row_site_' + id).fadeOut(function(){this.parentNode.removeChild(this);});
        }
        alert(response.data.message);
    });
    return false;
}
function restore_site(id) {
    var y = confirm("Are you sure to restore this site?");
    if(!y){
        return false;
    }
    axios.post(restore_site_url, {
        site: id
    }).then(function(response){
        if(response.data.success){
            $('#row_site_' + id).fadeOut(function(){this.parentNode.removeChild(this);});
        }
        alert(response.data.message);
    });
    return false;
}

function refresh_site_info(ids) {
    if(!ids){
        ids = [];
        $('tr.row_site').each(function(t){
            ids.push(this.id.replace('row_site_',''));
        });
    }
    axios.post(refresh_url, {id: ids})
        .then(function(response){
            for(var i in response.data){
                update_site_info(response.data[i]);
            }
        });
}

function update_site_info(site) {
    var site_row = $('#row_site_' + site.id);
    var fields = [
        'country',
        'start_url',
        'assigned',
        'updated_at',
        'header_checked',
        'files',
        'crawled',
        'status',
        'tasks_status',
        'downloaded_files',
        'uploaded',
    ];
    for(var i = 0; i < fields.length; i++){
        // if(site[fields[i]]){
            site_row.find('.site_' + fields[i]).html(site[fields[i]]);
        // }
    }
}