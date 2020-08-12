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

require 'config.php';
dol_include_once('basketabricot/class/basketabricot.class.php');
global $db, $langs;

if(empty($user->rights->basketabricot->basketabricot->read)) accessforbidden();

$langs->load('abricot@abricot');
$langs->load('basketabricot@basketabricot');


$massaction = GETPOST('massaction', 'alpha');
$confirmmassaction = GETPOST('confirmmassaction', 'alpha');
$toselect = GETPOST('toselect', 'array');
$search_fk_nom1 = GETPOST('search_fk_soc1', 'int');
$search_fk_nom2 = GETPOST('search_fk_nom2', 'int');
$search_nom_terrain = GETPOST('search_terrain', 'int');
$search_libelle = GETPOST('search_categ', 'int');

$object = new basketAbricot($db);

$hookmanager->initHooks(array('basketabricotlist'));

if ($object->isextrafieldmanaged)
{
    $extrafields = new ExtraFields($db);
    $extralabels = $extrafields->fetch_name_optionals_label($object->table_element);
}

/*
 * Actions
 */

$parameters=array();
$reshook=$hookmanager->executeHooks('doActions', $parameters, $object);    // Note that $action and $object may have been modified by some hooks
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');

if (!GETPOST('confirmmassaction', 'alpha') && $massaction != 'presend' && $massaction != 'confirm_presend')
{
    $massaction = '';
}


if (empty($reshook))
{
	// do action from GETPOST ...
}


/*
 * View
 */

llxHeader('', $langs->trans('basketAbricotList'), '', '');

//$type = GETPOST('type');
//if (empty($user->rights->basketabricot->all->read)) $type = 'mine';

// TODO ajouter les champs de son objet que l'on souhaite afficher
$keys = array_keys($object->fields);
$fieldList = 't.'.implode(', t.', $keys);
if (!empty($object->isextrafieldmanaged))
{
    $keys = array_keys($extralabels);
	if(!empty($keys)) {
		$fieldList .= ', et.' . implode(', et.', $keys);
	}
}

$sql = 'SELECT '.$fieldList.', s.nom as fk_nom1, s2.nom as fk_nom2, t2.nom_terrain, c.libelle';

// Add fields from hooks
$parameters=array('sql' => $sql);
$reshook=$hookmanager->executeHooks('printFieldListSelect', $parameters, $object);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;

$sql.= ' FROM '.MAIN_DB_PREFIX.'basketabricot as t';

$sql .= ' JOIN llx_societe as s ON t.fk_soc1 = s.rowid';
$sql .= ' JOIN llx_societe as s2 ON t.fk_soc2 = s2.rowid';
$sql .= ' JOIN llx_c_terrain_abricot as t2 ON t.terrain = t2.rowid';
$sql .= ' LEFT JOIN llx_c_categories_abricot as c ON t.categ = c.rowid';

$sql.= ' WHERE 1=1';
//$sql.= ' AND t.entity IN ('.getEntity('basketAbricot', 1).')';
//if ($type == 'mine') $sql.= ' AND t.fk_user = '.$user->id;
// Add where from hooks
$parameters=array('sql' => $sql);
$reshook=$hookmanager->executeHooks('printFieldListWhere', $parameters, $object);    // Note that $action and $object may have been modified by hook
$sql.=$hookmanager->resPrint;

$formcore = new TFormCore($_SERVER['PHP_SELF'], 'form_list_basketabricot', 'GET');

$nbLine = GETPOST('limit');
if (empty($nbLine)) $nbLine = !empty($user->conf->MAIN_SIZE_LISTE_LIMIT) ? $user->conf->MAIN_SIZE_LISTE_LIMIT : $conf->global->MAIN_SIZE_LISTE_LIMIT;

