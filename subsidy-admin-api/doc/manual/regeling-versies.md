# Regeling versies

Binnen een versie van een regeling zijn er een aantal belangrijke velden van toepassing:

- `contact_mail_address`: Wordt gebruikt in de brieven om bijvoorbeeld de dienst postbus aan te duiden bij
  contactgegevens.
- `mail_to_address_field_identifier`: Hier moet de veldcode van het e-mailadres ingevuld worden die gebruikt wordt om
  e-mail communicatie naar te sturen. Deze veldcode is afhankelijk van het formulier en is bijvoorbeeld `email`.
- `mail_to_name_field_identifier`: Hier moet de veldcode(s) voor de naam ingevuld worden die gebruikt wordt in de
  e-mail communicatie. Deze veldcode is afhankelijk van het formulier. Er kunnen ook meerdere velden gecombineerd worden
  door deze te scheiden met een `;` (puntkomma), bijvoorbeeld `firstName;lastName`.

Voor deze veldcodes is het ook mogelijk, indien de regeling uit meerdere stappen bestaat, om een afwijkende stap mee te
geven: `stage1:email` of `stage2:firstName`.

<div class="page-break"></div>
