# Array Map

## Old Mthod

** Declaration **

```JS
let numbers = [2,3,4,5,6,7];

let doubled = numbers.map(function(n) {
    return n * 2;
});

```

** Usage **

```JS
console.log(doubled);

```

## New Method

** Declaration **

```JS
let numbers = [2,3,4,5,6,7];

let doubled => numbers.map((n) => n * 2);
```
> Or if there is only 1 argument for the function you can remove the () around the 'b':

```
let doubled => numbers.map(n => n * 2);
** Usage **

```JS
console.log(doubled);

```