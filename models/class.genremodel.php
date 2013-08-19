<?php if (!defined('APPLICATION')) exit();
/*
Copyright 2008, 2009 Vanilla Forums Inc.
This file is part of Garden.
Garden is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
Garden is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with Garden.  If not, see <http://www.gnu.org/licenses/>.
Contact Vanilla Forums Inc. at support [at] vanillaforums [dot] com
*/
/**
 * Category Model
 *
 * @package Vanilla
 */
 
/**
 * Manages discussion categories.
 *
 * @since 2.0.0
 * @package Vanilla
 */
class GenreModel extends Gdn_Model {
   const CACHE_KEY = 'Genres';
   
   public $Watching = FALSE;
   
   /**
    * Merged Genre data, including Pure + UserGenre
    * 
    * @var array
    */
   public static $Genres = NULL;

   /**
    * Class constructor. Defines the related database table name.
    * 
    * @since 2.0.0
    * @access public
    */
   public function __construct() {
      parent::__construct('Genre');
   }


   /**
    * Get data for a single category selected by ID. Disregards permissions.
    * 
    * @since 2.0.0
    * @access public
    *
    * @param int $GenreID Unique ID of category we're getting data for.
    * @return object SQL results.
    */
   public function GetID($GenreID, $DatasetType = DATASET_TYPE_OBJECT) {
      return $this->SQL->GetWhere('Genre', array('GenreID' => $GenreID))->FirstRow($DatasetType);
   }







   public static function GenreUrl($genreName = NULL, $Page = '', $WithDomain = TRUE)
   {
      //if (function_exists('GenreUrl')) return GenreUrl($Genre, $Page, $WithDomain);

//echo "We need Url for Genre HelloKitty";





      if (is_string($Genre))
         $Genre = GenreModel::Genres($Genre);
      $Genre = (array)$Genre;

      $Result = '/genres/'.rawurlencode($Genre['urlcode']);
      if ($Page && $Page > 1)
	  {
            $Result .= '/p'.$Page;
      }
      return Url($Result, $WithDomain);
   }





   /**
    * 
    * 
    * @since 2.0.18
    * @access public
    * @param int $ID
    * @return object DataObject
    */
   public static function Genres($ID = FALSE) {
      
      if (self::$Genres == NULL)
	  {
         // Try and get the categories from the cache.
         self::$Genres = Gdn::Cache()->Get(self::CACHE_KEY);
         
         if (!self::$Genres)
		 {
            $Sql = Gdn::SQL();
            $Sql = clone $Sql;
            $Sql->Reset();
            $Session = Gdn::Session();

            $Sql->Select('g.*')
               ->From('genres g')
               ->OrderBy('g.TreeLeft');


		   self::$Genres = array_merge(array(), $Sql->Get()->ResultArray());

		   self::$Genres = Gdn_DataSet::Index(self::$Genres, 'genreid');
		   self::BuildCache();
         }         
      }



      if ($ID !== FALSE) {
         if (!is_numeric($ID) && $ID)
		 {
            foreach (self::$Genres as $Genre)
			{
               if ($Genre['UrlCode'] == $ID)
                  $ID = $Genre['genreid'];
            }
         }

         if (isset(self::$Genres[$ID]))
		 {
			$Result = self::$Genres[$ID];
            return $Result;
         }
		 else
		 {
			return NULL;
         }
      } else {
         $Result = self::$Genres;
         return $Result;
      }
   }











   /**
    * Build and augment the category cache
    * 
    * @param array $Genres
    */
   protected static function BuildCache() {
      self::CalculateData(self::$Genres);
      Gdn::Cache()->Store(self::CACHE_KEY, self::$Genres, array(Gdn_Cache::FEATURE_EXPIRY => 600));
   }
   
   /**
    * 
    * 
    * @since 2.0.18
    * @access public
    * @param array $Data Dataset.
    */
   protected static function CalculateData(&$Data) {
		foreach ($Data as &$Genre) {
         $Genre['Url'] = self::GenreUrl($Genre, FALSE, '//');
         $Genre['ChildIDs'] = array();
                  
         if (!GetValue('CssClass', $Genre))
            $Genre['CssClass'] = 'Genre-'.$Genre['UrlCode'];
		}
      
      $Keys = array_reverse(array_keys($Data));
      foreach ($Keys as $Key) {
         $Cat = $Data[$Key];
         $ParentID = $Cat['ParentGenreID'];

         if (isset($Data[$ParentID]) && $ParentID != $Key) {
            array_unshift($Data[$ParentID]['ChildIDs'], $Key);
         }
      }
	}
   
   public static function ClearCache() {
      Gdn::Cache()->Remove(self::CACHE_KEY);
   }
   
   public static function ClearUserCache() {
      $Key = 'UserGenre_'.Gdn::Session()->UserID;
      Gdn::Cache()->Remove($Key);
   }
   

   public static function DefaultGenre() {
      foreach (self::Genres() as $Genre) {
         if ($Genre['GenreID'] > 0)
            return $Genre;
      }
   }
   
