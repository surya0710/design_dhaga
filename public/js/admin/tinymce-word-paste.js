/**
 * TinyMCE Word paste helpers — preserves inline formatting from Microsoft Word.
 */
(function (global) {
    'use strict';

    const HEADING_MAP = {
        MsoHeading1: 'h1',
        MsoHeading2: 'h2',
        MsoHeading3: 'h3',
        MsoHeading4: 'h4',
        MsoHeading5: 'h5',
        MsoHeading6: 'h6',
        MsoTitle: 'h1',
        MsoSubtitle: 'h2',
    };

    const MSO_PROP_SKIP = /^(mso-|tab-stops|layout-grid|layout-grid-mode|page-break|punctuation-wrap|line-break|text-autospace)$/i;

    const VALID_STYLES = {
        '*': 'color,background,background-color,font,font-size,font-family,font-weight,font-style,text-decoration,text-decoration-line,text-decoration-color,text-decoration-style,text-align,line-height,margin,margin-top,margin-bottom,margin-left,margin-right,padding,padding-top,padding-bottom,padding-left,padding-right,border,border-top,border-bottom,border-left,border-right,border-color,border-style,border-width,border-collapse,width,height,min-width,max-width,vertical-align,list-style,list-style-type,text-indent,white-space,display,float,clear,letter-spacing,word-spacing,text-transform,opacity,position,top,left,right,bottom',
    };

    function cleanMsoStyle(style) {
        return (style || '')
            .split(';')
            .map(function (rule) { return rule.trim(); })
            .filter(function (rule) {
                if (!rule) return false;
                var colon = rule.indexOf(':');
                if (colon === -1) return false;
                var prop = rule.slice(0, colon).trim().toLowerCase();
                var val = rule.slice(colon + 1).trim().toLowerCase();
                if (MSO_PROP_SKIP.test(prop)) return false;
                if (prop.indexOf('mso-') !== -1 || val.indexOf('mso-') !== -1) return false;
                return true;
            })
            .join('; ');
    }

    function mergeStyles(existing, addition) {
        var merged = cleanMsoStyle(existing);
        var extra = cleanMsoStyle(addition);
        if (merged && extra) return merged + '; ' + extra;
        return merged || extra || '';
    }

    function applyEmbeddedWordStyles(html) {
        var styleBlocks = html.match(/<style[^>]*>[\s\S]*?<\/style>/gi);
        if (!styleBlocks || !styleBlocks.length) return html;

        var classRules = {};

        styleBlocks.forEach(function (block) {
            var css = block.replace(/<\/?style[^>]*>/gi, '');
            css.replace(/([^{@]+)\{([^}]+)\}/g, function (_, selector, declarations) {
                var decl = cleanMsoStyle(declarations);
                if (!decl) return '';

                selector.split(',').forEach(function (sel) {
                    var match = sel.trim().match(/\.([A-Za-z0-9_-]+)/);
                    if (!match) return;
                    var cls = match[1];
                    classRules[cls] = classRules[cls]
                        ? mergeStyles(classRules[cls], decl)
                        : decl;
                });

                return '';
            });
        });

        if (!Object.keys(classRules).length) return html;

        var doc = new DOMParser().parseFromString('<div id="word-root">' + html + '</div>', 'text/html');
        var root = doc.getElementById('word-root');
        if (!root) return html;

        root.querySelectorAll('[class]').forEach(function (el) {
            var styles = cleanMsoStyle(el.getAttribute('style') || '');
            (el.getAttribute('class') || '').split(/\s+/).forEach(function (cls) {
                if (classRules[cls]) {
                    styles = mergeStyles(styles, classRules[cls]);
                }
            });
            if (styles) {
                el.setAttribute('style', styles);
            }
        });

        return root.innerHTML;
    }

    function stripWordHtml(html) {
        html = applyEmbeddedWordStyles(html);

        return html
            .replace(/<!--\[if[\s\S]*?\[endif\]-->/gi, '')
            .replace(/<\?xml[\s\S]*?\?>/gi, '')
            .replace(/<xml>[\s\S]*?<\/xml>/gi, '')
            .replace(/<style[^>]*>[\s\S]*?<\/style>/gi, '')
            .replace(/<link[^>]*rel=["']?File-List["']?[^>]*>/gi, '')
            .replace(/<meta[^>]*>/gi, '')
            .replace(/<o:p[^>]*>\s*<\/o:p>/gi, '')
            .replace(/<o:p[^>]*>([\s\S]*?)<\/o:p>/gi, '$1')
            .replace(/<\/?o:[^>]+>/gi, '')
            .replace(/<\/?w:[^>]+>/gi, '')
            .replace(/<\/?m:[^>]+>/gi, '')
            .replace(/<\/?v:[^>]+>/gi, '')
            .replace(/class=["']MsoNormal["']/gi, '')
            .replace(/\sclass=["']\s*["']/gi, '');
    }

    function removeMsoListMarkers(node) {
        node.querySelectorAll('span[style*="mso-list"], span[style*="Mso-list"]').forEach(function (el) {
            el.remove();
        });
    }

    function promoteHeadings(node) {
        node.querySelectorAll('p[class]').forEach(function (el) {
            var cls = el.className || '';
            Object.keys(HEADING_MAP).some(function (msoCls) {
                if (cls.indexOf(msoCls) === -1) return false;
                var heading = document.createElement(HEADING_MAP[msoCls]);
                heading.innerHTML = el.innerHTML;
                var style = cleanMsoStyle(el.getAttribute('style'));
                if (style) heading.setAttribute('style', style);
                el.replaceWith(heading);
                return true;
            });
        });
    }

    function isOrderedListItem(text) {
        return /^\s*\d+[\.\)]\s/.test(text);
    }

    function stripListPrefix(html) {
        return html
            .replace(/^(\s*<[^>]+>)*\s*[·•◦▪▸○●\-–—]\s*(<\/[^>]+>)*/u, '')
            .replace(/^\s*\d+[\.\)]\s+/, '')
            .replace(/^\s*[·•◦▪▸○●]\s*/u, '')
            .trim();
    }

    function convertWordLists(node) {
        var listParas = Array.from(node.querySelectorAll(
            'p.MsoListParagraph, p[class*="MsoList"], p[style*="mso-list"], p[style*="Mso-list"]'
        ));

        if (!listParas.length) return;

        var currentList = null;
        var prevEl = null;

        listParas.forEach(function (el) {
            var rawText = el.innerText || el.textContent || '';
            var isOrdered = isOrderedListItem(rawText);
            var html = stripListPrefix(el.innerHTML);
            var li = document.createElement('li');
            li.innerHTML = html;
            var listTag = isOrdered ? 'ol' : 'ul';

            if (!currentList || currentList.tagName.toLowerCase() !== listTag ||
                (prevEl && el.previousElementSibling !== prevEl)) {
                currentList = document.createElement(listTag);
                el.parentNode.insertBefore(currentList, el);
            }

            currentList.appendChild(li);
            prevEl = el;
            el.remove();
        });
    }

    function convertLegacyFontTags(node) {
        node.querySelectorAll('font').forEach(function (el) {
            var span = document.createElement('span');
            var styles = [];

            if (el.getAttribute('color')) {
                styles.push('color:' + el.getAttribute('color'));
            }
            if (el.getAttribute('face')) {
                styles.push('font-family:' + el.getAttribute('face'));
            }
            if (el.getAttribute('size')) {
                var sizeMap = { 1: '8pt', 2: '10pt', 3: '12pt', 4: '14pt', 5: '18pt', 6: '24pt', 7: '36pt' };
                var size = sizeMap[el.getAttribute('size')] || el.getAttribute('size') + 'pt';
                styles.push('font-size:' + size);
            }

            var existing = cleanMsoStyle(el.getAttribute('style'));
            if (existing) styles.push(existing);

            if (styles.length) span.setAttribute('style', styles.join('; '));
            span.innerHTML = el.innerHTML;
            el.replaceWith(span);
        });
    }

    function cleanElementStyles(node) {
        node.querySelectorAll('[style]').forEach(function (el) {
            var cleaned = cleanMsoStyle(el.getAttribute('style'));
            if (cleaned) {
                el.setAttribute('style', cleaned);
            } else {
                el.removeAttribute('style');
            }
        });
    }

    function removeMsoClasses(node) {
        node.querySelectorAll('[class]').forEach(function (el) {
            var cls = (el.getAttribute('class') || '')
                .split(/\s+/)
                .filter(function (c) { return c && c.indexOf('Mso') !== 0; })
                .join(' ');
            if (cls) {
                el.setAttribute('class', cls);
            } else {
                el.removeAttribute('class');
            }
        });
    }

    function unwrapEmptySpans(node) {
        node.querySelectorAll('span').forEach(function (el) {
            var style = el.getAttribute('style');
            var cls = el.getAttribute('class');
            var hasStyle = style && style.trim();
            var hasClass = cls && cls.trim();
            if (!hasStyle && !hasClass && el.attributes.length <= 1) {
                el.replaceWith.apply(el, el.childNodes);
            }
        });
    }

    function normalizeTables(node) {
        node.querySelectorAll('table').forEach(function (table) {
            if (!table.getAttribute('style')) {
                table.setAttribute('style', 'border-collapse:collapse;width:100%');
            }
        });
    }

    function postprocessWordPaste(node) {
        removeMsoListMarkers(node);
        promoteHeadings(node);
        convertWordLists(node);
        convertLegacyFontTags(node);
        cleanElementStyles(node);
        removeMsoClasses(node);
        unwrapEmptySpans(node);
        normalizeTables(node);
    }

    global.TinyMceWordPaste = {
        pasteOptions: {
            paste_as_text: false,
            paste_merge_formats: true,
            paste_remove_styles_if_webkit: false,
            paste_webkit_styles: 'all',
            paste_data_images: true,
        },

        htmlOptions: {
            extended_valid_elements: '*[*]',
            valid_children: '+body[style],+div[p|div|img|h1|h2|h3|h4|h5|h6|ul|ol|li|blockquote|table|tr|td|th|thead|tbody|span|strong|em|u|s|sub|sup|a|br|hr|font]',
            verify_html: false,
            valid_styles: VALID_STYLES,
            convert_fonts_to_spans: true,
        },

        paste_preprocess: function (plugin, args) {
            args.content = stripWordHtml(args.content);
        },

        paste_postprocess: function (plugin, args) {
            postprocessWordPaste(args.node);
        },

        contentStyle: [
            'body {',
            '  font-family: Calibri, "Segoe UI", Arial, sans-serif;',
            '  font-size: 11pt;',
            '  line-height: 1.15;',
            '  color: #000000;',
            '  padding: 16px;',
            '  margin: 0;',
            '}',
            'p { margin-top: 0; margin-bottom: 8pt; }',
            'table { border-collapse: collapse; }',
            'td, th { vertical-align: top; }',
            'img { max-width: 100%; height: auto; }',
            'img[alt="column-image"] { outline: 2px dashed #2275fc; cursor: pointer; }',
            'ul, ol { padding-left: 1.5em; margin-bottom: 8pt; }',
            'li { margin-bottom: 2pt; }',
        ].join('\n'),
    };
})(window);
