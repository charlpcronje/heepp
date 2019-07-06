# Box Shadow Tricks

> ** The below will create a white modal with an overlay that is made out of a box-shadow.
This means there is no need for for extra div tags and noo scrolling will accur because 
a shadow is not a content element. **

```CSS
.modal {
    background: white;
    display: grid;
    width: 300px;
    height: 300px;
    place-items: center;
    box-shadow: 0 0 0 100vw rgba(0,0,0,.5);
}
```

> The following Youtube video explains the above and also explains 
another cool box shadow for notifications
** https://www.youtube.com/watch?v=TZRSXNc0T1k **
