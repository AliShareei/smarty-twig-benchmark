#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

function benchmark(string $type, int $iterations)
{
    echo 'Benchmarking ' . $type . "\n";

    $instance = setup($type);
    $data = [
        'data' => [
            'some', 'bits', 'to', 'iterate', 'over'
        ]
    ];

    // Prime the cache
    $result = benchmarkOnce($type, $instance, $data);

    echo "Result:\n";
    echo $result;

    $start = microtime(true);

    for ($i = 0; $i < $iterations; $i++) {
        benchmarkOnce($type, $instance, $data);
    }

    $end = microtime(true);

    echo "\n\n";
    echo "Time taken: " . ($end - $start) . "\n";
    echo "Time taken per iteration: " . (($end - $start) / $iterations) . "\n";
}

function setup($type)
{
    switch ($type) {
        case 'smarty':
            $smarty = new Smarty\Smarty();
            $smarty->setEscapeHtml(true);
            $smarty->setCompileCheck(false);
            $smarty->setCacheDir(__DIR__ . '/cache');
            $smarty->setCompileDir(__DIR__ . '/cache');
            return $smarty;

        case 'smarty_reuse':
            $smarty = new Smarty\Smarty();
            $smarty->setEscapeHtml(true);
            $smarty->setCompileCheck(false);
            $smarty->setCacheDir(__DIR__ . '/cache');
            $smarty->setCompileDir(__DIR__ . '/cache');
            return $smarty->createTemplate('index.html.smarty');

        case 'twig':
            $loader = new \Twig\Loader\FilesystemLoader('templates');

            return new \Twig\Environment($loader, [
                'cache' => __DIR__ . '/cache',
            ]);

        case 'twig_reuse':
            $loader = new \Twig\Loader\FilesystemLoader('templates');

            $env = new \Twig\Environment($loader, [
                'cache' => __DIR__ . '/cache',
            ]);

            return $env->load('index.html.twig');
        default:
            throw new InvalidArgumentException('Unknown type');
    }
}

function benchmarkOnce($type, $instance, $data)
{
    switch ($type) {
        case 'smarty':
            /** @var Smarty\Smarty $instance */
            $instance->assign($data);
            return $instance->fetch('index.html.smarty');
        case 'smarty_reuse':
            /** @var Smarty\Template $instance */
            $instance->assign($data);
            return $instance->fetch();

        case 'twig':
            /** @var Twig_Environment $instance */
            $template = $instance->load('index.html.twig');
            return $template->render($data);

        case 'twig_reuse':
            /** @var Twig_TemplateWrapper $instance */
            return $instance->render($data);
        default:
            throw new InvalidArgumentException('Unknown type');
    }
}

exec('rm -rf cache');
exec('mkdir cache');

$type = $argv[1] ?? null;
$iterations = (int) ($argv[2] ?? 100000);
benchmark($type, $iterations);
