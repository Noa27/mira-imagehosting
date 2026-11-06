# Mira

Hébergement d'images entre potes.

## En local

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

Au premier passage sur la page de connexion, le site propose de créer le compte admin. Les autres comptes se créent ensuite depuis le panel.

Les dossiers `storage/`, `bootstrap/cache/`, `database/` et `public/i/` doivent être accessibles en écriture.

Pour une mise à jour, renvoyer seulement `app/`, `resources/`, `routes/` et ce qui a changé, jamais `database/database.sqlite`, `.env` ni `public/i/`.

Après le déploiement, `composer install` remet les outils de dev en local.

## API

Générer un token depuis la page d'accueil, puis :

```bash
curl -H "Authorization: Bearer TOKEN" -F "images[]=@photo.png" https://youindanger.site/image-hosting/api/images
```

| Route | Effet |
| --- | --- |
| `POST /api/images` | Upload (`images[]` ou `image`), renvoie les `<img>` à la suite. `?format=json` pour du JSON |
| `GET /api/images` | Liste en JSON, `?format=html` pour du HTML |
| `DELETE /api/images/{id}` | Supprime une image |
