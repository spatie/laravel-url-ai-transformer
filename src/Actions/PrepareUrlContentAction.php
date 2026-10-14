<?php

namespace Spatie\LaravelUrlAiTransformer\Actions;

use Illuminate\Support\Str;
use RuntimeException;

class PrepareUrlContentAction
{
    public function execute(string $urlContent): string
    {
        $urlContent = $this->removeScriptAndStyleBlocks($urlContent);
        $urlContent = preg_replace('#<[^>]+>#', ' ', $urlContent) ?? strip_tags($urlContent);
        $urlContent = html_entity_decode($urlContent, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return Str::limit(Str::squish($urlContent), 6000);
    }

    /*
     * Script and style blocks are removed by searching for the closing tag with
     * stripos instead of a lazy regex, so very large inline scripts can't run
     * into pcre.backtrack_limit. A block without a closing tag is removed up
     * to the end of the content.
     */
    protected function removeScriptAndStyleBlocks(string $html): string
    {
        $result = '';
        $offset = 0;

        while (($found = preg_match('#<(script|style)\b#i', $html, $match, PREG_OFFSET_CAPTURE, $offset)) === 1) {
            $start = $match[0][1];

            $openingTagEnd = strpos($html, '>', $start);

            if ($openingTagEnd === false) {
                break;
            }

            $result .= substr($html, $offset, $start - $offset);

            $offset = $this->findEndOfBlock($html, $match[1][0], $openingTagEnd + 1);
        }

        if ($found === false) {
            throw new RuntimeException('Could not remove script and style blocks: '.preg_last_error_msg());
        }

        return $result.substr($html, $offset);
    }

    protected function findEndOfBlock(string $html, string $tag, int $offset): int
    {
        $closingTag = '</'.$tag;

        while (($position = stripos($html, $closingTag, $offset)) !== false) {
            $end = $position + strlen($closingTag);
            $end += strspn($html, " \t\n\r\f", $end);

            if (($html[$end] ?? null) === '>') {
                return $end + 1;
            }

            $offset = $position + 1;
        }

        return strlen($html);
    }
}
