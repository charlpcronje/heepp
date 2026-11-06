'use strict';

ui.workspace = class {
    constructor() {
        this.resizeMove = function (event) {
            var target = event.target,
                x = (parseFloat(target.getAttribute('data-x')) || 0),
                y = (parseFloat(target.getAttribute('data-y')) || 0);

            // update the element's style
            target.style.width  = event.rect.width + 'px';
            target.style.height = event.rect.height + 'px';

            // translate when resizing from top or left edges
            x += event.deltaRect.left;
            y += event.deltaRect.top;

            target.style.webkitTransform = target.style.transform =
                'translate(' + x + 'px,' + y + 'px)';

            target.setAttribute('data-x', x);
            target.setAttribute('data-y', y);
            // target.textContent = Math.round(event.rect.width) + '\u00D7' + Math.round(event.rect.height);
        };

        this.resizeDefaults = {
            // resize from all edges and corners
            edges: { left: true },
            // keep the edges inside the parent
            restrictEdges: {
                outer: 'parent',
                endOnly: true,
            },
            // minimum size
            restrictSize: {
                min: { width: 100, height: 50 },
            },
            inertia: true,
        };
    }

    makeDraggable() {
        interact('.drag')
        .draggable({
            onmove: window.dragMoveListener,
            restrict: {
                restriction: 'parent',
                elementRect: { top: 0, left: 0, bottom: 1, right: 1 }
            },
        })
        .on('resizemove', function (event) {
            var target = event.target,
                x = (parseFloat(target.getAttribute('data-x')) || 0),
                y = (parseFloat(target.getAttribute('data-y')) || 0);

            // update the element's style
            target.style.width  = event.rect.width + 'px';
            target.style.height = event.rect.height + 'px';

            // translate when resizing from top or left edges
            x += event.deltaRect.left;
            y += event.deltaRect.top;

            target.style.webkitTransform = target.style.transform =
                'translate(' + x + 'px,' + y + 'px)';

            target.setAttribute('data-x', x);
            target.setAttribute('data-y', y);
            target.textContent = Math.round(event.rect.width) + '\u00D7' + Math.round(event.rect.height);
        });
    }

    makeResizeable() {
        interact('.ws-right')
        .resizable($.extend({},this.resizeDefaults,{edges: { left: true, right: false}}))
        .on('resizemove',this.resizeMove);

        interact('.ws-left')
        .resizable($.extend({},this.resizeDefaults,{edges:{right: true, left: false}}))
        .on('resizemove',this.resizeMove);
    }

    init() {
        // this.makeDraggable();
        //  this.makeResizeable();

    }
};
