<?php

/**
 * DokuWiki Barcodes Plugin
 * Copyright (C) 2023 Matthias Lohr <mail@mlohr.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace DokuWiki\Barcodes;

use ParseError;

class Color {

    private $red;
    private $green;
    private $blue;

    public function __construct($color)
    {
        if (preg_match('/^#?([0-9a-f]{6})/i', $color, $matches)) {
            $this->red = hexdec(substr($matches[1], 0, 2));
            $this->green = hexdec(substr($matches[1], 2, 2));
            $this->blue = hexdec(substr($matches[1], 4, 2));
        }
        else {
            throw new ParseError('Could not parse "' . $color . '" to a color');
        }
    }

    public static function parse($str) {
        return new Color($str);
    }

    public static function str2hex($str) {
        return (new Color($str))->getHex();
    }

    public function getHex() {
        return sprintf("%02X%02X%02X", $this->red, $this->green, $this->blue);
    }

    public function __toString() {
        return $this->getHex();
    }
}
