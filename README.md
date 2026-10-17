# STDOUT MVC API

STDOUT MVC API is a **front-controller runtime** for PHP applications. It captures the current HTTP request, matches it to XML-defined routes, validates its method and parameters, executes lifecycle listeners and a controller, resolves a view, and emits an HTTP response.

Its goals are to:

- keep routing, controller selection, rendering, and response transformation in one predictable lifecycle;
- expose request, session, cookies, application metadata, and validated routing state as injectable facets;
- support conventional controllers and HTTP-method-driven RESTful controllers;
- select application-specific controllers, resolvers, listeners, and validators through XML;
- build on `lucinda/abstract_mvc` while supplying the HTTP-specific request and response boundary.

Install it with Composer:

```bash
composer require lucinda/mvc
```

It requires PHP 8.1 or newer and SimpleXML.

## Basic input and output

The application supplies:

- a root XML document containing or referencing `application`, `resolvers`, and `routes` elements;
- optional `session` and `cookies` elements;
- an `EventScheduler` containing any lifecycle listeners;
- the standard PHP request superglobals.

`FrontController` owns the request lifecycle. Construct it with the root XML path and scheduler, then call `run()`:

```php
<?php

require __DIR__."/vendor/autoload.php";

use Lucinda\MVC\EventScheduler;
use Lucinda\MVC\EventType;
use Lucinda\STDOUT\FrontController;

$events = new EventScheduler();
$events->add(EventType::START, App\EventListeners\Bootstrap::class);
$events->add(EventType::RESPONSE, App\Response\SecurityHeaders::class);

$frontController = new FrontController(__DIR__."/application.xml", $events);
$frontController->run();
```

For each call, `run()` performs these stages in order:

1. executes `START` listeners;
2. parses the application XML and executes `APPLICATION` listeners;
3. captures `Request`, creates `Session` and `Cookies`, and validates the route into `ValidatedRequest`;
4. executes `REQUEST` listeners;
5. selects the response resolver and Content-Type;
6. runs the configured controller and resolves its view;
7. applies `RESPONSE` body, status, and header transformers;
8. emits the response and executes `END` listeners.

If a `Lucinda\MVC\TerminationException` is thrown, its embedded response is emitted immediately and the remaining normal stages are skipped.

## XML configuration

The root document can hold each section directly or reference separate XML files. The fixture-backed split form is:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xml>
<xml>
    <application ref="config/application" />
    <resolvers ref="config/resolvers" />
    <routes ref="config/routes" />
    <session ref="config/session" />
    <cookies ref="config/cookies" />
</xml>
```

Paths in `ref` are resolved by the XML reader supplied by `lucinda/abstract_mvc`.

### Application and resolvers

The `application` element defines the default route and format, view directory, template extension, and application version:

```xml
<application
    default_format="html"
    default_route="users"
    views_folder="app/views"
    views_extension="phtml"
    version="1.0.0"
/>
```

An empty request path selects `default_route`. A route without its own `format` uses `default_format`.

Every selected format must have a resolver. HTTP resolvers add required `content_type` and optional `charset` attributes to the base resolver definition:

```xml
<resolvers>
    <resolver
        format="html"
        class="App\Resolvers\PhtmlResolver"
        content_type="text/html"
        charset="UTF-8"
    />
    <resolver
        format="json"
        class="App\Resolvers\JsonResolver"
        content_type="application/json"
        charset="UTF-8"
    />
</resolvers>
```

Resolver classes implement `Lucinda\MVC\Response\ViewResolver`. The media type and optional charset become the response `Content-Type` header.

### Routes and parameters

Routes select controllers, optional views and formats, an optional HTTP method, and optional parameter validators:

```xml
<routes>
    <route
        id="users"
        controller="App\Controllers\UsersController"
        view="users"
        format="html"
        method="GET"
    />

    <route
        id="user/(name)"
        controller="App\Controllers\UserController"
        format="json"
        method="GET"
    >
        <parameter
            name="name"
            validator="App\Validators\UserName"
        />
        <parameter
            name="page"
            validator="App\Validators\PositiveInteger"
            mandatory="0"
        />
    </route>
</routes>
```

Exact route IDs are matched first. Placeholders such as `(name)` match one path segment and expose the captured value through `ValidatedRequest::getPathParameters()`. Path values override request parameters with the same name before validation.

Parameter validator classes implement `Lucinda\STDOUT\Validators\ParameterValidator`. They return the validated or transformed value, and return `null` to reject it. Parameters are mandatory unless `mandatory="0"` is configured.

### Sessions and cookies

The optional `session` element maps to PHP session configuration:

```xml
<session
    save_path="/tmp"
    name="APPSESSID"
    expired_time="1800"
    expired_on_close="120"
    https_only="1"
    headers_only="1"
    referrer_check="example.com"
    auto_start="0"
    handler="App\Session\Handler"