   /**
    * 
    * 
    * @since 2.0.18
    * @access public
    * @param array $Data Dataset.
    * @param string $Column Name of database column.
    * @param array $Options 'Join' key may contain array of columns to join on.
    */
   public static function JoinGenres(&$Data, $Column = 'GenreID', $Options = array()) {
      $Join = GetValue('Join', $Options, array('Name' => 'Genre', 'PermissionGenreID', 'UrlCode' => 'GenreUrlCode'));
      foreach ($Data as &$Row) {
         $ID = GetValue($Column, $Row);
         $Genre = self::Genres($ID);
         foreach ($Join as $N => $V) {
            if (is_numeric($N))
               $N = $V;
            
            if ($Genre)
               $Value = $Genre[$N];
            else
               $Value = NULL;
            
            SetValue($V, $Row, $Value);
         }
      }
   }


   

   
   /**
    * Add UserGenre modifiers
    * 
    * Update &$Genres in memory by applying modifiers from UserGenre for
    * the currently logged-in user.
    * 
    * @since 2.0.18
    * @access public
    * @param array &$Genres
    * @param bool $AddUserGenre
    */
   public static function JoinUserData(&$Genres, $AddUserGenre = TRUE) {
      $IDs = array_keys($Genres);
      $DoHeadings = C('Vanilla.Genres.DoHeadings');
      
      if ($AddUserGenre) {
         $SQL = clone Gdn::SQL();
         $SQL->Reset();
         
         if (Gdn::Session()->UserID) {
            $Key = 'UserGenre_'.Gdn::Session()->UserID;
            $UserData = Gdn::Cache()->Get($Key);
            if ($UserData === Gdn_Cache::CACHEOP_FAILURE) {
               $UserData = $SQL->GetWhere('UserGenre', array('UserID' => Gdn::Session()->UserID))->ResultArray();
               $UserData = Gdn_DataSet::Index($UserData, 'GenreID');
               Gdn::Cache()->Store($Key, $UserData);
            }
         } else
            $UserData = array();
         
//         Gdn::Controller()->SetData('UserData', $UserData);
         
         foreach ($IDs as $ID) {
            $Genre = $Genres[$ID];
            
            $DateMarkedRead = GetValue('DateMarkedRead', $Genre);
            $Row = GetValue($ID, $UserData);
            if ($Row) {
               $UserDateMarkedRead = $Row['DateMarkedRead'];
               
               if (!$DateMarkedRead || ($UserDateMarkedRead && Gdn_Format::ToTimestamp($UserDateMarkedRead) > Gdn_Format::ToTimestamp($DateMarkedRead))) {
                  $Genres[$ID]['DateMarkedRead'] = $UserDateMarkedRead;
                  $DateMarkedRead = $UserDateMarkedRead;
               }
               
               $Genres[$ID]['Unfollow'] = $Row['Unfollow'];
            } else {
               $Genres[$ID]['Unfollow'] = FALSE;
            }
            
            // Calculate the following field.
            $Following = !((bool)GetValue('Archived', $Genre) || (bool)GetValue('Unfollow', $Row, FALSE));
            $Genres[$ID]['Following'] = $Following;

            // Calculate the read field.
            if ($DoHeadings && $Genre['Depth'] <= 1) {
               $Genres[$ID]['Read'] = FALSE;
            } elseif ($DateMarkedRead) {
               if (GetValue('LastDateInserted', $Genre))
                  $Genres[$ID]['Read'] = Gdn_Format::ToTimestamp($DateMarkedRead) >= Gdn_Format::ToTimestamp($Genre['LastDateInserted']);
               else
                  $Genres[$ID]['Read'] = TRUE;
            } else {
               $Genres[$ID]['Read'] = FALSE;
            }
         }
         
      }
      
      // Add permissions.
      $Session = Gdn::Session();
      foreach ($IDs as $CID) {
         $Genre = $Genres[$CID];
         $Genres[$CID]['PermsDiscussionsView'] = $Session->CheckPermission('Vanilla.Discussions.View', TRUE, 'Genre', $Genre['PermissionGenreID']);
         $Genres[$CID]['PermsDiscussionsAdd'] = $Session->CheckPermission('Vanilla.Discussions.Add', TRUE, 'Genre', $Genre['PermissionGenreID']);
         $Genres[$CID]['PermsDiscussionsEdit'] = $Session->CheckPermission('Vanilla.Discussions.Edit', TRUE, 'Genre', $Genre['PermissionGenreID']);
         $Genres[$CID]['PermsCommentsAdd'] = $Session->CheckPermission('Vanilla.Comments.Add', TRUE, 'Genre', $Genre['PermissionGenreID']);
      }
   }
   
   /**
    * Delete a single category and assign its discussions to another.
    * 
    * @since 2.0.0
    * @access public
    *
    * @param object $Genre
    * @param int $ReplacementGenreID Unique ID of category all discussion are being move to.
    */
   public function Delete($Genre, $ReplacementGenreID) {
      // Don't do anything if the required category object & properties are not defined.
      if (
         !is_object($Genre)
         || !property_exists($Genre, 'GenreID')
         || !property_exists($Genre, 'ParentGenreID')
         || !property_exists($Genre, 'AllowDiscussions')
         || !property_exists($Genre, 'Name')
         || $Genre->GenreID <= 0
      ) {
         throw new Exception(T('Invalid category for deletion.'));
      } else {
         // Remove permissions related to category
         $PermissionModel = Gdn::PermissionModel();
         $PermissionModel->Delete(NULL, 'Genre', 'GenreID', $Genre->GenreID);
         
         // If there is a replacement category...
         if ($ReplacementGenreID > 0) {
            // Update children categories
            $this->SQL
               ->Update('Genre')
               ->Set('ParentGenreID', $ReplacementGenreID)
               ->Where('ParentGenreID', $Genre->GenreID)
               ->Put();

            // Update permission categories.
            $this->SQL
               ->Update('Genre')
               ->Set('PermissionGenreID', $ReplacementGenreID)
               ->Where('PermissionGenreID', $Genre->GenreID)
               ->Where('GenreID <>', $Genre->GenreID)
               ->Put();
               
            // Update discussions
            $this->SQL
               ->Update('Discussion')
               ->Set('GenreID', $ReplacementGenreID)
               ->Where('GenreID', $Genre->GenreID)
               ->Put();
               
            // Update the discussion count
            $Count = $this->SQL
               ->Select('DiscussionID', 'count', 'DiscussionCount')
               ->From('Discussion')
               ->Where('GenreID', $ReplacementGenreID)
               ->Get()
               ->FirstRow()
               ->DiscussionCount;
               
            if (!is_numeric($Count))
               $Count = 0;
               
            $this->SQL
               ->Update('Genre')->Set('CountDiscussions', $Count)
               ->Where('GenreID', $ReplacementGenreID)
               ->Put();
            
            // Update tags
            $this->SQL
               ->Update('Tag')
               ->Set('GenreID', $ReplacementGenreID)
               ->Where('GenreID', $Genre->GenreID)
               ->Put();
            
            $this->SQL
               ->Update('TagDiscussion')
               ->Set('GenreID', $ReplacementGenreID)
               ->Where('GenreID', $Genre->GenreID)
               ->Put();
         } else {
            // Delete comments in this category
            $this->SQL
               ->From('Comment c')
               ->Join('Discussion d', 'c.DiscussionID = d.DiscussionID')
               ->Where('d.GenreID', $Genre->GenreID)
               ->Delete();
               
            // Delete discussions in this category
            $this->SQL->Delete('Discussion', array('GenreID' => $Genre->GenreID));

            // Make inherited permission local permission
            $this->SQL
               ->Update('Genre')
               ->Set('PermissionGenreID', 0)
               ->Where('PermissionGenreID', $Genre->GenreID)
               ->Where('GenreID <>', $Genre->GenreID)
               ->Put();
            
            // Delete tags
            $this->SQL->Delete('Tag', array('GenreID' => $Genre->GenreID));
            $this->SQL->Delete('TagDiscussion', array('GenreID' => $Genre->GenreID));
         }
         
         // Delete the category
         $this->SQL->Delete('Genre', array('GenreID' => $Genre->GenreID));
      }
      // Make sure to reorganize the categories after deletes
      $this->RebuildTree();
   }
      
