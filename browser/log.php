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

  if (isset($_GET["package"]))
  {
    $name = $_GET["package"];
    $packages = getPackages();
    $package = $packages[$name];
    $output = getChangeLog($packages, $name);

    $smarty = new Smarty();
    $smarty->template_dir = dirname(__FILE__);
    $smarty->compile_dir = COMPILE_DIR;
    $smarty->assign("title", REPOSITORY_DESCRIPTION);
    $smarty->assign("target", get_target());
    $smarty->assign("package", $package);
    $smarty->assign("output", $output);
    $smarty->display(basename(__FILE__, ".php").".tpl");
  }

?>
