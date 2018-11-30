/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 40);
/******/ })
/************************************************************************/
/******/ ({

/***/ 40:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(41);


/***/ }),

/***/ 41:
/***/ (function(module, exports) {

$('.category-move').click(function (e) {
    e.preventDefault();
    move_link = $(this).attr('data-link');
    $.get(move_link, '', function (data) {
        if (data.movable) {
            bootbox.dialog({
                title: data.move_title,
                message: data.message,
                buttons: {
                    cancel: {
                        label: 'Hủy',
                        callback: {}
                    },
                    yes: {
                        label: 'Đồng ý',
                        callback: function callback() {
                            sendRequest(data.move_link);
                        }
                    }
                }
            }).show();
        } else {
            bootbox.alert({
                title: data.move_title,
                message: data.message
            });
        }
    }, 'json');
    return false;
});
$('.category-delete').click(function (e) {
    e.preventDefault();
    del_link = $(this).attr('data-link');
    bootbox.dialog({
        title: 'Xóa danh mục',
        message: 'Bạn có muốn xóa danh mục',
        buttons: {
            cancel: {
                label: 'Hủy',
                callback: {}
            },
            yes: {
                label: 'Đồng ý',
                callback: function callback() {
                    sendRequest(del_link);
                }
            }
        }
    }).show();
    return false;
});
function sendRequest(move_link) {
    $.ajax({
        url: move_link,
        method: "GET",
        success: function success(data) {
            bootbox.alert({
                title: data.title,
                message: data.message,
                callback: function callback() {
                    location.reload();
                }

            });
        }
    });
}

// phan loc theo lớp , môn
$(document).ready(function () {

    $('.select2').select2();
    var cate_root = $('#category_root').val();
    if (cate_root != '') {
        changeSelectCategory();
    }
    $('#category_root').on('change', function () {
        changeSelectCategory();
    });
    function changeSelectCategory() {
        var id_cate = $('#category_root').val();
        var url = $('#category_root').attr('data-link');
        $.ajax({
            method: 'POST',
            data: { id_cate: id_cate },
            url: url,
            success: function success(data) {
                var id_cate_one = $('#category_one').val();
                $('#category_one').find('option').remove().end().append('<option value= "" selected>Chọn môn</option>');
                for (var i = 0; i < data.length; i++) {

                    $('#category_one').append($('<option>', {
                        value: data[i].id,
                        text: data[i].name
                    }));
                    if (data[i].id == id_cate_one) {
                        $('#category_one').val(id_cate_one).trigger('change.select2');
                    }
                }
            }

        });
    }
});

/***/ })

/******/ });