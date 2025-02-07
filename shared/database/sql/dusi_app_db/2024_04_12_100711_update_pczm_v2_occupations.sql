UPDATE public.fields
SET params = '{"default": null, "options": ["Ambulancechauffeur/(zorgambulance)begeleider", "Anesthesiemedewerker en/of operatieassistent", "Arts", "Bachelor medisch hulpverlener", "Doktersassistent", "Helpende", "Physician assistant", "Praktijkondersteuner huisarts", "Verpleegkundig specialist", "(gespecialiseerd) Verpleegkundige", "Verzorgende in de individuele gezondheidszorg (VIG’er) of (kraam)verzorgende", "ADL-assistent, zorgondersteuner en/of voedingsassistent", "Begeleider gehandicaptenzorg, jeugdzorg en/of psychiatrische inrichting", "GGD-test medewerker", "Anders"]}'
WHERE code = 'employmentFunction'
  AND subsidy_stage_id = 'd7f38409-6805-408c-87e9-afd9b00a8de0';
