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
/******/ 	return __webpack_require__(__webpack_require__.s = 38);
/******/ })
/************************************************************************/
/******/ ({

/***/ 38:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(39);


/***/ }),

/***/ 39:
/***/ (function(module, exports) {


$('.action_public').click(function () {
    var btn_text = $('#btn_public').text();
    var btn_value = $('#btn_public').attr('value');
    $('#btn_public').text($(this).text());
    $('#btn_public').attr('value', $(this).attr('value'));
    $(this).text(btn_text);
    $(this).attr('value', btn_value);
});
$('.action_post').click(function () {
    var btn_text = $('#btn_approve').text();
    var btn_value = $('#btn_approve').attr('value');
    $('#btn_approve').text($(this).text());
    $('#btn_approve').attr('value', $(this).attr('value'));
    $(this).text(btn_text);
    $(this).attr('value', btn_value);
    if ($('#btn_approve').attr('value') == -1) {
        $('#save_action').prop('disabled', true);
    } else {
        $('#save_action').prop('disabled', false);
    }
});
if ($('#btn_approve').attr('value') == -1) {
    $('#save_action').prop('disabled', true);
} else {
    $('#save_action').prop('disabled', false);
}

// {{--Gửi request ajax khi ấn nút lưu--}}

$('#save_action').click(function () {
    var is_public = $('#btn_public').attr("value");
    var is_approve = $('#btn_approve').attr("value");
    var id_post = $('#btn-route').attr("value");
    var route = $('#btn-route').attr("name");

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: "POST",
        url: route,
        data: { is_public: is_public, is_approve: is_approve, id_post: id_post },
        dataType: 'json',

        success: function success(data) {
            location.reload();
        }
    });
});

$('#save_all_action').click(function () {
    var is_public = 1;
    var is_approve = 1;
    var id_post = $('#btn-route').attr("value");
    var route = $('#btn-route').attr("name");

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        method: "POST",
        url: route,
        data: { is_public: is_public, is_approve: is_approve, id_post: id_post },
        dataType: 'json',

        success: function success(data) {
            location.reload();
        }
    });
});

/***/ })

/******/ });