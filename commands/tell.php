<?php

/**
 * Leave a message for an offline user, for Ligrev to send later
 *
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License 3
 * @author Sylae Jiendra Corell <sylae@calref.net>
 */

namespace Ligrev\Command;

class tell extends \Ligrev\command {

  function process() {
    global $config, $roster, $db;
    $textParts = $this->_split($this->text);
    $r = (array_key_exists(1, $textParts) ? $textParts[1] : null);
    $message = trim(str_replace($textParts[0] . " " . $textParts[1], "", $this->text));
    if ($r) {
      $recipient = $r;
    } else {
      $this->_send($this->room, "Error: No recipient.");
      return;
    }
    $private = ($this->origin == "groupchat" ? false : true);

    if (!preg_match("/@+/", $recipient)) {
      // If there's no domain, assume it's for the default
      $recipient = $r . "@" . $config['defaultTellDomain'];
    }

    // Let's make sure the user isn't already online.
    if ($roster->onlineByJID($recipient)) {
      $this->_send($this->room, "Error: $recipient already online. Contact user directly.");
      return false;
    }
    var_dump($message, $recipient, $private);
    $sql = $db->prepare('INSERT INTO tell (sender, recipient, sent, private, message) VALUES(?, ?, ?, ?, ?);', array('string', 'string', 'integer', 'boolean', 'string'));
    $sql->bindValue(1, $this->from->to_string(), "string");
    $sql->bindValue(2, $recipient, "string");
    $sql->bindValue(3, time(), "integer");
    $sql->bindValue(4, $private, "boolean");
    $sql->bindValue(5, $message, "string");
    $sql->execute();
    $this->_send($this->room, "Message for $recipient processed.");
  }

}