// List configuration
$listViewConfig = array(
	'view_type' => 'list' // default = [list], [raw], [chart]
	,'allow-fields-select' => true
	,'limit'=>array(
		'nbLine' => $nbLine
	)
	,'list' => array(
		'title' => $langs->trans('basketAbricotList')
		,'image' => 'title_generic.png'
		,'picto_precedent' => '<'
		,'picto_suivant' => '>'
		,'noheader' => 0
		,'messageNothing' => $langs->trans('NobasketAbricot')
		,'picto_search' => img_picto('', 'search.png', '', 0)
		,'massactions'=>array(
			'yourmassactioncode'  => $langs->trans('Delete')
		)
		,'selected' => $toselect
	)
	,'subQuery' => array()
	,'link' => array()
	,'type' => array(
		'date_creation' => 'date' // [datetime], [hour], [money], [number], [integer]
		,'tms' => 'date'
	)
	,'search' => array(
		'ref' => array('search_type' => true, 'table' => 't', 'field' => 'ref'),
		'nom' => array('search_type' => true, 'table' => 't', 'field' => 'nom'),
		'fk_nom1' => array('search_type' => 'override',  'override' => $object->showInputField('','fk_soc1',$search_fk_nom1, '','', 'search_'), 'fieldname' => 'search_fk_soc1', 'table'=>'t', 'field'=>'fk_soc1'),
		'fk_nom2' => array('search_type' => 'override',  'override' => $object->showInputField('','fk_soc2',$search_fk_nom2, '','', 'search_'), 'fieldname' => 'search_fk_soc2', 'table'=>'t', 'field'=>'fk_soc2'),
		'date' => array('search_type' => 'calendar', 'table' => 't', 'field' => 'date'),
		'nom_terrain' => array('search_type' => 'override',  'override' => $object->showInputField('','terrain',$search_nom_terrain, '','', 'search_'), 'fieldname' => 'search_terrain', 'table'=>'t', 'field'=>'terrain'),
		'libelle' => array('search_type' => 'override',  'override' => $object->showInputField('','categ',$search_libelle, '','', 'search_'), 'fieldname' => 'search_categ', 'table'=>'t', 'field'=>'categ'),
	)
	,'translate' => array()
	,'hide' => array(
		'rowid' // important : rowid doit exister dans la query sql pour les checkbox de massaction
	)
	,'title'=>array(
		'ref' => $langs->trans('Ref.')
		,'nom' => $langs->trans('Name')
		,'fk_nom1' => $langs->trans('HomeTeam')
		,'fk_nom2' => $langs->trans('OutTeam')
		,'date' => $langs->trans('Date')
		,'nom_terrain' => $langs->trans('BasketballCourt')
		,'libelle' => $langs->trans('Championship')
	)
	,'eval'=>array(
		'ref' => '_getObjectNomUrl(\'@rowid@\', \'@val@\')'
//		,'fk_user' => '_getUserNomUrl(@val@)' // Si on a un fk_user dans notre requête
	)
);

$r = new Listview($db, 'basketabricot');

//MassAction
if ($confirmmassaction == 'Confirmer')
{
	foreach ($toselect as $cbcheck){
		$sqldelete = 'DELETE FROM '.MAIN_DB_PREFIX.'basketabricot WHERE rowid ='.$cbcheck;
		$resdelete = $db->query($sqldelete);
	}
}


// Change view from hooks
$parameters=array(  'listViewConfig' => $listViewConfig);
$reshook=$hookmanager->executeHooks('listViewConfig',$parameters,$r);    // Note that $action and $object may have been modified by hook
if ($reshook < 0) setEventMessages($hookmanager->error, $hookmanager->errors, 'errors');
if ($reshook>0)
{
	$listViewConfig = $hookmanager->resArray;
}

echo $r->render($sql, $listViewConfig);

$parameters=array('sql'=>$sql);
$reshook=$hookmanager->executeHooks('printFieldListFooter', $parameters, $object);    // Note that $action and $object may have been modified by hook
print $hookmanager->resPrint;

$formcore->end_form();

llxFooter('');
$db->close();

/**
 * TODO remove if unused
 */
function _getObjectNomUrl($id, $ref)
{
	global $db;

	$o = new basketAbricot($db);
	$res = $o->fetch($id, false, $ref);
	if ($res > 0)
	{
		return $o->getNomUrl(1);
	}

	return '';
}

/**
 * TODO remove if unused
 */
function _getUserNomUrl($fk_user)
{
	global $db;

	$u = new User($db);
	if ($u->fetch($fk_user) > 0)
	{
		return $u->getNomUrl(1);
	}

	return '';
}
