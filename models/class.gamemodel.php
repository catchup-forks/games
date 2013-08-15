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
 * Game Model
 */
class GameModel extends Gdn_Model
{
   /**
    * Class constructor. Defines the related database table name.
    * 
    * @param string $Name Database table name.
    */
   public function __construct()
   {
      parent::__construct('Game');
   }



   public function GameQuery()
   {
      $this->SQL
         ->Select('g.*')
         ->From('games g');
   }

   public static function Slug($Game, $IncludeVersion = TRUE) {
         return Gdn_Format::Url(GetValue('zipname', $Game));
   }


   /**
    * Get list of all pages.
    *
    * @return object $GameData; SQL results.
    */
   public function GetAll()
   {
      $GameData = $this->SQL
         ->Select('g.*')
         ->From('games g')
         ->OrderBy('gamename', 'asc')
         ->Get();
      
      return $GameData;
   }




   /*
    * @return Gdn_DataSet
    */
   public function GetWhere($Where = FALSE, $OrderFields = '', $OrderDirection = NULL, $Limit = FALSE, $Offset = FALSE)
   {
      $this->GameQuery();
      
      if ($Where !== FALSE)
         $this->SQL->Where($Where);

	if ($OrderDirection == 'NULL')
		$OrderDirection = "ASC";



	 if ($OrderFields != '')
         $this->SQL->OrderBy($OrderFields, $OrderDirection);

      //if ($Limit !== FALSE) {
       //  if ($Offset == FALSE || $Offset < 0)
      //      $Offset = 0;

         $this->SQL->Limit($Limit, $Offset);
      //}

      $Result = $this->SQL->Get();
      return $Result;
   }
   
   public function GetCount($Wheres = '') {
      if (!is_array($Wheres))
         $Wheres = array();

      return $this->SQL
         ->Select('g.gameid', 'count', 'CountGames')
         ->From('games g')
         ->Where($Wheres)
         ->Get()
         ->FirstRow()
         ->CountGames;
   }















   /**
    * Get data for a single page by ID.
    *
	 * @param int $GameID; Unique ID of page to get.
	 * @return object $Game; SQL result.
	 */
   public function GetID($GameID)
   {
      $Game = $this->SQL
         ->Select('g.*')
         ->From('games g')
         ->Where('g.gameid', $GameID)
         ->Get()
         ->FirstRow();
      
      if (!$Game)
         return NULL;
      
		return $Game;
   }
   
   /**
    * Get data for a single page by zipname.
    *
	 * @param int $zipname; Unique zipname of page to get.
	 * @return object $Game; SQL result.
	 */
   public function GetByUrlCode($UrlCode)
   {
      $Game = $this->SQL
         ->Select('g.*')
         ->From('games g')
         ->Where('g.zipname', $UrlCode)
         ->Get()
         ->FirstRow();
      
      //if (!$Game)
         //return NULL;
		
		return $Game;
   }
   
   /**
    * Get list of all pages with SiteMenuLink column set to 1.
    *
	 * @param int $zipname; Unique zipname of page to get.
	 * @return object $Game; SQL result.
	 */
   public function GetAllSiteMenuLink()
   {
      $GameData = $this->SQL
         ->Select('g.gamename', '', 'Name')
         ->Select('g.zipname', '', 'zipname')
         ->From('games g')
         ->Where('g.SiteMenuLink', '1')
         ->Get();
      
      return $GameData;
   }
   
   /**
    * Return a url for a page.
    *
    * @param object $Game; Game object.
    * @param object $WithDomain; Return with domain in URL.
    * @return string; The URL to the page.
    */
   public static function GameUrl($Game, $WithDomain = TRUE)
   {
      $Game = (array)$Game;
      
      // Define route variables.
      $RouteExpressionSuffix = '(/.*)?$';
      
      if(Gdn::Router()->MatchRoute($Game['zipname'] . $RouteExpressionSuffix))
	  {
         $Result = rawurlencode($Game['zipname']);
      }
	  else
	  {
         $Result = '/game/' . rawurlencode($Game['zipname']);
      }
      return Url($Result, $WithDomain);
   }
}
