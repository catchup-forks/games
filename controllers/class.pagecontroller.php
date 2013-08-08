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

/**
 * The Page controller.
 */
class PageController extends PagesController {
   /** @var array List of objects to prep. They will be available as $this->$Name. */
   public $Uses = array('PageModel');
   
   /**
    * Loads default page view.
    * 
    * @param string $PageUrlCode; Unique page URL stub identifier.
    */
   public function Index($PageUrlCode = '') {
      $Page = $this->PageModel->GetUrlCode($PageUrlCode);
      
      // If page doesn't exist.
      if($Page == NULL) {
         throw new Exception(sprintf(T('%s Not Found'), T('Page')), 404);
         return NULL;
      }
      
      // Get page data.
      $this->SetData('PageData', $Page);
      
      // Add description meta tag.
      $this->Description(SliceParagraph(Gdn_Format::PlainText($Page->Body, $Page->Format), 160));
      
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
         if($Title && (strpos($DefaultControllerRoute, 'page/' . $Page->UrlCode) !== FALSE))
            $this->Title($Title, '');
         else
            $this->Title($Page->Name);
      }
      $this->Render();
   }
}
