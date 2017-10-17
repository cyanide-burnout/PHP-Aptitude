<?php

/**
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @version 1.0
 * @copyright Copyright: 2009-2010 Artem Prilutskiy.
 * @author Artem Prilutskiy <cyanide.burnout@gmail.com>
 * @access public
 * @package PHP Aptitude
 */

  require_once("config.php");

  function get_target()
  {
    $agent = $_SERVER["HTTP_USER_AGENT"];
    if (strpos($agent, "Maemo Browser") !== false)
      return "maemo";
    return "default";
  }

  function make_hyperlink($value)
  {
    $expression = "!(((http|https|ftp)\\://)([\\w-]+\\.)*[\\w-]+(\\:[0-9]+)?(/($|[a-zA-Z0-9\\.\\,\\;\\?\\'\\\\\\+&%\\$#\\=~_\\-]+))*)!m";
    return preg_replace($expression, "<a href=\"$1\" target=\"_blank\">$1</a>", $value);
  }
  
  function hide_mail($value)
  {
    $expression = "!(<([\\w._-]+)@([\\w-]+\\.)*[\\w-]+>)!";
    return preg_replace($expression, "", $value);
  }
  

?>
