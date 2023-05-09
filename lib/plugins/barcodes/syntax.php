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

require dirname(__FILE__) . '/vendor/autoload.php';

use DokuWiki\Barcodes\BarcodesWrapper;
use DokuWiki\Barcodes\BarcodesException;
use DokuWiki\Barcodes\Color;

class syntax_plugin_barcodes extends DokuWiki_Syntax_Plugin
{
    public function getType()
    {
        return 'substition';
    }

    public function getSort()
    {
        return 32;
    }

    public function connectTo($mode)
    {
        $this->Lexer->addSpecialPattern('<barcode[^>]*/?>', $mode, 'plugin_barcodes');
    }

    public function handle($match, $state, $pos, Doku_Handler $handler)
    {
        $data = new stdClass();

        // parse <barcode /> element
        try {
            $barcode = new SimpleXMLElement($match);

        }
        catch (Exception $e) {
            $data->error = 'error parsing barcode tag';
            return $data;
        }

        // parse attributes
        $attributes = new stdClass();
        $attributes->type = strtoupper($barcode->attributes()['type']);
        $attributes->value = strval($barcode->attributes()['value']);
        $attributes->img_type = $barcode->attributes()['img-type'] ? strval($barcode->attributes()['img-type']) : $this->getConf('default_img_type');
        $attributes->color = $barcode->attributes()['color'] ? Color::str2hex($barcode->attributes()['color']) : Color::str2hex($this->getConf('default_color'));
        $attributes->background_color = $barcode->attributes()['background-color'] ? Color::str2hex($barcode->attributes()['background-color']) : ($this->getConf('default_background_color') ? Color::str2hex($this->getConf('default_background_color')) : null);
        $attributes->size = preg_match('/^([0-9]+)(px)?$/', $barcode->attributes()['size'], $matches) ? intval($matches[1]) : null;
        $attributes->scale = $barcode->attributes()['scale'] ? floatval($barcode->attributes()['scale']) : 1;
        $attributes->padding = preg_match('/^([0-9]+)(px)?$/', $barcode->attributes()['padding'], $matches) ? intval($matches[1]) : 0;
        $data->attributes = $attributes;

        // return
        return $data;
    }

    public function render($mode, Doku_Renderer $renderer, $data)
    {
        if ($mode == 'xhtml') {
            // check if an parsing error has occured
            if ($data->error) {
                $renderer->doc .= $this->renderErrorMessage($data->error);
                return false;
            }

            // render barcode
            try {
                $barcode = new BarcodesWrapper($data->attributes);
                $renderer->doc .= $barcode->getHtml();
                return true;
            }
            catch (BarcodesException $e) {
                $renderer->doc .= $this->renderErrorMessage($e->getMessage());
                return false;
            }
        }
        return false;
    }

    private function renderErrorMessage($error_message) {
        return '<span style="font-weight: bold; color: red;">&lt;barcode: ' . htmlspecialchars($error_message) . ' /&gt;</span>';
    }
}