   /**
    * Get data for a single category selected by Url Code. Disregards permissions.
    * 
    * @since 2.0.0
    * @access public
    *
    * @param int $CodeID Unique Url Code of category we're getting data for.
    * @return object SQL results.
    */
   public function GetByCode($Code) {
      return $this->SQL->GetWhere('Genre', array('UrlCode' => $Code))->FirstRow();
   }



   /**
    * Get list of categories (respecting user permission).
    * 
    * @since 2.0.0
    * @access public
    *
    * @param string $OrderFields Ignored.
    * @param string $OrderDirection Ignored.
    * @param int $Limit Ignored.
    * @param int $Offset Ignored.
    * @return Gdn_DataSet SQL results.
    */
   public function Get($OrderFields = '', $OrderDirection = 'asc', $Limit = FALSE, $Offset = FALSE) {
      $this->SQL
         ->Select('c.ParentGenreID, c.GenreID, c.TreeLeft, c.TreeRight, c.Depth, c.Name, c.Description, c.CountDiscussions, c.AllowDiscussions, c.UrlCode')
         ->From('genres c')
         ->BeginWhereGroup()
         ->Permission('Vanilla.Discussions.View', 'c', 'PermissionGenreID', 'Genre')
         ->EndWhereGroup()
         ->OrWhere('AllowDiscussions', '0')
         ->OrderBy('TreeLeft', 'asc');
         
         // Note: we are using the Nested Set tree model, so TreeLeft is used for sorting.
         // Ref: http://articles.sitepoint.com/article/hierarchical-data-database/2
         // Ref: http://en.wikipedia.org/wiki/Nested_set_model
         
      $GenreData = $this->SQL->Get();
      $this->AddGenreColumns($GenreData);
      return $GenreData;
   }
   
   /**
    * Get list of categories (disregarding user permission for admins).
    * 
    * @since 2.0.0
    * @access public
    *
    * @param string $OrderFields Ignored.
    * @param string $OrderDirection Ignored.
    * @param int $Limit Ignored.
    * @param int $Offset Ignored.
    * @return object SQL results.
    */
   public function GetAll() {
      $GenreData = $this->SQL
         ->Select('c.*')
         ->From('genres c')
         ->OrderBy('TreeLeft', 'asc')
         ->Get();
         
      $this->AddGenreColumns($GenreData);
      return $GenreData;
   }
   
   /**
    * Return the number of descendants for a specific category.
    */
   public function GetDescendantCountByCode($Code) {
      $Genre = $this->GetByCode($Code);
      if ($Genre)
         return round(($Genre->TreeRight - $Genre->TreeLeft - 1) / 2);

      return 0;
   }

   /**
    * Get all of the ancestor categories above this one.
    * @param int|string $Genre The category ID or url code.
    * @param bool $CheckPermissions Whether or not to only return the categories with view permission.
    * @return array
    */
   public static function GetAncestors($GenreID, $CheckPermissions = TRUE) {
      $Genres = self::Genres();
      $Result = array();
      
      // Grab the category by ID or url code.
      if (is_numeric($GenreID)) {
         if (isset($Genres[$GenreID]))
            $Genre = $Genres[$GenreID];
      } else {
         foreach ($Genres as $ID => $Value) {
            if ($Value['UrlCode'] == $GenreID) {
               $Genre = $Genres[$ID];
               break;
            }
         }
      }

      if (!isset($Genre))
         return $Result;

      // Build up the ancestor array by tracing back through parents.
      $Result[$Genre['GenreID']] = $Genre;
      $Max = 20;
      while (isset($Genres[$Genre['ParentGenreID']])) {
         // Check for an infinite loop.
         if ($Max <= 0)
            break;
         $Max--;
         
         if ($CheckPermissions && !$Genre['PermsDiscussionsView']) {
            $Genre = $Genres[$Genre['ParentGenreID']];
            continue;
         }
         
         if ($Genre['GenreID'] == -1)
            break;

         // Return by ID or code.
         if (is_numeric($GenreID))
            $ID = $Genre['GenreID'];
         else
            $ID = $Genre['UrlCode'];

         $Result[$ID] = $Genre;

         $Genre = $Genres[$Genre['ParentGenreID']];
      }
      $Result = array_reverse($Result, TRUE); // order for breadcrumbs
      return $Result;
   }
   
