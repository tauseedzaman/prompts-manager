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
    const tabBtns = document.querySelectorAll('.tab-btn');
    const sortSelect = document.getElementById('sortSelect');
    const workspaceSelect = document.getElementById('workspaceSelect');
    let currentTab = 'prompts';

    // Load settings
    const settings = await chrome.storage.local.get(['apiUrl', 'apiToken']);
    if (settings.apiUrl) apiUrlInput.value = settings.apiUrl;
    if (settings.apiToken) apiTokenInput.value = settings.apiToken;

    if (!settings.apiUrl || !settings.apiToken) {
        showSettings();
    } else {
        fetchWorkspaces();
        const { lastWorkspace } = await chrome.storage.local.get('lastWorkspace');
        if (lastWorkspace) workspaceSelect.value = lastWorkspace;
        fetchPrompts(searchInput.value, sortSelect.value, workspaceSelect.value);
    }

    // Tab switching
    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            tabBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentTab = btn.dataset.tab;
            fetchPrompts(searchInput.value, sortSelect.value);
        });
    });

    sortSelect.addEventListener('change', () => {
        fetchPrompts(searchInput.value, sortSelect.value, workspaceSelect.value);
    });

    workspaceSelect.addEventListener('change', async () => {
        const wsId = workspaceSelect.value;
        await chrome.storage.local.set({ lastWorkspace: wsId });
        fetchPrompts(searchInput.value, sortSelect.value, wsId);
    });

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
        fetchPrompts(searchInput.value, sortSelect.value, workspaceSelect.value);
    }, 300));

    async function fetchPrompts(search = '', sort = 'latest', workspace_id = '') {
        const { apiUrl, apiToken } = await chrome.storage.local.get(['apiUrl', 'apiToken']);
        if (!apiUrl || !apiToken) return;

        promptsList.innerHTML = '<p class="empty-msg">Loading...</p>';

        try {
            const payload = { search };
            if (currentTab === 'favorites') payload.is_favorite = true;
            if (currentTab === 'marketplace') payload.marketplace = true;
            payload.sort = sort;
            if (workspace_id) payload.workspace_id = workspace_id;

            const response = await chrome.runtime.sendMessage({
                type: 'FETCH_PROMPTS',
                payload
            });

            if (response.error) throw new Error(response.error);

            displayPrompts(response.data);
        } catch (error) {
            promptsList.innerHTML = `<p class="empty-msg" style="color: #ef4444;">Error: ${error.message}</p>`;
        }
    }

    async function fetchWorkspaces() {
        try {
            const response = await chrome.runtime.sendMessage({ type: 'FETCH_WORKSPACES' });
            if (response.error) throw new Error(response.error);

            const { workspaces } = response;
            // Clear but keep "Private Library"
            workspaceSelect.innerHTML = '<option value="">Private Library</option>';
            workspaces.forEach(ws => {
                const opt = document.createElement('option');
                opt.value = ws.id;
                opt.textContent = ws.name;
                workspaceSelect.appendChild(opt);
            });

            // Re-apply selection if possible
            const { lastWorkspace } = await chrome.storage.local.get('lastWorkspace');
            if (lastWorkspace) workspaceSelect.value = lastWorkspace;
        } catch (error) {
            console.error('Failed to fetch workspaces:', error);
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
                <div class="prompt-content">
                    <span class="prompt-title">${prompt.title}</span>
                    <span class="prompt-desc">${prompt.description || prompt.prompt_text}</span>
                </div>
                ${currentTab !== 'marketplace' ? `
                <button class="delete-btn" title="Delete Prompt" data-id="${prompt.id}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2M10 11v6M14 11v6"/></svg>
                </button>` : ''}
            `;

            // Click content to copy
            div.querySelector('.prompt-content').addEventListener('click', (e) => {
                copyToClipboard(prompt.prompt_text);

                // Track usage
                chrome.runtime.sendMessage({
                    type: 'INCREMENT_USAGE',
                    payload: { id: prompt.id }
                });

                const title = div.querySelector('.prompt-title');
                const originalText = title.textContent;
                title.textContent = '✅ Copied!';
                title.classList.add('copied');
                setTimeout(() => {
                    title.textContent = originalText;
                    title.classList.remove('copied');
                }, 1500);
            });

            // Delete functionality
            const delBtn = div.querySelector('.delete-btn');
            if (delBtn) {
                delBtn.addEventListener('click', async (e) => {
                    e.stopPropagation();
                    if (!confirm('Are you sure you want to delete this prompt?')) return;

                    delBtn.disabled = true;
                    const response = await chrome.runtime.sendMessage({
                        type: 'DELETE_PROMPT',
                        payload: { id: prompt.id }
                    });

                    if (response.success) {
                        div.remove();
                        if (promptsList.children.length === 0) {
                            promptsList.innerHTML = '<p class="empty-msg">No prompts found.</p>';
                        }
                    } else {
                        alert('Delete failed: ' + response.error);
                        delBtn.disabled = false;
                    }
                });
            }

            promptsList.appendChild(div);
        });
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text);
    }

    function debounce(func, wait) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }
});
