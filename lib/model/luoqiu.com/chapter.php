<?php
/**
 * Represents as a novel chapter in `luoqiu.com'.
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

namespace NrModel\Luoqiu_com;

use Exception;
use NrModel;

class Chapter extends NrModel\Chapter
{
    /**
     * Defines the matched URL pattern.
     *
     * INHERITED from {@link NrModel\Page::PATTERN}.
     *
     * @var string
     */
    const PATTERN = '~^http://www\.luoqiu\.com/html/\d+/\d+/\d+\.html$~';

    /**
     * Parses retrieved content into meta-data.
     *
     * OVERRIDEN FROM {@link NrModel\Page::parse()}.
     *
     * @param  string  $content
     * @return Chapter
     */
    protected function parse($content)
    {
        settype($content, 'string');
        $content = iconv('gbk', 'utf-8//ignore', $content);
        $s_ret = $this->crop('@<H1 class=bname_content>@', '@</H1>@', $content);
        if (false === $s_ret)
            return $this;
        $this->title = $s_ret;
        $s_ret = $this->crop('@书名：<a href=".*">@', '@</A>@', $content);
        if (false === $s_ret)
            return $this;
        $this->novelTitle = $s_ret;
        $s_ret = $this->crop('@<DIV id=content name="content">(\s*&nbsp;)*@', '@(&nbsp;\s*)*(</p>\s*)?<p></DIV>@', $content);
        if (false === $s_ret)
            return $this;
        $this->paragraphs = array();
        if (!strpos($s_ret, '<img src="'))
        {
            $a_tmp = preg_split('@\s*(<br />\s*)+(&nbsp;\s*)*@', $s_ret);
            for ($ii = 0, $jj = count($a_tmp); $ii < $jj; $ii++)
            {
                $a_tmp[$ii] = trim($a_tmp[$ii], '　');
                if (strlen($a_tmp[$ii]))
                    $this->paragraphs[] = $a_tmp[$ii];
            }
        }
        else if (preg_match_all('@<img src="([^\s"]+)" border="0" class="imagecontent">@', $s_ret, $a_tmp))
        {
            for ($ii = 0, $jj = count($a_tmp[0]); $ii < $jj; $ii++)
                $this->paragraphs[] = '![IMAGE](' . $a_tmp[1][$ii] . ')';
        }
        $this->tocLink = './';
        $s_ret = $this->crop('@\(快捷键:←\)\s*<A href="@', '@">上一页</A>@', $content);
        if (false === $s_ret)
            return $this;
        $this->prevLink = $s_ret;
        if ('index.html' == $this->prevLink)
            $this->prevLink = '';
        $s_ret = $this->crop('@">回书目\(快捷键:Enter\)</A>(&nbsp\s*)*<A href="@', '@">下一页</A>@', $content);
        if (false === $s_ret)
            return $this;
        $this->nextLink = $s_ret;
        if ('index.html' == $this->nextLink)
            $this->nextLink = '';
        return $this;
    }
}

# vim:se ft=php ff=unix fenc=utf-8 tw=120:
