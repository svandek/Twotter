<?php

use Symfony\Component\HttpFoundation\Request;

$app = new Silex\Application();
$app['debug'] = true;

require_once __DIR__.'/config.php';

$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\DoctrineServiceProvider(), [
    'db.options' => $app['database'],
]);

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/views',
));

function getColor($session) {
    if ($session->has('color')) {
        $color = $session->get('color');
    } else {
        $color = 'lightblue';
    }

    return $color;
}

$app->get('/', function () use ($app) {
    $app['db']->query('CREATE SEQUENCE IF NOT EXISTS msg_id_seq');
    $app['db']->query('CREATE TABLE IF NOT EXISTS twoots (id INT PRIMARY KEY NOT NULL DEFAULT nextval(\'msg_id_seq\'), msg TEXT NOT NULL, created DATE NOT NULL)');

    $twoots = $app['db']->fetchAll('SELECT * FROM twoots');
    $color = getColor($app['session']);

    return $app['twig']->render('index.html.twig', [
        'twoots' => $twoots,
        'style' => [
            'col1' => $color,
        ]
    ]);
});

$app->post('/', function (Request $request) use ($app) {
    $msg = $request->get('msg');
    if($msg != null) {
        // save to database
        $app['db']->executeQuery('INSERT INTO twoots (msg, created) VALUES (?, NOW())', [$msg]);

        // redirect naar /
    }
    return $app->redirect('/');
});

$app->post('/destroy', function () use ($app) {
    $app['db']->query('TRUNCATE TABLE ONLY twoots RESTART IDENTITY');
    return $app->redirect('/');
});

$app->post('/remove', function () use ($app) {
    $app['db']->query('DELETE FROM twoots where id in(SELECT id FROM twoots ORDER BY id DESC LIMIT 1)');

    return $app->redirect('/');
});


$app->post('/theme', function () use ($app) {
    $color = getColor($app['session']);

    if($color === 'lightblue') {
        $color = '#edc380';
        $app['session'] ->set('color', $color);
    } else {
        $color = 'lightblue';
        $app['session'] ->set('color', $color);
    }
    return $app->redirect('/');
});

$app->run();
