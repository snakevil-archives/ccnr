<?php
/**
 * Represents as a novel chapter in `leduwo.com'.
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
 * @copyright © 2012-2013 szen.in
 * @license   http://www.gnu.org/licenses/gpl.html
 */

namespace CCNR\Model\Leduwo_com;

use CCNR\Model;

class Chapter extends Model\Chapter
{
    /**
     * Defines the matched URL pattern.
     *
     * INHERITED from {@link Model\Page::PATTERN}.
     *
     * @var string
     */
    const PATTERN = '~^http://(www\.)?leduwo\.com/book/\d+/\d+/\d+\.html$~';

    /**
     * Parses retrieved content into meta-data.
     *
     * OVERRIDEN FROM {@link Model\Page::parse()}.
     *
     * @param  string  $content
     * @return Chapter
     */
    protected function parse($content)
    {
        settype($content, 'string');
        $content = iconv('gbk', 'utf-8//ignore', $content);
        $s_ret = $this->crop('@乐读窝</a>-&gt;<a href="index.html">@', '@</div>@', $content);
        if (false === $s_ret)
            throw new Model\NovelTitleNotFoundException;
        $a_tmp = explode('</a>-&gt;', $s_ret);
        $this->novelTitle = array_shift($a_tmp);
        $this->title = array_shift($a_tmp);
        $s_ret = $this->crop('@<div id="content">(&nbsp;)*@', '@(&nbsp;)*</div>\s*<div id="footlink">@', $content);
        if (false === $s_ret)
            throw new Model\ParagraphsNotFoundException;
        $this->paragraphs = array();
        if (!strpos($s_ret, '<img src="'))
        {
            $a_tmp = preg_split('@\s*(</p>\s*)?(<br />\s*)+(&nbsp;\s*)*@', $s_ret);
            for ($ii = 0, $jj = count($a_tmp); $ii < $jj; $ii++)
            {
                $a_tmp[$ii] = preg_replace('@^[　]+@u', '', $a_tmp[$ii]);
                if (strlen($a_tmp[$ii]))
                    $this->paragraphs[] = $a_tmp[$ii];
            }
        }
        else if (preg_match_all('@<img src="([^\s"]+)" border="0" class="imagecontent">@', $s_ret, $a_tmp))
        {
            for ($ii = 0, $jj = count($a_tmp[0]); $ii < $jj; $ii++)
                $this->paragraphs[] = '![IMAGE](' . $a_tmp[1][$ii] . ')';
        }
        if (empty($this->paragraphs))
            throw new Model\ParagraphsNotFoundException;
        $this->tocLink = './';
        $s_ret = $this->crop('@\(快捷键：←\)<a href="@', '@">上一页</a>@', $content);
        if (false === $s_ret)
            throw new Model\PrevLinkNotFoundException;
        $this->prevLink = $s_ret;
        if ('index.html' == $this->prevLink)
            $this->prevLink = '';
        $s_ret = $this->crop('@">返回列表</a>\(快捷键：Enter\)(&nbsp;\s*)*<a href="@', '@">下一页</a>@', $content);
        if (false === $s_ret)
            throw new Model\NextLinkNotFoundException;
        $this->nextLink = $s_ret;
        if ('index.html' == $this->nextLink)
            $this->nextLink = '';
        return $this;
    }
}

# vim:se ft=php ff=unix fenc=utf-8 tw=120:
