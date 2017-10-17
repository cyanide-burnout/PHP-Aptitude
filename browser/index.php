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
  require_once("common.php");
  require_once("../packages.php");
  require_once(LIB_SMARTY_DIR."/Smarty.class.php");

  $locales = array();
  if (array_key_exists("HTTP_ACCEPT_LANGUAGE", $_SERVER))
  {
    $languages = explode(",", $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
    foreach ($languages as $language)
    {
      if (preg_match("/^(\\w{2,3})-(\\w{2})(?:;.+)?$/", $language, $matches))
      {
        $language = $matches[1];
        $region = strtoupper($matches[2]);
        $locales[] = "${language}_${region}";
        continue;
      }
      if (preg_match("/^(\\w{2,3})(?:;.+)?$/", $language, $matches))
      {
        $language = $matches[1];
        $region = strtoupper($language);
        $locales[] = "${language}_${region}";
        continue;
      }
    }
  }
  $locales = implode(",", array_unique(array_merge($locales, explode(",", DEFAULT_LOCALE))));

  $packages = getPackages(DEFAULT_TARGET, $locales);

  function get_section($key)
  {
    $sections = array(
      "user/desktop" => "Desktop",
      "user/development" => "Programming",
      "user/education" => "Education", 	
      "user/games" => "Games",
      "user/graphics" => "Graphics",
      "user/multimedia" => "Multimedia",
      "user/navigation" => "Location & navigation",
      "user/network" => "Internet &amp; Networking",
      "user/office" => "Office",
      "user/science" => "Science",
      "user/system" => "System",
      "user/utilities" => "Utilities"
    );
    if (array_key_exists($key, $sections))
      return $sections[$key];
    if (strpos($key, "user/") === 0)
      return "Other";
    return $key;
  }

  $smarty = new Smarty();
  $smarty->template_dir = dirname(__FILE__);
  $smarty->compile_dir = COMPILE_DIR;
  $smarty->assign("title", REPOSITORY_DESCRIPTION);
  $smarty->assign("target", get_target());
  $smarty->assign("packages", $packages);
  $smarty->display(basename(__FILE__, ".php").".tpl");

?>
