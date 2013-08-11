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

/**
 * The GamesSettings controller.
 */
class ManageGamesController extends Gdn_Controller {
   /** @var array List of objects to prep. They will be available as $this->$Name. */
   public $Uses = array('GameModel', 'Form');
   
   /**
    * Configures navigation sidebar in Dashboard.
    *
    * @param $CurrentUrl; Path to current location in dashboard.
    */
   private function AddSideMenu($CurrentUrl) {
      // Only add to the assets if this is not a view-only request
      if($this->_DeliveryType == DELIVERY_TYPE_ALL) {
         $SideMenu = new SideMenuModule($this);
         $SideMenu->HtmlId = '';
         $SideMenu->HighlightRoute($CurrentUrl);
         $SideMenu->Sort = C('Garden.DashboardMenu.Sort');
         $this->EventArguments['SideMenu'] = &$SideMenu;
         $this->FireEvent('GetAppSettingsMenuItems');
         $this->AddModule($SideMenu, 'Panel');
      }
   }
   
   /**
    * Loads default view for this controller.
    */
   public function Index() {
      // Check permission
      $this->Permission('Garden.Settings.Manage');
      
      $this->View = 'allgames';
      $this->AllGames();
   }
   
   /**
    * Loads view with list of all games.
    */
   public function AllGames() {
      // Check permission
      $this->Permission('Garden.Settings.Manage');
      
      // Get game data
      $this->SetData('GameData', $this->GameModel->GetAll());
      
      $this->AddSideMenu('managegames/allgames');
      $this->Title(T('GamerPortal.Settings.AllGames', 'All Games'));
      $this->Render();
   }
   
   /**
    * Loads view for creating a new game.
    *
    * @param object $Game; Not NULL when editing a valid game.
    */
   public function NewGame($Game = NULL) {
      // Check permission
      $this->Permission('Garden.Settings.Manage');
      
      $this->AddJsFile('jquery-ui.js');
      $this->AddJsFile('jquery.autogrow.js');
      $this->AddJsFile('managegames-newgame.js');
      
      // Temporary Fix for loading ButtonBar CSS file if ButtonBar is enabled.
      if(Gdn::PluginManager()->CheckPlugin('ButtonBar'))
         $this->AddCssFile('buttonbar.css', 'plugins/ButtonBar');
      
      // Prep Model
      $this->Form->SetModel($this->GameModel);
      
      // Define route variables.
      $RouteExpressionSuffix = '(/.*)?$';
      $RouteTargetSuffix = '$1';
      
      // If form wasn't submitted.
      if($this->Form->IsPostBack() == FALSE) {
         // Prep form with current data for editing
         $FormValues = $this->Form->FormValues();
         
         if(isset($Game)) {
            $this->Form->SetData($Game);
            $this->Form->AddHidden('UrlCodeIsDefined', '1');
            
            if(Gdn::Router()->MatchRoute($Game->UrlCode . $RouteExpressionSuffix)) {
               $this->Form->SetValue('HideGameFromURL', '1');
               $this->Form->SetFormValue('HideGameFromURL', '1');
            }
         } else {
            $this->Form->AddHidden('UrlCodeIsDefined', '0');
         }
      } else {
         // Form was submitted.
         $FormValues = $this->Form->FormValues();
         
         if(isset($Game)) {
            $FormValues['GameID'] = $Game->gameid;
            $this->Form->SetFormValue('GameID', $Game->gameid);
         }
         
         // Validate form values.
         if($FormValues['Name'] == '')
            $this->Form->AddError(T('GamerPortal.Settings.NewGame.ErrorName', 'Game title is required.'), 'Name');
         if($FormValues['Body'] == '')
            $this->Form->AddError(T('GamerPortal.Settings.NewGame.ErrorBody', 'Game body is required.'), 'Body');
         
         // Validate UrlCode.
         if($FormValues['UrlCode'] == '')
            $FormValues['UrlCode'] = $FormValues['Name'];
         $FormValues['UrlCode'] = Gdn_Format::Url($FormValues['UrlCode']);
         $this->Form->SetFormValue('UrlCode', $FormValues['UrlCode']);
         
         $SQL = Gdn::Database()->SQL();
         // Check if editing and if slug is same as one currently set in GameID.
         $ValidGameID = $SQL
            ->Select('p.UrlCode')
            ->From('Game p')
            ->Where('p.GameID', $Game->gameid)
            ->Get()->FirstRow();
         // Make sure that the UrlCode is unique among games.
         $InvalidUrlCode = $SQL
            ->Select('p.GameID')
            ->From('Game p')
            ->Where('p.UrlCode', $FormValues['UrlCode'])
            ->Get()
            ->NumRows();
         if(($InvalidUrlCode && ($ValidGameID->UrlCode != $FormValues['UrlCode']))
               || ((!isset($Game) && $InvalidUrlCode)))
            $this->Form->AddError(T('GamerPortal.Settings.NewGame.ErrorUrlCode', 'The specified URL code is already in use by another game.'), 'UrlCode');
         
         // If all form values are validated.
         if($this->Form->ErrorCount() == 0) {
            $GameID = $this->GameModel->Save($FormValues);
            
            $ValidationResults = $this->GameModel->ValidationResults();
            $this->Form->SetValidationResults($ValidationResults);
            
            // Create and clean up routes for UrlCode.
            if($Game->UrlCode != $FormValues['UrlCode']) {
               if(Gdn::Router()->MatchRoute($Game->UrlCode . $RouteExpressionSuffix))
                  Gdn::Router()->DeleteRoute($Game->UrlCode . $RouteExpressionSuffix);
            }
            if($FormValues['HideGameFromURL'] == '1'
                  && !Gdn::Router()->MatchRoute($FormValues['UrlCode'] . $RouteExpressionSuffix)) {
               Gdn::Router()->SetRoute(
                  $FormValues['UrlCode'] . $RouteExpressionSuffix,
                  'game/' . $FormValues['UrlCode'] . $RouteTargetSuffix,
                  'Internal'
               );
            } elseif($FormValues['HideGameFromURL'] == '0'
                        && Gdn::Router()->MatchRoute($FormValues['UrlCode'] . $RouteExpressionSuffix)) {
               Gdn::Router()->DeleteRoute($FormValues['UrlCode'] . $RouteExpressionSuffix);
            }
            
            if ($this->DeliveryType() == DELIVERY_TYPE_ALL)
               Redirect('managegames/allgames#Game_' . $GameID);
         }
      }
      
      // Setup head.
      if($this->Data('Title')) {
         $this->AddSideMenu();
         $this->Title($this->Data('Title'));
      } else {
         $this->AddSideMenu('managegames/newgame');
         $this->Title(T('GamerPortal.Settings.NewGame', 'New Game'));
      }
      $this->Render();
   }
   
