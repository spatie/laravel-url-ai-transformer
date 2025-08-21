<?php

it('loads environment variables from tests/.env', function () {
    // Check if the OPENAI_API_KEY from tests/.env is loaded
    expect(env('OPENAI_API_KEY'))->toBe('test-key');
    
    // Dump the prism config to see if test-key appears
    $prismConfig = config('prism');
    dump($prismConfig);
    
    // Check that the test-key is in the prism config
    expect(config('prism.providers.openai.api_key'))->toBe('test-key');
});