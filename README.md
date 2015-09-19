NewsSearchMonitor
============================

Esto es mi proyecto fin de carrera, para ITIS (Ingeniería Técnica en Informática de Sistemas), en la universidad de Salamanca.

Es un monitor de posiciones del módulo de Google news que Google saca en sus resultados cuando algo es noticia. La aplicación web:
 * Trackea las posiciones cada pocos minutos (configurable)
 * Alterna entre la lista de proxies que se indique.
 * Scrappea y guarda el html completo de los resultados de Google
 * Scrappea también algunos datos de cada noticia que se encuentra en el módulo de News
 * Calcula la visibilidad de cada site 
 * Genera gráficas de esa visibilidad y de la evolución de posiciones de cada site.
 * Genera informes de cambios por site

Está todavía en desarrollo, usando Silex Kitchen Edition (http://lyrixx.github.io/Silex-Kitchen-Edition/) y Doctrine como ORM