   /**
    * Wrapper for the NewGame view.
    *
    * @param int $GameID; Game ID for getting game data.
    */
   public function EditGame($GameID = NULL) {
      // Check permission
      $this->Permission('Garden.Settings.Manage');
      
      $Game = $this->GameModel->GetID($GameID);
      if($Game != NULL) {
         $this->View = 'newgame';
         $this->Title(T('GamerPortal.Settings.EditGame', 'Edit Game'));
         $this->NewGame($Game);
         return NULL;
      }
      
      throw new Exception(sprintf(T('%s Not Found'), T('Game')), 404);
      return NULL;
   }
   
   /**
    * Loads view for deleting a game.
    *
    * @param int $GameID; Game ID for deleting game data.
    */
   public function DeleteGame($GameID = NULL) {
      // Check permission
      $this->Permission('Garden.Settings.Manage');
      
      $Game = $this->GameModel->GetID($GameID);
      if($Game != NULL) {
         // Form was submitted with OK
         if($this->Form->AuthenticatedPostBack()) {
            $this->GameModel->Delete($GameID);
            
            // Define route variables.
            $RouteExpressionSuffix = '(/.*)?$';
            
            // Clean up routes for UrlCode.
            if(Gdn::Router()->MatchRoute($Game->UrlCode . $RouteExpressionSuffix))
               Gdn::Router()->DeleteRoute($Game->UrlCode . $RouteExpressionSuffix);
            
            if($this->DeliveryType() == DELIVERY_TYPE_ALL) // Full Game
               Redirect('managegames/allgames');
            elseif($this->DeliveryType() == DELIVERY_TYPE_VIEW) // Popup
               $this->RedirectUrl = Url('managegames/allgames');
         }
      
         $this->AddSideMenu();
         $this->Title(T('GamerPortal.Settings.DeleteGame', 'Delete Game'));
         $this->Render();
         return NULL;
      }
      
      throw new Exception(sprintf(T('%s Not Found'), T('Game')), 404);
      return NULL;
   }
   
   /**
    * Include JS, CSS, and modules used by all methods of this controller.
    * Called by dispatcher before controller's requested method.
    */
   public function Initialize() {
      if($this->DeliveryType() == DELIVERY_TYPE_ALL)
         $this->Head = new HeadModule($this);
      $this->AddJsFile('jquery.js');
      $this->AddJsFile('jquery.livequery.js');
      $this->AddJsFile('jquery.form.js');
      $this->AddJsFile('jquery.popup.js');
      $this->AddJsFile('jquery.gardenhandleajaxform.js');
      $this->AddJsFile('global.js');

      $this->AddCssFile('admin.css');
         
      // Call Gdn_Controller's Initialize() as well.
      $this->MasterView = 'admin';
      parent::Initialize();
      Gdn_Theme::Section('Dashboard');
   }
}
