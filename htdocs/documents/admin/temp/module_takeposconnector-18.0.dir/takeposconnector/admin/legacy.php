<?php
/* Copyright (C) 2004-2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2022 SuperAdmin
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
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    takeposconnector/admin/setup.php
 * \ingroup takeposconnector
 * \brief   TakeposConnector setup page.
 */

// Load Dolibarr environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
	$res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
}
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME']; $tmp2 = realpath(__FILE__); $i = strlen($tmp) - 1; $j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {
	$i--; $j--;
}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/main.inc.php")) {
	$res = @include substr($tmp, 0, ($i + 1))."/main.inc.php";
}
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php")) {
	$res = @include dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php";
}
// Try main.inc.php using relative path
if (!$res && file_exists("../../main.inc.php")) {
	$res = @include "../../main.inc.php";
}
if (!$res && file_exists("../../../main.inc.php")) {
	$res = @include "../../../main.inc.php";
}
if (!$res) {
	die("Include of main fails");
}

global $langs, $user;

// Libraries
require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";
require_once '../lib/takeposconnector.lib.php';
//require_once "../class/myclass.class.php";

// Translations
$langs->loadLangs(array("admin", "takeposconnector@takeposconnector"));

// Initialize technical object to manage hooks of page. Note that conf->hooks_modules contains array of hook context
$hookmanager->initHooks(array('takeposconnectorsetup', 'globalsetup'));

// Access control
if (!$user->admin) {
	accessforbidden();
}

// Parameters
$action = GETPOST('action', 'aZ09');
$backtopage = GETPOST('backtopage', 'alpha');
$modulepart = GETPOST('modulepart', 'aZ09');	// Used by actions_setmoduleoptions.inc.php

$value = GETPOST('value', 'alpha');
$label = GETPOST('label', 'alpha');
$scandir = GETPOST('scan_dir', 'alpha');
$type = 'myobject';

$arrayofparameters = array(
	//'TAKEPOSCONNECTOR_MYPARAM1'=>array('type'=>'string', 'css'=>'minwidth500' ,'enabled'=>1),
	//'TAKEPOSCONNECTOR_MYPARAM2'=>array('type'=>'textarea','enabled'=>1),
	//'TAKEPOSCONNECTOR_MYPARAM3'=>array('type'=>'category:'.Categorie::TYPE_CUSTOMER, 'enabled'=>1),
	//'TAKEPOSCONNECTOR_MYPARAM4'=>array('type'=>'emailtemplate:thirdparty', 'enabled'=>1),
	//'TAKEPOSCONNECTOR_MYPARAM5'=>array('type'=>'yesno', 'enabled'=>1),
	//'TAKEPOSCONNECTOR_MYPARAM5'=>array('type'=>'thirdparty_type', 'enabled'=>1),
	//'TAKEPOSCONNECTOR_MYPARAM6'=>array('type'=>'securekey', 'enabled'=>1),
	//'TAKEPOSCONNECTOR_MYPARAM7'=>array('type'=>'product', 'enabled'=>1),
);

$error = 0;
$setupnotempty = 0;

// Set this to 1 to use the factory to manage constants. Warning, the generated module will be compatible with version v15+ only
$useFormSetup = 0;
// Convert arrayofparameter into a formSetup object
if ($useFormSetup && (float) DOL_VERSION >= 15) {
	require_once DOL_DOCUMENT_ROOT.'/core/class/html.formsetup.class.php';
	$formSetup = new FormSetup($db);

	// you can use the param convertor
	$formSetup->addItemsFromParamsArray($arrayofparameters);

	// or use the new system see exemple as follow (or use both because you can ;-) )

	/*
	// Hôte
	$item = $formSetup->newItem('NO_PARAM_JUST_TEXT');
	$item->fieldOverride = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'];
	$item->cssClass = 'minwidth500';

	// Setup conf TAKEPOSCONNECTOR_MYPARAM1 as a simple string input
	$item = $formSetup->newItem('TAKEPOSCONNECTOR_MYPARAM1');

	// Setup conf TAKEPOSCONNECTOR_MYPARAM1 as a simple textarea input but we replace the text of field title
	$item = $formSetup->newItem('TAKEPOSCONNECTOR_MYPARAM2');
	$item->nameText = $item->getNameText().' more html text ';

	// Setup conf TAKEPOSCONNECTOR_MYPARAM3
	$item = $formSetup->newItem('TAKEPOSCONNECTOR_MYPARAM3');
	$item->setAsThirdpartyType();

	// Setup conf TAKEPOSCONNECTOR_MYPARAM4 : exemple of quick define write style
	$formSetup->newItem('TAKEPOSCONNECTOR_MYPARAM4')->setAsYesNo();

	// Setup conf TAKEPOSCONNECTOR_MYPARAM5
	$formSetup->newItem('TAKEPOSCONNECTOR_MYPARAM5')->setAsEmailTemplate('thirdparty');

	// Setup conf TAKEPOSCONNECTOR_MYPARAM6
	$formSetup->newItem('TAKEPOSCONNECTOR_MYPARAM6')->setAsSecureKey()->enabled = 0; // disabled

	// Setup conf TAKEPOSCONNECTOR_MYPARAM7
	$formSetup->newItem('TAKEPOSCONNECTOR_MYPARAM7')->setAsProduct();
	*/

	$setupnotempty = count($formSetup->items);
}


