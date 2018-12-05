function open_un_verify_box(id){
    // var data = {
    //     reasons : [
    //         {
    //             'id' : 1,
    //             'content' : "Chứa danh sách bảng biểu, biểu mẫu, báo cáo tài chính, báo cáo thường liên, lịch học, thông báo của trường học,..."
    //         },
    //         {
    //             'id' : 2,
    //             'content' : "Không đảm bảo đủ số link/site như đã quy định"
    //         },
    //         {
    //             'id' : 3,
    //             'content' : "Tài liệu không là 1 file thống nhất (bị cắt, ghép, trang đầu cuối)"
    //         }
    //     ],
    //     recent_usage : [
    //         {
    //             'id' : 1,
    //             'content' : "Chứa danh sách bảng biểu, biểu mẫu, báo cáo tài chính, báo cáo thường liên, lịch học, thông báo của trường học,..."
    //         },
    //         {
    //             'id' : 2,
    //             'content' : "Không đảm bảo đủ số link/site như đã quy định"
    //         }
    //     ]
    // };

    var dialog = bootbox.dialog({
        title: 'Chọn 1 lý do',
        message: '<div>' +
        'Tìm <input name="search_reason" placeholder="Tìm hoặc thêm mới"/> ' +
            ' <label><input type="checkbox" id="show_selected" name="show_selected" value="show_selected"/> selected </label>' +
        '<ul id="reason_list" class="list-unstyled"></ul>' +
        '<button id="confirm_un_verify_button" class="btn btn-primary">UnVerify</button> ' +
        '</div>'
    });

    var selected = [], all_reasons = [], recent_reasons = [];

    dialog.init(function(){/** khởi tạo box */
        dialog.find('input[name=search_reason]').focus();
        var input_text = dialog.find('input[name=search_reason]').val();

        dialog.on('change', '#reason_list input.unverify_reason', function (e) {
            if (this.checked){/** Thêm 1 lý do */
                selected.push(parseInt(this.value));
            }else{/** Bỏ chọn 1 lý do */
                var this_value = parseInt(this.value);
                var index = selected.indexOf(this_value);
                if (index > -1) {
                    selected.splice(index, 1);
                }
            }
        });

        dialog.find('#show_selected').change(function (e) {/** Hiển thị các lý do đã chọn */
            if (this.checked){// chọn
                var selected_reasons = [];
                $.each(all_reasons, function (j,i) {
                   if (selected.indexOf(i.id) > -1){
                       selected_reasons.push(i);
                   }
                });
                console.log(selected, selected_reasons);
                dialog.find('#reason_list').html(make_list(selected_reasons, selected));
            }else{
                dialog.find('#reason_list').html(make_list(recent_reasons, selected));
            }
        });

        axios.get(link_get_reasons, {
            id : id
        }).then(function (response) {
            all_reasons = response.data.reasons;
            recent_reasons = response.data.recent_usage;

            dialog.find('#reason_list').html(make_list(response.data.recent_usage, selected));
            dialog.find('input[name=search_reason]').keyup(function (e) {
                var reasons = search(this.value, response.data.reasons);
                if (this.value == ""){// nếu trống trả về danh sách mới sử dụng
                    dialog.find('#reason_list').html(make_list(response.data.recent_usage, selected));
                    return;
                }
                // nếu không có kết quả tìm kiếm trả về lự chọn thêm mới
                reasons.push({
                    id : 0,
                    content : 'Thêm mới : ' + this.value
                });
                dialog.find('#reason_list').html(make_list(reasons, selected));
            });
            dialog.find('#confirm_un_verify_button').click(function (e) {
                un_verify(id, input_text, selected, function (e) {
                    dialog.find('.bootbox-close-button').trigger('click');
                })
            })
        });
    });
}

function un_verify(id, text, reason_id, cb) {
    console.log(id, text, reason_id, cb);
    axios.post(link_update_reasons, {
        id: id,
        text: text,
        reason_id: reason_id,
        verified: 'UnVerify',
    }).then(function (response) {
        if (response.data.success){
            window.location.reload();
        }else{
            alert(response.data.message);
        }
    });
}

function make_list(reasons, selected) {
    var html = "";
    for(var i in reasons){
        var checked = selected.indexOf(reasons[i].id) > -1 ? "checked" : "";
        html += "<li><label><input type='checkbox' " + checked + " class='unverify_reason' name='unverify_reason[]' value='" + reasons[i].id + "'/> " + reasons[i].content + "</label></li>";
    }
    return html;
}

function search(text, reasons) {
    var options = {
        shouldSort: true,
        tokenize: true,
        threshold: 0.9,
        location: 0,
        distance: 100,
        maxPatternLength: 32,
        minMatchCharLength: 1,
        keys: [
            "id",
            "content"
        ]
    };
    var fuse = new Fuse(reasons, options); // "list" is the item array
    return fuse.search(text);
}

