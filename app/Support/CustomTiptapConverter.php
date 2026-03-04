<?php

namespace App\Support;

use FilamentTiptapEditor\TiptapConverter;
use Tiptap\Marks\Highlight;

class CustomTiptapConverter extends TiptapConverter
{
    public function getExtensions(): array
    {
        return array_map(
            static fn ($extension) => $extension instanceof Highlight
                ? new Highlight(['multicolor' => true])
                : $extension,
            parent::getExtensions(),
        );
    }

    public function asJSON(string | array | null $content, bool $decoded = false, bool $toc = false, int $maxDepth = 3): string | array
    {
        $json = parent::asJSON($content, decoded: true, toc: $toc, maxDepth: $maxDepth);

        if (! is_array($json)) {
            return $decoded ? [] : '[]';
        }

        $normalized = $this->normalizeNode($json);

        return $decoded ? $normalized : json_encode($normalized, JSON_UNESCAPED_UNICODE);
    }

    private function normalizeNode(array $node): array
    {
        if (isset($node['attrs']) && is_array($node['attrs'])) {
            $node['attrs'] = $this->normalizeAttributes($node['attrs']);
        }

        if (isset($node['marks']) && is_array($node['marks'])) {
            $node['marks'] = array_map(function ($mark) {
                if (! is_array($mark)) {
                    return $mark;
                }

                if (isset($mark['attrs']) && is_array($mark['attrs'])) {
                    $mark['attrs'] = $this->normalizeAttributes($mark['attrs']);
                }

                return $mark;
            }, $node['marks']);
        }

        if (isset($node['content']) && is_array($node['content'])) {
            $node['content'] = array_map(fn ($child) => is_array($child) ? $this->normalizeNode($child) : $child, $node['content']);
        }

        return $node;
    }

    private function normalizeAttributes(array $attrs): array
    {
        if (! isset($attrs['style']) || ! is_string($attrs['style'])) {
            return $attrs;
        }

        $blockedProperties = [
            'text-align',
            'color',
            'background-color',
        ];

        $styleParts = array_filter(array_map('trim', explode(';', $attrs['style'])));

        $styleParts = array_values(array_filter($styleParts, function (string $part) use ($blockedProperties) {
            [$property] = array_pad(explode(':', $part, 2), 2, null);

            if (! is_string($property)) {
                return true;
            }

            return ! in_array(strtolower(trim($property)), $blockedProperties, true);
        }));

        if (empty($styleParts)) {
            unset($attrs['style']);

            return $attrs;
        }

        $attrs['style'] = implode('; ', $styleParts) . ';';

        return $attrs;
    }
}
