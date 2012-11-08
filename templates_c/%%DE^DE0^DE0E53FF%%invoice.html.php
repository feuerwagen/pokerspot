<?php /* Smarty version 2.6.22, created on 2010-08-26 02:35:57
         compiled from invoice.html */ ?>
Das Freie Bildungswerk Balduinstein e.V. stellt den Betrag von <?php echo $this->_tpl_vars['inv']->price['base']; ?>
€ für die <?php if ($this->_tpl_vars['inv']->booking->nights == 0): ?>Veranstaltung am <?php echo $this->_tpl_vars['inv']->booking->start->returnDate('j.n.Y'); ?>
<?php else: ?>Übernachtung vom <?php echo $this->_tpl_vars['inv']->booking->start->returnDate('j.n.'); ?>
 bis <?php echo $this->_tpl_vars['inv']->booking->end->returnDate('j.n.Y'); ?>
<?php endif; ?> auf der Jugendburg Balduinstein in Rechnung. <?php if (is_array ( $this->_tpl_vars['inv']->items )): ?>


Zusätzlich werden folgende Posten berechnet:
<?php $_from = $this->_tpl_vars['inv']->items; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['i']):
?>
– <?php echo $this->_tpl_vars['i']['title']; ?>
: <?php echo $this->_tpl_vars['i']['value']; ?>
€
<?php endforeach; endif; unset($_from); ?>

Somit ergibt sich ein Gesamtbetrag von <?php echo $this->_tpl_vars['inv']->price['full']; ?>
€. <?php endif; ?><?php if ($this->_tpl_vars['inv']->bail !== false && $this->_tpl_vars['inv']->bail->paid !== false): ?>Für diese Unterbringung sind bereits <?php echo $this->_tpl_vars['inv']->bail->value; ?>
€ per Überweisung angezahlt.
Es bleibt ein Restbetrag von <?php echo $this->_tpl_vars['inv']->price['full']-$this->_tpl_vars['inv']->bail->value; ?>
€.
<?php endif; ?>


<?php if ($this->_tpl_vars['inv']->renr == 'bar'): ?>Der Betrag wurde in Bar gezahlt.<?php else: ?><?php if ($this->_tpl_vars['inv']->split != ''): ?>Davon wurden bereits <?php echo $this->_tpl_vars['inv']->split; ?>
€ in Bar gezahlt. Wir bitten den verbleibenden Betrag<?php else: ?>Wir bitten den Betrag<?php endif; ?> von <?php echo $this->_tpl_vars['inv']->price['full']-$this->_tpl_vars['inv']->bail->value-$this->_tpl_vars['inv']->split; ?>
€ innerhalb der nächsten 14 Tage auf folgendes Konto zu überweisen:

Freies Bildungswerk
Nassauische Sparkasse Diez
BLZ: 510 500 15
Konto: 630 047 226
Verwendungszweck: ReNr <?php echo $this->_tpl_vars['inv']->renr; ?>
<?php endif; ?>