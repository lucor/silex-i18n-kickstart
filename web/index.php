<?php

$loader = require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\Loader\YamlFileLoader;

$app = new Silex\Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
  'twig.path' => __DIR__ . '/views',
));

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app['session']->start();

$app['app_default_locale'] = 'en';
$app['app_allowed_locales'] = array('en','it');

$locale = function() use ($app) {
  $path = $_SERVER['REQUEST_URI'];

  $request_tokens = explode('/', $path);
  $locale = in_array($request_tokens[1], $app['app_allowed_locales']) ? $request_tokens[1] : $app['app_default_locale'];
  $localeFromSession = $app['session']->get('locale');

  if($locale != $localeFromSession) {
    $app['session']->set('locale', $locale);
  }
  return $locale;
};

$app->register(new Silex\Provider\TranslationServiceProvider(), array(
  'locale' => $locale,
  'locale_fallback' => $app['app_default_locale'],
));

$app->register(new Umpirsky\Silex\I18nRouting\Provider\I18nRoutingServiceProvider());

$app['translator.domains'] = array(
  'routes' => array(
    'it' => array(
      '/' => '/it',
      '/blog' => '/it/blog',
      '/archive' => '/it/archivio'
    )
  )
);

$app['translator'] = $app->share($app->extend('translator', function ($translator, $app) {
  $translator->addLoader('yaml', new YamlFileLoader());
  $translator->addResource('yaml', __DIR__ . '/locales/it.yml', 'it');
  $translator->addResource('yaml', __DIR__ . '/locales/en.yml', 'en');
  return $translator;
}));


$app->get('/', function () use ($app) {
  return $app['twig']->render('home.twig');
})->bind('index');

$app->get('/blog', function () use ($app) {
  return $app['twig']->render('home.twig');
})->bind('blog');

$app->get('/archive', function () use ($app) {
  return $app['twig']->render('home.twig');
})->bind('archive');

$app['debug'] = true;

$app->run();



