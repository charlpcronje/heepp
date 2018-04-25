<javascript src="${env.core.element.url}ui/bootstrap/popover.js"/>

<!--
    type: light, dark
    
-->

<div class="popover popover-${type} fade ${position}"    
        style="display:block; ${style}"
        id="${id}">
    <div class="arrow"></div>
    <h3>${title}</h3>
    <div>${children}</div>
</div>


<div class="popover popover-dark fade bottom in" 
        id="popover756613" 
        style="top: 984px; 
            left: 1199.77px; 
            display: block;">
    
    <div class="arrow" 
        style="left: 50%;">
    </div>
    <h3 class="popover-title popover-title">
        Title
    </h3>
    <div class="popover-content popover-content">
        Vivamus sagittis lacus vel augue laoreet rutrum faucibus.
    </div>
</div>
<button 
    rel="popover_dark" 
    type="button" 
    class="btn btn-dark m-b-10" 
    data-container="body" 
    data-toggle="popover" 
    data-placement="bottom" 
    data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus." 
    data-original-title="Title" 
    title="" 
    aria-describedby="popover756613">
    Dark Style
</button>