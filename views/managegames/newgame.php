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
?>
<h1><?php echo $this->Data('Title'); ?></h1>
<?php
echo $this->Form->Open();
echo $this->Form->Errors();
?>
<ul>
   <li>
      <?php
         echo $this->Form->Label(T('GamersPortal.Settings.GameTitle', 'Game Title'), 'Name');
         echo $this->Form->TextBox('Name', array('maxlength' => 100, 'class' => 'InputBox'));
      ?>
   </li>
   <li id="UrlCode">
      <?php
         echo Wrap(T('GamersPortal.Settings.NewGame.GameUrl', 'Game URL:'), 'strong') . ' ';
         echo Gdn::Request()->Url('page', TRUE);
         echo '/';
         echo Wrap(htmlspecialchars($this->Form->GetValue('UrlCode')));
         echo $this->Form->TextBox('UrlCode');
         echo Anchor(T('edit'), '#', 'Edit');
         echo Anchor(T('OK'), '#', 'Save SmallButton');
		?>
   </li>
   <li>
      <?php
			echo $this->Form->Label(T('GamersPortal.Settings.NewGame.GameBody', 'Game Body'), 'Body');
         echo $this->Form->BodyBox('Body', array('Table' => 'Game'));
      ?>
   </li>
   <li>
      <?php echo $this->Form->CheckBox('SiteMenuLink', T('GamersPortal.Settings.NewGame.GameShowSiteMenuLink', 'Show header site menu link?')); ?>
   </li>
   <li>
      <?php echo $this->Form->CheckBox('HideGameFromURL', T('GamersPortal.Settings.NewGame.GameHideGameFromURL', 'Remove "/game" from the URL?')); ?>
   </li>
</ul>
<div class="Buttons">
   <?php
      echo $this->Form->Button(T('GamersPortal.Settings.NewGame.Save', 'Save'), array('class' => 'Button Primary'));
      echo Anchor(T('GamersPortal.Settings.Cancel', 'Cancel'), 'managegames/allgames', 'Button');
   ?>
</div>
<?php echo $this->Form->Close();
