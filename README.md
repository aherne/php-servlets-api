# PHPServletsAPI

There are many MVC frameworks for building web applications in PHP, so writing a new one doesn’t make sense on a first glance. However, looking at existing solutions (such as Laravel, Zend, Symfony), following problems are evident:

1. they are too complicated: they require a lot of learning and even after one manages to learn them, they are still hard to work with, because of complicated patterns one has to obey whenever writing a new component (controller, view, etc.). 
2. they are coupled: internal components are not detachable, libraries that have nothing to do with MVC concerns are bundled in distribution (Zend DB, for example), and coupled with MVC part.
3. they are overprogrammed: they always do much more than needed. Having state-of-the-art programming inside is always good, but that needs to go hand in hand with spareness and modularity.
4. they are VERY slow, as a consequence of points above. The issue of performance (CPU time & memory consumption) is always ignored in favor of popularity (user base) and supposed "speed of development".

The conclusion of above considerations is that the main purpose of a WEB application has been forgotten: that of serving users instead of programmers. A WEB application absolutely needs to be as fast and accurate as possible: all concerns must be channeled to bring a positive user experience. In PHP, which is after all a slow scripting language, this can only be achieved through a combination of minimalism and modularity. The only other solution would be to write a C extension to PHP that performs a web framework logic (stuff people at Phalcon did): this does bring huge performance benefits, but needs being installed on server (load extension on apache startup), plus it doesn’t guarantee this extension is free of memory leaks and other problems that might arise while running a third party library.

In light of above, PHP Servlets, built with concepts from Java Servlets API and Spring MVC in mind, it is supposed to be a small scalable web MVC API that is both very fast and modular.
