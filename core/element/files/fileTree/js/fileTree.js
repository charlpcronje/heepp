'use strict';

class FileTree {
    constructor() {
        this.treeContainer = $(".element-file-tree");
    }

    init() {
        /* Hide all sub-folders at startup */
        this.treeContainer.find("ul").hide();
        /* Expand/Collapse on click */
        $(".pft-directory a").click( function() {
            $(this).parent().toggleClass('open');
            $(this).parent().find("ul:first").slideToggle("medium");
            if($(this).parent().attr('className') === 'pft-directory' ) {
                return false;
            }
        });
    }
}
$(() => {
    const fileTree = new FileTree();
    fileTree.init();
});