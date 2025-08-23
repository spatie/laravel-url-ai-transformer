<?php

namespace Spatie\LaravelUrlAiTransformer\Transformers;

use Prism\Prism\Prism;
use Spatie\LaravelUrlAiTransformer\Support\Config;

class ImageTransformer extends Transformer
{
    public function transform(): void
    {
        $response = Prism::image()
            ->using(Config::aiProvider('image'), Config::aiModel('image'))
            ->withPrompt($this->getPrompt())
            ->withClientOptions(['timeout' => 120])
            ->generate();

        $image = $response->firstImage();

        $this->transformationResult->result = $image->url;
    }

    public function getPrompt(): string
    {
        $response = Prism::text()
            ->using(Config::aiProvider(), Config::aiModel())
            ->withPrompt('generate a promt of 3000 characters maximum to create an image from this content: '.$this->urlContent)
            ->asText();

        return $response->text;
    }
}