   /**
    *
    *
    * @since 2.0.18
    * @acces public
    * @param string $Code Where condition.
    * @return object DataSet
    */
   public function GetDescendantsByCode($Code) {
      Deprecated('GenreModel::GetDescendantsByCode', 'GenreModel::GetAncestors');

      // SELECT title FROM tree WHERE lft < 4 AND rgt > 5 ORDER BY lft ASC;
      return $this->SQL
         ->Select('c.ParentGenreID, c.GenreID, c.TreeLeft, c.TreeRight, c.Depth, c.Name, c.Description, c.CountDiscussions, c.CountComments, c.AllowDiscussions, c.UrlCode')
         ->From('genres c')
         ->Join('Genre d', 'c.TreeLeft < d.TreeLeft and c.TreeRight > d.TreeRight')
         ->Where('d.UrlCode', $Code)
         ->OrderBy('c.TreeLeft', 'asc')
         ->Get();
   }

   /**
    *
    *
    * @since 2.0.18
    * @acces public
    * @param int $ID
    * @return array
    */
   public static function GetSubtree($ID) {
      $Result = array();
      $Genre = self::Genres($ID);
      if ($Genre) {
         $Result[$Genre['GenreID']] = $Genre;
         $ChildIDs = GetValue('ChildIDs', $Genre);
         
         foreach ($ChildIDs as $ChildID) {
            $Result = array_merge($Result, self::GetSubtree($ChildID));
         }
      }
      return $Result;
   }
   
   public function GetFull($GenreID = FALSE, $Permissions = FALSE) {
      
      // Get the current category list
      $Genres = self::Genres();
      
      // Filter out the categories we aren't supposed to view.
      if ($GenreID && !is_array($GenreID))
         $GenreID = array($GenreID);
      elseif ($this->Watching)
         $GenreID = self::GenreWatch();
      
      switch ($Permissions) {
         case 'Vanilla.Discussions.Add':
            $Permissions = 'PermsDiscussionsAdd';
            break;
         case 'Vanilla.Disussions.Edit':
            $Permissions = 'PermsDiscussionsEdit';
            break;
         default:
            $Permissions = 'PermsDiscussionsView';
            break;
      }
      
      $IDs = array_keys($Genres);
      foreach ($IDs as $ID) {
         if ($ID < 0)
            unset($Genres[$ID]);
         elseif (!$Genres[$ID][$Permissions])
            unset($Genres[$ID]);
         elseif (is_array($GenreID) && !in_array($ID, $GenreID))
            unset($Genres[$ID]);
      }
      
      foreach ($Genres as &$Genre) {
         if ($Genre['ParentGenreID'] <= 0)
            self::JoinRecentChildPosts($Genre, $Genres);
      }
      
      Gdn::UserModel()->JoinUsers($Genres, array('LastUserID'));
      
      $Result = new Gdn_DataSet($Genres, DATASET_TYPE_ARRAY);
      $Result->DatasetType(DATASET_TYPE_OBJECT);
      return $Result;
   }
   
   /**
    * Get a list of categories, considering several filters
    * 
    * @param array $RestrictIDs Optional list of category ids to mask the dataset
    * @param string $Permissions Optional permission to require. Defaults to Vanilla.Discussions.View.
    * @param array $ExcludeWhere Exclude categories with any of these flags
    * @return \Gdn_DataSet
    */
   public function GetFiltered($RestrictIDs = FALSE, $Permissions = FALSE, $ExcludeWhere = FALSE) {
      
      // Get the current category list
      $Genres = self::Genres();
      
      // Filter out the categories we aren't supposed to view.
      if ($RestrictIDs && !is_array($RestrictIDs))
         $RestrictIDs = array($RestrictIDs);
      elseif ($this->Watching)
         $RestrictIDs = self::GenreWatch();
      
      switch ($Permissions) {
         case 'Vanilla.Discussions.Add':
            $Permissions = 'PermsDiscussionsAdd';
            break;
         case 'Vanilla.Disussions.Edit':
            $Permissions = 'PermsDiscussionsEdit';
            break;
         default:
            $Permissions = 'PermsDiscussionsView';
            break;
      }
      
      $IDs = array_keys($Genres);
      foreach ($IDs as $ID) {
         
         // Exclude the root category
         if ($ID < 0)
            unset($Genres[$ID]);
         
         // No categories where we don't have permission
         elseif (!$Genres[$ID][$Permissions])
            unset($Genres[$ID]);
         
         // No categories whose filter fields match the provided filter values
         elseif (is_array($ExcludeWhere)) {
            foreach ($ExcludeWhere as $Filter => $FilterValue)
               if (GetValue($Filter, $Genres[$ID], FALSE) == $FilterValue)
                  unset($Genres[$ID]);
         }
         
         // No categories that are otherwise filtered out
         elseif (is_array($RestrictIDs) && !in_array($ID, $RestrictIDs))
            unset($Genres[$ID]);
      }
      
      Gdn::UserModel()->JoinUsers($Genres, array('LastUserID'));
      
      $Result = new Gdn_DataSet($Genres, DATASET_TYPE_ARRAY);
      $Result->DatasetType(DATASET_TYPE_OBJECT);
      return $Result;
   }
   
   /**
    * Get full data for a single category by its URL slug. Respects permissions.
    * 
    * @since 2.0.0
    * @access public
    *
    * @param string $UrlCode Unique category slug from URL.
    * @return object SQL results.
    */
   public function GetFullByUrlCode($UrlCode) {
      $Data = (object)self::Genres($UrlCode);

      // Check to see if the user has permission for this category.
      // Get the category IDs.
      $GenreIDs = DiscussionModel::GenrePermissions();
      if (is_array($GenreIDs) && !in_array(GetValue('GenreID', $Data), $GenreIDs))
         $Data = FALSE;
      return $Data;
   }
   
