## Easily center text vertically, with SVG!

>> http://lea.verou.me/2013/03/easily-center-text-vertically-with-svg/

1. You don’t need to change anything on the parent HTML element
2. Degrades gracefully in non-SVG browsers
3. Should be perfectly accessible and won’t break SEO
4. Works perfectly in IE9, unlike Flexbox
5. You can include any kind of SVG styling on the text. For example, strokes!

> CSS

```CSS
/* Vertically centered text with SVG */

div {
    width: 300px;
    height: 150px;
    background: #f06;
    font: bold 150% sans-serif;
    text-shadow: 0 1px 2px rgba(0,0,0,.5);
    overflow: hidden; resize: both; /* just for this demo */
    color: white;
}

svg {
    width: 100%; height: 100%;
    pointer-events: none; /* so that you can resize the element */
}

text {
    text-anchor: middle;
    pointer-events: auto; /* Cancel the svg’s pointer-events */
    fill: currentColor;
}

p {
    font: italic 100% Georgia, serif;
}
```

> HTML

```
<div>
    <svg>
        <text x="50%" y="50%" dy=".3em">
            Look, I’m centered!
        </text>
    </svg>
</div>
```

### Some links to some elements I still want to add to Lib

1. https://www.cssscript.com/demo/dialog-popup-javascript-plugin-prompt-boxes/
2. https://www.cssscript.com/demo/mobile-first-dialog-popup-javascript-library-mcx-dialog-mobile/
3. https://demo.agektmr.com/dialog/
4. https://www.cssscript.com/demo/simple-elegant-modal-popup-javascript/
5. https://www.cssscript.com/demo/text-selection-popup-pure-javascript/
6. https://www.cssscript.com/demo/lightweight-fullscreen-popup-library-jpopup/

> AWESOME!!
7. https://www.cssscript.com/demo/modern-responsive-popup-library-bop/

> Nice notfications:
8. https://www.cssscript.com/demo/animated-popup-notification-javascript-library-enotice/
- Download Page: https://www.cssscript.com/animated-popup-notification-javascript-library-enotice/

> Mini Toast (Just 3kb for JS)
9. https://www.cssscript.com/demo/mobile-friendly-popup-notification-library-minitoast/#

> Very clean: Prompt-Boxes Demos
10. https://www.cssscript.com/demo/dialog-popup-javascript-plugin-prompt-boxes/

> Busy implementing this one:
11. https://www.cssscript.com/demo/interactive-mobile-friendly-toast-library-toastedjs/
> Material Icons that works with toastedjs
http://google.github.io/material-design-icons/
> Github Page:
https://github.com/shakee93/toastedjs
> Nice demo page
https://shakee93.github.io/toastedjs/

#### Animated Circular Progress Bar Demos
https://www.cssscript.com/demo/animated-circular-progress-bar-using-svg-path-animation/

#### Pure CSS Circular Percentage Bar
https://www.cssscript.com/demo/pure-css-circular-percentage-bar/


#### Responsive Step Progress Indicator with Pure CSS
https://www.cssscript.com/demo/responsive-step-progress-indicator-with-pure-css/

#### Simple 5-star Rating System with CSS and Html Radios
https://www.cssscript.com/demo/simple-5-star-rating-system-with-css-and-html-radios/

#### Awesome file upload controls
https://www.cssscript.com/custom-file-input-javascript-css/

#### JS-Share Social Share Buttons
https://www.cssscript.com/demo/social-share-buttons-javascript/

#### scrollbooster mini test
https://www.cssscript.com/demo/smooth-drag-scroll-library-scrollbooster/

#### Experiment with multiple cubes and CSS transitions, still no JavaScript
https://paulrhayes.com/3d-cube-using-css-transformations/
> Demo page:
https://paulrhayes.com/experiments/cube/multiCubes.html
> Single Cube Demo
https://paulrhayes.com/experiments/cube/

#### Dynamically generated SVG through SASS + A 3D animated RGB cube!
http://lea.verou.me/2014/04/dynamically-generated-svg-through-sass-a-3d-animated-rgb-cube/

#### Google Style Loader in CSS
https://codepen.io/anon/pen/XRyGop
https://codepen.io/designcouch/pen/KDwCs

## CSS Conic Gradients
#### Bouncing cube CSS only
https://codepen.io/thebabydino/pen/vOMooy

#### Cool Conic gradients to form interesting circles
https://codepen.io/thebabydino/pen/aOLpvo

#### Very cool auto-completer
http://leaverou.github.io/awesomplete/#basic-usage
> Advanced examples
http://leaverou.github.io/awesomplete/#advanced-examples

#### Button to copy element content to clipboard
https://github.com/ryanpcmcquen/cheval


#### Drag and drop uploader (DropZone)
http://www.dropzonejs.com/


#### AWESOME!! Element Queries. Like CSS Media Queries just much better
https://elementqueries.com/

#### GitHub Profile Card
http://github-profile.com/

#### Grid Navigation with keyboard control
https://codepo8.github.io/gridnav/#smaller

#### Intercooler (Ajax framework)
- Manage Ajax calls
- Element updates and replacements
- Server polling
- Progress Indicators
- File Uploads
- CSS Transitions for Elements being updated or replaced
- History (Back button handling)
- Server sent Events (Push notfications)

#### Order and Filter DOM elements (Like Masonry)
https://isotope.metafizzy.co/#getting-started

#### Masonry Order and Filter DOM elements (Like Isotope) :)
https://masonry.desandro.com/

#### lazySizes: Lazy Loader which loads images
- Also responsive images(picture/srcset))
- iframes and scripts
```
put the class lazyload to all elements
```


#### Gapless, draggable grid layouts (Packery)
https://packery.metafizzy.co/
https://packery.metafizzy.co/extras.html
> Resize (Cool for Dashboards)
https://codepen.io/desandro/pen/tfugk
> Packery Methhods


#### Code Highlight:
http://prismjs.com/

#### Presentation creation frammework
https://github.com/shower/shower

#### SVG Goo Menu
https://codepen.io/oddvalue/pen/PGboPr

#### SVG Goo Blobs
https://codepen.io/oddvalue/pen/xEOdWW

#### aniJS Framework and Animations
> 1. aniJS Main Demo Page
http://anijs.github.io
> 2. Demo of some container animationns
https://codepen.io/darielnoel/pen/trnzk?editors=1000
> 3. aniJS Tab Bar
https://codepen.io/darielnoel/full/uJLGb
> 4. aniJS Accordion
https://codepen.io/darielnoel/full/qvGEb
> 5. aniJS Modal
https://codepen.io/darielnoel/full/vpBhy
> 6. aniJS Sliding menu
https://codepen.io/darielnoel/full/ypfEs

#### CSS Only Tooltips
https://codepen.io/oddvalue/pen/obXyQg

#### Maintain aspect ratio using only CSS
https://codepen.io/oddvalue/pen/dGoYON

#### CSS Interactive CUBE Wall
https://codepen.io/oddvalue/pen/YEZbwX

#### Some Cool Loaders
1. https://codepen.io/sashatran/pen/qoMNrN
2. https://codepen.io/sashatran/pen/vRrxXw
3. https://codepen.io/sashatran/pen/MVKpKK

## Some CSS only Very Cool Layouts
1. Pinterestt Profile Page
https://codepen.io/GeorgePark/pen/VXrwOP
2. Trello Board Layout
https://codepen.io/GeorgePark/pen/bLLzJK

