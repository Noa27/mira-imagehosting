<?php

return [
    'alpha_dash' => 'Le champ :attribute ne peut contenir que des lettres, chiffres, tirets et underscores.',
    'array' => 'Le champ :attribute doit être une liste.',
    'boolean' => 'Le champ :attribute doit être vrai ou faux.',
    'file' => 'Le champ :attribute doit être un fichier.',
    'max' => [
        'file' => 'Le fichier :attribute ne doit pas dépasser :max Ko.',
        'string' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
    ],
    'mimes' => 'Le fichier :attribute doit être de type : :values.',
    'min' => [
        'string' => 'Le champ :attribute doit faire au moins :min caractères.',
    ],
    'required' => 'Le champ :attribute est obligatoire.',
    'string' => 'Le champ :attribute doit être du texte.',
    'unique' => 'Ce :attribute est déjà pris.',
    'uploaded' => 'Le fichier :attribute n\'a pas pu être envoyé.',

    'attributes' => [
        'name' => 'nom',
        'password' => 'mot de passe',
        'images' => 'images',
        'images.*' => 'image',
    ],
];
