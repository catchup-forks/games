<?php if (!defined('APPLICATION')) exit(); // Make sure this file can't get accessed directly
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

/**
 * class GamersPortalHooks
 *
 * A special function that is automatically run upon enabling this application.
 */
class GamesHooks implements Gdn_IPlugin {
   /**
    * Add pages in the config value array by ID to header site menu.
    */
   public function Base_Render_Before($Sender) {
      /*
	  if($Sender->Menu && $Sender->MasterView != 'admin') {
         $GameModel = new GameModel();
         $Games = $GameModel->GetAllSiteMenuLink()->Result();
         
         foreach($Games as $Game) {
            $Sender->Menu->AddLink('GamersPortal', $Game->Name, $GameModel->GameUrl($Game), FALSE, array('class' => 'Game-' . $Game->UrlCode));
         }
      }
	  */
   }
   
   /**
    * Add links to the settings of this application in the admin dashboard.
    */
   public function Base_GetAppSettingsMenuItems_Handler($Sender) {
      /*
	  $Menu = &$Sender->EventArguments['SideMenu'];
      $Section = 'Games';
      $Namespace = 'managegames/';
      $Menu->AddLink($Section, T('GamersPortal.Settings.AllGames', 'All Games'), $Namespace . 'allgames', 'Garden.Settings.Manage');
      $Menu->AddLink($Section, T('GamersPortal.Settings.NewGame', 'New Game'), $Namespace . 'newgame', 'Garden.Settings.Manage');
	  */
   }
   
   /**
    * Special function automatically run upon clicking 'Enable' on this application.
    */
   public function Setup() {
      // Variables to be used by structure.php.
      $Database = Gdn::Database();
      $Config = Gdn::Factory(Gdn::AliasConfig);
      $Drop = Gdn::Config('GamersPortal.Version') === FALSE ? TRUE : FALSE;
      $Explicit = TRUE;
      
      // Needed by structure.php to validate permission names.
      // $Validation = new Gdn_Validation();
      
      // Call structure.php to update database.
      include(PATH_APPLICATIONS . DS . 'games' . DS . 'settings' . DS . 'structure.php');
   }
   
   /**
    * Special function automatically run upon clicking 'Disable' on this application.
    */
   public function OnDisable() {
      // Optional. Delete this if you don't need it.
   }
   
   /**
    * Special function automatically run upon clicking 'Remove' on this application.
    */
   public function CleanUp() {
      RemoveFromConfig('GamersPortal.Version');
   }
}