<?php

// http://docs.doctrine-project.org/en/latest/reference/configuration.html

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/resources/config/dev.php';

$newDefaultAnnotationDrivers = array(
    __DIR__."/src/Dev",
);

$config = new \Doctrine\ORM\Configuration();
$config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache);

$driverImpl = $config->newDefaultAnnotationDriver($newDefaultAnnotationDrivers);
$config->setMetadataDriverImpl($driverImpl);

$config->setProxyDir($app['orm.proxies_dir']);
$config->setProxyNamespace('Proxies');

$em = \Doctrine\ORM\EntityManager::create($app['db.options'], $config);

$helpers = new Symfony\Component\Console\Helper\HelperSet(array(
    'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection()),
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em),
));