# STDOUT MVC API

`lucinda/mvc` is a PHP 8.1+ library that turns an HTTP request into an MVC response using XML configuration plus Lucinda MVC contracts.

The current package is centered around one runtime entry point, [`FrontController`](src/FrontController.php), and a small set of HTTP-specific services:

- XML-backed application metadata via [`Application`](src/Application.php)
- immutable request inspection via [`Request`](src/Request.php)
- writable session and cookie wrappers via [`Session`](src/Session.php) and [`Cookies`](src/Cookies.php)
- route, method, format, and parameter validation via [`ValidatedRequest`](src/Validators/ValidatedRequest.php)

The old README described a much larger API surface. This version documents only what exists in `src/` today and how it is actually exercised in `tests/`.

## Installation

```bash
composer require lucinda/mvc
```

Requirements:

- PHP `^8.1`
- `ext-simplexml`
- [`lucinda/abstract_mvc`](https://packagist.org/packages/lucinda/abstract_mvc)

For local verification in this repository:

```bash
composer install
php test.php
```

## Runtime Flow

[`FrontController`](src/FrontController.php) coordinates the whole request lifecycle:

1. runs `START` event listeners from the provided `EventScheduler`
2. loads XML into [`Application`](src/Application.php)
3. builds [`Request`](src/Request.php), [`Session`](src/Session.php), and [`Cookies`](src/Cookies.php)
4. validates route, method, format, path parameters, and request parameters into [`ValidatedRequest`](src/Validators/ValidatedRequest.php)
5. runs `REQUEST` event listeners
6. resolves the response content type from the selected resolver
7. runs the route controller, if one is configured
8. resolves the returned `View`, if any, through the configured view resolver
9. applies `RESPONSE` transformers for body, status, and headers
10. sends the HTTP response
11. runs `END` event listeners

If a `Lucinda\MVC\TerminationException` is thrown, the embedded response is sent immediately.

## Minimal Integration

The constructor signature is:

```php
public function __construct(string $documentDescriptor, EventScheduler $eventScheduler)
```

Example:

```php
use Lucinda\MVC\EventScheduler;
use Lucinda\MVC\EventType;
use Lucinda\STDOUT\FrontController;

$scheduler = new EventScheduler();
$scheduler->add(EventType::START, App\EventListeners\BootListener::class);
$scheduler->add(EventType::RESPONSE, App\Response\CacheHeaders::class);

$frontController = new FrontController(__DIR__ . "/stdout.xml", $scheduler);
$frontController->run();
```

## XML Configuration

[`Application`](src/Application.php) extends the base Lucinda MVC application and reads:

- `application`
- `resolvers`
- `routes`
- optional `session`
- optional `cookies`

The test fixtures show the expected split-file setup:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xml>
<xml>
  <application ref="config/application"/>
  <resolvers ref="config/resolvers"/>
  <routes ref="config/routes"/>
  <session ref="config/session"/>
  <cookies ref="config/cookies"/>
</xml>
```

### Application

The `application` tag comes from `lucinda/abstract_mvc`. In this package it is used directly by routing and view detection. The fixture-backed example is:

```xml
<application
  default_format="html"
  default_route="users"
  views_folder="app/views"
  views_extension="phtml"
  version="1.0.0"/>
```

Important constraints enforced by runtime code:

- an empty request path falls back to `default_route`
- the selected format falls back to `default_format` unless the route overrides it

### Resolvers

Each resolver format must map to a view resolver class and a content type. [`ContentTypeDetector`](src/Service/ContentTypeDetector.php) builds the final `Content-Type` header from this data and appends `charset` when present.

```xml
<resolvers>
  <resolver
    format="html"
    content_type="text/html"
    class="App\Resolvers\PhtmlResolver"
    charset="UTF-8"/>
  <resolver
    format="json"
    content_type="application/json"
    class="App\Resolvers\JsonResolver"
    charset="UTF-8"/>
</resolvers>
```

If a route resolves to a format that has no matching resolver, [`FormatValidator`](src/Validators/FormatValidator.php) throws.

### Routes

Routes connect request paths to controllers, optional views, formats, and parameter validators:

```xml
<routes>
  <route
    id="users"
    controller="App\Controllers\UsersController"
    view="hello"
    format="html"
    method="GET"/>

  <route
    id="user/(name)"
    controller="App\Controllers\UserJsonController"
    format="json"
    method="GET">
    <parameter
      name="name"
      validator="App\RouteValidators\NameLengthValidator"/>
    <parameter
      name="page"
      validator="App\RouteValidators\PositiveIntegerValidator"
      mandatory="0"/>
  </route>
</routes>
```

Behavior enforced by [`RouteValidator`](src/Validators/RouteValidator.php):

- exact route matches are tried first
- placeholders like `user/(name)` are matched with regex extraction
- path parameters override request parameters with the same name
- unsupported HTTP methods raise [`MethodNotAllowedException`](src/MethodNotAllowedException.php)
- unmatched routes raise [`PathNotFoundException`](src/PathNotFoundException.php)
- missing or failed validated parameters raise [`ValidationFailedException`](src/Validators/ValidationFailedException.php)

### Session

[`Session`](src/Session.php) optionally applies PHP session ini settings from XML and can register a custom `SessionHandlerInterface`.

```xml
<session
  save_path="/tmp"
  name="TESTSESSID"
  expired_time="60"
  expired_on_close="120"
  https_only="1"
  headers_only="1"
  auto_start="0"/>
```

Supported attributes are parsed by [`SessionOptions`](src/XmlTags/SessionOptions.php), including `referrer_check` and `handler`.

### Cookies

[`Cookies`](src/Cookies.php) uses XML defaults when creating cookies:

```xml
<cookies path="/" domain="example.com" https_only="1" headers_only="1"/>
```

If no cookie options are configured, `Cookies::set()` falls back to path `/`.

## Extension Points

### Controllers

Controllers are configured per route. If the instantiated controller implements `Lucinda\MVC\Controller\ViewAware`, its `run()` result is used as the view for resolver execution.

Fixture-backed example:

```php
use Lucinda\MVC\Controller\ViewAware;
use Lucinda\MVC\Response\View;

final class UsersController implements ViewAware
{
    public function run(): View
    {
        return new View(["message" => "Hello from controller"], "hello");
    }
}
```

Controllers can also receive resolved facets through constructor injection. The tests include a controller that consumes [`ValidatedRequest`](src/Validators/ValidatedRequest.php) directly.

### View Resolvers

Resolvers implement `Lucinda\MVC\Response\ViewResolver`.

```php
use Lucinda\MVC\Response\View;
use Lucinda\MVC\Response\ViewResolver;

final class JsonResolver implements ViewResolver
{
    public function resolve(View $view): string
    {
        return json_encode($view->getData(), JSON_THROW_ON_ERROR);
    }
}
```

### Event Listeners and Response Transformers

`FrontController` delegates lifecycle hooks to `EventScheduler`.

- standard events are loaded via `EventType::START`, `APPLICATION`, `REQUEST`, `RESPONSE`, and `END`
- listeners are instantiated through Lucinda MVC reflection injection
- listeners implementing `Faceted` or `MultiFaceted` can publish additional injectable facets
- `RESPONSE` listeners may also implement body, status, or headers transformer contracts

The tests include a response body transformer that appends `!` to the generated output.

### Parameter Validators

Route parameter validators must implement [`Lucinda\STDOUT\Validators\ParameterValidator`](src/Validators/ParameterValidator.php):

```php
interface ParameterValidator
{
    public function validate(mixed $value): mixed;
}
```

Return `null` to fail validation. Any non-null value is stored in [`ValidatedRequest`](src/Validators/ValidatedRequest.php) and made available to downstream code.

## Runtime Objects

### Request

[`Request`](src/Request.php) captures:

- client information via [`Request\Client`](src/Request/Client.php)
- server information via [`Request\Server`](src/Request/Server.php)
- parsed URI information via [`Request\URI`](src/Request/URI.php)
- request method via [`Request\Method`](src/Request/Method.php)
- protocol via [`Request\Protocol`](src/Request/Protocol.php)
- normalized request headers
- parameters from `$_GET`, `$_POST`, or `php://input` for `PUT` and `DELETE`
- uploaded files wrapped through [`Request\UploadedFiles`](src/Request/UploadedFiles.php)

If `$_SERVER["REQUEST_URI"]` is missing, construction fails with a configuration exception.

### ValidatedRequest

[`ValidatedRequest`](src/Validators/ValidatedRequest.php) is the post-routing view of the request. It exposes:

- `getRoute()`
- `getFormat()`
- `getPathParameters()`
- `getValidationResults()`

This is the object to inject when controllers need route-derived state rather than raw HTTP input.

### Session and Cookies

[`Session`](src/Session.php) wraps common session operations:

- `start()`, `isStarted()`
- `set()`, `get()`, `contains()`, `remove()`
- `destroy()`, `abort()`, `commit()`
- `cookie()` for access to [`Session\Cookie`](src/Session/Cookie.php)

[`Cookies`](src/Cookies.php) wraps `set()`, `get()`, `contains()`, and `remove()` around `$_COOKIE` and `setcookie()`.

## Testing

This repository uses `lucinda/unit-testing`. The main test entry point is [`test.php`](test.php).

```bash
php test.php
```

Coverage in `tests/` currently exercises:

- request parsing, uploaded files, session, and cookies
- XML tag parsing and route/resolver metadata
- route, method, format, and parameter validation
- full front controller flow with events, controller execution, resolver execution, and response transformation

## Repository References

- source: [`src`](src)
- tests: [`tests`](tests)
- runnable test bootstrap: [`test.php`](test.php)
- fixture-based example app config: [`tests/fixtures/root.xml`](tests/fixtures/root.xml)
