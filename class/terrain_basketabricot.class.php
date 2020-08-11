<?php
/* Copyright (C) 2020 ATM Consulting <support@atm-consulting.fr>
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

if (!class_exists('SeedObject'))
{
	/**
	 * Needed if $form->showLinkedObjectBlock() is call or for session timeout on our module page
	 */
	define('INC_FROM_DOLIBARR', true);
	require_once dirname(__FILE__).'/../config.php';
}


class Terrain_BasketAbricot extends SeedObject
{

	/** @var string $table_element Table name in SQL */
	public $table_element = 'c_terrain_abricot';

	/** @var string $element Name of the element (tip for better integration in Dolibarr: this value should be the reflection of the class name with ucfirst() function) */
	public $element = 'terrain_basketabricot';

	public $fields = array(

		'code' => array(
			'type' => 'varchar(5)',
			'label' => 'Code',
			'enabled' => 1,
			'visible' => 1,
			'notnull' => 1,
			'index' => 1,
			'position' => 10,
		),

		'nom_terrain' => array(
			'type' => 'varchar(255)',
			'label' => 'BasketballCourtName',
			'enabled' => 1,
			'visible' => 1,
			'notnull' => 1,
			'index' => 1,
			'position' => 20,
			'showoncombobox' => 1,
		),

		'ville' => array(
			'type' => 'varchar(5)',
			'label' => 'City',
			'enabled' => 1,
			'visible' => 1,
			'notnull' => 1,
			'index' => 1,
			'position' => 30,),
	);

	public $code;
	public $nom_terrain;
	public $ville;
	
	public function getNomUrl($withpicto = 0, $option = '', $maxlength = 0)
	{
		$result = $this->nom_terrain;
		return $result;
	}
}
