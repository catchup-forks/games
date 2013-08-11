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

$Session = Gdn::Session();

$Game = $this->Data('GameData');
$FormatBody = Gdn_Format::To($Game->Body, $Game->Format);
?>
<div id="Game_<?php echo $Game->gameid; ?>" class="Game-<?php echo $Game->UrlCode; ?>">

      <div class="Options">
         <span class="ToggleFlyout OptionsMenu">
            <span class="OptionsTitle" title="<?php echo T('Options'); ?>"><?php echo T('Options'); ?></span>
            <?php echo Sprite('SpFlyoutHandle', 'Arrow'); ?>
            <ul class="Flyout MenuItems" style="display: none;">
               <?php echo Wrap(Anchor(T('GamersPortal.Settings.EditGame', 'Edit Game'), 'managegames/editgame/' . $Game->gameid, 'EditGame'), 'li'); ?>
            </ul>
         </span>
      </div>

   <h1 class="H"><?php echo $Game->gamename; ?></h1>

   <div id="GameBody"><?php echo $FormatBody; ?></div>




</div>
