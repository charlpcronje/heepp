# HeEPP Framework - Upgrade Roadmap to PHP 8.1+

**Created:** November 2025
**Target:** PHP 8.1+ Compatibility & Modern Standards
**Framework Age:** 15 years (2010-2025)

---

## Executive Summary

HeEPP was built in an era before Composer, NPM, PSR standards, and modern PHP features. This roadmap outlines a phased approach to modernize the framework while preserving its innovative core concepts.

**Key Challenges:**
- Removal of deprecated PHP features (magic quotes, dynamic properties)
- Migration from custom package management to Composer
- Addition of type safety and modern PHP features
- Security updates and best practices
- Performance optimizations

---

## Critical Issues (MUST FIX for PHP 8.1+)

### 1. Removed PHP Functions

**Problem:** Functions that no longer exist in PHP 8.0+

**Locations:**
- `get_magic_quotes_gpc()` - Line 47 in Database.php
- `set_magic_quotes_runtime()` - Multiple occurrences in Mpdf library
- `memcache_connect()` - Should use `Memcached` extension instead

**Solution:**
```php
// BEFORE (Database.php:47)
if (get_magic_quotes_gpc()) {
    $value = stripslashes($value);
}

// AFTER
// Remove entirely - magic quotes removed in PHP 5.4
// Ensure raw input is properly validated/sanitized instead
```

**Priority:** 🔴 CRITICAL - Breaks on PHP 8.0+

---

### 2. Dynamic Properties

**Problem:** PHP 8.2+ requires `#[AllowDynamicProperties]` attribute

**Affected Classes:**
- All Model classes
- Controller classes
- Element classes
- Any class using `$this->propertyName` without declaration

**Solution:**
```php
// Add to classes that need dynamic properties
#[\AllowDynamicProperties]
class Model {
    // ...
}

// OR declare properties explicitly
class Model {
    private array $data = [];

    public function __set($name, $value) {
        $this->data[$name] = $value;
    }

    public function __get($name) {
        return $this->data[$name] ?? null;
    }
}
```

**Priority:** 🔴 CRITICAL - Deprecated in PHP 8.2, removed in PHP 9.0

---

### 3. Error Reporting Syntax

**Problem:** Incorrect bitwise operator usage

**Location:** Bootstrap files, configuration

```php
// BEFORE
error_reporting(E_ALL ^ ~E_DEPRECATED);

// AFTER
error_reporting(E_ALL & ~E_DEPRECATED);
```

**Priority:** 🟡 HIGH - Incorrect behavior

---

### 4. MySQLi Parameter Issues

**Problem:** Incorrect parameter order in MySQLi functions

**Solution:**
- Review all MySQLi calls for correct parameter order
- Consider migrating to PDO for better abstraction
- Ensure all queries use prepared statements

**Priority:** 🟡 HIGH - Security and stability

---

## High Priority Updates

### 1. Add Type Declarations

**Impact:** Improves code quality, IDE support, catches bugs early

**Implementation:**
```php
// BEFORE
class Controller {
    public function setData($key, $value) {
        // ...
    }

    public function getData($key, $default = null) {
        // ...
    }
}

// AFTER
class Controller {
    public function setData(string $key, mixed $value): void {
        // ...
    }

    public function getData(string $key, mixed $default = null): mixed {
        return $this->data[$key] ?? $default;
    }
}
```

**Priority:** 🟡 HIGH
**Effort:** Medium - Requires analysis of all methods
**Benefits:**
- Better IDE autocomplete
- Type safety
- Self-documenting code

---

### 2. Implement Namespaces & PSR-4

**Current State:** No namespaces, custom autoloading

**Target Structure:**
```
Heepp\
├── Core\
│   ├── Controller
│   ├── Element
│   ├── Fragment
│   └── Output
├── Extension\
│   ├── Database\
│   │   └── Model
│   └── UI\
│       └── View
├── System\
│   ├── Router
│   ├── Bootstrap
│   └── Container
└── Traits\
    ├── DataTrait
    ├── SessionTrait
    └── InputTrait
```

**composer.json:**
```json
{
    "name": "heepp/framework",
    "description": "Hyper eXtensible Element Pre-Processor",
    "type": "framework",
    "license": "MIT",
    "autoload": {
        "psr-4": {
            "Heepp\\": "core/"
        }
    },
    "require": {
        "php": "^8.1"
    }
}
```

**Priority:** 🟡 HIGH
**Effort:** High - Requires refactoring all classes
**Benefits:**
- Modern autoloading
- Better IDE support
- Composer ecosystem integration

