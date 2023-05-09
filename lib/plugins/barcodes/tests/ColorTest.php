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

use DokuWiki\Barcodes\Color;
use PHPUnit\Framework\TestCase;

class ColorTest extends TestCase
{
    public function testConstruct() {
        self::assertInstanceOf("DokuWiki\Barcodes\Color", new Color("#FF0000"));

        self::expectException(ParseError::class);
        new Color("#XXXXXX");
    }

    public function testGetHex() {
        self::assertEquals("123456", (new Color("#123456"))->getHex());
        self::assertEquals("FEDCBA", (new Color("#FEDCBA"))->getHex());
    }
}
