<?php

use Symfony\Component\HttpFoundation\Request;

umask(0000);

require __DIR__.'/../src/Kernel.php';

$kernel = new \App\Kernel('dev', true);
$request = Request::createFromGlobals();
try {
    $response = $kernel->handle($request);
} catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
    $response = new \Symfony\Component\HttpFoundation\Response('404 - resource not found', 404);
}
$response->send();
$kernel->terminate($request, $response);
