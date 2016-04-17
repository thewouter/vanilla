<?php if (!defined('APPLICATION')) exit();

class DefaultThemeHooks implements Gdn_IPlugin {

	public function Setup() {
		return TRUE;
		}

	public function OnDisable() {
		return TRUE;
		}

	/**
	  *
	  * Adds Author Icon before discussion title.
	  *
	  **/

	public function Base_BeforeDiscussionContent_Handler($Sender) {
		$Discussion = GetValue('Discussion', $Sender->EventArguments);
		$Author = UserBuilder($Discussion, 'First');
		echo '<span class="Author">';
		echo UserPhoto($Author);
		echo "</span>";
		}

}