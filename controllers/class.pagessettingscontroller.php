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
 * The PagesSettings controller.
 */
class PagesSettingsController extends Gdn_Controller {
   /** @var array List of objects to prep. They will be available as $this->$Name. */
   public $Uses = array('PageModel', 'Form');
   
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
      
      $this->View = 'allpages';
      $this->AllPages();
   }
   
   /**
    * Loads view with list of all pages.
    */
   public function AllPages() {
      // Check permission
      $this->Permission('Garden.Settings.Manage');
      
      // Get page data
      $this->SetData('PageData', $this->PageModel->GetAll());
      
      $this->AddSideMenu('pagessettings/allpages');
      $this->Title(T('BasicPages.Settings.AllPages', 'All Pages'));
      $this->Render();
   }
   
   /**
    * Loads view for creating a new page.
    *
    * @param object $Page; Not NULL when editing a valid page.
    */
   public function NewPage($Page = NULL) {
      // Check permission
      $this->Permission('Garden.Settings.Manage');
      
      $this->AddJsFile('jquery-ui.js');
      $this->AddJsFile('jquery.autogrow.js');
      $this->AddJsFile('pagessettings-newpage.js');
      
      // Temporary Fix for loading ButtonBar CSS file if ButtonBar is enabled.
      if(Gdn::PluginManager()->CheckPlugin('ButtonBar'))
         $this->AddCssFile('buttonbar.css', 'plugins/ButtonBar');
      
      // Prep Model
      $this->Form->SetModel($this->PageModel);
      
      // Define route variables.
      $RouteExpressionSuffix = '(/.*)?$';
      $RouteTargetSuffix = '$1';
      
      // If form wasn't submitted.
      if($this->Form->IsPostBack() == FALSE) {
         // Prep form with current data for editing
         $FormValues = $this->Form->FormValues();
         
         if(isset($Page)) {
            $this->Form->SetData($Page);
            $this->Form->AddHidden('UrlCodeIsDefined', '1');
            
            if(Gdn::Router()->MatchRoute($Page->UrlCode . $RouteExpressionSuffix)) {
               $this->Form->SetValue('HidePageFromURL', '1');
               $this->Form->SetFormValue('HidePageFromURL', '1');
            }
         } else {
            $this->Form->AddHidden('UrlCodeIsDefined', '0');
         }
      } else {
         // Form was submitted.
         $FormValues = $this->Form->FormValues();
         
         if(isset($Page)) {
            $FormValues['PageID'] = $Page->PageID;
            $this->Form->SetFormValue('PageID', $Page->PageID);
         }
         
         // Validate form values.
         if($FormValues['Name'] == '')
            $this->Form->AddError(T('BasicPages.Settings.NewPage.ErrorName', 'Page title is required.'), 'Name');
         if($FormValues['Body'] == '')
            $this->Form->AddError(T('BasicPages.Settings.NewPage.ErrorBody', 'Page body is required.'), 'Body');
         
         // Validate UrlCode.
         if($FormValues['UrlCode'] == '')
            $FormValues['UrlCode'] = $FormValues['Name'];
         $FormValues['UrlCode'] = Gdn_Format::Url($FormValues['UrlCode']);
         $this->Form->SetFormValue('UrlCode', $FormValues['UrlCode']);
         
         $SQL = Gdn::Database()->SQL();
         // Check if editing and if slug is same as one currently set in PageID.
         $ValidPageID = $SQL
            ->Select('p.UrlCode')
            ->From('Page p')
            ->Where('p.PageID', $Page->PageID)
            ->Get()->FirstRow();
         // Make sure that the UrlCode is unique among pages.
         $InvalidUrlCode = $SQL
            ->Select('p.PageID')
            ->From('Page p')
            ->Where('p.UrlCode', $FormValues['UrlCode'])
            ->Get()
            ->NumRows();
         if(($InvalidUrlCode && ($ValidPageID->UrlCode != $FormValues['UrlCode']))
               || ((!isset($Page) && $InvalidUrlCode)))
            $this->Form->AddError(T('BasicPages.Settings.NewPage.ErrorUrlCode', 'The specified URL code is already in use by another page.'), 'UrlCode');
         
         // If all form values are validated.
         if($this->Form->ErrorCount() == 0) {
            $PageID = $this->PageModel->Save($FormValues);
            
            $ValidationResults = $this->PageModel->ValidationResults();
            $this->Form->SetValidationResults($ValidationResults);
            
            // Create and clean up routes for UrlCode.
            if($Page->UrlCode != $FormValues['UrlCode']) {
               if(Gdn::Router()->MatchRoute($Page->UrlCode . $RouteExpressionSuffix))
                  Gdn::Router()->DeleteRoute($Page->UrlCode . $RouteExpressionSuffix);
            }
            if($FormValues['HidePageFromURL'] == '1'
                  && !Gdn::Router()->MatchRoute($FormValues['UrlCode'] . $RouteExpressionSuffix)) {
               Gdn::Router()->SetRoute(
                  $FormValues['UrlCode'] . $RouteExpressionSuffix,
                  'page/' . $FormValues['UrlCode'] . $RouteTargetSuffix,
                  'Internal'
               );
            } elseif($FormValues['HidePageFromURL'] == '0'
                        && Gdn::Router()->MatchRoute($FormValues['UrlCode'] . $RouteExpressionSuffix)) {
               Gdn::Router()->DeleteRoute($FormValues['UrlCode'] . $RouteExpressionSuffix);
            }
            
            if ($this->DeliveryType() == DELIVERY_TYPE_ALL)
               Redirect('pagessettings/allpages#Page_' . $PageID);
         }
      }
      
      // Setup head.
      if($this->Data('Title')) {
         $this->AddSideMenu();
         $this->Title($this->Data('Title'));
      } else {
         $this->AddSideMenu('pagessettings/newpage');
         $this->Title(T('BasicPages.Settings.NewPage', 'New Page'));
      }
      $this->Render();
   }
   
   /**
    * Wrapper for the NewPage view.
    *
    * @param int $PageID; Page ID for getting page data.
    */
   public function EditPage($PageID = NULL) {
      // Check permission
      $this->Permission('Garden.Settings.Manage');
      
      $Page = $this->PageModel->GetID($PageID);
      if($Page != NULL) {
         $this->View = 'newpage';
         $this->Title(T('BasicPages.Settings.EditPage', 'Edit Page'));
         $this->NewPage($Page);
         return NULL;
      }
      
      throw new Exception(sprintf(T('%s Not Found'), T('Page')), 404);
      return NULL;
   }
   
   /**
    * Loads view for deleting a page.
    *
    * @param int $PageID; Page ID for deleting page data.
    */
   public function DeletePage($PageID = NULL) {
      // Check permission
      $this->Permission('Garden.Settings.Manage');
      
      $Page = $this->PageModel->GetID($PageID);
      if($Page != NULL) {
         // Form was submitted with OK
         if($this->Form->AuthenticatedPostBack()) {
            $this->PageModel->Delete($PageID);
            
            // Define route variables.
            $RouteExpressionSuffix = '(/.*)?$';
            
            // Clean up routes for UrlCode.
            if(Gdn::Router()->MatchRoute($Page->UrlCode . $RouteExpressionSuffix))
               Gdn::Router()->DeleteRoute($Page->UrlCode . $RouteExpressionSuffix);
            
            if($this->DeliveryType() == DELIVERY_TYPE_ALL) // Full Page
               Redirect('pagessettings/allpages');
            elseif($this->DeliveryType() == DELIVERY_TYPE_VIEW) // Popup
               $this->RedirectUrl = Url('pagessettings/allpages');
         }
      
         $this->AddSideMenu();
         $this->Title(T('BasicPages.Settings.DeletePage', 'Delete Page'));
         $this->Render();
         return NULL;
      }
      
      throw new Exception(sprintf(T('%s Not Found'), T('Page')), 404);
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