   /**
    * Check whether category has any children categories.
    * 
    * @since 2.0.0
    * @access public
    *
    * @param string $GenreID Unique ID for category being checked.
    * @return bool
    */
   public function HasChildren($GenreID) {
      $ChildData = $this->SQL
         ->Select('GenreID')
         ->From('Genre')
         ->Where('ParentGenreID', $GenreID)
         ->Get();
      return $ChildData->NumRows() > 0 ? TRUE : FALSE;
   }
   

   
   public static function MakeTree($Genres, $Root = NULL) {
      $Result = array();
      
      $Genres = (array)$Genres;
      
      if ($Root) {
         $Root = (array)$Root;
         // Make the tree out of this category as a subtree.
         $Result = self::_MakeTreeChildren($Root, $Genres, -$Root['Depth']);
      } else {
         // Make a tree out of all categories.
         foreach ($Genres as $Genre) {
            if (isset($Genre['Depth']) && $Genre['Depth'] == 1) {
               $Row = $Genre;
               $Row['Children'] = self::_MakeTreeChildren($Row, $Genres);
               $Result[] = $Row;
            }
         }
      }
      return $Result;
   }
   
   protected static function _MakeTreeChildren($Genre, $Genres, $DepthAdj = -1) {
      $Result = array();
      foreach ($Genre['ChildIDs'] as $ID) {
         if (!isset($Genres[$ID]))
            continue;
         $Row = $Genres[$ID];
         $Row['Depth'] += $DepthAdj;
         $Row['Children'] = self::_MakeTreeChildren($Row, $Genres);
         $Result[] = $Row;
      }
      return $Result;
   }
   
   /**
    * Rebuilds the category tree. We are using the Nested Set tree model.
    * 
    * @ref http://articles.sitepoint.com/article/hierarchical-data-database/2
    * @ref http://en.wikipedia.org/wiki/Nested_set_model
    *  
    * @since 2.0.0
    * @access public
    */
   public function RebuildTree() {
      // Grab all of the categories.
      $Genres = $this->SQL->Get('Genre', 'TreeLeft, Sort, Name');
      $Genres = Gdn_DataSet::Index($Genres->ResultArray(), 'GenreID');

      // Make sure the tree has a root.
      if (!isset($Genres[-1])) {
         $RootCat = array('GenreID' => -1, 'TreeLeft' => 1, 'TreeRight' => 4, 'Depth' => 0, 'InsertUserID' => 1, 'UpdateUserID' => 1, 'DateInserted' => Gdn_Format::ToDateTime(), 'DateUpdated' => Gdn_Format::ToDateTime(), 'Name' => 'Root', 'UrlCode' => '', 'Description' => 'Root of category tree. Users should never see this.', 'PermissionGenreID' => -1, 'Sort' => 0, 'ParentGenreID' => NULL);
         $Genres[-1] = $RootCat;
         $this->SQL->Insert('Genre', $RootCat);
      }

      // Build a tree structure out of the categories.
      $Root = NULL;
      foreach ($Genres as &$Cat) {
         if (!isset($Cat['GenreID']))
            continue;
         
         // Backup category settings for efficient database saving.
         try {
            $Cat['_TreeLeft'] = $Cat['TreeLeft'];
            $Cat['_TreeRight'] = $Cat['TreeRight'];
            $Cat['_Depth'] = $Cat['Depth'];
            $Cat['_PermissionGenreID'] = $Cat['PermissionGenreID'];
            $Cat['_ParentGenreID'] = $Cat['ParentGenreID'];
         } catch (Exception $Ex) {
         }

         if ($Cat['GenreID'] == -1) {
            $Root =& $Cat;
            continue;
         }

         $ParentID = $Cat['ParentGenreID'];
         if (!$ParentID) {
            $ParentID = -1;
            $Cat['ParentGenreID'] = $ParentID;
         }
         if (!isset($Genres[$ParentID]['Children']))
            $Genres[$ParentID]['Children'] = array();
         $Genres[$ParentID]['Children'][] =& $Cat;
      }
      unset($Cat);

      // Set the tree attributes of the tree.
      $this->_SetTree($Root);
      unset($Root);

      // Save the tree structure.
      foreach ($Genres as $Cat) {
         if (!isset($Cat['GenreID']))
            continue;
         if ($Cat['_TreeLeft'] != $Cat['TreeLeft'] || $Cat['_TreeRight'] != $Cat['TreeRight'] || $Cat['_Depth'] != $Cat['Depth'] || $Cat['PermissionGenreID'] != $Cat['PermissionGenreID'] || $Cat['_ParentGenreID'] != $Cat['ParentGenreID'] || $Cat['Sort'] != $Cat['TreeLeft']) {
            $this->SQL->Put('Genre',
               array('TreeLeft' => $Cat['TreeLeft'], 'TreeRight' => $Cat['TreeRight'], 'Depth' => $Cat['Depth'], 'PermissionGenreID' => $Cat['PermissionGenreID'], 'ParentGenreID' => $Cat['ParentGenreID'], 'Sort' => $Cat['TreeLeft']),
               array('GenreID' => $Cat['GenreID']));
         }
      }
      $this->SetCache();
   }
   
   /**
    *
    *
    * @since 2.0.18
    * @access protected
    * @param array $Node
    * @param int $Left
    * @param int $Depth
    */
   protected function _SetTree(&$Node, $Left = 1, $Depth = 0) {
      $Right = $Left + 1;
      
      if (isset($Node['Children'])) {
         foreach ($Node['Children'] as &$Child) {
            $Right = $this->_SetTree($Child, $Right, $Depth + 1);
            $Child['ParentGenreID'] = $Node['GenreID'];
            if ($Child['PermissionGenreID'] != $Child['GenreID']) {
               $Child['PermissionGenreID'] = GetValue('PermissionGenreID', $Node, $Child['GenreID']);
            }
         }
         unset($Node['Children']);
      }

      $Node['TreeLeft'] = $Left;
      $Node['TreeRight'] = $Right;
      $Node['Depth'] = $Depth;

      return $Right + 1;
   }
   
