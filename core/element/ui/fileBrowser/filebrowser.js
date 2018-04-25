function jsTreeInit() {
	const dataElem = $('#data');
	const fileBrowser = $('#filebrowser');

	$("#filebrowser:not('.initiated')").jstree({
		'core' : {
			'data' : {
				'url' : 'filebrowser/action/get_node/',
				'data' : function(node) {
                    return { 'id' : node.id };
				}
			},
			'check_callback' : function(o, n, p, i, m) {
				if(m && m.dnd && m.pos !== 'i') { return false; }
				if(o === "move_node" || o === "copy_node") {
					if(this.get_node(n).parent === this.get_node(p).id) { return false; }
				}
				return true;
			},
			'force_text' : true,
			'themes' : {
				'responsive' : true,
				'variant' : 'small',
				'stripes' : false,
				'name'	  : 'default'
			}
		},
		'sort' : function(a, b) {
			return this.get_type(a) === this.get_type(b) ? (this.get_text(a) > this.get_text(b) ? 1 : -1) : (this.get_type(a) >= this.get_type(b) ? 1 : -1);
		},
		'contextmenu' : {
			'items' : function(node) {
				var tmp = $.jstree.defaults.contextmenu.items();
				delete tmp.create.action;
				tmp.create.label = "New";
				tmp.create.submenu = {
					"create_folder" : {
						"separator_after"	: true,
						"label"				: "Folder",
						"action"			: function (data) {
							var inst = $.jstree.reference(data.reference),
								obj = inst.get_node(data.reference);
							inst.create_node(obj, { type : "default" }, "last", function (new_node) {
								setTimeout(function () { inst.edit(new_node); },0);
							});
						}
					},
					"create_file" : {
						"label"				: "File",
						"action"			: function (data) {
							var inst = $.jstree.reference(data.reference),
								obj = inst.get_node(data.reference);
							inst.create_node(obj, { type : "file" }, "last", function (new_node) {
								setTimeout(function () { inst.edit(new_node); },0);
							});
						}
					}
				};
				if(this.get_type(node) === "file") {
					delete tmp.create;
				}
				return tmp;
			}
		},
		'types' : {
			'default' : { 'icon' : 'fa fa-folder' },
			'file' : { 'valid_children' : [], 'icon' : 'fa-file' }
		},
		'unique' : {
			'duplicate' : function (name, counter) {
				return name + ' ' + counter;
			}
		},
		'plugins' : ['state','dnd','sort','types','contextmenu','unique']
	})
	.on('open_node.jstree', function (e, data) {
    	var nodesToKeepOpen = [];
    
        // get all parent nodes to keep open
        try {
	        $('#'+data.node.id).each(function() {
	           nodesToKeepOpen.push(this.id);
	        });
    	} catch (error) {
    		return true;
    	}
		
		// add current node to keep open
		nodesToKeepOpen.push( data.node.id );
			// close all other nodes
			$('.jstree-node').each( function() {
           	if( nodesToKeepOpen.indexOf(this.id) === -1 ) {
                fileBrowser.jstree().close_node(this.id);
            }
        })
    })
	.on('delete_node.jstree', function (e, data) {
		$.get('filebrowser/action/delete_node/'+data.node.id)
			.fail(function () {
				data.instance.refresh();
			});
	})
	.on('create_node.jstree', function (e, data) {
		$.get('filebrowser/action/create_node/'+data.node.parent+'/'+data.node.text+'/'+data.node.type)
			.done(function (d) {
				data.instance.set_id(data.node, d.id);
			})
			.fail(function () {
				data.instance.refresh();
			});
	})
	.on('rename_node.jstree', function (e, data) {
		$.get('filebrowser/action/rename_node/'+data.node.id+'/'+data.text)
			.done(function (d) {
				data.instance.set_id(data.node, d.id);
			})
			.fail(function () {
				data.instance.refresh();
			});
	})
	.on('move_node.jstree', function (e, data) {
		$.get('filebrowser/action/move_node/'+data.node.id+'/'+data.parent)
			.done(function() {
				//data.instance.load_node(data.parent);
				data.instance.refresh();
			})
			.fail(function () {
				data.instance.refresh();
			});
	})
	.on('copy_node.jstree', function (e, data) {
		$.get('filebrowser/action?action=copy_node', { 'id' : data.original.id, 'parent' : data.parent })
			.done(function() {
				//data.instance.load_node(data.parent);
				data.instance.refresh();
			})
			.fail(function () {
				data.instance.refresh();
			});
	})
	.on('changed.jstree', function (e, data) {
		if(data && data.selected && data.selected.length) {
			$.get('filebrowser/action?action=get_content&id=' + data.selected.join(':'), function (d) {
				if(d && typeof d.type !== 'undefined') {
					dataElem.find('.content').hide();
                    log(d.type);
					var editorMode = 'htmlmixed';
					switch(d.type) {
						case 'text':
							editorMode = 'htmlmixed';
                        break;    
						case 'txt':
							editorMode = 'htmlmixed';
                        break;
						case 'md':
							editorMode = 'markdown';
                        break;
						case 'htaccess':
							editorMode = 'htmlmixed';
                        break;
						case 'log':
							editorMode = 'htmlmixed';
                        break;
						case 'sql':
							editorMode = 'text/x-mysql';
                        break;
						case 'php':
							editorMode = 'application/x-httpd-php';
                        break;
						case 'js':
							editorMode = 'text/javascript';
                        break;
						case 'json':
							editorMode = 'text/javascript';
                        break;
						case 'css':
							editorMode = 'text/css';
                        break;
						case 'html':
							editorMode = 'htmlmixed';
                        break;
						case 'xml':
                        case 'pml':
							editorMode = 'application/xml';
                        break;
						case 'png':
						case 'jpg':
						case 'jpeg':
						case 'bmp':
						case 'gif':
							dataElem.find('.image img').one('load', function () {
                                $(this).css({
                                    'marginTop':'-' + $(this).height()/2 + 'px',
                                    'marginLeft':'-' + $(this).width()/2 + 'px'
                                }); 
                            }).attr('src',d.content);
							
                            dataElem.find('.image').show();
                        break;
                        
						default:
                            editorMode = 'htmlmixed';
                        break;
					}
                    
                    log(editorMode);
                    if (typeof editor === undefined) {
                        return false;
                    } else if (typeof editor === 'object'){
                        editor.setOption('mode',editorMode);
                        editor.setValue(d.content);
                        //$('#code').val(d.content);
                    }
				}
			});
		} else {
			dataElem.find('.content').hide();
			dataElem.find('.default').html('Select a file from the tree.').show();
		}
	});
    fileBrowser.addClass('initiated');
}

$(document).ready(function() {
	jsTreeInit();
});