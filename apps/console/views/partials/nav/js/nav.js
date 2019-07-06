'use strict';

class nav {
    constructor() {
        /* UI KIT STUFF */
        this.UIKitNavApplied = false;
        this.dragObject = {
            target : '',
            x      : 0,
            y      : 0
        };

        /* SOME SELECTORS */
        this.body = $('body');

        /* FOR NAVIGATION */
        this.nav = {
            currentTab : undefined,
            serverRequests : {
                lastRequest : undefined,
                lastRequestErrors : undefined
            }
        };

        /* FOR RESIZE AND DRAG FUNCTIONS */
        this.resizeDefaultsOptions = {
            // resize from all edges and corners
            edges: {
                left: false,
                right: true,
                bottom: false,
                top: false
            },
            // keep the edges inside the parent
            restrictEdges: {
                outer: 'parent',
                endOnly: true,
            },
            // minimum size
            restrictSize: {
                min: {
                    width: 70,
                    height: 70
                },
            },
            inertia: true
        };
        this.resizeAndMove = function(event) {
            core.console.nav.dragObject.target = event.target;
            core.console.nav.dragObject.x = (parseFloat(core.console.nav.dragObject.target.getAttribute('data-x')) || 0);
            core.console.nav.dragObject.y = (parseFloat(core.console.nav.dragObject.target.getAttribute('data-y')) || 0);
            // update the element's style
            core.console.nav.dragObject.target.style.width  = event.rect.width + 'px';
            core.console.nav.dragObject.target.style.height = event.rect.height + 'px';
            // translate when resizing from top or left edges
            core.console.nav.dragObject.x += event.deltaRect.left;
            core.console.nav.dragObject.y += event.deltaRect.top;
            core.console.nav.dragObject.target.style.webkitTransform = core.console.nav.dragObject.target.transform = `translate(${core.console.nav.dragObject.x}px, ${core.console.nav.dragObject.y}px)`;
            core.console.nav.dragObject.target.setAttribute('data-x',core.console.nav.dragObject.x);
            core.console.nav.dragObject.target.setAttribute('data-y',core.console.nav.dragObject.y);
            // core.console.nav.dragObject.target.textContent = Math.round(event.rect.width) + '\u00D7' + Math.round(event.rect.height);
        };


    }

    applyDragAndResizeable() {
        /* Options that will overwrite the resizeDefaults. */
        var resizeOptions = {
            edges: {
                right: true
            }
        };

        /* Merge object recursively without modifying the defaults */
        var resizeSettings = $.extend(true,{},this.resizeDefaultsOptions,resizeOptions);

        /* Apply resizable behaviour */
        interact('.resize.right-handle')
        .resizable(resizeSettings)
        .on('resizemove',self.resizeAndMove);

        /* Apply draggable behaviour */
        interact('#resize-drag-elem')
        .draggable({
            onmove: core.console.nav.dragMoveListener,
            restrict: {
                restriction: 'parent',
                elementRect: { top: 0, left: 0, bottom: 1, right: 1 }
            },
        });
        window.dragMoveListener = core.console.nav.dragMoveListener;
    }

    dragMoveListener(event) {
        core.console.nav.dragObject.target = event.target;

        /* keep the dragged position in the data-x/data-y attributes */
        core.console.nav.dragObject.x = (parseFloat(core.console.nav.dragObject.target.getAttribute('data-x')) || 0) + event.dx;
        core.console.nav.dragObject.y = (parseFloat(core.console.nav.dragObject.target.getAttribute('data-y')) || 0) + event.dy;

        /* translate the element */
        core.console.nav.dragObject.target.style.webkitTransform = core.console.nav.dragObject.target.style.transform = `translate(${core.console.nav.dragObject.x}px, ${core.console.nav.dragObject.y}px)`;

        /* update the position attributes */
        core.console.nav.dragObject.target.setAttribute('data-x', core.console.nav.dragObject.x);
        core.console.nav.dragObject.target.setAttribute('data-y', core.console.nav.dragObject.y);
    }

    applyIconCommands() {
        /* Show & Hide Inspection Panel */
        const inspectPanel = $('#resize-drag-elem');
        $('#inspection-panel-icon').click(function() {
            if (inspectPanel.hasClass('open')) {
                log('Closing Inspection Panel');
                inspectPanel.removeClass('open');
            } else {
                log('Opening Inspection Panel');
                $('#dev-info-block').height()
                inspectPanel.addClass('open');
            }
        });
    }

    applyNavEvents() {
        const self = this;
        if (!this.UIKitNavApplied) {
            UIkit.nav(self.body.find('.uk-nav'));
            $('.nice-scroll').niceScroll();
            self.UIKitNavApplied = true;
        }
    }

    applyWindowEvents() {
        $(window).resize(function() {
            // let devLeftNavHeight = $('#main-left-nav').height();
            // $('.main-container,#main-sub-left-nav-container,#main-sub-left-nav').css('max-height',devLeftNavHeight).height(devLeftNavHeight);
            // $('#dev-info-block,.dev-info-inner-block').css('min-height',devLeftNavHeight-110);
            // $('.nice-scroll').niceScroll().resize();
        });
        $(window).resize();
    }

    applyContainerAndFileTabs() {
        $('#cc').find('> header > nav.bottom').on('click','.cc-ws-tab-close',function(event) {
            event.preventDefault();
            var canCloseContainer = $(this).parent().find('.cc-tab-container');
            if (canCloseContainer.hasClass('unsaved') && core.console.settings.warn_for_unsaved_content) {
                let confirm = new core.confirm();
                confirm.init('Closing Unsaved Tab','You are about to close a tab that contains unsaved data. Please  confirm','none','core.console.nav.closeContainerOrFiletab')
            }

        });
    }

    init() {
        const self = this;
        /* Icon Click Bindings */
        this.applyIconCommands();
        /* Events to trigger when window resize */
        this.applyWindowEvents();
        /* Apply navigation events */
        this.applyNavEvents();
        /* Apply draggable behaviour for inspection panel */
        this.applyDragAndResizeable();
        /* Apply window & file tabs. */
        this.applyContainerAndFileTabs();
    }
}
