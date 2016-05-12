# PHPServletsAPI

There are many MVC frameworks for building web applications in PHP, so writing a new one doesn’t make sense on a first glance. However, looking at existing solutions (such as Laravel, Zend, Symfony), following problems are evident:

1. they are too complicated: they require a lot of learning and even after one manages to learn them, they are still hard to work with, because of complicated (and more often than not unintuitive) patterns one has to obey whenever writing a new component. 
2. they are coupled: internal components are not detachable, libraries that have nothing to do with MVC concerns are bundled in distribution (Zend DB, for example), and coupled with MVC part. This makes them impossible to optimize and use except as a whole.
3. they are overprogrammed: they always do much more than needed. Their purpose is to speed up development time, but whenever doesn't go hand in hand with spareness and modularity, users of application suffer (from low performance) while programmers rejoice.
4. they are VERY slow, as a consequence of points above. The issue of performance (CPU time & memory consumption) is always ignored in favor of popularity (user base), good documentation, reliability and supposed "speed of development" (which only applies for their experienced followers).

The conclusion of above is that **the main purpose of a WEB application, that of serving users instead of programmers, has been blissfully forgotten**. A WEB application absolutely needs to be as fast and accurate as possible: all concerns must be channeled to bring a positive user experience. In PHP, which is after all a slow scripting language, this can only be achieved through a combination of minimalism and modularity. In light of above, PHP Servlets, built with concepts from Java Servlets API and Spring MVC in mind, it is supposed to be JUST a small scalable web MVC API that is both very fast and modular.