---

### 3. Migrate to Composer Dependencies

**Replace Built-in Libraries:**

| Current | Replace With | Benefits |
|---------|-------------|----------|
| Custom autoloader | Composer PSR-4 | Standard, optimized |
| Mpdf (old version) | mpdf/mpdf ^8.0 | PHP 8.1 compatible |
| PHPMailer (old) | phpmailer/phpmailer ^6.8 | Modern, maintained |
| Ratchet (old) | cboden/ratchet ^0.4 | Up-to-date |
| Custom XML parser | symfony/dom-crawler | Robust, tested |

**Priority:** 🟡 HIGH
**Effort:** Medium
**Benefits:**
- Security updates
- Modern features
- Community support

---

### 4. Modernize Database Layer

**Current Issues:**
- XML model definitions are verbose
- MySQLi directly used (not abstracted)
- Limited query builder capabilities
- No migration system

**Recommendations:**

**Option A: Modernize Existing**
```php
// Use PHP 8 attributes instead of XML
#[Table(name: 'users', key: 'id')]
class User extends Model {
    #[Column(alias: 'First Name')]
    public string $first_name;

    #[Column(alias: 'Last Name')]
    public string $last_name;

    #[Column(alias: 'E-Mail Address')]
    public string $email;

    #[BelongsTo(Company::class, foreignKey: 'company_id')]
    public Company $company;

    #[Dataset]
    public static function byEmailAndPassword(string $email, string $password): ?self {
        return static::where('email', $email)
            ->where('password', $password)
            ->first();
    }
}
```

**Option B: Integrate Existing ORM**
- Consider Doctrine ORM or Eloquent
- Maintain custom ORM as alternative
- Provide migration path

**Priority:** 🟡 HIGH
**Effort:** High
**Decision Needed:** Keep custom ORM or adopt standard?

---

## Medium Priority Updates

### 1. Leverage PHP 8+ Features

**Constructor Property Promotion:**
```php
// BEFORE
class Element {
    private $tag;
    private $attributes;

    public function __construct($tag, $attributes = []) {
        $this->tag = $tag;
        $this->attributes = $attributes;
    }
}

// AFTER
class Element {
    public function __construct(
        private string $tag,
        private array $attributes = []
    ) {}
}
```

**Match Expressions:**
```php
// BEFORE
switch ($type) {
    case 'success':
        $class = 'alert-success';
        break;
    case 'error':
        $class = 'alert-danger';
        break;
    default:
        $class = 'alert-info';
}

// AFTER
$class = match($type) {
    'success' => 'alert-success',
    'error' => 'alert-danger',
    default => 'alert-info'
};
```

**Nullsafe Operator:**
```php
// BEFORE
$name = isset($user) && isset($user->profile) ? $user->profile->name : null;

// AFTER
$name = $user?->profile?->name;
```

**Named Arguments:**
```php
// BEFORE
Element::mold('div', ['class' => 'container'], true, false, 'content');

// AFTER
Element::mold(
    tag: 'div',
    attributes: ['class' => 'container'],
    selfClosing: true,
    content: 'content'
);
```

**Priority:** 🟢 MEDIUM
**Effort:** Medium - Gradual adoption
**Benefits:**
- Cleaner code
- Better readability
- Modern PHP practices

---

### 2. Implement PSR Standards

**PSR-7: HTTP Message Interfaces**
```php
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class Controller {
    public function handle(ServerRequestInterface $request): ResponseInterface {
        // Standard HTTP handling
    }
}
```

**PSR-11: Container Interface**
```php
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface {
    public function get(string $id): mixed {
        // Dependency resolution
    }

    public function has(string $id): bool {
        // Check if service exists
    }
}
```

**PSR-15: HTTP Server Request Handlers**
```php
interface MiddlewareInterface {
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface;
}
```

**PSR-6/16: Caching**
```php
// Replace Memcache with PSR-6/16 cache
use Psr\SimpleCache\CacheInterface;

class Cache implements CacheInterface {
    public function get(string $key, mixed $default = null): mixed;
    public function set(string $key, mixed $value, int|DateInterval $ttl = null): bool;
    public function delete(string $key): bool;
}
```

**Priority:** 🟢 MEDIUM
**Effort:** High
**Benefits:**
- Interoperability
- Standard interfaces
- Framework agnostic components

---

### 3. Security Updates

**Password Hashing:**
```php
// BEFORE (assuming plain text or MD5)
$password = md5($input);

// AFTER
$password = password_hash($input, PASSWORD_ARGON2ID);

// Verification
if (password_verify($input, $stored_hash)) {
    // Authenticated
}
```

