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
class GameModel extends GamesModel
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
    * Get data for a single page by UrlCode.
    *
	 * @param int $UrlCode; Unique UrlCode of page to get.
	 * @return object $Game; SQL result.
	 */
   public function GetByUrlCode($UrlCode)
   {
      $Game = $this->SQL
         ->Select('g.*')
         ->From('games g')
         ->Where('g.UrlCode', $UrlCode)
         ->Get()
         ->FirstRow();
      
      if (!$Game)
         return NULL;
		
		return $Game;
   }
   
   /**
    * Get list of all pages with SiteMenuLink column set to 1.
    *
	 * @param int $UrlCode; Unique UrlCode of page to get.
	 * @return object $Game; SQL result.
	 */
   public function GetAllSiteMenuLink()
   {
      $GameData = $this->SQL
         ->Select('g.gamename', '', 'Name')
         ->Select('g.UrlCode', '', 'UrlCode')
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
      
      if(Gdn::Router()->MatchRoute($Game['UrlCode'] . $RouteExpressionSuffix))
	  {
         $Result = rawurlencode($Game['UrlCode']);
      }
	  else
	  {
         $Result = '/game/' . rawurlencode($Game['UrlCode']);
      }
      return Url($Result, $WithDomain);
   }
}
