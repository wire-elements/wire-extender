let livewireScript;
let componentAssets;
let currentScript = document.currentScript;
let livewireStarted = false;

function getUri(append = '')
{
    let uri = document.querySelector('[data-uri]')?.getAttribute('data-uri');

    if (!uri) {
        uri = new URL(currentScript.src).origin;
    }

    if (!uri.endsWith('/')) {
        uri += '/';
    }

    return uri + append;
}

function getLivewireAssetUri()
{
    return document.querySelector('[data-livewire-asset-uri]')?.getAttribute('data-livewire-asset-uri') ?? getUri('livewire/livewire.min.js');
}

function getLivewireUpdateUri()
{
    return document.querySelector('[data-update-uri]')?.getAttribute('data-update-uri') ?? getUri('livewire/update');
}

function getEmbedUri()
{
    const base = document.querySelector('[data-embed-uri]')?.getAttribute('data-embed-uri') ?? getUri('livewire/embed');
    const queryString = window.location.search;

    return base + queryString;
}

function injectLivewire()
{
    if (window.Livewire || livewireStarted) {
        return;
    }

    const style = document.createElement('style');
    style.innerHTML = '[wire\\:loading][wire\\:loading], [wire\\:loading\\.delay][wire\\:loading\\.delay], [wire\\:loading\\.inline-block][wire\\:loading\\.inline-block], [wire\\:loading\\.inline][wire\\:loading\\.inline], [wire\\:loading\\.block][wire\\:loading\\.block], [wire\\:loading\\.flex][wire\\:loading\\.flex], [wire\\:loading\\.table][wire\\:loading\\.table], [wire\\:loading\\.grid][wire\\:loading\\.grid], [wire\\:loading\\.inline-flex][wire\\:loading\\.inline-flex] {display: none;}[wire\\:loading\\.delay\\.none][wire\\:loading\\.delay\\.none], [wire\\:loading\\.delay\\.shortest][wire\\:loading\\.delay\\.shortest], [wire\\:loading\\.delay\\.shorter][wire\\:loading\\.delay\\.shorter], [wire\\:loading\\.delay\\.short][wire\\:loading\\.delay\\.short], [wire\\:loading\\.delay\\.default][wire\\:loading\\.delay\\.default], [wire\\:loading\\.delay\\.long][wire\\:loading\\.delay\\.long], [wire\\:loading\\.delay\\.longer][wire\\:loading\\.delay\\.longer], [wire\\:loading\\.delay\\.longest][wire\\:loading\\.delay\\.longest] {display: none;}[wire\\:offline][wire\\:offline] {display: none;}[wire\\:dirty]:not(textarea):not(input):not(select) {display: none;}:root {--livewire-progress-bar-color: #2299dd;}[x-cloak] {display: none !important;}';
    document.head.appendChild(style);

    livewireScript = document.createElement('script');
    livewireScript.src = getLivewireAssetUri();
    livewireScript.dataset.csrf = '';
    livewireScript.dataset.updateUri = getLivewireUpdateUri();
    document.body.appendChild(livewireScript);
}

function waitForLivewireAndStart() {
    if (livewireStarted) {
        return;
    }

    if(window.Livewire) {
        startLivewire();
    }
    livewireScript.onload = async function () {
        await startLivewire();
    }

    livewireStarted = true;
}

async function startLivewire(assets)
{
    Livewire.hook('request', ({ options }) => {
        options.headers['X-Wire-Extender'] = '';
        options.credentials = 'include';
    })
    await Livewire.triggerAsync('payload.intercept', {assets: componentAssets});
    Livewire.start();
}

function renderComponents(components)
{
    injectLivewire();

    fetch(getEmbedUri(), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            components: components
        }),
       'credentials': 'include'
    })
        .then(response => response.json())
        .then(data => {
            for (let component in data.components) {
                let el = document.querySelector(`[data-component-key="${component}"]`);
                el.innerHTML = data.components[component];
            }

            componentAssets = data.assets;
            waitForLivewireAndStart();
        });
}

document.addEventListener('DOMContentLoaded', function() {
    let components = [];

    document.querySelectorAll('livewire').forEach((el) => {
        if (!el.hasAttribute('data-component-key')) {
            el.setAttribute('data-component-key', Math.random().toString(36).substring(2));
        }

        components.push({
            key: el.getAttribute('data-component-key'),
            name: el.getAttribute('data-component'),
            params: el.getAttribute('data-params')
        });
    });

    renderComponents(components);
});
