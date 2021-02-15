<?php

/*
  Plugin Name: Rs2010 Forum
  Plugin URI: https://www.thomasdeer.co.uk
  Description: Rs2010 Forum is a highly edited version of the Asgaros Forum Plugin Created by Thomas Belser.
  Version: 2.1
  Author: Thomas Deer
  Author URI: https://www.thomasdeer.co.uk
  License: GPL2
  License URI: https://www.gnu.org/licenses/gpl-2.0.html
  Text Domain: rs2010-forum
  Domain Path: /languages

  Rs2010 Forum is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 2 of the License, or
  any later version.

  Rs2010 Forum is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with Rs2010 Forum. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

if (!defined('ABSPATH')) exit;

// Include Rs2010 Forum core files.
require('includes/forum.php');
require('includes/forum-database.php');
require('includes/forum-compatibility.php');
require('includes/forum-rewrite.php');
require('includes/forum-permissions.php');
require('includes/forum-content.php');
require('includes/forum-notifications.php');
require('includes/forum-appearance.php');
require('includes/forum-unread.php');
require('includes/forum-uploads.php');
require('includes/forum-search.php');
require('includes/forum-statistics.php');
require('includes/forum-breadcrumbs.php');
require('includes/forum-editor.php');
require('includes/forum-shortcodes.php');
require('includes/forum-pagination.php');
require('includes/forum-online.php');
require('includes/forum-usergroups.php');
require('includes/forum-profile.php');
require('includes/forum-memberslist.php');
require('includes/forum-reports.php');
require('includes/forum-reactions.php');
require('includes/forum-mentioning.php');
require('includes/forum-activity.php');
require('includes/forum-feed.php');
require('includes/forum-ads.php');
require('includes/forum-approval.php');
require('includes/forum-spoilers.php');
require('includes/forum-polls.php');
require('includes/forum-user-query.php');

// Include widget files.
require('includes/forum-widgets.php');
require('widgets/widget-recent-posts.php');
require('widgets/widget-recent-topics.php');
require('widgets/widget-search.php');

// Include integration files.
require('integrations/integration-mycred.php');

// Include admin files.
require('admin/admin.php');
require('admin/tables/admin-structure-table.php');
require('admin/tables/admin-usergroups-table.php');
require('admin/tables/admin-ads-table.php');

$rs2010forum = new Rs2010Forum();

if (is_admin()) {
    $rs2010forum_admin = new Rs2010ForumAdmin($rs2010forum);
}
