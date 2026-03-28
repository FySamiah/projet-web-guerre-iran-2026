INSERT INTO categories (nom, slug, description) VALUES
('Actualités', 'actualites', 'Dernières informations sur la guerre en Iran'),
('Géopolitique', 'geopolitique', 'Analyse des enjeux politiques et militaires'),
('Économie', 'economie', 'Impact économique mondial du conflit'),
('Humanitaire', 'humanitaire', 'Conséquences sur les populations civiles');

INSERT INTO articles (titre, slug, resume, contenu, image, alt_image, statut, categorie_id)
VALUES (
'Frappes massives en Iran : une escalade majeure du conflit',
'frappes-massives-iran-2026',
'Des frappes américaines et israéliennes ont marqué une nouvelle phase du conflit en Iran.',
'Depuis fin février 2026, des frappes militaires menées par les États-Unis et Israël ont visé des installations stratégiques en Iran, notamment des sites militaires et nucléaires. Cette opération a marqué une escalade majeure dans la région. L’Iran a riposté en lançant des missiles et des drones vers plusieurs cibles au Moyen-Orient, augmentant les tensions régionales.',
'iran1.jpg',
'Frappe militaire en Iran avec fumée au-dessus de la ville',
'publie',
1
);


INSERT INTO articles (titre, slug, resume, contenu, image, alt_image, statut, categorie_id)
VALUES (
'Plus de 1900 morts dans le conflit en Iran selon les ONG',
'1900-morts-iran-2026',
'Le bilan humain du conflit ne cesse d’augmenter avec des milliers de victimes.',
'Selon la Fédération internationale de la Croix-Rouge, plus de 1900 personnes ont été tuées et plus de 20 000 blessées depuis le début du conflit. Les infrastructures civiles, y compris des hôpitaux et des écoles, ont été gravement touchées, aggravant la situation humanitaire.',
'iran2.jpg',
'Victimes civiles et secours après une attaque',
'publie',
4
);

INSERT INTO articles (titre, slug, resume, contenu, image, alt_image, statut, categorie_id)
VALUES (
'Des milliers de civils fuient Téhéran',
'fuite-civils-teheran',
'La population civile fuit massivement les zones de conflit.',
'Face à l’intensité des bombardements, plus de 100 000 habitants ont quitté Téhéran en seulement 48 heures. Les organisations humanitaires alertent sur une crise majeure avec des pénuries de nourriture, d’électricité et de médicaments.',
'iran3.jpg',
'Population fuyant la ville de Téhéran',
'publie',
4
);

INSERT INTO articles (titre, slug, resume, contenu, image, alt_image, statut, categorie_id)
VALUES (
'Le détroit d’Ormuz au cœur des tensions mondiales',
'detroit-ormuz-tensions',
'Le conflit menace un passage clé pour le pétrole mondial.',
'Le détroit d’Ormuz représente environ 20% du commerce mondial de pétrole. Toute perturbation dans cette zone stratégique pourrait entraîner une hausse massive des prix de l’énergie et impacter l’économie mondiale.',
'iran4.jpg',
'Détroit d’Ormuz avec des pétroliers',
'publie',
3
);

INSERT INTO articles (titre, slug, resume, contenu, image, alt_image, statut, categorie_id)
VALUES (
'L’Iran refuse les propositions de cessez-le-feu',
'iran-refuse-cessez-feu',
'Les négociations diplomatiques échouent malgré la pression internationale.',
'L’Iran a rejeté les propositions de cessez-le-feu proposées par les États-Unis et maintient ses exigences. Le conflit continue de s’étendre, impliquant plusieurs pays du Moyen-Orient et augmentant le risque d’une guerre régionale.',
'iran5.jpg',
'Réunion diplomatique sur le conflit iranien',
'publie',
2
);


