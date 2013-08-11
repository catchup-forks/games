<?php if (!defined('APPLICATION')) exit();
/**
 * Basic Games - An application for Garden & Vanilla Forums.
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

$Games = $this->Data('GameData')->Result();
?>
<h1><?php echo $this->Data('Title'); ?></h1>

<h3><?php echo T('GamersPortal.Settings.AllGamersPortal.ManageGames', 'Manage Games'); ?></h3>
<div class="Info">
   <?php echo T('GamersPortal.Settings.AllGamersPortal.Info', 'Info'); ?>
   <br /><br />Get started by clicking the button below to create a new game.
</div>
<div class="FilterMenu">
   <?php echo Anchor(T('GamersPortal.Settings.NewGame', 'New Game'), '/managegames/newgame', 'SmallButton'); ?>
</div>

<?php
   if(count($Games) > 0):
?>
      <table class="AltColumns">
         <thead>
            <tr>
               <th><?php echo T('GamersPortal.Settings.GameTitle', 'Game Title'); ?></th>
               <th><?php echo T('GamersPortal.Settings.AllGamersPortal.GameCreated', 'Created'); ?></th>
               <th><?php echo T('GamersPortal.Settings.AllGamersPortal.GameLastUpdated', 'Last Updated'); ?></th>
               <th><?php echo T('GamersPortal.Settings.AllGamersPortal.GameOptions', 'Options'); ?></th>
            </tr>
         </thead>
         <tbody>
            <?php foreach($Games as $Game): ?>
               <tr id="<?php echo 'Game_' . $Game->GameID; ?>">
                  <td><?php
                     echo '<strong>' . $Game->Name . '</strong>';
                     echo '<br />' . Anchor($Game->UrlCode, $this->GameModel->GameUrl($Game));
                  ?></td>
                  <td><?php
                     echo Gdn_Format::Date($Game->DateInserted, 'html');
                     echo ' ' . T('GamersPortal.Settings.AllGamersPortal.By', 'by') . ' ';
                     echo UserAnchor(Gdn::UserModel()->GetID($Game->InsertUserID));
                  ?></td>
                  <td><?php
                     if(($Game->DateUpdated != NULL) && ($Game->UpdateUserID != NULL)) {
                        echo Gdn_Format::Date($Game->DateUpdated, 'html');
                        echo ' ' . T('GamersPortal.Settings.AllGamersPortal.By', 'by') . ' ';
                        echo UserAnchor(Gdn::UserModel()->GetID($Game->UpdateUserID));
                     } else {
                        echo 'N/A';
                     }
                  ?></td>
                  <td><?php
                     echo Anchor(T('GamersPortal.Settings.AllGamersPortal.GameEdit', 'Edit'), '/managegames/editgame/' . $Game->GameID, array('class' => 'SmallButton Edit'));
                     echo ' ';
                     echo Anchor(T('GamersPortal.Settings.AllGamersPortal.GameDelete', 'Delete'), '/managegames/deletegame/' . $Game->GameID, array('class' => 'SmallButton Delete Popup'));
                  ?></td>
               </tr>
            <?php endforeach; ?>
         </tbody>
      </table>
<?php
   else:
      echo '<h3>' . T('GamersPortal.Settings.AllGamersPortal.Games', 'Games') . '</h3>';
      echo '<div class="Info">';
      echo T('GamersPortal.Settings.AllGamersPortal.NoGames', 'No games currently exist. Create a new game by clicking the button above.');
      echo '</div>';
   endif;
?>
