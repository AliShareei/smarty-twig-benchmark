# An Smarty versus Twig benchmark

An *opinionated* comparison benchmark of the latest versions of Smarty v5 and Twig v3.

## Opinionated How?

The main differences from other benchmarks you might run into:

* Both engines are tested with auto-escaping turned **on**
  * This is because you shouldn't be relying on your template developers to manually escape variables, ever. It's asking for
    XSS trouble.
  * Doing a benchmark that _disables auto-escaping_ can hide performance problems in a part of the template engine you
    *definitely* want to be using.

## Templates

The test templates are pretty simple:

* Extending one base template and overriding its blocks
* 3 blocks, with varying default content
* A single for loop, outputting elements of an array within one block

## Running Yourself

Don't take my word for it:

* `composer install`
* `php bench.php smarty`, `php bench.php smarty_ruse`, `php bench.php twig` or `php bench.php twig_reuse`

## Results

With Smarty 5.4.0 and Twig 3.11.0, on PHP 8.3, 1.000.000 iterations, compile time ignored, cache warmed, my machine:

| Benchmark    | Time Taken  |
|--------------|-------------|
| twig         | 9.2 seconds |
| twig_reuse   | 8.5 seconds |
| smarty       | 9.5 seconds |
| smarty_reuse | 8.8 seconds |

See the code for the difference between the normal and reuse scenarios (basically: using the same Template instance, versus
loading the template again.)