$dirmodels = array_merge(array('/'), (array) $conf->modules_parts['models']);


/*
 * Actions
 */

include DOL_DOCUMENT_ROOT.'/core/actions_setmoduleoptions.inc.php';

if ($action == 'updateMask') {
	$maskconst = GETPOST('maskconst', 'alpha');
	$maskvalue = GETPOST('maskvalue', 'alpha');

	if ($maskconst) {
		$res = dolibarr_set_const($db, $maskconst, $maskvalue, 'chaine', 0, '', $conf->entity);
		if (!($res > 0)) {
			$error++;
		}
	}

	if (!$error) {
		setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
	} else {
		setEventMessages($langs->trans("Error"), null, 'errors');
	}
} elseif ($action == 'specimen') {
	$modele = GETPOST('module', 'alpha');
	$tmpobjectkey = GETPOST('object');

	$tmpobject = new $tmpobjectkey($db);
	$tmpobject->initAsSpecimen();

	// Search template files
	$file = ''; $classname = ''; $filefound = 0;
	$dirmodels = array_merge(array('/'), (array) $conf->modules_parts['models']);
	foreach ($dirmodels as $reldir) {
		$file = dol_buildpath($reldir."core/modules/takeposconnector/doc/pdf_".$modele."_".strtolower($tmpobjectkey).".modules.php", 0);
		if (file_exists($file)) {
			$filefound = 1;
			$classname = "pdf_".$modele;
			break;
		}
	}

	if ($filefound) {
		require_once $file;

		$module = new $classname($db);

		if ($module->write_file($tmpobject, $langs) > 0) {
			header("Location: ".DOL_URL_ROOT."/document.php?modulepart=".strtolower($tmpobjectkey)."&file=SPECIMEN.pdf");
			return;
		} else {
			setEventMessages($module->error, null, 'errors');
			dol_syslog($module->error, LOG_ERR);
		}
	} else {
		setEventMessages($langs->trans("ErrorModuleNotFound"), null, 'errors');
		dol_syslog($langs->trans("ErrorModuleNotFound"), LOG_ERR);
	}
} elseif ($action == 'setmod') {
	// TODO Check if numbering module chosen can be activated by calling method canBeActivated
	$tmpobjectkey = GETPOST('object');
	if (!empty($tmpobjectkey)) {
		$constforval = 'TAKEPOSCONNECTOR_'.strtoupper($tmpobjectkey)."_ADDON";
		dolibarr_set_const($db, $constforval, $value, 'chaine', 0, '', $conf->entity);
	}
} elseif ($action == 'set') {
	// Activate a model
	$ret = addDocumentModel($value, $type, $label, $scandir);
} elseif ($action == 'del') {
	$ret = delDocumentModel($value, $type);
	if ($ret > 0) {
		$tmpobjectkey = GETPOST('object');
		if (!empty($tmpobjectkey)) {
			$constforval = 'TAKEPOSCONNECTOR_'.strtoupper($tmpobjectkey).'_ADDON_PDF';
			if ($conf->global->$constforval == "$value") {
				dolibarr_del_const($db, $constforval, $conf->entity);
			}
		}
	}
} elseif ($action == 'setdoc') {
	// Set or unset default model
	$tmpobjectkey = GETPOST('object');
	if (!empty($tmpobjectkey)) {
		$constforval = 'TAKEPOSCONNECTOR_'.strtoupper($tmpobjectkey).'_ADDON_PDF';
		if (dolibarr_set_const($db, $constforval, $value, 'chaine', 0, '', $conf->entity)) {
			// The constant that was read before the new set
			// We therefore requires a variable to have a coherent view
			$conf->global->$constforval = $value;
		}

		// We disable/enable the document template (into llx_document_model table)
		$ret = delDocumentModel($value, $type);
		if ($ret > 0) {
			$ret = addDocumentModel($value, $type, $label, $scandir);
		}
	}
} elseif ($action == 'unsetdoc') {
	$tmpobjectkey = GETPOST('object');
	if (!empty($tmpobjectkey)) {
		$constforval = 'TAKEPOSCONNECTOR_'.strtoupper($tmpobjectkey).'_ADDON_PDF';
		dolibarr_del_const($db, $constforval, $conf->entity);
	}
}



