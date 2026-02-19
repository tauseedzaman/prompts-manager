// popup.js

document.addEventListener('DOMContentLoaded', async () => {
    const mainView = document.getElementById('mainView');
    const settingsView = document.getElementById('settingsView');
    const settingsBtn = document.getElementById('settingsBtn');
    const backBtn = document.getElementById('backBtn');
    const saveSettings = document.getElementById('saveSettings');
    const apiUrlInput = document.getElementById('apiUrl');
    const apiTokenInput = document.getElementById('apiToken');
    const promptsList = document.getElementById('promptsList');
    const searchInput = document.getElementById('searchInput');

    // Load settings
    const settings = await chrome.storage.local.get(['apiUrl', 'apiToken']);
    if (settings.apiUrl) apiUrlInput.value = settings.apiUrl;
    if (settings.apiToken) apiTokenInput.value = settings.apiToken;

    if (!settings.apiUrl || !settings.apiToken) {
        showSettings();
    } else {
        fetchPrompts();
    }

    // Toggle views
    settingsBtn.addEventListener('click', showSettings);
    backBtn.addEventListener('click', showMain);

    function showSettings() {
        mainView.classList.add('hidden');
        settingsView.classList.remove('hidden');
    }

    function showMain() {
        settingsView.classList.add('hidden');
        mainView.classList.remove('hidden');
    }

    // Save settings
    saveSettings.addEventListener('click', async () => {
        const apiUrl = apiUrlInput.value.trim();
        const apiToken = apiTokenInput.value.trim();

        if (!apiUrl || !apiToken) {
            alert('Please fill in both fields.');
            return;
        }

        await chrome.storage.local.set({ apiUrl, apiToken });
        alert('Settings saved!');
        showMain();
        fetchPrompts();
    });

    // Search prompts
    searchInput.addEventListener('input', debounce(() => {
        fetchPrompts(searchInput.value);
    }, 300));

    async function fetchPrompts(search = '') {
        const { apiUrl, apiToken } = await chrome.storage.local.get(['apiUrl', 'apiToken']);
        if (!apiUrl || !apiToken) return;

        promptsList.innerHTML = '<p class="empty-msg">Loading...</p>';

        try {
            const url = new URL(`${apiUrl}/prompts`);
            if (search) url.searchParams.append('search', search);

            const response = await fetch(url.toString(), {
                headers: {
                    'Authorization': `Bearer ${apiToken}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Failed to fetch prompts');

            const data = await response.json();
            displayPrompts(data.data || data); // Laravel pagination returns .data
        } catch (error) {
            promptsList.innerHTML = `<p class="empty-msg" style="color: #ef4444;">Error: ${error.message}</p>`;
        }
    }

    function displayPrompts(prompts) {
        if (!prompts || prompts.length === 0) {
            promptsList.innerHTML = '<p class="empty-msg">No prompts found.</p>';
            return;
        }

        promptsList.innerHTML = '';
        prompts.forEach(prompt => {
            const div = document.createElement('div');
            div.className = 'prompt-item';
            div.innerHTML = `
                <span class="prompt-title">${prompt.title}</span>
                <span class="prompt-desc">${prompt.description || prompt.prompt_text}</span>
            `;
            div.addEventListener('click', () => {
                copyToClipboard(prompt.prompt_text);
                const title = div.querySelector('.prompt-title');
                const originalText = title.textContent;
                title.textContent = '✅ Copied!';
                setTimeout(() => title.textContent = originalText, 1500);
            });
            promptsList.appendChild(div);
        });
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text);
    }

    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }
});
