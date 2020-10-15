import Prism from "prismjs";

Prism.hooks.add("after-highlight", function (env) {
    var pre = env.element.parentNode;

    if (
        !pre ||
        !/pre/i.test(pre.nodeName) ||
        pre.className.indexOf("line-numbers") === -1
    ) {
        return;
    }

    var linesNum = 1 + env.code.trim().split("\n").length;

    if (linesNum === 2) {
        return;
    }

    var lineNumbersWrapper;

    let lines = new Array(linesNum).join("<span></span>");
    lineNumbersWrapper = document.createElement("span");
    lineNumbersWrapper.className = "line-numbers-rows";
    lineNumbersWrapper.innerHTML = lines;

    env.element.appendChild(lineNumbersWrapper);
});

Prism.hooks.add("after-highlight", function (env) {
    env.element.setAttribute("source", env.code.trim());
});
