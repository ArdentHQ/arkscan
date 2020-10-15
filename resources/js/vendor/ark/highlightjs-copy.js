export function highlightJsBadge(opt) {
    var options = {
        // the selector for the badge template
        templateSelector: "#CodeBadgeTemplate",

        // base content selector that is searched for snippets
        contentSelector: "body",

        // Delay in ms used for `setTimeout` before badging is applied
        // Use if you need to time highlighting and badge application
        // since the badges need to be applied afterwards.
        // 0 - direct execution (ie. you handle timing
        loadDelay: 0,

        // function called before code is placed on clipboard
        // Passed in text and returns back text function(text, codeElement) { return text; }
        onBeforeCodeCopied: null,
    };

    function initialize(opt) {
        Object.assign(options, opt);

        if (document.readyState == "loading") {
            document.addEventListener("DOMContentLoaded", load);
        } else {
            load();
        }
    }

    function load() {
        if (options.loadDelay) {
            setTimeout(addCodeBadge, loadDelay);
        } else {
            addCodeBadge();
        }
    }

    function addCodeBadge() {
        // first make sure the template exists - if not we embed it
        if (!document.querySelector(options.templateSelector)) {
            var node = document.createElement("div");
            node.innerHTML = getTemplate();
            var template = node.querySelector(options.templateSelector);
            document.body.appendChild(template);
        }

        var hudText = document.querySelector(options.templateSelector)
            .innerHTML;

        var $codes = document.querySelectorAll("pre>code.hljs-copy");
        for (var index = 0; index < $codes.length; index++) {
            var el = $codes[index];
            if (el.parentElement.parentElement.querySelector(".code-badge")) {
                continue;
            }

            var html = hudText
                .replace("{{copyIconClass}}", options.copyIconClass)
                .trim();

            // insert the Hud panel
            var $newHud = document.createElement("div");
            $newHud.innerHTML = html;
            $newHud = $newHud.querySelector(".code-badge");

            // make <pre> tag position:relative so positioning keeps pinned right
            // even with scroll bar scrolled
            var pre = el.parentElement.parentElement;
            pre.classList.add("code-badge-pre");

            if (options.copyIconContent)
                $newHud.querySelector(".code-badge-copy-icon").innerText =
                    options.copyIconContent;

            pre.insertBefore($newHud, el.parentElement);
        }

        var $content = document.querySelector(options.contentSelector);

        // single copy click handler
        $content.addEventListener("click", function (e) {
            var $clicked = e.srcElement;
            if ($clicked.classList.contains("code-badge-copy-icon")) {
                e.preventDefault();
                e.cancelBubble = true;
                copyCodeToClipboard(e);
            }
            return false;
        });
    }

    function copyCodeToClipboard(e) {
        // walk back up to <pre> tag
        var $origCode = e.srcElement.parentElement.parentElement.parentElement;

        // select the <code> tag and grab text
        var $code = $origCode.querySelector(".hljs-copy");
        var text = $code.getAttribute("source");

        if (options.onBeforeCodeCopied) {
            text = options.onBeforeCodeCopied(text, $code);
        }

        // Create a textblock and assign the text and add to document
        var textarea = document.createElement("textarea");
        textarea.value = text.trim();
        document.body.appendChild(textarea);
        textarea.style.display = "hidden";
        textarea.style.position = "absolute";
        textarea.style.left = "-10000px";

        // select the entire textblock
        if (window.document.documentMode) {
            textarea.setSelectionRange(0, textarea.value.length);
        } else {
            textarea.select();
        }

        // copy to clipboard
        document.execCommand("copy");

        // clean up element
        document.body.removeChild(textarea);

        // show the check icon (copied) briefly
        swapIcons($origCode);
    }

    function swapIcons($code) {
        const copyIcon = $code.querySelector(".code-badge-copy-icon");
        const checkmarkIcon = $code.querySelector(".code-badge-checkmark-icon");

        copyIcon.style.display = "none";
        checkmarkIcon.style.display = "block";

        setTimeout(function () {
            copyIcon.style.display = "block";
            checkmarkIcon.style.display = "none";
        }, 2000);
    }

    function getTemplate() {
        var stringArray = [
            '<div id="CodeBadgeTemplate" style="display:none">',
            '    <div class="code-badge">',
            '        <div title="Copy to clipboard">',
            '            <svg class="code-badge-copy-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18 21"><path d="M10.3.1H3.2c-.8 0-1.5.3-2 .8C.6 1.5.3 2.2.3 3v8.6h2.9V3h7.1V.1zm4.8 5.7H8.9c-.8 0-1.5.3-2 .8-.6.6-.9 1.3-.9 2.1v8.6c0 .8.3 1.5.8 2s1.3.8 2 .8H15c.8 0 1.5-.3 2-.8s.8-1.3.8-2V8.7c0-.8-.3-1.5-.8-2-.4-.6-1.2-.9-1.9-.9zm0 11.4H8.9V8.7h6.2v8.5z"/></svg>',
            '            <svg class="code-badge-checkmark-icon" style="display: none;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 17 14"><path d="M5.125 13.822c.3.3.8.3 1.1 0l10.6-10.4c.3-.3.3-.8 0-1.1l-2.2-2.1c-.3-.3-.8-.3-1.1 0l-7.8 7.7-2.2-2.1c-.3-.3-.8-.3-1.1 0l-2.2 2.1c-.3.3-.3.8 0 1.1l4.9 4.8z"/></svg>',
            "        </div>",
            "     </div>",
            "</div>",
        ];

        var t = "";
        for (var i = 0; i < stringArray.length; i++) {
            t += stringArray[i] + "\n";
        }

        return t;
    }

    initialize(opt);
}
