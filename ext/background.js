// background.js

chrome.runtime.onInstalled.addListener(() => {
    chrome.contextMenus.create({
        id: "save-to-prompt-manager",
        title: "Save to Prompt Manager",
        contexts: ["selection"]
    });
});

chrome.contextMenus.onClicked.addListener((info) => {
    if (info.menuItemId === "save-to-prompt-manager") {
        // Use first 5 words as title
        const words = info.selectionText.trim().split(/\s+/).slice(0, 5).join(' ');
        const title = words + (info.selectionText.split(/\s+/).length > 5 ? '...' : '');

        handleSavePrompt({
            text: info.selectionText,
            title: title,
            category_name: 'ChatGPT',
            tag_names: ['ChatGPT']
        }).then(result => {
            if (result.error) {
                chrome.notifications.create('', {
                    type: 'basic',
                    iconUrl: '/icons/icon128.png',
                    title: 'Save Failed',
                    message: result.error,
                    priority: 2
                });
            } else {
                chrome.notifications.create('', {
                    type: 'basic',
                    iconUrl: '/icons/icon128.png',
                    title: 'Prompt Saved!',
                    message: `Successfully saved "${title}" to your library.`,
                    priority: 1
                });
            }
        });
    }
});

chrome.runtime.onMessage.addListener((request, sender, sendResponse) => {
    if (request.type === 'FETCH_PROMPTS') {
        const payload = request.payload || {};
        if (payload.marketplace) {
            handleFetchMarketplace(payload).then(sendResponse);
        } else {
            handleFetchPrompts(payload).then(sendResponse);
        }
        return true; // Keep channel open for async
    }

    if (request.type === 'SAVE_PROMPT') {
        handleSavePrompt(request.payload).then(sendResponse);
        return true;
    }

    if (request.type === 'DELETE_PROMPT') {
        handleDeletePrompt(request.payload.id).then(sendResponse);
        return true;
    }
});

async function handleFetchPrompts({ search, is_favorite }) {
    const { apiUrl, apiToken } = await chrome.storage.local.get(['apiUrl', 'apiToken']);
    if (!apiUrl || !apiToken) return { error: 'Settings missing' };

    try {
        const apiUrlBase = getApiUrl(apiUrl);
        const url = new URL(`${apiUrlBase}prompts`);
        if (search) url.searchParams.append('search', search);
        if (is_favorite) url.searchParams.append('is_favorite', '1');

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

async function handleFetchMarketplace({ search }) {
    const { apiUrl, apiToken } = await chrome.storage.local.get(['apiUrl', 'apiToken']);
    if (!apiUrl || !apiToken) return { error: 'Settings missing' };

    try {
        const apiUrlBase = getApiUrl(apiUrl);
        const url = new URL(`${apiUrlBase}marketplace`);
        if (search) url.searchParams.append('search', search);

        const response = await fetch(url.toString(), {
            headers: {
                'Authorization': `Bearer ${apiToken}`,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) throw new Error('Failed to fetch marketplace');
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
                title: payload.title || 'New Prompt from Web',
                prompt_text: payload.text,
                category_name: payload.category_name,
                tag_names: payload.tag_names,
                source: 'Browser Extension'
            })
        });

        const data = await response.json();
        if (!response.ok) throw new Error(data.message || 'Failed to save');

        return { data };
    } catch (error) {
        return { error: error.message };
    }
}

async function handleDeletePrompt(id) {
    const { apiUrl, apiToken } = await chrome.storage.local.get(['apiUrl', 'apiToken']);
    if (!apiUrl || !apiToken) return { error: 'Settings missing' };

    try {
        const apiUrlBase = getApiUrl(apiUrl);
        const response = await fetch(`${apiUrlBase}prompts/${id}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${apiToken}`,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) throw new Error('Failed to delete');

        chrome.notifications.create('', {
            type: 'basic',
            iconUrl: '/icons/icon128.png',
            title: 'Prompt Deleted',
            message: 'Successfully removed from Prompt Manager.',
            priority: 1
        });

        return { success: true };
    } catch (error) {
        return { error: error.message };
    }
}
function getApiUrl(apiUrl) {
    const base = apiUrl.endsWith('/') ? apiUrl : `${apiUrl}/`;
    return base.endsWith('/api/') ? base : `${base}api/`;
}
