<?php
/**
 * Represents as the novel TOC page view.
 *
 * This file is part of NOVEL.READER.
 *
 * NOVEL.READER is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * NOVEL.READER is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with NOVEL.READER.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package   novel.reader
 * @author    Snakevil Zen <zsnakevil@gmail.com>
 * @copyright © 2012 snakevil.in
 * @license   http://www.gnu.org/licenses/gpl.html
 */

namespace NrView;

use Exception;
use NrModel;
use NrView;

class TOC extends NrView
{
    /**
     * CONSTRUCT FUNCTION
     *
     * OVERRIDEN FROM {@link NrView::__construct()}.
     *
     * @param NrModel\TOC $toc
     */
    public function __construct(NrModel\TOC $toc)
    {
        $this->page = $toc;
    }

    /**
     * Implements magic method.
     *
     * IMPLEMENTED FROM {@link NrView::__toString()}.
     *
     * @return string
     */
    public function __toString()
    {
        $s_chapters = '<a href="' . implode("</a></li>\n<li><a href=\"", array_map(function($url, $title)
                {
                    return $url . '">' . $title;
                }, array_keys($this->page->chapters), array_values($this->page->chapters))) . '</a>';
        $a_tmp = count_chars($this->page->url, 1);
        $s_pshare = str_repeat('../', $a_tmp[47]) . 'share/';
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>{$this->page->title}</title>
<link rel="stylesheet" media="screen" href="{$s_pshare}screen.css" />
</head>
<body>
<h1>{$this->page->title}</h1>
<ol>
<li>{$s_chapters}</li>
</ol>
</body>
</html>
HTML;
    }
}

# vim:se ft=php ff=unix fenc=utf-8 tw=120:
