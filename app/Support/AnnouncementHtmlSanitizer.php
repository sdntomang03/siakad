<?php

namespace App\Support;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class AnnouncementHtmlSanitizer
{
    private HtmlSanitizer $sanitizer;

    public function __construct()
    {
        $config = (new HtmlSanitizerConfig)
            ->withAttributeSanitizer(new AnnouncementClassSanitizer)
            ->allowElement('p')
            ->allowElement('br')
            ->allowElement('strong')
            ->allowElement('b')
            ->allowElement('em')
            ->allowElement('i')
            ->allowElement('u')
            ->allowElement('s')
            ->allowElement('ul')
            ->allowElement('ol')
            ->allowElement('li')
            ->allowElement('blockquote')
            ->allowElement('div', ['class'])
            ->allowElement('span', ['class'])
            ->allowElement('h2', ['class'])
            ->allowElement('h3', ['class'])
            ->allowElement('h4', ['class'])
            ->allowElement('p', ['class'])
            ->allowElement('hr')
            ->allowElement('a', ['href', 'title'])
            ->allowElement('table')
            ->allowElement('thead')
            ->allowElement('tbody')
            ->allowElement('tr')
            ->allowElement('th')
            ->allowElement('td')
            ->allowLinkSchemes(['http', 'https', 'mailto'])
            ->allowRelativeLinks();

        $this->sanitizer = new HtmlSanitizer($config);
    }

    public function sanitize(?string $html): string
    {
        return $this->sanitizer->sanitize($html ?? '');
    }
}
