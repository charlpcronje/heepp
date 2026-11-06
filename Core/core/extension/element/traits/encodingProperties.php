<?php
namespace core\extension\element\traits;

trait encodingProperties {
    public function getMimeTypeDetails($extension) {                      //Assign MimeType Details to element
        $mimeTypes = [
            'html' => [
                'mimeType' => 'text/html',
                'mimeProperties'=>'xhtml',
                'cache'=>true,
                'gzip' => true,
                'minify' => true,
                'minifier'=>'minifyHTML',
                'charsetUpdatable'=>true,
                'import' => [
                    'tag' => 'link',
                    'tags' => [
                        'link' => [
                            'tag' => 'link',
                            'src_attr' => 'href',
                            'attributes' => [
                                'rel' => 'import'
                            ]
                        ],
                        'embed' => [
                            'tag' => 'embed',
                            'src_attr' => 'src',
                            'options' => [
                                'width' => '',                //Width in pixels without the 'px' or width in percentage width the % sign
                                'height' => ''                //Height in pixels without the 'px' or height in percentage width the % sign
                            ]
                        ]
                    ]
                ]
            ],
            'htm' => [
                'mimeType' => 'text/html',
                'mimeProperties'=>'xhtml',
                'cache'=>true,
                'gzip' => true,
                'minify' => true,
                'minifier'=>'minifyHTML',
                'charsetUpdatable'=>true,
                'import' => [
                    'tag' => 'link',
                    'tags' => [
                        'link' => [
                            'tag' => 'link',
                            'src_attr' => 'href',
                            'attributes' => [
                                'rel' => 'import'
                            ]
                        ],
                        'embed' => [
                            'tag' => 'embed',
                            'src_attr' => 'src',
                            'options' => [
                                'width' => '',                //Width in pixels without the 'px' or width in percentage width the % sign
                                'height' => ''                //Height in pixels without the 'px' or height in percentage width the % sign
                            ]
                        ]
                    ]
                ]
            ],
            'shtml' => [
                'mimeType' => 'text/html',
                'mimeProperties'=>'xhtml',
                'cache'=>true,
                'gzip' => true,
                'minify' => true,
                'minifier'=>'minifyHTML',
                'charsetUpdatable'=>true,
                'import' => [
                    'tag' => 'link',
                    'tags' => [
                        'link' => [
                            'tag' => 'link',
                            'src_attr' => 'href',
                            'attributes' => [
                                'rel' => 'import'
                            ]
                        ],
                        'embed' => [
                            'tag' => 'embed',
                            'src_attr' => 'src',
                            'options' => [
                                'width' => '',                //Width in pixels without the 'px' or width in percentage width the % sign
                                'height' => ''                //Height in pixels without the 'px' or height in percentage width the % sign
                            ]
                        ]
                    ]
                ]
            ],
            'xhtml' => [
                'mimeType' => 'application/xhtml+xml',
                'mimeProperties'=>'xhtml',
                'cache'=>true,
                'gzip' => true,
                'minify' => true,
                'minifier'=>'minifyXML',
                'charsetUpdatable'=>true,
                'import' => [
                    'tag' => 'link',
                    'tags' => [
                        'link' => [
                            'tag' => 'link',
                            'src_attr' => 'href',
                            'attributes' => [
                                'rel' => 'import'
                            ]
                        ],
                        'embed' => [
                            'tag' => 'embed',
                            'src_attr' => 'src',
                            'options' => [
                                'width' => '',                //Width in pixels without the 'px' or width in percentage width the % sign
                                'height' => ''                //Height in pixels without the 'px' or height in percentage width the % sign
                            ]
                        ]
                    ]
                ]
            ],
            'xml' => [
                'mimeType' => 'text/xml',
                'mimeProperties'=>'xhtml',
                'cache'=>true,
                'gzip' => true,
                'minify' => true,
                'minifier'=>'minifyXML',
                'charsetUpdatable'=>true,
                'import' => [
                    'tag' => 'link',
                    'src_attr' => 'href',
                    'attributes' => [
                        'rel' => 'import'
                    ]
                ]
            ],
            'css' => [
                'mimeType' => 'text/css',
                'mimeProperties'=>'scriptsAndStyles',
                'cache'=>true,
                'gzip' => true,
                'minify' => true,
                'minifier'=>'minifyCSS',
                'charsetUpdatable'=>true,
                'concatenate' => true,
                'import' => [
                    'tag' => 'link',
                    'src_attr' => 'href',
                    'attributes' => [
                        'rel' => 'stylesheet',
                        'type' => 'text/css'
                    ],
                    'options' => [
                        'media' => ''
                    ]
                ]
            ],
            'js' => [
                'mimeType' => 'application/x-javascript',
                'mimeProperties'=>'scriptsAndStyles',
                'cache'=>true,
                'gzip' => true,
                'minify' => true,
                'minifier'=>'minifyJS',
                'charsetUpdatable'=>true,
                'concatenate' => true,
                'import' => [
                    'tag' => 'script',
                    'src_attr' => 'src',
                    'attributes' => [
                        'type' => 'text/javascript'
                    ],
                    'options' => [
                        'defer' => '',                  //Specifies that the script is executed when the page has finished parsing (only for external scripts]. In XHTML, attribute minimization is forbidden, and the defer attribute must be defined as <script defer="defer">.
                        'async' => ''                   //Specifies that the script is executed asynchronously (only for external scripts], the script will be executed while the page continues the parsing. If async is not present and defer is present: The script is executed when the page has finished parsing. If neither async or defer is present: The script is fetched and executed immediately, before the browser continues parsing the page
                    ]
                ]
            ],
            'gif' => [
                'mimeType' => 'image/gif',
                'mimeProperties'=>'webImages',
                'cache'=>true,
                'gzip' =>false,
                'resize' =>true,
                'resizer'=>'stdImageResizer',
                'compress' => 90,
                'compressor'=>'stdImageCompressor',
                'import' => [
                    'tag' => 'img',
                    'src_attr' => 'src',
                    'attributes' => [
                        'alt' => ''                     //Alternate Text If Image Can Not Be Found
                    ],
                    'options' => [
                        'width' => '',                  //Width in Pixels without the 'px'. If Resize == true then the actual image will be re-sized on the server. If you specify only the width or height the corresponding axis will be calculated automatically
                        'height' => '',                 //Height in Pixels without the 'px'
                        'title' => ''                   //Not an Official Image attribute but works well for displaying more information onMouseOver
                    ]
                ]
            ],
            'jpeg' => [
                'mimeType' => 'image/jpeg',
                'mimeProperties'=>'webImages',
                'cache'=>true,
                'gzip' => false,
                'resize' => true,
                'resizer'=>'stdImageResizer',
                'compress' => 90,
                'compressor'=>'stdImageCompressor',
                'import' => [
                    'tag' => 'img',
                    'src_attr' => 'src',
                    'attributes' => [
                        'alt' => ''                     //Alternate Text If Image Can Not Be Found
                    ],
                    'options' => [
                        'width' => '',                  //Width in Pixels without the 'px'. If Resize == true then the actual image will be re-sized on the server. If you specify only the width or height the corresponding axis will be calculated automatically
                        'height' => '',                 //Height in Pixels without the 'px'
                        'title' => ''                   //Not an Official Image attribute but works well for displaying more information onMouseOver
                    ]
                ]
            ],
            'jpg' => [
                'mimeType' => 'image/jpeg',
                'mimeProperties'=>'webImages',
                'cache'=>true,
                'gzip' => false,
                'resize' => true,
                'resizer'=>'stdImageResizer',
                'compress' => 90,
                'compressor'=>'stdImageCompressor',
                'import' => [
                    'tag' => 'img',
                    'src_attr' => 'src',
                    'attributes' => [
                        'alt' => ''                     //Alternate Text If Image Can Not Be Found
                    ],
                    'options' => [
                        'width' => '',                  //Width in Pixels without the 'px'. If Resize == true then the actual image will be re-sized on the server. If you specify only the width or height the corresponding axis will be calculated automatically
                        'height' => '',                 //Height in Pixels without the 'px'
                        'title' => ''                   //Not an Official Image attribute but works well for displaying more information onMouseOver
                    ]
                ]
            ],
            'png' => [
                'mimeType' => 'image/png',
                'mimeProperties'=>'webImages',
                'cache'=>true,
                'gzip' => false,
                'resize' => true,
                'resizer'=>'stdImageResizer',
                'compress' => 90,
                'compressor'=>'stdImageCompressor',
                'import' => [
                    'tag' => 'img',
                    'src_attr' => 'src',
                    'attributes' => [
                        'alt' => ''                     //Alternate Text If Image Can Not Be Found
                    ],
                    'options' => [
                        'width' => '',                  //Width in Pixels without the 'px'. If Resize == true then the actual image will be re-sized on the server. If you specify only the width or height the corresponding axis will be calculated automatically
                        'height' => '',                 //Height in Pixels without the 'px'
                        'title' => ''                   //Not an Official Image attribute but works well for displaying more information onMouseOver
                    ]
                ]
            ],
            'tif' => [
                'mimeType' => 'image/tiff',
                'mimeProperties'=>'webImages',
                'cache'=>true,
                'gzip' => false,
                'resize' => true,
                'resizer'=>'stdImageResizer',
                'compress' => 90,
                'compressor'=>'stdImageCompressor',
                'import' => [
                    'tag' => 'img',
                    'src_attr' => 'src',
                    'attributes' => [
                        'alt' => ''                     //Alternate Text If Image Can Not Be Found
                    ],
                    'options' => [
                        'width' => '',                  //Width in Pixels without the 'px'. If Resize == true then the actual image will be re-sized on the server. If you specify only the width or height the corresponding axis will be calculated automatically
                        'height' => '',                 //Height in Pixels without the 'px'
                        'title' => ''                   //Not an Official Image attribute but works well for displaying more information onMouseOver
                    ]
                ]
            ],
            'tiff' => [
                'mimeType' => 'image/tiff',
                'mimeProperties'=>'webImages',
                'cache'=>true,
                'gzip' => false,
                'resize' => true,
                'resizer'=>'stdImageResizer',
                'compress' => 90,
                'compressor'=>'stdImageCompressor',
                'import' => [
                    'tag' => 'img',
                    'src_attr' => 'src',
                    'attributes' => [
                        'alt' => ''                     //Alternate Text If Image Can Not Be Found
                    ],
                    'options' => [
                        'width' => '',                  //Width in Pixels without the 'px'. If Resize == true then the actual image will be re-sized on the server. If you specify only the width or height the corresponding axis will be calculated automatically
                        'height' => '',                 //Height in Pixels without the 'px'
                        'title' => ''                   //Not an Official Image attribute but works well for displaying more information onMouseOver
                    ]
                ]
            ],
            'bmp' => [
                'mimeType' => 'image/x-ms-bmp',
                'mimeProperties'=>'webImages',
                'cache'=>true,
                'gzip' => false,
                'resize' => true,
                'resizer'=>'stdImageResizer',
                'compress' => 90,
                'compressor'=>'stdImageCompressor',
                'import' => [
                    'tag' => 'img',
                    'src_attr' => 'src',
                    'attributes' => [
                        'alt' => ''                     //Alternate Text If Image Can Not Be Found
                    ],
                    'options' => [
                        'width' => '',                  //Width in Pixels without the 'px'. If Resize == true then the actual image will be re-sized on the server. If you specify only the width or height the corresponding axis will be calculated automatically
                        'height' => '',                 //Height in Pixels without the 'px'
                        'title' => ''                   //Not an Official Image attribute but works well for displaying more information onMouseOver
                    ]
                ]
            ],
            'ico' => [
                'mimeType' => 'image/x-icon',
                'mimeProperties'=>'webImages',
                'cache'=>false,
                'import' => [
                    'tag' => 'link',
                    'src_attr' => 'href',
                    'attributes' => [
                        'rel' => 'shortcut icon',
                        'type' => 'image/x-icon'
                    ]
                ]
            ],
            'svg' => [
                'mimeType' => 'image/svg+xml',
                'mimeProperties'=>'vectorImages',
                'cache'=>false,
                'gzip' => false,
                'import' => [
                    'tag' => 'object',
                    'tags' => [
                        'object' => [
                            'tag' => 'object',
                            'inner_html' => 'Your browser does not support SVG',
                            'src_attr' => 'data',
                            'attributes' => [
                                'type' => 'image/svg+xml'
                            ]
                        ],
                        'embed' => [
                            'tag' => 'embed',
                            'src_attr' => 'src',
                            'attributes' => [
                                'type' => 'image/svg+xml'
                            ]
                        ],
                        'iframe' => [
                            'tag' => 'iframe',
                            'inner_html' => 'Your browser does not support iframes',
                            'src_attr' => 'src',
                            'options' => [
                                'frameborder' => '',                //frameborder 1 or 0
                                'width' => '',                      //width in pixels without 'px'
                                'height' => '',                     //height in pixels without 'px'
                                'scrolling' => '',                  //yes,no or auto
                                'seamless' => '',                   //When present, it specifies that the <iframe> should look like it is a part of the containing document (no borders or scrollbars]. seamless="seamless"
                                'srcdoc' => ''                      //actual html to display inside the iframe. srcdoc="<p>Hello world!</p>"
                            ]
                        ],
                        'svg' => [
                            'tag' => 'svg',
                            'inner_html' => '',                     //SVG XML: <circle cx="50" cy="50" r="40" stroke="green" stroke-width="4" fill="yellow" />, <rect width="300" height="100" style="fill:rgb(0,0,255];stroke-width:3;stroke:rgb(0,0,0]" />, <rect x="50" y="20" width="150" height="150" style="fill:blue;stroke:pink;stroke-width:5;fill-opacity:0.1;stroke-opacity:0.9" />, <rect x="50" y="20" width="150" height="150" style="fill:blue;stroke:pink;stroke-width:5;opacity:0.5" />, <rect x="50" y="20" rx="20" ry="20" width="150" height="150" style="fill:red;stroke:black;stroke-width:5;opacity:0.5" />, <circle cx="50" cy="50" r="40" stroke="black" stroke-width="3" fill="red" />, <ellipse cx="200" cy="80" rx="100" ry="50" style="fill:yellow;stroke:purple;stroke-width:2" />, <ellipse cx="240" cy="100" rx="220" ry="30" style="fill:purple" /> <ellipse cx="220" cy="70" rx="190" ry="20" style="fill:lime" /> <ellipse cx="210" cy="45" rx="170" ry="15" style="fill:yellow" />
                            'attributes' => [
                                'xmlns' => 'http://www.w3.org/2000/svg'
                            ],
                            'options' => [
                                'width' => '',
                                'height' => '',
                            ]
                        ],
                        'img' => [
                            'tag' => 'img',
                            'src_attr' => 'src',
                            'attributes' => [
                                'alt' => ''                         //Alternate Text If Image Can Not Be Found
                            ],
                            'options' => [
                                'width' => '',                      //Width in Pixels without the 'px'. If Resize == true then the actual image will be re-sized on the server. If you specify only the width or height the corresponding axis will be calculated automatically
                                'height' => '',                     //Height in Pixels without the 'px'
                                'title' => ''                       //Not an Official Image attribute but works well for displaying more information onMouseOver
                            ]
                        ]
                    ]
                ]
            ],
            'svgz' => [
                'mimeType' => 'image/svg+xml',
                'mimeProperties'=>'vectorImages',
                'cache'=>false,
                'gzip' => false,
                'import' => [
                    'tag' => 'object',
                    'tags' => [
                        'object' => [
                            'tag' => 'object',
                            'inner_html' => 'Your browser does not support SVG',
                            'src_attr' => 'data',
                            'attributes' => [
                                'type' => 'image/svg+xml'
                            ]
                        ],
                        'embed' => [
                            'tag' => 'embed',
                            'src_attr' => 'src',
                            'attributes' => [
                                'type' => 'image/svg+xml'
                            ]
                        ],
                        'iframe' => [
                            'tag' => 'iframe',
                            'inner_html' => 'Your browser does not support iframes',
                            'src_attr' => 'src',
                            'options' => [
                                'frameborder' => '',                //frameborder 1 or 0
                                'width' => '',                      //width in pixels without 'px'
                                'height' => '',                     //height in pixels without 'px'
                                'scrolling' => '',                  //yes,no or auto
                                'seamless' => '',                   //When present, it specifies that the <iframe> should look like it is a part of the containing document (no borders or scrollbars]. seamless="seamless"
                                'srcdoc' => ''                      //actual html to display inside the iframe. srcdoc="<p>Hello world!</p>"
                            ]
                        ],
                        'svg' => [
                            'tag' => 'svg',
                            'inner_html' => '',                     //SVG XML: <circle cx="50" cy="50" r="40" stroke="green" stroke-width="4" fill="yellow" />, <rect width="300" height="100" style="fill:rgb(0,0,255];stroke-width:3;stroke:rgb(0,0,0]" />, <rect x="50" y="20" width="150" height="150" style="fill:blue;stroke:pink;stroke-width:5;fill-opacity:0.1;stroke-opacity:0.9" />, <rect x="50" y="20" width="150" height="150" style="fill:blue;stroke:pink;stroke-width:5;opacity:0.5" />, <rect x="50" y="20" rx="20" ry="20" width="150" height="150" style="fill:red;stroke:black;stroke-width:5;opacity:0.5" />, <circle cx="50" cy="50" r="40" stroke="black" stroke-width="3" fill="red" />, <ellipse cx="200" cy="80" rx="100" ry="50" style="fill:yellow;stroke:purple;stroke-width:2" />, <ellipse cx="240" cy="100" rx="220" ry="30" style="fill:purple" /> <ellipse cx="220" cy="70" rx="190" ry="20" style="fill:lime" /> <ellipse cx="210" cy="45" rx="170" ry="15" style="fill:yellow" />
                            'attributes' => [
                                'xmlns' => 'http://www.w3.org/2000/svg'
                            ],
                            'options' => [
                                'width' => '',
                                'height' => '',
                            ]
                        ],
                        'img' => [
                            'tag' => 'img',
                            'src_attr' => 'src',
                            'attributes' => [
                                'alt' => ''                         //Alternate Text If Image Can Not Be Found
                            ],
                            'options' => [
                                'width' => '',                      //Width in Pixels without the 'px'. If Resize == true then the actual image will be re-sized on the server. If you specify only the width or height the corresponding axis will be calculated automatically
                                'height' => '',                     //Height in Pixels without the 'px'
                                'title' => ''                       //Not an Official Image attribute but works well for displaying more information onMouseOver
                            ]
                        ]
                    ]
                ]
            ],
            'txt' => [
                'mimeType' => 'text/plain',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'charsetUpdatable'=>true,
                'import' => [
                    'tag' => 'embed',
                    'src_attr' => 'src',
                    'attributes' => [
                        'type' => 'text/plain'
                    ],
                    'options' => [
                        'width' => '',                  //Width in Pixels without the 'px'
                        'height' => ''                  //Height in Pixels without the 'px'
                    ]
                ]
            ],
            'htc' => [
                'mimeType' => 'text/x-component',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                 //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',           //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'doc' => [
                'mimeType' => 'application/msword',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'pdf' => [
                'mimeType' => 'application/pdf',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'rtf' => [
                'mimeType' => 'application/rtf',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'xls' => [
                'mimeType' => 'application/vnd.ms-excel',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'ppt' => [
                'mimeType' => 'application/vnd.ms-powerpoint',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'wmlc' => [
                'mimeType' => 'application/vnd.wap.wmlc',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'kml' => [
                'mimeType' => 'application/vnd.google-earth.kml+xml',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'kmz' => [
                'mimeType' => 'application/vnd.google-earth.kmz',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'cco' => [
                'mimeType' => 'application/x-cocoa',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'jardiff' => [
                'mimeType' => 'application/x-java-archive-diff',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'jnlp' => [
                'mimeType' => 'application/x-java-jnlp-file',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'run' => [
                'mimeType' => 'application/x-makeself',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'pl' => [
                'mimeType' => 'application/x-perl',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'pm' => [
                'mimeType' => 'application/x-perl',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'prc' => [
                'mimeType' => 'application/x-pilot',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'pdb' => [
                'mimeType' => 'application/x-pilot',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'rpm' => [
                'mimeType' => 'application/x-redhat-package-manager',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'sea' => [
                'mimeType' => 'application/x-sea',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'sit' => [
                'mimeType' => 'application/x-stuffit',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'tcl' => [
                'mimeType' => 'application/x-tcl',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'tk' => [
                'mimeType' => 'application/x-tcl',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'der' => [
                'mimeType' => 'application/x-x509-ca-cert',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'pem' => [
                'mimeType' => 'application/x-x509-ca-cert',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'crt' => [
                'mimeType' => 'application/x-x509-ca-cert',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'xpi' => [
                'mimeType' => 'application/x-xpinstall',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'rar' => [
                'mimeType' => 'application/x-rar-compressed',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            '7z' => [
                'mimeType' => 'application/x-7z-compressed',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'zip' => [
                'mimeType' => 'application/zip',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'bin' => [
                'mimeType' => 'application/octet-stream',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'exe' => [
                'mimeType' => 'application/octet-stream',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'dll' => [
                'mimeType' => 'application/octet-stream',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'deb' => [
                'mimeType' => 'application/octet-stream',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'dmg' => [
                'mimeType' => 'application/octet-stream',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'eot' => [
                'mimeType' => 'application/octet-stream',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'iso' => [
                'mimeType' => 'application/octet-stream',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'img' => [
                'mimeType' => 'application/octet-stream',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'msi' => [
                'mimeType' => 'application/octet-stream',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'msp' => [
                'mimeType' => 'application/octet-stream',
                'mimeProperties'=>'forDownload',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'src_attr' => 'src',
                    'force_download' => true,
                    'options' => [                             //The download_only indicates that PHP will force a download on the file
                        'trigger' => 'click',                       //What will Trigger The Download?. trigger, auto. With trigger you have to specify a trigger(click,delay]. Click has to specify specify an element(s] with a jquery selector. Delay you have to specify the delay in seconds.
                        'trigger_element' => '#download',
                        'delay' => 5
                    ]
                ]
            ],
            'swf' => [
                'mimeType' => 'application/x-shockwave-flash',
                'mimeProperties'=>'soundAndMedia',
                'cache'=>false,
                'gzip' => true,
                'import' => [
                    'tag' => 'object',
                    'tags' => [
                        'object' => [
                            'tag' => 'object',
                            'inner_html' => 'Your browser does not support SVG',
                            'src_attr' => 'data',
                            'attributes' => [
                                'type' => 'image/svg+xml'
                            ],
                            'options' => [
                                'width' => '',                      //Width in pixels without the 'px' or width in percentage with the % sign,
                                'height' => ''                      //Height in pixels without the 'px' or height in percentage with the % sign,
                            ]
                        ],
                        'embed' => [
                            'tag' => 'embed',
                            'src_attr' => 'src',
                            'attributes' => [
                                'type' => 'image/svg+xml'
                            ],
                            'options' => [
                                'width' => '',                      //Width in pixels without the 'px' or width in percentage with the % sign,
                                'height' => ''                      //Height in pixels without the 'px' or height in percentage with the % sign,
                            ]
                        ]
                    ]
                ]
            ],
            'mid' => [
                'mimeType' => 'audio/midi',
                'mimeProperties'=>'soundAndMedia',
                'cache'=>false,
                'import' => [
                    'tag' => 'audio',
                    'tags' => [
                        'audio' => [
                            'tag' => 'audio',
                            'src_attr' => 'src',
                            'inner_html' => '',                     //The <source> tag should be used as inner_html: <source src="elvis.mid" /><source src="elvis.ogg" /><!-- now include flash fall back -->
                            'options' => [
                                'autoplay' => false,                //a boolean specifying whether the file should play as soon as it can
                                'loop' => false,                    //a boolean specifying whether the file should be repeatedly played.
                                'controls' => true,                 //a boolean specifying whether the browser should display its default media controls
                                'preload' => 'none'                 //none / metadata / auto — where 'metadata' means preload just the metadata and 'auto' leaves the browser to decide whether to preload the whole file.
                            ]
                        ],
                        'embed' => [
                            'tag' => 'embed',
                            'src_attr' => 'src',
                            'options' => [
                                'autostart' => true,
                                'loop' => false,
                                'hidden' => false,
                                'volume' => '5',                    //Volume from 1 to 10
                                'width' => '',
                                'height' => ''
                            ]
                        ],
                        'bgsound' => [
                            'tag' => 'bgsound',
                            'src_attr' => 'src',
                            'options' => [
                                'loop' => 'infinite'
                            ]
                        ]
                    ]
                ]
            ],
            'midi' => [
                'mimeType' => 'audio/midi',
                'mimeProperties'=>'soundAndMedia',
                'cache'=>false,
                'import' => [
                    'tag' => 'audio',
                    'tags' => [
                        'audio' => [
                            'tag' => 'audio',
                            'src_attr' => 'src',
                            'inner_html' => '',                     //The <source> tag should be used as inner_html: <source src="elvis.mid" /><source src="elvis.ogg" /><!-- now include flash fall back -->
                            'options' => [
                                'autoplay' => false,                //a boolean specifying whether the file should play as soon as it can
                                'loop' => false,                    //a boolean specifying whether the file should be repeatedly played.
                                'controls' => true,                 //a boolean specifying whether the browser should display its default media controls
                                'preload' => 'none'                 //none / metadata / auto — where 'metadata' means preload just the metadata and 'auto' leaves the browser to decide whether to preload the whole file.
                            ]
                        ],
                        'embed' => [
                            'tag' => 'embed',
                            'src_attr' => 'src',
                            'options' => [
                                'autostart' => true,
                                'loop' => false,
                                'hidden' => false,
                                'volume' => '5',                    //Volume from 1 to 10
                                'width' => '',
                                'height' => ''
                            ]
                        ],
                        'bgsound' => [
                            'tag' => 'bgsound',
                            'src_attr' => 'src',
                            'options' => [
                                'loop' => 'infinite'
                            ]
                        ]
                    ]
                ]
            ],
            'kar' => [
                'mimeType' => 'audio/midi',
                'mimeProperties'=>'soundAndMedia',
                'cache'=>false,
                'import' => [
                    'tag' => 'audio',
                    'tags' => [
                        'audio' => [
                            'tag' => 'audio',
                            'src_attr' => 'src',
                            'inner_html' => '',                     //The <source> tag should be used as inner_html: <source src="elvis.mid" /><source src="elvis.ogg" /><!-- now include flash fall back -->
                            'options' => [
                                'autoplay' => false,                //a boolean specifying whether the file should play as soon as it can
                                'loop' => false,                    //a boolean specifying whether the file should be repeatedly played.
                                'controls' => true,                 //a boolean specifying whether the browser should display its default media controls
                                'preload' => 'none'                 //none / metadata / auto — where 'metadata' means preload just the metadata and 'auto' leaves the browser to decide whether to preload the whole file.
                            ]
                        ],
                        'embed' => [
                            'tag' => 'embed',
                            'src_attr' => 'src',
                            'options' => [
                                'autostart' => true,
                                'loop' => false,
                                'hidden' => false,
                                'volume' => '5',                    //Volume from 1 to 10
                                'width' => '',
                                'height' => ''
                            ]
                        ],
                        'bgsound' => [
                            'tag' => 'bgsound',
                            'src_attr' => 'src',
                            'options' => [
                                'loop' => 'infinite'
                            ]
                        ]
                    ]
                ]
            ],
            'mp3' => [
                'mimeType' => 'audio/mpeg',
                'mimeProperties'=>'soundAndMedia',
                'cache'=>false,
                'import' => [
                    'tag' => 'audio',
                    'tags' => [
                        'audio' => [
                            'tag' => 'audio',
                            'src_attr' => 'src',
                            'inner_html' => '',                 //The <source> tag should be used as inner_html: <source src="elvis.mid" /><source src="elvis.ogg" /><!-- now include flash fall back -->
                            'options' => [
                                'autoplay' => false,            //a boolean specifying whether the file should play as soon as it can
                                'loop' => false,                //a boolean specifying whether the file should be repeatedly played.
                                'controls' => true,             //a boolean specifying whether the browser should display its default media controls
                                'preload' => 'none'             //none / metadata / auto — where 'metadata' means preload just the metadata and 'auto' leaves the browser to decide whether to preload the whole file.
                            ]
                        ],
                        'embed' => [
                            'tag' => 'embed',
                            'src_attr' => 'src',
                            'options' => [
                                'autostart' => true,
                                'loop' => false,
                                'hidden' => false,
                                'volume' => '5',                //Volume from 1 to 10
                                'width' => '',
                                'height' => ''
                            ]
                        ],
                        'bgsound' => [
                            'tag' => 'bgsound',
                            'src_attr' => 'src',
                            'options' => [
                                'loop' => 'infinite'
                            ]
                        ]
                    ]
                ]
            ],
            'm4a' => [
                'mimeType' => 'audio/x-m4a',
                'mimeProperties'=>'soundAndMedia',
                'cache'=>false,
                'import' => [
                    'tag' => 'audio',
                    'tags' => [
                        'audio' => [
                            'tag' => 'audio',
                            'src_attr' => 'src',
                            'inner_html' => '',                 //The <source> tag should be used as inner_html: <source src="elvis.mid" /><source src="elvis.ogg" /><!-- now include flash fall back -->
                            'options' => [
                                'autoplay' => false,            //a boolean specifying whether the file should play as soon as it can
                                'loop' => false,                //a boolean specifying whether the file should be repeatedly played.
                                'controls' => true,             //a boolean specifying whether the browser should display its default media controls
                                'preload' => 'none'             //none / metadata / auto — where 'metadata' means preload just the metadata and 'auto' leaves the browser to decide whether to preload the whole file.
                            ]
                        ],
                        'embed' => [
                            'tag' => 'embed',
                            'src_attr' => 'src',
                            'options' => [
                                'autostart' => true,
                                'loop' => false,
                                'hidden' => false,
                                'volume' => '5',                //Volume from 1 to 10
                                'width' => '',
                                'height' => ''
                            ]
                        ],
                        'bgsound' => [
                            'tag' => 'bgsound',
                            'src_attr' => 'src',
                            'options' => [
                                'loop' => 'infinite'
                            ]
                        ]
                    ]
                ]
            ],
            'ra' => [
                'mimeType' => 'audio/x-realaudio',
                'mimeProperties'=>'soundAndMedia',
                'cache'=>false,
                'import' => [
                    'tag' => 'audio',
                    'tags' => [
                        'audio' => [
                            'tag' => 'audio',
                            'src_attr' => 'src',
                            'inner_html' => '',                 //The <source> tag should be used as inner_html: <source src="elvis.mid" /><source src="elvis.ogg" /><!-- now include flash fall back -->
                            'options' => [
                                'autoplay' => false,            //a boolean specifying whether the file should play as soon as it can
                                'loop' => false,                //a boolean specifying whether the file should be repeatedly played.
                                'controls' => true,             //a boolean specifying whether the browser should display its default media controls
                                'preload' => 'none'             //none / metadata / auto — where 'metadata' means preload just the metadata and 'auto' leaves the browser to decide whether to preload the whole file.
                            ]
                        ],
                        'embed' => [
                            'tag' => 'embed',
                            'src_attr' => 'src',
                            'options' => [
                                'autostart' => true,
                                'loop' => false,
                                'hidden' => false,
                                'volume' => '5',                //Volume from 1 to 10
                                'width' => '',
                                'height' => ''
                            ]
                        ],
                        'bgsound' => [
                            'tag' => 'bgsound',
                            'src_attr' => 'src',
                            'options' => [
                                'loop' => 'infinite'
                            ]
                        ]
                    ]
                ]
            ],
            'ogg' => [
                'mimeType' => 'audio/ogg',
                'mimeProperties'=>'soundAndMedia',
                'cache'=>false,
                'import' => [
                    'tag' => 'audio',
                    'tags' => [
                        'audio' => [
                            'tag' => 'audio',
                            'src_attr' => 'src',
                            'inner_html' => '',                 //The <source> tag should be used as inner_html: <source src="elvis.mid" /><source src="elvis.ogg" /><!-- now include flash fall back -->
                            'options' => [
                                'autoplay' => false,            //a boolean specifying whether the file should play as soon as it can
                                'loop' => false,                //a boolean specifying whether the file should be repeatedly played.
                                'controls' => true,             //a boolean specifying whether the browser should display its default media controls
                                'preload' => 'none'             //none / metadata / auto — where 'metadata' means preload just the metadata and 'auto' leaves the browser to decide whether to preload the whole file.
                            ]
                        ],
                        'video' => [
                            'tag' => 'video',
                            'src_attr' => 'src',
                            'inner_html' => '',                 //The <source> tag should be used as inner_html: <source src="elvis.mp4" type="video/mp4" /><source src="elvis.WebM" type="video/webm" /><source src="elvis.ogg" type="video/ogg" /><track src="subtitles_en.vtt" kind="subtitles" srclang="en" label="English"><track src="subtitles_no.vtt" kind="subtitles" srclang="no" label="Norwegian"><!-- now include flash fall back -->
                            'options' => [
                                'autoplay' => false,            //a boolean specifying whether the file should play as soon as it can
                                'loop' => false,                //a boolean specifying whether the file should be repeatedly played.
                                'muted' => false,               //Specifies that the audio output of the video should be muted
                                'poster' => '',                 //Specifies the URL of the image file. An absolute URL - points to another web site (like href="http://www.example.com/poster.jpg"], A relative URL - points to a file within a web site (like href="poster.jpg"]
                                'controls' => true,             //a boolean specifying whether the browser should display its default media controls
                                'preload' => 'none',            //none / metadata / auto — where 'metadata' means preload just the metadata and 'auto' leaves the browser to decide whether to preload the whole file.
                                'width' => '',                  //Sets the width of the video player
                                'height' => '',                 //Sets the height of the video player
                            ]
                        ],
                        'embed' => [
                            'tag' => 'embed',
                            'src_attr' => 'src',
                            'options' => [
                                'autostart' => true,
                                'loop' => false,
                                'hidden' => false,
                                'volume' => '5',                //Volume from 1 to 10
                                'width' => '',
                                'height' => ''
                            ]
                        ],
                        'bgsound' => [
                            'tag' => 'bgsound',
                            'src_attr' => 'src',
                            'options' => [
                                'loop' => 'infinite'
                            ]
                        ]
                    ]
                ]
            ],
            '3gpp' => [
                'mimeType' => 'video/3gpp',
                'mimeProperties'=>'soundAndMedia',
                'cache'=>false,
                'import' => [
                    'tag' => 'video',
                    'src_attr' => 'src',
                    'inner_html' => '',                         //The <source> tag should be used as inner_html: <source src="elvis.mp4" type="video/mp4" /><source src="elvis.WebM" type="video/webm" /><source src="elvis.ogg" type="video/ogg" /><track src="subtitles_en.vtt" kind="subtitles" srclang="en" label="English"><track src="subtitles_no.vtt" kind="subtitles" srclang="no" label="Norwegian"><!-- now include flash fall back -->
                    'options' => [
                        'autoplay' => false,                    //a boolean specifying whether the file should play as soon as it can
                        'loop' => false,                        //a boolean specifying whether the file should be repeatedly played.
                        'muted' => false,                       //Specifies that the audio output of the video should be muted
                        'poster' => '',                         //Specifies the URL of the image file. An absolute URL - points to another web site (like href="http://www.example.com/poster.jpg"], A relative URL - points to a file within a web site (like href="poster.jpg"]
                        'controls' => true,                     //a boolean specifying whether the browser should display its default media controls
                        'preload' => 'none',                    //none / metadata / auto — where 'metadata' means preload just the metadata and 'auto' leaves the browser to decide whether to preload the whole file.
                        'width' => '',                          //Sets the width of the video player
                        'height' => '',                         //Sets the height of the video player
                    ]
                ]
            ],
            '3gp' => [
                'mimeType' => 'video/3gpp',
                'mimeProperties'=>'soundAndMedia',
                'cache'=>false,
                'import' => [
                    'tag' => 'video',
                    'src_attr' => 'src',
                    'inner_html' => '',                         //The <source> tag should be used as inner_html: <source src="elvis.mp4" type="video/mp4" /><source src="elvis.WebM" type="video/webm" /><source src="elvis.ogg" type="video/ogg" /><track src="subtitles_en.vtt" kind="subtitles" srclang="en" label="English"><track src="subtitles_no.vtt" kind="subtitles" srclang="no" label="Norwegian"><!-- now include flash fall back -->
                    'options' => [
                        'autoplay' => false,                    //a boolean specifying whether the file should play as soon as it can
                        'loop' => false,                        //a boolean specifying whether the file should be repeatedly played.
                        'muted' => false,                       //Specifies that the audio output of the video should be muted
                        'poster' => '',                         //Specifies the URL of the image file. An absolute URL - points to another web site (like href="http://www.example.com/poster.jpg"], A relative URL - points to a file within a web site (like href="poster.jpg"]
                        'controls' => true,                     //a boolean specifying whether the browser should display its default media controls
                        'preload' => 'none',                    //none / metadata / auto — where 'metadata' means preload just the metadata and 'auto' leaves the browser to decide whether to preload the whole file.
                        'width' => '',                          //Sets the width of the video player
                        'height' => '',                         //Sets the height of the video player
                    ]
                ]
            ],
            'mp4' => [
                'mimeType' => 'video/mp4',
                'mimeProperties'=>'soundAndMedia',
                'cache'=>false,
                'import' => [
                    'tag' => 'video',
                    'src_attr' => 'src',
                    'inner_html' => '',                         //The <source> tag should be used as inner_html: <source src="elvis.mp4" type="video/mp4" /><source src="elvis.WebM" type="video/webm" /><source src="elvis.ogg" type="video/ogg" /><track src="subtitles_en.vtt" kind="subtitles" srclang="en" label="English"><track src="subtitles_no.vtt" kind="subtitles" srclang="no" label="Norwegian"><!-- now include flash fall back -->
                    'options' => [
                        'autoplay' => false,                    //a boolean specifying whether the file should play as soon as it can
                        'loop' => false,                        //a boolean specifying whether the file should be repeatedly played.
                        'muted' => false,                       //Specifies that the audio output of the video should be muted
                        'poster' => '',                         //Specifies the URL of the image file. An absolute URL - points to another web site (like href="http://www.example.com/poster.jpg"], A relative URL - points to a file within a web site (like href="poster.jpg"]
                        'controls' => true,                     //a boolean specifying whether the browser should display its default media controls
                        'preload' => 'none',                    //none / metadata / auto — where 'metadata' means preload just the metadata and 'auto' leaves the browser to decide whether to preload the whole file.
                        'width' => '',                          //Sets the width of the video player
                        'height' => '',                         //Sets the height of the video player
                    ]
                ]
            ],
            'mpeg' => [
                'mimeType' => 'video/mpeg',
                'mimeProperties'=>'soundAndMedia',
                'cache'=>false,
                'import' => [
                    'tag' => 'video',
                    'src_attr' => 'src',
                    'inner_html' => '',                         //The <source> tag should be used as inner_html: <source src="elvis.mp4" type="video/mp4" /><source src="elvis.WebM" type="video/webm" /><source src="elvis.ogg" type="video/ogg" /><track src="subtitles_en.vtt" kind="subtitles" srclang="en" label="English"><track src="subtitles_no.vtt" kind="subtitles" srclang="no" label="Norwegian"><!-- now include flash fall back -->
                    'options' => [
                        'autoplay' => false,                    //a boolean specifying whether the file should play as soon as it can
                        'loop' => false,                        //a boolean specifying whether the file should be repeatedly played.
                        'muted' => false,                       //Specifies that the audio output of the video should be muted
                        'poster' => '',                         //Specifies the URL of the image file. An absolute URL - points to another web site (like href="http://www.example.com/poster.jpg"], A relative URL - points to a file within a web site (like href="poster.jpg"]
                        'controls' => true,                     //a boolean specifying whether the browser should display its default media controls
                        'preload' => 'none',                    //none / metadata / auto — where 'metadata' means preload just the metadata and 'auto' leaves the browser to decide whether to preload the whole file.
                        'width' => '',                          //Sets the width of the video player
                        'height' => '',                         //Sets the height of the video player
                    ]
                ]
            ],
            'mpg' => [
                'mimeType' => 'video/mpeg',
                'mimeProperties'=>'soundAndMedia',
                'cache'=>false,
                'import' => [
                    'tag' => 'video',
                    'src_attr' => 'src',
                    'inner_html' => '',                         //The <source> tag should be used as inner_html: <source src="elvis.mp4" type="video/mp4" /><source src="elvis.WebM" type="video/webm" /><source src="elvis.ogg" type="video/ogg" /><track src="subtitles_en.vtt" kind="subtitles" srclang="en" label="English"><track src="subtitles_no.vtt" kind="subtitles" srclang="no" label="Norwegian"><!-- now include flash fall back -->
                    'options' => [
                        'autoplay' => false,                    //a boolean specifying whether the file should play as soon as it can
                        'loop' => false,                        //a boolean specifying whether the file should be repeatedly played.
                        'muted' => false,                       //Specifies that the audio output of the video should be muted
                        'poster' => '',                         //Specifies the URL of the image file. An absolute URL - points to another web site (like href="http://www.example.com/poster.jpg"], A relative URL - points to a file within a web site (like href="poster.jpg"]
                        'controls' => true,                     //a boolean specifying whether the browser should display its default media controls
                        'preload' => 'none',                    //none / metadata / auto — where 'metadata' means preload just the metadata and 'auto' leaves the browser to decide whether to preload the whole file.
                        'width' => '',                          //Sets the width of the video player
                        'height' => '',                         //Sets the height of the video player
                    ]
                ]
            ],
            'mov' => [
                'mimeType' => 'video/quicktime',
                'mimeProperties'=>'soundAndMedia',
                'cache'=>false,
                'import' => [
                    'tag' => 'video',
                    'src_attr' => 'src',
                    'inner_html' => '',                         //The <source> tag should be used as inner_html: <source src="elvis.mp4" type="video/mp4" /><source src="elvis.WebM" type="video/webm" /><source src="elvis.ogg" type="video/ogg" /><track src="subtitles_en.vtt" kind="subtitles" srclang="en" label="English"><track src="subtitles_no.vtt" kind="subtitles" srclang="no" label="Norwegian"><!-- now include flash fall back -->
                    'options' => [
                        'autoplay' => false,                    //a boolean specifying whether the file should play as soon as it can
                        'loop' => false,                        //a boolean specifying whether the file should be repeatedly played.
                        'muted' => false,                       //Specifies that the audio output of the video should be muted
                        'poster' => '',                         //Specifies the URL of the image file. An absolute URL - points to another web site (like href="http://www.example.com/poster.jpg"], A relative URL - points to a file within a web site (like href="poster.jpg"]
                        'controls' => true,                     //a boolean specifying whether the browser should display its default media controls
                        'preload' => 'none',                    //none / metadata / auto — where 'metadata' means preload just the metadata and 'auto' leaves the browser to decide whether to preload the whole file.
                        'width' => '',                          //Sets the width of the video player
                        'height' => '',                         //Sets the height of the video player
                    ]
                ]
            ],
            'webm' => [
                'mimeType' => 'video/webm',
                'mimeProperties'=>'soundAndMedia',
                'cache'=>false,
                'import' => [
                    'tag' => 'video',
                    'src_attr' => 'src',
                    'inner_html' => '',                         //The <source> tag should be used as inner_html: <source src="elvis.mp4" type="video/mp4" /><source src="elvis.WebM" type="video/webm" /><source src="elvis.ogg" type="video/ogg" /><track src="subtitles_en.vtt" kind="subtitles" srclang="en" label="English"><track src="subtitles_no.vtt" kind="subtitles" srclang="no" label="Norwegian"><!-- now include flash fall back -->
                    'options' => [
                        'autoplay' => false,                    //a boolean specifying whether the file should play as soon as it can
                        'loop' => false,                        //a boolean specifying whether the file should be repeatedly played.
                        'muted' => false,                       //Specifies that the audio output of the video should be muted
                        'poster' => '',                         //Specifies the URL of the image file. An absolute URL - points to another web site (like href="http://www.example.com/poster.jpg"], A relative URL - points to a file within a web site (like href="poster.jpg"]
                        'controls' => true,                     //a boolean specifying whether the browser should display its default media controls
                        'preload' => 'none',                    //none / metadata / auto — where 'metadata' means preload just the metadata and 'auto' leaves the browser to decide whether to preload the whole file.
                        'width' => '',                          //Sets the width of the video player
                        'height' => '',                         //Sets the height of the video player
                    ]
                ]
            ],
            'flv' => [
                'mimeType' => 'video/x-flv',
                'mimeProperties'=>'soundAndMedia',
                'cache'=>false,
                'import' => [
                    'tag' => 'video',
                    'src_attr' => 'src',
                    'inner_html' => '',                         //The <source> tag should be used as inner_html: <source src="elvis.mp4" type="video/mp4" /><source src="elvis.WebM" type="video/webm" /><source src="elvis.ogg" type="video/ogg" /><track src="subtitles_en.vtt" kind="subtitles" srclang="en" label="English"><track src="subtitles_no.vtt" kind="subtitles" srclang="no" label="Norwegian"><!-- now include flash fall back -->
                    'options' => [
                        'autoplay' => false,                    //a boolean specifying whether the file should play as soon as it can
                        'loop' => false,                        //a boolean specifying whether the file should be repeatedly played.
                        'muted' => false,                       //Specifies that the audio output of the video should be muted
                        'poster' => '',                         //Specifies the URL of the image file. An absolute URL - points to another web site (like href="http://www.example.com/poster.jpg"], A relative URL - points to a file within a web site (like href="poster.jpg"]
                        'controls' => true,                     //a boolean specifying whether the browser should display its default media controls
                        'preload' => 'none',                    //none / metadata / auto — where 'metadata' means preload just the metadata and 'auto' leaves the browser to decide whether to preload the whole file.
                        'width' => '',                          //Sets the width of the video player
                        'height' => '',                         //Sets the height of the video player
                    ]
                ]
            ],
            'm4v' => [
                'mimeType' => 'video/x-m4v',
                'mimeProperties'=>'soundAndMedia',
                'cache'=>false,
                'import' => [
                    'tag' => 'video',
                    'src_attr' => 'src',
                    'inner_html' => '',                         //The <source> tag should be used as inner_html: <source src="elvis.mp4" type="video/mp4" /><source src="elvis.WebM" type="video/webm" /><source src="elvis.ogg" type="video/ogg" /><track src="subtitles_en.vtt" kind="subtitles" srclang="en" label="English"><track src="subtitles_no.vtt" kind="subtitles" srclang="no" label="Norwegian"><!-- now include flash fall back -->
                    'options' => [
                        'autoplay' => false,                    //a boolean specifying whether the file should play as soon as it can
                        'loop' => false,                        //a boolean specifying whether the file should be repeatedly played.
                        'muted' => false,                       //Specifies that the audio output of the video should be muted
                        'poster' => '',                         //Specifies the URL of the image file. An absolute URL - points to another web site (like href="http://www.example.com/poster.jpg"], A relative URL - points to a file within a web site (like href="poster.jpg"]
                        'controls' => true,                     //a boolean specifying whether the browser should display its default media controls
                        'preload' => 'none',                    //none / metadata / auto — where 'metadata' means preload just the metadata and 'auto' leaves the browser to decide whether to preload the whole file.
                        'width' => '',                          //Sets the width of the video player
                        'height' => '',                         //Sets the height of the video player
                    ]
                ]
            ],
            'mng' => [
                'mimeType' => 'video/x-mng',
                'mimeProperties'=>'soundAndMedia',
                'cache'=>false,
                'import' => [
                    'tag' => 'video',
                    'src_attr' => 'src',
                    'inner_html' => '',                         //The <source> tag should be used as inner_html: <source src="elvis.mp4" type="video/mp4" /><source src="elvis.WebM" type="video/webm" /><source src="elvis.ogg" type="video/ogg" /><track src="subtitles_en.vtt" kind="subtitles" srclang="en" label="English"><track src="subtitles_no.vtt" kind="subtitles" srclang="no" label="Norwegian"><!-- now include flash fall back -->
                    'options' => [
                        'autoplay' => false,                    //a boolean specifying whether the file should play as soon as it can
                        'loop' => false,                        //a boolean specifying whether the file should be repeatedly played.
                        'muted' => false,                       //Specifies that the audio output of the video should be muted
                        'poster' => '',                         //Specifies the URL of the image file. An absolute URL - points to another web site (like href="http://www.example.com/poster.jpg"], A relative URL - points to a file within a web site (like href="poster.jpg"]
                        'controls' => true,                     //a boolean specifying whether the browser should display its default media controls
                        'preload' => 'none',                    //none / metadata / auto — where 'metadata' means preload just the metadata and 'auto' leaves the browser to decide whether to preload the whole file.
                        'width' => '',                          //Sets the width of the video player
                        'height' => '',                         //Sets the height of the video player
                    ]
                ]
            ],
            'asx' => [
                'mimeType' => 'video/x-ms-asf',
                'mimeProperties'=>'soundAndMedia',
                'cache'=>false,
                'import' => [
                    'tag' => 'video',
                    'src_attr' => 'src',
                    'inner_html' => '',                         //The <source> tag should be used as inner_html: <source src="elvis.mp4" type="video/mp4" /><source src="elvis.WebM" type="video/webm" /><source src="elvis.ogg" type="video/ogg" /><track src="subtitles_en.vtt" kind="subtitles" srclang="en" label="English"><track src="subtitles_no.vtt" kind="subtitles" srclang="no" label="Norwegian"><!-- now include flash fall back -->
                    'options' => [
                        'autoplay' => false,                    //a boolean specifying whether the file should play as soon as it can
                        'loop' => false,                        //a boolean specifying whether the file should be repeatedly played.
                        'muted' => false,                       //Specifies that the audio output of the video should be muted
                        'poster' => '',                         //Specifies the URL of the image file. An absolute URL - points to another web site (like href="http://www.example.com/poster.jpg"], A relative URL - points to a file within a web site (like href="poster.jpg"]
                        'controls' => true,                     //a boolean specifying whether the browser should display its default media controls
                        'preload' => 'none',                    //none / metadata / auto — where 'metadata' means preload just the metadata and 'auto' leaves the browser to decide whether to preload the whole file.
                        'width' => '',                          //Sets the width of the video player
                        'height' => '',                         //Sets the height of the video player
                    ]
                ]
            ],
            'asf' => [
                'mimeType' => 'video/x-ms-asf',
                'mimeProperties'=>'soundAndMedia',
                'cache'=>false,
                'import' => [
                    'tag' => 'video',
                    'src_attr' => 'src',
                    'inner_html' => '',                         //The <source> tag should be used as inner_html: <source src="elvis.mp4" type="video/mp4" /><source src="elvis.WebM" type="video/webm" /><source src="elvis.ogg" type="video/ogg" /><track src="subtitles_en.vtt" kind="subtitles" srclang="en" label="English"><track src="subtitles_no.vtt" kind="subtitles" srclang="no" label="Norwegian"><!-- now include flash fall back -->
                    'options' => [
                        'autoplay' => false,                    //a boolean specifying whether the file should play as soon as it can
                        'loop' => false,                        //a boolean specifying whether the file should be repeatedly played.
                        'muted' => false,                       //Specifies that the audio output of the video should be muted
                        'poster' => '',                         //Specifies the URL of the image file. An absolute URL - points to another web site (like href="http://www.example.com/poster.jpg"], A relative URL - points to a file within a web site (like href="poster.jpg"]
                        'controls' => true,                     //a boolean specifying whether the browser should display its default media controls
                        'preload' => 'none',                    //none / metadata / auto — where 'metadata' means preload just the metadata and 'auto' leaves the browser to decide whether to preload the whole file.
                        'width' => '',                          //Sets the width of the video player
                        'height' => '',                         //Sets the height of the video player
                    ]
                ]
            ],
            'wmv' => [
                'mimeType' => 'video/x-ms-wmv',
                'mimeProperties'=>'soundAndMedia',
                'cache'=>false,
                'import' => [
                    'tag' => 'video',
                    'src_attr' => 'src',
                    'inner_html' => '',                         //The <source> tag should be used as inner_html: <source src="elvis.mp4" type="video/mp4" /><source src="elvis.WebM" type="video/webm" /><source src="elvis.ogg" type="video/ogg" /><track src="subtitles_en.vtt" kind="subtitles" srclang="en" label="English"><track src="subtitles_no.vtt" kind="subtitles" srclang="no" label="Norwegian"><!-- now include flash fall back -->
                    'options' => [
                        'autoplay' => false,                    //a boolean specifying whether the file should play as soon as it can
                        'loop' => false,                        //a boolean specifying whether the file should be repeatedly played.
                        'muted' => false,                       //Specifies that the audio output of the video should be muted
                        'poster' => '',                         //Specifies the URL of the image file. An absolute URL - points to another web site (like href="http://www.example.com/poster.jpg"], A relative URL - points to a file within a web site (like href="poster.jpg"]
                        'controls' => true,                     //a boolean specifying whether the browser should display its default media controls
                        'preload' => 'none',                    //none / metadata / auto — where 'metadata' means preload just the metadata and 'auto' leaves the browser to decide whether to preload the whole file.
                        'width' => '',                          //Sets the width of the video player
                        'height' => '',                         //Sets the height of the video player
                    ]
                ]
            ],
            'avi' => [
                'mimeType' => 'video/x-msvideo',
                'mimeProperties'=>'soundAndMedia',
                'cache'=>false,
                'import' => [
                    'tag' => 'video',
                    'src_attr' => 'src',
                    'inner_html' => '',                         //The <source> tag should be used as inner_html: <source src="elvis.mp4" type="video/mp4" /><source src="elvis.WebM" type="video/webm" /><source src="elvis.ogg" type="video/ogg" /><track src="subtitles_en.vtt" kind="subtitles" srclang="en" label="English"><track src="subtitles_no.vtt" kind="subtitles" srclang="no" label="Norwegian"><!-- now include flash fall back -->
                    'options' => [
                        'autoplay' => false,                    //a boolean specifying whether the file should play as soon as it can
                        'loop' => false,                        //a boolean specifying whether the file should be repeatedly played.
                        'muted' => false,                       //Specifies that the audio output of the video should be muted
                        'poster' => '',                         //Specifies the URL of the image file. An absolute URL - points to another web site (like href="http://www.example.com/poster.jpg"], A relative URL - points to a file within a web site (like href="poster.jpg"]
                        'controls' => true,                     //a boolean specifying whether the browser should display its default media controls
                        'preload' => 'none',                    //none / metadata / auto — where 'metadata' means preload just the metadata and 'auto' leaves the browser to decide whether to preload the whole file.
                        'width' => '',                          //Sets the width of the video player
                        'height' => '',                         //Sets the height of the video player
                    ]
                ]
            ],
            'vtt' => [
                'mimeType' => 'text/vtt',
                'mimeProperties'=>'soundAndMedia',
                'cache'=>false,
                'charsetUpdatable'=>true,
                'import' => [
                    'src_attr' => 'src',
                    'options' => [
                        'kind' => '',                           //captions: The track defines translation of dialogue and sound effects (suitable for deaf users], chapters: The track defines chapter titles (suitable for navigating the media resource], descriptions: The track defines a textual description of the video content (suitable for blind users], metadata: The track defines content used by scripts. Not visible for the user, subtitles: The track defines subtitles, used to display subtitles in a video
                        'srclang' => 'en',                      //Specifies the language of the track text data (required if kind="subtitles"]. Specifies a two-letter language code (http://www.w3schools.com/tags/ref_language_codes.asp] that specifies the language of the track text data
                        'label' => 'English',
                        'default' => ''
                    ]
                ]
            ],
        ];
        return $this->mimeTypeDetails = $mimeTypes[$extension];
    }
    
    public function assignMimeTypeProperties() {
        //When Calling these Properties all Types get The Global Properties and then the other categories has some properties that are unique to them
        $mimeTypeProperties = [                            //array of properties per mineType that is relevant to that type. if mimeType extension don't exist, use default
            'global'=>[
                'project'=>PROJECT,                             //Your Current Project name
                //'cachedFileName'=>null,                       //If this property is set then we know it is a cached element
                //'src'=>null,                                  //src url of import
                //'path'=>null,                                 //Path to the file without filename but ending with DIRECTORY_SEPARATOR
                //'fileName'=>null,                             //The Name of the File Including The File Extension
                //'fullPath'=>null,                             //Full Path of the File Including File Path And File Name. completely Un-parsed
                //'fileExtension'=>null,                        //The Extension of the File
                //'hashPath'=>null,                             //MD5 Hash of PROJECT Path concatenated With Path
                //'fileSize'=>null,                             //The File Size in Bytes
                'fileContentConversions'=>[],              //Array of all the conversion that has been done on the file           
                //'fileContent'=>[],                       //Hold The Current File Content

                'mimeType'=>null,                               //The mimeType of the file. This is to set the correct headers
                'mimeTypeDetails'=>[],                     //Details of the mimeType. Ex: html tag, if it can be minified, zipped, re-sized etc

                //'lastAccessDate'=>null,                       //The Last Time The File Was Accessed. In UNIX Timestamp
                //'modifiedDate'=>null,                         //The Last Time The File Was Modified. In UNIX Timestamp

                //'cashedFileName'=>null,                       //Cached File Name Is Determined By The mimeTypeProperties. If any one of the properties change the file must be reloaded
                'characterSetEncode'=>true,                     //Must The File Be Encoded With $characterSet
                'characterSet'=>null                            //Encoding of your js and css files. (utf-8 or iso-8859-1]
            ],
            'xhtml'=>[
                'minify'=>true,                                 //set Minifier true or false. This boolean will be overwritten by the mimeType Detail of it is different
                'minified'=>false,                              //True if the file is already minified

                'gzip'=>true,                                   //gzip compression true or false
                'gzipCompressionLevel'=>9,                      //gzip compression level (an integer between 1 and 9]
                'clientBrowserGzipCompatible'=>false,           //It must be checked if the client's browser is gzip encoding
                'gzipped'=>false                                //The File's gzip status
            ],
            'scriptsAndStyles'=>[            
                'minify'=>true,                                 //set Minifier true or false. This boolean will be overwritten by the mimeType Detail of it is different
                'minified'=>false,                              //True if the file is already minified

                'gzip'=>true,                                   //gzip compression true or false
                'gzipCompressionLevel'=>9,                      //gzip compression level (an integer between 1 and 9]
                'clientBrowserGzipCompatible'=>false,           //It must be checked if the client's browser is gzip encoding
                'gzipped'=>false,                               //The File's gzip status

                'concatenate'=>true,                            //file concatenation true or false (Can only concatenate files in the same folder], css url(] paths will break if more than one folder is combined
                'fileConcatenated'=>false,                      //Is The File Concatenated With Any Other Files
                'concatenatedWith'=>[]                     //Array of Files it is Concatenated With
            ],
            'webImages'=>[            
                'gzip'=>true,                                   //gzip compression true or false
                'gzipCompressionLevel'=>9,                      //gzip compression level (an integer between 1 and 9]
                'clientBrowserGzipCompatible'=>false,           //It must be checked if the client's browser is gzip encoding
                'gzipped'=>false,                               //The File's gzip status

                'setMaxImageSize'=>true,                        //Resize Images to The Max width and height
                'maxImageSize'=>[                          //Image Max Sizes, The Images will be re-sized to be smaller than max sizes
                    'width' => 1000,
                    'height' => 1000
                ],
                'imageCompression'=>true,                       //Should the Images be Compressed
                'imageCompressionQuality'=>80,                  //Image Quality After Compression 0-100
                'resizeImagesToSetDimentions'=>true,            //This will let the server resize the image to the actual size set by in the attributes in HTML. Ex: <img src="img_url" width="100" height="150"/> will resize the image on the server to 100px wide and 150 in height and serve the re-sized image to the browser
            ],
            'vectorImages'=>[
                'gzip'=>true,                                   //gzip compression true or false
                'gzipCompressionLevel'=>9,                      //gzip compression level (an integer between 1 and 9]
                'clientBrowserGzipCompatible'=>false,           //It must be checked if the client's browser is gzip encoding
                'gzipped'=>false                                //The File's gzip status
            ],
            'forDownload'=>[
                'gzip'=>true,                                   //gzip compression true or false
                'gzipCompressionLevel'=>9,                      //gzip compression level (an integer between 1 and 9]
                'clientBrowserGzipCompatible'=>false,           //It must be checked if the client's browser is gzip encoding
                'gzipped'=>false                                //The File's gzip status
            ],
            'soundAndMedia'=>[

            ]
        ];
        foreach($mimeTypeProperties['global'] as $property=>$value) {
            $this->element->$property = $value;
        }
        
        foreach($mimeTypeProperties[$this->mimeTypeDetails['mimeProperties']] as $property=>$value) {
            $this->element->$property = $value;
        }
    }
}
