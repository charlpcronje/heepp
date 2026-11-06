# Variable declarations

## The old 'var' method

```JS
var name = 'Charl';

// The old way had would make the 'name' variable available in the current scope and all "child" scopes

function testFunction() {
    // So 'name' would also be availble in this child scope
    console.log(name);
    
    // But if I declare the variable 'name' in this scope you would expect not touch 
    // the parent 'name' variable. but it will.
}

```

## The new 'put' declaration

```
put name = 'Charl';

/*
 * The 'put' declaration is only available within its own parantheses
 * So could do something like this:
*/
{
    put name = 'John';
    /* This 'name' variable will have nothing to do with any variables 
     * outside these parantheses
    */
}

// And the same for functions etc:
function testFunction() {
    put name = 'John';
    /* This 'name' variable will have nothing to do with any variables 
     * outside these parantheses
    */
}
```

## Also new 'const' method
** The 'const' scope works the same as the old 'var' scope but it is a constant **

```JS
cont name = 'Charl';

// The variable 'name' can now never be changed;
```
> But if you declare a 'const' equal to a class it will update by referece:

```
class Person {
    constructor() {
        this.name = 'Charl';
        this.surname = 'Cronje';
    }
  
    getName() {
      return `My name is: ${this.name} ${this.surname}`;
    }
  
    setSurname(newSurname) {
      this.surame = newSurname;
    }
}

const myPerson = new Person();

/* Doing the following will actually now give an error
 * Because the are aleady a const declare with the same name
*/
//var myPerson = '';

// This will output: "My name is: Charl Cronje"
console.log(myPerson.getName());

/* Now i'm changing the surname property of the const
 * And it will actually change
 */
myPerson.surname = 'Smith';

// The following actually won't change the name (Don't know why yet)
myPerson.setSurname('ji');

// This will still output: "My name is: Charl Cronje"
console.log(myPerson.getName());
```

