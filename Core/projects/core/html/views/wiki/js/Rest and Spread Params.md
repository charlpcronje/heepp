# Rest Parameters & Spead Operator 

## Old Rest paramaters:

```JS
/* Here I am declaring a function with rest params ('name' and 'surname').
   So in other words a function with a set amount of params is called 
   'rest' paramaters.
*/
function fullName(name,surname) {
    return `My name is ${name} ${surname}`;
}

console.log(fullName('Charl','Cronje'));
```
** I can use the special 'arguments' variable that is available in all functions **

```JS
function argTest() {
    var name = arguments[0];
    var surname = arguments[1];
    return `My name is ${name} ${surname}`;
}
console.log(argTest('Charl','Cronje'));
```
** It would seem that the 'arguments' is of type Array **
> But actually the argments is a very basic version of the javascript Array.

Here is a good example:
1. Normally a Javascript array has a few usefull methods
- One of them is the reduce method:

```JS
/* Normally you can do the following with reduce:
  var numbers = [1,2,3,4,5];
  var reducedNumbers = numbers.reduce((prev,curr) => {
      return prev + curr;
  });
  console.log(reducedNumbers);
```
> So what reduce did was it took the first 2 items of 
  the array and added them up "return prev + curr".
  and in the above mentioned comment code the outcome
  would have been: 15. So 1 + 2, 2+ 3, 3 + 4, 4 + 5
  But the arguments variable does not have the reduce method

```JS
let sum = function() { 
    return arguments.reduce((prev,curr) => {
        return prev + curr;
    });
};

console.log(sum(2,3,4,5));
```
> The code above will give the following error: 
> "Uncaught TypeError: arguments.reduce is not a function"

** We can parse arguments in a different context by adding prototype **

```JS
let sum = function() { 
    return Array.prototype.reduce.call(arguments,(prev,curr) => {
        return prev + curr;
    });
};

console.log(sum(2,3,4,5));
```
> The code above also works and we now have the reduce method working
> by calling reduce on the Array.prototype's context.

## Spread Operator
** Here is an example of using Spread Operator
```JS
let sum = function(...args) {
    console.log(args);
}
console.log(sum(2,3,4,5));
```
> So now you will notice that the 'args' variable is a proper Array and
> has all the methods normal js arrays have.

** Now I can use the 'reduce' method on the 'args' variable
```JS
let sum = function(...args) {
    return args.reduce((prev,curr) => prev + curr);
}
console.log(sum(2,3,4,5));
```
> The above example returns 14 just like it should.

# Combine Spread and Rest
```JS
let multiply = (mul,...numbers) => {
    console.log(mul,...numbers);
}
multiply(2,7,4,5);
```

> The above will return 2 and [7,4,5]

** Now i will use the map method of Array to iterate over each item I will then perform **
** a function each item in the array. **

```JS
let multiply = (mul,...numbers) => {
    return numbers.map((n) => {
        return mul * n;
    });
}

let result = multiply(2,7,4,5);
console.log(result);
```

# Using Rest and Spread on Math

** Now I will use the Math.max() method **
This method gets the max number in a list

```JS
let max = Math.max(4,6,3,8);
console.log(max);

```
> The above will give me 8 because it was the max number in the list

But now I want to use a an array of numbers instead of a list

```JS
let numbers = [4,6,3,8];

/* I can't just do: Math.max(numbers); Because the numbers is not a list but an Array. So I have to use the 'apply' method. It works simular to the 'call' method where you can call something in a different context but call does not accept anything but the correct arguments

Apply mimics the call method but the second param of the 'apply' method can be an array that will be handled as a list like we had.
 */
let max = Math.max.apply(null,numbers);
console.log(max);
```

# Simplifying with Spread

```JS
let numbers = [4,6,3,8];
let max = Math.max(...numbers);
console.log(max);
```
> The above example also give 8 as the answer because the Spread method in the background actually creates a list of the numbers

# Use Spread to concat arrays

```JS
let numbersOne = [1,2,4,6,7];
let numbersTwo = [9,5,4,2,3,4,5];

// Now to merge (concat) them
let concatArray = numbersOne.concat(numbersTwo);
console.log(concatArray);
```
> The above example works but you have to use a a 3rd variable and it looks messy.
** Now for using a Spread Operator

```JS
let numbersOne = [1,2,4,6,7];
let concatArray = [9,5,4,2,3,4,5,...numbersOne];
// OR
concatArray = [9,5,4,...numbersOne,2,3,4,5];
console.log(concatArray);
```
> Using the Spread Operator can be used in many more places and it is alot easier to type and read.