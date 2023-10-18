# Brief sjablonen

Voor het genereren van de brieven wordt er gebruik gemaakt van sjablonen die deels naar eigen inzicht ingericht kunnen
worden. Voor het vullen van deze brieven zijn er een select aantal, generieke en dynamische, codes beschikbaar die
gebruikt kunnen worden om de data vanuit de backoffice te koppelen aan de inhoud.

Het algemene sjabloon bevat de hoofdstructuur van de brief en deze bestaat uit twee blokken:

- `{block content}`: deze bevat de hoofdtekst van de brief
- `{block sidebar}`: deze bevat de zij-kolom met bijvoorbeeld de adresgegevens van DUS-i en is al voor ingevuld.

De codes die gebruikt kunnen worden binnen het sjabloon beginnen en eindigen altijd met een accolades waarbij de
openingsaccolade altijd gevolgd wordt `$content->`, bijvoorbeeld `{$content->subsidyTitle}`.

## Generieke codes

- `{$content->subsidyTitle}`: De titel van de regeling
- `{$content->createdAt}`: De aanmaakdatum van de brief
- `{$content->submittedAt}`: De aanmaakdatum van de aanvraag
- `{$content->contactEmailAddress}`: E-mailadres van de afdeling, in te vullen via de regeling versie
- `{$content->reference}`: De interne referentie of kenmerk

## Dynamische codes

De dynamische codes zijn afhankelijk van welke formuliervelden er beschikbaar en ingevuld zijn. Deze zijn verder
opgesplitst in meerdere stappen (aanvraag, beoordeling). Deze kunnen worden gebruikt door de volgende notatie aan te
houden:

```{$content->stage[[stapNr]]->[[formulier veldcode]]}```

Noot: Hierbij dienen [[stapNr]] en [[formulier veldcode]] vervangen te worden door de juiste data.

Een voorbeeld code van bijvoorbeeld een formulier die uit twee stappen bestaat, aanvraag (1) en beoordeling (2), waarbij
er een formulierveld ingevuld kan worden voor een voornaam (firstName) kan er dan als volgt uit zien:

```{$content->stage1->firstName}```

## Dynamisch functies

Binnen het sjabloon is het ook beperkt mogelijk om bepaalde functies te gebruiken waarmee je het gedrag van de inhoud
kan beïnvloeden. Denk hierbij aan een datumnotatie of condities. Deze gebruik je altijd door deze achter de code te
zetten met een `|`-teken.

De beschikbare functies op dit moment zijn:

- `date`: Formateren van een datum (`{$content->createdAt|date:"d-m-Y"}` toont de datum als 23-08-2023)
- `firstUpper`: Zorgt dat de eerste letter altijd een hoofdletter is
- `lower`: Zet alles om naar kleine letters
- `upper`: Zet alles om naar hoofdletters
- `if`: Om een conditie te controleren waarna je bijvoorbeeld een bepaald blok kan tonen
- `elseif`: kan gebruikt worden in combinatie met `if`, als de vorige conditie niet matched wordt deze conditie
  gecontroleerd
- `else`: kan gebruikt worden in combinatie met `if` en `elseif`, als alle voorgaande condities niet matchen wordt de
  tekst die in dit blok staat getoond.

Voorbeeld van `if` / `elseif` / `else`:

```text
{if $content->stage2->decision === 'Goedgekeurd'}
    Uw aanvraag is geaccepteerd.
{elseif $content->stage2->decision === 'Aanvulling nodig'}
    Uw aanvraag behoeft nog aanvulling.
{else}
    We zijn uw aanvraag nog aan het beoordelen.
{/if}
```

<div class="page-break"></div>
