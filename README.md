<div align="left">
    <a href="https://spatie.be/open-source?utm_source=github&utm_medium=banner&utm_campaign=laravel-url-ai-transformer">
      <picture>
        <source media="(prefers-color-scheme: dark)" srcset="https://spatie.be/packages/header/laravel-url-ai-transformer/html/dark.webp?1756452689">
        <img alt="Logo for laravel-permission" src="https://spatie.be/packages/header/laravel-url-ai-transformer/html/light.webp?1756452689">
      </picture>
    </a>

<h1>Transform URLs and their content using AI</h1>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-url-ai-transformer.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-url-ai-transformer)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/spatie/laravel-url-ai-transformer/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/spatie/laravel-url-ai-transformer/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/spatie/laravel-url-ai-transformer/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/spatie/laravel-url-ai-transformer/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-url-ai-transformer.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-url-ai-transformer)
    
</div>

Using this package, you can transform URLs and their content using AI. Whether you want to extract structured data, generate summaries, create image, or apply custom AI transformations to web content - this package can do it.

The result of the transformation is stored in a database. You can retrieve the transformed content at any time.

Here's how you can transform a blog post into structured [ld+json data](https://json-ld.org) using AI:

```php
use Spatie\LaravelUrlAiTransformer\Support\Transform;
use Spatie\LaravelUrlAiTransformer\Transformers\LdJsonTransformer;

Transform::urls('https://example.com/blog/my-post')
    ->usingTransformers(new LdJsonTransformer);
```

A transformer is a class where you can configure the AI transformation, and specify the prompt to use.

The configured transformation can be run using the `transform-urls` command.

```bash
php artisan transform-urls
```

After the transformation is complete, you can retrieve the transformed content using the `TransformationResult` model.

```php
use Spatie\LaravelUrlAiTransformer\Models\TransformationResult;

$structuredData = TransformationResult::forUrl('https://example.com/blog/my-post','ldJson'
);
```

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-url-ai-transformer.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-url-ai-transformer)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Documentation

All documentation is available [on our documentation site](https://spatie.be/docs/laravel-url-ai-transformer).

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
