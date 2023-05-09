[![Latest Release](https://gitlab.com/MatthiasLohr/dokuwiki-barcodes/-/badges/release.svg)](https://gitlab.com/MatthiasLohr/dokuwiki-barcodes/-/releases/permalink/latest/downloads/dokuwiki-barcodes.zip)

# Barcodes for DokuWiki

*Barcodes (1D & 2D) for DokuWiki*

## Installation

The recommended way to install this plugin is to use the [DokuWiki Extension Manager](https://www.dokuwiki.org/plugin:extension).
Search for `barcodes` and lick on *Install*.

Alternatively, you can [download the latest version here](https://gitlab.com/MatthiasLohr/dokuwiki-barcodes/-/releases/permalink/latest/downloads/dokuwiki-barcodes.zip) and manually upload and install it to your DokuWiki.
Please take a look [here](https://www.dokuwiki.org/plugins) to learn how to install plugins manually.


## Usage

Create a [QR code](https://en.wikipedia.org/wiki/QR_code), pointing to https://dokuwiki.org:
```xml
<barcode type="QRCODE" value="https://dokuwiki.org" />
```

Alternatively, put the same URL in [Data Matrix](https://en.wikipedia.org/wiki/Data_Matrix):
```xml
<barcode type="DATAMATRIX" value="https://dokuwiki.org" />
```

### Options

The following tables contains a list of attributes, which can be used to modify the different types of supported barcodes.

| Option             | QR Code         | Data Matrix        | Other Barcodes | Description                                                                                                                                                                                                                                                             | Example                                            | 
|--------------------|-----------------|--------------------|----------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|----------------------------------------------------|
| `type`             | `type="QRCODE"` | `type="DATAMATRIX` | `type=...`     | One of: `C39`, `C39+`, `C39E`, `C39E+`, `C93`, `S25`, `S25+`, `I25`, `I25+`, `C128`, `C128A`, `C128B`, `C128C`, `EAN2`, `EAN5`, `EAN8`, `EAN13`, `UPCA`, `UPCE`, `MSI`, `MSI+`, `POSTNET`, `PLANET`, `RMS4CC`, `KIX`, `IMB`, `CODABAR`, `CODE11`, `PHARMA`, `PHARMA2T`. | `type="QRCODE"` `type="DATAMATRIX"` `type="EAN13"` |
| `value`            | required        | required           | required       | Value to be represented by the Barcode.                                                                                                                                                                                                                                 | `value="https://dokuwiki.org"` `value="42"`        |
| `img-type`         | optional        | optional           | optional       | Type of the image to be created. Must be one of `svg` (default) or `png`                                                                                                                                                                                                | `img-type="png"`                                   |
| `color`            | optional        | optional           | optional       | Base color of the barcode.                                                                                                                                                                                                                                              | `color="#FF0000"`                                  |
| `background-color` | optional        | optional           | optional       | Background color of the barcode. Leave empty for transparent.                                                                                                                                                                                                           | `background-color="#FFFFFF"`                       |
| `size`             | optional        | optional           | optional       | Set the size in px (width and height for QR code and Data Matrix, height for other barcodes).                                                                                                                                                                           | `size="64px"`                                      |
| `scale `           | *N.A.*          | *N.A.*             | optional       | Scale the width of the barcode.                                                                                                                                                                                                                                         | `scale="1.5"`                                      |
| `padding`          | optional        | optional           | optional       | Padding of the barcode in px.                                                                                                                                                                                                                                           | `padding="10px"`                                   |

## Development

This plugin is fairly new and therefore does not yet have all the features you might want.
If you are missing a feature, feel invited to submit a [feature request](https://gitlab.com/MatthiasLohr/dokuwiki-barcodes/-/issues/new).


## Change Log

The Changelog is available here: https://gitlab.com/MatthiasLohr/dokuwiki-barcodes/-/blob/main/CHANGELOG.md.


## License

This DokuWiki plugin is published under the [GNU General Public License v3.0](LICENSE.md).

DokuWiki barcodes Plugin
Copyright (C) 2023 by [Matthias Lohr](https://mlohr.com/) &lt;[mail@mlohr.com](mailto:mail@mlohr.com)&gt;

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.


## Attributions

This DokuWiki plugin makes use of the [php-barcode](https://github.com/jucksearm/php-barcode) library from [Jucksearm](https://github.com/jucksearm).
