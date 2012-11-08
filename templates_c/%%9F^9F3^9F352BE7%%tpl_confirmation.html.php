<?php /* Smarty version 2.6.22, created on 2010-09-01 23:07:04
         compiled from tpl_confirmation.html */ ?>
<p>Guten Tag <?php echo $this->_tpl_vars['b']->name; ?>
,</p>
<p>Du hast Dich mit <?php echo $this->_tpl_vars['b']->guests; ?>
 Personen bei uns auf der Burg angemeldet. Wir halten Dir eine Unterkunft für diese Personenzahl und für den gewünschten Zeitraum (<?php echo $this->_tpl_vars['b']->start->returnDate('j.n.'); ?>
 – <?php echo $this->_tpl_vars['b']->end->returnDate('j.n.Y'); ?>
) frei.</p>
<p>Die Gegebenheiten auf Burg Balduinstein sind Dir hoffentlich bekannt. An dieser Stelle trotzdem noch einmal zur Erinnerung:</p>
<ul>
	<li>Schlafsack oder Bettzeug mitbringen</li>
	<li>Selbstverpflegung</li>
	<li>Ein Gasherd, Grill und ein Bräter sind vorhanden. Große Töpfe und Pfannen, Geschirr und Besteck ebenfalls.</li>
	<li>Vor der Abreise sind die benutzten Räume zu säubern.</li>
</ul>
<p>Ich gehe davon aus, dass Du das Haus in einem sauberen Zustand hinterlässt. Sollte ich oder meine Vertretung bei Deiner Abreise mit der Ordnung und der Sauberkeit nicht zufrieden sein, wird ein Endreinigungsgeld gefordert.</p>
<?php if ($this->_tpl_vars['b']->rate == 'pauschal'): ?>
<p>Für diesen Aufenthalt wurde ein Pauschalpreis von <?php echo $this->_tpl_vars['b']->price; ?>
 € vereinbart.</p>
<?php else: ?>
<p>Der Tagessatz bei der Unterkunft auf der Burg beträgt pro Person <?php echo $this->_tpl_vars['b']->rate; ?>
 €.</p>
<?php endif; ?>
<?php if ($this->_tpl_vars['r'] !== false): ?>
<p>Überweise bitte eine Anzahlung von <?php echo $this->_tpl_vars['r']->value; ?>
 € auf das Vereinskonto der Burg (Rechnungsnummer <?php echo $this->_tpl_vars['r']->id; ?>
 angeben!). Mit dieser Anzahlung, die später verrechnet wird, sicherst Du Deine Anmeldung und gibst mir die Gewissheit, dass Du mit Deinen Leuten kommst. Bei einer Absage Deinerseits weniger als vier Wochen vor Deiner Anreise müssen wir die Anzahlung einbehalten.</p>
<?php endif; ?>
<p>Bei Rückfragen erreichst Du mich unter der Telefonummer 06432-83910 oder per E-mail.</p>
<p>Es grüßt Dich<br/>
<?php echo $this->_tpl_vars['user']->realname; ?>
</p>
<?php if ($this->_tpl_vars['r'] !== false): ?>
<p>P.S.:<br/>
Überweisung bitte auf das Konto:<br/>
FREIES BILDUNGSWERK BALDUINSTEIN<br/>
Konto: 630047226<br/>
BLZ: 51050015 (Nassauische Sparkasse)<br/>
Vermerk: ReNr <?php echo $this->_tpl_vars['r']->id; ?>
 (bitte nicht vergessen anzugeben)</p>
<?php endif; ?>