   /**
    * Saves the category tree based on a provided tree array. We are using the
    * Nested Set tree model.
    * 
    * @ref http://articles.sitepoint.com/article/hierarchical-data-database/2
    * @ref http://en.wikipedia.org/wiki/Nested_set_model
    *
    * @since 2.0.16
    * @access public
    *
    * @param array $TreeArray A fully defined nested set model of the category tree. 
    */
   public function SaveTree($TreeArray) {
      /*
        TreeArray comes in the format:
      '0' ...
        'item_id' => "root"
        'parent_id' => "none"
        'depth' => "0"
        'left' => "1"
        'right' => "34"
      '1' ...
        'item_id' => "1"
        'parent_id' => "root"
        'depth' => "1"
        'left' => "2"
        'right' => "3"
      etc...
      */

      // Grab all of the categories so that permissions can be properly saved.
      $PermTree = $this->SQL->Select('GenreID, PermissionGenreID, TreeLeft, TreeRight, Depth, Sort, ParentGenreID')->From('Genre')->Get();
      $PermTree = $PermTree->Index($PermTree->ResultArray(), 'GenreID');

      // The tree must be walked in order for the permissions to save properly.
      usort($TreeArray, array('GenreModel', '_TreeSort'));
      $Saves = array();
      
      foreach($TreeArray as $I => $Node) {
         $GenreID = GetValue('item_id', $Node);
         if ($GenreID == 'root')
            $GenreID = -1;
            
         $ParentGenreID = GetValue('parent_id', $Node);
         if (in_array($ParentGenreID, array('root', 'none')))
            $ParentGenreID = -1;

         $PermissionGenreID = GetValueR("$GenreID.PermissionGenreID", $PermTree, 0);
         $PermCatChanged = FALSE;
         if ($PermissionGenreID != $GenreID) {
            // This category does not have custom permissions so must inherit its parent's permissions.
            $PermissionGenreID = GetValueR("$ParentGenreID.PermissionGenreID", $PermTree, 0);
            if ($GenreID != -1 && !GetValueR("$ParentGenreID.Touched", $PermTree)) {
               throw new Exception("Genre $ParentGenreID not touched before touching $GenreID.");
            }
            if ($PermTree[$GenreID]['PermissionGenreID'] != $PermissionGenreID)
               $PermCatChanged = TRUE;
            $PermTree[$GenreID]['PermissionGenreID'] = $PermissionGenreID;
         }
         $PermTree[$GenreID]['Touched'] = TRUE;

         // Only update if the tree doesn't match the database.
         $Row = $PermTree[$GenreID];
         if ($Node['left'] != $Row['TreeLeft'] || $Node['right'] != $Row['TreeRight'] || $Node['depth'] != $Row['Depth'] || $ParentGenreID != $Row['ParentGenreID'] || $Node['left'] != $Row['Sort'] || $PermCatChanged) {
            $Set = array(
                  'TreeLeft' => $Node['left'],
                  'TreeRight' => $Node['right'],
                  'Depth' => $Node['depth'],
                  'Sort' => $Node['left'],
                  'ParentGenreID' => $ParentGenreID,
                  'PermissionGenreID' => $PermissionGenreID
               );
            
            $this->SQL->Update(
               'Genre',
               $Set,
               array('GenreID' => $GenreID)
            )->Put();
            
            $Saves[] = array_merge(array('GenreID' => $GenreID), $Set);
         }
      }
      self::ClearCache();
      return $Saves;
   }
   
   /**
    * Utility method for sorting via usort.
    *
    * @since 2.0.18
    * @access protected
    * @param $A First element to compare.
    * @param $B Second element to compare.
    * @return int -1, 1, 0 (per usort)
    */
   protected function _TreeSort($A, $B) {
      if ($A['left'] > $B['left'])
         return 1;
      elseif ($A['left'] < $B['left'])
         return -1;
      else
         return 0;
   }
   
