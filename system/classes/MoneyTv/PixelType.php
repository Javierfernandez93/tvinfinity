<?php

namespace MoneyTv;

class PixelType {
    public static function hasPixel(array $element = null) : bool
    {
        return in_array("pixel_name",array_column($element, "key"));
    }
}