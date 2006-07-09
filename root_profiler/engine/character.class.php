<?php
  // character.class.php

  // 3EProfiler (tm) source file.
  // Copyright (C) 2003 Michael J. Eggertson.

  // This program is free software; you can redistribute it and/or modify
  // it under the terms of the GNU General Public License as published by
  // the Free Software Foundation; either version 2 of the License, or
  // (at your option) any later version.

  // This program is distributed in the hope that it will be useful,
  // but WITHOUT ANY WARRANTY; without even the implied warranty of
  // MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  // GNU General Public License for more details.

  // You should have received a copy of the GNU General Public License
  // along with this program; if not, write to the Free Software
  // Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

  // **

  // Encompasses access to a character. This class isn't designed to hide
  // access to the character data, but rather to provide succinct and easy
  // access to it. Data that isn't part of the characters table though,
  // should be accessed using the accessor functions.

  if (defined('_CHARACTER_CLASS_INCLUDED_'))
    return;
  define ('_CHARACTER_CLASS_INCLUDED_', true, true);

  require('db.php');
  require('charpermission.class.php');
  require(dirname(__FILE__) . '/../system.php');

  class Character
  {
    //////////////////////////////////////////////////////////////////////
    // CTOR
    //////////////////////////////////////////////////////////////////////

    function Character($id = 0)
    {
      global $TABLE_CHARS;

      $this->id = (int) $id;
      $this->_valid = false;

      // Retrieve the character information if requested.
      if ($this->id)
      {
        $res = mysql_query(sprintf("SELECT cname, lastedited, public, editedby, template_id, data, owner, campaign ".
                                   "FROM $TABLE_CHARS WHERE id = %d",
          (int) $this->id));
        if (!$res)
          return;
        if (mysql_num_rows($res) != 1)
          return;
        $row = mysql_fetch_row($res);

        $this->cname = $row[0];
        $this->lastedited = $row[1];
        $this->public = $row[2];
        $this->editedby = $row[3];
        $this->template_id = $row[4];
        $this->_data = unserialize($row[5]);
        $this->owner = $row[6];
        $this->campaign_id = $row[7];

        while (list($key, $val) = @each($this->_data))
          $this->_data[$key] = stripslashes($val);
        @reset($this->_data);

        $this->_permissions = new CharPermission(null, $this->id);
        $this->_valid = true;
      }
    }

    //////////////////////////////////////////////////////////////////////
    // Public members.
    //////////////////////////////////////////////////////////////////////

    //////////////////////////////////////////////////////////////////////
    // Data.

    var $id;
    var $cname;
    var $lastedited;
    var $public;
    var $editedby;
    var $template_id;
    var $owner;
    var $campaign_id;

    //////////////////////////////////////////////////////////////////////
    // Accessors

    // Retrieve the data hash.
    function & GetData()
    {
      return $this->_data;
    }

    // Retrieve a data value from a key.
    function Get($key)
    {
      return $this->_data[$key];
    }

    // Validate and set the data hash (overwrites the existing hash).
    function SetData(&$unvalidated)
    {
      $this->_data = array();
      @reset($unvalidated);
      while (list($key, $val) = @each($unvalidated))
        if (strlen($val))
          $this->_data[$key] = htmlspecialchars($val);
    }

    // Set a data key value.
    function Set($key, $val)
    {
      $this->_data[$key] = htmlspecialchars($val);
    }

    //////////////////////////////////////////////////////////////////////
    // General methods.

    // Returns an array of all profiles that have access to the character.
    function GetProfiles()
    {
      return $this->_permissions->GetProfiles();
    }
   
    // Get a pending campaign for the user.
    function GetPendingCampaign() {
      global $TABLE_CAMPAIGN_REQUESTS;

      $res = mysql_query(sprintf("SELECT campaign_id, status ".
                                 "FROM $TABLE_CAMPAIGN_REQUESTS WHERE char_id = %d",
          (int) $this->id));
      if (!$res)
        return;
      if (mysql_num_rows($res) != 1)
        return;
      $row = mysql_fetch_row($res);
  
      return array('campaign_id' => $row[0], 'status' => $row[1], 'user_id' => (int) $this->id );
    }
     
    // Set the characters campaign ID.
    function SetCampaign($id) 
    {
      global $TABLE_CHARS;

      if( $id == null ) {
        $id = 'null';
        $this->campaign_id = null;
      } else {
        $this->campaign_id = (int) $id;
      }

      // Update the db.
      // - Note, owner is never updated, and campaign is updated in a separate process.
      $res = mysql_query(sprintf("UPDATE %s SET campaign = %s WHERE id = %d LIMIT 1",
        $TABLE_CHARS,
        $id,
        (int) $this->id));
      return $res ? true : false;
    }

    // Create a request/invitation to join a campaign.
    function JoinCampaign($campaign_id, $join_type) 
    {
      global $TABLE_CAMPAIGN_REQUESTS;

      $sql = sprintf("INSERT INTO %s (campaign_id, char_id, status) VALUES (%d, %d, '%s')",
        $TABLE_CAMPAIGN_REQUESTS,
        (int) $campaign_id,
        (int) $this->id,
        $join_type);
  
      // Update the db.
      $res = mysql_query($sql);
        
      return $res ? true : false;
    }
 
    function RemoveJoinRequest()
    {
      global $TABLE_CAMPAIGN_REQUESTS;

      $sql = sprintf("DELETE FROM %s WHERE char_id = %d",
        $TABLE_CAMPAIGN_REQUESTS,
        (int) $this->id);

      // Update the db.
      $res = mysql_query($sql);

      return $res ? true : false;
    }

    // Grant permission to specified profile. Return true on success.
    function GrantAccessTo($name)
    {
      // Modify the table.
      $cp = new CharPermission($name, $this->id);
      if ($cp->GrantPermission())
      {
        // Refresh this object's permissions object.
        $this->_permissions = new CharPermission(null, $this->id);
        return true;
      }
      else
        return false;
    }
 
    function RemoveAccessFrom($name)
    {
      $cp = new CharPermission($name, $this->id);
      if ($cp->RemovePermission())
      {
        // Refresh this object's permissions object.
        $this->_permissions = new CharPermission(null, $this->id);
        return true;
      }
      else
        return false;

    }

    function IsValid()
    {
      return $this->_valid;
    }

    // Save character data to the db. $sid must be a session id for the
    // user who is editing the character. Return true on success.
    function Save(&$sid)
    {
      global $TABLE_CHARS;

      // Update the db.
      // - Note, owner is never updated, and campaign is updated in a separate process.
      $res = mysql_query(sprintf("UPDATE %s SET editedby = '%s', public = '%s', template_id = %d, data = '%s' WHERE id = %d LIMIT 1",
        $TABLE_CHARS,
        addslashes($sid->GetUserName()),
        $this->public == 'y' ? 'y' : 'n',
        (int) $this->template_id,
        addslashes(serialize($this->_data)),
        (int) $this->id));
      return $res ? true : false;
    }

    //////////////////////////////////////////////////////////////////////
    // Internal members. Members defined below this line should not be
    // modified or called through instances of an object.
    //////////////////////////////////////////////////////////////////////

    //////////////////////////////////////////////////////////////////////
    // Data members.

    // Is the character valid? (Has data been retrieved successfully by
    // the ctor?)
    var $_valid;

    // Profiles that have permission to access this character.
    var $_permissions;
    
    // The main character data hash.
    var $_data = array();
  }
?>
