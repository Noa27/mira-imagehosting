@extends('layout')

@section('content')
    <style>
        #drop { border: 2px dashed #6b4a12; border-radius: 10px; padding: 40px 16px; text-align: center; cursor: pointer; color: var(--muted); background: var(--well); transition: background .15s, border-color .15s, box-shadow .15s; }
        #drop:hover { border-color: var(--amber); }
        #drop.over { border-color: var(--amber); background: rgba(255, 182, 39, .07); box-shadow: 0 0 24px rgba(255, 182, 39, .25), inset 0 0 24px rgba(255, 182, 39, .08); }
        #drop * { pointer-events: none; }
        #drop strong { display: block; margin: 14px 0 6px; font: 900 clamp(15px, 3.4vw, 20px) var(--display); letter-spacing: 2px; text-transform: uppercase; color: var(--amber); text-shadow: 0 0 12px rgba(255, 182, 39, .5); }
        #drop svg { color: #fffbe0; filter: drop-shadow(0 0 6px #ffd23f); }
        #drop.over svg, body.warp #drop svg { animation: spin .6s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .strip { display: flex; flex-wrap: wrap; line-height: 0; }
        .strip img { height: 170px; max-width: 100%; object-fit: cover; display: block; }
        .item { position: relative; }
        .item form { position: absolute; top: 6px; right: 6px; display: none; }
        .item:hover form { display: block; }
        .item a { display: block; }
        #result-section { display: none; }
        .row { display: flex; gap: 8px; align-items: center; margin-top: 10px; flex-wrap: wrap; }
        #progress { margin-top: 10px; min-height: 1.4em; font: 15px var(--mono); color: var(--amber); text-shadow: 0 0 6px rgba(255, 182, 39, .4); }
        .strip img { border: 0; }
        #count { font-family: var(--mono); font-size: 14px; }
    </style>

    <div class="card c-red">
        <div id="drop">
            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" aria-hidden="true">
                <path d="M12 13v8M12 13 5 5M12 13l7-8"/><circle cx="12" cy="13" r="2.6" fill="currentColor" stroke="none"/>
            </svg>
            <strong>{{ __('Glisse tes images ici') }}</strong>
            {{ __('ou clique pour choisir, ou colle avec Ctrl+V') }}
        </div>
        <input type="file" id="file" accept="image/*" multiple hidden>
        <div id="progress"></div>
    </div>

    <div class="card c-orange" id="result-section">
        <h2>{{ __('Résultat') }}</h2>
        <div class="strip" id="result"></div>
        <div class="row" style="margin-top: 14px;">
            <textarea id="html" rows="3" readonly></textarea>
        </div>
        <div class="row">
            <button class="btn small" data-copy="html">{{ __('Copier le HTML') }}</button>
            <button class="btn small ghost" data-copy="links">{{ __('Copier les liens') }}</button>
        </div>
    </div>

    <div class="card">
        <h2>{{ __('Mes images') }} (<span id="count">{{ $images->count() }}</span>)</h2>
        <div class="strip" id="gallery">
            @foreach ($images as $image)
                <div class="item">
                    <a href="{{ $image->url() }}" target="_blank"><img src="{{ $image->url() }}" alt="{{ $image->original_name }}" loading="lazy"></a>
                    <form method="post" action="{{ route('images.destroy', $image) }}" onsubmit="return confirm(@js(__('Supprimer cette image ?')))">
                        @csrf @method('delete')
                        <button class="btn small danger">{{ __('Suppr.') }}</button>
                    </form>
                </div>
            @endforeach
        </div>
        @if ($images->isEmpty())
            <p class="muted" id="empty">{{ __("Rien pour l'instant.") }}</p>
        @endif
    </div>

    <div class="card c-pink">
        <h2>API</h2>
        @if (session('token'))
            <p>{{ __('Ton token (il ne sera plus affiché) :') }} <code>{{ session('token') }}</code></p>
        @endif
        <form method="post" action="{{ route('token') }}" onsubmit="return confirm(@js(__("Générer un nouveau token ? L'ancien ne marchera plus.")))">
            @csrf
            <button class="btn small">{{ auth()->user()->api_token ? __('Régénérer le token') : __('Générer un token') }}</button>
        </form>
        <p class="muted" style="margin-bottom: 4px;">{{ __('Upload, renvoie les balises img à la suite :') }}</p>
        <code>curl -H "Authorization: Bearer TOKEN" -F "images[]=@photo.png" -F "images[]=@other.jpg" {{ url('/api/images') }}</code>
        <p class="muted" style="margin-bottom: 4px;">{{ __('Réponse en JSON :') }} <code>?format=json</code></p>
        <p class="muted" style="margin: 0;">{{ __('Lister :') }} <code>GET {{ url('/api/images') }}</code> (<code>?format=html</code>)</p>
        <p class="muted" style="margin: 0;">{{ __('Supprimer :') }} <code>DELETE {{ url('/api/images') }}/{id}</code></p>
    </div>

    <script>
        const drop = document.getElementById('drop');
        const input = document.getElementById('file');
        const progress = document.getElementById('progress');
        const result = document.getElementById('result');
        const html = document.getElementById('html');
        const gallery = document.getElementById('gallery');
        const token = document.querySelector('meta[name=csrf-token]').content;
        const urls = [];
        const t = @js([
            'sending' => __('Envoi'),
            'sent' => __('envoyée(s)'),
            'failed' => __('en échec'),
            'copied' => __('Copié'),
        ]);

        drop.addEventListener('click', () => input.click());
        input.addEventListener('change', () => { upload([...input.files]); input.value = ''; });
        ['dragenter', 'dragover'].forEach(e => drop.addEventListener(e, ev => { ev.preventDefault(); drop.classList.add('over'); }));
        ['dragleave', 'drop'].forEach(e => drop.addEventListener(e, ev => { ev.preventDefault(); drop.classList.remove('over'); }));
        drop.addEventListener('drop', ev => upload([...ev.dataTransfer.files]));
        ['dragover', 'drop'].forEach(e => document.addEventListener(e, ev => ev.preventDefault()));
        document.addEventListener('paste', ev => {
            const files = [...ev.clipboardData.files].filter(f => f.type.startsWith('image/'));
            if (files.length) upload(files);
        });

        async function upload(files) {
            files = files.filter(f => f.type.startsWith('image/'));
            if (!files.length) return;
            let done = 0, failed = 0;
            document.body.classList.add('warp');
            for (const file of files) {
                progress.textContent = `> ${t.sending} ${done + failed + 1}/${files.length}...`;
                const body = new FormData();
                body.append('images[]', file);
                try {
                    const res = await fetch('{{ route('images.store') }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                        body,
                    });
                    if (!res.ok) throw new Error((await res.json()).message || res.status);
                    const data = await res.json();
                    data.images.forEach(add);
                    done++;
                } catch (e) {
                    failed++;
                    console.error(file.name, e);
                }
            }
            progress.textContent = `> ${done} ${t.sent}` + (failed ? `, ${failed} ${t.failed}` : '');
            document.body.classList.remove('warp');
        }

        function add(image) {
            urls.push(image.url);
            document.getElementById('result-section').style.display = 'block';
            const img = new Image();
            img.src = image.url;
            result.append(img);
            html.value += `<img src="${image.url}">`;

            const a = document.createElement('a');
            a.href = image.url;
            a.target = '_blank';
            a.append(img.cloneNode());
            const item = document.createElement('div');
            item.className = 'item';
            item.append(a);
            gallery.prepend(item);
            const count = document.getElementById('count');
            count.textContent = +count.textContent + 1;
            document.getElementById('empty')?.remove();
        }

        document.querySelectorAll('[data-copy]').forEach(btn => btn.addEventListener('click', async () => {
            await navigator.clipboard.writeText(btn.dataset.copy === 'html' ? html.value : urls.join('\n'));
            const label = btn.textContent;
            btn.textContent = t.copied;
            setTimeout(() => btn.textContent = label, 1200);
        }));
    </script>
@endsection
