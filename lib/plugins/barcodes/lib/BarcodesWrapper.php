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

use jucksearm\barcode\Barcode;
use jucksearm\barcode\Datamatrix;
use jucksearm\barcode\lib\BarcodeFactory;
use jucksearm\barcode\lib\DatamatrixFactory;
use jucksearm\barcode\lib\QRcodeFactory;
use jucksearm\barcode\QRcode;

class BarcodesWrapper {

    private $factory;
    private $attributes;

    const JUCKESARM_BARCODES = array(
        "C39", "C39+", "C39E", "C39E+", "C93", "S25", "S25+", "I25", "I25+", "C128", "C128A", "C128B", "C128C", "EAN2",
        "EAN5", "EAN8", "EAN13", "UPCA", "UPCE", "MSI", "MSI+", "POSTNET", "PLANET", "RMS4CC", "KIX", "IMB", "CODABAR",
        "CODE11", "PHARMA", "PHARMA2T"
    );

    public function __construct($attributes)
    {
        $this->attributes = $attributes;
        $this->applyAttributes();
    }

    /**
     * @throws BarcodesException
     */
    private function applyAttributes() {
        // attribute: type
        if (!$this->attributes->type) {
            throw new BarcodesException('type cannot be empty');
        }
        elseif ($this->attributes->type == "QRCODE") {
            $this->factory = QRcode::factory();
            $this->factory->setMargin(0);
        }
        elseif ($this->attributes->type == "DATAMATRIX") {
            $this->factory = Datamatrix::factory();
            $this->factory->setMargin(0);
        }
        elseif (in_array($this->attributes->type, self::JUCKESARM_BARCODES)) {
            $this->factory = Barcode::factory();
            $this->factory->setType($this->attributes->type);
        }
        else {
            throw new BarcodesException('unsupported type "' . $this->attributes->type . '"');
        }

        // attribute: value
        $this->factory->setCode($this->attributes->value);

        // attribute: color
        $this->factory->setColor($this->attributes->color);

        // attribute: size
        if ($this->attributes->size) {
            if ($this->factory instanceof QRcodeFactory || $this->factory instanceof DatamatrixFactory) {
                $this->factory->setSize($this->attributes->size);
            }
            elseif ($this->factory instanceof BarcodeFactory) {
                $this->factory->setHeight($this->attributes->size);
            }
        }

        // attribute: scale
        if ($this->factory instanceof BarcodeFactory) $this->factory->setScale($this->attributes->scale);
    }

    public function getStyle() {
        $styles = array();

        // attribute: background-color
        if ($this->attributes->background_color) $styles[] = sprintf('background-color: #%s', $this->attributes->background_color);

        // attribute: size
        if ($this->attributes->size) {
            if ($this->factory instanceof QRcodeFactory || $this->factory instanceof DatamatrixFactory) {
                $styles[] = sprintf('width: %dpx', $this->attributes->size);
                $styles[] = sprintf('height: %dpx', $this->attributes->size);
            }
            elseif ($this->factory instanceof BarcodeFactory) {
                $styles[] = sprintf('height: %dpx', $this->attributes->size);
            }
        }

        // attribute: padding
        $styles[] = sprintf('padding: %dpx', $this->attributes->padding);

        // return
        return implode('; ', $styles);
    }

    /**
     * @throws BarcodesException
     */
    public function getImgSrc() {
        if ($this->attributes->img_type == 'svg') {
            if ($this->factory instanceof QRcodeFactory) {
                $svg = $this->factory->getQRcodeSvgData();
            } elseif ($this->factory instanceof DatamatrixFactory) {
                $svg = $this->factory->getDatamatrixSvgData();
            } elseif ($this->factory instanceof BarcodeFactory) {
                $svg = $this->factory->getBarcodeSvgData();
            } else {
                throw new BarcodesException('BUG: Missing factory handler! Please open ticket!');
            }
            return 'data:image/svg+xml;base64,' . base64_encode($svg);
        } elseif ($this->attributes->img_type == 'png') {
            if ($this->factory instanceof QRcodeFactory) {
                $png = $this->factory->getQRcodePngData();
            } elseif ($this->factory instanceof DatamatrixFactory) {
                $png = $this->factory->getDatamatrixPngData();
            } elseif ($this->factory instanceof BarcodeFactory) {
                $png = $this->factory->getBarcodePngData();
            } else {
                throw new BarcodesException('BUG: Missing factory handler! Please open ticket!');
            }
            return 'data:image/png;base64,' . base64_encode($png);
        } else {
            throw new BarcodesException('Unsupported img-type "' . $this->attributes->img_type . '"');
        }
    }

    public function getHtml() {
        return '<img style="' . $this->getStyle() . '" src="' . $this->getImgSrc() . '" />';
    }
}
