import Prism from "prismjs";
import "prismjs/components/prism-markup";
import "prismjs/components/prism-markup-templating";
import "prismjs/components/prism-bash";
import "prismjs/components/prism-clike";
import "prismjs/components/prism-csharp";
import "prismjs/components/prism-elixir";
import "prismjs/components/prism-go";
import "prismjs/components/prism-ini";
import "prismjs/components/prism-java";
import "prismjs/components/prism-javascript";
import "prismjs/components/prism-json";
import "prismjs/components/prism-php";
import "prismjs/components/prism-python";
import "prismjs/components/prism-ruby";
import "prismjs/components/prism-rust";
import "prismjs/components/prism-sql";
import "prismjs/components/prism-swift";
import "prismjs/components/prism-typescript";
import "prismjs/plugins/line-highlight/prism-line-highlight.js";
// import 'prismjs/plugins/line-highlight/prism-line-highlight.css'

import "./prism-line-numbers";
import { highlightJsBadge } from "./highlightjs-copy";

Prism.hooks.add("before-highlight", function (env) {
    env.code = env.element.innerText;
});

function beautifyCode(pre, options) {
    // Clone <pre> with new classes
    const preNew = document.createElement("pre");
    preNew.innerHTML = pre.innerHTML;
    preNew.classList = pre.classList;
    preNew.classList.add("hljs");

    if (options && !options.omitLineNumbers) {
        const isSingleLine = preNew.innerText.trim().split("\n").length === 1;

        if (!isSingleLine) {
            preNew.classList.add("line-numbers");
        }
    }

    const code = preNew.querySelector("code");

    code.classList.add("hljs-copy");
    if (code.classList.value.indexOf("language-") === -1) {
        code.classList.add("language-plain");
    }

    // Wrap <pre> with fancy styling
    const replacement = document.createElement("div");
    replacement.innerHTML = preNew.outerHTML;
    replacement.setAttribute("class", "custom-code-block");

    pre.replaceWith(replacement);
}

window.useHighlight = (pre, beautifyOptions) => {
    const options = { omitLineNumbers: true, ...beautifyOptions };

    if (pre) {
        beautifyCode(pre, options);
    }

    Prism.highlightAll();

    highlightJsBadge();
};

document.addEventListener("DOMContentLoaded", () => useHighlight());
