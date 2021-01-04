<?php

namespace GreenCheap\Comment;

class CommentPlugin
{
    public static function onContentPlugins($data)
    {
        // remove all html tags or escape if in [code] tag
        $content = preg_replace_callback('/\[code\](.+?)\[\/code\]/is', function($matches) { return htmlspecialchars($matches[0]); }, $data);
        $content = strip_tags($content);

        $content = ' '.$content.' ';
        $content = preg_replace_callback('/(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)(?:\([-A-Z0-9+&@#\/%=~_|$?!:;,.]*\)|[-A-Z0-9+&@#\/%=~_|$?!:;,.])*(?:\([-A-Z0-9+&@#\/%=~_|$?!:;,.]*\)|[A-Z0-9+&@#\/%=~_|$])/ix', function ($matches) {

            $url = $original_url = $matches[0];

            if (empty($url)) {
                return $url;
            }

            // Prepend scheme if URL appears to contain no scheme (unless a relative link starting with / or a php file).
            if (strpos($url, ':') === false &&	substr($url, 0, 1) != '/' && substr($url, 0, 1) != '#' && !preg_match('/^[a-z0-9-]+?\.php/i', $url)) {
                $url = 'http://' . $url;
            }

            return " <a href=\"$url\" rel=\"nofollow\" target=\"_blank\">$original_url</a>";

        }, $content);

        $content = preg_replace("/\s([a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]*\@[a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]{2,6})([\s|\.|\,])/i"," <a href=\"mailto:$1\" rel=\"nofollow\">$1</a>$2", $content);
        $content = substr($content, 1);
        $content = substr($content, 0, -1);

        $content = preg_replace_callback('/(\*\*|__)(.*?)\1/', function ($matches){
            $text = $matches[2];
            return " <strong>$text</strong>";
        } , $content);

        $content = preg_replace_callback("/`(.*?)`/" , function($matches){
            $code = $matches[1];
            return " <code>$code</code>";
        } , $content);

        return nl2br($content);
    }
}
