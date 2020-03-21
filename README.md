# STDOUT MVC API

This API was created to efficiently handle web requests into server responses using a MVC version where views and models are expected to be independent while controllers mediate between the two based on user request. Designed with modularity, efficiency and simplicity at its foundation, API is both object and event oriented: similar to JavaScript, it allows developers to bind logic that will be executed when predefined events are reached while handling.

API does nothing more than standard MVC logic, so in real life it expects a web framework to be built on top to add further features (eg: DB connectivity). In order to use it, following steps are required from developers:

- **[configuration](#configuration)**: setting up an XML file where this API is configured
- **[configuring shared variables](#configuring-shared-variables)**: extend [Lucinda\STDOUT\Attributes](https://github.com/aherne/php-servlets-api/tree/v3.0.0/src/Attributes.php) class to encapsulate variables specific to your project, to be shared between event listeners and controllers
- **[initialization](#initialization)**: instancing [Lucinda\STDOUT\FrontController](https://github.com/aherne/php-servlets-api/tree/v3.0.0/src/FrontController.php), a [Lucinda\STDOUT\Runnable](https://github.com/aherne/php-servlets-api/tree/v3.0.0/src/Runnable.php) able to handle requests into responses later on based on above two
- **[binding events](#binding-events)**: setting up [Lucinda\STDOUT\Runnable](https://github.com/aherne/php-servlets-api/tree/v3.0.0/src/Runnable.php) classes that will be instanced and *run* when predefined events are reached during handling process
- **[handling](#handling)**: calling *run* method @ [Lucinda\STDOUT\FrontController](https://github.com/aherne/php-servlets-api/tree/v3.0.0/src/FrontController.php)  to finally handle requests into responses, triggering events above (if any)

API is fully PSR-4 compliant, only requiring PHP7.1+ interpreter and SimpleXML extension. To quickly see how it works, check:

- **[installation](#installation)**: describes how to install API on your computer, in light of steps above
- **[reference guide](#reference-guide)**: describes all API classes, methods and fields relevant to developers
- **[unit tests](#unit-tests)**: API has 100% Unit Test coverage, using [UnitTest API](https://github.com/aherne/unit-testing) instead of PHPUnit for greater flexibility
- **[example](https://github.com/aherne/php-servlets-api/tree/v3.0.0/tests/FrontController.php)**: shows a deep example of API functionality based on [Lucinda\STDOUT\FrontController](https://github.com/aherne/php-servlets-api/tree/v3.0.0/src/FrontController.php) unit test

## Configuration

To configure this API you must have a XML with following tags inside:

- **[application](#application)**: (mandatory) configures your application on a general basis
- **[formats](#formats)**: (mandatory) configures formats in which your application is able to resolve responses to a handled HTTP request
- **[routes](#routes)**: (mandatory) configures routes that bind requested resources to controllers and views

### Application

Maximal syntax of this tag is:

```xml
<application default_format="..." default_page="..." version="...">
	<paths controllers="..." renderers="..." validators="..." views="..."/>
</application>
```

Where:

- **application**: (mandatory) holds settings to configure your application
    - *default_format*: (mandatory) defines default display format (extension) for your application. Must match a *name* attribute @ **[formats](#formats)**! Example: "html"
    - *default_page*: (mandatory) defines implicit page when your site is invoked with none (eg: http://www.example.com). Must match a *url* attribute @ **[routes](#routes)**! Example: "index"
    - *version*: (optional) defines your application version, to be used in versioning static resources. Example: "1.0.0"
    - **paths**: (optional) holds where core components used by API are located based on attributes:
        - *controllers*: (optional) holds folder in which user-defined controllers will be located. Each controller must be a [Lucinda\STDOUT\Controller](#abstract-class-controller) instance!  
        - *renderers*: (mandatory) holds folder in which user-defined view resolvers will be located. Each resolver must be a [Lucinda\STDOUT\ViewResolver](#abstract-class-viewresolver) instance!
        - *validators*: (optional) holds folder in which user-defined parameter validators will be located. Each validator must be a [Lucinda\STDOUT\EventListeners\Validators\ParameterValidator](#abstract-class-parametervalidator) instance!
        - *views*: (optional) holds folder in which user-defined views will be located (if HTML).

Tag example:

```xml
<application default_format="html" default_page="index" version="1.0.1">
	<paths controllers="application/controllers" renderers="application/renderers" validators="application/validators" views="application/views"/>
</application>
```

### Formats

Maximal syntax of this tag is:

```xml
<formats>
	<format name="..." content_type="..." class="..." {OPTIONS}/>
	...
</formats>
```
Where:

- **formats**: (mandatory) holds settings to resolve views based on response format (extension). Holds a child for each format supported:
    - **format**: (mandatory) configures a format-specific view resolver based on attributes:
        - *name*: (mandatory) defines display format (extension) handled by view resolver. Example: "html"
        - *content_type*: (mandatory) defines content type matching display format above. Example: "text/html"
        - *class*: (mandatory) name of user-defined class that will resolve views (including namespace or subfolder), found in folder defined by *resolvers* attribute of **paths** tag @ **[application](#application)**. Must be a [Lucinda\STDOUT\ViewResolver](#abstract-class-viewresolver) instance!
        - {OPTIONS}: a list of extra attributes necessary to configure respective resolver identified by *class* above            

Tag example:

```xml
<formats>
    <format name="html" content_type="text/html" class="ViewLanguageRenderer" charset="UTF-8"/>
    <format name="json" content_type="application/json" class="JsonRenderer" charset="UTF-8"/>
</formats>
```

### Routes

Maximal syntax of this tag is:

```xml
<routes>
    <route url="..." controller="..." view="..." format="..." method="...">
        <parameter name="..." validator="..." mandatory="..."/>
        ...
    </route>
    ...
</routes>
```

Where:

- **routes**: (mandatory) holds routing rules for handled requests
    - **route**: (optional) holds routing rules specific to a requested URI based on attributes:
        - *url*: (mandatory) requested requested resource url without trailing slash. Can be an exact url (eg: *foo/bar*) or a url pattern (eg: *user/(id)*). If pattern is used, each variable must be named and enclosed in parenthesis!
        - *controller*: (optional) holds user-defined controller (including namespace or subfolder) that will mitigate requests and responses based on models, found in folder defined by *controllers* attribute of **paths** tag @ **[application](#application)**. Must be a [Lucinda\STDOUT\Controller](#abstract-class-controller) instance!
        - *view*: (optional) holds user-defined template file that holds the recipe of response for request. Example: "homepage"
        - *format*: (optional) holds response format, if different from *default_format* @ [application](#application). Must match a *name* attribute @ **[formats](#formats)**! Example: "json"
        - *method*: (optional) holds single HTTP method by which resource MUST be requested with. If request comes with a different method, a [Lucinda\STDOUT\MethodNotAllowedException](https://github.com/aherne/php-servlets-api/tree/v3.0.0/src/MethodNotAllowedException.php) is thrown!

Each **route** tag can hold one or more rules to validate values of request and path parameters that came along with request. Each parameter corresponds to a **parameter** tag, where validation is configurable based on attributes:

- *name*: (mandatory) name of request or path parameter you want to validate. Examples:
    - *foo*, if request was GET and came with query-string *?foo=bar*
    - *id*, if route url is *user/(id)*
- *validator*: (mandatory) holds user-defined class (including namespace or subfolder) that validates value of parameter, found in folder defined by *validators* attribute of **paths** tag @ **[application](#application)**. Must be a [Lucinda\STDOUT\EventListeners\Validators\ParameterValidator](#abstract-class-parametervalidator) instance!
- *mandatory*: (optional) holds whether or not parameter is mandatory (value can be 0 or 1). If none, mandatory (1) is assumed!

**^ If parameter names collide, path parameters take precedence over request parameters!**

Tag example:

```xml
<routes>
    <route url="index" controller="HomepageController" view="index"/>
    <route url="user/(id)" controller="UserInfoController" view="user-info" method="GET">
        <parameter name="id" validator="UserNameValidator"/>
    </route>
</routes>
```

## Configuring Shared Variables

API allows event listeners to set variables that are going to be made available to subsequent event listeners and controllers. For each variable there is a:

- *setter*: to be ran once by a event listener
- *getter*: to be ran by subsequent event listeners and controllers

API comes with [Lucinda\STDOUT\Attributes](https://github.com/aherne/php-servlets-api/tree/v3.0.0/src/Attributes.php), which holds the foundation every site must extend in order to set up its own variables. Class comes with following generic methods:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| __construct | string $eventsFolder | void | Sets folder in which user-defined event listeners are located |
| getEventsFolder | void | string | Gets folder in which user-defined event listeners are located |

While handling request to response, it needs to add its own [Lucinda\STDOUT\EventListeners\RequestValidator](https://github.com/aherne/php-servlets-api/tree/v3.0.0/src/EventListeners/RequestValidator.php) in order to bind route requested to a [route](#routes) XML tag. Results of this binding are saved into [Lucinda\STDOUT\Attributes](https://github.com/aherne/php-servlets-api/tree/v3.0.0/src/Attributes.php) via setters and made available via following getters:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| getRequestedPage | void | string | Gets value of *url* attribute of matching [route](#routes) XML tag found either explicitly (via *url* attribute @ [route](#routes)) or implicitly (via *default_page* attribute @ [application](#application)) |
| getRequestedResponseFormat | void | string | Gets value of *name* attribute of matching [format](#formats) XML tag found either explicitly (via *format* attribute @ [route](#routes)) or implicitly (via *default_format* attribute @ [application](#application)) |
| getPathParameters | void | array | Gets all path parameters detected from parameterized *url* attribute of matching [route](#routes) XML tag |
| getPathParameters | string $name | string | Gets value of a path parameter detected by its name. Returns NULL if not existing! |
| getValidParameters | void | array | Gets all parameter validation results by parameter name and validation result |
| getValidParameters | string $name | string | Gets parameter validation result by parameter name. Returns NULL if not existing! |

Unless your site is extremely simple, it will require developers to extend this class and add further variables, for whom setters and getters must be defined!

## Initialization

Now that developers have finished setting up XML that configures the API, they are finally able to initialize it by instantiating [Lucinda\STDOUT\FrontController](https://github.com/aherne/php-servlets-api/tree/v3.0.0/src/FrontController.php).

Apart of method *run* required by [Lucinda\STDOUT\Runnable](https://github.com/aherne/php-servlets-api/tree/v3.0.0/src/Runnable.php) interface it implements, class comes with following public method:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| __construct | string $documentDescriptor, [Lucinda\STDOUT\Attributes](https://github.com/aherne/php-servlets-api/tree/v3.0.0/src/Attributes.php) $attributes | void | Records user defined XML and attributes for later handling |

Where:

- *$documentDescriptor*: relative location of XML [configuration](#configuration) file. Example: "configuration.xml"
- *$attributes*: see **[configuring shared variables](#configuring-shared-variables)**.

## Binding Events

As mentioned above, API allows developers to bind listeners to handling lifecycle events. Each event  type corresponds to a abstract [Lucinda\STDOUT\Runnable](https://github.com/aherne/php-servlets-api/tree/v3.0.0/src/Runnable.php) class:

| Type | Class | Description |
| --- | --- | --- |
| start | [Lucinda\STDOUT\EventListeners\Start](https://github.com/aherne/php-servlets-api/tree/v3.0.0/src/EventListeners/Start.php) | Ran before [configuration](#configuration) XML is read. *Example*: tracking when application started handling a request. |
| application | [Lucinda\STDOUT\EventListeners\Application](https://github.com/aherne/php-servlets-api/tree/v3.0.0/src/EventListeners/Application.php) | Ran after [configuration](#configuration) XML is read. *Example*: setting database connection credentials based on XML. |
| request | [Lucinda\STDOUT\EventListeners\Request](https://github.com/aherne/php-servlets-api/tree/v3.0.0/src/EventListeners/Request.php) | Ran after user request is read based on $_SERVER. *Example*: implementing authentication and authorization. |
| session | [Lucinda\STDOUT\EventListeners\Session](https://github.com/aherne/php-servlets-api/tree/v3.0.0/src/EventListeners/Session.php) | Ran after session data is read based on $_SESSION. *Example*: starting session and customizing handler. |
| cookies | [Lucinda\STDOUT\EventListeners\Cookies](https://github.com/aherne/php-servlets-api/tree/v3.0.0/src/EventListeners/Cookies.php) | Ran after session data is read based on $_COOKIE. *Example*: validating cookies data. |
| response | [Lucinda\STDOUT\EventListeners\Response](https://github.com/aherne/php-servlets-api/tree/v3.0.0/src/EventListeners/Response.php) | Ran after response body is compiled but before it's rendered. *Example*: adding support for HTTP caching. |
| end | [Lucinda\STDOUT\EventListeners\End](https://github.com/aherne/php-servlets-api/tree/v3.0.0/src/EventListeners/End.php) | Ran after response was rendered back to caller. *Example*: tracking duration of handling a request.  |

Listeners must extend matching event class and implement required *run* method holding the logic that will execute when event is triggered. It is required for them to be registered BEFORE **[handling](#handling)** via [Lucinda\STDOUT\FrontController](https://github.com/aherne/php-servlets-api/tree/v3.0.0/src/FrontController.php) method:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| addEventListener | string $type, string $className | void | Binds a listener to an event type |

Where:

- *$type*: event type (see above) encapsulated by enum [Lucinda\STDOUT\EventType](https://github.com/aherne/php-servlets-api/tree/v3.0.0/src/EventType.php)
- *$className*: listener *class name*, including namespace and subfolder, found in *folder* defined when [Lucinda\STDOUT\Attributes](https://github.com/aherne/php-servlets-api/tree/v3.0.0/src/Attributes.php) was instanced.

To better understand how *folder* and *$className* above play together in locating event listener later on, let's take a look at table below:

| folder | $className | File Loaded | Class Instanced |
| --- | --- | --- | --- |
| application/events | TestEvent | application/events/TestEvent.php | TestEvent |
| application/events | foo/TestEvent | application/events/foo/TestEvent.php | TestEvent |
| application/events | \Foo\TestEvent | application/events/TestEvent.php | \Foo\TestEvent |
| application/events | foo/\Bar\TestEvent | application/events/foo/TestEvent.php | \Bar\TestEvent |

## Handling

Once above steps are done, developers are finally able to handle requests into responses via *run* method of [Lucinda\STDOUT\FrontController](https://github.com/aherne/php-servlets-api/tree/v3.0.0/src/FrontController.php), which:

- detects [Lucinda\STDOUT\EventListeners\Start](https://github.com/aherne/php-servlets-api/tree/v3.0.0/src/EventListeners/Start.php) listeners and executes them in order they were registered
- encapsulates [configuration](#configuration) XML file into [Lucinda\STDOUT\Application](#class-application) object
- detects [Lucinda\STDOUT\EventListeners\Application](https://github.com/aherne/php-servlets-api/tree/v3.0.0/src/EventListeners/Application.php) listeners and executes them in order they were registered
- encapsulates request information based on superglobals into [Lucinda\STDOUT\Request](#class-request) object
- detects [Lucinda\STDOUT\EventListeners\Request](https://github.com/aherne/php-servlets-api/tree/v3.0.0/src/EventListeners/Request.php) listeners and executes them in order they were registered
- encapsulates session variables based on superglobals into [Lucinda\STDOUT\Session](#class-session) object
- detects [Lucinda\STDOUT\EventListeners\Session](https://github.com/aherne/php-servlets-api/tree/v3.0.0/src/EventListeners/Session.php) listeners and executes them in order they were registered
- encapsulates cookie variables based on superglobals into [Lucinda\STDOUT\Cookie](#class-cookie) object
- detects [Lucinda\STDOUT\EventListeners\Cookies](https://github.com/aherne/php-servlets-api/tree/v3.0.0/src/EventListeners/Cookies.php) listeners and executes them in order they were registered
- initializes empty [Lucinda\STDOUT\Response](#class-response) based on information detected above from request or XML
- locates [Lucinda\STDOUT\Controller](#abstract-class-controller) based on information already detected and, if found, executes it in order to bind models to views
- locates [Lucinda\STDOUT\ViewResolver](#abstract-class-viewresolver) based on information already detected and executes it in order to feed response body based on view
- detects [Lucinda\STDOUT\EventListeners\Response](https://github.com/aherne/php-servlets-api/tree/v3.0.0/src/EventListeners/Response.php) listeners and executes them in order they were registered
- sends [Lucinda\STDOUT\Response](#class-response) back to caller, containing headers and body
- detects [Lucinda\STDOUT\EventListeners\End](https://github.com/aherne/php-servlets-api/tree/v3.0.0/src/EventListeners/End.php) listeners and executes them in order they were registered

All components that are in developers' responsibility ([Lucinda\STDOUT\Controller](#abstract-class-controller), [Lucinda\STDOUT\ViewResolver](#abstract-class-viewresolver), along with event listeners themselves, implement [Lucinda\STDOUT\Runnable](https://github.com/aherne/php-servlets-api/tree/v3.0.0/src/Runnable.php) interface, which only comes with a single method:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| run | void | void | Executes component's logic |

## Installation

First choose a folder, then write this command there using console:

```console
composer require lucinda/mvc
```

Rename folder above to DESTINATION_FOLDER then create an .htaccess file there with following content:

```console
RewriteEngine on
Options -Indexes
ErrorDocument 404 default
RewriteCond %{REQUEST_URI} !^/public
RewriteRule ^(.*)$ index.php
```

Then create a *configuration.xml* file holding configuration settings (see [configuration](#configuration) above) and a *index.php* file (see [initialization](#initialization) above) in project root with following code:

```php
require(__DIR__."/vendor/autoload.php");

$controller = new Lucinda\STDOUT\FrontController("configuration.xml", new Lucinda\STDOUT\Attributes("application/events"));
// TODO: add event listeners here
$controller->run();
```

## Reference Guide

These classes are fully implemented by API:

- [Lucinda\STDOUT\Application](#class-application): reads [configuration](#configuration) XML file and encapsulates information inside
- [Lucinda\STDOUT\Request](#class-request): encapsulates user request based on information in superglobals: $\_SERVER, $\_POST, $\_GET
    - [Lucinda\STDOUT\Request\Client](#class-request-client): encapsulates client information detected from request
    - [Lucinda\STDOUT\Request\Server](#class-request-server): encapsulates server information detected from request
    - [Lucinda\STDOUT\Request\URI](#class-request-uri): encapsulates uri information detected from request
    - [Lucinda\STDOUT\Request\UploadedFiles\File](#class-request-uploadedfile): encapsulates information about an uploaded file
- [Lucinda\STDOUT\Session](#class-session): encapsulates session data based on information in superglobal: $\_SESSION
- [Lucinda\STDOUT\Cookie](#class-cookie): encapsulates session data based on information in superglobal: $\_COOKIE
- [Lucinda\STDOUT\Response](#class-response): encapsulates response to send back to caller
    - [Lucinda\STDOUT\Response\Status](#class-status): encapsulates response HTTP status
    - [Lucinda\STDOUT\Response\View](#class-view): encapsulates view template and data that will be bound into a response body

Apart of classes mentioned in **[binding events](#binding-events)**, following abstract classes require to be extended by developers in order to gain an ability:

- [Lucinda\STDOUT\Controller](#abstract-class-controller): encapsulates binding [Lucinda\STDOUT\Request](#class-request) to [Lucinda\STDOUT\Response](#class-response) based on user request and XML info
- [Lucinda\STDOUT\ViewResolver](#abstract-class-viewresolver): encapsulates conversion of [Lucinda\STDOUT\Response\View](#class-view) into a [Lucinda\STDOUT\Response](#class-response) body

### Class Application

Class [Lucinda\STDOUT\Application](https://github.com/aherne/php-servlets-api/tree/v3.0.0/src/Application.php) encapsulates information detected from XML and defines following public methods relevant to developers:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| getVersion | void | string | Gets application version based on *version* attribute @ [application](#application) XML tag |
| getTag | string $name | [\SimpleXMLElement](https://www.php.net/manual/en/class.simplexmlelement.php) | Gets a pointer to a custom tag in XML root |

### Class Request

Class [Lucinda\STDOUT\Request](https://github.com/aherne/php-servlets-api/tree/v3.0.0/src/Request.php) encapsulates information detected about user request based on superglobals and defines following public methods relevant to developers:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| getClient | void | [Lucinda\STDOUT\Request\Client](#class-request-client) | Gets client information detected from request. |
| getInputStream | void | string | Gets access to input stream for binary requests. |
| getMethod | void | string | Gets request HTTP method. |
| getProtocol | void | string | Gets request protocol (http or https)
| getServer | void | [Lucinda\STDOUT\Request\Server](#class-request-server) | Gets server information detected from request. ||
| getUri | void | [Lucinda\STDOUT\Request\URI](#class-request-uri) | Gets path information detected from request. |
| headers | void | array | Gets all request headers received from client by standard ISO name |
| headers | string $name | string | Gets value of request header by name or NULL if not found. |
| parameters | void | array | Gets all request parameters received from client matching current request method. |
| parameters | string $name | mixed | Gets value of request parameter by name or NULL if not found. |
| uploadedFiles | void | array | Gets all uploaded files received from client, each encapsulated as [Lucinda\STDOUT\Request\UploadedFiles\File](#class-request-uploadedfile) |
| uploadedFiles | string $name | mixed | Gets [Lucinda\STDOUT\Request\UploadedFiles\File](#class-request-uploadedfile) received by name or NULL if not found. |

#### How are uploaded files processed

Unlike $\_FILES superglobal, like parameters sent by $\_GET or $\_POST, API preserves structure sent in form, so:

```html
<input type="file" name="asd[fgh]"/>
```

Once posted, *uploadedFiles* method will return:

```php
[
  "asd"=>["fgh"=>object]
]
```

Where *object* is a [Lucinda\STDOUT\Request\UploadedFiles\File](#class-request-uploadedfile)!  
