# Using the icons in HTML
### Basic Usage
> ES6

```JS
import Toasted from 'toastedjs'
import 'toastedjs/dist/toastedjs.min.css'
//import 'toastedjs/src/sass/toast.scss'
let toasted = new Toasted({
 // your options..
});
toasted.show('yo, toasted here !!');
```

> DIRECT

```JS
// pull the css
<link rel="stylesheet" href="https://unpkg.com/toastedjs/dist/toasted.min.css">
// pull the js file
<script src="https://unpkg.com/toastedjs/dist/toasted.min.js"></script>
<script>
    var toasted = new Toasted({ /* your options.. */ })
    toasted.show('yo, toasted is directly here !!')
</script>
```

## HINTS:
1. you can pass multiple actions as an array of actions

```
action : {
    text : 'Save',
    onClick : (e, toasted) => {
        toasted.delete()
    }
}
```

2.Material Icons supported. you will have to import the icon packs into your project.

```
{
    // pass the icon name as string
    icon : 'check'

    // or you can pass an object
    icon : {
        name : 'watch',
        after : true // this will append the icon to the end of content
    }
}
```

### API
---
#### Options:

> below are the options you can pass to create a toast or you can set these options globally.

```
// you can pass options either
let toasted = new Toasted({
    position : 'top-center',
    theme : 'alive',
    onComplete : () => {
        console.log('i am done !')
    }
})
```

| Option | Type's |	Default |	Description |
|--------|--------|---------|---------------|
| position |	String | 'top-right' | Position of the toast container ['top-right', 'top-center', 'top-left', 'bottom-right', 'bottom-center', 'bottom-left'] |
| duration	| Number	| null |	Display time of the toast in millisecond
| action	| Object, Array	| null	| ⬇ check action api below
| fullWidth	| Boolean	| FALSE	| Enable Full Width
| fitToScreen	| Boolean	| FALSE	 | Fits to Screen on Full Width
| className	| String, Array	| null	| Custom css class name of the toast
| containerClass	| String, Array	| null |	Custom css classes for toast container
| Icon	| String, Object| 	null |	⬇ check icons api below
| type	| String	| 'default'	| Type of the Toast ['success', 'info', 'error']
| theme	| String	| 'primary' |	Theme of the toast you prefer ['primary', 'outline', 'bubble']
| onComplete	| Function	| null |	Trigger when toast is completed

#### Actions

| Parameters | Type's | Default	| Description |
| ---------- | ------ | ------- | ----------- |
| text*   |	String	| -	| name of action |
| href	  | String	| null	| url of action |
| icon	  | String	| null	| name of material for action |
| class	  | String/Array	| null	| custom css class for the action |
| onClick | Function(e,toastObject)	| null	| onClick Function of action |

#### Icon

| Parameters | Type's   | Default |              Description            |
| ---------- | -------- | ------- | ----------------------------------- |
| name	     | String	|  -      | name of the icon                    |
| color	     | String	|  null   | color of the icon                   |
| after	     | Boolean	|  null   | append the icon to end of the toast |


#### Methods

> Methods available under ToastedJS

```
// you can pass string or html to message
let toasted = new Toasted({ /* global options */ })
toasted.show( 'my message', { /* some new option */ })
```

| Method   |     Parameter's                                      |   	Description
| -------- | ---------------------------------------------------- | ------------------------------------------------------------------- |
| show	   |  message*, options                                   |	Show a toast                                                        |
| success  |  message*, options                                   |	Show a toast success style                                          |
| info	   |  message*, options                                   |	Show a toast info style                                             |
| error	   |  message*, options                                   |	Show a toast error style                                            |
| register |  name, message[string,function(payload)]* , options  | Register your own toast with options explained here                 |
| group	   |  options	                                          | Create a new group of toasts (new toast container with its options) |
| clear	   |  -	                                                  | Clear all toasts                                                    |


#### Toast Instance (Single toast instance)

