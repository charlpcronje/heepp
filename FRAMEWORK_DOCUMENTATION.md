# HeEPP Framework - Comprehensive Documentation

**Framework Name:** HeEPP (Hyper eXtensible Element Pre-Processor)
**Created:** 2010-2014
**Context:** Pre-Composer, pre-NPM era PHP framework
**Current Status:** Legacy framework requiring modernization for PHP 8.1+

---

## Table of Contents

1. [Framework Architecture](#framework-architecture)
2. [Core Components](#core-components)
3. [Key Features](#key-features)
4. [Request Lifecycle](#request-lifecycle)
5. [Project Structure](#project-structure)
6. [Development Patterns](#development-patterns)
7. [Built-in Extensions](#built-in-extensions)

---

## Framework Architecture

### Design Philosophy

HeEPP was built with several innovative concepts for its time:

- **Element-based composition**: Everything is an "Element" that can be rendered to HTML
- **Trait-based architecture**: Heavy use of PHP traits for code reuse and modular functionality
- **Singleton pattern**: Output management through singleton instances
- **Dot notation data access**: Sophisticated dot notation system for nested object/array access
- **Built-in package management**: Pre-Composer era solution for managing libraries and dependencies

### Directory Structure

```
/heepp
├── core/
│   ├── Controller.php      # Base controller class
│   ├── Heepp.php           # Main framework class with traits
│   ├── Output.php          # Singleton output manager
│   ├── Element.php         # Base element class for HTML generation
│   ├── Fragment.php        # Fragment handling
│   ├── library.php         # Built-in library/package management
│   ├── system/             # Core system components
│   │   ├── bootstrap.php   # Framework initialization
│   │   ├── route.php       # Routing system
│   │   ├── app.php         # Application configuration
│   │   ├── traits/         # Core traits
│   │   └── handlers/       # Autoloaders and handlers
│   ├── extension/          # Built-in extensions
│   ├── library/            # UI libraries and components
│   └── element/            # Pre-built element components
```

---

## Core Components

### 1. Heepp Class (`core/Heepp.php`)

Central framework class using multiple traits:

**Responsibilities:**
- Data storage and retrieval with dot notation
- Session management
- Access control
- Logging
- Request handling
- Caching with Memcache support
- Redirects and page refresh

**Traits Used:**
- SetterTraits
- GetterTraits
- DataTraits
- SessionTraits
- InputTraits
- AccessTraits
- LogTraits
- RequestTraits

### 2. Element Class (`core/Element.php`)

Base class for all HTML element generation.

**Features:**
- Self-closing tag awareness
- Attribute management with method chaining
- Child element composition
- Static `mold()` method for quick element creation

**Example:**
```php
// Create an element
$div = Element::mold('div')
    ->setAttribute('class', 'container')
    ->addChild(Element::mold('p')->setText('Hello World'));

echo $div->render();
```

### 3. Controller Class (`core/Controller.php`)

**Purpose:** Base for all application controllers

**Features:**
- Extends Heepp class
- Inherits all framework functionality
- Minimal implementation, relies on parent functionality

**Common Methods:**
- `$this->input('field')` - Get request input
- `$this->session('key', $value)` - Session management
- `$this->setData('key', $value)` - Pass data to views
- `$this->setNotify('type', 'message')` - User notifications
- `$this->redirect($url)` - Redirect to URL

### 4. Output Class (`core/Output.php`)

**Purpose:** Centralized output management using Singleton pattern

**Features:**
- Singleton pattern implementation
- Stores UI data, session data, model data
- Manages final output rendering

---

## Key Features

### 1. Built-in Package Management (Pre-Composer Era)

HeEPP includes a sophisticated library management system:

**Features:**
- XML-based library definitions (`libraries.xml`)
- Automatic dependency resolution
- CSS/JS bundling and minification
- CDN fallback support
- GZip compression
- Section-based loading for partial library inclusion

**Example Library Definition:**
```xml
<library name="ui" version="3.0">
    <section name="core">
        <css src="//cdn.example.com/ui.css"/>
        <js src="//cdn.example.com/ui.js"/>
    </section>
    <section name="buttons" requires="core">
        <css src="components/buttons.css"/>
    </section>
</library>
```

### 2. Sophisticated Data Access with Dot Notation

HeEPP implements an advanced dot notation system for data access:

**Features:**
- Nested object/array access: `person.address.city`
- Array notation: `person.hobbies:0.hobbyName`
- Escaped dots for keys with dots: `config.some\.key\.with\.dots`
- Closure support in data storage
- Automatic data type conversion

**Example:**
```php
$heepp->setData('user.profile.name', 'John Doe');
$name = $heepp->getData('user.profile.name'); // "John Doe"

// Array access
$heepp->setData('users:0.name', 'Jane');
$firstName = $heepp->getData('users:0.name'); // "Jane"
```

### 3. Element-Based Templating

Composable HTML elements as PHP objects:

**Features:**
- Method chaining for attributes
- Automatic self-closing tag detection
- Built-in UI component library
- Custom element definitions (Fragments)

### 4. Multi-Project Support

Single framework serving multiple projects:

**Features:**
- Project-specific configuration
- Shared libraries across projects
- Isolated sessions per project
- Environment variable support

### 5. Advanced Routing System

RESTful routing with middleware support:

**Features:**
- Route groups with prefixes
- Before/after middleware
- Named routes with parameters
- Closure routes
- Route parameter binding

**Example:**
```php
// Basic route
route::get('/', 'Home@index');

// Route with parameters
route::get('/user/?{id}', 'User@show');

// Route group with middleware
route::group([
    'prefix' => 'admin',
    'before' => function() {
        // Check authentication
    }
], function() {
    route::get('dashboard', 'Admin@dashboard');
});
```

---

## Request Lifecycle

### 1. Bootstrap Phase
**File:** `index.php` → `core/system/bootstrap.php`

Steps:
- Error reporting configuration
- Helper function loading
- Environment variable parsing (.env)
- Session initialization
- Autoloader registration
- Database connection setup

### 2. Routing Phase
**File:** `core/system/route.php`

Steps:
- Route matching against request URI
- Before middleware execution
- Controller instantiation
- Controller method invocation
- After middleware execution

### 3. Controller Processing

Steps:
- Controller extends Heepp
- Access control verification
- Data processing
- Element composition
- View rendering

### 4. Output Rendering

Steps:
- Element `render()` method calls
- HTML generation
- Library inclusion (CSS/JS)
- Final output to browser

---

## Project Structure

### Standard Project Layout

```
project/
├── html/                         # Web root directory
│   ├── .env                      # Environment configuration
│   ├── .htaccess                 # Apache URL rewriting
│   ├── index.php                 # Bootstrap entry point
│   ├── assets/                   # Static resources
│   │   ├── css/                  # Stylesheets (SCSS supported)
│   │   ├── js/                   # JavaScript files
│   │   ├── images/               # Image assets
│   │   └── vendor/               # Third-party libraries
│   ├── config/                   # Configuration files
│   │   ├── app.*.php             # Modular config
│   │   └── database/             # Database connections
│   ├── controllers/              # Controller classes
│   │   └── controllers.xml       # Controller metadata
│   ├── models/                   # XML model definitions
│   ├── views/                    # Template files (.phtml)
│   │   └── partials/             # Reusable components
│   ├── routes/                   # Route definitions
│   │   ├── web.php               # Web routes
│   │   └── api.php               # API routes
│   ├── fragment/                 # Custom element definitions
│   └── logs/                     # Application logs
└── data/                         # Database dumps
```

### Project Bootstrap

**index.php Pattern:**
```php
<?php
$basePath = realpath('../../heepp');  // Path to framework
chdir($basePath.DIRECTORY_SEPARATOR);
include realpath($basePath).DIRECTORY_SEPARATOR.'index.php';
```

---

## Development Patterns

### Controller Pattern

```php
<?php
use core\extension\database\Model;
use core\extension\ui\view;

class Blog extends Controller {
    public function __construct($class = 'Controller') {
        parent::__construct($class);
    }

    public function index() {
        // Get data from model
        $posts = Model::mold('blog')->getAll();

        // Set data for view
        $this->setData('posts', $posts);

        // Set page metadata
        $this->setHeading('Blog Posts');
        $this->addBreadcrumb('Home', '/');
        $this->addBreadcrumb('Blog', 'Blog');

        // Render view
        return view::phtml('views/blog/index.phtml');
    }

    public function create() {
        if ($this->input('submit')) {
            $model = Model::mold('blog');
            $model->title = $this->input('title');
            $model->content = $this->input('content');
            $model->save();

            $this->setNotify('success', 'Post created!');
            $this->redirect('Blog');
        }

        return view::phtml('views/blog/create.phtml');
    }
}
```

### Model Pattern (XML-based ORM)

```xml
<!-- models/user.xml -->
<database connection="heepp" name="user" alias="Users">
    <table name="users" key="id">
        <columns>
            <first_name alias="First Name"/>
            <last_name alias="Last Name"/>
            <email alias="E-Mail Address"/>
            <password alias="Password"/>
            <created_at alias="Created Date"/>
        </columns>
    </table>

    <join table="companies" type="left"
          left="users.company_id" right="companies.id">
        <columns>
            <company_name alias="Company Name"/>
        </columns>
    </join>
</database>

<datasets>
    <byEmailAndPassword alias="Login check">
        <filter>
            <email equal="${email}"/>
            <password equal="${password}"/>
        </filter>
    </byEmailAndPassword>
</datasets>
```

**Model Usage:**
```php
// Using predefined datasets
$user = Model::mold('user', 'byEmailAndPassword', [
    'email' => $email,
    'password' => $password
])->current();

// CRUD operations
$user = Model::mold('user')->find($id);
$user->name = 'New Name';
$user->save();

// Get all records
$users = Model::mold('user')->getAll();
```

### View Pattern (.phtml templates)

```html
<!-- views/blog/index.phtml -->
<html>
    <head>
        <import src="views/partials/base/meta.phtml"/>
        <import src="views/partials/base/css.phtml"/>
        <title>${app.core.website.title} - Blog</title>
    </head>
    <body>
        <h1>${heading}</h1>

        <div class="posts">
            <!-- Loop through posts -->
            <!-- HeEPP uses custom templating syntax -->
        </div>

        <core.form method="post" action="Blog/create">
            <uk.textfield
                label="Title"
                name="title"
                required="true"/>

            <uk.textarea
                label="Content"
                name="content"
                rows="10"/>

            <uk.button type="submit">Create Post</uk.button>
        </core.form>
    </body>
</html>
```

### Custom Elements (Fragments)

```xml
<!-- fragment/ui.frag -->
<ui:button name="Custom Button"
           description="Enhanced button element"
           extends="html">
    <attributes>
        <data-action dataType="string"
                     description="Button action"/>
        <data-icon dataType="string"
                   description="Icon class"/>
    </attributes>
    <library library="ui" section="buttons"/>
</ui:button>
```

---

## Built-in Extensions

HeEPP includes extensive built-in functionality:

### Parsers & Processors
- **XML Parser** - DOM manipulation
- **JSON Parser** - JSON encoding/decoding
- **HTML Parser** - HTML parsing and manipulation
- **CSS Parser** - CSS processing
- **Markdown Parser** - Markdown to HTML
- **PDF Parser** - PDF generation (Mpdf)
- **Video Parser** - Video metadata extraction
- **URL Parser** - URL manipulation
- **ENV Parser** - Environment variable handling

### Database & Models
- **Model System** - XML-based ORM
- **MySQLi Database** - Database abstraction layer
- **Query Builder** - Fluent query interface
- **Soft Deletes** - Status-based record deletion

### Communication
- **Email (PHPMailer)** - Email sending
- **FTP/SFTP** - File transfer protocols
- **WebSockets (Ratchet)** - Real-time communication

### UI Libraries (Built-in)
- Bootstrap 3/4
- UIKit
- Foundation
- Metro UI
- jQuery UI

### Cloud Services
- **AWS S3** - File storage
- **AWS SES** - Email service

### Utilities
- **Image Manipulation** - GD Library wrapper
- **Excel Generation** - Spreadsheet creation
- **Session Management** - PHP/SQL-based sessions
- **Caching** - Memcache integration
- **Logging** - File-based logging

---

## Agent Reflections

### Agent 1 (Framework Core) Reflection

**What impressed me:**
1. The sophistication for 2010 is remarkable
2. The dot notation data access system is clever and well-implemented
3. The library management system with bundling and minification was innovative
4. Element composition approach is clean and intuitive
5. Early adoption of traits (PHP 5.4) shows forward thinking

**Innovative features for its time:**
- Built-in package management predating Composer by 2 years
- JavaScript/CSS bundling before webpack/gulp existed
- Component-based UI architecture
- Multi-project support from single codebase
- Sophisticated routing with middleware concepts

**Overall design quality:** 8/10 for its era, demonstrating excellent architectural thinking and anticipation of industry patterns.

### Agent 2 (Projects & Patterns) Reflection

**What works well:**
- Intuitive project structure with clear MVC separation
- Clean separation of concerns
- Familiar patterns for developers from other frameworks
- XML-based ORM provides declarative model definitions
- Custom element system was ahead of its time

**Clever for 2010:**
- Built-in SCSS support before it was common
- Route groups and middleware before Laravel popularized them
- Environment-based configuration
- Fragment system similar to modern web components

**Developer experience:** The framework is intuitive for simple projects with clear conventions, though there's a learning curve for XML model definitions.

---

## Conclusion

HeEPP represents an impressive engineering effort that solved real problems before standard solutions existed. The framework was remarkably ahead of its time, introducing concepts that would later become industry standards. While it now needs significant modernization for contemporary PHP development, many core concepts remain valuable and could form the basis of a unique and powerful modern framework.

The framework demonstrates:
- **Innovation**: Anticipated many patterns that became standard
- **Pragmatism**: Solved real problems with practical solutions
- **Architecture**: Well-organized with clear separation of concerns
- **Flexibility**: Multi-project support and extensibility

With proper modernization (see UPGRADE_ROADMAP.md), HeEPP could continue to serve as a powerful framework while maintaining its unique approaches to web development challenges.
