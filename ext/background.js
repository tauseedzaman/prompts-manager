// background.js

chrome.runtime.onMessage.addListener((request, sender, sendResponse) => {
    if (request.type === 'FETCH_PROMPTS') {
        handleFetchPrompts(request.payload).then(sendResponse);
        return true; // Keep channel open for async
    }

    if (request.type === 'SAVE_PROMPT') {
        handleSavePrompt(request.payload).then(sendResponse);
        return true;
    }
});

async function handleFetchPrompts({ search }) {
    const { apiUrl, apiToken } = await chrome.storage.local.get(['apiUrl', 'apiToken']);
    if (!apiUrl || !apiToken) return { error: 'Settings missing' };

    try {
        const apiUrlBase = getApiUrl(apiUrl);
        const url = new URL(`${apiUrlBase}prompts`);
        if (search) url.searchParams.append('search', search);

        const response = await fetch(url.toString(), {
            headers: {
                'Authorization': `Bearer ${apiToken}`,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) throw new Error('Failed to fetch');
        const data = await response.json();
        return { data: data.data || data };
    } catch (error) {
        return { error: error.message };
    }
}

async function handleSavePrompt(payload) {
    const { apiUrl, apiToken } = await chrome.storage.local.get(['apiUrl', 'apiToken']);
    if (!apiUrl || !apiToken) return { error: 'Settings missing' };

    try {
        const apiUrlBase = getApiUrl(apiUrl);
        const response = await fetch(`${apiUrlBase}prompts`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${apiToken}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                title: payload.title || 'New Prompt from ChatGPT',
                prompt_text: payload.text,
                source: 'ChatGPT'
            })
        });

        if (!response.ok) throw new Error('Failed to save');
        const data = await response.json();
        return { data };
    } catch (error) {
        return { error: error.message };
    }
}
function getApiUrl(apiUrl) {
    const base = apiUrl.endsWith('/') ? apiUrl : `${apiUrl}/`;
    return base.endsWith('/api/') ? base : `${base}api/`;
}