> Each Toast Returns a Toast Instance where you can manipulate the toast.

```
let toasted = new Toasted()

let myToast = toasted.show("Holla !!")
myToast.text("Changing the text !!!").delete(1500)

let anotherToast = toasted.error("Oopss.. my bad !")
anotherToast.text("Oopss.. it's okey..")
```

| Option	| Type's	            | Description                                       |
| --------- | --------------------- | ------------------------------------------------- |
| options	| Object	            | Options of the toast instance                     |
| toast	    | HTMLElement	        | Html Element of the toast                         |
| text	    | Function(message)	    | Change text of the toast                          |
| delete	| Function(delay = 300)	| Delete the toast with animation and delay         |
| destroy	| Function	            | Destroy the toast unregister from parent instance |


#### Browsers support

| IE / Edge        | Firefox         | Chrome          | Safari          | iOS Safari      | Chrome for Android |
| ---------------- | --------------- | --------------- | --------------- | --------------- | ------------------ |
| IE10, IE11, Edge | last 7 versions | last 7 versions | last 7 versions | last 3 versions   | last 3 versions    |
Firefox	Chrome


> Please Open and issue If You have Found any Issues.

#### Mobile Responsiveness

> On Mobile Toasts will be on full width. according to the position the toast will either be on top or bottom.

------

> View all the available icons here:
https://material.io/icons/

```
<i class="material-icons">face</i>
```

## Styling icons in material design
> These icons were designed to follow the material design guidelines and they look best when using the recommended icon sizes and colors. The styles below make it easy to apply our recommended sizes, colors, and activity states.

```
/* Rules for sizing the icon. */
.material-icons.md-18 { font-size: 18px; }
.material-icons.md-24 { font-size: 24px; }
.material-icons.md-36 { font-size: 36px; }
.material-icons.md-48 { font-size: 48px; }

/* Rules for using icons as black on a light background. */
.material-icons.md-dark { color: rgba(0, 0, 0, 0.54); }
.material-icons.md-dark.md-inactive { color: rgba(0, 0, 0, 0.26); }

/* Rules for using icons as white on a dark background. */
.material-icons.md-light { color: rgba(255, 255, 255, 1); }
.material-icons.md-light.md-inactive { color: rgba(255, 255, 255, 0.3); }
```

> CSS rules for the standard material design sizing guidelines:
```
.material-icons.md-18 { font-size: 18px; }
.material-icons.md-24 { font-size: 24px; }
.material-icons.md-36 { font-size: 36px; }
.material-icons.md-48 { font-size: 48px; }
```

---
> Material icons look best at 24px, but if an icon needs to be displayed in an alternative size, using the above CSS rules can help:
```
<i class="material-icons md-18">face</i>
<i class="material-icons md-24">face</i>
<i class="material-icons md-36">face</i>
<i class="material-icons md-48">face</i>
```

## Coloring
> Here are some examples, using the material CSS styles described above:

```
.material-icons.md-dark { color: rgba(0, 0, 0, 0.54); }
.material-icons.md-dark.md-inactive { color: rgba(0, 0, 0, 0.26); }
.material-icons.md-light { color: rgba(255, 255, 255, 1); }
.material-icons.md-light.md-inactive { color: rgba(255, 255, 255, 0.3); }
```
---

> Example for drawing an icon on a light background with a dark foreground color:
```
// Normal
<i class="material-icons md-dark">face</i>
// Disabled
<i class="material-icons md-dark md-inactive">face</i>
```

---
> Example for drawing an icon on a dark background with a light foreground color:
```
// Normal
<i class="material-icons md-light">face</i>
// Disabled
<i class="material-icons md-light md-inactive">face</i>
```

---
> To set a custom icon color, define a CSS rule specifying the desired color for the font:
```
.material-icons.orange600 { color: #FB8C00; }
```
> and then use the class when referring to the icon:
```
<i class="material-icons orange600">face</i>
```



