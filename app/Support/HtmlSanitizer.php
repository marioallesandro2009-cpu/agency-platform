<?php

namespace App\Support;

use HTMLPurifier;
use HTMLPurifier_Config;

class HtmlSanitizer
{
    public static function clean(?string $html): ?string
    {
        if ($html === null || $html === "") {
            return $html;
        }

        $config = HTMLPurifier_Config::createDefault();
        $config->set("Cache.SerializerPath", storage_path("app/htmlpurifier"));
        $config->set("HTML.Allowed", "p,br,strong,b,em,i,u,ul,ol,li,a[href],h2,h3,h4,blockquote,img[src|alt],code,pre");
        $config->set("URI.AllowedSchemes", ["http" => true, "https" => true, "mailto" => true]);

        return (new HTMLPurifier($config))->purify($html);
    }
}
