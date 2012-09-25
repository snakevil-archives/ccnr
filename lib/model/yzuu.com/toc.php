<?php
/**
 * Represents as a novel TOC page in `yzuu.com'.
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

namespace NrModel\Yzuu_com;

use Exception;
use NrModel;

class TOC extends NrModel\TOC
{
    /**
     * Defines the matched URL pattern.
     *
     * INHERITED from {@link NrModel\Page::PATTERN}.
     *
     * @var string
     */
    const PATTERN = '~^http://www\.yzuu\.com/look/\d+/?$~';

    /**
     * Parses retrieved content into meta-data.
     *
     * OVERRIDEN FROM {@link NrModel\Page::parse()}.
     *
     * @param  string  $content
     * @return TOC
     */
    protected function parse($content)
    {
        settype($content, 'string');
        $content = iconv('gb2312', 'utf-8//ignore', $content);
        $s_ret = $this->crop('@<h1 class="tc fred"><font size="6">@', '@</font></h1>@', $content);
        if (false === $s_ret)
            return $this;
        $this->title = $s_ret;
        $s_ret = $this->crop('@<h2 class="tc">正文</h2>\s*<ul>@', '@</ul>@', $content);
        if (false === $s_ret ||
            false === preg_match_all('@<li><a href="/look/\d+/(\d+)/".*>(.*)</a></li>@U', $s_ret, $a_tmp)
        )
            return $this;
        $s_prefix = '/' == substr($this->url, -1) ? '' : basename($this->url) . '/';
        $this->chapters = array();
        for ($ii = 0, $jj = count($a_tmp[1]); $ii < $jj; $ii++)
        {
            $this->chapters[$s_prefix . $a_tmp[1][$ii] . '/'] = $a_tmp[2][$ii];
        }
        return $this;
    }
}

# vim:se ft=php ff=unix fenc=utf-8 tw=120: