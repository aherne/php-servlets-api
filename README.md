# PHPServletsAPI

This is a standalone MVC API translating requests into responses using MVC pattern and also serving as engine behind Lucinda Framework. It was designed on following principles:

- Simplicity: no needless abstraction, not a line of code more than needed. Code must be streamlined.
- Performance: all expenses were made to optimize for speed without sacrificing anything in strong design.
- Reusability: each component is designed to be independently reusable, with the exception of those that are component’s helpers (such as Locators for FrontController).
- Completeness: whatever non-abstract class API defines encapsulates all possible behavior on that topic, thus is final and non-extendible.
- Loose coupling: encapsulation of behavior in separate classes (Strategy Design Pattern). All classes designed for a single purpose.
- Independence: API depends on nothing but PHP 5+. You are no longer forced to bundle other libraries in order to make it work.

The main point was building a solution that is extremely fast (as close to php raw as possible), extremely simple to use, but at the same time full featured and with easily extensible architecture, allowing seamless integration with libraries following same programming principles of minimalism and single-purpose design. 

More information here:<br/>
http://www.lucinda-framework.com/servlets