   /**
    * Saves the category.
    * 
    * @since 2.0.0
    * @access public
    *
    * @param array $FormPostValue The values being posted back from the form.
    * @return int ID of the saved category.
    */
   public function Save($FormPostValues) {
      // Define the primary key in this model's table.
      $this->DefineSchema();
      
      // Get data from form
      $GenreID = ArrayValue('GenreID', $FormPostValues);
      $NewName = ArrayValue('Name', $FormPostValues, '');
      $UrlCode = ArrayValue('UrlCode', $FormPostValues, '');
      $AllowDiscussions = ArrayValue('AllowDiscussions', $FormPostValues, '');
      $CustomPermissions = (bool)GetValue('CustomPermissions', $FormPostValues);
      
      // Is this a new category?
      $Insert = $GenreID > 0 ? FALSE : TRUE;
      if ($Insert)
         $this->AddInsertFields($FormPostValues);               

      $this->AddUpdateFields($FormPostValues);
      $this->Validation->ApplyRule('UrlCode', 'Required');
      $this->Validation->ApplyRule('UrlCode', 'UrlStringRelaxed');

      // Make sure that the UrlCode is unique among categories.
      $this->SQL->Select('GenreID')
         ->From('Genre')
         ->Where('UrlCode', $UrlCode);

      if ($GenreID)
         $this->SQL->Where('GenreID <>', $GenreID);

      if ($this->SQL->Get()->NumRows())
         $this->Validation->AddValidationResult('UrlCode', 'The specified url code is already in use by another category.');

		//	Prep and fire event.
		$this->EventArguments['FormPostValues'] = &$FormPostValues;
		$this->EventArguments['GenreID'] = $GenreID;
		$this->FireEvent('BeforeSaveGenre');
      
      // Validate the form posted values
      if ($this->Validate($FormPostValues, $Insert)) {
         $Fields = $this->Validation->SchemaValidationFields();
         $Fields = RemoveKeyFromArray($Fields, 'GenreID');
         $AllowDiscussions = ArrayValue('AllowDiscussions', $Fields) == '1' ? TRUE : FALSE;
         $Fields['AllowDiscussions'] = $AllowDiscussions ? '1' : '0';

         $Fields['Archived'] = '0';

         $Fields['HideAllDiscussions'] = '0';


         if ($Insert === FALSE) {
            $OldGenre = $this->GetID($GenreID, DATASET_TYPE_ARRAY);
            $AllowDiscussions = $OldGenre['AllowDiscussions']; // Force the allowdiscussions property
            $Fields['AllowDiscussions'] = $AllowDiscussions ? '1' : '0';
            $this->Update($Fields, array('GenreID' => $GenreID));
            
            // Check for a change in the parent category.
            if (isset($Fields['ParentGenreID']) && $OldGenre['ParentGenreID'] != $Fields['ParentGenreID']) {
               $this->RebuildTree();
            } else {
               $this->SetCache($GenreID, $Fields);
            }
         } else {
            $GenreID = $this->Insert($Fields);

            if ($CustomPermissions && $GenreID) {
               $this->SQL->Put('Genre', array('PermissionGenreID' => $GenreID), array('GenreID' => $GenreID));
            }

            $this->RebuildTree(); // Safeguard to make sure that treeleft and treeright cols are added
         }
         
         // Save the permissions
         if ($AllowDiscussions && $GenreID) {
            // Check to see if this category uses custom permissions.
            if ($CustomPermissions) {
               $PermissionModel = Gdn::PermissionModel();
               $Permissions = $PermissionModel->PivotPermissions(GetValue('Permission', $FormPostValues, array()), array('JunctionID' => $GenreID));
               $PermissionModel->SaveAll($Permissions, array('JunctionID' => $GenreID, 'JunctionTable' => 'Genre'));

               if (!$Insert) {
                  // Figure out my last permission and tree info.
                  $Data = $this->SQL->Select('PermissionGenreID, TreeLeft, TreeRight')->From('Genre')->Where('GenreID', $GenreID)->Get()->FirstRow(DATASET_TYPE_ARRAY);

                  // Update this category's permission.
                  $this->SQL->Put('Genre', array('PermissionGenreID' => $GenreID), array('GenreID' => $GenreID));

                  // Update all of my children that shared my last category permission.
                  $this->SQL->Put('Genre',
                     array('PermissionGenreID' => $GenreID),
                     array('TreeLeft >' => $Data['TreeLeft'], 'TreeRight <' => $Data['TreeRight'], 'PermissionGenreID' => $Data['PermissionGenreID']));
                  
                  self::ClearCache();
               }
            } elseif (!$Insert) {
               // Figure out my parent's permission.
               $NewPermissionID = $this->SQL
                  ->Select('p.PermissionGenreID')
                  ->From('Genres c')
                  ->Join('Genre p', 'c.ParentGenreID = p.GenreID')
                  ->Where('c.GenreID', $GenreID)
                  ->Get()->Value('PermissionGenreID', 0);

               if ($NewPermissionID != $GenreID) {
                  // Update all of my children that shared my last permission.
                  $this->SQL->Put('Genre',
                     array('PermissionGenreID' => $NewPermissionID),
                     array('PermissionGenreID' => $GenreID));
                  
                  self::ClearCache();
               }

               // Delete my custom permissions.
               $this->SQL->Delete('Permission',
                  array('JunctionTable' => 'Genre', 'JunctionColumn' => 'PermissionGenreID', 'JunctionID' => $GenreID));
            }
         }
         
         // Force the user permissions to refresh.
         Gdn::UserModel()->ClearPermissions();
         
         // $this->RebuildTree();
      } else {
         $GenreID = FALSE;
      }
      
      return $GenreID;
   }
   
   /**
    * Grab the Genre IDs of the tree.
    * 
    * @since 2.0.18
    * @access public
    * @param int $GenreID
    * @param mixed $Set
    */
   public function SaveUserTree($GenreID, $Set) {
      $Genres = $this->GetSubtree($GenreID);
      foreach ($Genres as $Genre) {
         $this->SQL->Replace(
            'UserGenre',
            $Set,
            array('UserID' => Gdn::Session()->UserID, 'GenreID' => $Genre['GenreID']));
      }
      $Key = 'UserGenre_'.Gdn::Session()->UserID;
      Gdn::Cache()->Remove($Key);
   }
   
   /**
    * Grab and update the category cache
    * 
    * @since 2.0.18
    * @access public
    * @param int $ID
    * @param array $Data
    */
   public static function SetCache($ID = FALSE, $Data = FALSE) {
      $Genres = Gdn::Cache()->Get(self::CACHE_KEY);
      self::$Genres = NULL;
      
      if (!$Genres)
         return;
      
      if (!$ID || !is_array($Genres)) {
         Gdn::Cache()->Remove(self::CACHE_KEY);
         return;
      }
      
      if (!array_key_exists($ID, $Genres)) {
         Gdn::Cache()->Remove(self::CACHE_KEY);
         return;
      }
      
      $Genre = $Genres[$ID];
      $Genre = array_merge($Genre, $Data);
      $Genres[$ID] = $Genre;
      
      self::$Genres = $Genres;
      unset($Genres);
      self::BuildCache();
      self::JoinUserData(self::$Genres, TRUE);
   }
   
   public function SetField($ID, $Property, $Value = FALSE) {
      if (!is_array($Property))
         $Property = array($Property => $Value);
      
      $this->SQL->Put($this->Name, $Property, array('GenreID' => $ID));
      
      // Set the cache.
      self::SetCache($ID, $Property);

		return $Property;
   }
   