**Prepared Statements (Everywhere):**
```php
// BEFORE
$query = "SELECT * FROM users WHERE email = '$email'";

// AFTER
$stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
```

**CSRF Protection:**
```php
// Add CSRF token to forms
class Form extends Element {
    public function __construct() {
        parent::__construct('form');
        $this->addCsrfToken();
    }

    private function addCsrfToken(): void {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        $this->addChild(
            Element::mold('input')
                ->setAttribute('type', 'hidden')
                ->setAttribute('name', 'csrf_token')
                ->setAttribute('value', $token)
        );
    }
}
```

**Input Validation:**
```php
// Add validation layer
class Validator {
    public function validate(array $data, array $rules): array {
        // Validation logic
    }
}

// In controller
$validator = new Validator();
$errors = $validator->validate($_POST, [
    'email' => 'required|email',
    'password' => 'required|min:8'
]);
```

**Priority:** 🔴 HIGH
**Effort:** Medium
**Benefits:**
- Secure by default
- Prevent common vulnerabilities
- OWASP compliance

---

### 4. Replace Memcache with Modern Caching

**Current:** Memcache (old extension)

**Options:**

**Option A: Memcached (with 'd')**
```php
// Better maintained, more features
$cache = new Memcached();
$cache->addServer('localhost', 11211);
```

**Option B: Redis**
```php
// More features, persistent storage
$redis = new Redis();
$redis->connect('localhost', 6379);
```

**Option C: APCu (for single server)**
```php
// No external service needed
apcu_store('key', $value, $ttl);
$value = apcu_fetch('key');
```

**Recommendation:** Redis for production, APCu for development

**Priority:** 🟢 MEDIUM
**Effort:** Low
**Benefits:**
- Better performance
- More features
- Active maintenance

---

## Low Priority (Nice to Have)

### 1. Improve Template Engine

**Current State:** Custom .phtml with XML-like syntax

**Enhancements:**
- Template compilation for performance
- Better error messages with line numbers
- Modern syntax options (Blade-like)
- Component system

**Example:**
```html
<!-- Current -->
<import src="views/partials/header.phtml"/>
${user.name}

<!-- Enhanced -->
@include('partials.header')
{{ $user->name }}

@foreach($posts as $post)
    <article>{{ $post->title }}</article>
@endforeach
```

**Priority:** 🟢 LOW
**Effort:** High
**Decision:** Maintain backward compatibility

---

### 2. Add Testing Framework

**Current State:** No built-in testing

**Implementation:**
```bash
composer require --dev phpunit/phpunit ^10.0
```

**Example Test:**
```php
use PHPUnit\Framework\TestCase;

class ElementTest extends TestCase {
    public function testElementCreation(): void {
        $div = Element::mold('div')
            ->setAttribute('class', 'container');

        $this->assertStringContainsString(
            'class="container"',
            $div->render()
        );
    }
}
```

**Priority:** 🟢 LOW
**Effort:** Medium
**Benefits:**
- Prevent regressions
- Confidence in changes
- Documentation via tests

---

### 3. Developer Experience Improvements

**Debug Toolbar:**
```bash
composer require --dev symfony/debug-bundle
```

**Better Logging:**
```bash
composer require monolog/monolog
```

**IDE Metadata:**
- Generate PHPStorm metadata for IDE autocomplete
- Add stub files for magic methods
- Type hints in docblocks

**Priority:** 🟢 LOW
**Effort:** Low-Medium
**Benefits:**
- Faster development
- Better debugging
- Improved productivity

---

## Migration Strategy

### Phase 1: Critical Fixes (1-2 months)
- [ ] Fix removed PHP functions
- [ ] Add `#[AllowDynamicProperties]` or refactor
- [ ] Fix error reporting syntax
- [ ] Review MySQLi usage
- [ ] Update password hashing
- [ ] Implement prepared statements everywhere

**Goal:** PHP 8.1+ compatibility, basic security

### Phase 2: Foundation (2-3 months)
- [ ] Create composer.json
- [ ] Add namespaces to core classes
- [ ] Implement PSR-4 autoloading
- [ ] Add type declarations to core
- [ ] Replace old dependencies
- [ ] Set up CI/CD pipeline

**Goal:** Modern foundation, Composer integration

### Phase 3: Modernization (3-4 months)
- [ ] Implement PSR-7 HTTP messages
- [ ] Create PSR-11 container
- [ ] Modernize database layer (attributes)
- [ ] Replace Memcache with Redis
- [ ] Add middleware system (PSR-15)
- [ ] Implement caching interfaces (PSR-6/16)

