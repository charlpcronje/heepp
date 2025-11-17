## Javascript Factories

> The "this" keyword in JS classes can sometimes be confusing because the context of "this"
> changes every time it is it used inside a another function

```
const dag = () => {
    const sound = 'woof';
    return {
        talk: () => console.log(sound);
    }
}

const sniffles = dof();
sniffles.talk();    // Output: "woof"
```

> In the factories you don't use the "this" keyword so the following now works:

```
$('button.myButton').cick(sniffles.talk);
```
