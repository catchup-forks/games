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
 * The Game controller.
 */
class GameController extends GamesController {
   /** @var array List of objects to prep. They will be available as $this->$Name. */
   public $Uses = array('GameModel');
   
   /**
    * Loads default page view.
    * 
    * @param string $GameUrlCode; Unique page URL stub identifier.
    */
   public function Index($GameUrlCode = '') {
      $Game = $this->GameModel->GetByUrlCode($GameUrlCode);
      
      // If page doesn't exist.
      if($Game == NULL) {
         throw new Exception(sprintf(T('%s Not Found'), T('Game')), 404);
         return NULL;
      }
      
      // Get page data.
      $this->SetData('GameData', $Game);
      
      // Add description meta tag.
      $this->Description(SliceParagraph(Gdn_Format::PlainText($Game->Body, $Game->Format), 160));
      
      // Add modules
      $this->AddModule('GuestModule');
      $this->AddModule('SignedInModule');
      $this->AddModule('NewDiscussionModule');
      $this->AddModule('DiscussionFilterModule');
      $this->AddModule('BookmarkedModule');
      $this->AddModule('DiscussionsModule');
      $this->AddModule('RecentActivityModule');
      
      // Setup head.
      if (!$this->Data('Title')) {
         $Title = C('Garden.HomepageTitle');
         $DefaultControllerRoute = Gdn::Router()->GetRoute('DefaultController')['Destination'];
         if($Title && (strpos($DefaultControllerRoute, 'game/' . $Game->UrlCode) !== FALSE))
            $this->Title($Title, '');
         else
            $this->Title($Game->Name);
      }
      $this->Render();
   }
}
