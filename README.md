# STDOUT MVC API

Table of contents:

- [About](#about)
- [Configuration](#configuration)
- [Binding Points](#binding-points)
- [Execution](#execution)
    - [Initialization](#initialization)
    - [Binding Events](#binding-events)
    - [Configuring Shared Variables](#configuring-shared-variables)
    - [Handling](#handling)
- [Installation](#installation)
- [Unit Tests](#unit-tests)
- [Reference Guide](#reference-guide)
- [Specifications](#specifications)
    - [How Is Response Format Detected](#how-is-response-format-detected)
    - [How Are View Resolvers Located](#how-are-view-resolvers-located)
    - [How Is Route Detected](#how-is-route-detected)
    - [How Are Controllers Located](#how-are-controllers-located)
    - [How Are Parameter Validators Working](#how-are-parameter-validators-working)
    - [How to Set Cookies Path and Domain](#how-to-set-cookies-path-and-domain)
    - [How Are Uploaded Files Processed](#how-are-uploaded-files-processed)
    - [How Is Requested URI Processed](#how-is-requested-uri-processed)
    - [How Are Views Located](#how-are-views-located)

## About

This API is a **skeleton** (requires [binding](#binding-points) by developers) created to efficiently handle web requests into server responses using a MVC version where views and models are expected to be independent while controllers mediate between the two based on user request. Designed with modularity, efficiency and simplicity at its foundation, API is both object and event oriented: similar to JavaScript, it allows developers to bind logic that will be executed when predefined events are reached while handling.

![diagram](https://www.lucinda-framework.com/stdout-mvc-api.svg)

API does nothing more than standard MVC logic, so in real life it expects a web framework to be built on top to add further features (eg: DB connectivity). In order to use it, following steps are required from developers:

- **[configuration](#configuration)**: setting up an XML file where this API is configured
- **[binding points](#binding-points)**: binding user-defined components defined in XML/code to API prototypes in order to gain necessary abilities
- **[initialization](#initialization)**: instancing [FrontController](https://github.com/aherne/php-servlets-api/blob/master/src/FrontController.php), a [Runnable](https://github.com/aherne/mvc/blob/master/src/Runnable.php) able to handle requests into responses later on based on above two
- **[binding events](#binding-events)**: setting up [Runnable](https://github.com/aherne/mvc/blob/master/src/Runnable.php) classes that will be instanced and *run* when predefined events are reached during handling process
- **[configuring shared variables](#configuring-shared-variables)**: extend [Attributes](#class-attributes) class to encapsulate variables specific to your project, to be shared between event listeners and controllers
- **[handling](#handling)**: calling *run* method @ [FrontController](https://github.com/aherne/php-servlets-api/blob/master/src/FrontController.php)  to finally handle requests into responses, triggering events above (if any)

API is fully PSR-4 compliant, only requiring [Abstract MVC API](https://github.com/aherne/mvc) for basic MVC logic, PHP8.1+ interpreter and SimpleXML extension. To quickly see how it works, check:

- **[installation](#installation)**: describes how to install API on your computer, in light of steps above
- **[reference guide](#reference-guide)**: describes all API classes, methods and fields relevant to developers
- **[unit tests](#unit-tests)**: API has 100% Unit Test coverage, using [UnitTest API](https://github.com/aherne/unit-testing) instead of PHPUnit for greater flexibility
- **[example](https://github.com/aherne/php-servlets-api/blob/master/tests/FrontController.php)**: shows a deep example of API functionality based on [FrontController](https://github.com/aherne/php-servlets-api/blob/master/src/FrontController.php) unit test

All classes inside belong to **Lucinda\STDOUT** namespace!

## Configuration

To configure this API you must have a XML with following tags inside:

- **[application](#application)**: (mandatory) configures your application on a general basis
- **[resolvers](#resolvers)**: (mandatory) configures formats in which your application is able to resolve responses to
- **[routes](#routes)**: (mandatory) configures routes that bind requested resources to controllers and views
- **[session](#session)**: (optional) configures options to use automatically when creating sessions
- **[cookies](#cookies)**: (optional) configures options to use automatically when setting cookies

### Application

Tag documentation is completely covered by inherited Abstract MVC API [specification](https://github.com/aherne/mvc#application)! Since STDIN for this API is made of HTTP(s) requests, value of *default_route* attribute must point to **index** (homepage) for requests that come with no route. 

### Resolvers

Tag documentation is completely covered by inherited Abstract MVC API [specification](https://github.com/aherne/mvc#resolvers)!

### Routes

Maximal syntax of this tag is:

```xml
<routes>
    <route id="..." controller="..." view="..." format="..." method="...">
        <parameter name="..." validator="..." mandatory="..."/>
        ...
    </route>
    ...
</routes>
```

Most of tag logic is already covered by Abstract MVC API [specification](https://github.com/aherne/mvc#routes). Following extra observations need to be made:

- *id*: (mandatory) requested requested resource url without trailing slash. Can be an exact url (eg: *foo/bar*) or a url pattern (eg: *user/(id)*). If pattern is used, each variable must be named and enclosed in parenthesis!
- *controller*: (optional) name of user-defined PS-4 autoload compliant class (including namespace) that will mitigate requests and responses based on models.<br/>Class must be a [Controller](#abstract-class-controller) instance!
- *method*: (optional) holds single HTTP method by which resource MUST be requested with. If request comes with a different method, a [MethodNotAllowedException](https://github.com/aherne/php-servlets-api/blob/master/src/MethodNotAllowedException.php) is thrown!

Tag example:

```xml
<routes>
    <route id="index" controller="Lucinda\Project\Controllers\Homepage" view="index"/>
    <route id="user/(id)" controller="Lucinda\Project\Controllers\UserInfo" view="user-info">
</routes>
```

**^ It is mandatory to define a route for that defined by default_route attribute @ [application](#application) XML tag!**

If request came without route, **default** route is used. If, however, request came with a route that matches no **id**, a [PathNotFoundException](https://github.com/aherne/php-servlets-api/blob/master/src/PathNotFoundException.php) is thrown!

#### Route Parameters

Each **route** tag can hold one or more rules to validate values of request and path parameters that came along with request. Each parameter corresponds to a **parameter** tag, where validation is configurable based on attributes:

- *name*: (mandatory) name of request or path parameter you want to validate. Examples:
    - *foo*, if request was GET and came with query-string *?foo=bar*
    - *id*, if route url is *user/(id)*
- *validator*: (mandatory)  name of user-defined PS-4 autoload compliant class (including namespace) that will validate value of parameter.<br/>Must be a [EventListeners\Validators\ParameterValidator](#interface-parametervalidator) instance!
- *mandatory*: (optional) holds whether or not parameter is mandatory (value can be 0 or 1). If none, mandatory (1) is assumed!

**^ If parameter names collide, path parameters take precedence over request parameters!**

Tag example:

```xml
<routes>
    <route id="index" controller="Lucinda\Project\Controllers\Homepage" view="index"/>
    <route id="user/(id)" controller="Lucinda\Project\Controllers\UserInfo" view="user-info" method="GET">
        <parameter name="id" validator="Lucinda\Project\ParameterValidators\UserNameValidator"/>
    </route>
</routes>
```

### Session

Maximal syntax of this tag is:

```xml
<session save_path="..." name="..." expired_time="..." expired_on_close="..." https_only="..." headers_only="..." referrer_check="..." handler="..." auto_start="...">
```

Where:

- *save_path*: (optional) absolute path in which sessions are saved on server. Example: "/tmp/sessions/"
- *name*: (optional) name of the session which is used as cookie name (default: PHPSESSID). Example: "SESSID"
- *expired_time*: (optional) number of seconds after which data will be garbage collected. Example: "60"
- *expired_on_close*: (optional) number of seconds session cookie is expected to survive in client browser after close. Example: "120"
- *https_only*: (optional) marks session cookie as accessible over secure HTTPS connections. Value: "1"
- *headers_only*: (optional) marks session cookie as accessible only through the HTTP protocol. Value: "1"
- *referrer_check*: (optional) substring you want to check each HTTP Referer for in order to validate session cookie. Example: "Chrome"
- *handler*: (optional) name of user-defined PS-4 autoload compliant class (including namespace) that implements [\SessionHandlerInterface](https://www.php.net/manual/en/class.sessionhandlerinterface.php). Example: "application/models/RedisHandler"
- *auto_start*: (optional) signals session to be started automatically for each request. Value: "1"

Tag example:

```xml
<session save_path="/tmp/sessions/" name="SESSID" expired_time="60" expired_on_close="120" https_only="1" headers_only="1" referrer_check="Chrome" handler="application/models/RedisHandler" auto_start="1">
```

### Cookies

Maximal syntax of this tag is:

```xml
<cookies path="..." domain="..." https_only="..." headers_only="...">
```

Where:

- *path*: (optional) path on the server in which the cookie will be available on. Example: "/foo/"
- *domain*: (optional) the (sub)domain that the cookie is available to. Example: "www.example.com"
- *https_only*: (optional) signals cookie should only be transmitted over a secure HTTPS connection from the client. Value: "1"
- *headers_only*: (optional) signals cookie should be made accessible only through the HTTP protocol. Value: "1"

Tag example:

```xml
<cookies path="/" domain="example.com" https_only="1" headers_only="1">
```

To understand how to properly set *path* and *domain* when needed, check [specification](#how-to-set-cookies-path-and-domain)!

## Binding Points

In order to remain flexible and achieve highest performance, API takes no more assumptions than those absolutely required! It offers developers instead an ability to bind to its prototypes in order to gain certain functionality.

### Declarative Binding

It offers developers an ability to **bind declaratively** to its prototype classes/interfaces via XML:

| XML Attribute @ Tag | Class Prototype | Ability Gained |
| --- | --- | --- |
| [controller @ route](#routes) | [Controller](#abstract-class-controller) | MVC controller for any request URI |
| [validator @ parameter](#routes) | [EventListeners\Validators\ParameterValidator](#interface-parametervalidator) | Validates value of request/path parameter |
| [class @ resolver](#resolvers) | [\Lucinda\MVC\ViewResolver](https://github.com/aherne/mvc#Abstract-Class-ViewResolver) | Resolving response in a particular format (eg: html) |
| [handler @ session](#session) | [\SessionHandlerInterface](https://www.php.net/manual/en/class.sessionhandlerinterface.php) | Handling session to a storage medium |

### Programmatic Binding

It offers developers an ability to **bind programmatically** to its prototypes via [FrontController](#initialization) constructor:

| Class Prototype | Ability Gained |
| --- | --- |
| [Attributes](#class-attributes) | (mandatory) Collects data (via setters and getters) to be made available throughout request-response cycle |

and addEventListener method (see: [Binding Events](#binding-events) section)!

## Execution

### Initialization

Now that developers have finished setting up XML that configures the API, they are finally able to initialize it by instantiating [FrontController](https://github.com/aherne/php-servlets-api/blob/master/src/FrontController.php).

Apart of method *run* required by [Runnable](https://github.com/aherne/mvc/blob/master/src/Runnable.php) interface it implements, class comes with following public methods:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| __construct | string $documentDescriptor, [Attributes](#class-attributes) $attributes | void | Records user defined XML and attributes for later handling |
| addEventListener | [EventType](https://github.com/aherne/php-servlets-api/blob/master/src/EventType.php) $type, string $className | void | Binds a listener to an event type |

Where:

- *$documentDescriptor*: relative location of XML [configuration](#configuration) file. Example: "configuration.xml"
- *$attributes*: see **[configuring shared variables](#configuring-shared-variables)**.
- *$type*: event type (see  **[binding events](#binding-events)** below) encapsulated by enum [EventType](https://github.com/aherne/php-servlets-api/blob/master/src/EventType.php)
- *$className*: listener *class name*, including namespace and subfolder, found in *folder* defined when [Attributes](#class-attributes) was instanced.

Example:

```php
$handler = new FrontController("configuration.xml", new MyCustomAttributes("application/event_listeners");
$handler->run();
```

### Binding Events

As mentioned above, API allows developers to bind listeners to handle lifecycle events via *addEventListener* method. Each [EventType](https://github.com/aherne/php-servlets-api/blob/master/src/EventType.php) corresponds to an abstract [Runnable](https://github.com/aherne/mvc/blob/master/src/Runnable.php) class:

| Type | Class | Description |
| --- | --- | --- |
| EventType::START | [EventListeners\Start](#abstract-class-eventlisteners-start) | Ran before [configuration](#configuration) XML is read |
| EventType::APPLICATION | [EventListeners\Application](#abstract-class-eventlisteners-application) | Ran after [configuration](#configuration) XML is read into [Application](#class-application) |
| EventType::REQUEST | [EventListeners\Request](#abstract-class-eventlisteners-request) | Ran after user request is read into [Request](#class-request), [Session](#class-session) and [Cookies](#class-cookies) objects |
| EventType::RESPONSE | [EventListeners\Response](#abstract-class-eventlisteners-response) | Ran after [Lucinda\MVC\Response](https://github.com/aherne/mvc#class-response) body is compiled but before it's rendered |
| EventType::END | [EventListeners\End](#abstract-class-eventlisteners-end) | Ran after [Lucinda\MVC\Response](https://github.com/aherne/mvc#class-response) was rendered back to caller  |

Listeners must extend matching event class and implement required *run* method holding the logic that will execute when event is triggered. It is required for them to be registered BEFORE *run* method is ran:

```php
$handler = new FrontController("stdout.xml", new FrameworkAttributes("application/listeners");
$handler->addEventListener(EventType::APPLICATION, Lucinda\Project\EventListeners\Logging::class);
$handler->addEventListener(EventType::REQUEST, Lucinda\Project\EventListeners\Security::class);
$handler->run();
```

To understand how event listeners are located, check [specifications](#how-are-event-listeners-located)!

### Configuring Shared Variables

API allows event listeners to set variables that are going to be made available to subsequent event listeners and controllers. For each variable there is a:

- *setter*: to be ran once by a event listener
- *getter*: to be ran by subsequent event listeners and controllers

API comes with [Attributes](#class-attributes), which holds the foundation every site must extend in order to set up its own variables. Unless your site is extremely simple, it will require developers to extend this class and add further variables, for whom setters and getters must be defined!

### Handling

Once above steps are done, developers are finally able to handle requests into responses via *run* method of [FrontController](https://github.com/aherne/php-servlets-api/blob/master/src/FrontController.php), which:

- detects [EventListeners\Start](#abstract-class-eventlisteners-start) listeners and executes them in order they were registered
- encapsulates [configuration](#configuration) XML file into [Application](#class-application) object
- detects [EventListeners\Application](#abstract-class-eventlisteners-application) listeners and executes them in order they were registered
- encapsulates request information based on $\_SERVER superglobal into [Request](#class-request) object
- encapsulates session information based on $\_SESSION superglobal as well as operations available into [Session](#class-session) object
- encapsulates cookie operations and variables based on $\_COOKIE  superglobal as well as operations available into [Cookie](#class-cookies) object
- detects [EventListeners\Request](#abstract-class-eventlisteners-request) listeners and executes them in order they were registered
- initializes empty [Lucinda\MVC\Response](https://github.com/aherne/mvc#class-response) based on information detected above from request or XML
- locates [Controller](#abstract-class-controller) based on information already detected and, if found, executes it in order to bind models to views
- locates [Lucinda\MVC\ViewResolver](https://github.com/aherne/mvc#abstract-class-viewresolver) based on information already detected and executes it in order to feed response body based on view
- detects [EventListeners\Response](#abstract-class-eventlisteners-response) listeners and executes them in order they were registered
- sends [Lucinda\MVC\Response](https://github.com/aherne/mvc#class-response) back to caller, containing headers and body
- detects [EventListeners\End](#abstract-class-eventlisteners-end) listeners and executes them in order they were registered

All components that are in developers' responsibility ([Controller](#abstract-class-controller), [Lucinda\MVC\ViewResolver](https://github.com/aherne/mvc#abstract-class-viewresolver), along with event listeners themselves, implement [Runnable](https://github.com/aherne/mvc/blob/master/src/Runnable.php) interface.

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
$controller = new Lucinda\STDOUT\FrontController("configuration.xml", new Attributes("application/events"));
// TODO: add event listeners here
$controller->run();
```

## Unit Tests

For tests and examples, check following files/folders in API sources:

- [test.php](https://github.com/aherne/php-servlets-api/blob/master/test.php): runs unit tests in console
- [unit-tests.xml](https://github.com/aherne/php-servlets-api/blob/master/unit-tests.xml): sets up unit tests and mocks "loggers" tag
- [tests](https://github.com/aherne/php-servlets-api/blob/master/tests): unit tests for classes from [src](https://github.com/aherne/php-servlets-api/blob/master/src) folder

## Reference Guide

These classes are fully implemented by API:

- [Application](#class-application): reads [configuration](#configuration) XML file and encapsulates information inside
- [Request](#class-request): encapsulates user request based on information in superglobals: $\_SERVER, $\_POST, $\_GET
    - [Request\Client](#class-request-client): encapsulates client information detected from request
    - [Request\Server](#class-request-server): encapsulates server information detected from request
    - [Request\URI](#class-request-uri): encapsulates uri information detected from request
    - [Request\UploadedFiles\File](#class-request-uploadedfile): encapsulates information about an uploaded file
- [Session](#class-session): encapsulates operations to perform with a http session mapped to $\_SESSION superglobal
- [Cookies](#class-cookies): encapsulates operations to perform with a http cookie mapped to $\_COOKIE superglobal

Apart of classes mentioned in **[binding events](#binding-events)**, following abstract classes require to be extended by developers in order to gain an ability:

- [Controller](#abstract-class-controller): encapsulates binding [Request](#class-request) to [Lucinda\MVC\Response](https://github.com/aherne/mvc#class-response) based on user request and XML info
- [EventListeners\Validators\ParameterValidator](#interface-parametervalidator): performs validation of a request parameter value

### Class Application

Class [Application](https://github.com/aherne/php-servlets-api/blob/master/src/Application.php) encapsulates information detected from XML and defines following public methods relevant to developers:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| getVersion | void | string | Gets application version based on *version* attribute @ [application](#application) XML tag |
| getTag | string $name | [\SimpleXMLElement](https://www.php.net/manual/en/class.simplexmlelement.php) | Gets a pointer to a custom tag in XML root |

### Class Request

Class [Request](https://github.com/aherne/php-servlets-api/blob/master/src/Request.php) encapsulates information detected about user request based on superglobals ($\_SERVER, $\_GET, $\_POST, $\_FILES) and defines following public methods relevant to developers:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| getClient | void | [Request\Client](#class-request-client) | Gets client information detected from request. |
| getInputStream | void | string | Gets access to input stream for binary requests. |
| getMethod | void | [Request\Method](https://github.com/aherne/php-servlets-api/blob/master/src/Request/Method.php) | Gets request HTTP method (REQUEST_METHOD @ $\_SERVER). |
| getProtocol | void | [Request\Protocol](https://github.com/aherne/php-servlets-api/blob/master/src/Request/Protocol.php) | Gets request protocol (HTTPS @ $\_SERVER)
| getServer | void | [Request\Server](#class-request-server) | Gets server information detected from request. ||
| getUri | void | [Request\URI](#class-request-uri) | Gets path information detected from request. |
| headers | void | array | Gets all request headers received from client by standard ISO name |
| headers | string $name | string | Gets value of request header by name or NULL if not found. |
| parameters | void | array | Gets all request parameters received from client matching current request method ($\_GET, $\_POST, etc). |
| parameters | string $name | mixed | Gets value of request parameter by name or NULL if not found. |
| uploadedFiles | void | array | Gets all uploaded files received from client, each encapsulated as [Request\UploadedFiles\File](#class-request-uploadedfile) based on $\_FILES |
| uploadedFiles | string $name | mixed | Gets [Request\UploadedFiles\File](#class-request-uploadedfile) received by name or NULL if not found. |

### Class Request Client

Class [Request\Client](https://github.com/aherne/php-servlets-api/blob/master/src/Request/Client.php) encapsulates client information detected from request based on $\_SERVER superglobal  and defines following public methods relevant to developers:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| getName | void | string | Gets client server name (REMOTE_HOST @ $\_SERVER) |
| getIP | void | string | Gets client ip (REMOTE_ADDR @ $\_SERVER) |
| getPort | void | int | Gets client port (REMOTE_PORT @ $\_SERVER) |

### Class Request Server

Class [Request\Server](https://github.com/aherne/php-servlets-api/blob/master/src/Request/Server.php) encapsulates web server information detected from request based on $\_SERVER superglobal  and defines following public methods relevant to developers:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| getName | void | string | Gets server name (SERVER_NAME @ $\_SERVER) |
| getIP | void | string | Gets server ip (SERVER_ADDR @ $\_SERVER) |
| getPort | void | int | Gets server port (SERVER_PORT @ $\_SERVER) |
| getEmail | void | int | Gets server admin email (SERVER_ADMIN @ $\_SERVER) |
| getSoftware | void | int | Gets server software info (SERVER_SOFTWARE @ $\_SERVER) |

### Class Request URI

Class [Request\URI](https://github.com/aherne/php-servlets-api/blob/master/src/Request/URI.php) encapsulates path information detected from request based on $\_SERVER superglobal  and defines following public methods relevant to developers:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| getContextPath | void | string | Gets context path based on requested URI (DOCUMENT_ROOT & SCRIPT_FILENAME @ $\_SERVER) |
| getPage | void | string | Gets resource (page) requested based on requested URI (REQUEST_URI @ $\_SERVER) |
| getQueryString | void | string | Gets query string that came with URI (QUERY_STRING @ $\_SERVER) |
| parameters | void | array | Gets query string parameters that came with URI ($\_GET) |
| parameters | string $name | mixed | Gets value of query string parameter by name or NULL if not found. |

To understand how requested URI is processed by this class, check [specifications](#how-is-requested-uri-processed)!

### Class Request UploadedFile

Class [Request\UploadedFiles\File](https://github.com/aherne/php-servlets-api/blob/master/src/Request/UploadedFiles\File.php) encapsulates information about a single file uploaded based on $\_FILES superglobal  and defines following public methods relevant to developers:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| getName | void | string | Gets name of file uploaded. |
| getLocation | void | string | Gets temporary location of file uploaded. |
| getContentType | void | string | Gets content type of file uploaded. |
| getSize | void | int | Gets size of file uploaded.|

To process file uploaded, two methods were added for developers:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| move | string $destination | boolean | Moves file uploaded to a final location and returns whether or not operation was successful |
| delete | void | boolean | Deletes file uploaded and returns whether or not operation was successful |

To understand how uploaded files are processed into this class, check [specifications](#how-are-uploaded-files-processed)!

### Class Session

Class [Session](https://github.com/aherne/php-servlets-api/blob/master/src/Session.php) encapsulates operations to perform with a http session via $\_SESSION superglobal and defines following public methods, all relevant to developers:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| start | void | void | Starts session, using settings defined in [session](#session) XML tag |
| isStarted | void | bool | Checks if session was started |
| set | string $key, mixed $value | void | Sets session parameter by key and value |
| get | string $key | mixed | Gets value of session parameter by key |
| contains | string $key | bool | Checks if session contains parameter by key |
| remove | string $key | void | Deletes session parameter by key, if any |
| destroy | void | void | Destroys session, clearing of all parameters. |

### Class Cookies

Class [Cookies](https://github.com/aherne/php-servlets-api/blob/master/src/Cookies.php) encapsulates operations to perform with a http cookie via $\_COOKIE superglobal and defines following public methods, all relevant to developers:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| set | string $name, mixed $value, int $expiration | void | Sets cookie parameter by name and value, lasting for expiration seconds from now, using settings defined in [cookies](#cookies) XML tag |
| get | string $name | mixed | Gets value of cookie by name |
| contains | string $name | bool | Checks if a cookie exists by name |
| remove | string $name | void | Deletes cookie by name |

### Interface ParameterValidator

Interface [EventListeners\Validators\ParameterValidator](https://github.com/aherne/php-servlets-api/blob/master/src/EventListeners/Validators/ParameterValidator.php) implements blueprint for a single request parameter value validation via method:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| validate | mixed $value | mixed | Validates value and returns result on success (eg: matching DB id) or NULL on failure |

Example of a class that validates user name received as parameter (eg: /user/(name) route):

```php
class UserNameValidator implements Lucinda\STDOUT\EventListeners\Validators\ParameterValidator
{
    public function validate($value)
    {
        $result = DB("SELECT id FROM users WHERE name=:name", [":name"=>$value])->toValue();
        return ($result?$result:null);
    }
}
```

To understand more how parameters work, check [specifications](#how-are-parameter-validators-working) and [tag documentation](#route-parameters)!

### Abstract Class EventListeners Start

Abstract class [EventListeners\Start](https://github.com/aherne/php-servlets-api/blob/master/src/EventListeners/Start.php) implements [Runnable](https://github.com/aherne/mvc/blob/master/src/Runnable.php)) and listens to events that execute BEFORE [configuration](#configuration) XML is read.

Developers need to implement a *run* method, where they are able to access following protected fields injected by API via constructor:

| Field | Type | Description |
| --- | --- | --- |
| $attributes | [Attributes](#class-attributes) | Gets access to object encapsulating data where custom attributes should be set. |

A common example of a START listener is the need to set start time, in order to benchmark duration of handling later on:

```php
class StartBenchmark extends Lucinda\STDOUT\EventListeners\Start
{
    public function run(): void
    {
        // you will first need to extend Application and add: setStartTime, getStartTime
        $this->attributes->setStartTime(microtime(true));
    }
}
```

### Abstract Class EventListeners Application

Abstract class [EventListeners\Application](https://github.com/aherne/php-servlets-api/blob/master/src/EventListeners/Application.php) implements [Runnable](https://github.com/aherne/mvc/blob/master/src/Runnable.php)) and listens to events that execute AFTER [configuration](#configuration) XML is read.

Developers need to implement a *run* method, where they are able to access following protected fields injected by API via constructor:

| Field | Type | Description |
| --- | --- | --- |
| $application | [Application](#class-application) | Gets application information detected from XML. |
| $attributes | [Attributes](#class-attributes) | Gets access to object encapsulating data where custom attributes should be set. |

Usage example:

https://github.com/aherne/lucinda-framework/blob/master/src/EventListeners/SQLDataSource.php

### Abstract Class EventListeners Request

Abstract class [EventListeners\Request](https://github.com/aherne/php-servlets-api/blob/master/src/EventListeners/Request.php) implements [Runnable](https://github.com/aherne/mvc/blob/master/src/Runnable.php)) and listens to events that execute AFTER [Request](#class-request), [Session](#class-session) and [Cookies](#class-cookies) objects are created.

Developers need to implement a *run* method, where they are able to access following protected fields injected by API via constructor:

| Field | Type | Description |
| --- | --- | --- |
| $application | [Application](#class-application) | Gets application information detected from XML. |
| $request | [Request](#class-request) | Gets request information detected from superglobals. |
| $session | [Session](#class-session) | Gets pointer to class encapsulating operations on http session. |
| $cookies | [Cookies](#class-cookies) | Gets pointer to class encapsulating operations on http cookies. |
| $attributes | [Attributes](#class-attributes) | Gets access to object encapsulating data where custom attributes should be set. |

Usage example:

https://github.com/aherne/lucinda-framework/blob/master/src/EventListeners/Security.php

### Abstract Class EventListeners Response

Abstract class [EventListeners\Response](https://github.com/aherne/php-servlets-api/blob/master/src/EventListeners/Response.php) implements [Runnable](https://github.com/aherne/mvc/blob/master/src/Runnable.php)) and listens to events that execute AFTER [Lucinda\MVC\Response](https://github.com/aherne/mvc#class-response) body was set but before it's committed back to caller.

Developers need to implement a *run* method, where they are able to access following protected fields injected by API via constructor:

| Field | Type | Description |
| --- | --- | --- |
| $application | [Application](#class-application) | Gets application information detected from XML. |
| $request | [Request](#class-request) | Gets request information detected from superglobals. |
| $session | [Session](#class-session) | Gets pointer to class encapsulating operations on http session. |
| $cookies | [Cookies](#class-cookies) | Gets pointer to class encapsulating operations on http cookies. |
| $response | [Lucinda\MVC\Response](https://github.com/aherne/mvc#class-response) | Gets access to object based on which response can be manipulated. |
| $attributes | [Attributes](#class-attributes) | Gets access to object encapsulating data where custom attributes should be set. |

Usage example:

https://github.com/aherne/lucinda-framework/blob/master/src/EventListeners/HttpCaching.php

### Abstract Class EventListeners End

Abstract class [EventListeners\End](https://github.com/aherne/php-servlets-api/blob/master/src/EventListeners/End.php) implements [Runnable](https://github.com/aherne/mvc/blob/master/src/Runnable.php)) and listens to events that execute AFTER [Lucinda\MVC\Response](https://github.com/aherne/mvc#class-response) was rendered back to caller.

Developers need to implement a *run* method, where they are able to access following protected fields injected by API via constructor:

| Field | Type | Description |
| --- | --- | --- |
| $application | [Application](#class-application) | Gets application information detected from XML. |
| $request | [Request](#class-request) | Gets request information detected from superglobals. |
| $session | [Session](#class-session) | Gets pointer to class encapsulating operations on http session. |
| $cookies | [Cookies](#class-cookies) | Gets pointer to class encapsulating operations on http cookies. |
| $response | [Lucinda\MVC\Response](https://github.com/aherne/mvc#class-response) | Gets access to object based on which response can be manipulated. |
| $attributes | [Attributes](#class-attributes) | Gets access to object encapsulating data where custom attributes should be set. |

A common example of a START listener is the need to set end time, in order to benchmark duration of handling:

```php
class EndBenchmark extends Lucinda\STDOUT\EventListeners\End
{
    public function run(): void
    {
        $benchmark = new Benchmark();
        $benchmark->save($this->attributes->getStartTime(), microtime(true));
    }
}
```

### Abstract Class Controller

Abstract class [Controller](https://github.com/aherne/php-servlets-api/blob/master/src/Controller.php) implements [Runnable](https://github.com/aherne/mvc/blob/master/src/Runnable.php)) to set up response (views in particular) by binding information detected beforehand to models. It defines following public method relevant to developers:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| run | void | void | Inherited prototype to be implemented by developers to set up response based on information saved by constructor |

Developers need to implement *run* method for each controller, where they are able to access following protected fields injected by API via constructor:

| Field | Type | Description |
| --- | --- | --- |
| $application | [Application](#class-application) | Gets application information detected from XML. |
| $request | [Request](#class-request) | Gets request information detected from superglobals. |
| $session | [Session](#class-session) | Gets pointer to class encapsulating operations on http session. |
| $cookies | [Cookies](#class-cookies) | Gets pointer to class encapsulating operations on http cookies. |
| $response | [Lucinda\MVC\Response](https://github.com/aherne/mvc#class-response) | Gets access to object based on which response can be manipulated. |
| $attributes | [Attributes](#class-attributes) | Gets access to object encapsulating data set by event listeners beforehand. |

Usage example:

https://github.com/aherne/lucinda-framework-configurer/blob/master/files/controllers/no_rest/IndexController.php

To understand more about how controllers are detected, check [specifications](#how-are-controllers-located)!

### Class Attributes

Class [Attributes](https://github.com/aherne/php-servlets-api/blob/master/src/Attributes.php) encapsulates data collected throughout request-response cycle, each corresponding to a getter and a setter, and made available to subsequent event listeners or controllers. API already comes with following:

| Method | Arguments | Returns | Description |
| --- | --- | --- | --- |
| __construct | string $folder | void | Sets folder in which event listeners will be searched for |
| getEventsFolder | void | string | Gets folder in which event listeners will be searched for |
| getPathParameters | string $name | string | Gets value of path parameter by its name or NULL if not found |
| getPathParameters | void | array | Gets all path parameters received in request |
| getValidFormat | void | string | Gets final response format to use |
| getValidPage | void | string | Gets final route requested |
| getValidParameters | string $name | mixed | Gets result of request/path parameter validation by its name or NULL if validation failed |
| getValidParameters | void | array | Gets results of request/path parameters validation |

Most of the data collected will need to be set by developers themselves to fit their project demands so in 99% of cases class will need to be extended for each project! Usage example:

https://github.com/aherne/lucinda-framework/blob/master/src/Attributes.php

## Specifications

Since this API works on top of [Abstract MVC API](https://github.com/aherne/mvc) specifications it follows their requirements and adds extra ones as well:

- [How Is Response Format Detected](#how-is-response-format-detected)
- [How Are View Resolvers Located](#how-are-view-resolvers-located)
- [How Is Route Detected](#how-is-route-detected)
- [How Are Controllers Located](#how-are-controllers-located)
- [How Are Parameter Validators Working](#how-are-parameter-validators-working)
- [How to Set Cookies Path and Domain](#how-to-set-cookies-path-and-domain)
- [How Are Uploaded Files Processed](#how-are-uploaded-files-processed)
- [How Is Requested URI Processed](#how-is-requested-uri-processed)
- [How Are Views Located](#how-are-views-located)

### How Is Response Format Detected

This section follows parent API [specifications](https://github.com/aherne/mvc#how-is-response-format-detected) only that routes are detected based on value of *$_SERVER["REQUEST_URI"]*.

### How Are View Resolvers Located

This section follows parent API [specifications](https://github.com/aherne/mvc#how-are-view-resolvers-located) in its entirety.

### How Is Route Detected

This section follows parent API [specifications](https://github.com/aherne/mvc#how-are-view-resolvers-located) only that routes are detected based on value of *$_SERVER["REQUEST_URI"]*. Let's take this XML for example:

```xml
<application default_route="index" ...>
	...
</application>
<routes>
    <route id="index" .../>
    <route id="users" .../>
    <route id="user/(id)" .../>
</routes>
```

There will be following situations for above:

| If Page Requested | Then Route ID Detected | Description |
| --- | --- | --- |
| / | index | Because requested page came empty, that identified by *default_route* is used |
| /users | users | Because requested page is matched to a route, specific route is used |
| /hello | - | Because no route is found matching the one requested a [PathNotFoundException](https://github.com/aherne/php-servlets-api/blob/master/src/PathNotFoundException.php) is thrown |
| /user/12 | user/(id) | Because requested page matched one with a route parameter, specific route is used and id=12 path parameter is detected |

### How Are Controllers Located

This section follows parent API [specifications](https://github.com/aherne/mvc#how-are-controllers-located) only that class defined as *controller* attribute in [route](#routes) tag must extend [Controller](#abstract-class-controller).

### How Are Parameter Validators Working

To better understand how *validators* attribute in **[application](#application)** XML tag plays together with **parameter** sub-tags in **[routes](#route-parameters)** tag in order to locate validators to run based on incoming request, let's take this XML for example:

```xml
<routes>
    <route id="users/(uname)" method="GET" ...>
    	<parameter name="uname" class="Lucinda\Project\ParameterValidators\UserName"/>
    </route>
    <route id="user/info" method="POST" ...>
    	<parameter name="id" class="Lucinda\Project\ParameterValidators\UserId"/>
    	<parameter name="name" class="Lucinda\Project\ParameterValidators\UserName" mandatory="0"/>
    </route>
    ...
</routes>
```

When a request to */users/aherne* is received, API will:

- detect route with id *users/(uname)* and request parameters received (path parameters, GET, POST)
- check if route is called with GET method. If not, a [MethodNotAllowedException](https://github.com/aherne/php-servlets-api/blob/master/src/MethodNotAllowedException.php) is thrown!
- instances *Lucinda\Project\ParameterValidators\UserName* and runs *validate* method on value of "uname" path parameter. If param not sent or validation fails, a [ValidationFailedException](https://github.com/aherne/php-servlets-api/blob/master/src/ValidationFailedException.php) is thrown!

When a request to */users/info* is received, API will: 

- detect route with id *user/info* and request parameters received (path parameters, GET, POST)
- check if route is called with POST method. If not, a [MethodNotAllowedException](https://github.com/aherne/php-servlets-api/blob/master/src/MethodNotAllowedException.php) is thrown!
- instances *Lucinda\Project\ParameterValidators\UserId* and runs *validate* on value of "id" request parameter. If *param not sent or validation fails*, a [ValidationFailedException](https://github.com/aherne/php-servlets-api/blob/master/src/ValidationFailedException.php) will be thrown!
- instances *Lucinda\Project\ParameterValidators\UserName* and runs *validate* on value of "name" request parameter. *If param sent and validation fails*, a [ValidationFailedException](https://github.com/aherne/php-servlets-api/blob/master/src/ValidationFailedException.php) will be thrown!

All parameter validators need to be PSR-4 autoload compliant and implement [EventListeners\Validators\ParameterValidator](#interface-parametervalidator)! 

### How to Set Cookies Path and Domain

Table below shows the effects of *path* attribute @ [cookies](#cookies) XML tag:

| Value | Effect |
| --- | --- |
|  |  the cookie will be available in the current directory that the cookie is being set in (default) |
| / | the cookie will be available within the entire domain (recommended) |
| /foo/ | the cookie will only be available within the /foo/ directory and all sub-directories such as /foo/bar/ of domain |

Table below shows the effects of *domain* attribute @ [cookies](#cookies) XML tag:

| Value | Effect |
| --- | --- |
|  | makes cookie available to current subdomain |
| www.example.com | makes cookie available to that subdomain and all other sub-domains of it (i.e. w2.www.example.com) |
| example.com | makes cookie available to the whole domain (including all subdomains of it) |

### How Are Uploaded Files Processed

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

Where *object* is a [Request\UploadedFiles\File](#class-request-uploadedfile).  To retrieve uploaded files, use *uploadedFiles* method @ [Request](#class-request)!

### How Is Requested URI Processed

API breaks down requested URI (value of REQUEST_URI param @ $\_SERVER) into relevant components based on following algorithm:

- it first detects **context path** by stripping SCRIPT_FILENAME from DOCUMENT_ROOT. Normally, context path is empty since sites are deployed to a specific hostname, but there are cases in which they are deployed on *localhost* directly, so when REQUEST_URI is *http://localhost/mySite/foo/bar*, context path will be *mySite*
- it strips context path and QUERY_STRING from REQUEST_URI in order to detect **requested page**. If no specific page was requested as in *http://www.example.com*, homepage is assumed, so requested page will be an empty string
- it records query string and its array representation based on QUERY_STRING param @ $\_SERVER. Recording an array representation of query string parameters separate from that returned by *parameters* method of [Request](#class-request) is justified when a non-GET request is received.

Examples:

| DOCUMENT_ROOT | SCRIPT_FILENAME | REQUEST_URI | context path | route | query string |
| --- | --- | --- | --- | --- | --- |
| /aaa/bbb | /aaa/bbb/ccc/index.php | /ddd?a=b | ccc | ddd | ?a=b |
| /aaa/bbb | /aaa/bbb/index.php | /ddd/fff |  | ddd/fff |  |

### How Are Views Located

This section follows parent API [specifications](https://github.com/aherne/mvc#how-are-views-located) in its entirety. Extension is yet to be decided, since it depends on type of view resolved!