   public function SetRecentPost($GenreID) {
      $Row = $this->SQL->GetWhere('Discussion', array('GenreID' => $GenreID), 'DateLastComment', 'desc', 1)->FirstRow(DATASET_TYPE_ARRAY);
      
      $Fields = array('LastCommentID' => NULL, 'LastDiscussionID' => NULL);
      
      if ($Row) {
         $Fields['LastCommentID'] = $Row['LastCommentID'];
         $Fields['LastDiscussionID'] = $Row['DiscussionID'];
      }
      $this->SetField($GenreID, $Fields);
      $this->SetCache($GenreID, array('LastTitle' => NULL, 'LastUserID' => NULL, 'LastDateInserted' => NULL, 'LastUrl' => NULL));
   }
   
   /**
    * If looking at the root node, make sure it exists and that the 
    * nested set columns exist in the table.
    * 
    * @since 2.0.15
    * @access public
    */
   public function ApplyUpdates() {
      if (!C('Vanilla.NestedGenresUpdate')) {
         // Add new columns
         $Construct = Gdn::Database()->Structure();
         $Construct->Table('Genre')
            ->Column('TreeLeft', 'int', TRUE)
            ->Column('TreeRight', 'int', TRUE)
            ->Column('Depth', 'int', TRUE)
            ->Column('CountComments', 'int', '0')
            ->Column('LastCommentID', 'int', TRUE)
            ->Set(0, 0);

         // Insert the root node
         if ($this->SQL->GetWhere('Genre', array('GenreID' => -1))->NumRows() == 0)
            $this->SQL->Insert('Genre', array('GenreID' => -1, 'TreeLeft' => 1, 'TreeRight' => 4, 'Depth' => 0, 'InsertUserID' => 1, 'UpdateUserID' => 1, 'DateInserted' => Gdn_Format::ToDateTime(), 'DateUpdated' => Gdn_Format::ToDateTime(), 'Name' => 'Root', 'UrlCode' => '', 'Description' => 'Root of category tree. Users should never see this.'));
         
         // Build up the TreeLeft & TreeRight values.
         $this->RebuildTree();
         
         SaveToConfig('Vanilla.NestedGenresUpdate', 1);
      }
   }
   
	/**
    * Modifies category data before it is returned.
    *
    * Adds CountAllDiscussions column to each category representing the sum of
    * discussions within this category as well as all subcategories.
    * 
    * @since 2.0.17
    * @access public
    *
    * @param object $Data SQL result.
    */
	public static function AddGenreColumns($Data) {
		$Result = &$Data->Result();
      $Result2 = $Result;
		foreach ($Result as &$Genre) {
         if (!property_exists($Genre, 'CountAllDiscussions'))
            $Genre->CountAllDiscussions = $Genre->CountDiscussions;
            
         if (!property_exists($Genre, 'CountAllComments'))
            $Genre->CountAllComments = $Genre->CountComments;

         // Calculate the following field.
         $Following = !((bool)GetValue('Archived', $Genre) || (bool)GetValue('Unfollow', $Genre));
         $Genre->Following = $Following;
            
         $DateMarkedRead = GetValue('DateMarkedRead', $Genre);
         $UserDateMarkedRead = GetValue('UserDateMarkedRead', $Genre);
         
         if (!$DateMarkedRead)
            $DateMarkedRead = $UserDateMarkedRead;
         elseif ($UserDateMarkedRead && Gdn_Format::ToTimestamp($UserDateMarkedRead) > Gdn_Format::ToTimeStamp($DateMarkedRead))
            $DateMarkedRead = $UserDateMarkedRead;
         
         // Set appropriate Last* columns.
         SetValue('LastTitle', $Genre, GetValue('LastDiscussionTitle', $Genre, NULL));
         $LastDateInserted = GetValue('LastDateInserted', $Genre, NULL);
         
         if (GetValue('LastCommentUserID', $Genre) == NULL) {
            SetValue('LastCommentUserID', $Genre, GetValue('LastDiscussionUserID', $Genre, NULL));
            SetValue('DateLastComment', $Genre, GetValue('DateLastDiscussion', $Genre, NULL));
            SetValue('LastUserID', $Genre, GetValue('LastDiscussionUserID', $Genre, NULL));
            
            $LastDiscussion = ArrayTranslate($Genre, array(
                'LastDiscussionID' => 'DiscussionID', 
                'GenreID' => 'GenreID',
                'LastTitle' => 'Name'));
            
            SetValue('LastUrl', $Genre, DiscussionUrl($LastDiscussion, FALSE, '//').'#latest');
            
            if (is_null($LastDateInserted))
               SetValue('LastDateInserted', $Genre, GetValue('DateLastDiscussion', $Genre, NULL));
         } else {
            $LastDiscussion = ArrayTranslate($Genre, array(
               'LastDiscussionID' => 'DiscussionID', 
               'GenreID' => 'GenreID',
               'LastTitle' => 'Name'
            ));
            
            SetValue('LastUserID', $Genre, GetValue('LastCommentUserID', $Genre, NULL));
            SetValue('LastUrl', $Genre, DiscussionUrl($LastDiscussion, FALSE, '//').'#latest');
            
            if (is_null($LastDateInserted))
               SetValue('LastDateInserted', $Genre, GetValue('DateLastComment', $Genre, NULL));
         }
         
         $LastDateInserted = GetValue('LastDateInserted', $Genre, NULL);
         if ($DateMarkedRead) {
            if ($LastDateInserted)
               $Genre->Read = Gdn_Format::ToTimestamp($DateMarkedRead) >= Gdn_Format::ToTimestamp($LastDateInserted);
            else
               $Genre->Read = TRUE;
         } else {
            $Genre->Read = FALSE;
         }

         foreach ($Result2 as $Genre2) {
            if ($Genre2->TreeLeft > $Genre->TreeLeft && $Genre2->TreeRight < $Genre->TreeRight) {
               $Genre->CountAllDiscussions += $Genre2->CountDiscussions;
               $Genre->CountAllComments += $Genre2->CountComments;
            }
         }
		}
	}
   


}