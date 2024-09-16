<?php
/* Copyright (C) 2007-2008 Jeremie Ollivier    <jeremie.o@laposte.net>
 * Copyright (C) 2011-2023 Laurent Destailleur <eldy@users.sourceforge.net>
 * Copyright (C) 2012      Marcos García       <marcosgdf@gmail.com>
 * Copyright (C) 2018      Andreu Bisquerra    <jove@bisquerra.com>
 * Copyright (C) 2019      Josep Lluís Amador  <joseplluis@lliuretic.cat>
 * Copyright (C) 2021      Nicolas ZABOURI     <info@inovea-conseil.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 *	\file       htdocs/takepos/receiptFinal.php
 *	\ingroup    takepos
 *	\brief      Page to show a receipt.
 */

if (!isset($action)) {
    if (!defined('NOTOKENRENEWAL')) {
        define('NOTOKENRENEWAL', '1');
    }
    if (!defined('NOREQUIREMENU')) {
        define('NOREQUIREMENU', '1');
    }
    if (!defined('NOREQUIREHTML')) {
        define('NOREQUIREHTML', '1');
    }
    if (!defined('NOREQUIREAJAX')) {
        define('NOREQUIREAJAX', '1');
    }

    require '../main.inc.php';
}
include_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';

$langs->loadLangs(array("main", "bills", "cashdesk", "companies"));

$place = (GETPOST('place', 'aZ09') ? GETPOST('place', 'aZ09') : 0);
$facid = GETPOST('facid', 'int');
$action = GETPOST('action', 'aZ09');
$gift = GETPOST('gift', 'int');

if (!$user->hasRight('takepos', 'run')) {
    accessforbidden();
}

$object = new Facture($db);
$object->fetch($facid);

top_httphead('text/html', 1);

?>
<body>
<style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 5px;
}
.right {
    text-align: right;
}
.center {
    text-align: center;
}
.left {
    text-align: left;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 10px;
}
th, td {
    padding: 3px;
}
</style>
<center>
<font size="4">
<?php echo '<b>'.$mysoc->name.'</b>'; ?>
</font>
</center>
<br>
<p class="left">
<?php
$constFreeText = 'TAKEPOS_HEADER'.(empty($_SESSION['takeposterminal']) ? '0' : $_SESSION['takeposterminal']);
if (getDolGlobalString('TAKEPOS_HEADER') || getDolGlobalString($constFreeText)) {
    $newfreetext = '';
    $substitutionarray = getCommonSubstitutionArray($langs);
    if (getDolGlobalString('TAKEPOS_HEADER')) {
        $newfreetext .= make_substitutions(getDolGlobalString('TAKEPOS_HEADER'), $substitutionarray);
    }
    if (getDolGlobalString($constFreeText)) {
        $newfreetext .= make_substitutions(getDolGlobalString($constFreeText), $substitutionarray);
    }
    print nl2br($newfreetext);
}
?>
</p>
<p class="right">
<?php
print $langs->trans('Date')." ".dol_print_date($object->date, 'day').'<br>';
if (getDolGlobalString('TAKEPOS_RECEIPT_NAME')) {
    print getDolGlobalString('TAKEPOS_RECEIPT_NAME') . " ";
}
if ($object->statut == Facture::STATUS_DRAFT) {
    print str_replace(")", "", str_replace("-", " ".$langs->trans('Place')." ", str_replace("(PROV-POS", $langs->trans("Terminal")." ", $object->ref)));
} else {
    print $object->ref;
}
if (getDolGlobalString('TAKEPOS_SHOW_CUSTOMER')) {
    if ($object->socid != getDolGlobalInt('CASHDESK_ID_THIRDPARTY'.$_SESSION["takeposterminal"])) {
        $soc = new Societe($db);
        if ($object->socid > 0) {
            $soc->fetch($object->socid);
        } else {
            $soc->fetch(getDolGlobalInt('CASHDESK_ID_THIRDPARTY'.$_SESSION["takeposterminal"]));
        }
        print "<br>".$langs->trans("Customer").': '.$soc->name;
    }
}
if (getDolGlobalString('TAKEPOS_SHOW_DATE_OF_PRINING')) {
    print "<br>".$langs->trans("DateOfPrinting").': '.dol_print_date(dol_now(), 'dayhour', 'tzuserrel').'<br>';
}
?>
</p>
<br>

