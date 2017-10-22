<?php


namespace Pfs;


class Helper
{
    public static function slugify($text)
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    public static function getThumbUrl($size)
    {
        global $post;

        $thumb    = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), $size);
        $thumbUrl = $thumb['0'];

        return $thumbUrl;
    }

    public static function getPostTerms($taxonomy)
    {
        $terms     = get_the_terms(get_the_ID(), $taxonomy);
        $termNames = [];

        if ($terms) {
            foreach ($terms as $term) {
                $termNames[] = $term->name;
            }
        }

        return implode(', ', $termNames);
    }

    public static function getTaxLabel($taxonomy)
    {
        return get_taxonomy($taxonomy)->label;
    }

    public static function getTaxSlug($taxonomy)
    {
        $label = self::getTaxLabel($taxonomy);

        return self::slugify($label);
    }
}