/*
 * View
 */

$form = new Form($db);

$help_url = '';
$page_name = "TakeposConnectorSetup";

llxHeader('', $langs->trans($page_name), $help_url);

// Subheader
$linkback = '<a href="'.($backtopage ? $backtopage : DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1').'">'.$langs->trans("BackToModuleList").'</a>';

print load_fiche_titre($langs->trans($page_name), $linkback, 'title_setup');

// Configuration header
$head = takeposconnectorAdminPrepareHead();
print dol_get_fiche_head($head, 'legacy', $langs->trans($page_name), -1, "takeposconnector@takeposconnector");

// Setup page goes here
echo '<span class="opacitymedium">'.$langs->trans("TakePOS Connector").'</span><br><br>';






// Load Dolibarr environment
require_once DOL_DOCUMENT_ROOT.'/product/class/html.formproduct.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/pdf.lib.php';
require_once DOL_DOCUMENT_ROOT."/core/lib/takepos.lib.php";



$langs->loadLangs(array("admin", "cashdesk", "commercial"));


/*
 * Actions
 */

if (GETPOST('action', 'alpha') == 'set') {
	$db->begin();

	$res = dolibarr_set_const($db, "TAKEPOS_HEADER", GETPOST('TAKEPOS_HEADER', 'restricthtml'), 'chaine', 0, '', $conf->entity);
	$res = dolibarr_set_const($db, "TAKEPOS_FOOTER", GETPOST('TAKEPOS_FOOTER', 'restricthtml'), 'chaine', 0, '', $conf->entity);
	$res = dolibarr_set_const($db, "TAKEPOS_RECEIPT_NAME", GETPOST('TAKEPOS_RECEIPT_NAME', 'alpha'), 'chaine', 0, '', $conf->entity);
	$res = dolibarr_set_const($db, "TAKEPOS_PRINT_SERVER", GETPOST('TAKEPOS_PRINT_SERVER', 'alpha'), 'chaine', 0, '', $conf->entity);
	$res = dolibarr_set_const($db, 'TAKEPOS_PRINT_WITHOUT_DETAILS_LABEL_DEFAULT', GETPOST('TAKEPOS_PRINT_WITHOUT_DETAILS_LABEL_DEFAULT', 'alphanohtml'), 'chaine', 0, '', $conf->entity);

	dol_syslog("admin/cashdesk: level ".GETPOST('level', 'alpha'));

	if (!($res > 0)) {
		$error++;
	}

	if (!$error) {
		$db->commit();
		setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
	} else {
		$db->rollback();
		setEventMessages($langs->trans("Error"), null, 'errors');
	}
} elseif (GETPOST('action', 'alpha') == 'setmethod') {
	dolibarr_set_const($db, "TAKEPOS_PRINT_METHOD", GETPOST('value', 'alpha'), 'chaine', 0, '', $conf->entity);
	// TakePOS connector require ReceiptPrinter module
	if (getDolGlobalString('TAKEPOS_PRINT_METHOD') == "takeposconnector" && !isModEnabled('receiptprinter')) {
		activateModule("modReceiptPrinter");
	}
}


/*
 * View
 */

$form = new Form($db);
$formproduct = new FormProduct($db);


print '<form action="'.$_SERVER["PHP_SELF"].'?terminal='.(empty($terminal) ? 1 : $terminal).'" method="post">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="set">';

print load_fiche_titre($langs->trans("PrintMethod"), '', '');

print '<div class="div-table-responsive-no-min">';
print '<table class="noborder centpercent">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Name").'</td><td>'.$langs->trans("Description").'</td><td class="right">'.$langs->trans("Status").'</td>';
print "</tr>\n";

// Browser method
print '<tr class="oddeven"><td>';
print $langs->trans('Browser');
print '<td>';
print $langs->trans('BrowserMethodDescription');
print '</td><td class="right">';
if (getDolGlobalString('TAKEPOS_PRINT_METHOD') == "browser") {
	print img_picto($langs->trans("Activated"), 'switch_on');
} else {
	print '<a class="reposition" href="'.$_SERVER["PHP_SELF"].'?action=setmethod&token='.newToken().'&value=browser">'.img_picto($langs->trans("Disabled"), 'switch_off').'</a>';
}
print "</td></tr>\n";

// Receipt printer module
print '<tr class="oddeven"><td>';
print $langs->trans('DolibarrReceiptPrinter');
print '<td>';
print $langs->trans('ReceiptPrinterMethodDescription');
if (isModEnabled('receiptprinter')) {
	if (getDolGlobalString('TAKEPOS_PRINT_METHOD') == "receiptprinter") {
		print '<br>';
		print img_picto('', 'printer', 'class="paddingright"').'<a href="'.DOL_URL_ROOT.'/admin/receiptprinter.php">'.$langs->trans("Setup").'</a>';
	}
}
print '</td><td class="right">';
if (isModEnabled('receiptprinter')) {
	if (getDolGlobalString('TAKEPOS_PRINT_METHOD') == "receiptprinter") {
		print img_picto($langs->trans("Activated"), 'switch_on');
	} else {
		print '<a class="reposition" href="'.$_SERVER["PHP_SELF"].'?action=setmethod&token='.newToken().'&value=receiptprinter">'.img_picto($langs->trans("Disabled"), 'switch_off').'</a>';
	}
} else {
	print '<span class="opacitymedium">';
	print $langs->trans("ModuleReceiptPrinterMustBeEnabled");
	print '</span>';
}
print "</td></tr>\n";

// TakePOS Connector
print '<tr class="oddeven"><td>';
print "TakePOS Connector";
print '<td>';
print $langs->trans('TakeposConnectorMethodDescription');

if (getDolGlobalString('TAKEPOS_PRINT_METHOD') == "takeposconnector") {
	print '<br>';
	print $langs->trans("URL")." / ".$langs->trans("IPAddress").' (<a href="http://en.takepos.com/connector" target="_blank" rel="noopener noreferrer external">'.$langs->trans("TakeposConnectorNecesary").'</a>)';
	print ' <input type="text" class="minwidth200" id="TAKEPOS_PRINT_SERVER" name="TAKEPOS_PRINT_SERVER" value="'.getDolGlobalString('TAKEPOS_PRINT_SERVER').'">';
}

print '</td><td class="right">';
if (getDolGlobalString('TAKEPOS_PRINT_METHOD') == "takeposconnector") {
	print img_picto($langs->trans("Activated"), 'switch_on');
} else {
	print '<a class="reposition" href="'.$_SERVER["PHP_SELF"].'?action=setmethod&token='.newToken().'&value=takeposconnector">'.img_picto($langs->trans("Disabled"), 'switch_off').'</a>';
}
print "</td></tr>\n";
print '</table>';
print '</div>';


print load_fiche_titre($langs->trans("Receipt"), '', '');

print '<div class="div-table-responsive-no-min">';
print '<table class="noborder centpercent">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Parameters").'</td><td>'.$langs->trans("Value").'</td>';
print "</tr>\n";

// VAT Grouped on ticket
print '<tr class="oddeven"><td>';
print $langs->trans('TicketVatGrouped');
print '<td colspan="2">';
print ajax_constantonoff("TAKEPOS_TICKET_VAT_GROUPPED", array(), $conf->entity, 0, 0, 1, 0);
print "</td></tr>\n";

if (getDolGlobalString('TAKEPOS_PRINT_METHOD') == "browser" || getDolGlobalString('TAKEPOS_PRINT_METHOD') == "takeposconnector") {
	$substitutionarray = pdf_getSubstitutionArray($langs, null, null, 2);
	$substitutionarray['__(AnyTranslationKey)__'] = $langs->trans("Translation");
	$htmltext = '<i>'.$langs->trans("AvailableVariables").':<br>';
	foreach ($substitutionarray as $key => $val) {
		$htmltext .= $key.'<br>';
	}
	$htmltext .= '</i>';

	print '<tr class="oddeven"><td>';
	print $form->textwithpicto($langs->trans("FreeLegalTextOnInvoices")." - ".$langs->trans("Header"), $htmltext, 1, 'help', '', 0, 2, 'freetexttooltip').'<br>';
	print '</td><td>';
	$variablename = 'TAKEPOS_HEADER';
	if (!getDolGlobalString('PDF_ALLOW_HTML_FOR_FREE_TEXT')) {
		print '<textarea name="'.$variablename.'" class="flat" cols="120">'.getDolGlobalString($variablename).'</textarea>';
	} else {
		include_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
		$doleditor = new DolEditor($variablename, getDolGlobalString($variablename), '', 80, 'dolibarr_notes');
		print $doleditor->Create();
	}
	print "</td></tr>\n";

	print '<tr class="oddeven"><td>';
	print $form->textwithpicto($langs->trans("FreeLegalTextOnInvoices")." - ".$langs->trans("Footer"), $htmltext, 1, 'help', '', 0, 2, 'freetexttooltip').'<br>';
	print '</td><td>';
	$variablename = 'TAKEPOS_FOOTER';
	if (!getDolGlobalString('PDF_ALLOW_HTML_FOR_FREE_TEXT')) {
		print '<textarea name="'.$variablename.'" class="flat" cols="120">'.getDolGlobalString($variablename).'</textarea>';
	} else {
		include_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
		$doleditor = new DolEditor($variablename, getDolGlobalString($variablename), '', 80, 'dolibarr_notes');
		print $doleditor->Create();
	}
	print "</td></tr>\n";

	print '<tr class="oddeven"><td><label for="receipt_name">'.$langs->trans("ReceiptName").'</label></td><td>';
	print '<input name="TAKEPOS_RECEIPT_NAME" id="TAKEPOS_RECEIPT_NAME" class="minwidth200" value="'.getDolGlobalString('TAKEPOS_RECEIPT_NAME').'">';
	print '</td></tr>';

	// Customer information
	print '<tr class="oddeven"><td>';
	print $langs->trans('PrintCustomerOnReceipts');
	print '<td colspan="2">';
	print ajax_constantonoff("TAKEPOS_SHOW_CUSTOMER", array(), $conf->entity, 0, 0, 1, 0);
	print "</td></tr>\n";

	// Print payment method
	print '<tr class="oddeven"><td>';
	print $langs->trans('PrintPaymentMethodOnReceipts');
	print '<td colspan="2">';
	print ajax_constantonoff("TAKEPOS_PRINT_PAYMENT_METHOD", array(), $conf->entity, 0, 0, 1, 0);
	print "</td></tr>\n";
}

// Auto print tickets
print '<tr class="oddeven"><td>';
print $langs->trans("AutoPrintTickets");
print '<td colspan="2">';
print ajax_constantonoff("TAKEPOS_AUTO_PRINT_TICKETS", array(), $conf->entity, 0, 0, 1, 0);
print "</td></tr>\n";


// Show price without vat
print '<tr class="oddeven"><td>';
print $langs->trans('ShowPriceHTOnReceipt');
print '<td colspan="2">';
print ajax_constantonoff("TAKEPOS_SHOW_HT_RECEIPT", array(), $conf->entity, 0, 0, 1, 0);
print "</td></tr>\n";

if (getDolGlobalString('TAKEPOS_PRINT_METHOD') == "takeposconnector" && filter_var(getDolGlobalString('TAKEPOS_PRINT_SERVER'), FILTER_VALIDATE_URL) == true) {
	print '<tr class="oddeven"><td>';
	print $langs->trans('WeighingScale');
	print '<td colspan="2">';
	print ajax_constantonoff("TAKEPOS_WEIGHING_SCALE", array(), $conf->entity, 0, 0, 1, 0);
	print "</td></tr>\n";
}

if (getDolGlobalString('TAKEPOS_PRINT_METHOD') == "takeposconnector" && filter_var(getDolGlobalString('TAKEPOS_PRINT_SERVER'), FILTER_VALIDATE_URL) == true) {
	print '<tr class="oddeven"><td>';
	print $langs->trans('CustomerDisplay');
	print '<td colspan="2">';
	print ajax_constantonoff("TAKEPOS_CUSTOMER_DISPLAY", array(), $conf->entity, 0, 0, 1, 0);
	print "</td></tr>\n";
}

// Print without details
print '<tr class="oddeven"><td>';
print $langs->trans('PrintWithoutDetailsButton');
print '<td colspan="2">';
print ajax_constantonoff('TAKEPOS_PRINT_WITHOUT_DETAILS', array(), $conf->entity, 0, 0, 1, 0);
print "</td></tr>\n";
if (getDolGlobalString('TAKEPOS_PRINT_WITHOUT_DETAILS')) {
	print '<tr class="oddeven"><td>';
	print $langs->trans('PrintWithoutDetailsLabelDefault');
	print '<td colspan="2">';
	print '<input type="text" name="TAKEPOS_PRINT_WITHOUT_DETAILS_LABEL_DEFAULT" value="' . getDolGlobalString('TAKEPOS_PRINT_WITHOUT_DETAILS_LABEL_DEFAULT') . '" />';
	print "</td></tr>\n";
}

print '</table>';
print '</div>';

print '<br>';

print $form->buttonsSaveCancel("Save", '');

print "</form>\n";

print '<br>';

llxFooter();
$db->close();




// Page end
print dol_get_fiche_end();

llxFooter();
