(self["webpackChunk"] = self["webpackChunk"] || []).push([["/js/clipboard"],{

/***/ "./vendor/arkecosystem/foundation/resources/assets/js/clipboard.js":
/*!*************************************************************************!*\
  !*** ./vendor/arkecosystem/foundation/resources/assets/js/clipboard.js ***!
  \*************************************************************************/
/***/ (() => {

window.clipboard = function () {
  return {
    copying: false,
    notSupported: false,
    copy: function copy(value) {
      var _this = this;

      this.copying = true;
      var clipboard = window.navigator.clipboard;

      if (clipboard && window.isSecureContext) {
        clipboard.writeText(value).then(function () {
          return _this.copying = false;
        }, function () {
          _this.copying = false;
          console.error("Failed to copy contents to the clipboard.");
        });
        return;
      }

      console.warn("Using fallback due to lack of navigator support or HTTPS in this browser"); // fallback to execCommand for older browsers and non-https

      this.copyUsingExec(value);
    },
    copyUsingExec: function copyUsingExec(value) {
      var _this2 = this;

      var textArea = document.createElement("textarea");
      textArea.value = value; // Prevent keyboard from showing on mobile

      textArea.setAttribute("readonly", ""); // fontSize prevents zooming on iOS

      textArea.style.cssText = "position:absolute;top:0;left:0;z-index:-9999;opacity:0;fontSize:12pt;";
      this.$root.append(textArea);
      var isiOSDevice = navigator.userAgent.match(/ipad|iphone/i);

      if (isiOSDevice) {
        var editable = textArea.contentEditable;
        var readOnly = textArea.readOnly;
        textArea.contentEditable = "true";
        textArea.readOnly = false;
        var range = document.createRange();
        range.selectNodeContents(textArea);
        var selection = window.getSelection();

        if (selection) {
          selection.removeAllRanges();
          selection.addRange(range);
        }

        textArea.setSelectionRange(0, 999999);
        textArea.contentEditable = editable;
        textArea.readOnly = readOnly;
      } else {
        textArea.select();
        textArea.focus();
      }

      this.copying = true;
      setTimeout(function () {
        return _this2.copying = false;
      }, 1200);
      document.execCommand("copy");
      textArea.remove();
    },
    copyFromInput: function copyFromInput(identifier) {
      var element = document.querySelector(identifier);
      this.copy(element.value);
    }
  };
};

/***/ })

},
/******/ __webpack_require__ => { // webpackRuntimeModules
/******/ var __webpack_exec__ = (moduleId) => (__webpack_require__(__webpack_require__.s = moduleId))
/******/ var __webpack_exports__ = (__webpack_exec__("./vendor/arkecosystem/foundation/resources/assets/js/clipboard.js"));
/******/ }
]);