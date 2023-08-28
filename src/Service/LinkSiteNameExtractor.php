<?php

namespace App\Service;

class LinkSiteNameExtractor
{
    const REGEXES = [
        'instagram' => 'https?\://((w{3}?)\.)?(instagram\.com)(.*)',
        'facebook' => 'https?\://((w{3}?)\.)?(facebook\.com)(.*)',
        'twitter' => 'https?\://((w{3}?)\.)?(twitter\.com)(.*)',
        'youtube' => 'https?\://((w{3}?)\.)?(youtube\.com)(.*)'
    ];

    public function getSiteName($link): ?string
    {
        foreach (self::REGEXES as $siteName => $regex) {
            if (preg_match('#^' . $regex . '$#', $link)) {
                return $siteName;
            }
        }
        return null;
    }
}
