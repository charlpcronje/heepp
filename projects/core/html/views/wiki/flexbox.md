# CSS FLEXBOX ESSENTIALS

## What Is Flexbox?

> The CSS3 Flexible Box, or flexbox, is a layout mode providing for the arrangement of elements on a page such that the elements behave predictably when the page layout must accommodate different screen sizes and different display devices. For many applications, the flexible box model provides an improvement over the block model in that it does not use floats, nor do the flex container's margins collapse with the margins of its contents.
## Examples
### Default Display
```
<style>
    .parent {
        border: 5px solid lightcoral;
    }
    
    .item {
          /*height: 50px;*/
          margin: 5px;
          background: DeepSkyBlue;
          color: white;
          text-align: center;
          line-height: 50px;
          font-weight: 600;
    }
</style>

<div class="example example-default">
    <div class="example-content">
        <div class="parent">
                <div class="item">1</div>
                <div class="item">2</div>
                <div class="item">3</div>
                <div class="item">4</div>
                <div class="item">5</div>
            </div>
        </div>
   </div>
</div>
```
### display: flex
```
<style>
/**** display: flex ****/
.example-flex .parent {
    display: flex;
}

.example-flex .item {
    width: 50px;
}
</style>

<div class="example example-flex">
    <div class="example-content">
        <div class="parent">
            <div class="item">1</div>
            <div class="item">2</div>
            <div class="item">3</div>
            <div class="item">4</div>
            <div class="item">5</div>
        </div>
    </div>
</div>
```
### ROW-REVERSE
```
<style>
    /**** flex-direction ****/
    .example-direction .parent {
        display: flex;
        flex-direction: row-reverse;
    }
    
    .example-direction .item {
        width: 50px;
    }
</style>

<div class="example example-direction">
        <div class="example-content">
            <div class="parent">
                <div class="item">1</div>
                <div class="item">2</div>
                <div class="item">3</div>
                <div class="item">4</div>
                <div class="item">5</div>
            </div>
        </div>
    </div>
</div>
```
### FLEX-WRAP
```
<style>
/**** flex-wrap ****/
.example-wrap .parent {
    display: flex;
    flex-wrap: wrap;
}

.example-wrap .item {
    width: 30%;
}
</style>

<div class="example example-wrap">
    <div class="example-content">
        <div class="parent">
            <div class="item">1</div>
            <div class="item">2</div>
            <div class="item">3</div>
            <div class="item">4</div>
            <div class="item">5</div>
        </div>
    </div>
</div>
```
### JUSTIFY-CONTENT
```
<style>
/**** justify-content ****/
.example-justify .parent {
    display: flex;
    justify-content: space-around;
}

.example-justify .item {
    width: 50px;
}
</style>

<div class="example example-justify">
    <div class="example-content">
        <div class="parent">
            <div class="item">1</div>
            <div class="item">2</div>
            <div class="item">3</div>
            <div class="item">4</div>
            <div class="item">5</div>
        </div>
    </div>
</div>
```
### ALIGN-ITEMS
```
<style>
    /**** align-items ****/
    .example-align .parent {
          height: 200px;
          display: flex;
          align-items: flex-end;
          justify-content: space-around;
          
    }
    
    .example-align .item {
        width: 50px;
    }
</style>

<div class="example example-align">
    <div class="example-content">
        <div class="parent">
            <div class="item">1</div>
            <div class="item">2</div>
            <div class="item">3</div>
            <div class="item">4</div>
            <div class="item">5</div>
        </div>
    </div>
</div>
```
### ALIGN-CONTENT
```
<style>
/**** align-content ****/
.example-align-content .parent {
      height: 200px;
      display: flex;
      align-content: center;
      flex-wrap: wrap;
}

.example-align-content .item {
    width: 20%;
}
</style>

<div class="example example-align-content">
    <div class="example-content">
        <div class="parent">
            <div class="item">1</div>
            <div class="item">2</div>
            <div class="item">3</div>
            <div class="item">4</div>
            <div class="item">5</div>
        </div>
    </div>
</div>
```
### ORDER
```
<style>
    /**** order ****/
    .example-order .parent {
        display: flex;
    }
    
    .example-order .item {
        width: 50px;
    }
    
    .example-order .item:nth-child(2) {
        order: 1;
        background: lightgreen;
    }
</style>

<div class="example example-order">
    <div class="example-content">
        <div class="parent">
            <div class="item">1</div>
            <div class="item">2</div>
            <div class="item">3</div>
            <div class="item">4</div>
            <div class="item">5</div>
        </div>
    </div>
</div>
```
### FLEX-GROW
```
<style>
    /**** flex-grow ****/
    .example-grow .parent {
        display: flex;
    }
    
    .example-grow .item {
        width: 50px;
    }
    
    .example-grow .item:nth-child(2) {
        flex-grow: 2;
        background: lightgreen;
    }
</style>

<div class="example example-grow">
    <div class="example-content">
        <div class="parent">
            <div class="item">1</div>
            <div class="item">2</div>
            <div class="item">3</div>
            <div class="item">4</div>
            <div class="item">5</div>
        </div>
    </div>
</div>
```
### FLEX-SHRINK
```
<style>
    /**** flex-shrink ****/
    .example-shrink .parent {
        display: flex;
    }
    
    .example-shrink .item {
        width: 200px;
    }
    
    .example-shrink .item:nth-child(2) {
        flex-shrink: 2;
        background: lightgreen;
    }
</style>

<div class="example example-shrink">
    <div class="example-content">
        <div class="parent">
            <div class="item">1</div>
            <div class="item">2</div>
            <div class="item">3</div>
            <div class="item">4</div>
            <div class="item">5</div>
        </div>
    </div>
</div>
```
### FLEX-BASIS
```
<style>
    /**** flex-basis ****/
    .example-basis .parent {
        display: flex;
        flex-direction: column;
    }
    
    .example-basis .item {
        width: 200px;
    }
    
    .example-basis .item:nth-child(2) {
        flex-basis: 39px;
        background: lightgreen;
    }
</style>

<div class="example example-basis">
    <div class="example-content">
        <div class="parent">
            <div class="item">1</div>
            <div class="item">2</div>
            <div class="item">3</div>
            <div class="item">4</div>
            <div class="item">5</div>
        </div>
    </div>
</div>
```
### ALIGN-SELF
```
<style>
    /**** align-self ****/
    .example-align-self .parent {
        height: 200px;
        display: flex;
        justify-content: space-between;
    }
    
    .example-align-self .item {
        width: 50px;
        height: 50px;
    }
    
    .example-align-self .item:nth-child(2) {
        align-self: flex-end;
        background: lightgreen;
    }
</style>

<div class="example example-align-self">
    <div class="example-content">
        <div class="parent">
            <div class="item">1</div>
            <div class="item">2</div>
            <div class="item">3</div>
            <div class="item">4</div>
            <div class="item">5</div>
        </div>
    </div>
</div>
```