**Goal:** PSR compliance, modern architecture

### Phase 4: Enhancement (2-3 months)
- [ ] Improve template engine
- [ ] Add testing framework
- [ ] Developer tools (debug bar)
- [ ] Documentation updates
- [ ] Migration guides
- [ ] Example applications

**Goal:** Better DX, comprehensive docs

### Phase 5: Optimization (Ongoing)
- [ ] Performance profiling
- [ ] Code optimization
- [ ] Caching strategies
- [ ] Database query optimization
- [ ] Asset pipeline improvements

**Goal:** Production-ready performance

---

## Backward Compatibility Strategy

### Option 1: Parallel Versions
- Maintain v1.x (legacy, security fixes only)
- Develop v2.x (modern, PHP 8.1+)
- Provide migration tools

### Option 2: Gradual Migration
- Maintain backward compatibility
- Add deprecation warnings
- Provide upgrade path
- Support both old and new syntax

### Option 3: Adapter Pattern
```php
// Legacy support
class LegacyModel {
    public static function fromXml(string $xml): Model {
        // Parse XML, create modern model
    }
}

// Both work
$user = Model::mold('user');           // Legacy
$user = new User();                     // Modern
```

**Recommendation:** Option 3 (Adapter Pattern) for smoother transition

---

## Breaking Changes to Consider

### Major Version (v2.0)

**Accept Breaking Changes:**
1. Remove XML model definitions (use attributes)
2. Require namespaces
3. Remove legacy library system (use Composer)
4. Modernize template syntax
5. PSR compliance mandatory

**Provide Migration Tools:**
- XML to Attribute converter
- Template syntax converter
- Automated refactoring scripts
- Comprehensive migration guide

---

## Success Criteria

### Technical
- [ ] PHP 8.1+ compatibility
- [ ] 90%+ test coverage
- [ ] PSR-2/12 code style compliance
- [ ] Security audit passed
- [ ] Performance benchmarks met

### Documentation
- [ ] API documentation complete
- [ ] Migration guide published
- [ ] Tutorials updated
- [ ] Example applications working
- [ ] Video walkthroughs created

### Community
- [ ] GitHub issues < 10 open
- [ ] Response time < 48 hours
- [ ] Community contributions enabled
- [ ] Changelog maintained
- [ ] Semantic versioning adopted

---

## Timeline Summary

| Phase | Duration | Key Deliverables |
|-------|----------|------------------|
| Phase 1 | 1-2 months | PHP 8.1 compatible, secure |
| Phase 2 | 2-3 months | Composer, namespaces, types |
| Phase 3 | 3-4 months | PSR compliance, modern DB |
| Phase 4 | 2-3 months | Testing, docs, DX tools |
| Phase 5 | Ongoing | Optimization, maintenance |

**Total Estimated Time:** 8-12 months to v2.0 release

---

## Recommendations

### Immediate Actions (This Week)
1. Set up version control branching strategy
2. Create PHP 8.1 compatibility branch
3. Fix critical PHP 8 issues
4. Set up basic CI/CD (GitHub Actions)
5. Document current API

### Short Term (This Month)
1. Create comprehensive test suite
2. Add type declarations to core
3. Implement PSR-4 autoloading
4. Create composer.json
5. Security audit and fixes

### Long Term (This Year)
1. Full PSR compliance
2. Modern database layer
3. Improved documentation
4. Community building
5. v2.0 release

---

## Resources Needed

### Development
- Senior PHP developer (familiar with PHP 8+)
- Security specialist (for audit)
- Technical writer (for documentation)

### Tools
- CI/CD platform (GitHub Actions - free)
- Static analysis (PHPStan, Psalm - free)
- Code coverage (Codecov - free for open source)

### Time Investment
- 10-15 hours/week for 8-12 months
- OR 2-3 months full-time development

---

## Conclusion

HeEPP was innovative for its time and many concepts remain valuable. The framework needs modernization but has a solid foundation. With systematic updates following this roadmap, HeEPP can become a modern, secure, and performant framework while maintaining its unique approach to web development.

**Key Strengths to Preserve:**
- Element-based composition system
- Dot notation data access
- Multi-project architecture
- Fragment/component system

**Areas Requiring Change:**
- Package management (→ Composer)
- Type safety (→ PHP 8+ types)
- Database layer (→ Attributes or modern ORM)
- Security practices (→ Modern standards)

The path forward is clear: modernize the infrastructure while preserving the innovative core concepts that make HeEPP unique.