/>
```

`expired_time` and `expired_on_close` must be positive when present. Boolean values are `0` or `1`. A custom handler is constructed without arguments and registered through `session_set_save_handler()`.

The optional `cookies` element supplies defaults used by `Cookies::set()`:

```xml
<cookies
    path="/"
    domain="example.com"
    https_only="1"
    headers_only="1"
/>
```

Without cookie configuration, created cookies use path `/` and PHP's remaining defaults.

## Controllers and views

Controller classes are constructed through facet-aware dependency injection. They may request application information, `Request`, `ValidatedRequest`, `Session`, `Cookies`, or facets published by earlier listeners.

A conventional view-aware controller implements `Lucinda\MVC\Controller\ViewAware`:

```php
<?php

namespace App\Controllers;

use Lucinda\MVC\Controller\ViewAware;
use Lucinda\MVC\Response\View;

final class UsersController implements ViewAware
{
    public function run(): View
    {
        return new View(["message" => "Users"], "users");
    }
}
```

A RESTful controller implements `Lucinda\STDOUT\Restful`. `RestfulWrapper` calls the public method named after the request method; PHP method names are case-insensitive, so conventional lowercase handler names work:

```php
<?php

namespace App\Controllers;

use Lucinda\MVC\Response\View;
use Lucinda\STDOUT\Restful;
use Lucinda\STDOUT\Validators\ValidatedRequest;

final class UserController implements Restful
{
    public function __construct(private ValidatedRequest $request)
    {
    }

    public function get(): View
    {
        return new View([
            "name" => $this->request->getPathParameters()["name"],
        ]);
    }

    public function delete(): ?View
    {
        return null;
    }
}
```

The selected RESTful handler must be public, require no arguments, and return `Lucinda\MVC\Response\View` or `null`. Constructor injection remains available for dependencies.

Controllers without a view contract are run for side effects. When no controller-provided view exists, the base MVC view detector may use the route's configured `view`.

## Request and validated state

`Request` captures the current superglobals once during construction. It exposes:

- client and receiving-server network information;
- application-relative URI, raw query string, and parsed query parameters;
- normalized `HTTP_*` request headers;
- request method and HTTP/HTTPS protocol enums;
- method-specific parameters from GET, POST, or parsed PUT/DELETE input;
- multipart uploads as `Request\UploadedFiles\File` objects.

`ValidatedRequest` is the post-routing view of the request. It exposes the matched route ID, selected response format, raw path parameters, and successful custom validation results.

`Session` and `Cookies` are mutable facades over PHP's `$_SESSION` and `$_COOKIE` state. Cookie writes are mirrored into `$_COOKIE` so later code in the same request observes them.

## Lifecycle extensions

The supplied `EventScheduler` controls listener order for `START`, `APPLICATION`, `REQUEST`, `RESPONSE`, and `END`.

Listeners implementing `Lucinda\MVC\EventListener\Faceted` publish one injectable facet. `MultiFaceted` listeners publish several named facets. Other listeners run only for side effects.

During `RESPONSE`, listeners can implement one or more response transformer contracts:

| Contract | Effect |
| --- | --- |
| `Response\Transformer\Body` | Transforms the response body. |
| `Response\Transformer\Status` | Replaces the HTTP status. |
| `Response\Transformer\Headers` | Adds or replaces response headers. |

Transformations are applied in scheduler order.

## Exceptions

Configuration, route matching, parameter validation, RESTful dispatch, controller execution, and rendering can fail before a response is emitted:

| Exception | Meaning |
| --- | --- |
| `Lucinda\MVC\ConfigurationException` | Required root application or request configuration is unavailable or invalid. |
| `Lucinda\MVC\XmlReader\Exception` | An XML value, route method, resolver, session option, cookie option, or validator declaration is invalid. |
| `PathNotFoundException` | No exact or parameterized route matches the requested path. |
| `MethodNotAllowedException` | A route rejects the request method, or a RESTful controller has no callable handler for it. |
| `Validators\ValidationFailedException` | A mandatory parameter is absent or its validator returns `null`. |
| `Request\UploadedFiles\Exception` | PHP reports a multipart upload failure. |
| `LogicException` | A RESTful handler requires arguments or returns a value other than `View` or `null`. |

Applications should map these exceptions at their HTTP boundary according to their own error response policy. A `TerminationException` is different: `FrontController` catches it and emits its attached response.

## Development

Install dependencies and run the test suite:

```bash
composer install
php test.php
```

From Windows, run PHP through WSL:

```powershell
wsl -d Ubuntu --cd /home/aherne/framework/mvc_http php test.php
```

## License

MIT.
