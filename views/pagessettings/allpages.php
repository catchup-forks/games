<?php if (!defined('APPLICATION')) exit();
/**
 * Basic Pages - An application for Garden & Vanilla Forums.
 * Copyright (C) 2013  Livid Tech
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

$Pages = $this->Data('PageData')->Result();
?>
<h1><?php echo $this->Data('Title'); ?></h1>
<div class="Info">
   <?php echo T('BasicPages.Settings.AllBasicPages.Welcome', 'Welcome to the Basic Pages application by'); ?> <a href="http://lividtech.com/" target="_blank">Livid Tech</a>!
   
   <br /><br /><?php echo T('BasicPages.Settings.AllBasicPages.About1', 'Please consider making a donation to'); ?> Livid Tech <?php echo T('BasicPages.Settings.AllBasicPages.About2', 'in support of using this application.'); ?>
   <br /><?php echo T('BasicPages.Settings.AllBasicPages.About3', 'This is a great way that you can contribute to the community.'); ?>
   <br /><?php echo T('BasicPages.Settings.AllBasicPages.About4', 'Click this button to donate:'); ?> <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=72R6B2BUCMH46" target="_blank"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" alt="" style="vertical-align: middle;" /></a>
</div>

<h3><?php echo T('BasicPages.Settings.AllBasicPages.ManagePages', 'Manage Pages'); ?></h3>
<div class="Info">
   <?php echo T('BasicPages.Settings.AllBasicPages.Info', 'With the Basic Pages application, you can create basic public pages for static content.'); ?>
   <br /><br />Get started by clicking the button below to create a new page.
</div>
<div class="FilterMenu">
   <?php echo Anchor(T('BasicPages.Settings.NewPage', 'New Page'), '/pagessettings/newpage', 'SmallButton'); ?>
</div>

<?php
   if(count($Pages) > 0):
?>
      <table class="AltColumns">
         <thead>
            <tr>
               <th><?php echo T('BasicPages.Settings.PageTitle', 'Page Title'); ?></th>
               <th><?php echo T('BasicPages.Settings.AllBasicPages.PageCreated', 'Created'); ?></th>
               <th><?php echo T('BasicPages.Settings.AllBasicPages.PageLastUpdated', 'Last Updated'); ?></th>
               <th><?php echo T('BasicPages.Settings.AllBasicPages.PageOptions', 'Options'); ?></th>
            </tr>
         </thead>
         <tbody>
            <?php foreach($Pages as $Page): ?>
               <tr id="<?php echo 'Page_' . $Page->PageID; ?>">
                  <td><?php
                     echo '<strong>' . $Page->Name . '</strong>';
                     echo '<br />' . Anchor($Page->UrlCode, $this->PageModel->PageUrl($Page));
                  ?></td>
                  <td><?php
                     echo Gdn_Format::Date($Page->DateInserted, 'html');
                     echo ' ' . T('BasicPages.Settings.AllBasicPages.By', 'by') . ' ';
                     echo UserAnchor(Gdn::UserModel()->GetID($Page->InsertUserID));
                  ?></td>
                  <td><?php
                     if(($Page->DateUpdated != NULL) && ($Page->UpdateUserID != NULL)) {
                        echo Gdn_Format::Date($Page->DateUpdated, 'html');
                        echo ' ' . T('BasicPages.Settings.AllBasicPages.By', 'by') . ' ';
                        echo UserAnchor(Gdn::UserModel()->GetID($Page->UpdateUserID));
                     } else {
                        echo 'N/A';
                     }
                  ?></td>
                  <td><?php
                     echo Anchor(T('BasicPages.Settings.AllBasicPages.PageEdit', 'Edit'), '/pagessettings/editpage/' . $Page->PageID, array('class' => 'SmallButton Edit'));
                     echo ' ';
                     echo Anchor(T('BasicPages.Settings.AllBasicPages.PageDelete', 'Delete'), '/pagessettings/deletepage/' . $Page->PageID, array('class' => 'SmallButton Delete Popup'));
                  ?></td>
               </tr>
            <?php endforeach; ?>
         </tbody>
      </table>
<?php
   else:
      echo '<h3>' . T('BasicPages.Settings.AllBasicPages.Pages', 'Pages') . '</h3>';
      echo '<div class="Info">';
      echo T('BasicPages.Settings.AllBasicPages.NoPages', 'No pages currently exist. Create a new page by clicking the button above.');
      echo '</div>';
   endif;
?>
