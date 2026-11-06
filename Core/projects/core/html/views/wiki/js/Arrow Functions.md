# Arrow functions

**There are a few ways to use arrow functions**

## 1. With Parantheses

** Declaration **

```JS
let add = (a,b) => a + b;

```

** Usage **

```JS
console.log(add(2,3));
```

## 2. Without Parantheses

** Declaration **

```JS
let add = (a,b) => {
    return a + b;
}
```

** Usage **

```JS
console.log(add(2,3));
```


# Arrow Functions with objects

```JS
// Old way
let person = {
  name: 'Charl',
  sayName: function() {
    console.log(`Hi I am ${this.name}`)
  }
}
person.sayName();


// New short way (No need to type 'function')
let person = {
  name: 'Charl',
  sayName() {
    console.log(`Hi I am ${this.name}`)
  }
}
person.sayName();
```

## Arrow functions and Lexical Scope
```JS
let person = {
  name: 'Charl',
  hobbies: ['TV','Coding','Internet'],
  showHobbies: function() {
    // This way the 'this' below's scope is the forEach
    /*
      this.hobbies.forEach(function(hobby) {
        console.log(`${this.name} likes ${hobby}`)
      });
    */
    
    /* 
     * So to make it work you can use the arrow function method (Change to lexical scope). 
     * The arrow function method
     * refers to the 'parent' scope
     */
    this.hobbies.forEach((hobby) => {
      console.log(`${this.name} likes ${hobby}`)
    });
    
    // Then because there are only one argument on the forEach the '()' can be removed
    this.hobbies.forEach(hobby => {
      console.log(`${this.name} likes ${hobby}`)
    });
  }
}
person.showHobbies();
```
