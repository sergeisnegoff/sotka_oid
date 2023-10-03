<?php

use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use League\Glide\Urls\UrlBuilderFactory;

if (!function_exists('categoryTree')) {
    function categoryTree($selected = 0, $parent_id = 0, $sub_mark = '', $subcategory = true) {
        $items = \App\Models\Category::where('parent_id', $parent_id)->get();

        foreach ($items as $row) {
            echo '<option value="' .
                $row->id .
                '" ' .
                ($row->id == $selected ? 'selected' : '') .
                ' >' .
                $sub_mark .
                ' ' .
                $row->title .
                '</option>';
            if ($subcategory)
                categoryTree($selected, $row->id, $sub_mark . '---');
        }
    }
}

if (!function_exists('categoryTreeSort')) {
    function categoryTreeSort() {
        return Cache::remember('catalog-categories', 60, function () {
            $categories = $categories ?? Category::query()
                    ->withCount('productReal')
                    ->get()
                    ->groupBy('parent_id');

            function loop(&$categories, $parent): array {
                return ($categories[$parent] ?? collect())
                    ->map(function ($category) use ($categories) {
                        return array_merge(
                            $category->only(['id', 'title', 'parent_id']),
                            [
                                'productsCount' => $category->product_real_count,
                                'children' => loop($categories, $category->id),
                            ]
                        );
                    })->all();
            }

            return loop($categories, 0);
        });
    }
}

if (!function_exists('thumbImg')) {
    function thumbImg($img, $width = 0, $height = 0, $crop = false) {
        $img = str_replace(['\\'], '/', $img);

        $key = "v-LK4WCdhcfcc%jt*VC2cj%nVpu+xQKvLUA%H86kRVk_4bgG8&CWM#k*b_7MUJpmTc=4GFmKFp7=K%67je-skxC5vz+r#xT?62tT?Aw%FtQ4Y3gvnwHTwqhxUh89wCa_";
        $urlBuilder = UrlBuilderFactory::create(
            '/img/' . str_replace(['/'], '.', pathinfo($img, PATHINFO_DIRNAME)),
            $key
        );

        return $urlBuilder->getUrl(basename($img), ['w' => $width, 'h' => $height, 'fit' => $crop ? 'crop' : 'contain']
        );
    }
}

if (!function_exists('slideImg')) {
    function slideImg($img, $width = 0, $height = 0, $crop = false): string
    {
        $img = str_replace(['\\'], '/', $img);

        $urlBuilder = UrlBuilderFactory::create(
            '/storage/' . pathinfo($img, PATHINFO_DIRNAME)
        );

        return $urlBuilder->getUrl(basename($img), ['w' => $width, 'h' => $height]);
    }
}

if (!function_exists('rusDate')) {
    function rusDate($month = 1) {
        $month = (int)$month;
        $months = array(
            1 => 'Января',
            'Февраля',
            'Марта',
            'Апреля',
            'Мая',
            'Июня',
            'Июля',
            'Августа',
            'Сентября',
            'Октября',
            'Ноября',
            'Декабря'
        );

        return $months[$month];
    }
}

if (!function_exists('getImageExtensions')) {
    function getImageExtensions() {
        return array(
            'jpe',
            'jpeg',
            'jpg',
            'png',
            'svg'
        );
    }
}

if (!function_exists('checkSrc')) {
    function checkSrc($url) {
        $headers = @get_headers($url);

        $return = false;
        if (!empty($headers))
            foreach ($headers as $header) {
                $return = stripos($header, '404') === false ? true : false;
            }

        return $return;
    }
}

if (!function_exists('mb_ucfirst') && extension_loaded('mbstring')) {
    /**
     * mb_ucfirst - преобразует первый символ в верхний регистр
     * @param string $str - строка
     * @param string $encoding - кодировка, по-умолчанию UTF-8
     * @return string
     */
    function mb_ucfirst($str, $encoding = 'UTF-8') {
        $str = mb_ereg_replace('^[\ ]+', '', $str);
        $str = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding) .
            mb_substr($str, 1, mb_strlen($str), $encoding);
        return $str;
    }
}