<table width="100%" style="border-top-style: double;">
    <thead>
    <tr>
        <th class="left"><?php print $langs->trans("Label"); ?></th>
        <th class="right"><?php print $langs->trans("Qty"); ?></th>
        <th class="right"><?php if ($gift != 1) print $langs->trans("Prix"); ?></th>
        <th class="right"><?php if ($gift != 1) print $langs->trans("TotalTTC"); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    $total_qty = 0;
    foreach ($object->lines as $line) {
        $total_qty += $line->qty;
        ?>
        <tr>
            <td class="left">
            <?php 
            if (!empty($line->product_label)) {
                echo $line->product_label;
            } else {
                echo $line->description;
            }
            if ($line->remise_percent > 0) {
                echo ' (-'.$line->remise_percent.'%)';
            }
            ?>
            </td>
            <td class="right"><?php echo $line->qty; ?></td>
            <td class="right">
            <?php 
            if ($gift != 1) {
                if ($line->remise_percent > 0) {
                    echo '<s>'.price($line->subprice, 1).'</s> ';
                    echo price(price2num($line->total_ttc / $line->qty, 'MT'), 1);
                } else {
                    echo price($line->total_ttc / $line->qty, 1);
                }
            } 
            ?>
            </td>
            <td class="right"><?php if ($gift != 1) echo price($line->total_ttc, 1); ?></td>
        </tr>
    <?php
    }
    ?>
    </tbody>
</table>
<br>
<table class="right">
    <tr>
        <th class="right"><?php echo $langs->trans("Nombre d'articles"); ?></th>
        <td class="right"><?php echo $total_qty; ?></td>
    </tr>
<tr>
    <th class="right"><?php if ($gift != 1) {
        echo $langs->trans("TotalHT");
                      } ?></th>
    <td class="right"><?php if ($gift != 1) {
        echo price($object->total_ht, 1, '', 1, - 1, - 1, $conf->currency)."\n";
                      } ?></td>
</tr>
<?php if ($conf->global->TAKEPOS_TICKET_VAT_GROUPPED) {
        $vat_groups = array();
    foreach ($object->lines as $line) {
        if (!array_key_exists($line->tva_tx, $vat_groups)) {
            $vat_groups[$line->tva_tx] = 0;
        }
        $vat_groups[$line->tva_tx] += $line->total_tva;
    }
        // Loop on each VAT group
    foreach ($vat_groups as $key => $val) {
        ?>
    <tr>
        <th align="right"><?php if ($gift != 1) {
                echo $langs->trans("VAT").' '.vatrate($key, 1);
                          } ?></th>
        <td align="right"><?php if ($gift != 1) {
                echo price($val, 1, '', 1, - 1, - 1, $conf->currency)."\n";
                          } ?></td>
    </tr>
        <?php
    }
} else { ?>
<tr>
    <th class="right"><?php if ($gift != 1) {
        echo $langs->trans("TotalVAT").'</th><td class="right">'.price($object->total_tva, 1, '', 1, - 1, - 1, $conf->currency)."\n";
                      } ?></td>
</tr>
<?php }

if (price2num($object->total_localtax1, 'MU') || $mysoc->useLocalTax(1)) { ?>
<tr>
    <th class="right"><?php if ($gift != 1) {
        echo ''.$langs->trans("TotalLT1").'</th><td class="right">'.price($object->total_localtax1, 1, '', 1, - 1, - 1, $conf->currency)."\n";
                      } ?></td>
</tr>
<?php } ?>
<?php if (price2num($object->total_localtax2, 'MU') || $mysoc->useLocalTax(2)) { ?>
<tr>
    <th class="right"><?php if ($gift != 1) {
        echo ''.$langs->trans("TotalLT2").'</th><td class="right">'.price($object->total_localtax2, 1, '', 1, - 1, - 1, $conf->currency)."\n";
                      } ?></td>
</tr>
<?php } ?>
<tr>
    <th class="right"><?php if ($gift != 1) {
        echo ''.$langs->trans("TotalTTC").'</th><td class="right">'.price($object->total_ttc, 1, '', 1, - 1, - 1, $conf->currency)."\n";
                      } ?></td>
