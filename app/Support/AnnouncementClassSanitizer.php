<?php

namespace App\Support;

use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;
use Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface;

class AnnouncementClassSanitizer implements AttributeSanitizerInterface
{
    private const ALLOWED_CLASSES = [
        'bg-amber-50',
        'bg-amber-100',
        'bg-amber-200/80',
        'border',
        'border-amber-200',
        'border-amber-200/70',
        'border-b',
        'font-bold',
        'font-semibold',
        'flex',
        'gap-3',
        'h-7',
        'inline-flex',
        'items-center',
        'items-start',
        'justify-between',
        'justify-center',
        'leading-relaxed',
        'mt-0.5',
        'mt-1',
        'mt-4',
        'p-5',
        'pb-3',
        'px-3',
        'py-1',
        'rounded-2xl',
        'rounded-full',
        'shadow-sm',
        'shrink-0',
        'text-amber-700',
        'text-amber-800',
        'text-amber-900',
        'text-lg',
        'text-slate-900',
        'text-sm',
        'text-xs',
        'tracking-wider',
        'uppercase',
        'w-7',
    ];

    public function getSupportedElements(): ?array
    {
        return ['div', 'h2', 'h3', 'h4', 'p', 'span'];
    }

    public function getSupportedAttributes(): ?array
    {
        return ['class'];
    }

    public function sanitizeAttribute(string $element, string $attribute, string $value, HtmlSanitizerConfig $config): ?string
    {
        $classes = preg_split('/\s+/', trim($value), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $allowedClasses = array_values(array_intersect($classes, self::ALLOWED_CLASSES));

        return $allowedClasses === [] ? null : implode(' ', $allowedClasses);
    }
}
