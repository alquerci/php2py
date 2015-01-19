<?php

/*
 * (c) Alexandre Quercia <alquerci@email.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Instinct\Php2py;

/**
 * @author Alexandre Quercia <alquerci@email.com>
 */
interface ConverterInterface
{
    /**
     * @param string       $content
     *
     * @return string
     */
    public function convert($content);
}