</tr>
<?php
if (isModEnabled('multicurrency') && !empty($_SESSION["takeposcustomercurrency"]) && $_SESSION["takeposcustomercurrency"] != "" && $conf->currency != $_SESSION["takeposcustomercurrency"]) {
    include_once DOL_DOCUMENT_ROOT.'/multicurrency/class/multicurrency.class.php';
    $multicurrency = new MultiCurrency($db);
    $multicurrency->fetch(0, $_SESSION["takeposcustomercurrency"]);
    echo '<tr><th class="right">';
    if ($gift != 1) {
        echo ''.$langs->trans("TotalTTC").' '.$_SESSION["takeposcustomercurrency"].'</th><td class="right">'.price($object->total_ttc * $multicurrency->rate->rate, 1, '', 1, - 1, - 1, $_SESSION["takeposcustomercurrency"])."\n";
    }
    echo '</td></tr>';
}

if (getDolGlobalString('TAKEPOS_PRINT_PAYMENT_METHOD')) {
    if (empty($facid)) {
        echo '<tr>';
        echo '<td class="right">';
        echo $langs->transnoentitiesnoconv("PaymentTypeShortLIQ");
        echo '</td>';
        echo '<td class="right">';
        $amount_payment = 0;
        echo price($amount_payment, 1, '', 1, - 1, - 1, $conf->currency);
        echo '</td>';
        echo '</tr>';
    } else {
        $sql = "SELECT p.pos_change as pos_change, p.datep as date, p.fk_paiement, p.num_paiement as num,";
        $sql .= " f.multicurrency_code,";
        $sql .= " pf.amount as amount, pf.multicurrency_amount,";
        $sql .= " cp.code";
        $sql .= " FROM ".MAIN_DB_PREFIX."paiement_facture as pf, ".MAIN_DB_PREFIX."facture as f, ".MAIN_DB_PREFIX."paiement as p";
        $sql .= " LEFT JOIN ".MAIN_DB_PREFIX."c_paiement as cp ON p.fk_paiement = cp.id";
        $sql .= " WHERE pf.fk_facture = f.rowid AND pf.fk_paiement = p.rowid AND pf.fk_facture = ".((int) $facid);
        $sql .= " ORDER BY p.datep";

        $resql = $db->query($sql);
        if ($resql) {
            $num = $db->num_rows($resql);

            $i = 0;
            while ($i < $num) {
                $row = $db->fetch_object($resql);

                echo '<tr>';
                echo '<td class="right">';
                echo $langs->transnoentitiesnoconv("PaymentTypeShort".$row->code);
                echo '</td>';
                echo '<td class="right">';
                $amount_payment = (isModEnabled('multicurrency') && $object->multicurrency_tx != 1) ? $row->multicurrency_amount : $row->amount;
                if ((!isModEnabled('multicurrency') || $object->multicurrency_tx == 1) && $row->code == "LIQ" && $row->pos_change > 0) {
                    $amount_payment = $amount_payment + $row->pos_change; // Show amount with excess received if it's cash payment
                    $currency = $conf->currency;
                } else {
                    // We do not show change if payment into a different currency because not yet supported
                    $currency = $row->multicurrency_code;
                }
                echo price($amount_payment, 1, '', 1, - 1, - 1, $currency);
                echo '</td>';
                echo '</tr>';

                if ($row->code == "LIQ" && $row->pos_change > 0) {
					?>
					<tr>
						<td class="right"><?php echo $langs->trans("Rendu"); ?></td>
						<td class="right"><?php echo price($row->pos_change, 1, '', 1, - 1, - 1, $currency); ?></td>
					</tr>
					<?php
				}
				
								$i++;
							}
						}
					}
				}
				?>
				</table>
				<div style="border-top-style: double;">
				<br>
				<br>
				<br>
				<?php
				$constFreeText = 'TAKEPOS_FOOTER'.(empty($_SESSION['takeposterminal']) ? '0' : $_SESSION['takeposterminal']);
				if (getDolGlobalString('TAKEPOS_FOOTER') || getDolGlobalString($constFreeText)) {
					$newfreetext = '';
					$substitutionarray = getCommonSubstitutionArray($langs);
					if (getDolGlobalString($constFreeText)) {
						$newfreetext .= make_substitutions(getDolGlobalString($constFreeText), $substitutionarray);
					}
					if (getDolGlobalString('TAKEPOS_FOOTER')) {
						$newfreetext .= make_substitutions(getDolGlobalString('TAKEPOS_FOOTER'), $substitutionarray);
					}
					print $newfreetext;
				}
				?>
				
				<script type="text/javascript">
					<?php
					if ($facid) {
						print 'window.print();';
					} //Avoid print when is specimen
					?>
				</script>
				
				</body>
				</html>